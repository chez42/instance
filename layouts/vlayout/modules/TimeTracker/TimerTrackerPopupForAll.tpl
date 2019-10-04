{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Time Tracker ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div style="width: 270px;">
        <div class="modelContainer" id="">
            <form class="form-horizontal timeTrackerForm" name="TrackerForm" method="post" action="index.php">
                <div class="quickCreateContent">
                    <div class="modal-body">
                        {assign var=FIELD_SETTINGS value=$SETTINGS['field_settings']}
                        <table style="margin: 0 auto;">
                            <tr>
                                <td class="fieldValue medium" colspan="2">
                                    <input type="hidden" id="timeTrackerTotal" name="form_data[timeTrackerTotal]" value="{$FORM_DATA['timeTrackerTotal']}" />
                                    {if $RECORD_RUNNING}
                                        <input type="hidden" id="timeTrackerTotalRunning" value="{$RECORD_RUNNING['form_data']['timeTrackerTotal']}" />
                                        <input type="hidden" id="record_running" value="{$RECORD_RUNNING['record']}" />
                                    {/if}
                                    <div class="row-fluid" style="text-align: center">
                                        {if $SETTINGS['allow_create_comments'] eq 1}
                                            <input type="hidden" id="auto_comment" name="form_data[auto_comment]" value="{$SETTINGS['auto_create_comments']}">
                                            <a href="javascript:void(0);" id="commentIcon" data-auto-comment="{$SETTINGS['auto_create_comments']}" style="margin: 0 10px;display: inline-block;">
                                                <img src="layouts/vlayout/modules/TimeTracker/images/comment-on.jpg" width="35" id="commnentOn" {if $SETTINGS['auto_create_comments'] neq 1 && $FORM_DATA['auto_comment'] neq 1}style="display: none;" {/if}/>
                                                <img src="layouts/vlayout/modules/TimeTracker/images/comment-off.jpg" width="35" id="commentOff" {if $SETTINGS['auto_create_comments'] eq 1 || $FORM_DATA['auto_comment'] eq 1}style="display: none;" {/if}/>
                                            </a>
                                        {else}
                                            &nbsp;
                                        {/if}
                                        <a href="javascript:void(0);" id="btnPause" style="margin: 0 10px;display: inline-block;">
                                            <img src="layouts/vlayout/modules/TimeTracker/images/pause.jpg" width="35">
                                        </a>
                                        <a href="javascript:void(0);" id="btnCancel" style="margin-left: 20px;display: inline-block;">
                                            <img src="layouts/vlayout/modules/TimeTracker/images/cancel.gif" width="35">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </form>

            {************LIST TIMER ACTIVE************}
            {if $LIST_TIMER_ACTIVE[0]}
                <div class="modal-header contentsBackground" style="text-align: center; border-bottom: none; "><h3 style="color: #004123;">{vtranslate('LBL_ACTIVE_TIMERS',$QUALIFIED_MODULE)}</h3></div>
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
                                    <img src="layouts/vlayout/modules/TimeTracker/images/go_play_pause.png" alt="Go back record"/>
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
                                <img src="layouts/vlayout/modules/TimeTracker/images/go_play_pause.png" alt="Go back record"/>
                            </a>
                         </span>
                        </td>
                    </tr>
                </table>
                <input type="hidden" id="listActiveTimer" value="1" />
                {else}
                <input type="hidden" id="listActiveTimer" value="0" />
            {/if}
        </div>
    </div>
{/strip}