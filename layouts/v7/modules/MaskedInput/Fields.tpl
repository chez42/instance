{*<!--
/* ********************************************************************************
* The content of this file is subject to the Google Address ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
    <td class="fieldLabel col-lg-2">
    <label class="muted pull-right">
        {vtranslate('LBL_FIELD', 'MaskedInput')}
    </label>
    </td>
    <td class="fieldValue col-lg-3">
        <select class="select2 span6" name="fieldname" data-validation-engine='validate[required]]' style="width: 100%">
            <option value="">{vtranslate('LBL_SELECT_OPTION', 'MaskedInput')}</option>
            {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                <optgroup label='{vtranslate($BLOCK_LABEL, $SELECTED_MODULE)}'>
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                        <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                {if $FIELD_MODEL->getCustomViewColumnName() eq $CONFIGURED_FIELD['fieldname']}selected{/if}
                                >{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE)}
                        </option>
                    {/foreach}
                </optgroup>
            {/foreach}
        </select>
    </td>
