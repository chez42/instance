
{strip}
	{if count($DATA) gt 0 }
		<div class="textAlignCenter clearfix" style="font-size:1.2em;margin-top:-10px;"><b>Grand Total : ${$GRAND_TOTAL|number_format:2:".":","}</b></div>
		<input class="widgetData" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
		<div class="widgetChartContainer" id="{$CHART_TYPE}" style="height:210px;width:100%;margin-top: -5px;"></div>
	{else}
		<span class="noDataMsg">
			{vtranslate('LBL_NO')} {vtranslate($MODULE_NAME, $MODULE_NAME)} {vtranslate('LBL_MATCHED_THIS_CRITERIA')}
		</span>
	{/if}
{/strip}