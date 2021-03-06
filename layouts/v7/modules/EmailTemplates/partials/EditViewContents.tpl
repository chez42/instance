{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
	
	{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
	{assign var=ALL_ACTIVEGROUP_LIST value=$USER_MODEL->getAccessibleGroups()}
	
	{assign var=FIELD_VALUE value=explode(' |##| ',$RECORD->get('template_view_permission'))}

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
                    {* 07-Aug-2018 change for template_view_permission *}
            		{if $USER_MODEL->isAdminUser() OR !$RECORD_ID OR $USER_MODEL->id eq $RECORD->get('creatorid') }
	            		<tr>
	            			<td class="fieldLabel {$WIDTHTYPE} alignMiddle">{vtranslate('View Permission', $MODULE)}&nbsp;<span class="redColor">*</span></td>
	                        <td class="fieldValue {$WIDTHTYPE}">
	                            <select class="inputElement select2 {$ASSIGNED_USER_ID}" data-name="template_view_permission" name="template_view_permission[]" multiple>
									<optgroup label="{vtranslate('LBL_USERS')}">
										{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
												<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}'{foreach item=USER from=$FIELD_VALUE}{if $USER eq $OWNER_ID } selected {/if}{/foreach}
													data-userId="{$CURRENT_USER_ID}">
												{$OWNER_NAME}
												</option>
										{/foreach}
									</optgroup>
									<optgroup label="{vtranslate('LBL_GROUPS')}">
										{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEGROUP_LIST}
											<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' {foreach item=USER from=$FIELD_VALUE}{if $USER eq $OWNER_ID } selected {/if}{/foreach}>
											{$OWNER_NAME}
											</option>
										{/foreach}
									</optgroup>
								</select>
	                        </td>
			            </tr>
		            {/if}
		            {* 07-Aug-2018 change for template_view_permission END *}
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
                                            <option value="{$MODULENAME}" {if $RECORD->get('module') eq $MODULENAME}selected{/if}>{vtranslate($MODULENAME, $MODULENAME)}</option>
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
            <div class="row padding-bottom1per" style="margin:10px !important;">
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