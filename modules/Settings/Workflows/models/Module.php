<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'modules/com_vtiger_workflow/include.inc';
require_once 'modules/com_vtiger_workflow/expression_engine/VTExpressionsManager.inc';

class Settings_Workflows_Module_Model extends Settings_Vtiger_Module_Model {

	var $baseTable = 'com_vtiger_workflows';
	var $baseIndex = 'workflow_id';
//	var $listFields = array('summary' => 'Summary', 'module_name' => 'Module', 'execution_condition' => 'Execution Condition');
	var $listFields = array('module_name' => 'Module', 'workflowname' => 'Workflow Name', 'summary'=>'Description', 'execution_condition' => 'Trigger',  'test' => 'Conditions');
	var $name = 'Workflows';

	static $metaVariables = array(
		'Current Date' => '(general : (__VtigerMeta__) date) ($_DATE_FORMAT_)',
		'Current Time' => '(general : (__VtigerMeta__) time)',
		'System Timezone' => '(general : (__VtigerMeta__) dbtimezone)',
		'User Timezone' => '(general : (__VtigerMeta__) usertimezone)',
		'CRM Detail View URL' => '(general : (__VtigerMeta__) crmdetailviewurl)',
		'Portal Detail View URL' => '(general : (__VtigerMeta__) portaldetailviewurl)',
		'Site Url' => '(general : (__VtigerMeta__) siteurl)',
		'Portal Url' => '(general : (__VtigerMeta__) portalurl)',
		'Record Id' => '(general : (__VtigerMeta__) recordId)',
		'LBL_HELPDESK_SUPPORT_NAME' => '(general : (__VtigerMeta__) supportName)',
		'LBL_HELPDESK_SUPPORT_EMAILID' => '(general : (__VtigerMeta__) supportEmailid)',
	);

	static $triggerTypes = array(
		1 => 'ON_FIRST_SAVE',
		2 => 'ONCE',
		3 => 'ON_EVERY_SAVE',
		4 => 'ON_MODIFY',
		// Reserving 5 & 6 for ON_DELETE and ON_SCHEDULED types.
		6=>	 'ON_SCHEDULE'
	);

	/**
	 * Function to get the url for default view of the module
	 * @return <string> - url
	 */
	public static function getDefaultUrl() {
		return 'index.php?module=Workflows&parent=Settings&view=List';
	}

	/**
	 * Function to get the url for create view of the module
	 * @return <string> - url
	 */
	public static function getCreateViewUrl() {
		return "javascript:Settings_Workflows_List_Js.triggerCreate('index.php?module=Workflows&parent=Settings&view=Edit')";
	}

	public static function getCreateRecordUrl() {
		return 'index.php?module=Workflows&parent=Settings&view=Edit';
	}

	public static function getSupportedModules() {
		$moduleModels = Vtiger_Module_Model::getAll(array(0,2));
		$supportedModuleModels = array();
		foreach($moduleModels as $tabId => $moduleModel) {
			if($moduleModel->isWorkflowSupported() && $moduleModel->getName() != 'Webmails') {
				$supportedModuleModels[$tabId] = $moduleModel;
			}
		}
		return $supportedModuleModels;
	}

	public static function getTriggerTypes() {
		return self::$triggerTypes;
	}

	public static function getExpressions() {
		$db = PearDatabase::getInstance();

		$mem = new VTExpressionsManager($db);
		return $mem->expressionFunctions();
	}

	public static function getMetaVariables() {
		return self::$metaVariables;
	}

	public function getListFields() {
		if(!$this->listFieldModels) {
			$fields = $this->listFields;
			$fieldObjects = array();
			foreach($fields as $fieldName => $fieldLabel) {
				if($fieldName == 'module_name' || $fieldName == 'execution_condition') {
					$fieldObjects[$fieldName] = new Vtiger_Base_Model(array('name' => $fieldName, 'label' => $fieldLabel, 'sort'=>false));
				} else {
					$fieldObjects[$fieldName] = new Vtiger_Base_Model(array('name' => $fieldName, 'label' => $fieldLabel));
				}
			}
			$this->listFieldModels = $fieldObjects;
		}
		return $this->listFieldModels;
	}

	/**
	 * Function to get the count of active workflows
	 * @return <Integer> count of active workflows
	 */
	public function getActiveWorkflowCount($moduleCount = false){
		$db = PearDatabase::getInstance();

		$query = 'SELECT count(*) AS count, vtiger_tab.tabid FROM com_vtiger_workflows 
				  INNER JOIN vtiger_tab ON vtiger_tab.name = com_vtiger_workflows.module_name 
				  AND vtiger_tab.presence IN (0,2) WHERE com_vtiger_workflows.status = ? ';

		if($moduleCount){
		   $query .=' GROUP BY com_vtiger_workflows.module_name';
		}

		$result = $db->pquery($query, array(1));
		$count = 0;
		$wfModulesCount = array();
		$noOfRows = $db->num_rows($result);
		for($i=0; $i<$noOfRows; ++$i) {
			$row = $db->query_result_rowdata($result, $i);
			$count = $count+$row['count'];
			$wfModulesCount[$row['tabid']] = $row['count'];
		}

		if($moduleCount){
		   $wfModulesCount['All'] = $count;
		   return $wfModulesCount;
		} else {
		   return $count;
		}

	}

	public function getFields() {
	   return array();
	}

	public function getModuleBasicLinks(){
	   return array();
	}
	
	/**
	 * Function retrives all company details merge tags and add to field array
	 * @return string
	 */
	function getCompanyMergeTagsInfo(){
	    global $site_URL;
	    $companyModuleModel = Settings_Vtiger_CompanyDetails_Model::getInstance();
	    $basicFields = $companyModuleModel->companyBasicFields;
	    $socialFields = $companyModuleModel->companySocialLinks;
	    $qualifiedModule = "Settings:Vtiger";
	    $moduleName = vtranslate("LBL_COMPANY_DETAILS", $qualifiedModule);
	    $allFields = array();
	    $logoPath = $site_URL . '/' . $companyModuleModel->getLogoPath();
	    foreach ($basicFields as $columnName => $value) {
	        //For column logo we need place logo in content
	        if($columnName == 'brochure')
	            continue;
	        
	        if($columnName == 'logo'){
	            $allFields[] = array($moduleName.':'. vtranslate($columnName, $qualifiedModule),"$$columnName$");
	        } else {
	            $allFields[] = array($moduleName.':'. vtranslate($columnName, $qualifiedModule),"$".strtolower("companydetails")."-".$columnName."$");
	        }
	    }
	    // Social links will be having hyperlink redirected to URL mentioned
	    foreach($socialFields as $columnName => $value){
	        $url = $companyModuleModel->get($columnName);
	        if($columnName == 'website'){
	            $websiteURL = $url;
	            if(empty($url)){
	                $websiteURL = $columnName;
	            }
	            $allFields[] = array($moduleName.':'. vtranslate($columnName, $qualifiedModule),"<a target='_blank' href='".$url."'>$websiteURL</a>");
	        } else {
	            $allFields[] = array($moduleName.':'. vtranslate($columnName, $qualifiedModule),"<a target='_blank' href='".$url."'>$columnName</a>");
	        }
	    }
	    return $allFields;
	} 
	
	function getCompanyBrochure(){
	    
	    global $adb;
	    
	    $query = $adb->pquery("SELECT * FROM vtiger_attachments 
        INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_attachments.attachmentsid
        INNER JOIN vtiger_organization_attachmentsrel ON 
        vtiger_organization_attachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
        WHERE vtiger_crmentity.deleted = 0 AND vtiger_crmentity.setype = 'Company Brochure'");
	    
	    $allFiles = array();
	    
	    if($adb->num_rows($query)){
	        
	        for($i=0;$i<$adb->num_rows($query);$i++){
	            
	            $fileData = $adb->query_result_rowdata($query, $i);
	            $allFiles[] = array('Company Files:'.substr($fileData['name'], 0, strpos($fileData['name'], ".")), "<a target='_blank' href='".$fileData['short_url']."'>".$fileData['name']."</a>");
	            
	        }
	        
	    }
	   
	    return $allFiles;
	    
	}
}
