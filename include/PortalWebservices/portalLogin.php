<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

function vtws_portallogin($element,$user){
    
    global $adb,$site_URL;
    
    $resultData = array();
    
    if($element['email']){
        
        $current_date = date("Y-m-d");
        
        
        $result = $adb->pquery("SELECT * FROM vtiger_portalinfo
		INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_portalinfo.id
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portalinfo.id
		inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
		WHERE vtiger_crmentity.deleted = 0 AND vtiger_portalinfo.user_name = ? AND BINARY vtiger_portalinfo.user_password = ?
		AND vtiger_portalinfo.isactive = ? and vtiger_customerdetails.portal=1
		and vtiger_customerdetails.support_start_date <= ? and vtiger_customerdetails.support_end_date >= ?",
            array($element['email'],$element['pass'], 1,$current_date,$current_date));
        
        if($adb->num_rows($result)) {
            
            $customerid = null;
            
            for ($i = 0; $i < $adb->num_rows($result); $i++) {
                $customerid = $adb->query_result($result, $i,'id');
            }
            
            if($customerid){
                
                $resultData["ID"] = $customerid;
                
                $contact_name = strtoupper(substr($adb->query_result($result,0 ,"firstname"), 0, 1)).' '.$adb->query_result($result,0,"lastname");
                
                $resultData["name"] = $contact_name;
                
                $customerid = $adb->query_result($result,0,'id');
                
                $accountid = $adb->query_result($result,0,'accountid');
                
                $resultData['accountid'] = $accountid;
                
                $resultData['user_email'] = $adb->query_result($result,0,'user_name');
                
                $resultData["ownerId"] = $adb->query_result($result,0,'smownerid');
                
                
                $owner_result = $adb->pquery("select * from vtiger_users where id = ?", array($resultData["ownerId"]));
                
                if($adb->num_rows($owner_result)){
                    $resultData["owner_name"] = $adb->query_result($owner_result, 0, "first_name") . ' '. $adb->query_result($owner_result, 0, "last_name");
                    $resultData["owner_title"] = $adb->query_result($owner_result, 0, "title");
                    $resultData["owner_office_phone"] = $adb->query_result($owner_result, 0, "phone_work");
                    $resultData["owner_email"] = $adb->query_result($owner_result, 0, "email1");
                    $userRecordModel = Vtiger_Record_Model::getInstanceById($resultData["ownerId"], 'Users');
                    $userImageDetail = $userRecordModel->getImageDetails();
                    if(
                        !empty($userImageDetail['imagename'][0]['orgname']) &&
                        !empty($userImageDetail['imagename'][0]['path'])) {
                            $resultData["owner_image"] = "https://hq.360vew.com/" . $userImageDetail['imagename'][0]['path']."_".$userImageDetail['imagename'][0]['orgname'];
                        }
                }
                
                
                $setype = getSalesEntityType($customerid);
                
                if($setype == 'Contacts'){
                    
                    $recordModel = Vtiger_Record_Model::getInstanceById($customerid, 'Contacts');
                    
                    $selectedModules = array();
                    
                    $PortalModules = array(getTabid('HelpDesk') => 'tickets', getTabid('Documents') => 'Documents', getTabid('Reports') => 'Reports');
                    $PortalReports = array('Portfolios'=>array('Asset Class Report'),'Income'=>array('Last 12 months','Last Year','Projected','Month Over Month'),'Performance'=>array('Gain Loss','GH1 Report','GH2 Report','Overview'));
                    
                    $selectedPortalModulesInfo = array();
                    if($customerid){
                        global $adb;
                        $selectedPortalInfo = $adb->pquery("SELECT * FROM vtiger_contact_portal_permissions WHERE crmid = ?",array($customerid));
                        if($adb->num_rows($selectedPortalInfo)){
                            $selectedPortalModulesInfo = $adb->query_result_rowdata($selectedPortalInfo);
                        }else{
                            $defaultPortalInfo = $adb->pquery("SELECT * FROM vtiger_default_portal_permissions WHERE userid = ?",array($resultData["ownerId"]));
                            if($adb->num_rows($defaultPortalInfo)){
                                $selectedPortalModulesInfo = $adb->query_result_rowdata($defaultPortalInfo);
                            }else{
                                $globalPortalInfo =$adb->pquery("SELECT * FROM vtiger_default_portal_permissions WHERE userid = 0");
                                if($adb->num_rows($globalPortalInfo))
                                    $selectedPortalModulesInfo = $adb->query_result_rowdata($defaultPortalInfo);
                            }
                        }
                    }
                    
                    $selectedModules = array();
                    
                    foreach($PortalModules as $tabid => $module_name){
                        $modulePortalName = strtolower(str_replace(' ', '_', $module_name));
                        if(isset($selectedPortalModulesInfo[$modulePortalName.'_visible']) && $selectedPortalModulesInfo[$modulePortalName.'_visible'] == '1'){
                            
                            $moduleName = getTabModuleName($tabid);
                            
                            $selectedModules[$tabid] = array(
                                "module" => $moduleName,
                                "edit_record" => ($selectedPortalModulesInfo[$modulePortalName.'_edit_records'])?$selectedPortalModulesInfo[$modulePortalName.'_edit_records']:0,
                                "record_across_org" => ($selectedPortalModulesInfo[$modulePortalName.'_record_across_org'])?$selectedPortalModulesInfo[$modulePortalName.'_record_across_org']:0
                            );
                        }
                    }
                    
                    $allowed_reports = array();
                    
                    $portfolioModel = Vtiger_Module_Model::getInstance('PortfolioInformation');
                    $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
                    $permissionPortfolio = $currentUserModel->hasModulePermission($portfolioModel->getId());
                    if($permissionPortfolio){
                        foreach($PortalReports as $ReprtName => $PortalReport){
                            foreach($PortalReport as $ReportModules){
                                $portalReportName = strtolower(str_replace(' ', '_', $ReportModules));
                                if(isset($selectedPortalModulesInfo[$portalReportName.'_visible']) && $selectedPortalModulesInfo[$portalReportName.'_visible'] == '1'){
                                    $allowed_reports[$ReprtName][$ReportModules] =  array(
                                        'visible' => ($selectedPortalModulesInfo[$portalReportName.'_visible'])?$selectedPortalModulesInfo[$portalReportName.'_visible']:0,
                                        'record_across_org' => ($selectedPortalModulesInfo[$portalReportName.'_record_across_org'])?$selectedPortalModulesInfo[$portalReportName.'_record_across_org']:0,
                                    );
                                }
                            }
                        }
                    }
                    
                    $allowed_modules = $selectedModules;
                    
                    
                    $list[0]['basic_details'] = array(
                        "firstname" => $recordModel->get('firstname'),
                        "lastname" => $recordModel->get('lastname'),
                        "enable_household_accounts" => $recordModel->get('portal_enable_household_accounts'),
                        "allowed_modules" => $allowed_modules,
                        "allowed_reports" => $allowed_reports,
                    );
                    
                    $logo = '';
                    
                    $result = $adb->pquery("SELECT vtiger_attachments.* FROM vtiger_salesmanattachmentsrel
            		INNER JOIN vtiger_attachments ON vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
            		INNER JOIN vtiger_users ON vtiger_users.id = vtiger_salesmanattachmentsrel.smid
            		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesmanattachmentsrel.attachmentsid
            		WHERE vtiger_salesmanattachmentsrel.smid = ? and vtiger_crmentity.setype = ?", array($resultData["ownerId"], "User Logo"));
                    
                    if($adb->num_rows($result) == 1){
                        
                        $portalLogo = $site_URL;
                        $portalLogo .= "/".$adb->query_result($result, "0", "path");
                        $portalLogo .= $adb->query_result($result, "0", "attachmentsid");
                        $portalLogo .= "_".$adb->query_result($result, "0", "name");
                        
                        $logo = ($portalLogo);
                        
                        // 						if(!file_exists($logo))
                            // 							$logo = "images/logo1.png";
                        
                    } else {
                        $logo = "";
                    }
                    
                    $resultData["portal_logo"] = $logo;
                    
                    $profile_image = '';
                    
                    $result = $adb->pquery("SELECT vtiger_attachments.* FROM vtiger_seattachmentsrel
            		INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid
            		INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seattachmentsrel.crmid
            		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid
            		WHERE vtiger_seattachmentsrel.crmid = ? and vtiger_crmentity.setype = ?", array($resultData['ID'], "Contacts Image"));
                    
                    if($adb->num_rows($result) == 1){
                        
                        $profileImage = $site_URL;
                        $profileImage .= "/".$adb->query_result($result, "0", "path");
                        $profileImage .= $adb->query_result($result, "0", "attachmentsid");
                        $profileImage .= "_".decode_html($adb->query_result($result, "0", "name"));
                        $profile_image = ($profileImage);
                        
                    }
                    
                    $resultData["portal_profile_image"] = $profile_image;
                    
                    $showPortalFields = array();
                    
                    $portalField = $adb->pquery("SELECT * FROM vtiger_portal_editable_profile_fields");
                    if($adb->num_rows($portalField)){
                        $portalFields  = json_decode(html_entity_decode($adb->query_result($portalField, 0, 'portal_fields')));
                        if(!empty($portalFields)){
                            $c_module = Vtiger_Module_Model::getInstance($setype);
                            foreach($portalFields as $portal_field){
                                $showField = array();
                                $field = Vtiger_Field_Model::getInstance($portal_field, $c_module);
                                $showField['label'] = vtranslate($field->get('label'), $setype);
                                $showField['name'] = $field->getName();
                                $showField['type'] = $field->getFieldDataType();
                                if($field->getFieldDataType() == 'picklist' || $field->getFieldDataType() == 'multipicklist'){
                                    $showField['picklist'] = $field->getPicklistValues();
                                }
                                $showPortalFields[] = $showField;
                            }
                        }
                    }
                    $resultData['profileFields'] = $showPortalFields;
                }
                
                $resultData['data']  = $list[0];
                
                $finalRes = array('success'=>true, 'data' => $resultData);
                
            } else {
                
                $finalRes = array('success'=>false);
                
            }
            
        } else{
            
            $finalRes = array('success'=>false);
            
        }
    }
    
    if(isset($element['fgtemail']) && $element['fgtemail'] != ''){
        
        $fgtqueryacc = "SELECT * FROM vtiger_portalinfo
			INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_portalinfo.id
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portalinfo.id
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_portalinfo.user_name = ? AND
			isactive = ? ";
        $fgtresult = $adb->pquery($fgtqueryacc, array($element['fgtemail'], 1));
        
        if($adb->num_rows($fgtresult)){
            $contact_id = $adb->query_result($fgtresult,0 ,"id");
            
            global $current_user,$HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;
            
            $subject = 'Customer Portal Login Details';
            
            $moduleName ='Contacts';
            
            $password = makeRandomPassword();
            
            require_once("modules/Emails/mail.php");
            //$enc_password = Vtiger_Functions::generateEncryptedPassword($password);
            
            $companyDetails = getCompanyDetails();
            
            $portalURL = vtranslate('Please ',$moduleName).'<a href="'.$PORTAL_URL.'" style="font-family:Arial, Helvetica, sans-serif;font-size:13px;">'.  vtranslate('click here', $moduleName).'</a>';
            
            $query='SELECT vtiger_emailtemplates.subject,vtiger_emailtemplates.body
							FROM vtiger_notificationscheduler
								INNER JOIN vtiger_emailtemplates ON vtiger_emailtemplates.templateid=vtiger_notificationscheduler.notificationbody
							WHERE schedulednotificationid=5';
            
            $result = $adb->pquery($query, array());
            
            $body=decode_html($adb->query_result($result,0,'body'));
            $contents=$body;
            $contents = str_replace('$contact_name$',$adb->query_result($fgtresult,0 ,"salutation").''.$adb->query_result($fgtresult,0 ,"firstname").' '.$adb->query_result($fgtresult,0 ,"lastname"),$contents);
            $contents = str_replace('$login_name$',$adb->query_result($fgtresult,0 ,"email"),$contents);
            $contents = str_replace('$password$',$password,$contents);
            $contents = str_replace('$URL$',$portalURL,$contents);
            $contents = str_replace('$support_team$',getTranslatedString('Support Team', $moduleName),$contents);
            $contents = str_replace('$logo$','<img src="cid:logo" />',$contents);
            
            //Company Details
            $contents = str_replace('$address$',$companyDetails['address'],$contents);
            $contents = str_replace('$companyname$',$companyDetails['companyname'],$contents);
            $contents = str_replace('$phone$',$companyDetails['phone'],$contents);
            $contents = str_replace('$companywebsite$',$companyDetails['website'],$contents);
            $contents = str_replace('$supportemail$',$HELPDESK_SUPPORT_EMAIL_ID,$contents);
            
            $contents= decode_html(getMergedDescription($contents, $contact_id, 'Contacts'));
            
            $subject = decode_html(getMergedDescription($subject, $contact_id,'Contacts'));
            
            $status = send_mail('Contacts', $adb->query_result($fgtresult,0 ,"email"), $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject, $contents,'','','','','',true);
            
            if($status == '1'){
                $sql = "UPDATE vtiger_portalinfo SET user_password=? WHERE id=?";
                $params = array($password,  $contact_id);
                $adb->pquery($sql, $params);
                
                $adb->pquery("UPDATE vtiger_contactdetails SET portal_password = ? WHERE contactid = ?",
                    array($params));
                
                $successmess="We have send an Email containing your Password at the requested Address!";
                $finalRes = array('success'=>true, 'data'=>$successmess);
            }else{
                $login_err = "SomeThing Went Wrong Please Try Again Later!";
                $finalRes = array('success'=>false, 'data'=>$login_err);
            }
            
        }else{
            $login_err = "The Email you Request is not in our system!";
            $finalRes = array('success'=>false, 'data'=>$login_err);
        }
    }
    
    return $finalRes;
}

?>