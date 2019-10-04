<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent" id="asset_allocation_chart">
	{include file="dashboards/PortfolioWidgetContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<script type="text/javascript">
	Vtiger_AssetAllocation_Widget_Js('PortfolioInformation_Assetallocation_Widget_Js',{},{});
</script>
 
<div class="widgeticons dashBoardWidgetFooter">

    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
    </div>
</div>