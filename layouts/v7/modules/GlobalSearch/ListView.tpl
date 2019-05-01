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
            padding: 0;

        }

        {/literal}
    </style>
    <div class="row detailViewTitle">
        <div class="col-md-12" style="padding-top: 10px;">
            <span class="details" style=" padding-left: 43px;font-size: 20px; font-weight: bold">
                <span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown"
                      title="Global Search">
                    {vtranslate('LBL_SEARCH_RESULT', 'GlobalSearch')}
                </span>
            </span>
            <hr>
        </div>
    </div>
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
        {assign var=FOUND_RECORDS value=0}
        {foreach item=LIST_RESULTS key=SEARCH_MODULE from=$SEARCH_RESULTS name=listresult}
            {if $LIST_RESULTS['totalResults'] eq 0}{continue}{/if}
            {assign var=FOUND_RECORDS value=FOUND_RECORDS+1}
            <div class="listViewTopMenuDiv noprint" style="padding-left: 10px">
                {assign var=LISTVIEW_HEADERS value=$LIST_RESULTS['listViewHeaders']}
                {assign var=LISTVIEW_ENTRIES value=$LIST_RESULTS['listViewEntries']}
                {assign var=TOTAL_RESULTS value=$LIST_RESULTS['totalResults']}
                {assign var=PAGE_COUNT value=$LIST_RESULTS['PAGE_COUNT']}
                {assign var=LISTVIEW_ENTRIES_COUNT value=$LIST_RESULTS['listViewEntries']|count}
                {assign var=PAGING_MODEL value=$PAGING_MODELS[$SEARCH_MODULE]}
                {assign var=PAGE_NUMBER value=1}
                <div class="row" style="background-color:#f5f5f5; color: #444444;font-weight:bold;">
                    <div class="col-lg-12" style="height: 40px; padding-top: 10px">
                        <div class="col-lg-8">
                       <span style="margin-left: 15px;">
                            <span style="font-size: 14px; color: blue">{vtranslate($SEARCH_MODULE, $SEARCH_MODULE)} ({$TOTAL_RESULTS})</span> <span
                                   style="font-weight: normal;"> -- Search results for: </span>{$VALUE}
                        </span>
                            <input type="hidden" name="search_module" value="{$MODULE}"/>
                        </div>
                        <div class="col-lg-4" style="margin-top: 10px;">
                            {*{include file="ListViewActions.tpl"|vtemplate_path:'GlobalSearch' MODULE=$SEARCH_MODULE}*}
                            <div class="pull-right" >
                                <input type="hidden" name="pageNumber" value="{$PAGE_NUMBER}">
                                <input type="hidden" name="recordsCount" value="{$TOTAL_RESULTS}">
						<span class="pageNumbersText" id="pageNumbersText{$SEARCH_MODULE}" style="padding-right:5px">
							{if $PAGING_MODEL->getRecordStartRange() neq ''}{$PAGING_MODEL->getRecordStartRange()}{else}0{/if} {vtranslate('LBL_to', $MODULE)} {if $PAGING_MODEL->getRecordEndRange() neq ''}{$PAGING_MODEL->getRecordEndRange()}{else}0{/if}
						</span>{vtranslate('LBL_OF',$SEARCH_MODULE)} {$TOTAL_RESULTS}
                                <a id="listViewPreviousPageButton{$SEARCH_MODULE}" class="previousPageButton listViewPreviousPageButton navigationButton verticalAlignMiddle" data-start='{$PAGING_MODEL->getRecordStartRange()-$PAGING_MODEL->getPageLimit()}'data-module="{$SEARCH_MODULE}" {if !$PAGING_MODEL->isPrevPageExists()}disabled=""{/if}>
                                    <i class="fa fa-caret-left"></i>&nbsp;&nbsp;
                                </a>
                                <a id="listViewNextPageButton{$SEARCH_MODULE}" class="nextPageButton listViewNextPageButton navigationButton verticalAlignMiddle" data-module="{$SEARCH_MODULE}" data-start='{$PAGING_MODEL->getRecordEndRange()}' {if !$PAGING_MODEL->isNextPageExists()} disabled=""{/if}>
                                    <i class="fa fa-caret-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {*<div class="listViewActionsDiv row-fluid"*}
                     {*style="background-color:#f5f5f5; color: #444444;font-weight:bold;">*}
                    {*<span class="span8" style="margin-top: 15px;text-shadow: 0 1px #ffffff;">*}
                        {*<span style="margin-left: 15px;">*}
                            {*{vtranslate($SEARCH_MODULE, $SEARCH_MODULE)} ({$TOTAL_RESULTS}) <span*}
                                    {*style="font-weight: normal;"> -- Search results for: </span>{$VALUE}*}
                        {*</span>*}
                    {*</span>*}
                    {*<span class="span4 btn-toolbar">*}
                        {*{include file="ListViewActions.tpl"|vtemplate_path:'GlobalSearch' MODULE=$SEARCH_MODULE}*}
                    {*</span>*}
                {*</div>*}
                <div id="searchResult{$SEARCH_MODULE}" class="table-container">
                    {include file="ListViewContents.tpl"|vtemplate_path:'GlobalSearch' SEARCH_MODE_RESULTS=true}
                </div>
            </div>
        {/foreach}
        {if $FOUND_RECORDS eq 0}
            <h4 style="text-align: center;">No records found !</h4>
        {/if}
    {/if}
{/strip}