{*/*<!--
/*********************************************************************************
 * The content of this file is subject to the VTE Reports ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 ********************************************************************************/
-->*/*}
<style>
    .vte_link{
        border: none;
        padding: 3px 10px 3px 10px;
        color: #fff;
        box-shadow: 1px 1px 4px #DADADA;
        -moz-box-shadow: 1px 1px 4px #DADADA;
        -webkit-box-shadow: 1px 1px 4px #DADADA;
        border-radius: 3px;
        -webkit-border-radius: 3px;
        -moz-border-radius: 3px;
        margin-right: 5px;
    }
    .vte_edit_link{
        background-color: #FF8500;
    }
    .vte_del_link{
        background-color: #E51400;
    }
</style>
{strip}
<input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
<input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
<input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
<input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
<input type="hidden" id="numberOfEntries" value= "{$LISTVIEW_ENTRIES_COUNT}" />
<input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
<input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}" >
<input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
<input type="hidden" id="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}">
<div id="selectAllMsgDiv" class="alert-block msgDiv">
	<strong><a id="selectAllMsg">{vtranslate('LBL_SELECT_ALL',$MODULE)}&nbsp;{vtranslate($MODULE ,$MODULE)}&nbsp;(<span id="totalRecordsCount"></span>)</a></strong>
</div>
<div id="deSelectAllMsgDiv" class="alert-block msgDiv">
	<strong><a id="deSelectAllMsg">{vtranslate('LBL_DESELECT_ALL_RECORDS',$MODULE)}</a></strong>
</div>

<div class="listViewEntriesDiv contents-bottomscroll">
	<div class="bottomscroll-div">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	<p class="listViewLoadingMsg hide">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table table-bordered listViewEntriesTable">
		<thead>
			<tr class="listViewHeaders">
				<th class="{$WIDTHTYPE}"><input type="checkbox" id="listViewEntriesMainCheckBox"></th>
				{foreach key=LISTVIEW_HEADER_KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
					<th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if} class="{$WIDTHTYPE}">
						<a href="javascript:void(0);" class="listViewHeaderValues" data-nextsortorderval="{if $COLUMN_NAME eq $LISTVIEW_HEADER_KEY}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER_KEY}">{vtranslate($LISTVIEW_HEADERS[$LISTVIEW_HEADER_KEY],$MODULE)}
						&nbsp;&nbsp;{if $COLUMN_NAME eq $LISTVIEW_HEADER_KEY}<img class="{$SORT_IMAGE} icon-white">{/if}</a>
					</th>
				{/foreach}
                <th></th>
			</tr>
		</thead>
        <tr class = "vte_fillter_row">
            <td class="{$WIDTHTYPE}"></td>
            {foreach key=LISTVIEW_HEADER_KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                <td nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if} class="{$WIDTHTYPE}">
                   {$LISTVIEW_FILTER[$LISTVIEW_HEADER_KEY]}
                </td>
            {/foreach}
            <td><button class="btn"  data-trigger="listSearch">{vtranslate('Search')}</button></td>
        </tr>
		{foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries" data-id={$LISTVIEW_ENTRY->getId()} data-recordUrl='{$LISTVIEW_ENTRY->getDetailViewUrl()}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
			<td class="{$WIDTHTYPE}"><input type="checkbox" value="{$LISTVIEW_ENTRY->getId()}" class="listViewEntriesCheckBox"></td>
			{foreach key=LISTVIEW_HEADER_KEY item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
				<td nowrap class="listViewEntryValue"  width="{$WIDTHTYPE}">
					<a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{vtranslate($LISTVIEW_ENTRY->get($LISTVIEW_HEADER_KEY), $MODULE)}</a>
					{if $LISTVIEW_HEADER@last}
						</td><td nowrap width="{$WIDTHTYPE}" colspan="2">
						<div class="pull-right actions">
							<span class="actionImages">
                                {foreach item=LINK from=$LISTVIEW_ENTRY->getRecordLinks()}
                                    {assign var=link_type value= $LINK->getIcon()}
                                    {if $link_type == "icon-pencil"}
                                        <a  class="vte_edit_link vte_link" href={$LINK->getUrl()}>{vtranslate("Edit",$QUALIFIED_MODULE)}</a>
                                    {/if}
                                    {if $link_type == "icon-trash"}
                                        <a class="deleteRecordButton vte_del_link vte_link" href="javascript:void(0);">{vtranslate("Delete",$QUALIFIED_MODULE)}</a>
                                    {/if}
                                {/foreach}
							</span>
						</div>
						</td>
					{/if}
				</td>
			{/foreach}
		</tr>
		{/foreach}
	</table>

<!--added this div for Temporarily -->
{if $LISTVIEW_ENTRIES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
					{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}.
				</td>
			</tr>
		</tbody>
	</table>
{/if}
</div>
</div>
{/strip}
