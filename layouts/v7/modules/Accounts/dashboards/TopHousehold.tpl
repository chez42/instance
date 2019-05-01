{strip}
	<div class="dashboardWidgetHeader">
		{assign var=WidgetTitle value=$WIDGET->getTitle()}
		{if $WidgetTitle eq 'Top 10 Revenue Household'}
			{assign var=WidgetHeader value="Households Revenues"}
		{else}
			{assign var=WidgetHeader value="Households AUM"}
		{/if}
		<div class="title clearfix">
			<div class="col-md-2 dashboardTitle"><b>{vtranslate('Top')}</b></div>
			<div class="col-md-3">
				<select class="widgetFilter" name="household_limit" style='width:70px;margin-bottom:0px'>
					<option value="5">{vtranslate('5')}</option>
					<option value="10">{vtranslate('10')}</option>
					<option value="15">{vtranslate('15')}</option>
					<option value="20">{vtranslate('20')}</option>
				</select>
			</div>
			<div class="col-md-7 dashboardTitle"><b>{vtranslate($WidgetHeader)}</b></div>
					
		</div>
	</div>

	<div class="dashboardWidgetContent" style='padding:5px'>
		{include file="dashboards/TopHouseholdContent.tpl"|@vtemplate_path:$MODULE_NAME}
	</div>
	
	
	<div class="widgeticons dashBoardWidgetFooter">

	    <div class="footerIcons pull-right">
	        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME SETTING_EXIST=true}
	    </div>
	</div>
{/strip}	 