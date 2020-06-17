<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Documents_Field_Model extends Vtiger_Field_Model {

    /**
     * Function to check whether field is ajax editable'
     * @return <Boolean>
     */
    public function isAjaxEditable() {
        $ajaxRestrictedFields = array('filename', 'filelocationtype');
        if(!$this->isEditable() || in_array($this->get('name'), $ajaxRestrictedFields)) {
            return false;
        }
        return true;
    }
    
	/**
	 * Function to retieve display value for a value
	 * @param <String> $value - value which need to be converted to display value
	 * @return <String> - converted display value
	 */
	public function getDisplayValue($value, $record=false, $recordInstance = false) {
		$fieldName = $this->getName();

		if($fieldName == 'filesize' && $recordInstance) {
			$downloadType = $recordInstance->get('filelocationtype');
			if($downloadType == 'I') {
				$filesize = $value;
				if($filesize < 1024)
					$value=$filesize.' B';
				elseif($filesize > 1024 && $filesize < 1048576)
					$value=round($filesize/1024,2).' KB';
				else if($filesize > 1048576)
					$value=round($filesize/(1024*1024),2).' MB';
			} else {
				$value = ' --';
			}
			return $value;
		}

		return parent::getDisplayValue($value, $record, $recordInstance);
	}
    
    public function hasCustomLock() {
        $fieldsToLock = array('filename','notecontent','folderid','document_source','filelocationtype');
        if(in_array($this->getName(), $fieldsToLock)) {
            return true;
        }
        return false;
    }
    /*12-Sep-2018*/
    public function getFieldDataType(){
        
        $uiType = $this->get('uitype');
        
        if($uiType == '911')
            return "DocFolderPicklist";
            else
                return parent::getFieldDataType();
    }
    
    function getDocumentFolderList() {
        
        $db = PearDatabase::getInstance();
        
        $moduleName = "DocumentFolder";
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
        
        $queryGenerator->setFields( array('folder_name','id') );
        
        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
        
        $query = $queryGenerator->getQuery();
        
        $result = $db->pquery($query,array());
        
        $rows = $db->num_rows($result);
        
        $folders = array();
        
        for($i=0; $i<$rows; $i++){
            $folderId = $db->query_result($result, $i, 'documentfolderid');
            $folderName = $db->query_result($result, $i, 'folder_name');
            $folders[$folderId] = $folderName;
        }
        return $folders;
    }
    
    /*12-Sep-2018*/
    
    function getDocumentFolderWithParentList($view=false) {
        
        $db = PearDatabase::getInstance();
        
        $moduleName = "DocumentFolder";
        
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        
        $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
        
        $queryGenerator->setFields( array('folder_name','id', 'parent_id') );
        
        $listviewController = new ListViewController($db, $currentUserModel, $queryGenerator);
        
        $query = $queryGenerator->getQuery();
        
        if($view=='AddDocumentFolder')
            $query = str_replace(" OR vtiger_documentfolder.is_default=1", "", $query);
            
            
            $result = $db->pquery($query,array());
            
            $rows = $db->num_rows($result);
            
            
            
            
            $folders['xxx'] =  array("id"=>'xxx',"parent_id"=>'',"text"=>'/') ;  //27.02.2018
            
            for($i=0; $i<$rows; $i++){
                $folderId = $db->query_result($result, $i, 'documentfolderid');
                if(!array_key_exists($folderId,$folders)) {
                    $folderName = $db->query_result($result, $i, 'folder_name');
                    $parent_id = $db->query_result($result, $i, 'parent_id');
                    $folders[$folderId] =  array("id"=>$folderId,"parent_id"=>$parent_id,"text"=>$folderName);
                }
            }
            
            return $folders;
    }
    
    
    /**
     * Function to get the field details
     * @return <Array> - array of field values
     */
    public function getFieldInfo() {
        
        $this->fieldInfo = parent::getFieldInfo();
        if($this->getFieldDataType() == 'DocFolderPicklist'){
            $this->fieldInfo['picklistvalues'] = $this->getDocumentFolderList();
        }
        
        return $this->fieldInfo;
    }
    
}