{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
	{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
		<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
	{/if}

    {if $RECORD && $EDITTEMPLATE neq "1"}
	<div name='editContent'>
		{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
			{if $BLOCK_FIELDS|@count gt 0}
				<div class='fieldBlockContainer'>
					<h4 class='fieldBlockHeader'>{vtranslate($BLOCK_LABEL, $MODULE)}</h4>
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
							{/foreach}
							{*If their are odd number of fields in edit then border top is missing so adding the check*}
							{if $COUNTER is odd}
								<td></td>
								<td></td>
							{/if}
						</tr>
					</table>
				</div>
			{/if}
		{/foreach}
	</div>
	{else}
        {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
			<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
        {/if}
		<div name='editContent'>
			<div class='fieldBlockContainer'>
            <span>
                <h4 class='fieldBlockHeader' >{vtranslate('SINGLE_EmailTemplates', $MODULE)}</h4>
            </span>
				<hr>
				<table class="table table-borderless">
					<tbody>
					<tr>
						<td class="fieldLabel {$WIDTHTYPE} alignMiddle">{vtranslate('LBL_TEMPLATE_NAME', $MODULE)}&nbsp;<span class="redColor">*</span></td>
						<td class="fieldValue {$WIDTHTYPE}">
							<input id="{$MODULE}_editView_fieldName_templatename" type="text" class="inputElement" data-rule-required="true" name="templatename" value="{$RECORD->get('templatename')}">
						</td>
					</tr>
					<tr>
						<td class="fieldLabel {$WIDTHTYPE} alignMiddle">{vtranslate('LBL_DESCRIPTION', $MODULE)}</td>
						<td class="fieldValue {$WIDTHTYPE}">
							<textarea class="inputElement col-lg-12" id="description" name="description">{$RECORD->get('description')}</textarea>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class='fieldBlockContainer'>
            <span>
                <h4 class='fieldBlockHeader'>{vtranslate('LBL_EMAIL_TEMPLATE', $MODULE)} {vtranslate('LBL_DESCRIPTION', $MODULE)}</h4>
            </span>
				<hr>
				<table class="table table-borderless">
					<tbody>
					<tr>
						<td class="fieldLabel {$WIDTHTYPE}">{vtranslate('LBL_SELECT_FIELD_TYPE', $MODULE)}&nbsp;<span class="redColor">*</span></td>
						<td class="fieldValue {$WIDTHTYPE}">
                            <span class="filterContainer" >
                                <input type=hidden name="moduleFields" data-value='{Vtiger_Functions::jsonEncode($ALL_FIELDS)}' />
                                <span class="col-sm-4 col-xs-4 conditionRow">
                                    <select class="inputElement select2" name="modulename" data-rule-required="true">
                                        <option value="">{vtranslate('LBL_SELECT_MODULE',$MODULE)}</option>
                                        {foreach key=MODULENAME item=FIELDS from=$ALL_FIELDS}
											<option  value="{$MODULENAME}" {if $MODULENAME eq 'Contacts'}selected{/if}>{vtranslate($MODULENAME, $MODULENAME)}</option>
                                        {/foreach}
                                    </select>
                                </span>&nbsp;&nbsp;
                                <span class="col-sm-6 col-xs-6">
                                    <select class="inputElement select2 col-sm-5 col-xs-5" id="templateFields" name="templateFields">
                                        <option value="">{vtranslate('LBL_NONE',$MODULE)}</option>
                                    </select>
                                </span>
                            </span>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel {$WIDTHTYPE}">{vtranslate('LBL_GENERAL_FIELDS', $MODULE)}</td>
						<td class="fieldValue {$WIDTHTYPE}">
                            <span class="col-sm-6 col-xs-6">
                                <select class="inputElement select2 col-sm5 col-xs-5" id="generalFields" name="generalFields">
                                    <option value="">{vtranslate('LBL_NONE',$MODULE)}</option>
                                    <optgroup label="{vtranslate('LBL_COMPANY_DETAILS','Settings:Vtiger')}">
                                        {foreach key=index item=COMPANY_FIELD from=$COMPANY_FIELDS}
											<option value="{{$COMPANY_FIELD[1]}}">{$COMPANY_FIELD[0]}</option>
                                        {/foreach}
                                    </optgroup>
                                    <optgroup label="{vtranslate('LBL_GENERAL_FIELDS', $MODULE)}">
                                        {foreach key=index item=GENERAL_FIELD from=$GENERAL_FIELDS}
											<option value="{$GENERAL_FIELD[1]}">{$GENERAL_FIELD[0]}</option>
                                        {/foreach}
                                    </optgroup>
                                </select>
                            </span>
						</td>
					</tr>
					<tr>
						<td class="fieldLabel {$WIDTHTYPE}">{vtranslate('LBL_SUBJECT', $MODULE)}&nbsp;<span class="redColor">*</span></td>
						<td class="fieldValue {$WIDTHTYPE}">
							<div class="col-sm-6 col-xs-6">
								<input id="{$MODULE}_editView_fieldName_subject" type="text" {if $IS_SYSTEM_TEMPLATE_EDIT} disabled="disabled" {/if} class="inputElement col-lg-12" data-rule-required="true" name="subject" value="{$RECORD->get('subject')}"  spellcheck="true" />
							</div>
						</td>
					</tr>
					</tbody>
				</table>
				<div class="text-center" style="width: 100%;padding-bottom:10px;">
					<a style="" class="btn-emailtemplate-builder" data-toggle="modal" data-target="#mosaico">
						Switch to Mosaico Email Template builder
					</a>

					<!-- Modal -->
					<div id="mosaico" style="overflow: hidden" class="modal fade" role="dialog">
						<div style="width: 100%;height: 100%;margin:0;" class="modal-dialog">
							<!-- Modal content-->
							<div style="height: 100%"class="modal-content">
								<iframe id="iframe-content" style="width: 100%; height:calc(100% - 61px)" src="{$current_url}test/mosaico"></iframe>
								<div class="col-md-12 modal-footer" style="width: 100%;position: absolute;bottom: 0;">
									<div class="row">
										<div class="col-md-4 pull-left">
											<button type="button" class="btn btn-warning btn-reload-iframe">
												Select from Template Library
											</button>
											<span style="height: 30px;padding: 8px 10px;display: inline-block;" data-toggle="tooltip" data-placement="top" class="pull-left glyphicon glyphicon-info-sign" aria-hidden="true" title="You can select email template from many existing designs.">
										</span>
										</div>
										<div class="warning-phpimagick col-md-4">
											{if $IMAGICKCHECK eq false}
												<a href="https://www.vtexperts.com/how-to-enable-php-imagick-imagemagick/" target="_blank">
													<span data-toggle="tooltip" data-placement="top" class="glyphicon glyphicon-warning-sign warning-sign-custom"
														  aria-hidden="true" title="If php-imagick (ImageMagick) is not enabled, you will not be able to create email templates properly. The images will not show up/upload, layout will be hard to control and text will overlap. You need to enable this php extension. Please click on the warning message for instructions.">
													</span>
													<span>IMPORTANT: php-imagick is missing!</span>
												</a>
											{/if}
										</div>
										<div class="col-md-4 pull-right">
											<button type="button" class="btn btn-copy-paste-template">Save Template to VTiger</button>
											<button type="button" class="btn btn-default btn-close" data-dismiss="modal">Close</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="row padding-bottom1per">
                    {assign var="TEMPLATE_CONTENT" value=$RECORD->get('body')}
					<textarea id="templatecontent" name="templatecontent" {if $IS_SYSTEM_TEMPLATE_EDIT} data-rule-required="true" {/if} >
                    {if !empty($TEMPLATE_CONTENT)}
                        {$TEMPLATE_CONTENT}
                    {else}
                        {include file="DefaultContentForTemplates.tpl"|@vtemplate_path:$MODULE}
                    {/if}
                </textarea>
				</div>
			</div>
		</div>
	{/if}
{/strip}
