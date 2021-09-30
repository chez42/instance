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
            <form class="form-horizontal" id="addpriceform" name="addpriceform" method="post" action="index.php">
                
				<input type="hidden" name="module" value="ModSecurities" />
                <input type="hidden" name="action" value="SavePrice" />
				<input type="hidden" name="modsecurityid" value="{$RECORD_ID}" />
				
                {assign var=HEADER_TITLE value={vtranslate($TITLE, $MODULE)}}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
                
                <div class="modal-body">
                    <div class="form-group">
						<label class="col-lg-4 control-label">{vtranslate('Price',$MODULE)}</label>
						<div class="col-lg-6">
							<input type = "text" name = "price" class = "form-control" value = "{$SECURITY_PRICE}" data-rule-required="true" />
						</div>
                    </div>
                    <div class="form-group">
                       <label class="col-lg-4 control-label">{vtranslate('Date', $MODULE)}</label>
                       <div class="col-lg-6">
                              <input type = "text" name = "price_date" class = "dateField form-control" value = "{$SECURITY_PRICE_DATE}"  data-rule-required="true"/>  
                       </div>
                    </div>
                </div>
                {include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
            </form>
        </div>
    </div>
{/strip}