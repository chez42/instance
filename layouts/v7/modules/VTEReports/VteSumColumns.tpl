{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
<style>
    .div_column_child{
        width: 200px!important;
    }
    .div_column_child_off{
        background: -webkit-linear-gradient(left, #ddd 0, #ddd 16%, transparent 12%);
    }
    .div_column_child_off:hover, .div_column_child_on {
        background: -webkit-linear-gradient(left, #1998cb 0, #1998cb 16%, transparent 15%);
</style>
{strip}
    <input type="hidden" name="timelinecolumn_html" id='timelinecolumn_html' value='{$timelinecolumn}'>
    <div id='date_options_json' class="none" style='display:none;'>{$date_options_json}</div>
    <div id='sum_group_columns' class="none" style='display:none;'>{$sum_group_columns}</div>
    <input type="hidden" id="summaries_orderby_val" value="{$summaries_orderby}">
    {assign var="matrix_js" value="" }
    {assign var="reporttype_readonly" value="" }
    {if $REPORTTYPE == "summaries" || $REPORTTYPE == "summaries_matrix"}
        {assign var="reporttype_readonly" value="readonly" }
    {elseif $REPORTTYPE == "summaries_matrix"}
        {assign var="matrix2_js" value="matrix_js(2);" }
        {assign var="matrix3_js" value="matrix_js(3);" }
        {assign var="timeline_type2" value="cols" }
    {/if}

    <div class="row-fluid" id="div_summary_step2">
        <div class="span12">
            <div class="row-fluid">
                <table class="table table-bordered table-report">
                    <tbody>
                    <tr>
                        <td style="padding:0px;" cellpadding="0" cellspacing="0">
                            <div class="row-fluid padding1per" style="margin-left: 20%;margin-top: 20px;">
                                <div class="span8">
                                    <div style="height:40px;">
                                        <div style="float: left;padding-top: 10px;padding-right: 15px;">{vtranslate('LBL_SELECT_MODULE',$MODULE)}</div>
                                        <div class="select-style" style="float: left;width: 280px!important;">
                                            <select id="SummariesModules" name="SummariesModules" onchange='defineSUMModuleFields(this)' class="txtBox" style="width:auto;margin:auto;" >
                                                {foreach item=modulearr from=$SummariesModules}
                                                    <option value={$modulearr.id} {if $modulearr.checked == "checked"}selected="selected"{/if} >{$modulearr.name}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                        <input type="text" id="search_input_sum" class="span3" onkeyup="getFieldsOptionsSearch(this);" placeholder="{vtranslate('LBL_Search_column',$MODULE)}" style="height: 38px;margin-left: 16px;">
                                    </div>
                                </div>
                                <div id="availSumModValues" style="display:none;">{$ALL_FIELDS_STRING}</div>
                                <input type="hidden" name="selectedSummariesTmp" id="selectedSummariesTmp" value="{$SUM_BLOCK_FIELDS}">
                                <input type="hidden" name="selectedSummariesString" id="selectedSummariesString" value="{$selectedSummariesString}">
                            </div>
                            <div class="span12" id="div_selected_column" style="padding: 10px;Z">
                            </div>
                        </td>
                    </tr>
                    <tbody>
                </table>
                <br />
                <div id="hdVteGroupAndLimit" class="hide">
                    {include file='modules/VTEReports/VteGroupAndLimit.tpl'}
                </div>
            </div>
        </div>
    </div>
{/strip}