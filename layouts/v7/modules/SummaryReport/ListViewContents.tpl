{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
<div class="listViewEntriesDiv contents-bottomscroll">
	<div class="bottomscroll-div">
	<input type="hidden" value="{$ORDER_BY}" id="orderBy">
	<input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
	<span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
		<img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
		<p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
	</span>
	{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
	<table class="table listViewEntriesTable table-hover listViewContentDiv" style="border: 1px solid #DDDDDD">
		<thead>
			<tr class="listViewHeaders">
                <th nowrap class="{$WIDTHTYPE}" style="border-right: 1px solid #DDDDDD">{vtranslate('LBL_REPORTNAME', $MODULE)}</th>
                <th colspan="2" nowrap class="{$WIDTHTYPE}">{vtranslate('LBL_DESCRIPTION', $MODULE)}</th>
			</tr>
		</thead>
		{foreach item=LISTVIEW_ENTRY key=ID from=$LISTVIEW_ENTRIES name=listview}
		<tr class="listViewEntries" data-id='{$ID}' id="{$MODULE}_listView_row_{$smarty.foreach.listview.index+1}" data-recordurl="index.php?module={$MODULE}&view=Detail&record={$ID}">
            <td class="listViewEntryValue {$WIDTHTYPE}"width="40%" style="border-right:  1px solid #DDDDDD;vertical-align:top !important;cursor: pointer;" nowrap class="medium" data-field-type="text" data-field-name="radius">{$LISTVIEW_ENTRY['reportname']}</td>
            <td class="listViewEntryValue {$WIDTHTYPE}" width="50%" data-field-type="text" data-field-name="radius" style="vertical-align:top !important;" nowrap>{$LISTVIEW_ENTRY['description']}</td>
            <td nowrap class="{$WIDTHTYPE}">
                <div class="actions pull-right">
                    <span class="actionImages">
                        <a href='index.php?module=SummaryReport&view=Edit&record={$ID}' class="btnEdit"><i title="{vtranslate('LBL_EDIT', $MODULE)}" class="glyphicon glyphicon-pencil alignMiddle"></i></a>&nbsp;
                        <a class="deleteRecordButton" data-id="{$ID}"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="glyphicon glyphicon-trash alignMiddle"></i></a>
                    </span>
                </div>
            </td>
		</tr>
		{/foreach}
	</table>

<!--added this div for Temporarily -->
{if $LISTVIEW_ENTIRES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{assign var=SINGLE_MODULE value="SINGLE_$MODULE"}
					{vtranslate('LBL_NO')} {vtranslate($MODULE, $MODULE)} {vtranslate('LBL_FOUND')}</a>
				</td>
			</tr>
		</tbody>
	</table>
{/if}
</div>
</div>
</div>
{/strip}
