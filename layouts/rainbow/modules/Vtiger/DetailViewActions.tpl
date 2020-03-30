{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 detailViewButtoncontainer">
        <div class="pull-right">

            <!-- bread svv
            <div class="module-breadcrumb module-breadcrumb-{$smarty.request.view} transitionsAllHalfSecond">
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
                <a title="{vtranslate($MODULE, $MODULE)}" href='{$DEFAULT_FILTER_URL}&app={$SELECTED_MENU_CATEGORY}'><h4 class="module-title pull-left "> {vtranslate($MODULE, $MODULE)} </h4></a>
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
                    <p class="current-filter-name filter-name pull-left cursorPointer" title="{$CVNAME}"><span class="ti-angle-right pull-left" style="margin-left: 10px;" aria-hidden="true"></span><a href='{$MODULE_MODEL->getListViewUrl()}&viewname={$VIEWID}&app={$SELECTED_MENU_CATEGORY}'>&nbsp;&nbsp;{$CVNAME}&nbsp;&nbsp;</a> </p>
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
            bread svv -->
            </div>
            <div class="pull-right btn-toolbar">
             
                  <!-- svv <div class="btn-group pull-left">
                
                        {foreach item=BASIC_ACTION from=$MODULE_BASIC_ACTIONS}
                            {if $BASIC_ACTION->getLabel() == 'LBL_IMPORT'}
                                    <button tippytitle data-toggle="toolstip" data-placement="top" title="{vtranslate($BASIC_ACTION->getLabel(), $MODULE)}" id="{$MODULE}_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($BASIC_ACTION->getLabel())}" type="button" class="btn btn-primary addButton" 
                                            {if stripos($BASIC_ACTION->getUrl(), 'javascript:')===0}  
                                                onclick='{$BASIC_ACTION->getUrl()|substr:strlen("javascript:")};'
                                            {else}
                                                onclick="Vtiger_Import_Js.triggerImportAction('{$BASIC_ACTION->getUrl()}')"
                                            {/if}>
                                        <div aria-hidden="true"><i class="material-icons">import_export</i></div>
                                        
                                    </button>
                            {else}
                                    <button tippytitle data-toggle="toolstip" data-placement="top" title="{vtranslate($BASIC_ACTION->getLabel(), $MODULE)}" id="{$MODULE}_listView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($BASIC_ACTION->getLabel())}" type="button" class="btn btn-primary addButton" 
                                            {if stripos($BASIC_ACTION->getUrl(), 'javascript:')===0}  
                                                onclick='{$BASIC_ACTION->getUrl()|substr:strlen("javascript:")};'
                                            {else} 
                                                onclick='window.location.href = "{$BASIC_ACTION->getUrl()}&app={$SELECTED_MENU_CATEGORY}"'
                                            {/if}>
                                        <div aria-hidden="true"><i class="material-icons>{$BASIC_ACTION->getIcon()}</i></div>
                                    </button>
                            {/if}
                        {/foreach}
                        {if $MODULE_SETTING_ACTIONS|@count gt 0}
                                    <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" data-toggle="tooltisp" tippytitle data-placement="top" title="{vtranslate('LBL_CUSTOMIZE', 'Reports')}" aria-expanded="false">
                                        <span aria-hidden="true" title="{vtranslate('LBL_SETTINGS', $MODULE)}"><i class="material-icons">settings</i></span>&nbsp; <span class="caret"></span>
                                    </button>
                                    <ul class="detailViewSetting dropdown-menu pull-right animated fadeIn">
                                        {foreach item=SETTING from=$MODULE_SETTING_ACTIONS}
                                            <li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}"><a href={$SETTING->getUrl()}>{vtranslate($SETTING->getLabel(), $MODULE_NAME ,vtranslate($MODULE_NAME, $MODULE_NAME))}</a></li>
                                        {/foreach}
                                    </ul>
                        {/if}
                   </div>---->
		<div class="btn-group">
            {assign var=STARRED value=$RECORD->get('starred')}
            {if $MODULE_MODEL->isStarredEnabled()}
                <button class="btn btn-primary markStar {if $STARRED} active {/if}" id="starToggle">
                    <div class='starredStatus' title="{vtranslate('LBL_STARRED', $MODULE)}">
                        <div class='unfollowMessage'>
                            <i class="material-icons">star</i> &nbsp;{vtranslate('LBL_UNFOLLOW',$MODULE)}
                        </div>
                        <div class='followMessage'>
                            <i class="material-icons active">star_border</i> <span class="hidden-xs">{vtranslate('LBL_FOLLOWING',$MODULE)}</span>
                        </div>
                    </div>
                    <div class='unstarredStatus' title="{vtranslate('LBL_NOT_STARRED', $MODULE)}">
                        {vtranslate('LBL_FOLLOW',$MODULE)}
                    </div>

                </button>
            {/if}
           
            {foreach item=DETAIL_VIEW_BASIC_LINK from=$DETAILVIEW_LINKS['DETAILVIEWBASIC']}
                <button data-toggle="toosltip" data-placement="top" tippytitle data-tippy-content="{vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}" class="btn btn-primary" id="{$MODULE_NAME}_detailView_basicAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_BASIC_LINK->getLabel())}"
                        {if $DETAIL_VIEW_BASIC_LINK->isPageLoadLink()}
                            onclick="window.location.href = '{$DETAIL_VIEW_BASIC_LINK->getUrl()}&app={$SELECTED_MENU_CATEGORY}'"
                        {else}
                            onclick="{$DETAIL_VIEW_BASIC_LINK->getUrl()}"
                        {/if}
                        {if $MODULE_NAME eq 'Documents' && $DETAIL_VIEW_BASIC_LINK->getLabel() eq 'LBL_VIEW_FILE'}
                            data-filelocationtype="{$DETAIL_VIEW_BASIC_LINK->get('filelocationtype')}" data-filename="{$DETAIL_VIEW_BASIC_LINK->get('filename')}" >
                        <i class="material-icons">zoom_in</i>
                        {else if Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_BASIC_LINK->getLabel()) eq "LBL_EDIT"}>
                        	<i class="fa fa-pencil"></i> 
                        {else if Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_BASIC_LINK->getLabel()) eq "LBL_SEND_EMAIL"}>
                        	<i class="fa fa-envelope"></i>
						{else if $MODULE_NAME eq 'Project'}
						 	<span class="hidden-sm hidden-xs">{vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}</span>
						{else if $MODULE_NAME eq 'Leads'}
						 	<span class="hidden-sm hidden-xs">{vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}</span>
						{else if $MODULE_NAME eq 'Potentials'}
						 	<span class="hidden-sm hidden-xs">{vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}</span>
						{else} 
						 	<span class="hidden-sm hidden-xs">{vtranslate($DETAIL_VIEW_BASIC_LINK->getLabel(), $MODULE_NAME)}</span>
						{/if}

                
                </button>
            {/foreach}
            {if $DETAILVIEW_LINKS['DETAILVIEW']|@count gt 0}
                <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="javascript:void(0);">
                   <i class="material-icons">list</i> <span class="hidden-sm hidden-xs">{vtranslate('LBL_MORE', $MODULE_NAME)}</span> &nbsp;&nbsp;<i class="caret"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right animated fadeIn">
                    {foreach item=DETAIL_VIEW_LINK from=$DETAILVIEW_LINKS['DETAILVIEW']}
                        {if $DETAIL_VIEW_LINK->getLabel() eq ""} 
                            <li class="divider"></li>   
                            {else}
                            <li id="{$MODULE_NAME}_detailView_moreAction_{Vtiger_Util_Helper::replaceSpaceWithUnderScores($DETAIL_VIEW_LINK->getLabel())}">
                                {if $DETAIL_VIEW_LINK->getUrl()|strstr:"javascript"} 
                                    <a href='{$DETAIL_VIEW_LINK->getUrl()}'>{vtranslate($DETAIL_VIEW_LINK->getLabel(), $MODULE_NAME)}</a>
                                {else}
                                    <a href='{$DETAIL_VIEW_LINK->getUrl()}&app={$SELECTED_MENU_CATEGORY}' >{vtranslate($DETAIL_VIEW_LINK->getLabel(), $MODULE_NAME)}</a>
                                {/if}
                            </li>
                        {/if}
                    {/foreach}
                </ul>
            {/if}
            </div>
            {if !{$NO_PAGINATION}}
            <div class="button-group pull-right">
                <button class="btn btn-secondary" id="detailViewPreviousRecordButton" {if empty($PREVIOUS_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href = '{$PREVIOUS_RECORD_URL}&app={$SELECTED_MENU_CATEGORY}'" {/if} >
                    <i class="material-icons">keyboard_arrow_left</i>
                </button>
                <button class="btn btn-secondary " id="detailViewNextRecordButton"{if empty($NEXT_RECORD_URL)} disabled="disabled" {else} onclick="window.location.href = '{$NEXT_RECORD_URL}&app={$SELECTED_MENU_CATEGORY}'" {/if}>
                    <i class="material-icons">keyboard_arrow_right</i>
                </button>
            </div>
            {/if}        
        </div>
        <input type="hidden" name="record_id" value="{$RECORD->getId()}">

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
    {if $TIMECONTROL_ENABLE}
	    <div  class="col-lg-3 pull-right" style="margin-top:10px;">
			<div class="summaryWidgetContainer">
				<form action="index.php" method="post" name="QuickTimer{$MODULE_NAME}">
					<input type="hidden" name="module" value="Timecontrol">
					<input type="hidden" name="record" value="{$TIMECONTROLID}">
					<input type="hidden" name="modeTC" value="continueTimetracker">
					<input type="hidden" name="mode" value="edit">
					<input type="hidden" name="ticketId" value="{$RECORD->getId()}">
					{if $SHOW_WATCH eq 'started'}<input type="hidden" name="action" value="Finish">{/if}
					{if $SHOW_WATCH eq 'halted'}
					<input type="hidden" name="isDuplicate" value="true">
					<input type="hidden" name="view" value="Edit">
					{*<input type="hidden" name="action" value="Save">*}
					{/if}
					<input type="hidden" name="stop_watch" value="{if $SHOW_WATCH eq 'halted'}0{else}1{/if}">
					
					<div class="widget_header clearfix">
						<div class="pull-right">
							<button class="btn pull-right btn-success ticketHalted startTimer {if $SHOW_WATCH neq 'halted'}hide{/if}" type="submit">
								<strong>{vtranslate('Start','Timecontrol')}</strong>
							</button>
	                        <button class="btn pull-right btn-danger ticketStart {if $SHOW_WATCH eq 'halted'}hide{/if} stopTimer" type="submit">
	                            <strong>{vtranslate('LBL_WATCH_STOP','Timecontrol')}</strong>
	                        </button>
						</div>
						<div class="pull-right" style="font-size: 150%;font-weight: bold;margin-right: 10px;">
							<div class="ticketHalted {if $SHOW_WATCH neq 'halted'}hide{/if}">
								<img src="modules/Timecontrol/images/clock-red-small.png" id="clock_image" style="vertical-align: middle; margin-right:5px; width:30px;">
								<span id="clock_display" style="vertical-align: middle">00:00</span>
							</div>
							<div class="ticketStart {if $SHOW_WATCH neq 'started'}hide{/if}">
								<img src="modules/Timecontrol/images/clock-green-small.png" id="clock_image" style="vertical-align: middle; margin-right:5px; width:30px;">
								<span id="clock_display_hours" style="vertical-align: middle;">00</span><span id="clock_display_separator" style="vertical-align: middle;">:</span><span id="clock_display_minutes" style="vertical-align: middle;">00</span>
								<span id="clock_display_separator2" style="vertical-align: middle;">:</span><span id="clock_display_seconds" style="vertical-align: middle;">00</span>
								<input id="clock_counter" type="hidden" value="{$WATCH_COUNTER}">
								<script type="text/javascript">
								var tc_clock;
								$(document).ready(function() {ldelim}
									clearInterval(tc_clock);
									updateClock(true);
									tc_clock = setInterval("updateClock()", 1000);
								{rdelim});
								</script>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	{/if}
{strip}
