<?php

class Settings_GlobalSearch_GetSearchData_Action extends Settings_Vtiger_Basic_Action {
    
    public function process(Vtiger_Request $request) {
        
    	$selectedModule = $request->get('selected_module');
        
    	$fieldList = array();
    	$alreadySavedData = array();
    	
    	if($selectedModule != ''){
    	    
	        $recordModel = Vtiger_Record_Model::getCleanInstance($selectedModule);			
	        $allFieldList = $recordModel->getModule()->getFields();
			if( !empty($allFieldList) ){
				foreach($allFieldList as $fieldname => $fieldModel){
					$fieldList[$fieldname] = $fieldModel->get('label');
				}
			}
			
			$searchRecordModel = Settings_GlobalSearch_Record_Model::getInstance($selectedModule);
			if(!empty($searchRecordModel)){
				$recordData = $searchRecordModel->getData();
				$alreadySavedData['fieldnames'] = explode(',', $recordData['fieldnames']);
				$alreadySavedData['allow_global_search'] = $recordData['allow_global_search'];
				$alreadySavedData['fieldshow'] = explode(',', $recordData['fieldname_show']);
			}
			
			$moduleModel = Vtiger_Module_Model::getInstance($selectedModule);
			$recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
			$recordStructure = $recordStructureInstance->getStructure();
			
			if (in_array($selectedModule, getInventoryModules())) {
			    $itemsBlock = "LBL_ITEM_DETAILS";
			    unset($recordStructure[$itemsBlock]);
			}
			$selectedFields = '';
			
			foreach($recordStructure as $BLOCK_LABEL => $BLOCK_FIELDS ){
			    $selectedFields .= '<optgroup label="'.vtranslate($BLOCK_LABEL, $selectedModule).'">';
			    foreach($BLOCK_FIELDS as $FIELD_NAME => $FIELD_MODEL){
			        $selectedFields .= '<option value="'.$FIELD_MODEL->getCustomViewColumnName().'" data-field-name="'.$FIELD_NAME.'"';
			        if (in_array($FIELD_MODEL->getCustomViewColumnName(), $alreadySavedData['fieldnames'])){
			            $selectedFields .= 'selected';
			        }
			        $selectedFields .= '>'.vtranslate($FIELD_MODEL->get('label'), $selectedModule);
			        $selectedFields .= '</option>';
			    }
			    $selectedFields .= '</optgroup>';
			}
			
			if ($selectedModule == "Calendar") {
			    $selectedFields = '';
			    $relatedModuleName = "Events";
			    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
			    $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
			    $eventBlocksFields = $relatedRecordStructureInstance->getStructure();
			    
			    foreach($eventBlocksFields as $BLOCK_LABEL => $BLOCK_FIELDS){
			        $selectedFields .='<optgroup label="'.vtranslate($BLOCK_LABEL, 'Events').'">';
			        foreach($BLOCK_FIELDS as $FIELD_NAME => $FIELD_MODEL){
			            $selectedFields .= '<option value="'.$FIELD_MODEL->getCustomViewColumnName().'" data-field-name="'.$FIELD_NAME.'"';
			            if (in_array($FIELD_MODEL->getCustomViewColumnName(), $alreadySavedData['fieldnames'])){
			                $selectedFields .= 'selected';
			            }
			            $selectedFields .= '>'.vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE);
			            $selectedFields .= '</option>';
			        }
			        $selectedFields .= '</optgroup>';
			    }
			}
			
			$showFields = '';
			
			foreach($recordStructure as $BLOCK_LABEL => $BLOCK_FIELDS ){
			    $showFields .= '<optgroup label="'.vtranslate($BLOCK_LABEL, $selectedModule).'">';
			    foreach($BLOCK_FIELDS as $FIELD_NAME => $FIELD_MODEL){
			        $showFields .= '<option value="'.$FIELD_MODEL->getCustomViewColumnName().'" data-field-name="'.$FIELD_NAME.'"';
			        if (in_array($FIELD_MODEL->getCustomViewColumnName(), $alreadySavedData['fieldshow'])){
			            $showFields .= 'selected';
			        }
			        $showFields .= '>'.vtranslate($FIELD_MODEL->get('label'), $selectedModule);
			        $showFields .= '</option>';
			    }
			    $showFields .= '</optgroup>';
			}
			
			if ($selectedModule == "Calendar") {
			    $showFields = '';
			    $relatedModuleName = "Events";
			    $relatedModuleModel = Vtiger_Module_Model::getInstance($relatedModuleName);
			    $relatedRecordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($relatedModuleModel);
			    $eventBlocksFields = $relatedRecordStructureInstance->getStructure();
			    
			    foreach($eventBlocksFields as $BLOCK_LABEL => $BLOCK_FIELDS){
			        $showFields .='<optgroup label="'.vtranslate($BLOCK_LABEL, 'Events').'">';
			        foreach($BLOCK_FIELDS as $FIELD_NAME => $FIELD_MODEL){
			            $showFields .= '<option value="'.$FIELD_MODEL->getCustomViewColumnName().'" data-field-name="'.$FIELD_NAME.'"';
			            if (in_array($FIELD_MODEL->getCustomViewColumnName(), $alreadySavedData['fieldshow'])){
			                $showFields .= 'selected';
			            }
			            $showFields .= '>'.vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE);
			            $showFields .= '</option>';
			        }
			        $showFields .= '</optgroup>';
			    }
			}
    	}
    	
    	$response = new Vtiger_Response();
        try{            
            $response->setResult( array('all_fields' => $fieldList, 'savedData' => $alreadySavedData, 'selectedFields' => $selectedFields, 'showFields' => $showFields) );
        }catch (Exception $e) {
            $response->setError($e->getCode(), $e->getMessage());
        }
        $response->emit();
    }
    
    public function validateRequest(Vtiger_Request $request) {
        //$request->validateWriteAccess();
    }
    
}