{*/* ********************************************************************************
* The content of this file is subject to the Custom Forms & Views ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="contents">
    <div class="tab-content layoutContent padding20 themeTableColor overflowVisible">
        <div id="moduleBlocks">
            {foreach from=$BLOCKS key=BLOCK_LABEL_KEY item=BLOCK_MODEL}
                {assign var=BLOCK_ID value=$BLOCK_MODEL->get('id')}
                {assign var=FIELDS_LIST value=$BLOCK_MODEL->getFields()}
                <div id="block_{$BLOCK_ID}" class="editFieldsTable block_{$BLOCK_ID} marginBottom10px border1px blockSortable" data-block-id="{$BLOCK_ID}" data-sequence="{$BLOCK_MODEL->get('sequence')}" data-visible="{if $BLOCK_MODEL->get('visible') eq ''}1{else}{$BLOCK_MODEL->get('visible')}{/if}" style="border-radius: 4px 4px 0px 0px;background: white;">
                    <div class="row-fluid layoutBlockHeader">
                        <div class="blockLabel span5 padding10 marginLeftZero">
                            <img class="alignMiddle" src="{vimage_path('drag.png')}" />&nbsp;&nbsp;
                            <strong>{vtranslate($BLOCK_LABEL_KEY, $SELECTED_MODULE_NAME)}</strong>
                        </div>
                        <div class="span6 marginLeftZero" style="float:right !important;">
                            <div class="pull-right btn-toolbar blockActions" style="margin: 4px;">
                                <div class="btn-group">
                                    <button class="btn dropdown-toggle" data-toggle="dropdown">
                                        <strong>{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}</strong>&nbsp;&nbsp;
                                        <i class="caret"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right">
                                        <li class="blockVisibility" data-visible="{$BLOCK_MODEL->get('visible')}" data-block-id="{$BLOCK_MODEL->get('id')}">
                                            <a href="javascript:void(0)">
                                                <i class="icon-ok {if $BLOCK_MODEL->get('visible') eq '' || $BLOCK_MODEL->get('visible') eq '1'}{else} hide {/if}"></i>&nbsp;
                                                {vtranslate('LBL_ALWAYS_SHOW', $QUALIFIED_MODULE)}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="blockFieldsList blockFieldsSortable row-fluid" style="padding:5px;min-height: 27px">
                        <ul name="sortable1" class="connectedSortable span6" style="list-style-type: none; float: left;min-height: 1px;padding:2px;">
                            {if $FIELDS_LIST|is_array}
                                {foreach item=FIELD_MODEL from=$FIELDS_LIST name=fieldlist}
                                    {if $smarty.foreach.fieldlist.index % 2 eq 0}
                                        {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                        <li>
                                            <div class="opacity editFields marginLeftZero border1px" data-block-id="{$FIELD_MODEL->get('blockid')}" data-field-id="{$FIELD_MODEL->get('id')}" data-sequence="{$FIELD_MODEL->get('sequence')}">
                                                <div class="row-fluid padding1per">
                                                    <span class="span1">&nbsp;
                                                        <a>
                                                            <img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                        </a>
                                                    </span>
                                                    <div class="span11 marginLeftZero" style="word-wrap: break-word;">
                                                        <span class="fieldLabel">{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}&nbsp;{if $IS_MANDATORY}<span class="redColor">*</span>{/if}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    {/if}
                                {/foreach}
                            {/if}
                        </ul>
                        <ul name="sortable2" class="connectedSortable span6" style="list-style-type: none; margin: 0; float: left;min-height: 1px;padding:2px;">
                            {if $FIELDS_LIST|is_array}
                                {foreach item=FIELD_MODEL from=$FIELDS_LIST name=fieldlist}
                                    {if $smarty.foreach.fieldlist.index % 2 neq 0}
                                        {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                        <li>
                                            <div class="opacity editFields marginLeftZero border1px" data-block-id="{$FIELD_MODEL->get('blockid')}" data-field-id="{$FIELD_MODEL->get('id')}" data-sequence="{$FIELD_MODEL->get('sequence')}">
                                                <div class="row-fluid padding1per">
                                                    <span class="span1">&nbsp;
                                                        <a>
                                                            <img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                        </a>
                                                    </span>
                                                    <div class="span11 marginLeftZero" style="word-wrap: break-word;">
                                                        <span class="fieldLabel">{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}&nbsp;{if $IS_MANDATORY}<span class="redColor">*</span>{/if}</span>
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

            <div id="block_available_fields" class="editFieldsTable block_available_fields marginBottom10px border1px" style="border-radius: 4px 4px 0px 0px;background: white;" data-block-id="0">
                <div class="row-fluid layoutBlockHeader">
                    <div class="blockLabel span5 padding10 marginLeftZero" style="padding-bottom:15px;padding-left:20px;">
                        <strong>{vtranslate('LBL_AVAILABLE_FIELDS', $QUALIFIED_MODULE)}</strong>
                    </div>
                    {*<div class="span6 marginLeftZero" style="float:right !important;">
                        <div class="pull-right btn-toolbar blockActions" style="margin: 4px;">
                            <div class="btn-group">
                                <button class="btn dropdown-toggle" data-toggle="dropdown" style="display: none;">
                                    <strong>{vtranslate('LBL_ACTIONS', $QUALIFIED_MODULE)}</strong>&nbsp;&nbsp;
                                    <i class="caret"></i>
                                </button>
                            </div>
                        </div>
                    </div>*}
                </div>
                <div class="blockFieldsList blockFieldsSortable row-fluid" style="padding:5px;min-height: 27px">
                    <ul name="sortable1" class="connectedSortable span6" style="list-style-type: none; float: left;min-height: 1px;padding:2px;">
                        {foreach item=FIELD_MODEL from=$AVAILABLE_FIELDS name=fieldlist}
                            {if $smarty.foreach.fieldlist.index % 2 eq 0}
                                {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                <li>
                                    <div class="opacity editFields marginLeftZero border1px" data-block-id="0" data-field-id="{$FIELD_MODEL->get('id')}" data-sequence="{$FIELD_MODEL->get('sequence')}">
                                        <div class="row-fluid padding1per">
                                            <span class="span1">&nbsp;
                                                <a>
                                                    <img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                </a>
                                            </span>
                                            <div class="span11 marginLeftZero" style="word-wrap: break-word;">
                                                <span class="fieldLabel">{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}&nbsp;{if $IS_MANDATORY}<span class="redColor">*</span>{/if}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            {/if}
                        {/foreach}
                    </ul>
                    <ul name="sortable2" class="connectedSortable span6" style="list-style-type: none; margin: 0; float: left;min-height: 1px;padding:2px;">
                        {foreach item=FIELD_MODEL from=$AVAILABLE_FIELDS name=fieldlist}
                            {if $smarty.foreach.fieldlist.index % 2 neq 0}
                                {assign var=IS_MANDATORY value=$FIELD_MODEL->isMandatory()}
                                <li>
                                    <div class="opacity editFields marginLeftZero border1px" data-block-id="0" data-field-id="{$FIELD_MODEL->get('id')}" data-sequence="{$FIELD_MODEL->get('sequence')}">
                                        <div class="row-fluid padding1per">
                                            <span class="span1">&nbsp;
                                                <a>
                                                    <img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$QUALIFIED_MODULE)}"/>
                                                </a>
                                            </span>
                                            <div class="span11 marginLeftZero" style="word-wrap: break-word;">
                                                <span class="fieldLabel">{vtranslate($FIELD_MODEL->get('label'), $SELECTED_MODULE_NAME)}&nbsp;{if $IS_MANDATORY}<span class="redColor">*</span>{/if}</span>
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