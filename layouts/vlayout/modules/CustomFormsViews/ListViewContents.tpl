{*/* ********************************************************************************
* The content of this file is subject to the Custom Forms & Views ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
<div class="listViewEntriesDiv" style='overflow-x:auto;'>
    <table class="table table-bordered table-condensed listViewEntriesTable">
        <thead>
            <tr class="listViewHeaders">
                <th class="medium">{vtranslate('LBL_MODULE',$QUALIFIED_MODULE)}</th>
                <th class="medium">{vtranslate('LBL_NAME',$QUALIFIED_MODULE)}</th>
                <th class="medium" colspan="2">{vtranslate('LBL_STATUS',$QUALIFIED_MODULE)}</th>
            </tr>
        </thead>
        <tbody>
        {assign var=LISTVIEW_ENTRIES_COUNT value=$LISTVIEW_ENTRIES|count}
        {foreach item=LISTVIEW_ENTRY key=RECORDID from=$LISTVIEW_ENTRIES}
            <tr class="listViewEntries" data-url="index.php?module=CustomFormsViews&view=EditView&parent=Settings&record={$RECORDID}">
                <td width="30%" style="vertical-align:top !important;" nowrap class="medium">{vtranslate($LISTVIEW_ENTRY['module'], $LISTVIEW_ENTRY['module'])}</td>
                <td width="30%" style="vertical-align:top !important;" nowrap class="medium">{$LISTVIEW_ENTRY['custom_name']}</td>
                <td width="30%" style="vertical-align:top !important;" nowrap class="medium">{$LISTVIEW_ENTRY['status']}</td>
                <td nowrap class="{$WIDTHTYPE}">
                    <div class="actions pull-right">
                            <span class="actionImages">
                                    <a href='javascript: void(0);' class="editRecordButton" data-url="index.php?module=CustomFormsViews&view=EditView&parent=Settings&record={$RECORDID}"><i title="{vtranslate('LBL_EDIT')}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                                    <a class="deleteRecordButton" data-id="{$RECORDID}"><i title="{vtranslate('LBL_DELETE')}" class="icon-trash alignMiddle"></i></a>
                            </span>
                    </div>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>

	<!--added this div for Temporarily -->
	{if $LISTVIEW_ENTRIES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{vtranslate('LBL_NO')} {vtranslate($MODULE, $QUALIFIED_MODULE)} {vtranslate('LBL_FOUND')}
				</td>
			</tr>
		</tbody>
	</table>
	{/if}
</div>
{/strip}