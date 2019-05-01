{*<!--
/* ********************************************************************************
* The content of this file is subject to the Google Address ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{foreach $FIELDS_MAPPING as $FIELD_MAPPING}
    <tr class="mapping_fields">
        <td class="fieldLabel col-lg-2">
            <select class="select2 inputElement google_field" type="picklist">
                <option value="">{vtranslate('LBL_SELECT_OPTION', 'GoogleAddress')}</option>
                {foreach $GOOGLE_FIELDS as $GOOGLE_FIELD}
                    <option value="{$GOOGLE_FIELD}" {if $GOOGLE_FIELD == $FIELD_MAPPING}selected{/if}>
                        {vtranslate("LBL_{$GOOGLE_FIELD|upper}", 'GoogleAddress')}
                    </option>
                {/foreach}
            </select>
        </td>
        <td class="fieldValue col-lg-3">
            <select class="inputElement select2 vtiger_field" type="picklist" name="{$FIELD_MAPPING}" data-validation-engine='validate[required]]'>
                <option value="">{vtranslate('LBL_SELECT_OPTION', 'GoogleAddress')}</option>
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                    <optgroup label='{vtranslate($BLOCK_LABEL, $SELECTED_MODULE)}'>
                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                            <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}"
                                    {if $FIELD_MODEL->getCustomViewColumnName() eq $ADDRESS_DETAIL[$FIELD_MAPPING]}selected{/if}
                            >{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE)}
                            </option>
                        {/foreach}
                    </optgroup>
                {/foreach}
            </select>
        </td>
        <td class="fieldValue col-lg-2">
            <div class="actions pull-left" style="margin-top: 5px; margin-right: 60px;">
                <a class="deleteRecordButton"><i title="Delete" class="fa fa-trash fa-lg alignMiddle"></i></a>
            </div>
        </td>
    </tr>
{/foreach}

<tr class="addMoreGoogleField hide mapping_fields">
    <td class="fieldLabel col-lg-2">
        <select class="inputElement google_field" type="picklist">
            <option value="">{vtranslate('LBL_SELECT_OPTION', 'GoogleAddress')}</option>
            {foreach $GOOGLE_FIELDS as $GOOGLE_FIELD}
                <option value="{$GOOGLE_FIELD}">
                    {vtranslate("LBL_{$GOOGLE_FIELD|upper}", 'GoogleAddress')}
                </option>
            {/foreach}
        </select>
    </td>
    <td class="fieldValue col-lg-3">
        <select class="inputElement vtiger_field " name="" type="picklist" data-validation-engine='validate[required]]'>
            <option value="">{vtranslate('LBL_SELECT_OPTION', 'GoogleAddress')}</option>
            {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                <optgroup label='{vtranslate($BLOCK_LABEL, $SELECTED_MODULE)}'>
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                        <option value="{$FIELD_MODEL->getCustomViewColumnName()}" data-field-name="{$FIELD_NAME}">
                            {vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE)}
                        </option>
                    {/foreach}
                </optgroup>
            {/foreach}
        </select>
    </td>
    <td class="fieldValue col-lg-2">
        <div class="actions pull-left" style="margin-top: 5px; margin-right: 60px;">
            <a class="deleteRecordButton"><i title="Delete" class="fa fa-trash fa-lg alignMiddle"></i></a>
        </div>
    </td>
</tr>

<tr class="add_mapping_field">
    <td colspan="2">
        <button class="btn" type="button" id="addMore" name="addMore"><strong>{vtranslate('LBL_ADD_MORE', 'GoogleAddress')}</strong></button>
    </td>
</tr>