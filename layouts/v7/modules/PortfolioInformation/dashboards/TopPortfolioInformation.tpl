{strip}
	<div class="dashboardWidgetHeader">
		{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
	</div>

	<div class="dashboardWidgetContent" style='padding:5px'>
		{include file="dashboards/TopPortfolioInformationContent.tpl"|@vtemplate_path:$MODULE_NAME}
	</div>
	
	
<div class="widgeticons dashBoardWidgetFooter">

    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
    </div>
</div>
{/strip}	 