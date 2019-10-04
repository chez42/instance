<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
<div class="dashboardWidgetContent" id="trailing_revenue_chart">
	{include file="dashboards/PortfolioWidgetContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>


{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}

{*
<script type="text/javascript">
	Vtiger_Trailingrevenue_Widget_Js('PortfolioInformation_Trailingrevenue_Widget_Js',{},{});
</script>
*}