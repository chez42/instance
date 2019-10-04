<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_DetailView_Model extends Vtiger_DetailView_Model
{
    /**
     * Function to get the detail view widgets
     * @return <Array> - List of widgets , where each widget is an Vtiger_Link_Model
     */
    public function getWidgets()
    {
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $widgetLinks = parent::getWidgets();
        $widgets = array();
        $documentsInstance = Vtiger_Module_Model::getInstance("Documents");
        if ($userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), "DetailView")) {
            $createPermission = $userPrivilegesModel->hasModuleActionPermission($documentsInstance->getId(), "EditView");
            $widgets[] = array("linktype" => "DETAILVIEWWIDGET", "linklabel" => "Documents", "linkName" => $documentsInstance->getName(), "linkurl" => "module=" . $this->getModuleName() . "&view=Detail&record=" . $this->getRecord()->getId() . "&relatedModule=Documents&mode=showRelatedRecords&page=1&limit=5", "action" => $createPermission == true ? array("Add") : array(), "actionURL" => $documentsInstance->getQuickCreateUrl());
        }
        foreach ($widgets as $widgetDetails) {
            $widgetLinks[] = Vtiger_Link_Model::getInstanceFromValues($widgetDetails);
        }
        return $widgetLinks;
    }
}

?>