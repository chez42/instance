{*<!--
/* ********************************************************************************
 * The content of this file is subject to the Masked Input ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
<div id="massEditContainer"  class='modal-dialog modal-lg' style="width: 600px;">
    <div id="massEdit" class="modal-content">
        <div class="modal-header contentsBackground">
            <div class="clearfix">
                <div class="pull-right "><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
                </div><h4 class="pull-left" id="massEditHeader">{vtranslate('LBL_CONFIGURED', 'MaskedInput')} {vtranslate('MaskedInput', 'MaskedInput')} {vtranslate('LBL_FIELD', 'MaskedInput')}</h4>
            </div>
        </div>
        <form class="form-horizontal" id="editForm">
            <input type="hidden" name="record" value="{$RECORD}" />
            <input type="hidden" name="mode" value="saveConfiguredField" />

            <div name='massEditContent' class="row-fluid">
                <div class="modal-body">
                    <table class="massEditTable table no-border">
                        <tr>
                            <td class="fieldLabel col-lg-2">
                                <label class="muted pull-right">
                                    &nbsp;{vtranslate('LBL_MODULE', 'MaskedInput')}
                                </label>
                            </td>
                            <td class="fieldValue col-lg-3">
                                <select class="select2 col-lg-6" name="select_module" data-validation-engine='validate[required]]' style="width: 100%">
                                    {*<option value="">Select a module</option>*}
                                    {foreach item=MODULE from=$LIST_MODULE name=moduleIterator}
                                        <option value="{$MODULE}" {if $MODULE eq $CONFIGURED_FIELD['module']}selected{/if}>
                                            {vtranslate($MODULE, $MODULE)}</option>
                                    {/foreach}
                                </select>
                            </td>

                            <td class="fieldValue col-lg-2"></td>
                        </tr>
                        <tr id="fields">
                            {include file='Fields.tpl'|@vtemplate_path:'MaskedInput'}

                        </tr>
                        <tr>
                            <td class="fieldLabel col-lg-2">
                                <label class="muted pull-right">
                                    &nbsp;{vtranslate('MaskedInput', 'MaskedInput')}
                                </label>
                            </td>
                            <td class="fieldValue col-lg-3">
                                <select class="select2 col-lg-6" name="masked_input" data-validation-engine='validate[required]]' style="width: 100%">
                                    <option value="">{vtranslate('LBL_SELECT_OPTION', 'MaskedInput')}</option>
                                    {foreach item=MASKED_INPUT key=MASKED_INPUT_ID from=$MASKED_INPUTS name=moduleIterator}
                                        <option value="{$MASKED_INPUT_ID}" {if $MASKED_INPUT_ID eq $CONFIGURED_FIELD['masked_input']}selected{/if}>
                                            {$MASKED_INPUT}
                                        </option>
                                    {/foreach}
                                </select>
                            </td>

                            <td class="fieldValue col-lg-2"></td>
                        </tr>
                        <tr>
                            <td class="fieldLabel col-lg-2">
                                <label class="muted pull-right">
                                    &nbsp;{vtranslate('LBL_ACTIVE', 'MaskedInput')}
                                </label>
                            </td>
                            <td class="fieldValue col-lg-3">
                                <select class="select2 col-lg-6" name="active"  style="width: 100%">
                                    <option value="1" {if $CONFIGURED_FIELD['active'] eq '1'}selected{/if}>{vtranslate('LBL_YES')}</option>
                                    <option value="0" {if $CONFIGURED_FIELD['active'] eq '0'}selected{/if}>{vtranslate('LBL_NO')}</option>
                                </select>
                            </td>

                            <td class="fieldValue col-lg-2"></td>
                        </tr>


                    </table>


                </div>
            </div>
            <div class="modal-footer">
                <center>
                    <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                    <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                </center>
            </div>
        </form>
    </div>
</div>
{/strip}