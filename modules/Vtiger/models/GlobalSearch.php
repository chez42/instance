<?php
class Vtiger_GlobalSearch_Model extends Vtiger_Base_Model {
	
	public $defaultGlobalSearchAllowedModules = array();
	
	function __construct(){
	    
	    $modules = array();
	    
	    $adb = PearDatabase::getInstance();
	    $moduleQuery = $adb->pquery("SELECT modulename FROM vtiger_globalsearch");
	    
	    if($adb->num_rows($moduleQuery)){
	        for($i=0;$i<$adb->num_rows($moduleQuery);$i++){
	           $modules[] = $adb->query_result($moduleQuery,$i,'modulename');
	        }
	    }
	    
	    $this->defaultGlobalSearchAllowedModules = $modules;
	}
	
	public function getSearchResult($searchValue,$searchModule,$userCurrentModule) {

		$searchAbleModules = $this->getSearchModules();
		if(in_array($searchModule,$searchAbleModules))
		    $object_array = array($searchModule => $searchModule);
	    else
	        return true;
	    
		$adb = PearDatabase::getInstance();
		
		$currentUser = vglobal('current_user');
		
		if(isset($object_array[$userCurrentModule])){
			unset($object_array[$userCurrentModule]);
			$object_array = array($userCurrentModule => $userCurrentModule) + $object_array;
		}
		
		$matchingRecords = array();
		
		foreach($object_array as $module => $object_name){			
		    
			$focus = CRMEntity::getInstance($module);
			
			$moduleModel = Vtiger_Module_Model::getInstance($module);					
			
			$queryGenerator = new QueryGenerator($module, $currentUser);
			
			$globalSearchFields = $moduleModels = $leadIdsList = array();

			$search_ListFields_result = $adb->pquery("select * from vtiger_globalsearch where modulename = ?",array($module));
			
			if( $adb->num_rows($search_ListFields_result) ){
				
				$gsData = $adb->fetch_array($search_ListFields_result);
				
				$allowed_in_gs = $gsData['allow_global_search'];
				
				if($allowed_in_gs != '1' ) continue;
				
				$globalSearchFields = explode(',',$gsData['fieldnames']);
			}
			
			$controller = new ListViewController($adb, $currentUser, $queryGenerator);
			
			if(isPermitted($module,"index") == "yes"){
				
				if( empty($globalSearchFields) ){
					
					$customView = new CustomView();				
					$viewid = $customView->getViewId($module);			
					$listViewModel = Vtiger_ListView_Model::getInstance($module, $viewid);					
					$listViewHeaders = $listViewModel->getListViewHeaders();
					
					$queryGenerator->initForCustomViewById($viewid);		

				} else {
					array_push($globalSearchFields, 'id');
					$queryGenerator->setFields($globalSearchFields);
				}
				
				$moduleFields = $queryGenerator->getFields();
				array_push($moduleFields, "createdtime");
                        
                        if($module == 'HelpDesk'){
                            if(!in_array('cf_788',$moduleFields))
                                array_push($moduleFields, "cf_788");
                        }
                        
				$queryGenerator->setFields($moduleFields);
				
				$queryGenerator = $this->getUnifiedWhere($module,$searchValue, $queryGenerator);
					
				$listquery =  $queryGenerator->getQuery();
				
			 	$result = $adb->pquery($listquery);
				
				$queryGenerator->reset();
				
				$noOfRows = $adb->num_rows($result);
					
				if ($module === 'Leads') {
					for($i=0; $i<$noOfRows; ++$i) {
						$row = $adb->query_result_rowdata($result, $i);
							$leadIdsList[] = $row['leadid'];
					}
				}
				
				$convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);
		
				for($i=0, $recordsCount = 0; $i<$noOfRows && $recordsCount<100; ++$i) {
					
					$row = $adb->query_result_rowdata($result, $i);
					
					$row['crmid'] = $row[$moduleModel->get("basetableid")];
					$row['setype'] = $module;
					
					if ($module === 'Leads' && $convertedInfo[$row['leadid']]) {
						continue;
					}
					
					
						if($row['setype'] == 'HelpDesk'){
						
							$recordPermission = Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid']);
					
							if(!$recordPermission) {

								if(Vtiger_Util_Helper::getCreator($row['crmid']) == $current_user->id){
									$recordPermission = true;
								}
								
								if(HelpDesk_Sharing_Model::IsUserPartOfSharingPermission($row['crmid']))
									$recordPermission = true;
								
								if(HelpDesk_Sharing_Model::DoesTicketBelongToUsersGroup($row['crmid']))
									$recordPermission = true;
							}
							
							if($recordPermission){
								$row['id'] = $row['crmid'];
								$moduleName = $row['setype'];
								if(!array_key_exists($moduleName, $moduleModels)) {
									$moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
								}
								$moduleModel = $moduleModels[$moduleName];
								$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
								$recordInstance = new $modelClassName();
								$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
								$recordsCount++;
							}
							
							
							
					
					
					} else if(Users_Privileges_Model::isPermitted($row['setype'], 'DetailView', $row['crmid'])) {
						$row['id'] = $row['crmid'];
						$moduleName = $row['setype'];
						if(!array_key_exists($moduleName, $moduleModels)) {
							$moduleModels[$moduleName] = Vtiger_Module_Model::getInstance($moduleName);
						}
						$moduleModel = $moduleModels[$moduleName];
						$modelClassName = Vtiger_Loader::getComponentClassName('Model', 'Record', $moduleName);
						$recordInstance = new $modelClassName();
						$matchingRecords[$moduleName][$row['id']] = $recordInstance->setData($row)->setModuleFromInstance($moduleModel);
						$recordsCount++;
					}
					
					
					
					
					
				}
			}
		}
		return $matchingRecords;
	}
	
	public function getSearchModules(){
		
		global $adb;
		
		$sql = 'select distinct vtiger_field.tabid, name 
		from vtiger_field 
		inner join vtiger_tab on vtiger_tab.tabid=vtiger_field.tabid 
		where vtiger_tab.tabid not in (16,29) and vtiger_tab.presence != 1 and vtiger_field.presence in (0,2)';
	
		$result = $adb->pquery($sql, array());
	
		while($module_result = $adb->fetch_array($result)){
			
			$modulename = $module_result['name'];
			
			if(!in_array($modulename,$this->defaultGlobalSearchAllowedModules))continue;
			
			if($modulename != 'Calendar'){
				$return_arr[$modulename] = $modulename;
			}else{
				$return_arr[$modulename] = 'Activity';
			}
		}
		return $return_arr;
	}
	
	function getUnifiedWhere($module,$search_val, $queryGenerator){
		
		$fields = $queryGenerator->getFields();
		
		$moduleFields = $queryGenerator->getModuleFields();
		
		foreach($moduleFields as $field_obj){
			
			$fieldname = $field_obj->getFieldName();
			
			$field_data_type = $field_obj->getFieldDataType();
			
			if(in_array($fieldname, $fields)){
				$columns[] = array("fieldname" => $fieldname, "field_data_type" => $field_data_type);
			}
		}
		
		$counter = 1;
		
		$search_value = explode(" ", $search_val);
		
		if(count($queryGenerator->getWhereFields()) > 0){
				$queryGenerator->addConditionGlue("AND");
		}
		
		foreach($search_value as $search_val){
			
			if($counter % 2 == 0)
				$queryGenerator->addConditionGlue("AND");
			
			$counter++;
			
			$queryGenerator->startGroup('');

			$filtered_columns = array();
			
			foreach($columns as $column_data){
				$typeofdata = $column_data['field_data_type'];
				if($typeofdata == 'integer' || $typeofdata == 'currency' || $typeofdata == 'double'){
					if(is_numeric($search_val)){
						$filtered_columns[] = $column_data;
					} 
				} else if($typeofdata == 'date' || $typeofdata == 'datetime'){
					$date = date_parse($search_val);
					if ($date["error_count"] == 0 && checkdate($date["month"], $date["day"], $date["year"])){
						$filtered_columns[] = $column_data;
					}
				} else {
					$filtered_columns[] = $column_data;
				}
			}
			
			$index = 0 ;
			
			foreach($filtered_columns as $column_data){
				$columnname = $column_data['fieldname'];
				$typeofdata = $column_data['field_data_type'];
				
				if($typeofdata == 'integer' || $typeofdata == 'currency' || $typeofdata == 'double'){
					$queryGenerator->addCondition($columnname, 
					$search_val, 'e');
				} else if($typeofdata == 'date' || $typeofdata == 'datetime'){
					$queryGenerator->addCondition($columnname, 
						$search_val, 'e');
				} else {
					$queryGenerator->addCondition($columnname, 
						$search_val, 'c');
				}
				
				if($index < (count($filtered_columns)-1))
					$queryGenerator->addConditionGlue("OR");
				
				$index++;
			}
			
			$queryGenerator->endGroup();
		}		
		
		return $queryGenerator;	
	}	
	
}
