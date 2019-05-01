{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Google Address ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
<div class="listViewEntriesDiv contents-bottomscroll" style="border:1px solid #ccc;">
    <div class="bottomscroll-div">
        <input type="hidden" value="{$ORDER_BY}" id="orderBy">
        <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
        <span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
            <img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
            <p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
        </span>
        {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
        {assign var=LISTVIEW_ENTRIES_COUNT value=$LISTVIEW_ENTRIES|count}

            <table class="table  listview-table  floatThead-table">
                <thead>
                    <tr class="listViewHeaders">
                        <td style="width: 1%;"></td>
                        <th nowrap class="medium">{vtranslate('LBL_MODULE', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_ADDRESS_NAME', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_STREET', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_CITY', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_SUBLOCALITY', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_STATE', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_POSTAL_CODE', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_COUNTRY', 'GoogleAddress')}</th>
                        <th nowrap class="medium">{vtranslate('LBL_STATUS', 'GoogleAddress')}</th>
                    </tr>
                </thead>
                {foreach item=LISTVIEW_ENTRY key=ADDRESSID from=$LISTVIEW_ENTRIES name=listview}
                <tr class="listViewEntries" data-id='{$LISTVIEW_ENTRY['id']}' id="GoogleAddress_listView_row_{$smarty.foreach.listview.index+1}">
                    <td style="width: 1%;">
                        <div class="table-actions" style="width: 30px !important;">
                            <span class="more dropdown action">
                                <span href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-ellipsis-v icon"></i></span>
                                <ul class="dropdown-menu">
                                    <li><a href='javascript: void(0);' class="editAddressButton" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getEditForm&record={$LISTVIEW_ENTRY['id']}">{vtranslate('LBL_EDIT', $MODULE)}</a></li>
                                    <li><a class="deleteRecordButton">{vtranslate('LBL_DELETE', $MODULE)}</a></li>
                                </ul>
                            </span>
                        </div>
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {vtranslate($LISTVIEW_ENTRY['module_name'], $LISTVIEW_ENTRY['module'])}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['address_name']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['street']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['city']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['sublocality']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['state']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['postal_code']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['country']}
                    </td>
                    <td class="listViewEntryValue {$WIDTHTYPE}" nowrap>
                        {$LISTVIEW_ENTRY['status']}
                    </td>
                </tr>
                {/foreach}
            </table>
            <!--added this div for Temporarily -->
            {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                <table class="table table-bordered listViewEntriesTable">
                    <tbody>
                        <tr>
                            <td>
                                {vtranslate('LBL_NO')} {vtranslate('LBL_ADDRESS', 'GoogleAddress')} {vtranslate('LBL_FOUND')}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            {/if}
            <br/>
    </div>
</div>
{/strip}
