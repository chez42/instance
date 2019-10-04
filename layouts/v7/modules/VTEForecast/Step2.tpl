{*/* * *******************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="installationContents" style="padding-left: 3%;padding-right: 3%">
        <form name="EditWorkflow" action="index.php" method="post" id="installation_step2" class="form-horizontal">
            <input type="hidden" class="step" value="2" />

            <div class="padding1per" style="border:1px solid #ccc; padding: 0px 10px;">
                <h3>{vtranslate('LBL_WELCOME','VTEForecast')} {vtranslate('MODULE_LBL','VTEForecast')} {vtranslate('LBL_INSTALLATION_WIZARD','VTEForecast')}</h3>
                <br />
                <div class="row">
                    <div class="col-lg-12">{vtranslate('LBL_YOU_ARE_REQUIRED_VALIDATE','VTEForecast')}</div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-md-3"><strong>{vtranslate('LBL_VTIGER_URL','VTEForecast')}</strong></div>
                    <div class="col-lg-10 col-md-9">{$SITE_URL}</div>
                </div>
                <div class="row">
                    <div class="col-lg-2 col-md-3"><strong>{vtranslate('LBL_LICENSE_KEY','VTEForecast')}</strong></div>
                    <div class="col-lg-8 col-md-6"><input type="text" id="license_key" name="license_key" value="" data-validation-engine="validate[required]" class="form-control" name="summary"></div>
                    <div class="col-lg-2 col-md-3">
                        {if $VTELICENSE->result eq 'bad' || $VTELICENSE->result eq 'invalid'}
                            <label class="label label-danger">{$VTELICENSE->message}</label>
                        {/if}
                    </div>
                </div>                
                <br />
                <div class="control-group">
                    <div><span>{vtranslate('LBL_HAVE_TROUBLE','VTEForecast')} {vtranslate('LBL_CONTACT_US','VTEForecast')}</span></div>
                </div>
                <div class="control-group">
                    <ul style="padding-left: 10px;">
                        <li>{vtranslate('LBL_EMAIL','VTEForecast')}: &nbsp;&nbsp;<a href="mailto:Support@VTExperts.com">Support@VTExperts.com</a></li>
                        <li>{vtranslate('LBL_PHONE','VTEForecast')}: &nbsp;&nbsp;<span>+1 (818) 495-5557</span></li>
                        <li>{vtranslate('LBL_CHAT','VTEForecast')}: &nbsp;&nbsp;{vtranslate('LBL_AVAILABLE_ON','VTEForecast')} <a href="http://www.vtexperts.com" target="_blank">http://www.VTExperts.com</a></li>
                    </ul>
                </div>

                <div class="control-group" style="text-align: center; margin-bottom: 5px;">
                    <button class="btn btn-success" name="btnActivate" type="button"><strong>{vtranslate('LBL_ACTIVATE', 'VTEForecast')}</strong></button>
                    <button class="btn btn-info" name="btnOrder" type="button" onclick="window.open('https://www.vtexperts.com/vtiger-extensions/')"><strong>{vtranslate('LBL_ORDER_NOW', 'VTEForecast')}</strong></button>
                </div>
            </div>
            <div class="clearfix"></div>
        </form>
    </div>
{/strip}