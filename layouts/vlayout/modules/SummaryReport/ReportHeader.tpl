{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="container-fluid">
    <div class="row-fluid reportsDetailHeader">
        <input type="hidden" name="date_filters" data-value='{ZEND_JSON::encode($DATE_FILTERS)}' />
        <form id="detailView" onSubmit="return false;">
            <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
            <br>
            <div class="reportHeader row-fluid">
                <div class="span3">
                    <div class="btn-toolbar">
                        {if $REPORT_MODEL->isEditable() eq true}
                            <div class="btn-group">
                                <button onclick='window.location.href="{$REPORT_MODEL->getEditViewUrl()}"' type="button" class="cursorPointer btn">
                                    <strong>{vtranslate('LBL_CUSTOMIZE',$MODULE)}</strong>&nbsp;
                                    <i class="icon-pencil"></i>
                                </button>
                            </div>
                        {/if}
                    </div>
                </div>
                <div class='span5 textAlignCenter'>
                    <h3>{$REPORT_MODEL->getName()}</h3>
                    <div id="noOfRecords">{vtranslate('LBL_NO_OF_RECORDS','Reports')} <span id="countValue">{$COUNT}</span>
                    </div>
                </div>
                <div class='span4'>
                    {if $REPORT_MODEL->get('reporttype') eq 'LBL_TABULAR'}
                    <span class="pull-right">
                        <div class="btn-toolbar">
                            {*<div class="btn-group">
                                <button class="btn reportActions" name="{vtranslate('LBL_REPORT_CSV', 'Reports')}" data-href="index.php?module=SummaryReport&view=ExportReport&mode=GetCSV&record={$RECORD_ID}">
                                    <strong>{vtranslate('LBL_REPORT_CSV', 'Reports')}</strong>
                                </button>
                            </div>*}
                            <div class="btn-group">
                                <button class="btn reportActions" name="{vtranslate('LBL_REPORT_EXPORT_EXCEL', 'Reports')}" data-href="index.php?module=SummaryReport&view=ExportReport&mode=GetXLS&record={$RECORD_ID}">
                                    <strong>{vtranslate('LBL_REPORT_EXPORT_EXCEL', 'Reports')}</strong>
                                </button>
                            </div>
                        </div>
                    </span>
                    {/if}
                </div>
            </div>
            <br>
            <div class="row-fluid">
                <input type="hidden" id="recordId" value="{$RECORD_ID}" />
                {*{include file='AdvanceFilter.tpl'|@vtemplate_path RECORD_STRUCTURE=$MODULE_RECORD_STRUCTURE ADVANCE_CRITERIA=$SELECTED_ADVANCED_FILTER_FIELDS COLUMNNAME_API=getReportFilterColumnName SOURCE_MODULE=$REPORT_MODEL->get('modulename')}*}

                <div class="filterContainer">
                    {assign var="dateFormat" value=$USER_MODEL->get('date_format')}
                    <input type="hidden" name="date_format" data-value='{$dateFormat}' value="{$dateFormat}" />
                    <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
                    <input type=hidden name="advanceFilterOpsByFieldType" data-value='{ZEND_JSON::encode($ADVANCED_FILTER_OPTIONS_BY_TYPE)}' />
                    {foreach key=ADVANCE_FILTER_OPTION_KEY item=ADVANCE_FILTER_OPTION from=$ADVANCED_FILTER_OPTIONS}
                        {$ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION_KEY] = vtranslate($ADVANCE_FILTER_OPTION, $MODULE)}
                    {/foreach}
                    <input type=hidden name="advanceFilterOptions" data-value='{ZEND_JSON::encode($ADVANCED_FILTER_OPTIONS)}' />
                    <div class="allConditionContainer conditionGroup contentsBackground well">
                        <div class="contents">
                            <div class="conditionList">
                                <div class="row-fluid conditionRow marginBottom10px">
                                    <span class="span4" style="text-align: right; margin-top:4px;">
                                        <strong>{vtranslate('LBL_DATE', $MODULE)}</strong>
                                    </span>
                                    <span class="span3">
                                        {assign var=ADVANCE_FILTER_OPTIONS value=$ADVANCED_FILTER_OPTIONS_BY_TYPE['DT']}
                                        {assign var=DATE_FILTER_CONDITIONS value=array_keys($DATE_FILTERS)}
                                        {assign var=ADVANCE_FILTER_OPTIONS value=array_merge($ADVANCE_FILTER_OPTIONS, $DATE_FILTER_CONDITIONS)}
                                        <select class="{if empty($NOCHOSEN)}chzn-select{/if} row-fluid" name="comparator">
                                            <option value="none">{vtranslate('LBL_NONE',$MODULE)}</option>
                                            {foreach item=ADVANCE_FILTER_OPTION from=$ADVANCE_FILTER_OPTIONS}
                                                <option value="{$ADVANCE_FILTER_OPTION}" {if $ADVANCE_FILTER_OPTION eq 'today'}selected="" {/if}>{if vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION]) neq ''}{vtranslate($ADVANCED_FILTER_OPTIONS[$ADVANCE_FILTER_OPTION])}{else}{$DATE_FILTERS[$ADVANCE_FILTER_OPTION]['label']}{/if}</option>
                                            {/foreach}
                                        </select>
                                    </span>
                                    <span class="span4 fieldUiHolder">
                                        <input name="date_filter" data-value="value" class="row-fluid" type="text" value="{$CONDITION_INFO['value']|escape}" />
                                    </span>
                                </div>
                                {if $REPORT_MODEL->get('reporttype') eq 'LBL_TABULAR'}
                                <div class="row-fluid conditionRow marginBottom10px">
                                    <span class="span4" style="text-align: right; margin-top:4px;">
                                        <strong>{vtranslate('LBL_INCLUDE_COMMENTS', $MODULE)}</strong>
                                    </span>
                                    <span class="span1" style="text-align: left;">
                                        <input type="checkbox" value="1" name="include_comments" id="include_comments" class="pull-left"/>
                                    </span>
                                </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="textAlignCenter">
                        <button class="btn generateReport" data-mode="generate" value="{vtranslate('LBL_GENERATE_NOW',$MODULE)}"/>
                        <strong>{vtranslate('LBL_GENERATE_NOW',$MODULE)}</strong>
                        </button>
                    </div>
                </div>
                <br>
            </div>
        </form>
    </div>
    <div id="reportContentsDiv">
{/strip}