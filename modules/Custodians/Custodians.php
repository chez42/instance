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

class Custodians extends Vtiger_CRMEntity {
        var $table_name = 'vtiger_custodians';
        var $table_index= 'custodiansid';

        var $customFieldTable = Array('vtiger_custodianscf', 'custodiansid');

        var $tab_name = Array('vtiger_crmentity', 'vtiger_custodians', 'vtiger_custodianscf');

        var $tab_name_index = Array(
                'vtiger_crmentity' => 'crmid',
                'vtiger_custodians' => 'custodiansid',
                'vtiger_custodianscf'=>'custodiansid');

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
                'Assigned To' => 'assigned_user_id'
        );

        // Make the field link to detail view
        var $list_link_field = 'account_number';

        // For Popup listview and UI type support
        var $search_fields = Array(
                // Format: Field Label => Array(tablename, columnname) 
                // tablename should not have prefix 'vtiger_'
                'Account Number' => Array('custodians', 'account_number'),
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
        var $def_basicsearch_col = 'account_number';

        // Column value to use on detail view record text display
        var $def_detailview_recname = 'account_number';

        // Used when enabling/disabling the mandatory fields for the module.
        // Refers to vtiger_field.fieldname values.
        var $mandatory_fields = Array('assigned_user_id');

        var $default_order_by = 'custodian_name';
        var $default_sort_order='ASC';
}
?>