{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<input type="hidden" name="is_record_creation_allowed" id="is_record_creation_allowed" value="{$IS_CREATE_PERMITTED}">
	<div class="col-sm-12 col-xs-12 module-action-bar clearfix">
		<div class="module-action-content clearfix coloredBorderTop">
			<div class="col-lg-5 col-md-5">
				<span>
					{assign var="VIEW_HEADER_LABEL" value="LBL_CALENDAR_VIEW"}
					{if $VIEW === 'SharedCalendar'}
						{assign var="VIEW_HEADER_LABEL" value="LBL_SHARED_CALENDAR"}
					{/if}
					<a href='javascript:void(0)'><h4 class="module-title pull-left"><span style="cursor: default;"> {strtoupper(vtranslate($VIEW_HEADER_LABEL, $MODULE))} </span></h4></a>
				</span>
			</div>
			<div class="col-lg-7 col-md-7 pull-right">
				<div id="appnav" class="navbar-right">
					<div class="btn-group">
						{if $IS_CREATE_PERMITTED}
							<button id="calendarview_basicaction_addevent" type="button" 
									class="btn addButton module-buttons cursorPointer" 
									onclick='Calendar_Calendar_Js.showCreateEventModal();'>
								<div aria-hidden="true"><i class="material-icons">add</i>
								<span class="hidden-sm hidden-xs">&nbsp;&nbsp;{vtranslate('LBL_ADD_EVENT', $MODULE)}</span></div>
							</button>
							{if $VIEW === 'SharedCalendar' || $VIEW == 'Calendar'}
								<button id="calendarview_basicaction_print" type="button" 
									class="btn  btn-default module-buttons cursorPointer" 
									onclick='window.print();'>
									{vtranslate('Print', $MODULE)}
								</button>
							{/if}
								{*<button id="calendarview_basicaction_addtask" type="button" 
										class="btn addButton module-buttons cursorPointer" 
										onclick='Calendar_Calendar_Js.showCreateTaskModal();'>
									<div aria-hidden="true"><i class="material-icons">add</i>
									<span class="hidden-sm hidden-xs">&nbsp;&nbsp;{vtranslate('LBL_ADD_TASK', $MODULE)}</span></div>
								</button>*}
						{/if}
						<button type="button" class="btn module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
							<span aria-hidden="true" title="{vtranslate('LBL_SETTINGS', $MODULE)}">
							<i class="material-icons">settings</i></span>
							<span class="hidden-sm hidden-xs">&nbsp;&nbsp;{vtranslate('LBL_CUSTOMIZE', 'Reports')}&nbsp;</span> <span class="caret"></span>
						</button>
						<ul class="detailViewSetting dropdown-menu animated fadeIn">
							{if $MODULE_SETTING_ACTIONS|@count gt 0}
								{foreach item=SETTING from=$MODULE_SETTING_ACTIONS}
									{if $SETTING->getLabel() eq 'LBL_EDIT_FIELDS'}
										<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_Events"><a href="{$SETTING->getUrl()}&sourceModule=Events">{vtranslate($SETTING->getLabel(), $MODULE_NAME,vtranslate('LBL_EVENTS',$MODULE_NAME))}</a></li>
										{*<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_Calendar"><a href="{$SETTING->getUrl()}&sourceModule=Calendar">{vtranslate($SETTING->getLabel(), $MODULE_NAME,vtranslate('LBL_TASKS','Calendar'))}</a></li>*}
									{else if $SETTING->getLabel() eq 'LBL_EDIT_WORKFLOWS'} 
										<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_WORKFLOWS"><a href="{$SETTING->getUrl()}&sourceModule=Events">{vtranslate('LBL_EVENTS', $MODULE_NAME)} {vtranslate('LBL_WORKFLOWS',$MODULE_NAME)}</a></li>	
										{*<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_WORKFLOWS"><a href="{$SETTING->getUrl()}&sourceModule=Calendar">{vtranslate('LBL_TASKS', 'Calendar')} {vtranslate('LBL_WORKFLOWS',$MODULE_NAME)}</a></li>*}
									{else}
										<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}"><a href={$SETTING->getUrl()}>{vtranslate($SETTING->getLabel(), $MODULE_NAME, vtranslate($MODULE_NAME, $MODULE_NAME))}</a></li>
									{/if}
								{/foreach}
							{/if}
							<li>
								<a>
									<span id="calendarview_basicaction_calendarsetting" onclick='Calendar_Calendar_Js.showCalendarSettings();' class="cursorPointer">
										{vtranslate('LBL_CALENDAR_SETTINGS', 'Calendar')}
									</span>
								</a>
							</li>
						</ul>
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