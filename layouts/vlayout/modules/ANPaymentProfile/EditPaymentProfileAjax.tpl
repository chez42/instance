{*/* ********************************************************************************
* The content of this file is subject to the VTEPayments("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <style type="text/css">
        .modal-open .tooltip {
            z-index: 12070;
        }
        .tooltip-inner{
            max-width: 600px;
        }
    </style>
    <div class="modelContainer" style="min-width: 850px; max-height: 550px; overflow-y: auto;">
        <form class="form-horizontal recordEditView" name="form-payment-profile" id="paymentProfileForm" method="post"
              action="index.php">
            {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
                <input type="hidden" name="picklistDependency"
                       value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}'/>
            {/if}
            <input type="hidden" name="module" value="{$MODULE}">
            <input type="hidden" name="action" value="SavePaymentProfileAjax">
            <input type="hidden" name="record" value="{$RECORD}">
            <div class="modal-header contentsBackground">
                <a class="cancelLink cancelLinkContainer pull-right" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                <button class="btn btn-success pull-right" type="button" id="save-btn"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                <h3>{if $RECORD}{vtranslate('LBL_EDIT', $MODULE)}{else}{vtranslate('LBL_CREATE', $MODULE)}{/if} {vtranslate($SINGLE_MODULE, $MODULE)}</h3>
            </div>
            <div class="quickCreateContent">
                <div class="modal-body">
                    {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                    {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
                        <input type="hidden" name="picklistDependency"
                               value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}'/>
                    {/if}
                    {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
                    <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}"/>
                    <input type="hidden" name="defaultOtherEventDuration"
                           value="{$USER_MODEL->get('othereventduration')}"/>
                    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
                        {assign var="BLOCK_NAME_UPPER" value=$BLOCK_LABEL|upper}
                        {assign var="BLOCK_DESC_TOOLTIP" value=$BLOCK_NAME_UPPER|cat:'_TOOLTIP'|replace:' ':'_'}
                        {assign var="BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP" value=$BLOCK_NAME_UPPER|cat:'_EDIT_PAYMENT_BTN_TOOLTIP'|replace:' ':'_'}
                        {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
                        <table class="table table-bordered blockContainer showInlineTable equalSplit"
                               style="margin-bottom: 15px;">
                            <thead>
                            <tr>
                                <th class="blockHeader" colspan="4">
                                    {if vtranslate($BLOCK_DESC_TOOLTIP, $MODULE) neq $BLOCK_DESC_TOOLTIP}
                                        <small><i class="icon-info-sign alignMiddle an-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($BLOCK_DESC_TOOLTIP, $MODULE))}"></i></small>
                                    {/if}
                                    {vtranslate($BLOCK_LABEL, $MODULE)}&nbsp;
                                    {if $RECORD && ($BLOCK_LABEL eq 'Credit Card Information' || $BLOCK_LABEL eq 'Bank Information')}
                                        <span class="pull-right">
                                            {if vtranslate($BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP, $MODULE) neq $BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP}
                                                <small><i class="icon-info-sign alignMiddle an-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP, $MODULE))}"></i></small>
                                            {/if}
                                            <a href="javascript:void(0);" style="text-decoration: underline !important; cursor: pointer;" onclick="Vtiger_VTEPayments_AuthorizeNet_Js.setBankCardEditable(this); return false;">
                                                <small style="color: #15c;">{vtranslate('LBL_EDIT_PAYMENT_DETAIL_BTN', $MODULE)}</small>
                                            </a>
                                            </span>
                                    {/if}
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                {assign var=COUNTER value=0}
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                                {assign var="FIELD_NAME_UPPDER" value=$FIELD_NAME|upper}
                                {assign var="FIELD_DESC_TOOLTIP" value=$FIELD_NAME_UPPDER|cat:'_TOOLTIP'}
                                {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                                {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                                {if $COUNTER eq '1'}
                                <td class="{$WIDTHTYPE}"></td>
                                <td class="{$WIDTHTYPE}"></td>
                            </tr>
                            <tr>
                                {assign var=COUNTER value=0}
                                {/if}
                                {/if}
                                {if $COUNTER eq 2}
                            </tr>
                            <tr>
                                {assign var=COUNTER value=1}
                                {else}
                                {assign var=COUNTER value=$COUNTER+1}
                                {/if}
                                <td class="fieldLabel {$WIDTHTYPE} {if $FIELD_NAME=='account_id' || $FIELD_NAME=='assigned_user_id' || $FIELD_NAME=='customer_profile_id'}hide {/if}">
                                    {if $isReferenceField neq "reference"}
                                    <label class="muted pull-right marginRight10px">
                                        {if vtranslate($FIELD_DESC_TOOLTIP, $MODULE) neq $FIELD_DESC_TOOLTIP}
                                            <i class="icon-info-sign alignMiddle an-tooltip" data-toggle="tooltip"
                                               title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($FIELD_DESC_TOOLTIP, $MODULE))}"></i>
                                        {/if}
                                        {/if}

                                        {if ($FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference")
                                            || ($FIELD_NAME=='card_number' || $FIELD_NAME=='expiration_date' || $FIELD_NAME=='account_type' || $FIELD_NAME=='routing_number'
                                            || $FIELD_NAME=='account_number' || $FIELD_NAME=='name_on_account' || $FIELD_NAME=='e_check_type')}
                                            <span class="redColor">*</span>
                                        {/if}
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
                                    {if $FIELD_MODEL->isMandatory() eq true}<span class="redColor">*</span>{/if}
                                                    <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown"
                                                            class="chzn-select referenceModulesList streched"
                                                            style="width:160px;">
                                                        <optgroup>
                                                            {foreach key=index item=value from=$REFERENCE_LIST}
                                                                <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                            {/foreach}
                                                        </optgroup>
                                                    </select>
                                </span>
                                            {else}
                                                <label class="muted pull-right marginRight10px">
                                                    {if vtranslate($FIELD_DESC_TOOLTIP, $MODULE) neq $FIELD_DESC_TOOLTIP}
                                                        <i class="icon-info-sign alignMiddle an-tooltip"
                                                           data-toggle="tooltip"
                                                           title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($FIELD_DESC_TOOLTIP, $MODULE))}"></i>
                                                    {/if}
                                                    {if $FIELD_MODEL->isMandatory() eq true}
                                                        <span class="redColor">*</span>
                                                    {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                            {/if}
                                        {else if $FIELD_MODEL->get('uitype') eq "83"}
                                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                                        {else}
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                        {/if}
                                        {if $isReferenceField neq "reference"}</label>{/if}
                                </td>
                                {if $FIELD_MODEL->get('uitype') neq "83"}
                                    <td class="fieldValue {$WIDTHTYPE} {if $FIELD_NAME=='account_id' || $FIELD_NAME=='assigned_user_id' || $FIELD_NAME=='customer_profile_id'}hide {/if}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                        <div class="row-fluid">
                        <span class="span12">
                            {if $FIELD_NAME eq 'expiration_date'}
                                <input type="hidden" name="expiration_date" value="" />
                                <input type="hidden" id="an-current-month" value="{$CURRENT_MONTH}" />
                                <input type="hidden" id="an-current-year" value="{$CURRENT_YEAR}" />
                                <select class="chzn-select input-small" id="an-expiration-month-alias">
                                    <option value="">{vtranslate('LBL_EXPIRATION_MONTH', $MODULE)}</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <select class="chzn-select input-small" id="an-expiration-year-alias">
                                    <option value="">{vtranslate('LBL_EXPIRATION_YEAR', $MODULE)}</option>
                                    {foreach item=YEAR from=$YEAR_ARRANGE}
                                        <option value="{$YEAR}">{$YEAR}</option>
                                    {/foreach}
                                </select>
                            {else}
                                {if $FIELD_NAME eq 'an_id'}
                                    {$FIELD_MODEL->get('fieldvalue')}
                                {else}
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                                {/if}
                            {/if}
                        </span>
                                        </div>
                                    </td>
                                {/if}
                                {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                                    <td class="{$WIDTHTYPE}"></td>
                                    <td class="{$WIDTHTYPE}"></td>
                                {/if}
                                {/foreach}
                                {* adding additional column for odd number of fields in a block *}
                                {if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
                                    <td class="fieldLabel {$WIDTHTYPE}"></td>
                                    <td class="{$WIDTHTYPE}"></td>
                                {/if}
                            </tr>
                            </tbody>
                        </table>
                    {/foreach}
                </div>
            </div>
        </form>
    </div>
{/strip}