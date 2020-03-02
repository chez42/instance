{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
<style>
	.listview-table tr.listViewContentHeader{
	    background: #f9f9f9;
	}
	.listview-table tr {
	    border: 1px solid #DDD;
	    border-bottom: 0px!important;
	}
	.listview-table:not(.stateContents) tr th:first-child {
	    width: inherit ! important;
	}
	.extensionContents .listview-table thead th:not(:last-child) {
	    border-right: thin solid #DDDDDD!important;
	}
	.extensionContents .listview-table thead th {
    	border-bottom: thin solid #DDDDDD!important;
	}
	#listview-table thead, #listview-table tbody {
     	display: contents !important;
    }
</style>
<div class="col-sm-12 col-xs-12 extensionContents">
	{if !$MODAL}
		<div class="row">
        	<div class="col-md-6">
        		<h3 class="module-title pull-left" style="margin-top:0px;">{vtranslate($MODULE,$MODULE)} - {vtranslate('LBL_SYNC_LOG', $MODULE)}</h3>
			</div>
        	<div class="col-md-6">
        		<div class="ext-actions pull-right">
        			<button type="button" data-url="{$MODULE_MODEL->getExtensionSettingsUrl($SOURCE_MODULE)|cat:'&returnToLogs=true'}" class="settingsPage btn btn-default">
                   		{vtranslate('LBL_SYNC_SETTINGS', $MODULE)}
                   	</button>
					<button type="button" class="revokeMSAccount btn btn-default">
                    	{vtranslate('LBL_REVOKE_ACCESS', $MODULE)}
                   	</button>
				</div>
        		
        	</div>
        </div>
    {/if}
   	<div class="marginTop15px">
   		<div class="row">
   			<div class="col-md-offset-4 col-md-8">
	   			<input type="hidden" name="pageStartRange" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" /> 
		        <input type="hidden" name="pageEndRange" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" /> 
		        <input type="hidden" name="previousPageExist" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" /> 
		        <input type="hidden" name="nextPageExist" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" /> 
		        <input type="hidden" name="totalCount" id="totalCount" value="{$TOTAL_RECORD_COUNT}" /> 
		        <input type='hidden' name="pageNumber" value="{$PAGING_MODEL->get('page')}" id='pageNumber'> 
		        <input type='hidden' name="pageLimit" value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'> 
		        <input type="hidden" name="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries"> 
		        <input type="hidden" value="{$SOURCE_MODULE}" id="source_module"> 
		        <input type="hidden" value="{$MODULE}" name="ext-module" id="ext-module" /> 
		        {assign var=RECORD_COUNT value=$TOTAL_RECORD_COUNT} 
	            {assign var=PAGE_NUMBER value=$PAGING_MODEL->get('page')} 
	            {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true} 
    		</div>
   		</div>
   	    
        <div id="table-content" class="table-container" style="border:0px;">
        	<table id="listview-table" class="table listview-table table-bordered" align="center">
	    	    <thead>
	                <tr class="listViewContentHeader">
	                    <th rowspan="2" class="align-middle" > {vtranslate('LBL_DATE', $MODULE)} </th>
	                    <th rowspan="2" class="align-middle"> {vtranslate('LBL_TIME', $MODULE)} </th>
	                    <th rowspan="2" class="align-middle"> {vtranslate('LBL_MODULE', $MODULE)} </th>
	                    <th colspan = "3" > {vtranslate('APPTITLE', $MODULE)} </th>
	                    <th colspan = "3" > {vtranslate($MODULE,$MODULE)} </th>
	                </tr>
	                <tr class="listViewContentHeader">
	                    <th> {vtranslate('Created', $MODULE)} </th>
	                    <th> {vtranslate('LBL_UPDATED', $MODULE)} </th>
	                    <th> {vtranslate('LBL_DELETED', $MODULE)} </th>
                        <th> {vtranslate('Created', $MODULE)} </th>
	                    <th> {vtranslate('LBL_UPDATED', $MODULE)} </th>
	                    <th> {vtranslate('LBL_DELETED', $MODULE)} </th>
	                </tr>
	            </thead>
	            <tbody>
	            	{if count($DATA) gt 0}
	            	{foreach item=LOG from=$DATA}
	                    <tr>
	                        <td>{$LOG['sync_date']} </td>
	                        <td>{$LOG['sync_time']} </td>
	                        <td>{vtranslate($LOG['module'], $LOG['module'])}</td>
	                        <td> <a class="{if $LOG['vt_create_count'] > 0} syncLogDetail extensionLink {/if}" data-type="vt_create" data-id="{$LOG['id']}"> {$LOG['vt_create_count']} </a> </td>
	                        <td> <a class="{if $LOG['vt_update_count'] > 0} syncLogDetail extensionLink {/if}" data-type="vt_update" data-id="{$LOG['id']}"> {$LOG['vt_update_count']} </a> </td>
	                        <td> <a class="{if $LOG['vt_delete_count'] > 0} syncLogDetail extensionError {/if}" data-type="vt_delete" data-id="{$LOG['id']}"> {$LOG['vt_delete_count']} </a></td>
	                        <td> <a class="{if $LOG['app_create_count'] > 0} syncLogDetail extensionLink {/if}" data-type="app_create" data-id="{$LOG['id']}"> {$LOG['app_create_count']} </a> </td>
	                        <td> <a class="{if $LOG['app_update_count'] > 0} syncLogDetail extensionLink {/if}" data-type="app_update" data-id="{$LOG['id']}"> {$LOG['app_update_count']} </a> </td>
	                        <td> <a class="{if $LOG['app_delete_count'] > 0} syncLogDetail extensionError {/if}" data-type="app_delete" data-id="{$LOG['id']}"> {$LOG['app_delete_count']} </a></td>
	                    </tr>
	                {/foreach}
	                {/if}
	                {if $LISTVIEW_ENTRIES_COUNT eq '0'}
	                    <tr class="emptyRecordsDiv">
	                        {assign var=COLSPAN_WIDTH value=12}
	                        <td colspan="{$COLSPAN_WIDTH}">
	                            <div class="emptyRecordsContent">
	                                <center> 
	                                    {vtranslate('LBL_NO')} {vtranslate('LBL_SYNC_LOG', $MODULE)} {vtranslate('LBL_FOUND')}. 
	                                    {if $IS_SYNC_READY}
	                                        <a href="#" class="syncNow"> <span class="blueColor"> {vtranslate('LBL_SYNC_NOW', $MODULE)} </span></a>
	                                    {else}
	                                        <a href="#" data-url="{$MODULE_MODEL->getExtensionSettingsUrl($SOURCE_MODULE)}" class="settingsPage"> <span class="blueColor"> {vtranslate('LBL_CONFIGURE', $MODULE)} {vtranslate('LBL_SYNC_SETTINGS', $MODULE)} </span></a>
	                                    {/if}
	                                </center>
	                            </div>
	                        </td>
	                    </tr>
	                {/if}
	            </tbody>
	        </table>
        </div>
   	</div>
   	{if !$MODAL}
   		{if $IS_SYNC_READY}
   			<div class="modal-overlay-footer clearfix">
				<div class="row clearfix">
					<div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
						<button id="Contacts_basicAction_LBL_Sync_Settings" type="submit" class="btn btn-success syncNow">
		                	<i class="fa fa-refresh"></i>
	                		<span>{vtranslate('LBL_SYNC_NOW', $MODULE)}</span>
		                </button>
					</div>
				</div>
			</div>
		{/if}
   	{/if}       
        