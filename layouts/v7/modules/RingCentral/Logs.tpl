{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}


{strip}	
	
    {assign var=PAGING_MODEL value=$PAGING}
	{assign var=RECORD_COUNT value=$RECENT_LOGS|@count}
	{assign var=PAGE_NUMBER value=$PAGING->get('page')}
	
	<div class="logsrelatedContainer">
		
		<input type='hidden' id='pageNumber' value="{$PAGING_MODEL->get('page')}">
        <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
        <input type="hidden" id="noOfEntries" value="{$RELATED_ENTIRES_COUNT}">
        <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
        <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
        <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
        <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
		 <input type="hidden" id="totalCount" value="{$TOTAL_ENTRIES}" />
		<input type="hidden" name="currentPageNum" value="{$PAGING_MODEL->getCurrentPage()}" />
		
		<div class="relatedHeader">
			<div class = "row" style="margin-right: 0px !important;">
				<div class="col-md-6">
					<h4 class="fieldBlockHeader">Logs</h4>
				</div>
				<div class="col-md-6">
					{if !empty($RECENT_LOGS)}
			            {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
		      		{/if}  
	      		</div> 
      		</div>       
		</div>
		<hr>
		<div id="table-content" class="table-container">
			<table id="log-table" class="table {if $RECENT_LOGS eq '0'}listview-table-norecords {/if} listview-table ">
				<thead>
					
					<tr class="listViewContentHeader">
					
						{*<th style="min-width:50px">
						</th>*}
						<th class="nowrap">
							Contact
						</th>
						{*<th class="nowrap">
							Type
						</th>*}
						<th class="nowrap">
							Content
						</th>
						<th class="nowrap">
							To Number
						</th>
						<th class="nowrap">
							Status
						</th>
						<th class="nowrap">
							User
						</th>
						<th class="nowrap">
							Date
						</th>
						<th style="min-width:50px">
						</th>
					</tr>
					
				</thead>
				{if !empty($RECENT_LOGS)}
					{foreach item=RECENT_LOG from=$RECENT_LOGS}
						<tr class="listViewEntries" data-id='{$RECENT_LOG['crmid']}'>
							{*<td class="related-list-actions">

							</td>*}
							<td class="relatedListEntryValues" nowrap>
								<span class="value textOverflowEllipsis">
									{assign var=recordModel value= Vtiger_Record_Model::getInstanceById($RECENT_LOG['crmid'])}
									<a href="{$recordModel->getDetailViewUrl()}" title='Contacts'>{getContactName($RECENT_LOG['crmid'])}</a>
								</span>
							</td>
							{*<td class="relatedListEntryValues" nowrap>
								<span class="value textOverflowEllipsis">
									{$RECENT_LOG['type']}
								</span>
							</td>*}
							<td class="relatedListEntryValues" nowrap>
								<span class="value textOverflowEllipsis">
									{wordwrap($RECENT_LOG['content'],75,"<br>\n")}
								</span>
							</td>
							<td class="relatedListEntryValues" nowrap>
								<span class="value textOverflowEllipsis">
									{$RECENT_LOG['tono']}
								</span>
							</td>
							<td class="relatedListEntryValues status" nowrap>
								<span class="value textOverflowEllipsis">
									{$RECENT_LOG['status']}
								</span>
							</td>
							<td class="relatedListEntryValues" nowrap>
								<span class="value textOverflowEllipsis">
									{getUserFullName($RECENT_LOG['user_id'])}
								</span>
							</td>
							<td class="relatedListEntryValues" nowrap>
								<span class="value textOverflowEllipsis">
									{$RECENT_LOG['created_date']|date_format:"%A, %B %e, %Y"}
								</span>
							</td>
							<td class="related-list-actions">
								<button class="btn btn-success btn-sm updateStatus" data-crmid="{$RECENT_LOG['crmid']}" data-ringcentralid="{$RECENT_LOG['ringcentral_id']}">Update Status</button>
							</td>
						</tr>
					
					            
					{/foreach}
				{else}
	                <tr class="summaryWidgetContainer">
	                    <td class="textAlignCenter" colspan=7><strong>{vtranslate('LBL_NO_RECENT_UPDATES')}</strong></td>
	                </tr>
	            {/if}    
			</table>
		</div>
	</div>
{/strip}