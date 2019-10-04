{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <span class="span3">{vtranslate('LBL_SELECT_FIELDS',$MODULE)}<span class="redColor">*</span></span>
    <span class="span7 row-fluid">
        <select class="select2 span8" data-validation-engine='validate[required]' id="seleted_fields" multiple="true" name="seleted_fields[]">
            {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$MODULE_RECORD_STRUCTURE}
            <optgroup label='{vtranslate($BLOCK_LABEL, $SELECTED_MODULE_NAME)}'>
                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                    {assign var=columnNameApi value=getCustomViewColumnName}
                    <option {if in_array({$FIELD_MODEL->$columnNameApi()}, $SELECTED_FIELDS)} selected="" {/if} value="{$FIELD_MODEL->$columnNameApi()}">{vtranslate($FIELD_MODEL->get('label'),$SELECTED_MODULE_NAME)}</option>
                {/foreach}
            </optgroup>
            {/foreach}
            {if $MODTRACKER_FIELDS|count > 0}
                <optgroup label="{vtranslate('ModTracker', 'ModTracker')}">
                    {foreach from=$MODTRACKER_FIELDS key=FIELD_LBL item=FIELD_NAME}
                        <option {if in_array({$FIELD_NAME}, $SELECTED_FIELDS)} selected="" {/if} value="{$FIELD_NAME}">{vtranslate($FIELD_LBL,$MODULE)}</option>
                    {/foreach}
                </optgroup>
            {/if}
        </select>
    </span>
    <input type="hidden" name="selectedFieldsList" />
{/strip}