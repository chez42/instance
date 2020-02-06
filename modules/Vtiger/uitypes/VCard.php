<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
class Vtiger_VCard_UIType extends Vtiger_Base_UIType {
	/**
	 * Function to get the Template name for the current UI Type Object
	 * @return <String> - Template Name
	 */
	public function getTemplateName() {
		return 'uitypes/VCard.tpl';
	}
        
        public function getDisplayValue($value, $record = false, $recordInstance = false) {
            $request = new Vtiger_Request($_REQUEST, $_REQUEST);
            $viewer = new Vtiger_Viewer();
            $moduleName = $request->getModule();
            
            $viewer->assign("VCARD_RECORD", $record);
            $viewer->assign("VCARD_MODULE", $moduleName);
//            $viewer->assign("VCARD_RECORD", $request->get('record'));
            return $viewer->view("uitypes/VCard.tpl", "Vtiger", true);
        }
}
?>