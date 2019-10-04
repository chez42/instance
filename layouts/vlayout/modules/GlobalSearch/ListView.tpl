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
    <style>
        {literal}
        .highlight {
            background-color: #fbed50;
        }
        {/literal}
    </style>
    <input type='hidden' value="{$VALUE}" id='searchKey'>
    {if $NO_MODULE eq 'true'}
        <div class="detailViewInfo row-fluid">
            <div class="span12 details">
                <div class="alert alert-error">
                    <p style="font-weight:bold;">{vtranslate('LBL_NO_MODULE_SELECTED', 'GlobalSearch')}</p>
                </div>
            </div>
        </div>
    {else}
        {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
        {foreach item=LIST_RESULTS key=SEARCH_MODULE from=$SEARCH_RESULTS name=listresult}
            {if $LIST_RESULTS['totalResults'] eq 0}{continue}{/if}
            <div class="listViewTopMenuDiv noprint">
                {assign var=LISTVIEW_HEADERS value=$LIST_RESULTS['listViewHeaders']}
                {assign var=LISTVIEW_ENTRIES value=$LIST_RESULTS['listViewEntries']}
                {assign var=TOTAL_RESULTS value=$LIST_RESULTS['totalResults']}
                {assign var=PAGE_COUNT value=$LIST_RESULTS['PAGE_COUNT']}
                {assign var=LISTVIEW_ENTRIES_COUNT value=$LIST_RESULTS['listViewEntries']|count}
                {assign var=PAGING_MODEL value=$PAGING_MODELS[$SEARCH_MODULE]}
                {assign var=PAGE_NUMBER value=1}
                <div class="listViewActionsDiv row-fluid" style="background-color:#f5f5f5; color: #444444;font-weight:bold;">
                    <span class="span8" style="margin-top: 15px;text-shadow: 0 1px #ffffff;">
                        <span style="margin-left: 15px;">
                            {vtranslate($SEARCH_MODULE, $SEARCH_MODULE)} ({$TOTAL_RESULTS}) <span style="font-weight: normal;"> -- Search results for: </span>{$VALUE}
                        </span>
                    </span>
                    <span class="span4 btn-toolbar">
                        {include file="ListViewActions.tpl"|vtemplate_path:'GlobalSearch' MODULE=$SEARCH_MODULE}
                    </span>
                </div>
                <div id="searchResult{$SEARCH_MODULE}">
                    {include file="ListViewContents.tpl"|vtemplate_path:'GlobalSearch'}
                </div>
            </div>
        {/foreach}
    {/if}
{/strip}