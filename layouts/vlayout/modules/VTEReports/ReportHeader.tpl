{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
<script type="text/javascript"
  src="modules/VTEReports/highcharts/js/angular.js">
</script>

<script type="text/javascript" 
  src="modules/VTEReports/highcharts/js/highcharts-ng.js">
</script> 

<script type="text/javascript" 
  src="modules/VTEReports/highcharts/js/rgbcolor.js">
</script> 

<script type="text/javascript" 
  src="modules/VTEReports/highcharts/js/StackBlur.js">
</script>

<script type="text/javascript" 
  src="modules/VTEReports/highcharts/js/canvg.js">
</script> 

{strip}
	<div id="toggleButton" class="toggleButton" title="{vtranslate('LBL_LEFT_PANEL_SHOW_HIDE', 'Vtiger')}">
		<i id="tButtonImage" class="{if $LEFTPANELHIDE neq '1'}icon-chevron-left{else}icon-chevron-right{/if}"></i>
	</div>
    <div class="container-fluid no-print">
        <div class="row-fluid reportsDetailHeader">
            <input type="hidden" name="date_filters" data-value='{ZEND_JSON::encode($DATE_FILTERS)}' />
            <input type="hidden" name="report_filename" id="report_filename" value="" />
            
            <input type="hidden" name="export_pdf_format" id="export_pdf_format" value="" />
            <input type="hidden" name="pdf_file_name" id="pdf_file_name" value="" />
            <input type="hidden" name="ch_image_name" id="ch_image_name" value="" />
             
            <form id="detailView" onSubmit="return false;"  method="POST">
            <input type="hidden" name="date_filters" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATE_FILTERS))}' />
            <input type="hidden" name="advft_criteria" id="advft_criteria" value='' />
            <input type="hidden" name="advft_criteria_groups" id="advft_criteria_groups" value='' />
            <input type="hidden" name="groupft_criteria" id="groupft_criteria" value='' />
            <input type="hidden" name="reload" id="reload" value='' />
            <input type="hidden" name="currentMode" id="currentMode" value='generate' />
            
            <br>
            <div class="reportHeader row-fluid">
                <div class="span4">
                    <div class="btn-toolbar">
                        {if $REPORT_MODEL->isEditable() eq true}
                            <div class="btn-group">
                                <button onclick='window.location.href="{$REPORT_MODEL->getEditViewUrl()}"' type="button" class="cursorPointer btn">
                                    <img src="modules/VTEReports/img/edit.png" width="15px" height="15px" style="margin: 0px 3px -3px 0px;"/>
                                    <strong>{vtranslate('Edit',$MODULE)}</strong>
                                </button>
                            </div>
	                        <div class="btn-group">
	                            <button onclick='window.location.href="{$REPORT_MODEL->getDuplicateRecordUrl()}"' type="button" class="cursorPointer btn">
                                    <img src="modules/VTEReports/img/duplicate.png" width="15px" height="15px" style="margin: 0px 3px -3px 0px;"/>
	                                <strong>{vtranslate('LBL_DUPLICATE',$MODULE)}</strong>
	                            </button>
	                        </div>
                            <div class="btn-group">
                                <button onclick='window.location.href="index.php?module=VTEReports&view=List"' type="button" class="cursorPointer btn">
                                    <img src="modules/VTEReports/img/list.png" width="15px" height="15px" style="margin: 0px 3px -3px 0px;"/>
                                    <strong>{vtranslate('List Reports',$MODULE)}</strong>
                                </button>
                            </div>
                        {/if}
                    </div>
                </div>
                <div class='span3 textAlignCenter'>
                    <h3>{$REPORT_MODEL->getName()}</h3>
                    <div id="noOfRecords">{vtranslate('LBL_NO_OF_RECORDS',$MODULE)} <span id="countValue">{$COUNT}</span>
						{if $COUNT > 1000}
							<span class="redColor" id="moreRecordsText"> ({vtranslate('LBL_MORE_RECORDS_TXT',$MODULE)})</span>
						{else}
							<span class="redColor hide" id="moreRecordsText"> ({vtranslate('LBL_MORE_RECORDS_TXT',$MODULE)})</span>
                    {/if}
                    </div>
                    <div id='activate_pdfmaker' style="display:block;">
                        {if $IS_TEST_WRITE_ABLE !== true}
                            {vtranslate('Test_Not_WriteAble',$MODULE)}
                        {/if}
                    </div>
                </div>
                <div class='span5' style="width: 40%;float: right;">
                    <span class="pull-right">
                        <div class="btn-toolbar">
                            {foreach item=DETAILVIEW_LINK name=LINK from=$DETAILVIEW_LINKS}
                                {assign var=LINKNAME value=$DETAILVIEW_LINK->getLabel()}
                                <div class="btn-group">
                                    <button class="btn reportActions" name="{$LINKNAME}" data-href="{$DETAILVIEW_LINK->getUrl()}" {if $DETAILVIEW_LINK->get('id')!=""}id="{$DETAILVIEW_LINK->get('id')}"{/if} {if $DETAILVIEW_LINK->get('style')!=""}style="{$DETAILVIEW_LINK->get('style')}"{/if} {if $DETAILVIEW_LINK->get('onClick')!=""}onClick="{$DETAILVIEW_LINK->get('onClick')}"{/if} >
                                        {if $smarty.foreach.LINK.index == 0}
                                            <img src="modules/VTEReports/img/print.png" width="15px"  style="margin: 0px 3px -3px 0px;height: 15px;"/>
                                        {else}
                                            <img src="modules/VTEReports/img/{$DETAILVIEW_LINK->getIcon()}" width="15px" height="15px" style="margin: 0px 3px -3px 0px;"/>
                                        {/if}
                                        <strong>{$LINKNAME}</strong>
                                    </button>
                                </div>
                            {/foreach}
                        </div>
                    </span>
                </div>
            </div>
			<br>
            <div class="row-fluid">
                <input type="hidden" id="recordId" value="{$RECORD_ID}" />
                <input type="hidden" id="widgetvtereportsId" value="{$RECORD_ID}" />
                {assign var=RECORD_STRUCTURE value=array()}
                {assign var=PRIMARY_MODULE_LABEL value=vtranslate($PRIMARY_MODULE, $PRIMARY_MODULE)}
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$PRIMARY_MODULE_RECORD_STRUCTURE}
                    {assign var=PRIMARY_MODULE_BLOCK_LABEL value=vtranslate($BLOCK_LABEL, $PRIMARY_MODULE)}
                    {assign var=key value="$PRIMARY_MODULE_LABEL $PRIMARY_MODULE_BLOCK_LABEL"}
                    {if $LINEITEM_FIELD_IN_CALCULATION eq false && $BLOCK_LABEL eq 'LBL_ITEM_DETAILS'}
                        {* dont show the line item fields block when Inventory fields are selected for calculations *}
                    {else}
                        {$RECORD_STRUCTURE[$key] = $BLOCK_FIELDS}
                    {/if}
                {/foreach}
                {foreach key=MODULE_LABEL item=SECONDARY_MODULE_RECORD_STRUCTURE from=$SECONDARY_MODULE_RECORD_STRUCTURES}
                    {assign var=SECONDARY_MODULE_LABEL value=vtranslate($MODULE_LABEL, $MODULE_LABEL)}
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$SECONDARY_MODULE_RECORD_STRUCTURE}
                        {assign var=SECONDARY_MODULE_BLOCK_LABEL value=vtranslate($BLOCK_LABEL, $MODULE_LABEL)}
                        {assign var=key value="$SECONDARY_MODULE_LABEL $SECONDARY_MODULE_BLOCK_LABEL"}
                        {$RECORD_STRUCTURE[$key] = $BLOCK_FIELDS}
                    {/foreach}
                {/foreach}
                {*{include file='AdvanceFilter.tpl'|@vtemplate_path RECORD_STRUCTURE=$RECORD_STRUCTURE ADVANCE_CRITERIA=$SELECTED_ADVANCED_FILTER_FIELDS COLUMNNAME_API=getReportFilterColumnName}*}
{include file='modules/VTEReports/AdvanceFilter.tpl'}
{* ADVANCE FILTER START *}
<div class="allConditionContainer conditionGroup contentsBackground well">
{include file='modules/VTEReports/FiltersBlock.tpl'}
</div>
{* ADVANCE FILTER END *}
                <div class="row-fluid">
                    <div class="textAlignCenter">
                        <button class="btn generateReport" data-mode="generate" value="{vtranslate('LBL_GENERATE_NOW',$MODULE)}"/>
                            <strong>{vtranslate('LBL_GENERATE_NOW',$MODULE)}</strong>
                        </button>&nbsp;
                        {if $REPORT_MODEL->isEditable() eq true}
													<button class="btn btn-success generateReport" data-mode="save" value="{vtranslate('LBL_SAVE',$MODULE)}"/>
	                            <strong>{vtranslate('LBL_SAVE',$MODULE)}</strong>
	                        </button>
												{/if}
                        {if $checkDashboardWidget != "" && $checkDashboardWidget != "Exist"}
                            <button class="btn addWidget" data-mode="addwidget" data-type = "false" value="{vtranslate('LBL_ADD_WIDGET',$MODULE)}"/>
                                <strong>{vtranslate('LBL_ADD_WIDGET',$MODULE)}</strong>
                            </button>
                        {/if}
                        {if  $checkDashboardListWidget != ""  && $checkDashboardListWidget != "Exist"}
                            &nbsp;&nbsp;<button class="btn addWidget" data-mode="addwidget" data-type = "true" value="{vtranslate('Add Detail to dashboard home page',$MODULE)}"/>
                            <strong>{vtranslate('Add Detail to dashboard home page',$MODULE)}</strong>
                            </button>
                        {/if}
                   </div>
                </div>
				<br>
            </div>
           </form>
        </div>
    </div>

{/strip}
<form method="post" action="IndexAjax" target="_blank">
    <input type="hidden" name="module" value="VTEReports"/>
    <input type="hidden" name="action" value="IndexAjax"/>  
    <input type="hidden" name="mode" value="ExportXLS"/>  
    <input type="hidden" name="filename" value="Test.xls"/>  
    <input type="hidden" name="report_html" id="report_html" value=""/>
</form>
<form method="post" action="index.php" name="CreatePDF" id="CreatePDF" target="_blank">
    <input type="hidden" name="module" value="VTEReports"/>
    <input type="hidden" name="action" value="CreatePDF"/>
    <input type="hidden" name="form_export_pdf_format" id="form_export_pdf_format" value=""/>  
    <input type="hidden" name="form_filename" id="form_filename" value=""/>
    <input type="hidden" name="form_report_name" id="form_report_name" value=""/>  
    <input type="hidden" name="form_report_html" id="form_report_html" value=""/>
    <input type="hidden" name="form_chart_canvas" id="form_chart_canvas" value=""/>
</form>
<script>
    jQuery(document).ready(function () {
        jQuery('#btnExport').click(function(e){
            var element = jQuery(e.currentTarget);
            var report_filename_obj = document.getElementById('report_filename');
            if (report_filename_obj && report_filename_obj.value != "") {
                exportToPDF(report_filename_obj.value);
                return false;
            }
            return false;
        });
    });
</script>