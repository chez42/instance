{*/* ********************************************************************************
* The content of this file is subject to the Related Record Update ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
    <script type="text/javascript" src="layouts/v7/modules/RelatedRecordUpdate/resources/RelatedRecordUpdate.js"></script>
    <div class="row">
        <div class="col-sm-12"><strong>{vtranslate('LBL_ADD_FIELDS_TO_UPDATE','RelatedRecordUpdate')}</strong></div>
    </div><br>
    <div>
        <button type="button" class="btn" id="addMappingButton">{vtranslate('LBL_ADD_MAPPING','RelatedRecordUpdate')}</button>
    </div><br>
    <div class="col-sm-7">
        <div class="row conditionsContainer" id="save_fieldvaluemapping">
            {assign var=FIELD_VALUE_MAPPING value=ZEND_JSON::decode($TASK_OBJECT->field_value_mapping)}
            {assign var=RELATED_MODULES value=$TASK_OBJECT->getRelatedModules($MODULE_MODEL->get('name'))}
            <input type="hidden" id="fieldValueMapping" name="field_value_mapping" value='{Vtiger_Util_Helper::toSafeHTML($TASK_OBJECT->field_value_mapping)}' />
                <div class="row mappingRow form-group">
                    <div class="col-sm-5 text-center"><label class="control-label">{vtranslate('LBL_SOURCE_FROM','RelatedRecordUpdate')}</label></div>
                    <div class="col-sm-5 text-center"><label class="control-label">{vtranslate('LBL_TARGET_TO','RelatedRecordUpdate')}</label></div>
                    <div class="cursorPointer col-sm-1">&nbsp;
                    </div>
                </div>
            {foreach from=$FIELD_VALUE_MAPPING item=FIELD_MAP}
                {if $FIELD_MAP['fieldname'] neq ''}
                    <div class="row mappingRow form-group">
                        <div class="col-sm-5">
                            <select name="fieldname" class="select2" style="min-width: 250px" data-placeholder="{vtranslate('LBL_SELECT_FIELD','RelatedRecordUpdate')}">
                                <option></option>
                                {foreach from=$MODULE_MODEL->getFields() item=FIELD_MODEL}
                                    {if !$FIELD_MODEL->isEditable() or ($MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                                        {continue}
                                    {/if}
                                    {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}

                                    <option value="{$FIELD_MODEL->get('name')}" {if $FIELD_MAP['fieldname'] eq $FIELD_MODEL->get('name')}selected=""{/if}data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                        {if $SOURCE_MODULE neq $MODULE_MODEL->get('name')}
                                            ({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
                                        {else}
                                            {vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                        {/if}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="fieldUiHolder col-sm-5 marginLeftZero">
                            <select name="related_fieldname" class="select2" style="min-width: 250px" data-placeholder="{vtranslate('LBL_SELECT_FIELD','RelatedRecordUpdate')}">
                                <option></option>
                                {foreach from=$RELATED_MODULES key=REL_MODULENAME item=REL_MODULE}
                                    {if $SOURCE_MODULE eq $REL_MODULENAME}{continue}{/if}
                                    {assign var=REL_MODULE_MODEL value=$REL_MODULE['ModuleModel']}
                                    {assign var=REFERENCE_FIELD value=$REL_MODULE['ReferenceField']}
                                    <optgroup label="{vtranslate($REL_MODULENAME, $REL_MODULENAME)}">
                                        {foreach from=$REL_MODULE_MODEL->getFields() item=FIELD_MODEL}
                                            {if !$FIELD_MODEL->isEditable() or ($REL_MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                                                {continue}
                                            {/if}
                                            {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                            {assign var=FIELD_NAME value=$REL_MODULENAME|cat:'::'}
                                            {assign var=FIELD_NAME value=$FIELD_NAME|cat:$REFERENCE_FIELD}
                                            {assign var=FIELD_NAME value=$FIELD_NAME|cat:'::'}
                                            {assign var=FIELD_NAME value=$FIELD_NAME|cat:$FIELD_MODEL->get('name')}

                                            <option value="{$FIELD_NAME}" {if $FIELD_MAP['related_fieldname'] eq $FIELD_NAME}selected=""{/if}data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                                ({vtranslate($REL_MODULE_MODEL->get('name'), $REL_MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $REL_MODULE_MODEL->get('name'))}
                                            </option>
                                        {/foreach}
                                    </optgroup>
                                {/foreach}
                            </select>
                        </div>
                        <div class="cursorPointer col-sm-1">
                            <i class="alignMiddle deleteMappingButton icon-trash"></i>&nbsp;
                        </div>
                    </div>
                {/if}
            {/foreach}
        </div><br>
        <div class="row basicAddFieldContainer hide form-group">
            <div class="col-sm-5">
                <select name="fieldname" data-placeholder="{vtranslate('LBL_SELECT_FIELD','RelatedRecordUpdate')}" style="min-width: 250px">
                    <option></option>
                    {foreach from=$MODULE_MODEL->getFields() item=FIELD_MODEL}
                        {if !$FIELD_MODEL->isEditable() or ($MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                            {continue}
                        {/if}
                        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                        {assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
                        <option value="{$FIELD_MODEL->get('name')}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                            ({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="fieldUiHolder col-sm-5 marginLeftZero">
                <select name="related_fieldname" style="min-width: 250px" data-placeholder="{vtranslate('LBL_SELECT_FIELD','RelatedRecordUpdate')}">
                    <option></option>
                    {foreach from=$RELATED_MODULES key=REL_MODULENAME item=REL_MODULE}
                        {if $SOURCE_MODULE eq $REL_MODULENAME}{continue}{/if}
                        {if $REL_MODULENAME eq 'ModComments'}{continue}{/if}
                        {assign var=REL_MODULE_MODEL value=$REL_MODULE['ModuleModel']}
                        {assign var=REFERENCE_FIELD value=$REL_MODULE['ReferenceField']}
                        <optgroup label="{vtranslate($REL_MODULENAME, $REL_MODULENAME)}">
                            {foreach from=$REL_MODULE_MODEL->getFields() item=FIELD_MODEL}
                                {if !$FIELD_MODEL->isEditable() or ($REL_MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                                    {continue}
                                {/if}
                                {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                {assign var=FIELD_NAME value=$REL_MODULENAME|cat:'::'}
                                {assign var=FIELD_NAME value=$FIELD_NAME|cat:$REFERENCE_FIELD}
                                {assign var=FIELD_NAME value=$FIELD_NAME|cat:'::'}
                                {assign var=FIELD_NAME value=$FIELD_NAME|cat:$FIELD_MODEL->get('name')}
                                <option value="{$FIELD_NAME}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                    ({vtranslate($REL_MODULE_MODEL->get('name'), $REL_MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $REL_MODULE_MODEL->get('name'))}
                                </option>
                            {/foreach}
                        </optgroup>
                    {/foreach}
                </select>
            </div>
            <div class="cursorPointer col-sm-1" style="margin-left: 10px">
                <i class="alignMiddle deleteMappingButton glyphicon glyphicon-trash"></i>&nbsp;
            </div>
        </div>
    </div>
    <div class="col-sm-5" style="min-height: 160px; border-left: 3px solid #53aace;">
        <H5 style="color: #53aace"><i class="fa fa-info-circle"></i>&nbsp;{vtranslate('LBL_INFO','RelatedRecordUpdate')}</H5>
        <p>{vtranslate('LBL_INFO_TEXT','RelatedRecordUpdate')}</p>
    </div>
{/strip}