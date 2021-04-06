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
    <div class="row" style="margin-bottom: 70px;">
        <div class="col-lg-9">
        	<div class="row form-group">
				<div class="col-lg-2">{vtranslate('Notification Type',$QUALIFIED_MODULE)}</div>
                <div class="col-lg-6">
                	<select name="notification_type" class="select2 inputElement" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $QUALIFIED_MODULE)}">
						<option value=''>Select an option</option>
                        {foreach item=TYPE key=VALUE from=$NOTIFICATION_TYPE}
                        	<option value="{$VALUE}" {if $VALUE eq $TASK_OBJECT->notification_type} selected {/if}>{$TYPE}</option>
                        {/foreach}
                    </select>	
                </div>
				
			</div>
			<div class="row form-group">
				<div class="col-lg-2">{vtranslate('Subject',$QUALIFIED_MODULE)}</div>
                <div class="col-lg-6">
                    <input name="subject" class="inputElement fields" value="{$TASK_OBJECT->subject}" />
                </div>
				<div class="col-lg-4">
                    <select class="select2 task-fields" style="min-width: 250px;" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $QUALIFIED_MODULE)}">
						<option></option>
                        {$ALL_FIELD_OPTIONS}
                    </select>	
                </div>
			</div>
            <div class="row form-group">
                <div class="col-lg-2">{vtranslate('Description',$QUALIFIED_MODULE)}</div>
                <div class="col-lg-6">
                    <textarea name="content" class="inputElement fields" style="height: inherit;">{$TASK_OBJECT->content}</textarea>
                </div>
				<div class="col-lg-4">
                    <select class="select2 task-fields" style="min-width: 250px;" data-placeholder="{vtranslate('LBL_SELECT_FIELDS', $QUALIFIED_MODULE)}">
						<option></option>
                        {$ALL_FIELD_OPTIONS}
                    </select>	
                </div>
            </div>
        </div>
    </div>
{/strip}	
