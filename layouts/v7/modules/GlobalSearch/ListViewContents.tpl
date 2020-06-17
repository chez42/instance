{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Global Search ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <input type="hidden" id="pageStartRange{$SEARCH_MODULE}" value="{$PAGING_MODEL->getRecordStartRange()}" />
    <input type="hidden" id="pageEndRange{$SEARCH_MODULE}" value="{$PAGING_MODEL->getRecordEndRange()}" />
    <input type="hidden" id="previousPageExist{$SEARCH_MODULE}" value="{$PAGING_MODEL->isPrevPageExists()}" />
    <input type="hidden" id="nextPageExist{$SEARCH_MODULE}" value="{$PAGING_MODEL->isNextPageExists()}" />
    <input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber{$SEARCH_MODULE}'>
    <input type='hidden' value="{$VALUE}" id='searchKey{$SEARCH_MODULE}'>
    <input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit{$SEARCH_MODULE}'>
    <input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries{$SEARCH_MODULE}">
    <table class="table  listview-table  listViewEntriesTable">
        <thead>
        <tr class="listViewHeaders listViewContentHeader">
            <th>
                    {vtranslate('LBL_ACTIONS',$SEARCH_MODULE)}
            </th>
            {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                {if $SEARCH_MODULE eq 'Emails' && $LISTVIEW_HEADER->get('label') eq 'Description'} {continue} {/if}
                <th nowrap {if $LISTVIEW_HEADER@last} colspan="2" {/if}>
                    {vtranslate($LISTVIEW_HEADER->get('label'), $SEARCH_MODULE)}
                </th>
            {/foreach}
        </tr>
        </thead>
        <tbody class="overflow-y">
        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
            {assign var=MODULE_MODEL value=$LISTVIEW_ENTRY->getModule()}
            {assign var=QUICK_PREVIEW_ENABLED value=$MODULE_MODEL->isQuickPreviewEnabled()}
            <tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY->getId()}' data-searchmodule = '{$SEARCH_MODULE}'  data-recordUrl='{$LISTVIEW_ENTRY->getDetailViewUrl()}' id="{$SEARCH_MODULE}_listView_row_{$smarty.foreach.listview.index+1}">
                <td class = "listViewRecordActions">
                    {include file="ListViewRecordActions.tpl"|vtemplate_path:$SEARCH_MODULE}
                </td>
                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                    {if $SEARCH_MODULE eq 'Emails' && $LISTVIEW_HEADER->get('label') eq 'Description'} {assign var=HAS_DESCRIPTION value=1}{continue} {/if}
                    {assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}

                <td class="listViewEntryValue {$WIDTHTYPE}" data-field-type="{$LISTVIEW_HEADER->getFieldDataType()}" nowrap>
                    {if ($LISTVIEW_HEADER->isNameField() eq true or $LISTVIEW_HEADER->get('uitype') eq '4')}
                        {if $SEARCH_MODULE eq 'Emails' && $LISTVIEW_HEADERNAME eq 'subject'}
                            {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                        {else}
                            <a href="{$LISTVIEW_ENTRY->getDetailViewUrl()}">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}</a>
                        {/if}

                    {elseif $LISTVIEW_HEADER->get('uitype') eq '72'}
                        {assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
                        {if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
                            {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{$LISTVIEW_ENTRY->get('currencySymbol')}
                        {else}
                            {$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                        {/if}
                    {elseif $LISTVIEW_HEADER->get('uitype') eq '11'}
                        {assign var=PBXMANAGER_MODULE value='PBXManager'}
                        {assign var=MODULEMODEL value=Vtiger_Module_Model::getInstance($PBXMANAGER_MODULE)}
                        {if $MODULEMODEL}
                            {assign var=PERMISSION value=PBXManager_Server_Model::checkPermissionForOutgoingCall()}
                            {if $PERMISSION}
                                {assign var=PHONE_FIELD_VALUE value=$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)|strip_tags}
                                {assign var=PHONE_NUMBER value=$PHONE_FIELD_VALUE|regex_replace:"/[-()\s]/":""}
                                <a class="phoneField" data-value="{$PHONE_NUMBER}" record="{$LISTVIEW_ENTRY->getId()}" onclick="Vtiger_PBXManager_Js.registerPBXOutboundCall('{$PHONE_NUMBER}',{$LISTVIEW_ENTRY->getId()})">{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)|strip_tags}</a>
                            {else}
                                {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                            {/if}
                        {else}
                            {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                        {/if}
                    {else}
                        {if $LISTVIEW_HEADER->getFieldDataType() eq 'double'}
                            {decimalFormat($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME))}
                        {else}
                            {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                        {/if}
                    {/if}
                    {if $LISTVIEW_HEADER@last}
                        </td><td nowrap class="{$WIDTHTYPE}">
                        <div class="actions pull-right">
                                <span class="actionImages">
                                    <a href="{$LISTVIEW_ENTRY->getFullDetailViewUrl()}"><i title="{vtranslate('LBL_SHOW_COMPLETE_DETAILS', $SEARCH_MODULE)}" class="icon-th-list alignMiddle"></i></a>&nbsp;
                                    {if $IS_MODULE_EDITABLE}
                                        <a href='{$LISTVIEW_ENTRY->getEditViewUrl()}'><i title="{vtranslate('LBL_EDIT', $SEARCH_MODULE)}" class="icon-pencil alignMiddle"></i></a>&nbsp;
                                    {/if}
                                    {if $IS_MODULE_DELETABLE}
                                        <a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $SEARCH_MODULE)}" class="icon-trash alignMiddle"></i></a>
                                    {/if}
                                </span>
                        </div></td>
                    {/if}
                    </td>
                {/foreach}
            </tr>
            {if $SEARCH_MODULE eq 'Emails' && $HAS_DESCRIPTION eq 1}
                <tr class="listViewEntries">
                    <td class="fieldLabel"><strong>{vtranslate('Description', $SEARCH_MODULE)}</strong></td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" colspan="{$LISTVIEW_HEADERS|count}">{decode_html($LISTVIEW_ENTRY->get('description'))}</td>
                </tr>
            {/if}
        {/foreach}
        </tbody>

    </table>
    <!--added this div for Temporarily -->
    {if $LISTVIEW_ENTRIES_COUNT eq '0'}
        <table class="table table-bordered listViewEntriesTable">
            <tbody>
            <tr>
                <td>
                    {assign var=SINGLE_MODULE value="SINGLE_$SEARCH_MODULE"}
                    {vtranslate('LBL_NO')} {vtranslate($SEARCH_MODULE, $SEARCH_MODULE)} {vtranslate('LBL_FOUND')}.
                </td>
            </tr>
            </tbody>
        </table>
    {/if}
    <br/>
{/strip}
