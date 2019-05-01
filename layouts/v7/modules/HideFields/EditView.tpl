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
        margin-left: -8px;
    }
    .d_symbol{
        width: 330px;
    }
</style>
{strip}
    <div id="massEditContainer" class="modal-dialog modal-lg modelContainer">
        <div id="massEdit" class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
                <h4>{if $RECORD}{vtranslate('LBL_EDIT')}{else}{vtranslate('LBL_ADD')}{/if} {vtranslate('HideFields', 'HideFields')}</h4>
            </div>
            <form class="form-horizontal" action="index.php" id="HideFieldss_form">
                <input type="hidden" name="record" id="record" value="{$RECORD}" />
                <div name='massEditContent' class="row-fluid">
                    <div class="modal-body">
                        <table class="massEditTable table no-border">
                            <tbody>
                            <tr>
                                <td class="fieldLabel col-lg-2"><label class="muted pull-right">{vtranslate('LBL_MODULE', 'HideFields')}&nbsp;</label></td>
                                <td class="fieldValue col-lg-4">
                                    <select class="select2 span5" id="hfModuleSelect" name="select_module" data-validation-engine='validate[required]]'>
                                        {foreach item=MODULE from=$SUPPORTED_MODULES name=moduleIterator}
                                            <option value="{$MODULE}" {if $MODULE eq $BLOCK_DATA['module']}selected{/if}>
                                                {vtranslate($MODULE, $MODULE)}</option>
                                        {/foreach}
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel col-lg-2"><label class="muted pull-right">Fields <span class="redColor">*</span>&nbsp;</label></td>
                                <td class="fieldValue col-lg-4">
                                    <div id="fields" class="control-group">
                                        {include file='Fields.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel col-lg-2"><label class="muted pull-right">{vtranslate('LBL_SYMBOL', 'HideFields')}&nbsp;</label></td>
                                <td class="fieldValue col-lg-4">
                                    <span id="s_symbol">
                                      {if $RECORD}
                                          {$BLOCK_DATA['symbol']}
                                      {else}
                                          <input type="text" class="symbol" name="symbol[]" placeholder="[_X_]" maxlength="6" value=""/>
                                      {/if}
                                    </span>
                                    <span class="fieldLabel"><label class="muted">&nbsp;&nbsp;{vtranslate('LBL_NOTES', 'HideFields')}</label></span>
                                    <button class="btn btn-success add_more" type="button" id="add_more">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td class="fieldLabel col-lg-2"><label class="muted pull-right">{vtranslate('LBL_STATUS', 'HideFields')}&nbsp;</label></td>
                                <td class="fieldValue col-lg-4">
                                    <select class="select2 span5" name="status">
                                        <option value="1" {if $BLOCK_DATA['status'] eq '1'}selected{/if}>{vtranslate('LBL_ACTIVE', 'HideFields')}</option>
                                        <option value="0" {if $BLOCK_DATA['status'] eq '0'}selected{/if}>{vtranslate('LBL_INACTIVE', 'HideFields')}</option>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="button" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                    <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </div>
            </form>
        </div>
    </div>
{/strip}