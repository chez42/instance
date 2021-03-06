{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
*
 ********************************************************************************/
-->*}
{strip}
{foreach item=DETAIL_VIEW_WIDGET from=$DETAILVIEW_LINKS['DETAILVIEWWIDGET']}
	{if ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
		{assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_UPDATES')}
		{assign var=UPDATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Reporting')}
		{assign var=REPORTING_REVENUE_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
    {elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Risk Assessment')}
        {assign var=RISK_ASSESSMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
    {*{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Historical Information')}
        {assign var=HISTORICAL_INFORMATION_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}*}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Reports')}
		{assign var=REPORT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Balance History')}
		{assign var=BALANCE_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{/if}
{/foreach}

{assign var=PortfolioModel value=Vtiger_Module_Model::getInstance('PortfolioInformation')}

{if $PortfolioModel->isActive()}
	<div class="top-block container-fluid">
		{* Balance Widget Model Start*}
		{if $BALANCE_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_balances" data-url="{$BALANCE_WIDGET_MODEL->getUrl()}" data-name="{$BALANCE_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$BALANCE_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($BALANCE_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents" style="width:100%; height:200px;">
					</div>
				</div>
			</div>
		{/if}
		{* Balance Widget Model End*}
	</div>
{/if}
<div class="left-block col-lg-4">
	{* Module Summary View*}
		<div class="summaryView">
			<div class="summaryViewHeader">
				<h4 class="display-inline-block">{vtranslate('LBL_KEY_FIELDS', $MODULE_NAME)}</h4>
			</div>
			<div class="summaryViewFields">
				{$MODULE_SUMMARY}
			</div>
		</div>
	{* Module Summary View Ends Here*}
	{if $PortfolioModel->isActive()}
		{* Historical Information Widget Model Start*}
		{if $HISTORICAL_INFORMATION_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_historical_information" data-url="{$HISTORICAL_INFORMATION_WIDGET_MODEL->getUrl()}" data-name="{$COMMENTS_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$HISTORICAL_INFORMATION_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($HISTORICAL_INFORMATION_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Historical Information Widget Model End*}
	{/if}
	{* Summary View Comments Widget*}
	{if $COMMENTS_WIDGET_MODEL}
		<div class="summaryWidgetContainer">
			<div class="widgetContainer_comments" data-url="{$COMMENTS_WIDGET_MODEL->getUrl()}" data-name="{$COMMENTS_WIDGET_MODEL->getLabel()}">
				<div class="widget_header">
					<input type="hidden" name="relatedModule" value="{$COMMENTS_WIDGET_MODEL->get('linkName')}" />
					<h4 class="display-inline-block">{vtranslate($COMMENTS_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
				</div>
				<div class="widget_contents">
				</div>
			</div>
		</div>
	{/if}
	{* Summary View Comments Widget Ends Here*}
	
	
</div>

<div class="middle-block col-lg-4 col-md-4 col-sm-4">
	{*17-Oct-2018*}
	{* Summary View Task Widget*}
		<div id="relatedTasks">
			{$RELATED_TASKS}
		</div>
		{* Summary View Task Widget Ends Here*}
			
	{* Summary View Related Activities Widget*}
		<div id="relatedActivities">
			{$RELATED_ACTIVITIES}
		</div>
	{* Summary View Related Activities Widget Ends Here*}
	{if $PortfolioModel->isActive()}
		{* Report Widget Model Start*}
		{if $REPORT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_report" data-url="{$REPORT_WIDGET_MODEL->getUrl()}" data-name="{$REPORT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$REPORT_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($REPORT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Report Widget Model End*}
	
		{* Risk Assessment Widget Model Start*}
	    {if $RISK_ASSESSMENT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_risk_assessment" data-url="{$RISK_ASSESSMENT_WIDGET_MODEL->getUrl()}" data-name="{$RISK_ASSESSMENT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$RISK_ASSESSMENT_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($RISK_ASSESSMENT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
	    {/if}
	    {* Risk Assessment Widget Model End*}
	{/if}
	
</div>
{if $PortfolioModel->isActive()}
	<div class="right-block col-lg-4 col-sm-4 col-md-4">
		{* Revenue Widget Model Start*}
		{if $REPORTING_REVENUE_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_risk_assessment" data-url="{$REPORTING_REVENUE_WIDGET_MODEL->getUrl()}" data-name="{$REPORTING_REVENUE_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$REPORTING_REVENUE_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($REPORTING_REVENUE_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{* Revenue Widget Model End*}
	</div>
{/if}
{/strip}