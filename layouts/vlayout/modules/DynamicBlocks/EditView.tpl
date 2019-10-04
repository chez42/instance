{*/* ********************************************************************************
* The content of this file is subject to the Dynamic Blocks ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
    </div>
    <hr>
    <br />
    <div class="row-fluid" id="dynamicblocks_form">
        <form class="form-horizontal" action="index.php">
            <input type="hidden" name="module" value="{$QUALIFIED_MODULE}"/>
            <input type="hidden" name="action" value="Save"/>
            <input type="hidden" name="record" value="{$BLOCK_DATA['id']}"/>
            <input type="hidden" id="arrModule" value="{$ARR_MODULE}"/>
            <input type="hidden" name="selectedModule" value="{$SELECTED_MODULE_NAME}"/>
            <div class="control-group">
                <div class="span6">
                    <label class="muted control-label">
                        &nbsp;<strong>{vtranslate('LBL_MODULE',$QUALIFIED_MODULE)}</strong>
                    </label>
                    <div class="controls row-fluid">
                        <select class="select2 span6" id="modulesList" name="source_module">
                            {foreach item=MODULE_NAME from=$SUPPORTED_MODULES}
                                <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE_NAME} selected {/if}>{vtranslate($MODULE_NAME, $MODULE_NAME)}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
            </div>
            <div id="field_blocks" class="control-group">
                {include file='FieldAndBlocks.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
            </div>
            <div style="padding-left: 10px;">
                <button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
            </div>
        </form>
    </div>
</div>