{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is: vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}


 
<style>
	.pyramidChart {
		width: 100%;
		max-height: 600px;
		height: 100vh;
	}
</style>
<div class="dashboardWidgetData main_content">
	<input type="hidden" id="chart_data" value='{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($CHART_DATA))}' />
	<div class="pyramidChart" id="chartdiv"></div>
	{if empty($CHART_DATA)}
		<span class="noDataMsg">
			{vtranslate('LBL_NO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
		</span>
	{/if}
</div>
 
 
 
 
 

