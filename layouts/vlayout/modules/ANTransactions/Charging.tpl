{*/* ********************************************************************************
* The content of this file is subject to the VTEPayments("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div id="an-charging-container-popup" style="width: 550px; height: 185px; overflow-y: auto; padding: 20px; text-align: center;">
    <div class="row-fluid">
        <input type="hidden" id="charging-payment-id" value="{$PAYMENT_ID}" />
        <input type="hidden" id="charging-invoice-id" value="{$INVOICE_ID}" />
        <input type="hidden" id="charging-amount" value="{$AMOUNT_PAID}" />
        <div class="span12">
            <h4>{vtranslate('LBL_SELECT_PAMENT_METHOD', $MODULE)}</h4>
            <br />
            <select id="an-payment-profile-id" class="chzn-select input-xxlarge">
                <option value="">{vtranslate('LBL_SELECT_PAMENT_METHOD', $MODULE)}</option>
                {foreach item=PAYMENT_PROFILE from=$AN_CUSTOMER_PAYMENT_PROFILES}
                    <option value="{$PAYMENT_PROFILE.anpaymentprofileid}" {if $AN_CUSTOMER_PAYMENT_PROFILE_COUNT eq 1}selected=""{/if}>{$PAYMENT_PROFILE.payment_profile_label}</option>
                {/foreach}
            </select>
        </div>
        <div class="span12" style="margin-top: 20px; margin-left: 0;">
            <h2>$ {$AMOUNT_PAID_DISPLAY}</h2>
        </div>
        <div class="span12" style="margin-top: 30px; margin-left: 0;">
            <a class="cancelLink cancelLinkContainer" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
            <button class="btn btn-success" type="button" id="submit-btn"><strong>{vtranslate('LBL_SUBMIT', $MODULE)}</strong></button>
        </div>
    </div>
</div>
{/strip}