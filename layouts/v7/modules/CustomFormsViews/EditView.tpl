{*/* ********************************************************************************
* The content of this file is subject to the Custom Forms & Views ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
<div class="container-fluid" id="customFromView">
    <form class="form-horizontal" action="index.php">
        <div class="widget_header row">
            <div class="col-md-8">
                <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
            </div>
            <div class="col-md-4">
                <div class="pull-right">
                    <button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
                </div>
            </div>
        </div>
        <hr>
        <br />
        <div class="row">
            <input type="hidden" name="module" value="{$QUALIFIED_MODULE}"/>
            <input type="hidden" name="action" value="Save"/>
            <input type="hidden" name="record" value="{$RECORD}"/>
            <div class="row">
                <div class="col-md-6">
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-md-4" >
                            <label class="text-muted control-label" style="float: right;">
                                &nbsp;<strong>{vtranslate('LBL_MODULE',$QUALIFIED_MODULE)}</strong>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <select class="select2" id="modulesList" name="source_module" style="width: 60%;">
                                {foreach item=MODULE_NAME from=$SUPPORTED_MODULES}
                                    <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE_NAME} selected {/if}>{vtranslate($MODULE_NAME, $MODULE_NAME)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-md-4">
                            <label class="text-muted control-label" style="float: right;">
                                &nbsp;<strong>{vtranslate('LBL_NAME',$QUALIFIED_MODULE)}</strong>
                            </label>
                        </div>
                        <div class="col-md-8">
                                <input type="text" name="custom_name" value="{$CUSTOM_DATA['custom_name']}" style="width: 60%; padding: 4px;color: #808080;font-size: 13px; border: 1px solid #cccccc;"/>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-md-4">
                            <label class="text-muted control-label" style="float: right;">
                                &nbsp;<strong>{vtranslate('LBL_STATUS',$QUALIFIED_MODULE)}</strong>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <select class="select2" id="status" name="status" style="width: 60%;">
                                <option value="Active" {if $CUSTOM_DATA['status'] eq 'Active'} selected="" {/if}>{vtranslate('LBL_ACTIVE',$QUALIFIED_MODULE)}</option>
                                <option value="Inactive" {if $CUSTOM_DATA['status'] eq 'Inactive'} selected="" {/if}>{vtranslate('LBL_INACTIVE',$QUALIFIED_MODULE)}</option>
                            </select>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 10px">
                        <div class="col-md-4">
                            <label class="text-muted control-label" style="float: right;">
                                &nbsp;<strong>{vtranslate('LBL_PROFILES',$QUALIFIED_MODULE)}</strong>
                            </label>
                        </div>
                        <div class="col-md-8">
                            <select class="select2 col-md-8" id="selected_profiles" multiple="true" name="profiles[]" style="width: 80%; ">
                                {foreach from=$PROFILES key=PROFILE_NAME item=PROFILE_DATA}
                                    <option {if in_array($PROFILE_DATA['id'], $CUSTOM_DATA['profiles'])} selected {/if} value="{$PROFILE_DATA['id']}" data-id={$PROFILE_DATA['id']}>{vtranslate($PROFILE_DATA['name'],$SELECTED_MODULE_NAME)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div style="
    margin-bottom: 5px;
    padding-right: 10%;
">
                        <span style="line-height: 18px;">{vtranslate('LBL_EDITOR_TEXT',$QUALIFIED_MODULE)}</span>
                    </div>
                    <div style="
    padding-right: 10%;
">
                        <span style="line-height: 18px;">{vtranslate('LBL_FIELDS_AND_LAYOUT_EDITOR1',$QUALIFIED_MODULE)} {$LAYOUT_EDITOR} {vtranslate('LBL_FIELDS_AND_LAYOUT_EDITOR2',$QUALIFIED_MODULE)}</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div id="field_blocks">
                    {include file='Blocks.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
                </div>
                <div class="col-sm-12" style="margin-bottom: 20px" >
                    <button class="btn btn-success" type="submit" style="margin-left: 25px">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
                </div>
            </div>
        </div>
    </form>
</div>