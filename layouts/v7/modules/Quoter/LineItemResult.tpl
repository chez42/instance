{*/* * *******************************************************************************
* The content of this file is subject to the Quoter ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
    {if $MODE eq 'Edit'}
        {assign var="IS_INDIVIDUAL_TAX_TYPE" value=false}
        {assign var="IS_GROUP_TAX_TYPE" value=true}

        {if $FINAL.taxtype eq 'individual'}
            {assign var="IS_GROUP_TAX_TYPE" value=false}
            {assign var="IS_INDIVIDUAL_TAX_TYPE" value=true}
        {/if}
        {if !empty($TOTAL_SETTINGS)}
            <tbody>
                {foreach item = VALUE key = ROW_NAME from=$TOTAL_SETTINGS}
                    {if $ROW_NAME =='tax'}
                        <!-- Group Tax - starts -->
                        <tr id="group_tax_row" valign="top" class="{if $IS_INDIVIDUAL_TAX_TYPE}hide{/if}">
                            <td width="83%">
                                <span class="pull-right">(+)&nbsp;<strong><a href="javascript:void(0)" id="finalTax">{vtranslate('LBL_TAX','Quoter')}</a></strong></span>
                                <!-- Pop Div For Group TAX -->
                                <div class="hide finalTaxUI validCheck" id="group_tax_div">
                                    <input type="hidden" class="popover_title" value="{vtranslate('LBL_GROUP_TAX',$MODULE)}" />
                                    <table width="100%" border="0" cellpadding="5" cellspacing="0" class="table table-nobordered popupTable">
                                        {foreach item=tax_detail name=group_tax_loop key=loop_count from=$TAXES}
                                            <tr>
                                                <td class="lineOnTop">{$tax_detail.taxlabel}</td>
                                                <td class="lineOnTop">
                                                    <input type="text" size="5" data-compound-on="{if $tax_detail['method'] eq 'Compound'}{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($tax_detail['compoundon']))}{/if}"
                                                           name="{$tax_detail.taxname}_group_percentage" id="group_tax_percentage{$smarty.foreach.group_tax_loop.iteration}" value="{$tax_detail.percentage}" class="span1 groupTaxPercentage"
                                                           data-rule-positive=true data-rule-inventory_percentage=true />&nbsp;%
                                                </td>
                                            </tr>
                                        {/foreach}
                                        <input type="hidden" id="group_tax_count" value="{$smarty.foreach.group_tax_loop.iteration}" />
                                    </table>
                                </div>
                                <!-- End Popup Div Group Tax -->
                            </td>
                            <td>
                                <div data-fieldType = "{$VALUE.fieldType}" class="pull-right {$ROW_NAME}" align="right">
                                    <input class="inputElement" id="{$ROW_NAME}" name="{$ROW_NAME}" type="text" readonly  value="{if $IS_INDIVIDUAL_TAX_TYPE}0{else}{$TOTAL_VALUE.$ROW_NAME}{/if}" style="text-align: right; max-width: 50%">
                                </div>
                            </td>
                        </tr>

                        <!-- Group Tax - ends -->
                    {elseif $ROW_NAME =='s_h_percent'}
                        <tr>
                            <td width="83%">
                                <span class="pull-right">(+)&nbsp;<strong><a href="javascript:void(0)" id="chargeTaxes">{vtranslate('LBL_TAXES_ON_CHARGES','Quoter')} </a></strong></span>

                                <!-- Pop Div For Shipping and Handling TAX -->
                                <div id="chargeTaxesBlock" class="hide validCheck chargeTaxesBlock">
                                    <p class="popover_title hide">
                                        {vtranslate('LBL_TAXES_ON_CHARGES', $MODULE)} : <span id="SHChargeVal" class="SHChargeVal">{if $FINAL.shipping_handling_charge}{$FINAL.shipping_handling_charge}{else}0{/if}</span>
                                    </p>
                                    <table class="table table-nobordered popupTable">
                                        <tbody>
                                        {foreach key=CHARGE_ID item=CHARGE_MODEL from=$INVENTORY_CHARGES}
                                            {foreach key=CHARGE_TAX_ID item=CHARGE_TAX_MODEL from=$RECORD->getChargeTaxModelsList($CHARGE_ID)}
                                                {if !isset($CHARGE_AND_CHARGETAX_VALUES[$CHARGE_ID]['taxes'][$CHARGE_TAX_ID]) && $CHARGE_TAX_MODEL->isDeleted()}
                                                    {continue}
                                                {/if}
                                                {if !$RECORD_ID && $CHARGE_TAX_MODEL->isDeleted()}
                                                    {continue}
                                                {/if}
                                                <tr>
                                                    {assign var=SH_TAX_VALUE value=$CHARGE_TAX_MODEL->getTax()}
                                                    {if $CHARGE_AND_CHARGETAX_VALUES[$CHARGE_ID]['value'] neq NULL}
                                                        {assign var=SH_TAX_VALUE value=0}
                                                        {if $CHARGE_AND_CHARGETAX_VALUES[$CHARGE_ID]['taxes'][$CHARGE_TAX_ID]}
                                                            {assign var=SH_TAX_VALUE value=$CHARGE_AND_CHARGETAX_VALUES[$CHARGE_ID]['taxes'][$CHARGE_TAX_ID]}
                                                        {/if}
                                                    {/if}

                                                    <td class="lineOnTop">{$CHARGE_MODEL->getName()} - {$CHARGE_TAX_MODEL->getName()}</td>
                                                    <td class="lineOnTop">
                                                        <input type="text" data-charge-id="{$CHARGE_ID}" data-compound-on="{if $CHARGE_TAX_MODEL->getTaxMethod() eq 'Compound'}{$CHARGE_TAX_MODEL->get('compoundon')}{/if}"
                                                               class="span1 chargeTaxPercentage" name="charges[{$CHARGE_ID}][taxes][{$CHARGE_TAX_ID}]" value="{$SH_TAX_VALUE}"
                                                               data-rule-positive=true data-rule-inventory_percentage=true />&nbsp;%
                                                    </td>
                                                </tr>
                                            {/foreach}
                                        {/foreach}
                                        </tbody>
                                    </table>
                                </div>
                                <!-- End Popup Div for Shipping and Handling TAX -->
                            </td>
                            <td>
                                <div data-fieldType = "{$VALUE.fieldType}" class="pull-right {$ROW_NAME}" align="right">
                                <input class="inputElement" id="{$ROW_NAME}" name="{$ROW_NAME}" type="text" readonly  value="{$TOTAL_VALUE.$ROW_NAME}" style="text-align: right; max-width: 50%">
                                </div>
                            </td>
                        </tr>
                    {else}
                        <tr>
                            <td style="width: 85% !important">
                                <div class="pull-right"><strong>{vtranslate($VALUE['fieldLabel'], 'Quoter')}</strong></div> 

                            </td>
                            <td>
                                {if $VALUE.fieldType == 1}
                                    <div data-fieldType = "{$VALUE.fieldType}" class="pull-right {$ROW_NAME}" align="right">
                                        <input class="inputElement" id="{$ROW_NAME}" name="{$ROW_NAME}" type="text"  value="{$TOTAL_VALUE.$ROW_NAME}" style="text-align: right; max-width: 50%">
                                    </div>
                                {else}
                                    <div  class="pull-right {$ROW_NAME}">
                                        <span>{$RECORD_MODEL->numberFormat($TOTAL_VALUE.$ROW_NAME)}</span>
                                        <input type="hidden" id="{$ROW_NAME}" name= "{$ROW_NAME}" value="{$TOTAL_VALUE.$ROW_NAME}"/>
                                    </div>
                                {/if}

                            </td>
                        </tr>
                    {/if}
                {/foreach}
            </tbody>
        {/if}
    {else}
        {if !empty($TOTAL_SETTINGS)}
            <tbody>
            {foreach item = VALUE key = ROW_NAME from=$TOTAL_SETTINGS}
                <tr>
                    <td style="width: 85% !important">
                        <div class="pull-right"><strong>{vtranslate($VALUE['fieldLabel'], 'Quoter')}</strong></div>

                    </td>
                    <td>
                        <div  class="pull-right {$ROW_NAME}">
                            <span>{$RECORD_MODEL->numberFormat($TOTAL_VALUE.$ROW_NAME)}</span>
                        </div>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        {/if}
    {/if}
{/strip}