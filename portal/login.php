<?php 
	include_once('includes/head.php'); 
	
	$loginstatus = "";
	
	//$login_err = "";
	
	if($_REQUEST) {
		
		global $adb;
		if($_REQUEST['email']){
			
			$current_date = date("Y-m-d");
			
			$accountquery = "SELECT * FROM vtiger_portalinfo
			INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_portalinfo.id
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portalinfo.id
			inner join vtiger_customerdetails on vtiger_portalinfo.id=vtiger_customerdetails.customerid
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_portalinfo.user_name = ? AND BINARY vtiger_portalinfo.user_password = ?
			AND vtiger_portalinfo.isactive = ? and vtiger_customerdetails.portal=1
			and vtiger_customerdetails.support_start_date <= ? and vtiger_customerdetails.support_end_date >= ?";
			
			
			$result = $adb->pquery($accountquery, array($_REQUEST['email'],$_REQUEST['pass'], 1,$current_date,$current_date));
			
			if($adb->num_rows($result)) {
				
				$customerid = null;
				for ($i = 0; $i < $adb->num_rows($result); $i++) {
					$customerid = $adb->query_result($result, $i,'id');
				}
				if($customerid){
					$_SESSION["ID"] = $customerid;
					$contact_name = strtoupper(substr($adb->query_result($result,0 ,"firstname"), 0, 1)).' '.$adb->query_result($result,0,"lastname");
					$_SESSION["name"] = $contact_name;
					$customerid = $adb->query_result($result,0,'id');
					$accountid = $adb->query_result($result,0,'accountid');
					$_SESSION['accountid'] = $accountid;
					$_SESSION['user_email'] = $adb->query_result($result,0,'user_name');
					$_SESSION["ownerId"] = $adb->query_result($result,0,'smownerid');
					$setype = getSalesEntityType($customerid);
					
					$userid = $_SESSION['ownerId'];
					$user_obj = CRMEntity::getInstance("Users");
					$user_obj->id = $userid;
					$user_obj->retrieve_entity_info($userid, "Users");
					vglobal("current_user", $user_obj);
					
					if($setype == 'Contacts'){
					    
						$recordModel = Vtiger_Record_Model::getInstanceById($customerid, 'Contacts');
						
						$selectedModules = array();
	
						$selectedPortalModulesInfo = getSingleFieldValue("vtiger_contact_portal_permissions", "permissions", "crmid", $customerid);
					
						$selectedPortalModulesInfo = stripslashes(html_entity_decode($selectedPortalModulesInfo));
					
						$selectedPortalModulesInfo = json_decode($selectedPortalModulesInfo, true);
							
						$selectedModules = array();
						
						foreach($selectedPortalModulesInfo as $tabid => $module_permission){
							
							if(isset($module_permission['visible']) && $module_permission['visible'] == '1'){
								
								$moduleName = getTabModuleName($tabid);
								
								$selectedModules[$tabid] = array(
									"module" => $moduleName, 
									"edit_record" => ($module_permission['edit_records'])?$module_permission['edit_records']:0,
									"record_across_org" => ($module_permission['record_across_org'])?$module_permission['record_across_org']:0
								);
							}
						}
				
						$reportModuleTabid = getTabid("Reports");
						
						if(isset($selectedPortalModulesInfo[$reportModuleTabid]) && !empty($selectedPortalModulesInfo[$reportModuleTabid])){
				
							$portalReports = $selectedPortalModulesInfo[$reportModuleTabid];
							
							if(isset($portalReports['allowed_reports']) && !empty($portalReports['allowed_reports'])){
								foreach($portalReports['allowed_reports'] as $report){
									$selectedModules[$reportModuleTabid]['allowed_reports'] = $portalReports['allowed_reports'];
									break;
								}
							}
						}
						
						$allowed_modules = $selectedModules;
						
						$allowed_reports = array();
						
						$record_across_org = false;
						
						if(!empty($allowed_modules)){
							
							foreach($allowed_modules as $index => $allowedModule){
							    
								if($allowedModule['allowed_reports']){
									
									$record_across_org = $allowedModule['record_across_org'];
									
									$allowed_reports = ($allowedModule['allowed_reports'])?$allowedModule['allowed_reports']:array();
									
									unset($allowedModule['record_across_org']);
									
									if(isset($allowedModule['allowed_reports']))
										unset($allowedModule['allowed_reports']);
										
									unset($allowed_modules[$index]);
								}
							}
						}
						
						$list[0]['basic_details'] = array(
							"firstname" => $recordModel->get('firstname'),
							"lastname" => $recordModel->get('lastname'),
							"enable_household_accounts" => $recordModel->get('portal_enable_household_accounts'), 
							"allowed_modules" => $allowed_modules,
							"allowed_reports" => $allowed_reports, 
						);
						
					}
					
					$_SESSION['data']  = $list[0];
					
					header("Location: index.php");
				}else{
					$login_err = "The Username or Password is not correct!";
				}	
			} else{
					$login_err = "The Username or Password is not correct!";
				}
			}

		if(isset($_REQUEST['fgtemail']) && $_REQUEST['fgtemail'] != ''){
			
			$fgtqueryacc = "SELECT * FROM vtiger_portalinfo
			INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_portalinfo.id
			INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_portalinfo.id
			WHERE vtiger_crmentity.deleted = 0 AND vtiger_portalinfo.user_name = ? AND 
			isactive = ? ";
			$fgtresult = $adb->pquery($fgtqueryacc, array($_REQUEST['fgtemail'], 1));
			
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
					
					//header("Location: index.php");
					$successmess="We have send an Email containing your Password at the requested Address!";
				}else{
					$login_err = "SomeThing Went Wrong Please Try Again Later!";
				}
				
			}else{
				$login_err = "The Email you Request is not in our system!";
			}
		}
		
	}
	
	
	
?>

<link href="assets/css/login.css" rel="stylesheet" type="text/css" />

<!-- BEGIN LOGO -->
<!-- <div class="logo">
	<a href="index.php">
		<?php if(isset($GLOBALS['contact_portal_logo']) && $GLOBALS['contact_portal_logo'] == true){?>
			<img src="<?php echo $GLOBALS['portal_logo']?>" style="max-height:40px;">
		<?php } else { ?>
			<img src="<?php echo $GLOBALS['portal_logo']?>" style="max-height:40px;">
		<?php } ?>
	</a>
</div> -->
<!-- END LOGO -->
<div class="m-grid m-grid--hor m-grid--root m-page">
			
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--hor m-login m-login--signin m-login--2 m-login-2--skin-2" id="m_login" style="background-image: url(metronic/img/bg-3.jpg);">
    	<div class="m-grid__item m-grid__item--fluid	m-login__wrapper">
    		<div class="m-login__container">
    			<div class="m-login__logo">
    				<a href="#">
        				<?php if(isset($GLOBALS['contact_portal_logo']) && $GLOBALS['contact_portal_logo'] == true){?>
                			<img src="<?php echo $GLOBALS['portal_logo']?>" >
                		<?php } else { ?>
                			<img src="<?php echo $GLOBALS['portal_logo']?>" >
                		<?php } ?>	
    				</a>
    			</div>
    			<div class="alert alert-danger alert-dismissible fade show <?php if(!isset($login_err))echo "m--hide"; ?> ">
        			<button type="button" class="close" data-dismiss="alert" aria-label="Close"></button>
        			<span>
        				<?php 
        					if(isset($login_err)):  
        						echo $login_err;
        					else :
        						echo "Enter any Email and password.";
        					endif;
        				?>
        			</span>
        		</div>
        		
        		<?php if(isset($successmess)):  ?>
            		<div class="alert alert-success alert-dismissible fade show" role="alert">
    					<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    					</button>
    				  	<?php echo $successmess; ?>					  	
    				</div>
				<?php endif;  ?>    
				
    			<div class="m-login__signin">
    				<form class="m-login__form m-form login-form" action="" method="post">
    					<div class="form-group m-form__group">
    						<input class="form-control m-input" type="text" placeholder="Email" name="email" autocomplete="off">
    					</div>
    					<div class="form-group m-form__group">
    						<input class="form-control m-input m-login__form-input--last" type="password" placeholder="Password" name="pass">
    					</div>
    					<div class="row m-login__form-sub">
    						<div class="col m--align-right m-login__form-right">
    							<a href="javascript:;" id="m_login_forget_password" class="m-link">Forget Password ?</a>
    						</div>
    					</div>
    					<div class="m-login__form-action">
    						<button id="m_login_signin_submit" type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary">Sign In</button>
    					</div>
    				</form>
    			</div>
    			
    			<div class="m-login__forget-password">
    				<div class="m-login__head">
    					<h3 class="m-login__title">Forgotten Password ?</h3>
    					<div class="m-login__desc">Enter your email to reset your password:</div>
    				</div>
    				<form class="forget-form m-login__form m-form" action="" method="post">
    					<div class="form-group m-form__group">
    						<input class="form-control m-input" type="text" placeholder="Email" name="fgtemail" id="m_email" autocomplete="off">
    					</div>
    					<div class="m-login__form-action">
    						<button id="m_login_forget_password_submit" type="submit" class="btn btn-focus m-btn m-btn--pill m-btn--custom m-btn--air  m-login__btn m-login__btn--primaryr">Request</button>&nbsp;&nbsp;
    						<button id="m_login_forget_password_cancel" class="btn btn-outline-focus m-btn m-btn--pill m-btn--custom m-login__btn">Cancel</button>
    					</div>
    				</form>
    			</div>
    		</div>	
    	</div>
    </div>				
</div>


<?php include_once("includes/footer.php");?>