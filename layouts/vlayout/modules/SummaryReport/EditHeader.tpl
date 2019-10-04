{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <div class="editContainer" style="padding-left: 2%;padding-right: 2%">
		<br>
        <h3>
            {if $RECORD_ID eq ''}
                {vtranslate('LBL_CREATING_REPORT','Reports')}
            {else}
                {vtranslate('LBL_EDITING_REPORT','Reports')} : {$REPORT_DETAIL['reportname']}
			{/if}
        </h3>
        <hr>
        <div>
            <ul class="crumbs marginLeftZero">
                <li class="first step"  style="z-index:9" id="step1">
                    <a>
                        <span class="stepNum">1</span>
                        <span class="stepText">{vtranslate('LBL_REPORT_DETAILS','Reports')}</span>
                    </a>
                </li>
                <li class="step last" style="z-index:7" id="step2">
                    <a>
                        <span class="stepNum">2</span>
                        <span class="stepText">{vtranslate('LBL_FILTERS','Reports')}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="clearfix"></div>
    </div>
{/strip}