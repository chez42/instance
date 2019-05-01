{*/* ********************************************************************************
* The content of this file is subject to the Custom Forms & Views ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="contents">
    <div class="row" style="border-top: 4px solid #2f973f; margin-top: 30px"></div>
    <div class="tab-content layoutContent padding20 themeTableColor overflowVisible">
        <div id="moduleBlocks">
            {foreach from=$BLOCKS key=BLOCK_LABEL_KEY item=BLOCK_MODEL}
                {assign var=BLOCK_ID value=$BLOCK_MODEL->get('id')}
                {assign var=FIELDS_LIST value=$BLOCK_MODEL->getFields()}
                <div id="block_{$BLOCK_ID}"
                     class="editFieldsTable block_{$BLOCK_ID} marginBottom10px border1px blockSortable"
                     data-block-id="{$BLOCK_ID}" data-sequence="{$BLOCK_MODEL->get('sequence')}"
                     data-visible="{if $BLOCK_MODEL->get('visible') eq ''}1{else}{$BLOCK_MODEL->get('visible')}{/if}"
                     style="background: white;">
                    <div class="col-sm-12">
                        <div class="row layoutBlockHeader">
                            <div class="blockLabel col-sm-3 padding10 marginLeftZero" style="word-break: break-all;">
                                <img class="cursorPointerMove" src="{vimage_path('drag.png')}"/>&nbsp;&nbsp;
                                <strong class="translatedBlockLabel">{vtranslate($BLOCK_LABEL_KEY, $SELECTED_MODULE_NAME)}</strong>
                            </div>
                            <div class="col-sm-9 padding10 marginLeftZero">
                                <div class="blockActions" style="float:right !important;">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            <strong>{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}</strong>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"
                                             style="margin: 1px -82px 0;">
                                            <a href="javascript:void(0)" style="margin-left: 10%;" class="blockVisibility">
                                                <i class="glyphicon glyphicon-ok {if $BLOCK_MODEL->get('visible') eq '' || $BLOCK_MODEL->get('visible') eq '1'}{else} hide {/if}"></i>&nbsp;
                                                {vtranslate('LBL_ALWAYS_SHOW', $QUALIFIED_MODULE)}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="blockFieldsList blockFieldsSortable row">
                        <ul name="sortable1" class="connectedSortable col-sm-6">
                            {if $FIELDS_LIST|is_array}
                                {foreach item=FIELD_MODEL from=$FIELDS_LIST name=fieldlist}
                                    {if $smarty.foreach.fieldlist.index % 2 eq 0}
                                        {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                        <li>
                                            <div class="row border1px">
                                                <div class="col-sm-12">
                                                    <div class="opacity editFields marginLeftZero"
                                                         style="min-height: 30px; border: none"
                                                         data-block-id="{$FIELD_MODEL->get('blockid')}"
                                                         data-field-id="{$FIELD_MODEL->get('id')}"
                                                         data-sequence="{$FIELD_MODEL->get('sequence')}">
                                                        <div class="row">
                                                    <span class="col-sm-1" style="margin-top: 5px;">
                                                        <a>
                                                            <img src="{vimage_path('drag.png')}"
                                                                 class="cursorPointerMove" border="0"
                                                                 title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                        </a>
                                                    </span>
                                                            <div class="col-sm-9" style="word-wrap: break-word;">
                                                                <div class="fieldLabelContainer row"
                                                                     style="padding-top: 5px;text-align: left">
                                                                    <span class="fieldLabel">{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}
                                                                        &nbsp;{if $IS_MANDATORY}<span
                                                                                class="redColor">*</span>{/if}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    {/if}
                                {/foreach}
                            {/if}
                        </ul>
                        <ul name="sortable2" class="connectedSortable col-sm-6">
                            {if $FIELDS_LIST|is_array}
                                {foreach item=FIELD_MODEL from=$FIELDS_LIST name=fieldlist}
                                    {if $smarty.foreach.fieldlist.index % 2 neq 0}
                                        {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                        <li>
                                            <div class="row border1px">
                                                <div class="col-sm-12">
                                                    <div class="opacity editFields marginLeftZero"
                                                         style="min-height: 30px; border: none"
                                                         data-block-id="{$FIELD_MODEL->get('blockid')}"
                                                         data-field-id="{$FIELD_MODEL->get('id')}"
                                                         data-sequence="{$FIELD_MODEL->get('sequence')}">
                                                        <div class="row-fluid padding1per">
                                                            <span class="col-sm-1" style="margin-top: 5px;">
                                                                <a>
                                                                    <img src="{vimage_path('drag.png')}" border="0"
                                                                         class="cursorPointerMove"
                                                                         title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                                </a>
                                                            </span>
                                                            <div class="col-sm-9" style="word-wrap: break-word;">
                                                                <div class="fieldLabelContainer row"
                                                                     style="padding-top: 5px;text-align: left">
                                                                            <span class="fieldLabel">
                                                                                <b>{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}</b>
                                                                                &nbsp;{if $IS_MANDATORY}<span
                                                                                    class="redColor">*</span>{/if}
                                                                            </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </li>
                                    {/if}
                                {/foreach}
                            {/if}
                        </ul>
                    </div>
                </div>
            {/foreach}

            <div id="block_available_fields" class="editFieldsTable block_available_fields marginBottom10px border1px"
                 style="background: white;" data-block-id="0">
                <div class="col-sm-12">
                    <div class="row layoutBlockHeader">
                        <div class="blockLabel col-sm-3 padding10 marginLeftZero" style="word-break: break-all;">
                            <strong class="translatedBlockLabel">{vtranslate('LBL_AVAILABLE_FIELDS', $QUALIFIED_MODULE)}</strong>
                        </div>
                    </div>
                </div>
                <div class="blockFieldsList blockFieldsSortable row">
                    <ul name="sortable1" class="connectedSortable col-sm-6">
                        {foreach item=FIELD_MODEL from=$AVAILABLE_FIELDS name=fieldlist}
                            {if $smarty.foreach.fieldlist.index % 2 eq 0}
                                {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                <li>
                                    <div class="row border1px">
                                        <div class="col-sm-12">
                                            <div class="opacity editFields marginLeftZero"
                                                 style="min-height: 30px; border: none" data-block-id="0"
                                                 data-field-id="{$FIELD_MODEL->get('id')}"
                                                 data-sequence="{$FIELD_MODEL->get('sequence')}">
                                                <div class="row">
                                            <span class="col-sm-1" style="margin-top: 5px;">
                                                <a>
                                                    <img src="{vimage_path('drag.png')}" border="0"
                                                         title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                </a>
                                            </span>
                                                    <div class="col-sm-9" style="word-wrap: break-word;">
                                                        <div class="fieldLabelContainer row" style="padding-top: 5px;text-align: left">
                                                            <span class="fieldLabel"><b>{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}</b>&nbsp;{if $IS_MANDATORY}
                                                                <span class="redColor">*</span>{/if}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                    <ul name="sortable2" class="connectedSortable col-sm-6">
                        {foreach item=FIELD_MODEL from=$AVAILABLE_FIELDS name=fieldlist}
                            {if $smarty.foreach.fieldlist.index % 2 neq 0}
                                {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                <li>
                                    <div class="row border1px">
                                        <div class="col-sm-12">
                                            <div class="opacity editFields marginLeftZero"
                                                 style="min-height: 30px; border: none" data-block-id="0"
                                                 data-field-id="{$FIELD_MODEL->get('id')}"
                                                 data-sequence="{$FIELD_MODEL->get('sequence')}">
                                                <div class="row">
                                            <span class="col-sm-1" style="margin-top: 5px;">
                                                <a>
                                                    <img src="{vimage_path('drag.png')}" class="cursorPointerMove"
                                                         border="0" title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                </a>
                                            </span>
                                                    <div class="col-sm-9" style="word-wrap: break-word;">
                                                        <div class="fieldLabelContainer row" style="padding-top: 5px;text-align: left">
                                                            <span class="fieldLabel"><b>{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}</b>&nbsp;{if $IS_MANDATORY}
                                                                <span class="redColor">*</span>{/if}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>