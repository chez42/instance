<?php
/*
 * @ PHP 5.6
 * @ Decoder version : 1.0.0.1
 * @ Release on : 24.03.2018
 * @ Website    : http://EasyToYou.eu
 */

chdir("../../../");
require_once "includes/Loader.php";
require_once "include/utils/utils.php";
vimport("includes.http.Request");
vimport("includes.runtime.Globals");
vimport("includes.runtime.BaseModel");
vimport("includes.runtime.Controller");
vimport("includes.runtime.LanguageHandler");
class VTEEmailMarketing_Unsubcribes_Action extends Vtiger_Action_Controller
{
    public function process(Vtiger_Request $request)
    {
        if (strpos($_SERVER["HTTP_REFERER"], vglobal("site_URL")) !== false) {
            exit;
        }
        global $current_user;
        global $adb;
        global $site_URL;
        $current_user = Users::getActiveAdminUser();
        $recordIds = $request->get("parent");
        $recordIdEM = $request->get("record");
        $keyUnsubcribe = $request->get("key");
        $params = array($recordIds, $keyUnsubcribe);
        $result = $adb->pquery("SELECT * FROM vtiger_vteemailmarketing_unsubcribes WHERE crmid = ? AND `key` = ?", $params);
        if (0 < $adb->num_rows($result)) {
            $adb->pquery("UPDATE vtiger_vteemailmarketing_unsubcribes SET status = 1, vteemailmarketingid = " . $recordIdEM . " WHERE crmid = ? AND `key` = ?", $params);
            $rsCountUns = $adb->pquery("SELECT COUNT(*) as 'count' FROM vtiger_vteemailmarketing_unsubcribes WHERE status = 1 AND vteemailmarketingid = ?", array($recordIdEM));
            $countUns = $adb->query_result($rsCountUns, 0, "count");
            $adb->pquery("UPDATE vtiger_vteemailmarketingcf SET `unsubcribes` = ? WHERE vteemailmarketingid = ?", array($countUns, $recordIdEM));
        } else {
            header("Location: " . $site_URL);
        }
    }
}
$track = new VTEEmailMarketing_Unsubcribes_Action();
$track->process(new Vtiger_Request($_REQUEST));
echo "\n<!doctype html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\"\n          content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">\n    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">\n    <title>Sorry to see you go!</title>\n    <link rel=\"SHORTCUT ICON\" href=\"../../../layouts/v7/skins/images/favicon.ico\">\n    <link type=\"text/css\" rel=\"stylesheet\" href=\"../../../layouts/v7/lib/todc/css/bootstrap.min.css\">\n    <script src=\"../../../layouts/v7/lib/jquery/jquery.min.js?v=7.0.1\"></script>\n\n</head>\n<body>\n<div style=\"margin-top: 150px\">\n    <h1 class=\"text-center\" style=\"font-size: 50px; margin-bottom: 25px\">Sorry to see you go!</h1>\n    <h4 class=\"text-center\" style=\"margin-bottom: 25px\">You will no longer receive any emails from us. You can now close this page.</h4>\n    <div class=\"alert alert-info\" style=\"width: 300px;height: 70px;FONT-SIZE: 30px;margin: 0 auto;color: WHITE;background:  lightseagreen;text-align: center;font-weight:  bold;\">UNSUBCRIBED!</div>\n</div>\n</body>\n\n<script type=\"text/javascript\" src=\"../../../layouts/v7/lib/todc/js/bootstrap.min.js\"></script>\n</html>";

?>