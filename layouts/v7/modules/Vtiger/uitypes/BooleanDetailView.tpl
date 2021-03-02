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
{assign var="FIELD_NAME" value=$FIELD_MODEL->get('name')}
{if (!$FIELD_NAME)}
    {assign var="FIELD_NAME" value=$FIELD_MODEL->getFieldName()}
{/if}
{if $FIELD_MODEL->isEditable()}
	<label class="checkbox-switch">
		<input class="inputElement input-checkbox checkboxSwitch" style="width:15px;height:15px;" {if $FIELD_MODEL->get('fieldvalue') eq true} checked {/if} data-fieldname="{$FIELD_NAME}" 
		type="checkbox" name="{$FIELD_NAME}" />
		<span class="checkbox-slider checkbox-round"></span>
	</label>
{else}
	{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'), $RECORD->getId(), $RECORD)}
{/if}
{/strip}