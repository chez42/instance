{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Documents/views/AddFolder.php *}
{strip}
<div class="modal-dialog modelContainer">
	<div class = "modal-content">
	
	{assign var=HEADER_TITLE value={vtranslate('LBL_ADD_NEW_FOLDER', $MODULE)}}
	{if $FOLDER_ID}
		{assign var=HEADER_TITLE value="{vtranslate('LBL_EDIT_FOLDER', $MODULE)}: {$FOLDER_NAME}"}
	{/if}
	{include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
	<form class="form-horizontal" id="addDocumentsFolder" method="post" action="index.php">
		<input type="hidden" name="module" value="{$MODULE}" />
		<input type="hidden" name="action" value="GetTreeData" />
		<input type="hidden" name="mode" value="{$SAVE_MODE}" />
		{if $SRC neq 'List'}
			<input type="hidden" name="id" value="{$PARENT}" />
		{/if}
		{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
							
		{assign var=ACCESSIBLE_USER_LIST value=$USER_MODEL->getAccessibleUsersForModule($MODULE)}
						
		<div class="modal-body">
			<div class="container-fluid">
				{if $SRC eq 'List'}
					<div class="form-group">
						<label class="control-label fieldLabel col-sm-3">
							{vtranslate('Parent Folder', $MODULE)}
						</label>
						<div class="controls col-sm-9">
							<select class="select2 inputElement" data-name="id" name="id">
								<option value=''>Select Folder </option>
								{foreach  item=FOLDER from=$PARENT_FOLDERS}
				                    <option value="{$FOLDER->getId()}" data-picklistvalue= '{$FOLDER->get('folder_name')}'>
				                    {$FOLDER->get('folder_name')}
				                    </option>
								{/foreach}
							</select>	
						</div>
					</div>
				{/if}
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						<span class="redColor">*</span>
						{vtranslate('LBL_FOLDER_NAME', $MODULE)}
					</label>
					<div class="controls col-sm-9">
						<input class="inputElement" id="documentsFolderName" data-rule-required="true" name="text" type="text" value="{if $FOLDER_NAME neq null}{$FOLDER_NAME}{/if}"/>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						{vtranslate('Share With', $MODULE)}
					</label>
					<div class="controls col-sm-9">
					
						<select class="select2 inputElement" data-name="view_permissions" name="view_permissions[]" multiple>
							<optgroup label="{vtranslate('LBL_USERS')}">
								{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
				                    <option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}'{foreach item=USER from=$VIEWPERMISSIONS}{if $USER eq $OWNER_ID && $USER_MODEL->id neq $USER} selected {/if}{/foreach}
										{if array_key_exists($OWNER_ID, $ACCESSIBLE_USER_LIST)} data-recordaccess=true {else} data-recordaccess=false {/if}
										data-userId="{$CURRENT_USER_ID}">
				                    {$OWNER_NAME}
				                    </option>
								{/foreach}
							</optgroup>
						</select>	
					</div>
				</div>
			</div>
			{if $USER_MODEL->isAdminUser()}
				<div class="form-group">
					<label class="control-label fieldLabel col-sm-3">
						{vtranslate('Global Folder', $MODULE)}
					</label>
					<div class="controls col-sm-9">
						<input type="checkbox" name='default_for_all_users' value="1" {if $GLOBAL} checked {/if}/>
					</div>
				</div>
			{/if}	
			<div class="form-group">
				<label class="control-label fieldLabel col-sm-3">
					{vtranslate('Hide From Portal', $MODULE)}
				</label>
				<div class="controls col-sm-9">
					<input type="checkbox" name='hide_from_portal' value="1" {if $HIDE} checked {/if}/>
				</div>
			</div>
			
			<div class="form-group">
				<label class="control-label fieldLabel col-sm-3">
					{vtranslate('Is Default', $MODULE)}
				</label>
				<div class="controls col-sm-9">
					<input type="checkbox" name='is_default' value="1" {if $IS_DEFAULT} checked disabled{/if}/>
				</div>
			</div>
			
		</div>
		{include file='ModalFooter.tpl'|@vtemplate_path:$MODULE}
	</form>
	</div>
</div>
{/strip}

