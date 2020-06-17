<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
chdir(dirname(__FILE__) . "/../../../");
require_once "includes/Loader.php";
require_once "include/utils/utils.php";
vimport("includes.http.Request");
vimport("includes.runtime.Globals");
vimport("includes.runtime.BaseModel");
vimport("includes.runtime.Controller");
vimport("includes.runtime.LanguageHandler");
class VTEEmailMarketing_TrackAccess_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        if (vglobal("application_unique_key") !== $request->get("applicationKey")) {
            exit;
        }
        if (strpos($_SERVER["HTTP_REFERER"], vglobal("site_URL")) !== false) {
            exit;
        }
        global $current_user;
        global $adb;
        $current_user = Users::getActiveAdminUser();
        if ($request->get("method") == "click") {
            $this->clickHandler($request);
        } else {
            $parentId = $request->get("parentId");
            $recordId = $request->get("record");
            if ($parentId && $recordId) {
                $recordModel = Emails_Record_Model::getInstanceById($recordId);
                $recordModel->updateTrackDetails($parentId);
                Vtiger_ShortURL_Helper::sendTrackerImage();
                $this->updateTrackEmailOnVEM($parentId);
            }
        }
    }
    public function updateTrackEmailOnVEM($parentId)
    {
        global $adb;
        $rsCheckModule = $adb->pquery("SELECT * FROM vtiger_crmentity WHERE crmid = ?", array($parentId));
        $module = $adb->query_result($rsCheckModule, 0, "setype");
        if ($module == "VTEEmailMarketing") {
            $rsGetTracking = $adb->pquery("SELECT COUNT(*) as 'count_track' FROM vtiger_email_track WHERE crmid = ? AND access_count > 0", array($parentId));
            $rsGetDelivery = $adb->pquery("SELECT sent FROM vtiger_vteemailmarketingcf WHERE vteemailmarketingid = ?", array($parentId));
            $sent = $adb->query_result($rsGetDelivery, 0, "sent");
            $count_track = $adb->query_result($rsGetTracking, 0, "count_track");
            $unopend = intval($sent) - intval($count_track);
            $adb->pquery("UPDATE vtiger_vteemailmarketingcf SET `unique_open` = ?, `unopened` = ? WHERE vteemailmarketingid = ?", array($count_track, $unopend, $parentId));
        }
    }
    public function clickHandler(Vtiger_Request $request)
    {
        $parentId = $request->get("parentId");
        $recordId = $request->get("record");
        if ($parentId && $recordId) {
            $recordModel = Emails_Record_Model::getInstanceById($recordId);
            $recordModel->trackClicks($parentId);
        }
        $redirectUrl = $request->get("redirectUrl");
        if (!empty($redirectUrl)) {
            return Vtiger_Functions::redirectUrl(rawurldecode($redirectUrl));
        }
    }
}
$track = new VTEEmailMarketing_TrackAccess_Action();
$track->process(new Vtiger_Request($_REQUEST));

?>