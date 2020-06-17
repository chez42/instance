{*/* ********************************************************************************
* The content of this file is subject to the VTEAuthnet("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<style type="text/css">
    .authnet-status-1{
        background-color: #008000;
        color: #ffffff;
        padding: 2px 5px;
    }
    .authnet-status-0{
        background-color: #ff0000;
        color: #ffffff;
        padding: 2px 5px;
    }
</style>
<div class="container-fluid">
    {if !$AUTHNET_LIB_EXISTED}
        <div class="contentHeader row-fluid">
            <div class="span12">
                <div class="text-warning bg-warning" style="padding: 10px; margin-bottom: 10px;">{vtranslate('LBL_AUTHNET_LIB_MISSING', $QUALIFIED_MODULE)}</div>
            </div>
        </div>
        <div class="row-fluid btn-toolbar">
            <div style="text-align: center;">
                <a href="javascript:Settings_ANCustomers_Js.downloadAuthNetLib();" class="btn btn-primary" title="{vtranslate('LBL_DOWNLOAD',$QUALIFIED_MODULE)}">{vtranslate('LBL_DOWNLOAD',$QUALIFIED_MODULE)}</a>
            </div>
        </div>
    {else}
        <div class="contentHeader row-fluid">
            <div class="span12 btn-toolbar" style="margin-left: 0;">
                <div class="">
                    <table style="width: 100%; border: none;" cellpadding="5">
                        <tr>
                            <td style="width:150px; padding: 5px; vertical-align: top;">
                                <a href="index.php?module=VTEPayments&view=List" target="_blank" class="btn btn-success">{vtranslate('LBL_PAYMENTS_LIST_VIEW', $QUALIFIED_MODULE)}</a>
                            </td>
                            <td style="vertical-align: top; padding-top: 5px;">{vtranslate('LBL_PAYMENTS_LIST_VIEW_DESC', $QUALIFIED_MODULE)}</td>
                        </tr>
                        <tr>
                            <td style="width:150px; padding: 5px; vertical-align: top;">
                                <a href="index.php?module=ANCustomers&view=List" target="_blank" class="btn btn-success">{vtranslate('LBL_CUSTOMERS_LIST_VIEW', $QUALIFIED_MODULE)}</a>
                            </td>
                            <td style="vertical-align: top; padding-top: 5px;">{vtranslate('LBL_CUSTOMERS_LIST_VIEW_DESC', $QUALIFIED_MODULE)}</td>
                        </tr>
                        <tr>
                            <td style="width:150px;  padding: 5px; vertical-align: top;">
                                <a href="index.php?module=ANPaymentProfile&view=List" target="_blank" class="btn btn-success">{vtranslate('LBL_PAYMENTPROFILE_LIST_VIEW', $QUALIFIED_MODULE)}</a>
                            </td>
                            <td style="vertical-align: top; padding-top: 5px;">{vtranslate('LBL_PAYMENTPROFILE_LIST_VIEW_DESC', $QUALIFIED_MODULE)}</td>
                        </tr>
                        <tr>
                            <td style="width:150px;  padding: 5px; vertical-align: top;">
                                <a href="index.php?module=ANTransactions&view=List" target="_blank" class="btn btn-success">{vtranslate('LBL_TRANSACTION_LIST_VIEW', $QUALIFIED_MODULE)}</a>
                            </td>
                            <td style="vertical-align: top; padding-top: 5px;">{vtranslate('LBL_TRANSACTION_LIST_VIEW_DESC', $QUALIFIED_MODULE)}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="listViewContentDiv row-fluid" id="listViewContents">
            <div class="tabbable margin0px" style="padding-bottom: 20px;">
                <ul id="extensionTab" class="nav nav-tabs" style="margin-bottom: 0px; padding-bottom: 0px;">
                    <li class="active"><a href="#authorizenet-tab" data-toggle="tab"><strong>{vtranslate('TAB_INTEGRATE_AUTHORIZE_NET', $QUALIFIED_MODULE)}</strong></a></li>
                </ul>
                <div class="tab-content row-fluid boxSizingBorderBox" style="background-color: #fff; padding: 20px; border: 1px solid #ddd; border-top-width: 0px;">
                    <div class="alert alert-info">{vtranslate('LBL_API_DETAIL_ALERT_INFO', $QUALIFIED_MODULE)}</div>
                    <div class="tab-pane active" id="authorizenet-tab">
                        <table class="table table-bordered table-condensed themeTableColor">
                            <tbody>
                            <tr>
                                <td class="medium">
                                    <label class="muted pull-right marginRight10px">{vtranslate('LBL_INTEGRATE_AUTHORIZE_NET_STATUS',$QUALIFIED_MODULE)}</label>
                                </td>
                                <td class="medium" style="border-left: none;">
                                    <span class="authnet-status-{$INTEGRATE_AUTHORIZE_NET.active}">
                                    {if $INTEGRATE_AUTHORIZE_NET.active eq 0}
                                        {vtranslate('LBL_INACTIVE',$QUALIFIED_MODULE)}
                                    {else}
                                        {vtranslate('LBL_ACTIVE',$QUALIFIED_MODULE)}
                                    {/if}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="medium">
                                    <label class="muted pull-right marginRight10px">{vtranslate('LBL_AUTHORIZE_NET_MODE',$QUALIFIED_MODULE)}</label>
                                </td>
                                <td class="medium" style="border-left: none;">
                                    {if $INTEGRATE_AUTHORIZE_NET.mode eq 'https://apitest.authorize.net'}
                                        {vtranslate('LBL_AUTHORIZE_NET_MODE_SANBOX',$QUALIFIED_MODULE)}
                                    {elseif $INTEGRATE_AUTHORIZE_NET.mode eq 'https://api2.authorize.net'}
                                        {vtranslate('LBL_AUTHORIZE_NET_MODE_PRODUCTION',$QUALIFIED_MODULE)}
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <td class="medium">
                                    <label class="muted pull-right marginRight10px">{vtranslate('LBL_VALIDATION_MODE',$QUALIFIED_MODULE)}</label>
                                </td>
                                <td class="medium" style="border-left: none;">
                                    {if $INTEGRATE_AUTHORIZE_NET.validation_mode eq 'testMode'}
                                        {vtranslate('LBL_VALIDATION_MODE_TEST_MODE',$QUALIFIED_MODULE)}
                                    {elseif $INTEGRATE_AUTHORIZE_NET.validation_mode eq 'liveMode'}
                                        {vtranslate('LBL_VALIDATION_MODE_LIVE_MODE',$QUALIFIED_MODULE)}
                                    {/if}
                                </td>
                            </tr>
                            <tr>
                                <td class="medium">
                                    <label class="muted pull-right marginRight10px">{vtranslate('LBL_MERCHANT_LOGIN_ID',$QUALIFIED_MODULE)}</label>
                                </td>
                                <td class="medium" style="border-left: none;">
                                    {$INTEGRATE_AUTHORIZE_NET.merchant_login_id}
                                </td>
                            </tr>
                            <tr>
                                <td class="medium">
                                    <label class="muted pull-right marginRight10px">{vtranslate('LBL_MERCHANT_TRANSACTION_KEY',$QUALIFIED_MODULE)}</label>
                                </td>
                                <td class="medium" style="border-left: none;">
                                    {$INTEGRATE_AUTHORIZE_NET.merchant_transaction_key}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row-fluid btn-toolbar">
            <div style="text-align: center;">
                <a href="index.php?module=ANCustomers&view=Settings&mode=AuthorizeNetEdit&parent=Settings" class="editLink btn btn-default" title="{vtranslate('LBL_EDIT',$QUALIFIED_MODULE)}">{vtranslate('LBL_EDIT',$QUALIFIED_MODULE)}</a>
            </div>
        </div>
    {/if}
</div>

