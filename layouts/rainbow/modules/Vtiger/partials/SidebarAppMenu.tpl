{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}



<aside class="left-sidebar hidden-lg hidden-md" style="" id="parent">
<div class="user-panel">
		 
        <div class="image hide">
         {assign var=IMAGE_DETAILS value=$USER_MODEL->getImageDetails()}
												{if $IMAGE_DETAILS neq '' && $IMAGE_DETAILS[0] neq '' && $IMAGE_DETAILS[0].path eq ''}
										
									
									<span class="link-text-xs-only hidden-lg hidden-md hidden-sm">{$USER_MODEL->getName()}</span>
												{else}
													{foreach item=IMAGE_INFO from=$IMAGE_DETAILS}
														{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
															<img style="width: 30px;border-radius: 50%;
    padding: 7px 0px;" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}">
														{/if}
													{/foreach}
												{/if}
        </div>



</div>
	  
	  
            <!-- Sidebar scroll-->

<!-- search mobile-->

<div class="col-xs-12 visible-sm visible-xs" id ="searchmobile">
<div class="search-links-container">
				 <div class="search-link">
						<span class="ti-search" aria-hidden="true"></span>
						<input class="mobile-search-key keyword-input" type="text" placeholder="{vtranslate('LBL_TYPE_SEARCH')}" value="{$GLOBAL_SEARCH_VALUE}">
					</div>
				</div>
				</div>  
<!--/ search mobile-->

<div class="clearfix"></div>

				<div class="scroll-sidebar " >

                <!-- Sidebar navigation-->
                <nav class="sidebar-nav active" style="padding-bottom: 50px">
                    <ul id="sidebarnav" class="in mini-sidebar">
	                    
	                    <li class="sidebar-logo hide">
	                    	<img src="{$COMPANY_LOGO->get('imagepath')}" alt="{$COMPANY_LOGO->get('alt')}"/>
	                    </li>
                        
                        <li class="nav-small-cap hide">APPS</li>
                        
                        {assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
						{assign var=HOME_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Home')}
						{assign var=DASHBOARD_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Dashboard')}
			
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($DASHBOARD_MODULE_MODEL->getId())}
							<li class="{if $MODULE eq "Home"}active{/if}"> 
								<a class=" waves-effect waves-dark" href="{$HOME_MODULE_MODEL->getDefaultUrl()}" >
									<i class="material-icons">dashboard</i>
									<span class="hide-menu" style="text-transform: uppercase">{vtranslate('LBL_DASHBOARD',$MODULE)} </span>
								</a>
                    		</li>
						{/if}
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
									
								<li class="with-childs {if $SELECTED_MENU_CATEGORY eq $APP_NAME}active{/if}"> <a class="has-arrow waves-effect waves-dark " href="#" aria-expanded="{if $SELECTED_MENU_CATEGORY eq $APP_NAME}true{else}false{/if}">
									<i class="app-icon-list fa {$APP_IMAGE_MAP.$APP_NAME}" ></i><span class="hide-menu">{vtranslate("$APP_NAME")}</span></a>
		                            
		                            <ul aria-expanded="{if $SELECTED_MENU_CATEGORY eq $APP_NAME}true{else}false{/if}" class="collapse {if $SELECTED_MENU_CATEGORY eq $APP_NAME}in{/if}" style="padding-left:0px;padding-top:4px;">
			                            {foreach item=moduleModel key=moduleName from=$APP_GROUPED_MENU[$APP_NAME]}
				                            {assign var='translatedModuleLabel' value=vtranslate($moduleModel->get('label'),$moduleName )}
											
			                                <li>
			                                	<a class="waves-effect waves-dark {if $MODULE eq $moduleName}active{/if}" href="{$moduleModel->getDefaultUrl()}&app={$APP_NAME}" {if $moduleName eq 'Accounts' || $moduleName eq 'PriceBooks'}target="_blank"{/if} >
													{if $moduleName eq 'PortfolioInformation'}
														<span class="module-icon"><img src="layouts/rainbow/icons/PortfolioInformation.png" title="{$translatedModuleLabel}"></span>
														{*<i class="fa fa-line-chart" aria-hidden="true"></i>*}
													{else if $moduleName eq 'Connection'}
														<span class="module-icon"><img src="layouts/rainbow/icons/Connection.png" title="{$translatedModuleLabel}"></span>
														{*<i class="fa fa-users" aria-hidden="true"></i>*}
													{else if $moduleName eq 'ModComments'}
														<span class="module-icon"><img src="layouts/rainbow/icons/ModComments.png" title="{$translatedModuleLabel}"></span>
														{*<i class="fa fa-comments-o" aria-hidden="true"></i>*}
													{else if $moduleName eq 'RingCentral'}
														<i class="fa fa-phone-square module-icon" aria-hidden="true"></i>
													{else if $moduleName eq 'Task'}
														<span class="module-icon"><img src="layouts/rainbow/icons/Task.png" title="{$translatedModuleLabel}"></span>
														{*<i class="fa fa-tasks" aria-hidden="true"></i>*}
													{else if $moduleName eq 'Timecontrol'}
														<i class="fa fa-hourglass module-icon" aria-hidden="true"></i>
													{else if $moduleName eq 'EmailTemplates'}
														<span class="module-icon"><img src="layouts/rainbow/icons/EmailTemplates.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'CalendarTemplate'}
														<i class="fa fa-fast-forward module-icon" aria-hidden="true"></i>
													{else if $moduleName eq 'Documents'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Documents.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'HelpDesk'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/HelpDesk.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'Instances'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Instances.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'ModSecurities'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/ModSecurities.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'Notifications'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Notifications.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'PositionInformation'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/PositionInformation.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'QuotingTool'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/QuotingTool.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'Transactions'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Transactions.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'Contacts'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Contacts.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'PandaDoc'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/PandaDoc.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'VTEEmailMarketing'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/EmailMarketing.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'Invoice'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Invoice.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'PurchaseOrder'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/PurchaseOrder.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'Quotes'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/Quotes.png" title="{$translatedModuleLabel}"></span>
													{else if $moduleName eq 'SalesOrder'} 
														<span class="module-icon"><img src="layouts/rainbow/icons/SalesOrder.png" title="{$translatedModuleLabel}"></span>
													{else}
														<i class="material-icons module-icon" >{$iconsarray[{strtolower($moduleName)}]}</i>
													{/if}
													<span class="hide-menu"> {$translatedModuleLabel}</span>
												</a>
											</li>
		                                {/foreach}
		                            </ul>
	                        	</li>
							{/if}
						{/foreach}
                        
                        <li class="nav-small-cap hide">TOOLS & SETTINGS</li>
                       
						{*assign var=MAILMANAGER_MODULE_MODEL value=Vtiger_Module_Model::getInstance('MailManager')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($MAILMANAGER_MODULE_MODEL->getId())}
							<li class="{if $MODULE eq "MailManager"}active{/if}"> 
								<a class=" waves-effect waves-dark" href="index.php?module=MailManager&view=List" >
									<i class="app-icon-list material-icons">email</i>
									<span class="hide-menu"> {vtranslate('MailManager')}</span>
								</a>
                        	</li>
						{/if*}
						{assign var=DOCUMENTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Documents')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($DOCUMENTS_MODULE_MODEL->getId())}
							
							<li class="{if $MODULE eq "Documents"}active{/if}"> 
								<a class=" waves-effect waves-dark" href="index.php?module=Documents&view=List" target="_blank">
									{*<i class="app-icon-list material-icons">file_download</i>*}
									<span class="module-icon"><img src="layouts/rainbow/icons/Documents.png" title="{vtranslate('Documents')}"></span>
									<span class="hide-menu"> {vtranslate('Documents')}</span>
								</a>
                        </li>
						{/if}
						{assign var=TASK_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Task')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($TASK_MODULE_MODEL->getId())}
							<li class="{if $MODULE eq "Task"}active{/if}"> 
								<a class=" waves-effect waves-dark" href="index.php?module=Task&view=List" >
									{*<i class="fa fa-tasks" aria-hidden="true"></i>*}
									<span class="module-icon"><img src="layouts/rainbow/icons/Task.png" title="{vtranslate('Task')}"></span>
									<span class="hide-menu"> {vtranslate('Task')}</span>
								</a>
                        	</li>
						{/if}
						{assign var=COMMENTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('ModComments')}
						{if $USER_PRIVILEGES_MODEL->hasModulePermission($COMMENTS_MODULE_MODEL->getId())}
							<li class="{if $MODULE eq "ModComments"}active{/if}"> 
								<a class=" waves-effect waves-dark" href="index.php?module=ModComments&view=List" target="_blank">
									{*<i class="fa fa-comments-o" aria-hidden="true"></i>*}
									<span class="module-icon"><img src="layouts/rainbow/icons/ModComments.png" title="{vtranslate('ModComments')}"></span>
									<span class="hide-menu"> {vtranslate('ModComments')}</span>
								</a>
                        	</li>
						{/if}
						{if $USER_MODEL->isAdminUser()}
							{if vtlib_isModuleActive('ExtensionStore')}
								
								<li class="{if $MODULE eq "ExtensionStore"}active{/if}"> <a class=" waves-effect waves-dark" href="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore" ><i class="app-icon-list material-icons">shopping_cart</i><span class="hide-menu"> {vtranslate('LBL_EXTENSION_STORE', 'Settings:Vtiger')}</span></a>
                        </li>
							{/if}
						{/if}
			                      
			                      
			                      
			                      
			                      
			                      
			                      
			                      
				{foreach item=APP_MENU_MODEL from=$APP_GROUPED_MENU.$APP_NAME}
					{assign var=FIRST_MENU_MODEL value=$APP_MENU_MODEL}
					{if $APP_MENU_MODEL}
						{break}
					{/if}
				{/foreach}
			
			
			{if $USER_MODEL->isAdminUser()}
			
			<li class="with-childs {if $SELECTED_MENU_CATEGORY eq $APP_NAME}active{/if}"> <a class="has-arrow waves-effect waves-dark " href="#" aria-expanded="{if $SELECTED_MENU_CATEGORY eq $APP_NAME}true{else}false{/if}"><i class="app-icon-list material-icons">settings</i><span class="hide-menu"> {vtranslate('LBL_SETTINGS', 'Settings:Vtiger')}</span></a>
                            
                            <ul  style="padding-left:6px;padding-top:4px;" aria-expanded="{if $SELECTED_MENU_CATEGORY eq $APP_NAME}true{else}false{/if}" class="collapse {if $SELECTED_MENU_CATEGORY eq $APP_NAME}in{/if}">
	                           
	                            <li><a class="waves-effect waves-dark {if $MODULE eq $moduleName}active{/if}" href="index.php?module=Vtiger&parent=Settings&view=Index" target="_blank" ><span class="module-icon"><i class="material-icons">settings</i></span><span class="hide-menu">  {vtranslate('LBL_CRM_SETTINGS','Vtiger')}</span></a></li>
					
								<li><a class="waves-effect waves-dark {if $MODULE eq $moduleName}active{/if}" href="index.php?module=Users&parent=Settings&view=List" ><span class="module-icon"><i class="material-icons">contacts</i></span><span class="hide-menu">   {vtranslate('LBL_MANAGE_USERS','Vtiger')}</span></a></li>
					
						
	                            
                            </ul>
			</li>  
			
			{*else}
				
				<li class="{if $MODULE eq "Users"}active{/if}"> <a class=" waves-effect waves-dark" href="index.php?module=Users&view=Settings" ><i class="material-icons">settings</i><span class="hide-menu" style="text-transform: uppercase"> {vtranslate('LBL_SETTINGS', 'Settings:Vtiger')}</span></a>
                        </li>*}
                        
			{/if}
			
                       
                    </ul>
                </nav>
        </aside>




<div class="app-menu hide" id="app-menu">
	<div class="container-fluid">
		<div class="row">
			<div class="col-sm-2 col-xs-2 cursorPointer app-switcher-container">
				<div class="row app-navigator">
					<span id="menu-toggle-action" class="app-icon ti-close"></span>
				</div>
			</div>
		</div>
		{assign var=USER_PRIVILEGES_MODEL value=Users_Privileges_Model::getCurrentUserPrivilegesModel()}
		{assign var=HOME_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Home')}
		{assign var=DASHBOARD_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Dashboard')}
		<div class="app-list row">
			{if $USER_PRIVILEGES_MODEL->hasModulePermission($DASHBOARD_MODULE_MODEL->getId())}
				<div class="menu-item app-item dropdown-toggle" data-default-url="{$HOME_MODULE_MODEL->getDefaultUrl()}">
					<div class="menu-items-wrapper">
						<span class="app-icon-list"><i class="material-icons">dashboard</i></span>
						<span class="app-name textOverflowEllipsis"> {vtranslate('LBL_DASHBOARD',$MODULE)}</span>
					</div>
				</div>
			{/if}
			{assign var=APP_GROUPED_MENU value=Settings_MenuEditor_Module_Model::getAllVisibleModules()}
			{assign var=APP_LIST value=Vtiger_MenuStructure_Model::getAppMenuList()}
			{foreach item=APP_NAME from=$APP_LIST}
				{if $APP_NAME eq 'ANALYTICS'} {continue}{/if}
				{if count($APP_GROUPED_MENU.$APP_NAME) gt 0}
					<div class="dropdown app-modules-dropdown-container">
						{foreach item=APP_MENU_MODEL from=$APP_GROUPED_MENU.$APP_NAME}
							{assign var=FIRST_MENU_MODEL value=$APP_MENU_MODEL}
							{if $APP_MENU_MODEL}
								{break}
							{/if}
						{/foreach}
						<div class="menu-item app-item dropdown-toggle app-item-color-{$APP_NAME}" data-app-name="{$APP_NAME}" id="{$APP_NAME}_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-default-url="{$FIRST_MENU_MODEL->getDefaultUrl()}&app={$APP_NAME}">
							<div class="menu-items-wrapper app-menu-items-wrapper">
								<span class="app-icon-list fa {$APP_IMAGE_MAP.$APP_NAME}"></span>
								<span class="app-name textOverflowEllipsis"> {vtranslate("$APP_NAME")}</span>
								<span class="ti-angle-right pull-right"></span>
							</div>
						</div>
						<ul class="dropdown-menu app-modules-dropdown" aria-labelledby="{$APP_NAME}_modules_dropdownMenu">
							<li class="visible-sm visible-xs app-item-color-{$APP_NAME}">
								<span style="color:white">
									<span class="app-icon-list fa {$APP_IMAGE_MAP.$APP_NAME}"></span>
									<span class="app-name textOverflowEllipsis"> {vtranslate("$APP_NAME")}</span>
								</span>
							</li>
							
							{foreach item=moduleModel key=moduleName from=$APP_GROUPED_MENU[$APP_NAME]}
								{assign var='translatedModuleLabel' value=vtranslate($moduleModel->get('label'),$moduleName )}
								<li>
									<a href="{$moduleModel->getDefaultUrl()}&app={$APP_NAME}" title="{$translatedModuleLabel}">
									
										<span class="module-icon">
											<i class="material-icons">{$iconsarray[{strtolower($moduleName)}]}</i>
										</span>
										<span class="module-name textOverflowEllipsis">{$translatedModuleLabel}</span>
									</a>
								</li>
							{/foreach}
						</ul>
					</div>
				{/if}
			{/foreach}
			<div class="app-list-divider"></div>
			{assign var=MAILMANAGER_MODULE_MODEL value=Vtiger_Module_Model::getInstance('MailManager')}
			{if $USER_PRIVILEGES_MODEL->hasModulePermission($MAILMANAGER_MODULE_MODEL->getId())}
				<div class="menu-item app-item app-item-misc" data-default-url="index.php?module=MailManager&view=List">
					<div class="menu-items-wrapper">
						<span class="app-icon-list"><i class="maerial-icons">email</i></span>
						<span class="app-name textOverflowEllipsis"> {vtranslate('MailManager')}</span>
					</div>
				</div>
			{/if}
			{assign var=DOCUMENTS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Documents')}
			{if $USER_PRIVILEGES_MODEL->hasModulePermission($DOCUMENTS_MODULE_MODEL->getId())}
				<div class="menu-item app-item app-item-misc" data-default-url="index.php?module=Documents&view=List">
					<div class="menu-items-wrapper">
						<span class="app-icon-list">
							{*<i class="material-icons">file_download</i>*}
							<span class="module-icon"><img src="layouts/rainbow/icons/Documents.png" title="{vtranslate('Documents')}"></span>
						</span>
						<span class="app-name textOverflowEllipsis"> {vtranslate('Documents')}</span>
					</div>
				</div>
			{/if}
			{if $USER_MODEL->isAdminUser()}
				{if vtlib_isModuleActive('ExtensionStore')}
					<div class="menu-item app-item app-item-misc" data-default-url="index.php?module=ExtensionStore&parent=Settings&view=ExtensionStore">
						<div class="menu-items-wrapper">
							<span class="app-icon-list"><i class="material-icons">shopping_cart</i></span>
							<span class="app-name textOverflowEllipsis"> {vtranslate('LBL_EXTENSION_STORE', 'Settings:Vtiger')}</span>
						</div>
					</div>
				{/if}
			{/if}
			<div class="dropdown app-modules-dropdown-container dropdown-compact">
				{foreach item=APP_MENU_MODEL from=$APP_GROUPED_MENU.$APP_NAME}
					{assign var=FIRST_MENU_MODEL value=$APP_MENU_MODEL}
					{if $APP_MENU_MODEL}
						{break}
					{/if}
				{/foreach}
				<div class="menu-item app-item dropdown-toggle app-item-misc" data-app-name="TOOLS" id="TOOLS_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					<div class="menu-items-wrapper app-menu-items-wrapper">
						<span class="app-icon-list ti-more-alt"></span>
						<span class="app-name textOverflowEllipsis"> {vtranslate("LBL_MORE")}</span>
						<span class="ti-angle-right pull-right"></span>
					</div>
				</div>
				<ul class="dropdown-menu app-modules-dropdown dropdown-modules-compact" aria-labelledby="{$APP_NAME}_modules_dropdownMenu" data-height="0.34">
					{assign var=EMAILTEMPLATES_MODULE_MODEL value=Vtiger_Module_Model::getInstance('EmailTemplates')}
					
					<li class="visible-sm visible-xs app-item-misc">
								<span style="color:white">
									<span class="app-icon-list ti-more-alt"></span>
									<span class="app-name textOverflowEllipsis"> {vtranslate("LBL_MORE")}</span>
								</span>
					</li>
							
					{if $EMAILTEMPLATES_MODULE_MODEL && $USER_PRIVILEGES_MODEL->hasModulePermission($EMAILTEMPLATES_MODULE_MODEL->getId())}
						<li>
							<a href="{$EMAILTEMPLATES_MODULE_MODEL->getDefaultUrl()}">
								<span class="module-icon"><i class="fa fa-fast-forward" aria-hidden="true"></i></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate($EMAILTEMPLATES_MODULE_MODEL->getName(), $EMAILTEMPLATES_MODULE_MODEL->getName())}</span>
							</a>
						</li>
					{/if}
					{assign var=RECYCLEBIN_MODULE_MODEL value=Vtiger_Module_Model::getInstance('RecycleBin')}
					{if $RECYCLEBIN_MODULE_MODEL && $USER_PRIVILEGES_MODEL->hasModulePermission($RECYCLEBIN_MODULE_MODEL->getId())}
						<li>
							<a href="{$RECYCLEBIN_MODULE_MODEL->getDefaultUrl()}">
								<span class="module-icon"><i class="material-icons">delete_forever</i></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate('Recycle Bin')}</span>
							</a>
						</li>
					{/if}
					{assign var=RSS_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Rss')}
					{if $RSS_MODULE_MODEL && $USER_PRIVILEGES_MODEL->hasModulePermission($RSS_MODULE_MODEL->getId())}
						<li>
							<a href="index.php?module=Rss&view=List">
								<span class="module-icon"><i class="material-icons">rss_feed</i></span>
								<span class="module-name textOverflowEllipsis">{vtranslate($RSS_MODULE_MODEL->getName(), $RSS_MODULE_MODEL->getName())}</span>
							</a>
						</li>
					{/if}
					{assign var=PORTAL_MODULE_MODEL value=Vtiger_Module_Model::getInstance('Portal')}
					{if $PORTAL_MODULE_MODEL && $USER_PRIVILEGES_MODEL->hasModulePermission($PORTAL_MODULE_MODEL->getId())}
						<li>
							<a href="index.php?module=Portal&view=List">
								<span class="module-icon"><i class="material-icons">desktop_windows</i></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate('Portal')}</span>
							</a>
						</li>
					{/if}
				</ul>
			</div>
			{if $USER_MODEL->isAdminUser()}
				<div class="dropdown app-modules-dropdown-container dropdown-compact">
					<div class="menu-item app-item dropdown-toggle app-item-misc" data-app-name="SETTINGS" id="TOOLS_modules_dropdownMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-default-url="{if $USER_MODEL->isAdminUser()}index.php?module=Vtiger&parent=Settings&view=Index{else}index.php?module=Users&view=Settings{/if}">
						<div class="menu-items-wrapper app-menu-items-wrapper">
							<span class="app-icon-list"><i class="material-icons">settings</i></span>
							<span class="app-name textOverflowEllipsis"> {vtranslate('LBL_SETTINGS', 'Settings:Vtiger')}</span>
							{if $USER_MODEL->isAdminUser()}
								<span class="ti-angle-right pull-right"></span>
							{/if}
						</div>
					</div>
					<ul class="dropdown-menu app-modules-dropdown dropdown-modules-compact" aria-labelledby="{$APP_NAME}_modules_dropdownMenu" data-height="0.27">
						<li class="visible-sm visible-xs app-item-misc">
								<span style="color:white">
									<span class="app-icon-list"><i class="material-icons">settings</i></span>
									<span class="app-name textOverflowEllipsis"> {vtranslate('LBL_SETTINGS', 'Settings:Vtiger')}</span>
								</span>
						</li>
						<li>
							<a href="?module=Vtiger&parent=Settings&view=Index">
								<span class="module-icon"><i class="material-icons">settings</i></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate('LBL_CRM_SETTINGS','Vtiger')}</span>
							</a>
						</li>
						<li>
							<a href="?module=Users&parent=Settings&view=List">
								<span class="module-icon"><i class="material-icons">contacts</i></span>
								<span class="module-name textOverflowEllipsis"> {vtranslate('LBL_MANAGE_USERS','Vtiger')}</span>
							</a>
						</li>
					</ul>
				</div>
			{/if}
		</div>
	</div>
</div>
