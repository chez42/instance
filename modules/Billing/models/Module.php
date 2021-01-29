<?php

class Billing_Module_Model extends Vtiger_Module_Model
{
    
    /**
     * Function to check whether the module is an entity type module or not
     * @return <Boolean> true/false
     */
    public function isQuickCreateSupported() {
        return false;
    }
    
    public function isPermitted($actionName) {
        if($actionName == 'EditView')
            return false;
            return ($this->isActive() && Users_Privileges_Model::isPermitted($this->getName(), $actionName));
    }
    
    public function getModuleBasicLinks(){
        if(!$this->isEntityModule() && $this->getName() !== 'Users') {
            return array();
        }
        $createPermission = Users_Privileges_Model::isPermitted($this->getName(), 'CreateView');
        $moduleName = $this->getName();
        $basicLinks = array();
        $basicLinks[] = array(
            'linktype' => 'BASIC',
            'linklabel' => 'Generate Billing Statement',
            'linkurl' => 'javascript:Billing_Js.tiggerBillingSepcifications("index.php?module='.$this->getName().'&view=BillingReportPdf&mode=getPortfoilioLists");',
            'linkicon' => 'fa-plus'
        );
       
        return $basicLinks;
    }
    
    function isStarredEnabled() {
        return false;
    }
    
    function isTagsEnabled() {
        return false;
    }
    
    
}