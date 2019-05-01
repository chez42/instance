<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Connection_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function returns the url for create event
	 * @return <String>
	 */
    function getContactDetailViewUrl($parentId) {
        $data = $this->getInstanceById($this->getId());
        if($data->get('child_contact_id') != $parentId){
            $contact = $data->get('child_contact_id');
        }elseif($data->get('parent_contact_id') != $parentId){
            $contact = $data->get('parent_contact_id');
        }
       
        if($contact){
    		$ModuleModel = Vtiger_Module_Model::getInstance('Contacts');
    		return 'index.php?module='.$ModuleModel->getName().'&view='.$ModuleModel->getDetailViewName().'&record='.$contact;
        }
        return '';
    }
	

}
