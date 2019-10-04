{*/* ********************************************************************************
* The content of this file is subject to the Dynamic Blocks ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="form-group">
    <label class="control-label col-sm-2">{vtranslate('LBL_SOURCE_FIELD',$QUALIFIED_MODULE)}</label>
    <div class="col-sm-10">
        <select class="select2 col-sm-6" id="modulePickList" name="fieldname">
            {foreach key=PICKLIST_FIELD item=FIELD_MODEL from=$PICKLIST_FIELDS}
                <option {if $FIELD_MODEL->getId() eq $BLOCK_DATA['fieldname']} selected="" {/if} value="{$FIELD_MODEL->getId()}">{vtranslate($FIELD_MODEL->get('label'),$SELECTED_MODULE_NAME)}</option>
            {/foreach}
        </select>
    </div>
</div>
<div class="form-group" id="picklistValues">
    {include file='PicklistValues.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
</div>
<div class="row">
    <label class="col-sm-3">{vtranslate('LBL_SELECT_BLOCKS',$QUALIFIED_MODULE)}</label>
</div>
<div id="blocks" class="layoutContent" style="padding-left: 10px; padding-right: 40%;border-top: 4px solid #1560bd;">
    <table class="table table-bordered">
        <thead>
        <tr class="listViewHeaders" style="background: #dddddd">
            <th>{vtranslate('LBL_BLOCKS', $QUALIFIED_MODULE)}</th>
            <th>{vtranslate('LBL_HIDE', $QUALIFIED_MODULE)}</th>
            <th>{vtranslate('LBL_SHOW', $QUALIFIED_MODULE)}</th>
            <th>{vtranslate('LEAVE_AS_IS', $QUALIFIED_MODULE)}</th>
        </tr>
        </thead>
        <tbody>
        {foreach from=$BLOCKS key=BLOCK_LABEL item=BLOCK}
            {if $BLOCK_LABEL neq ''}
                {assign var=BLOCK_DATA_DETAIL value=$BLOCK_DATA['blocks'][$BLOCK->get('id')]}
                <tr>
                    <td>
                        <input type="hidden" name="blocks[{$BLOCK->get('id')}][label]" value="{$BLOCK_LABEL}"/>
                        {vtranslate($BLOCK_LABEL,$SELECTED_MODULE_NAME)}</td>
                    <td><input type="radio" name="blocks[{$BLOCK->get('id')}][show]" {if $BLOCK_DATA_DETAIL['show'] eq '0'}checked="" {/if} value="0"/></td>
                    <td><input type="radio" name="blocks[{$BLOCK->get('id')}][show]" {if $BLOCK_DATA_DETAIL['show'] eq '1'}checked="" {/if} value="1"/></td>
                    <td><input type="radio" name="blocks[{$BLOCK->get('id')}][show]" {if $BLOCK_DATA_DETAIL['show'] eq '2'}checked=""{elseif $BLOCK_DATA_DETAIL['show'] eq ''}checked="" {/if} value="2"/></td>
                </tr>
            {/if}
        {/foreach}
        {foreach from=$RBL_BLOCKS key=BLOCK_LABEL item=BLOCKID}
            {if $BLOCK_LABEL neq ''}
                {assign var=BLOCK_DATA_DETAIL value=$BLOCK_DATA['blocks'][$BLOCKID]}
                <tr>
                    <td>
                        <input type="hidden" name="blocks[{$BLOCKID}][label]" value="{$BLOCK_LABEL}"/>
                        {vtranslate($BLOCK_LABEL,$SELECTED_MODULE_NAME)}</td>
                    <td><input type="radio" name="blocks[{$BLOCKID}][show]" {if $BLOCK_DATA_DETAIL['show'] eq '0'}checked="" {/if} value="0"/></td>
                    <td><input type="radio" name="blocks[{$BLOCKID}][show]" {if $BLOCK_DATA_DETAIL['show'] eq '1'}checked="" {/if} value="1"/></td>
                    <td><input type="radio" name="blocks[{$BLOCKID}][show]" {if $BLOCK_DATA_DETAIL['show'] eq '2'}checked="" {/if} value="2"/></td>
                </tr>
            {/if}
        {/foreach}
        </tbody>
    </table>
    <br/>
</div>