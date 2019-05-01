{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/Popup.php *}

{strip}
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="clearfix">
                    <div class="pull-right " >
                        <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                            <span aria-hidden="true" class='fa fa-close'></span>
                        </button>
                    </div>
                    <h4 class="pull-left">
                        {$MODULE}
                    </h4>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    {include file='PopupNavigation.tpl'|vtemplate_path:'Vtiger'}
                </div>
                <div id="popupPageContainer" class="contentsDiv col-sm-12">
                    <input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
                    <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
                    <input type="hidden" id="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}">
                    <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
                    <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
                    <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
                    <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
                    <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
                    <input type="hidden" value="{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($SEARCH_DETAILS))}" id="currentSearchParams" />
                    <div id="table-content" class="table-container" style="padding-top:0px !important;">
                        <table id="listview-table" class="workflow-table table listview-table">
                            {assign var="NAME_FIELDS" value=$MODULE_MODEL->getNameFields()}
                            {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
                            <thead>
                            <tr class="listViewContentHeader">
                                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                    {assign var="HEADER_NAME" value="{$LISTVIEW_HEADER->get('name')}"}
                                    {if $HEADER_NAME neq 'summary' && $HEADER_NAME neq 'module_name' && $HEADER_NAME neq 'test'}
                                        <th nowrap>
                                            <a {if !($LISTVIEW_HEADER->has('sort'))} class="listViewHeaderValues cursorPointer" data-nextsortorderval="{if $COLUMN_NAME eq $HEADER_NAME}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$HEADER_NAME}" {/if}>{vtranslate($LISTVIEW_HEADER->get('label'), $QUALIFIED_MODULE)}
                                                &nbsp;{if $COLUMN_NAME eq $HEADER_NAME}<img class="{$SORT_IMAGE} icon-white">{/if}</a>&nbsp;
                                        </th>
                                    {elseif $HEADER_NAME eq 'module_name' && empty($SOURCE_MODULE)}
                                        <th nowrap style="padding-left: 10px">
                                            <a {if !($LISTVIEW_HEADER->has('sort'))} class="listViewHeaderValues cursorPointer" data-nextsortorderval="{if $COLUMN_NAME eq $HEADER_NAME}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$HEADER_NAME}" {/if}>{vtranslate($LISTVIEW_HEADER->get('label'), $QUALIFIED_MODULE)}
                                                &nbsp;{if $COLUMN_NAME eq $HEADER_NAME}<img class="{$SORT_IMAGE} icon-white">{/if}</a>&nbsp;
                                        </th>
                                    {else}
                                    {/if}
                                {/foreach}
                                <th nowrap>{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
                                <tr class="listViewEntries" data-id="{$LISTVIEW_ENTRY->get('workflow_id')}" data-name="{$LISTVIEW_ENTRY->get('workflowname')}" data-info=""  id="{$MODULE}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">
                                    {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                        {assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
                                        {assign var=LAST_COLUMN value=$LISTVIEW_HEADER@last}
                                        {if $LISTVIEW_HEADERNAME neq 'summary' && $LISTVIEW_HEADERNAME neq 'module_name' && $LISTVIEW_HEADERNAME neq 'test'}
                                            <td class="listViewEntryValue {$WIDTHTYPE}" width="{$WIDTH}%" nowrap>
                                                {$LISTVIEW_ENTRY->getDisplayValue($LISTVIEW_HEADERNAME)}
                                            </td>
                                        {elseif $LISTVIEW_HEADERNAME eq 'module_name' && empty($SOURCE_MODULE)}
                                            <td class="listViewEntryValue {$WIDTHTYPE}" width="{$WIDTH}%" style="padding-left: 10px" nowrap>
                                                {assign var="MODULE_ICON_NAME" value="{strtolower($LISTVIEW_ENTRY->get('raw_module_name'))}"}
                                                {if $MODULE_ICON_NAME eq 'events'}
                                                    {assign var="MODULE_ICON_NAME" value="calendar"}
                                                {/if}
                                                <i class="vicon-{$MODULE_ICON_NAME}" title='{$LISTVIEW_ENTRY->get('module_name')}'>&nbsp;</i>
                                            </td>
                                        {else}
                                        {/if}
                                    {/foreach}
                                    <td class="listViewEntryValue {$WIDTHTYPE}" width="{$WIDTH}%" nowrap>
                                        {assign var=ACTIONS value=$LISTVIEW_ENTRY->getActionsDisplayValue()}
                                        {if is_array($ACTIONS) && !empty($ACTIONS)}
                                            {foreach item=ACTION_COUNT key=ACTION_NAME from=$ACTIONS}
                                                {vtranslate("LBL_$ACTION_NAME", $QUALIFIED_MODULE)}&nbsp;({$ACTION_COUNT})
                                            {/foreach}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                                <tr class="emptyRecordsDiv">
                                    {assign var=COLSPAN_WIDTH value={count($LISTVIEW_HEADERS)+1}}
                                    <td colspan="{$COLSPAN_WIDTH}" style="vertical-align:inherit !important;">
                                        <center>{vtranslate('LBL_NO')} {vtranslate($MODULE, $QUALIFIED_MODULE)} {vtranslate('LBL_FOUND')}</center>
                                    </td>
                                </tr>
                            {/if}
                            </tbody>
                        </table>
                    </div>
                    <div id="scroller_wrapper" class="bottom-fixed-scroll">
                        <div id="scroller" class="scroller-div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/strip}