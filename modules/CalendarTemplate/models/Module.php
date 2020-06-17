<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ************************************************************************************/

class CalendarTemplate_Module_Model extends Vtiger_Module_Model {
    
    public function isQuickCreateSupported() {
        return false;
    }
    
    public function getAllTemplates(){
        
        $db = PearDatabase::getInstance();
        
        $moduleName = "CalendarTemplate";
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
        
        $queryGenerator->setFields( array('subject','id') );
        
        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
        
        $query = $queryGenerator->getQuery();
        
        $result = $db->pquery($query,array());
        
        $rows = $db->num_rows($result);
        
        $templates = array();
        
        for($i=0; $i<$rows; $i++){
            $templateId = $db->query_result($result, $i, 'calendartemplateid');
            $templateName = $db->query_result($result, $i, 'subject');
            $templates[$templateId] = $templateName;
        }
        
        return $templates;
    }
    
}
