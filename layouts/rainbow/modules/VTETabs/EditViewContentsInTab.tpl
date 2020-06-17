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
{literal}
<style>
    .tab {
        display:none;
    }

    .tab.active {
        display:block;
    }
</style>
{/literal}
{strip}
    {assign var=TAB_ARRAYS_LABEL value=[]}
    {assign var=INVENTORY_LIST value=[113,114,115,116]}
    {assign var=TAB_ARRAYS_BLOCK_FIELDS value=[]}
    {assign var=LABEL_ARRAYS_TAB_WITH_ORDER value=[]}
    {assign var=COMBINE_TAB_LIST value=VTETabs_Module_Model::getCombineTabs($MODULE)}
    {assign var=IMG_BLOCK_LABEL value = ""}
    {assign var=IMG_ARRAYS_BLOCK_FIELD value = []}
    {assign var=IS_IMG_TAB value= false}
    {*Display un_tab block*}
    {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
        {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL]->get('id'))}
        {if $TAB_INFO['is_tab_store'] eq 1}
            {$TAB_ARRAYS_LABEL[] = $BLOCK_LABEL}
            {$TAB_ARRAYS_BLOCK_FIELDS[$BLOCK_LABEL] = $RECORD_STRUCTURE[$BLOCK_LABEL]}
            {continue}
        {/if}
        {if $BLOCK_LABEL eq "LBL_IMAGE_INFORMATION"}
            {$IS_IMG_TAB = true}
            {$IMG_BLOCK_LABEL = $BLOCK_LABEL}
            {$IMG_ARRAYS_BLOCK_FIELD = $RECORD_STRUCTURE[$BLOCK_LABEL]}
            {continue}
        {/if}
        {if $BLOCK_LIST[$BLOCK_LABEL]->get('id')|in_array:$INVENTORY_LIST}
            {continue}
        {/if}
        {if $BLOCK_FIELDS|@count gt 0}
            {include file="ChildEditViewContentsInTab.tpl"|vtemplate_path:"VTETabs" BLOCK_FIELDS = $BLOCK_FIELDS RECORD_STRUCTURE =$RECORD_STRUCTURE BLOCK_LABEL = $BLOCK_LABEL  NONE_TAB = true}
        {/if}
    {/foreach}
    {*End un_tab block*}
    {*Start tab block*}
    {if $TAB_ARRAYS_BLOCK_FIELDS|@count gt 0}
        <div id="vte_tabs" class="fieldBlockContainer tabs">
            <div class="related-tabs">
                <ul class="tab-links nav nav-tabs">
                    {foreach key=BLOCK_LABEL_KEY item=BLOCK_LABEL from=$TAB_ARRAYS_LABEL}
                        {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL]->get('id'))}
						{if $TAB_INFO['num_fields'] eq 0}
                            {continue}
                        {/if}
                        {if $TAB_INFO['parent_tab'] neq "" && $TAB_INFO['parent_tab'] neq null}
                            {$TAB_CHILD_ARRAYS_LABEL[] = $BLOCK_LABEL}
                            {$TAB_CHILD_ARRAYS_BLOCK_FIELDS[$BLOCK_LABEL] = $TAB_ARRAYS_BLOCK_FIELDS[$BLOCK_LABEL]}
                            {continue}
                        {/if}
                        {$LABEL_ARRAYS_TAB_WITH_ORDER[$BLOCK_LIST[$BLOCK_LABEL]->get('id')] = $BLOCK_LABEL}
                    {/foreach}
                    {if $COMBINE_TAB_LIST|@count gt 0}
                        {foreach key=TAB_KEY item=TAB_NAME from=$COMBINE_TAB_LIST}
                            {$LABEL_ARRAYS_TAB_WITH_ORDER[$TAB_KEY] = $TAB_NAME}
                        {/foreach}
                    {/if}
                    {assign var=TAB_LIST_WITH_ORDER value=VTETabs_Module_Model::getTabsLabelWithOrder($MODULE)}
                    {if $LABEL_ARRAYS_TAB_WITH_ORDER|@count gt 0}
                        {foreach key=TAB_KEY item=TAB_NAME_DB from=$TAB_LIST_WITH_ORDER}
                            {foreach key=LABEL_KEY item=TAB_LABEL from=$LABEL_ARRAYS_TAB_WITH_ORDER}
                                {if vtranslate({$TAB_LABEL},{$MODULE}) eq vtranslate({$TAB_NAME_DB},{$MODULE})}
                                    <li class="tab-item">
                                        <a class="tablinks textOverflowEllipsis" href="#block-{$LABEL_KEY}" >
                                            <span class="tab-label"><strong>{vtranslate({$TAB_LABEL},{$MODULE})}</strong></span>
                                        </a>
                                    </li>
                                {/if}
                            {/foreach}
                        {/foreach}
                    {/if}
                </ul>
            </div>
            <br />
            <div class="tab-content">
                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$TAB_ARRAYS_BLOCK_FIELDS}
                    {*print all single tab block*}
                    {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL]->get('id'))}
                    {if $BLOCK_FIELDS|@count gt 0 && $TAB_INFO['parent_tab'] eq "" && $TAB_INFO['parent_tab'] eq null}
                        {assign var = BLABEL value = $BLOCK_LABEL|replace:' ':'-'}
                        <div class='fieldBlockContainer tab' id="block-{$BLOCK_LIST[$BLOCK_LABEL]->get('id')}">
                            {include file="ChildEditViewContentsInTab.tpl"|vtemplate_path:"VTETabs" BLOCK_FIELDS = $BLOCK_FIELDS RECORD_STRUCTURE =$RECORD_STRUCTURE BLOCK_LABEL = $BLOCK_LABEL}
                        </div>
                    {/if}
                    {*End print all single tab block*}
                {/foreach}
                {*print all child block for this tab*}
                {foreach key=TAB_KEY item=TAB_NAME from=$COMBINE_TAB_LIST}
                    <div class='fieldBlockContainer tab' id="block-{$TAB_KEY}">
                        {foreach key=BLOCK_LABEL2 item=BLOCK_FIELDS2 from=$TAB_CHILD_ARRAYS_BLOCK_FIELDS name=blockIterator}
                            {assign var=CHILD_TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL2]->get('id'))}
                            {if $BLOCK_FIELDS2|@count gt 0 &&  $CHILD_TAB_INFO['parent_tab'] eq {$TAB_NAME}}
                                {include file="ChildEditViewContentsInTab.tpl"|vtemplate_path:"VTETabs" BLOCK_FIELDS = $BLOCK_FIELDS2 RECORD_STRUCTURE =$RECORD_STRUCTURE BLOCK_LABEL = $BLOCK_LABEL2}
                            {/if}
                        {/foreach}
                    </div>
                {/foreach}
            </div>
            {*End tab block*}
            {if $IS_IMG_TAB}
                <div class='fieldBlockContainer'>
                    <h4 class='fieldBlockHeader'>{vtranslate($IMG_BLOCK_LABEL, $MODULE)}</h4>
                    <hr>
                    <table class="table table-borderless">
                        <tr>
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$IMG_ARRAYS_BLOCK_FIELD name=blockfields}
                                {assign var="IMG_FIELD_NAME" value=$FIELD_NAME}
                                {assign var="IMG_FIELD_MODEL" value=$FIELD_MODEL}

                            {/foreach}
                            <td class="fieldLabel alignMiddle">
                                {vtranslate($IMG_FIELD_MODEL->get('label'), $MODULE)}
                            </td>
                            <td class="fieldValue">
                                {include file=vtemplate_path($IMG_FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                            </td>
                        </tr>
                    </table>
                </div>
            {/if}
        </div>
    {/if}
{/strip}
