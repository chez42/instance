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
{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
    <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
{/if}
<div name='editContent'>
    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
        {if $BLOCK_LABEL eq 'LBL_ITEM_DETAILS'}{continue}{/if}
         {if $BLOCK_FIELDS|@count gt 0}
             <div class='fieldBlockContainer'>
                     <h4 class='fieldBlockHeader'>{vtranslate($BLOCK_LABEL, $MODULE)}</h4>
                 <hr>
                 <table class="table table-borderless {if $BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION'} addressBlock{/if}">
                     {if ($BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION') and ($MODULE neq 'PurchaseOrder')}
                        <tr>
                            <td class="fieldLabel " name="copyHeader1">
                                <label  name="togglingHeader">{vtranslate('LBL_BILLING_ADDRESS_FROM', $MODULE)}</label>
                            </td>
                            <td class="fieldValue" name="copyAddress1">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="copyAddressFromRight" class="accountAddress" data-copy-address="billing" checked="checked">
                                        &nbsp;{vtranslate('SINGLE_Accounts', $MODULE)}
                                    </label>
                                </div>
                                <div class="radio">
                                    <label> 
                                        {if $MODULE eq 'Quotes'}
                                            <input type="radio" name="copyAddressFromRight" class="contactAddress" data-copy-address="billing" checked="checked">
                                            &nbsp;{vtranslate('Related To', $MODULE)}
                                        {else}
                                            <input type="radio" name="copyAddressFromRight" class="contactAddress" data-copy-address="billing" checked="checked">
                                            &nbsp;{vtranslate('SINGLE_Contacts', $MODULE)}
                                        {/if}
                                    </label>
                                </div>
                                <div class="radio" name="togglingAddressContainerRight">
                                    <label>
                                        <input type="radio" name="copyAddressFromRight" class="shippingAddress" data-target="shipping" checked="checked">
                                        &nbsp;{vtranslate('Shipping Address', $MODULE)}
                                    </label>
                                </div>
                                <div class="radio hide" name="togglingAddressContainerLeft">
                                    <label>
                                        <input type="radio" name="copyAddressFromRight"  class="billingAddress" data-target="billing" checked="checked">
                                        &nbsp;{vtranslate('Billing Address', $MODULE)}
                                    </label>
                                </div>
                            </td>
                            <td class="fieldLabel" name="copyHeader2">
                                <label  name="togglingHeader">{vtranslate('LBL_SHIPPING_ADDRESS_FROM', $MODULE)}</label>
                            </td>
                            <td class="fieldValue" name="copyAddress2">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="copyAddressFromLeft" class="accountAddress" data-copy-address="shipping" checked="checked">
                                        &nbsp;{vtranslate('SINGLE_Accounts', $MODULE)}
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        {if $MODULE eq 'Quotes'}
                                            <input type="radio" name="copyAddressFromLeft" class="contactAddress" data-copy-address="shipping" checked="checked">
                                            &nbsp;{vtranslate('Related To', $MODULE)}
                                        {else}
                                            <input type="radio" name="copyAddressFromLeft" class="contactAddress" data-copy-address="shipping" checked="checked">
                                            &nbsp;{vtranslate('SINGLE_Contacts', $MODULE)}	
                                        {/if}
                                    </label>
                                </div>
                                <div class="radio" name="togglingAddressContainerLeft">
                                    <label>
                                        <input type="radio" name="copyAddressFromLeft" class="billingAddress" data-target="billing" checked="checked">
                                        &nbsp;{vtranslate('Billing Address', $MODULE)}
                                    </label>
                                </div>
                                <div class="radio hide" name="togglingAddressContainerRight">
                                    <label>
                                        <input type="radio" name="copyAddressFromLeft" class="shippingAddress" data-target="shipping" checked="checked">
                                        &nbsp;{vtranslate('Shipping Address', $MODULE)}
                                    </label>
                                </div>
                            </td>
                        </tr>
                    {/if}
                     <tr>
                     {assign var=COUNTER value=0}
                         {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                         {if $FIELD_NAME neq 'target_module' || ($QUOTER_MODULE && $QUOTER_MODULE->isActive() && $FIELD_NAME eq 'target_module')}
                         {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                         {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                         {if $COUNTER eq '1'}
                         <td></td><td></td></tr><tr>
                         {assign var=COUNTER value=0}
                         {/if}
                         {/if}
                         {if $COUNTER eq 2}
                     </tr><tr>
                         {assign var=COUNTER value=1}
                         {else}
                         {assign var=COUNTER value=$COUNTER+1}
                         {/if}
                         <td class="fieldLabel">
                             {if $isReferenceField neq "reference"}<label class="muted pull-right marginRight10px">{/if}
                                 {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                                 {if $isReferenceField eq "reference"}
                                     {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                     {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                     {if $REFERENCE_LIST_COUNT > 1}
                                         {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                         {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                         {if !empty($REFERENCED_MODULE_STRUCT)}
                                             {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                         {/if}
                                         <span class="pull-right">
                                        {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                             <select class="chzn-select referenceModulesList streched" style="width:140px;">
                                                 <optgroup>
                                                     {foreach key=index item=value from=$REFERENCE_LIST}
                                                         <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                     {/foreach}
                                                 </optgroup>
                                             </select>
                                    </span>
                                     {else}
                                         <label class="muted pull-right marginRight10px">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                     {/if}
                                 {else if $FIELD_MODEL->get('uitype') eq "83"}
                                     {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER}
                                 {else}
                                     {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                 {/if}
                                 {if $isReferenceField neq "reference"}</label>{/if}
                         </td>
                         {if $FIELD_MODEL->get('uitype') neq "83"}
                             <td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if} {if $FIELD_MODEL->get('uitype') eq '20'} colspan="3"{/if}>
                                 {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                             </td>
                         {/if}
                         {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}<td></td><td></td>{/if}
                         {/if}
                         {/foreach}
                     {*If their are odd number of fields in edit then border top is missing so adding the check*}
                     {if $COUNTER is odd}
                         <td></td>
                         <td></td>
                     {/if}
                     </tr>
                 </table>
             </div>
         {/if}
     {/foreach}
</div>
{if $QUOTER_MODULE && $QUOTER_MODULE->isActive()}
    {assign var=LINEITEM_FIELDS value=$RECORD_STRUCTURE['LBL_ITEM_DETAILS']}
    {assign var="FINAL" value=$RELATED_PRODUCTS.1.final_details}
    {assign var="IS_INDIVIDUAL_TAX_TYPE" value=false}
    {assign var="IS_GROUP_TAX_TYPE" value=true}

    {if $FINAL.taxtype eq 'individual'}
        {assign var="IS_GROUP_TAX_TYPE" value=false}
        {assign var="IS_INDIVIDUAL_TAX_TYPE" value=true}
    {/if}
    <div class='fieldBlockContainer' style="display: none">
        <div class="row">
                <span class="col-lg-6">
                    <div class="row">
                        <div class="col-lg-6">
                            <h3 class='fieldBlockHeader' style="margin-top:5px;">&nbsp;&nbsp;&nbsp;{vtranslate('LBL_ITEM_DETAILS', $MODULE)}</h3>
                        </div>
                        <div class="col-lg-6" style="top: 3px;">
                            {if $LINEITEM_FIELDS['region_id'] && $LINEITEM_FIELDS['region_id']->isEditable()}
                                <span class="pull-right">
                                    <i class="fa fa-info-circle"></i>&nbsp;
                                    <label>{vtranslate($LINEITEM_FIELDS['region_id']->get('label'), $MODULE)}</label>&nbsp;
                                    <select class="select2" id="region_id" name="region_id" style="width: 164px;">
                                        <option value="0" data-info="{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($DEFAULT_TAX_REGION_INFO))}">{vtranslate('LBL_SELECT_OPTION', $MODULE)}</option>
                                        {foreach key=TAX_REGION_ID item=TAX_REGION_INFO from=$TAX_REGIONS}
                                            <option value="{$TAX_REGION_ID}" data-info='{Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($TAX_REGION_INFO))}' {if $TAX_REGION_ID eq $RECORD->get('region_id')}selected{/if}>{$TAX_REGION_INFO['name']}</option>
                                        {/foreach}
                                    </select>
                                    <input type="hidden" id="prevRegionId" value="{$RECORD->get('region_id')}" />
                                    &nbsp;&nbsp;<a class="fa fa-wrench" href="index.php?module=Vtiger&parent=Settings&view=TaxIndex" target="_blank" style="vertical-align:middle;"></a>

                                </span>
                            {/if}
                        </div>
                    </div>
                </span>
            <div class="col-lg-3" style="top: 3px;">
                <center>
                    <i class="fa fa-info-circle"></i>&nbsp;
                    <label>{vtranslate('LBL_CURRENCY',$MODULE)}</label>&nbsp;
                    {assign var=SELECTED_CURRENCY value=$CURRENCINFO}
                    {* Lookup the currency information if not yet set - create mode *}
                    {if $SELECTED_CURRENCY eq ''}
                        {assign var=USER_CURRENCY_ID value=$USER_MODEL->get('currency_id')}
                        {foreach item=currency_details from=$CURRENCIES}
                            {if $currency_details.curid eq $USER_CURRENCY_ID}
                                {assign var=SELECTED_CURRENCY value=$currency_details}
                            {/if}
                        {/foreach}
                    {/if}

                    <select class="select2" id="currency_id" name="currency_id" style="width: 150px;">
                        {foreach item=currency_details key=count from=$CURRENCIES}
                            <option value="{$currency_details.curid}" class="textShadowNone" data-conversion-rate="{$currency_details.conversionrate}" {if $SELECTED_CURRENCY.currency_id eq $currency_details.curid} selected {/if}>
                                {$currency_details.currencylabel|@getTranslatedCurrencyString} ({$currency_details.currencysymbol})
                            </option>
                        {/foreach}
                    </select>

                    {assign var="RECORD_CURRENCY_RATE" value=$RECORD_STRUCTURE_MODEL->getRecord()->get('conversion_rate')}
                    {if $RECORD_CURRENCY_RATE eq ''}
                        {assign var="RECORD_CURRENCY_RATE" value=$SELECTED_CURRENCY.conversionrate}
                    {/if}
                    <input type="hidden" name="conversion_rate" id="conversion_rate" value="{$RECORD_CURRENCY_RATE}" />
                    <input type="hidden" value="{$SELECTED_CURRENCY.currency_id}" id="prev_selected_currency_id" />
                    <!-- TODO : To get default currency in even better way than depending on first element -->
                    <input type="hidden" id="default_currency_id" value="{$CURRENCIES.0.curid}" />
                    <input type="hidden" value="{$SELECTED_CURRENCY.currency_id}" id="selectedCurrencyId" />
                </center>
            </div>
            <div class="col-lg-3" style="top: 3px;">
                <div style="float: right;">
                    <i class="fa fa-info-circle"></i>&nbsp;
                    <label>{vtranslate('LBL_TAX_MODE',$MODULE)}</label>&nbsp;
                    <select class="select2 lineItemTax" id="taxtype" name="taxtype" style="width: 150px;">
                        <option value="individual" {if $IS_INDIVIDUAL_TAX_TYPE}selected{/if}>{vtranslate('LBL_INDIVIDUAL', $MODULE)}</option>
                        <option value="group" {if $IS_GROUP_TAX_TYPE}selected{/if}>{vtranslate('LBL_GROUP', $MODULE)}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="lineitemTableContainer">
        <table class="table table-bordered" id="lineItemTab">
        </table>
    </div>
    <div>
        <div style="display: none;" class="btn-addProduct">
            {if $PRODUCT_ACTIVE eq 'true' && $SERVICE_ACTIVE eq 'true'}
                <div class="btn-toolbar">
					<span class="btn-group">
						<button type="button" class="btn btn-default" id="addProduct" data-module-name="Products" >
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;<strong>{vtranslate('LBL_ADD_PRODUCT',$MODULE)}</strong>
                        </button>
					</span>
					<span class="btn-group">
						<button type="button" class="btn btn-default" id="addService" data-module-name="Services" >
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;<strong>{vtranslate('LBL_ADD_SERVICE',$MODULE)}</strong>
                        </button>
					</span>
                </div>
            {elseif $PRODUCT_ACTIVE eq 'true'}
                <div class="btn-group">
                    <button type="button" class="btn btn-default" id="addProduct" data-module-name="Products">
                        <i class="fa fa-plus"></i><strong> {vtranslate('LBL_ADD_PRODUCT',$MODULE)}</strong>
                    </button>
                </div>
            {elseif $SERVICE_ACTIVE eq 'true'}
                <div class="btn-group">
                    <button type="button" class="btn btn-default" id="addService" data-module-name="Services">
                        <i class="fa fa-plus"></i><strong> {vtranslate('LBL_ADD_SERVICE',$MODULE)}</strong>
                    </button>
                </div>
            {/if}
        </div>
    </div>
    <br>
    <table class="table table-bordered blockContainer lineItemTable" id="lineItemResult" >
    </table>
    <input type="hidden" name="totalProductCount" id="totalProductCount" value="" />
    <input type="hidden" name="subtotal" id="subtotal" value="" />
    <input type="hidden" name="total" id="total" value="" />
{else}
    {include file="partials/LineItemsEdit.tpl"|@vtemplate_path:'PSTemplates'}
{/if}
