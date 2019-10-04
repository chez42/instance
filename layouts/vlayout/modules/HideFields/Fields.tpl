{*/* ********************************************************************************
* The content of this file is subject to the Hide Fields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div>
    <label class="muted control-label">
        &nbsp;<span class="redColor">*</span> <strong>{vtranslate('LBL_FIELDS',$QUALIFIED_MODULE)}</strong>
    </label>
    <div class="controls row-fluid">
        <select class="select2 span8" id="selected_fields" multiple="true" name="fields[]"  data-validation-engine="validate[ required,funcCall[Vtiger_Base_Validator_Js.invokeValidation]]">
            <option value="0" data-id="0" {if in_array(0, $BLOCK_DATA['fields'])} selected="" {/if}>{vtranslate("--All--",$QUALIFIED_MODULE)}</option>
            {foreach from=$FIELDS key=FIELD_NAME item=FIELD_DATA}
                {if $FIELD_DATA->isEditable() && $BLOCK_DATA['just_choisen_all'] eq 0}
                    <option {if in_array($FIELD_DATA->get('id'), $BLOCK_DATA['fields'])} selected="" {/if} value="{$FIELD_DATA->get('id')}" data-id={$FIELD_DATA->get('id')}>{vtranslate($FIELD_DATA->get('label'),$QUALIFIED_MODULE)}</option>
                {/if}
            {/foreach}
        </select>
    </div>
    <input type="hidden" name="selectedFieldsList" />
    <input type="hidden" name="selectedAll" value=" {$BLOCK_DATA['just_choisen_all']}" />
    <input type="hidden" name="topFieldIdsList" value='{ZEND_JSON::encode($BLOCK_DATA['fields'])}' />
</div>