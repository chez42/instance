<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Instances_DetailView_Model extends Vtiger_DetailView_Model {

    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams) {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $recordModel = $this->getRecord();

        $linkModelList = parent::getDetailViewLinks($linkParams);

        $basicActionLink = array(
            'linktype' => 'DETAILVIEW',
            'linklabel' => 'Instance Modules List',
            'linkurl' => 'javascript:Instances_Detail_Js.triggerGetAllModules();',
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);

        $basicActionLink = array(
            'linktype' => 'DETAILVIEW',
            'linklabel' => 'Manage Rep Codes',
            'linkurl' => 'javascript:Instances_Detail_Js.triggerGetAllUsers();',
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
		
		
        $basicActionLink = array(
            'linktype' => 'DETAILVIEW',
            'linklabel' => 'Manage Instance Permissions',
            'linkurl' => 'javascript:Instances_Detail_Js.manageInstancePermissions();',
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        
		
        
        return $linkModelList;
    }
}
