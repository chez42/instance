<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/modules/Contacts/Contacts.php,v 1.70 2005/04/27 11:21:49 rank Exp $
 * Description:  TODO: To be written.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 ********************************************************************************/

include_once 'modules/Vtiger/CRMEntity.php';

class PortfolioInformation extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_portfolioinformation';
        var $table_index= 'portfolioinformationid';

        var $customFieldTable = Array('vtiger_portfolioinformationcf', 'portfolioinformationid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_portfolioinformation', 'vtiger_portfolioinformationcf', 'vtiger_pc_account_custom');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_portfolioinformation' => 'portfolioinformationid',
                'vtiger_portfolioinformationcf'=>'portfolioinformationid',
                'vtiger_contactdetails' => 'contactid',
                'vtiger_pc_account_custom'=>'account_number');

/*var $related_module_table_index = array(
		'account_number' => array('table_name' => 'vtiger_pc_account_custom', 'table_index' => 'account_number', 'rel_index' => 'account_number'),
	);        */
        
        var $list_fields = Array (
                // Format: Field Label => Array(tablename, columnname)
                // tablename should not have prefix 'vtiger_'
                'Summary' => Array('portfolioinformation', 'summary'),
                'Contacts' => Array('contactdetails', 'contact_link'),
                'Assigned To' => Array('crmentity','smownerid'),
                'Nickname' => Array('pc_account_custom','nickname')
        );
        
        var $list_fields_name = Array (
                // Format: Field Label => fieldname
                'Account Number' => 'account_number',
                'Assigned To' => 'assigned_user_id',
                'Nickname' => 'account_number'
        );

        // Make the field link to detail view
        var $list_link_field = 'account_number';

        // For Popup listview and UI type support
        var $search_fields = Array(
                // Format: Field Label => Array(tablename, columnname) 
                // tablename should not have prefix 'vtiger_'
                'Account Number' => Array('portfolioinformation', 'account_number'),
                'Assigned To' => Array('crmentity','assigned_user_id'),
                'Contact' => Array('contactdetails', 'contact_link'),
                'Nickname' => Array('pc_account_custom', 'nickname')
        );
        var $search_fields_name = Array (
                // Format: Field Label => fieldname 
                'Account Number' => 'account_number',
                'Assigned To' => 'assigned_user_id',
        );

        // For Popup window record selection
        var $popup_fields = Array ('account_number');

        // For Alphabetical search
        var $def_basicsearch_col = 'household_account';

        // Column value to use on detail view record text display
        var $def_detailview_recname = 'account_number';

        // Used when enabling/disabling the mandatory fields for the module.
        // Refers to vtiger_field.fieldname values.
        var $mandatory_fields = Array('account_number','assigned_user_id');

        var $default_order_by = 'account_number';
        var $default_sort_order='ASC';

        /* ===== START : Felipe Project Run Changes ===== */
        
	function PortfolioInformation() {
		$this->log =LoggerManager::getLogger('PortfolioInformation');
		$this->log->debug("Entering PortfolioInformation() method ...");
		$this->db = PearDatabase::getInstance();
		$this->column_fields = getColumnFields('PortfolioInformation');
		$this->log->debug("Exiting PortfolioInformation() method ...");
	}

	function save_module($module)
	{
	}
	
	/* ===== END : Felipe Project Run Changes ===== */
	
	/**
	 *
	 * @param String $tableName
	 * @return String
	 */
	public function getJoinClause($tableName) {
        if($tableName == "vtiger_pc_account_custom")
            return 'LEFT JOIN';
		return parent::getJoinClause($tableName);
	}
        
        static public function GetPortfolioInformationRecordIDFromAccountNumber($account_number){
            global $adb;
            $query = "SELECT portfolioinformationid FROM vtiger_portfolioinformation WHERE account_number = ?";
            $result = $adb->pquery($query, array($account_number));
            if($adb->num_rows($result) > 0){
                return $adb->query_result($result, 0, 'portfolioinformationid');
            }
            return 0;
        }
        
        static public function GetChartColorForTitle($title){
            global $adb;
            $query = "SELECT color FROM vtiger_chart_colors WHERE title = ?";
            $result = $adb->pquery($query, array($title));
            if($adb->num_rows($result) > 0){
                return $adb->query_result($result, 0, 'color');
            }
            return 0;
        }
        
	function get_transactions($id, $cur_tab_id, $rel_tab_id, $actions = false){
		
		global $currentModule, $app_strings, $singlepane_view;

		$account_number = getSingleFieldValue("vtiger_portfolioinformation", "account_number", "portfolioinformationid", $id);
		
		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($currentModule, $this);
		vtlib_setup_modulevars($related_module, $other);

		$singular_modname = 'SINGLE_' . $related_module;

		$button = '';
		if ($actions) {
			if (is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " .
						" type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
						" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true')
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		else
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name',
														'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT vtiger_transactions.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_portfolioinformation.*,
		case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
		FROM vtiger_transactions 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_transactions.transactionsid
		INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid
		INNER JOIN vtiger_portfolioinformation ON vtiger_portfolioinformation.account_number = vtiger_transactions.account_number
		LEFT JOIN vtiger_groups	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_transactions.account_number = '".$account_number."'";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null)
			$return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		return $return_value;
	}
	
		
	function get_positions($id, $cur_tab_id, $rel_tab_id, $actions = false){

		global $currentModule, $app_strings, $singlepane_view;

		$adb = PearDatabase::getInstance();
		
		$account_number = getSingleFieldValue("vtiger_portfolioinformation", "account_number", "portfolioinformationid", $id);
		
		if(strpos($account_number, "-") !== false)
			$account_number = "'".$account_number."', '".str_replace('-', "", $account_number)."'";
		else
			$account_number = "'".$account_number."'";
		
		$parenttab = getParentTab();

		$related_module = vtlib_getModuleNameById($rel_tab_id);
		$other = CRMEntity::getInstance($related_module);

		// Some standard module class doesn't have required variables
		// that are used in the query, they are defined in this generic API
		vtlib_setup_modulevars($currentModule, $this);
		vtlib_setup_modulevars($related_module, $other);

		$singular_modname = 'SINGLE_' . $related_module;

		$button = '';
		if ($actions) {
			if (is_string($actions))
				$actions = explode(',', strtoupper($actions));
			if (in_array('SELECT', $actions) && isPermitted($related_module, 4, '') == 'yes') {
				$button .= "<input title='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module) . "' class='crmbutton small edit' " .
						" type='button' onclick=\"return window.open('index.php?module=$related_module&return_module=$currentModule&action=Popup&popuptype=detailview&select=enable&form=EditView&form_submit=false&recordid=$id&parenttab=$parenttab','test','width=640,height=602,resizable=0,scrollbars=0');\"" .
						" value='" . getTranslatedString('LBL_SELECT') . " " . getTranslatedString($related_module, $related_module) . "'>&nbsp;";
			}
			if (in_array('ADD', $actions) && isPermitted($related_module, 1, '') == 'yes') {
				$button .= "<input type='hidden' name='createmode' id='createmode' value='link' />" .
						"<input title='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname) . "' class='crmbutton small create'" .
						" onclick='this.form.action.value=\"EditView\";this.form.module.value=\"$related_module\"' type='submit' name='button'" .
						" value='" . getTranslatedString('LBL_ADD_NEW') . " " . getTranslatedString($singular_modname, $related_module) . "'>&nbsp;";
			}
		}

		// To make the edit or del link actions to return back to same view.
		if ($singlepane_view == 'true')
			$returnset = "&return_module=$currentModule&return_action=DetailView&return_id=$id";
		else
			$returnset = "&return_module=$currentModule&return_action=CallRelatedList&return_id=$id";

		$userNameSql = getSqlForNameInDisplayFormat(array('first_name'=>'vtiger_users.first_name',
														'last_name' => 'vtiger_users.last_name'), 'Users');

		$query = "SELECT vtiger_positioninformation.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_portfolioinformation.*,
		case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
		FROM vtiger_positioninformation 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_positioninformation.positioninformationid
		INNER JOIN vtiger_positioninformationcf ON vtiger_positioninformationcf.positioninformationid = vtiger_positioninformation.positioninformationid
		LEFT JOIN vtiger_groups	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
		LEFT JOIN vtiger_modsecurities ON vtiger_positioninformation.security_symbol = vtiger_modsecurities.security_symbol
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_positioninformation.account_number IN (".$account_number.")
		AND vtiger_positioninformation.quantity != 0 ";

		$return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

		if ($return_value == null)
			$return_value = Array();
		$return_value['CUSTOM_BUTTON'] = $button;

		$query = "SET @global_total = (SELECT SUM(current_value) FROM vtiger_positioninformation WHERE account_number IN ($account_number))";
        
		$adb->pquery($query, array());
		
		return $return_value;
	}
}
?>