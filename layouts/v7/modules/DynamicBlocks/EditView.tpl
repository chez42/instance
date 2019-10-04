{*/* ********************************************************************************
* The content of this file is subject to the Dynamic Blocks ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="widget_header clearfix">
        <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row" id="dynamicblocks_form">
        <form class="form-horizontal col-lg-12" action="index.php">
            <input type="hidden" name="module" value="{$QUALIFIED_MODULE}"/>
            <input type="hidden" name="action" value="Save"/>
            <input type="hidden" name="record" value="{$BLOCK_DATA['id']}"/>
            <input type="hidden" id="arrModule" value="{$ARR_MODULE}"/>
            <input type="hidden" name="selectedModule" value="{$SELECTED_MODULE_NAME}"/>
            <div class="form-group">
                <label class="control-label col-sm-2">{vtranslate('LBL_MODULE',$QUALIFIED_MODULE)}</label>
                <div class="col-sm-10">
                    <select class="select2 col-sm-6" id="modulesList" name="source_module">
                        {foreach key = MODULE_NAME item=$MODULE_LABEL from=$SUPPORTED_MODULES}
                            <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE_NAME} selected {/if}>{vtranslate($MODULE_NAME, $MODULE_NAME)}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div id="field_blocks" class="control-group">
                {include file='FieldAndBlocks.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
            </div>
            <div style="padding-left: 10px;">
                <button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
            </div>
            {*<div class="modal-overlay-footer clearfix">*}
                {*<div class="row clearfix">*}
                    {*<div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">*}
                        {*<button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>*}
                    {*</div>*}
                {*</div>*}
            {*</div>*}
        </form>
    </div>
</div>