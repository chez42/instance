{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Time Tracker ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <form action="index.php" id="formSettings">
        <input type="hidden" name="module" value="{$QUALIFIED_MODULE}"/>
        <input type="hidden" name="action" value="SaveAjax"/>
        <div class="summaryWidgetContainer">
            <ul class="nav nav-tabs massEditTabs">
                <li class="active">
                    <a href="#module_LBL_MODULE_SETTINGS" data-toggle="tab">
                        <strong>{vtranslate('LBL_MODULE_SETTINGS', $QUALIFIED_MODULE)}</strong>
                    </a>
                </li>
                <li>
                    <a href="#module_LBL_EVENT_SETTINGS" data-toggle="tab">
                        <strong>{vtranslate('LBL_EVENT_SETTINGS', $QUALIFIED_MODULE)}</strong>
                    </a>
                </li>
                <li>
                    <a href="#module_LBL_TIME_TRACKING_SETTINGS" data-toggle="tab">
                        <strong>{vtranslate('LBL_TIME_TRACKING_SETTINGS', $QUALIFIED_MODULE)}</strong>
                    </a>
                </li>
                <li>
                    <a href="#module_LBL_COMMENTS" data-toggle="tab">
                        <strong>{vtranslate('LBL_COMMENTS', $QUALIFIED_MODULE)}</strong>
                    </a>
                </li><li>
                    <a href="#module_LBL_INVOICE_SETTING" data-toggle="tab">
                        <strong>{vtranslate('LBL_INVOICE_SETTING', $QUALIFIED_MODULE)}</strong>
                    </a>
                </li>
            </ul>
            <div class="tab-content massEditContent">
                {assign var=SELECTED_MODULES value=$SETTINGS['selected_modules']}
                <div class="tab-pane active" id="module_LBL_MODULE_SETTINGS">
                    <div class="widgetContainer" style="padding: 20px 5px 5px 20px;">
                        {foreach from=$MODULES_LIST item=MODULE}
                            <div class="row-fluid">
                                <input type="checkbox" value="{$MODULE}" {if in_array($MODULE,$SELECTED_MODULES)}checked{/if}  class="selectedModules"/>&nbsp;&nbsp; {vtranslate($MODULE, $MODULE)}
                            </div>
                        {/foreach}
                    </div>
                </div>

                <div class="tab-pane" id="module_LBL_EVENT_SETTINGS">
                    {assign var=FIELD_SETTINGS value=$SETTINGS['field_settings']}
                    <div class="widgetContainer" style="padding: 20px 5px 5px 20px;">
                        <table class="table table-bordered equalSplit" style="width: 50%;">
                            <thead>
                            <tr>
                                <th>{vtranslate('LBL_FIELD', $QUALIFIED_MODULE)}</th>
                                <th>{vtranslate('LBL_VISIBLE', $QUALIFIED_MODULE)}</th>
                                <th>{vtranslate('LBL_DEFAULT', $QUALIFIED_MODULE)}</th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach from=$FIELDS_LIST item=FIELDNAME}
                                {assign var="FIELD_MODEL" value=$EVENT_MODULE_MODEL->getField($FIELDNAME)}
                                <tr>
                                    <td>{vtranslate($FIELD_MODEL->get('label'), $EVENT_MODULE_MODEL->getName())}</td>
                                    <td><input type="checkbox" value="1" {if $FIELD_SETTINGS[$FIELDNAME]['visible']}checked{/if} name="field_settings[{$FIELDNAME}][visible]"></td>
                                    <td>
                                        {if $FIELDNAME neq 'description' && $FIELDNAME neq 'subject' && $FIELDNAME neq 'cf_type'}
                                            {if $FIELDNAME eq 'assigned_user_id'}
                                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                                <div class="row-fluid">
                                                    <span class="span10">
                                                        <select class="chzn-select" name="field_settings[{$FIELDNAME}][default]">
                                                            <option value="Logged in User" {if trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq 'Logged in User'} selected {/if}>Logged in User</option>
                                                            <option value="Record Assigned To" {if trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq 'Record Assigned To'} selected {/if}>Record Assigned To</option>
                                                        </select>
                                                    </span>
                                                </div>
                                            {else}
                                                {assign var=PICKLIST_VALUES value=$FIELD_MODEL->getPicklistValues()}
                                                <div class="row-fluid">
                                                    <span class="span10">
                                                        <select class="chzn-select" name="field_settings[{$FIELDNAME}][default]">
                                                            <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                                            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                                                <option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
                                                            {/foreach}
                                                        </select>
                                                    </span>
                                                </div>
                                            {/if}
                                        {elseif $FIELDNAME eq 'cf_type'}
                                            {assign var=PICKLIST_VALUES value=$SERVICES_LIST}
                                            <div class="row-fluid">
                                                    <span class="span10">
                                                        <select class="chzn-select" name="field_settings[{$FIELDNAME}][default]">
                                                            <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                                                            {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
                                                                <option value="{$PICKLIST_VALUE}" {if trim(decode_html($FIELD_SETTINGS[$FIELDNAME]['default'])) eq trim($PICKLIST_NAME)} selected {/if}>{$PICKLIST_VALUE}</option>
                                                            {/foreach}
                                                        </select>
                                                    </span>
                                            </div>
                                        {else}
                                            <div class="row-fluid">
                                                    <span class="span10">
                                                        <input type="text" class="input-large fieldInput" name="field_settings[{$FIELDNAME}][default]" value="{$FIELD_SETTINGS[$FIELDNAME]['default']}" />
                                                    </span>
                                            </div>
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="tab-pane" id="module_LBL_TIME_TRACKING_SETTINGS">
                    <div class="widgetContainer" style="padding: 20px 5px 5px 20px;">
                        <div class="row-fluid">
                            <span class="span2">
                                {vtranslate('LBL_START_DATETIME_EDITABLE', $QUALIFIED_MODULE)}
                            </span>
                            <input type="checkbox" value="1" {if $SETTINGS['start_datetime_editable'] eq 1}checked{/if} name="start_datetime_editable"/>
                        </div>
                        <div class="row-fluid">
                            <span class="span2">
                                {vtranslate('LBL_DUE_DATETIME_EDITABLE', $QUALIFIED_MODULE)}
                            </span>
                            <input type="checkbox" value="1" {if $SETTINGS['due_datetime_editable'] eq 1}checked{/if} name="due_datetime_editable"/>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="module_LBL_COMMENTS">
                    <div class="widgetContainer" style="padding: 20px 5px 5px 20px;">
                        <div class="row-fluid">
                            <span class="span2">
                                {vtranslate('LBL_ALLOW_CREATE_COMMENTS', $QUALIFIED_MODULE)} &nbsp;&nbsp;
                            </span>
                            <input type="checkbox" value="1" {if $SETTINGS['allow_create_comments'] eq 1}checked{/if} name="allow_create_comments"/>
                        </div>
                        <div class="row-fluid">
                            <span class="span2">
                                {vtranslate('LBL_AUTO_CREATE_COMMENTS', $QUALIFIED_MODULE)} &nbsp;&nbsp;
                            </span>
                            <input type="checkbox" value="1" {if $SETTINGS['auto_create_comments'] eq 1}checked{/if} name="auto_create_comments"/>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="module_LBL_INVOICE_SETTING">
                    <div class="widgetContainer" style="padding: 20px 5px 5px 20px;">
                        <div class="row-fluid">
                            <span class="span2">
                                {vtranslate('LBL_INVOICE_BILLING_ACTIVE_INACTIVE', $QUALIFIED_MODULE)} &nbsp;&nbsp;
                            </span>
                            <select name="allow_bill_event_invoice">
                                <option value="0">Inactive</option>
                                <option value="1" {if $SETTINGS['allow_bill_event_invoice'] eq 1}selected{/if}>Active</option>
                            </select>

                        </div>
                        {*<div class="row-fluid">*}
                        {*<span class="span2" style="margin-top: 10px;">*}
                        {*{vtranslate('LBL_SERVICE_PRICE_SETTING', $QUALIFIED_MODULE)} &nbsp;&nbsp;*}
                        {*</span>*}
                        {*<input type="text" value="{$SETTINGS['price_setting']}"  name="price_setting"/>*}
                        {*</div>*}
                    </div>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <span>
                    <button class="btn btn-success" type="button" id="btnSaveSettings">{vtranslate('LBL_SAVE')}</button>
                </span>
            </div>
        </div>
    </form>
</div>