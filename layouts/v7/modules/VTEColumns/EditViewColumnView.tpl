{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
********************************************************************************/
-->*}
    {if $NUM_OF_COLUMNS le 6}
        <style>
            .vte-columns-{$NUM_OF_COLUMNS} .inputElement {
                height: 30px;
                width: 150px!important;
            }
            .vte-columns-{$NUM_OF_COLUMNS} .input-group {
                width: 124px!important;
            }
            .vte-columns-{$NUM_OF_COLUMNS} .autoComplete {
                height: 30px;
                width: 90px!important;
            }
            .vte-columns-{$NUM_OF_COLUMNS} .input-group {
                min-width: auto!important;
            }
        </style>
    {elseif $NUM_OF_COLUMNS le 8}
        <style>
            .vte-columns-{$NUM_OF_COLUMNS}  .inputElement {
                height: 30px;
                width: 120px!important;
            }
            .vte-columns-{$NUM_OF_COLUMNS}  .input-group {
                width: 124px!important;
            }
            .vte-columns-{$NUM_OF_COLUMNS}  .autoComplete {
                height: 30px;
                width: 70px!important;
            }
            .vte-columns-{$NUM_OF_COLUMNS} .input-group {
                min-width: auto!important;
            }
        </style>
     {else}
            <style>
                .vte-columns-{$NUM_OF_COLUMNS} .inputElement {
                    width: 100px!important;
                }
                .vte-columns-{$NUM_OF_COLUMNS} .input-group {
                    width: 104px!important;
                }
                .vte-columns-{$NUM_OF_COLUMNS} .autoComplete {
                    width: 50px!important;
                }
                .vte-columns-{$NUM_OF_COLUMNS} .input-group {
                    min-width: 100px!important;
                }
            </style>
    {/if}
<table class="table table-borderless detailview-table">
<tr>
        {assign var=COUNTER value=0}
        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
        {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
        {assign var="refrenceListCount" value=count($refrenceList)}
        {if $FIELD_MODEL->isEditable() eq true}
        {if $FIELD_MODEL->get('uitype') eq "19"}
        {if $COUNTER eq '1'}
        <td></td><td></td></tr><tr>
        {assign var=COUNTER value=0}
        {/if}
        {/if}
        {if $COUNTER eq $NUM_OF_COLUMNS}
    </tr><tr>
        {assign var=COUNTER value=1}
        {else}
        {assign var=COUNTER value=$COUNTER+1}
        {/if}
        <td class="fieldLabel alignMiddle">
            {if $isReferenceField eq "reference"}
                {if $refrenceListCount > 1}
                    {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                    {assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                    {if !empty($REFERENCED_MODULE_STRUCTURE)}
                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCTURE->get('name')}
                    {/if}
                    <select style="width: 140px;" class="select2 referenceModulesList">
                        {foreach key=index item=value from=$refrenceList}
                            <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $value)}</option>
                        {/foreach}
                    </select>
                {else}
                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                {/if}
            {elseif $FIELD_MODEL->get('uitype') eq "83"}
                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) COUNTER=$COUNTER MODULE=$MODULE}
                {if $TAXCLASS_DETAILS}
                    {assign 'taxCount' count($TAXCLASS_DETAILS)%2}
                    {if $taxCount eq 0}
                        {if $COUNTER eq 2}
                            {assign var=COUNTER value=1}
                        {else}
                            {assign var=COUNTER value=2}
                        {/if}
                    {/if}
                {/if}
            {else}
                {if $MODULE eq 'Documents' && $FIELD_MODEL->get('label') eq 'File Name'}
                    {assign var=FILE_LOCATION_TYPE_FIELD value=$RECORD_STRUCTURE['LBL_FILE_INFORMATION']['filelocationtype']}
                    {if $FILE_LOCATION_TYPE_FIELD}
                        {if $FILE_LOCATION_TYPE_FIELD->get('fieldvalue') eq 'E'}
                            {vtranslate("LBL_FILE_URL", $MODULE)}&nbsp;<span class="redColor">*</span>
                        {else}
                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                        {/if}
                    {else}
                        {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                    {/if}
                {else}
                    {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                {/if}
            {/if}
            &nbsp;{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
        </td>
        {if $FIELD_MODEL->get('uitype') neq '83'}
            <td class="fieldValue" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                <div class="vte-columns-{$NUM_OF_COLUMNS}">
                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE) FIELD_NAME=$FIELD_MODEL->get('name')}
                </div>
            </td>
        {/if}
        {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_NAME eq 'unit_price')}
            <div id="divMoreCurrencies" class="hide">
                <a id="moreCurrencies" class="span cursorPointer">{vtranslate('LBL_MORE_CURRENCIES', $MODULE)}>></a>
                <span id="moreCurrenciesContainer" class="hide"></span>
            </div>
        {/if}
        {if $BLOCK_FIELDS|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype"}
            <td class="{$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
        {/if}
        {/if}
        {/foreach}
        {*If their are odd number of fields in edit then border top is missing so adding the check*}
        {if $COUNTER is odd}
            <td></td>
            <td></td>
        {/if}
    </tr>
</table>