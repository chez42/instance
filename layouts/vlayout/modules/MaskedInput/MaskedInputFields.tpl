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
    {assign var=MASKEDINPUT_FIELDS_COUNT value=$MASKEDINPUT_FIELDS|count}
            <table class="table table-bordered listViewEntriesTable">
                <thead>
                    <tr>
                        <th colspan="5">
                            {vtranslate('LBL_CONFIGURED', 'MaskedInput')} {vtranslate('MaskedInput', 'MaskedInput')} {vtranslate('LBL_FIELD', 'MaskedInput')}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="listViewHeaders">
                        <td nowrap class="medium" style="font-weight: bold;">{vtranslate('LBL_MODULE', 'MaskedInput')}</td>
                        <td nowrap class="medium" style="font-weight: bold;">{vtranslate('LBL_FIELD', 'MaskedInput')}</td>
                        <td nowrap class="medium" style="font-weight: bold;">{vtranslate('MaskedInput', 'MaskedInput')}</td>
                        <td nowrap colspan="2" class="medium" style="font-weight: bold;">{vtranslate('LBL_ACTIVE', 'MaskedInput')}</td>
                    </tr>
                </tbody>
                {foreach item=MASKEDINPUT_FIELD key=ID from=$MASKEDINPUT_FIELDS name=maskedinput_fields_view}
                <tr class="listViewEntries" data-id='{$ID}' data-type="MaskedInputField" id="MaskedInput_field_listView_row_{$smarty.foreach.maskedinput_fields_view.index+1}">
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {vtranslate($MASKEDINPUT_FIELD['module'], $MASKEDINPUT_FIELD['module'])}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {vtranslate($MASKEDINPUT_FIELD['fieldname'], $MASKEDINPUT_FIELD['module'])}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$MASKEDINPUT_FIELD['masked_input']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {if $MASKEDINPUT_FIELD['active'] eq '1'}{vtranslate('LBL_YES')}{else}{vtranslate('LBL_NO')}{/if}
                    </td>
                    <td nowrap class="{$WIDTHTYPE}">
                        <div class="actions pull-right">
                            <span class="actionImages">
                                    <a href='javascript: void(0);' class="editRecordButton" data-url="index.php?module=MaskedInput&view=EditAjax&mode=getConfiguredFieldForm&record={$ID}"><i title="{vtranslate('LBL_EDIT')}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                                    <a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE')}" class="icon-trash alignMiddle"></i></a>
                            </span>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </table>
            <!--added this div for Temporarily -->
            {if $MASKEDINPUT_FIELDS_COUNT eq '0'}
                <table class="table table-bordered listViewEntriesTable">
                    <tbody>
                        <tr>
                            <td>
                                {vtranslate('LBL_NO')} {vtranslate('LBL_CONFIGURED', 'MaskedInput')} {vtranslate('LBL_FIELD', 'MaskedInput')} {vtranslate('LBL_FOUND')}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            {/if}
            <br/>
    {*</div>
</div>*}
{/strip}
