<?php

class OmniMail_EmailMultiple_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        global $adb;
        $current_user = Users_Record_Model::getCurrentUserModel();        
        $emails['to'] = $current_user->get('email1');
        $emails['user_id'] = $current_user->get('id');
        $email_type = 'email';
        if($request->get('email_type') == "secondary")
            $email_type = 'secondaryemail';
        
        if($request->get('selected_ids') == 'all'){
            $list = Contacts_ListView_Model::getInstance('Contacts', $request->get('viewname'));
            $paging = new Vtiger_Paging_Model();
            $paging->set('limit', $list->getListViewCount());//set the limit to the number of entries returned
            $entries = $list->getListViewEntries($paging);

            foreach($entries AS $k => $v){
                $contact_info = Contacts_Record_Model::getInstanceById($k);
                $emails['bcc'] .= $contact_info->get($email_type) . ', ';
            }
        } else {
            foreach($request->get('selected_ids') AS $k => $v){
                $contact_info = Contacts_Record_Model::getInstanceById($v);
                $emails['bcc'] .= $contact_info->get($email_type) . ', ';
            }
        }
        echo json_encode($emails);
    }
}

?>