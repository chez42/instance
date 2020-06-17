{*/* * *******************************************************************************
* The content of this file is subject to the Quoter ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
    {assign var="FINAL" value=$RELATED_PRODUCTS.1.final_details}

    {assign var="IS_INDIVIDUAL_TAX_TYPE" value=false}
    {assign var="IS_GROUP_TAX_TYPE" value=true}

    {if $FINAL.taxtype eq 'individual'}
        {assign var="IS_GROUP_TAX_TYPE" value=false}
        {assign var="IS_INDIVIDUAL_TAX_TYPE" value=true}
    {/if}

    <tr>
        <td style="padding:0;margin: 0; border: 0;">
            <div class="divLineItemContainer" style="overflow-y: hidden !important; overflow-x: auto !important; width: 100%;">
                <table class="table table-bordered lineItemContainer" style="border: none; table-layout: fixed;" data-currency-id = "{$SELECTED_CURRENCY.currency_id}">
                    <thead>
                        <tr>
                            {*<th><b>{vtranslate('LBL_TOOLS',$MODULE)}</b></th>*}
                            {foreach from = $SETTING  item = COLUMN}

                                <th class="cellItem {if  $COLUMN->columnName == 'tax_total'}tax_column {if $IS_GROUP_TAX_TYPE}hide{/if}{/if}" {if $COLUMN->columnWidth > 0} width = "{$COLUMN->columnWidth}px" {elseif $COLUMN->columnName eq 'item_name'} width="160px" {else} width="120px" {/if}  >
                                    {if $COLUMN->isMandatory eq 1}
                                        <span class="redColor">*</span>
                                    {/if}
                                    {if in_array($COLUMN->columnName,$COLUMN_DEFAULT)}
                                        <b style="font-size: 12px">{vtranslate($COLUMN->columnName,'Quoter')}</b>
                                    {else}
                                        <b style="font-size: 12px">{$COLUMN->customHeader}</b>
                                    {/if}
                                </th>
                            {/foreach}
                        </tr>
                    </thead>
                    <tbody class="itemBase">
                        <tr class="hide lineItemCloneCopyForProduct" level="1" rowName="1">
                            {include file="LineItemsContent.tpl"|@vtemplate_path:'Quoter' row_no=0 data=[] BASE_ROW = 'Products'}
                        </tr>

                        <tr class="hide lineItemCloneCopyForService" level="1" rowName="1">
                            {include file="LineItemsContent.tpl"|@vtemplate_path:'Quoter' row_no=0 data=[] BASE_ROW = 'Services'}
                        </tr>
                    </tbody>

                    <tbody class="listItem">
                        {foreach key=row_no item=data from=$RELATED_PRODUCTS}
                            {assign var="hdnProductId" value="hdnProductId"|cat:$row_no}
                            {assign var="parentProductId" value="parentProductId"|cat:$row_no}
                            {assign var="next_row" value=$row_no+1}
                            {assign var="pre_row" value=$row_no-1}
                            {assign var="nextParentProductId" value="parentProductId"|cat:$next_row}
                            {assign var="preData" value=$RELATED_PRODUCTS[$pre_row]}
                            {assign var="nextData" value=$RELATED_PRODUCTS[$next_row]}

                            {if !empty($data["section$row_no"])}

                                <tr class="section">

                                    <td class="fieldLabel" colspan="{count($SETTING)-1}" style = "font-size: 14px;border-left:0;">
                                        <span class="section_tool" style="display: inline-block; width:15px; text-align: left; position: relative;">
                                            <img class="section_move_icon" src="layouts/vlayout/skins/images/drag.png"  border="0" title="Drag" style="position: absolute; top: -13px;">
                                        </span>&nbsp;&nbsp;
                                        <span style="text-align: left;"><b>{$data["section$row_no"]}</b></span>
                                        <input type = "hidden" class ="section_value" name="section{$row_no}" value="{$data["section$row_no"]}" />
                                    </td>
                                    <td class="tdSpace{if $IS_GROUP_TAX_TYPE} hide{/if}" style="border-left:0;">&nbsp;</td>
                                </tr>
                            {/if}
                            <tr  id="row{$row_no}" class="lineItemRow" level="{if $data["level$row_no"]}{$data["level$row_no"]}{else}1{/if}" {if $data["entityType$row_no"] eq 'Products'}data-quantity-in-stock={$data["qtyInStock$row_no"] }{/if} rowName = "{$data['rowName']}">
                                {include file="LineItemsContent.tpl"|@vtemplate_path:'Quoter' row_no=$row_no data=$data SETTING =$SETTING}
                            </tr>
                            {assign var=arrRowName value=$data['arrRowName']}
                            {assign var='arr' value=array()}

                            {if !$nextData["level$next_row"]}
                                {if $data['isParentProduct']}
                                    {assign var='var' value=$data["level$row_no"]}
                                    {for $i = 0 to count($arrRowName)-1}
                                        {$arr.$i = $arrRowName.$i}
                                    {/for}
                                {else}
                                    {assign var='var' value=$data["level$row_no"]-1}
                                    {for $i = 0 to count($arrRowName)-2}
                                        {$arr.$i = $arrRowName.$i}
                                    {/for}
                                {/if}
                            {elseif $nextData["level$next_row"] <= $data["level$row_no"]}
                                {if $data['isParentProduct']}
                                    {assign var='var' value=$data["level$row_no"]-$nextData["level$next_row"]+1}
                                    {for $i = 0 to count($arrRowName)-1}
                                        {$arr.$i = $arrRowName.$i}
                                    {/for}
                                {else}
                                    {assign var='var' value=$data["level$row_no"]-$nextData["level$next_row"]}
                                    {for $i = 0 to count($arrRowName)-2}
                                        {$arr.$i = $arrRowName.$i}
                                    {/for}
                                {/if}
                            {/if}

                            {assign var='levelAction' value=count($arr)}
                            {while $var > 0}
                                <tr class="lineItemAction" rowName = "{for $i = 0 to $levelAction-1}{$arr.$i}{if $i neq $levelAction-1}-{/if}{/for}">
                                    <td><i class="muted addSubProduct "
                                           data-level = "{$levelAction+1}"
                                           {$parrentRow = $arr[$levelAction-1]}
                                           {$parrentRowId = $RELATED_PRODUCTS[$parrentRow]["hdnProductId$parrentRow"]}
                                           data-parent-id = "{$parrentRowId}">
                                            {for $i = 1 to $levelAction} &#8594; &nbsp; {/for}
                                            {vtranslate('LBL_ADD_ITEM','Quoter')}...</i>
                                    </td>
                                    {$var = $var-1}
                                    {$levelAction = $levelAction-1}
                                    {for $i = 1 to count($SETTING) -1}
										<td {if $i ==(count($SETTING) -1) && $IS_GROUP_TAX_TYPE}class="hide"{/if}>&nbsp;</td>
									{/for}
                                </tr>
                            {/while}
                            {if !empty($data["running_item_value$row_no"])}
                                {assign var=RUNNING_ITEMS value=$data["running_item_value$row_no"]}
                                {foreach key=RUNNING_NAME item=RUNNING_VALUE from=$RUNNING_ITEMS}
                                    <tr class="running_item" data-running-item-name = "{$RUNNING_NAME}" data-running-item-rowno = "{$row_no}">
                                        <td>
                                            <span class="running_item_tool" style="display: inline-block; width:40px; text-align: left;">
                                                <i class="icon-trash delete_running_item cursorPointer" title="Delete"></i>&nbsp;
                                                <img class="running_item_move_icon" src="layouts/vlayout/skins/images/drag.png" border="0" title="Drag">
                                            </span>
                                        </td>
                                        <td class="tdSpace{if $IS_GROUP_TAX_TYPE} hide{/if}" style="border-left:0;">&nbsp;</td>
                                        <td colspan="{count($SETTING) -2}" style="border-left:0;">
                                            <span class="pull-right" style="text-align: left;"><b>Running {vtranslate($TOTAL_SETTING[$RUNNING_NAME]['fieldLabel'],'Quoter')}: </b><b class="running_item_display">{$RUNNING_VALUE}</b></span>
                                            <input type = "hidden" class ="running_item_name" name="running_item_name{$row_no}[]" value="{$RUNNING_NAME}" />
                                            <input type = "hidden" class ="running_item_value" name="running_item_value{$row_no}[]" value="{$RUNNING_VALUE}" />
                                            {foreach from = $TOTAL_SETTING item=TOTAL_FIELD key=TOTAL_FIELD_NAME}
                                                <input type = "hidden" class ="running_{$TOTAL_FIELD_NAME}" value="" />
                                            {/foreach}
                                        </td>
                                    </tr>
                                {/foreach}
                            {/if}

                        {/foreach}
                        {if count($RELATED_PRODUCTS) eq 0}
                            <tr id="row1" class="lineItemRow" level="1" rowName="1">
                                {include file="LineItemsContent.tpl"|@vtemplate_path:'Quoter' row_no=1 data=[] SETTING =$SETTING BASE_ROW = 'Products'}
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
{/strip}