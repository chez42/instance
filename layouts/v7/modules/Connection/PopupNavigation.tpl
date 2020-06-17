{*<!--
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}

{strip}
    <div class="col-md-1">
        {if $MULTI_SELECT}
            {if !empty($LISTVIEW_ENTRIES)}<button class="select btn btn-default" disabled="disabled"><strong>{vtranslate('LBL_ADD', $MODULE)}</strong></button>{/if}
        {else}
            &nbsp;
        {/if}
    </div>
    <div class="col-md-3">
        {if !empty($FROMFIELD)}
	        <select id="status{$row_no}" class="select2 status smallInputBox inputElement pull-right" name="connection_from_pop" >
				<option value="">{vtranslate('Select From..','Vtiger')}</option>
				{foreach  item=PICKLIST_VAL from=$FROMFIELD}
					<option value="{$PICKLIST_VAL}" >{$PICKLIST_VAL}</option>
				{/foreach}
	        </select>
        {/if}
    </div>
    <div class="col-md-3">
        {if !empty($TOFIELD)}
	        <select id="status{$row_no}" class="select2 status smallInputBox inputElement pull-right" name="connection_to_pop" >
				<option value="">{vtranslate('Select To..','Vtiger')}</option>
				{foreach  item=PICKLIST_VAL from=$TOFIELD}
					<option value="{$PICKLIST_VAL}">{$PICKLIST_VAL}</option>
				{/foreach}
	        </select>
        {/if}
    </div>
    <div class="col-md-5">
        {assign var=RECORD_COUNT value=$LISTVIEW_ENTRIES_COUNT}
        {include file="Pagination.tpl"|vtemplate_path:$MODULE SHOWPAGEJUMP=true}
    </div>
{/strip}