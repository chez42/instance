<?php 
    global $portal_logo, $user_basic_details, $avmod;
   
    $allowedModuleForRecordCreation = array();
    
    if(isset($user_basic_details['allowed_modules']) && !empty($user_basic_details['allowed_modules'])){
        
        $allowedModules = $user_basic_details['allowed_modules'];
        
        foreach($allowedModules as $moduleInfo){
            if(isset($moduleInfo['edit_record']) && $moduleInfo['edit_record'] == 1 && $moduleInfo['module'] != 'Documents' && $moduleInfo['module'] != 'HelpDesk' && $moduleInfo['module'] != 'Tickets'){
                $allowedModuleForRecordCreation[$moduleInfo['module']] = array(
                    "label" => "LBL_NEW_".strtoupper($moduleInfo['module']), "link" => strtolower($moduleInfo['module']).".php?view=edit"
                );
            }
        }
    }
    
    $portalMenus = array();
    
    if(!empty($avmod)){
        
        foreach($avmod as $mod){
            
            if($mod['modules'] == "Home" || $mod['modules'] == "Documents" || $mod['modules'] == 'HelpDesk' || $mod['modules'] == 'Potentials' || $mod['modules'] == 'Products'){
                $portalMenus[$mod['modules']]['submenu'] = array();
                $portalMenus[$mod['modules']]['label'] = $mod['label'];
                $portalMenus[$mod['modules']]['link'] =  strtolower($mod['modules']).".php";
            }else if($mod == "Accounts"){
                $portalMenus[$mod['modules']]['submenu'] = array("detail" => array("label" => "LBL_MANAGE_".$mod['modules'], "link" => strtolower($mod['modules']).".php"));
                $portalMenus[$mod['modules']]['label'] = $mod['label'];
                $portalMenus[$mod['modules']]['link'] =  strtolower($mod['modules']).".php";
            }else{
                $portalMenus[$mod['modules']]['submenu'] = array("list" => array("label" => "LBL_MANAGE_".$mod['modules'], "link" => strtolower($mod['modules']).".php"));
                $portalMenus[$mod['modules']]['label'] = $mod['label'];
                $portalMenus[$mod['modules']]['link'] =  strtolower($mod).".php";
            }
            if(array_key_exists($mod['modules'], $allowedModuleForRecordCreation)){
                
                $portalMenus[$mod['modules']]['submenu']['edit'] = $allowedModuleForRecordCreation[$mod['modules']];
            }
        }
    }
    
    $allowed_reports = $user_basic_details['allowed_reports'];
    if(isset($allowed_reports) && !empty($allowed_reports)){
        
        foreach($allowed_reports as $mod=>$subReport){
            
            $basic_details = $user_basic_details;
            
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
<div id="kt_header_mobile" class="kt-header-mobile  kt-header-mobile--fixed ">
	<div class="kt-header-mobile__logo">
		<a href="index.php">
			<img alt="Logo" src="<?php echo $portal_logo;?>" style = "width:50px;width:50px;" />
		</a>
	</div>
	<div class="kt-header-mobile__toolbar">
		<button class="kt-header-mobile__toggler kt-header-mobile__toggler--left" id="kt_aside_mobile_toggler"><span></span></button>
		<button class="kt-header-mobile__toggler" id="kt_header_mobile_toggler"><span></span></button>
		<button class="kt-header-mobile__topbar-toggler" id="kt_header_mobile_topbar_toggler"><i class="flaticon-more"></i></button>
	</div>
</div>
<!-- end:: Header Mobile -->
<div class="kt-grid kt-grid--hor kt-grid--root">
	<div class="kt-grid__item kt-grid__item--fluid kt-grid kt-grid--ver kt-page">
    <!-- begin:: Aside -->
    <!-- Uncomment this to display the close button of the panel
        <button class="kt-aside-close " id="kt_aside_close_btn"><i class="la la-close"></i></button>
    -->
	<div class="kt-aside  kt-aside--fixed  kt-grid__item kt-grid kt-grid--desktop kt-grid--hor-desktop" id="kt_aside">
        <!-- begin:: Aside -->
    	<div class="kt-aside__brand kt-grid__item " id="kt_aside_brand">
    		<div class="kt-aside__brand-logo">
    			<a href="index.php">
    				<img alt="Logo" src="<?php echo $portal_logo;?>" style = "width:100%;" />
    			</a>
    		</div>
			<div class="kt-aside__brand-tools">
				<button class="kt-aside__brand-aside-toggler" id="kt_aside_toggler">
					<span>
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon points="0 0 24 0 24 24 0 24" />
								<path d="M5.29288961,6.70710318 C4.90236532,6.31657888 4.90236532,5.68341391 5.29288961,5.29288961 C5.68341391,4.90236532 6.31657888,4.90236532 6.70710318,5.29288961 L12.7071032,11.2928896 C13.0856821,11.6714686 13.0989277,12.281055 12.7371505,12.675721 L7.23715054,18.675721 C6.86395813,19.08284 6.23139076,19.1103429 5.82427177,18.7371505 C5.41715278,18.3639581 5.38964985,17.7313908 5.76284226,17.3242718 L10.6158586,12.0300721 L5.29288961,6.70710318 Z" fill="#000000" fill-rule="nonzero" transform="translate(8.999997, 11.999999) scale(-1, 1) translate(-8.999997, -11.999999) " />
								<path d="M10.7071009,15.7071068 C10.3165766,16.0976311 9.68341162,16.0976311 9.29288733,15.7071068 C8.90236304,15.3165825 8.90236304,14.6834175 9.29288733,14.2928932 L15.2928873,8.29289322 C15.6714663,7.91431428 16.2810527,7.90106866 16.6757187,8.26284586 L22.6757187,13.7628459 C23.0828377,14.1360383 23.1103407,14.7686056 22.7371482,15.1757246 C22.3639558,15.5828436 21.7313885,15.6103465 21.3242695,15.2371541 L16.0300699,10.3841378 L10.7071009,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(15.999997, 11.999999) scale(-1, 1) rotate(-270.000000) translate(-15.999997, -11.999999) " />
							</g>
						</svg>
					</span>
					<span>
						<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
								<polygon points="0 0 24 0 24 24 0 24" />
								<path d="M12.2928955,6.70710318 C11.9023712,6.31657888 11.9023712,5.68341391 12.2928955,5.29288961 C12.6834198,4.90236532 13.3165848,4.90236532 13.7071091,5.29288961 L19.7071091,11.2928896 C20.085688,11.6714686 20.0989336,12.281055 19.7371564,12.675721 L14.2371564,18.675721 C13.863964,19.08284 13.2313966,19.1103429 12.8242777,18.7371505 C12.4171587,18.3639581 12.3896557,17.7313908 12.7628481,17.3242718 L17.6158645,12.0300721 L12.2928955,6.70710318 Z" fill="#000000" fill-rule="nonzero" />
								<path d="M3.70710678,15.7071068 C3.31658249,16.0976311 2.68341751,16.0976311 2.29289322,15.7071068 C1.90236893,15.3165825 1.90236893,14.6834175 2.29289322,14.2928932 L8.29289322,8.29289322 C8.67147216,7.91431428 9.28105859,7.90106866 9.67572463,8.26284586 L15.6757246,13.7628459 C16.0828436,14.1360383 16.1103465,14.7686056 15.7371541,15.1757246 C15.3639617,15.5828436 14.7313944,15.6103465 14.3242754,15.2371541 L9.03007575,10.3841378 L3.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.000003, 11.999999) rotate(-270.000000) translate(-9.000003, -11.999999) " />
							</g>
						</svg>
					</span>
				</button>
                <!--
			         <button class="kt-aside__brand-aside-toggler kt-aside__brand-aside-toggler--left" id="kt_aside_toggler"><span></span></button>
			    -->
				</div>
			</div>
			
			
			<div class="kt-aside-menu-wrapper kt-grid__item kt-grid__item--fluid" id="kt_aside_menu_wrapper">
				<div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
					<?php if(!empty($portalMenus)){ ?>
    					<ul class="kt-menu__nav ">
        					<?php
        					foreach($portalMenus as $mod => $subMenus){
        					    
        					    if($mod == $module)
        					        $className = "kt-menu__item--open";
    					        else
    					            $className = "";
        						
            				?>
            				<?php if(isset($subMenus['submenu']) && !empty($subMenus['submenu']) ){ ?>
        						
        						<li class="kt-menu__item  kt-menu__item--submenu <?php echo $className;?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
        							<a href="javascript:;" class="kt-menu__link kt-menu__toggle">
        								<span class="kt-menu__link-icon">
        									<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
        										<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
        											<rect x="0" y="0" width="24" height="24" />
        											<rect fill="#000000" x="4" y="4" width="7" height="7" rx="1.5" />
        											<path d="M5.5,13 L9.5,13 C10.3284271,13 11,13.6715729 11,14.5 L11,18.5 C11,19.3284271 10.3284271,20 9.5,20 L5.5,20 C4.67157288,20 4,19.3284271 4,18.5 L4,14.5 C4,13.6715729 4.67157288,13 5.5,13 Z M14.5,4 L18.5,4 C19.3284271,4 20,4.67157288 20,5.5 L20,9.5 C20,10.3284271 19.3284271,11 18.5,11 L14.5,11 C13.6715729,11 13,10.3284271 13,9.5 L13,5.5 C13,4.67157288 13.6715729,4 14.5,4 Z M14.5,13 L18.5,13 C19.3284271,13 20,13.6715729 20,14.5 L20,18.5 C20,19.3284271 19.3284271,20 18.5,20 L14.5,20 C13.6715729,20 13,19.3284271 13,18.5 L13,14.5 C13,13.6715729 13.6715729,13 14.5,13 Z" fill="#000000" opacity="0.3" />
        										</g>
        									</svg>
        								</span>
        								<span class="kt-menu__link-text"><?php echo $mod; ?></span>
        								<i class="kt-menu__ver-arrow la la-angle-right"></i>
        							</a>
        							<div class="kt-menu__submenu "><span class="kt-menu__arrow"></span>
        								<ul class="kt-menu__subnav">
        								<?php 
                    						foreach($subMenus['submenu'] as $menu_view => $subMenu){
            										
            									$viewname = (isset($_REQUEST['view']))?$_REQUEST['view']:'list';
            									
        										if($mod == $module && $viewname == $menu_view) 
        											$subMenuClassName = "kt-menu__item--open";
        										else 
        											$subMenuClassName = "";
            								?>
        									<li class="kt-menu__item  kt-menu__item--submenu <?php echo $subMenuClassName; ?>" aria-haspopup="true" data-ktmenu-submenu-toggle="hover">
        										<a href="<?php echo $subMenu['link']; ?>" class="kt-menu__link kt-menu__toggle">
        											<i class="kt-menu__link-bullet kt-menu__link-bullet--line"><span></span></i>
        											<span class="kt-menu__link-text"><?php echo $subMenu['label']; ?></span>
    											</a>
        									</li>
        									<?php }?>
        									
        								</ul>
        							</div>
        						</li>
    						<?php }else{?>	
        						<li class="kt-menu__item  kt-menu__item--submenu <?php echo $className;?>" aria-haspopup="true">
        						<a href="<?php if($mod == 'Home') echo 'index.php'; elseif($mod != 'Reports') echo $subMenus['link']; else echo 'javascript:;';?>" class="kt-menu__link "><span class="kt-menu__link-icon"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
        							<?php if($mod == 'Home'){?>
        								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
											<polygon points="0 0 24 0 24 24 0 24" />
											<path d="M12.9336061,16.072447 L19.36,10.9564761 L19.5181585,10.8312381 C20.1676248,10.3169571 20.2772143,9.3735535 19.7629333,8.72408713 C19.6917232,8.63415859 19.6104327,8.55269514 19.5206557,8.48129411 L12.9336854,3.24257445 C12.3871201,2.80788259 11.6128799,2.80788259 11.0663146,3.24257445 L4.47482784,8.48488609 C3.82645598,9.00054628 3.71887192,9.94418071 4.23453211,10.5925526 C4.30500305,10.6811601 4.38527899,10.7615046 4.47382636,10.8320511 L4.63,10.9564761 L11.0659024,16.0730648 C11.6126744,16.5077525 12.3871218,16.5074963 12.9336061,16.072447 Z" fill="#000000" fill-rule="nonzero" />
											<path d="M11.0563554,18.6706981 L5.33593024,14.122919 C4.94553994,13.8125559 4.37746707,13.8774308 4.06710397,14.2678211 C4.06471678,14.2708238 4.06234874,14.2738418 4.06,14.2768747 L4.06,14.2768747 C3.75257288,14.6738539 3.82516916,15.244888 4.22214834,15.5523151 C4.22358765,15.5534297 4.2250303,15.55454 4.22647627,15.555646 L11.0872776,20.8031356 C11.6250734,21.2144692 12.371757,21.2145375 12.909628,20.8033023 L19.7677785,15.559828 C20.1693192,15.2528257 20.2459576,14.6784381 19.9389553,14.2768974 C19.9376429,14.2751809 19.9363245,14.2734691 19.935,14.2717619 L19.935,14.2717619 C19.6266937,13.8743807 19.0546209,13.8021712 18.6572397,14.1104775 C18.654352,14.112718 18.6514778,14.1149757 18.6486172,14.1172508 L12.9235044,18.6705218 C12.377022,19.1051477 11.6029199,19.1052208 11.0563554,18.6706981 Z" fill="#000000" opacity="0.3" />
										</g>
        							<?php }elseif($mod == 'Documents'){?>
            							<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
            								<rect x="0" y="0" width="24" height="24" />
            								<path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
            								<rect fill="#000000" opacity="0.3" x="11" y="2" width="2" height="14" rx="1" />
            								<path d="M12.0362375,3.37797611 L7.70710678,7.70710678 C7.31658249,8.09763107 6.68341751,8.09763107 6.29289322,7.70710678 C5.90236893,7.31658249 5.90236893,6.68341751 6.29289322,6.29289322 L11.2928932,1.29289322 C11.6689749,0.916811528 12.2736364,0.900910387 12.6689647,1.25670585 L17.6689647,5.75670585 C18.0794748,6.12616487 18.1127532,6.75845471 17.7432941,7.16896473 C17.3738351,7.57947475 16.7415453,7.61275317 16.3310353,7.24329415 L12.0362375,3.37797611 Z" fill="#000000" fill-rule="nonzero" />
            							</g>
        							<?php }elseif($mod == 'HelpDesk'){?>
        								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M3,10.0500091 L3,8 C3,7.44771525 3.44771525,7 4,7 L9,7 L9,9 C9,9.55228475 9.44771525,10 10,10 C10.5522847,10 11,9.55228475 11,9 L11,7 L21,7 C21.5522847,7 22,7.44771525 22,8 L22,10.0500091 C20.8588798,10.2816442 20,11.290521 20,12.5 C20,13.709479 20.8588798,14.7183558 22,14.9499909 L22,17 C22,17.5522847 21.5522847,18 21,18 L11,18 L11,16 C11,15.4477153 10.5522847,15 10,15 C9.44771525,15 9,15.4477153 9,16 L9,18 L4,18 C3.44771525,18 3,17.5522847 3,17 L3,14.9499909 C4.14112016,14.7183558 5,13.709479 5,12.5 C5,11.290521 4.14112016,10.2816442 3,10.0500091 Z M10,11 C9.44771525,11 9,11.4477153 9,12 L9,13 C9,13.5522847 9.44771525,14 10,14 C10.5522847,14 11,13.5522847 11,13 L11,12 C11,11.4477153 10.5522847,11 10,11 Z" fill="#000000" opacity="0.3" transform="translate(12.500000, 12.500000) rotate(-45.000000) translate(-12.500000, -12.500000) "/>
                                        </g>
        							<?php }elseif($mod == 'Potentials'){?>
        								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <rect fill="#000000" opacity="0.3" x="11.5" y="2" width="2" height="4" rx="1"/>
                                            <rect fill="#000000" opacity="0.3" x="11.5" y="16" width="2" height="5" rx="1"/>
                                            <path d="M15.493,8.044 C15.2143319,7.68933156 14.8501689,7.40750104 14.4005,7.1985 C13.9508311,6.98949895 13.5170021,6.885 13.099,6.885 C12.8836656,6.885 12.6651678,6.90399981 12.4435,6.942 C12.2218322,6.98000019 12.0223342,7.05283279 11.845,7.1605 C11.6676658,7.2681672 11.5188339,7.40749914 11.3985,7.5785 C11.2781661,7.74950085 11.218,7.96799867 11.218,8.234 C11.218,8.46200114 11.2654995,8.65199924 11.3605,8.804 C11.4555005,8.95600076 11.5948324,9.08899943 11.7785,9.203 C11.9621676,9.31700057 12.1806654,9.42149952 12.434,9.5165 C12.6873346,9.61150047 12.9723317,9.70966616 13.289,9.811 C13.7450023,9.96300076 14.2199975,10.1308324 14.714,10.3145 C15.2080025,10.4981676 15.6576646,10.7419985 16.063,11.046 C16.4683354,11.3500015 16.8039987,11.7268311 17.07,12.1765 C17.3360013,12.6261689 17.469,13.1866633 17.469,13.858 C17.469,14.6306705 17.3265014,15.2988305 17.0415,15.8625 C16.7564986,16.4261695 16.3733357,16.8916648 15.892,17.259 C15.4106643,17.6263352 14.8596698,17.8986658 14.239,18.076 C13.6183302,18.2533342 12.97867,18.342 12.32,18.342 C11.3573285,18.342 10.4263378,18.1741683 9.527,17.8385 C8.62766217,17.5028317 7.88033631,17.0246698 7.285,16.404 L9.413,14.238 C9.74233498,14.6433354 10.176164,14.9821653 10.7145,15.2545 C11.252836,15.5268347 11.7879973,15.663 12.32,15.663 C12.5606679,15.663 12.7949989,15.6376669 13.023,15.587 C13.2510011,15.5363331 13.4504991,15.4540006 13.6215,15.34 C13.7925009,15.2259994 13.9286662,15.0740009 14.03,14.884 C14.1313338,14.693999 14.182,14.4660013 14.182,14.2 C14.182,13.9466654 14.1186673,13.7313342 13.992,13.554 C13.8653327,13.3766658 13.6848345,13.2151674 13.4505,13.0695 C13.2161655,12.9238326 12.9248351,12.7908339 12.5765,12.6705 C12.2281649,12.5501661 11.8323355,12.420334 11.389,12.281 C10.9583312,12.141666 10.5371687,11.9770009 10.1255,11.787 C9.71383127,11.596999 9.34650161,11.3531682 9.0235,11.0555 C8.70049838,10.7578318 8.44083431,10.3968355 8.2445,9.9725 C8.04816568,9.54816454 7.95,9.03200304 7.95,8.424 C7.95,7.67666293 8.10199848,7.03700266 8.406,6.505 C8.71000152,5.97299734 9.10899753,5.53600171 9.603,5.194 C10.0970025,4.85199829 10.6543302,4.60183412 11.275,4.4435 C11.8956698,4.28516587 12.5226635,4.206 13.156,4.206 C13.9160038,4.206 14.6918294,4.34533194 15.4835,4.624 C16.2751706,4.90266806 16.9686637,5.31433061 17.564,5.859 L15.493,8.044 Z" fill="#000000"/>
                                        </g>
                                    <?php }elseif($mod == 'Products'){?>
        								<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <rect x="0" y="0" width="24" height="24"/>
                                            <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3"/>
                                            <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000"/>
                                        </g>
        							<?php }?>
        							</svg></span><span class="kt-menu__link-text"><?php echo $subMenus['label'];?></span></a>
        						</li>
    						<?php }}?>
    					</ul>
					<?php }?>
				</div>
			</div>
		</div>
		