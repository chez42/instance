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
	<style>
		.details.row{
			background : #fff;
		}
	</style>
	{foreach item=DETAIL_VIEW_WIDGET from=$DETAILVIEW_LINKS['DETAILVIEWWIDGET']}
		{if ($DETAIL_VIEW_WIDGET->getLabel() eq 'Documents') }
			{assign var=DOCUMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
			{assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_UPDATES')}
			{assign var=UPDATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Reporting')}
			{assign var=REPORTING_REVENUE_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Holdings')}
			{assign var=HOLDINGS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
{*		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Historical Information')}
			{assign var=HISTORICAL_INFORMATION_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}*}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Reports')}
			{assign var=REPORT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'Balance History')}
			{assign var=BALANCE_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
		{/if}
	{/foreach}
	<div class="left-block col-lg-4">
		{* Revenue Widget Model Start*}
		{if $REPORTING_REVENUE_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_risk_assessment" data-url="{$REPORTING_REVENUE_WIDGET_MODEL->getUrl()}" data-name="{$REPORTING_REVENUE_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$REPORTING_REVENUE_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($REPORTING_REVENUE_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents" style="min-height:150px;">
					</div>
				</div>
			</div>
		{/if}
		{* Revenue Widget Model End*}
	</div>
	<div class="middle-block col-lg-8">
		{* Balance Widget Model Start*}
		{if $BALANCE_WIDGET_MODEL}
			<div class="summaryWidgetContainer col-lg-8" style="border-radius:10px;">
				<div class="widgetContainer_balances" data-url="{$BALANCE_WIDGET_MODEL->getUrl()}" data-name="{$BALANCE_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$BALANCE_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block">{vtranslate($BALANCE_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents" style="min-height:150px;">
					</div>
				</div>
			</div>
		{/if}
		{* Balance Widget Model End*}
	</div>
	<div class="left-block col-lg-4">
		{* Module Summary View*}
{*		<div class="summaryView">
			<div class="summaryViewHeader">
				<h4 class="display-inline-block">{vtranslate('LBL_KEY_FIELDS', $MODULE_NAME)}</h4>
			</div>
			<div class="summaryViewFields">
				{$MODULE_SUMMARY}
			</div>
		</div>*}
		{* Module Summary View Ends Here*}

		{* Historical Information Widget Model Start*}
		{if $HISTORICAL_INFORMATION_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_historical_information" data-url="{$HISTORICAL_INFORMATION_WIDGET_MODEL->getUrl()}" data-name="{$HISTORICAL_INFORMATION_WIDGET_MODEL->getLabel()}">
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

		{* Holdings Widget Model*}
		{if $HOLDINGS_WIDGET_MODEL}
			<div class="summaryWidgetContainer resizeable">
				<div class="widgetContainer_holdings" data-url="{$HOLDINGS_WIDGET_MODEL->getUrl()}" data-name="{$HOLDINGS_WIDGET_MODEL->getLabel()}">
					<div class="widget_header">
						<input type="hidden" name="relatedModule" value="{$HOLDINGS_WIDGET_MODEL->get('linkName')}" />
						<h4 class="display-inline-block" style="text-align:center; width:100%;">{vtranslate($HOLDINGS_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>
					</div>
					<div class="widget_contents">
					</div>
				</div>
			</div>
		{/if}
		{*Holdings Widget Model End*}


		{* Summary View Documents Widget*}
		{if $DOCUMENT_WIDGET_MODEL}
			<div class="summaryWidgetContainer">
				<div class="widgetContainer_documents" data-url="{$DOCUMENT_WIDGET_MODEL->getUrl()}" data-name="{$DOCUMENT_WIDGET_MODEL->getLabel()}">
					<div class="widget_header clearfix">
						<input type="hidden" name="relatedModule" value="{$DOCUMENT_WIDGET_MODEL->get('linkName')}" />
						<span class="toggleButton pull-left"><i class="fa fa-angle-down"></i>&nbsp;&nbsp;</span>
						<h4 class="display-inline-block pull-left">{vtranslate($DOCUMENT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>

						{if $DOCUMENT_WIDGET_MODEL->get('action')}
							{assign var=PARENT_ID value=$RECORD->getId()}
							<div class="pull-right">
								<div class="dropdown">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										<span class="fa fa-plus" title="{vtranslate('LBL_NEW_DOCUMENT', $MODULE_NAME)}"></span>&nbsp;{vtranslate('LBL_NEW_DOCUMENT', 'Documents')}&nbsp; <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li class="dropdown-header"><i class="fa fa-upload"></i> {vtranslate('LBL_FILE_UPLOAD', 'Documents')}</li>
										<li id="VtigerAction">
											<a href="javascript:Documents_Index_Js.uploadTo('Vtiger',{$PARENT_ID},'{$MODULE_NAME}')">
												{*<img style="  margin-top: -3px;margin-right: 4%;" title="Vtiger" alt="Vtiger" src="layouts/v7/skins//images/Vtiger.png">*}
												<i class="fa fa-desktop"> </i>  {vtranslate('LBL_FROM_COMPUTER', 'Documents' )}
											</a>
										</li>
										<li class="dropdown-header"><i class="fa fa-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', 'Documents')}</li>
										<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E',{$PARENT_ID},'{$MODULE_NAME}')">&nbsp;<i class="fa fa-external-link"></i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', 'Documents', {vtranslate('LBL_FILE_URL', 'Documents')})}</a></li>
										<li role="separator" class="divider"></li>
										<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W',{$PARENT_ID},'{$MODULE_NAME}')"><i class="fa fa-file-text"></i> {vtranslate('LBL_CREATE_NEW', 'Documents', {vtranslate('SINGLE_Documents', 'Documents')})}</a></li>
									</ul>
								</div>
							</div>
						{/if}
					</div>
					<div class="widget_contents">

					</div>
				</div>
			</div>
		{/if}
		{* Summary View Documents Widget Ends Here*}
	</div>

	<div class="middle-block col-lg-8">
		{* Summary View Related Activities Widget*}
{*		<div id="relatedActivities">
			{$RELATED_ACTIVITIES}
		</div>*]
		{* Summary View Related Activities Widget Ends Here*}

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
{/strip}