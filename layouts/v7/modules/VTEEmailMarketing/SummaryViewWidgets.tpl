{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
    <style>
        .table-actions span {
            opacity: 0.7;
        }
        .table-actions span:hover {
            opacity: 1;
            cursor: pointer;
        }
    </style>
    <div class="col-lg-12 resizable-summary-view">
        <div class="left-block col-lg-5">
            {* Module Summary View*}
            <div class="summaryView">
                <div class="summaryViewHeader">
                    <h4 class="display-inline-block">{vtranslate('LBL_CAMPAIGN_INFORMATION', 'VTEEmailMarketing')}</h4>
                    <button type="button" class="btn btn-success pull-right" id="PreviewEmailTemplate" style="margin-top: -8px">Preview Template</button>
                </div>
                <hr/>
                <div id="summaryViewArea" class="summaryViewFields">
                    {$MODULE_SUMMARY}
                </div>
            </div>
            {* Module Summary View Ends Here*}

            {* Summary View Documents Widget*}
        </div>
        <div class="right-block col-lg-7" style="background: white; border: 1px solid #F3F3F3"
             id="vte-campagin-pie-chart">
            {include file='PieChart.tpl'|vtemplate_path:$MODULE_NAME}
        </div>
    </div>
    <div class="col-lg-12 resizable-summary-view" style="margin-top:30px ">
        <div class="left-block col-lg-1">
        </div>
        <div class="middle-block col-lg-10">
            <table class="table table-bordered text-center" style="width: 100%">
                <tr>
                    <td class="DisplayType" data-type="" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="total_email">{$RECORD->get('total')}</h3>
                        <h4>Total</h4>
                    </td>
                    <td class="DisplayType" data-type="queued" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="queued_email">{$RECORD->get('queued')}</h3>
                        <h4>Queued</h4>
                    </td>
                    <td class="DisplayType" data-type="sent" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="sent_mail">{$RECORD->get('sent')}</h3>
                        <h4>Sent</h4>
                    </td>
                    <td class="DisplayType" data-type="failed_to_send" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="failed_sent_mail">{$RECORD->get('failed_to_send')}</h3>
                        <h4>Failed to Send</h4>
                    </td>
                    <td class="DisplayType" data-type="unique_open" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="open_email">{$RECORD->get('unique_open')}</h3>
                        <h4>Unique Opens</h4>
                    </td>
                    <td class="DisplayType" data-type="unopened" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="unopen_email">{$RECORD->get('unopened')}</h3>
                        <h4>Unopened</h4>
                    </td>
                    <td class="DisplayType" data-type="unsubcribes" style="width: 14.2%">
                        <h3 style="color:#4774ba" id="unsubcribes_email">{$RECORD->get('unsubcribes')}</h3>
                        <h4>Unsubcribed</h4>
                    </td>

                </tr>
            </table>
        </div>
        <div class="right-block col-lg-1">
        </div>
        <input type="hidden" id="hfDispayType" value="" />
    </div>
    <div class="col-lg-12 resizable-summary-view" style="margin-top:30px ">
        <div class="left-block col-lg-1">
        </div>
        <div class="middle-block col-lg-10">
            <div class="container-fluid">
                <div id="pagination-record-sent-email">
                    <input type="hidden" name="pageStartRange" id="pageStartRange" value="{$PAGINATION['startRecord']}">
                    <input type="hidden" name="pageEndRange" id="pageEndRange" value="{$PAGINATION['endRecord']}">
                    <input type="hidden" name="currentPage" id="currentPage" value="{$PAGINATION['currentPage']}">
                    <input type="hidden" name="totalPage" id="totalPage" value="{$PAGINATION['totalPage']}">
                    <input type="hidden" name="noOfEntries" id="noOfEntries" value="{$PAGINATION['totalRecord']}">
                    <div class="btn-group pull-right">
                        <button type="button" id="PreviousPageButton" class="btn btn-default" {if $PAGINATION['currentPage'] == 1} disabled="disabled" {/if}><i class="fa fa-caret-left"></i></button>
                        <button type="button" id="PageJump" class="btn btn-default" >
                            <i class="fa fa-ellipsis-h icon" title="Page Jump"></i>
                        </button>
                        <ul class="listViewBasicAction dropdown-menu" id="PageJumpDropDown">
                            <li>
                                <div class="listview-pagenum">
                                    <span >{vtranslate('LBL_PAGE',$moduleName)}</span>&nbsp;
                                    <strong><span>{$PAGINATION['currentPage']}</span></strong>&nbsp;
                                    <span >{vtranslate('LBL_OF',$moduleName)}</span>&nbsp;
                                    <strong><span id="totalPageCount">{$PAGINATION['totalPage']}</span></strong>
                                </div>
                                <div class="listview-pagejump">
                                    <input type="text" id="pageToJump" placeholder="Jump To" class="listViewPagingInput text-center">&nbsp;
                                    <button type="button" id="pageToJumpSubmit" class="btn btn-success listViewPagingInputSubmit text-center">GO</button>
                                </div>
                            </li>
                        </ul>
                        <button type="button" id="NextPageButton" class="btn btn-default"  {if $PAGINATION['currentPage'] == $PAGINATION['totalPage'] || $PAGINATION['totalPage'] == 0} disabled="disabled" {/if}  ><i class="fa fa-caret-right"></i></button>
                    </div>
                    {if {$PAGINATION['totalRecord']} > 0}
                        <span class="pageNumbers  pull-right" style="position:relative;top:7px;margin-right:5px">
                        <span class="pageNumbersText"> {$PAGINATION['startRecord']} to {$PAGINATION['endRecord']} </span>
                        <span class="totalNumberOfRecords cursorPointer"> of {$PAGINATION['totalRecord']} </span>
                    </span>
                    {/if}
                </div>

                <table class="table table-bordered text-center" style="background-color: white; width: 97% ; margin-top: 50px" id="table-record-sent-email">
                    <thead>
                    <tr>
                        <th style="border-right: none !important;"></th>
                        <th class="text-center" style="vertical-align: middle; width: 25% ;border-left: none !important;">Record Type</th>
                        <th class="text-center" style="vertical-align: middle; width: 25% ">Name</th>
                        <th class="text-center" style="vertical-align: middle; width: 25%">Email</th>
                        <th class="text-center" style="vertical-align: middle; width: 25% ;border-right: none !important;">Sent On</th>
                        <th style="border-left: none !important;"></th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach item=item from=$RECORD_RELATED_SENT_MAIL}
                        <tr>
                            <td style="border-right: none !important;">
                                <div class="table-actions">
                                    <span class="quickView fa fa-eye icon action listViewEntries" data-app="MARKETING" title="Quick View"  data-id="{$item['record_id']}" data-recordurl="{$item['data_url']}"></span>
                                </div>
                            </td>
                            <td class="relatedListEntryValues"
                                style="vertical-align: middle;border-left: none !important">{vtranslate($item['record_type'],$item['record_type'])}</td>
                            <td class="relatedListEntryValues" style="vertical-align: middle">{$item['name']}</td>
                            <td class="relatedListEntryValues" style="vertical-align: middle">{$item['email']}</td>
                            <td class="relatedListEntryValues" style="vertical-align: middle ; border-right: none !important">{if $item['sent_on'] neq 'null' && $item['sent_on'] neq ''}{$item['sent_on']}{/if}</td>
                            <td style="border-left: none !important;">
                                {$item['error_info']}
                            </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>

            </div>
        </div>
        <div class="right-block col-lg-1">
        </div>
    </div>
{/strip}