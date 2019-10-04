<?php
include_once("libraries/vcard/vcard_class.php");
include_once("include/utils/omniscientCustom.php");
class Contacts_VCard_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $contactid = $request->get('record');
        
        $contact_info = Contacts_Record_Model::getInstanceById($contactid);

        /*
        Instantiate a new vcard object.
        */
        ob_clean();
        $vc = new vcard();

        /*
        Contact's name data.
        If you leave display_name blank, it will be built using the first and last name.
        */

        $employerName = GetFieldNameFromFieldLabel("Employer Name");
        $employerPhone = GetFieldNameFromFieldLabel("Employer Phone Number");
        $employerState = GetFieldNameFromFieldLabel("Employer State");
        $employerPostal = GetFieldNameFromFieldLabel("Employer Postal Code");
        $employerWebsite = GetFieldNameFromFieldLabel("Employer Website");
        $employerStreet1 = GetFieldNameFromFieldLabel("Employer Street Address 1");
        $employerStreet2 = GetFieldNameFromFieldLabel("Employer Street Address 2");
        $employerCity = GetFieldNameFromFieldLabel("Employer City");
        $employerFax = GetFieldNameFromFieldLabel("Employer Fax");
        $nickname = GetFieldNameFromFieldLabel("Nickname");

        $vc->data['first_name'] = $contact_info->get("firstname");
        $vc->data['last_name'] = $contact_info->get("lastname");

        /*
        Contact's company, department, title, profession
        */
        $vc->data['company'] = $contact_info->get("{$employerName}");
        #$vc->data['department'] = "";
        $vc->data['title'] = $contact_info->get("title");//"Web Developer";
        #$vc->data['role'] = "";

        /*
        Contact's work address
        */
        #$vc->data['work_po_box'] = "";
        #$vc->data['work_extended_address'] = "";
        $vc->data['work_address'] = $contact_info->get("{$employerStreet1}");//"7027 N. Hickory";
        $vc->data['work_city'] = $contact_info->get("{$employerCity}");//"Kansas City";
        $vc->data['work_state'] = $contact_info->get("{$employerState}");//"MO";
        $vc->data['work_postal_code'] = $contact_info->get("{$employerPostal}");//"64118";
        #$vc->data['work_country'] = "United States of America";

        /*
        Contact's home address
        */
        #$vc->data['home_po_box'] = "";
        #$vc->data['home_extended_address'] = "";
        $vc->data['home_address'] = $contact_info->get("mailingstreet");//"7027 N. Hickory";
        $vc->data['home_city'] = $contact_info->get("mailingcity");//"Kansas City";
        $vc->data['home_state'] = $contact_info->get("mailingstate");//"MO";
        $vc->data['home_postal_code'] = $contact_info->get("mailingzip");//"64118";
        #$vc->data['home_country'] = "United States of America";

        /*
        Contact's telephone numbers.
        */
        $vc->data['office_tel'] = $contact_info->get("phone");//"";
        $vc->data['employer_phone'] = $contact_info->get("{$employerPhone}");//"";
        $vc->data['home_tel'] = $contact_info->get("homephone");//"";
        $vc->data['cell_tel'] = $contact_info->get("mobile");//"(816) 739-9653";
        $vc->data['fax_tel'] = $contact_info->get("fax");//"";
        #$vc->data['pager_tel'] = "";

        /*
        Contact's email addresses
        */
        $vc->data['email1'] = $contact_info->get("email");//"troy@troywolf.com";
        $vc->data['email2'] = $contact_info->get("otheremail");//"";

        /*
        Contact's website
        */
        $vc->data['url'] = $contact_info->get("{$employerWebsite}");//"http://www.troywolf.com";

        /*
        Some other contact data.
        */
        #$vc->data['photo'] = "";  //Enter a URL.
        $vc->data['birthday'] = $contact_info->get("birthday");//"1971-08-13";
        #$vc->data['timezone'] = "-06:00";

        /*
        If you leave this blank, the class will default to using last_name or company.
        */
        #$vc->data['sort_string'] = "";

        /*
        Notes about this contact.
        */
        #$vc->data['note'] = "Troy is an amazing guy!";

        /*
        Generate card and send as a .vcf file to user's browser for download.
        */
        $vc->download();

    }
}

?>