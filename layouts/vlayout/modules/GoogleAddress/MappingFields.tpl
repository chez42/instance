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
    <div class="control-group">
        <select class="select2 muted control-label google_field">
            <option value="">{vtranslate('LBL_SELECT_OPTION', 'GoogleAddress')}</option>
            {foreach $GOOGLE_FIELDS as $GOOGLE_FIELD}
                <option value="{$GOOGLE_FIELD}" {if $GOOGLE_FIELD == $FIELD_MAPPING}selected{/if}>
                    {vtranslate("LBL_{$GOOGLE_FIELD|upper}", 'GoogleAddress')}
                </option>
            {/foreach}
        </select>

        <div class="controls row-fluid">
            <select class="select2 span6 vtiger_field" name="{$FIELD_MAPPING}" data-validation-engine='validate[required]]'>
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
        </div>

        <div class="actions pull-right" style="margin-top: -20px; margin-right: 60px;">
            <a class="deleteRecordButton"><i title="Delete" class="icon-trash alignMiddle"></i></a>
        </div>
    </div>
{/foreach}

<div class="control-group addMoreGoogleField hide addGoogleField">
    <select class="muted control-label google_field" >
        <option value="">{vtranslate('LBL_SELECT_OPTION', 'GoogleAddress')}</option>
        {foreach $GOOGLE_FIELDS as $GOOGLE_FIELD}
            <option value="{$GOOGLE_FIELD}">
                {vtranslate("LBL_{$GOOGLE_FIELD|upper}", 'GoogleAddress')}
            </option>
        {/foreach}
    </select>

    <div class="controls row-fluid">
        <select class="span6 vtiger_field " name="" data-validation-engine='validate[required]]'>
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
    </div>

    <div class="actions pull-right" style="margin-top: -20px; margin-right: 60px;">
        <a class="deleteRecordButton"><i title="Delete" class="icon-trash alignMiddle"></i></a>
    </div>
</div>
<div class="block_add_google_fields">

</div>
<div>
    <button class="btn" type="button" id="addMore" name="addMore"><strong>{vtranslate('LBL_ADD_MORE', 'GoogleAddress')}</strong></button>
</div>