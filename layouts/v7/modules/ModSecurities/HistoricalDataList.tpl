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
	{assign var=RECORD_COUNT value=$PRICE_HISTORY|@count}
	{assign var=PAGE_NUMBER value=$PAGING->get('page')}
	
	<div class="historicalDataRelatedContainer" >
		
		<input type='hidden' id='pageNumber' value="{$PAGING_MODEL->get('page')}">
        <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
        <input type="hidden" id="noOfEntries" value="{$RELATED_ENTIRES_COUNT}">
        <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
        <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
        <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
        <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
		 <input type="hidden" id="totalCount" value="{$TOTAL_ENTRIES}" />
		<input type="hidden" name="currentPageNum" value="{$PAGING_MODEL->getCurrentPage()}" />
		<input type='hidden' value="{vtranslate('Historical Data', $MODULE_NAME)}" id='tab_label' name='tab_label'>
		
		<div class="relatedHeader" style = "padding:30px;">
			
			<div class = "row">
			
				<div class = "col-md-4 pull-left">
					<button type = "button" name = "addPrice" class = "btn btn-primary">
						<i class="ti-plus"></i>&nbsp;
						Add Price
					</button>
				</div>
			
				{if !empty($PRICE_HISTORY)}
						<div class="col-md-4">
							
						</div>       
						{include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
					</div>  
				{/if}
				
			</div>
			
		</div>

		<div class="relatedContents col-lg-12 col-md-12 col-sm-12 table-container">
			<div class="bottomscroll-div">
				<table id="listview-table" class="table listview-table">
					<thead>
						
						<tr class="listViewHeaders">
						
							<th style="min-width:100px">
							</th>
							<th class="nowrap">
								Date
							</th>
							<th class="nowrap">
								Price
							</th>
						</tr>
						<tr class="searchRow">
							<th class="inline-search-btn">
								<button class="btn btn-success btn-sm" data-trigger="relatedListSearch">{vtranslate("LBL_SEARCH",$MODULE)}</button>
							</th>
							<th>
								<div class="row-fluid">
							        <input type="text" name="date" class="listSearchContributor inputElement dateField" data-date-format="{$USER_MODEL->get('date_format')}" data-calendar-type="range" value="{$SEARCH_DETAILS['date']['searchValue']}" data-field-type="date"/>
							    </div>
								<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS['createddate']['comparator']}">
							</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					{if !empty($PRICE_HISTORY)}
						{foreach item=PRICE_HISTORY_DETAIL from=$PRICE_HISTORY}
							<tr class="listViewEntries" >
								<td class="related-list-actions">
	
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis" title="{$RECENT_ACTIVITY['date']}">
										{if $PRICE_HISTORY_DETAIL['date']}
											{$PRICE_HISTORY_DETAIL['date']|date_format:"%A, %B %e, %Y"}
										{/if}
									</span>
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis" title="{$PRICE_HISTORY_DETAIL['price']}">
										{if $PRICE_HISTORY_DETAIL['price']}
											{number_format($PRICE_HISTORY_DETAIL['price'],2)}
										{/if}
									</span>
								</td>
							</tr>
						
						            
						{/foreach}
					{else}
		                <tr class="summaryWidgetContainer">
		                    <td class="textAlignCenter" colspan=8><strong>{vtranslate('LBL_NO_RECENT_UPDATES')}</strong></td>
		                </tr>
		            {/if}    
				</table>
			</div>
		</div>
	</div>
{/strip}