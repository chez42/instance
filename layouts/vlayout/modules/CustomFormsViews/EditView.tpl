{*/* ********************************************************************************
* The content of this file is subject to the Custom Forms & Views ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid" id="customFromView">
    <form class="form-horizontal" action="index.php">
        <div class="widget_header row-fluid">
            <div class="span8">
                <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
            </div>
            <div class="span4">
                <div class="pull-right">
                    <button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
                </div>
            </div>
        </div>
        <hr>
        <br />
        <div class="row-fluid">
            <input type="hidden" name="module" value="{$QUALIFIED_MODULE}"/>
            <input type="hidden" name="action" value="Save"/>
            <input type="hidden" name="record" value="{$RECORD}"/>
            <div class="row-fluid">
                <div class="span6">
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
                    <div class="control-group">
                        <div class="span6">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_NAME',$QUALIFIED_MODULE)}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="text" name="custom_name" value="{$CUSTOM_DATA['custom_name']}"/>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="span6">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_STATUS',$QUALIFIED_MODULE)}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <select class="select2 span6" id="status" name="status">
                                    <option value="Active" {if $CUSTOM_DATA['status'] eq 'Active'} selected="" {/if}>{vtranslate('LBL_ACTIVE',$QUALIFIED_MODULE)}</option>
                                    <option value="Inactive" {if $CUSTOM_DATA['status'] eq 'Inactive'} selected="" {/if}>{vtranslate('LBL_INACTIVE',$QUALIFIED_MODULE)}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="span6">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_PROFILES',$QUALIFIED_MODULE)}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <select class="select2 span8" id="selected_profiles" multiple="true" name="profiles[]">
                                    {foreach from=$PROFILES key=PROFILE_NAME item=PROFILE_DATA}
                                           <option {if in_array($PROFILE_DATA['id'], $CUSTOM_DATA['profiles'])} selected {/if} value="{$PROFILE_DATA['id']}" data-id={$PROFILE_DATA['id']}>{vtranslate($PROFILE_DATA['name'],$SELECTED_MODULE_NAME)}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="span6">
                    <div>
                        <span>{vtranslate('LBL_EDITOR_TEXT',$QUALIFIED_MODULE)}</span>
                    </div>
                    <div>
                        <span>{vtranslate('LBL_FIELDS_AND_LAYOUT_EDITOR1',$QUALIFIED_MODULE)} {$LAYOUT_EDITOR} {vtranslate('LBL_FIELDS_AND_LAYOUT_EDITOR2',$QUALIFIED_MODULE)}</span>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div id="field_blocks">
                    {include file='Blocks.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
                </div>
                <div style="padding-left: 20px;margin-top:-20px;padding-bottom: 20px;">
                    <button class="btn btn-success" type="submit">{vtranslate('LBL_SAVE', $QUALIFIED_MODULE)}</button>
                </div>
            </div>
        </div>
    </form>
</div>