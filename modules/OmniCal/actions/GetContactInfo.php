<?php

class OmniCal_GetContactInfo_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        if($request->get('contact_id') != 0){
            $contact_id = $request->get('contact_id');
            $contact = Contacts_Record_Model::getInstanceById($contact_id);
            $data = $contact->getData();
            $return_data = array("id" => $data['id'],
                                 "first_name" => $data['firstname'],
                                 "last_name" => $data['lastname'],
                                 "email" => $data['email'],
                                 "user_name" => "(contact)");
        }else{
            $user_id = $request->get('user_id');
            $contact = Users_Record_Model::getInstanceById($user_id, 'Users');
            $data = $contact->getData();
            $return_data = array("id" => $data['id'],
                                 "first_name" => $data['first_name'],
                                 "last_name" => $data['last_name'],
                                 "user_name" => $data['user_name'],
                                 "email" => $data['email1']);

        }
        
        echo json_encode($return_data);

    }
}

?>