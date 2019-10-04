{*/* ********************************************************************************
* The content of this file is subject to the VTEAuthnet("License");
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
        .an-payment-profile-form .fa-info-circle{
            margin-right: 5px;
        }
    </style>
    <div class="modal-dialog modal-lg an-payment-profile-form">
        <div class="modal-content">
            <form class="form-horizontal recordEditView" name="form-customer-profile" id="paymentProfileForm" method="post" action="index.php">
                {if $RECORD}
                    {assign var=HEADER_TITLE value=vtranslate('LBL_EDIT', $MODULE)|cat:' '|cat:vtranslate($SINGLE_MODULE, $MODULE)}
                {else}
                    {assign var=HEADER_TITLE value=vtranslate('LBL_CREATE', $MODULE)|cat:' '|cat:vtranslate($SINGLE_MODULE, $MODULE)}
                {/if}
                {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
                    <input type="hidden" name="picklistDependency"
                           value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}'/>
                {/if}
                <input type="hidden" name="module" value="{$MODULE}">
                <input type="hidden" name="action" value="SavePaymentProfileAjax">
                <input type="hidden" name="record" value="{$RECORD}">
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}

                <div class="modal-body">
                    <div class="quickCreateContent">
                        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                        {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
                            <input type="hidden" name="picklistDependency"
                                   value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}'/>
                        {/if}
                        {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
                        <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}"/>
                        <input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}"/>
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
                            {assign var="BLOCK_NAME_UPPER" value=$BLOCK_LABEL|upper}
                            {assign var="BLOCK_DESC_TOOLTIP" value=$BLOCK_NAME_UPPER|cat:'_TOOLTIP'|replace:' ':'_'}
                            {assign var="BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP" value=$BLOCK_NAME_UPPER|cat:'_EDIT_PAYMENT_BTN_TOOLTIP'|replace:' ':'_'}
                            {if $BLOCK_FIELDS|@count gt 0}
                                <div class='fieldBlockContainer'>
                                    <h4 class='fieldBlockHeader'>
                                        {if vtranslate($BLOCK_DESC_TOOLTIP, $MODULE) neq $BLOCK_DESC_TOOLTIP}
                                            <small><i class="fa fa-info-circle alignMiddle an-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($BLOCK_DESC_TOOLTIP, $MODULE))}"></i></small>
                                        {/if}
                                        {vtranslate($BLOCK_LABEL, $MODULE)}&nbsp;
                                        {if $RECORD && ($BLOCK_LABEL eq 'Credit Card Information' || $BLOCK_LABEL eq 'Bank Information')}
                                            <span class="pull-right">
                                            {if vtranslate($BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP, $MODULE) neq $BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP}
                                                <small><i class="fa fa-info-circle alignMiddle an-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($BLOCK_EDIT_PAYMENT_BTN_DESC_TOOLTIP, $MODULE))}"></i></small>
                                            {/if}
                                                <a href="javascript:void(0);" style="text-decoration: underline !important; cursor: pointer;" onclick="Vtiger_VTEPayments_AuthorizeNet_Js.setBankCardEditable(this); return false;">
                                                    <small style="color: #15c;">{vtranslate('LBL_EDIT_PAYMENT_DETAIL_BTN', $MODULE)}</small>
                                                </a>
                                            </span>
                                        {/if}
                                    </h4>
                                    <hr>
                                    <table class="table table-borderless">
                                        <tr>
                                            {assign var=COUNTER value=0}
                                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                                            {assign var="FIELD_NAME_UPPDER" value=$FIELD_NAME|upper}
                                            {assign var="FIELD_DESC_TOOLTIP" value=$FIELD_NAME_UPPDER|cat:'_TOOLTIP'}
                                            {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                                            {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                                            {assign var="refrenceListCount" value=count($refrenceList)}
                                            {if $FIELD_MODEL->isEditable() eq true}
                                            {if $FIELD_MODEL->get('uitype') eq "19"}
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
                                            <td class="fieldLabel alignMiddle col-lg-2 {if $FIELD_NAME=='account_id' || $FIELD_NAME=='assigned_user_id' || $FIELD_NAME=='customer_profile_id'}hide {/if}">
                                                {if vtranslate($FIELD_DESC_TOOLTIP, $MODULE) neq $FIELD_DESC_TOOLTIP}
                                                    <i class="fa fa-info-circle alignMiddle an-tooltip" data-toggle="tooltip" title="{Vtiger_Util_Helper::toSafeHTML(vtranslate($FIELD_DESC_TOOLTIP, $MODULE))}"></i>
                                                {/if}

                                                {if $isReferenceField eq "reference"}
                                                    {if $refrenceListCount > 1}
                                                        {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                                        {assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
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
                                                {else if $FIELD_MODEL->get('uitype') eq "83"}
                                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                                                    {if $TAXCLASS_DETAILS}
                                                        {assign 'taxCount' count($TAXCLASS_DETAILS)%2}
                                                        {if $taxCount eq 0}
                                                            {if $COUNTER eq 2}
                                                                {assign var=COUNTER value=1}
                                                            {else}
                                                                {assign var=COUNTER value=2}
                                                            {/if}
                                                        {/if}
                                                    {/if}
                                                {else}
                                                    {if $MODULE eq 'Documents' && $FIELD_MODEL->get('label') eq 'File Name'}
                                                        {assign var=FILE_LOCATION_TYPE_FIELD value=$RECORD_STRUCTURE['LBL_FILE_INFORMATION']['filelocationtype']}
                                                        {if $FILE_LOCATION_TYPE_FIELD}
                                                            {if $FILE_LOCATION_TYPE_FIELD->get('fieldvalue') eq 'E'}
                                                                {vtranslate("LBL_FILE_URL", $MODULE)}&nbsp;<span class="redColor">*</span>
                                                            {else}
                                                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                                            {/if}
                                                        {else}
                                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                                        {/if}
                                                    {else}
                                                        {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                                    {/if}
                                                {/if}
                                                &nbsp;{if $FIELD_MODEL->isMandatory() eq true || ($FIELD_NAME=='card_number' || $FIELD_NAME=='expiration_date' || $FIELD_NAME=='account_type' || $FIELD_NAME=='routing_number'
                                                || $FIELD_NAME=='account_number' || $FIELD_NAME=='name_on_account' || $FIELD_NAME=='e_check_type')} <span class="redColor">*</span> {/if}
                                            </td>
                                            {if $FIELD_MODEL->get('uitype') neq '83'}
                                                <td class="fieldValue col-lg-4  {if $FIELD_NAME=='account_id' || $FIELD_NAME=='assigned_user_id' || $FIELD_NAME=='customer_profile_id'}hide {/if}" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                                {if $FIELD_NAME eq 'expiration_date'}
                                                    <input type="hidden" name="expiration_date" value="" />
                                                    <input type="hidden" id="an-current-month" value="{$CURRENT_MONTH}" />
                                                    <input type="hidden" id="an-current-year" value="{$CURRENT_YEAR}" />
                                                    <select class="select2 input-small" id="an-expiration-month-alias">
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
                                                    <select class="select2 input-small" id="an-expiration-year-alias">
                                                        <option value="">{vtranslate('LBL_EXPIRATION_YEAR', $MODULE)}</option>
                                                        {foreach item=YEAR from=$YEAR_ARRANGE}
                                                            <option value="{$YEAR}">{$YEAR}</option>
                                                        {/foreach}
                                                    </select>
                                                {else}
                                                    {if $FIELD_NAME eq 'an_id'}
                                                        {$FIELD_MODEL->get('fieldvalue')}
                                                    {else}
                                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                                                    {/if}
                                                {/if}
                                                </td>
                                            {/if}
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
                </div>

                {include file="ModalFooter.tpl"|vtemplate_path:$MODULE}
            </form>
        </div>
    </div>
{/strip}