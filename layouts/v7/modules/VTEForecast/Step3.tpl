{*/* * *******************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="installationContents" style="padding-left: 3%;padding-right: 3%">
        <form name="EditWorkflow" action="index.php" method="post" id="installation_step3" class="form-horizontal">
            <input type="hidden" class="step" value="3" />

            <div class="padding1per" style="border:1px solid #ccc; padding: 0px 10px;">
                <h3>{vtranslate('LBL_INSTALLATION_COMPLETED','VTEForecast')}</h3>
                <br>
                <div class="control-group">
                    <div>
                        <span>
                            The {vtranslate($QUALIFIED_MODULE, 'VTEForecast')} {vtranslate('LBL_HAS_BEEN_SUCCESSFULLY','VTEForecast')}
                        </span>
                    </div>
                </div>
                <div class="control-group">
                    <div>
                        <span>
                            {vtranslate('LBL_MORE_EXTENSIONS','VTEForecast')} - <a style="color: #0088cc; text-decoration:none;" href="http://www.vtexperts.com" target="_blank">http://www.VTExperts.com</a>
                        </span>
                    </div>
                </div>

                <div class="control-group">
                    <div><span>{vtranslate('LBL_FEEL_FREE_CONTACT','VTEForecast')}</span></div>
                </div>
                <div class="control-group">
                    <ul style="padding-left: 10px;">
                        <li>{vtranslate('LBL_EMAIL','VTEForecast')}: &nbsp;&nbsp;<a style="color: #0088cc; text-decoration:none;" href="mailto:Support@VTExperts.com">Support@VTExperts.com</a></li>
                        <li>{vtranslate('LBL_PHONE','VTEForecast')}: &nbsp;&nbsp;<span>+1 (818) 495-5557</span></li>
                        <li>{vtranslate('LBL_CHAT','VTEForecast')}: &nbsp;&nbsp;{vtranslate('LBL_AVAILABLE_ON','VTEForecast')} <a style="color: #0088cc; text-decoration:none;" href="http://www.vtexperts.com" target="_blank">http://www.VTExperts.com</a></li>
                    </ul>
                </div>

                <div class="control-group" style="text-align: center; margin-bottom: 5px;">
                    <button class="btn btn-success" name="btnFinish" type="button"><strong>{vtranslate('LBL_FINISH', 'VTEForecast')}</strong></button>
                </div>
            </div>
            <div class="clearfix"></div>
        </form>
    </div>
{/strip}