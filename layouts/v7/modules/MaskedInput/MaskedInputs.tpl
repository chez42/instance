{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Masked Input ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
    {assign var=MASKEDINPUTS_COUNT value=$MASKEDINPUTS|count}
    <table class="table table-bordered listViewEntriesTable">
        <thead>
        <tr>
            <th colspan="5">
                {vtranslate('MaskedInputs', 'MaskedInput')}
            </th>
        </tr>
        </thead>
        <tbody>
        <tr class="listViewHeaders">
            <td nowrap class="medium" style="font-weight: bold;">{vtranslate('MaskedInput', 'MaskedInput')}</td>
            <td nowrap colspan="2" class="medium" style="font-weight: bold;">{vtranslate('LBL_ALERT', 'MaskedInput')}</td>
        </tr>
        </tbody>
        {foreach item=MASKEDINPUT key=ID from=$MASKEDINPUTS name=maskedinput_view}
            <tr class="listViewEntries" data-id='{$ID}' data-type="MaskedInput" id="MaskedInput_listView_row_{$smarty.foreach.maskedinput_view.index+1}">
                <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                    {$MASKEDINPUT['masked_input']}
                </td>
                <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                    <span style="overflow: hidden;">
                    {$MASKEDINPUT['alert_text']}
                    </span>
                </td>
                <td nowrap class="{$WIDTHTYPE}">
                    <div class="actions pull-right">
                            <span class="actionImages">
                                    <a href='javascript: void(0);' class="editRecordButton" data-url="index.php?module=MaskedInput&view=EditAjax&mode=getCustomInputForm&record={$ID}"><i title="{vtranslate('LBL_EDIT')}" class="glyphicon glyphicon-pencil alignMiddle"></i></a>&nbsp;
                                    <a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE')}" class="glyphicon glyphicon-trash alignMiddle"></i></a>
                            </span>
                    </div>
                </td>
            </tr>
        {/foreach}
    </table>
    <!--added this div for Temporarily -->
    {if $MASKEDINPUTS_COUNT eq '0'}
        <table class="table table-bordered listViewEntriesTable">
            <tbody>
            <tr>
                <td>
                    {vtranslate('LBL_NO')} {vtranslate('LBL_FIELD', 'MaskedInput')} {vtranslate('LBL_FOUND')}.
                </td>
            </tr>
            </tbody>
        </table>
    {/if}
    <br/>
    {*</div>
</div>*}
{/strip}
