<?php 	
	include_once('head.php'); 	
	
	vglobal('user_basic_details',$_SESSION['data']['basic_details']);
	
	foreach($GLOBALS['user_basic_details']['allowed_modules'] as 	$allowedModule){
			$modules[] = $allowedModule['module'];
	}
	$avmod = array_values($modules);
	$avmod=array_merge(array("Home"),$avmod);
	
	vglobal('avmod',$avmod);
	vglobal('hiddenmodules',array());
		
	$logo = '';
	$ownerResult = $adb->pquery("SELECT smownerid FROM vtiger_crmentity WHERE crmid = ?", array($_SESSION['ID']));
	$ownerId = $adb->query_result($ownerResult, 0, 'smownerid');
	
	$sql = "SELECT vtiger_attachments.* FROM vtiger_salesmanattachmentsrel 
		INNER JOIN vtiger_attachments ON vtiger_salesmanattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
		INNER JOIN vtiger_users ON vtiger_users.id = vtiger_salesmanattachmentsrel.smid
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_salesmanattachmentsrel.attachmentsid 
		WHERE vtiger_salesmanattachmentsrel.smid = ? and vtiger_crmentity.setype = ?";
	$result = $adb->pquery($sql, array($ownerId, "User Logo"));
	
	if($adb->num_rows($result) == 1){
		$portalLogo = $site_URL;
		$portalLogo .= "/".$adb->query_result($result, "0", "path");
		$portalLogo .= $adb->query_result($result, "0", "attachmentsid");
		$portalLogo .= "_".$adb->query_result($result, "0", "name");
		$logo = ($portalLogo);
	}
	
	vglobal("portal_logo",$logo);
	
	$profile_image = '';
	$sql = "SELECT vtiger_attachments.* FROM vtiger_seattachmentsrel 
		INNER JOIN vtiger_attachments ON vtiger_seattachmentsrel.attachmentsid = vtiger_attachments.attachmentsid 
		INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_seattachmentsrel.crmid 
		INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seattachmentsrel.attachmentsid 
		WHERE vtiger_seattachmentsrel.crmid = ? and vtiger_crmentity.setype = ?";
	$result = $adb->pquery($sql, array($_SESSION['ID'], "Contacts Image"));
	
	if($adb->num_rows($result) == 1){
		$profileImage = $site_URL;
		$profileImage .= "/".$adb->query_result($result, "0", "path");
		$profileImage .= $adb->query_result($result, "0", "attachmentsid");
		$profileImage .= "_".decode_html($adb->query_result($result, "0", "name"));
		$profile_image = ($profileImage);
	}
	
	vglobal("portal_profile_image",$profile_image);	
	
	$logged_in_user_name = "";
	
	if(isset($GLOBALS['user_basic_details']['firstname']) && $GLOBALS['user_basic_details']['firstname'] != '')
		$logged_in_user_name = strtoupper(substr($GLOBALS['user_basic_details']['firstname'], 0, 1)).".";
		
	$logged_in_user_name .= $GLOBALS['user_basic_details']['lastname'];
		
	$allowedModuleForRecordCreation = array();
	
	if(isset($GLOBALS['user_basic_details']['allowed_modules']) && !empty($GLOBALS['user_basic_details']['allowed_modules'])){
		
		$allowedModules = $GLOBALS['user_basic_details']['allowed_modules'];
		
		foreach($allowedModules as $moduleInfo){
		    if(isset($moduleInfo['edit_record']) && $moduleInfo['edit_record'] == 1 && $moduleInfo['module'] != 'Documents' ){
				$allowedModuleForRecordCreation[$moduleInfo['module']] = array(
					"label" => "LBL_NEW_".strtoupper($moduleInfo['module']), "link" => strtolower($moduleInfo['module']).".php?view=edit"
				);
			}
		}
	}
	
	$portalMenus = array();
	
	if(!empty($GLOBALS["avmod"])){
		
		foreach($GLOBALS["avmod"] as $mod){
			
		    if($mod == "Home" || $mod == "Documents"){
				$portalMenus[$mod]['submenu'] = array();
				$portalMenus[$mod]['link'] =  strtolower($mod).".php";
		    }else if($mod == "Accounts"){
				$portalMenus[$mod]['submenu'] = array("detail" => array("label" => "LBL_MANAGE_".$mod, "link" => strtolower($mod).".php"));
				$portalMenus[$mod]['link'] =  strtolower($mod).".php";
		    }else{
				$portalMenus[$mod]['submenu'] = array("list" => array("label" => "LBL_MANAGE_".$mod, "link" => strtolower($mod).".php"));
				$portalMenus[$mod]['link'] =  strtolower($mod).".php";
		    }
			if(array_key_exists($mod, $allowedModuleForRecordCreation)){
				
				$portalMenus[$mod]['submenu']['edit'] = $allowedModuleForRecordCreation[$mod];
			} 
		}
	}
	
	$allowed_reports = $GLOBALS['user_basic_details']['allowed_reports'];
	if(isset($allowed_reports) && !empty($allowed_reports)){
	    
	    foreach($allowed_reports as $mod=>$subReport){
    	        
    	    $basic_details = $GLOBALS['user_basic_details'];
    	    
    	    $is_enabled_household_accounts = false;
    	    
    	    if(isset($basic_details['enable_household_accounts']) && $basic_details['enable_household_accounts'] == 1)
    	        $is_enabled_household_accounts = true;
        
            foreach($subReport as $subreportName => $submenuReport){
               
                $reportLink = strtolower(str_replace(' ','',$subreportName)).".php";
                
                if($submenuReport['record_across_org'] == 1)
                    $reportLink .= "?show_reports=Accounts";
                
                if($submenuReport['visible'] == 1){
                    $report_submenu = array("label" => $subreportName, "link" => $reportLink);
                    
                    $portalMenus[$mod]['submenu'][] = $report_submenu;
                }
            }
                
	    }
	}
	
?>

<div id="responsive_change_password_container" class="modal fade" tabindex="-1" aria-hidden="true" >
	<style>
		.error{
			 color:red;
			}
	</style>
	<div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        	<div class="modal-header">
        		<h5 class="modal-title" ><?php echo vtranslate("Change Password",'Vtiger'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
                </button>
        	</div>
        	<form id="change_password" role="form" method="POST" action="">
        		<input type="hidden" name="fun" value="changepassword">
        		<input type="hidden" name="is_ajax" value="1" />
        		<div class="modal-body">
        			<div class="form-group">
                        <label for="recipient-name" class="form-control-label"><?php echo vtranslate("New Password",'Vtiger'); ?>:</label>
                        <input type="password" class="form-control" name="new_password" id="new_password" placeholder="<?php echo vtranslate("New Password",'Vtiger'); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message-text" class="form-control-label"><?php echo vtranslate("Confirm New Password",'Vtiger'); ?>:</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="<?php echo vtranslate("Confirm New Password",'Vtiger'); ?>">
                    </div>
        		</div>
        		<div class="modal-footer">
        			<button type="submit" class="btn green"><?php echo vtranslate("Change Password",'Vtiger'); ?></button>
        			<button type="button" data-dismiss="modal" class="btn btn-outline dark">Close</button>
        		</div>
        	</form>
    	</div>
	</div>
</div>
<div class="m-grid m-grid--hor m-grid--root m-page">
	<header id="m_header" class="m-grid__item    m-header " m-minimize-offset="200" m-minimize-mobile-offset="200">
    	<div class="m-container m-container--fluid m-container--full-height">
    		<div class="m-stack m-stack--ver m-stack--desktop">		
                <div class="m-stack__item m-brand  m-brand--skin-light m--hide">
                	<div class="m-stack m-stack--ver m-stack--general">
                		<div class="m-stack__item m-stack__item--middle m-brand__logo">
                			<a href="index.php" class="m-brand__logo-wrapper">
                				<img alt="" src="<?php echo $GLOBALS['portal_logo']?>"style="width: 100px;">
                			</a>  
                			<h3 class="m-header__title">Apps</h3>
                		</div>
                	</div>
                </div>
    			<div class="m-stack__item m-stack__item--fluid m-header-head" id="m_header_nav">
    				<!-- BEGIN: Topbar -->
    				<div class="m-header-logo d-none d-md-block d-lg-block d-xl-block">
    					<div class="m-brand__logo">
    						<a href="index.php" class="m-brand__logo-wrapper company-logo">
    							<img src="<?php echo $GLOBALS['portal_logo']?>" alt="logo.png">
							</a>
						</div>
					</div>
    				
    				<div id="m_header_topbar" class="m-topbar  m-stack m-stack--ver m-stack--general">
    
                    	<div class="m-stack__item m-topbar__nav-wrapper">
                    		<ul class="m-topbar__nav m-nav m-nav--inline">
                    			<li class="m-nav__item m-topbar__user-profile  m-dropdown m-dropdown--medium m-dropdown--arrow  m-dropdown--align-right m-dropdown--mobile-full-width m-dropdown--skin-light" m-dropdown-toggle="click">
                                	<a href="#" class="m-nav__link m-dropdown__toggle">
                                		<span class="m-topbar__userpic m--hide">
                                			<img src="./assets/app/media/img/users/user4.jpg" class="m--img-rounded m--marginless m--img-centered" alt="">
                                		</span>
                                		<span class="m-nav__link-icon m-topbar__usericon">
                                			<span class="m-nav__link-icon-wrapper"><i class="flaticon-user-ok"></i></span>
                                		</span>
                                	</a>
                                	<div class="m-dropdown__wrapper">
                                		<span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                		<div class="m-dropdown__inner">
                                			<div class="m-dropdown__header m--align-center">
                                				<div class="m-card-user m-card-user--skin-light">
                                					<div class="m-card-user__pic">
                                    					<?php if(isset($GLOBALS['portal_profile_image']) && $GLOBALS['portal_profile_image'] != ''): ?>
            												<img class="m--img-rounded m--marginless" alt="" src="<?php echo $GLOBALS['portal_profile_image']; ?>" />
            											<?php else: ?>
            												<img class="m--img-rounded m--marginless" alt="" src="assets/img/user-default-icon.png" alt="" style="background-color: dimgrey;"/>
            											<?php endif; ?>
                                					</div>
                                					<div class="m-card-user__details">
                                						<span class="m-card-user__name m--font-weight-500"><?php echo $logged_in_user_name;?></span>
                                						<a href="" class="m-card-user__email m--font-weight-300 m-link"><?php echo $_SESSION['user_email'];?></a>
                                					</div>
                                				</div>
                                			</div>
                                			<div class="m-dropdown__body">
                                				<div class="m-dropdown__content">
                                					<ul class="m-nav m-nav--skin-light">
                                						<li class="m-nav__section m--hide">
                                							<span class="m-nav__section-text">Section</span>
                                						</li>
                                						<!-- <li class="m-nav__item">
                                							<a href="index.php?module=Contacts&action=UserPreference" class="m-nav__link">
                                								<i class="m-nav__link-icon flaticon-profile-1"></i>
                                								<span class="m-nav__link-title">  
                                									<span class="m-nav__link-wrap">      
                                										<span class="m-nav__link-text">My Profile</span>      
                                									</span>
                                								</span>
                                							</a>
                                						</li> 
                                						
                                						<li class="m-nav__separator m-nav__separator--fit">
                                						</li>-->
                                						<li class="m-nav__item">
                                							<a href="#responsive_change_password_container"  data-toggle="modal" class="m-nav__link">
                                								<i class="m-nav__link-icon flaticon-lock"></i>
                                								<span class="m-nav__link-text"><?php echo vtranslate("Change Password",'Vtiger'); ?></span>
                                							</a>
                                						</li>
                                					
                                						<li class="m-nav__separator m-nav__separator--fit">
                                						</li>
                                						<li class="m-nav__item">
                                							<a href="index.php?logout=1" class="btn m-btn--pill btn-secondary m-btn m-btn--custom m-btn--label-brand m-btn--bolder">Logout</a>
                                						</li>
                                					</ul>
                                				</div>
                                			</div>
                                		</div>
                                	</div>
                                </li>
    						</ul>
    					</div>
    				</div>
        		</div>
    		</div>
    	</div>
    </header>

<div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver-desktop m-grid--desktop m-body">
	<button class="m-aside-left-close  m-aside-left-close--skin-light " id="m_aside_left_close_btn"><i class="la la-close"></i></button>
	<div id="m_aside_left" class="m-grid__item m-aside-left  m-aside-left--skin-light ">
    	<div id="m_ver_menu" class="m-aside-menu m-aside-menu--skin-light m-aside-menu--submenu-skin-light m-scroller ps ps--active-y" data-menu-vertical="true" m-menu-scrollable="1" m-menu-dropdown-timeout="500" style="height: 250px; overflow: hidden;">		
    		<?php if(!empty($portalMenus)){ ?>
    			<ul class="m-menu__nav  m-menu__nav--dropdown-submenu-arrow ">
    				<?php
    					foreach($portalMenus as $mod => $subMenus){
    					   
    						if($mod == $module) 
    							$className = "active open"; 
    						else 
    							$className = "start";
    				?>
    				<?php if(isset($subMenus['submenu']) && !empty($subMenus['submenu']) ){ ?>
    					<li class="m-menu__item m-menu__item--submenu m-menu__item--hover <?php echo $className ?> " aria-haspopup="true" m-menu-submenu-toggle="hover">
    						<a href="javascript:;" class="m-menu__link m-menu__toggle">
                				<i class="m-menu__link-icon"><img src="assets/img/<?php echo $mod;?>.png" alt="<?php echo vtranslate($mod,'Vtiger'); ?>" title="<?php echo vtranslate($mod,'Vtiger'); ?>"/></i>
                				<span class="m-menu__link-text"><?php echo vtranslate($mod,'Vtiger'); ?></span>
                				<i class="m-menu__ver-arrow la la-angle-right"></i>
            				</a>
            				<div class="m-menu__submenu ">
            					<span class="m-menu__arrow"></span>
            					<ul class="m-menu__subnav">
            						<li class="m-menu__item  m-menu__item--parent <?php echo $className ?>" aria-haspopup="true">
            							<span class="m-menu__link">
            								<span class="m-menu__link-text"><?php echo vtranslate($mod,'Vtiger'); ?></span>
            							</span>
            						</li>
            						<?php 
            						foreach($subMenus['submenu'] as $menu_view => $subMenu){
    										
    									$viewname = (isset($_REQUEST['view']))?$_REQUEST['view']:'list';
    									
										if($mod == $module && $viewname == $menu_view) 
											$subMenuClassName = "active open";
										else 
											$subMenuClassName = "start";
    								?>
            						<li class="m-menu__item  <?php echo $subMenuClassName; ?>" aria-haspopup="true">
            							<a href="<?php echo $subMenu['link']; ?>" class="m-menu__link ">
            								<i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
            								<span class="m-menu__link-text"><?php echo $app_strings[$subMenu['label']]; ?></span>
            							</a>
            						</li>
            						<?php }?>
            					</ul>
            				</div>
    					</li>	
    				<?php }else{?>	
    					<li class="m-menu__item <?php echo $className ?>" aria-haspopup="true" m-menu-link-redirect="1">
    						<a href="<?php if($mod == 'Home') echo 'index.php?module=Home&action=index'; elseif($mod != 'Reports') echo $subMenus['link']; else echo 'javascript:;';?>" class="m-menu__link ">
                				<?php if($mod=='Home'){?><i class="flaticon-home-2 m-menu__link-icon"></i><?php }else{?><i class="m-menu__link-icon"><img src="assets/img/<?php echo $mod;?>.png" alt="<?php echo vtranslate($mod,'Vtiger'); ?>" title="<?php echo vtranslate($mod,'Vtiger'); ?>"/></i><?php }?>
                				<span class="m-menu__link-text"><?php echo vtranslate($mod,'Vtiger'); ?></span>
            				</a>
        				</li>
    				<?php }}?>
    			</ul>
    		<?php }?>
    		<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
    			<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    		</div>
    		<div class="ps__rail-y" style="top: 0px; height: 250px; right: 4px;">
    			<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 85px;"></div>
    		</div>
    	</div>
    </div>
    <div class="m-grid__item m-grid__item--fluid m-wrapper">
	  	<div class="m-content">  			   