{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is: vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
<div id="sendEmailRelatedContainer" class='modal-dialog modal-lg'>
    <form class="form-horizontal" id="sendEnvelope" name="sendEnvelope" method="post" action="index.php">
        <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="action" value="SendEmailToRelated" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="viewname" value="{$CVID}" />
            <input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
            <input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
            <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
            <input type="hidden" name="parent_module" value="{$PARENT_MODULE}" />
            <input type="hidden" name="parent_record" value="{$PARENT_RECORD}" />
            
            <div class = "modal-content">
                {assign var=TITLE value="{vtranslate('Send Envelope',$MODULE)}"}
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}
                <div class='modal-body' style="margin-bottom:60px">
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
	                        <select name="templateid" id="templateid" data-placeholder="{vtranslate('Select template for send email',$MODULE)}" data-rule-required="true" class = "select2 form-control">
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
	                <hr>
	                <div>
	                    <span><strong>{vtranslate('Step 3',$MODULE)}</strong></span>
	                </div>
	                <br>
	                <div>
	                	<select name="selected_contacts" id="selected_contacts" data-placeholder="{vtranslate('Select Contact For Sign',$MODULE)}" class = "select2 form-control">
                            <option value=""></option>
                            {foreach item=CONTACT key=ID from=$CONTACTS}
                                <option value="{$ID}_SIGN">
                                    {vtranslate($CONTACT, $SOURCE_MODULE)}
                                </option>
                            {/foreach}
                        </select>
                    </div>
	                <br>
	                <div>
	                	 <textarea name="envelope_content" id="envelope_content"></textarea>
	                </div>
                </div>
                <div>
	                <div class="modal-footer">
	                   <center>
	                       <button type='submit' class='btn btn-success saveButton'>{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
	                       <a class='cancelLink' data-dismiss="modal" href="#">{vtranslate('LBL_CANCEL', $MODULE)}</a>
	                   </center>
	               </div>
               </div>
            </div>
    </form>
</div>
{/strip}