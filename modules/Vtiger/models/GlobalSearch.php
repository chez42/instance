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
	
	public function getSearchResult($searchValue,$searchModule,$userCurrentModule, $pagingModel) {

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
		
		foreach($object_array as $search_module => $object_name){			
		    
		    $pagingModel = new Vtiger_Paging_Model();
		    $pagingModel->set("page", "1");
		    $queryFields = array();
		    $search_module = $searchModule;
		    if (in_array($search_module, array("ANCustomers", "ANPaymentProfile", "ANTransactions", "DuplicateCheckMerge"))) {
		        continue;
		    }
		    $presenceFields = $this->getPresenceFields($search_module);
		    $user = Users_Record_Model::getCurrentUserModel();
		    $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
		    $moduleFocus = CRMEntity::getInstance($search_module);
		    $moduleModel = Vtiger_Module_Model::getInstance($search_module);
		    $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
		    if ($permission) {
		        if (version_compare($vtiger_current_version, "7.0.0", "<")) {
		            $queryGenerator = new QueryGenerator($search_module, $user);
		        } else {
		            $queryGenerator = new EnhancedQueryGenerator($search_module, $user);
		        }
		        $referenceFieldInfoList = $queryGenerator->getReferenceFieldInfoList();
		        $referenceFieldList = $queryGenerator->getReferenceFieldList();
		        $modulePhoneFields = $this->getPhoneFields($search_module);
		        $moduleFieldList = $moduleModel->getFields();
		        $queryFields[] = "id";
		        $rsFields = $adb->pquery("SELECT * FROM vtiger_globalsearch WHERE modulename=? AND fieldnames <> '' AND fieldnames IS NOT NULL", array($search_module));
		        $numRows = $adb->num_rows($rsFields);
		        if ($numRows == 0) {
		            $rsFields = $adb->pquery("SELECT vtiger_cvcolumnlist.columnname AS fieldname
                    FROM vtiger_cvcolumnlist                                
                    INNER JOIN vtiger_customview ON vtiger_customview.cvid=vtiger_cvcolumnlist.cvid
                    WHERE entitytype=? AND viewname='All' ORDER BY columnindex", array($search_module));
		            $numRows = $adb->num_rows($rsFields);
		        }
		        $i = 0;
		        $phoneFields = array();
		        if (0 < $numRows) {
		            $rowFields_array = array();
		            while ($row = $adb->fetch_array($rsFields)) {
		                $selectedField =  explode(',', $row['fieldnames']);
		                foreach($selectedField as $selectField){
		                  $row['fieldname'] = $selectField;
		                  $rowFields_array[] = $row;
		                  
		                }
		            }
		            $search_value = trim($searchValue);
		          
		            if (strpos($search_value, ";") !== false && 1 < count(explode(";", $search_value))) {
		                $search_value = explode(";", $search_value);
		                $j = 0;
		                foreach ($search_value as $value) {
		                    if (0 < $j) {
		                        $queryGenerator->addConditionGlue("and");
		                    }
		                    if (trim($value) != "") {
		                        $queryGenerator->startGroup("");
		                    }
		                    $k = 0;
		                    foreach ($rowFields_array as $rowFields) {
		                        $fieldDetails = explode(":", $rowFields["fieldname"]);
		                        if (in_array($fieldDetails[2], $presenceFields)) {
		                            if (in_array($fieldDetails[2], $referenceFieldList) && count($referenceFieldInfoList[$fieldDetails[2]]) == 0) {
		                                continue;
		                            }
		                            $queryFields[] = $fieldDetails[2];
		                            if (empty($fieldDetails[2]) && $fieldDetails[1] == "crmid" && $fieldDetails[0] == "vtiger_crmentity") {
		                                $name = $queryGenerator->getSQLColumn("id");
		                            } else {
		                                $name = $fieldDetails[2];
		                            }
		                            $fieldModel = $moduleFieldList[$name];
		                            if (empty($fieldModel) || !$fieldModel->isViewableInDetailView()) {
		                                continue;
		                            }
		                            if (in_array($name, array_keys($modulePhoneFields))) {
		                                $searchKey = preg_replace("/[^A-Za-z0-9]/", "", trim($value));
		                                if ($searchKey != "") {
		                                    $phoneFields[] = $modulePhoneFields[$name] . "." . $name;
		                                    if (0 < $k) {
		                                        $queryGenerator->addConditionGlue("or");
		                                    }
		                                    $queryGenerator->addCondition($name, $searchKey, "c");
		                                    $k++;
		                                }
		                            } else {
		                                $searchKey = trim($value);
		                                if (!empty($searchKey)) {
		                                    if ($fieldDetails[4] == "D" || $fieldDetails[4] == "DT" || $fieldDetails[4] == "T") {
		                                        $searchValue = DateTimeField::convertToDBFormat($searchKey);
		                                        if (0 < $k) {
		                                            $queryGenerator->addConditionGlue("or");
		                                        }
		                                        $queryGenerator->addCondition($name, $searchValue, "e");
		                                        $k++;
		                                    } else {
		                                        if (($fieldDetails[4] == "N" || $fieldDetails[4] == "NN" || $fieldDetails[4] == "I") && is_numeric($searchKey)) {
		                                            if (0 < $k) {
		                                                $queryGenerator->addConditionGlue("or");
		                                            }
		                                            $queryGenerator->addCondition($name, $searchKey, "e");
		                                            $k++;
		                                        } else {
		                                            if ($fieldDetails[4] != "N" && $fieldDetails[4] != "NN" && $fieldDetails[4] != "I") {
		                                                if (0 < $k) {
		                                                    $queryGenerator->addConditionGlue("or");
		                                                }
		                                                $queryGenerator->addCondition($name, $searchKey, "c");
		                                                $k++;
		                                            }
		                                        }
		                                    }
		                                }
		                            }
		                        }
		                    }
		                    if (trim($value) != "") {
		                        $queryGenerator->endGroup();
		                    }
		                    $j++;
		                }
		            } else {
		                if (trim($searchValue) != "") {
		                    $queryGenerator->startGroup("");
		                }
		                
		                foreach ($rowFields_array as $rowFields) {
		                    
		                    $fieldDetails = explode(":", $rowFields["fieldname"]);
		                   
		                    if (in_array($fieldDetails[2], $presenceFields)) {
		                        if (in_array($fieldDetails[2], $referenceFieldList) && count($referenceFieldInfoList[$fieldDetails[2]]) == 0) {
		                            continue;
		                        }
		                        $queryFields[] = $fieldDetails[2];
		                        if (empty($fieldDetails[2]) && $fieldDetails[1] == "crmid" && $fieldDetails[0] == "vtiger_crmentity") {
		                            $name = $queryGenerator->getSQLColumn("id");
		                        } else {
		                            $name = $fieldDetails[2];
		                        }
		                        $fieldModel = $moduleFieldList[$name];
		                        if (empty($fieldModel) || !$fieldModel->isViewableInDetailView()) {
		                            continue;
		                        }
		                        if (in_array($name, array_keys($modulePhoneFields))) {
		                            $searchKey = preg_replace("/[^A-Za-z0-9,]/", "", trim($searchValue));
		                            if ($searchKey != "") {
		                                $phoneFields[] = $modulePhoneFields[$name] . "." . $name;
		                                if (0 < $i) {
		                                    $queryGenerator->addConditionGlue("or");
		                                }
		                                $queryGenerator->addCondition($name, $searchKey, "c");
		                                $i++;
		                            }
		                        } else {
		                            $searchKey = trim($searchValue);
		                            if (!empty($searchKey)) {
		                                if ($fieldDetails[4] == "D" || $fieldDetails[4] == "DT" || $fieldDetails[4] == "T") {
		                                    $searchValue = DateTimeField::convertToDBFormat($searchKey);
		                                    if (0 < $i) {
		                                        $queryGenerator->addConditionGlue("or");
		                                    }
		                                    $queryGenerator->addCondition($name, $searchValue, "e");
		                                    $i++;
		                                } else {
		                                    if (($fieldDetails[4] == "N" || $fieldDetails[4] == "NN" || $fieldDetails[4] == "I") && is_numeric($searchKey)) {
		                                        if (0 < $i) {
		                                            $queryGenerator->addConditionGlue("or");
		                                        }
		                                        $queryGenerator->addCondition($name, $searchKey, "e");
		                                        $i++;
		                                    } else {
		                                        if ($fieldDetails[4] != "N" && $fieldDetails[4] != "NN" && $fieldDetails[4] != "I") {
		                                            if (0 < $i) {
		                                                $queryGenerator->addConditionGlue("or");
		                                            }
		                                            $queryGenerator->addCondition($name, $searchKey, "c");
		                                            $i++;
		                                        }
		                                    }
		                                }
		                            }
		                        }
		                    }
		                }
		                if (trim($searchValue) != "") {
		                    $queryGenerator->endGroup();
		                }
		            }
		        }
		
		        $columnFieldMapping = $moduleModel->getColumnFieldMapping();
		        $queryFields_show = array();
		        $sql_fields_show = "SELECT * FROM vtiger_globalsearch WHERE modulename = '" . $search_module . "'
                   AND fieldname_show <> '' AND fieldname_show IS NOT NULL;";
		        $re_fields_show = $adb->pquery($sql_fields_show, array());
		        if (0 < $adb->num_rows($re_fields_show)) {
		            while ($row_fields_show = $adb->fetchByAssoc($re_fields_show)) {
		                $fieldShow =  explode(',', $row_fields_show["fieldname_show"]);
		                foreach($fieldShow as $showField){
		                    $fieldDetails = explode(":", $showField);
		                    $queryFields_show[] = $fieldDetails[2];
		                }
		            }
		        }
		        
		        if (!empty($queryFields_show)) {
		            $queryFields_show[] = "id";
		            $queryGenerator->setFields($queryFields_show);
		        } else {
		            $queryGenerator->setFields($queryFields);
		        }
		        $listViewContoller = new ListViewController($adb, $user, $queryGenerator);
		        $headerFieldModels = array();
		        $headerFields = $listViewContoller->getListViewHeaderFields();
		        foreach ($headerFields as $fieldName => $webserviceField) {
		            if ($webserviceField && !in_array($webserviceField->getPresence(), array(0, 2))) {
		                continue;
		            }
		            $headerFieldModels[$fieldName] = Vtiger_Field_Model::getInstance($fieldName, $moduleModel);
		        }
		        $search_results[$search_module]["listViewHeaders"] = $headerFieldModels;
		        if ($search_module == "Calendar") {
		            $queryGenerator->addCondition("activitytype", "Emails", "n", "AND");
		        }
		        $query = $queryGenerator->getQuery();
		        if (0 < count($phoneFields)) {
		            $position = stripos($query, " from ");
		            if ($position) {
		                $split = spliti(" from ", $query);
		                $whereQ = $split[1];
		                foreach ($phoneFields as $pfield) {
		                    $whereQ = str_replace($pfield, "replace(replace(replace(replace(replace(replace(replace(" . $pfield . ", '+', ''), '-', ''), ')', ''),'(',''), '#', ''),'/',''),' ', '')", $whereQ);
		                }
		                $query = $split[0] . " FROM " . $whereQ;
		            }
		        }
		        $searchKey = trim($searchValue);
		        if (!empty($searchKey)) {
		            $position = stripos($query, " from ");
		            if ($position) {
		                $split = spliti(" where ", $query);
		                if ($search_module == "ModComments") {
		                    $whereQ = "(" . $split[1] . " AND vtiger_modcomments.related_to > 0 AND vtiger_modcomments.related_to IS NOT NULL )";
		                } else {
		                    $whereQ = "(" . $split[1] . ")";
		                }
		                $convertedLead = "";
		                if ($search_module == "Leads") {
		                    $convertedLead = " AND vtiger_leaddetails.converted = 0 ";
		                }
		                $whereQ .= " ";
		                if ($search_module == "ModComments") {
		                    $whereQ .= " OR (  vtiger_modcomments.related_to > 0 AND vtiger_modcomments.related_to IS NOT NULL )";
		                }
		                $query = $split[0] . " where " . $whereQ;
		            }
		        }
		        $startIndex = $pagingModel->getStartIndex();
		        $pageLimit = $pagingModel->getPageLimit();
		        $query .= " ORDER BY vtiger_crmentity.label ASC";
		  
		        $countQuery = "";
		        $position = stripos($query, " from ");
		        if ($position) {
		            $split = spliti(" from ", $query);
		            $splitCount = count($split);
		            if (!empty($keyword)) {
		                $countQuery = "SELECT count(DISTINCT result." . $table_index . ") AS count ";
		            } else {
		                $countQuery = "SELECT count(*) AS count ";
		            }
		            for ($i = 1; $i < $splitCount; $i++) {
		                $countQuery = $countQuery . " FROM " . $split[$i];
		            }
		        }
		        if ($debug == "1") {
		            echo "countQuery : " . $countQuery . " <br><br>";
		        }
		        $countResult = $adb->pquery($countQuery, array());
		        $totalResults = $adb->query_result($countResult, 0, "count");
		        $search_results[$search_module]["totalResults"] = $totalResults;
		        $pageCount = ceil((int) $totalResults / (int) $pageLimit);
		        if ($pageCount == 0) {
		            $pageCount = 1;
		        }
		        $search_results[$search_module]["PAGE_COUNT"] = $pageCount;
		        $query .= " LIMIT " . $startIndex . "," . ($pageLimit + 1);
		        if ($debug == "1") {
		            echo "query : " . $query . " <br><br>";
		        }
		        $result = $adb->pquery($query, array());
		        
		        $queryGenerator->reset();
		        
		        $noOfRows = $adb->num_rows($result);
		        
		        if ($search_module === 'Leads') {
		            for($i=0; $i<$noOfRows; ++$i) {
		                $row = $adb->query_result_rowdata($result, $i);
		                $leadIdsList[] = $row['leadid'];
		            }
		        }
		        
		        $convertedInfo = Leads_Module_Model::getConvertedInfo($leadIdsList);
		        
		        for($i=0, $recordsCount = 0; $i<$noOfRows && $recordsCount<100; ++$i) {
		            
		            $row = $adb->query_result_rowdata($result, $i);
		            
		            $row['crmid'] = $row[$moduleModel->get("basetableid")];
		            $row['setype'] = $search_module;
		            
		            if ($search_module === 'Leads' && $convertedInfo[$row['leadid']]) {
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
	
	public function getPhoneFields($module){
	    global $adb;
	    $arrFields = array();
	    $tabid = getTabid($module);
	    $sql = "SELECT fieldname,tablename FROM `vtiger_field` WHERE uitype=? AND tabid=?";
	    $rs = $adb->pquery($sql, array("11", $tabid));
	    if (0 < $adb->num_rows($rs)) {
	        while ($row = $adb->fetch_array($rs)) {
	            $arrFields[$row["fieldname"]] = $row["tablename"];
	        }
	    }
	    return $arrFields;
	}
	
	public function getPresenceFields($module){
	    global $adb;
	    $arrFields = array();
	    $tabid = getTabid($module);
	    $sql = "SELECT fieldname FROM `vtiger_field` WHERE tabid=? AND presence IN (0,2)";
	    $rs = $adb->pquery($sql, array($tabid));
	    if (0 < $adb->num_rows($rs)) {
	        while ($row = $adb->fetch_array($rs)) {
	            $arrFields[] = $row["fieldname"];
	        }
	    }
	    return $arrFields;
	}
	
	
	public function getGlobalSearchFields($module){
	    
	    global $adb;
	    
	    $moduleModel = Vtiger_Module_Model::getInstance($module);
	    $field = $adb->pquery("SELECT * FROM vtiger_globalsearch WHERE modulename = ?", 
	        array($module));
	    
	    if($adb->num_rows($field)){
	        $fields = $adb->query_result($field, 0, 'fieldname_show');
	       
	        $showField = array();
	        if($fields)
	           $showField = explode(',', $fields);
	          
	        if(!empty($showField)){
	          
	            foreach($showField as $shwField){
	               
	                $fieldDetails = explode(":", $shwField);
	               
	                $fieldInstance = Vtiger_Field_Model::getInstance($fieldDetails[2],$moduleModel);
	                
            	    $fieldInstance->set('listViewRawFieldName', $fieldInstance->get('column'));
            	    $headerFieldModels[$fieldDetails[2]] = $fieldInstance;
	            }
	        }
	        
	    }
        
	    return $headerFieldModels;
	    
	}
	
}
