{*<!--
/* ********************************************************************************
* The content of this file is subject to the Custom Expenses/Bills ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('VTECustomExpenses', 'VTECustomExpenses')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class = "row">
        <div class='col-md-5'>
            <div class="foldersContainer pull-left">
                <button type="button" class="btn addButton btn-default module-buttons"
                        onclick='window.location.href = "{$MODULE_MODEL->getCreateViewUrl()}"'>
                    <div class="fa fa-plus" ></div>
                    &nbsp;&nbsp;{vtranslate('New Expenses/Bills' , $MODULE)}
                </button>
            </div>
        </div>
        <div class="col-md-4">
        </div>
        <div class="col-md-3">

        </div>
    </div>
    <div class="list-content row">
        <div class="col-sm-12 col-xs-12 ">
            <div id="table-content" class="table-container" style="padding-top:0px !important;">
                <table id="listview-table" class="table listview-table">
                    <thead>
                    <tr class="listViewContentHeader">
                        <th></th>
                        <th nowrap>Module</th>
                        <th nowrap>Quantity</th>
                        <th nowrap>Price</th>
                        <th nowrap>Description</th>
                        <th nowrap>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES}
                    <tr class="listViewEntries" >
                        <td>
                            <input style="opacity: 0;" {if $LISTVIEW_ENTRY['active'] == 'Active'} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" type="checkbox" name="custom_expenses-status" id="custom_expenses-status">
                        </td>
                        <td>
                            <span class="vicon-{strtolower($LISTVIEW_ENTRY['module'])} module-icon"></span>&nbsp;{vtranslate($LISTVIEW_ENTRY['module'],$LISTVIEW_ENTRY['module'])}
                        </td>
                        <td>
                            {$LISTVIEW_ENTRY['quantityFieldLabel']}
                        </td>
                        <td>
                            {$LISTVIEW_ENTRY['priceFieldLabel']}
                        </td>
                        <td>
                            {$LISTVIEW_ENTRY['descriptionFieldLabel']}
                        </td>
                        <td>
                            <a href="{$MODULE_MODEL->getCreateViewUrl($LISTVIEW_ENTRY['id'])}"><i class="fa fa-pencil"></i> Edit</a>
                            <a href="javascript:void(0)" data-id="{$LISTVIEW_ENTRY['id']}" id="vtecustom_expenses_delete" style="margin-left: 10px;"><i class="fa fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
            <div id="scroller_wrapper" class="bottom-fixed-scroll">
                <div id="scroller" class="scroller-div"></div>
            </div>
        </div>
    </div>
</div>