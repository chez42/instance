{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}

<div id="getPortfolioList" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        {assign var=TITLE value="{vtranslate('Select Portfolio List', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="getPortfolioViews" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="view" value="BillingReportPdf" />
			<input type="hidden" name="mode" value="GenrateLink" />
        	
            
            <div class="modal-body">
                <div class="row">
                	<div class="col-md-4">	
                		<label>Select Portfolio List : </label>
                	</div>
                	<div class="clo-md-8">
	                     <select name="viewid" id="viewid" class="select2" style="width:50%;" data-rule-required="true">
	                     	<option value="" selected="selected">Select List</option>
	                     	{foreach item=CUSTOM_VIEW from=$CUSTOM_VIEWS}
	                     		<option value="{$CUSTOM_VIEW->getId()}">{vtranslate($CUSTOM_VIEW->get('viewname'), $MODULE)}</option>
	                     	{/foreach}
					    </select>
				    </div>
                </div>
				 <div class="row">
                	<div class="col-md-4">	
                		<label>Prorate Capital Flows</label>
                	</div>
                	<div class="clo-md-8">
	                     <input type = "checkbox" name = "proratecapitalflows" value = "1" checked/>
				    </div>
                </div>
            </div>
            <div>
                <div class="modal-footer">
                    <center>
                        <button class="btn btn-success ExportReport" type="submit" name="saveButton"><strong>{vtranslate('Generate Statement', $MODULE)}</strong></button>
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>
