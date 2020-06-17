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
                 <div class="table table-borderless {if $BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION'} addressBlock{/if}">
                     {if ($BLOCK_LABEL eq 'LBL_ADDRESS_INFORMATION') and ($MODULE neq 'PurchaseOrder')}
                        <div class="row">
                            <div class="col-md-2 col-sm-12 col-xs-12 fieldLabel " name="copyHeader1">
                                <label  name="togglingHeader">{vtranslate('LBL_BILLING_ADDRESS_FROM', $MODULE)}</label>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12 fieldValue" name="copyAddress1">
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
                            </div>
                        

                            <div class="col-md-2 col-sm-12 col-xs-12 fieldLabel" name="copyHeader2">
                                <label  name="togglingHeader">{vtranslate('LBL_SHIPPING_ADDRESS_FROM', $MODULE)}</label>
                            </div>
                            <div class="col-md-4 col-sm-12 col-xs-12 fieldValue" name="copyAddress2">
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
                            </div>
                        </div>
                    {/if}
                     <div class="row">
                     {assign var=COUNTER value=0}
                     {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                         {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                         {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                         {assign var="refrenceListCount" value=count($refrenceList)}
                         {if $FIELD_MODEL->isEditable() eq true}
                             {if $FIELD_MODEL->get('uitype') eq "19"}
                                 {if $COUNTER eq '1'}
                                     <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                                     <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                                     <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                                     <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                                 </div>
                                     <div class="row">
                                     {assign var=COUNTER value=0}
                                 {/if}
                             {/if}
                             {if $COUNTER eq 2}
                                 </div><div class="row">
                                 {assign var=COUNTER value=1}
                             {else}
                                 {assign var=COUNTER value=$COUNTER+1}
                             {/if}
                             <div class="col-md-2 col-sm-12 col-xs-12  fieldLabel alignMiddle">
                             {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                             {if $isReferenceField eq "reference"}
                                 {if $refrenceListCount > 1}
                                     {assign var="REFERENCED_MODULE_ID" value=$FIELD_MODEL->get('fieldvalue')}
                                     {assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($REFERENCED_MODULE_ID)}
                                     {if !empty($REFERENCED_MODULE_STRUCTURE)}
                                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCTURE->get('name')}
                                     {/if}
                                     <select style="width: 140px;" class="select2 referenceModulesList">
                                        {foreach key=index item=value from=$refrenceList}
                                            <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $value)}</option>
                                        {/foreach}
                                    </select>
                                 {else}
                                     {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                 {/if}
                             {else}
                                 {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                             {/if}
                             &nbsp;&nbsp;
                         </div>
                         <div class="col-md-{if $FIELD_MODEL->get('uitype') eq '19'}10{assign var=COUNTER value=$COUNTER+1}{else}4{/if} col-sm-12 col-xs-12  fieldValue" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} >
                             {if $FIELD_MODEL->getFieldDataType() eq 'image' || $FIELD_MODEL->getFieldDataType() eq 'file'}
                                 <div class='col-lg-4 col-md-4 redColor'>
                                     {vtranslate('LBL_NOTE_EXISTING_ATTACHMENTS_WILL_BE_REPLACED', $MODULE)}
                                 </div>
                             {/if}
                             {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                         </div>
                     {/if}
                     {/foreach}
                     <div class="visible-sm visible-xs clearfix"></div>
                     {*If their are odd number of fields in edit then border top is missing so adding the check*}
                     {if $COUNTER is odd}
                         <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                         <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                         <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                        <div class="col-md-3 col-sm-12 col-xs-12 "></div>
                     {/if}
                     </div><!--chiusura row-->
                 </div><!--chiusura div table-->
             </div>
         {/if}
     {/foreach}
</div>
{include file="partials/LineItemsEdit.tpl"|@vtemplate_path:'Inventory'}