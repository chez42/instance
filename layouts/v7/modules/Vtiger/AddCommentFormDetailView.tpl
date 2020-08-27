{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/MassActionAjax.php *}

{assign var="COMMENT_TEXTAREA_DEFAULT_ROWS" value="2"}
{assign var="PRIVATE_COMMENT_MODULES" value=Vtiger_Functions::getPrivateCommentModules()}
<div class="modal-dialog">
    <div class="modal-content">
        <form class="form-horizontal" id="add_comment">
            
            <input type="hidden" name="module" id="module" value="{$MODULE}" />
			<input type="hidden" name="source_module" value="{$SOURCE_MODULE}" />
			<input type="hidden" name="related_to" id="related_to" value="{$SELECTED_IDS}" />

            {assign var=HEADER_TITLE value={vtranslate('LBL_ADDING_COMMENT', $MODULE)}}
            {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}

            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row commentTextArea" id="mass_action_add_comment">
                        <textarea class="col-lg-12" name="commentcontent" id="commentcontent" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}" placeholder="{vtranslate('LBL_WRITE_YOUR_COMMENT_HERE', $MODULE)}..." data-rule-required="true"></textarea>
                    </div>
                </div>
             	<div class='row'>
					<div class="col-xs-6 pull-right paddingTop5 paddingLeft0">
						<div style="text-align: right;">
							{if in_array($SOURCE_MODULE, $PRIVATE_COMMENT_MODULES)}
								<div class="" style="margin: 7px 0;">
									<label>
										<input type="checkbox" name="is_private" id="is_private" style="margin:2px 0px -2px 0px">&nbsp;&nbsp;{vtranslate('LBL_INTERNAL_COMMENT')}
									</label>&nbsp;&nbsp;
								</div>
							{/if}
						</div>
					</div>
					<div class="col-xs-6 pull-left">
						<div class="fileUploadContainer text-left" style="display:none;">
							<div class="fileUploadBtn btn btn-sm btn-primary">
								<span><i class="fa fa-laptop"></i> {vtranslate('LBL_ATTACH_FILES', $MODULE)}</span>
								<input type="file" id="{$MODULE}_editView_fieldName_commentFile" class="inputElement multi commentFile" maxlength="6" id="filename" name="filename[]"
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
				</div>
            </div>
            
			<div class="modal-footer">
		        <div class="row-fluid">
		            <div class="col-xs-12">
		                <div>
		                    <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
		                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
		                    </div>
		                    <button class="btn btn-success saveButton" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
		                </div>
		            </div>
		        </div>
		    </div>
		    
       </form>
    </div>
</div>

