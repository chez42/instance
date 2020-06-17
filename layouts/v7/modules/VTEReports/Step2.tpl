{*/* * *******************************************************************************
* The content of this file is subject to the ControlLayoutFields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
<style>
    #breadcrumb .crumbs li{
        border-left: 20px solid #ffffff
    }
    .crumbs li {
        height: 0;
        border-top: 20px solid #ECECEC;
        border-bottom: 20px solid #ECECEC;
        border-left: 20px solid transparent;
        display: inline-block;
        cursor: pointer;
        box-shadow: 0 1px #ddd;
        margin-right: 5px;
    }
    .padding1per{
        padding: 1%;
    }
    .form-horizontal .control-label {
        float: left;
        width: 140px;
        padding-top: 5px;
        text-align: right;
    }
    .control-group div.controls{
        padding-top: 7px;
        margin-left: 158px;
    }
    #license_key{
        width: 50%;
        margin-top: -3px;
    }
</style>
{strip}
    <div class="installationContents" style="padding-left: 3%;padding-right: 3%;    margin-top: 12px;">
        <form name="EditWorkflow" action="index.php" method="post" id="installation_step2" class="form-horizontal">
            <input type="hidden" class="step" value="2" />


            <div class="padding1per" style="border:1px solid #ccc;">
                <label>
                    <strong>{vtranslate('LBL_WELCOME',$QUALIFIED_MODULE)} {vtranslate('MODULE_LBL',$QUALIFIED_MODULE)} {vtranslate('LBL_INSTALLATION_WIZARD',$QUALIFIED_MODULE)}</strong>
                </label>
                <br>
                <div class="control-group" style="margin-top:15px;">
                    <div>
                        <span>
                            {vtranslate('LBL_YOU_ARE_REQUIRED_VALIDATE',$QUALIFIED_MODULE)}
                        </span>
                    </div>
                </div>
                <div class="control-group" style="margin-bottom:10px;">
                    <div class="control-label"><strong>{vtranslate('LBL_VTIGER_URL',$QUALIFIED_MODULE)}</strong></div>
                    <div class="controls" style="margin-top: 5px;">
                        <span>
                            {$SITE_URL}
                        </span>
                    </div>
                </div>
                <div class="control-group" style="margin-bottom:10px;">
                    <div class="control-label"><strong>{vtranslate('LBL_LICENSE_KEY',$QUALIFIED_MODULE)}</strong></div>
                    <div class="controls"><input type="text" id="license_key" name="license_key" value="" data-validation-engine="validate[required]" class="span4" name="summary"></div>
                </div>
                {if $VTELICENSE->result eq 'bad' || $VTELICENSE->result eq 'invalid'}
                    <div class="alert alert-error" id="error_message">
                        {$VTELICENSE->message}
                    </div>
                {/if}


                <div class="control-group" style="margin-top: 25px;">
                    <div><span>{vtranslate('LBL_HAVE_TROUBLE',$QUALIFIED_MODULE)} {vtranslate('LBL_CONTACT_US',$QUALIFIED_MODULE)}</span></div>
                </div>
                <div class="control-group" style="margin-top: 10px;">
                    <ul style="padding-left: 40px;">
                        <li>{vtranslate('LBL_EMAIL',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<a href="mailto:Support@VTExperts.com">Support@VTExperts.com</a></li>
                        <li>{vtranslate('LBL_PHONE',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<span>+1 (818) 495-5557</span></li>
                        <li>{vtranslate('LBL_CHAT',$QUALIFIED_MODULE)}: &nbsp;&nbsp;{vtranslate('LBL_AVAILABLE_ON',$QUALIFIED_MODULE)} <a href="http://www.vtexperts.com" target="_blank">http://www.VTExperts.com</a></li>
                    </ul>
                </div>

                <div class="control-group" style="text-align: center;">
                    <button class="btn btn-success" name="btnActivate" type="button"><strong>{vtranslate('LBL_ACTIVATE', $QUALIFIED_MODULE)}</strong></button>
                    <button class="btn btn-info" name="btnOrder" type="button" onclick="window.open('https://www.vtexperts.com/product/vtiger-professional-reports/')"><strong>{vtranslate('LBL_ORDER_NOW', $QUALIFIED_MODULE)}</strong></button>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </form>
</div>
{/strip}