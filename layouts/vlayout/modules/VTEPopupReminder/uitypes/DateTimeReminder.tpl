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
{if $FIELD_MODEL->getName() == 'popup_reminder_date'}
    {assign var=DATE_FIELD value=$FIELD_MODEL}
    {assign var=MODULE_MODEL value=$RECORD_STRUCTURE_MODEL->getModule()}
    {assign var=RECORD_MODEL value=$RECORD_STRUCTURE_MODEL->getRecord()}
    {assign var=TIME_FIELD value=$MODULE_MODEL->getField('popup_reminder_time')}
{/if}

{assign var=DATE_VALUE value=$FIELD_MODEL->get('fieldvalue')}
{assign var=TIME_VALUE value=$RECORD_MODEL->get('popup_reminder_time')}
{if $DATE_VALUE neq '' && $TIME_VALUE neq ''}
    {assign var=DATE_TIME_VALUE value=$DATE_VALUE|cat:" "|cat:$TIME_VALUE}

    {* Set the date after converting with repsect to timezone *}
    {assign var=DATE_TIME_CONVERTED_VALUE value=DateTimeField::convertToUserTimeZone($DATE_TIME_VALUE)->format('Y-m-d H:i:s')}
    {assign var=DATE_TIME_COMPONENTS value=explode(' ' ,$DATE_TIME_CONVERTED_VALUE)}
    {assign var=DATE_FIELD value=$DATE_FIELD->set('fieldvalue',$DATE_TIME_COMPONENTS[0])}
    {assign var=TIME_FIELD value=$TIME_FIELD->set('fieldvalue',$DATE_TIME_COMPONENTS[1])}
{/if}
<div>
    {include file=vtemplate_path('uitypes/Date.tpl',$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS FIELD_MODEL=$DATE_FIELD}
</div>
<div>
    {include file=vtemplate_path('uitypes/Time.tpl',$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS FIELD_MODEL=$TIME_FIELD FIELD_NAME=$TIME_FIELD->getFieldName()}
</div>
{/strip}