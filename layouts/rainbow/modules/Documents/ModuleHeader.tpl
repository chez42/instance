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
			<div class="col-xs-12 col-lg-7 col-md-7 col-sm-7 module-breadcrumb module-breadcrumb-{$smarty.request.view}">
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
				<a title="{vtranslate($MODULE, $MODULE)}" href='{$DEFAULT_FILTER_URL}&app={$SELECTED_MENU_CATEGORY}'><h4 class="module-title pull-left textOverflowEllipsis text-uppercase"> {vtranslate($MODULE, $MODULE)} </h4>&nbsp;&nbsp;</a>
				{if $smarty.session.lvs.$MODULE.viewname}
					{assign var=VIEWID value=$smarty.session.lvs.$MODULE.viewname}
				{/if}
				{if $VIEWID}
					{foreach item=FILTER_TYPES from=$CUSTOM_VIEWS}
						{foreach item=FILTERS from=$FILTER_TYPES}
							{if $FILTERS->get('cvid') eq $VIEWID}
								{assign var=CVNAME value=$FILTERS->get('viewname')}
								{break}
							{/if}
						{/foreach}
					{/foreach}
					<p  class="current-filter-name filter-name pull-left cursorPointer" title="{$CVNAME}"><span class="ti-angle-right pull-left" aria-hidden="true"></span><a  href='{$MODULE_MODEL->getListViewUrl()}&viewname={$VIEWID}'>&nbsp;&nbsp;{$CVNAME}&nbsp;&nbsp;</a> </p>
				{/if}
				{assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
				{if $RECORD and $smarty.request.view eq 'Edit'}
					<p class="current-filter-name filter-name pull-left "><span class="ti-angle-right pull-left" aria-hidden="true"></span><a title="{$RECORD->get('label')}">&nbsp;&nbsp;{vtranslate('LBL_EDITING', $MODULE)} : {$RECORD->get('label')} &nbsp;&nbsp;</a></p>
				{else if $smarty.request.view eq 'Edit'}
					<p class="current-filter-name filter-name pull-left "><span class="ti-angle-right pull-left" aria-hidden="true"></span><a>&nbsp;&nbsp;{vtranslate('LBL_ADDING_NEW', $MODULE)}&nbsp;&nbsp;</a></p>
				{/if}
				{if $smarty.request.view eq 'Detail'}
					<p class="current-filter-name filter-name pull-left"><span class="ti-angle-right pull-left" aria-hidden="true"></span><a title="{$RECORD->get('label')}">&nbsp;&nbsp;{$RECORD->get('label')} &nbsp;&nbsp;</a></p>
				{/if}
			</div>
			<div class="col-xs-12 col-lg-5 col-md-5 col-sm-5 module-breadcrumb-List">
				<div id="appnav" class="navbar-right">
					<ul class="nav navbar-nav">
						<li class="pull-right">
							{if $MODULE_SETTING_ACTIONS|@count gt 0}
										<button type="button" class="btn module-buttons dropdown-toggle" data-toggle="dropdown">
											<span aria-hidden="true" title="{vtranslate('LBL_SETTINGS', $MODULE)}"><i class="material-icons">settings</i></span>&nbsp;<span class="hidden-sm hidden-xs">{vtranslate('LBL_CUSTOMIZE', 'Reports')}</span>&nbsp; <span class="caret"></span>
										</button>
										<ul class="detailViewSetting dropdown-menu">
											{foreach item=SETTING from=$MODULE_SETTING_ACTIONS}
												{if {vtranslate($SETTING->getLabel())} eq "%s Numbering"}
													<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}"><a href={$SETTING->getUrl()}>{vtranslate($SETTING->getLabel(), $MODULE_NAME ,vtranslate($MODULE_NAME, $MODULE_NAME))}</a></li>
												{else}
													<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}"><a href={$SETTING->getUrl()}>{vtranslate($SETTING->getLabel(), $MODULE_NAME, vtranslate("SINGLE_$MODULE_NAME", $MODULE_NAME))}</a></li>
												{/if}
											{/foreach}
										</ul>
							{/if}
						</li>
						<li class="pull-right">
							{foreach item=BASIC_ACTION from=$MODULE_BASIC_ACTIONS}
								{if $BASIC_ACTION->getLabel() eq 'LBL_ADD_RECORD'}
											<button type="button" class="btn  module-buttons dropdown-toggle" data-toggle="dropdown">
												<span title="{vtranslate('LBL_NEW_DOCUMENT', $MODULE)}"><i class="material-icons">add</i></span>&nbsp;<span class="hidden-sm hidden-xs">{vtranslate('LBL_NEW_DOCUMENT', $MODULE)}</span> &nbsp; <span class="caret"></span>
											</button>
											<ul class="detailViewSetting dropdown-menu animated fadeIn">
												<li class="dropdown-header"><i class="material-icons">file_upload</i> {vtranslate('LBL_FILE_UPLOAD', $MODULE)}</li>
												<li id="VtigerAction">
													<a href="javascript:Documents_Index_Js.uploadTo('Vtiger')">
														<i class="fa fa-desktop"> </i>  {vtranslate('LBL_FROM_COMPUTER', 'Documents' )}
													</a>
												</li>
												<li role="separator" class="divider"></li>
												<li class="dropdown-header"><i class="material-icons">link</i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', $MODULE)}</li>
												<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E')">&nbsp;<i class="material-icons">link</i>&nbsp; {vtranslate('LBL_FROM_SERVICE', $MODULE_NAME, {vtranslate('LBL_FILE_URL', $MODULE_NAME)})}</a></li>
												<li role="separator" class="divider"></li>
												<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W')"><i class="material-icons">file_upload</i> {vtranslate('LBL_CREATE_NEW', $MODULE_NAME, {vtranslate('SINGLE_Documents', $MODULE_NAME)})}</a></li>
											</ul>
								{/if}
							{/foreach}
						</li>
						{if $smarty.request.view eq 'List' || $smarty.request.view eq 'FolderView'}
							<li class="pull-right">
								<div>
									<button type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown">
										{if $smarty.request.view eq 'List'}
											<span class="fa fa-list" title="{vtranslate('List View', $MODULE)}"></span>&nbsp;&nbsp;
											{vtranslate('List View', $MODULE)}
										{else if $smarty.request.view eq 'FolderView'}	
											<span class="fa fa-folder" title="{vtranslate('Folder View', $MODULE)}"></span>&nbsp;&nbsp;
											{vtranslate('Folder View', $MODULE)}
										{/if}	
										&nbsp;<span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										{if $smarty.request.view eq 'List'}
											<li>
												<a href="index.php?module=Documents&view=FolderView">
													<span class="fa fa-folder"></span>&nbsp;&nbsp;{vtranslate('Folder View', 'Documents' )}
												</a>
											</li>
										{else if $smarty.request.view eq 'FolderView'}	
											<li>
												<a href="index.php?module=Documents&view=List">
													<span class="fa fa-list"></span>&nbsp;&nbsp;{vtranslate('List View', 'Documents' )}
												</a>
											</li>
										{/if}
									</ul>
								</div>
							</li>
						{/if}
					</ul>
				</div>
			</div>
		</div>
		{if $FIELDS_INFO neq null}
			<script type="text/javascript">
				var uimeta = (function() {
					var fieldInfo  = {$FIELDS_INFO};
					return {
						field: {
							get: function(name, property) {
								if(name && property === undefined) {
									return fieldInfo[name];
								}
								if(name && property) {
									return fieldInfo[name][property]
								}
							},
							isMandatory : function(name){
								if(fieldInfo[name]) {
									return fieldInfo[name].mandatory;
								}
								return false;
							},
							getType : function(name){
								if(fieldInfo[name]) {
									return fieldInfo[name].type
								}
								return false;
							}
						}
					};
				})();
			</script>
		{/if}
	</div>
{/strip}
