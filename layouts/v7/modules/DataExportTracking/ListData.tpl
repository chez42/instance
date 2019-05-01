{*/* ********************************************************************************
* The content of this file is subject to the Data Export Tracking ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="container-fluid">
        <div class="widget_header row-fluid">
            <h3 style="text-align: center;">{vtranslate($MODULE, $MODULE)}</h3>
        </div>
        <hr>
        <div class="row-fluid">
            <span class="span12 btn-toolbar">
                <div class="listViewActions pull-right">
                    <div class="pageNumbers alignTop pull-left" style="margin-right: 10px;">
                        <span>
                            <span class="pageNumbersText" style="padding-right:5px; line-height: 30px;">
                                {if $LISTVIEW_ENTRIES_COUNT}
                                    {$PAGING_MODEL->getRecordStartRange()}&nbsp;{vtranslate('LBL_to', $MODULE)}&nbsp;{$PAGING_MODEL->getRecordEndRange()}
                                {else}
                                    <span>&nbsp;</span>
                                {/if}
                            </span>
                            <span class="fa fa-refresh pull-right totalNumberOfRecords cursorPointer{if !$LISTVIEW_ENTRIES_COUNT} hide{/if}"
                                  style="line-height: 30px;"></span>
                        </span>
                    </div>
                    <div class="btn-group alignTop margin0px alignTop pull-left">
                        <span class="pull-right">
                            <span class="btn-group">
                                <button class="btn" id="detListViewPreviousPageButton" {if !$PAGING_MODEL->isPrevPageExists()} disabled {/if} type="button">
                                    <span class="fa fa-chevron-left"></span>
                                </button>
                                <button class="btn btn-default dropdown-toggle" type="button" id="detListViewPageJump" data-toggle="dropdown" {if $PAGE_COUNT eq 1} disabled {/if}>
                                    <i class="fa fa-ellipsis-h icon" title="{vtranslate('LBL_LISTVIEW_PAGE_JUMP',$moduleName)}"></i>
                                </button>
                                <ul class="detListViewBasicAction dropdown-menu" id="detListViewPageJumpDropDown" style="margin-left: -26px;">
                                    <li>
                                        <span class="row-fluid" style="display: inline-block; margin-left: 22px;">
                                            <span class="span3 pushUpandDown2per pull-left" style="margin-right: 4px;">
                                                <span class="pull-right">{vtranslate('LBL_PAGE',$moduleName)}</span>
                                            </span>
                                            <span class="span4 pull-left">
                                                <input type="text" id="detPageToJump" class="listViewPagingInput" value="{$PAGE_NUMBER}"
                                                       style="width: 36px; text-align: center;" />
                                            </span>
                                            <span class="span2 textAlignCenter pushUpandDown2per pull-left" style="margin-left: 4px;">
                                                {vtranslate('LBL_OF',$moduleName)}&nbsp;
                                            </span>
                                            <span class="span2 pushUpandDown2per pull-left" id="totalPageCount">{$PAGE_COUNT}</span>
                                        </span>

                                        <div style="clear: both;"></div>
                                    </li>
                                </ul>
                                <button class="btn" id="detListViewNextPageButton" {if (!$PAGING_MODEL->isNextPageExists()) or ($PAGE_COUNT eq 1)} disabled {/if} type="button"><span class="fa fa-chevron-right"></span></button>
                            </span>
                            <span class="pull-right btn-group">
                                <button class="btn dropdown-toggle" id="btn_settings" data-toggle="dropdown" href="index.php?module=DataExportTracking&parent=Settings&view=Settings">
                                    <i class="fa fa-wrench" title="Settings"></i>
                                </button>
                             </span>
                        </span>

                    </div>
                </div>
            </span>

            <span class="span4"></span>
        </div>
        <div class="listViewContentDiv" id="listViewContents">
            <input type="hidden" id="view" value="{$VIEW}" />
            <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
            <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
            <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
            <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
            <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
            <input type='hidden' value="{$PAGE_NUMBER}" id='pageNumber'>
            <input type='hidden' value="{$PAGING_MODEL->getPageLimit()}" id='pageLimit'>
            <input type="hidden" value="{$LISTVIEW_ENTRIES_COUNT}" id="noOfEntries">
            <div class="contents-topscroll noprint">
                <div class="topscroll-div">
                    &nbsp;
                </div>
            </div>
            <div class="listViewEntriesDiv contents-bottomscroll">
                <div class="bottomscroll-div">
                    <input type="hidden" value="{$ORDER_BY}" id="orderBy">
                    <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
                    <span class="listViewLoadingImageBlock hide modal noprint" id="loadingListViewModal">
                        <img class="listViewLoadingImage" src="{vimage_path('loading.gif')}" alt="no-image" title="{vtranslate('LBL_LOADING', $MODULE)}"/>
                        <p class="listViewLoadingMsg">{vtranslate('LBL_LOADING_LISTVIEW_CONTENTS', $MODULE)}........</p>
                    </span>
                    {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
                    <table class="table table-bordered listViewEntriesTable">
                        <thead>
                        <tr class="listViewHeaders">
                            <th>Type</th>
                            <th>Date/time</th>
                            <th>User</th>
                            <th>Screen</th>
                            <th>Size</th>
                            <th>Download</th>
                            <th></th>
                        </tr>
                        </thead>
                        {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=listview}
                            <tr class="listViewEntries2" data-id='{$LISTVIEW_ENTRY['id']}' id="data_tracking_listView_row_{$smarty.foreach.listview.index+1}">
                                <td class="listViewEntryValue" nowrap>
                                    {$LISTVIEW_ENTRY['type']}
                                </td>
                                <td class="listViewEntryValue" nowrap>
                                    {$LISTVIEW_ENTRY['time']}
                                </td>
                                <td class="listViewEntryValue link" nowrap>
                                    {$LISTVIEW_ENTRY['user']}
                                </td>
                                <td class="listViewEntryValue link" nowrap>
                                    {$LISTVIEW_ENTRY['link']}
                                </td>
                                <td class="listViewEntryValue" nowrap>
                                    {$LISTVIEW_ENTRY['size']}
                                </td>
                                <td class="listViewEntryValue link" nowrap>
                                    {$LISTVIEW_ENTRY['download']}
                                </td>
                                <td nowrap class="link">
                                    <div class="actions pull-right">
                                        <span class="actionImages">
                                            <a class="deleteRecordButton deleteDetRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="fa fa-trash alignMiddle"></i></a>
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        {/foreach}
                    </table>

                    <!--added this div for Temporarily -->
                    {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                        <table class="emptyRecordsDiv">
                            <tbody>
                            <tr>
                                <td>
                                    {vtranslate('No ')} {vtranslate('Data Export Tracking', $MODULE)} {vtranslate('LBL_FOUND')}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    {/if}
                </div>
            </div>
        </div>
    </div>
{/strip}
{literal}
<script>
    jQuery('.listViewEntries2 td').on('click',function(){
        var this_td = jQuery(this);
        if(!this_td.hasClass('link')) return false;
        else{
            var link = this_td.find('a').attr('href');
            window.open(link,'_blank');
            return false;
        }
    });
    jQuery('.deleteDetRecordButton').on('click',function(){
        var current_page  = jQuery('#pageNumber').val();
        var log_id= jQuery(this).closest('tr').data('id');
        var link ="index.php?module=DataExportTracking&view=ListData&mode=delete&log_id="+log_id+"&page=" + current_page;
        var message = app.vtranslate('Are you sure you want to delete this log?');
        app.helper.showConfirmationBox({'message': message}).then(function () {
                window.location.href = link;
            }, function (error, err) {
            }
        );
        return false;
    });
    jQuery('#detListViewNextPageButton').on('click',function(){
        var current_page  = jQuery('#pageNumber').val();
        var nextPageExist  = jQuery('#nextPageExist').val();
        if(nextPageExist){
            var link ="index.php?module=DataExportTracking&parent=Settings&view=ListData&page=" + (parseInt(current_page) + 1);
            window.location.href = link;
        }
    });
    jQuery('#detListViewPreviousPageButton').on('click',function(){
        var current_page  = jQuery('#pageNumber').val();
        var previousPageExist  = jQuery('#previousPageExist').val();
        if(previousPageExist){
            var link ="index.php?module=DataExportTracking&parent=Settings&view=ListData&page=" + (parseInt(current_page) - 1);
            window.location.href = link;
        }
    });
    jQuery('#detListViewPageJump').on('click',function(){
        var current_page  = jQuery('#pageNumber').val();
       jQuery('#detListViewPageJumpDropDown').toggle();
    });
    jQuery('#btn_settings').on('click',function(){
        var link = jQuery(this).attr('href');
        window.location.href = link;
    });
    jQuery('#detPageToJump').on('keydown', function (e) {
        if (e.which == 13) {
            var current_page  = parseInt(jQuery('#pageNumber').val());
            var total_page  =  parseInt(jQuery('#pageEndRange').val());
            var inputed_page  = parseInt(jQuery(this).val());
            if(!isNaN(inputed_page) && inputed_page > 0 && inputed_page <= total_page){
                if(inputed_page == current_page){
                    var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+current_page;
                    app.helper.showAlertNotification({'message': message});
                    return;
                }
                else{
                    var link ="index.php?module=DataExportTracking&parent=Settings&view=ListData&page=" + inputed_page;
                    window.location.href = link;
                }
            }
            else{
                var error = app.vtranslate('JS_PAGE_NOT_EXIST');
                jQuery(this).validationEngine('showPrompt',error,'',"topLeft",true);
                return;
            }
            return false;
        }
    });
</script>
{/literal}
