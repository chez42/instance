<div class="dashboardWidgetHeader">
	{include file="dashboards/WidgetHeader.tpl"|@vtemplate_path:$MODULE_NAME}
</div>
{*Start Date <input type="text" name="revenue_start_date" id="revenue_start_date" value=""/>
End Date <input type="text" name="revenue_end_date" id="revenue_end_date" value="" />*}
{*<input type="button" name="CalculateRevenue" id="CalculateRevenue" value="Calculate Revenue" />*}
{*<input id="historical_fees" class="historical_fees" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />*}
<div id="management_fees" class="management_fees" style="width:100%; height:200px;"></div>
{*<div class="dashboardWidgetContent" id="trailing_revenue_chart">
	{include file="dashboards/PortfolioWidgetContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>*}
{*
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}*}

<div class="widgeticons dashBoardWidgetFooter">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
    </div>
</div>