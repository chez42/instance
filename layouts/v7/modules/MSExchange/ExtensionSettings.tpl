{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{assign var=RETURN_URL value={$MODULE_MODEL->getExtensionSettingsUrl($SOURCEMODULE)}}
{if $PARENT neq 'Settings'}
    {assign var=RETURN_URL value=$MODULE_MODEL->getBaseExtensionUrl($SOURCEMODULE)}
{/if}
<input type="hidden" name="settingsPage" value="{$RETURN_URL}">
{assign var="dateFormat" value=$CURRENT_USER_MODEL->get('date_format')}

<div class="col-sm-12 col-xs-12 extensionContents">
	<form name="settingsForm" action="index.php" method="POST" class="m-form m-form--label-align-left" id="settingsForm">
        <input type="hidden" name="module" value="{$MODULE}" />
        <input type="hidden" name="action" value="SaveSyncSettings" />
        <input type="hidden" name="sourceModule" id="source_module" value="{$SOURCEMODULE}" />
        <input type="hidden" name="parent" value="{$PARENT}">
     	<input type="hidden" value="{$MODULE}" name="ext-module" id="ext-module" /> 
        
        <div class="row">
			<div class="col-md-6">
				<h3 class="module-title pull-left" style="margin-top:0px;">{vtranslate('LBL_MSEXCHANGE', $MODULE)}</h3>
			</div>
			{if is_array($USER_IMPERSONATION) and !empty($USER_IMPERSONATION)}
				<div class="col-md-6">
					<a href="#" class="revokeMSAccount btn btn-default pull-right" data-refresh-page="true">
                   		{vtranslate('LBL_REVOKE_ACCESS', $MODULE)}
                   	</a>
				</div>
			{/if}
		</div>
		<div class="marginTop15px">
			<div class="m-section">
				<div class="m-section__content">
					
					{if !empty($GLOBAL_SETTINGS)}
						{if isset($GLOBAL_SETTINGS['impersonate_user_account']) and !empty($GLOBAL_SETTINGS['impersonate_user_account'])}
							{assign var=IMPERSONATIONTYPE value=$GLOBAL_SETTINGS['impersonation_type']}
							{assign var=FieldName value="impersonation_identifier"}
							<div class="form-group m-form__group row">
			                    {if $IMPERSONATIONTYPE eq 'smtp_address'}
								    <label class="col-sm-3 col-md-2 col-form-label">{vtranslate('SMTP email address', $MODULE)} <span class="redColor">*</span></label>
			                    {else if $IMPERSONATIONTYPE eq 'upn'}
							    	<label class="col-sm-3 col-md-2 col-form-label">{vtranslate('User Principle Name', $MODULE)} <span class="redColor">*</span></label>
			                    {else}
									<label class="col-sm-3 col-md-2 col-form-label">{vtranslate('SID', $MODULE)} <span class="redColor">*</span></label>
			                    {/if}
							 	<div class="col-sm-4 col-md-4">
			                        <input type="text" name="{$FieldName}" class="form-control" data-rule-required="true" value="{$USER_IMPERSONATION['impersonation_identifier']}" />
			                  	</div>
							</div>
						{else}
							{assign var=UFieldName value=$SOURCEMODULE|cat:"[username]"}
							{assign var=PFieldName value=$SOURCEMODULE|cat:"[password]"}
							<div class="form-group m-form__group row">
		                        <label class="col-sm-3 col-md-2 col-form-label">{vtranslate('Username', $MODULE)}</label>
		                        <div class="col-sm-4 col-md-4">
		                      		<input type="text" name="{$UFieldName}" class="form-control" value="{$USER_IMPERSONATION['username']}" data-rule-required="true"/>
			                  	</div>
		                   	</div>
							<div class="form-group m-form__group row">
		                        <label class="col-sm-3 col-md-2 col-form-label">{vtranslate('Password', $MODULE)}</label>
		                        <div class="col-sm-4 col-md-4">
		                        	<input type="password" name="{$PFieldName}" class="form-control" value="{$USER_IMPERSONATION['password']}" data-rule-required="true"/>
			                  	</div>
		                   	</div>
						{/if}
					{/if}
				
					{*if $SOURCEMODULE eq 'Contacts'*}
						<input name="Contacts[enabled]" type="hidden" value="1"/>
				        <div class="form-group m-form__group row">
	                        <label class="col-sm-3 col-md-2 col-form-label">Contact {vtranslate('LBL_SYNC_DIRECTION', $MODULE)}</label>
	                        <div class="col-sm-4 col-md-4">
	                        	<select name="Contacts[sync_direction]" class="form-control select2">
                                    <option value="11" {if $CONTACTS_SYNC_DIRECTION eq 11} selected {/if}> {vtranslate('LBL_SYNC_BOTH_WAYS', $MODULE)} </option>
                                    <option value="10" {if $CONTACTS_SYNC_DIRECTION eq 10} selected {/if}> {vtranslate('LBL_SYNC_FROM_MSEXCHANGE_TO_VTIGER', $MODULE)} </option>
                                    <option value="01" {if $CONTACTS_SYNC_DIRECTION eq 01} selected {/if}> {vtranslate('LBL_SYNC_FROM_VTIGER_TO_MSEXCHANGE', $MODULE)} </option>
                                </select>
	                        </div>
	                    </div>
					{*/if*}
					{*if $SOURCEMODULE eq 'Calendar'*}
						<input name="Calendar[enabled]" type="hidden" value="1"/ >
						<div class="form-group m-form__group row">
	                        <label class="col-sm-3 col-md-2 col-form-label">Calendar {vtranslate('LBL_SYNC_DIRECTION', $MODULE)}</label>
	                        <div class="col-sm-4 col-md-4">
	                        	<select name="Calendar[sync_direction]" class="form-control select2">
                                    <option value="11" {if $CALENDAR_SYNC_DIRECTION eq 11} selected {/if}> {vtranslate('LBL_SYNC_BOTH_WAYS', $MODULE)} </option>
                                    <option value="10" {if $CALENDAR_SYNC_DIRECTION eq 10} selected {/if}> {vtranslate('LBL_SYNC_FROM_MSEXCHANGE_TO_VTIGER', $MODULE)} </option>
                                    <option value="01" {if $CALENDAR_SYNC_DIRECTION eq 01} selected {/if}> {vtranslate('LBL_SYNC_FROM_VTIGER_TO_MSEXCHANGE', $MODULE)} </option>
                                </select>
	                        </div>
	                    </div>
	                    <div class="form-group m-form__group row">
	                    	<label class="col-sm-3 col-md-2 col-form-label">Calendar {vtranslate('LBL_SYNC_START', $MODULE)}</label>
	                        <div class="col-sm-4 col-md-4">
	                        	<div class="input-group">
	                        		<input type="text" name="Calendar[sync_start_from]" class="dateField form-control m-input " data-rule-required="true"  data-rule-date="true" data-date-format="{$dateFormat}" data-fieldtype="date" value="{$SYNC_START_FROM}"/>
	                        		<div class="input-group-append input-group-addon">
										<span class="input-group-text ">
											<i class="fa fa-calendar "></i>
										</span>
									</div>
								</div>
							</div>
	                    </div>
						<div class="form-group m-form__group row">
							<label class="col-sm-3 col-md-2 col-form-label">{vtranslate('LBL_AUTOMATIC_CALENDAR_SYNC', $MODULE)}</label>
							<div class="col-sm-4 col-md-4">
								<label class="m-checkbox m-checkbox--square">
									<input name="Calendar[enable_cron]" type="checkbox" {if $AUTOMATIC_SYNC} checked {/if}>
									<span></span>
								</label>
							</div>
						</div>
					{*/if*}
					
					{*if $SOURCEMODULE eq 'Task'*}
						<input name="Task[enabled]" type="hidden" value="1"/ >
						<div class="form-group m-form__group row">
	                        <label class="col-sm-3 col-md-2 col-form-label">Task {vtranslate('LBL_SYNC_DIRECTION', $MODULE)}</label>
	                        <div class="col-sm-4 col-md-4">
	                        	<select name="Task[sync_direction]" class="form-control select2">
                                    <option value="11" {if $TASK_SYNC_DIRECTION eq 11} selected {/if}> {vtranslate('LBL_SYNC_BOTH_WAYS', $MODULE)} </option>
                                    <option value="10" {if $TASK_SYNC_DIRECTION eq 10} selected {/if}> {vtranslate('LBL_SYNC_FROM_MSEXCHANGE_TO_VTIGER', $MODULE)} </option>
                                    <option value="01" {if $TASK_SYNC_DIRECTION eq 01} selected {/if}> {vtranslate('LBL_SYNC_FROM_VTIGER_TO_MSEXCHANGE', $MODULE)} </option>
                                </select>
	                        </div>
	                    </div>
	                    <div class="form-group m-form__group row">
	                    	<label class="col-sm-3 col-md-2 col-form-label">Task {vtranslate('LBL_SYNC_START', $MODULE)}</label>
	                        <div class="col-sm-4 col-md-4">
	                        	<div class="input-group">
	                        		<input type="text" name="Task[sync_start_from]" class="dateField form-control m-input " data-rule-required="true"  data-rule-date="true" data-date-format="{$dateFormat}" data-fieldtype="date" value="{$SYNC_TASK_START_FROM}"/>
	                        		<div class="input-group-append input-group-addon">
										<span class="input-group-text ">
											<i class="fa fa-calendar "></i>
										</span>
									</div>
								</div>
							</div>
	                    </div>
						<div class="form-group m-form__group row">
							<label class="col-sm-3 col-md-2 col-form-label">{vtranslate('Automatic Task Sync', $MODULE)}</label>
							<div class="col-sm-4 col-md-4">
								<label class="m-checkbox m-checkbox--square">
									<input name="Task[enable_cron]" type="checkbox" {if $TASK_AUTOMATIC_SYNC} checked {/if}>
									<span></span>
								</label>
							</div>
						</div>
					{*/if*}
					{if $SOURCEMODULE neq 'Task'}
						<div class="form-group m-form__group row">
							<div class="col-sm-6 col-xs-4 col-md-offset-2 col-sm-offset-3">
								<a href="#" id="syncSetting" class="btn btn-default" title="{vtranslate('LBL_CONFIGURE', $MODULE)}" data-sync-module="{$SOURCEMODULE}">
	                    			<span>
	                    				<i class="fa fa-cog"></i>
	                    				<span>{vtranslate("LBL_FIELD_MAPPING", $MODULE)}</span>
	                    			</span>
	                    		</a>
	                    	</div>
						</div>
					{/if}
		        </div>
			</div>
		</div>
		<div class='modal-overlay-footer clearfix'>
			<div class=" row clearfix">
				<div class=' textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
				    <button id="saveSettings" type="submit" class="btn btn-success saveButton">{vtranslate('LBL_SAVE_SETTINGS', $MODULENAME)}</button>
	              	{if $RETURNTOLOGS eq true}<a href="#" data-url="{$MODULE_MODEL->getBaseExtensionUrl($SOURCEMODULE)}" class="m-link m--font-bold cancelLink navigationLink">{vtranslate('LBL_CANCEL', $MODULENAME)}</a>{/if}
	            </div>
			</div>
		</div>
	</form>
</div>