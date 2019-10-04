{*/* ********************************************************************************
* The content of this file is subject to the Dynamic Blocks ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<label class="muted control-label">
    &nbsp;<strong>{vtranslate('LBL_VALUES',$QUALIFIED_MODULE)}</strong>
</label>
<div class="controls row-fluid">
    <select class="select2 span8" id="values" multiple="true" name="values[]">
        <option value="__blank__" {if $BLANK_SELECTED eq 1}selected=""{/if}>{vtranslate('NO_VALUE_SELECTED',$QUALIFIED_MODULE)}</option>
        {foreach from=$SELECTED_PICKLISTFIELD_ALL_VALUES key=PICKLIST_VALUE_KEY item=PICKLIST_VALUE}
            <option {if in_array(Vtiger_Util_Helper::toSafeHTML($PICKLIST_VALUE), $BLOCK_DATA['picklist_values'])} selected="" {/if} value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_VALUE)}" data-id={$PICKLIST_VALUE_KEY}>{vtranslate($PICKLIST_VALUE,$SELECTED_MODULE_NAME)}</option>
        {/foreach}
    </select>
</div>