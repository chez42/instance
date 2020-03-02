{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{strip}
	{include file="layouts/rainbow/modules/Vtiger/Header.tpl"}
	{assign var="APP_IMAGE_MAP" value= Vtiger_MenuStructure_Model::getAppIcons()}

<style>
	
	.dropdown-submenu {
	    position: relative;
	}
	
	.dropdown-submenu .dropdown-menu {
	    top: 0;
	    left: 100%;
	    margin-top: -1px;
	}
	
	.mycFsAppMenu{
		position: fixed;
		top: 0;
		height: 100vh;
		width: 100vw;
		background-color: rgba(0,0,0,.6);
		z-index: 99999;
		padding: 10vw;
		overflow: auto;
		display: none;
	}
	
	.mycFsAppMenu-applink{
		padding: 20px;
		color: white;
	}	
	
	.mycFsAppMenu-appicon{
		font-size: 80px;
	    padding: 30px;
	    border-radius: 100%;
	    background-color: white;
	    color: black;
	}	
	.mycFsAppMenu-appname{
		font-size: 20px;
		margin-top: 10px;
	}	
</style>

<div class="mycFsAppMenu">
	<div class="row">
		{include file="modules/Vtiger/partials/ModuleIcons.tpl"|myclayout_path}
		{assign var=APP_GROUPED_MENU value=Settings_MenuEditor_Module_Model::getAllVisibleModules()}
		{assign var=APP_LIST value=Vtiger_MenuStructure_Model::getAppMenuList()}
		{foreach item=APP_NAME from=$APP_LIST}
			{foreach item=moduleModel key=moduleName from=$APP_GROUPED_MENU[$APP_NAME]}
		    {assign var='translatedModuleLabel' value=vtranslate($moduleModel->get('label'),$moduleName )}
	   
			<div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 text-center mycFsAppMenu-applink">
				<a href="{$moduleModel->getDefaultUrl()}&app={$APP_NAME}">
				<span class="mycFsAppMenu-appicon">
					{if $moduleModel->getName() eq 'Portal'}
						<span class="ti-desktop module-icon"></span>
					{else}
						{$moduleModel->getModuleIcon()}
					{/if}
				</span>
				<div class="clearfix"></div><br>
				<span class="mycFsAppMenu-appname">{$translatedModuleLabel}</span>
				</a>
			</div>
			{/foreach}
		{/foreach}
	</div>
</div>

<nav class="navbar navbar-default navbar-fixed-top app-fixed-navbar">
		
  <div class="container-fluid global-nav">
	<div class="row">
		<div class="col-lg-3 col-md-5 col-sm-4 col-xs-8 paddingRight0 app-navigator-container">
			<div class="row">
				<div id="appnavigator" class="col-lg-2 col-md-2 col-sm-2 col-xs-2 cursorPointer app-switcher-container hidden-lg hidden-md" data-app-class="{if $MODULE eq 'Home' || !$MODULE}ti-dashboard{else}{$APP_IMAGE_MAP[$SELECTED_MENU_CATEGORY]}{/if}">
					<div class="row app-navigator">
						<span class="app-icon fa fa-bars"></span>
					</div>
				</div>
				<!-- nuovo menu-->	
				<div class="dropdown col-lg-2 hidden-sm hidden-xs">
				  	<button class="btn btn-fask btn-lg" type="button" id="dropdownMenuButtonDesk" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				    	<i class="material-icons">menu</i>
				  	</button>
  					<div class="dropdown-menu fask" aria-labelledby="dropdownMenuButtonDesk">
  						<div class="bluredBackground"></div>
					    <ul class="faskfirst">
						    <li class="nav-small-cap hide">APPS</li>
		                        {assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
								{assign var=HOME_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Home')}
								{assign var=DASHBOARD_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Dashboard')}
				
								{if $USER_PRIVILEGES_MODEL->hasModulePermission($DASHBOARD_MODULE_MODEL->getId())}
									<li class="{if $MODULE eq "Home"}active{/if}"> 
										<a class=" waves-effect waves-dark" href="{$HOME_MODULE_MODEL->getDefaultUrl()}" >
											<i class="material-icons">dashboard</i>
											<span class="hide-menu" style="text-transform: uppercase">
											{vtranslate('LBL_DASHBOARD',$MODULE)} </span>
										</a>
	                        		</li>
								{/if}
								{*assign var=MAILMANAGER_MODULE_MODEL value=Vtiger_Module_Model::getInstance('MailManager')}
								{if $USER_PRIVILEGES_MODEL->hasModulePermission($MAILMANAGER_MODULE_MODEL->getId())}
									<li class="{if $MODULE eq "MailManager"}active{/if}"> 
										<a class=" waves-effect waves-dark" href="index.php?module=MailManager&view=List" >
											<i class="app-icon-list material-icons">email</i>
											<span class="hide-menu"> {vtranslate('MailManager')}</span>
										</a>
	                        		</li>
								{/if*}
								{assign var=TASK_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Task')}
								{if $USER_PRIVILEGES_MODEL->hasModulePermission($TASK_MODULE_MODEL->getId())}
									<li class="{if $MODULE eq "Task"}active{/if}"> 
										<a class=" waves-effect waves-dark" href="index.php?module=Task&view=List" >
											<i class="app-icon-list ">{$TASK_MODULE_MODEL->getModuleIcon()}</i>
											<span class="hide-menu"> {vtranslate('Task')}</span>
										</a>
	                        		</li>
								{/if}
								{assign var=COMMENTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('ModComments')}
								{if $USER_PRIVILEGES_MODEL->hasModulePermission($COMMENTS_MODULE_MODEL->getId())}
									<li class="{if $MODULE eq "ModComments"}active{/if}"> 
										<a class=" waves-effect waves-dark" href="index.php?module=ModComments&view=List" >
											<i class="app-icon-list fa fa-comment"></i>
											<span class="hide-menu"> {vtranslate('ModComments')}</span>
										</a>
	                        		</li>
								{/if}
								{assign var=DOCUMENTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Documents')}
								{if $USER_PRIVILEGES_MODEL->hasModulePermission($DOCUMENTS_MODULE_MODEL->getId())}
									<li class="{if $MODULE eq "Documents"}active{/if}"> 
										<a class=" waves-effect waves-dark" href="index.php?module=Documents&view=List" >
											<i class="app-icon-list material-icons">file_download</i>
											<span class="hide-menu"> {vtranslate('Documents')}</span>
										</a>
	                        		</li>
								{/if}
								{if $USER_MODEL->isAdminUser()}
									{if vtlib_isModuleActive('ExtensionStore')}
									
									<li class="{if $MODULE eq "ExtensionStore"}active{/if}"> 
										<a class=" waves-effect waves-dark" href="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore" >
											<i class="app-icon-list material-icons">shopping_cart</i>
											<span class="hide-menu"> {vtranslate('LBL_EXTENSION_STORE', 'Settings:Vtiger')}</span>
										</a>
	                        		</li>
								{/if}
							{/if}
							<hr/>
							{if $USER_MODEL->isAdminUser()}
	                            <li>
	                            	<a class="waves-effect waves-dark {if $MODULE eq $moduleName}active{/if}" href="index.php?module=Vtiger&parent=Settings&view=Index" >
	                            		<span class="module-icon"><i class="material-icons">settings</i></span>
	                            		<span class="hide-menu">  {vtranslate('LBL_CRM_SETTINGS','Vtiger')}</span>
	                        		</a>
	                    		</li>
								<li>
									<a class="waves-effect waves-dark {if $MODULE eq $moduleName}active{/if}" href="index.php?module=Users&parent=Settings&view=List" >
										<span class="module-icon"><i class="material-icons">contacts</i></span>
										<span class="hide-menu">   {vtranslate('LBL_MANAGE_USERS','Vtiger')}</span>
									</a>
								</li>
							{else}
								<li class="{if $MODULE eq "Users"}active{/if}"> 
									<a class=" waves-effect waves-dark" href="index.php?module=Users&view=Settings" >
										<i class="material-icons">settings</i>
										<span class="hide-menu" style="text-transform: uppercase"> {vtranslate('LBL_SETTINGS', 'Settings:Vtiger')}</span>
									</a>
	                        	</li>
							{/if}
						</ul>
						<ul class="fasksecond">
							{assign var=APP_GROUPED_MENU value=Settings_MenuEditor_Module_Model::getAllVisibleModules()}
							{assign var=APP_LIST value=Vtiger_MenuStructure_Model::getAppMenuList()}
							
							{if $MODULE eq "Home"}
								{assign var=SELECTED_MENU_CATEGORY value='Dashboard'}
							{/if}
							
							{foreach item=APP_NAME from=$APP_LIST}
								{if $APP_NAME eq 'ANALYTICS'} {continue}{/if}
								{if count($APP_GROUPED_MENU.$APP_NAME) gt 0}
									
									{foreach item=APP_MENU_MODEL from=$APP_GROUPED_MENU.$APP_NAME}
										{assign var=FIRST_MENU_MODEL value=$APP_MENU_MODEL}
										{if $APP_MENU_MODEL}
											{break}
										{/if}
									{/foreach}
										
									{include file="modules/Vtiger/partials/ModuleIcons.tpl"|myclayout_path}
									
									<li class="with-childs {if $SELECTED_MENU_CATEGORY eq $APP_NAME}active{/if}"> 
										<a class="has-arrow waves-effect waves-dark " >
											<i class="app-icon-list fa {$APP_IMAGE_MAP.$APP_NAME}" ></i>
											<span class="hide-menu">{vtranslate("$APP_NAME")}</span>
										</a>
                            
			                            <ul style="padding-left:6px;padding-top:15px;">
				                            {foreach item=moduleModel key=moduleName from=$APP_GROUPED_MENU[$APP_NAME]}
				                            	{assign var='translatedModuleLabel' value=vtranslate($moduleModel->get('label'),$moduleName )}
				                                <li>
				                                	<a class="waves-effect waves-dark {if $MODULE eq $moduleName}active{/if}" href="{$moduleModel->getDefaultUrl()}&app={$APP_NAME}" >
													{if $moduleModel->getName() eq 'Portal'}
														<span class="ti-desktop module-icon"></span>
													{else}
														{$moduleModel->getModuleIcon()}
													{/if}
												 	<span class="hide-menu"> {$translatedModuleLabel}</span></a>
											 	</li>
			                                {/foreach}
			                            </ul>
			                            
			                        </li>
											
								{/if}
							{/foreach}
							
						
                        
                        <li class="nav-small-cap hide">TOOLS & SETTINGS</li>
                         
			                      
						{foreach item=APP_MENU_MODEL from=$APP_GROUPED_MENU.$APP_NAME}
							{assign var=FIRST_MENU_MODEL value=$APP_MENU_MODEL}
							{if $APP_MENU_MODEL}
								{break}
							{/if}
						{/foreach}
			
		  			</div>
				</div>
				<!--fine menu-->
				
				<div class="logo-container col-lg-8 col-md-8 col-sm-8 col-xs-8">
					<div class="row">
						<a href="index.php" class="company-logo">
							<img src="{$COMPANY_LOGO->get('imagepath')}" alt="{$COMPANY_LOGO->get('alt')}"/>
						</a>
					</div>
				</div>
						
			</div>
		</div>
		<!--<div class="search-links-container col-md-3 col-lg-3 hidden-sm">
			<div class="search-link hidden-xs">
				<span class="ti-search" aria-hidden="true"></span>
				<input class="keyword-input" type="text" placeholder="{vtranslate('LBL_TYPE_SEARCH')}" value="{$GLOBAL_SEARCH_VALUE}">
				<span id="adv-search" class="adv-search ti-arrow-circle-down pull-right cursorPointer" aria-hidden="true"></span>
			</div>
		</div>-->
		<div id="navbar" class="col-sm-6 col-md-3 col-lg-3 collapse navbar-collapse navbar-right global-actions">
			<ul class="nav navbar-nav">
				<li>
					<div class="search-links-container hidden-sm">
						<div class="search-link hidden-xs">
							<span class="ti-search" aria-hidden="true"></span>
							<input class="keyword-input" type="text" placeholder="{vtranslate('LBL_TYPE_SEARCH')}" value="{$GLOBAL_SEARCH_VALUE}">
							<span id="adv-search" title="Advanced Search" class="adv-search ti-arrow-circle-down pull-right cursorPointer" aria-hidden="true"></span>
						</div>
					</div>
				</li>
                <li>
					<div class="dropdown">
						<div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
							<a href="#" id="menubar_quickCreate" class="qc-button" title="{vtranslate('LBL_QUICK_CREATE',$MODULE)}" aria-hidden="true"> 
							<i class="material-icons">add</i></a>
						</div>
						<ul class="dropdown-menu animated fadeIn" role="menu" aria-labelledby="dropdownMenu1" style="width:650px;">
							<li class="title" style="padding: 5px 0 0 15px;">
								<h4><strong>{vtranslate('LBL_QUICK_CREATE',$MODULE)}</strong></h4>
							</li>
							<hr/>
							<li id="quickCreateModules" style="padding: 0 5px;">
								<div class="col-lg-12" style="padding-bottom:15px;">
									{foreach key=moduleName item=moduleModel from=$QUICK_CREATE_MODULES}
										{if $moduleModel->isPermitted('CreateView') || $moduleModel->isPermitted('EditView')}
											{assign var='quickCreateModule' value=$moduleModel->isQuickCreateSupported()}
											{assign var='singularLabel' value=$moduleModel->getSingularLabelKey()}
											{assign var=hideDiv value={!$moduleModel->isPermitted('CreateView') && $moduleModel->isPermitted('EditView')}}
											{include file="modules/Vtiger/partials/ModuleIcons.tpl"|myclayout_path}
											{if $quickCreateModule == '1'}
												{if $count % 3 == 0}
													<div class="row">
													{/if}
													{* Adding two links,Event and Task if module is Calendar *}
													{if $singularLabel == 'SINGLE_Calendar'}
														{assign var='singularLabel' value='LBL_TASK'}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4{/if}">
															<a id="menubar_quickCreate_Events" class="quickCreateModule" data-name="Events"
															   data-url="index.php?module=Events&view=QuickCreateAjax" href="javascript:void(0)"><i class="material-icons pull-left">event</i><span class="quick-create-module">{vtranslate('LBL_EVENT',$moduleName)}</span></a>
														</div>
														{*if $count % 3 == 2}
															</div>
															<br>
															<div class="row">
														{/if}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4{/if}">
															<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
															   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)"><i class="material-icons pull-left">card_travel</i><span class="quick-create-module">{vtranslate($singularLabel,$moduleName)}</span></a>
														</div>
														{if !$hideDiv}
															{assign var='count' value=$count+1}
														{/if*}
													{else if $singularLabel == 'SINGLE_Documents'}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4{/if} dropdown">
															<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModuleSubmenu dropdown-toggle" data-name="{$moduleModel->getName()}" data-toggle="dropdown" 
															   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">
																
																{$moduleModel->getModuleIcon()}
																<span class="quick-create-module">
																	{vtranslate($singularLabel,$moduleName)}
																	<i class="fa fa-caret-down quickcreateMoreDropdownAction"></i>
																</span>
															</a>
															<ul class="dropdown-menu quickcreateMoreDropdown" aria-labelledby="menubar_quickCreate_{$moduleModel->getName()}">
																<li class="dropdown-header"><i class="material-icons">file_upload</i> {vtranslate('LBL_FILE_UPLOAD', $moduleName)}</li>
																<li id="VtigerAction">
																	<a href="javascript:Documents_Index_Js.uploadTo('Vtiger')">
																		<i class="fa fa-desktop"> </i>  {vtranslate('LBL_FROM_COMPUTER', 'Documents' )}
																	</a>
																</li>
																<li class="dropdown-header"><i class="ti-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', $moduleName)}</li>
																<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E')">&nbsp;<i class="material-icons">link</i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', $moduleName, {vtranslate('LBL_FILE_URL', $moduleName)})}</a></li>
																<li role="separator" class="divider"></li>
																<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W')"><i class="ti-file"></i> {vtranslate('LBL_CREATE_NEW', $moduleName, {vtranslate('SINGLE_Documents', $moduleName)})}</a></li>
															</ul>
														</div>
													{else}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-lg-4{/if} {if $moduleModel->getName() eq 'Campaigns' || $moduleModel->getName() eq 'ProjectTask' || $moduleModel->getName() eq 'ProjectMilestone'}hide {/if}">
															<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
															   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">
																{*<i class="material-icons pull-left">{$iconsarray[{strtolower($moduleName)}]}</i>*}
																{*<i class="vicon-{strtolower($moduleName)} pull-left"></i>*}
																{$moduleModel->getModuleIcon()}
																<span class="quick-create-module">{vtranslate($singularLabel,$moduleName)}</span>
															</a>
														</div>
													{/if}
													{if $count % 3 == 2}
														</div>
														<br>
													{/if}
												{if !$hideDiv}
													{assign var='count' value=$count+1}
												{/if}
											{/if}
										{/if}
									{/foreach}
								</div>
							</li>
						</ul>
					</div>
				</li>
				{assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
				{assign var=MAILMANAGER_MODULE_MODEL value=Vtiger_Module_Model::getInstance('MailManager')}
				{if $USER_PRIVILEGES_MODEL->hasModulePermission($MAILMANAGER_MODULE_MODEL->getId())}
					<li>
						<div style="margin:-5px !important;">
							<a href="index.php?module=MailManager&view=List" target = "_blank" class="vicon"  title="{vtranslate('MailManager')}" aria-hidden="true">
								<i class="vicon-mailmanager"></i>
							</a>
						</div>
					</li>
				{/if}
				{assign var=CALENDAR_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Calendar')}
				{if $USER_PRIVILEGES_MODEL->hasModulePermission($CALENDAR_MODULE_MODEL->getId())}
					<li>
						<div>
							<a href="index.php?module=Calendar&view={$CALENDAR_MODULE_MODEL->getDefaultViewName()}" title="{vtranslate('Calendar','Calendar')}" aria-hidden="true">
								<i class="material-icons">event</i>
							</a>
						</div>
					</li>
				{/if}
				{assign var=REPORTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Reports')}
				{if $USER_PRIVILEGES_MODEL->hasModulePermission($REPORTS_MODULE_MODEL->getId())}
					<li>
						<div>
							<a href="index.php?module=Reports&view=List" title="{vtranslate('Reports','Reports')}" aria-hidden="true">
								<i class="material-icons">show_chart</i>
							</a>
						</div>
					</li>
				{/if}
				{if $USER_PRIVILEGES_MODEL->hasModulePermission($CALENDAR_MODULE_MODEL->getId())}
					<li>
						<div>
							<a href="#" class="taskManagement" title="{vtranslate('Tasks','Vtiger')}" aria-hidden="true">
								<i class="material-icons">card_travel</i>
							</a>
						</div>
					</li>
				{/if}
				
				{assign var=MYCTHEME_MODULE_MODEL value=Vtiger_Module_Model::getInstance('OmniThemeManager')}
				{if $USER_PRIVILEGES_MODEL->hasModulePermission($MYCTHEME_MODULE_MODEL->getId())}
					<li>
						<div>
							<a href="#" class="themeStyler" title="Theme Styler" aria-hidden="true">
								<i class="material-icons">brush</i>
							</a>
						</div>
					</li>
				{/if}
				<li class="dropdown">
					<div>
						{assign var=IMAGE_DETAILS value=$USER_MODEL->getImageDetails()}
						{$IMAGE_DETAILS = $IMAGE_DETAILS['imagename']}
						{if $IMAGE_DETAILS neq '' && $IMAGE_DETAILS[0] neq '' && $IMAGE_DETAILS[0].path eq ''}
							<a href="#" class="userName dropdown-toggle " data-toggle="dropdown" role="button" title="{$USER_MODEL->get('first_name')} {$USER_MODEL->get('last_name')}
							  ({$USER_MODEL->get('user_name')})">
							  <i class="material-icons">perm_identity</i>
							  <span class="link-text-xs-only hidden-lg hidden-md hidden-sm">{$USER_MODEL->getName()}</span>
						    </a>
						{else}
							{foreach item=IMAGE_INFO from=$IMAGE_DETAILS}
								{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
									<a href="#" class="userName dropdown-toggle" data-toggle="dropdown" role="button" title="{$USER_MODEL->get('first_name')} {$USER_MODEL->get('last_name')}
				  					({$USER_MODEL->get('user_name')})"><img style="width: 30px;border-radius: 50%;
										padding: 7px 0px;" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}"></a>
								{/if}
							{/foreach}
						{/if}
						<div class="dropdown-menu logout-content animated flipInY" role="menu">
							<div class="row">
								<div class="col-lg-12 col-sm-12" style="padding:10px;">
									<div class="profile-container col-lg-5 col-sm-5">
										{assign var=IMAGE_DETAILS value=$USER_MODEL->getImageDetails()}
										{if $IMAGE_DETAILS neq '' && $IMAGE_DETAILS[0] neq '' && $IMAGE_DETAILS[0].path eq ''}
											<i class='material-icons'>perm_identity</i>
										{else}
											{foreach item=IMAGE_INFO from=$IMAGE_DETAILS}
												{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
													<img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}">
												{/if}
											{/foreach}
										{/if}
									</div>
									<div class="col-lg-7 col-sm-7">
										<h5>{$USER_MODEL->get('first_name')} {$USER_MODEL->get('last_name')}</h5>
										<h6 class="textOverflowEllipsis" title='{$USER_MODEL->get('user_name')}'>{$USER_MODEL->get('user_name')} | {$USER_MODEL->getUserRoleName()}</h6>
										{assign var=useremail value=$USER_MODEL->get('email1')}
										<h6 class="textOverflowEllipsis" title='{$USER_MODEL->get('email')}'>{$useremail}</h6>
									</div>
									<hr style="margin: 10px 0 !important">
									<div class="col-lg-12 col-sm-12">
										<ul class="dropdown-user">
									<li role="separator" class="divider"></li>
									<li>
										
										<a id="menubar_item_right_LBL_MY_PREFERENCES" href="{$USER_MODEL->getPreferenceDetailViewUrl()}">
										<i class="material-icons">settings</i> {vtranslate('LBL_MY_PREFERENCES')}</a>
									</li>
									<li>
										
										<a id="menubar_item_right_LBL_SIGN_OUT" href="index.php?module=Users&action=Logout">
										<i class="material-icons">power_settings_new</i> {vtranslate('LBL_SIGN_OUT')}</a>
									</li>
								</ul>
								</div>
								</div>
							</div>
							
						</div>
							</div>
						</li>
					</ul>
				</div>
				<div class="col-xs-4 visible-xs padding0px quickTopButtons">
					<div class="dropdown btn-group pull-right">
						
						<button class="btn dropdown-toggle" style="background-color: transparent;padding: 12px;color: #fff;margin-top: -1px;margin-bottom:0px;border: none;" data-toggle="dropdown" aria-expanded="true">
							<a href="#" id="menubar_quickCreate_mobile" class="qc-button" title="{vtranslate('LBL_QUICK_CREATE',$MODULE)}" aria-hidden="true">
							<i class="material-icons">add</i>&nbsp;<span class="caret"></span></a>
						</button>
						<ul class="dropdown-menu">
							<li class="title" style="padding: 5px 0 0 15px;">
								<h4><strong>{vtranslate('LBL_QUICK_CREATE',$MODULE)}</strong></h4>
							</li>
							<hr/>
							<li id="quickCreateModules_mobile" style="padding: 0 8px;">
								<div class="col-xs-12 padding0px" style="padding-bottom:15px;">
									{foreach key=moduleName item=moduleModel from=$QUICK_CREATE_MODULES}
										{if $moduleModel->isPermitted('CreateView') || $moduleModel->isPermitted('EditView')}
											{assign var='quickCreateModule' value=$moduleModel->isQuickCreateSupported()}
											{assign var='singularLabel' value=$moduleModel->getSingularLabelKey()}
											{assign var=hideDiv value={!$moduleModel->isPermitted('CreateView') && $moduleModel->isPermitted('EditView')}}
											{if $quickCreateModule == '1'}
												{if $count % 3 == 0}
													<div class="row">
													{/if}
													{* Adding two links,Event and Task if module is Calendar *}
													{if $singularLabel == 'SINGLE_Calendar'}
														{assign var='singularLabel' value='LBL_TASK'}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-xs-12{/if}">
															<a id="menubar_quickCreate_Events" class="quickCreateModule" data-name="Events"
															   data-url="index.php?module=Events&view=QuickCreateAjax" href="javascript:void(0)"><i class="material-icons pull-left">event</i><span class="quick-create-module">{vtranslate('LBL_EVENT',$moduleName)}</span></a>
														</div>
														{if $count % 3 == 2}
															</div>
															<div class="row">
														{/if}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-xs-12{/if}">
															<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
															   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)"><i class="material-icons pull-left">card_travel</i><span class="quick-create-module">{vtranslate($singularLabel,$moduleName)}</span></a>
														</div>
														{if !$hideDiv}
															{assign var='count' value=$count+1}
														{/if}
													{else if $singularLabel == 'SINGLE_Documents'}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-xs-12{/if} dropdown">
															<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModuleSubmenu dropdown-toggle" data-name="{$moduleModel->getName()}" data-toggle="dropdown" 
															   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">
																{*<i class="material-icons pull-left">{$iconsarray[{strtolower($moduleName)}]}</i>*}
																{*<i class="vicon-{strtolower($moduleName)} pull-left"></i>*}
																{$moduleModel->getModuleIcon()}
																<span class="quick-create-module">
																	{vtranslate($singularLabel,$moduleName)}
																	<i class="fa fa-caret-down quickcreateMoreDropdownAction"></i>
																</span>
															</a>
															<ul class="dropdown-menu quickcreateMoreDropdown" aria-labelledby="menubar_quickCreate_{$moduleModel->getName()}">
																<li class="dropdown-header"><i class="ti-upload"></i> {vtranslate('LBL_FILE_UPLOAD', $moduleName)}</li>
																<li id="VtigerAction">
																	<a href="javascript:Documents_Index_Js.uploadTo('Vtiger')">
																		<i class="fa fa-desktop"> </i>  {vtranslate('LBL_FROM_COMPUTER', 'Documents' )}
																	</a>
																</li>
																<li class="dropdown-header"><i class="ti-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', $moduleName)}</li>
																<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E')">&nbsp;<i class="ti-link"></i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', $moduleName, {vtranslate('LBL_FILE_URL', $moduleName)})}</a></li>
																<li role="separator" class="divider"></li>
																<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W')"><i class="ti-file"></i> {vtranslate('LBL_CREATE_NEW', $moduleName, {vtranslate('SINGLE_Documents', $moduleName)})}</a></li>
															</ul>
														</div>
														{if $count % 3 == 2}
															</div>
														{/if}
														{if !$hideDiv}
															{assign var='count' value=$count+1}
														{/if}
													{else}
														<div class="{if $hideDiv}create_restricted_{$moduleModel->getName()} hide{else}col-xs-12{/if}">
															<a id="menubar_quickCreate_{$moduleModel->getName()}" class="quickCreateModule" data-name="{$moduleModel->getName()}"
															   data-url="{$moduleModel->getQuickCreateUrl()}" href="javascript:void(0)">
																{*<i class="material-icons pull-left">{$iconsarray[{strtolower($moduleName)}]}</i>*}
																{*<i class="vicon-{strtolower($moduleName)} pull-left"></i>*}
																{$moduleModel->getModuleIcon()}
																<span class="quick-create-module">{vtranslate($singularLabel,$moduleName)}</span>
															</a>
														</div>
													{/if}
													{if $count % 3 == 2}
														</div>
													{/if}
												{if !$hideDiv}
													{assign var='count' value=$count+1}
												{/if}
											{/if}
										{/if}
									{/foreach}
								</div>
							</li>
						</ul>
							
				</div>

				<div class="dropdown btn-group pull-right">
				    <button style="background-color: transparent;padding: 12px;color: #fff;margin-top: -1px;margin-bottom:0px;border: none;border-right: 1px solid #fff; border-radius: 0px; " class="btn dropdown-toggle" type="button" data-toggle="dropdown"><i class="material-icons">settings</i>
				    &nbsp;<span class="caret"></span></button>
				    					<ul class="dropdown-menu">
				      					
				      					
				
						<div class="clearfix"></div>

						
      					{assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
						{assign var=CALENDAR_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Calendar')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($CALENDAR_MODULE_MODEL->getId())}
							<li><a href="index.php?module=Calendar&view={$CALENDAR_MODULE_MODEL->getDefaultViewName()}" title="{vtranslate('Calendar','Calendar')}" aria-hidden="true"><i class="material-icons">event</i>&nbsp;{vtranslate('Calendar','Calendar')}</a></li>
						{/if}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($CALENDAR_MODULE_MODEL->getId())}
							<li><a class="taskManagement" href="#" title="{vtranslate('Task','Task')}" aria-hidden="true"><i class="material-icons">card_travel</i>&nbsp;{vtranslate('Task','Task')}</a></li>
						{/if}
						{assign var=MYCTHEME_MODULE_MODEL value=Vtiger_Module_Model::getInstance('OmniThemeManager')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($MYCTHEME_MODULE_MODEL->getId())}
						<li><div><a href="#" class="themeStyler" title="Theme Styler" aria-hidden="true">
							<i class="material-icons">brush</i>&nbsp;Theme Styler</a></div></li>
						{/if}
						
						{assign var=REPORTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Reports')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($REPORTS_MODULE_MODEL->getId())}
							<li><a href="index.php?module=Reports&view=List" title="{vtranslate('Reports','Reports')}" aria-hidden="true"><i class="material-icons">pie_chart</i>&nbsp;{vtranslate('Reports','Reports')}</a></li>
						{/if}

                        <li class="divider"></li>
                        <li class="dropdown-header"> 
	                        {$USER_MODEL->get('first_name')} {$USER_MODEL->get('last_name')}
	                        <br/>
	                        {$USER_MODEL->get('user_name')} | {$USER_MODEL->getUserRoleName()}
							</li>
							 <li class="divider"></li>
							<li>
							<a id="menubar_item_right_LBL_MY_PREFERENCES" href="{$USER_MODEL->getPreferenceDetailViewUrl()}"><i class="material-icons">settings</i>&nbsp;{vtranslate('LBL_MY_PREFERENCES')}</a>
							</li>
							<li>
							<a id="menubar_item_right_LBL_SIGN_OUT" href="index.php?module=Users&action=Logout"><i class="material-icons">power_settings_new</i>&nbsp;{vtranslate('LBL_SIGN_OUT')}</a>
						</li>

    					</ul>
				 </div>
				</div>
			</div>
		</div>
{/strip}