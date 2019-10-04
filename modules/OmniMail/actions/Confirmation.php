<?php
/*
This file returns the json filled with instructions on what javascript should do based on the result.
Contacts for example will send back an action id of 1 if the email already exists in the system, or 2
if it doesn't.
*/
class OmniMail_Confirmation_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $to = $request->get('to');
        $from = $request->get('from');
        $body = $request->get('body');//not currently used, but may be used to check against currently existing emails
        $subject = $request->get('subject');
        $action = $request->get('qualify');

        global $adb, $currentModule;

        if(!strlen($from)){
            $actionid = 0;
            $message = "There is an error retrieving the senders email address, please ensure 'Show Preview Pane' is checked and try again";
        }
        else
        if($action == "Contacts")
        {
            $query = "SELECT * FROM vtiger_contactdetails WHERE email = ?";
            $result = $adb->pquery($query, array($from));
            if($adb->num_rows($result) > 0)
            {
                $actionid = 1;
                $message = "The contact email address {$from} already exists, would you like to add this email to their message list?";
                $record_id = $adb->query_result($result, 0, "contactid");
            }
            else
            {
                $actionid = 2;
                $message = "The contact email {$from} does not exist, would you like to add them to your contacts list?";
            }
        }
        else
        if($action == "Leads")
        {
            $query = "SELECT * FROM vtiger_leaddetails WHERE email = ?";
            $result = $adb->pquery($query, array($from));
            if($adb->num_rows($result) > 0)
            {
                $actionid = 3;
                $message = "The lead email address {$from} already exists, would you like to add this email to their message list?";
                $record_id = $adb->query_result($result, 0, "leadid");
            }
            else
            {
                $actionid = 4;
                $message = "The lead email {$from} does not exist, would you like to add them to your leads list?";
            }
        }

        $tmp = array("actionid"=>$actionid,
                     "record_id"=>$record_id,
                     "message"=>$message);

        echo json_encode($tmp);
    }
}
?>