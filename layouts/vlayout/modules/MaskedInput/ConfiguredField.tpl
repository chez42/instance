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
<div id="massEditContainer" class='modelContainer'>
    <div id="massEdit">
        <div class="modal-header contentsBackground">
            <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 id="massEditHeader">{vtranslate('LBL_CONFIGURED', 'MaskedInput')} {vtranslate('MaskedInput', 'MaskedInput')} {vtranslate('LBL_FIELD', 'MaskedInput')}</h3>
        </div>
        <form class="form-horizontal" id="editForm">
            <input type="hidden" name="record" value="{$RECORD}" />
            <input type="hidden" name="mode" value="saveConfiguredField" />

            <div name='massEditContent' class="row-fluid">
                <div class="modal-body">
                    <div class="control-group">
                        <label class="muted control-label">
                            &nbsp;{vtranslate('LBL_MODULE', 'MaskedInput')}
                        </label>
                        <div class="controls row-fluid">
                            <select class="select2 span6" name="select_module" data-validation-engine='validate[required]]'>
                                {*<option value="">Select a module</option>*}
                                {foreach item=MODULE from=$LIST_MODULE name=moduleIterator}
                                    <option value="{$MODULE}" {if $MODULE eq $CONFIGURED_FIELD['module']}selected{/if}>
                                        {vtranslate($MODULE, $MODULE)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div id="fields">
                        {include file='Fields.tpl'|@vtemplate_path:'MaskedInput'}
                    </div>
                    <div class="control-group">
                        <label class="muted control-label">
                            &nbsp;{vtranslate('MaskedInput', 'MaskedInput')}
                        </label>
                        <div class="controls row-fluid">
                            <select class="select2 span6" name="masked_input" data-validation-engine='validate[required]]'>
                                <option value="">{vtranslate('LBL_SELECT_OPTION', 'MaskedInput')}</option>
                                {foreach item=MASKED_INPUT key=MASKED_INPUT_ID from=$MASKED_INPUTS name=moduleIterator}
                                    <option value="{$MASKED_INPUT_ID}" {if $MASKED_INPUT_ID eq $CONFIGURED_FIELD['masked_input']}selected{/if}>
                                        {$MASKED_INPUT}
                                    </option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="muted control-label">
                            &nbsp;{vtranslate('LBL_ACTIVE', 'MaskedInput')}
                        </label>
                        <div class="controls row-fluid">
                            <select class="select2 span6" name="active">
                                <option value="1" {if $CONFIGURED_FIELD['active'] eq '1'}selected{/if}>{vtranslate('LBL_YES')}</option>
                                <option value="0" {if $CONFIGURED_FIELD['active'] eq '0'}selected{/if}>{vtranslate('LBL_NO')}</option>
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