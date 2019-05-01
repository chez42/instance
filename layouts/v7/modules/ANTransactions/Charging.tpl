{*/* ********************************************************************************
* The content of this file is subject to the VTEAuthnet("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div class="modal-dialog modal-medium" id="an-charging-container-popup" style="padding: 10px;">
    <input type="hidden" id="charging-payment-id" value="{$PAYMENT_ID}" />
    <input type="hidden" id="charging-invoice-id" value="{$INVOICE_ID}" />
    <input type="hidden" id="charging-amount" value="{$AMOUNT_PAID}" />
    <div class="modal-content">
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=vtranslate('LBL_SELECT_PAMENT_METHOD', $MODULE)}
        <div class="modal-body container-fluid" style="text-align: center;">
            <div class="row">
                <div class="col-sm-12 col-xs-12">
                    <select id="an-payment-profile-id" class="select2" style="width: 70%;">
                        <option value="">{vtranslate('LBL_SELECT_PAMENT_METHOD', $MODULE)}</option>
                        {foreach item=PAYMENT_PROFILE from=$AN_CUSTOMER_PAYMENT_PROFILES}
                            <option value="{$PAYMENT_PROFILE.anpaymentprofileid}" {if $AN_CUSTOMER_PAYMENT_PROFILE_COUNT eq 1}selected=""{/if}>{$PAYMENT_PROFILE.payment_profile_label}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="col-sm-12 col-xs-12" style="margin-top: 20px;">
                    <h2>$ {$AMOUNT_PAID_DISPLAY}</h2>
                </div>
                <div class="col-sm-12 col-xs-12" style="margin-top: 30px;">
                    <a style="margin-right: 5px;" class="cancelLink cancelLinkContainer" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    <button class="btn btn-success" type="button" id="submit-btn"><strong>{vtranslate('LBL_SUBMIT', $MODULE)}</strong></button>
                </div>
            </div>
        </div>
    </div>
</div>
{/strip}