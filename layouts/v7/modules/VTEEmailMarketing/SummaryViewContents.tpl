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
{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
   <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
{/if}
<table class="summary-table no-border">
	<tbody>
	{foreach item=FIELD_MODEL key=FIELD_NAME from=$SUMMARY_RECORD_STRUCTURE['SUMMARY_FIELDS']}
        {assign var=fieldDataType value=$FIELD_MODEL->getFieldDataType()}
		{if $FIELD_MODEL->get('name') neq 'modifiedtime'}
			<tr class="summaryViewEntries">
				<td class="fieldLabel" >
                        <label class="muted textOverflowEllipsis" title="{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}">
                            {vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}
                            {if $FIELD_MODEL->get('uitype') eq '71' || $FIELD_MODEL->get('uitype') eq '72'}
							{assign var=CURRENCY_INFO value=getCurrencySymbolandCRate($USER_MODEL->get('currency_id'))}
							&nbsp;({$CURRENCY_INFO['symbol']})
                            {/if}
                        </label>
                    </td>
				<td class="fieldValue">
                    <div class="row">
                        {assign var=DISPLAY_VALUE value="{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get("fieldvalue"))}"}                  
                        <span class="value textOverflowEllipsis" title="{strip_tags($DISPLAY_VALUE)}"  {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'}style="word-wrap: break-word;"{/if}>
                            {include file=$FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName()|@vtemplate_path:$MODULE_NAME FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                            {if $FIELD_MODEL->get('name') eq 'vteemailmarketing_status'}
                                {if $FIELD_MODEL->get("fieldvalue") eq 'Sending'}
                                    <div class="action_send_mail">
                                        <button class="btn btn-primary action_scheduler" id="pause_scheduler" value="Pause">Pause</button>
                                        <button class="btn btn-danger action_scheduler" id="stop_scheduler" value="Stop">Stop</button>
                                    </div>
                                {elseif $FIELD_MODEL->get("fieldvalue") eq 'Paused'}
                                    <button class="btn btn-success action_scheduler" id="resume_scheduler" value="Resume">Resume</button>
                                {elseif ($FIELD_MODEL->get("fieldvalue") eq 'Failed' ||  $FIELD_MODEL->get("fieldvalue") eq 'Sent') && $RECORD->get('failed_to_send') > 0}
                                    <button class="btn btn-primary action_scheduler" style="width: auto !important;" id="restart_scheduler" value="Retry Failed">Retry Failed</button>

                                {/if}
                            {/if}
                        </span>
                        {if $FIELD_MODEL->isEditable() eq 'true' && $IS_AJAX_ENABLED && $FIELD_MODEL->isAjaxEditable() eq 'true' && $FIELD_MODEL->get('uitype') neq 69}
                            <span class="hide edit">
                                {if $FIELD_MODEL->getFieldDataType() eq 'multipicklist'}
                                <input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}[]' data-type="{$fieldDataType}" data-displayvalue='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' data-value="{$FIELD_MODEL->get('fieldvalue')}" />
                                {else}
                                <input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}' data-value="{$FIELD_MODEL->get('fieldvalue')}" />
                                {/if}
                            </span>
                        {/if}
                    </div>
				</td>
			</tr>
		{/if}
	{/foreach}
	</tbody>
</table>

{/strip}