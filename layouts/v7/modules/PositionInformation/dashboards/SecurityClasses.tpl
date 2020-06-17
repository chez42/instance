<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent">
	{include file="dashboards/SecurityClassesContent.tpl"|@vtemplate_path:$MODULE_NAME}
</div>

<script type="text/javascript">
	Vtiger_AmChartPie_Widget_Js('Vtiger_SecurityClasses_Widget_Js',{},{});
</script>
<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
    </div>
</div>