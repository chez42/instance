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
	{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
		<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
	{/if}

	<div name='editContent'>
		{if $DUPLICATE_RECORDS}
			<div class="fieldBlockContainer duplicationMessageContainer">
				<div class="duplicationMessageHeader"><b>{vtranslate('LBL_DUPLICATES_DETECTED', $MODULE)}</b></div>
				<div>{getDuplicatesPreventionMessage($MODULE, $DUPLICATE_RECORDS)}</div>
			</div>
		{/if}
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
			{if $BLOCK_FIELDS|@count gt 0}
				<div class='fieldBlockContainer' data-block="{$BLOCK_LABEL}">
					<h4 class='fieldBlockHeader'>{vtranslate($BLOCK_LABEL, $MODULE)}</h4>
					{if $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION'}
						<div class="pull-right" style="margin-top:-35px !important;">
							{assign var=CALENDAR_TEMPLATES value=CalendarTemplate_Module_Model::getAllTemplates()}
							<select class="select2 inputElement" name="template_id" >
								<option value="">Select Template</option>
								{foreach item=TEMPLATE_NAME key=TEMPLATE_VALUE from=$CALENDAR_TEMPLATES}
									<option value="{$TEMPLATE_VALUE}">{$TEMPLATE_NAME}</option>
								{/foreach}
							</select>
						</div>
					{/if}
					<hr>
					<table class="table table-borderless">
						<tr>
							{assign var=COUNTER value=0}
							{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
								{assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
								{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
								{assign var="refrenceListCount" value=count($refrenceList)}
								{if $FIELD_MODEL->isEditable() eq true}
									{if $FIELD_MODEL->get('uitype') eq "19"}
										{if $COUNTER eq '1'}
											<td></td><td></td></tr><tr>
											{assign var=COUNTER value=0}
										{/if}
									{/if}
									{if $COUNTER eq 2}
									</tr><tr>
										{assign var=COUNTER value=1}
									{else}
										{assign var=COUNTER value=$COUNTER+1}
									{/if}
									<td class="fieldLabel alignMiddle">
										{if $isReferenceField eq "reference"}
											{if $refrenceListCount > 1}
												{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
												{assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
												{if !empty($REFERENCED_MODULE_STRUCTURE)}
													{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCTURE->get('name')}
												{/if}
												<select style="width: 140px;" class="select2 referenceModulesList">
													{foreach key=index item=value from=$refrenceList}
														<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $value)}</option>
													{/foreach}
												</select>
											{else}
												{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
											{/if}
										{else if $FIELD_MODEL->get('uitype') eq "83"}
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
											{if $TAXCLASS_DETAILS}
												{assign 'taxCount' count($TAXCLASS_DETAILS)%2}
												{if $taxCount eq 0}
													{if $COUNTER eq 2}
														{assign var=COUNTER value=1}
													{else}
														{assign var=COUNTER value=2}
													{/if}
												{/if}
											{/if}
										{else}
											{if $MODULE eq 'Documents' && $FIELD_MODEL->get('label') eq 'File Name'}
												{assign var=FILE_LOCATION_TYPE_FIELD value=$RECORD_STRUCTURE['LBL_FILE_INFORMATION']['filelocationtype']}
												{if $FILE_LOCATION_TYPE_FIELD}
													{if $FILE_LOCATION_TYPE_FIELD->get('fieldvalue') eq 'E'}
														{vtranslate("LBL_FILE_URL", $MODULE)}&nbsp;<span class="redColor">*</span>
													{else}
														{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
													{/if}
												{else}
													{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
												{/if}
											{else}
												{vtranslate($FIELD_MODEL->get('label'), $MODULE)}
											{/if}
										{/if}
										&nbsp;{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
									</td>
									{if $FIELD_MODEL->get('uitype') neq '83'}
										<td class="fieldValue" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
										</td>
									{/if}
								{/if}
								{if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
					                {include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
					            {/if}
							{/foreach}
							{*If their are odd number of fields in edit then border top is missing so adding the check*}
							{if $COUNTER is odd}
								<td class="empty_fields"></td>
								<td class="empty_fields"></td>
							{/if}
						</tr>
					</table>
				</div>
			{/if}
		{/foreach}
	</div>
	
	<div name='editContent'>
		<div class='fieldBlockContainer' data-block="{$BLOCK_LABEL}">
			<h4 class='fieldBlockHeader'>{vtranslate('LBL_INVITE_USER_BLOCK', $MODULE)}</h4>
			<hr>
			<table class="table table-borderless">
				<tr>
					<td class="fieldLabel alignMiddle">{vtranslate('LBL_INVITE_USERS', $MODULE)}</td>
					<td class="fieldValue">
						<select id="selectedUsers" class="select2 inputElement" multiple name="selectedusers[]">
							{foreach key=USER_ID item=USER_NAME from=$ACCESSIBLE_USERS}
								{if $USER_ID eq $CURRENT_USER->getId()}
									{continue}
								{/if}
								<option value="{$USER_ID}" {if in_array($USER_ID,$INVITIES_SELECTED)}selected{/if}>
									{$USER_NAME}
								</option>
							{/foreach}
						</select>
					</td>
					<td></td><td></td>
				</tr>
			</table>
			<input type="hidden" name="recurringEditMode" value="" />
			<!--Confirmation modal for updating Recurring Events-->
			{assign var=MODULE value="Calendar"}
			<div class="modal-dialog modelContainer recurringEventsUpdation modal-content hide" style='min-width:350px;'>
				{assign var=HEADER_TITLE value={vtranslate('LBL_EDIT_RECURRING_EVENT', $MODULE)}}
				{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
				<div class="modal-body">
					<div class="container-fluid">
						<div class="row" style="padding: 1%;padding-left: 3%;">{vtranslate('LBL_EDIT_RECURRING_EVENTS_INFO', $MODULE)}</div>
						<div class="row" style="padding: 1%;">
							<span class="col-sm-12">
								<span class="col-sm-4">
									<button class="btn btn-default onlyThisEvent" style="width : 150px">{vtranslate('LBL_ONLY_THIS_EVENT', $MODULE)}</button>
								</span>
								<span class="col-sm-8">{vtranslate('LBL_ONLY_THIS_EVENT_EDIT_INFO', $MODULE)}</span>
							</span>
						</div>
						<div class="row" style="padding: 1%;">
							<span class="col-sm-12">
								<span class="col-sm-4">
									<button class="btn btn-default futureEvents" style="width : 150px">{vtranslate('LBL_FUTURE_EVENTS', $MODULE)}</button>
								</span>
								<span class="col-sm-8">{vtranslate('LBL_FUTURE_EVENTS_EDIT_INFO', $MODULE)}</span>
							</span>
						</div>
						<div class="row" style="padding: 1%;">
							<span class="col-sm-12">
								<span class="col-sm-4">
									<button class="btn btn-default allEvents" style="width : 150px">{vtranslate('LBL_ALL_EVENTS', $MODULE)}</button>
								</span>
								<span class="col-sm-8">{vtranslate('LBL_ALL_EVENTS_EDIT_INFO', $MODULE)}</span>
							</span>
						</div>
					</div>
				</div>
			</div>
			<!--Confirmation modal for updating Recurring Events--> 
		</div>
	</div>
{/strip}