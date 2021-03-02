{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}

<div id="sendSmsContainer" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        {assign var=TITLE value="{vtranslate('Generate Report PDF', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="massGeneratePdf" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="action" value="ReportPdf" />
			<input type="hidden" name="mode" value="generateReportPdf" />
        	<input type="hidden" name="viewname" value="{$VIEWNAME}" />
            <input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
            <input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
            <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
            <input type="hidden" name="operator" value="{$OPERATOR}" />
            <input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
            <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
            
            <div class="modal-body">
                <div class="row">
                	<div class="col-md-4">	
                		<label>Select Report : </label>
                	</div>
                	<div class="clo-md-8">
	                     <select name="reportselect" id="reportselect" class="select2" style="width:50%;">
					        <option value="0" selected="selected">Report View...</option>
					        <option value="OmniOverview"  data-orientation="LETTER">Overview</option>
					        <option value="AssetClassReport" data-orientation="LETTER">Asset Class Report</option>
					        <option value="GainLoss" data-orientation="LETTER">Gain/Loss</option>
					        <option value="GHReport" data-orientation="LETTER">GH Report (Estimated Income)</option>
					        <option value="GHReportActual" data-orientation="LETTER">GH Report (Actual Income)</option>
					        <option value="GH2Report" data-orientation="LETTER-L">GH2 Report</option>
					        <option value="GHXReport" data-orientation="LETTER-L">GHX Report</option>
					        <option value="LastYearIncome" data-orientation="LETTER">Income - Last Year</option>
					        <option value="OmniProjected">Income Projected</option>
					        <option value="OmniIncome" data-orientation="LETTER">Income - Trailing 12</option>
					{*        <option value="GHReport" data-orientation="LETTER-L">Annual Summary</option>*}
					        {*<option value="GH2Report" data-orientation="LETTER">GH2 Report-P</option>*}
					        {*<option value="OmniIntervalsDaily">Intervals Daily</option>*}
					        {*<option value="OmniIntervals" >Intervals Monthly</option>*}
					        <option value="MonthOverMonth" >Month Over Month</option>
					        {*if $USER_MODEL->isAdminUser()}
					            <option value="PortfoliosReset" >--Portfolios Transaction Reset--</option>
					        {/if*}
					    </select>
				    </div>
                </div>
                 <div class="row omniOverview" style="margin-top:5px;display:none;">
                	<div class="col-md-4">
						<label>Selected Date : </label>
					</div>
                	<div class="col-md-8">	
                		<input type="text" class="select_end_date" id="select_end_date" name="omni_select_end_date" value="{date('Y-m-d')}" style="display:block; margin-left:-15px;" />
                	</div>
                </div>
                <div class="row dateselectiontable assetClassReport" style="margin-top:5px;display:none;">
                	<div class="col-md-4">
						<label>Selected Date : </label>
					</div>
                	<div class="col-md-8">	
                		 <select id="report_date_selection" name="report_date_selection" class="select2 report_date_selection" style=" margin-left:-15px;width:50%;">
	                        {foreach key=index item=option from=$ASSET_DATE_OPTIONS}
	                            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}" >{$option.option_name}</option>
	                        {/foreach}
	                    </select>
	                    
	                    <input type="text" class="select_end_date" id="select_end_date" name="asset_select_end_date" value="{$ASSET_END_DATE}" style="display:block; margin-left:-15px;margin-top: 5px;" />
                	</div>
				</div>
				<div class="row dateselectiontable gh2Report" style="margin-top:5px;display:none;">
                	<div class="col-md-4">
						<label>Selected Date : </label>
					</div>
                	<div class="col-md-8">	
                		<select id="report_date_selection" name="report_date_selection" class="select2 report_date_selection" style="margin-left:-15px; width:50%;">
	                        {foreach key=index item=option from=$GH2_DATE_OPTIONS}
	                            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}">{$option.option_name}</option>
	                        {/foreach}
	                    </select>
	                    <div style="display:block;">
		                    <input type="text" class="select_start_date" name="gh2_select_start_date" id="select_start_date" value="{$START_DATE}" style="width:50%; margin-left:-15px;margin-top:5px;" />
	                		<input type="text" class="select_end_date" name="gh2_select_end_date" id="select_end_date" value="{$END_DATE}" style="width:50%; margin-top:5px;" />
                		</div>
                	</div>
				</div>
				<div class="row dateselectiontable ghReport" style="margin-top:5px;display:none;">
                	<div class="col-md-4">
						<label>Selected Date : </label>
					</div>
                	<div class="col-md-8">	
                		<select id="report_date_selection" name="report_date_selection" class="select2 report_date_selection" style="margin-left:-15px;width:50%;">
	                        {foreach key=index item=option from=$GH_DATE_OPTIONS}
	                            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}">{$option.option_name}</option>
	                        {/foreach}
	                    </select>
	                    <div style="display:block;">
		                    <input type="text" class="select_start_date" name="select_start_date" id="select_start_date" value="{$START_DATE}" style="width:50%; margin-left:-15px;margin-top:5px;" />
	                		<input type="text" class="select_end_date" name="select_end_date" id="select_end_date" value="{$END_DATE}" style="width:50%; margin-top:5px;" />
                		</div>
                	</div>
				</div>
				{*<div class="row" style="margin-top:5px;">
                	<div class="col-md-12">
				        <table class="dateselectiontable omniOverview" style="display:none;">
				            <tr>
				            	<td><label>Selected Date : </label></td>
				                <td>
				                    <input type="text" class="select_end_date" id="select_end_date" name="omni_select_end_date" value="{date('Y-m-d')}" style="display:block; margin-left:5px; margin-right:5px;" />
				                </td>
				            </tr>
				        </table>
	
						<table class="dateselectiontable assetClassReport" style="display:none;">
				            <tr>
				            	<td><label>Selected Date : </label></td>
				                <td>
				                    <select id="report_date_selection" name="report_date_selection" class="select2 report_date_selection" style="border:2px solid black; margin-right:5px;width:110px;">
				                        {foreach key=index item=option from=$ASSET_DATE_OPTIONS}
				                            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}" >{$option.option_name}</option>
				                        {/foreach}
				                    </select>
				                </td>
			                    <td>
			                        <input type="text" class="select_end_date" id="select_end_date" name="asset_select_end_date" value="{$ASSET_END_DATE}" style="display:block; margin-left:5px; margin-right:5px;" />
			                    </td>
				            </tr>
				        </table>
				        
						 <table class="dateselectiontable gh2Report" style="display:none;">
				            <tr>
				            	<td><label>Selected Date : </label></td>
				                <td>
				                    <select id="report_date_selection" name="report_date_selection" class="select2 report_date_selection" style="border:2px solid black; margin-right:5px; width:110px;">
				                        {foreach key=index item=option from=$GH2_DATE_OPTIONS}
				                            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}">{$option.option_name}</option>
				                        {/foreach}
				                    </select>
				                </td>
			                    <td>
			                        <input type="text" class="select_start_date" name="gh2_select_start_date" id="select_start_date" value="{$START_DATE}" style="display:block; margin-right:5px;" />
			                    </td>
			                    <td>
			                        <input type="text" class="select_end_date" name="gh2_select_end_date" id="select_end_date" value="{$END_DATE}" style="display:block; margin-left:5px; margin-right:5px;" />
			                    </td>
				            </tr>
				        </table>
				        
				        <table class="dateselectiontable ghReport" style="display:none;">
				            <tr>
				            	<td><label>Selected Date : </label></td>
				                <td>
				                    <select id="report_date_selection" name="report_date_selection" class="select2 report_date_selection" style="border:2px solid black; margin-right:5px;width:110px;">
				                        {foreach key=index item=option from=$GH_DATE_OPTIONS}
				                            <option value="{$option.option_value}" data-start_date="{$option.date.start}" data-end_date="{$option.date.end}">{$option.option_name}</option>
				                        {/foreach}
				                    </select>
				                </td>
			                    <td>
			                        <input type="text" class="select_start_date" name="select_start_date" id="select_start_date" value="{$START_DATE}" style="display:block; margin-right:5px;" />
			                    </td>
			                    <td>
			                        <input type="text" class="select_end_date" name="select_end_date" id="select_end_date" value="{$END_DATE}" style="display:block; margin-left:5px; margin-right:5px;" />
			                    </td>
				            </tr>
				        </table>
			        </div>
                </div>*}
                <div class="row" style="margin-top:5px;">
					<div class="col-md-4">
						<label>User Email : </label>
					</div>
                	<div class="col-md-8">	
                		<input type="text" class="useremail" name="useremail" id="useremail" value="{$USER_EMAIL}" style="margin-left: -15px;" />
                		<i class="fa fa-question-circle cursorPointer" data-toggle="tooltip" data-placement="top" data-original-title="Testing For ToolTip"></i>
                	</div>
                </div>
            </div>
            <div>
                <div class="modal-footer">
                    <center>
                        <button class="btn btn-success ExportReport" type="submit" name="saveButton"><strong>{vtranslate('Generate PDF', $MODULE)}</strong></button>
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>
