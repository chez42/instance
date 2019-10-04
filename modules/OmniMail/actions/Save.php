<?php

class OmniMail_Save_Action extends Vtiger_SaveAjax_Action{
    public function process(Vtiger_Request $request) {
        switch($request->get('method')){
            case "save_contact"://Mark the activity_id as completed
                $this->SaveContactEmail($request);
                break;
            case "save_lead":
                $this->SaveLeadEmail($request);
                break;
        }
    }
//Array ( [salutationtype] => [firstname] => [contact_no] => [phone] => [lastname] => [mobile] => [account_id] => [homephone] => [leadsource] => [otherphone] => [title] => [fax] => 
//[birthday] => [email] => [assistant] => [secondaryemail] => [assistantphone] => [emailoptout] => [assigned_user_id] => [createdtime] => [modifiedtime] => [portal] => 
//[support_start_date] => [support_end_date] => [mailingstreet] => [mailingcity] => [mailingstate] => [mailingzip] => [mailingcountry] => [imagename] => [description] => 
//[campaignrelstatus] => [ssn] => [cf_634] => [cf_635] => [cf_636] => [cf_637] => [cf_638] => [cf_639] => [cf_641] => [cf_642] => [cf_660] => [cf_661] => [cf_662] => [cf_663] => 
//[cf_664] => [cf_665] => [cf_666] => [cf_667] => [cf_668] => [cf_675] => [cf_676] => [cf_677] => [cf_678] => [cf_683] => [cf_697] => [cf_698] => [cf_712] => [cf_721] => [cf_724] => 
//[cf_725] => [cf_727] => [cf_732] => [cf_736] => [cf_737] => [cf_764] => [cf_784] => [cf_785] => [cf_786] => [cf_805] => [cf_806] => [cf_807] => [cf_808] => [cf_809] => [cf_810] => )
//var $contact_id, $module, $action, $method, $subject, $fromname, $from, $toname, $to, $body, $fname, $lname;
    public function SaveContactEmail(Vtiger_Request $request){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $contact_model = new OmniMail_Contact_Model();
        $contact_model->LoadInfo($request);
        $record = $contact_model->GetContactRecordModel($request->get('record_id'));
        $data = $record->getData();
        $contact_id = $contact_model->contact_id;
        
        //The contact doens't exist, we need to create it
        if(!$contact_model->contact_id){
            $data['firstname'] = $contact_model->fname;
            $data['lastname'] = $contact_model->lname;
            $data['email'] = $contact_model->email;
            $data['homephone'] = $contact_model->homephone;
            $data['mobile'] = $contact_model->mobile;
            $data['phone'] = $contact_model->office;
            $data['assigned_user_id'] = $currentUserModel->get('id');
            $record->set('mode', 'create');
            $record->setData($data);
            $record->save();
            $contact_id = $record->get('id');
        }
        
        $email = Vtiger_record_Model::getCleanInstance('Emails');
        $data = $email->getData();
        $data['subject'] = $contact_model->subject;
        $data['description'] = $contact_model->body;
        $data['from_email'] = $contact_model->fromname . ' <' . $contact_model->from . '>';
        $data['email_flag'] = "WEBMAIL";
        $data['parent_id'] = $contact_id . "@-1|";
        $data['saved_toid'] = $contact_model->toname . ' <' . $contact_model->to . '>';
        $data['assigned_user_id'] = $currentUserModel->get('id');
        $email->setData($data);
        $email->set('mode', 'create');
        $email->save();
        
        echo "Theoretically, this saved...";
    }
    
    public function SaveLeadEmail(Vtiger_Request $request){
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $lead_model = new OmniMail_Lead_Model();
        $lead_model->LoadInfo($request);
        $record = $lead_model->GetLeadRecordModel($request->get('record_id'));
        $data = $record->getData();
        $lead_id = $lead_model->lead_id;

        //The contact doens't exist, we need to create it
        if(!$lead_model->lead_id){
            $data['firstname'] = $lead_model->fname;
            $data['lastname'] = $lead_model->lname;
            $data['email'] = $lead_model->email;
            $data['cf_704'] = $lead_model->homephone;
            $data['mobile'] = $lead_model->mobile;
            $data['phone'] = $lead_model->office;
            $data['assigned_user_id'] = $currentUserModel->get('id');
            $record->set('mode', 'create');
            $record->setData($data);
            $record->save();
            $lead_id = $record->get('id');
        }
        
        $email = Vtiger_record_Model::getCleanInstance('Emails');
        $data = $email->getData();
        $data['subject'] = $lead_model->subject;
        $data['description'] = $lead_model->body;
        $data['from_email'] = $lead_model->fromname . ' <' . $lead_model->from . '>';
        $data['email_flag'] = "WEBMAIL";
        $data['parent_id'] = $lead_id . "@-1|";
        $data['saved_toid'] = $lead_model->toname . ' <' . $lead_model->to . '>';
        $data['assigned_user_id'] = $currentUserModel->get('id');
        $email->setData($data);
        $email->set('mode', 'create');
        $email->save();
        
        echo "Theoretically, this saved...";
    }
}

?>
