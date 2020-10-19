<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

class MailManager_Folder_View extends MailManager_Abstract_View {

	/**
	 * Process the request for Folder opertions
	 * @global <type> $maxEntriesPerPage
	 * @param Vtiger_Request $request
	 * @return MailManager_Response
	 */
	public function process(Vtiger_Request $request) {
		$currentUserModel = Users_Record_Model::getCurrentUserModel();
		$maxEntriesPerPage = vglobal('list_max_entries_per_page');

		$response = new MailManager_Response();
		$moduleName = $request->getModule();
		if ('open' == $this->getOperationArg($request)) {
			$q = $request->get('q');
			$foldername = $request->get('_folder');
			$type = $request->get('type');
            $date = $request->get('date');
			$connector = $this->getConnector($foldername);
			$folder = $connector->folderInstance($foldername);
			
			if (empty($q) && empty($date)) {
				$connector->folderMails($folder, intval($request->get('_page', 0)), $maxEntriesPerPage);
			} else {
				if(empty($type)) {
					$type='ALL';
				}
				if($type == 'ON') {
					/*$dateFormat = $currentUserModel->get('date_format');
					if ($dateFormat == 'mm-dd-yyyy') {
						$dateArray = explode('-', $q);
						$temp = $dateArray[0];
						$dateArray[0] = $dateArray[1];
						$dateArray[1] = $temp;
						$q = implode('-', $dateArray);
					}
					$query = date('d M Y',strtotime($q));
					$q = ''.$type.' "'.vtlib_purify($searchQuery).'"';
					*/
				   
				} else {
				    if($q){
				        if($connector->serverType == 'Office365' || $connector->serverType == 'Google'){
    				        $q = ''.strtolower($type).':'.vtlib_purify($q);
    				    }else{
    					   $q = ''.$type.' "'.vtlib_purify($q).'"';
    				    }
				    }
				}
				
				if($date){
				    $date = explode(',',$date);
				    
				    $start='';
				    $end = '';
				    if($date[0]){
				        $startDate = getValidDBInsertDateValue($date[0]);
				        $start = date('d M Y',strtotime($startDate));
				        $officeStarrt = date('m-d-Y',strtotime($startDate));
				    }
				    
				    if($date[1]){
				        $endDate = getValidDBInsertDateValue($date[1]);
				        $end = date('d M Y',strtotime($endDate));
				        $officeEnd = date('m-d-Y',strtotime($endDate));
				    }
				    
				    if($start && $end){
				        if($connector->serverType == 'Office365'){
				            $searchQuery .= 'received:'.$officeStarrt.'..'.$officeEnd;
				        }else if($connector->serverType == 'Google'){
				            $searchQuery .= 'before:'.strtotime($end).' after:'.strtotime($start);
				        }else{
				            $searchQuery .= 'BEFORE "'.$end.'" SINCE "'.$start.'"';
				        }
				    }
				    $q .= ' '.vtlib_purify($searchQuery);
				}
				
				$connector->searchMails($q, $folder, intval($request->get('_page', 0)), $maxEntriesPerPage);
			}

			$folderList = $connector->getFolderList();

			$viewer = $this->getViewer($request);
			
			$viewer->assign('TYPE', $type);
			$viewer->assign('QUERY', $request->get('q'));
			$viewer->assign('DATE', $request->get('date'));
			$viewer->assign('FOLDER', $folder);
			$viewer->assign('FOLDERLIST',  $folderList);
			$viewer->assign('SEARCHOPTIONS' ,self::getSearchOptions());
			$viewer->assign("JS_DATEFORMAT",parse_calendardate(getTranslatedString('NTC_DATE_FORMAT')));
			$viewer->assign('USER_DATE_FORMAT', $currentUserModel->get('date_format'));
			$viewer->assign('MODULE', $moduleName);
			$response->setResult($viewer->view( 'FolderOpen.tpl', $moduleName, true ));
		} elseif('drafts' == $this->getOperationArg($request)) {
			$moduleName = $request->getModule();
			$q = $request->get('q');
			$type = $request->get('type');
			$page = intval($request->get('_page', 0));

			$connector = $this->getConnector('__vt_drafts');
			$folder = $connector->folderInstance();

			if(empty($q)) {
				$draftMails = $connector->getDrafts($page, $maxEntriesPerPage, $folder);
			} else {
				$draftMails = $connector->searchDraftMails($q, $type, $page, $maxEntriesPerPage, $folder);
			}

			$viewer = $this->getViewer($request);
			$viewer->assign('MAILS', $draftMails);
			$viewer->assign('FOLDER', $folder);
			$viewer->assign('SEARCHOPTIONS' ,MailManager_Draft_View::getSearchOptions());
			$viewer->assign('USER_DATE_FORMAT', $currentUserModel->get('date_format'));
			$viewer->assign('MODULE', $moduleName);
			$viewer->assign('QUERY', $request->get('q'));
			$viewer->assign('TYPE', $type);
			$response->setResult($viewer->view('FolderDrafts.tpl', $moduleName, true));
		} else if ('getFoldersList' == $this->getOperationArg($request)) {
			$viewer = $this->getViewer($request);
            if ($this->hasMailboxModel($request->get('account_id'))) {
                $connector = $this->getConnector();
                
                if ($connector->isConnected()) {
                    $folders = $connector->folders();
                    $connector->updateFolders();
                    $eleFol = array();
                    
                    /* $eleFol['xxx'] =  array(
                        "id"=>'xxx',
                        "parent_id"=>'',
                        "text"=>'/',
                        'is_default'=>1,
                        "type"=>"folder") ; */
                   // fa fa-caret-right
                    foreach($folders as $folder){
                        if( $folder->name() != 'Sent Items' && $folder->name() != 'Trash'){
                            $folNa = explode('/',$folder->name()) ;
                            $folderCount = $folder->unreadCount();
                            foreach($folNa as $key => $fol_na){
                                if(!in_array($folNa[$key], $folName)){
                                    $eleFol[$fol_na] = array(
                                        'id' => $folder->name(),
                                        'text'=>$fol_na,
                                        'parent_id'=>'',
                                        'type'=>'folder',
                                        "icon"=>"fa fa-caret-right",
                                    );
                                }
                                if($key > 0){
                                    if(!in_array($fol_na, $folName)){
                                        $eleFol[$fol_na] = array(
                                            'id' => $eleFol[$folNa[$key-1]]['id'].'/'.$fol_na,
                                            'text' => $fol_na,
                                            'parent_id' => $eleFol[$folNa[$key-1]]['id'],
                                            'type'=>'folder',
                                            //'children'=>true,
                                            "icon"=>"fa fa-caret-right",
                                        );
                                    }
                                }
                            }
                        }
                        $folName[] = $folder->name();
                    }
                    
                    $branch = $this->buildTree($eleFol);
                    
                    $viewer->assign('OTHERFOLDER', json_encode($branch));
                    
                    $viewer->assign('FOLDERS', $folders);
                    
                } else if($connector->hasError()) {
                    $error = $connector->lastError();
                    $response->isJSON(true);
                    $response->setError(101, $error);
                }
                $this->closeConnector();
            }
            $viewer->assign('MODULE', $request->getModule());
            $response->setResult($viewer->view('FolderList.tpl', $moduleName, true));
        }
        return $response;
    }
    
    public function buildTree(array $elements, $parentId = 0) {
        $branch = array();
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children =  $this->buildTree($elements, $element['id']);
                if($children) {
                    $element['children'] = $children;
                }
                $branch[] = $element;
            }
        }
        
        return $branch;
    }
    
    /**
     * Returns the List of search string on the MailBox
     * @return string
     */
    public static function getSearchOptions() {
        $options = array('FROM'=>'FROM','SUBJECT'=>'SUBJECT','TO'=>'TO','BODY'=>'BODY','BCC'=>'BCC','CC'=>'CC'/*,'DATE'=>'ON'*/);
        return $options;
    }
    public function validateRequest(Vtiger_Request $request) {
        return $request->validateWriteAccess();
    }
}
?>
