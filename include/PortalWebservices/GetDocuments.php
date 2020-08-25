<?php

function vtws_getdocuments($element,$user){
    
    global $adb,$site_URL;

    $foldersData = array();
    
    $html = '';
    
    if(isset($element['ID']) && $element['ID'] != ''){
        
        
        $params = array();
        
        $folder_query = "SELECT DISTINCT vtiger_documentfolder.documentfolderid,
            
		vtiger_documentfolder.folder_name, vtiger_documentfolder.parent_id
		FROM vtiger_documentfolder
            
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_documentfolder.documentfolderid
            
		WHERE vtiger_crmentity.deleted = 0
            
		AND (vtiger_documentfolder.hide_from_portal != 1 OR vtiger_documentfolder.hide_from_portal IS NULL )";
        
        
        $html = '';
        
        $folder_id = isset($element['folder_id'])?$element['folder_id']:'';
        
        $html .= '<div class="foldersData dragfile row " data-parent-folder = "'.  $folder_id .'">';
        
        if($element['folder_id']){
            
            $html .= '<input type="hidden" name="folderName" value="'.Vtiger_Functions::getCRMRecordLabel($element['folder_id']).'"/>
					  <input type="hidden" name="folderId" value="'.$element['folder_id'].'"/>';
            
            $folder_query .= ' AND vtiger_documentfolder.parent_id = ? ';
            
            $params[] = $element['folder_id'];
            
        } else {
            
            $folder_query .= " AND ( vtiger_documentfolder.parent_id is NULL or  vtiger_documentfolder.parent_id = '' ) ";
            
        }
        
        if($element['emptyFolder']){
            
            $folder_query .= ' AND (vtiger_crmentity.smownerid = ?
            and vtiger_documentfolder.is_default = 1) OR vtiger_documentfolder.default_for_all_users = 1 OR vtiger_documentfolder.documentfolderid
			in (select doc_folder_id from vtiger_notes
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid = vtiger_notes.notesid where vtiger_senotesrel.crmid = ?)';
            
            $params[] = $element['owner_id'];
            $params[] = $element['ID'];
            
        } else {
            
            $folder_query .= ' AND vtiger_documentfolder.documentfolderid in (select doc_folder_id from vtiger_notes
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid = vtiger_notes.notesid where vtiger_senotesrel.crmid = ?)';
            
            $params[] = $element['ID'];
            
        }
        
        $folder_result = $adb->pquery($folder_query, $params);
        
        if($adb->num_rows($folder_result)){
            
            for($i=0;$i<$adb->num_rows($folder_result);$i++){
                
                $row = $adb->query_result_rowdata($folder_result,$i);
                
                $html .= '<div class="col-md-3 folderFiles folderActions" title="'.$row['folder_name'].'" data-folderid="'. $row['documentfolderid'].'" style="padding:5px;cursor:pointer;" >
						<div class="pull-left" ><img style="border-radius:10px;" src="images/Folder.jpg" /> </div>
						<div style = "vertical-align:middle;line-height:40px;color:#48465b;font-weight:500;">'. substr($row['folder_name'],0,20).'</div>
					</div>';
                
            }
            
        }
        
        if($element['folder_id']){
            
            if(isset($element['index'])){
                $startIndex = $element['index'];
            } else {
                $startIndex = 0;
            }
            
            $document_query = "select * from vtiger_notes
			inner JOIN vtiger_crmentity on vtiger_crmentity.crmid=vtiger_notes.notesid
			inner join vtiger_senotesrel on vtiger_senotesrel.notesid = vtiger_notes.notesid";
            
            $document_query .=  " where vtiger_crmentity.deleted = 0 ";
            
            $document_query .= " AND (vtiger_notes.is_private != 1 OR vtiger_notes.is_private IS NULL) ";
            
            $document_query .= " AND vtiger_notes.doc_folder_id = ? and vtiger_senotesrel.crmid = ? LIMIT ".$startIndex.",50";
            
            
            $document_result = $adb->pquery($document_query,array($element['folder_id'], $element['ID']));
            
            
            for($i=0; $i < $adb->num_rows($document_result); $i++){
                
                $docId = $adb->query_result($document_result, $i, 'notesid');
                
                $docName = $adb->query_result($document_result, $i, 'title');
                
                $loctype = $adb->query_result($document_result, $i, 'filelocationtype');
                
                $fileName = $adb->query_result($document_result, $i, 'filename');
                
                $file = explode('/',$adb->query_result($document_result, $i, 'filetype'));
                
                if($file[0] == 'image'){
                    $icon = 'img.jpg';
                    $fileType = 'image File';
                }else if($file[0] == 'video'){
                    $icon = 'video.jpg';
                    $fileType = 'video File';
                }else if($file[0] == 'text'){
                    $icon = 'docx.jpg';
                    $fileType = 'text File';
                }else if($file[1] == 'pdf'){
                    $icon = 'pdf.jpg';
                    $fileType = 'pdf File';
                }else if($file[1] == 'zip'){
                    $icon = 'zip.jpg';
                    $fileType = 'zip File';
                }else if(strpos($file[1], 'ms')!== false || strpos($file[1], 'vnd') !== false){
                    $icon = 'office.jpg';
                    $fileType = 'office File';
                }else {
                    $icon = 'txt.jpg';
                    $fileType = 'doc File';
                    if($loctype == 'E')
                        $fileType = 'external File';
                }
                
                
                $html .= '<div class="col-md-3 fileDrag" id="fileDrag" title="' . $docName . '" data-fileid="' . $docId . '" style="padding:5px;cursor:pointer;" >
					<div class="pull-left"><img style="border-radius:10px;" src="images/' . $icon . '" /> </div>
					<div style = "float:left;padding-left:5px;vertical-align:middle;line-height:20px;color:#48465b;font-weight:500;">
						<a href="javascript:void(0)" data-filelocationtype="' . $loctype .'" data-filename="'.$folder_data['fileName'].'" data-fileid="' . $docId . '">
							'.substr($docName,0,20).'
							<br><span class="document_preview" title="Preview" style="font-size:1.5em!important;">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
										<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"/>
									</g>
								</svg>
							</span>&nbsp;&nbsp;
							<span class="document_download" title="Download" style="font-size:1.5em!important;">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
										<rect x="0" y="0" width="24" height="24"/>
										<path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
										<rect fill="#000000" opacity="0.3" transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) " x="11" y="1" width="2" height="14" rx="1"/>
										<path d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) "/>
									</g>
								</svg>
							</span>
						</a>
					</div>
				</div>';
            }
            
            if($adb->num_rows($document_result) >=50)
                $value= '1';
            else
                $value ='0';
                
            $html .= '<input type="hidden" name="scrollevent" value="'.$value.'" />';
                    
        }
        
        
        
    }
    
    return $html;
}