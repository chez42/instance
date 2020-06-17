<?php
class DocumentFolder_Record_Model extends Vtiger_Record_Model {

	/**
	 * Function to get the description of the folder
	 * @return <String>
	 */
	function getDescription() {
		return $this->get('description');
	}
	
	public function getName(){
		return $this->get('folder_name');
	}
	
	/**
	 * Function to check duplicate exists or not
	 * @return <boolean>
	 */
	public function checkDuplicate() {
		
		$db = PearDatabase::getInstance();

		$query = "SELECT vtiger_documentfolder.documentfolderid FROM vtiger_documentfolder 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid 
		WHERE vtiger_documentfolder.folder_name = ? AND vtiger_crmentity.deleted = 0";
        $params = array(decode_html($this->get('folder_name'))); 

		$record = $this->getId();
		
		if ($record) {
			$query .= " AND crmid != ?";
			array_push($params, $record);
		}

		$result = $db->pquery($query, $params);
		if ($db->num_rows($result)) {
			return true;
		}
		return false;
	}

	/**
	 * Function to get info array while saving a folder
	 * @return Array  info array
	 */
	public function getInfoArray() {
		return array('folderName' => $this->getName(),'folderid' => $this->getId());
	}
	
	/**
	 * Function returns whether documents are exist or not in that folder
	 * @return true if exists else false
	 */
	public function hasDocuments() {
		$db = PearDatabase::getInstance();
		$folderId = $this->getId();

		$result = $db->pquery("SELECT 1 FROM vtiger_notes
						INNER JOIN vtiger_documentfolder ON vtiger_documentfolder.documentfolderid = vtiger_notes.doc_folder_id
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
						WHERE vtiger_documentfolder.documentfolderid = ?
						AND vtiger_documentfolder.folder_name != 'Default'
						AND vtiger_crmentity.deleted = 0", array($folderId));
		$num_rows = $db->num_rows($result);
		if ($num_rows>0) {
			return true;
		}
		return false;
	}
	
	
	public function folderViewPermissions($folder_id,$mode=false){
	    
	    global $adb,$current_user;
	    
	    $folderView = $adb->pquery("SELECT view_permission FROM vtiger_documentfolder 
        WHERE documentfolderid = ?",array($folder_id));
	    
	    $users = '';
	    $view_permissions = '';
	    
	    if($adb->num_rows($folderView)){
	    
	        $view_permissions = explode(' |##| ', $adb->query_result($folderView,0,'view_permission'));
            
	        if(!$mode)
                return $view_permissions;
	        
	        foreach($view_permissions as $key => $viewPermission){
	            if($viewPermission != $current_user->id){
    	            if($key > 0){
    	                $users .= ', ';
    	            }
    	            
    	            $users .= getUserFullName($viewPermission);
	           }
	        }
	        
	    }
	    
	    if($mode == 'name')
            return $users;
	    
	}
	
	public function folderHidePortal($folder_id){
	    
	    global $adb;
	    
	    $folderView = $adb->pquery("SELECT hide_from_portal FROM vtiger_documentfolder
        WHERE documentfolderid = ?",array($folder_id));
	    
	    $portal = '';
	    
	    if($adb->num_rows($folderView)){
	        
	        $portal = $adb->query_result($folderView,0,'hide_from_portal');
	            
	    }
	    
	    return $portal;
	        
	}
	
	public function folderForAllUsers($folder_id){
	    
	    global $adb;
	    
	    $folderView = $adb->pquery("SELECT default_for_all_users FROM vtiger_documentfolder
        WHERE documentfolderid = ?",array($folder_id));
	    
	    $portal = '';
	    
	    if($adb->num_rows($folderView)){
	        
	        $portal = $adb->query_result($folderView,0,'default_for_all_users');
	        
	    }
	    
	    return $portal;
	    
	}

}