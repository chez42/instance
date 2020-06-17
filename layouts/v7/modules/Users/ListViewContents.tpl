{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Users/views/List.php *}

{* START YOUR IMPLEMENTATION FROM BELOW. Use {debug} for information *}
{strip}
	<input type="hidden" id="listViewEntriesCount" value="{$LISTVIEW_ENTRIES_COUNT}" />
	<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
	<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
	<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
	<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
	<input type="hidden" id="pageNumberValue" value= "{$PAGE_NUMBER}"/>
	<input type="hidden" id="pageLimitValue" value= "{$PAGING_MODEL->getPageLimit()}" />
	<input type="hidden" id="numberOfEntries" value= "{$LISTVIEW_ENTRIES_COUNT}" />
	<input type="hidden" id="alphabetSearchKey" value= "{$MODULE_MODEL->getAlphabetSearchField()}" />
	<input type="hidden" id="Operator" value="{$OPERATOR}" />
	<input type="hidden" id="alphabetValue" value="{$ALPHABET_VALUE}" />
	<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
	<input type="hidden" name="orderBy" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" name="sortOrder" value="{$SORT_ORDER}" id="sortOrder">
	<input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
	<input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
	<input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">
	<input type="hidden" value="{$NO_SEARCH_PARAMS_CACHE}" id="noFilterCache" >
	<input type="hidden" name="cvid" value="{$VIEWID}" />
	 <input type="hidden" name="allCvId" value="{CustomView_Record_Model::getAllFilterByModule($MODULE)->get('cvid')}" />
	
	<div id="table-content" class="table-container">
		<form name='list' id='listedit' action='' onsubmit="return false;">
			<table id="listview-table" class="table {if $LISTVIEW_ENTRIES_COUNT eq '0'}listview-table-norecords {/if} listview-table">
				<thead>
					<tr class="listViewContentHeader">
						<th>
							{if !$SEARCH_MODE_RESULTS}
								{if Users_CustomView_Model::getNameBycvId({$VIEWID}) != 'LBL_INACTIVE_USERS'}	
									<div class="table-actions">
										<div class="dropdown" style="float:left;">
											<span class="input dropdown-toggle" data-toggle="dropdown" title="{vtranslate('LBL_CLICK_HERE_TO_SELECT_ALL_RECORDS',$MODULE)}">
												<input class="listViewEntriesMainCheckBox" type="checkbox">
											</span>
										</div>
									</div>
								{/if}	
							{elseif $SEARCH_MODE_RESULTS}
								{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}
							{/if}
						</th>
						{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
							
									{assign var=HEADER_LABEL value=$LISTVIEW_HEADER->get('label')}
							
								<th {if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')} nowrap="nowrap" {/if} >
									<a href="#" class="listViewContentHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('name')}">
										{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}
											<i class="fa fa-sort {$FASORT_IMAGE}"></i>
										{else}
											<i class="fa fa-sort customsort"></i>
										{/if}
										&nbsp;{vtranslate($HEADER_LABEL, $MODULE)}&nbsp;
									</a>
									{if $COLUMN_NAME eq $LISTVIEW_HEADER->get('name')}
										<a href="#" class="removeSorting"><i class="fa fa-remove"></i></a>
									{/if}
								</th>
							
						{/foreach}
					</tr>
				</thead>
				<tbody class="overflow-y">
					{if $MODULE_MODEL->isQuickSearchEnabled() && !$SEARCH_MODE_RESULTS}
						<tr class="searchRow">
							<th class="inline-search-btn">
								<div class="table-actions">
									<button class="btn btn-success btn-sm" data-trigger="listSearch">{vtranslate("LBL_SEARCH",$MODULE)}</button>
								</div>
							</th>
							{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
							
								{if $LISTVIEW_HEADER->getName() eq 'imagename' or $LISTVIEW_HEADER->getName() eq 'user_logo'}
									<th>&nbsp;</th>
								{else}
									<th>
										{assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
										{include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$MODULE) FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$CURRENT_USER_MODEL}
										<input type="hidden" class="operatorValue" value="{$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()]['comparator']}">
									</th>
								{/if}
							{/foreach}
						</tr>
					{/if}
					{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
						<tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}' data-recordUrl='{$LISTVIEW_ENTRY->getDetailViewUrl()}&parentblock=LBL_USER_MANAGEMENT' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
							<td class="listViewRecordActions">
								{include file="ListViewRecordActions.tpl"|vtemplate_path:$MODULE}
							</td>
							{foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
								{assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
								{assign var=LISTVIEW_ENTRY_RAWVALUE value=$LISTVIEW_ENTRY->getRaw($LISTVIEW_HEADER->get('column'))}
								{assign var=LISTVIEW_ENTRY_VALUE value=$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
								
								{if $LISTVIEW_HEADER->getName() eq 'imagename' }
									<td class="{$WIDTHTYPE}" nowrap>
										<span class="fieldValue">
											<span class="value textOverflowEllipsis">
												{assign var=IMAGE_DETAILS value=$LISTVIEW_ENTRY->getImageDetails()}
												{foreach item=IMAGE_INFO from=$IMAGE_DETAILS['imagename']}
													{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
														<div class='col-lg-2'>
															<img height="25px" width="25px" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}">
														</div>
													{/if}
												{/foreach}
												
											</span>
										</span>
									</td>
								{elseif $LISTVIEW_HEADER->getName() eq 'user_logo'}	
									<td class="{$WIDTHTYPE}" nowrap>
										<span class="fieldValue">
											<span class="value textOverflowEllipsis">
												{assign var=IMAGE_DETAILS value=$LISTVIEW_ENTRY->getImageDetails()}
												{foreach item=IMAGE_INFO from=$IMAGE_DETAILS['user_logo']}
													{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
														<div class='col-lg-2'>
															<img height="25px" width="25px" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}">
														</div>
													{/if}
												{/foreach}
												
											</span>
										</span>
									</td>
								{else}
									<td class="{$WIDTHTYPE}" nowrap>
										<span class="fieldValue">
											<span class="value textOverflowEllipsis">
												{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
											</span>
										</span>
									</td>
								{/if}
							{/foreach}
						</tr>
					{/foreach}
					{if $LISTVIEW_ENTRIES_COUNT eq '0'}
						<tr class="emptyRecordsDiv">
							{assign var=COLSPAN_WIDTH value={count($LISTVIEW_HEADERS)}}
							<td colspan="{$COLSPAN_WIDTH}">
								<div class="emptyRecordsContent">
									<center>
										{if $SEARCH_VALUE eq 'Active'}
											{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
											{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')},{if $IS_MODULE_EDITABLE} <a style="color:blue" href="{$MODULE_MODEL->getCreateRecordUrl()}"> {vtranslate('LBL_CREATE_USER',$MODULE)}</a>{/if}
										{else}
											{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}
										{/if}
									</center>
								</div>
							</td>
						</tr>
					{/if}
				</tbody>
			</table>
		</form>
	</div>
	<div id="scroller_wrapper" class="bottom-fixed-scroll">
		<div id="scroller" class="scroller-div"></div>
	</div>
{/strip}