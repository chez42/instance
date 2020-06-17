{*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}
{strip}
    {*SLA Policy Details*}
    <style>
        .tooltip-inner {
            max-width: 350px;
            /* If max-width does not work, try using width instead */
            width: 350px;
        }
    </style>
    <div class="editViewPageDiv">
        {assign var=RECORD_DETAILS value=$RECORD_VALUES->details }
        <form id="EditView" action="index.php" method="post" name="EditSLAPolicies">
            <input type="hidden" name="module" id="module" value="{$MODULE}">
            <input type="hidden" name="action" value="SaveVTESLAPolicies" />
            <input type="hidden" name="parent" value="Settings" />
            <input type="hidden" name="record" id="record" value="{$RECORD_DETAILS['record']}">
            <input type="hidden" name="moduleSelected" id="moduleSelected" value="{$RECORD_DETAILS['slaModule']}">
            <input type="hidden" name="count_sla_actions"  value="{if $RECORD_DETAILS['count_sla_actions']}{$RECORD_DETAILS['count_sla_actions']}{else}0{/if}">
            <input type="hidden" name="conditions" id="advanced_filter" value='' />
            <div class="col-sm-12 col-xs-12">
                <h4 class="label-block">{vtranslate('SLA Policy Details',{$QUALIFIED_MODULE})}</h4>
                <hr/>
                <div class="col-sm-6 col-xs-6 form-horizontal">
                    <div class="form-group">
                        <label for="name" class="control-label col-sm-3">
                            <span>{vtranslate('Policy Name',{$QUALIFIED_MODULE})}</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input class="form-control" id="name" name="sla_name" value="{$RECORD_DETAILS['slaName']}" data-rule-required="true"></div>
                    </div>
                    <div class="form-group">
                        <label for="module" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_MODULE',{$QUALIFIED_MODULE})}</span>
                            <span class="redColor">*</span>

                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="sla_module" name="sla_module" data-rule-required="true">
                                <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                {foreach item=MODULE_VALUES from=$ALL_MODULES}
                                    <option value="{$MODULE_VALUES->name}" {if {$RECORD_DETAILS['slaModule']} eq $MODULE_VALUES->name} selected="selected"{/if}>{vtranslate($MODULE_VALUES->label,$RECORD_DETAILS['slaModule'])}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="picklist_field" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_PICKLIST_FIELD',{$QUALIFIED_MODULE})}</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="picklist_field"
                                    name="sla_picklist_field" data-rule-required="true">
                                <option VALUE="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                {if $RECORD_DETAILS['record']}
                                    {foreach item=FIELD_LABEL key=FIELD_NAME from=$FIELDS_PICKLIST_MODULE}
                                        <option value="{$FIELD_NAME}" {if $RECORD_DETAILS['slaPickListField'] == $FIELD_NAME}selected="selected"{/if}>{$FIELD_LABEL}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="picklist_field" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_EXCLUSIONS_FIELD',{$QUALIFIED_MODULE})}</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="exclude_field"
                                    name="exclude_field">
                                <option VALUE="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                {if $RECORD_DETAILS['record']}
                                    {foreach item=FIELD_LABEL key=FIELD_NAME from=$FIELDS_PICKLIST_MODULE}
                                        <option value="{$FIELD_NAME}" {if $RECORD_DETAILS['slaExcludeField'] == $FIELD_NAME}selected="selected"{/if}>{$FIELD_LABEL}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sla_exclude_field" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_EXCLUSIONS_VALUES',{$QUALIFIED_MODULE})}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="exclude_fieldvalue"
                                    name="sla_exclude_fieldvalue[]" multiple="multiple">
                                {if $RECORD_DETAILS['record']}
                                    {foreach item=VALUE_FIELD from=$VALUE_PICKLIST_FIELD_EXCLUDE}
                                        <option value="{$VALUE_FIELD}" {if in_array(Vtiger_Util_Helper::toSafeHTML($VALUE_FIELD),$RECORD_DETAILS['slaExcludeFieldValue'])} selected {/if}>{vtranslate($VALUE_FIELD,$RECORD_DETAILS['slaModule'])}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sla_fulfillment_values" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_FULFILLMENT_VALUES',{$QUALIFIED_MODULE})}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="sla_fulfillment_values"
                                    name="sla_fulfillment_values[]" multiple="multiple">
                                {if $RECORD_DETAILS['record']}
                                    {foreach item=VALUE_FIELD from=$VALUE_PICKLIST_FIELD_EXCLUDE}
                                        <option value="{$VALUE_FIELD}" {if in_array(Vtiger_Util_Helper::toSafeHTML($VALUE_FIELD),$RECORD_DETAILS['fulfillment_values'])} selected {/if}>{vtranslate($VALUE_FIELD,$RECORD_DETAILS['slaModule'])}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_STATUS_SLALOG',{$QUALIFIED_MODULE})}<i class="cursorPointer fa fa-info-circle" data-toggle="tooltip" title="When set to yes, it will show SLA Logs for the selected module. Please see documentation for more details."></i></span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="display_log" name="display_log">
                                <option value="Yes" selected="selected">{vtranslate('LBL_YES',{$QUALIFIED_MODULE})}</option>
                                <option value="No" {if $RECORD_DETAILS['display_slalog'] eq 'No'}selected="selected"{/if}>{vtranslate('LBL_NO',{$QUALIFIED_MODULE})}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="status" class="control-label col-sm-3">
                            <span>{vtranslate('LBL_STATUS',{$QUALIFIED_MODULE})}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="status" name="sla_status">
                                <option value="Active" selected="selected">{vtranslate('LBL_ACTIVE',{$QUALIFIED_MODULE})}</option>
                                <option value="Inactive" {if $RECORD_DETAILS['slaStatus'] eq 'Inactive'}selected="selected"{/if}>{vtranslate('LBL_INACTIVE',{$QUALIFIED_MODULE})}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="business" class="control-label col-sm-3">
                            <span>{vtranslate('Business Hours',{$QUALIFIED_MODULE})}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="business_hour" name="business_hour">
                                <option value="Yes" selected="selected">{vtranslate('LBL_YES',{$QUALIFIED_MODULE})}</option>
                                <option value="No" {if $RECORD_DETAILS['slaBusinessHour'] eq 'No'}selected="selected"{/if}>{vtranslate('LBL_NO',{$QUALIFIED_MODULE})}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-6 sla-policies-info">
                    <div class="label-info">
                        <h5>
                            <span class="glyphicon glyphicon-info-sign"></span> Info
                        </h5>
                    </div>
                    <span>Important: Please review the user guide to understand how the "Resolved Within" and Trigger (before/after) works.</span>
                    <br/><br/>
                    <span>Documentation: <a href="https://www.vtexperts.com/helpdesk/sla" target="_blank">https://www.vtexperts.com/helpdesk/sla</a></span>
                    <br/><br/>
                    <span>For help, please send us an email at help@vtexperts.com</span>
                </div>
            </div>
            {*SLA Target*}
            <div class="col-sm-12 col-xs-12 conditionsContainer">
                <h4 class="label-block">{vtranslate('SLA Target',{$QUALIFIED_MODULE})}</h4>
                <hr/>
                <div class="col-sm-12 col-xs-12 form-horizontal" style="margin-left: 25px">
                    <div class="form-group col-sm-6">
                        <label for="sla_picklist_field_value"
                               class="control-label col-sm-4" name="picklist_field_label" style="padding-right:0px">
                            {vtranslate('LBL_SLA_TARGET',{$QUALIFIED_MODULE})}
                            <span>{if $FIELD_LABEL_SELECTED}{$FIELD_LABEL_SELECTED}{else}{/if} </span>
                            <span class="redColor">*</span>
                            <i class="cursorPointer fa fa-info-circle pull-right" data-toggle="tooltip" title="Select the value, which will be used to trigger the SLA." ></i>
                        </label>
                        <div class="col-sm-7">
                            <select class="inputElement select2 select2-offscreen" name="sla_picklist_value" data-rule-required="true">
                                <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                {if $RECORD_DETAILS['record']}
                                    {foreach item=VALUE_FIELD from=$VALUE_PICKLIST_FIELD}
                                        <option value="{$VALUE_FIELD}" {if $RECORD_DETAILS['slaPickListValue'] == $VALUE_FIELD}selected="selected"{/if}>{vtranslate($VALUE_FIELD,$RECORD_DETAILS['slaModule'])}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-6">
                        <div class="col-sm-4" style="padding-right: 0px;">
                            <label for="sla_picklist_field_value"
                                   class="control-label">
                                <span>{vtranslate('LBL_RESOLVED_WITHIN',{$QUALIFIED_MODULE})}</span>
                                <span class="redColor">*</span>
                                <i class="cursorPointer fa fa-info-circle" style="margin-left: 3.3em !important;" data-toggle="tooltip" title='Resolved within should be set to the number of hours/minutes needed/allocated to resolve the issue. For exmaple, if your SLA is 5 hours for "Critical" ticket, then you should set Resolved Within to 5 hours.'></i>
                            </label>
                        </div>
                        <div class="col-sm-8">
                            <input class="form-control" type="number" id="resolved_time" name="sla_resolved_time" value="{$RECORD_DETAILS['slaResolvedTime']}" min="0" data-rule-required="true">
                            <select class="select2 select2-offscreen" style="width: 30%; vertical-align: top"
                                    id="resolved_typetime" name="sla_resolved_typetime" data-rule-required="true">
                                <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                <option {if $RECORD_DETAILS['slaResolvedTypeTime'] == 'Mins'}selected="selected"{/if} value="Mins">{vtranslate('LBL_MINS',{$QUALIFIED_MODULE})}</option>
                                <option {if $RECORD_DETAILS['slaResolvedTypeTime'] == 'Hours'}selected="selected"{/if} value="Hours">{vtranslate('LBL_HOURS',{$QUALIFIED_MODULE})}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-1 col-xs-1 " style="padding-left: 0px">
                    <h5>
                        <i class="cursorPointer fa fa-info-circle" style="margin-left: 4em !important" data-toggle="tooltip" title='In addition to Picklist field, you can also add other conditions, such as Organization - equals - Apple, Inc. (If you had differnet SLA agreements for Apple versus another company).' ></i>
                    </h5>
                </div>
                <div class="col-sm-10 col-xs-10" id="advanceFilterContainer">
                    <div class="" id="table-conditions" style="padding-left: 3%">
                        {include file='AdvanceFilter.tpl'|@vtemplate_path:$QUALIFIED_MODULE RECORD_STRUCTURE=$RECORD_STRUCTURE SOURCE_MODULE=$SELECTED_MODULE_NAME}
                    </div>
                </div>
            </div>
            {*SLA Actions*}
            {assign var=RECORDS_ACTIONS value=$RECORD_VALUES->actions }
            <div class="col-sm-12 col-xs-12">
                <h4 class="label-block">{vtranslate('SLA Actions (Alert/Escalations)',{$QUALIFIED_MODULE})} <i class="cursorPointer fa fa-info-circle" data-toggle="tooltip" title='This is where you will specify what happens before and after the SLA is breached.'></i></h4>
                <hr/>
                <table class="table" id="sla_actions">
                    <thead>
                    <tr>
                        <th></th>
                        <th>{vtranslate('LBL_NAME_SLA_ACTION',{$QUALIFIED_MODULE})}<i class="cursorPointer fa fa-info-circle"  data-toggle="tooltip" title='Alert Name - can be anything.' ></i></th>
                        <th>{vtranslate('LBL_TYPE',{$QUALIFIED_MODULE})}<i class="cursorPointer fa fa-info-circle"  data-html="true" data-toggle="tooltip" title="Select from 3 options: <br><br> Email - email will be sent to the selected user.<br><br>Reassign - record will be reassigned to selected user. </br></br>Workflow - selected workflow will get executed on the active record. Please see documentation for more details." ></i></th>
                        <th>{vtranslate('LBL_TRIGGER_SLA_ACTION',{$QUALIFIED_MODULE})}<i class="cursorPointer fa fa-info-circle"  data-html="true" data-toggle="tooltip" title='Select from 2 options:</br></br>Before - meaning that you can trigger action BEFORE the SLA has been breached. </br></br>After - meaning that you can trigger action AFTER SLA has been breached.' ></i></th>
                        <th>{vtranslate('LBL_USER',{$QUALIFIED_MODULE})} <i class="cursorPointer fa fa-info-circle"  data-toggle="tooltip" title='Depending on TYPE, this selection will be used to send an email (to selected user) OR assign active record (to selected user).' ></i></th>
                        <th>{vtranslate('LBL_EMAIL_WORKFLOW',{$QUALIFIED_MODULE})}<i class="cursorPointer fa fa-info-circle"  data-toggle="tooltip" title='Select existing email template OR workflow. Please see documentation for more details.' ></i></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="recordClone hide">
                        <td><img src="{vimage_path('drag.png')}" class="cursorPointerMove" border="0" title="Drag">
                            <input type="hidden" name="rowno[]" value="">
                        </td>
                        <td><input class="inputElement sla_action_name" name="sla_action_name"></td>
                        <td>
                            <select class="inputElement sla_action_type" type="picklist" name="sla_action_type">
                                <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                <option value="Email">{vtranslate('LBL_EMAIL',{$QUALIFIED_MODULE})}</option>
                                <option value="Reassign">{vtranslate('LBL_REASSIGN',{$QUALIFIED_MODULE})}</option>
                                <option value="Workflow">{vtranslate('LBL_WORKFLOW',{$QUALIFIED_MODULE})}</option>
                            </select>
                        </td>
                        <td>
                            <div style="width: 40%;display: inline">
                                <select class="sla_action_trigger" style="vertical-align: top ; width: 94px" name="sla_action_trigger">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                    <option value="Alter">{vtranslate('LBL_AFTER',{$QUALIFIED_MODULE})}</option>
                                    <option value="Before">{vtranslate('LBL_BEFORE',{$QUALIFIED_MODULE})}</option>
                                </select>
                            </div>
                            <div  style="width: 50%;display: inline;margin-left: 7% ">
                                <input class="inputElement sla_action_time" type="number" min="0" style="width: 25%" name="sla_action_time">
                                <select class="sla_action_typetime" style="width: 35%;vertical-align: top" name="sla_action_typetime">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                    <option value="Mins">{vtranslate('LBL_MINS',{$QUALIFIED_MODULE})}</option>
                                    <option value="Hours">{vtranslate('LBL_HOURS',{$QUALIFIED_MODULE})}</option>
                                </select>
                            </div>
                        </td>
                        <td>
                            <select class="inputElement sla_action_users" name="sla_action_users">
                                <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                <optgroup label="Users">
                                    {foreach item=USER from=$ALL_USERS}
                                        <option value="{$USER->id}">{$USER->getName()}</option>
                                    {/foreach}
                                </optgroup>
                                <optgroup label="Groups">
                                    {foreach item=GROUP from=$ALL_GROUPS}
                                        <option value="{$GROUP->get('groupid')}">{$GROUP->get('groupname')}</option>
                                    {/foreach}
                                </optgroup>
                            </select>
                        </td>
                        <td>
                            <select class="inputElement sla_action_email_template" name="sla_action_email_template">
                                <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                {foreach item=TEMPLATE_EMAIL  from=$TEMPLATE_EMAIL_MODULE}
                                    <option value="{$TEMPLATE_EMAIL['templateid']}">{$TEMPLATE_EMAIL['templatename']}</option>
                                {/foreach}
                            </select>
                            <span class="fa vicon-emailtemplates selectEmailTemplate cursorPointer" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupEmailTemplate"></span>
                            {if $RECORD_DETAILS['record']}
                                <select class="inputElement sla_action_workflow hide" name="sla_action_workflow{$INDEX}">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                    {foreach item=WORKFLOW_NAME key=WORKFLOW_ID from=$WORKFLOWS_MODULE}
                                        <option value="{$WORKFLOW_ID}" {if $RECORD_ACTIONS_VALUE['type_id'] == $WORKFLOW_ID && $RECORD_ACTIONS_VALUE['type'] == 'Workflow' } selected="selected"{/if}>{$WORKFLOW_NAME}</option>
                                    {/foreach}
                                </select>
                                <span class="fa fa-cog selectWorkflow cursorPointer hide" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupWorkflow&sourceModule={$RECORD_DETAILS['slaModule']}"></span>
                            {else}
                                <select class="inputElement sla_action_workflow hide" name="sla_action_workflow">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                </select>
                                <span class="fa fa-cog selectWorkflow cursorPointer hide" style="vertical-align: middle; margin-left:10px"></span>
                            {/if}
                        </td>
                        <td><span class="glyphicon glyphicon-trash cursorPointer deleterow"></span></td>
                    </tr>
                    {foreach item=RECORD_ACTIONS_VALUE from=$RECORDS_ACTIONS}
                        {assign var=$INDEX++ value=1 }
                        <tr class="record" sla-record-id="{$RECORD_ACTIONS_VALUE['action_id']}" data-id="{$RECORD_ACTIONS_VALUE['action_id']}">
                            <td>
                                <img src="{vimage_path('drag.png')}" class="cursorPointerMove" border="0" title="Drag">
                                <input type="hidden" name="sla_action_record_id{$INDEX}" value="{$RECORD_ACTIONS_VALUE['action_id']}">
                                <input type="hidden" name="rowno[]" value="{$INDEX}">
                            </td>
                            <td><input class="inputElement sla_action_name" name="sla_action_name{$INDEX}" value="{$RECORD_ACTIONS_VALUE['action_name']}"></td>
                            <td>
                                <select class="inputElement sla_action_type select2" name="sla_action_type{$INDEX}">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                    <option value="Email" {if $RECORD_ACTIONS_VALUE['type'] == 'Email'}selected="selected"{/if}>{vtranslate('LBL_EMAIL',{$QUALIFIED_MODULE})}</option>
                                    <option value="Reassign" {if $RECORD_ACTIONS_VALUE['type'] == 'Reassign'}selected="selected"{/if}>{vtranslate('LBL_REASSIGN',{$QUALIFIED_MODULE})}</option>
                                    <option value="Workflow" {if $RECORD_ACTIONS_VALUE['type'] == 'Workflow'}selected="selected"{/if}>{vtranslate('LBL_WORKFLOW',{$QUALIFIED_MODULE})}</option>
                                </select>
                            </td>
                            <td>
                                <div style="width: 40%;display: inline">
                                    <select class="select2 sla_action_trigger" style="vertical-align: top; width: 94px" name="sla_action_trigger{$INDEX}">
                                        <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                        <option value="Alter" {if $RECORD_ACTIONS_VALUE['trigger'] == 'Alter'}selected="selected"{/if}>{vtranslate('LBL_AFTER',{$QUALIFIED_MODULE})}</option>
                                        <option value="Before" {if $RECORD_ACTIONS_VALUE['trigger'] == 'Before'}selected="selected"{/if} >{vtranslate('LBL_BEFORE',{$QUALIFIED_MODULE})}</option>
                                    </select>
                                </div>
                                <div  style="width: 50%;display: inline;margin-left: 7% ">
                                    <input class="inputElement sla_action_time" style="width: 25%" type="number" name="sla_action_time{$INDEX}" value="{$RECORD_ACTIONS_VALUE['trigger_time']}" min="0">
                                    <select class="select2 sla_action_typetime" style="width: 35%;vertical-align: top" name="sla_action_typetime{$INDEX}">
                                        <option value="">{vtranslate('LBL_SELECT_OPTION',{$QUALIFIED_MODULE})}</option>
                                        <option value="Mins" {if $RECORD_ACTIONS_VALUE['trigger_typetime'] == 'Mins'}selected="selected"{/if}>{vtranslate('LBL_MINS',{$QUALIFIED_MODULE})}</option>
                                        <option value="Hours" {if $RECORD_ACTIONS_VALUE['trigger_typetime'] == 'Hours'}selected="selected"{/if}>{vtranslate('LBL_HOURS',{$QUALIFIED_MODULE})}</option>
                                    </select>
                                </div>
                            </td>
                            <td>
                                {if $RECORD_ACTIONS_VALUE['type'] == 'Workflow'}
                                    <select class="inputElement select2 sla_action_users hide" name="sla_action_users{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        <optgroup label="Users">
                                            {foreach item=USER from=$ALL_USERS}
                                                <option value="{$USER->id}" {if $RECORD_ACTIONS_VALUE['user'] == $USER->id}selected="selected"{/if}>{$USER->getName()}</option>
                                            {/foreach}
                                        </optgroup>
                                        <optgroup label="Groups">
                                            {foreach item=GROUP from=$ALL_GROUPS}
                                                <option value="{$GROUP->get('groupid')}" {if $RECORD_ACTIONS_VALUE['user'] == $GROUP->get('groupid')}selected="selected"{/if}>{$GROUP->get('groupname')}</option>
                                            {/foreach}
                                        </optgroup>
                                    </select>
                                {else}
                                    <select class="inputElement sla_action_users select2" name="sla_action_users{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        <optgroup label="Users">
                                            {foreach item=USER from=$ALL_USERS}
                                                <option value="{$USER->id}" {if $RECORD_ACTIONS_VALUE['user'] == $USER->id}selected="selected"{/if}>{$USER->getName()}</option>
                                            {/foreach}
                                        </optgroup>
                                        <optgroup label="Groups">
                                            {foreach item=GROUP from=$ALL_GROUPS}
                                                <option value="{$GROUP->get('groupid')}" {if $RECORD_ACTIONS_VALUE['user'] == $GROUP->get('groupid')}selected="selected"{/if}>{$GROUP->get('groupname')}</option>
                                            {/foreach}
                                        </optgroup>
                                    </select>
                                {/if}
                            </td>
                            <td>
                                {if $RECORD_ACTIONS_VALUE['type'] == 'Email'}
                                    <select class="inputElement sla_action_email_template select2" name="sla_action_email_template{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        {foreach item=TEMPLATE_EMAIL  from=$TEMPLATE_EMAIL_MODULE}
                                            <option value="{$TEMPLATE_EMAIL['templateid']}" {if $RECORD_ACTIONS_VALUE['type_id'] == $TEMPLATE_EMAIL['templateid'] && $RECORD_ACTIONS_VALUE['type'] == 'Email'}selected="selected"{/if}>{$TEMPLATE_EMAIL['templatename']}</option>
                                        {/foreach}
                                    </select>
                                    <span class="fa vicon-emailtemplates selectEmailTemplate cursorPointer" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupEmailTemplate"></span>

                                    <select class="inputElement sla_action_workflow hide" name="sla_action_workflow{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        {foreach item=WORKFLOW_NAME key=WORKFLOW_ID from=$WORKFLOWS_MODULE}
                                            <option value="{$WORKFLOW_ID}" {if $RECORD_ACTIONS_VALUE['type_id'] == $WORKFLOW_ID && $RECORD_ACTIONS_VALUE['type'] == 'Workflow' } selected="selected"{/if}>{$WORKFLOW_NAME}</option>
                                        {/foreach}
                                    </select>
                                    <span class="fa fa-cog selectWorkflow cursorPointer hide" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupEmailTemplate"></span>
                                {elseif $RECORD_ACTIONS_VALUE['type'] == 'Workflow'}
                                    <select class="inputElement sla_action_email_template select2 hide" name="sla_action_email_template{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        {foreach item=TEMPLATE_EMAIL  from=$TEMPLATE_EMAIL_MODULE}
                                            <option value="{$TEMPLATE_EMAIL['templateid']}" {if $RECORD_ACTIONS_VALUE['type_id'] == $TEMPLATE_EMAIL['templateid'] && $RECORD_ACTIONS_VALUE['type'] == 'Email'}selected="selected"{/if}>{$TEMPLATE_EMAIL['templatename']}</option>
                                        {/foreach}
                                    </select>
                                    <span class="fa vicon-emailtemplates selectEmailTemplate cursorPointer hide" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupEmailTemplate"></span>

                                    <select class="inputElement sla_action_workflow select2" name="sla_action_workflow{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        {foreach item=WORKFLOW_NAME key=WORKFLOW_ID from=$WORKFLOWS_MODULE}
                                            <option value="{$WORKFLOW_ID}" {if $RECORD_ACTIONS_VALUE['type_id'] == $WORKFLOW_ID && $RECORD_ACTIONS_VALUE['type'] == 'Workflow' } selected="selected"{/if}>{$WORKFLOW_NAME}</option>
                                        {/foreach}
                                    </select>
                                    <span class="fa fa-cog selectWorkflow cursorPointer" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupWorkflow&sourceModule={$RECORD_DETAILS['slaModule']}"></span>
                                {else}
                                    <select class="inputElement sla_action_email_template select2 hide" name="sla_action_email_template{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        {foreach item=TEMPLATE_EMAIL  from=$TEMPLATE_EMAIL_MODULE}
                                            <option value="{$TEMPLATE_EMAIL['templateid']}" {if $RECORD_ACTIONS_VALUE['type_id'] == $TEMPLATE_EMAIL['templateid']}selected="selected"{/if}>{$TEMPLATE_EMAIL['templatename']}</option>
                                        {/foreach}
                                    </select>
                                    <span class="fa vicon-emailtemplates selectEmailTemplate cursorPointer hide" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupEmailTemplate"></span>

                                    <select class="inputElement sla_action_workflow select2 hide" name="sla_action_workflow{$INDEX}">
                                        <option value="">{vtranslate('LBL_ASSIGNED_USER',{$QUALIFIED_MODULE})}</option>
                                        {foreach item=WORKFLOW_NAME key=WORKFLOW_ID from=$WORKFLOWS_MODULE}
                                            <option value="{$WORKFLOW_ID}" {if $RECORD_ACTIONS_VALUE['type_id'] == $WORKFLOW_ID && $RECORD_ACTIONS_VALUE['type'] == 'Workflow' } selected="selected"{/if}>{$WORKFLOW_NAME}</option>
                                        {/foreach}
                                    </select>
                                    <span class="fa fa-cog selectWorkflow cursorPointer hide" style="vertical-align: middle; margin-left:10px" data-url="module=VTESLAPolicies&parent=Settings&view=PopupWorkflow&sourceModule={$RECORD_DETAILS['slaModule']}"></span>
                                {/if}
                            </td>
                            <td><span class="glyphicon glyphicon-trash cursorPointer deleterow"></span></td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
                <button type="button" class="btn btn-default" id="addrow" style="margin-bottom: 100px">
                    <span class="glyphicon glyphicon-plus"></span>
                    {vtranslate('LBL_ADD_ROW',{$QUALIFIED_MODULE})}
                </button>
            </div>
            {*footer*}

            <div class="modal-overlay-footer clearfix">
                <div class="row clearfix">
                    <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
                        <button type="submit" class="btn btn-success buttonSave">Save</button>&nbsp;&nbsp;<a class="cancelLink" href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL',{$QUALIFIED_MODULE})}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
{/strip}