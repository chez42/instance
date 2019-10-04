{strip}
{if count($DATA) gt 0 }
	<input class="widgetData" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
	<div class="widgetChartContainer" id="us_heat_map_chart" style="height:250px;width:85%"></div>
{else}
	<span class="noDataMsg">
		{vtranslate('LBL_EQ_ZERO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
	</span>
{/if}
{/strip}