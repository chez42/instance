{*/* ********************************************************************************
* The content of this file is subject to the Hide Fields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
<div class="listViewEntriesDiv" style='overflow-x:auto;'>
    {foreach item=LISTVIEW_ENTRY key=RECORDID from=$LISTVIEW_ENTRIES}
        <div style="margin-bottom:20px;" class="blockSortable" data-id="{$RECORDID}" data-sequence="{$LISTVIEW_ENTRY['sequence']}">
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr class="listViewHeaders">
                        <th width="24%" class="medium">{vtranslate('LBL_MODULE',$QUALIFIED_MODULE)}</th>
                        <th width="25%" class="medium">{vtranslate('LBL_FIELDS',$QUALIFIED_MODULE)}</th>
                        <th width="20%" class="medium">{vtranslate('LBL_SYMBOL',$QUALIFIED_MODULE)}</th>
                        <th width="20%" class="medium">{vtranslate('LBL_STATUS',$QUALIFIED_MODULE)}</th>
                        <th width="10%" class="medium">
                            <span class="btn-group actions">
                                <a class="editBlockDetails" href='javascript: void(0);' data-url="index.php?module=HideFields&view=EditAjax&mode=getEditForm&record={$RECORDID}">
                                    <i title="Edit" class="fa fa-pencil alignMiddle"></i>
                                </a>
                                &nbsp;&nbsp;
                                <a class="deleteBlock" href="javascript:void(0);" data-id="{$RECORDID}">
                                    <i title="Delete" class="fa fa-trash alignMiddle"></i>
                                </a>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="listViewEntries">
                        <td width="30%" style="vertical-align:top !important;" nowrap class="medium">{vtranslate($LISTVIEW_ENTRY['module'], $LISTVIEW_ENTRY['module'])}</td>
                        <td width="20%" style="vertical-align:top !important;" nowrap class="medium">
                            {foreach item=FIELD from=$LISTVIEW_ENTRY['fields']}
                                <div class="row-fluid">
                                    {if is_object($FIELD) eq TRUE}
                                        <span>{vtranslate($FIELD->get('label'),$LISTVIEW_ENTRY['module'])}</span>
                                    {else}
                                        <span>{vtranslate($FIELD,$LISTVIEW_ENTRY['module'])}</span>
                                    {/if}

                                </div>
                            {/foreach}
                        </td>
                        <td width="20%" style="vertical-align:top !important;" nowrap class="medium">
                            {vtranslate($LISTVIEW_ENTRY['symbol'], $LISTVIEW_ENTRY['module'])}
                        </td>
                        <td width="20%" style="vertical-align:top !important;" nowrap class="medium"  colspan="2" >{if $LISTVIEW_ENTRY['status'] eq '1'}{vtranslate('LBL_ACTIVE',$QUALIFIED_MODULE)}{else}{vtranslate('LBL_INACTIVE',$QUALIFIED_MODULE)}{/if}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    {/foreach}

	<!--added this div for Temporarily -->
	{if $LISTVIEW_ENTRIES_COUNT eq '0'}
	<table class="emptyRecordsDiv">
		<tbody>
			<tr>
				<td>
					{vtranslate('LBL_NO')} {vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)} {vtranslate('LBL_FOUND')}
				</td>
			</tr>
		</tbody>
	</table>
	{/if}
</div>
{/strip}