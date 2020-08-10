<?php

class Office365_List_View extends Vtiger_PopupAjax_View {
        
    protected $noRecords = false;
    
    public function __construct() {
        $this->exposeMethod('Contacts');
        $this->exposeMethod('Calendar');
    }
    
    function checkPermission(Vtiger_Request $request) {
        
        parent::checkPermission($request);
       
    }
    
    function process(Vtiger_Request $request) {
        switch ($request->get('operation')) {
            case "deleteSync" :  $this->deleteSync($request);
            break;
            default: echo "Default view";
            break;
        }
    }
    
    /**
     * Sync Contacts Records
     * @return <array> Count of Contacts Records
     */
    public function Contacts($userId = false) {
           
        $syncRecords = array();
        if(!$userId){
            $user = Users_Record_Model::getCurrentUserModel();
        } else {
            $user = new Users();
            $user = $user->retrieve_entity_info($userId, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($user);
        }
        
        $controller = new Office365_Contacts_Controller($user);
        $syncDirection = Office365_Utils_Helper::getSyncDirectionForUser('Contacts');
        
        $records = array();
        $records = $controller->synchronize(true,$syncDirection[0],$syncDirection[1]);
        $syncRecords = $this->getSyncRecordsCount($records);
        $syncRecords['vtiger']['more'] = $controller->targetConnector->moreRecordsExits();
        $syncRecords['office365']['more'] = $controller->sourceConnector->moreRecordsExits();
       
        return $syncRecords;
        
    }
    
    /**
     * Sync Calendar Records
     * @return <array> Count of Calendar Records
     */
    public function Calendar($userId = false) {
        
        if(!$userId)
            $user = Users_Record_Model::getCurrentUserModel();
        else {
            $user = new Users();
            $user = $user->retrieve_entity_info($userId, 'Users');
            $user = Users_Record_Model::getInstanceFromUserObject($user);
        }
        
        $controller = new Office365_Calendar_Controller($user);
       
        $syncDirection = Office365_Utils_Helper::getSyncDirectionForUser('Calendar');
        $records = array();
        $records = $controller->synchronize(true,$syncDirection[0],$syncDirection[1]);
        $syncRecords = $this->getSyncRecordsCount($records);
        $syncRecords['vtiger']['more'] = $controller->targetConnector->moreRecordsExits();
        $syncRecords['office365']['more'] = $controller->sourceConnector->moreRecordsExits();
        return $syncRecords;
    
        return array();
    }
    
    function deleteSync($request) {
        $sourceModule = $request->get('sourcemodule');
        $userModel = Users_Record_Model::getCurrentUserModel();
        Office365_Module_Model::deleteSync($sourceModule, $userModel->getId());
    }
    
    public function getSyncRecordsCount($syncRecords) {
        $countRecords = array(	
            'vtiger'    => array('update' => 0, 'create' => 0, 'delete' => 0),
            'office365'=> array('update' => 0, 'create' => 0, 'delete' => 0)
        );
        
        foreach ($syncRecords as $key => $records) {
            if ($key == 'push') {
                $pushRecord = false;
                if (count($records) == 0) {
                    $pushRecord = true;
                }
                foreach ($records as $record) {
                    foreach ($record as $type => $data) {
                        if ($type == 'source') {
                            if ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                                $countRecords['vtiger']['update']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_CREATE_MODE) {
                                $countRecords['vtiger']['create']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                                $countRecords['vtiger']['delete']++;
                            }
                        }
                    }
                }
            } else if ($key == 'pull') {
                $pullRecord = false;
                if (count($records) == 0) {
                    $pullRecord = true;
                }
                foreach ($records as $type => $record) {
                    foreach ($record as $type => $data) {
                        if ($type == 'target') {
                            if ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_UPDATE_MODE) {
                                $countRecords['office365']['update']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_CREATE_MODE) {
                                $countRecords['office365']['create']++;
                            } elseif ($data->getMode() == WSAPP_SyncRecordModel::WSAPP_DELETE_MODE) {
                                $countRecords['office365']['delete']++;
                            }
                        }
                    }
                }
            }
        }
        
        if ($pullRecord && $pushRecord) {
            $this->noRecords = true;
        }
        return $countRecords;
    }
    
    public function validateRequest(Vtiger_Request $request) {
        //don't do validation because there is a redirection from google
    }
    
}

?>