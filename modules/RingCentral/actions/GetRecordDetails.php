<?php

class RingCentral_GetRecordDetails_Action extends Vtiger_Mass_Action {
    
    function __construct() {
        parent::__construct();
        $this->exposeMethod('fetchDetails');
        $this->exposeMethod('addComment');
        $this->exposeMethod('getDetailsFromNo');
    }
    
    function checkPermission(Vtiger_Request $request) {
        return true;
    }
    
    public function validateRequest(Vtiger_Request $request) {
       return true;
    }
    
    public function process(Vtiger_Request $request) {
        
        $mode = $request->getMode();
        if(!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
        }
        
    }
    
    public function fetchDetails(Vtiger_Request $request){
        
        $recordId = $request->get('record');
        
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
        
        $MODULE_MODEL = Vtiger_Module_Model::getInstance(getSalesEntityType($recordId));
        
        $fieldData = array();
        
        $FIELDS_MODELS_LIST = $MODULE_MODEL->getFields();
        
        foreach($FIELDS_MODELS_LIST as $FIELD_MODEL){
            $FIELD_DATA_TYPE = $FIELD_MODEL->getFieldDataType();
            $FIELD_NAME = $FIELD_MODEL->getName();
            if($FIELD_MODEL->isHeaderField() && $FIELD_MODEL->isActiveField() && $recordModel->get($FIELD_NAME) && $FIELD_MODEL->isViewable()){
                $fieldData[$FIELD_MODEL->get('label')] = $recordModel->get($FIELD_NAME);
            }
        }
        $imagePath = '';
        
        $imagedetails = $recordModel->getImageDetails();
        foreach($imagedetails as $key => $image){
            if(!empty($image['path']) && !empty($image['orgname'])){
                $imagePath = $image['path'].'_'.$image['orgname'];
            }
        }
        
        $fullname = $recordModel->get('label');
        $lastname = $recordModel->get('lastname');
        $firstname = $recordModel->get('firstname');
       
        $result = array('fields'=>$fieldData,'imagepath'=>$imagePath,'fullname'=>$fullname,'firstname'=>$firstname,'lastname'=>$lastname);
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function addComment(Vtiger_Request $request){
        
        $recordId = $request->get('record');
        $comment = $request->get('comment');
        
        $modCommentsModel = Vtiger_Record_Model::getCleanInstance('ModComments');
        $modCommentsModel->set('commentcontent',$comment);
        $modCommentsModel->set('related_to', $recordId);
        $modCommentsModel->save();
        
        $result = array('success'=>true);
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
    public function getDetailsFromNo(Vtiger_Request $request){
        
        global $adb;
        
        $result = array();
        
        $no = $request->get('mobno');
        
        $records = PBXManager_Record_Model::lookUpRelatedWithNumber($no);
        
        $recordId = $records['id'];
        
        if($recordId){
            
            $recordModel = Vtiger_Record_Model::getInstanceById($recordId);
            
            $MODULE_MODEL = Vtiger_Module_Model::getInstance(getSalesEntityType($recordId));
            
            $fieldData = array();
            
            $FIELDS_MODELS_LIST = $MODULE_MODEL->getFields();
            
            foreach($FIELDS_MODELS_LIST as $FIELD_MODEL){
                $FIELD_DATA_TYPE = $FIELD_MODEL->getFieldDataType();
                $FIELD_NAME = $FIELD_MODEL->getName();
                if($FIELD_MODEL->isHeaderField() && $FIELD_MODEL->isActiveField() && $recordModel->get($FIELD_NAME) && $FIELD_MODEL->isViewable()){
                    $fieldData[$FIELD_MODEL->get('label')] = $recordModel->get($FIELD_NAME);
                }
            }
            $imagePath = '';
            
            $imagedetails = $recordModel->getImageDetails();
            foreach($imagedetails as $key => $image){
                if(!empty($image['path']) && !empty($image['orgname'])){
                    $imagePath = $image['path'].'_'.$image['orgname'];
                }
            }
            
            $fullname = $recordModel->get('label');
            $lastname = $recordModel->get('lastname');
            $firstname = $recordModel->get('firstname');
            
            $result = array('fields'=>$fieldData,'imagepath'=>$imagePath,'fullname'=>$fullname,'firstname'=>$firstname,'lastname'=>$lastname);
            
        }
        
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
        
    }
    
}