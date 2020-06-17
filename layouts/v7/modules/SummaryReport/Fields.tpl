{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
    <label for="status" class="col-sm-3 control-label">
        {vtranslate('LBL_SELECT_FIELDS',$MODULE)}<span class="redColor">*</span>
    </label>
    {*<span class="span3">{vtranslate('LBL_SELECT_FIELDS',$MODULE)}<span class="redColor">*</span></span>*}
    <div class="col-sm-5 controls" style="text-align: left">
        <select class="select2 col-sm-6 pull-left" data-rule-required="true"  id="seleted_fields" multiple="true" name="seleted_fields[]" style="text-align: left" >
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
    </div>
    <input type="hidden" name="selectedFieldsList" />
{/strip}