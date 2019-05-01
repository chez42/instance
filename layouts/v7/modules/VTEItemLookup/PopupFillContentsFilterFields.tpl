{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="col-md-12">
        <div class="contents-topscroll">
            <div class="topscroll-div">
                &nbsp;
            </div>
        </div>
        <div class="popupFillContainer_filter_fields_scroll" style="height: 520px;  border: 1px solid #ccc; overflow-x: auto; ">
            <table style="width: 100%;" class="item-lookup-fillter-table-content listview-table listViewEntriesTable">
                <tr>
                    <td>
                        <ul style="list-style: none;margin-left: -29px;" class="item-lookup-fillter">
                            {if $CURRENT_SELECTED_ITEM_MODLUE == 'Products' && $PRODUCT_BUNDLES == 1}
                                <li><label style="font-size: 14px;">Bundles</label>
                                    <ul style="list-style: none;margin-left: -29px;">
                                    {foreach item=BUNDLES_RECORD from=$PRODUCT_BUNDLES_RECORDS}
                                        {assign var=PRODUCT_RECORD_MODEL value=$BUNDLES_RECORD['prod_record_model']}
                                        {assign var=SUB_PROD_COUNT value=$BUNDLES_RECORD['sub_prod_count']}
                                        <li><input value="{$PRODUCT_RECORD_MODEL->getId()}" data-name="" type="checkbox" class="checkbox-prod-get-sub"> &nbsp;{$PRODUCT_RECORD_MODEL->get('label')} ({$SUB_PROD_COUNT})</li>
                                    {/foreach}
                                    </ul>
                                </li>
                            {else}
                                {assign var=FILTER_LIST_FIELDS value=$ITEMMODULE_FILTER_LIST_FIELD[$CURRENT_SELECTED_ITEM_MODLUE]}
                                {foreach item=FILTER_FIELD_NAME from=$FILTER_LIST_FIELDS}
                                    {if $CONFIGURE[$FILTER_FIELD_NAME] != '' && count($CONFIGURE_FILTER_FIELDS_DATA[$FILTER_FIELD_NAME]['value']) > 0}
                                        <li>
                                            <label style="font-size: 14px;">{vtranslate($CONFIGURE_FILTER_FIELDS_DATA[$FILTER_FIELD_NAME]['label'], $CURRENT_SELECTED_ITEM_MODLUE)}</label>
                                            <ul style="list-style: none;margin-left: -29px;">
                                                {foreach key=KEY item=FILTER_FIELD_VALUE from=$CONFIGURE_FILTER_FIELDS_DATA[$FILTER_FIELD_NAME]['value']}
                                                    {if strlen($FILTER_FIELD_VALUE) > 15}
                                                        {assign var=LIMIT_LABEL value=substr($FILTER_FIELD_VALUE, 0, strrpos(substr($FILTER_FIELD_VALUE, 0, 20), ' '))|cat:'...'}
                                                    {else}
                                                        {assign var=LIMIT_LABEL value=$FILTER_FIELD_VALUE}
                                                    {/if}
                                                    <li><input value="{$KEY}" data-name="{$FILTER_FIELD_NAME}" data-filter-module="{$CURRENT_SELECTED_ITEM_MODLUE}" data-filter-field="{$CONFIGURE[$FILTER_FIELD_NAME]}" data-filter-value="{$FILTER_FIELD_VALUE}"  type="checkbox" class="checkbox-item-module-filter-fields"> {$LIMIT_LABEL} <span data-module="{$CURRENT_SELECTED_ITEM_MODLUE}" data-field="{$CONFIGURE[$FILTER_FIELD_NAME]}" data-value="{$FILTER_FIELD_VALUE}" data-filter="{$FILTER_FIELD_NAME}" class="item-lookup-field-value-count">({$CONFIGURE_FILTER_FIELDS_DATA[$FILTER_FIELD_NAME]['count'][$FILTER_FIELD_VALUE]})</span></li>
                                                {/foreach}
                                            </ul>
                                        </li>
                                        <li style="margin-top: 10px;" class="device"></li>
                                    {/if}
                                {/foreach}
                                <li><label style="font-size: 14px;">Miscellaneous</label>
                                    {if $CURRENT_SELECTED_ITEM_MODLUE eq 'Products'}
                                        <ul style="list-style: none;margin-left: -29px;">
                                            {if $CONFIGURE['product_show_instock_filter'] == 1}
                                                <li><input value="" data-name="product_show_instock_filter" type="checkbox" class="checkbox-miscellaneous"> In Stock Only</li>
                                            {/if}
                                            {if $CONFIGURE['product_show_bundles_filter'] == 1}
                                                <li><input value="" data-name="product_show_bundles_filter" type="checkbox" class="checkbox-miscellaneous"> Bundles Only</li>
                                            {/if}
                                            {if $CONFIGURE['product_show_inactive_filter'] == 1}
                                                <li><input value="" data-name="product_show_inactive_filter" type="checkbox" class="checkbox-miscellaneous"> Inactive</li>
                                            {/if}
                                        </ul>
                                    {/if}
                                    {if $CURRENT_SELECTED_ITEM_MODLUE eq 'Services'}
                                        <ul style="list-style: none;margin-left: -29px;">
                                            {if $CONFIGURE['service_show_inactive_filter'] == 1}
                                                <li><input value="" data-name="service_show_inactive_filter" type="checkbox" class="checkbox-miscellaneous"> Inactive</li>
                                            {/if}
                                        </ul>
                                    {/if}
                                </li>

                            {/if}
                            <li style="margin-top: 20px;" class="device"></li>
                            <li><button class="btn btn-default button-clear-left-filter-item">Clear Filter</button></li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
    </div>
{/strip}
