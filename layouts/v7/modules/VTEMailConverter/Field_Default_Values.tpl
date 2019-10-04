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
{assign var='TIME_FORMAT' value=$USER_MODEL->get('hour_format')}
<div style="visibility: hidden; height: 0px;" id="defaultValuesElementsContainer">
	{foreach key=_FIELD_NAME item=_FIELD_INFO from=$AVAILABLE_FIELDS}
		{assign var="_FIELD_NAME" value=$_FIELD_INFO->get('name')}
		<div id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue_container" class="small span11">
			{assign var="_FIELD_TYPE" value=$_FIELD_INFO->getFieldDataType()}
			{if $_FIELD_TYPE eq 'picklist' || $_FIELD_TYPE eq 'multipicklist'}
				<select id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="inputElement input-medium default_value_field">
				{if $_FIELD_NAME neq 'hdnTaxType'} <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option> {/if}
				{foreach key=PICKLIST_VALUE item=_PICKLIST_LABEL from=$_FIELD_INFO->getPicklistValues()}
					<option value="{$PICKLIST_VALUE}">{$_PICKLIST_LABEL}</option>
				{/foreach}
				</select>
			{elseif $_FIELD_TYPE eq 'integer'}
				<input type="text" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="inputElement input-medium defaultInputTextContainer default_value_field" value="0" />
			{elseif $_FIELD_TYPE eq 'owner' || $_FIELD_INFO->get('uitype') eq '52'}
				<select id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="inputElement input-medium default_value_field">
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
				<div class="input-group inputElement" style="margin-bottom: 3px">
					<input type="text" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" readonly
							data-date-format="{$DATE_FORMAT}" data-fieldtype="date" class="inputElement defaultInputTextContainer span2 form-control default_value_field" value="" />
					<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
				</div>
			{elseif $_FIELD_TYPE eq 'datetime'}
				<div class="input-group inputElement" style="margin-bottom: 3px">
					<input type="text" data-fieldtype="dateTimeField" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" readonly
						   class="inputElement defaultInputTextContainer input-medium span2 default_value_field form-control" value="" data-date-format="{$DATE_FORMAT}"/>
					<span class="input-group-addon"><i class="fa fa-calendar "></i></span>
				</div>
			{elseif $_FIELD_TYPE eq 'time'}
				<div class="input-group inputElement time">
					<input id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" data-fieldtype="time" type="text" data-format="{$TIME_FORMAT}" class="form-control default_value_field" value="{$FIELD_VALUE}" name="{$FIELD_NAME}" data-rule-time="true"/>
					<span class="input-group-addon" style="width: 30px;"><i class="fa fa-clock-o"></i></span>
				</div>
			{elseif $_FIELD_TYPE eq 'boolean'}
				<input type="checkbox" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="inputElement input-medium default_value_field" />
			{elseif $_FIELD_TYPE neq 'reference'}
				<input type="input" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="inputElement defaultInputTextContainer input-medium default_value_field" />
			{else}
				<input type="input" id="{$_FIELD_INFO->getModuleName()}_{$_FIELD_NAME}_defaultvalue" class="inputElement defaultInputTextContainer input-medium default_value_field" />
			{/if}
		</div>
	{/foreach}
</div>