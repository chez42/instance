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

class PositionRollup extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_positioninformation';
        var $table_index= 'positioninformationid';

        var $customFieldTable = Array('vtiger_positioninformationcf', 'positioninformationid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_positioninformation', 'vtiger_positioninformationcf');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_positioninformation' => 'positioninformationid',
                'vtiger_positioninformationcf'=>'positioninformationid');

/*var $related_module_table_index = array(
		'account_number' => array('table_name' => 'vtiger_pc_account_custom', 'table_index' => 'account_number', 'rel_index' => 'account_number'),
	);        */
        
        var $list_fields = Array (
                // Format: Field Label => Array(tablename, columnname)
                // tablename should not have prefix 'vtiger_'
                'Assigned To' => Array('crmentity','smownerid')
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
        
        static public function GetListPieFromFilterID($id){
            global $adb;
            $listViewModel = Vtiger_ListView_Model::getInstance("PositionInformation", $id);
            $generator = $listViewModel->get('query_generator');
            $query = $generator->getQuery();
            $query = strstr($query, 'FROM');
            //$query = " SELECT CASE WHEN asset_class = '' OR asset_class is null THEN 'Undefined Asset Class' ELSE asset_class END AS asset_class, SUM(current_value) as total_value " . $query;
            $query = " SELECT CASE WHEN security_type = '' OR security_type is null THEN 'Undefined Security Type' ELSE security_type END AS asset_class, SUM(current_value) as total_value " . $query;
            if(!strpos($query, "vtiger_positioninformationcf")){
                $query = str_replace("FROM vtiger_positioninformation", "FROM vtiger_positioninformation INNER JOIN vtiger_positioninformationcf ON vtiger_positioninformation.positioninformationid = vtiger_positioninformationcf.positioninformationid ", $query);
            }
            $query .= " GROUP BY CASE WHEN security_type = '' OR security_type is null THEN 'Undefined Security Type' ELSE security_type END ";

            $result = $adb->pquery($query, array());
            if($adb->num_rows($result) > 0){
                foreach($result AS $k => $v){
                    $values[$v['asset_class']] += $v['total_value'];
//2,119,299,415
//2,091,508,786.36
/*                    $values['total_value'] = money_format('%.0n',$v['total_value']);
                    $values['market_value'] = money_format('%.0n',$v['market_value']);
                    $values['cash_value'] = money_format('%.0n',$v['cash_value']);
                    $values['annual_management_fee'] = money_format('%.0n',abs($v['annual_management_fee']));*/
                }
                return $values;
            }
            return 0;
/*            
            global $current_user;

            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $moduleName = $request->getModule();
            $db = PearDatabase::getInstance();

            $query = "SELECT asset_class, SUM(current_value) as total_value";
            $q = $generator->getQuery();
            $query .= $generator->getFromClause();
            $query .= " JOIN vtiger_positioninformationcf ON vtiger_positioninformation.positioninformationid = vtiger_positioninformationcf.positioninformationid ";
            $query .= $generator->getWhereClause();
            $query .= " GROUP BY asset_class ";

            $result = $db->pquery($query, array());

            if($db->num_rows($result) > 0){
                $values['Equities'] = $db->query_result($result, 0, 'equities');
                $values['Cash'] = $db->query_result($result, 0, 'cash_value');
                $values['Fixed Income'] = $db->query_result($result, 0, 'fixed_income');
            }
            else{
                $values['Equities'] = 0;
                $values['Cash'] = 0;
                $values['Fixed Income'] = 0;
            }

            return $values;*/
        }        
}
?>