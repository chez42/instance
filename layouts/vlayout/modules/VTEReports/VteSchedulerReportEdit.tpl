{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}

<table class="table table-bordered table-report">
    <tbody>
    <tr>
        <td class="fieldLabel medium"><label class="pull-right marginRight10px">{vtranslate('LBL_SCHEDULE_FREQUENCY',$MODULE)}</label></td>
        <td>
            <div class="row-fluid span12">
                <div class="span select-style" id="scheduledTypeSpan" style="height: 37px;">
                    <select class="chzn-select  chzn-done" name="scheduledType" id="scheduledType" onchange="setScheduleOptions();" style="padding-top: 3px; height: 41px;">
                        <option id="schtype_1" value="1" {if $schtypeid eq 1}selected{/if}>{vtranslate('Hourly',$MODULE)}</option>
                        <option id="schtype_2" value="2" {if $schtypeid eq 2}selected{/if}>{vtranslate('Daily',$MODULE)}</option>
                        <option id="schtype_3" value="3" {if $schtypeid eq 3}selected{/if}>{vtranslate('Weekly',$MODULE)}</option>
                        <option id="schtype_4" value="4" {if $schtypeid eq 4}selected{/if}>{vtranslate('BiWeekly',$MODULE)}</option>
                        <option id="schtype_5" value="5" {if $schtypeid eq 5}selected{/if}>{vtranslate('Monthly',$MODULE)}</option>
                        <option id="schtype_6" value="6" {if $schtypeid eq 6}selected{/if}>{vtranslate('Annually',$MODULE)}</option>
                    </select>
                </div>
                <div style="margin-left: 56px;margin-top: 11px;float: left;width: 60px;">
                    {vtranslate('Active',$MODULE)} <input type="checkbox" name="isReportScheduled" id="isReportScheduled" {if $IS_SCHEDULED eq 'true'} checked {/if}>
                </div>
                <div style="display: inline;width: 400px;">
                    <table style="margin-top: 1px;">
                        <tr>
                            <td> <label class="pull-right marginRight10px" style="margin-top: 3px;">{vtranslate('LBL_REPORT_FORMAT',$MODULE)}</label></td>
                            <td>
                                {vtranslate('LBL_REPORT_FORMAT_PDF',$MODULE)} <input type="checkbox" name="scheduledReportFormat_pdf" id="scheduledReportFormat_pdf" {if $REPORT_FORMAT_PDF eq 'true'} checked {/if}>
                                {vtranslate('LBL_REPORT_FORMAT_EXCEL',$MODULE)} <input type="checkbox" name="scheduledReportFormat_xls" id="scheduledReportFormat_xls" {if $REPORT_FORMAT_XLS eq 'true'} checked {/if}>
                                {vtranslate('LBL_REPORT_FORMAT_CSV',$MODULE)} <input type="checkbox" name="scheduledReportFormat_csv" id="scheduledReportFormat_csv" {if $REPORT_FORMAT_CSV eq 'true'} checked {/if}>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="fieldLabel medium">&nbsp;</td>
        <td>
            <div id="schedule_to_display">
                <div class="span" id="scheduledMonthSpan" style="display: {if $schtypeid eq 6}inline{else}none{/if};">
                    <ul class="span12">
                        {assign var="MONTH_STRINGS" value=vtranslate('MONTH_STRINGS',$MODULE)}
                        {foreach key=mid item=month from=$MONTH_STRINGS}
                            <li data-value="{$mid}" class="scheduled-div scheduled-div-month {if $schmonth eq $mid}scheduled-div-selected{/if}">{$month}</li>
                        {/foreach}
                        <input type="hidden" name="scheduledMonth" id="scheduledMonth" value="{if $schmonth}{$schmonth}{else}0{/if}" class="hd_schedule_value" />
                    </ul>
                </div>

                <!-- day of month (monthly, annually) -->
                <div class="span" id="scheduledDOMSpan" style="display: {if $schtypeid eq 5 || $schtypeid eq 6}inline{else}none{/if};">
                    <ul class="span12">
                        {section name=day start=1 loop=32}
                            <li data-value="{$smarty.section.day.iteration}" class="scheduled-div {if $schday eq $smarty.section.day.iteration}scheduled-div-selected{/if}" >{$smarty.section.day.iteration}</li>
                        {/section}
                        <input type="hidden" name="scheduledDOM" id="scheduledDOM" value="{if $schday}{$schday}{else}1{/if}" class="hd_schedule_value" />
                    </ul>

                </div>
                <div class="span" id="scheduledDOWSpan" style="display: {if $schtypeid eq 3 || $schtypeid eq 4}inline{else}none{/if};">
                    <ul class="span12" name="scheduledDOW" id="scheduledDOW">
                        {assign var="WEEKDAY_STRINGS" value=vtranslate('WEEKDAY_STRINGS',$MODULE)}
                        {foreach key=wid item=week from=$WEEKDAY_STRINGS}
                            <li data-value="{$wid}" class="scheduled-div scheduled-div-weekly {if $schweek eq $wid}scheduled-div-selected{/if}">{$week|substr:0:3}</li>
                        {/foreach}
                        <input type="hidden" name="scheduledDOW" id="scheduledDOW" value="{if $schweek}{$schweek}{else}0{/if}" class="hd_schedule_value" />
                    </ul>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td class="fieldLabel medium"><label class="pull-right marginRight10px">{vtranslate('LBL_SCHEDULE_EMAIL_TIME',$MODULE)}</label></td>
        <td>
            <div class="row-fluid">
                <div class="span" id="scheduledTimeSpan" style="display: {if $schtypeid > 0}inline{else}none{/if};">
                    <input class="span2" type="text" name="scheduledTime" id="scheduledTime" value="{$schtime}" size="5" maxlength="5" />&nbsp;{vtranslate('LBL_TIME_FORMAT_MSG',$MODULE)}
                </div>
            </div>
            <input type="hidden" name="scheduledIntervalString" value="" />
        </td>
    </tr>
    <tr>
        <td class="fieldLabel medium" style="vertical-align: text-bottom;">
            <label class="pull-right marginRight10px" style="margin-top: 24px;">{vtranslate("Recipients",$MODULE)}</label>
        </td>
        <td>
            <table>
                <tr>
                    <td style="vertical-align: text-bottom;padding-left: 0px;">
                        <select id="selectedRecipients" name="selectedRecipients" class="select2 span8" multiple>
                            {$SELECTED_RECIPIENTS}
                        </select>
                        <input type="hidden" name="selectedRecipientsString"/>
                    </td>
                    <td>
                        <table style="width: 166px;">
                            <tr class="op-Users"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Users",$MODULE)}</p></td></tr>
                            <tr class="op-Groups"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Groups",$MODULE)}</p></td></tr>
                            <tr class="op-Roles"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Roles",$MODULE)}</p></td></tr>
                            <tr class="op-RoleAndSubordinates"><td class="textAlignCenter"><p style="text-align: center;">{vtranslate("Role And Subordinates",$MODULE)}</p></td></tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tbody>
</table>
<script>
    jQuery('.scheduled-div').live('click',function(){
        var this_val = jQuery(this).data('value');
        jQuery(this).closest('ul').find('li').removeClass('scheduled-div-selected');
        jQuery(this).addClass('scheduled-div-selected');
        jQuery(this).closest('ul').find('input.hd_schedule_value').val(this_val);
    });
</script>