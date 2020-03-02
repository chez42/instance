{*<!--
/*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
-->*}

{strip}
	<div class="col-sm-12 col-xs-12 module-action-bar clearfix coloredBorderTop">
		<div class="module-action-content clearfix">
			<div class="col-lg-7 col-md-7">
				{if $USER_MODEL->isAdminUser()}
					<a title="{vtranslate('Home', $MODULE)}" href='index.php?module=Vtiger&parent=Settings&view=Index'>
						<h4 class="module-title pull-left text-uppercase">{vtranslate('LBL_HOME', $MODULE)} </h4>
					</a>
					&nbsp;<span class="ti-angle-right pull-left {if $VIEW eq 'Index' && $MODULE eq 'Vtiger'} hide {/if}" aria-hidden="true" style="padding-top: 12px;padding-left: 5px;"></span>
				{/if}
				{if $MODULE neq 'Vtiger' or $smarty.request.view neq 'Index'}
					{if $ACTIVE_BLOCK['block']}
						<span class="current-filter-name filter-name pull-left">
							{vtranslate($ACTIVE_BLOCK['block'], $QUALIFIED_MODULE)}&nbsp;
							<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;
						</span>
					{/if}
					{if $MODULE neq 'Vtiger'}
						{assign var=ALLOWED_MODULES value=","|explode:'Users,Profiles,Groups,Roles,Webforms,Workflows'}
						{if $MODULE_MODEL and $MODULE|in_array:$ALLOWED_MODULES}
							{if $MODULE eq 'Webforms'}
								{assign var=URL value=$MODULE_MODEL->getListViewUrl()}
							{else}
								{assign var=URL value=$MODULE_MODEL->getDefaultUrl()}
							{/if}
							{if $URL|strpos:'parent' eq ''}
								{assign var=URL value=$URL|cat:'&parent='|cat:$smarty.request.parent}
							{/if}
						{/if}
						<span class="current-filter-name settingModuleName filter-name pull-left">	
							{if $smarty.request.view eq 'Calendar'}
								{if $smarty.request.mode eq 'Edit'}
									<a href="{"index.php?module="|cat:$smarty.request.module|cat:'&parent='|cat:$smarty.request.parent|cat:'&view='|cat:$smarty.request.view}">
										{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}
									</a>&nbsp;
									<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;
									{vtranslate('LBL_EDITING', $MODULE)} :&nbsp;{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}&nbsp;{vtranslate('LBL_OF',$QUALIFIED_MODULE)}&nbsp;{$USER_MODEL->getName()}
								{else}
									{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}&nbsp;<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;{$USER_MODEL->getName()}
								{/if}
							{else if $smarty.request.view neq 'List' and $smarty.request.module eq 'Users'}
								{if $smarty.request.view eq 'PreferenceEdit'}
									<a href="{"index.php?module="|cat:$smarty.request.module|cat:'&parent='|cat:$smarty.request.parent|cat:'&view=PreferenceDetail&record='|cat:$smarty.request.record}">
										{vtranslate($ACTIVE_BLOCK['block'], $QUALIFIED_MODULE)}&nbsp;
									</a>
									<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;
									{vtranslate('LBL_EDITING', $MODULE)} :&nbsp;{$USER_MODEL->getName()}
								{else if $smarty.request.view eq 'Edit' or $smarty.request.view eq 'Detail'}
									<a href="{$URL}">
									{if $smarty.request.extensionModule}{$smarty.request.extensionModule}{else}{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}{/if}&nbsp;
									</a>
									<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;
									{if $smarty.request.view eq 'Edit'}
										{if $RECORD}
											{vtranslate('LBL_EDITING', $MODULE)} :&nbsp;{$RECORD->getName()}
										{else}
											{vtranslate('LBL_ADDING_NEW', $MODULE)}
										{/if}
									{else}
										{$RECORD->getName()}
									{/if}
								{else}
									{$USER_MODEL->getName()}
								{/if}
							{else if $URL and $URL|strpos:$smarty.request.view eq ''}
								<a href="{$URL}">
								{if $smarty.request.extensionModule}
									{$smarty.request.extensionModule}
								{else}
									{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}
								{/if}
								</a>&nbsp;
								<span class="ti-angle-right" aria-hidden="true"></span>&nbsp;
								{if $RECORD}
									{if $smarty.request.view eq 'Edit'}
										{vtranslate('LBL_EDITING', $MODULE)} :&nbsp;
									{/if}
									{$RECORD->getName()}
								{/if}
							{else}
								&nbsp;{if $smarty.request.extensionModule}{$smarty.request.extensionModule}{else}{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}{/if}
							{/if}
						</span>
					{else}
						{if $smarty.request.view eq 'TaxIndex'}
							{assign var=SELECTED_MODULE value='LBL_TAX_MANAGEMENT'}
						{elseif $smarty.request.view eq 'TermsAndConditionsEdit'}
							{assign var=SELECTED_MODULE value='LBL_TERMS_AND_CONDITIONS'}
						{else}
							{assign var=SELECTED_MODULE value=$ACTIVE_BLOCK['menu']}
						{/if}
						<span class="current-filter-name filter-name pull-left" style='width:50%;'><span class="display-inline-block">{vtranslate({$PAGETITLE}, $QUALIFIED_MODULE)}</span></span>
					{/if}
				{/if}
			</div>
			<div class="col-lg-5 col-md-5 pull-right">
				<div id="appnav" class="navbar-right">
					<div class="btn-group">
						{if $smarty.request.view eq 'List' and $smarty.request.module eq 'Users'}
							<div class="btn-group listViewMassActions " role="group">
								<button type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown">
									<img class="filterImage" src="layouts/v7/skins/images/filter.png" style="height: 13px; margin-right: 2px; vertical-align: middle;">
									<span id="selected"> {vtranslate('LBL_MORE','Vtiger')}</span>&nbsp;<span class="caret"></span>
								</button>
								<ul class="dropdown-menu filter-menu" style="min-width:250px;">
									<div class="module-filters" id="module-filters">
										<div class="sidebar-container lists-menu-container">
											<div class="sidebar-header clearfix">
												<h5 class="pull-left">{vtranslate('LBL_LISTS',$MODULE)}</h5>
												<button id="createFilter" data-url="{CustomView_Record_Model::getCreateViewUrl($MODULE)}" class="btn btn-sm btn-info pull-right sidebar-btn" title="{vtranslate('LBL_CREATE_LIST',$MODULE)}">
													<div class="ti-plus" aria-hidden="true"></div>
												</button> 
											</div>
											<hr>
											<div>
												<input class="search-list"  type="hidden" placeholder="{vtranslate('LBL_SEARCH_FOR_LIST',$MODULE)}">
											</div>
											<div class="menu-scroller scrollContainer" style="position:relative; top:0; left:0;height: 450px;">
												<div class="list-menu-content">
													{assign var="CUSTOM_VIEW_NAMES" value=array()}
													{if $CUSTOM_VIEWS && count($CUSTOM_VIEWS) > 0}
														{foreach key=GROUP_LABEL item=GROUP_CUSTOM_VIEWS from=$CUSTOM_VIEWS}
														{if $GROUP_LABEL neq 'Mine' && $GROUP_LABEL neq 'Shared'}
															{continue}
														 {/if}
														<div class="list-group" id="{if $GROUP_LABEL eq 'Mine'}myList{else}sharedList{/if}">   
															<h6 class="lists-header {if count($GROUP_CUSTOM_VIEWS) <=0} hide {/if}" >
																{if $GROUP_LABEL eq 'Mine'}
																	{vtranslate('LBL_MY_LIST',$MODULE)}
																{else}
																	{vtranslate('LBL_SHARED_LIST',$MODULE)}
																{/if}
															</h6>
															<input type="hidden" name="allCvId" value="{CustomView_Record_Model::getAllFilterByModule($MODULE)->get('cvid')}" />
															<ul class="lists-menu" style="list-style: none;">
															{assign var=count value=0}
															{assign var=MODULE_MODEL value=Vtiger_Module_Model::getInstance($MODULE)}
															{assign var=LISTVIEW_URL value=$MODULE_MODEL->getListViewUrl()}
															{foreach item="CUSTOM_VIEW" from=$GROUP_CUSTOM_VIEWS name="customView"}
																{assign var=IS_DEFAULT value=$CUSTOM_VIEW->isDefault()}
																{assign var="CUSTOME_VIEW_RECORD_MODEL" value=CustomView_Record_Model::getInstanceById($CUSTOM_VIEW->getId())}
																{assign var="MEMBERS" value=$CUSTOME_VIEW_RECORD_MODEL->getMembers()}
																{assign var="LIST_STATUS" value=$CUSTOME_VIEW_RECORD_MODEL->get('status')}
																{foreach key=GROUP_LABEL item="MEMBER_LIST" from=$MEMBERS}
																	{if $MEMBER_LIST|@count gt 0}
																	{assign var="SHARED_MEMBER_COUNT" value=1}
																	{/if}
																{/foreach}
																<li style="font-size:12px;" class='listViewFilter {if $VIEWID eq $CUSTOM_VIEW->getId() && ($CURRENT_TAG eq '')} active {if $smarty.foreach.customView.iteration gt 10} {assign var=count value=1} {/if} {else if $smarty.foreach.customView.iteration gt 10} filterHidden hide{/if} '> 
																	{assign var=VIEWNAME value={vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}}
																	{append var="CUSTOM_VIEW_NAMES" value=$VIEWNAME}
																	 <a class="filterName listViewFilterElipsis" href="{$LISTVIEW_URL|cat:'&viewname='|cat:$CUSTOM_VIEW->getId()|cat:'&app='|cat:$SELECTED_MENU_CATEGORY}" oncontextmenu="return false;" data-filter-id="{$CUSTOM_VIEW->getId()}" title="{$VIEWNAME|@escape:'html'}">{$VIEWNAME|@escape:'html'}</a> 
																		<div class="pull-right">
																			<span class="js-popover-container" style="cursor:pointer;">
																				<span  class="fa fa-angle-down" rel="popover" data-toggle="popover" aria-expanded="true" 
																					{if $CUSTOM_VIEW->isMine() and $CUSTOM_VIEW->get('viewname') neq 'All'}
																						data-deletable="{if $CUSTOM_VIEW->isDeletable() and $CUSTOM_VIEW->get('viewname') neq 'All'}true{else}false{/if}" 
																						data-editable="{if $CUSTOM_VIEW->isEditable() }true{else}false{/if}" 
																						{if $CUSTOM_VIEW->isEditable() || $CURRENT_USER_MODEL->isAdminUser()} 
																						data-editurl="{$CUSTOM_VIEW->getEditUrl()}{/if}" 
																						{if $CUSTOM_VIEW->isDeletable()}
																						{if $SHARED_MEMBER_COUNT eq 1 or $LIST_STATUS eq 3} data-shared="1"{/if} 
																						data-deleteurl="{$CUSTOM_VIEW->getDeleteUrl()}"{/if}
																					   {/if}
																					  toggleClass="fa {if $IS_DEFAULT}fa-check-square-o{else}fa-square-o{/if}" 
																					  data-filter-id="{$CUSTOM_VIEW->getId()}" 
																					  data-is-default="{$IS_DEFAULT}" data-defaulttoggle="{$CUSTOM_VIEW->getToggleDefaultUrl()}" 
																					  data-default="{$CUSTOM_VIEW->getDuplicateUrl()}" 
																					  data-isMine="{if $CUSTOM_VIEW->isMine() && ($CUSTOM_VIEW->get('viewname') neq 'LBL_ACTIVE_USERS' && $CUSTOM_VIEW->get('viewname') neq 'LBL_INACTIVE_USERS')}true{else}false{/if}">
																				</span>
																				 </span>
																			</div>
																		</li>
																	{/foreach}
																</ul>
															
														 </div>
														{/foreach}
															
														<input type="hidden" id='allFilterNames'  value='{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($CUSTOM_VIEWS_NAMES))}'/>
														<div id="filterActionPopoverHtml">
															<ul class="listmenu hide" role="menu">
																<li role="presentation" class="editFilter">
																		<a role="menuitem"><i class="fa fa-pencil"></i>&nbsp;{vtranslate('LBL_EDIT',$MODULE)}</a>
																	</li>
																<li role="presentation" class="deleteFilter">
																		<a role="menuitem"><i class="fa fa-trash"></i>&nbsp;{vtranslate('LBL_DELETE',$MODULE)}</a>
																</li>
																<li role="presentation" class="duplicateFilter">
																			<a role="menuitem" ><i class="fa fa-files-o"></i>&nbsp;{vtranslate('LBL_DUPLICATE',$MODULE)}</a>
																		</li>
																<li role="presentation" class="toggleDefault">
																			<a role="menuitem" >
																		<i data-check-icon="fa-check-square-o" data-uncheck-icon="fa-square-o"></i>&nbsp;{vtranslate('LBL_DEFAULT',$MODULE)}
																			</a>
																		</li>
																	</ul>
														</div>
							
													{/if}
													<div class="list-group hide noLists">
														<h6 class="lists-header"><center> {vtranslate('LBL_NO')} {vtranslate('LBL_LISTS')} {vtranslate('LBL_FOUND')} ... </center></h6>
													</div>
												</div>
										   </div> 
										</div>
									</div>    
								</ul>
						   </div> 
						{/if}
						{foreach item=BASIC_ACTION from=$MODULE_BASIC_ACTIONS}
								{if $BASIC_ACTION->getLabel() == 'LBL_IMPORT'}
									<button id="{$MODULE}_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($BASIC_ACTION->getLabel())}" type="button" class="btn addButton module-buttons" 
										{if stripos($BASIC_ACTION->getUrl(), 'javascript:')===0}
											onclick='{$BASIC_ACTION->getUrl()|substr:strlen("javascript:")};'
										{else} 
											onclick="Vtiger_Import_Js.triggerImportAction('{$BASIC_ACTION->getUrl()}')"
										{/if}>
										<div class="fa {$BASIC_ACTION->getIcon()}" aria-hidden="true"></div>&nbsp;&nbsp;
										{vtranslate($BASIC_ACTION->getLabel(), $MODULE)}
									</button>
								{else}
									<button type="button" class="btn addButton module-buttons" 
										id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($BASIC_ACTION->getLabel())}"
										{if stripos($BASIC_ACTION->getUrl(), 'javascript:')===0}
											onclick='{$BASIC_ACTION->getUrl()|substr:strlen("javascript:")};'
										{else} 
											onclick='window.location.href="{$BASIC_ACTION->getUrl()}"'
										{/if}>
										<div class="fa {$BASIC_ACTION->getIcon()}" aria-hidden="true"></div>
										&nbsp;&nbsp;{vtranslate($BASIC_ACTION->getLabel(), $MODULE)}
									</button>
								{/if}
						{/foreach}
						{if $LISTVIEW_LINKS['LISTVIEWSETTING']|@count gt 0}
							{if empty($QUALIFIEDMODULE)} 
								{assign var=QUALIFIEDMODULE value=$MODULE}
							{/if}
								<div class="settingsIcon">
									<button type="button" class="btn module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
										<span class="ti-settings" aria-hidden="true" title="{vtranslate('LBL_SETTINGS', $MODULE)}"></span>&nbsp; <span class="caret"></span>
									</button>
									<ul class="detailViewSetting dropdown-menu animated flipInY">
										{foreach item=SETTING from=$LISTVIEW_LINKS['LISTVIEWSETTING']}
											<li id="{$MODULE}_setings_lisview_advancedAction_{$SETTING->getLabel()}"><a href="javascript:void(0);" onclick="{$SETTING->getUrl()};">{vtranslate($SETTING->getLabel(), $QUALIFIEDMODULE)}</a></li>
											{/foreach}
									</ul>
								</div>
						{/if}

						{assign var=RESTRICTED_MODULE_LIST value=['Users', 'EmailTemplates']}
						{if $LISTVIEW_LINKS['LISTVIEWBASIC']|@count gt 0 and !in_array($MODULE, $RESTRICTED_MODULE_LIST)}
							{if empty($QUALIFIED_MODULE)} 
								{assign var=QUALIFIED_MODULE value='Settings:'|cat:$MODULE}
							{/if}
							{foreach item=LISTVIEW_BASICACTION from=$LISTVIEW_LINKS['LISTVIEWBASIC']}
								{if $MODULE eq 'Users'} {assign var=LANGMODULE value=$MODULE} {/if}
									<button class="btn module-buttons"
										id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($LISTVIEW_BASICACTION->getLabel())}" 
										{if $MODULE eq 'Workflows'}
											onclick='Settings_Workflows_List_Js.triggerCreate("{$LISTVIEW_BASICACTION->getUrl()}&mode=V7Edit")'
										{else}
											{if stripos($LISTVIEW_BASICACTION->getUrl(), 'javascript:')===0}
												onclick='{$LISTVIEW_BASICACTION->getUrl()|substr:strlen("javascript:")};'
											{else}
												onclick='window.location.href = "{$LISTVIEW_BASICACTION->getUrl()}"'
											{/if}
										{/if}>
										{if $MODULE eq 'Tags'}
											<i class="ti-plus"></i>&nbsp;&nbsp;
											{vtranslate('LBL_ADD_TAG', $QUALIFIED_MODULE)}
										{else}
											{if $LISTVIEW_BASICACTION->getIcon()}
												<i class="{$LISTVIEW_BASICACTION->getIcon()}"></i>&nbsp;&nbsp;
											{/if}
											{vtranslate($LISTVIEW_BASICACTION->getLabel(), $QUALIFIED_MODULE)}
										{/if}
									</button>
							{/foreach}
						{/if}
					</div>
				</div>
			</div>
		</div>
		{if $FIELDS_INFO neq null}
			<script type="text/javascript">
				var uimeta = (function () {
					var fieldInfo = {$FIELDS_INFO};
					return {
						field: {
							get: function (name, property) {
								if (name && property === undefined) {
									return fieldInfo[name];
								}
								if (name && property) {
									return fieldInfo[name][property]
								}
							},
							isMandatory: function (name) {
								if (fieldInfo[name]) {
									return fieldInfo[name].mandatory;
								}
								return false;
							},
							getType: function (name) {
								if (fieldInfo[name]) {
									return fieldInfo[name].type
								}
								return false;
							}
						},
					};
				})();
			</script>
		{/if}
	</div>
{/strip}
