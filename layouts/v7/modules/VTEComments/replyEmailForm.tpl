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
<div>
    <form class="form-horizontal" id="massEmailCommentForm" method="post" action="index.php" enctype="multipart/form-data" name="massEmailCommentForm">
        <div class="">
            <input type="hidden" name="excluded_ids" value='{ZEND_JSON::encode($EXCLUDED_IDS)}' />
            <input type="hidden" name="module" value="VTEComments"/>
            <input type="hidden" name="mode" value="massSave" />
            <input type="hidden" name="toemailinfo" value='{ZEND_JSON::encode($TOMAIL_INFO)}' />
            <input type="hidden" name="view" value="MassSaveAjax" />
            <input type="hidden"  name="to" value='["{$TO}"]' />
            <input type="hidden"  name="toMailNamesList" value='{$TOMAIL_NAMES_LIST}'/>
            <input type="hidden" id="flag" name="flag" value="" />
            <input type="hidden" id="maxUploadSize" value="{$MAX_UPLOAD_SIZE}" />
            <input type="hidden" id="documentIds" name="documentids" value="" />
            <input type="hidden" name="emailMode" value="{$EMAIL_MODE}" />
            <input type="hidden" name="source_record_id" value="{$PARENT_RECORD}" />
            <input type="hidden" name="contact_id" value="{$CONTACT_ID}" />
            <input type="hidden" name="subject" value="{$SUBJECT}" />
            <div class="col-lg-12">
                <div class="col-lg-8">
                    <div class="row toEmailField">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_TO',$MODULE)}&nbsp;<span class="redColor">*</span></span>
                            </div>
                            <div class="col-lg-10">
                                {if !empty($TO)}
                                    {assign var=TO_EMAILS value=","|implode:$TO}
                                {/if}
                                <input id="emailField" style="width:100%" name="toEmail" type="text" class="autoComplete sourceField select2" data-rule-required="true" data-rule-multiEmails="true" value="{$TO}" placeholder="{vtranslate('LBL_TYPE_AND_SEARCH',$MODULE)} " disabled="disabled">
                            </div>
                        </div>
                    </div>

                    <div class="row {if empty($CC)} hide {/if} ccContainer">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_CC',$MODULE)}</span>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" name="cc" data-rule-multiEmails="true" value="{if !empty($CC)}{$CC}{/if}"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>

                    <div class="row {if (!empty($CC))} hide {/if} ">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                            </div>
                            <div class="col-lg-10">
                                <span href="#" class="cursorPointer {if (!empty($CC))}hide{/if}" id="ccLink">{vtranslate('LBL_ADD_CC', $MODULE)}</span>&nbsp;&nbsp;
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>

                    <div class="row subjectField">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_SUBJECT',$MODULE)}&nbsp;<span class="redColor">*</span></span>
                            </div>
                            <div class="col-lg-10">
                                <input type="text" name="subject" value="{$SUBJECT}" data-rule-required="true" id="subject" spellcheck="true" class="inputElement" disabled="disabled"/>
                            </div>
                            <div class="col-lg-4"></div>
                        </div>
                    </div>

                    <div class="row attachment">
                        <div class="col-lg-12">
                            <div class="col-lg-2">
                                <span class="">{vtranslate('LBL_ATTACHMENT',$MODULE)}</span>
                            </div>
                            <div class="col-lg-10">
                                <div class="row">
                                    <div class="col-lg-8 browse">
                                        <input type="file" {if $FILE_ATTACHED}class="removeNoFileChosen"{/if} id="multiFile" name="file[]"/>&nbsp;
                                    </div>
                                    <div class="col-lg-4 brownseInCrm">
                                        <button type="button" class="btn btn-small btn-default" id="vteCommentsBrowseCrm" data-url="view=Popup&module=Documents&src_module=Emails&src_field=composeEmail" title="{vtranslate('LBL_BROWSE_CRM',$MODULE)}">{vtranslate('LBL_BROWSE_CRM',$MODULE)}</button>
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
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-lg-8">
                                <span class="pull-left">{vtranslate('LBL_INCLUDE_SIGNATURE',$MODULE)}</span>
                            </div>
                            <div class="item col-lg-2">
                                <input class="" type="checkbox" name="signature" value="Yes" checked="checked" id="signature">
                            </div>
                        </div>
                        <div class="col-lg-12 insertTemplate">
                            <div class="col-lg-12" style="margin-top: 20px;">
                                <select  id="slEmailTemplates" name="slEmailTemplates" class="inputElement select2 slEmailTemplates" style="width: 100%">
                                    <option value="-1">{vtranslate('Please Select Template',$MODULE)}</option>
                                    {foreach from=$EMAIL_TEMPLATES key=ROW_ID item=ROW}
                                        <option value="{$ROW_ID}">{$ROW}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid hide" id='emailTemplateWarning'>
                        <div class="alert alert-warning fade in">
                            <a href="#" class="close" data-dismiss="alert">&times;</a>
                            <p>{vtranslate('LBL_EMAILTEMPLATE_WARNING_CONTENT',$MODULE)}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row templateContent">
                <div class="col-lg-12">
                    <textarea style="width:390px;height:200px;" id="emailbody" name="emailbody">{$DESCRIPTION}</textarea>
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

        <div class="col-lg-12 pull-right" style="text-align: right;">
            <button class="cancelLink t-btn-sm btn btn-success" type="button" id="commentReplyGoBack">Cancel</button>&nbsp;
            <button id="sendEmailComments" name="sendEmailComments" class="btn btn-success" title="{vtranslate("LBL_SEND_EMAIL",$MODULE)}" type="submit" style="pointer-events: none;cursor: default; opacity: 0.5;"><strong>{vtranslate("LBL_SEND_EMAIL",$MODULE)}</strong></button>
        </div>
    </form>
</div>
{/strip}
