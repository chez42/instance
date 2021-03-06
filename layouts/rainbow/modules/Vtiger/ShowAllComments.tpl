{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
<form id="detailView" method="POST">
	{assign var="COMMENT_TEXTAREA_DEFAULT_ROWS" value="2"}
	{assign var="PRIVATE_COMMENT_MODULES" value=Vtiger_Functions::getPrivateCommentModules()}
	{assign var=IS_CREATABLE value=$COMMENTS_MODULE_MODEL->isPermitted('CreateView')}
	{assign var=IS_EDITABLE value=$COMMENTS_MODULE_MODEL->isPermitted('EditView')}
	{foreach item=RELATED_LIST_MASSACTION from=$RELATED_LIST_MASSACTIONS name=massActions}
		{if $RELATED_LIST_MASSACTION->getLabel() eq 'LBL_EXPORT'}
	        {assign var=exportAction value=$RELATED_LIST_MASSACTION}
	    {/if}
	{/foreach}
	<div class="commentContainer commentsRelatedContainer container-fluid">
		{if $IS_CREATABLE}
			<div class="commentTitle row">
				<div class="addCommentBlock">
					<div class="commentTextArea">
						<textarea name="commentcontent" class="commentcontent form-control mention_listener"  placeholder="{vtranslate('LBL_POST_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
					</div>
					<div class="row">
						<div class="col-xs-4 pull-right">
							<div class="pull-right">
								{if in_array($MODULE_NAME, $PRIVATE_COMMENT_MODULES)}
									<input type="checkbox" id="is_private">&nbsp;&nbsp;{vtranslate('LBL_INTERNAL_COMMENT')}&nbsp;
									<i class="ti-info-alt cursorPointer" data-toggle="tooltip" data-placement="top" data-original-title="{vtranslate('LBL_INTERNAL_COMMENT_INFO')}"></i>&nbsp;&nbsp;
								{/if}
								<button class="btn btn-success btn-sm saveComment" type="button" data-mode="add"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
							</div>
						</div>
						<div class="col-xs-8 pull-left" style="display:none;">
							{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE_NAME) MODULE="ModComments"}
						</div>
					</div>
				</div>
			</div>
		{/if}
		<div class="showcomments container-fluid" style="margin-top:10px;">
			<div class="recentCommentsHeader row">
				<h4 class="display-inline-block  textOverflowEllipsis" title="{vtranslate('LBL_RECENT_COMMENTS', $MODULE_NAME)}">
					{vtranslate('LBL_COMMENTS',$MODULE)}
				</h4>
				{if $exportAction}
					<div class="col-lg-1 commentHeader pull-right">
		                <div class="btn-group relatedlistViewMassActions pull-right" role="group" style="margin:1%!important;">
		                	<button type="button" class="btn btn-default relatedexport export" id={$MODULE}_reletedlistView_massAction_{$exportAction->getLabel()} 
		                            {if stripos($exportAction->getUrl(), 'javascript:')===0} href="javascript:void(0);" url='{$exportAction->getUrl()|substr:strlen("javascript:")}'{else} href='{$exportAction->getUrl()}' {/if} title="{vtranslate('LBL_EXPORT', $MODULE)}" >
		                        Export
		                    </button>
		                </div> 
	                </div>   
                {/if}
				{if $MODULE_NAME ne 'Leads'}
					<div class="col-lg-4 commentHeader pull-right" style="margin-top:5px;text-align:right;padding-right:20px;">
						<div class="display-inline-block">
							<span class="">{vtranslate('LBL_ROLL_UP',$QUALIFIED_MODULE)} &nbsp;</span>
							<span class="ti-info-alt" data-toggle="tooltip" data-placement="top" title="{vtranslate('LBL_ROLLUP_COMMENTS_INFO',$QUALIFIED_MODULE)}"></span>&nbsp;&nbsp;
						</div>
						<input type="checkbox" class="bootstrap-switch" id="rollupcomments" hascomments="1" startindex="{$STARTINDEX}" data-view="relatedlist" rollupid="{$ROLLUPID}" 
							   rollup-status="{$ROLLUP_STATUS}" module="{$MODULE_NAME}" record="{$MODULE_RECORD}" checked data-on-color="success"/>
					</div> 
				{/if}
			</div>
			<hr>
			<div class="commentsList commentsBody marginBottom15">
				{include file='CommentsList.tpl'|@vtemplate_path COMMENT_MODULE_MODEL=$COMMENTS_MODULE_MODEL IS_CREATABLE=$IS_CREATABLE IS_EDITABLE=$IS_EDITABLE}
			</div>

			<div class="hide basicAddCommentBlock container-fluid">
				<div class="commentTextArea row">
					<textarea name="commentcontent" class="commentcontent" placeholder="{vtranslate('LBL_POST_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
				</div>
				<div class="pull-right row">
					{if in_array($MODULE_NAME, $PRIVATE_COMMENT_MODULES)}
						<input type="checkbox" id="is_private">&nbsp;&nbsp;{vtranslate('LBL_INTERNAL_COMMENT')}&nbsp;&nbsp;
					{/if}
					<button class="btn btn-success btn-sm saveComment" type="button" data-mode="add"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
					<a href="javascript:void(0);" class="cursorPointer closeCommentBlock cancelLink" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
				</div>
			</div>

			<div class="hide basicEditCommentBlock container-fluid">
				<div class="row" style="padding-bottom: 10px;">
					<input style="width:100%;height:30px;" type="text" name="reasonToEdit" placeholder="{vtranslate('LBL_REASON_FOR_CHANGING_COMMENT', $MODULE_NAME)}" class="input-block-level"/>
				</div>
				<div class="row">
					<div class="commentTextArea">
						<textarea name="commentcontent" class="commentcontenthidden"  placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
					</div>
				</div>
				<input type="hidden" name="is_private">
				<div class="pull-right row">
					<button class="btn btn-success btn-sm saveComment" type="button" data-mode="edit"><strong>{vtranslate('LBL_POST', $MODULE_NAME)}</strong></button>
					<a href="javascript:void(0);" class="cursorPointer closeCommentBlock cancelLink" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
				</div>
			</div>
		</div>
	</div>
</form>
{/strip}