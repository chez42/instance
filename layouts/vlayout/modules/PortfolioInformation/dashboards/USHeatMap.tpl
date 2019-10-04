<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<div class="dashboardWidgetContent contact_heat_map">
	{include file="dashboards/USHeatMapContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<script type="text/javascript">
	Vtiger_Usheatmap_Widget_Js('PortfolioInformation_Usheatmap_Widget_Js',{},{});
</script>