<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent" id="account_activity_chart">
	{include file="dashboards/PortfolioWidgetContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<script type="text/javascript">
	Vtiger_Accountactivity_Widget_Js('PortfolioInformation_Accountactivity_Widget_Js',{},{});
</script>
