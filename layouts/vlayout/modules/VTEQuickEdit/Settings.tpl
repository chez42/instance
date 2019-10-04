{*<!--
/* ********************************************************************************
 * The content of this file is subject to the VTEQuickEdit Search ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div class="container-fluid">
        <div class="widget_header row-fluid">
            <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
        </div>
        <hr>
        <div class="row-fluid">
        <span class="span4">
            <p>
                <strong>{vtranslate('LBL_STATUS', $MODULE_NAME)}</strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" id="status" name="status" {if $STATUS == 1}checked{/if} />
            </p>
        </span>
        </div>
        <div class="clearfix"></div>
        <div>
            <div style="padding: 10px; text-align: justify; font-size: 14px; border: 1px solid #ececec; border-left: 5px solid #2a9bbc; border-radius: 5px; overflow: hidden;">
                <h4 style="color: #2a9bbc; margin: 0px -15px 10px -15px; padding: 0px 15px 8px 15px; border-bottom: 1px solid #ececec;"><i class="fa fa-info-circle"></i>&nbsp;&nbsp;{vtranslate('LBL_INFO_BLOCK', $QUALIFIED_MODULE)}</h4>
                {vtranslate('LBL_INFO_BLOCK_ON_SETTING_PAGE', $QUALIFIED_MODULE)}
            </div>
        </div>
    </div>
{/strip}