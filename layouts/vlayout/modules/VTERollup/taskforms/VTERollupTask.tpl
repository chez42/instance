{*/* ********************************************************************************
* The content of this file is subject to the Rollup/Calculate Fields ("License") Template;
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
    <script type="text/javascript" src="layouts/vlayout/modules/VTERollup/resources/VTERollup.js"></script>
    <div class="row-fluid">
        <div class="span2"><strong>{vtranslate('LBL_ADD_FIELDS_TO_UPDATE','VTERollup')}</strong></div>
    </div><br>
    <div>
        <div class="span3">
             <button type="button" class="btn" id="addMappingButton">{vtranslate('LBL_ADD_MAPPING','VTERollup')}</button>
        </div>
        <div class="span9"><strong>{vtranslate('ADDNOTE_SOURCE_FIELD','VTERollup')}</strong></div>
    </div><br><br>

    <div class="row-fluid">
        <span  class="span3" style="width: 220px">{vtranslate('LBL_TARGET_FIELD','VTERollup')}
            <a href="#" data-toggle="tooltip" data-placement="top" title="{vtranslate('ADDNOTE_TARGET_FIELD1','VTERollup')} {vtranslate('ADDNOTE_TARGET_FIELD2','VTERollup')}">
                <i class='glyphicon glyphicon-info-sign'></i>
            </a>
        </span>
        <span  class="span3" style="width: 220px">{vtranslate('LBL_SOURCE_FIELD','VTERollup')}
            <a href="#" data-toggle="tooltip" data-placement="top" title="{vtranslate('ADDNOTE_SOURCE_FIELD','VTERollup')}">
                <i class='glyphicon glyphicon-info-sign'></i>
            </a>
        </span>
        <span  class="span3" style="width: 220px">{vtranslate('LBL_METHOD','VTERollup')}
            <a href="#" data-toggle="tooltip" data-placement="top" title="{vtranslate('ADDNOTE_METHOD','VTERollup')}">
                <i class='glyphicon glyphicon-info-sign'></i>
            </a>
        </span>
        <span  class="span1">&nbsp;</span>
    </div>

    <div class="row-fluid conditionsContainer" id="save_fieldvaluemapping">
        {assign var=FIELD_VALUE_MAPPING value=ZEND_JSON::decode($TASK_OBJECT->field_value_mapping)}
        {assign var=RELATED_MODULES value=$TASK_OBJECT->getRelatedModules($MODULE_MODEL->get('name'))}
        <input type="hidden" id="fieldValueMapping" name="field_value_mapping" value='{Vtiger_Util_Helper::toSafeHTML($TASK_OBJECT->field_value_mapping)}' />
        {foreach from=$FIELD_VALUE_MAPPING item=FIELD_MAP}
            <div class="row-fluid mappingRow padding-bottom1per">
				<span class="fieldUiHolder span3" style="width: 220px">
					<select name="target_field" class="select2" style="width: 220px" data-placeholder="{vtranslate('LBL_SELECT_FIELD','VTERollup')}">
                        <option></option>
                        {foreach from=$MODULE_MODEL->getFields() item=FIELD_MODEL}
                            {if !in_array($FIELD_MODEL->getFieldDataType(), array('string', 'currency', 'percentage', 'integer', 'double'))}{continue}{/if}
                            {if !$FIELD_MODEL->isEditable() or ($MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                                {continue}
                            {/if}
                            {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}

                            <option value="{$FIELD_MODEL->get('name')}" {if $FIELD_MAP['target_field'] eq $FIELD_MODEL->get('name')}selected=""{/if}data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                {if $SOURCE_MODULE neq $MODULE_MODEL->get('name')}
                                    ({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
                                {else}
                                    {vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                {/if}
                            </option>
                        {/foreach}
                    </select>
				</span>
				<span class="fieldUiHolder span3" style="width: 220px">
					<select name="source_field" class="select2" style="width: 220px" data-placeholder="{vtranslate('LBL_SELECT_FIELD','VTERollup')}">
                        <option></option>
                        {foreach from=$RELATED_MODULES key=REL_MODULENAME item=REL_MODULE_MODEL}
                            <optgroup label="{vtranslate($REL_MODULENAME, $REL_MODULENAME)}">
                                {foreach from=$REL_MODULE_MODEL->getFields() item=FIELD_MODEL}
                                    {if !in_array($FIELD_MODEL->getFieldDataType(), array('string', 'currency', 'percentage', 'integer', 'double'))}{continue}{/if}
                                    {if ($REL_MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                                        {continue}
                                    {/if}
                                    {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                    {assign var=FIELD_NAME value=$REL_MODULENAME|cat:'::'}
                                    {assign var=FIELD_NAME value=$FIELD_NAME|cat:$FIELD_MODEL->get('column')}
                                    {assign var=FIELD_NAME value=$FIELD_NAME|cat:'::'}
                                    {assign var=FIELD_NAME value=$FIELD_NAME|cat:$FIELD_MODEL->get('name')}

                                    <option value="{$FIELD_NAME}" {if $FIELD_MAP['source_field'] eq $FIELD_NAME}selected=""{/if}data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                        ({vtranslate($REL_MODULE_MODEL->get('name'), $REL_MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $REL_MODULE_MODEL->get('name'))}
                                    </option>
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    </select>
				</span>
                <span class="fieldUiHolder span3" style="width: 220px" >
                    <select  name="method_field" class="select2" style="width: 220px"
                            data-placeholder="{vtranslate('LBL_METHOD_FIELD','VTERollup')}">
                        <option value="SUM" {if $FIELD_MAP['method_field'] eq 'SUM'}selected=""{/if}>SUM</option>
                        <option value="AVG" {if $FIELD_MAP['method_field'] eq 'AVG'}selected=""{/if}>AVG</option>
                        <option value="MIN" {if $FIELD_MAP['method_field'] eq 'MIN'}selected=""{/if}>MIN</option>
                        <option value="MAX" {if $FIELD_MAP['method_field'] eq 'MAX'}selected=""{/if}>MAX</option>
                        <option value="COUNT" {if $FIELD_MAP['method_field'] eq 'COUNT'}selected=""{/if}>COUNT</option>
                    </select>
                </span>
				<span class="cursorPointer span1" >
					<i class="alignMiddle deleteMappingButton icon-trash"></i>
				</span>
            </div>
        {/foreach}
    </div><br>
    <div class="row-fluid basicAddFieldContainer hide padding-bottom1per">
			<span class="fieldUiHolder span3" style="width: 220px" >
				<select  name="target_field" style="width: 220px" data-placeholder="{vtranslate('LBL_TARGET_FIELD','VTERollup')}">
                    <option></option>
                    {foreach from=$MODULE_MODEL->getFields() item=FIELD_MODEL}
                        {if !in_array($FIELD_MODEL->getFieldDataType(), array('string', 'currency', 'percentage', 'integer', 'double'))}{continue}{/if}
                        {if !$FIELD_MODEL->isEditable() or ($MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                            {continue}
                        {/if}
                        {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                        <option value="{$FIELD_MODEL->get('name')}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                            ({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
                        </option>
                    {/foreach}
                </select>
			</span>
			<span class="fieldUiHolder span3" style="width: 220px" >
                <select  name="source_field" style="width: 220px" data-placeholder="{vtranslate('LBL_SOURCE_FIELD','VTERollup')}">
                    <option></option>
                    {foreach from=$RELATED_MODULES key=REL_MODULENAME item=REL_MODULE_MODEL}
                        <optgroup label="{vtranslate($REL_MODULENAME, $REL_MODULENAME)}">
                            {foreach from=$REL_MODULE_MODEL->getFields() item=FIELD_MODEL}
                                {if !in_array($FIELD_MODEL->getFieldDataType(), array('string', 'currency', 'percentage', 'integer', 'double'))}{continue}{/if}
                                {if ($REL_MODULE_MODEL->get('name')=="Documents" and in_array($FIELD_MODEL->get('name'),$RESTRICTFIELDS))}
                                    {continue}
                                {/if}
                                {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                {assign var=FIELD_NAME value=$REL_MODULENAME|cat:'::'}
                                {assign var=FIELD_NAME value=$FIELD_NAME|cat:$FIELD_MODEL->get('column')}
                                {assign var=FIELD_NAME value=$FIELD_NAME|cat:'::'}
                                {assign var=FIELD_NAME value=$FIELD_NAME|cat:$FIELD_MODEL->get('name')}
                                <option value="{$FIELD_NAME}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_MODEL->get('name')}" data-fieldinfo='{ZEND_JSON::encode($FIELD_INFO)}' >
                                    ({vtranslate($REL_MODULE_MODEL->get('name'), $REL_MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $REL_MODULE_MODEL->get('name'))}
                                </option>
                            {/foreach}
                        </optgroup>
                    {/foreach}
                </select>
            </span>
            <span class="fieldUiHolder span3" style="width: 220px" >
                    <select  name="method_field" style="width: 220px" data-placeholder="{vtranslate('LBL_METHOD_FIELD','VTERollup')}">
                    <option value="SUM">SUM</option>
                    <option value="AVG">AVG</option>
                    <option value="MIN">MIN</option>
                    <option value="MAX">MAX</option>
                    <option value="COUNT">COUNT</option>
                </select>
            </span>
			<span class="cursorPointer span1">
				<i class="alignMiddle deleteMappingButton icon-trash"></i>
			</span>
    </div>
{/strip}