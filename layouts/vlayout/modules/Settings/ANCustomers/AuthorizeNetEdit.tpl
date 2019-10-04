{*/* ********************************************************************************
* The content of this file is subject to the VTEAuthnet("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <form action="index.php?module=ANCustomers&parent=Settings&view=Settings" method="post" id="EditAuthnetSetting">
        <div class="contentHeader row-fluid">
            {if !$AUTHNET_LIB_EXISTED}
                <div class="span12">
                    <div class="text-warning bg-warning" style="padding: 10px; margin-bottom: 10px; background-color: #f9edbe; color: #333;">{vtranslate('LBL_AUTHNET_LIB_MISSING', $QUALIFIED_MODULE)}</div>
                </div>
            {/if}
            <div class="span12 btn-toolbar" style="margin-left: 0;">
                <div class="pull-right">
                    <button class="btn btn-success saveButton" type="submit" title="{vtranslate('LBL_SAVE',$QUALIFIED_MODULE)}"><strong>{vtranslate('LBL_SAVE',$QUALIFIED_MODULE)}</strong></button>
                    <a type="reset" class="cancelLink" href="javascript:history.back();" title="{vtranslate('LBL_CANCEL',$QUALIFIED_MODULE)}">{vtranslate('LBL_CANCEL',$QUALIFIED_MODULE)}</a>
                </div>
            </div>
        </div>
        <hr>
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
                                        <select class="chzn-select inputElement" name="setting[integrate_authorize_net][active]">
                                            <option value="1" {if $INTEGRATE_AUTHORIZE_NET.active eq 1}selected="" {/if}>{vtranslate('LBL_ACTIVE',$QUALIFIED_MODULE)}</option>
                                            <option value="0" {if $INTEGRATE_AUTHORIZE_NET.active eq 0}selected="" {/if}>{vtranslate('LBL_INACTIVE',$QUALIFIED_MODULE)}</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="medium">
                                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_AUTHORIZE_NET_MODE',$QUALIFIED_MODULE)}</label>
                                        <span class="redColor pull-right">*</span>
                                    </td>
                                    <td class="medium" style="border-left: none;">
                                        <select id="integrate_authorize_net_mode" class="chzn-select inputElement" name="setting[integrate_authorize_net][mode]">
                                            <option value="">{vtranslate('LBL_SELECT_AN_OPTION',$QUALIFIED_MODULE)}</option>
                                            <option value="https://apitest.authorize.net" {if $INTEGRATE_AUTHORIZE_NET.mode eq 'https://apitest.authorize.net'}selected="" {/if}>{vtranslate('LBL_AUTHORIZE_NET_MODE_SANBOX',$QUALIFIED_MODULE)}</option>
                                            <option value="https://api2.authorize.net" {if $INTEGRATE_AUTHORIZE_NET.mode eq 'https://api2.authorize.net'}selected="" {/if}>{vtranslate('LBL_AUTHORIZE_NET_MODE_PRODUCTION',$QUALIFIED_MODULE)}</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="medium">
                                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_VALIDATION_MODE',$QUALIFIED_MODULE)}</label>
                                        <span class="redColor pull-right">*</span>
                                    </td>
                                    <td class="medium" style="border-left: none;">
                                        <select id="integrate_authorize_net_validation_mode" class="chzn-select inputElement" name="setting[integrate_authorize_net][validation_mode]">
                                            <option value="">{vtranslate('LBL_SELECT_AN_OPTION',$QUALIFIED_MODULE)}</option>
                                            <option value="testMode" {if $INTEGRATE_AUTHORIZE_NET.validation_mode eq 'testMode'}selected="" {/if}>{vtranslate('LBL_VALIDATION_MODE_TEST_MODE',$QUALIFIED_MODULE)}</option>
                                            <option value="liveMode" {if $INTEGRATE_AUTHORIZE_NET.validation_mode eq 'liveMode'}selected="" {/if}>{vtranslate('LBL_VALIDATION_MODE_LIVE_MODE',$QUALIFIED_MODULE)}</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="medium">
                                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_MERCHANT_LOGIN_ID',$QUALIFIED_MODULE)}</label>
                                        <span class="redColor pull-right">*</span>
                                    </td>
                                    <td class="medium" style="border-left: none;">
                                        <input type="text" id="integrate_authorize_net_merchant_login_id" class="inputElement" value="{$INTEGRATE_AUTHORIZE_NET.merchant_login_id}" name="setting[integrate_authorize_net][merchant_login_id]"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="medium">
                                        <label class="muted pull-right marginRight10px">{vtranslate('LBL_MERCHANT_TRANSACTION_KEY',$QUALIFIED_MODULE)}</label>
                                        <span class="redColor pull-right">*</span>
                                    </td>
                                    <td class="medium" style="border-left: none;">
                                        <input type="text" id="integrate_authorize_net_merchant_transaction_key" class="inputElement" value="{$INTEGRATE_AUTHORIZE_NET.merchant_transaction_key}" name="setting[integrate_authorize_net][merchant_transaction_key]"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="module" value="ANCustomers" />
        <input type="hidden" name="parent" value="Settings" />
        <input type="hidden" name="action" value="Save" />
    </form>
</div>

