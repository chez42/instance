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

class PositionInformation extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_positioninformation';
        var $table_index= 'positioninformationid';

        var $customFieldTable = Array('vtiger_positioninformationcf', 'positioninformationid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_positioninformation', 'vtiger_positioninformationcf');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_positioninformation' => 'positioninformationid',
                'vtiger_positioninformationcf'=>'positioninformationid');
                #'vtiger_pc_account_custom'=>'account_number');


/*var $related_module_table_index = array(
		'account_number' => array('table_name' => 'vtiger_pc_account_custom', 'table_index' => 'account_number', 'rel_index' => 'account_number'),
	);        */

        var $list_fields = Array (
                // Format: Field Label => Array(tablename, columnname)
                // tablename should not have prefix 'vtiger_'
                'Summary' => Array('positioninformation', 'summary'),
                'Assigned To' => Array('crmentity','smownerid'),
        );
        var $list_fields_name = Array (
                // Format: Field Label => fieldname
                'Account Number' => 'account_number',
                'Assigned To' => 'assigned_user_id',
        );

        // Make the field link to detail view
        var $list_link_field = 'account_number';

        // For Popup listview and UI type support
        var $search_fields = Array(
                // Format: Field Label => Array(tablename, columnname) 
                // tablename should not have prefix 'vtiger_'
                'Account Number' => Array('positioninformation', 'account_number'),
                'Assigned To' => Array('vtiger_crmentity','assigned_user_id'),
        );
        var $search_fields_name = Array (
                // Format: Field Label => fieldname 
                'Account Number' => 'account_number',
                'Assigned To' => 'assigned_user_id',
        );

        // For Popup window record selection
        var $popup_fields = Array ('account_number');

        // For Alphabetical search
        var $def_basicsearch_col = 'security_symbol';

        // Column value to use on detail view record text display
        var $def_detailview_recname = 'account_number';

        // Used when enabling/disabling the mandatory fields for the module.
        // Refers to vtiger_field.fieldname values.
        var $mandatory_fields = Array('account_number','assigned_user_id');
        
        var $default_order_by = 'security_symbol';
        var $default_sort_order='ASC';


    function get_transactions($id, $cur_tab_id, $rel_tab_id, $actions = false){

        global $currentModule, $app_strings, $singlepane_view;

        $account_number = getSingleFieldValue("vtiger_positioninformation", "account_number", "positioninformationid", $id);
        $security_symbol = getSingleFieldValue("vtiger_positioninformation", "security_symbol", "positioninformationid", $id);

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

        $query = "SELECT vtiger_transactions.*, vtiger_crmentity.crmid, vtiger_crmentity.smownerid, vtiger_positioninformation.*,
		case when (vtiger_users.user_name not like '') then $userNameSql else vtiger_groups.groupname end as user_name
		FROM vtiger_transactions 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_transactions.transactionsid
		INNER JOIN vtiger_transactionscf ON vtiger_transactionscf.transactionsid = vtiger_transactions.transactionsid
		INNER JOIN vtiger_positioninformation ON vtiger_positioninformation.account_number = vtiger_transactions.account_number
		LEFT JOIN vtiger_groups	ON vtiger_groups.groupid = vtiger_crmentity.smownerid
		LEFT JOIN vtiger_users ON vtiger_crmentity.smownerid = vtiger_users.id
		WHERE vtiger_crmentity.deleted = 0 
		AND vtiger_transactions.security_symbol = '".$security_symbol."'
		AND vtiger_transactions.account_number = '".$account_number."'";

        $return_value = GetRelatedList($currentModule, $related_module, $other, $query, $button, $returnset);

        if ($return_value == null)
            $return_value = Array();
        $return_value['CUSTOM_BUTTON'] = $button;

        return $return_value;
    }
}
?>