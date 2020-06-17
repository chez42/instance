<?php
class MSExchange_SaveFieldMapping_Action extends Vtiger_BasicAjax_Action{
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        $adb = PearDatabase::getInstance();
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        
        $module = $request->get("sourcemodule");
        
        $mapping = Zend_Json::encode($request->get("mapping"));
        
        $result = $adb->pquery("select * from vtiger_msexchange_fieldmapping where userid = ? and module = ?",
            array($currentUser->getId(), $module));
        
        if($adb->num_rows($result)){
            
            $adb->pquery("update vtiger_msexchange_fieldmapping set field_mapping = ? where userid = ? and module = ?",
                array($mapping, $currentUser->getId(), $module));
            
        } else {
            $adb->pquery("insert into vtiger_msexchange_fieldmapping (field_mapping,userid,module) values(?,?,?)",
                array($mapping, $currentUser->getId(), $module));
        }
        
        $response = new Vtiger_Response();
        $response->setResult(true);
        $response->emit();
    }
}