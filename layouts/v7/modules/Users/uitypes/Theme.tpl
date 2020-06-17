{strip}
{assign var="FIELD_INFO" value=Vtiger_Util_Helper::toSafeHTML(Zend_Json::encode($FIELD_MODEL->getFieldInfo()))}
{assign var=PICKLIST_VALUES value=$FIELD_MODEL->getAllSkins()}
{assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
<select class="inputElement select2" name="{$FIELD_MODEL->getFieldName()}" data-fieldname="{$FIELD_MODEL->getFieldName()}"  data-fieldtype="picklist" data-validation-engine="validate[{if $FIELD_MODEL->isMandatory() eq true} required,{/if}funcCall[Vtiger_Base_Validator_Js.invokeValidation]]" data-fieldinfo='{$FIELD_INFO}' {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if} >
    <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
    {foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
		{assign var=CLASS_NAME value="picklistColor_{$PICKLIST_NAME|replace:' ':'_'}"}
		<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" class="{$CLASS_NAME}" {if trim(decode_html($FIELD_MODEL->get('fieldvalue'))) eq trim($PICKLIST_NAME)} selected {/if}>{ucfirst($PICKLIST_NAME)}</option>
	{/foreach}
</select>
<style type="text/css">
	{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$PICKLIST_VALUES}
		{assign var=CLASS_NAME value="{$PICKLIST_NAME|replace:' ':'_'}"}
		.picklistColor_{$CLASS_NAME} {
			background-color: {$PICKLIST_VALUE} !important;
			color: #FFF !important;
		}
		
	{/foreach}
</style>
{/strip}