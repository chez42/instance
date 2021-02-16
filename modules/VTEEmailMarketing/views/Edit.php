<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_Edit_View extends Vtiger_Edit_View
{
    public function __construct()
    {
        parent::__construct();
        //$this->vteLicense();
        $this->exposeMethod("createView");
    }
    public function process(Vtiger_Request $request)
    {
        $mode = $request->getMode();
        
        if (!empty($mode)) {
            echo $this->invokeExposedMethod($mode, $request);
            exit;
        }
        $recordId = $request->get("record");
        if (!$recordId) {
            $this->createView($request);
        } else {
            $this->editView($request);
        }
    }
    public function vteLicense()
    {
        $vTELicense = new VTEEmailMarketing_VTELicense_Model("VTEEmailMarketing");
        if (!$vTELicense->validate()) {
            header("Location: index.php?module=VTEEmailMarketing&parent=Settings&view=Settings&mode=step2");
        }
    }
    public function createView(Vtiger_Request $request)
    {
        global $adb;
        global $current_user;
        $record = $request->get("record");
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        if (!empty($record) && $request->get("isDuplicate") == true) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign("MODE", "");
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get("name");
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, "");
                    }
                }
            }
        } else {
            if (!empty($record)) {
                $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
                $viewer->assign("RECORD_ID", $record);
                $viewer->assign("MODE", "edit");
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
                $viewer->assign("MODE", "");
            }
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel = $recordModel->getModule();
        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAllPurified(), $fieldList);
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            $specialField = false;
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $relationId = VTEEmailMarketing_Record_Model::getRelationId();
        //$filter = VTEEmailMarketing_Record_Model::getFilter();
        $dateByUser = DateTimeField::convertToUserTimeZone(date("Y-m-d H:i:s"));
        $dateByUserFormat = DateTimeField::convertToUserFormat($dateByUser->format("Y-m-d H:i:s"));
        list($currentDateUser, $currentTimeUser) = explode(" ", $dateByUserFormat);
      /*  if (class_exists("MultipleSMTP_VTELicense_Model")) {
            $vteMultipleSMTP = new MultipleSMTP_VTELicense_Model("MultipleSMTP");
            $checkValidMultipleSMTP = $vteMultipleSMTP->validate();
            if ($checkValidMultipleSMTP) {
                $rsCheckEnableMultipleSMTP = $adb->pquery("SELECT * FROM multiple_smtp_settings");
                $checkEnableMultipleSMTP = $adb->query_result($rsCheckEnableMultipleSMTP, 0, "enable");
                $moduleMultipleSMTP = Vtiger_Module::getInstance("MultipleSMTP");
                $enableModuleMultipleSMTPE = $moduleMultipleSMTP->presence;
                if ($checkEnableMultipleSMTP == 1 && ($enableModuleMultipleSMTPE == 0 || $enableModuleMultipleSMTPE == 2)) {
                    $query = "SELECT vte_multiple_smtp.*,vtiger_users.first_name,vtiger_users.last_name FROM vte_multiple_smtp INNER JOIN vtiger_users ON vtiger_users.id = vte_multiple_smtp.userid";
                    if (!$current_user->is_admin) {
                        $query .= " WHERE userid = " . $current_user->id;
                    }
                    $query .= " ORDER BY server_username ASC";
                    $rsRecordsSMTP = $adb->pquery($query);
                    $numrows = $adb->num_rows($rsRecordsSMTP);
                    for ($i = 0; $i < $numrows; $i++) {
                        $recordsSMTP[$i] = $adb->fetchByAssoc($rsRecordsSMTP, $i);
                    }
                    $viewer->assign("RECORD_SMTP", $recordsSMTP);
                    $viewer->assign("MULTIPLE_SMTP_ENABLE", true);
                }
            }
        }*/
        $viewer->assign("CURRENT_DATE_USER", $currentDateUser);
        $viewer->assign("CURRENT_TIME_USER", $currentTimeUser);
        $viewer->assign("RECORD_STRUCTURE_MODEL", $recordStructureInstance);
        $viewer->assign("RECORD_STRUCTURE", $recordStructureInstance->getStructure());
        $viewer->assign("MODULE", $moduleName);
        $viewer->assign("CURRENTDATE", date("Y-n-j"));
        $viewer->assign("USER_MODEL", Users_Record_Model::getCurrentUserModel());
        $viewer->assign("RELATION_ID", $relationId);
        $viewer->assign("RECORDMODEL", $recordModel);
        //$viewer->assign("FILTER", $filter);
        
        $emailTemplates = $this->getAllEmailTemplates();
        $viewer->assign('EMAIL_TEMPLATES', $emailTemplates);
        
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $userId = $currentUser->getId();
        $result = $adb->pquery("SELECT * FROM vtiger_mail_accounts WHERE user_id=?", array($userId));
        if ($adb->num_rows($result)) {
            for($u=0;$u<$adb->num_rows($result);$u++){
                $list_servers[$u]['account_id'] = $adb->query_result($result, $u, 'account_id');
                $list_servers[$u]['account_name'] = $adb->query_result($result, $u, 'from_email');
                $list_servers[$u]['default'] = $adb->query_result($result, $u, 'set_default');
            }
        }
        
        $viewer->assign('LIST_SERVERS', $list_servers);
        
        $viewer->view("EditView.tpl", $moduleName);
    }
    public function editView(Vtiger_Request $request)
    {
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();
        $record = $request->get("record");
        if (!empty($record) && $request->get("isDuplicate") == true) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign("MODE", "");
            $mandatoryFieldModels = $recordModel->getModule()->getMandatoryFieldModels();
            foreach ($mandatoryFieldModels as $fieldModel) {
                if ($fieldModel->isReferenceField()) {
                    $fieldName = $fieldModel->get("name");
                    if (Vtiger_Util_Helper::checkRecordExistance($recordModel->get($fieldName))) {
                        $recordModel->set($fieldName, "");
                    }
                }
            }
        } else {
            if (!empty($record)) {
                $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
                $viewer->assign("RECORD_ID", $record);
                $viewer->assign("MODE", "edit");
            } else {
                $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
                $viewer->assign("MODE", "");
            }
        }
        if (!$this->record) {
            $this->record = $recordModel;
        }
        $moduleModel = $recordModel->getModule();
        $fieldList = $moduleModel->getFields();
        $requestFieldList = array_intersect_key($request->getAllPurified(), $fieldList);
        $relContactId = $request->get("contact_id");
        if ($relContactId && $moduleName == "Calendar") {
            $contactRecordModel = Vtiger_Record_Model::getInstanceById($relContactId);
            $requestFieldList["parent_id"] = $contactRecordModel->get("account_id");
        }
        foreach ($requestFieldList as $fieldName => $fieldValue) {
            $fieldModel = $fieldList[$fieldName];
            $specialField = false;
            if ($moduleName == "Calendar" && empty($record) && $fieldName == "time_start" && !empty($fieldValue)) {
                $specialField = true;
                $fieldValue = DateTimeField::convertToDBTimeZone($fieldValue)->format("H:i");
            }
            if ($moduleName == "Calendar" && empty($record) && $fieldName == "date_start" && !empty($fieldValue)) {
                $startTime = Vtiger_Time_UIType::getTimeValueWithSeconds($requestFieldList["time_start"]);
                $startDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($fieldValue . " " . $startTime);
                list($startDate, $startTime) = explode(" ", $startDateTime);
                $fieldValue = Vtiger_Date_UIType::getDisplayDateValue($startDate);
            }
            if ($fieldModel->isEditable() || $specialField) {
                $recordModel->set($fieldName, $fieldModel->getDBInsertValue($fieldValue));
            }
        }
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_EDIT);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign("RECORD", $record);
        $viewer->assign("PICKIST_DEPENDENCY_DATASOURCE", Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
        $viewer->assign("RECORD_STRUCTURE_MODEL", $recordStructureInstance);
        $viewer->assign("RECORD_STRUCTURE", $recordStructureInstance->getStructure());
        $viewer->assign("MODULE", $moduleName);
        $viewer->assign("CURRENTDATE", date("Y-n-j"));
        $viewer->assign("USER_MODEL", Users_Record_Model::getCurrentUserModel());
        $isRelationOperation = $request->get("relationOperation");
        $viewer->assign("IS_RELATION_OPERATION", $isRelationOperation);
        if ($isRelationOperation) {
            $viewer->assign("SOURCE_MODULE", $request->get("sourceModule"));
            $viewer->assign("SOURCE_RECORD", $request->get("sourceRecord"));
        }
        if ($request->get("returnview")) {
            $request->setViewerReturnValues($viewer);
        }
        $viewer->assign("MAX_UPLOAD_LIMIT_MB", Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign("MAX_UPLOAD_LIMIT_BYTES", Vtiger_Util_Helper::getMaxUploadSizeInBytes());
        if ($request->get("displayMode") == "overlay") {
            $viewer->assign("SCRIPTS", $this->getOverlayHeaderScripts($request));
            $viewer->view("OverlayEditView.tpl", "Vtiger");
        } else {
            if($recordModel->get('vteemailmarketing_status')){
                $viewer->view("EditView.tpl", "Vtiger");
            }else{
                global $adb;
                $currentUser = Users_Record_Model::getCurrentUserModel();
                $userId = $currentUser->getId();
                $result = $adb->pquery("SELECT * FROM vtiger_mail_accounts WHERE user_id=?", array($userId));
                if ($adb->num_rows($result)) {
                    for($u=0;$u<$adb->num_rows($result);$u++){
                        $list_servers[$u]['account_id'] = $adb->query_result($result, $u, 'account_id');
                        $list_servers[$u]['account_name'] = $adb->query_result($result, $u, 'from_email');
                        $list_servers[$u]['default'] = $adb->query_result($result, $u, 'set_default');
                    }
                }
                
                $viewer->assign('LIST_SERVERS', $list_servers);
                $emailTemplates = $this->getAllEmailTemplates();
                $viewer->assign('EMAIL_TEMPLATES', $emailTemplates);
                $template = $adb->pquery("SELECT template_email_id FROM vtiger_vteemailmarketing_schedule WHERE vteemailmarketingid = ?",
                    array($record));
                if($adb->num_rows($template)){
                    $viewer->assign("TEMPLATEID", $adb->query_result($template, 0, 'template_email_id'));
                }
                $viewer->assign("RECORDMODEL", $recordModel);
                $viewer->view("EditView.tpl", $moduleName);
            }
            
        }
    }
    public function getHeaderCss(Vtiger_Request $request)
    {
        $headerCssInstances = parent::getHeaderCss($request);
        $cssFileNames = array("~layouts/" . Vtiger_Viewer::getDefaultLayoutName() . "/modules/VTEEmailMarketing/resources/Styles.css");
        $cssInstances = $this->checkAndConvertCssStyles($cssFileNames);
        $headerCssInstances = array_merge($headerCssInstances, $cssInstances);
        return $headerCssInstances;
    }
    public function getHeaderScripts(Vtiger_Request $request)
    {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array("modules.Vtiger.resources.Detail", "modules.Vtiger.resources.RelatedList", "modules.CustomView.resources.CustomView", "modules." . $moduleName . ".resources.CustomView", "modules." . $moduleName . ".resources.Create", "~/" . Vtiger_Viewer::getDefaultLayoutName() . "/resources/helper.js");
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }
    
    function getAllEmailTemplates(){
        
        global $adb;
        
        $result = $adb->pquery("select * from vtiger_emailtemplates");
        
        $emailTemplates = array();
        
        if($adb->num_rows($result)){
            
            while($row = $adb->fetchByAssoc($result)){
                
                $emailTemplates[$row['templateid']] = decode_html($row['templatename']);
                
            }
        }
        
        return $emailTemplates;
    }
    
}


?>