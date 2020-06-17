{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="col-sm-12 col-xs-12 module-action-bar clearfix coloredBorderTop">
		<div class="module-action-content clearfix {$MODULE}-module-action-content">
			<div class="col-lg-7 col-md-7 module-breadcrumb module-breadcrumb-{$smarty.request.view} transitionsAllHalfSecond">
				{assign var=MODULE_MODEL value=Vtiger_Module_Model::getInstance($MODULE)}
				{if $MODULE_MODEL->getDefaultViewName() neq 'List'}
					{assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getDefaultUrl()}
				{else}
					{assign var=DEFAULT_FILTER_ID value=$MODULE_MODEL->getDefaultCustomFilter()}
					{if $DEFAULT_FILTER_ID}
						{assign var=CVURL value="&viewname="|cat:$DEFAULT_FILTER_ID}
						{assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getListViewUrl()|cat:$CVURL}
					{else}
						{assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getListViewUrlWithAllFilter()}
					{/if}
				{/if}
				<a title="{vtranslate($MODULE, $MODULE)}" href='{$DEFAULT_FILTER_URL}&app={$SELECTED_MENU_CATEGORY}'><h4 class="module-title pull-left text-uppercase"> {vtranslate($MODULE, $MODULE)} </h4>&nbsp;&nbsp;</a>
				{if $smarty.session.lvs.$MODULE.viewname}
					{assign var=VIEWID value=$smarty.session.lvs.$MODULE.viewname}
				{/if}
				{if $VIEWID}
					{foreach item=FILTER_TYPES from=$CUSTOM_VIEWS}
						{foreach item=FILTERS from=$FILTER_TYPES}
							{if $FILTERS->get('cvid') eq $VIEWID}
								{assign var=CVNAME value=$FILTERS->get('viewname')}
								{assign var=CVEDITURL value=$FILTERS->getEditUrl()}
								{assign var=CVDELETE value=$FILTERS->isDeletable()}
								{assign var=CVEDIT value=$FILTERS->isEditable()}
								{assign var=CVDELETEURL value=$FILTERS->getDeleteUrl()}
								{assign var=IS_DEFAULT value=$FILTERS->isDefault()}
								{assign var=CVID value=$FILTERS->getId()}
								{assign var=CVDUPLICATEURL value=$FILTERS->getDuplicateUrl()}
								{assign var=CVTOGGLEURL value=$FILTERS->getToggleDefaultUrl()}
								{break}
							{/if}
						{/foreach}
					{/foreach}
					<p class="current-filter-name filter-name pull-left cursorPointer" title="{$CVNAME}">
						<span class="fa fa-angle-right pull-left" aria-hidden="true"></span>
						<a href='{$MODULE_MODEL->getListViewUrl()}&viewname={$VIEWID}&app={$SELECTED_MENU_CATEGORY}'>&nbsp;&nbsp;{$CVNAME}&nbsp;&nbsp;</a>
	                    {if $smarty.request.view eq 'List'}
		                    <div class="module-filters filter-name listActions ">
			                    <i style="margin-top:13px;" class="fa fa-angle-down dropdown-toggle" data-toggle="dropdown" aria-expanded="true"></i>
			                    <ul class="dropdown-menu popover" role="menu" style="left:auto;top:70%;padding: 3px;">
			                    	{if $CVEDIT || $CURRENT_USER_MODEL->isAdminUser()}
			                            <li role="presentation" class="editFilter" data-url="{$CVEDITURL}">
			                                <a role="menuitem">
			                                	<i class="fa fa-pencil"></i>&nbsp;{vtranslate('LBL_EDIT',$MODULE)}
			                            	</a>
			                            </li>
			                        {/if}
			                        {*if $CVDELETE || $CURRENT_USER_MODEL->isAdminUser()}
			                            <li role="presentation" class="deleteFilter" data-url="{$CVDELETEURL}">
			                            	<a role="menuitem">
			                            		<i class="fa fa-trash"></i>&nbsp;{vtranslate('LBL_DELETE',$MODULE)}
			                        		</a>
			                            </li>
			                        {/if*}
			                        {if $CVDUPLICATEURL}
			                            <li role="presentation" class="duplicateFilter" data-url="{$CVDUPLICATEURL}">
			                                <a role="menuitem" >
			                                	<i class="fa fa-files-o"></i>&nbsp;{vtranslate('LBL_DUPLICATE',$MODULE)}
			                            	</a>
			                            </li>
			                        {/if}    
			                        {if $CVTOGGLEURL}
			                            <li role="presentation" class="toggleDefault" data-url="{$CVTOGGLEURL}">
			                                <a role="menuitem" >
			                            		<i data-check-icon="fa-check-square-o" data-uncheck-icon="fa-square-o"{if $IS_DEFAULT} class="fa fa-check-square-o" {else} class="fa fa-square-o" {/if}></i>&nbsp;{vtranslate('LBL_DEFAULT',$MODULE)}
			                                </a>
			                            </li>
			                        {/if}    
			                    </ul>
		                    </div>
	                	{/if}   
                    </p>
				{/if}
				{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
				{if $RECORD and $smarty.request.view eq 'Edit'}
					<p class="current-filter-name filter-name pull-left "><span class="fa fa-angle-right pull-left" aria-hidden="true"></span><a title="{$RECORD->get('label')}">&nbsp;&nbsp;{vtranslate('LBL_EDITING', $MODULE)} : {$RECORD->get('label')} &nbsp;&nbsp;</a></p>
				{else if $smarty.request.view eq 'Edit'}
					<p class="current-filter-name filter-name pull-left "><span class="fa fa-angle-right pull-left" aria-hidden="true"></span><a>&nbsp;&nbsp;{vtranslate('LBL_ADDING_NEW', $MODULE)}&nbsp;&nbsp;</a></p>
				{/if}
				{if $smarty.request.view eq 'Detail'}
					<p class="current-filter-name filter-name pull-left"><span class="fa fa-angle-right pull-left" aria-hidden="true"></span><a title="{$RECORD->get('label')}">&nbsp;&nbsp;{$RECORD->get('label')} &nbsp;&nbsp;</a></p>
				{/if}
			</div>
			<div class="col-lg-5 col-md-5 pull-right">
				<div id="appnav" class="navbar-right">
					<ul class="nav navbar-nav">
						{foreach item=BASIC_ACTION from=$MODULE_BASIC_ACTIONS}
							{if $BASIC_ACTION->getLabel() == 'LBL_IMPORT'}
								<li>
									<button id="{$MODULE}_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($BASIC_ACTION->getLabel())}" type="button" class="btn addButton btn-default module-buttons" 
											{if stripos($BASIC_ACTION->getUrl(), 'javascript:')===0}  
												onclick='{$BASIC_ACTION->getUrl()|substr:strlen("javascript:")};'
											{else}
												onclick="Vtiger_Import_Js.triggerImportAction('{$BASIC_ACTION->getUrl()}')"
											{/if}>
										<div class="fa {$BASIC_ACTION->getIcon()}" aria-hidden="true"></div>&nbsp;&nbsp;
										{vtranslate($BASIC_ACTION->getLabel(), $MODULE)}
									</button>
								</li>
							{else}
								<li>
									<button id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($BASIC_ACTION->getLabel())}" type="button" class="btn addButton btn-default module-buttons" 
											{if stripos($BASIC_ACTION->getUrl(), 'javascript:')===0}  
												onclick='{$BASIC_ACTION->getUrl()|substr:strlen("javascript:")};'
											{else} 
												onclick='window.location.href = "{$BASIC_ACTION->getUrl()}&app={$SELECTED_MENU_CATEGORY}"'
											{/if}>
										<div class="fa {$BASIC_ACTION->getIcon()}" aria-hidden="true"></div>&nbsp;&nbsp;
										{vtranslate($BASIC_ACTION->getLabel(), $MODULE)}
									</button>
								</li>
							{/if}
						{/foreach}
						{if $MODULE_SETTING_ACTIONS|@count gt 0}
							<li>
								<div class="settingsIcon">
									<button type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false" title="{vtranslate('LBL_SETTINGS', $MODULE)}">
										<span class="fa fa-wrench" aria-hidden="true"></span>&nbsp;{vtranslate('LBL_CUSTOMIZE', 'Reports')}&nbsp; <span class="caret"></span>
									</button>
									<ul class="detailViewSetting dropdown-menu">
										{foreach item=SETTING from=$MODULE_SETTING_ACTIONS}
											<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}"><a href={$SETTING->getUrl()}>{vtranslate($SETTING->getLabel(), $MODULE_NAME ,vtranslate($MODULE_NAME, $MODULE_NAME))}</a></li>
										{/foreach}
									</ul>
								</div>
							</li>
						{/if}
						<li>
							<button href="javascript:void(0);" data-toggle="dropdown" class="btn btn-default" style="margin-left: 4px;margin-top: 5px;">
								{vtranslate('Configure Mail Converter', $QUALIFIED_MODULE_NAME)}&nbsp;<i class="caret"></i>
							</button>
							<ul class="dropdown-menu pull-right mailBoxDropDown">
								<li>
									<a href="#" data-scannerid="" class="addMailBox"> <i class="fa fa-plus"></i> Add Mailbox</a>
								</li>
								{foreach item=SCANNER from=$MAILBOXES}
									<li>
										<a href="#" data-scannerid="{$SCANNER['scannerid']}"> {$SCANNER['scannername']}
											<i class="fa fa-trash pull-right deleteMailBox" title="Delete MailBox" data-scannerid="{$SCANNER['scannerid']}"></i>&nbsp;
											<i class="fa fa-plus pull-right addMailConverter" title="Add Rules MailBox" data-scannerid="{$SCANNER['scannerid']}"></i>&nbsp;
											{*<i class="fa fa-folder pull-right addMailBox selectFolder" title="Select Folders" data-scannerid="{$SCANNER['scannerid']}"></i>&nbsp;*}
											<i class="fa fa-pencil pull-right addMailBox" title="Edit MailBox" data-scannerid="{$SCANNER['scannerid']}"></i>&nbsp;
											<i class="fa fa-refresh pull-right scanMailBox" title="Scan MailBox" data-scannerid="{$SCANNER['scannerid']}"></i>&nbsp;
										</a>
									</li>
								{/foreach}
							</ul>
							{*<button id="{$MODULE}_listView_basicAction_MailConverter" type="button" class="btn addMailConverter btn-default module-buttons"
								/*onclick='window.location.href = "index.php?module=MailConverter&parent=Settings&view=Edit&mode=step1&create=new&app={$SELECTED_MENU_CATEGORY}"'*/>
								<div class="fa fa-plus" aria-hidden="true"></div>&nbsp;&nbsp;
								{vtranslate('Configure Mailbox', $MODULE)}
							</button>*}
						</li>
					</ul>
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
