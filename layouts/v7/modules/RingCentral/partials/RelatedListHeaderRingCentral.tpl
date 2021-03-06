{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}

    {foreach item=RELATED_LIST_MASSACTION from=$RELATED_LIST_MASSACTIONS name=massActions}
        
        {if $RELATED_LIST_MASSACTION->getLabel() eq 'LBL_EXPORT'}
            {assign var=exportAction value=$RELATED_LIST_MASSACTION}
            {* $a is added as its print the index of the array, need to find a way around it *}
        {/if}
        
    {/foreach}
	<div class="relatedHeader">
		<div class="btn-toolbar row">
			<div class="col-lg-12 col-md-12 col-sm-12 btn-toolbar">
				
			</div>
			{assign var=CLASS_VIEW_ACTION value='relatedViewActions'}
			{assign var=CLASS_VIEW_PAGING_INPUT value='relatedViewPagingInput'}
			{assign var=CLASS_VIEW_PAGING_INPUT_SUBMIT value='relatedViewPagingInputSubmit'}
			{assign var=CLASS_VIEW_BASIC_ACTION value='relatedViewBasicAction'}
			{assign var=PAGING_MODEL value=$PAGING}
			{assign var=RECORD_COUNT value=$RELATED_RECORDS|@count}
			{assign var=PAGE_NUMBER value=$PAGING->get('page')}
			
		</div>
		<div class="clearfix" style="margin: 5px;"></div>
		<div class = "row">
			<div class="col-md-2">
				{if $MODULE eq 'Contacts' || $MODULE eq 'Leads'}
					<div class="btn-group">
						<button class="btn btn-default" id="LBL_SEND_SMS" onclick="javascript:RingCentral_Js.triggerSendRingCentralSms('index.php?module=RingCentral&src_module={$MODULE}&view=MassActionAjax&mode=showSendRingCentralSMSForm&record={$PARENT_RECORD->getId()}');">Send SMS</button>
					</div>
				{/if}
			 	{if $exportAction}
	                <div class="btn-group relatedlistViewMassActions" role="group">
	                	<button type="button" class="btn btn-default relatedexport export" id={$MODULE}_reletedlistView_massAction_{$exportAction->getLabel()} 
	                            {if stripos($exportAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" url='{$exportAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$exportAction->getUrl()}' {/if} title="{vtranslate('LBL_EXPORT', $MODULE)}" >
	                        Export
	                    </button>
	                </div>    
                {/if}
			</div>	
		 	<div class="col-md-6">
			 	
            </div>
            {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
      	</div>          
	</div>
{/strip}