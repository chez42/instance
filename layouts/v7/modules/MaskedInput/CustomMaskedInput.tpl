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
    <div id="massEditContainer" class='modal-dialog modal-lg' style="width: 600px;">
        <div id="massEdit" class="modal-content">
            <div class="modal-header contentsBackground">
                <div class="clearfix">
                    <div class="pull-right "><button type="button" class="close" aria-label="Close" data-dismiss="modal"><span aria-hidden="true" class="fa fa-close"></span></button>
                    </div><h4 class="pull-left" id="massEditHeader">{if $RECORD}{vtranslate('LBL_EDIT')}{else}{vtranslate('LBL_ADD')}{/if} {vtranslate('LBL_CUSTOM', 'MaskedInput')} {vtranslate('MaskedInput', 'MaskedInput')}</h4>
                </div>
            </div>
        </div>
        <form class="form-horizontal" id="editForm">
            <input type="hidden" name="record" value="{$RECORD}" />
            <input type="hidden" name="mode" value="saveMaskedInput">
            <div name='massEditContent' class="row-fluid">
                <div class="modal-body">
                    <div class="form-group">
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
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="email"> <span class="redColor">*</span>&nbsp;{vtranslate('MaskedInput', 'MaskedInput')} {vtranslate('LBL_SYNTAX', 'MaskedInput')}</label>
                        <div class="col-sm-8">
                            <input type="text" value="{$MASKEDINPUT['masked_input']}" name="masked_input"  class="form-control" style="width: 50%"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="email">
                            &nbsp;{vtranslate('LBL_ALERT', 'MaskedInput')} {vtranslate('LBL_IF_NOT_COMPLETED', 'MaskedInput')}
                        </label>
                        <div class="col-sm-8">
                            <select class="select2 span4" name="alert" style="width: 50%">
                                <option value="0" {if $MASKEDINPUT['alert'] eq '0'}selected{/if}>{vtranslate('LBL_NO')}</option>
                                <option value="1" {if $MASKEDINPUT['alert'] eq '1'}selected{/if}>{vtranslate('LBL_YES')}</option>

                            </select>
                        </div>
                    </div>
                    {*<div class="control-group">*}
                        {*<label class="muted control-label" style="width: 160px;">*}
                            {*&nbsp;{vtranslate('LBL_ALERT', 'MaskedInput')} {vtranslate('LBL_IF_NOT_COMPLETED', 'MaskedInput')}*}
                        {*</label>*}
                        {*<div class="controls row-fluid" style="margin-left:180px;">*}
                            {*<select class="select2 span4" name="alert">*}
                                {*<option value="0" {if $MASKEDINPUT['alert'] eq '0'}selected{/if}>{vtranslate('LBL_NO')}</option>*}
                                {*<option value="1" {if $MASKEDINPUT['alert'] eq '1'}selected{/if}>{vtranslate('LBL_YES')}</option>*}

                            {*</select>*}
                        {*</div>*}
                    {*</div>*}
                    {*<div class="control-group">*}
                        {*<label class="muted control-label" style="width: 160px;">*}
                            {*{vtranslate('LBL_ALERT', 'MaskedInput')} {vtranslate('LBL_TEXT', 'MaskedInput')}*}
                        {*</label>*}
                        {*<div class="controls row-fluid" style="margin-left:180px;">*}
                            {*<textarea name="alert_text" class="span6">{$MASKEDINPUT['alert_text']}</textarea>*}
                        {*</div>*}
                    {*</div>*}
                    <div class="form-group">
                        <label class="control-label col-sm-4" for="email">
                            {vtranslate('LBL_ALERT', 'MaskedInput')} {vtranslate('LBL_TEXT', 'MaskedInput')}
                        </label>
                        <div class="col-sm-8">
                            <textarea name="alert_text" style="width: 60%">{$MASKEDINPUT['alert_text']}</textarea>
                        </div>
                    </div>
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
{/strip}