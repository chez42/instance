{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/ComposeEmail.php *}

{strip}
    <div class="SendEmailFormStep2 modal-dialog modal-lg" id="composeEmailContainer">
        <div class="modal-content">
            <form class="form-horizontal" id="massEmailForm" method="post" action="index.php" enctype="multipart/form-data" name="massEmailForm">
                {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE={vtranslate('LBL_COMPOSE_EMAIL', $MODULE)}}
                <div class="modal-body">
                    <input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)} />
                    <input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)} />
                    <input type="hidden" name="viewname" value="{$VIEWNAME}" />
                    <input type="hidden" name="module" value="{$MODULE}"/>
                    <input type="hidden" name="mode" value="massSave" />
                    <input type="hidden" name="toemailinfo" value='{ZEND_JSON::encode($TOMAIL_INFO)}' />
                    <input type="hidden" name="view" value="MassSaveAjax" />
                    <input type="hidden"  name="to" value='{ZEND_JSON::encode($TO)}' />
                    <input type="hidden"  name="toMailNamesList" value='{$TOMAIL_NAMES_LIST}'/>
                    <input type="hidden" id="flag" name="flag" value="" />
                    <input type="hidden" id="maxUploadSize" value="{$MAX_UPLOAD_SIZE}" />
                    <input type="hidden" id="documentIds" name="documentids" value="" />
                    <input type="hidden" name="emailMode" value="{$EMAIL_MODE}" />
                    <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
                    {if !empty($PARENT_EMAIL_ID)}
                        <input type="hidden" name="parent_id" value="{$PARENT_EMAIL_ID}" />
                        <input type="hidden" name="parent_record_id" value="{$PARENT_RECORD}" />
                    {/if}
                    {if !empty($RECORDID)}
                        <input type="hidden" name="record" value="{$RECORDID}" />
                    {/if}
                    <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
                    <input type="hidden" name="operator" value="{$OPERATOR}" />
                    <input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
                    <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
                    <input type="hidden" id="insertlinkids" name="insertlinkids" value=""/>
					
		            <div class="row">
		                <div class="col-lg-12">
		                    <div class="col-lg-2">
	                        	<span class="pull-right">From &nbsp; {if $SITE_URL neq 'crm4.omnisrv.com' && $SITE_URL neq 'lh.360vew.com'}<span class="redColor">*</span>{/if}</span>
		                    </div>
		                    <div class="col-lg-6">
		                        <select class="from_field select2" name="from_serveremailid" {if $SITE_URL neq 'crm4.omnisrv.com' && $SITE_URL neq 'lh.360vew.com'}data-rule-required="true"{/if} style="width:100%;">
		                        	{if $SITE_URL eq 'crm4.omnisrv.com' || $SITE_URL neq 'lh.360vew.com'}
			                        	<option value="" >System Mail</option>
			                        {else}
			                        	<option value="" ></option>
		                        	{/if}
		                            {foreach from=$LIST_SERVERS item=serverinfo}
		                                <option value="{$serverinfo["account_id"]}" {if $serverinfo["default"] == 0}  selected {/if} > {$serverinfo["account_name"]} </option>
		                            {/foreach}
		                            {if empty($LIST_SERVERS)}
		                            	<option value="clickHereToConfigureMail">Configure New Mail Server</option>
		                            {/if}
		                        </select>
		                    </div>
		                    <div class="col-lg-4">
			                	<span class="pull-left">
			                        <span class="cursorPointer select_refresh" title="Refresh">
			                        	<i class="fa fa-refresh"></i>
		                        	</span>&nbsp;
	                        	</span>
			                </div>
		                </div>
		                <div class="col-lg-4"></div>
		            </div>
                    <div class="row toEmailField">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="pull-right">{vtranslate('LBL_TO',$MODULE)}&nbsp;<span class="redColor">*</span></span>
                            </div>
                            <div class="col-lg-6">
                                {if !empty($TO)}
                                    {assign var=TO_EMAILS value=","|implode:$TO}
                                {/if}
                                {if !$TOEMAILCOUNT}
                                	<input id="emailField" style="width:100%" name="toEmail" type="text" class="autoComplete sourceField select2" data-rule-required="true" data-rule-multiEmails="true" value="{$TO_EMAILS}" placeholder="{vtranslate('LBL_TYPE_AND_SEARCH',$MODULE)}">
                            	{else}
                            		<strong>{$TOEMAILCOUNT}</strong> contacts selected.
                            		<input type="hidden" name="selectedfields" value='{ZEND_JSON::encode($SELECTED_FIELDS)}'>
                            	{/if}
                            </div>
                            <div class="col-lg-4 insertTemplate">
                                <button id="selectEmailTemplate" class="btn btn-success pull-right" data-url="module=EmailTemplates&view=Popup">{vtranslate('LBL_SELECT_EMAIL_TEMPLATE',$MODULE)}</button>
                            </div>
                           <div class="col-lg-4 input-group" style = "display:none;">
                                <select style="width: 140px;" class="select2 emailModulesList pull-right">
                                    {foreach item=MODULE_NAME from=$RELATED_MODULES}
                                        <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $FIELD_MODULE} selected {/if}>{vtranslate($MODULE_NAME,$MODULE_NAME)}</option>
							 {/foreach}
                                </select>
                                <a href="#" class="clearReferenceSelection cursorPointer" name="clearToEmailField"> X </a>
                                <span class="input-group-addon">
                                    <span class="selectEmail cursorPointer">
                                        <i class="fa fa-search" title="{vtranslate('LBL_SELECT', $MODULE)}"></i>
                                    </span>
                                </span>
                            </div>
                            </div>
                                    
                        </div>
                    
                    <div class="row {if empty($CC)} hide {/if} ccContainer">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="pull-right">{vtranslate('LBL_CC',$MODULE)}</span>
                            </div>
                            <div class="col-lg-6">
                                <input id="ccEmailField" style="width:100%" class="autoComplete sourceField select2" type="text" name="cc" data-rule-multiEmails="true" value="{if !empty($CC)}{$CC}{/if}" placeholder="{vtranslate('LBL_TYPE_AND_SEARCH',$MODULE)}"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>

                    <div class="row {if empty($BCC)} hide {/if} bccContainer">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="pull-right">{vtranslate('LBL_BCC',$MODULE)}</span>
                            </div>
                            <div class="col-lg-6">
                                <input id="bccEmailField" style="width:100%" class="autoComplete sourceField select2" type="text" name="bcc" data-rule-multiEmails="true" value="{if !empty($BCC)}{$BCC}{/if}" placeholder="{vtranslate('LBL_TYPE_AND_SEARCH',$MODULE)}"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>
                    
                    <div class="row {if (!empty($CC)) and (!empty($BCC))} hide {/if} ">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                            </div>
                            <div class="col-lg-6">
                                <a href="#" class="cursorPointer {if (!empty($CC))}hide{/if}" id="ccLink">{vtranslate('LBL_ADD_CC', $MODULE)}</a>&nbsp;&nbsp;
                                <a href="#" class="cursorPointer {if (!empty($BCC))}hide{/if}" id="bccLink">{vtranslate('LBL_ADD_BCC', $MODULE)}</a>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>
                    
                    <div class="row subjectField">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="pull-right">{vtranslate('LBL_SUBJECT',$MODULE)}&nbsp;<span class="redColor">*</span></span>
                            </div>
                            <div class="col-lg-6">
                                <input type="text" name="subject" value="{$SUBJECT}" data-rule-required="true" id="subject" spellcheck="true" class="inputElement"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>
                            
                    <div class="row attachment">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="pull-right">{vtranslate('LBL_ATTACHMENT',$MODULE)}</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-5 browse">
                                        <input type="file" {if $FILE_ATTACHED}class="removeNoFileChosen"{/if} id="multiFile" name="file[]"/>&nbsp;
                                    </div>
                                    <div class="col-lg-6 brownseInCrm">
                                        <button type="button" class="btn btn-small btn-success" id="browseCrm" data-url="{$DOCUMENTS_URL}" title="{vtranslate('LBL_BROWSE_CRM',$MODULE)}">{vtranslate('LBL_BROWSE_CRM',$MODULE)}</button>
                                    </div>
                                     
                                </div>
                                <div id="attachments">
                                    {foreach item=ATTACHMENT from=$ATTACHMENTS}
                                        {if ('docid'|array_key_exists:$ATTACHMENT)}
                                            {assign var=DOCUMENT_ID value=$ATTACHMENT['docid']}
                                            {assign var=FILE_TYPE value="document"}
                                        {else}
                                            {assign var=FILE_TYPE value="file"}
                                        {/if}
                                        <div class="MultiFile-label customAttachment" data-file-id="{$ATTACHMENT['fileid']}" data-file-type="{$FILE_TYPE}"  data-file-size="{$ATTACHMENT['size']}" {if $FILE_TYPE eq "document"} data-document-id="{$DOCUMENT_ID}"{/if}>
                                            {if $ATTACHMENT['nondeletable'] neq true}
                                                <a name="removeAttachment" class="cursorPointer">x </a>
                                            {/if}
                                            <span>{$ATTACHMENT['attachment']}</span>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        </div>
                    </div>
                                
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="pull-right">{vtranslate('LBL_INCLUDE_SIGNATURE',$MODULE)}</span>
                            </div>
                            <div class="col-lg-10">
                            	<div class="row">
		                            <div class="item col-lg-1">
		                                <input class="" type="checkbox" name="signature" value="Yes" checked="checked" id="signature">
		                            </div>
		                            <div class="col-lg-6 insertLink">
		                                <button type="button" class="btn btn-small btn-success" id="insertLink" data-url="module=Documents&view=InsertLinkPopup&src_module=Emails&src_field=composeEmail" title="{vtranslate('Insert Link',$MODULE)}">{vtranslate('Insert Link',$MODULE)}</button>
		                            </div>
	                            </div>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid hide" id='emailTemplateWarning'>
                        <div class="alert alert-warning fade in">
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                            <p>{vtranslate('LBL_EMAILTEMPLATE_WARNING_CONTENT',$MODULE)}</p>
                        </div>
                    </div>         
                    <div class="row templateContent">
                        <div class="col-lg-12">
                            <textarea style="width:390px;height:200px;" id="description" name="description">{$DESCRIPTION}</textarea>
                        </div>
                    </div>
                    
                    {if $RELATED_LOAD eq true}
                        <input type="hidden" name="related_load" value={$RELATED_LOAD} />
                    {/if}
                    <input type="hidden" name="attachments" value='{ZEND_JSON::encode($ATTACHMENTS)}' />
                    <div id="emailTemplateWarningContent" style="display: none;">
                        {vtranslate('LBL_EMAILTEMPLATE_WARNING_CONTENT',$MODULE)}
                    </div>
                </div>
                
                <div class="modal-footer">
                    <div class="pull-right cancelLinkContainer">
                        <a href="#" class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button id="sendEmail" name="sendemail" class="btn btn-success" title="{vtranslate("LBL_SEND_EMAIL",$MODULE)}" type="submit"><strong>{vtranslate("LBL_SEND_EMAIL",$MODULE)}</strong></button>
                    <button id="saveDraft" name="savedraft" class="btn btn-default" title="{vtranslate('LBL_SAVE_AS_DRAFT',$MODULE)}" type="submit"><strong>{vtranslate('LBL_SAVE_AS_DRAFT',$MODULE)}</strong></button>
                </div>
            </form>
        </div>
    </div>
{/strip}
