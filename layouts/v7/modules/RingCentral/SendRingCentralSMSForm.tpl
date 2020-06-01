{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}
<style>
   
    ::-webkit-scrollbar {
    	width: 4px;
    }
    ::-webkit-scrollbar-thumb {
    	background-color: #4c4c6a;
    	border-radius: 2px;
    }
    .chatbox {
        width: 100%;
        height: 400px;
        max-height: 400px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }
    .chat-window {
        flex: auto;
        max-height: calc(100% - 60px);
         overflow: hidden;
    }
    .chat-window:hover {
    	overflow: auto;
	}
    .msg-container {
        position: relative;
        display: inline-block;
        width: 100%;
        margin: 0 0 10px 0;
        padding: 0;
    }
    .msg-box {
        display: flex;
        background: #5b5e6c;
        padding: 10px 10px 0 10px;
        border-radius: 0 6px 6px 0;
        max-width: 80%;
        width: auto;
        float: left;
        box-shadow: 0 0 2px rgba(0,0,0,.12),0 2px 4px rgba(0,0,0,.24);
    }
    .flr {
        flex: 1 0 auto;
        display: flex;
        flex-direction: column;
        width: calc(100% - 50px);
    }
    .messages {
        flex: 1 0 auto;
    }
    .msg {
        display: inline-block;
        font-size: 11pt;
        line-height: 13pt;
        color: rgba(255,255,255,.7);
        margin: 0 0 4px 0;
    }
    .msg:first-of-type {
        margin-top: 8px;
    }
    .timestamp {
         color: rgba(255,255,255,.7);
        font-size: 8pt;
        margin-bottom: 10px;
    }
    .posttime {
        margin-left: 3px;
    }
    .msg-self .msg-box {
        border-radius: 6px 0 0 6px;
        background: #2671ff;
        float: right;
    }
    .msg-self .msg {
        text-align: left;
    }
    .msg-self .timestamp {
        text-align: right;
    }
</style>
<div id="sendSmsContainer" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        {assign var=TITLE value="{vtranslate('LBL_SEND_SMS', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="massSaveRingCentral" method="post" action="index.php">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="action" value="MassSaveAjax" />
            <input type="hidden" name="record" value="{$RECORD}" />
            <input type="hidden" name="type" value='sms' />
            <input type="hidden" class="ringcentral_phoneno" name="number" value= ''>
            
            <div class="modal-body">
            	{if !empty($MESSAGES)}
	            	<div>
	            		<section class="chatbox">
							<section class="chat-window">
								{foreach item=MESSAGE from=$MESSAGES}
									{if $MESSAGE['direction'] eq 'Incoming'}
										<article class="msg-container msg-remote">
											<div class="msg-box">
												<div class="flr">
													<div class="messages">
														<p class="msg">
															{$MESSAGE['message']}
														</p>
													</div>
													<span class="timestamp" title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($MESSAGE['createdtime'])}">&bull;
														<span class="posttime">{Vtiger_Util_Helper::formatDateDiffInStrings($MESSAGE['createdtime'])}</span>
													</span>
												</div>
											</div>
										</article>
									{else if $MESSAGE['direction'] eq 'Outgoing'}
										<article class="msg-container msg-self">
											<div class="msg-box">
												<div class="flr">
													<div class="messages">
														<p class="msg">
															{$MESSAGE['message']}
														</p>
													</div>
													<span class="timestamp" title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($MESSAGE['createdtime'])}">&bull;
														<span class="posttime">{Vtiger_Util_Helper::formatDateDiffInStrings($MESSAGE['createdtime'])}</span>
													</span>
												</div>
												
											</div>
										</article>
									{/if}
								{/foreach}
							</section>
						</section>
	        		</div>		
        		{/if}			
                <div>
                    <span><strong>{vtranslate('Step 1',$MODULE)}</strong></span>
                    &nbsp;:&nbsp;
                    {vtranslate('Select the phone number fields to send',$MODULE)}
                </div>
                <br>
                <div>
                    <div>
                        <select name="fields" data-placeholder="{vtranslate('Select the phone number fields to send',$MODULE)}" data-rule-required="true" class = "select2 form-control phoneField">
                            <option value="">Select an option</option>
                            {foreach item=PHONE_FIELD from=$PHONE_FIELDS}
                                {assign var=PHONE_FIELD_NAME value=$PHONE_FIELD->get('name')}
                                {if !empty($SINGLE_RECORD)}
                                    {assign var=FIELD_VALUE value=$SINGLE_RECORD->get($PHONE_FIELD_NAME)}
                                {/if}
                                <option value="{$PHONE_FIELD_NAME}" data-phoneno="{$FIELD_VALUE}">
                                    
                                    {vtranslate($PHONE_FIELD->get('label'), $SOURCE_MODULE)}{if !empty($FIELD_VALUE)} ({$FIELD_VALUE}){/if}
                                </option>
                            {/foreach}
                        </select>
                    </div>
                    <br>        
                    <div>
                        <span id='phoneFormatWarning'> 
                            <i rel="popover" data-placement="right" id="phoneFormatWarningPop" class="glyphicon glyphicon-info-sign" style="padding-right : 5px; padding-left : 5px" data-original-title="{vtranslate('LBL_WARNING',$MODULE)}" data-trigger="hover" data-content="{vtranslate('LBL_PHONEFORMAT_WARNING_CONTENT',$MODULE)}"></i>
                            {vtranslate('LBL_PHONE_FORMAT_WARNING', $MODULE)}
                        </span>
                    </div>
                </div>
                <hr>
                <div>
                    <span><strong>{vtranslate('Step 2',$MODULE)}</strong></span>
                    &nbsp;:&nbsp;
                    {vtranslate('Type the message',$MODULE)}&nbsp;(&nbsp;{vtranslate('LBL_SMS_MAX_CHARACTERS_ALLOWED',$MODULE)}&nbsp;)
                </div>
                <br>
                <textarea class="form-control smsTextArea" data-rule-required="true" name="message" id="message" maxlength="160" placeholder="{vtranslate('write your message here', $MODULE)}"></textarea>
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
