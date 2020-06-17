{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Summary Report ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <form class="form-horizontal recordEditView" id="report_step2" method="post" action="index.php">
        <input type="hidden" name="module" value="{$MODULE}" />
        <input type="hidden" name="action" value="Save" />
        <input type="hidden" name="record" value="{$RECORD_ID}" />
        <input type="hidden" name="reportname" value="{$REPORT_MODEL->get('reportname')}" />
        <input type="hidden" name="reporttype" value="{$REPORT_MODEL->get('reporttype')}" />
        <input type="hidden" name="reports_description" value="{$REPORT_MODEL->get('description')}" />
        <input type="hidden" name="selected_module" value="{$REPORT_MODEL->get('selected_module')}" />
        <input type="hidden" name="selected_fields" id="seleted_fields" value='{ZEND_JSON::encode($REPORT_MODEL->get('seleted_fields'))}' />
        <input type="hidden" name="advanced_filter" id="advanced_filter" value="" />
        <input type="hidden" class="step" value="2" />

        <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
        <div class="well">
            <div class="row-fluid">
                <h4><strong>{vtranslate('LBL_CHOOSE_FILTER_CONDITIONS',$MODULE)}</strong></h4>
                <br>
					{include file='AdvanceFilter.tpl'|@vtemplate_path RECORD_STRUCTURE=$MODULE_RECORD_STRUCTURE ADVANCE_CRITERIA=$SELECTED_ADVANCED_FILTER_FIELDS COLUMNNAME_API=getReportFilterColumnName SOURCE_MODULE=$REPORT_MODEL->get('selected_module')}

            </div>
        </div>
        <br>
        <div class="pull-right block">
            <button type="button" class="btn btn-danger backStep"><strong>{vtranslate('LBL_BACK',$MODULE)}</strong></button>&nbsp;&nbsp;
            <button type="submit" class="btn btn-success" id="generateReport"><strong>{vtranslate('LBL_GENERATE_REPORT','Reports')}</strong></button>&nbsp;&nbsp;
            <a  class="cancelLink" onclick="window.history.back()">{vtranslate('LBL_CANCEL',$MODULE)}</a>&nbsp;&nbsp;
        </div>
    </form>
{/strip}
