{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}

<div id="sendSmsContainer" class='modal-xs modal-dialog'>
    <div class = "modal-content">
        {assign var=TITLE value="{vtranslate('Send Fax', $MODULE)}"}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$TITLE}

        <form class="form-horizontal" id="massSaveRingCentral" method="post" action="index.php" enctype="multipart/form-data">
            <input type="hidden" name="module" value="{$MODULE}" />
            <input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
            <input type="hidden" name="action" value="MassSaveAjax" />
            
            {if !$NUMBER}
	            <input type="hidden" name="viewname" value="{$VIEWNAME}" />
	            <input type="hidden" name="selected_ids" value={ZEND_JSON::encode($SELECTED_IDS)}>
	            <input type="hidden" name="excluded_ids" value={ZEND_JSON::encode($EXCLUDED_IDS)}>
	            <input type="hidden" name="search_key" value= "{$SEARCH_KEY}" />
	            <input type="hidden" name="operator" value="{$OPERATOR}" />
	            <input type="hidden" name="search_value" value="{$ALPHABET_VALUE}" />
	            <input type="hidden" name="search_params" value='{ZEND_JSON::encode($SEARCH_PARAMS)}' />
            {else if $NUMBER}
        		<input type="hidden" name="number" value="{$NUMBER}" />
	            <input type="hidden" name="record" value="{$RECORD}" />
            {/if}
            
            <input type="hidden" name="type" value='fax' />
            <div class="modal-body">
	            {if !$NUMBER}
	                <div>
	                    <span><strong>{vtranslate('LBL_STEP_1',$MODULE)}</strong></span>
	                    &nbsp;:&nbsp;
	                    {vtranslate('Select the fax number fields to send',$MODULE)}
	                </div>
	                <br>
	                <div>
	                    <div>
	                        <select name="fields[]" data-placeholder="{vtranslate('Select the fax number fields to send',$MODULE)}" data-rule-required="true" multiple class = "select2 form-control">
	                            {assign var=FAX_FIELD_NAME value=$FAX_FIELDS->get('name')}
	                            <option value="{$FAX_FIELD_NAME}">
	                                {if !empty($SINGLE_RECORD)}
	                                    {assign var=FIELD_VALUE value=$SINGLE_RECORD->get($FAX_FIELD_NAME)}
	                                {/if}
	                                {vtranslate($FAX_FIELDS->get('label'), $SOURCE_MODULE)}{if !empty($FIELD_VALUE)} ({$FIELD_VALUE}){/if}
	                            </option>
	                        </select>
	                    </div>
	                    <br>        
	                    <div>
	                        <span id='phoneFormatWarning'> 
	                            <i rel="popover" data-placement="right" id="phoneFormatWarningPop" class="glyphicon glyphicon-info-sign" style="padding-right : 5px; padding-left : 5px" data-original-title="{vtranslate('LBL_WARNING',$MODULE)}" data-trigger="hover" data-content="{vtranslate('LBL_PHONEFORMAT_WARNING_CONTENT',$MODULE)}"></i>
	                            {vtranslate(' Please ensure that fax number is in international E.164 format', $MODULE)}
	                        </span>
	                    </div>
	                </div>
	                <hr>
	                <div>
	                    <span><strong>{vtranslate('LBL_STEP_2',$MODULE)}</strong></span>
	                    &nbsp;:&nbsp;
	                    {vtranslate('LBL_TYPE_THE_MESSAGE',$MODULE)}&nbsp;(&nbsp;{vtranslate('LBL_SMS_MAX_CHARACTERS_ALLOWED',$MODULE)}&nbsp;)
	                </div>
	                <br>
                {/if}
                <textarea class="form-control smsTextArea" name="message" id="message" maxlength="160" placeholder="{vtranslate('LBL_WRITE_YOUR_MESSAGE_HERE', $MODULE)}"></textarea>
	            <style>
	            	.strike {
					    display: block;
					    text-align: center;
					    overflow: hidden;
					    white-space: nowrap; 
					    margin-top: 1%;
    					margin-bottom: 1%;
					}
					.strike > span {
					    position: relative;
					    display: inline-block;
					}
					.strike > span:before,
					.strike > span:after {
					    content: "";
					    position: absolute;
					    top: 50%;
					    width: 9999px;
					    height: 1px;
					    background: black;
					}
					.strike > span:before {
					    right: 100%;
					    margin-right: 15px;
					}
					.strike > span:after {
					    left: 100%;
					    margin-left: 15px;
					}
	            </style>
	            <div class="strike">
				   <span>Or</span>
				</div>
				<div class="fileUploadContainer text-left">
					<div class="fileUploadBtn btn btn-sm btn-primary">
						<span><i class="fa fa-laptop"></i> {vtranslate('LBL_ATTACH_FILES', $MODULE)}</span>
						<input type="file" id="{$MODULE}_editView_fieldName_faxfile" class="inputElement faxfile"  id="faxfile" name="faxfile"
								value="" />
					</div>&nbsp;&nbsp;
					<span class="uploadFileSizeLimit fa fa-info-circle" data-toggle="tooltip" data-placement="top" title="{vtranslate('LBL_MAX_UPLOAD_SIZE',$MODULE)} {$MAX_UPLOAD_LIMIT_MB} {vtranslate('MB',$MODULE)}">
						<span class="maxUploadSize" data-value="{$MAX_UPLOAD_LIMIT_BYTES}"></span>
					</span>
					<div class="uploadedFileDetails ">
						<div class="uploadedFileSize"></div>
						<div class="uploadedFileName">
						</div>
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
