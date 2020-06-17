<div class="dashboardWidgetData main_content">
	{if !empty($CHART_DATA)}
		<input type="hidden" id="chart_data" value='{Vtiger_Util_Helper::toSafeHTML(Zend_JSON::encode($CHART_DATA))}' />
		<div class="widgetChartContainer chartdiv" id="chartdiv"></div>
	{/if}
	{if empty($CHART_DATA)}
		<div class="noDataMsg">
			{vtranslate('LBL_NO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
		</div>	{/if}
</div>
 
 
 
 
 

