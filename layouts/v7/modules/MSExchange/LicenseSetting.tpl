{*/* * *******************************************************************************
* The content of this file is subject to the MSExchange ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is devitechnosolutions.com
* Portions created by devitechnosolutions.com. are Copyright(C)devitechnosolutions.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="installationContents" style="padding-left: 3%;padding-right: 3%">
        <form name="EditWorkflow" action="index.php" method="post" id="installation_step2" class="form-horizontal">
            <input type="hidden" class="step" value="2" />

            <div class="padding1per" style="border:1px solid #ccc; padding: 0px 10px;">
                <h3>{vtranslate('LBL_WELCOME',$QUALIFIED_MODULE)} {vtranslate('MODULE_LBL',$QUALIFIED_MODULE)} {vtranslate('LBL_INSTALLATION_WIZARD',$QUALIFIED_MODULE)}</h3>
                <br />
                <div class="row">
                    <div class="col-lg-2 col-md-3"><strong>{vtranslate('LBL_LICENSE_KEY',$QUALIFIED_MODULE)}</strong></div>
                    <div class="col-lg-8 col-md-6"><input type="text" id="license_key" name="license_key" value="{$EXCHANGELICENSE->license}" data-validation-engine="validate[required]" class="form-control" name="summary"></div>
                    <div class="col-lg-2 col-md-3">
                        {if $EXCHANGELICENSE->result eq 'bad' || $EXCHANGELICENSE->result eq 'invalid'}
                            <label class="label label-danger">{$EXCHANGELICENSE->message}</label>
                        {/if}
                    </div>
                </div>                
                <br />
                <div class="control-group">
                    <div><p>{vtranslate('LBL_CONTACT_US',$QUALIFIED_MODULE)}</p></div>
                </div>
                <div class="control-group">
                    <ul style="padding-left: 10px;">
                        <li>{vtranslate('LBL_EMAIL',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<a href="mailto:manish@devitechnosolutions.com">manish@devitechnosolutions.com</a></li>
                        <li>{vtranslate('LBL_PHONE',$QUALIFIED_MODULE)}: &nbsp;&nbsp;<span>+91 8437184024</span></li>
                        <li>{vtranslate('LBL_CHAT',$QUALIFIED_MODULE)}: &nbsp;&nbsp;{vtranslate('LBL_AVAILABLE_ON',$QUALIFIED_MODULE)} <a href="http://www.devitechnosolutions.com" target="_blank">http://www.devitechnosolutions.com</a></li>
                    </ul>
                </div>

                <div class="control-group" style="text-align: center; margin-bottom: 5px;">
                    {if $EXCHANGELICENSE->license neq ''}
                    	<button class="btn btn-success" name="btnReActivate" type="button"><strong>{vtranslate('LBL_UPGRADE', $QUALIFIED_MODULE)}</strong></button>
                    {else}
                    	<button class="btn btn-success" name="btnActivate" type="button"><strong>{vtranslate('LBL_ACTIVATE', $QUALIFIED_MODULE)}</strong></button>
                    {/if}
                </div>
            </div>
            <div class="clearfix"></div>
        </form>
    </div>
{/strip}