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
{*17-Oct-2018*}
{strip}
	{assign var=MODULE_NAME value="Task"}
	<div class="summaryWidgetContainer">
		<div class="widget_header clearfix">
			<h4 class="display-inline-block pull-left">{vtranslate('Tasks',$MODULE_NAME)}</h4>
			
			{assign var=CALENDAR_MODEL value = Vtiger_Module_Model::getInstance('Task')}
			<div class="pull-right" style="margin-top: -5px;">
				{if $CALENDAR_MODEL->isPermitted('CreateView')}
					<button class="btn addButton btn-sm btn-default toDotask textOverflowEllipsis max-width-100" title="{vtranslate('LBL_ADD_TASK',$MODULE_NAME)}" type="button" href="javascript:void(0)" data-url="sourceModule={$RECORD->getModuleName()}&sourceRecord={$RECORD->getId()}&relationOperation=true" >
						<i class="fa fa-plus"></i>&nbsp;&nbsp;{vtranslate('LBL_ADD_TASK',$MODULE_NAME)}
					</button>&nbsp;&nbsp;
					
				{/if}
			</div>
			{assign var=SOURCE_MODEL value=$RECORD}
		</div>
		<div class="widget_contents">
			{if count($TASKS) neq '0'}
				{foreach item=RECORD key=KEY from=$TASKS}
					{assign var=DUE_DATE value=$RECORD->get('due_date')}
					
					{assign var=EDITVIEW_PERMITTED value=isPermitted('Task', 'EditView', $RECORD->get('crmid'))}
					{assign var=DETAILVIEW_PERMITTED value=isPermitted('Task', 'DetailView', $RECORD->get('crmid'))}
					{assign var=DELETE_PERMITTED value=isPermitted('Task', 'Delete', $RECORD->get('crmid'))}
					<div class="activityEntries activities">
						<input type="hidden" class="activityId" value="{$RECORD->getId()}"/>
						<input type="hidden" class="activityModule" value="{$RECORD->getModuleName()}"/>
						<div class='media'>
							<div class='row'>
								<div class='media-left module-icon col-lg-1 col-md-1 col-sm-1 textAlignCenter'>
									<span class='vicon-task'></span>
								</div>
								<div class='media-body col-lg-7 col-md-7 col-sm-7'>
									<div class="summaryViewEntries">
										{if $DETAILVIEW_PERMITTED == 'yes'}<a href="{$RECORD->getDetailViewUrl()}" title="{$RECORD->get('subject')}">{$RECORD->get('subject')}</a>{else}{$RECORD->get('subject')}{/if}&nbsp;&nbsp;
										{if $EDITVIEW_PERMITTED == 'yes'}<a href="{$RECORD->getEditViewUrl()}&sourceModule={$SOURCE_MODEL->getModuleName()}&sourceRecord={$SOURCE_MODEL->getId()}&relationOperation=true" class="fieldValue"><i class="summaryViewEdit fa fa-pencil" title="{vtranslate('LBL_EDIT',$MODULE_NAME)}"></i></a>{/if}&nbsp;
										{if $DELETE_PERMITTED == 'yes'}<a onclick="Vtiger_Detail_Js.deleteRelatedTasks(event);" data-id="{$RECORD->getId()}" class="fieldValue"><i class="summaryViewEdit fa fa-trash " title="{vtranslate('LBL_DELETE',$MODULE_NAME)}"></i></a>{/if}
									</div>
								<span><strong title="{Vtiger_Util_Helper::formatDateTimeIntoDayString("$DUE_DATE")}">{Vtiger_Util_Helper::formatDateIntoStrings($DUE_DATE)}</strong></span>
							</div>

							<div class='col-lg-4 col-md-4 col-sm-4 activityStatus' style='line-height: 0px;padding-right:30px;'>
								<div class="row">
									
										{assign var=MODULE_NAME value=$RECORD->getModuleName()}
										<input type="hidden" class="activityModule" value="{$RECORD->getModuleName()}"/>
										<input type="hidden" class="activityType" value="{$RECORD->get('activitytype')}"/>
										<div class="pull-right">
											 {assign var=FIELD_MODEL value=$RECORD->getModule()->getField('task_status')}
											<style>
												{assign var=PICKLIST_COLOR_MAP value=Settings_Picklist_Module_Model::getPicklistColorMap('task_status', true)}
												{foreach item=PICKLIST_COLOR key=PICKLIST_VALUE from=$PICKLIST_COLOR_MAP}
													{assign var=PICKLIST_TEXT_COLOR value=Settings_Picklist_Module_Model::getTextColor($PICKLIST_COLOR)}
													{assign var=CONVERTED_PICKLIST_VALUE value=Vtiger_Util_Helper::convertSpaceToHyphen($PICKLIST_VALUE)}
														.picklist-{$FIELD_MODEL->getId()}-{Vtiger_Util_Helper::escapeCssSpecialCharacters($CONVERTED_PICKLIST_VALUE)} {
															background-color: {$PICKLIST_COLOR};color: {$PICKLIST_TEXT_COLOR};
														}
												{/foreach}
											</style>
											<strong><span class="value picklist-color picklist-{$FIELD_MODEL->getId()}-{Vtiger_Util_Helper::convertSpaceToHyphen($RECORD->get('task_status'))}">{vtranslate($RECORD->get('task_status'),$MODULE_NAME)}</span></strong>&nbsp&nbsp;
											{if $EDITVIEW_PERMITTED == 'yes'}
												<span class="editStatus cursorPointer"><i class="fa fa-pencil" title="{vtranslate('LBL_EDIT',$MODULE_NAME)}"></i></span>
												<span class="edit hide">
													{assign var=FIELD_VALUE value=$FIELD_MODEL->set('fieldvalue', $RECORD->get('task_status'))}
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME OCCUPY_COMPLETE_WIDTH='true'}
													<input type="hidden" class="fieldname" value='{$FIELD_MODEL->get('name')}' data-prev-value='{$FIELD_MODEL->get('fieldvalue')}' />
												</span>
											{/if}
										</div>
									
								</div>
							</div>
						</div>
					</div>
					<hr>
				</div>
			{/foreach}
		{else}
			<div class="summaryWidgetContainer noContent">
				<p class="textAlignCenter">{vtranslate('LBL_NO_PENDING_TASKS',$MODULE_NAME)}</p>
			</div>
		{/if}
		{if $PAGING_MODEL->isNextPageExists()}
			<div class="row">
				<div class="textAlignCenter">
					<a href="javascript:void(0)" class="moreRecentActivityTasks">{vtranslate('LBL_SHOW_MORE',$MODULE_NAME)}</a>
				</div>
			</div>
		{/if}
	</div>
</div>
{/strip}