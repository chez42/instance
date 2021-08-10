{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}
    
{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
    <div class="modal-dialog">
        <div class="modal-content">
            <form class="form-horizontal" id="downloadReport" name="downloadReport" method="post" action="index.php">
                
				<input type="hidden" name="module" value="PortfolioInformation" />
                <input type="hidden" name="view" value="{$TYPE}" />
				<input type="hidden" name="mode" value="DownloadReport" />
				
				<input type="hidden" name="viewname" value="{$CVID}" />
				<input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
				<input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
				<input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
				<input type="hidden" name="operator" value="{$OPERATOR}" />
				<input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
				<input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
				
				{assign var=HEADER_TITLE value={vtranslate('Download Report', $MODULE)}}
				
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
                
                <div class="modal-body">
                    
					{if $TYPE neq 'PerformanceReport'}
					<div class="form-group">
						<label class="col-lg-4 control-label">{vtranslate('From Date',$MODULE)}</label>
						<div class="col-lg-6">
							<input type = "text" name = "report_start_date" class = "form-control dateField"  data-rule-required="true" autocomplete="off" />
						</div>
                    </div>
					{/if}
					
                    <div class="form-group">
                       <label class="col-lg-4 control-label">{vtranslate('To Date', $MODULE)}</label>
                       <div class="col-lg-6">
                              <input type = "text" name = "report_end_date" class = "dateField form-control"   data-rule-required="true" autocomplete="off"/>  
                       </div>
                    </div>
                </div>
				{assign var=BUTTON_NAME value={vtranslate('Download Report', $MODULE)}}
                {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
            </form>
        </div>
    </div>
{/strip}