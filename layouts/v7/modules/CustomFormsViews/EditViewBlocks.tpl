{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}

{strip}
    <div class='editContent'>
    <form class="form-horizontal recordEditView" id="EditView" name="EditView" method="post" action="index.php"
          enctype="multipart/form-data">
    {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
    {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
        <input type="hidden" name="picklistDependency"
               value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}'/>
    {/if}
    {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
    {assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
    {if $IS_PARENT_EXISTS}
        {assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
        <input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}"/>
        <input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}"/>
    {else}
        <input type="hidden" name="module" value="{$MODULE}"/>
    {/if}
    <input type="hidden" name="action" value="Save"/>
    <input type="hidden" name="record" value="{$RECORD_ID}"/>
    <input type="hidden" name="defaultCallDuration" value="{$USER_MODEL->get('callduration')}"/>
    <input type="hidden" name="defaultOtherEventDuration" value="{$USER_MODEL->get('othereventduration')}"/>
    {if $IS_RELATION_OPERATION }
        <input type="hidden" name="sourceModule" value="{$SOURCE_MODULE}"/>
        <input type="hidden" name="sourceRecord" value="{$SOURCE_RECORD}"/>
        <input type="hidden" name="relationOperation" value="{$IS_RELATION_OPERATION}"/>
    {/if}
    {if $RETURN_VIEW}
        <input type="hidden" name="returnmodule" value="{$RETURN_MODULE}" />
        <input type="hidden" name="returnview" value="{$RETURN_VIEW}" />
        <input type="hidden" name="returnrecord" value="{$RETURN_RECORD}" />
        <input type="hidden" name="returntab_label" value="{$RETURN_RELATED_TAB}" />
        <input type="hidden" name="returnrelatedModule" value="{$RETURN_RELATED_MODULE}" />
        <input type="hidden" name="returnpage" value="{$RETURN_PAGE}" />
        <input type="hidden" name="returnviewname" value="{$RETURN_VIEW_NAME}" />
        <input type="hidden" name="returnsearch_params" value='{Vtiger_Functions::jsonEncode($RETURN_SEARCH_PARAMS)}' />
        <input type="hidden" name="returnsearch_key" value={$RETURN_SEARCH_KEY} />
        <input type="hidden" name="returnsearch_value" value={$RETURN_SEARCH_VALUE} />
        <input type="hidden" name="returnoperator" value={$RETURN_SEARCH_OPERATOR} />
        <input type="hidden" name="returnsortorder" value={$RETURN_SORTBY} />
        <input type="hidden" name="returnorderby" value="{$RETURN_ORDERBY}" />
        <input type="hidden" name="returnmode" value={$RETURN_MODE} />
        <input type="hidden" name="returnrelationId" value="{$RETURN_RELATION_ID}" />
        <input type="hidden" name="returnparent" value="{$RETURN_PARENT_MODULE}" />
    {/if}
    <div class="contentHeader row-fluid">
        {assign var=SINGLE_MODULE_NAME value='SINGLE_'|cat:$MODULE}
        {if $RECORD_ID neq ''}
            <h3 class="span8 textOverflowEllipsis" style="height: 30px"
                title="{vtranslate('LBL_EDITING', $MODULE)} {$CUSTOM_VIEW_NAME} {$RECORD_STRUCTURE_MODEL->getRecordName()}">{vtranslate('LBL_EDITING', $MODULE)} {$CUSTOM_VIEW_NAME}
                - {$RECORD_STRUCTURE_MODEL->getRecordName()}</h3>
        {else}
            <h3 class="span8 textOverflowEllipsis" style="height: 30px">{vtranslate('LBL_CREATING', 'CustomFormsViews')} {$CUSTOM_VIEW_NAME}</h3>
        {/if}
        <span class="pull-right">
                    <button class="btn btn-success" type="submit"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong>
                    </button>
                    <a class="cancelLink" type="reset"
                       onclick="javascript:window.history.back();">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </span>
    </div>
    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name="EditViewBlockLevelLoop"}
        {if $BLOCK_FIELDS|@count lte 0}{continue}{/if}
        <div class="fieldBlockContainer">
            <h4 class='fieldBlockHeader'>{vtranslate($BLOCK_LABEL, $MODULE)}</h4>
            <hr>
            <table class="table table-borderless">
                <tr>
                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                    {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                    {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                    {assign var="refrenceListCount" value=count($refrenceList)}
                    {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19"}
                    {if $COUNTER eq '1'}
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    {assign var=COUNTER value=0}
                    {/if}
                    {/if}
                    {if $COUNTER eq 2}
                </tr>
                <tr>
                    {assign var=COUNTER value=1}
                    {else}
                    {assign var=COUNTER value=$COUNTER+1}
                    {/if}
                    <td class="fieldLabel alignMiddle">
                        {if $isReferenceField neq "reference"}<label class="muted pull-right">{/if}
                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"}
                                <span class="redColor">*</span>
                            {/if}
                            {if $isReferenceField eq "reference"}
                                {assign var="REFERENCE_LIST" value=$FIELD_MODEL->getReferenceList()}
                                {assign var="REFERENCE_LIST_COUNT" value=count($REFERENCE_LIST)}
                                {if $REFERENCE_LIST_COUNT > 1}
                                    {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                    {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                    {if !empty($REFERENCED_MODULE_STRUCT)}
                                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                    {/if}
                                    <span class="pull-right">
                                    {if $FIELD_MODEL->isMandatory() eq true}<span class="redColor">*</span>{/if}
                                        <select id="{$MODULE}_editView_fieldName_{$FIELD_MODEL->getName()}_dropDown"
                                                class="chzn-select referenceModulesList streched" style="width:160px;">
                                            <optgroup>
                                                {foreach key=index item=value from=$REFERENCE_LIST}
                                                    <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $MODULE)}</option>
                                                {/foreach}
                                            </optgroup>
                                        </select>
                                </span>
                                {else}
                                    <label class="muted pull-right">{if $FIELD_MODEL->isMandatory() eq true}
                                            <span class="redColor">*</span>
                                        {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                {/if}
                            {elseif $FIELD_MODEL->get('uitype') eq "83"}
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                            {else}
                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                            {/if}
                            {if $isReferenceField neq "reference"}</label>{/if}
                    </td>
                    {if $FIELD_MODEL->get('uitype') neq "83"}
                        <td class="fieldValue" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if}  {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) BLOCK_FIELDS=$BLOCK_FIELDS}
                        </td>
                    {/if}
                    {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
                        <td></td>
                        <td></td>
                    {/if}
                    {if $MODULE eq 'Events' && $BLOCK_LABEL eq 'LBL_EVENT_INFORMATION' && $smarty.foreach.blockfields.last }
                        {include file=vtemplate_path('uitypes/FollowUp.tpl',$MODULE) COUNTER=$COUNTER}
                    {/if}
                    {/foreach}
                    {* adding additional column for odd number of fields in a block *}
                    {if $BLOCK_FIELDS|@end eq true and $BLOCK_FIELDS|@count neq 1 and $COUNTER eq 1}
                        <td class="fieldLabel {$WIDTHTYPE}"></td>
                        <td class="{$WIDTHTYPE}"></td>
                    {/if}
                </tr>
            </table>
        </div>
        <br>
    {/foreach}
{/strip}