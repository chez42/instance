<?php
include_once("libraries/vcard/vcard_class.php");
include_once("include/utils/omniscientCustom.php");
class Leads_VCard_Action extends Vtiger_BasicAjax_Action{
    public function process(Vtiger_Request $request) {
        $leadid = $request->get('record');
        
        $lead_info = Leads_Record_Model::getInstanceById($leadid);
        
        /*
        Instantiate a new vcard object.
        */
        ob_clean();
        $vc = new vcard();
        
        /*
        Contact's name data.
        If you leave display_name blank, it will be built using the first and last name.
        */

        $employerName = 'company';
        $employerPhone = 'cf_632';
        $employerState = 'cf_1881';
        $employerPostal = 'cf_1883';
        $employerWebsite = 'website';
        $employerStreet1 = 'cf_1875';
        $employerStreet2 = 'cf_1877';
        $employerCity = 'cf_1879';
        $employerFax = '';
        $nickname = '';
        
//         echo"<pre>";print_r($employerName.' -- '.$employerPhone.' -- '.$employerState.' -- '.$employerPostal
//             .' -- '.$employerWebsite.' -- '.$employerStreet1.' -- '.$employerStreet2.' -- '.$employerCity
//             .' -- '.$employerFax.' -- '.$nickname);echo"</pre>";exit;

        $vc->data['first_name'] = $lead_info->get("firstname");
        $vc->data['last_name'] = $lead_info->get("lastname");

        /*
        Contact's company, department, title, profession
        */
        $vc->data['company'] = $lead_info->get("{$employerName}");
        #$vc->data['department'] = "";
        $vc->data['title'] = $lead_info->get("cf_936");//"Web Developer";
        #$vc->data['role'] = "";

        /*
        Contact's work address
        */
        #$vc->data['work_po_box'] = "";
        #$vc->data['work_extended_address'] = "";
        $vc->data['work_address'] = $lead_info->get("{$employerStreet1}");//"7027 N. Hickory";
        $vc->data['work_city'] = $lead_info->get("{$employerCity}");//"Kansas City";
        $vc->data['work_state'] = $lead_info->get("{$employerState}");//"MO";
        $vc->data['work_postal_code'] = $lead_info->get("{$employerPostal}");//"64118";
        #$vc->data['work_country'] = "United States of America";

        /*
        Contact's home address
        */
        #$vc->data['home_po_box'] = "";
        #$vc->data['home_extended_address'] = "";
        $vc->data['home_address'] = $lead_info->get("lane");//"7027 N. Hickory";
        $vc->data['home_city'] = $lead_info->get("city");//"Kansas City";
        $vc->data['home_state'] = $lead_info->get("state");//"MO";
        $vc->data['home_postal_code'] = $lead_info->get("code");//"64118";
        #$vc->data['home_country'] = "United States of America";

        /*
        Contact's telephone numbers.
        */
        $vc->data['office_tel'] = $lead_info->get("phone");//"";
        $vc->data['employer_phone'] = $lead_info->get("{$employerPhone}");//"";
        $vc->data['home_tel'] = $lead_info->get("mobile");//"";
        $vc->data['cell_tel'] = $lead_info->get("mobile");//"(816) 739-9653";
        $vc->data['fax_tel'] = $lead_info->get("fax");//"";
        #$vc->data['pager_tel'] = "";

        /*
        Contact's email addresses
        */
        $vc->data['email1'] = $lead_info->get("email");//"troy@troywolf.com";
        $vc->data['email2'] = $lead_info->get("cf_1536");//"";

        /*
        Contact's website
        */
        $vc->data['url'] = $lead_info->get("{$employerWebsite}");//"http://www.troywolf.com";

        /*
        Some other contact data.
        */
        #$vc->data['photo'] = "";  //Enter a URL.
        $vc->data['birthday'] = $lead_info->get("cf_1600");//"1971-08-13";
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