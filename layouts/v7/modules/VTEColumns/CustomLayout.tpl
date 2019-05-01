{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}

{strip}
    {assign var=X_WIDTH value=200}
    {assign var=POPUP_WIDTH value=$X_WIDTH*{$NUM_OF_COLUMNS}}
    <div id="comnineTabContainer" class="modal-dialog" style='min-width:{$POPUP_WIDTH}px;'>
        <div class='modal-content'>
            {assign var=HEADER_TITLE value={vtranslate('Custom Layout', $MODULE)}}
            {assign var=COL_WIDTH value=100/{$NUM_OF_COLUMNS}}
            {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
            <form class="form-horizontal contentsBackground" id="saveLayout" method="post" action="index.php">
                <input type="hidden" name="module" value="VTEColumns" />
                <input type="hidden" name="action" value="ActionAjax" />
                <input type="hidden" name="mode" value="combineTab" />
                <input type="hidden" name="block_id" id="block_id" value="{$BLOCKID}" />
                <div class="modal-body blockSortable" style="display: flex;flex-wrap: wrap;">
                    {foreach key=FIELD_LABEL item=BLOCK_FIELDS from=$FIELDS_LIST name=blockIterator}
                        <ul class="opacity editFields marginLeftZero ui-sortable" style="width: {$COL_WIDTH}%;" data-field-id="{$BLOCK_FIELDS ->get('id')}" data-sequence="{$BLOCK_FIELDS ->get('sequence')}" data-field-name="{$BLOCK_FIELDS ->get('name')}">
                            <li class="row ui-sortable-handle" style="list-style-type: none;">
                                <span class="col-sm-1" style="width: 20%;padding-left: 0px;">&nbsp;
                                    <img src="layouts/v7/skins/images/drag.png" class="cursorPointerMove" border="0" title="Drag">
                                </span>
                                <div class="col-sm-9" style="word-wrap: break-word;">
                                    <div class="fieldLabelContainer row">
                                        <span class="fieldLabel"><b>{vtranslate($BLOCK_FIELDS ->get('label'),$SELECTED_MODULE)}</b>&nbsp;</span><br>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    {/foreach}
                </div>
                <div class="modal-footer">
                    <div class="pull-right cancelLinkContainer" style="margin-top:8px;">
                        <a class="cancelLink" id="cancelLink" type="reset" data-dismiss="modal">{vtranslate("Cancel",$MODULE)}</a>
                    </div>
                    <button class="btn btn-success" type="button" id="btnSaveCombine"><strong>{vtranslate("Save Layout",$MODULE)}</strong></button>
                </div>
            </form>
        </div>
    </div>
{/strip}