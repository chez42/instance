<?php

class OmniMail_Contact_Model extends Contacts_Module_Model{
    var $contact_id, $module, $action, $method, $subject, $fromname, $from, $toname, $to, $body, $fname, $lname, $office, $mobile, $homephone, $email;

    /**
     * Gets the record model for the contact, if the id doesn't already exist, it returns a clean instance
     * @param type $contact_id
     * @return type
     */
    public function GetContactRecordModel($contact_id){
        if($contact_id)
            $recordModel = Vtiger_Record_Model::getInstanceById($contact_id, 'Contacts');
        else
            $recordModel = Vtiger_record_Model::getCleanInstance ('Contacts');
        return $recordModel;
//        $x = new CustomV
    }
    
    /**
     * Fills in the class with the form data
     * @param Vtiger_Request $request
     */
    public function LoadInfo(Vtiger_Request $request){
        $this->module =     $request->get('module');
        $this->action =     $request->get('action');
        $this->method =     $request->get('method');
        $this->contact_id = $request->get('record_id');
        $this->subject =    htmlentities($request->get('subject'), ENT_QUOTES);
        $this->fromname =   htmlentities($request->get('fromname'), ENT_QUOTES);
        $this->from =       htmlentities($request->get('from'), ENT_QUOTES);
        $this->toname =     htmlentities($request->get('toname'), ENT_QUOTES);
        $this->to =         htmlentities($request->get('to'), ENT_QUOTES);
        $this->body =       htmlentities($request->get('body'), ENT_QUOTES);
        $this->fname =      htmlentities($request->get('fname'), ENT_QUOTES);
        $this->lname =      htmlentities($request->get('lname'), ENT_QUOTES);
        
        if(!strlen($this->lname))//If a last name doesn't exist, we split the first and last automatically if there's a space in it
        {
            list($fname, $lname) = split(' ', $this->fromname,2);
            $this->fname = $fname;
            $this->lname = $lname;
        }
        $this->office = htmlentities($request->get('office'), ENT_QUOTES);
        $this->mobile = htmlentities($request->get('mobile'), ENT_QUOTES);
        $this->homephone = htmlentities($request->get('homephone'), ENT_QUOTES);
        $this->email = htmlentities($request->get('email'), ENT_QUOTES);
    }
}

?>