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
                <h3 id="massEditHeader">{if $RECORD}{vtranslate('LBL_EDIT')}{else}{vtranslate('LBL_ADD')}{/if} {vtranslate('LBL_CUSTOM', 'MaskedInput')} {vtranslate('MaskedInput', 'MaskedInput')}</h3>
            </div>
        </div>
        <form class="form-horizontal" id="editForm">
            <input type="hidden" name="record" value="{$RECORD}" />
            <input type="hidden" name="mode" value="saveMaskedInput">
            <div name='massEditContent' class="row-fluid">
                <div class="modal-body">
                    <div class="control-group">
                        <div class="row-fluid" style="margin-left:25px;">
                            <span style="color: #7d7e7d">{vtranslate('LBL_PLEASE_USE_VALUE_BELOW_TO_CREATE', 'MaskedInput')}</span> <br/>
                        </div>
                        <div class="row-fluid" style="margin-left:40px; margin-top: 10px;">
                            <ul style="color: #7d7e7d">
                                <li>{vtranslate('LBL_REPRESENTS_ALPHA', 'MaskedInput')}</li>
                                <li>{vtranslate('LBL_REPRESENTS_NUMERIC', 'MaskedInput')}</li>
                                <li>{vtranslate('LBL_REPRESENTS_ALPHANUMERIC', 'MaskedInput')}</li>
                            </ul>
                        </div>
                        <div class="row-fluid" style="margin-left:25px; margin-top: 10px;">
                            <span style="color: #7d7e7d">{vtranslate('LBL_USE_OTHER_SYMBOLS', 'MaskedInput')}</span> <br/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="muted control-label" style="width: 160px;">
                            <span class="redColor">*</span>&nbsp;{vtranslate('MaskedInput', 'MaskedInput')} {vtranslate('LBL_SYNTAX', 'MaskedInput')}
                        </label>
                        <div class="controls row-fluid" style="margin-left:180px;">
                            <input type="text" value="{$MASKEDINPUT['masked_input']}" name="masked_input"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="muted control-label" style="width: 160px;">
                            &nbsp;{vtranslate('LBL_ALERT', 'MaskedInput')} {vtranslate('LBL_IF_NOT_COMPLETED', 'MaskedInput')}
                        </label>
                        <div class="controls row-fluid" style="margin-left:180px;">
                            <select class="select2 span4" name="alert">
                                <option value="0" {if $MASKEDINPUT['alert'] eq '0'}selected{/if}>{vtranslate('LBL_NO')}</option>
                                <option value="1" {if $MASKEDINPUT['alert'] eq '1'}selected{/if}>{vtranslate('LBL_YES')}</option>

                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="muted control-label" style="width: 160px;">
                            {vtranslate('LBL_ALERT', 'MaskedInput')} {vtranslate('LBL_TEXT', 'MaskedInput')}
                        </label>
                        <div class="controls row-fluid" style="margin-left:180px;">
                            <textarea name="alert_text" class="span6">{$MASKEDINPUT['alert_text']}</textarea>
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
{/strip}