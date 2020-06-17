{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
{if $FIELD_MODEL->get('uitype') eq '901'}
	{assign var=ALL_ACTIVEUSER_LIST value=$USER_MODEL->getAccessibleUsers()}
	{assign var=ALL_ACTIVEGROUP_LIST value=$USER_MODEL->getAccessibleGroups()}
	{assign var=ASSIGNED_USER_ID value=$FIELD_MODEL->get('name')}
    {assign var=CURRENT_USER_ID value=$USER_MODEL->get('id')}
	{assign var=FIELD_VALUE value=explode(' |##| ',$FIELD_MODEL->get('fieldvalue'))}

	<select id="accessible_ids" class="select2-container select2-container-multi select2 inputElement {$ASSIGNED_USER_ID}" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-name="{$ASSIGNED_USER_ID}" name="{$ASSIGNED_USER_ID}[]" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator={Zend_Json::encode($SPECIAL_VALIDATOR)}{/if} multiple style="width: 90%;">
		<optgroup label="{vtranslate('LBL_USERS')}">
			{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEUSER_LIST}
            	<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' 
            		{if $OWNER_ID|in_array:$FIELD_VALUE } selected {/if}>
                    {$OWNER_NAME}
                    </option>
			{/foreach}
		</optgroup>
		<optgroup label="{vtranslate('LBL_GROUPS')}">
			{foreach key=OWNER_ID item=OWNER_NAME from=$ALL_ACTIVEGROUP_LIST}
				<option value="{$OWNER_ID}" data-picklistvalue= '{$OWNER_NAME}' 
				{if $OWNER_ID|in_array:$FIELD_VALUE } selected {/if}>
				{$OWNER_NAME}
				</option>
			{/foreach}
		</optgroup>
	</select>
{/if}
{/strip}