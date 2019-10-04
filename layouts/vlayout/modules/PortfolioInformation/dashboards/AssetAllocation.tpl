<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent" id="asset_allocation_chart">
	{include file="dashboards/PortfolioWidgetContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<script type="text/javascript">
	Vtiger_Assetallocation_Widget_Js('PortfolioInformation_Assetallocation_Widget_Js',{},{});
</script>
