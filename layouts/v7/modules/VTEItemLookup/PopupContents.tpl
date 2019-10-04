{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="row">

        <div class="col-lg-10">


        </div>
    </div>
    {include file="PicklistColorMap.tpl"|vtemplate_path:$VTE_MODULE}
    <div class="row lookup-item-popup-navigation">
        {include file='PopupNavigation.tpl'|vtemplate_path:$VTE_MODULE}
    </div>
    <div class="row lockup-item-main" style="overflow-x: auto;height: 485px;">
        <div class="col-md-12">
            <input type="hidden" id="product_filter_field_1_value" value="{$PRODUCT_FILTER_FIELD_1_VALUE}">
            <input type="hidden" id="product_filter_field_2_value" value="{$PRODUCT_FILTER_FIELD_2_VALUE}">
            <input type="hidden" id="product_filter_field_3_value" value="{$PRODUCT_FILTER_FIELD_3_VALUE}">
            <input type="hidden" id="service_filter_field_1_value" value="{$SERVICE_FILTER_FIELD_1_VALUE}">
            <input type="hidden" id="service_filter_field_2_value" value="{$SERVICE_FILTER_FIELD_2_VALUE}">
            <input type="hidden" id="service_filter_field_3_value" value="{$SERVICE_FILTER_FIELD_3_VALUE}">
            <input type="hidden" id="product_show_instock_filter" value="{$PRODUCT_SHOW_INSTOCK_FILTER}">
            <input type="hidden" id="product_show_inactive_filter" value="{$PRODUCT_SHOW_INACTIVE_FILTER}">
            <input type="hidden" id="product_show_bundles_filter" value="{$PRODUCT_SHOW_BUNDLES_FILTER}">
            <input type="hidden" id="service_show_inactive_filter" value="{$SERVICE_SHOW_INACTIVE_FILTER}">
            <input type="hidden" id="current_selected_item_modlue" value="{$CURRENT_SELECTED_ITEM_MODLUE}">
            <input type="hidden" id="product_bundles" value="{$PRODUCT_BUNDLES}">
            <input type="hidden" id="products_get_bundles_id" value="{$PRODUCTS_GET_BUNDLES_ID}">
            <input type="hidden" id="selectedAnphabet" value="{$ANPHABET_FILTER}"/>
            <input type='hidden' id='pageNumber' value="{$PAGE_NUMBER}">
            <input type='hidden' id='pageLimit' value="{$PAGING_MODEL->getPageLimit()}">
            <input type="hidden" id="noOfEntries" value="{$LISTVIEW_ENTRIES_COUNT}">
            <input type="hidden" id="pageStartRange" value="{$PAGING_MODEL->getRecordStartRange()}" />
            <input type="hidden" id="pageEndRange" value="{$PAGING_MODEL->getRecordEndRange()}" />
            <input type="hidden" id="previousPageExist" value="{$PAGING_MODEL->isPrevPageExists()}" />
            <input type="hidden" id="nextPageExist" value="{$PAGING_MODEL->isNextPageExists()}" />
            <input type="hidden" id="totalCount" value="{$LISTVIEW_COUNT}" />
            <input type="hidden" value="{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($SEARCH_DETAILS))}" id="currentSearchParams" />
            <input type="hidden" value="{$ALL_FILTER_RECORD_ID}" id="all_filter_record_id" />
            <div class="contents-topscroll">
                <div class="topscroll-div popupEntriesDivTopScroll">&nbsp;</div>
            </div>
            <div class="iTL-popupEntriesDiv relatedContents" style="min-height: 450px; height: 100%; {if count($LISTVIEW_ENTRIES) > 0}overflow-y: auto;{/if}">
                <input type="hidden" value="{$ORDER_BY}" id="orderBy">
                <input type="hidden" value="{$SORT_ORDER}" id="sortOrder">
                {if $SOURCE_MODULE eq "Emails"}
                    {if $MODULE neq 'Documents'}
                        <input type="hidden" value="Vtiger_EmailsRelatedModule_Popup_Js" id="popUpClassName"/>
                    {/if}
                {/if}
                {assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
                <div class="popupEntriesTableContainer {if $MODULE eq 'EmailTemplates'} emailTemplatesPopupTableContainer{/if}">
                    <table style="border-top: 1px solid #eee;" class="table listview-table table-bordered listViewEntriesTable iTL-listViewEntriesTable">
                        <thead style="display: block;">
                        <tr class="listViewHeaders">
                            {if $MULTI_SELECT}
                                <th class="{$WIDTHTYPE}" style="width: 90px !important;min-width: 90px !important;">
                                    <input type="checkbox"  class="selectAllInCurrentPage hide" />
                                </th>
                            {elseif $MODULE neq 'EmailTemplates'}
                                <th class="{$WIDTHTYPE}">&nbsp;</th>
                            {/if}
                            <th class="{$WIDTHTYPE}" style="width: 51px !important;min-width: 51px !important;">QTY</th>
                            <th class="{$WIDTHTYPE}" style="width: 91px !important;min-width: 91px !important;">Price</th>
                            {if $MODULE eq 'Products' && $CONFIGURE['product_show_picture_column'] == 1}
                                <th class="{$WIDTHTYPE}" style="width: 100px !important;min-width: 100px !important;">Picture</th>
                            {/if}
                            {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                <th class="{$WIDTHTYPE}" style="width: 50px !important;min-width: 50px !important;">
                                    <a href="javascript:void(0);" class="listViewContentHeaderValues listViewHeaderValues {if $LISTVIEW_HEADER->get('name') eq 'listprice'} noSorting {/if}" data-nextsortorderval="{if $ORDER_BY eq $LISTVIEW_HEADER->get('name')}{$NEXT_SORT_ORDER}{else}ASC{/if}" data-columnname="{$LISTVIEW_HEADER->get('name')}">
                                        {if $ORDER_BY eq $LISTVIEW_HEADER->get('name')}
                                            <i class="fa fa-sort {$FASORT_IMAGE}"></i>
                                        {else}
                                            <i class="fa fa-sort customsort"></i>
                                        {/if}
                                        &nbsp;{vtranslate($LISTVIEW_HEADER->get('label'), $MODULE)}&nbsp;
                                    </a>
                                </th>
                            {/foreach}
                        </tr>
                        {if $MODULE_MODEL && $MODULE_MODEL->isQuickSearchEnabled()}
                            <tr class="searchRow">
                                <th class="textAlignCenter" style="width: 90px !important;min-width: 90px !important;">
                                    <button class="btn btn-success" data-trigger="PopupListSearch">{vtranslate('LBL_SEARCH', $MODULE )}</button>
                                </th>
                                <th style="width: 51px !important;min-width: 51px !important;"></th>
                                <th style="width: 91px !important;min-width: 91px !important;"></th>
                                {if $MODULE eq 'Products' && $CONFIGURE['product_show_picture_column'] == 1}
                                    <th style="width: 100px !important;min-width: 100px !important;"></th>
                                {/if}
                                {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                    <th style="width: 50px !important;min-width: 50px !important;">
                                        {assign var=FIELD_UI_TYPE_MODEL value=$LISTVIEW_HEADER->getUITypeModel()}
                                        {include file=vtemplate_path($FIELD_UI_TYPE_MODEL->getListSearchTemplateName(),$MODULE_NAME)
                                        FIELD_MODEL= $LISTVIEW_HEADER SEARCH_INFO=$SEARCH_DETAILS[$LISTVIEW_HEADER->getName()] USER_MODEL=$CURRENT_USER_MODEL}
                                    </th>
                                {/foreach}
                            </tr>
                        {/if}
                        </thead>
                        {if count($LISTVIEW_ENTRIES) == 0 && $PRODUCT_BUNDLES == 1}
                        {else}
                            <tbody style="display: block;  overflow-x: hidden; overflow-y: auto;">
                            {foreach item=LISTVIEW_ENTRY from=$LISTVIEW_ENTRIES name=popupListView}
                                {assign var="RECORD_DATA" value="{$LISTVIEW_ENTRY->getRawData()}"}
                                <tr class="itemLookUp-listViewEntries" style="cursor: pointer;" data-id="{$LISTVIEW_ENTRY->getId()}" {if $MODULE eq 'EmailTemplates'} data-name="{$RECORD_DATA['subject']}" data-info="{$LISTVIEW_ENTRY->get('body')}" {else} data-name="{$LISTVIEW_ENTRY->getName()}" data-info='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($LISTVIEW_ENTRY->getRawData()))}' {/if}
                                        {if $GETURL neq ''} data-url='{$LISTVIEW_ENTRY->$GETURL()}' {/if}  id="{$MODULE}_popUpListView_row_{$smarty.foreach.popupListView.index+1}">
                                    {if $MULTI_SELECT}
                                        <td style="border-bottom: 1px solid #ddd !important;border-right: 1px solid #ddd !important;padding: 0px 5px 0px 5px; width: 90px !important;min-width: 90px !important; text-align: center;" class="{$WIDTHTYPE}">
                                            <input class="entryCheckBox hide" type="checkbox" /><button class="vteLookUpAddAnItem" type="button" data-action="add" data-record-id="{$LISTVIEW_ENTRY->getId()}" data-item-module="{$MODULE}" data-item-value="{if $MODULE == 'Products'}{$LISTVIEW_ENTRY->get('productname')}{else}{$LISTVIEW_ENTRY->get('servicename')}{/if}" class="btn btn-default btn-xs">Add</button> <input type="hidden" value="add" class="action">
                                        </td>
                                    {elseif $MODULE neq 'EmailTemplates'}
                                        <td style="border-bottom: 1px solid #ddd !important;border-right: 1px solid #ddd !important;padding: 0px 5px 0px 5px; "></td>
                                    {/if}
                                    <td style="border-bottom: 1px solid #ddd !important;border-right: 1px solid #ddd !important;padding: 0px 5px 0px 5px; width: 51px !important;min-width: 51px !important;">
                                        <input type="text" data-item_name="{if $MODULE == 'Products'}{$LISTVIEW_ENTRY->get('productname')}{else}{$LISTVIEW_ENTRY->get('servicename')}{/if}" data-type="qty" value="{if $MODULE == 'Products' && $PRODUCT_BUNDLES == 1 && $LISTVIEW_ENTRY->get('qty_per_unit') > 0}{$LISTVIEW_ENTRY->get('qty_per_unit')}{else}1{/if}" class="inputElement add-item-qty" style="width: 40px; height: 24px; margin-top: 3px; margin-bottom: 3px;">
                                    </td>
                                    <td style="border-bottom: 1px solid #ddd !important;border-right: 1px solid #ddd !important;padding: 0px 5px 0px 5px; width: 91px !important;min-width: 91px !important;">
                                        <input type="text" data-item_name="{if $MODULE == 'Products'}{$LISTVIEW_ENTRY->get('productname')}{else}{$LISTVIEW_ENTRY->get('servicename')}{/if}" data-type="price" value="{$LISTVIEW_ENTRY->get('unit_price')}" class="inputElement add-item-price" style="width: 80px; height: 24px; margin-top: 3px; margin-bottom: 3px;">
                                    </td>
                                    {if $MODULE eq 'Products' && $CONFIGURE['product_show_picture_column'] == 1}
                                        <td style="border-bottom: 1px solid #ddd !important;border-right: 1px solid #ddd !important;padding: 0px 5px 0px 5px; width: 100px !important;min-width: 100px !important;">
                                            {assign var=IMAGE_DETAILS value=$LISTVIEW_ENTRY->getImageDetails()}
                                            {if count($IMAGE_DETAILS) > 0}
                                                {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
                                                    {if !empty($IMAGE_INFO.path)}
                                                        {if $IMAGE_DETAILS|@count eq 1}
                                                            <img style="width: {$CONFIGURE['product_show_picture_size_width']}px;height: {$CONFIGURE['product_show_picture_size_height']}px;" class="itemLookUp-product-image" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" alt="{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}"align="left"><br>
                                                        {else if $IMAGE_DETAILS|@count eq 2}
                                                            <span><img class="itemLookUp-product-image" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" alt="{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="50%" height="100%" align="left"></span>
                                                        {else if $IMAGE_DETAILS|@count eq 3}
                                                            <span><img class="itemLookUp-product-image" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" alt="{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" {if $ITER eq 0 or $ITER eq 1}width="50%" height = "50%"{/if}{if $ITER eq 2}width="100%" height="50%"{/if} align="left"></span>
                                                        {else if $IMAGE_DETAILS|@count eq 4 or $IMAGE_DETAILS|@count gt 4}
                                                            {if $ITER gt 3}{break}{/if}
                                                            <span><img class="itemLookUp-product-image" src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" alt="{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}"width="50%" height="50%" align="left"></span>
                                                        {/if}
                                                    {else}

                                                    {/if}
                                                {/foreach}
                                            {else}
                                                <i class="fa fa-image" style="font-size: 20px;color: #c2b8b8;"></i> <span style="font-size: 12px;color: #c2b8b8;">NO IMAGE</span>
                                            {/if}

                                        </td>
                                    {/if}
                                    {foreach item=LISTVIEW_HEADER from=$LISTVIEW_HEADERS}
                                        {assign var=LISTVIEW_HEADERNAME value=$LISTVIEW_HEADER->get('name')}
                                        {assign var=LISTVIEW_ENTRY_VALUE value=$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                        <td style="border-bottom: 1px solid #ddd !important;border-right: 1px solid #ddd !important;padding: 0px 5px 0px 5px; width: 50px !important;min-width: 50px !important;" class="listViewEntryValue value textOverflowEllipsis {$WIDTHTYPE}" title="{$RECORD_DATA[$LISTVIEW_HEADERNAME]}">
                                            {if $LISTVIEW_HEADER->isNameField() eq true or $LISTVIEW_HEADER->get('uitype') eq '4'}
                                                <a>{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{if $MODULE eq 'Products' && count($LISTVIEW_ENTRY->getSubProducts()) > 0} <i style="font-size: 12px;line-height: 1;" class="vicon-inventory icon-module" data-info="\e639"></i>{/if}</a>
                                            {else if $LISTVIEW_HEADER->get('uitype') eq '72'}
                                                {assign var=CURRENCY_SYMBOL_PLACEMENT value={$CURRENT_USER_MODEL->get('currency_symbol_placement')}}
                                                {if $CURRENCY_SYMBOL_PLACEMENT eq '1.0$'}
                                                    {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}{$LISTVIEW_ENTRY->get('currencySymbol')}
                                                {else}
                                                    {$LISTVIEW_ENTRY->get('currencySymbol')}{$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                                {/if}
                                            {else if $LISTVIEW_HEADERNAME eq 'listprice'}
                                                {CurrencyField::convertToUserFormat($LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME), null, true, true)}
                                            {else if $LISTVIEW_HEADER->getFieldDataType() eq 'picklist'}
                                                <span {if !empty($LISTVIEW_ENTRY_VALUE)} class="picklist-color picklist-{$LISTVIEW_HEADER->getId()}-{Vtiger_Util_Helper::convertSpaceToHyphen($LISTVIEW_ENTRY->getRaw($LISTVIEW_HEADERNAME))}" {/if}> {$LISTVIEW_ENTRY_VALUE} </span>
                                            {else if $LISTVIEW_HEADER->getFieldDataType() eq 'multipicklist'}
                                                {assign var=MULTI_RAW_PICKLIST_VALUES value=explode('|##|',$LISTVIEW_ENTRY->getRaw($LISTVIEW_HEADERNAME))}
                                                {assign var=MULTI_PICKLIST_VALUES value=explode(',',$LISTVIEW_ENTRY_VALUE)}
                                                {foreach item=MULTI_PICKLIST_VALUE key=MULTI_PICKLIST_INDEX from=$MULTI_RAW_PICKLIST_VALUES}
                                                    <span {if !empty($LISTVIEW_ENTRY_VALUE)} class="picklist-color picklist-{$LISTVIEW_HEADER->getId()}-{Vtiger_Util_Helper::convertSpaceToHyphen(trim($MULTI_PICKLIST_VALUE))}" {/if}> {trim($MULTI_PICKLIST_VALUES[$MULTI_PICKLIST_INDEX])} </span>
                                                {/foreach}
                                            {else}
                                                {$LISTVIEW_ENTRY->get($LISTVIEW_HEADERNAME)}
                                            {/if}
                                        </td>
                                    {/foreach}
                                </tr>
                            {/foreach}
                            </tbody>
                        {/if}

                    </table>
                </div>

                <!--added this div for Temporarily -->
                {if $LISTVIEW_ENTRIES_COUNT eq '0'}
                    <div class="row">
                        <div class="emptyRecordsDiv">
                            {if $IS_MODULE_DISABLED eq 'true'}
                                {vtranslate($RELATED_MODULE, $RELATED_MODULE)}
                                {vtranslate('LBL_MODULE_DISABLED', $RELATED_MODULE)}
                            {else}
                            {if count($LISTVIEW_ENTRIES) == 0 && $PRODUCT_BUNDLES == 1}
                                Please select a bundle
                            {else}
                                {vtranslate('LBL_NO', $MODULE)} {vtranslate($RELATED_MODULE, $RELATED_MODULE)} {vtranslate('LBL_FOUND', $MODULE)}.
                            {/if}

                            {/if}
                        </div>
                    </div>
                {/if}
                {if $FIELDS_INFO neq null}
                    <script type="text/javascript">
                        var popup_uimeta = (function() {
                            var fieldInfo  = {$FIELDS_INFO};
                            return {
                                field: {
                                    get: function(name, property) {
                                        if(name && property === undefined) {
                                            return fieldInfo[name];
                                        }
                                        if(name && property) {
                                            return fieldInfo[name][property]
                                        }
                                    },
                                    isMandatory : function(name){
                                        if(fieldInfo[name]) {
                                            return fieldInfo[name].mandatory;
                                        }
                                        return false;
                                    },
                                    getType : function(name){
                                        if(fieldInfo[name]) {
                                            return fieldInfo[name].type
                                        }
                                        return false;
                                    }
                                },
                            };
                        })();
                    </script>
                {/if}
            </div>
        </div>
    </div>
{/strip}
