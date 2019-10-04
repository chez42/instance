{*/* ********************************************************************************
* The content of this file is subject to the Hide Fields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div>
    <label class="muted control-label">
        &nbsp;<strong>{vtranslate('LBL_BLOCK',$QUALIFIED_MODULE)}</strong>
    </label>
    <div class="controls row-fluid">
        <select class="select2 span5" id="block" name="block">
            {foreach from=$BLOCKS key=BLOCK_LBL item=BLOCK}
                <option {if $BLOCK_LBL eq $BLOCK_DATA['block']} selected="" {/if} value="{Vtiger_Util_Helper::toSafeHTML($BLOCK_LBL)}">{vtranslate($BLOCK_LBL,$SELECTED_MODULE_NAME)}</option>
            {/foreach}
        </select>
    </div>
</div>