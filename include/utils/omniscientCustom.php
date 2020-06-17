<?php
require_once('include/database/PearDatabase.php');
require_once('include/utils/CommonUtils.php'); //new
require_once('include/utils/UserInfoUtil.php');
require_once('include/utils/utils.php');


function HasRoleAccess($user_id)
{
	$roleId = GetRoleID($user_id);
	$access_array = array("H2", "H13", "H17");
	if(in_array($roleId, $access_array))
		return true;
	else
		return false;
}

/**
 * Get the role ID of the user
 * @global type $adb
 * @param type $user_id
 * @return type
 */
function GetRoleID($user_id)
{
	global $adb;
	$query = 'SELECT roleid from vtiger_user2role where userid=?';
	$result = $adb->pquery($query,array($user_id));
	$roleId = $adb->query_result($result, 0, 'roleid');
	return $roleId;
}

/**
 * Converts from MYSQL format to YMD
 * @param type $date
 * @return type
 */
function ConvertDateToMDY($date)
{
	$time = strtotime($date);
	$time = date('m/d/Y', $time);
	return $time;
}

/**
 * Returns the portfolio ID's associated with the portfolio account numbers.  Must pass in an array
 * @param type $account_numbers
 */
function GetPortfolioIDsFromPortfolioAccountNumbers($account_numbers){
	global $adb;
	if(is_array($account_numbers))
		$account_numbers = SeparateArrayWithCommasAndSingleQuotes($account_numbers);
	else
		$account_numbers = "'{$account_numbers}'";
	$query = "SELECT MAX(portfolio_id) AS portfolio_id FROM vtiger_portfolios 
              WHERE REPLACE(portfolio_account_number, '-', '') IN ({$account_numbers}) AND portfolio_account_number != '' AND portfolio_account_number IS NOT NULL
              AND account_closed != 1
              GROUP BY portfolio_account_number";

	$result = $adb->pquery($query, array());
	if($adb->num_rows($result) > 0){
		foreach($result AS $k => $v)
			$pids[] = $v['portfolio_id'];
	}

	return $pids;
}

/**
 * This function is really just a one stop shop for modules that need access to the Activites "More Information" buttons
 * @param type $actions
 * @param type $button
 */
function GetActivityMoreInformationButtons($actions, $related_module, $button)
{
	global $current_user;
	if($actions) {
		if(is_string($actions)) $actions = explode(',', strtoupper($actions));
		if(in_array('ADD', $actions) && isPermitted($related_module,1, '') == 'yes') {
			if(getFieldVisibilityPermission('Calendar',$current_user->id,'parent_id', 'readwrite') == '0') {
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create create_new_task'" .
					"  type='button' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_TODO', $related_module) ."'>&nbsp;";
			}
			if(getFieldVisibilityPermission('Events',$current_user->id,'parent_id', 'readwrite') == '0') {
				$button .= "<input title='".getTranslatedString('LBL_NEW'). " ". getTranslatedString('LBL_TODO', $related_module) ."' class='crmbutton small create create_new_event'" .
					" type='button' name='button'" .
					" value='". getTranslatedString('LBL_ADD_NEW'). " " . getTranslatedString('LBL_EVENT', $related_module) ."'>";
			}
		}
	}

	return $button;
}

/**
 * Converts the passed in date from Y-M-D to M-D-Y format
 * @param type $date
 */
function ConvertYMDtoMDY($date)
{
	return(date("m-d-Y", strtotime($date)));
}

/**
 * Get the entity information for a given id... Currently returns entity_id, full_name, firstname, lastname if available
 * @param type $entity_id
 * @return type
 */
function GetEntityInfoFromID($entity_id)
{
	global $adb;
	$sql = "select setype from vtiger_crmentity where crmid=?";
	$result = $adb->pquery($sql, array($entity_id));
	$parent_module = $adb->query_result($result,0,"setype");
	switch($parent_module)
	{
		case "Accounts":$query = "SELECT accountid AS entity_id, accountname AS full_name FROM vtiger_account WHERE accountid = ?";
			break;
		case "Contacts":$query = "SELECT contactid AS entity_id, firstname, lastname, CONCAT(firstname, ' ', lastname) AS full_name FROM vtiger_contactdetails WHERE contactid = ?";
			break;
		case "Leads":$query = "SELECT leadid AS entity_id, firstname, lastname, CONCAT(firstname, ' ', lastname) AS full_name FROM vtiger_leaddetails WHERE leadid = ?";
			break;
		case "Potentials":$query = "SELECT potentialid AS entity_id, potentialname AS full_name FROM vtiger_potential WHERE potentialid = ?";
			break;
		case "HelpDesk":$query = "SELECT ticketid AS entity_id, title AS full_name FROM vtiger_troubletickets WHERE ticketid = ?";
	}

	$result = $adb->pquery($query, array($entity_id));

	return $result;
}

/**
 * Get all ticket information
 * @global type $adb
 * @param type $id
 * @return type
 */
function GetAllTicketInfo($id)
{
	global $adb;
	$query = "SELECT g.groupname AS group_name, tt.*, e.smcreatorid, e.smownerid, e.crmid, e.modifiedby FROM vtiger_troubletickets tt
              LEFT JOIN vtiger_crmentity e ON tt.ticketid = e.crmid
              LEFT JOIN vtiger_groups g ON g.groupid = e.smownerid
              WHERE tt.ticketid = ?";
	$result = $adb->pquery($query, array($id));

	foreach($result AS $k => $v)
		$info = array("ID" => $v['ticketid'],
			"TICKET_NO" => $v['ticket_no'],
			"GROUPNAME" => $v['groupname'],
			"GROUP_NAME" => $v['group_name'],
			"PARENT_ID" => $v['parent_id'],
			"PRIORITY" => $v['priority'],
			"SEVERITY" => $v['severity'],
			"STATUS" => $v['status'],
			"CATEGORY" => $v['category'],
			"TITLE" => $v['title'],
			"SOLUTION" => $v['solution'],
			"UPDATE_LOG" => $v['update_log'],
			"HOURS" => $v['hours'],
			"DAYS" => $v['days']);
	return $info;
}
/**
 * Remove duplicates from the given array
 * @param type $array
 * @return type
 */
function RemoveDuplicates($array) {
	$valueCount = array();
	foreach ($array as $value) {
		$valueCount[$value]++;
	}

	$return = array();
	foreach ($valueCount as $value => $count) {
		if ( $count == 1 ) {
			$return[] = $value;
		}
	}

	return $return;
}

/**
 * Get all contact information from the given contact...Does not include crmentity
 */
function GetPersonalInfoFromContactID($id){
	global $adb;
	$query = "SELECT * FROM vtiger_contactdetails 
                  LEFT JOIN vtiger_contactscf ON vtiger_contactdetails.contactid = vtiger_contactscf.contactid 
                  WHERE vtiger_contactscf.contactid AND vtiger_contactdetails.contactid={$id}";
	$result = $adb->pquery($query, array());
	return $result;
}

/**
 * Get all portfolio's that are related to the given SSN
 * @global type $adb
 * @param type $ssn
 * @return query result
 */

function GetAllPortfoliosFromSSN($ssn){
	global $adb;
	$query = "SELECT * FROM vtiger_portfolios WHERE portfolio_tax_id='{$ssn}'";
	$result = $adb->pquery($query, array());
	return $result;
}

/**
 * Get alternate email ID
 */
function GetAlternateEmailID($ticketid)
{
	global $adb;
	$field = GetFieldNameFromFieldLabel("View Permission");

	$query = "SELECT {$field} FROM vtiger_ticketcf
              WHERE ticketid = ?";
	$result = $adb->pquery($query, array($ticketid));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "{$field}");
	return 0;
}

/*Return all portfolio information based on portfolio account number*/
function GetPortfolioInfoFromPortfolioAccountNumber($id){
	global $adb;
	$query = "SELECT * FROM vtiger_portfolios p
                  LEFT JOIN vtiger_pc_account_custom ac ON ac.account_number = p.portfolio_account_number
                  WHERE p.portfolio_account_number IN ('{$id}') AND p.account_closed != 1";
	$result = $adb->pquery($query, array());
	return $result;
}

/*Return the Portfolio IDs related to a contact*/
function GetPortfolioIDsFromContactID($id)
{
	global $adb;

	$query = "SELECT p.portfolio_id
              FROM vtiger_portfolios p
              JOIN vtiger_portfolioinformation pinf ON p.portfolio_account_number = pinf.account_number
			  JOIN vtiger_portfolioinformationcf cf ON cf.portfolioinformationid = pinf.portfolioinformationid
              JOIN vtiger_crmentity e ON e.crmid = pinf.portfolioinformationid
              WHERE pinf.contact_link = ? OR REPLACE(cf.tax_id, '-', '') = (SELECT REPLACE(ssn, '-', '') FROM vtiger_contactscf WHERE contactid=? AND ssn != '' AND ssn IS NOT NULL)
              AND e.deleted = 0";
	/*
		$query = "select `p`.`portfolio_id` AS `portfolio_id`,
					`vtiger_account`.`accountid` AS `account_id`
					FROM (((`vtiger_portfolios` `p` left join `vtiger_contactscf` `p1` on((`p`.`portfolio_tax_id` = `p1`.`ssn`)))
							left join `vtiger_contactdetails` on((`vtiger_contactdetails`.`contactid` = `p1`.`contactid`)))
							left join `vtiger_account` on((`vtiger_account`.`accountid` = `vtiger_contactdetails`.`accountid`)))
					WHERE ((`p`.`portfolio_tax_id` <> '') and (`p1`.`ssn` <> '')) AND vtiger_contactdetails.contactid=? AND p.account_closed != 1";*/

//    echo "ID: {$id}<br />";
	$result = $adb->pquery($query,array($id, $id));
	$portfolio_ids = array();

	if($adb->num_rows($result) > 0)
		foreach($result AS $k => $v)
		{
			$portfolio_ids[] = $v["portfolio_id"];
		}
	return $portfolio_ids;
}

function GetPortfolioAccountNumbersFromSSN($ssn){
	global $adb;
	if(!is_array($ssn)) {
		$tmp = $ssn;
		$ssn = array();
		$ssn[] = $tmp;
	}
	foreach($ssn AS $k => $v){
		$ssn[$k] = str_replace('-', '', $v);
	}
	$questions = generateQuestionMarks($ssn);
	$query = "SELECT account_number FROM vtiger_portfolioinformationcf cf
              JOIN vtiger_portfolioinformation p USING (portfolioinformationid)
              WHERE REPLACE(cf.tax_id, '-', '') IN ({$questions}) AND REPLACE(cf.tax_id, '-', '') != '' AND accountclosed = 0";
	$result = $adb->pquery($query, array($ssn));
	if($adb->num_rows($result) > 0){
		foreach($result AS $k => $v){
			$numbers[] = $v['account_number'];
		}
		return $numbers;
	}
	return 0;
}

function GetPortfolioAccountNumbersFromContactID($contact_id){
    global $adb;
    $questions = generateQuestionMarks($contact_id);

    $query = "SELECT account_number FROM vtiger_portfolioinformationcf cf
              JOIN vtiger_portfolioinformation p USING (portfolioinformationid)
              WHERE contact_link IN ({$questions}) AND accountclosed = 0";
    $result = $adb->pquery($query, array($contact_id));
    if($adb->num_rows($result) > 0){
        foreach($result AS $k => $v){
            $numbers[] = $v['account_number'];
        }
        return $numbers;
    }
    return 0;
}

function GetPortfolioAccountNumbersFromHouseholdID($household_id){
    global $adb;
    $query = "SELECT account_number FROM vtiger_portfolioinformationcf cf
              JOIN vtiger_portfolioinformation p USING (portfolioinformationid)
              WHERE household_account = ? AND accountclosed = 0";
    $result = $adb->pquery($query, array($household_id));
    if($adb->num_rows($result) > 0){
        foreach($result AS $k => $v){
            $numbers[] = $v['account_number'];
        }
        return $numbers;
    }
    return 0;
}

function GetHouseholdIDFromAccountNumber($account_number){
    global $adb;
    $query = "SELECT household_account FROM vtiger_portfolioinformation p
              WHERE account_number = ? AND accountclosed = 0";
    $result = $adb->pquery($query, array($account_number));
    if($adb->num_rows($result) > 0)
        return $adb->query_result($result, 0, 'household_account');
    return 0;
}


function GetContactIDFromAccountNumber($account_number){
    global $adb;
    $query = "SELECT contact_link FROM vtiger_portfolioinformation p
              WHERE account_number = ? AND accountclosed = 0";
    $result = $adb->pquery($query, array($account_number));
    if($adb->num_rows($result) > 0)
        return $adb->query_result($result, 0, 'contact_link');
    return 0;
}

/**
 * @param $account_number
 * Return record entities by account number (contact, household, portfolio)
 */
function GetAccountDataRecords($account_number){
    global $adb;
    $questions = generateQuestionMarks($account_number);
    $query = "SELECT portfolioinformationid, contact_link, household_account
              FROM vtiger_portfolioinformation WHERE account_number IN ({$questions})";
    $result = $adb->pquery($query, array($account_number));
    $data = array();
    if($adb->num_rows($result) > 0) {
        while($x = $adb->fetchByAssoc($result)) {
            $tmp = array();
            if(!empty($x['portfolioinformationid']))
                $tmp['portfolio'] = PortfolioInformation_Record_Model::getInstanceById($x['portfolioinformationid']);
            if(!empty($x['contact_link']))
                $tmp['contact'] = Contacts_Record_Model::getInstanceById($x['contact_link']);
            if(!empty($x['household_account']))
                $tmp['household'] = Accounts_Record_Model::getInstanceById($x['household_account']);
            $data[] = $tmp;
        }
    }
    return $data;
}

/**
 * @param $account_number
 * @field_value is key value pairing for where conditioning.  IE:  array(in_arrears => ">= 1", security_type => " = flow")
 * Return transaction record ID's by account number
 */
function GetTransactionRecordIDs($account_number, $field_value = null){
    global $adb;
    $and = "";
    if($field_value != null){
        foreach($field_value AS $k => $v){
            $and .= " AND {$k} {$v}";
        }
    }
    $questions = generateQuestionMarks($account_number);
    $query = "SELECT t.transactionsid, t.account_number
              FROM vtiger_transactions t 
              JOIN vtiger_transactionscf cf USING (transactionsid)
              JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
              WHERE account_number IN ({$questions})
              AND e.deleted = 0
              {$and}";
    $result = $adb->pquery($query, array($account_number));
    $data = array();
    if($adb->num_rows($result) > 0) {
        while($v = $adb->fetchByAssoc($result)) {
            $data[] = $v['transactionsid'];//Vtiger_Record_Model::getInstanceById($v['transactionsid'], 'transactions');
        }
    }
    return $data;
}

/**
 * @param $account_number
 * @field_value is key value pairing for where conditioning.  IE:  array(in_arrears => ">= 1", security_type => " = flow")
 * Return transaction record entities by account number
 */
function GetTransactionRecords($account_number, $field_value = null){
    global $adb;
    $and = "";
    if($field_value != null){
        foreach($field_value AS $k => $v){
            $and .= " AND {$k} {$v}";
        }
    }
    $questions = generateQuestionMarks($account_number);
    $query = "SELECT t.transactionsid, t.account_number
              FROM vtiger_transactions t 
              JOIN vtiger_transactionscf cf USING (transactionsid)
              JOIN vtiger_crmentity e ON e.crmid = t.transactionsid
              WHERE account_number IN ({$questions})
              AND e.deleted = 0
              {$and}";
    $result = $adb->pquery($query, array($account_number));
    $data = array();
    if($adb->num_rows($result) > 0) {
        while($v = $adb->fetchByAssoc($result)) {
            $data[] = Vtiger_Record_Model::getInstanceById($v['transactionsid']);
        }
    }
    return $data;
}

/**
 * Returns the portfolio account numbers from the portfolio ID's passed in
 * @param type $pids
 */
function GetPortfolioAccountNumbersFromPids($pids){
	global $adb;
	$pids = SeparateArrayWithCommasAndSingleQuotes($pids);
	$query = "SELECT portfolio_account_number 
              FROM vtiger_portfolios
              WHERE portfolio_id IN ({$pids}) AND account_closed != 1";
	$result = $adb->pquery($query, array());
	if($adb->num_rows($result) > 0)
		foreach($result AS $k => $v)
			$numbers[] = $v['portfolio_account_number'];
	return $numbers;
}

/**Return the Portfolio IDs related to a HHID
 * If the ID passed in is a contact ID, it will snag the account ID from the contact automatically and change it to a HHID
 */
function GetPortfolioIDsFromHHID($id)
{
	global $adb;
	$type = GetSettypeFromID($id);
	if($type == "Contacts")
		$id = GetAccountIDFromContactID ($id);

	$query = "SELECT portfolio_id from vtiger_portfolios p
              WHERE p.portfolio_tax_id IN (SELECT cf.ssn
                                           FROM vtiger_contactdetails cd
                                           JOIN vtiger_contactscf cf USING (contactid)
                                           WHERE cd.accountid = ?
                                           AND cf.ssn != '' AND cf.ssn is not null)
              AND p.account_closed != 1";
	/*    $query = "select `p`.`portfolio_id` AS `portfolio_id`,
					`vtiger_account`.`accountid` AS `account_id`
					FROM (((`vtiger_portfolios` `p` left join `vtiger_contactscf` `p1` on((`p`.`portfolio_tax_id` = `p1`.`ssn`)))
							left join `vtiger_contactdetails` on((`vtiger_contactdetails`.`contactid` = `p1`.`contactid`)))
							left join `vtiger_account` on((`vtiger_account`.`accountid` = `vtiger_contactdetails`.`accountid`)))
							left join vtiger_crmentity e ON e.crmid = p1.contactid
					WHERE ((`p`.`portfolio_tax_id` <> '') and (`p1`.`ssn` <> '')) AND vtiger_account.accountid=? AND e.deleted=0 AND p.account_closed != 1";*/

	$result = $adb->pquery($query,array($id));

	$portfolio_ids = array();
    if($adb->num_rows($result) > 0) {
        foreach ($result AS $k => $v)
            $portfolio_ids[] = $v["portfolio_id"];
        return $portfolio_ids;
    }
	return $portfolio_ids;
}

function GetSSNsForHousehold($record_id){
	global $adb;
	$query = "SELECT REPLACE(ssn, '-', '') AS ssn FROM vtiger_contactscf cf
              JOIN vtiger_contactdetails c USING (contactid)
              WHERE accountid = ? AND ssn IS NOT NULL AND ssn != ''";
	$result = $adb->pquery($query, array($record_id));
	if($adb->num_rows($result) > 0){
		foreach($result AS $k => $v){
			$ssn[] = $v['ssn'];
		}
		return $ssn;
	}
	return 0;
}

/*
 * Get the alternate email address
 */
function GetAlternateEmail($ticketid)
{
	global $adb;
	$field = GetFieldNameFromFieldLabel("View Permission");

	$query = "SELECT u.email1 FROM vtiger_users u 
              LEFT JOIN vtiger_ticketcf tcf ON tcf.{$field} = u.id
              WHERE tcf.ticketid = ?";
	$result = $adb->pquery($query, array($ticketid));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "email1");
	return 0;
}

/*
 * Get the user who last modified the entity
 */
function GetLastModifiedUser($crmid)
{
	global $adb;
	$query = "SELECT CONCAT_WS(' ', u.last_name, u.first_name) AS user_name FROM vtiger_crmentity e
              LEFT JOIN vtiger_users u ON u.id = e.modifiedby
              WHERE e.crmid=?";
	$result = $adb->pquery($query, array($crmid));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "user_name");
	return 0;
}

/*
 * Get the entity creator
 */
function GetCreator($crmid)
{
	global $adb;
	$query = "SELECT CONCAT_WS(' ', u.last_name, u.first_name) AS user_name FROM vtiger_crmentity e
              LEFT JOIN vtiger_users u ON u.id = e.smcreatorid 
              WHERE e.crmid=?";
	$result = $adb->pquery($query, array($crmid));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "user_name");
	return 0;
}

/*
 * Get the entity creator
 */
function GetCreatorID($crmid)
{
	global $adb;
	$query = "SELECT u.id FROM vtiger_crmentity e
              LEFT JOIN vtiger_users u ON u.id = e.smcreatorid 
              WHERE e.crmid=?";
	$result = $adb->pquery($query, array($crmid));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "id");
	return 0;
}


/*
 * Get group id by name
 */
function GetGroupIDByName($name)
{
	global $adb;
	$query = "SELECT groupid FROM vtiger_groups WHERE groupname=?";
	$result = $adb->pquery($query, array($name));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "groupid");
	return 0;
}

/*
 * Returns the email belonging to the contact id
 */

function GetContactEmailByID($contactID)
{
	global $adb;
	$query = "SELECT email FROM vtiger_contactdetails WHERE contactid = ?";
	$result = $adb->pquery($query, array($contactID));
	return $adb->query_result($result, 0, "email");
}

/*
 * Get the user name based on user id
 */

function GetUserFirstLastNameByID($userid, $reverse = false)
{
	global $adb;
	$query = "SELECT first_name, last_name FROM vtiger_users WHERE id={$userid}";
	$result = $adb->pquery($query, array());
	if($adb->num_rows($result) > 0 )
	{
		$fname = $adb->query_result($result, 0, "first_name");
		$lname = $adb->query_result($result, 0, "last_name");
		if($reverse)
			return $fname . " " . $lname;
		else
			return $lname . " " . $fname;
	}
}

/*
 * Get the group ID from the given name
 */

function GetGroupIDFromName($groupName)
{
	global $adb;
	$query = "SELECT groupid FROM vtiger_groups WHERE groupname = 'Operations'";
	$result = $adb->pquery($query, array());
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "groupid");

	return 0;
}

/*
 * Get fieldname from fieldlabel
 */

function GetFieldNameFromFieldLabel($label)
{
	global $adb;
	$query = "SELECT fieldname FROM vtiger_field WHERE fieldlabel = ?";
	$result = $adb->pquery($query, array($label));
	if($adb->num_rows($result) > 0)
		$fieldname = $adb->query_result($result, 0, "fieldname");
	else
		$fieldname = "";

	return $fieldname;
}

/*
 * Check Module Access
 */

function CheckModuleAccessByFieldLabel($label)
{
	global $adb, $current_user;

	if(!is_admin($current_user))
		return false;

	$query = "SELECT * FROM omniscient_module_access WHERE user_id = ?";
	$result = $adb->pquery($query, array($current_user->id));
	if($adb->num_rows($result) <= 0)
		return true;//We have access because we haven't taken it away
	foreach($result AS $k => $v)
	{
		if($v["module_label"] == $label)
		{
			if($v["enabled"] == 0)
				return false;
		}
	}
	return true;
}
/*
 * Get the order by items for custom search from the specified action
 */

function GetCustomSearchItems($action)
{
	global $adb;
	$result = $adb->pquery("SELECT * FROM omniscient_custom_search WHERE action = ?", array($action));
	$search_info = array();
	foreach($result AS $k => $v)
		$search_info[] = $v;

	return $result;
}

/*
 * Gets the role name based on the user's ID
 */

function GetRoleFromUserId($userid)
{
	global $adb;
	$result = $adb->pquery("SELECT r.rolename FROM vtiger_user2role ur
                            LEFT JOIN vtiger_role r ON ur.roleid = r.roleid
                            WHERE ur.userid = ?",array($userid));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "rolename");
}

/*
 * Gets all user id's from the groups belonging to the user..
 * an Idea on how to use it:
 *
 * foreach($members['users'] as $k => $v)
 *  echo "USER: {$v}<br />";
 *
 */

function GetAllUserIdsFromGroupsBelongingToUser($userid, $filter=null)
{
	global $current_user, $adb;
	$bla = new Users();
	$profile_array = getUserProfile($userid);

	if($profile_array)
		foreach($profile_array AS $k => $v)
			$profile_name[] = getProfileName($v);
//    print_r($profile);
	if(is_admin($current_user) || in_array("Support Profile", $profile_name) )
	{
		$result = $adb->pquery("SELECT id from vtiger_users", array());
		if($adb->num_rows($result) > 0)
			foreach($result AS $k => $v)
			{
				$members["users"][] = $v['id'];
			}
		return $members;
	}
	if($filter == "HelpDesk")
	{
		$groups = fetchUserGroupids($userid);
		$query = "SELECT * FROM vtiger_datashare_grp2grp g
                  LEFT JOIN vtiger_datashare_module_rel rel ON g.shareid = rel.shareid
                  WHERE rel.tabid = 13
                  AND g.to_groupid IN ({$groups})";
		$result = $adb->pquery($query, array());
		$access = array();
		foreach($result AS $k => $v)
			$access[] = $v['share_groupid'];

		$groups = SeparateArrayWithCommas($access);
		$members = getGroupMembers($groups);
	}
	else
	{
		$groups = fetchUserGroupids($userid);
		$members = getGroupMembers($groups);
	}
//    $tmp = getSharingRuleList("HelpDesk");
//    print_r($tmp);
	return $members;
}

/*
 * Get transaction info based on the portfolio id given
 */

function GetAllTransactionInfoFromPortfolioID($portfolio_id, $order_by = "trade_date", $direction = "DESC")
{
	global $adb;
	$query = "SELECT * FROM v_UserPortfolioTransactions WHERE portfolio_id = '{$portfolio_id}' ORDER BY {$order_by} {$direction}";
	$result = $adb->pquery($query, array());
//    echo $query . "<br />";
	return $result;
}

/*
 * Determines if the user can view the supplied calendar record id
 *
 */

function DoesRecordBelongToUserInGroup($user_id, $record_id)//As defined in omniscientCustom.php
{
	global $adb;
	$users = GetAllUserIdsFromGroupsBelongingToUser($user_id);
	$result = $adb->pquery("SELECT smownerid, smcreatorid FROM vtiger_crmentity WHERE crmid=?",array($record_id));
	if($adb->num_rows($result) > 0)
	{
		$owner = $adb->query_result($result, 0, "smownerid");
		$creator = $adb->query_result($result, 0, "smcreatorid");
		if(in_array($owner, $users['users']) || ($creator == $user_id))
			return 'yes';
		else
			return 'no';
	}
	else
		return 'no';
}

/*
 * Determines if the user has access to view a ticket or not.  Access is given if the ticket is about a
 * contact or account that the user owns.  Access is also given if the ticket is assigned to the user.
 */
function DoesUserHaveTicketAccess($user_id, $record_id)
{
	global $adb;

	$users = GetAllUserIdsFromGroupsBelongingToUser($user_id);
	$users = SeparateArrayWithCommas($users['users']);

	$groups = fetchUserGroupids($user_id);

	$fieldname = GetFieldNameFromFieldLabel("View Permission");
	if($groups)
		$users .= ", " . $groups;
	$query = "SELECT * FROM vtiger_troubletickets t
              LEFT JOIN vtiger_ticketcf tcf ON t.ticketid = tcf.ticketid
              LEFT JOIN vtiger_crmentity c ON c.crmid = t.parent_id
              LEFT JOIN vtiger_crmentity d ON d.crmid = t.ticketid
              WHERE t.ticketid = ? 
              AND (c.smownerid IN({$users}) 
                   OR d.smownerid IN ({$users}) 
                   OR d.smcreatorid = {$user_id}
                   OR tcf.{$fieldname} = {$user_id})";//Allow the creator access to the ticket added

	/*    echo "QUERY: {$query}<br />";
		echo "recordid: {$record_id}<br />";*/

//echo "QUERY: {$query}<br />";
	$result = $adb->pquery($query, array($record_id));
	if($adb->num_rows($result) > 0)
		return 'yes';//$ticket = $record_id;


	$ticket_ids = GetTicketIDsForGroupViaUserID();

	if(in_array($record_id, $ticket_ids))
		return "yes";

	return "no";
	/*    global $adb;

		$users = GetAllUserIdsFromGroupsBelongingToUser($current_user->id);
		$groups = fetchUserGroupids($current_user->id);
	//    $users = array_merge($users, $groups);
		$users = SeparateArrayWithCommas($users);
		if($groups)
			$users .= ", " . $groups;

		$query = "SELECT * FROM vtiger_troubletickets t
				  LEFT JOIN vtiger_crmentity c ON c.crmid = t.parent_id
				  WHERE ticketid = ? AND smownerid IN ?";
		echo "QUERY: {$query}<br />";
		echo "recordid: {$record_id}<br />";
		echo "USERS: {$users}<br />";

		$result = $adb->pquery($query, array($record_id, $users));
		if($adb->num_rows($result) > 0)
			return 'yes';//$ticket = $record_id;


	//    $ticket_ids = GetTicketIDsForGroupViaUserID();

	/*    if(in_array($record_id, $ticket_ids))
			return "yes";
	*/
	/*    return "no";

	/*    $result = $adb->pquery("SELECT smownerid FROM vtiger_crmentity WHERE crmid=?",array($record_id));
		if($adb->num_rows($result) > 0)
		{
			if($adb->query_result($result, 0, "smownerid") == $user_id)
				return "yes";
			else
			{
				$result = $adb->pquery("SELECT parent_id FROM vtiger_troubletickets WHERE ticketid=?",array($record_id));//get the contact/account the ticket belongs to
				$parent_id = $adb->query_result($result, 0, "parent_id");
				$result = $adb->pquery("SELECT smownerid FROM vtiger_crmentity WHERE crmid=?",array($parent_id));
				if($adb->query_result($result,0,"smownerid") == $user_id)
				   return 'yes';
			}
		}
		return 'yes';//TEMPORY FIX FOR MYLES PRITCHARD*/
}

/*
 * Get advisor information from trouble tickets with the given ID
 */

function GetAdvisorNameFromTicketId($ticketid)
{
	global $adb;
	$query = "SELECT e.crmid, tt.parent_id, tt.ticketid, e.smownerid, u.first_name, u.last_name, g.groupname
                            FROM vtiger_troubletickets tt
                            LEFT JOIN vtiger_crmentity e ON e.crmid = tt.parent_id
                            LEFT JOIN vtiger_users u ON u.id = e.smownerid
                            LEFT JOIN vtiger_groups g ON g.groupid = e.smownerid
                            WHERE tt.ticketid = $ticketid";
	$result = $adb->pquery($query,null);
	$fname = $adb->query_result($result, 0, "first_name");
	$lname = $adb->query_result($result, 0, "last_name");
	$group_name = $adb->query_result($result, 0, "groupname");

	$advisor = $fname . " " . $lname;
	if(strlen($advisor) <= 3)
		$advisor = $group_name;

	return $advisor;
}

/*
 * Get user's timezone because vtiger seems to be completely unreliable for some reason
 */
function GetUserTimeZone($userid)
{
	global $adb;
	$query = "SELECT time_zone FROM vtiger_users WHERE id = ?";
	$result = $adb->pquery($query, array($userid));
	return $adb->query_result($result, 0, "time_zone");
}

/*
 * Get advisor information from trouble tickets with the given ID
 */

function GetAdvisorIDFromTicketId($ticketid)
{
	global $adb;
	$query = "SELECT e.crmid, tt.parent_id, tt.ticketid, e.smownerid, u.first_name, u.last_name, u.id
                            FROM vtiger_troubletickets tt
                            LEFT JOIN vtiger_crmentity e ON (e.crmid = tt.parent_id AND tt.parent_id != 0)
                            LEFT JOIN vtiger_users u ON u.id = e.smownerid
                            WHERE tt.ticketid = $ticketid";
	$result = $adb->pquery($query,null);

////    echo "ADVISOR QUERY: {$query}<br />";
	$id = $adb->query_result($result, 0, "id");
	return $id;
}

/*
 * Set Advisors to properly show
 */
function SetAdvisors()
{
	global $adb;

	$result = $adb->pquery("SELECT fieldname FROM vtiger_field WHERE fieldlabel = 'Financial Advisor'",null);
	$fieldlabel = $adb->query_result($result, 0, "fieldname");

	$query = "SELECT ticketid FROM vtiger_troubletickets";
	$result = $adb->pquery($query, array());

	foreach($result AS $k => $v)
	{
		//    echo "Return ID: {$return_id}<br />";
		//    echo "Ticket ID: {$focus->id}<br />";
		$ticketid = $v['ticketid'];
		$name = GetAdvisorInfoFromTicketId($ticketid);
		$query = "UPDATE vtiger_ticketcf SET {$fieldlabel} = '{$name}' WHERE ticketid={$ticketid}";
//        echo "QUERY: {$query}<br />";
		$adb->pquery($query,null);
	}
}

/*
 * Get advisor information from trouble tickets with the given ID
 */

function GetAdvisorInfoFromTicketId($ticketid)
{
	global $adb, $current_user;
	/*    $result = $adb->pquery("SELECT e.crmid, tt.parent_id, tt.ticketid, e.smownerid, u.first_name, u.last_name, u.id
								FROM vtiger_troubletickets tt
								LEFT JOIN vtiger_crmentity e ON e.crmid = tt.parent_id
								LEFT JOIN vtiger_users u ON u.id = e.smownerid
								WHERE tt.ticketid = $ticketid",null);*/

	$query = "SELECT e.crmid, tt.parent_id, tt.ticketid, e.smownerid, u.first_name, u.last_name, g.groupname
              FROM vtiger_troubletickets tt
              LEFT JOIN vtiger_crmentity e ON (e.crmid = tt.parent_id AND tt.parent_id != 0)
              LEFT JOIN vtiger_users u ON u.id = e.smownerid
              LEFT JOIN vtiger_groups g ON g.groupid = e.smownerid
              WHERE tt.ticketid = $ticketid";

	$result = $adb->pquery($query, array());
	if($adb->num_rows($result) > 0)
	{
		$fname = $adb->query_result($result, 0, "first_name");
		$lname = $adb->query_result($result, 0, "last_name");
		if($fname || $lname)
		{
			$name = $lname . " " . $fname;
			return $name;
		}
		else
			$group = $adb->query_result($result, 0, "groupname");

		if($group)
			return $group;
		else
			return GetUserFirstLastNameByID($current_user->id);
	}
}


/*
 * Get all advisor information for all trouble tickets.
 */
function GetAdvisorInfoFromAllTickets()
{
	global $adb;
	$result = $adb->pquery("SELECT e.crmid, tt.parent_id, tt.ticketid, e.smownerid, u.first_name, u.last_name
                            FROM vtiger_troubletickets tt
                            LEFT JOIN vtiger_crmentity e ON e.crmid = tt.parent_id
                            LEFT JOIN vtiger_users u ON u.id = e.smownerid
                            WHERE tt.parent_id >0",null);

	return $result;
}

/*
 * Get all ticket ID's for contacts/accounts that belong to the supplied user
 *
 */

function GetTicketIdsForContactsAndAccounts($users)
{
	global $adb, $current_user;
	$ticket_ids = array();
	$groups = fetchUserGroupids($current_user->id);
//    $users = array_merge($users, $groups);
	$users = SeparateArrayWithCommas($users);
	if($groups)
		$users .= ", " . $groups;

	//First get all contacts/accounts belonging to the user
	/*    $query = "SELECT ent.*, t.* FROM vtiger_crmentity ent
									 LEFT JOIN vtiger_troubletickets t ON ((t.ticketid = ent.crmid) OR (t.parent_id = ent.crmid))
									 WHERE (setype='Contacts' OR setype='Accounts' OR setype='HelpDesk')
									 AND ent.smownerid IN({$users}) AND deleted=0 AND title!=''";
	//    echo "QUERY: {$query}<br />";
	*/
	$query = "SELECT ent.*, t.* FROM vtiger_crmentity ent
                                 LEFT JOIN vtiger_troubletickets t ON ((t.ticketid = ent.crmid) OR (t.parent_id = ent.crmid))
                                 WHERE (setype='Contacts' OR setype='Accounts' OR setype='HelpDesk') 
                                 AND ent.smownerid IN({$users}) AND deleted=0 AND title!=''";

	$customQuery = $adb->pquery($query,array());//Get all contacts/accounts that have tickets on them for the logged in user
//SELECT ent.*, t.* FROM vtiger_crmentity ent LEFT JOIN vtiger_troubletickets t ON ((t.ticketid = ent.crmid) OR (t.parent_id = ent.crmid)) WHERE (setype='Contacts' OR setype='Accounts' OR setype='HelpDesk') AND ent.smownerid IN(1186, 1186, 1274, 1281, 1290, 1334, 5328, 5347, 11845, 14974, 15368, 7740,9701) AND deleted=0 AND title!=''
	/*    $customQuery = $adb->pquery("SELECT ent.*, t.* FROM vtiger_crmentity ent
									 LEFT JOIN vtiger_troubletickets t ON t.parent_id = ent.crmid
									 WHERE (setype='Contacts' OR setype='Accounts') AND ent.smownerid={$user_id} AND deleted=0 AND title!=''",null);//Get all contacts/accounts that have tickets on them for the logged in user*/
	$counter = 0;
	$tickets = array();

	foreach($customQuery AS $k => $v)
		$ticket_ids[] = $v['ticketid'];
	/****
	 * THIS IS WHERE THE SPEED TAKES A MASSIVE HIT.  The above 2 lines were put in to simplify things as isReadPermittedBySharing causes a major speed loss due to the way
	 * it accesses the sharing file for every single ticket.  If there are still issues with service tickets, that isReadPermittedBySharing function will need to be re-written.T
	 *
	 */
	/*    for($x = 0; $x < $adb->num_rows($customQuery); $x++)
		{/**************
		 * MUCH OF THIS HAS BEEN COMMENTED OUT AS IT IS UNECESSARY FOR WHAT THIS FUNCTION DOES.
		 * IT IS BEING LEFT IN PLACE FOR FUTURE REFERENCE IN CASE IT IS NEEDED FOR ANY REASON
		 **************/
	//echo $adb->query_result($customQuery, $x, "crmid") . ", " . $adb->query_result($customQuery, $x, "title") . "-- RELATED TO: {$adb->query_result($customQuery, $x, 'parent_id')}" . "<br />";
////OLD START HERE        $ticketID = $adb->query_result($customQuery, $x, "ticketid");
//        if(!in_array($adb->query_result($customQuery, $x, ""), ))
	/*        $setype = $adb->query_result($customQuery, $x, "setype");
			$tn = $adb->query_result($customQuery, $x, "ticket_no");
			$status = $adb->query_result($customQuery, $x, "status");
			$priority = $adb->query_result($customQuery, $x, "priority");
			$crmid = $adb->query_result($customQuery, $x, "crmid");
			$pid = $adb->query_result($customQuery, $x, "parent_id");
			$fname = "";
			$lname = "";
			if($setype == "Contacts")
			{
				$tmp = $adb->pquery("SELECT firstname, lastname FROM vtiger_contactdetails WHERE contactid = {$crmid}",null);
				$fname = $adb->query_result($tmp, 0, "firstname");
				$lname = $adb->query_result($tmp, 0, "lastname");
			}
			else
			if($setype == "Accounts")
			{
				$tmp = $adb->pquery("SELECT accountname FROM vtiger_account WHERE accountid = {$crmid}",null);
				$fname = $adb->query_result($tmp, 0, "accountname");
			}
			if($title != "")
			{
				$tmp = $adb->pquery("SELECT user_name FROM vtiger_users u
									 LEFT JOIN vtiger_crmentity ent ON ent.smownerid = u.id
									 LEFT JOIN vtiger_troubletickets t ON t.ticketid = ent.crmid
									 WHERE t.ticketid = {$ticketID}",null);//Get who the ticket is assigned to
				$assigned = $adb->query_result($tmp, 0, "user_name");

				$tickets[$counter] = array("ticket_no" => $tn,
									 "title" => $title,
									 "parent_id" => $pid,
									 "status" => $status,
									 "priority" => $priority,
									 "smownerid" => $assigned,
									 "ticketid" => $ticketID);
				$counter++;
				$tmp = array("TicketNo" => $tn,
									 "title" => $title,
									 "status" => $status,
									 "priority" => $priority,
									 "setype" => $setype,
									 "crmid" => $crmid,
									 "fname" => $fname,
									 "lname" => $lname,
									 "assigned" => $assigned);
				$ticket_ids[] = $ticketID;
			}//OLD END HERE
	////        if(isReadPermittedBySharing("HelpDesk", 13, 3, $ticketID))
				$ticket_ids[] = $ticketID;*/
///UNCOMMENT THIS TOO    }
	$tmp = SeparateArrayWithCommasAndSingleQuotes($ticket_ids);
	$query = "SELECT first_name, last_name 
              FROM vtiger_users 
              WHERE id IN({$users})";
	$result = $adb->pquery($query, array());
	$user_names = array();

	if($result)
		foreach($result AS $k => $v)
			$user_names[] = $v['first_name'] . " " . $v['last_name'];

	$query = "SELECT groupname FROM vtiger_groups WHERE groupid IN({$groups})";
//    echo "THE QUERY: {$query}<br />";
	$result = $adb->pquery($query, array());

	if($result)
		foreach($result AS $k => $v)
			$user_names[] = $v['groupname'];

	$query = "SELECT vtiger_troubletickets.title, vtiger_crmentity.smownerid, vtiger_ticketcf.cf_655, vtiger_troubletickets.ticketid 
              FROM vtiger_troubletickets 
              INNER JOIN vtiger_crmentity ON vtiger_troubletickets.ticketid = vtiger_crmentity.crmid 
              LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id 
              LEFT JOIN vtiger_groups ON vtiger_crmentity.smownerid = vtiger_groups.groupid 
              INNER JOIN vtiger_ticketcf ON vtiger_troubletickets.ticketid = vtiger_ticketcf.ticketid 
              WHERE vtiger_crmentity.deleted=0 
              AND vtiger_troubletickets.ticketid > 0 
              AND vtiger_troubletickets.ticketid IN ({$tmp})";

	$result = $adb->pquery($query, array());
	$new_tickets = array();
	if($result)
		foreach($result AS $k => $v)
		{
			if(!in_array($v['cf_655'], $user_names))
			{}
			else
				$new_tickets[] = $v['ticketid'];

			//          if(in_array($v['ticketid']), $ticket_ids)

			//          echo "GET RID OF {$v['cf_655']}!...STAT!<br />";
			//echo $v['cf_655'] . "<br />";
		}
//    return $ticket_ids;//liz luna, svillar, cwong, fluna, ehorton
	return $new_tickets;
}

/*
 * Separate array elements with commas
 */

function SeparateArrayWithCommasAndSingleQuotes($theArray)
{
	$items = "";
	$firstSet = 0;
	if(!is_array($theArray))
		return;

	foreach($theArray AS $k => $v)
	{
		if(!$firstSet)
			$items = "'" . $v . "'";
		else
			$items .= ",'" . $v . "'";
		$firstSet = 1;
	}

	return $items;
}

/**
 * Take an array of values and remove all dashes, returning the dashless result
 * @param $theArray
 */
function RemoveDashes($theArray){
	if(is_array($theArray)) {
		foreach ($theArray AS $k => $v) {
			$theArray[$k] = str_replace("-", "", $v);
		}
		return $theArray;
	}else
		return str_replace("-", "", $theArray);
}


/*
 * Separate array elements with commas
 */

function SeparateArrayWithCommas($theArray)
{
	$items = "";
	$firstSet = 0;
	if(sizeof($theArray) > 1)
		foreach($theArray AS $k => $v)
		{
			if(!$firstSet)
				$items = $v;
			$items .= ", " . $v;
			$firstSet = 1;
		}
	else
		$items = $theArray[0];
	return $items;
}

/*
 * Core injection for the query generator.  This injects into the "FROM" section of the query
 */

function InjectQueryGeneratorSectionFROM($injection, $module)//Inject code into the "FROM" section of the query generator
{
	/*    $current_user = new Users();
		$current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($current_user->id);
		//print_r($user_ids);
		$ticket_ids = array();
		foreach($user_ids["users"] AS $v)
			$ticket_ids = array_merge(GetTicketIdsForContactsAndAccounts($v), $ticket_ids);

		$ticket_ids = SeparateArrayWithCommas($ticket_ids);*/

//    echo $injection . "<br /><br />";
	if($module == "HelpDesk")
		$injection = "";// OR vtiger_troubletickets.ticketid IN ({$ticket_ids}) ";

	if($module == "Documents")
		$injection .= " JOIN vtiger_notes n ON n.public = 1 ";

	return $injection;
}

/*
 * Core injection for the query generator.  This injects into the "WHERE" section of the query
 */

function InjectQueryGeneratorSectionWHERE($injection, $module)//Inject code into the "FROM" section of the query generator
{
	$current_user = new Users();
	$current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);

	if($module == "Documents")
	{
		$injection .= " OR vtiger_notes.public = 1 ";
	}

	if($module == "HelpDesk")
	{
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($current_user->id, $module);

		$ticket_ids = array();

		foreach($user_ids['users'] AS $k => $v)
			$u[] = $v;

		$u = array_unique($u);
		$u = SeparateArrayWithCommas($u);

		$groups = fetchUserGroupids($current_user->id);
		$groups = GetTicketsAssignedToGroups($groups);
		$tmp = GetTicketsCreatedByUser($u);//$current_user->id);
		$assigned = GetTicketsAssignedToUser($u);//$current_user->id);
		$alternates = GetAlternatePermissions($current_user->id);
		$advised = GetUserAdvisorTickets($u);//$current_user->id);
		$ticket_ids = array_merge(GetTicketIdsForContactsAndAccounts($user_ids["users"]), $tmp, $assigned, $alternates, $advised, $ticket_ids, $groups);

		//    $ticket_ids = array_merg($ticket_ids, $tmp);
		$ticket_ids = SeparateArrayWithCommas($ticket_ids);

		if(!$ticket_ids)
			$ticket_ids = "''";
		if($module == "HelpDesk")//If we are in the HelpDesk module, completely replace the "WHERE" section and use this instead.
			$injection = " AND vtiger_troubletickets.ticketid IN ({$ticket_ids}) ";
	}

	return $injection;
}

/**
 * Gets all tickets where the given user is the advisor
 *
 * @global type $adb
 * @param type $userid
 * @return type
 */
function GetUserAdvisorTickets($userid)
{
	global $adb;
///echo "USERID: {$userid}<br />";
	$query = "SELECT first_name, last_name FROM vtiger_users WHERE id IN ({$userid})";
	$result = $adb->pquery($query, array());
	if($result)
		foreach($result AS $k => $v)
		{
			$name[] = $v['last_name'] . " " . $v['first_name'];//$adb->query_result($result, 0, "last_name") . " " . $adb->query_result($result, 0, "first_name");
		}

	$cf = GetFieldNameFromFieldLabel("Financial Advisor");
	$names = SeparateArrayWithCommasAndSingleQuotes($name);
	$query = "SELECT ticketid FROM vtiger_ticketcf WHERE {$cf} IN ({$names})";
	$result = $adb->pquery($query, array());

	$ids = array();
	if($result)
		foreach($result AS $k => $v)
			$ids[] = $v['ticketid'];

	return $ids;
}

/*
 * Get all leads a specific user has access to.  It returns the $adb query result
 */
function GetAllLeadsForUser($userid, $name_filter="")
{
	global $adb, $current_user;

	$profiles = getUserProfile($current_user->id);
	$viewall = 0;

	if($name_filter != "")
		$filter = " AND (ld.firstname REGEXP (?) OR ld.lastname REGEXP (?)) ";
	foreach($profiles AS $k => $v)
	{
		$read_access = getProfileGlobalPermission($v);
		if($read_access[1] == 0)
			$viewall = 1;
	}

	if(is_admin($current_user) || $viewall == 1)
	{
		$query = "SELECT ld.leadid, ld.firstname, ld.lastname FROM vtiger_leaddetails ld
                  INNER JOIN vtiger_crmentity e ON ld.leadid = e.crmid
                  WHERE e.deleted = 0 AND ld.converted = 0 {$filter}";
	}
	else
	{
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($userid);
		//    $users = SeparateArrayWithCommas($user_ids['users']);
		$groups = fetchUserGroupids($userid);

		//    $users = array_merge($user_ids['users'], $groups);
		$users = SeparateArrayWithCommas($user_ids['users']);
		if($groups)
			$users .= ", " . $groups;

		$query = "SELECT ld.leadid, ld.firstname, ld.lastname FROM vtiger_leaddetails ld
                  LEFT JOIN vtiger_crmentity e ON ld.leadid = e.crmid
                  WHERE e.smownerid IN ({$users})
                  AND e.deleted = 0 AND ld.converted = 0 {$filter}";
	}
	if($filter)
		$result = $adb->pquery($query, array($name_filter, $name_filter));
	else
		$result = $adb->pquery($query, array());
	return $result;
}

/*
 * Get all contacts a specific user has access to.  It returns the $adb query result
 */
function GetAllContactsForUser($userid, $name_filter="")
{
	global $adb, $current_user;

	$profiles = getUserProfile($current_user->id);
	$viewall = 0;

	if($name_filter != "")
		$filter = " AND (cd.firstname REGEXP (?) OR cd.lastname REGEXP (?)) ";
	foreach($profiles AS $k => $v)
	{
		$read_access = getProfileGlobalPermission($v);
		if($read_access[1] == 0)
			$viewall = 1;
	}

	if(is_admin($current_user) || $viewall == 1)
	{
		$query = "SELECT cd.contactid, cd.firstname, cd.lastname FROM vtiger_contactdetails cd
                  INNER JOIN vtiger_crmentity e ON cd.contactid = e.crmid
                  WHERE e.deleted = '0' {$filter}";
	}
	else
	{
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($userid);
		//    $users = SeparateArrayWithCommas($user_ids['users']);
		$groups = fetchUserGroupids($userid);

		//    $users = array_merge($user_ids['users'], $groups);
		$users = SeparateArrayWithCommas($user_ids['users']);
		if($groups)
			$users .= ", " . $groups;

		$query = "SELECT cd.contactid, cd.firstname, cd.lastname FROM vtiger_contactdetails cd
                  LEFT JOIN vtiger_crmentity e ON cd.contactid = e.crmid
                  WHERE e.smownerid IN ({$users})
                  AND e.deleted = 0 {$filter}";
	}
	if($filter)
		$result = $adb->pquery($query, array($name_filter, $name_filter));
	else
		$result = $adb->pquery($query, array());
	return $result;
}

/*
 * Get all accounts a specific user has access to.  It returns the $adb query result
 */
function GetAllAccountsForUser($userid, $name_filter="")
{
	global $adb, $current_user;

	$profiles = getUserProfile($current_user->id);
	$viewall = 0;
	if($name_filter != "")
		$filter = " AND (a.accountname REGEXP (?)) ";
	foreach($profiles AS $k => $v)
	{
		$standard_permission = getProfileActionPermission($v);
		$read_access = getProfileGlobalPermission($v);
		if($read_access[1] == 0)// && $standard_permission[6][1] == 0)
			$viewall = 1;
	}
	if(is_admin($current_user) || $viewall == 1)
	{
		$query = "SELECT a.accountid, a.accountname FROM vtiger_crmentity e
                  LEFT JOIN vtiger_account a ON a.accountid = e.crmid
                  WHERE setype = 'Accounts'
                  AND e.deleted = 0 {$filter}";
	}
	else
	{
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($userid);
		//    $users = SeparateArrayWithCommas($user_ids['users']);
		$groups = fetchUserGroupids($userid);

		//    $users = array_merge($user_ids['users'], $groups);
		$users = SeparateArrayWithCommas($user_ids['users']);
		if($groups)
			$users .= ", " . $groups;

		$query = "SELECT a.accountid, a.accountname FROM vtiger_account a
                  LEFT JOIN vtiger_crmentity e ON a.accountid = e.crmid
                  WHERE e.deleted = 0
                  AND smownerid IN ({$users}) {$filter}";
	}
	if($filter)
		$result = $adb->pquery($query, array($name_filter));
	else
		$result = $adb->pquery($query, array());

	return $result;
}

/*
 * Get all accounts a specific user has access to.  It returns the $adb query result
 */
function GetAllCampaignsForUser($userid, $name_filter="")
{
	global $adb, $current_user;

	$profiles = getUserProfile($current_user->id);
	$viewall = 0;
	if($name_filter != "")
		$filter = " AND (c.campaignname REGEXP (?)) ";
	foreach($profiles AS $k => $v)
	{
		$standard_permission = getProfileActionPermission($v);
		$read_access = getProfileGlobalPermission($v);
		if($read_access[1] == 0)// && $standard_permission[6][1] == 0)
			$viewall = 1;
	}
	if(is_admin($current_user) || $viewall == 1)
	{
		$query = "SELECT c.campaignid, c.campaignname FROM vtiger_crmentity e
                  LEFT JOIN vtiger_campaign c ON c.campaignid = e.crmid
                  WHERE setype = 'Campaigns'
                  AND e.deleted = 0 {$filter}";
	}
	else
	{
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($userid);
		//    $users = SeparateArrayWithCommas($user_ids['users']);
		$groups = fetchUserGroupids($userid);

		//    $users = array_merge($user_ids['users'], $groups);
		$users = SeparateArrayWithCommas($user_ids['users']);
		if($groups)
			$users .= ", " . $groups;

		$query = "SELECT c.campaignid, c.campaignname FROM vtiger_campaign c
                  LEFT JOIN vtiger_crmentity e ON c.campaignid = e.crmid
                  WHERE e.deleted = 0
                  AND smownerid IN ({$users}) {$filter}";
	}
	if($filter)
		$result = $adb->pquery($query, array($name_filter));
	else
		$result = $adb->pquery($query, array());

	return $result;
}

/*
 * Get all accounts a specific user has access to.  It returns the $adb query result
 */
function GetAllPotentialsForUser($userid, $name_filter="")
{
	global $adb, $current_user;

	$profiles = getUserProfile($current_user->id);
	$viewall = 0;
	if($name_filter != "")
		$filter = " AND (p.potentialname REGEXP (?)) ";
	foreach($profiles AS $k => $v)
	{
		$standard_permission = getProfileActionPermission($v);
		$read_access = getProfileGlobalPermission($v);
		if($read_access[1] == 0)// && $standard_permission[6][1] == 0)
			$viewall = 1;
	}
	if(is_admin($current_user) || $viewall == 1)
	{
		$query = "SELECT p.potentialid, p.potentialname FROM vtiger_crmentity e
                  LEFT JOIN vtiger_potential p ON p.potentialid = e.crmid
                  WHERE setype = 'Potentials'
                  AND e.deleted = 0 {$filter}";
	}
	else
	{
		$user_ids = GetAllUserIdsFromGroupsBelongingToUser($userid);
		//    $users = SeparateArrayWithCommas($user_ids['users']);
		$groups = fetchUserGroupids($userid);

		//    $users = array_merge($user_ids['users'], $groups);
		$users = SeparateArrayWithCommas($user_ids['users']);
		if($groups)
			$users .= ", " . $groups;

		$query = "SELECT p.potentialid, p.potentialname FROM vtiger_potential p
                  LEFT JOIN vtiger_crmentity e ON p.potentialid = e.crmid
                  WHERE e.deleted = 0
                  AND smownerid IN ({$users}) {$filter}";
	}
	if($filter)
		$result = $adb->pquery($query, array($name_filter));
	else
		$result = $adb->pquery($query, array());

	return $result;
}

/*  
 * Get type from the crm
 */
function GetSettypeFromID($id)
{
	global $adb;
	$query = "SELECT setype FROM vtiger_crmentity WHERE crmid = {$id}";
	$result = $adb->pquery($query, array());
	if($result)
		foreach($result AS $k => $v)
			$type = $v['setype'];
	return $type;
}

/*
 * Get Tickets Created By User
 */
function GetTicketsCreatedByUser($userID)
{
	global $adb;
	$query = "SELECT crmid FROM vtiger_crmentity WHERE smcreatorid IN ({$userID}) AND setype='HelpDesk'";
	$result = $adb->pquery($query, array());
	$ids = array();
	if($result)
		foreach($result AS $k => $v)
			$ids[] = $v['crmid'];

	return $ids;
}

/*
 * Get Tickets where the user was added as an alternate permission
 */
function GetAlternatePermissions($userID)
{
	global $adb;
	$field = GetFieldNameFromFieldLabel("View Permission");
	$query = "SELECT ticketid FROM vtiger_ticketcf WHERE {$field} IN ({$userID})";
	$result = $adb->pquery($query, array());
	$ids = array();
	if($result)
		foreach($result AS $k => $v)
			$ids[] = $v['ticketid'];

	return $ids;
}

/*
 * Get Tickets Assigned to group
 */
function GetTicketsAssignedToGroups($groups)
{
	global $adb;
	$query = "SELECT crmid FROM vtiger_crmentity WHERE smownerid IN ({$groups}) AND setype='HelpDesk'";
	$result = $adb->pquery($query, array());
	$ids = array();
	if($result)
		foreach($result AS $k => $v)
			$ids[] = $v['crmid'];

	return $ids;
}

/*
 * Get Tickets Created By User
 */
function GetTicketsAssignedToUser($userID)
{
	global $adb;
	$query = "SELECT crmid FROM vtiger_crmentity WHERE smownerid IN ({$userID}) AND setype='HelpDesk'";
	$result = $adb->pquery($query, array());
	$ids = array();
	if($result)
		foreach($result AS $k => $v)
			$ids[] = $v['crmid'];

	return $ids;
}

/*
 * Get all ticket ID's for each contact/account belonging to users in a group
 */
function GetCommaSeparatedTicketIDsForGroupViaUserID()
{
	$current_user = new Users();
	$current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
	$user_ids = GetAllUserIdsFromGroupsBelongingToUser($current_user->id);

	$ticket_ids = array();
	/*    foreach($user_ids["users"] AS $v)
			$ticket_ids = array_merge(GetTicketIdsForContactsAndAccounts($v), $ticket_ids);*/
	$ticket_ids = array_merge(GetTicketIdsForContactsAndAccounts($user_ids["users"]), $ticket_ids);

	$ticket_ids = SeparateArrayWithCommas($ticket_ids);

	return $ticket_ids;
}

/*
 * Get all ticket ID's for each contact/account belonging to users in a group
 */
function GetTicketIDsForGroupViaUserID()
{
	$current_user = new Users();
	$current_user->retrieveCurrentUserInfoFromFile($_SESSION['authenticated_user_id']);
	$user_ids = GetAllUserIdsFromGroupsBelongingToUser($current_user->id);

	$ticket_ids = array();
	/*    foreach($user_ids["users"] AS $v)
			$ticket_ids = array_merge(GetTicketIdsForContactsAndAccounts($v), $ticket_ids);*/
	$ticket_ids = array_merge(GetTicketIdsForContactsAndAccounts($user_ids["users"]), $ticket_ids);

	return $ticket_ids;
}

function GetAccountIDFromContactID($contact_id)
{
	global $adb;
	$query = "SELECT accountid FROM vtiger_contactdetails WHERE contactid = ?";
	$result = $adb->pquery($query, array($contact_id));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, "accountid");
	else
		return 0;
}

function GetOmniContactID($contact_id)
{
	global $adb;
	$query = "SELECT * FROM vtiger_crmentity e
              LEFT JOIN vtiger_contactdetails cd ON cd.contactid = e.crmid
              LEFT JOIN vtiger_leaddetails ld ON ld.leadid = e.crmid
              WHERE e.crmid = ?";

	$result = $adb->pquery($query, array($contact_id));

	$email = $adb->query_result($result, 0, "email");

	$query = "SELECT contact_id 
              FROM contacts
              WHERE email = ?";
	$result = $adb->pquery($query, array($email));

	return $adb->query_result($result, 0, "contact_id");
}

function SaveOmniMailContact($contact_id)
{
	global $adb;

	$query = "SELECT * FROM vtiger_crmentity e
              LEFT JOIN vtiger_contactdetails cd ON cd.contactid = e.crmid
              LEFT JOIN vtiger_contactaddress ca ON ca.contactaddressid = cd.contactid
              WHERE e.crmid = ?";//Get Contact info
	$result = $adb->pquery($query, array($contact_id));

	$contact = array();
	foreach($result AS $k => $v)
		$contact = array("fname"=>$v['firstname'],
			"lname"=>$v['lastname'],
			"fullname"=>$v['firstname'] . " " . $v['lastname'],
			"email"=>$v['email'],
			"phone"=>$v['phone'],
			"street"=>$v['mailingstreet'],
			"city"=>$v['mailingcity'],
			"state"=>$v['mailingstate'],
			"zip"=>$v['mailingzip'],
			"country"=>$v['mailingcountry']);

	$query = "SELECT contact_id 
              FROM contacts 
              WHERE crmid = ?";

	$result = $adb->pquery($query, array($contact_id));
	if($adb->num_rows($result) > 0)//If the contact actually exists
	{
		$vcard  = "BEGIN:VCARD\n";
		$vcard .= "VERSION:3.0\n";
		$vcard .= "N:" . $contact['fname'] . ";" . $contact['lname'] . ";;;\n";
		$vcard .= "FN:" . $contact['fullname'] . "\n";
		$vcard .= "EMAIL;type=INTERNET;type=HOME:" . $contact['email'] . "\n";
		$vcard .= "TEL;type=home:" . $contact['phone'] . "\n";
		$vcard .= "ADR;type=home:;;" . $contact['street'] . ";" . $contact['city'] . ";" . $contact['state'] . ";";
		$vcard .= $contact['zip'] . ";" . $contact['country'] . "\n";
		$vcard .= "END:VCARD";

		$words = $contact['fullname'] . " {$contact['street']}" . " {$contact['city']}" . " {$contact['state']}" . " {$contact['phone']}";

		$query = "UPDATE contacts SET name=?, email=?, firstname=?, surname=?, vcard=?, words=? WHERE crmid=?";
		$adb->pquery($query, array($contact['fullname'], $contact['email'], $contact['fname'], $contact['lname'], $vcard, $words, $contact_id));
	}

	/*    while($r = $db->fetch_assoc($result))
		  $contacts[] = array("fname"=>$db->escapeSimple($r['firstname']),
							  "lname"=>$db->escapeSimple($r['lastname']),
							  "fullname"=>$db->escapeSimple($r['firstname']) . " " . $db->escapeSimple($r['lastname']),
							  "email"=>$db->escapeSimple($r['email']),
							  "phone"=>$db->escapeSimple($r['phone']),
							  "street"=>$db->escapeSimple($r['mailingstreet']),
							  "city"=>$db->escapeSimple($r['mailingcity']),
							  "state"=>$db->escapeSimple($r['mailingstate']),
							  "zip"=>$db->escapeSimple($r['mailingzip']),
							  "country"=>$db->escapeSimple($r['mailingcountry']));

		$contacts = array();*/
}

function GetAccountNickname($account_number){
	global $adb;
	$query = "SELECT nickname FROM vtiger_pc_account_custom WHERE account_number = ?";
	$result = $adb->pquery($query, array($account_number));
	if($adb->num_rows($result) > 0)
		$nickname = $adb->query_result($result, 0, 'nickname');
	else
		$nickname = '';
	return $nickname;
}

function GetUserIDFromEmail($email){
	global $adb;
	$query = "SELECT id FROM vtiger_users WHERE email1 = ?";
	$result = $adb->pquery($query, array($email));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, 'id');
	else
		return 0;
}

function GetSequenceNumber(){
	global $adb;
	$query = "SELECT id FROM vtiger_crmentity_seq";
	$result = $adb->pquery($query, array());
	return $adb->query_result($result, 0, 'id');
}

function GetUsernameFromEmail($email){
	global $adb;
	$query = "SELECT user_name FROM vtiger_users WHERE email1 = ?";
	$result = $adb->pquery($query, array($email));
	if($adb->num_rows($result) > 0)
		return $adb->query_result($result, 0, 'user_name');
	else
		return '';
}

function SetIsClientContacts($reset = 0){
	global $adb;
	if($reset){
		$query = "UPDATE vtiger_contactscf 
                  SET cf_712 = 0";
		$adb->pquery($query, array());
	}

	$query = "UPDATE vtiger_contactscf c
              JOIN vtiger_portfolioinformation p ON p.contact_link = c.contactid
              SET cf_712 = 1
              WHERE p.contact_link = c.contactid AND p.accountclosed = 0";
	$adb->pquery($query, array());
}

function SetIsClientHouseholds($reset = 0){
	global $adb;
	if($reset){
		$query = "UPDATE vtiger_accountscf
                  SET cf_729 = 0";
		$adb->pquery($query, array());
	}

	$query = "UPDATE vtiger_accountscf c
              JOIN vtiger_portfolioinformation p ON p.household_account = c.accountid
              SET cf_729 = 1
              WHERE p.household_account = c.accountid;";
	$adb->pquery($query, array());
}

function DropTable($table_name){
    global $adb;
    if(strpos($table_name, 'vtiger_') !== false){
        echo "Tables with vtiger_ in them are not allowed to be deleted using this function.  Just a security measure";
        exit;
    }else{
        $query = "DROP TABLE IF EXISTS {$table_name}";
        $adb->pquery($query, array());
    }
}

function GetAllActiveUserIDs(){
    global $adb;
    $query = "SELECT id FROM vtiger_users WHERE status = 'Active'";
    $result = $adb->pquery($query, array());
    $ids = array();
    if($adb->num_rows($result) > 0){
        while($x = $adb->fetchByAssoc($result)){
            $ids[] = $x['id'];
        }
        return $ids;
    }
    return 0;
}

?>