<?php 

class Settings_Vtiger_RecalculatePermissions_Action extends Settings_Vtiger_Basic_Action {

	public function process(Vtiger_Request $request) {
	   
	    $db = PearDatabase::getInstance();
	    
	    create_tab_data_file();
	    create_parenttab_data_file();
	    
	    //require_once('modules/Users/CreateUserPrivilegeFile.php');
	    $result = $db->pquery('SELECT id FROM vtiger_users WHERE deleted = ?', array(0));
	    $numOfRows = $db->num_rows($result);
	    
	    for($i=0; $i<$numOfRows; $i++) {
	        $userId = $db->query_result($result, $i, 'id');
	        createUserPrivilegesfile($userId);
	        createUserSharingPrivilegesfile($userId);
	    }
	    
	    $responce = new Vtiger_Response();
	    $responce->setResult(array('success'=>true));
	    $responce->emit();
	}
    
    public function validateRequest(Vtiger_Request $request) {
        $request->validateWriteAccess();
    }
}