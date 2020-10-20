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
{assign var="COMMENT_TEXTAREA_DEFAULT_ROWS" value="2"}
{assign var="PRIVATE_COMMENT_MODULES" value=Vtiger_Functions::getPrivateCommentModules()}
{assign var=IS_CREATABLE value=$COMMENTS_MODULE_MODEL->isPermitted('CreateView')}
{assign var=IS_EDITABLE value=$COMMENTS_MODULE_MODEL->isPermitted('EditView')}
<div class = "summaryWidgetContainer">
    <div class="recentComments">
	    <div class="commentTitle">
			{if $IS_CREATABLE}
				<div class="addCommentBlock">
					<div class="row">
						<div class=" col-lg-12">
							<div class="commentTextArea ">
								<textarea name="commentcontent" class="commentcontent form-control mention_listener" placeholder="{vtranslate('LBL_POST_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
							</div>
						</div>
					</div>
					<div class='row'>
						<div class="col-xs-6 pull-right paddingTop5 paddingLeft0">
							<div style="text-align: right;">
								{if in_array($MODULE_NAME, $PRIVATE_COMMENT_MODULES)}
									<div class="" style="margin: 7px 0;">
										<label>
											<input type="checkbox" id="is_private" style="margin:2px 0px -2px 0px">&nbsp;&nbsp;{vtranslate('LBL_INTERNAL_COMMENT')}
										</label>&nbsp;&nbsp;
										<i class="material-icons cursorPointer" data-toggle="tooltip" data-placement="top" data-original-title="{vtranslate('LBL_INTERNAL_COMMENT_INFO')}">info_outline</i>&nbsp;&nbsp;
									</div>
								{/if}
								<button class="btn btn-success btn-sm quickViewSaveComment" type="button" data-mode="add">{vtranslate('LBL_POST', $MODULE_NAME)}</button>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
	
		<hr>
        <div class="commentsBody container-fluid" style = "height:100%">
            {if !empty($COMMENTS)}
                <div class="recentCommentsBody row">
                    {foreach key=index item=COMMENT from=$COMMENTS}
                        {assign var=CREATOR_NAME value=$COMMENT->getCommentedByName()}
                        <div class="commentDetails">
                            <div class="singleComment">
                                {assign var=PARENT_COMMENT_MODEL value=$COMMENT->getParentCommentModel()}
                                {assign var=CHILD_COMMENTS_MODEL value=$COMMENT->getChildComments()}
                                <div class="container-fluid">
                                    <div class="row">
                                         <div class="col-lg-2 col-xs-3 recordImage commentInfoHeader" data-commentid="{$COMMENT->getId()}" data-parentcommentid="{$COMMENT->get('parent_comments')}" data-relatedto = "{$COMMENT->get('related_to')}">
                                            {assign var=IMAGE_PATH value=$COMMENT->getImagePath()}
                                                {if !empty($IMAGE_PATH)}
                                                    <img src="{$IMAGE_PATH}" width="100%" height="100%" align="left">
                                                {else}
                                                    <div class="name"><span><strong> {$CREATOR_NAME|substr:0:2} </strong></span></div>
                                                {/if}
                                        </div>
                                        <div class="comment col-lg-10 col-xs-9">
                                            <span class="creatorName">
                                                {$CREATOR_NAME}
                                            </span>&nbsp;&nbsp;
                                            <div class="">
                                                <span class="commentInfoContent">
                                                    {nl2br($COMMENT->get('commentcontent'))}
                                                </span>
                                            </div>
                                            <div class="commentActionsContainer">      
                                                <span class="commentTime pull-right">
                                                    <p class="muted"><small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getCommentedTime())}">{Vtiger_Util_Helper::formatDateAndDateDiffInString($COMMENT->getCommentedTime())}</small></p>
                                                </span>
                                            </div>
                                            <div style="margin-top:5px;">
												{assign var="FILE_DETAILS" value=$COMMENT->getFileNameAndDownloadURL()}
                                                {foreach key=index item=FILE_DETAIL from=$FILE_DETAILS}
                                                    {assign var="FILE_NAME" value=$FILE_DETAIL['trimmedFileName']}
                                                    {if !empty($FILE_NAME)}
                                                    	{assign var="IS_DOWNLOAD_PERMITTED" value=Users_Privileges_Model::isPermitted('Documents', 'DownloadDocuments')}
                           								{if $IS_DOWNLOAD_PERMITTED}
	                                                        <a onclick="Vtiger_List_Js.previewFile(event,{$COMMENT->get('id')},{$FILE_DETAIL['attachmentId']});" data-filename="{$FILE_NAME}" href="javascript:void(0)" name="viewfile">
	                                                            <span title="{$FILE_DETAILS['rawFileName']}" style="line-height:1.5em;">{$FILE_NAME}</span>&nbsp
	                                                        </a>
	                                                        <br>
                                                        {/if} 
                                                    {/if}
                                                {/foreach}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                {include file="NoComments.tpl"|@vtemplate_path}
            {/if}
        </div>
    </div>
</div>
