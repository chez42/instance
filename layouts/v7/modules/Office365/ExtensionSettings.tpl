{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{assign var=RETURN_URL value={$MODULE_MODEL->getExtensionSettingsUrl($SOURCEMODULE)}}
{if $PARENT eq 'Settings'}
	{assign var=RETURN_URL value=$MODULE_MODEL->getExtensionSettingsUrl($SOURCEMODULE)|cat:"&parent=Settings"}
{/if}
<input type="hidden" name="settingsPage" value="{$RETURN_URL}">
<div class="col-sm-12 col-xs-12 extensionContents">
	<div class="row">
		<div class="col-sm-3 col-xs-3">
			<h3 class="module-title pull-left"> {vtranslate('LBL_SELECT_MODULES_TO_SYNC', $MODULE)} </h3>
		</div>
		<div class="col-sm-9 col-xs-9">
			{if !$IS_SYNC_READY}
				<div class="row">
					<div class="col-sm-4 col-xs-4 pull-right">
						<a id="authorizeButton" class="btn btn-block btn-social btn-lg btn-google-plus" style="font-size:1.1rem;padding-left:40px;" data-url='{$AUTH_URL}'>
							<div class="officeIcon" style="padding:6px;background-color:white;">
                    			<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 278050 333334" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd">
                    				<path fill="#ea3e23" d="M278050 305556l-29-16V28627L178807 0 448 66971l-448 87 22 200227 60865-23821V80555l117920-28193-17 239519L122 267285l178668 65976v73l99231-27462v-316z"></path>
                				</svg>
            				</div>
							{vtranslate('Sign in with Office365', $MODULE)}
						</a>
					</div>
				</div>
			{else}
				<div class="row">
				
					<div class="col-sm-3 col-xs-3 pull-right">
						<a id="authorizeButton" class="btn btn-block btn-social btn-lg btn-google-plus" style="font-size:1.05rem;padding-left:40px;" data-url='index.php?module={$MODULE}&view=List&operation=changeUser&sourcemodule={$SOURCEMODULE}'> 
							<div class="officeIcon pull-left" style="padding:6px;background-color:white;">
                    			<svg xmlns="http://www.w3.org/2000/svg" width="18px" height="18px" viewBox="0 0 278050 333334" shape-rendering="geometricPrecision" text-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" clip-rule="evenodd">
                    				<path fill="#ea3e23" d="M278050 305556l-29-16V28627L178807 0 448 66971l-448 87 22 200227 60865-23821V80555l117920-28193-17 239519L122 267285l178668 65976v73l99231-27462v-316z"></path>
                				</svg>
            				</div>
							{vtranslate('Revoke Access', $MODULE)} 
						</a>
					</div>
				</div>
			{/if}
		</div>
	</div>
	<br>
	<form name="settingsForm" action="index.php" method="POST" >
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="SaveSyncSettings" />
		<input type="hidden" name="sourceModule" value="{$SOURCEMODULE}" />
		<input type="hidden" name="parent" value="{$PARENT}">
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<table class="listview-table table-bordered" align="center">
					<thead>
					<th> {vtranslate('CRM', $MODULE)} {vtranslate('LBL_DATA', $MODULE)} </th>
					<th> {vtranslate('LBL_FIELD_MAPPING', $MODULE)} </th> 
					<th> {vtranslate('Sync Start Date', $MODULE)} </th> 
					<th> {vtranslate('Cron Sync', $MODULE)} </th>
					<th> {vtranslate('LBL_SYNC_DIRECTION', $MODULE)} </th>
					</thead>
					<tbody>
						{*<tr>
							<td>{vtranslate('Contacts', 'Contacts')}</td>
							<td><a id="syncSetting" class="extensionLink" data-sync-module="Contacts">{vtranslate('LBL_CONFIGURE', $MODULE)}</a></td>
							<td>
								<!--<div class="input-group inputElement" style="margin-bottom: 3px">
									<input type="text" class="dateField form-control" name="Contacts[sync_start_from]" data-fieldtype="date" data-date-format="yyyy-mm-dd"/>
									<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
								</div>-->
							</td>
							<td><input name="Contacts[enabled]" type="checkbox" {if $CONTACTS_ENABLED} checked {/if}></td>
							<td>
								<select name="Contacts[sync_direction]" class="inputElement select2 row" style="min-width: 250px;">
									<option value="11" {if $CONTACTS_SYNC_DIRECTION eq 11} selected {/if}> {vtranslate('LBL_SYNC_BOTH_WAYS', $MODULE)} </option>
									<option value="10" {if $CONTACTS_SYNC_DIRECTION eq 10} selected {/if}> {vtranslate('Sync office365 to vtiger', $MODULE)} </option>
									<option value="01" {if $CONTACTS_SYNC_DIRECTION eq 01} selected {/if}> {vtranslate('Sync vtiger to office365', $MODULE)} </option>
								</select>
							</td>
						</tr>*}
						<tr>
							<td>{vtranslate('Calendar', 'Calendar')}</td>
							<td><a id="syncSetting" class="extensionLink" data-sync-module="Calendar">{vtranslate('LBL_VIEW', $MODULE)}</a></td>
							<td>
								<div class="input-group inputElement" style="margin-bottom: 3px">
									<input type="text" class="{if !$SYNC_STATE} startDateField {/if} form-control" name="Calendar[sync_start_from]" data-fieldtype="date" data-date-format="yyyy-mm-dd" value="{$CALENDAR_SYNC_START}" {if $SYNC_STATE} readonly {/if}/>
									<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
								</div>	
							</td>
							<td><input name="Calendar[enabled]" type="checkbox" {if $CALENDAR_ENABLED} checked {/if}></td>
							<td>
								<select name="Calendar[sync_direction]" class="inputElement select2 row" style="min-width: 250px;">
									<option value="11" {if $CALENDAR_SYNC_DIRECTION eq 11} selected {/if}> {vtranslate('LBL_SYNC_BOTH_WAYS', $MODULE)} </option>
									<option value="10" {if $CALENDAR_SYNC_DIRECTION eq 10} selected {/if}> {vtranslate('Sync office365 to vtiger', $MODULE)} </option>
									<option value="01" {if $CALENDAR_SYNC_DIRECTION eq 01} selected {/if}> {vtranslate('Sync vtiger to office365', $MODULE)} </option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="scroller_wrapper" class="bottom-fixed-scroll">
				<div id="scroller" class="scroller-div"></div>
			</div>
			<div class="col-sm-2 col-xs-2">
			</div>
		</div>
		<br>
		
		<div style="margin-top: 8%;">
			<div class="text-center">
				<button id="saveSettings" type="submit" class="btn btn-success saveButton">{vtranslate('LBL_SAVE_SETTINGS', $MODULENAME)}</button>
				{if $PARENT neq 'Settings'}
					<a type="reset" data-url="{$MODULE_MODEL->getBaseExtensionUrl($SOURCEMODULE)}" class="cancelLink navigationLink">{vtranslate('LBL_CANCEL', $MODULENAME)}</a>
				{/if}
			</div>
		</div>		
	</form>
</div>