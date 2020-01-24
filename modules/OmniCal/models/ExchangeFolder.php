<?php

class OmniCal_ExchangeFolder_Model extends OmniCal_ExchangeEws_Model{
    public $folder_info;
    
    public function __construct($server = 'lanserver33', $user = 'concertglobal\concertadmin', $password = 'Consec1', $exchange_version = 'Exchange2007_SP1') {
        parent::__construct($server, $user, $password, $exchange_version);
    }
    
    /**
     * Get contact in from exchange.  If no contact id is specified, it will return all Tasks
     * @param type $contact_id
     * @return string
     */
    public function GetFolderInfoFromExchange($folder_id=''){
        if(!isset($this->sid->PrimarySmtpAddress))
            return 'Impersonation needs to be set';
        
        if(strlen($folder_id) > 0)
            return $this->GetIndividualFolderFromExchange($folder_id);
        else
            return $this->GetAllFoldersFromExchangeRaw();
    }
    
    private function GetIndividualFolderFromExchange($contact_id){

    }
    
    /**
     * Get all exchange sync info for the given user
     * @global type $adb
     * @param type $user_id
     * @return int
     */
    private function GetExchangeSyncInfo($user_id){
        global $adb;
        $query = "SELECT * FROM exchange_sync WHERE exchange_sync_user_id = ?";
        $result = $adb->pquery($query, array($user_id));
        $info = array();
        if($adb->num_rows($result) > 0){
            foreach($result AS $k => $v){
                $info[] = array ('sync_type'=> $v['exchange_sync_type'],
                                 'sync_state' => $v['exchange_sync_state'],
                                 'sync_date' => $v['exchange_sync_date'],
                                 'folder_id' => $v['exchange_sync_folder_id']);
            }
        }
        else{
            return 0;
        }
        $this->folder_info = $info;
        return $info;
    }
    
    private function SetFolderInfo($user_id, $info, $folder_type){
        global $adb;
        $query = "SELECT * FROM exchange_sync WHERE exchange_sync_user_id = ? AND exchange_sync_type=?";
        $result = $adb->pquery($query, array($user_id, $folder_type));
        if($adb->num_rows($result) > 0){//The user exchange info already exists
            foreach($result AS $k => $v){
                if(strlen($v['exchange_sync_folder_id']) <= 0){//If the folder ID hasn't been set already, we put it in here
                    $query = "UPDATE exchange_sync SET exchange_sync_folder_id = ? WHERE exchange_sync_id = ?";
                    $adb->pquery($query, array($info->FolderId->Id, $v['exchange_sync_id']));
                }
            }
        }
    }
    
    /**
     * Get all Folders from exchange exactly the way exchange returns it
     * @return type
     */
    private function GetAllFoldersFromExchangeRaw(){
        $request = new EWSType_FindFolderType();
        $request->Traversal = EWSType_FolderQueryTraversalType::SHALLOW; // use EWSType_FolderQueryTraversalType::DEEP for subfolders too
        $request->FolderShape = new EWSType_FolderResponseShapeType();
        $request->FolderShape->BaseShape = EWSType_DefaultShapeNamesType::ALL_PROPERTIES;

        // configure the view
        $request->IndexedPageFolderView = new EWSType_IndexedPageViewType();
        $request->IndexedPageFolderView->BasePoint = 'Beginning';
        $request->IndexedPageFolderView->Offset = 0;

        $request->ParentFolderIds = new EWSType_NonEmptyArrayOfBaseFolderIdsType();

        // use a distinguished folder name to find folders inside it
        $request->ParentFolderIds->DistinguishedFolderId = new EWSType_DistinguishedFolderIdType();
        $request->ParentFolderIds->DistinguishedFolderId->Id = EWSType_DistinguishedFolderIdNameType::MESSAGE_FOLDER_ROOT;

        // if you know exact folder id, then use this piece of code instead. For example
        // $folder_id = 'AAKkADE4N2NkZDRjLWZjY2EtNDNlFy04MjFlLTkzODAyXTMyMGVmOABGAAAAAACO4PBzuy...';
        // $request->ParentFolderIds->FolderId = new EWSType_FolderIdType();
        // $request->ParentFolderIds->FolderId->Id = $folder_id;

        // request
        $this->folders = $this->ews->FindFolder($request);
        
        $response = $this->ews->FindFolder($request);
        
        $this->GetExchangeSyncInfo($this->user_id);
        $folders = $this->folders->ResponseMessages->FindFolderResponseMessage->RootFolder->Folders;
        foreach($folders AS $k => $v){            
            switch($v->DisplayName){
                case "Tasks":
                    $this->SetFolderInfo($this->user_id, $v, 'Task');
                    break;
                case "Calendar":
                    $this->SetFolderInfo($this->user_id, $v, 'CalendarItem');
                    break;
            }
        }
        return $response;
    }
    
/*    public function GetAllFolders(){
        $folders = $this->folders->ResponseMessages->FindFolderResponseMessage->RootFolder->Folders;
        foreach($folders AS $k => $v){
//            echo "DISPLAY NAME: " . $v['DisplayName'] . "<br />";
            print_r($v);
            echo "<br /><br />";
        }
    }*/
    
}

?>