{*/* ********************************************************************************
* The content of this file is subject to the VTEPayments("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div id="authorize-net-info" class="relatedContainer">
    <div class="row-fluid" >
        <div class="span12">
            <div style="padding: 10px; background-color: #ffffcc; border-left: 6px solid #ffeb3b;">
            {vtranslate('AUTHORIZE_NET_INTROTEXT', $MODULE)}
            </div>
        </div>
    </div>
    <br />
    <br />
    <div class="row-fluid customer-profile">
        <h3><b>{vtranslate('LBL_CUSTOMER_PROFILE', $MODULE)}</b></h3>
        <div class="span12">
            {if $RECORD_EXISTED eq 1}
                <span style="color: green;">{vtranslate('LBL_AN_CUSTOMER_PROFILE_EXISTED', $MODULE)}</span>
                <a href="javascript:void(0);" class="an-c-profile" data-url="index.php?module=ANCustomers&view=EditCustomerProfileAjax&record={$RECORD.ancustomersid}&account_id={$ACCOUNT_ID}" data-record="{$RECORD.ancustomersid}" style="text-decoration: underline;">
                    {vtranslate('LBL_AN_CUSTOMER_PROFILE_VIEW', $MODULE)}
                </a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href='javascript:void(0);' class="an-c-profile-deleteRecordButton" data-url="index.php?module=ANCustomers&action=DeleteAjax&record={$RECORD.ancustomersid}">
                    <i title="Delete" class="icon-trash alignMiddle"></i>
                </a>
            {else}
                <span style="color: red;">{vtranslate('LBL_AN_CUSTOMER_PROFILE_DOES_NOT_EXIST', $MODULE)}</span>
                <a href="javascript:void(0);" class="an-c-profile" data-url="index.php?module=ANCustomers&view=EditCustomerProfileAjax&account_id={$ACCOUNT_ID}" data-record="" style="text-decoration: underline;">
                    {vtranslate('LBL_AN_CUSTOMER_PROFILE_CREATE', $MODULE)}
                </a>
            {/if}
        </div>
    </div>
    <br />
    <br />
    {if $RECORD_EXISTED eq 1}
    <div class="row-fluid payment-profiles">
        <h3><b>{vtranslate('LBL_PAYMENT_PROFILES', $MODULE)}</b></h3>
        {if $COUNT_PAYMENT_PROFILE gt 0}
        <div class="span12 list-payment-profile">
            {assign var=PAYMENT_PROFILES value=$RECORD['payment_profiles']}
            {foreach item=PAYMENT_PROFILE from=$PAYMENT_PROFILES}
                <div style="width: 100%;">
                {if $PAYMENT_PROFILE.payment_method eq 'CreditCardSimpleType'}
                    <label style="display: inline-block;">{$PAYMENT_PROFILE.card_type_display} - {$PAYMENT_PROFILE.card_number_display}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="javascript:void(0);" class="add-payment-profile" data-url="index.php?module=ANPaymentProfile&view=EditPaymentProfileAjax&account_id={$ACCOUNT_ID}&customerprofileid={$RECORD.ancustomersid}&record={$PAYMENT_PROFILE.anpaymentprofileid}" style="text-decoration: underline;">
                        {vtranslate('LBL_AN_CUSTOMER_PROFILE_VIEW', $MODULE)}
                    </a>
                {/if}
                {if $PAYMENT_PROFILE.payment_method eq 'BankAccountType'}
                    <label style="display: inline-block;">{$PAYMENT_PROFILE.bank_name_display} - {$PAYMENT_PROFILE.account_number_display}</label>&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="javascript:void(0);" class="add-payment-profile" data-url="index.php?module=ANPaymentProfile&view=EditPaymentProfileAjax&account_id={$ACCOUNT_ID}&customerprofileid={$RECORD.ancustomersid}&record={$PAYMENT_PROFILE.anpaymentprofileid}" style="text-decoration: underline;">
                        {vtranslate('LBL_AN_CUSTOMER_PROFILE_VIEW', $MODULE)}
                    </a>
                {/if}&nbsp;&nbsp;&nbsp;&nbsp;
                <a href='javascript:void(0);' class="an-p-profile-deleteRecordButton" data-url="index.php?module=ANPaymentProfile&action=DeleteAjax&record={$PAYMENT_PROFILE.anpaymentprofileid}">
                    <i title="Delete" class="icon-trash alignMiddle"></i>
                </a>
                </div>
            {/foreach}
        </div>
        <br />
        <br />
        {/if}
        <div class="span12 add-payment-profile">
            <a href="javascript:void(0);" class="add-payment-profile" data-url="index.php?module=ANPaymentProfile&view=EditPaymentProfileAjax&account_id={$ACCOUNT_ID}&customerprofileid={$RECORD.ancustomersid}&record=" >
                <label style="display: inline-block; text-decoration: underline;">{vtranslate('LBL_ADD_PAYMENT_PROFILE_BTN', $MODULE)}</label>
            </a>
        </div>
    </div>
    {/if}
</div>
{/strip}