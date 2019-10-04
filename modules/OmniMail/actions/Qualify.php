<?php

class OmniMail_Qualify_Action extends Vtiger_QuickCreateAjax_View{
    public function process(Vtiger_Request $request) {
        switch($request->get('method')){
            case "create_contact":
                $contact = new OmniMail_Contact_View();
                echo $contact->process($request);
                break;
            case "save_contact":
                $action = new OmniMail_Save_Action();
                $action->process($request);
                break;
            case "create_lead":
                $lead = new OmniMail_Lead_View();
                echo $lead->process($request);
                break;
            case "save_lead";
                $action = new OmniMail_Save_Action();
                $action->process($request);
                break;
        }
    }
}

?>