<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once 'modules/OwnCloud/vendor/autoload.php';

class OwnCloud_SyncDocuments_Action extends Vtiger_Mass_Action {
    
    public function checkPermission(Vtiger_Request $request) {
       return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        global $adb;
        
        $moduleName = $request->getModule();
        
        $userName = OwnCloud_Config_Connector::$username;
        
        $password = html_entity_decode(OwnCloud_Config_Connector::$password);
        
        $url = OwnCloud_Config_Connector::$url;
        
        $settings = array(
            'baseUri' => $url . 'remote.php/webdav/',
            'userName' => $userName,
            'password' => $password
        );
      
        $documentIdsList = $this->getRecordsListFromRequest($request);
        
        $notes = $adb->pquery("SELECT vtiger_attachments.* FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_seattachmentsrel ON vtiger_seattachmentsrel.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_attachments ON vtiger_attachments.attachmentsid = vtiger_seattachmentsrel.attachmentsid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_notes.notesid IN (".implode(',',$documentIdsList).")");
        
        $folder = $request->get("own_cloud_folder");
        
        if($adb->num_rows($notes)){
            
            for($i = 0; $i < $adb->num_rows($notes); $i++){
                
                $path = $adb->query_result($notes, $i, 'path');
                
                $fileId = $adb->query_result($notes, $i, 'attachmentsid');
                
                $fileName = $adb->query_result($notes, $i, 'name');
                
                $path =  $path. $fileId.'_'.$fileName;
                
                $client = new \Sabre\DAV\Client($settings);
                
                $fh = fopen($path, "r");
                
                try {
                    $upload_result = $client->request('PUT', $folder . '/' . $fileName, $fh, array());
                } catch(Exception $e){}
                
                fclose($fh);
                
            }
        }
        
        $result = array("success" => true);
            
        $response = new Vtiger_Response();
        
        $response->setResult($result);
        
        $response->emit();
       
    }
    
    public function validateRequest(Vtiger_Request $request) {
        return true;
    }
}