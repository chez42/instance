{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
    <div class="listViewActions pull-right">
        <div class="pageNumbers alignTop {if $LISTVIEW_LINKS['LISTVIEWSETTING']|@count gt 0}{else}{/if}">
            <span>
                <span class="pageNumbersText" id="pageNumbersText{$MODULE}" style="padding-right:5px">{if $LISTVIEW_ENTRIES_COUNT}{$PAGING_MODEL->getRecordStartRange()} {vtranslate('LBL_to', $MODULE)} {$PAGING_MODEL->getRecordEndRange()}{else}<span>&nbsp;</span>{/if}</span>
                {*<span class="icon-refresh pull-right totalNumberOfRecords cursorPointer{if !$LISTVIEW_ENTRIES_COUNT} hide{/if}"></span>*}
            </span>
        </div>
        <div class="btn-group alignTop margin0px">
            <span class="pull-right">
                <span class="btn-group">
                    <button class="btn listViewPreviousPageButton" id="listViewPreviousPageButton{$MODULE}" {if !$PAGING_MODEL->isPrevPageExists()} disabled {/if} type="button" data-module="{$MODULE}"><span class="icon-chevron-left"></span></button>
                    <button class="btn dropdown-toggle listViewPageJump" type="button" id="listViewPageJump{$MODULE}" data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if} data-module="{$MODULE}">
                        <i class="vtGlyph vticon-pageJump" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$MODULE)}"></i>
                    </button>
                    <ul class="listViewBasicAction dropdown-menu listViewPageJumpDropDown" id="listViewPageJumpDropDown{$MODULE}" style="left: -60px;">
                        <li>
                            <span class="row-fluid">
                                <span class="span3 pushUpandDown2per"><span class="pull-right">{vtranslate('LBL_PAGE',$MODULE)}</span></span>
                                <span class="span4">
                                    <input type="text" id="pageToJump{$MODULE}" class="listViewPagingInput pageToJump" value="{$PAGE_NUMBER}" data-module="{$MODULE}"/>
                                </span>
                                <span class="span2 textAlignCenter pushUpandDown2per">
                                    {vtranslate('LBL_OF',$MODULE)}&nbsp;
                                </span>
                                <span class="span2 pushUpandDown2per" id="totalPageCount{$MODULE}">{$PAGE_COUNT}</span>
                            </span>
                        </li>
                    </ul>
                    <button class="btn listViewNextPageButton" id="listViewNextPageButton{$MODULE}" {if (!$PAGING_MODEL->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if} type="button" data-module="{$MODULE}"><span class="icon-chevron-right"></span></button>
                </span>
            </span>
        </div>

    </div>
    <div class="clearfix"></div>
{/strip}