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
{assign var="dateFormat" value=$USER_MODEL->get('date_format')}
{assign var="currentDate" value=Vtiger_Date_UIType::getDisplayDateValue('')}
{assign var="time" value=Vtiger_Time_UIType::getDisplayTimeValue(null)}
{assign var="currentTimeInVtigerFormat" value=Vtiger_Time_UIType::getTimeValueInAMorPM($time)}
{if $COUNTER eq 2}
</tr>
	{assign var=COUNTER value=1}
{else}
	<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr>
	{assign var=COUNTER value=$COUNTER+1}
{/if}
{assign var=FOLLOW_UP_LABEL value={vtranslate('LBL_HOLD_FOLLOWUP_ON',$MODULE)}}
<tr class="followUpContainer massEditActiveField" {if !($SHOW_FOLLOW_UP)}style="display:none;" {/if}>
	<td class="fieldLabel alignMiddle">
		<label class="muted  marginRight10px">
			<input name="followup" type="checkbox" class="alignTop" {if $FOLLOW_UP_STATUS} checked{/if}/>
			{$FOLLOW_UP_LABEL}
		</label>	
	</td>
	{$FIELD_INFO['label'] = {$FOLLOW_UP_LABEL}}
	<td class="fieldValue">
		<div>
			<div class="input-group inputElement" style="margin-bottom: 3px">
				<input name="followup_date_start" type="text" class="dateField form-control" data-date-format="{$dateFormat}" type="text"  data-fieldinfo= '{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}'
					   value="{if !empty($FOLLOW_UP_DATE)}{$FOLLOW_UP_DATE}{else}{$currentDate}{/if}" data-rule-greaterThanOrEqualToToday="true" />
				<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
			</div>		
		</div>
		<div>
			<div class="input-group inputElement time">
				<input type="text" name="followup_time_start" class="timepicker-default form-control" 
					   value="{if !empty($FOLLOW_UP_TIME)}{$FOLLOW_UP_TIME}{else}{$currentTimeInVtigerFormat}{/if}" />
				<span class="input-group-addon" style="width: 30px;">
					<i class="fa fa-clock-o"></i>
				</span>
			</div>	
		</div>
	</td>
	<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
</tr> 
