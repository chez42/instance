{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}

<div id="sendEmailContainer" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        {assign var=TITLE value="{vtranslate('Send Email To Signer', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="massSaveDocuSign" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="action" value="MassSaveAjax" />
            
            <input type="hidden" name="record" value="{$RECORD}" />
            <div class="modal-body">
                <div>
                    <span><strong>{vtranslate('Step 1',$MODULE)}</strong></span>
                    &nbsp;:&nbsp;
                    {vtranslate('Select the email fields to send ',$MODULE)}
                </div>
                <br>
                <div>
                    <div>
                        <select name="fields" data-placeholder="{vtranslate('Select the email fields to send',$MODULE)}" data-rule-required="true"  class = "select2 form-control">
                            <option value="">Select Field</option>
                            {foreach item=EMAIL_FIELD from=$EMAIL_FIELDS}
                                {assign var=EMAIL_FIELD_NAME value=$EMAIL_FIELD->get('name')}
                                <option value="{$EMAIL_FIELD_NAME}">
                                    {if !empty($SINGLE_RECORD)}
                                        {assign var=FIELD_VALUE value=$SINGLE_RECORD->get($EMAIL_FIELD_NAME)}
                                    {/if}
                                    {vtranslate($EMAIL_FIELD->get('label'), $SOURCE_MODULE)}{if !empty($FIELD_VALUE)} ({$FIELD_VALUE}){/if}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <br>        
                </div>
                <hr>
                <div>
                    <span><strong>{vtranslate('Step 2',$MODULE)}</strong></span>
                    &nbsp;:&nbsp;
                    {vtranslate('Select template to send',$MODULE)}
                </div>
                <br>
                <div>
                    <div>
                        <select name="templateid" data-placeholder="{vtranslate('Select template for send email',$MODULE)}" data-rule-required="true" class = "select2 form-control">
                            <option value="">Select Template</option>
                            {foreach item=TEMPLATE from=$TEMPLATES}
                                <option value="{$TEMPLATE['id']}">
                                    {vtranslate($TEMPLATE['filename'], $SOURCE_MODULE)}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <br>        
                </div>
            </div>
            <div>
                <div class="modal-footer">
                    <center>
                        <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SEND', $MODULE)}</strong></button>
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </center>
                </div>
            </div>
        </form>
    </div>
</div>
