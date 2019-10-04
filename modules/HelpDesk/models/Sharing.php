<?php

class HelpDesk_Sharing_Model extends Vtiger_Base_Model{
    
	public $shared_tickets;
    
    public function __construct() {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        
        if($currentUserModel->isAdminUser())
            return;
        $shared_tickets = array();
        $questions = array();
        
        require('user_privileges/'.$GLOBALS['tenantGUID'].'/sharing_privileges_'.$currentUserModel->get('id').'.php');

        foreach($HelpDesk_share_read_permission['GROUP'] AS $groups => $users){
            $related_ids[] = $groups;
        }
        $related_ids[] = $currentUserModel->get('id');//Always at least give the current user ID
        $questions = generateQuestionMarks($related_ids);

        $query = "SELECT ticketid "
               . "FROM vtiger_troubletickets tt "
               . "LEFT JOIN vtiger_crmentity e ON e.crmid = tt.ticketid "
               . "WHERE view_permission IN ({$questions}) ";
               
        $result = $db->pquery($query, array($related_ids));
        if (is_object($result)){
            foreach($result AS $k => $v){
                $shared_tickets[] = $v['ticketid'];
            }
            $this->shared_tickets = $shared_tickets;
        }
        else{
            $this->shared_tickets = null;
        }
    }
    
    public function GetSharedTickets(){
        return $this->shared_tickets;
    }

    public function IsUserPartOfSharingPermission($record_id){
        
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
        
		$hd = HelpDesk_Record_Model::getInstanceById($record_id);
        
		$groups = $currentUserModel->getUserGroups($currentUserModel->getId());
        
		$idlist[] = $currentUserModel->getId();
        
		foreach($groups AS $k => $v) {
            $idlist[] = $v;
        }

        
        $permissionIds = explode(" |##| ",$hd->get('view_permission'));
        
        foreach($permissionIds as $user_permission_id){
        	if(in_array($user_permission_id, $idlist))
            	return true;
        }

        return false;
    }

    public function GetTicketsRelatingToParents(){}

    /**
     * 	Check get the ID's of the users groups and users within those groups 
	 *	to determine if they have permission to the given ticket
     */ 
    
	public function DoesTicketBelongToUsersGroup($record_id){
		global $adb;
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $groups = $currentUserModel->getUserGroups($currentUserModel->getId());
        $idlist[]=$currentUserModel->getId();
        try {
            foreach ($groups AS $k => $v) {
                $idlist[] = $v;
                $users = vtws_getUsersInTheSameGroup($currentUserModel->getId());
                foreach ($users AS $a => $b) {
                    $idlist[] = $a;
                }
            }

            $ticket = HelpDesk_Record_Model::getInstanceById($record_id);
            $data = $ticket->getData();
            if(in_array($data['assigned_user_id'], $idlist))
                return true;
                
                if($data['parent_id']){
                    $result = $adb->pquery("select * from vtiger_crmentity 
					where crmid = ? and deleted = 0", array($data['parent_id']));
					if($adb->num_rows($result)){
						$record = Vtiger_Record_Model::getInstanceById(($data['parent_id']));
						$parentdata = $record->getData();
						if(in_array($parentdata['assigned_user_id'], $idlist))
							return true;
					}
                }
                
        } catch(Exception $e){
            return false;
        }

        return false;
    }

    /**
     * Check if a user is permitted to access the given record
     * @param type $record_id
     * @return boolean
     */
    public function IsUserPermitted($record_id){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();
        
        if($currentUserModel->isAdminUser())
            return true;
        
        if(in_array($record_id, $this->shared_tickets))
            return true;
        else
            return false;

    }
    
}
