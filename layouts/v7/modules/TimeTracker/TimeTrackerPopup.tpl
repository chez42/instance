{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Time Tracker ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
<style>

    .datetimepicker.datetimepicker-dropdown-bottom-right.dropdown-menu{
        min-width: 240px;
    }
    .datetimepicker.datetimepicker-dropdown-bottom-right.dropdown-menu table.table-condensed{
        width: 100%;
    }
    .datetimepicker td, .datetimepicker th{
        width: 32px;
    }
    .grayBgProp{
        background-color: #f5f5f5 !important;
        background-image: none !important;
        border: 1px solid #ddd !important;
    }
    .input-time-tracker-popup{
        margin-bottom: 7px;
    }
    .form-control{
        height: 25px;
        padding: 2px 8px;
    }

    #listActiveTimers{
        margin-bottom: 0 !important;
    }

    fieldset.hour span.hour.hour_am,
    fieldset.hour span.hour.hour_pm,
    fieldset.minute span.minute{
        text-align: center;
    }
</style>
{strip}
    <div style="width: 300px;">
        <link rel="stylesheet" href="layouts/v7/modules/TimeTracker/css/bootstrap-datetimepicker.min.css" type="text/css" media="screen" />
        <script type="text/javascript" src="layouts/v7/modules/TimeTracker/resources/bootstrap-datetimepicker.min.js"></script>
        <script type="text/javascript" src="layouts/v7/modules/TimeTracker/resources/eventPause.min.js"></script>
        <div class="modelContainer" id="" style="min-height:580px;">
            <div class="modal-header contentsBackground" style="text-align: center;">
                <h5>
                    <input type="hidden" id="recordName" value="{if $RECORD_RUNNING_NAME} {$RECORD_RUNNING_NAME} {else} {$RECORD_MODEL->getName()} {/if}">
                    <a href="{if $RECORD_RUNNING}{$RECORD_RUNNING['detailUrl']}{else}{$RECORD_MODEL->getDetailViewUrl()}{/if}" id="header_popup">
                        {if $RECORD_RUNNING}
                            {vtranslate('LBL_RUNNING_FOR',$QUALIFIED_MODULE)}
                            {if $RECORD_RUNNING_NAME}
                                {$RECORD_RUNNING_NAME}
                            {else}
                                {$RECORD_RUNNING['name']}
                            {/if}
                        {else}
                            {$RECORD_MODEL->getName()}
                        {/if}
                    </a>
                </h5>
            </div>
            <form class="form-horizontal timeTrackerForm" name="TrackerForm" method="post" action="index.php">
                <input type="hidden" name="form_data[unique_id]" value="{$UNIQUE_ID}"/>
                <input type="hidden" name="parentId" value="{$RECORD_ID}"/>
                <input type="hidden" id="dateFormat" value="{$USER_MODEL->get('date_format')}"/>
                <input type="hidden" id="timeFormat" value="{$USER_MODEL->get('hour_format')}"/>
                <input type="hidden" name="form_data[module]" value="{$RECORD_MODEL->getModuleName()}"/>
                <input type="hidden" id="user_time" value="{$USER_TIME}">
                <div class="quickCreateContent">
                    <div class="modal-body">
                        {assign var=FIELD_SETTINGS value=$SETTINGS['field_settings']}
                        <table style="margin: 0 auto;">
                            {*Config fields*}
                            {foreach from=$FIELD_SETTINGS key=FIELDNAME item=FIELDINFO}
                                {if $FIELDINFO['visible'] and $FIELDNAME neq 'module'}
                                    <tr>
                                        <td class="fieldValue medium" >
                                            {assign var="FIELD_MODEL" value=$EVENT_MODULE_MODEL->getField($FIELDNAME)}
                                            {if $FIELDNAME eq 'subject'}
                                                <div class="form-group input-time-tracker-popup">
                                                    <input type="text" class="form-control fieldInput propFieldInput" id="{$FIELDNAME}" placeholder="Subject" name="form_data[{$FIELDNAME}]" value="{if trim(decode_html($FORM_DATA[$FIELDNAME])) neq ''}{trim(decode_html($FORM_DATA[$FIELDNAME]))}{elseif $RECORD_RUNNING}{$RECORD_RUNNING['form_data']['subject']}{elseif trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) neq ''}{$FIELD_SETTINGS[$FIELDNAME]['default']}{/if}" placeholder="{vtranslate('LBL_ENTER',$QUALIFIED_MODULE)} {vtranslate($FIELD_MODEL->get('label'), $EVENT_MODULE_MODEL->getName())}"/>
                                                </div>
                                            {elseif $FIELDNAME eq 'description'}
                                                <div class="form-group input-time-tracker-popup">
                                                    <textarea id="{$FIELDNAME}" name="form_data[{$FIELDNAME}]" placeholder="{vtranslate('LBL_ENTER',$QUALIFIED_MODULE)} {vtranslate($FIELD_MODEL->get('label'), $EVENT_MODULE_MODEL->getName())}" class="form-control fieldInput propFieldInput" rows="6">{if trim(decode_html($FORM_DATA[$FIELDNAME])) neq ''}{trim(decode_html($FORM_DATA[$FIELDNAME]))}{elseif $RECORD_RUNNING}{$RECORD_RUNNING['form_data']['description']}{elseif trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) neq ''}{$FIELD_SETTINGS[$FIELDNAME]['default']}{/if}</textarea>
                                                </div>
                                            {elseif $FIELDNAME eq 'activitytype'}
                                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                                <div class="form-group input-time-tracker-popup">
                                                    <select id="{$FIELDNAME}" data-default-value="{decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])}" class="form-control chzn-select propSelectFieldInput" name="form_data[{$FIELDNAME}]">
                                                        <option value="">{vtranslate('LBL_SELECT',$QUALIFIED_MODULE)} {vtranslate($FIELD_MODEL->get('label'), $EVENT_MODULE_MODEL->getName())}</option>
                                                        {if $FORM_DATA[$FIELDNAME] || $RECORD_RUNNING['form_data'][$FIELDNAME]}
                                                            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                                                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}"
                                                                        {if trim(decode_html($FORM_DATA[$FIELDNAME])) eq trim($PICKLIST_NAME) || trim(decode_html($RECORD_RUNNING['form_data'][$FIELDNAME])) eq trim($PICKLIST_NAME)}
                                                                            selected
                                                                        {/if}
                                                                        >{$PICKLIST_VALUE}</option>
                                                            {/foreach}
                                                        {else}
                                                            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                                                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}"
                                                                    {if trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq trim($PICKLIST_NAME)}
                                                                        selected
                                                                    {/if}
                                                                >{$PICKLIST_VALUE}</option>
                                                            {/foreach}
                                                        {/if}
                                                    </select>
                                                </div>
                                            {elseif $FIELDNAME eq 'assigned_user_id'}
                                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                                <div class="form-group input-time-tracker-popup">
                                                    {assign var=DEFAULT_ASSIGNED_TO value=$USER_MODEL->getId()}
                                                    {if trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq 'Record Assigned To'}
                                                        {assign var=DEFAULT_ASSIGNED_TO value=$RECORD_MODEL->get('assigned_user_id')}
                                                    {/if}
                                                    <select id="{$FIELDNAME}" data-default-value="{decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])}" class="form-control chzn-select propSelectFieldInput" name="form_data[{$FIELDNAME}]">
                                                        <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                                        {foreach from=$ASSIGNED_TO key=LABEL item=ASSIGNED_USERS_LIST}
                                                            <optgroup label="{vtranslate($LABEL,$QUALIFIED_MODULE)}">
                                                                {foreach from=$ASSIGNED_USERS_LIST item=ASSIGNED_USER key=ASSIGNED_USER_KEY}
                                                                    <option value="{$ASSIGNED_USER_KEY}" {if $ASSIGNED_USER_KEY eq $DEFAULT_ASSIGNED_TO} selected="" {/if}>{$ASSIGNED_USER}</option>
                                                                {/foreach}
                                                            </optgroup>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            {else}
                                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                                <div class="form-group input-time-tracker-popup">
                                                    <select class="form-control chzn-select propSelectFieldInput" name="form_data[{$FIELDNAME}]">
                                                        <option value="">{vtranslate('LBL_SELECT',$QUALIFIED_MODULE)} {vtranslate($FIELD_MODEL->get('label'), $EVENT_MODULE_MODEL->getName())}</option>
                                                        {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                                            <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}"
                                                                    {if trim(decode_html($FORM_DATA[$FIELDNAME])) eq trim($PICKLIST_NAME)}
                                                                        selected
                                                                    {elseif trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq trim($PICKLIST_NAME)}
                                                                        selected
                                                                    {/if}
                                                            >{$PICKLIST_VALUE}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            {/if}
                                        </td>
                                    </tr>
                                {else}
                                    <input type="hidden" name="form_data[{$FIELDNAME}]" value="{$FIELD_SETTINGS[$FIELDNAME]['default']}" />
                                {/if}
                            {/foreach}
                            {*Start date time and Due date time*}
                            <tr>
                                <td class="fieldValue medium">
                                    <div class="form-group input-time-tracker-popup">
                                        <input type="text" id="startDateTime" class="form-control dateTimeField"
                                               name="form_data[startdate]"
                                               value="{if $FORM_DATA['startdate']}{$FORM_DATA['startdate']}{elseif $RECORD_RUNNING['form_data']['startdate']}{$RECORD_RUNNING['form_data']['startdate']}{/if}"
                                               placeholder="{vtranslate('LBL_START_DATETIME',$QUALIFIED_MODULE)}"
                                               {if $SETTINGS['start_datetime_editable'] neq 1}readonly{/if}/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldValue medium">
                                    <div class="form-group input-time-tracker-popup">
                                        <input type="text" id="endDateTime" class="form-control dateTimeField"
                                               name="form_data[enddate]"
                                               value="{if $FORM_DATA['enddate']}{$FORM_DATA['enddate']}{elseif $RECORD_RUNNING['form_data']['enddate']}{$RECORD_RUNNING['form_data']['enddate']}{/if}"
                                               placeholder="{vtranslate('LBL_DUE_DATETIME',$QUALIFIED_MODULE)}"
                                               {if $SETTINGS['due_datetime_editable'] neq 1}readonly{/if}/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldValue medium" >
                                    <div style="text-align: center;" class="detailViewTitle">
                                        <input type="hidden" id="timeTrackerTotal" name="form_data[timeTrackerTotal]" value="{$FORM_DATA['timeTrackerTotal']}" />
                                        {if $RECORD_RUNNING}
                                            <input type="hidden" id="timeTrackerTotalRunning" value="{$RECORD_RUNNING['form_data']['timeTrackerTotal']}" />
                                            <input type="hidden" id="record_running" value="{$RECORD_RUNNING['record']}" />
                                        {/if}
                                        <span class="recordLabel font-x-x-large pushDown timeTrackerTotal" style="color: #2787e0;" >00:00:00</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldValue medium" >
                                    <div class="form-group" style="text-align: center">
                                        {if $SETTINGS['allow_create_comments'] eq 1}
                                            <input type="hidden" id="auto_comment" name="form_data[auto_comment]" value="{$SETTINGS['auto_create_comments']}">
                                            <a href="javascript:void(0);" id="commentIcon" data-auto-comment="{$SETTINGS['auto_create_comments']}" style="margin: 0 10px;display: inline-block;">
                                                <img src="layouts/v7/modules/TimeTracker/images/comment-on.jpg" width="35" id="commnentOn" {if $SETTINGS['auto_create_comments'] neq 1 && $FORM_DATA['auto_comment'] neq 1}style="display: none;" {/if}/>
                                                <img src="layouts/v7/modules/TimeTracker/images/comment-off.jpg" width="35" id="commentOff" {if $SETTINGS['auto_create_comments'] eq 1 || $FORM_DATA['auto_comment'] eq 1}style="display: none;" {/if}/><img src="layouts/v7/modules/TimeTracker/images/comment-off.jpg" width="35" id="commentOff" {if $SETTINGS['auto_create_comments'] eq 1 || $FORM_DATA['auto_comment'] eq 1}style="display: none;" {/if}/>
                                            </a>
                                        {else}
                                            &nbsp;
                                        {/if}
                                        <a href="javascript:void(0);" id="btnPause" style="margin: 0 10px;display: inline-block;">
                                            <img src="layouts/v7/modules/TimeTracker/images/pause.jpg" width="35">
                                        </a>
                                        <a href="javascript:void(0);" id="btnCancel" style="margin: 0 10px;display: inline-block;">
                                            <img src="layouts/v7/modules/TimeTracker/images/cancel.gif" width="35">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td >
                                    <div class="row-fluid" style="text-align: center">
                                        <input type="hidden" name="trackerStatus" id="trackerStatus" value="{$STATUS}"/>
                                        <button type="button" class="btn btn-success" style="padding: 4px; width: 210px;" id="controlButton" data-start-label="{vtranslate('LBL_START',$QUALIFIED_MODULE)}" data-complete-label="{vtranslate('LBL_COMPLETE',$QUALIFIED_MODULE)}" data-resume-label="{vtranslate('LBL_RESUME',$QUALIFIED_MODULE)}" data-status="{$STATUS}">
                                            {if $STATUS eq 'running'}
                                                {vtranslate('LBL_COMPLETE',$QUALIFIED_MODULE)}
                                            {elseif $STATUS eq 'pause'}
                                                {vtranslate('LBL_RESUME',$QUALIFIED_MODULE)}
                                            {else}
                                                {if $RECORD_RUNNING}
                                                    {vtranslate('LBL_START_TIMER_FOR',$QUALIFIED_MODULE)} {$RECORD_MODEL->getName()}
                                                {else}
                                                    {{vtranslate('LBL_START',$QUALIFIED_MODULE)}}
                                                {/if}

                                            {/if}
                                        </button>
                                        <input type="hidden" name="record_name" value="{$RECORD_MODEL->getName()}"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>

            {************LIST TIMER ACTIVE************}
            {if $LIST_TIMER_ACTIVE[0]}
                <div class="modal-header contentsBackground" style="text-align: center; border-bottom: none; "><h5 style="color: #FFF;">{vtranslate('LBL_ACTIVE_TIMERS',$QUALIFIED_MODULE)}</h5></div>
                <table class="table table-bordered listViewEntriesTable" id="listActiveTimers">

                    {foreach from=$LIST_TIMER_ACTIVE  item=TIMER_DATA }
                        <tr id="record_{$TIMER_DATA['record']}">
                            <td class="summaryViewEntries">
                            <span class="alignCenter " style="color: #004123;">
                                <a class="record_name" href="index.php?module={$TIMER_DATA['form_data']['module']}&record={$TIMER_DATA['record']}&view=Detail" style="display:inline-block;overflow: hidden;width: 145px;white-space: nowrap;text-overflow: ellipsis;-o-text-overflow: ellipsis;-ms-text-overflow: ellipsis;" title="{if $TIMER_DATA['name'] neq ''} {$TIMER_DATA['name']} {else} - {/if}">
                                    {if $TIMER_DATA['name'] neq ''} {$TIMER_DATA['name']} {else} - {/if}
                                </a>
                            </span>
                            </td>
                            <td class="summaryViewEntries " >
                            <span  class="alignCenter {if $TIMER_DATA['status'] eq 'running'}timeTrackerTotalRunning{/if} timeValue" style="color: #2787e0;">
                                {if $TIMER_DATA['form_data']['timeTrackerTotal'] neq ''} {$TIMER_DATA['form_data']['timeTrackerTotal']} {else} - {/if}
                            </span>
                            </td>
                            <td class="summaryViewEntries ">
                             <span class="alignMiddle">
                                <a class="play_icon" href="index.php?module={$TIMER_DATA['form_data']['module']}&record={$TIMER_DATA['record']}&view=Detail&go_back=1">
                                    <img src="layouts/v7/modules/TimeTracker/images/go_play_pause.png" alt="Go back record"/>
                                </a>
                             </span>
                            </td>
                        </tr>
                    {/foreach}
                    <tr class="hide row_base">
                        <td class="summaryViewEntries">
                        <span class="alignCenter " style="color: #004123;">
                            <a class="record_name" href="javascript:voice(0)" style="display:inline-block;overflow: hidden;width: 145px;white-space: nowrap;text-overflow: ellipsis;-o-text-overflow: ellipsis;-ms-text-overflow: ellipsis;" title=""</a>
                        </span>
                        </td>
                        <td class="summaryViewEntries" >
                            <span class="alignCenter timeTrackerTotal timeValue" style="color: #2787e0;"></span>
                        </td>
                        <td class="summaryViewEntries ">
                         <span class="alignMiddle">
                            <a class="play_icon" href="javascript:voice(0)">
                                <img src="layouts/v7/modules/TimeTracker/images/go_play_pause.png" alt="Go back record"/>
                            </a>
                         </span>
                        </td>
                    </tr>
                </table>
            {/if}
        </div>
    </div>
{/strip}