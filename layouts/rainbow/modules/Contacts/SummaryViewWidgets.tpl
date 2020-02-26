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
	{if ($DETAIL_VIEW_WIDGET->getLabel() eq 'Documents') }
		{assign var=DOCUMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
		{assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'LBL_UPDATES')}
		{assign var=UPDATES_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
	{/if}
{/foreach}

<div class="left-block col-lg-5">
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

	{* Summary View Documents Widget*}
	{if $DOCUMENT_WIDGET_MODEL}
		<div class="summaryWidgetContainer">
			<div class="widgetContainer_documents" data-url="{$DOCUMENT_WIDGET_MODEL->getUrl()}" data-name="{$DOCUMENT_WIDGET_MODEL->getLabel()}">
				<div class="widget_header clearfix">
					<input type="hidden" name="relatedModule" value="{$DOCUMENT_WIDGET_MODEL->get('linkName')}" />
					
					<h4 class="display-inline-block pull-left">{vtranslate($DOCUMENT_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h4>

					{if $DOCUMENT_WIDGET_MODEL->get('action')}
						{assign var=PARENT_ID value=$RECORD->getId()}
						<div class="pull-right">
							<div class="dropdown">
								<button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
									<span class="ti-plus" title="{vtranslate('LBL_NEW_DOCUMENT', $MODULE_NAME)}"></span>&nbsp;<span class="hidden-sm hidden-xs">{vtranslate('LBL_NEW_DOCUMENT', 'Documents')}</span>&nbsp; <span class="caret"></span>
								</button>
								<ul class="dropdown-menu pull-right">
									<li class="dropdown-header"><i class="ti-upload"></i> {vtranslate('LBL_FILE_UPLOAD', 'Documents')}</li>
									<li id="VtigerAction">
										<a href="javascript:Documents_Index_Js.uploadTo('Vtiger',{$PARENT_ID},'{$MODULE_NAME}')">
											<i class="fa fa-desktop"> </i>  {vtranslate('LBL_FROM_COMPUTER', 'Documents' )}
										</a>
									</li>
									<li class="dropdown-header"><i class="ti-link"></i> {vtranslate('LBL_LINK_EXTERNAL_DOCUMENT', 'Documents')}</li>
									<li id="shareDocument"><a href="javascript:Documents_Index_Js.createDocument('E',{$PARENT_ID},'{$MODULE_NAME}')">&nbsp;<i class="ti-link"></i>&nbsp;&nbsp; {vtranslate('LBL_FROM_SERVICE', 'Documents', {vtranslate('LBL_FILE_URL', 'Documents')})}</a></li>
									<li role="separator" class="divider"></li>
									<li id="createDocument"><a href="javascript:Documents_Index_Js.createDocument('W',{$PARENT_ID},'{$MODULE_NAME}')"><i class="ti-file"></i> {vtranslate('LBL_CREATE_NEW', 'Documents', {vtranslate('SINGLE_Documents', 'Documents')})}</a></li>
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

<div class="middle-block col-lg-7">
	{if $RECORD->getField('mailingstreet')->get('fieldvalue') neq '' or $RECORD->getField('mailingcity')->get('fieldvalue') neq ''}
	<div class="summaryWidgetContainer">
	<iframe class="mapIframe" width="100%" height="200" frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyAnSgEdBwrlr3f1rJtXLa7iSiMrgVjeSGY&q=
	{if $RECORD->getField('mailingstreet')->get('fieldvalue') neq ''}
		{$RECORD->getField('mailingstreet')->get('fieldvalue')}
		{if $RECORD->getField('mailingpobox')->get('fieldvalue') neq ''}
		 {$RECORD->getField('mailingpobox')->get('fieldvalue')}
		{/if}
		,%20
	{/if}
	{if $RECORD->getField('mailingcity')->get('fieldvalue') neq ''}
		{$RECORD->getField('mailingcity')->get('fieldvalue')}
		{if $RECORD->getField('mailingstate')->get('fieldvalue') neq ''}
			%20({$RECORD->getField('mailingstate')->get('fieldvalue')}) 
		{/if}
	{/if}
	
	{if $RECORD->getField('mailingzip')->get('fieldvalue') neq ''}
		,%20{$RECORD->getField('mailingzip')->get('fieldvalue')}
	{/if}
	{if $RECORD->getField('mailingcountry')->get('fieldvalue') neq ''}
		,%20{$RECORD->getField('mailingcountry')->get('fieldvalue')}
	{/if}
	" allowfullscreen></iframe>
	
	<div style="text-align: center">
		<a href="#" class="btn btn-sm btn-primary expandBtn" onclick="jQuery('.mapIframe').attr('height','600');jQuery(this).hide();jQuery('.reduceBtn').show();">Expand Map <i class="fa fa-chevron-down"></i></a>
		<a href="#" class="btn btn-sm btn-primary reduceBtn" onclick="jQuery('.mapIframe').attr('height','200');jQuery(this).hide();jQuery('.expandBtn').show();" style="display: none">Reduce Map <i class="fa fa-chevron-up"></i></a>
	</div>
	
	</div>
	{/if}
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