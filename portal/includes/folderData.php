<?php
    include_once('config.php');
    include_once('function.php'); 
    global $adb;
    $html ='';
    if($_REQUEST['folder_id'] && !isset($_REQUEST['index'])){
        $folderData = getDocumentFolderWithParentList($_REQUEST['folder_id'],0,$_REQUEST['emptyFolder']);
       
        $html .='<input type="hidden" name="folderName" value="'.Vtiger_Functions::getCRMRecordLabel($_REQUEST['folder_id']).'"/>
                <input type="hidden" name="folderId" value="'.$_REQUEST['folder_id'].'"/>
                <input type="hidden" name="startIndex" value="50"/>
                <input type="hidden" name="listLimit" value="50"/>';
        $html.=' <div class="foldersData dragfile row " data-parent-folder="'.$_REQUEST['folder_id'].'" >';
        if(!empty($folderData)){
            if(count($folderData) >=50)$value= '1';else $value ='0';
            
            $html .='<input type="hidden" name="scrollevent" value="'.$value.'" />';
       
            foreach($folderData as $folder_data){
                if($folder_data['type'] == 'folder'){
                    $html .='<div class="col-md-3 folderFiles  folderActions" title="'.$folder_data['text'].'" data-folderid="'.$folder_data['id'].'" style="padding:5px;cursor:pointer;" >
    						<div class="pull-left"><img style="border-radius:10px;" src="assets/img/Folder.jpg" /> </div>
    						<span class="fieldLabel">'.substr($folder_data['text'],0,20).'</span></br><span style="font-size:11px;">File Folder</span>
    					</div>';
                }elseif($folder_data['type'] == 'file'){
                    $html.='<div class="col-md-3 fileDrag" id="fileDrag" title="'.$folder_data['text'].'" data-fileid="'.$folder_data['id'].'" style="padding:5px;cursor:pointer;" >
    					<div class="pull-left"><img style="border-radius:10px;" src="assets/img/'.$folder_data['icon'].'" /> </div>
    					<span class="fieldLabel" style="padding:2px;">
    						<a href="javascript:void(0)" data-filelocationtype="'.$folder_data['fileLocation'].'" data-filename="'.$folder_data['fileName'].'" >
    							'.substr($folder_data['text'],0,20).'
    						</a>
    					</span></br><span style="font-size:11px;padding:2px;">'.$folder_data['fileType'].'</span>
    				</div>';
                }
            }
        }else{
            $html.= '<div class="col-md-12 emptyRecordsDiv text-center " style="padding:20% 0;">
    			<div class="emptyRecordsContent">
    				No Data found.
    			</div>
    		</div>';
        }
        
    }elseif($_REQUEST['folder_id'] && $_REQUEST['index']){
        
        $folderData = getDocumentFolderWithParentList($_REQUEST['folder_id'],$_REQUEST['index'],$_REQUEST['emptyFolder']);
        $html ='';
        if(!empty($folderData)){
            if(count($folderData)>=50)$value= '1';else $value ='0';
            
            $html .='<input type="hidden" name="scrollevent" value="'.$value.'" />';
            
            foreach($folderData as $folder_data){
                if($folder_data['type'] == 'file'){
                    $html.='<div class="col-md-3 fileDrag" id="fileDrag" title="'.$folder_data['text'].'" data-fileid="'.$folder_data['id'].'" style="padding:5px;cursor:pointer;" >
    					<div class="pull-left"><img style="border-radius:10px;" src="assets/img/'.$folder_data['icon'].'" /> </div>
    					<span class="fieldLabel" style="padding:2px;">
    						<a href="javascript:void(0)" data-filelocationtype="'.$folder_data['fileLocation'].'" data-filename="'.$folder_data['fileName'].'" >
    							'.substr($folder_data['text'],0,20).'
    						</a>
    					</span></br><span style="font-size:11px;padding:2px;">'.$folder_data['fileType'].'</span>
    				</div>';
                }
            }
        }else{
            $html.= '<div class="col-md-12 emptyRecordsDiv text-center " style="padding:20% 0;">
    			<div class="emptyRecordsContent">
    				No Data found.
    			</div>
    		</div>';
        }
        
    }else{
        
        global $current_user;
        
        $folders = $adb->pquery("SELECT DISTINCT vtiger_documentfolder.documentfolderid, vtiger_documentfolder.folder_name,
        vtiger_documentfolder.parent_id
        FROM vtiger_notes
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
        INNER JOIN vtiger_documentfolder ON vtiger_documentfolder.documentfolderid = vtiger_notes.doc_folder_id
        INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
        WHERE vtiger_crmentity.deleted = 0 
        AND (vtiger_notes.is_private != 1 OR vtiger_notes.is_private IS NULL) 
        AND (vtiger_documentfolder.hide_from_portal != 1 OR vtiger_documentfolder.hide_from_portal IS NULL )
        AND vtiger_senotesrel.crmid = ?",
        array($_SESSION['ID']));
        
//         AND (vtiger_crmentity.smcreatorid = ? OR vtiger_crmentity.smownerid = ?)$_SESSION['ownerId'],$_SESSION['ownerId'],
        
        $folderIds = array();
        $foldersData = array();
        if($adb->num_rows($folders)){
            for($i=0;$i<$adb->num_rows($folders);$i++){
                $folderIds[] = $adb->query_result($folders,$i,'documentfolderid');
                $foldersData[] = $adb->query_result_rowdata($folders,$i);
            }
        }
        if($_REQUEST['emptyFolder'] == 'true'){
            
            $moduleName = "DocumentFolder";
            
            $currentUserModel = Users_Record_Model::getInstanceFromPreferenceFile($current_user->id);
            
            $queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
            
            $queryGenerator->setFields( array('folder_name','id', 'parent_id') );
            
            $listviewController = new ListViewController($adb, $currentUserModel, $queryGenerator);
            
            $query = $queryGenerator->getQuery();
            
            $query .= " AND vtiger_documentfolder.hide_from_portal != 1  AND 
            vtiger_documentfolder.documentfolderid  NOT IN (".implode(',',$folderIds).") ";
            
            $pos = strpos($query, "SELECT");
            if ($pos !== false) {
                $query = substr_replace($query, "SELECT DISTINCT vtiger_documentfolder.documentfolderid, ", $pos, strlen("SELECT"));
            }
            
            $documentFolders = $adb->pquery($query,array());
           
            if($adb->num_rows($documentFolders)){
                for($i=0;$i<$adb->num_rows($documentFolders);$i++){
                    $foldersData[] = $adb->query_result_rowdata($documentFolders,$i);
                }
            }
            
        }
        $html .='<div class="foldersData dragfile row" data-parent-folder=""  >';
        if(!empty($foldersData)){
          foreach($foldersData as $folderData){
            	$html.='<div class="col-md-3 folderFiles folderActions" title="'.$folderData['folder_name'].'" data-folderid="'. $folderData['documentfolderid'].'" style="padding:5px;cursor:pointer;" >
    				<div class="pull-left" ><img style="border-radius:10px;" src="assets/img/Folder.jpg" /> </div>
    				<span class="fieldLabel">'. substr($folderData['folder_name'],0,20).'</span></br><span style="font-size:11px;">File Folder</span>
    			</div>';
            }
        }else{
            $html.='<div class="col-md-12 emptyRecordsDiv text-center " style="padding:20% 0;">
    			<div class="emptyRecordsContent">
    				No Folders found.
    			</div>
    		</div>';
        }
         $html.=' </div>';
        
    }
    echo $html;