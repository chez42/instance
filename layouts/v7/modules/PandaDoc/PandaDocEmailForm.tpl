{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}
<script type="text/javascript">
    var related_uimeta = (function() {
        return {
            field: {
                get: function(name, property) {
                    return false;
                },
                isMandatory: function(name) {
                    return false;
                },
                getType: function(name) {
                    return false;
                }
            },
        };
    })();
</script>
<style>
	.overlayPageContent.fade.in {
		top:6px !important;
	}
</style>

<div id="sendSmsContainer" class='fc-overlay-modal overlayDetail'>
    <div class = "modal-content">
        {assign var=TITLE value="{vtranslate('Send Envelope', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="massSaveSendEnvelope" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="action" value="MassSaveAjax" />
            
        	<input type="hidden" name="viewname" value="{$VIEWNAME}" />
            <input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
            <input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
            <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
            <input type="hidden" name="operator" value="{$OPERATOR}" />
            <input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
            <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
            
            <input type="hidden" name="parent_module" value="{$PARENT_MODULE}" />
            <input type="hidden" name="parent_record" value="{$PARENT_RECORD}" />
            
            <input type="hidden" name="mode" value="{$MODE}" />
            
            <div class="modal-body">
            	<div class="col-md-4">
	                <div>
	                    <span><strong>{vtranslate('Step 1',$MODULE)}</strong></span>
	                    &nbsp;:&nbsp;
	                    {vtranslate('Select the email fields to send',$MODULE)}
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
	                        <select id="templateid" name="templateid" data-placeholder="{vtranslate('Select template for send email',$MODULE)}" data-rule-required="true" class = "select2 form-control">
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
						<select name="selected_contacts" id="selected_contacts" data-rule-required="true" data-placeholder="{vtranslate('Select Recipients',$MODULE)}" class = "select2 form-control multiple_con">
							<option value=""></option>
							{foreach item=CONTACT key=ID from=$CONTACTS}
								<option value="{$ID}_SIGN">
									{vtranslate($CONTACT, $SOURCE_MODULE)}
								</option>
							{/foreach}
						</select>
					</div>
					<br>
                </div>
                <div class="col-md-8">
					<div>
						 <textarea name="envelope_content" id="envelope_content" style = "height:500px"></textarea>
					</div>
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
