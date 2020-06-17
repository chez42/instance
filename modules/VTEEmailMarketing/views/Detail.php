<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

class VTEEmailMarketing_Detail_View extends Vtiger_Detail_View
{
    public function __construct()
    {
        parent::__construct();
    }
    public function showModuleSummaryView($request)
    {
        global $adb;
        $recordId = $request->get("record");
        $moduleName = $request->getModule();
        $page = $request->get("page");
        if (!$page) {
            $page = 1;
        }
        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $moduleModel = $recordModel->getModule();
        $getRecordRelatedSentEmail = VTEEmailMarketing_Record_Model::getRecordRelatedSentEmail($recordId, $page);
        $pagination = VTEEmailMarketing_Record_Model::getPaginationRelatedSentEmail($recordId, $page);
        $rsScheduler = $adb->pquery("SELECT * FROM vtiger_vteemailmarketing_schedule WHERE vteemailmarketingid = ?", array($recordId));
        $emailTemplateId = $adb->query_result($rsScheduler, 0, "template_email_id");
        $viewer = $this->getViewer($request);
        $viewer->assign("EMAIL_TEMPLATE_ID", $emailTemplateId);
        $viewer->assign("PAGINATION", $pagination);
        $viewer->assign("RECORD_RELATED_SENT_MAIL", $getRecordRelatedSentEmail);
        $viewer->assign("RECORD", $recordModel);
        $viewer->assign("BLOCK_LIST", $moduleModel->getBlocks());
        $viewer->assign("USER_MODEL", Users_Record_Model::getCurrentUserModel());
        $viewer->assign("MODULE_NAME", $moduleName);
        $viewer->assign("IS_AJAX_ENABLED", $this->isAjaxEnabled($recordModel));
        $viewer->assign("SUMMARY_RECORD_STRUCTURE", $recordStrucure->getStructure());
        $viewer->assign("RELATED_ACTIVITIES", $this->getActivities($request));
        $viewer->assign("CURRENT_USER_MODEL", Users_Record_Model::getCurrentUserModel());
        $pagingModel = new Vtiger_Paging_Model();
        $viewer->assign("PAGING_MODEL", $pagingModel);
        $picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($moduleName);
        $viewer->assign("PICKIST_DEPENDENCY_DATASOURCE", Vtiger_Functions::jsonEncode($picklistDependencyDatasource));
        return $viewer->view("ModuleSummaryView.tpl", $moduleName, true);
    }
}

?>