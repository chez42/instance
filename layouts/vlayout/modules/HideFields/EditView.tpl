{*/* ********************************************************************************
* The content of this file is subject to the Hide Fields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
<style>
    #add_more{
        display: inline;
        float: right;
        margin-right: 34px;
        margin-top: 7px;
    }
    .symbol{
        width: 30px;
        display: inline;
        text-align: center;
    }
    .alignTop1{
        margin-left: -10px;
        padding-top: 10px;
    }
    .d_symbol{
        width: 330px;
    }
</style>
{strip}
    <div id="massEditContainer" class='modelContainer'>
        <div id="massEdit">
            <div class="modal-header contentsBackground">
                <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 id="massEditHeader">{if $RECORD}{vtranslate('LBL_EDIT')}{else}{vtranslate('LBL_ADD')}{/if} {vtranslate('HideFields', 'HideFields')}</h3>
            </div>
            <form class="form-horizontal" action="index.php" id="HideFieldss_form">
                <input type="hidden" name="record" id="record" value="{$RECORD}" />
                <div name='massEditContent' class="row-fluid">
                    <div class="modal-body">
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_MODULE', 'HideFields')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <select class="select2 span5" id="hfModuleSelect" name="select_module" data-validation-engine='validate[required]]'>
                                    {foreach item=MODULE from=$SUPPORTED_MODULES name=moduleIterator}
                                        <option value="{$MODULE}" {if $MODULE eq $BLOCK_DATA['module']}selected{/if}>
                                            {vtranslate($MODULE, $MODULE)}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div id="fields" class="control-group">
                            {include file='Fields.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_SYMBOL', 'HideFields')}</strong>
                            </label>
                            <button class="btn-success add_more" type="button" id="add_more">
                                <i class="icon-plus"></i>
                            </button>
                            <div class="controls row-fluid d_symbol">
                                <span id="s_symbol">
                                  {if $RECORD}
                                      {$BLOCK_DATA['symbol']}
                                  {else}
                                    <input type="text" class="symbol" name="symbol[]" placeholder="[_X_]" maxlength="6" value=""/>
                                  {/if}
                                </span>
                                <label>{vtranslate('LBL_NOTES', 'HideFields')}</label>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_STATUS', 'HideFields')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <select class="select2 span5" name="status">
                                    <option value="1" {if $BLOCK_DATA['status'] eq '1'}selected{/if}>{vtranslate('LBL_ACTIVE', 'HideFields')}</option>
                                    <option value="0" {if $BLOCK_DATA['status'] eq '0'}selected{/if}>{vtranslate('LBL_INACTIVE', 'HideFields')}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                </div>
            </form>
        </div>
    </div>
{/strip}