{*<!--
/* ********************************************************************************
* The content of this file is subject to the VTEMailConverter("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{assign var='USERS_LIST' value=$USER_MODEL->getAccessibleUsers()}
{assign var='GROUPS_LIST' value=$USER_MODEL->getAccessibleGroups()}
{assign var='DATE_FORMAT' value=$USER_MODEL->get('date_format')}
<div style="visibility: hidden; height: 0px;" id="defaultValuesElementsContainer">
	{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
		{assign var="_FIELD_NAME" value=$_FIELD_INFO->get('name')}
		<span id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue_container" class="small span11">
			{assign var="_FIELD_TYPE" value=$_FIELD_INFO->getFieldDataType()}
			{if $_FIELD_TYPE eq 'picklist' || $_FIELD_TYPE eq 'multipicklist'}
				<select id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="input-medium default_value_field">
				{if $_FIELD_NAME neq 'hdnTaxType'} <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option> {/if}
				{foreach key=PICKLIST_VALUE item=_PICKLIST_LABEL from=$_FIELD_INFO->getPicklistValues()}
					<option value="{$PICKLIST_VALUE}">{$_PICKLIST_LABEL}</option>
				{/foreach}
				</select>
			{elseif $_FIELD_TYPE eq 'integer'}
				<input type="text" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="input-medium defaultInputTextContainer default_value_field" value="0" />
			{elseif $_FIELD_TYPE eq 'owner' || $_FIELD_INFO->get('uitype') eq '52'}
				<select id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="input-medium default_value_field">
					<optgroup label="{vtranslate('LBL_USERS')}">
						{foreach key=OWNER_ID item=OWNER_NAME from=$USERS_LIST}
							<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' data-userId="{$CURRENT_USER_ID}">{$OWNER_NAME}</option>
						{/foreach}
					</optgroup>
					<optgroup label="{vtranslate('LBL_GROUPS')}">
						{foreach key=OWNER_ID item=OWNER_NAME from=$GROUPS_LIST}
							<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' >{$OWNER_NAME}</option>
						{/foreach}
					</optgroup>
				</select>
			{elseif $_FIELD_TYPE eq 'date'}
				<input type="text" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" data-fieldtype="date"
						data-date-format="{$DATE_FORMAT}" class="defaultInputTextContainer span2 dateField default_value_field" value="" />
			{elseif $_FIELD_TYPE eq 'datetime'}
					<input type="text" data-fieldtype="datetime" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue"
						   class="defaultInputTextContainer input-medium span2 dateField default_value_field" value="" data-date-format="{$DATE_FORMAT}"/>
			{elseif $_FIELD_TYPE eq 'boolean'}
				<input type="checkbox" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="input-medium default_value_field" />
			{elseif $_FIELD_TYPE neq 'reference'}
				<input type="input" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="defaultInputTextContainer input-medium default_value_field" />
			{else}
				<input type="input" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="defaultInputTextContainer input-medium default_value_field" />
			{/if}
		</span>
	{/foreach}
</div>