<?php
class Vtiger_SaveWidgetCondition_Action extends Vtiger_Save_Action {
    
    public function process(Vtiger_Request $request) {
        global $adb;
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $cond = $request->get('cond');
        $linkId = $request->get('linkid');
        $tabId = $cond['tab'];
      
        unset($cond['tab']);
        unset($cond['undefined']);
        
        $widget = $adb->pquery("SELECT * FROM vtiger_dashboard_widget_conditions
            WHERE user_id = ? AND link_id = ? AND tab_id = ?",array($currentUser->getId(), $linkId, $tabId));
        //json_encode($cond)
        if($adb->num_rows($widget)){
            
            $adb->pquery("UPDATE vtiger_dashboard_widget_conditions SET conditions = ? WHERE user_id = ?
             AND link_id = ? AND tab_id = ?",array(json_encode($cond), $currentUser->getId(), $linkId, $tabId));
            
        }else {
            
            $adb->pquery("INSERT INTO vtiger_dashboard_widget_conditions(user_id, link_id, tab_id, conditions)
            VALUES (?, ?, ?, ?)",array($currentUser->getId(), $linkId, $tabId, json_encode($cond)));
            
        }
    }
    
}
   
