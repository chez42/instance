<?php

class PositionInformation_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => 'LBL_DASHBOARD',
            'linkurl' => $this->getDashBoardUrl(),
            'linkicon' => '',
        );

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $parentQuickLinks;
    }

    /**
     * Function to get list view query for popup window
     * @param <String> $sourceModule Parent module
     * @param <String> $field parent fieldname
     * @param <Integer> $record parent id
     * @param <String> $listQuery
     * @return <String> Listview Query
     */
    public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
        if (($sourceModule == 'Accounts' && $field == 'account_id' && $record)
            || in_array($sourceModule, array('Campaigns', 'Products', 'Services', 'Emails'))) {

            if ($sourceModule === 'Campaigns') {
                $condition = " vtiger_account.accountid NOT IN (SELECT accountid FROM vtiger_campaignaccountrel WHERE campaignid = '$record')";
            } elseif ($sourceModule === 'Products') {
                $condition = " vtiger_account.accountid NOT IN (SELECT crmid FROM vtiger_seproductsrel WHERE productid = '$record')";
            } elseif ($sourceModule === 'Services') {
                $condition = " vtiger_account.accountid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid = '$record') ";
            } elseif ($sourceModule === 'Emails') {
                $condition = ' vtiger_account.emailoptout = 0';
            } else {
                $condition = " vtiger_account.accountid != '$record'";
            }

            $position = stripos($listQuery, 'where');
            if($position) {
                $split = spliti('where', $listQuery);
                $overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
            } else {
                $overRideQuery = $listQuery. ' WHERE ' . $condition;
            }
            return $overRideQuery;
        }
    }

    /**
     * Function to get relation query for particular module with function name
     * @param <record> $recordId
     * @param <String> $functionName
     * @param Vtiger_Module_Model $relatedModule
     * @return <String>
     */
    public function getRelationQuery($recordId, $functionName, $relatedModule) {
        if ($functionName === 'get_activities') {
            $focus = CRMEntity::getInstance($this->getName());
            $focus->id = $recordId;
            $entityIds = $focus->getRelatedContactsIds();
            $entityIds = implode(',', $entityIds);

            $userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

            $query = "SELECT CASE WHEN (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name,
						vtiger_crmentity.*, vtiger_activity.activitytype, vtiger_activity.subject, vtiger_activity.date_start, vtiger_activity.time_start,
						vtiger_activity.recurringtype, vtiger_activity.due_date, vtiger_activity.time_end, vtiger_seactivityrel.crmid AS parent_id,
						CASE WHEN (vtiger_activity.activitytype = 'Task') THEN (vtiger_activity.status) ELSE (vtiger_activity.eventstatus) END AS status
						FROM vtiger_activity
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
						LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
							WHERE vtiger_crmentity.deleted = 0 AND vtiger_activity.activitytype <> 'Emails'
								AND (vtiger_seactivityrel.crmid = ".$recordId;
            if($entityIds) {
                $query .= " OR vtiger_cntactivityrel.contactid IN (".$entityIds."))";
            } else {
                $query .= ")";
            }

            $relatedModuleName = $relatedModule->getName();
            $query .= $this->getSpecificRelationQuery($relatedModuleName);
            $nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
            if ($nonAdminQuery) {
                $query = appendFromClauseToQuery($query, $nonAdminQuery);
            }

            // There could be more than one contact for an activity.
            $query .= ' GROUP BY vtiger_activity.activityid';
        } else {
            $query = parent::getRelationQuery($recordId, $functionName, $relatedModule);
        }

        return $query;
    }

    static public function UpdatePositionInformationValuesUsingModSecuritiesSetting($accounts){
        global $adb;
        if(!is_array($accounts))
            $account_number[] = $accounts;
        else
            $account_number = $accounts;
        foreach($account_number AS $k => $v){
            $account_number[$k] = str_replace('-', '', $v);
        }
        $questions = generateQuestionMarks($account_number);
        $query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_modsecurities ms ON ms.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON ms.modsecuritiesid = cf.modsecuritiesid
				  SET p.last_price = ms.security_price, p.current_value = CASE WHEN cf.factor = 0 THEN p.quantity * ms.security_price * cf.security_price_adjustment 
				  																				  ELSE p.quantity * ms.security_price * cf.security_price_adjustment END 
				  WHERE REPLACE(account_number, '-', '') IN ({$questions})";
        $adb->pquery($query, array($account_number));
    }

    static public function CalculatePositionInformationWeightForAccountNumber($account_number){
        global $adb;
        $account_number = str_replace('-', '', $account_number);
        $query = "SET @global_total = (SELECT SUM(current_value) FROM vtiger_positioninformation WHERE account_number=?)";
        $adb->pquery($query, array($account_number));

        $query = "UPDATE vtiger_positioninformation
				  SET weight = current_value/@global_total*100 
				  WHERE account_number=?";
        $adb->pquery($query, array($account_number));

    }

    /**
     * Returns the entity ID for the given account number and symbol
     * @param $account_number
     * @param $symbol
     */
    static public function GetPositionEntityIDForAccountNumberAndSymbol($accounts, $symbol){
        global $adb;
        if(!is_array($accounts))
            $account_number[] = $accounts;
        else
            $account_number = $accounts;
        foreach($account_number AS $k => $v){
            $account_number[$k] = str_replace('-', '', $v);
        }

        $questions = generateQuestionMarks($account_number);
        $query = "SELECT positioninformationid FROM vtiger_positioninformation p 
				  JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
				  WHERE REPLACE(account_number, '-', '') IN ({$questions}) AND security_symbol = ? AND e.deleted = 0";

        $result = $adb->pquery($query, array($account_number, $symbol));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'positioninformationid');
        return 0;
    }

    static public function UndeleteAllPositionsForAccounts($account_numbers){
        global $adb;
        $questions = generateQuestionMarks($account_numbers);

        $query = "DROP TABLE IF EXISTS ToUndelete;";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE ToUndelete
                  SELECT positioninformationid FROM vtiger_positioninformation WHERE account_number IN ({$questions});";
        $adb->pquery($query, array($account_numbers));

        $query = "UPDATE vtiger_crmentity e
                  JOIN ToUndelete u ON e.crmid = u.positioninformationid
                  SET deleted = 0;";
        $adb->pquery($query, array());

#        $query = "UPDATE vtiger_crmentity e SET deleted= 0 WHERE crmid IN (SELECT positioninformationid FROM vtiger_positioninformation WHERE account_number IN ({$questions})) AND setype = 'PositionInformation'";
#        $adb->pquery($query, array($account_numbers));
    }

    static public function UndeletePositionEntity($record){
        global $adb;
        $query = "UPDATE vtiger_crmentity e SET deleted = 0 WHERE crmid = ? AND setype='PositionInformation'";
        $adb->pquery($query, array($record));
    }

    static public function GetTotalvalueForAccountNumberUsingPositions($account_number){
        global $adb;
        $account_number = str_replace('-', '', $account_number);
        $query = "SELECT SUM(current_value) AS total_value FROM vtiger_positioninformation p 
				  JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
				  WHERE REPLACE(p.account_number, '-', '') = ? AND e.deleted = 0";
        $result = $adb->pquery($query, array($account_number));
        if($adb->num_rows($result) > 0)
            return $adb->query_result($result, 0, 'total_value');
    }

    static public function GetPositionsForAccountNumber($account_number){
        global $adb;
        if(is_array($account_number)){
            $questions = generateQuestionMarks($account_number);
            $account_number = str_replace('-', '', $account_number);
            $query = "SELECT * FROM vtiger_positioninformation p
                      JOIN vtiger_positioninformationcf cf ON p.positioninformationid = cf.positioninformationid
                      JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
                      LEFT JOIN vtiger_modsecurities ms ON ms.security_symbol = p.security_symbol
                      WHERE account_number IN ({$questions})
                      AND e.deleted = 0
                      AND p.quantity != 0";

            $result = $adb->pquery($query, array($account_number));
        }else {
            $account_number = str_replace('-', '', $account_number);
            $query = "SELECT * FROM vtiger_positioninformation p
                      JOIN vtiger_positioninformationcf cf ON p.positioninformationid = cf.positioninformationid
                      JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
                      LEFT JOIN vtiger_modsecurities ms ON ms.security_symbol = p.security_symbol
                      WHERE REPLACE(p.account_number, '-', '') = ?
                      AND e.deleted = 0
                      AND p.quantity != 0";

            $result = $adb->pquery($query, array($account_number));
        }
        if($adb->num_rows($result) > 0){
            $positions = array();
            foreach($result AS $k => $v){
                $positions[] = $v;
            }
            return $positions;
        }
        return 0;
    }

    static public function GetSymbolsForAccountNumber($account_number, $character_count=0){
        global $adb;
        $account_number = str_replace('-', '', $account_number);
        if($character_count)
            $symbol_selector = " LEFT(security_symbol,{$character_count}) AS security_symbol ";
        else
            $symbol_selector = "security_symbol";
        $query = "SELECT {$symbol_selector} FROM vtiger_positioninformation p
				  WHERE REPLACE(p.account_number, '-', '') = ?";

        $result = $adb->pquery($query, array($account_number));
        $symbols = array();
        if($adb->num_rows($result) > 0){
            while($v = $adb->fetchByAssoc($result)){
                $symbols[] = $v['security_symbol'];
            }
            return $symbols;
        }
        return 0;
    }

    /**
     * Gets the positions and their calculated weight
     * @param $accounts
     */
    static public function GetPositionsAndCalculateDynamic($account_numbers){
        global $adb;

        if(!is_array($account_numbers))
            $accounts[] = $account_numbers;
        else
            $accounts = $account_numbers;
        foreach($accounts AS $k => $v){
            $accounts[$k] = str_replace('-', '', $v);
        }
        $questions = generateQuestionMarks($accounts);

        $query = "SET @global_total = (SELECT SUM(current_value) FROM vtiger_positioninformation WHERE account_number IN ({$questions}))";
        $adb->pquery($query, array($accounts));

        $query = "DROP TABLE IF EXISTS calculated_positions";
        $adb->pquery($query, array());

        $query = "CREATE TEMPORARY TABLE calculated_positions
				  SELECT positioninformationid, ms.modsecuritiesid, p.security_symbol, p.description, account_number, household_account, advisor_id, SUM(quantity) AS quantity, last_price, SUM(current_value) AS current_value, SUM(current_value)/@global_total*100 AS weight, 
													   SUM(cost_basis) AS cost_basis, SUM(unrealized_gain_loss) AS unrealized_gain_loss, gain_loss_percent, contact_link, symbol_id, dashless
		  		  FROM vtiger_positioninformation p
		  		  JOIN vtiger_crmentity e ON e.crmid = p.positioninformationid
		  		  LEFT JOIN vtiger_modsecurities ms ON p.security_symbol = ms.security_symbol
		  		  WHERE account_number IN ({$questions})
		  		  AND e.deleted = 0
		  		  AND p.quantity != 0
				  GROUP BY p.security_symbol";
        $adb->pquery($query, array($accounts));

        $query = "SELECT * FROM calculated_positions";
        $result = $adb->pquery($query, array());

        if($adb->num_rows($result) > 0){
            $positions = array();
            foreach($result AS $k => $v){
                $positions[] = $v;
            }
            return $positions;
        }
        return 0;
    }

    /**
     * Update the Position Information price
     * @param $symbol
     * @param $price
     */
    static public function UpdatePositionInformationPrice($symbol, $price){
        global $adb;

        $query = "UPDATE vtiger_positioninformation p SET last_price = ?, security_price = ? WHERE security_symbol = ?";
        $adb->pquery($query, array($price, $price, $symbol));
    }

    /**
     * Takes all positions in the system and calculates their value accordingly
     * @param $symbol
     */
    static public function UpdateIndividualPositionBasedOnModSecurities($symbol){
        global $adb;
        $query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_positioninformationcf pcf ON p.positioninformationid = pcf.positioninformationid
				  JOIN vtiger_modsecurities m ON m.security_symbol = p.security_symbol
				  JOIN vtiger_modsecuritiescf cf ON m.modsecuritiesid = cf.modsecuritiesid
				  SET p.last_price = m.security_price * cf.security_price_adjustment, p.dashless = REPLACE(p.account_number, '-', ''),
				      pcf.security_type = m.securitytype, pcf.base_asset_class = cf.aclass
				  WHERE p.security_symbol = ?";
        $adb->pquery($query, array($symbol));
    }

    static public function GetAccountNumbersThatHaveSymbol($symbol){
        global $adb;
        $query = "SELECT account_number FROM vtiger_positioninformation WHERE security_symbol = ?";
        $r = $adb->pquery($query, array($symbol));
        $accounts = array();
        while($v = $adb->fetchByAssoc($r)){
            $accounts[] = $v['account_number'];
        }
        return $accounts;
    }

    static public function ResetPositionValues($account_number){
        global $adb;
        $query = "UPDATE vtiger_positioninformation p
				  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
				  SET p.quantity = 0, p.last_price = 0, p.current_value = 0, p.weight = 0, p.cost_basis = 0, p.unrealized_gain_loss = 0, p.gain_loss_percent = 0
				  WHERE account_number = ? OR dashless = ?";
        $adb->pquery($query, array($account_number, $account_number));
    }

    /**
     * Close positions for the specified account(s) given as an array.  If none passed in, it closes positions for all accounts
     * @param null $account_number
     */
    static public function ClosePositions($account_number = null){
        global $adb;
        $params = array();
        if($account_number){
            $questions = generateQuestionMarks($account_number);
            $and = " AND pos.account_number IN ({$questions}) ";
            $params[] = $account_number;
        }

        $query = "UPDATE vtiger_positioninformation pos
                  JOIN vtiger_positioninformationcf cf USING (positioninformationid)
                  JOIN vtiger_portfolioinformation p ON p.account_number = pos.account_number
                  SET cf.closed_account = 1, cf.position_closed = 1, pos.quantity = 0, pos.current_value = 0, pos.last_price = 0, 
                      pos.cost_basis = 0, pos.unrealized_gain_loss = 0, pos.gain_loss_percent = 0, cf.household_weight = 0, cf.contact_weight = 0  
                  WHERE p.accountclosed = 1 {$and}";
        $adb->pquery($query, $params);
    }
}

?>
