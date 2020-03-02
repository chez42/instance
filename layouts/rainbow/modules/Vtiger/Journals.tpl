{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}


{strip}	
	<style>
		#listview-table thead, #listview-table tbody {
	     	display: contents !important;
	    }
	</style>
    {assign var=PAGING_MODEL value=$PAGING}
	{assign var=RECORD_COUNT value=$RECENT_ACTIVITIES|@count}
	{assign var=PAGE_NUMBER value=$PAGING->get('page')}
	
	<div class="journalsrelatedContainer">
		
		<input type='hidden' id='pageNumber' value="{$PAGING_MODEL->get('page')}">
        <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
        <input type="hidden" id="noOfEntries" value="{$RELATED_ENTIRES_COUNT}">
        <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
        <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
        <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
        <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
		 <input type="hidden" id="totalCount" value="{$TOTAL_ENTRIES}" />
		<input type="hidden" name="currentPageNum" value="{$PAGING_MODEL->getCurrentPage()}" />
		<input type='hidden' value="{vtranslate('LBL_JOURNAL', $MODULE_NAME)}" id='tab_label' name='tab_label'>
		
		<div class="relatedHeader">
			{if !empty($RECENT_ACTIVITIES)}
				<div class = "row">
					<div class="col-md-4">
						<div class="btn-group journalViewMassActions" role="group">
		                	<button type="button" class="btn btn-default journalexport export" id="{$MODULE_NAME}_JournalView_massAction_Export "
		                           href='index.php?module={$MODULE_NAME}&view=Journal&mode=Export'  title="{vtranslate('LBL_EXPORT', $MODULE_NAME)}" >
		                        {vtranslate('LBL_EXPORT', $MODULE_NAME)}
		                    </button>
		                </div>
		            </div>       
		            {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
		      	</div>  
	      	{/if}        
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
								Type
							</th>
							<th class="nowrap">
								Creator
							</th>
							<th class="nowrap">
								Subject
							</th>
							<th class="nowrap">
								Description
							</th>
							<th class="nowrap">
								Status
							</th>
						</tr>
						<tr class="searchRow">
							<th class="inline-search-btn">
								<button class="btn btn-danger btn-sm" data-trigger="clearSearch" style="width: 40%;">{vtranslate("Clear",$MODULE)}</button>&nbsp;
								<button class="btn btn-success btn-sm" data-trigger="relatedListSearch"  style="width: 54%;">{vtranslate("LBL_SEARCH",$MODULE)}</button>
							</th>
							<th>
								<div class="row-fluid">
							        <input type="text" name="createddate" class="listSearchContributor inputElement dateField" data-date-format="{$USER_MODEL->get('date_format')}" data-calendar-type="range" value="{$SEARCH_DETAILS['createddate']['searchValue']}" data-field-type="date"/>
							    </div>
								<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS['createddate']['comparator']}">
							</th>
							<th>
								{assign var=SEARCH_VALUES value=explode(',',$SEARCH_DETAILS['module']['searchValue'])}
								 <div class="select2_search_div">
									<select class="select2 listSearchContributor " name="module" multiple data-field-type="picklist" >
										<option value=''></option>
										<option value='Calendar' {if in_array('Calendar',$SEARCH_VALUES)} selected {/if}>Event</option>
										<option value="Task" {if in_array('Task',$SEARCH_VALUES)} selected {/if}>Task</option>
										<option value='HelpDesk' {if in_array('HelpDesk',$SEARCH_VALUES)} selected {/if}>Tickets</option>
										<option value="Emails" {if in_array('Emails',$SEARCH_VALUES)} selected {/if}>Email</option>
										<option value='ModComments' {if in_array('ModComments',$SEARCH_VALUES)} selected {/if}>Comment</option>
										<option value='Documents' {if in_array('Documents',$SEARCH_VALUES)} selected {/if}>Documents</option>
										<option value='RingCentral' {if in_array('RingCentral',$SEARCH_VALUES)} selected {/if}>RingCentral</option>
									</select>
								</div>
								<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS['module']['comparator']}">
							</th>
							<th>
								 {assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
								 {assign var=CREATOR_SEARCH_VALUES value=explode(',',$SEARCH_DETAILS['creator']['searchValue'])}
								 
								 <div class="select2_search_div">
								 	<select class="select2 listSearchContributor creator" name="creator" multiple data-field-type="picklist">
										<optgroup label="{vtranslate('LBL_USERS')}">
											{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
								                    <option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {if in_array($OWNER_ID,$CREATOR_SEARCH_VALUES)} selected {/if}
														data-userId="{$CURRENT_USER_ID}">
								                    {$OWNER_NAME}
								                    </option>
											{/foreach}
										</optgroup>
									</select>	
								 </div>
								 <input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS['creator']['comparator']}">
							</th>
							<th>
								 <div class="">
							        <input type="text" name="subject" class="listSearchContributor inputElement" value="{$SEARCH_DETAILS['subject']['searchValue']}" data-field-type="string" />
							    </div>
								<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS['subject']['comparator']}">
							</th>
							<th>
								 <div class="">
							        <input type="text" name="description" class="listSearchContributor inputElement" value="{$SEARCH_DETAILS['description']['searchValue']}" data-field-type="string" />
							    </div>
								<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS['description']['comparator']}">
							</th>
							<th>
							</th>
						</tr>
					</thead>
					{if !empty($RECENT_ACTIVITIES)}
						{foreach item=RECENT_ACTIVITY from=$RECENT_ACTIVITIES}
							<tr class="listViewEntries" data-id='{$RECENT_ACTIVITY->get('crmid')}' 
								{if $RECENT_ACTIVITY->get('module') neq 'Emails' }
									{assign var=DETAILVIEWPERMITTED value=isPermitted($RECENT_ACTIVITY->get('module'), 'DetailView', $RECENT_ACTIVITY->get('crmid'))}
									{if $DETAILVIEWPERMITTED eq 'yes'}
										{assign var=recordInstance value=Vtiger_Record_Model::getInstanceById($RECENT_ACTIVITY->get('crmid'))}
	                    				data-recordUrl='{$recordInstance->getDetailViewUrl()}'
	                    			{/if}
	                    		{else if $RECENT_ACTIVITY->get('module') eq 'Emails' }	
	                    			name="emailsRelatedRecord"
                    			{/if}
                    			>
								<td class="related-list-actions">
	
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis">
										{$RECENT_ACTIVITY->get('createddate')|date_format:"%A, %B %e, %Y"}
									</span>
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis">
										{if $RECENT_ACTIVITY->get('module') eq 'Calendar'}
											{vtranslate('Events', $RECENT_ACTIVITY->get('module'))}
										{else}
											{vtranslate($RECENT_ACTIVITY->get('module'), $RECENT_ACTIVITY->get('module'))}
										{/if}
									</span>
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis">
										{getUserFullName($RECENT_ACTIVITY->get('creator'))}
									</span>
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis">
										{if $RECENT_ACTIVITY->get('module') neq 'RingCentral' }
											{wordwrap(html_entity_decode($RECENT_ACTIVITY->get('subject')),75,"<br>\n")}
										{else}
											N/A
										{/if}
									</span>
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis" title="{strip_tags(html_entity_decode($RECENT_ACTIVITY->get('description')))}">
										{if $RECENT_ACTIVITY->get('module') eq 'ModComments' }
											N/A
										{else}
											{wordwrap(substr(strip_tags(html_entity_decode($RECENT_ACTIVITY->get('description'))),0,150),75,"<br>\n")}
										{/if}
									</span>
								</td>
								<td class="relatedListEntryValues" nowrap>
									<span class="value textOverflowEllipsis">
										
										{if $RECENT_ACTIVITY->get('module') eq 'ModComments' || $RECENT_ACTIVITY->get('module') eq 'Emails' ||
										$RECENT_ACTIVITY->get('module') eq 'Documents'}
											N/A
										{else}
											{$RECENT_ACTIVITY->get('status')}
										{/if}	
										
									</span>
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
	</div>
{/strip}