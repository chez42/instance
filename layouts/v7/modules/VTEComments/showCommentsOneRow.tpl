
{strip}
    <style type="text/css">
        .commentInfoContentBlock img{
            max-width: 80% !important;height: auto!important;
        }
        .userImage-md{
            display: block!important; height: 40px!important;width: 40px!important;min-height: 40px!important;min-width: 40px!important;font-size: 16px!important;color: #FFF;line-height: 40px!important;
        }
        .p-o-btn {
            border-radius: 2px; background-image: none !important;box-shadow: none !important;line-height: 18px;cursor: pointer;font-weight: 400;padding: 6px 16px !important;color: #448aff !important;border: thin solid #448aff !important;background-color: #FFFFFF !important;margin: 0px 4px!important;
        }
        .p-o-btn:hover {
            background-color: #448aff!important;color: #FFFFFF!important;
        }
        .p-btn{
            border-radius: 2px;background-image: none!important;box-shadow: none!important;line-height: 18px;cursor: pointer;font-weight: 400;padding: 6px 16px!important;color: #FFFFFF!important;border: thin solid #448aff!important;background-color: #448aff!important; margin: 0px 4px!important;
        }
        .t-btn-sm {
            border-radius: 2px;background-image: none!important;box-shadow: none!important;line-height: 18px;cursor: pointer;font-weight: 400;padding: 3px 12px!important;color: #555555!important;border: thin solid #CCC!important; background-color: #FFFFFF!important;margin: 0px 4px!important;
        }
        .noCommentsMsgContainer{
            border: none!important;
        }
        #moreInteractions {
            cursor: pointer; margin-bottom: 2px; position: relative; padding: 0px; border-top: none; border-bottom: none; margin-left: -15px; margin-right: -15px;
        }
        .commentCount {
            font-size: smaller; color: lightslategray; text-align: center; position: absolute; left: 0px; right: 0px; top: 0px;  bottom: 0px; margin: auto; height: 21px;
        }
        .divWithBorderBottom {
            border-bottom: 1px solid #f2e8fc; height: 5px;
        }
        .commentCountText {
            padding: 1px 8px 1px 8px; background: white;
        }
    </style>
    {* Change to this also refer: AddCommentForm.tpl *}
    {assign var="COMMENT_TEXTAREA_DEFAULT_ROWS" value="2"}
    {assign var=IS_CREATABLE value=$COMMENTS_MODULE_MODEL->isPermitted('CreateView')}
    {assign var=IS_EDITABLE value=$COMMENTS_MODULE_MODEL->isPermitted('EditView')}

<div class="row" style="margin-bottom: 20px;">
    <div class="hidWidgetPicklistComments" >
        <div class="row hide" id="widgetPicklistComments" style="margin-top: 15px;">
            <div class="col-lg-10 row">
                {foreach item =FIELD_NAME from=$ADD_FIELDS}
                    {assign var=FIELD_MODEL value=$ALL_FIELDS.$FIELD_NAME}
                    {assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
                    <div style="width:10%;float: left; text-align: right; padding-top: 7px;">
                        {vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}&nbsp;&nbsp;
                    </div>
                    <div style="width:20%;float: left;">
                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),'ModComments')}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
    {if $ORDER_BY =='DESC'}
        {if $SOURCE_MODULE_NAME =='HelpDesk'}
        <div class="col-lg-12 clearfix">
            <div class="col-lg-6">
                <div class="pull-right row show" id="searchCommentArea">
                    <div style="margin-right: 7px;" class="pull-left divSearchComment" ></div>
                </div>
            </div>
            <div class="col-lg-6" style="margin-top: 10px;">
                <div class="pull-right row hide" id="replyInfo">
                    <button id="vteSearchCommentButton" type="button" data-pupup-visible="false" class="p-o-btn vteSearchCommentButton"><i class="fa fa-search" aria-hidden="true"></i></button>
                    <button type="button" class="p-o-btn active" id="commentTextArea"><i class="fa fa-comment"></i> Add comment</button>
                    <button type="button" class="p-btn" id="composeEmailForm"><i class="fa fa-reply"></i> Reply</button>
                </div>
            </div>
            <div class="replyArea hide" >
                <div class="" id="composeEmailContainer">
                </div>
            </div>
        </div>
        {literal}
            <script>
                var ele = $('.widgetContainer_comments div.widget_header');
                if(ele.length==0)
                    ele = $('.recentCommentsHeader');
                ele.find('h4').removeClass("col-lg-7").addClass("col-lg-4");
                var wheaderR = ele.find('.w_header_r');
                if(wheaderR.length==0) {
                    ele.append('<div class="col-lg-8 pull-right w_header_r" style="margin-top: 10px;"><div class="w_header"></div></div>');
                    $(".commentHeader").insertAfter('.w_header').css({'padding-top':'0px'});
                    $(".commentHeader").removeClass("col-lg-5");
                    $(".recentComments").css({'margin-top':'25px'});
                    $("#replyInfo").insertAfter('.w_header');
                }
                ele.find("#replyInfo").removeClass("hide").addClass("show");
                if(jQuery('#rollupcomments').attr('rollup-status') == 1) {
                    jQuery('#rollupcomments').bootstrapSwitch('state', true, true);
                }else{
                    jQuery('#rollupcomments').bootstrapSwitch('state', false, true);
                }
                $( "#is_private").prop('checked', true);
                var elesummaryView = $('.summaryView');
                if(elesummaryView.length>0)
                    $(".recentCommentsHeader").addClass("hide");
                $(".commentTitle").removeClass("show").addClass("hide");
                $("#widgetPicklistComments").insertAfter('.addCommentBlock');
                $(".hidWidgetPicklistComments").remove();
                jQuery('body').delegate('#commentTextGoBack,#commentReplyGoBack','click',function(){
                    $(".commentTitle").removeClass("show").addClass("hide");
                    $(".replyArea").removeClass("show").addClass("hide");
                    $("#replyInfo").removeClass("hide").addClass("show");
                    var eleShowckeditor = jQuery('.showckeditor');
                    if(eleShowckeditor.length==0) {
                        var eshowckeditor = '<input type="hidden" value="1" name="showckeditor" class="showckeditor"/>';
                        $("#replyInfo").before(eshowckeditor);
                    }
                    $(".commentTextGoBack").remove();
                    var div_advancecomment = $('.cke_editor_vteAdvanceComment');
                    div_advancecomment.remove();
                    var noteContentElement = jQuery('[name="commentcontent"]');
                    noteContentElement.css({'visibility': '', 'display': ''});
                    jQuery('[name="showckeditor"]').val(1);
                });
                jQuery('body').delegate('#moreInteractions','click',function(){
                    jQuery("#moreInteractions").hide();
                    $(".commentDetails").removeClass("hide").addClass("show");
                });
            </script>
        {/literal}
        {else}
        {literal}
            <script>
                var ele = $('.commentHeader');
                var btnSearch = '<button id="vteSearchCommentButton" type="button" data-pupup-visible="false" class="p-o-btn vteSearchCommentButton"><i class="fa fa-search" aria-hidden="true"></i></button>';
                var eleSearchButton = ele.find('#vteSearchCommentButton');
                if (eleSearchButton.length==0)
                    ele.find('.display-inline-block').before(btnSearch);

                $("#widgetPicklistComments").insertAfter('.addCommentBlock');
                $(".hidWidgetPicklistComments").remove();
                jQuery('body').delegate('#moreInteractions','click',function(){
                    jQuery("#moreInteractions").hide();
                    $(".commentDetails").removeClass("hide").addClass("show");
                });
            </script>
        {/literal}
        {/if}
    {/if}
    {if !empty($COMMENTS)}

    <div class="recentCommentsBody container-fluid pull-left">
        {assign var=COUNT_TOTAL value=$COMMENTS|@count}
        {assign var=COUNTER value=0}
        {assign var=NUMBER_HIDE_COMMENTS value=0}
        {assign var=HAS_MORE_INTERACTIONS value=0}
        {assign var=CHK_HIDE_COMMENTS value=0}

        {if $COUNT_TOTAL > 7}
            {assign var=HAS_MORE_INTERACTIONS value=1}
            {assign var=CHK_HIDE_COMMENTS value=1}
            {assign var=NUMBER_HIDE_COMMENTS value=$COUNT_TOTAL-6}
        {/if}

        {foreach key=index item=COMMENT from=$COMMENTS}
        {assign var="COUNTER" value=$COUNTER+1}
        {if $COUNTER >3 && $HAS_MORE_INTERACTIONS==1}
            {assign var=HAS_MORE_INTERACTIONS value=0}
            <div class="publicCommentHeader" id="moreInteractions">
                <div class="commentCount" style=""><span class="commentCountText">{$NUMBER_HIDE_COMMENTS} Comments</span></div>
                {if $NUMBER_HIDE_COMMENTS >6}
                    {assign var=NUMBER_HIDE_COMMENTS value=6}
                {/if}
                {for $foo=1 to $NUMBER_HIDE_COMMENTS}
                    <div class="divWithBorderBottom"></div>
                {/for}
            </div>
        {/if}
        {assign var=HIDE_COMMENTS value=0}
        {if $CHK_HIDE_COMMENTS}
            {if $COUNTER >3 &&  $COUNTER<=$COUNT_TOTAL-3}
                {assign var=HIDE_COMMENTS value=1}
            {/if}
        {/if}
        <div class="commentDetails {if $HIDE_COMMENTS==1}hide{/if}">
            <div class="singleComment" >
                <input type="hidden" name='is_private' value="{$COMMENT->get('is_private')}">
                {assign var=PARENT_COMMENT_MODEL value=$COMMENT->getParentCommentModel()}
                {assign var=CHILD_COMMENTS_MODEL value=$COMMENT->getChildComments()}
                {assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
                {assign var=CREATOR_NAME value={decode_html($COMMENT->getCommentedByName())}}
                {assign var=FILE_DETAILS value=$COMMENT->getFileNameAndDownloadURL()}
                {assign var=PICKLIST_COLOR value=$COMMENT->get('color')}
                {if $COMMENT->get('color')==''}
                {assign var=PICKLIST_COLOR value=$COMMENT->get('color_picklist_two')}
                {/if}
                <div class="row">
                    <div class="">
                        <div class="media" style="background-color: {$PICKLIST_COLOR}">
                            <div style="padding: 10px 0px;margin: 0 20px;">
                                <div class="media-left title">
                                    <div class="col-lg-2 recordImage commentInfoHeader" data-commentid="{$COMMENT->getId()}" data-parentcommentid="{$COMMENT->get('parent_comments')}" data-relatedto = "{$COMMENT->get('related_to')}"
                                         style="{if $COMMENT->get('userid')}background:none {else} background-color:#{$MODULE_MODEL->stringToColorCode({$CREATOR_NAME})}{/if} ;display: inline-table;overflow-y: hidden;padding: 0;justify-content: center;align-items: center;border-radius: 5%!important;border: none;height: auto;width: auto;">
                                        {assign var=IMAGE_PATH value=$COMMENT->getImagePath()}
                                        {if !empty($IMAGE_PATH) && ($COMMENT->get('customer')>0 || $COMMENT->get('userid')>0)}
                                            {if $IMAGE_PATH !='layouts/v7/skins/images/CustomerPortal.png'}
                                                <img src="{$IMAGE_PATH}" width="40px" height="40px" align="left" {if $COMMENT->get('userid')}style="border-radius: 50%!important;"{/if}>
                                            {else}
                                                <div class="name">
                                                    <span class=" userImage-md " style="border-radius: 5%!important;background: none repeat scroll 0 0 #{$MODULE_MODEL->stringToColorCode({$CREATOR_NAME})};">
                                                        <span style="color: #FFF;display: inline-block;font-size: 16px!important;line-height: 40px;"> {$CREATOR_NAME|mb_substr:0:2|escape:"html"} </span></span></div>
                                            {/if}
                                        {elseif $COMMENT->get('related_email_id')>0 && $COMMENT->get('userid')==0}
                                            {assign var=EMAIL_ADD value={$MODULE_MODEL->getEmailInfo({$COMMENT->get('related_email_id')})}}
                                            <div class="name">
                                                <span class=" userImage-md " style="border-radius: 5%!important;background: none repeat scroll 0 0 #{$MODULE_MODEL->stringToColorCode({$EMAIL_ADD})};">
                                                    <span style="color: #FFF;display: inline-block;font-size: 16px!important;line-height: 40px;"> {$EMAIL_ADD|mb_substr:0:2|escape:"html"} </span>
                                                </span>
                                            </div>
                                        {else}
                                            <div class="name">
                                                <span class=" userImage-md " style="border-radius: 50%!important;background: none repeat scroll 0 0 #{$MODULE_MODEL->stringToColorCode({$CREATOR_NAME})};">
                                                    <span style="color: #FFF;display: inline-block;font-size: 16px!important;line-height: 40px;"> {$CREATOR_NAME|mb_substr:0:2|escape:"html"} </span>
                                                </span>
                                            </div>
                                        {/if}
                                    </div>
                                </div>
                                {assign var=EXPAND value=0}
                                <div class="media-body" style="width:100%">
                                    <div class="comment" style="line-height:1;">
                                        <div class="interactorDetails col-lg-12 col-md-12 p-x-0" {if $EXPAND =='0'}style="cursor: pointer;"{/if}>
                                                    <span class="pull-left">
                                                        {if $COMMENT->get('related_email_id')>0 && $COMMENT->get('userid')==0}
                                                            <strong style="color: #0070D2;">{$MODULE_MODEL->getEmailInfo({$COMMENT->get('related_email_id')})}</strong>
                                                        {else}
                                                            <strong>{$MODULE_MODEL->sentence_case({$CREATOR_NAME})}</strong>
                                                        {/if}
                                                    </span>
                                            {if $ROLLUP_STATUS and ($COMMENT->get('module') ne $MODULE_NAME or $COMMENT->get('related_to') ne $PARENT_RECORD)}
                                                {assign var=SINGULR_MODULE value='SINGLE_'|cat:$COMMENT->get('module')}
                                                {assign var=ENTITY_NAME value=getEntityName($COMMENT->get('module'), array($COMMENT->get('related_to')))}
                                                <span class="text-muted wordbreak display-inline-block">
                                                            {vtranslate('LBL_ON','Vtiger')}&nbsp;
                                                    {vtranslate($SINGULR_MODULE,$COMMENT->get('module'))}&nbsp;
                                                            <a href="index.php?module={$COMMENT->get('module')}&view=Detail&record={$COMMENT->get('related_to')}">
                                                                {$ENTITY_NAME[$COMMENT->get('related_to')]}
                                                            </a>
                                                        </span>&nbsp;&nbsp;
                                            {/if}

                                            <span class="pull-right">
                                                        <span class="text-muted" style="opacity: 0.5;padding-right: 10px;">{if $FILE_DETAILS|@count gt 0}{$FILE_DETAILS|@count} <i class="fa fa-paperclip"></i>&nbsp;{/if} {if $COMMENT->get('related_email_id') || $COMMENT->get('customer')}<i class="fa fa-envelope"></i>{else}<i class="fa fa-comment"></i>{/if}</span>
                                                        <span class="commentTime text-muted cursorDefault">
                                                        <span title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getCommentedTime())}">{Vtiger_Util_Helper::convertDateTimeIntoUsersDisplayFormat($COMMENT->getCommentedTime())} ({Vtiger_Util_Helper::formatDateDiffInStrings($COMMENT->getCommentedTime())})</span>
                                                    </span>
                                                     </span>
                                        </div>
                                        <div class="col-lg-12 col-md-12 col-sm-12" style="padding-top: 5px;">
                                            {if $EXPAND =='0'}
                                            <div class="textshortComment  display-block" style="cursor: pointer;">
                                                <div style="line-height: 18px;text-align: justify;">
                                                    {assign var=SHORT_COMMENT_CONTENT value={$COMMENT->get('commentcontent')}}
                                                    {assign var=SHORT_COMMENT_CONTENT value=preg_replace('/<\s*head.+?<\s*\/\s*head.*?>/si',"", $SHORT_COMMENT_CONTENT)}
                                                    {assign var=SHORT_COMMENT_CONTENT value=preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si',"", $SHORT_COMMENT_CONTENT)}
                                                    {assign var=SHORT_COMMENT_CONTENT value=preg_replace('/<\s*xml.+?<\s*\/\s*xml.*?>/si',"", $SHORT_COMMENT_CONTENT)}
                                                    {assign var=SHORT_COMMENT_CONTENT value={decode_html($SHORT_COMMENT_CONTENT)}}
                                                    {assign var=SHORT_COMMENTCONTENT value=preg_replace('/<\s*head.+?<\s*\/\s*head.*?>/si',"", $SHORT_COMMENT_CONTENT)}
                                                    {assign var=SHORT_COMMENTCONTENT value=preg_replace('/<\s*style.+?<\s*\/\s*style.*?>/si',"", $SHORT_COMMENTCONTENT)}
                                                    {assign var=SHORT_COMMENTCONTENT value=preg_replace('/<\s*xml.+?<\s*\/\s*xml.*?>/si',"", $SHORT_COMMENTCONTENT)}
                                                    {assign var=SHORT_COMMENTCONTENT value=preg_replace('/<\s*span class="vteuserid".+?<\s*\/\s*span.*?>/si',"", $SHORT_COMMENTCONTENT)}
                                                    {assign var=DISPLAYCONTENT value={$SHORT_COMMENTCONTENT|strip_tags}}
                                                    {*{if $DISPLAYCONTENT|count_characters:true gt 200}*}
                                                    {if $TEXT_LENGTH}
                                                        {mb_substr(trim($DISPLAYCONTENT),0,$TEXT_LENGTH)}...
                                                    {else}
                                                        {mb_substr(trim($DISPLAYCONTENT),0,100)}...
                                                    {/if}
                                                </div>
                                                {if $ALWAYS_SHOW ==1}
                                                <div class="col-lg-5"> </div>
                                                <div class="col-lg-6 pull-left">
                                                    <span class="pick_list_value_in_comment" style="color: #000; float: left; position: relative;margin-right: 11px;">
                                                    {foreach item =FIELD_NAME from=$ADD_FIELDS}
                                                        {assign var=FIELD_MODEL value=$ALL_FIELDS.$FIELD_NAME}
                                                        {assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
                                                        {if $COMMENT->get($FIELD_NAME)}
                                                        <span style="padding-right: 20px;">{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}:&nbsp;<strong>{$COMMENT->get($FIELD_NAME)}</strong></label></span>
                                                        {/if}
                                                    {/foreach}
                                                    </span>
                                                </div>
                                                {/if}
                                            </div>
                                            <div class="comment-details-wrapper commentBackground" style="display: none;">
                                            {else}
                                            <div class="comment-details-wrapper commentBackground">
                                            {/if}
                                                <div class="commentInfoContentBlock">
                                                    {assign var=COMMENT_CONTENT value={nl2br($COMMENT->get('commentcontent'))}}
                                                    {if $COMMENT_CONTENT}
                                                        {assign var=DISPLAYNAME value={$COMMENT->get('commentcontent')|escape:"html"}}
                                                        {assign var=DISPLAYNAME value=preg_replace('/head.*\/head/si',"input type='hidden'", $DISPLAYNAME)}
                                                        {assign var=DISPLAYNAME value=preg_replace('/style.*\/style/si',"input type='hidden'", $DISPLAYNAME)}
                                                        {assign var=DISPLAYNAME value={nl2br($DISPLAYNAME)}}
                                                        <span class="commentInfoContent" id="commentInfoContent_{$COMMENT->getId()}" style="display: block" data-fullComment="{$DISPLAYNAME}" data-shortComment="" data-more='{vtranslate('LBL_SHOW_MORE',$MODULE)}' data-less='{vtranslate('LBL_SHOW',$MODULE)} {vtranslate('LBL_LESS',$MODULE)}'>
                                                        </span>
                                                    {/if}
                                                </div>

                                                {foreach key=index item=FILE_DETAIL from=$FILE_DETAILS}
                                                    {assign var="FILE_NAME" value=$FILE_DETAIL['trimmedFileName']}
                                                    {if !empty($FILE_NAME)}
                                                        <div class="commentAttachmentName">
                                                            <div class="filePreview clearfix">
                                                                <span class="fa fa-paperclip cursorPointer" ></span>&nbsp;&nbsp;
                                                                <a class="previewfile" onclick="Vtiger_Detail_Js.previewFile(event,{$COMMENT->get('id')},{$FILE_DETAIL['attachmentId']});" data-filename="{$FILE_NAME}" href="javascript:void(0)" name="viewfile">
                                                                    <span title="{$FILE_DETAIL['rawFileName']}" style="line-height:1.5em;">{$FILE_NAME}</span>&nbsp
                                                                </a>&nbsp;
                                                                <a name="downloadfile1" href="{$FILE_DETAIL['url']}">
                                                                    <i title="{vtranslate('LBL_DOWNLOAD_FILE',$MODULE_NAME)}" class="fa fa-download alignMiddle" ></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    {/if}
                                                {/foreach}
                                                &nbsp;
                                                <div class="commentActionsContainer col-lg-5" id="commentActionsContainer_{$COMMENT->getId()}" style="margin-top: 2px;">
                                                        <span>
                                                        {if $PARENT_COMMENT_MODEL neq false or $CHILD_COMMENTS_MODEL neq null}
                                                            <a href="javascript:void(0);" class="cursorPointer detailViewThread">{vtranslate('LBL_VIEW_THREAD',$MODULE_NAME)}</a>&nbsp;&nbsp;
                                                        {/if}
                                                        </span>
                                                    <span class="summarycommemntActionblock" >
                                                        {if $IS_CREATABLE}
                                                            {if $SOURCE_MODULE_NAME !='HelpDesk'}
                                                            {if $PARENT_COMMENT_MODEL neq false or $CHILD_COMMENTS_MODEL neq null}<span>&nbsp;|&nbsp;</span>{/if}
                                                            <a href="javascript:void(0);" class="cursorPointer replyComment feedback" style="color: blue;">
                                                                {vtranslate('LBL_REPLY',$MODULE_NAME)}
                                                            </a>
                                                            {/if}
                                                        {/if}
                                                        {if (($CURRENTUSER->getId() eq $COMMENT->get('userid') && $IS_EDITABLE) || ($CURRENTUSER->isAdminUser() && $COMMENT->get('related_email_id')<=0)) }
                                                            {if $IS_CREATABLE}&nbsp;&nbsp;&nbsp;{/if}
                                                            <a href="javascript:void(0);" class="cursorPointer editComment feedback" style="color: blue;">
                                                                {vtranslate('LBL_EDIT',$MODULE_NAME)}
                                                            </a>
                                                            &nbsp;{*<span>|</span>&nbsp;
                                                            <a class="cursorPointer deleteCommentWidget feedback">
                                                                {vtranslate('LBL_DELETE',$MODULE_NAME)}
                                                            </a>*}
                                                        {/if}
                                                        </span>
                                                    <span class="" style="margin-left: 5px;"><a href="modules/VTEComments/ExportPDF.php?id={$COMMENT->getId()}&amp;record={$PARENT_RECORD}" target="_blank" class="cursorPointer printComment feedback" style="color: blue;">Print</a></span>
                                                    {if $COMMENT->getCommentedTime() neq $COMMENT->getModifiedTime() && REASON_TO_EDIT}
                                                        <span style="margin-left: 5px;" >
                                                                <a class="cursorPointer lblPreviousComment" style="color: blue;" data-commentid="{$COMMENT->getId()}">View Previous Version</a>
                                                            </span>
                                                    {/if}

                                                </div>
                                                <div class="col-lg-6">
                                                        <span class="pick_list_value_in_comment" style="color: #000; float: left; position: relative;margin-right: 11px;">
                                                        {foreach item =FIELD_NAME from=$ADD_FIELDS}
                                                            {assign var=FIELD_MODEL value=$ALL_FIELDS.$FIELD_NAME}
                                                            {assign var=FIELD_NAME value=$FIELD_MODEL->get('name')}
                                                            {if $COMMENT->get($FIELD_NAME)}
                                                            <span style="padding-right: 20px;">{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}:&nbsp;<strong>{$COMMENT->get($FIELD_NAME)}</strong></label></span>
                                                            {/if}
                                                        {/foreach}
                                                        </span>
                                                </div>
                                                {if $COMMENT->getCommentedTime() neq $COMMENT->getModifiedTime()}
                                                    {assign var="REASON_TO_EDIT" value=$COMMENT->get('reasontoedit')}
                                                    {if REASON_TO_EDIT}
                                                        {*<br>
                                                        <div class="row commentEditStatus marginBottom10px" name="editStatus">
                                                            <span class="col-lg-5 col-md-5 col-sm-5 hide">
                                                                <small>{vtranslate('LBL_EDIT_REASON',$MODULE_NAME)} : <span name="editReason" class="textOverflowEllipsis">{nl2br($REASON_TO_EDIT)}</span></small>
                                                            </span>
                                                                <span {if $REASON_TO_EDIT}class="col-lg-7 col-md-7 col-sm-7"{/if}>
                                                                    <p class="text-muted pull-right">
                                                                        <small><em>{vtranslate('LBL_MODIFIED',$MODULE_NAME)}</em></small>&nbsp;
                                                                        <small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getModifiedTime())}" class="commentModifiedTime">{Vtiger_Util_Helper::formatDateDiffInStrings($COMMENT->getModifiedTime())}</small>
                                                                    </p>
                                                                </span>
                                                        </div>*}
                                                    {/if}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            {if $index+1 neq $COMMENTS_COUNT}
                <hr class="row" style="margin-top:0; margin-bottom: 0;">
            {/if}
        </div>
        {/foreach}
        {else}
        {include file="NoComments.tpl"|@vtemplate_path}
        {/if}
        {if $ORDER_BY =='ASC'}
            {if $SOURCE_MODULE_NAME =='HelpDesk'}
                <div class="col-lg-12 marginTop10px formFooter interactionFooter">
                    <div class="pull-right row show" id="replyInfo">
                        <button id="vteSearchCommentButton" type="button" data-pupup-visible="false" class="p-o-btn vteSearchCommentButton"><i class="fa fa-search" aria-hidden="true"></i></button>
                        <button type="button" class="p-o-btn active" id="commentTextArea"><i class="fa fa-comment"></i> Add comment</button>
                        <button type="button" class="p-btn" id="composeEmailForm"><i class="fa fa-reply"></i> Reply</button>
                    </div>
                </div>
                <div class="replyArea hide" >
                    <div class="" id="composeEmailContainer">

                    </div>
                </div>

                {literal}
                <script>
                    var ele = $('.widgetContainer_comments div.widget_header');
                    var hasWHeaderR = ele.find('.w_header_r');
                    if(hasWHeaderR.length==0) {
                        ele.append('<div class="col-lg-8 pull-right w_header_r"><div class="w_header"></div></div>');
                        $(".commentHeader").insertAfter('.w_header').css({'padding-top':'0px'});
                        $(".commentHeader").removeClass("col-lg-5").addClass("col-lg-8");
                    }
                    $(".recentComments").css({'margin-top':'25px'});
                    if(jQuery('#rollupcomments').attr('rollup-status') == 1) {
                        jQuery('#rollupcomments').bootstrapSwitch('state', true, true);

                    }else{
                        jQuery('#rollupcomments').bootstrapSwitch('state', false, true);
                    }
                    $( "#is_private").prop('checked', true);
                    $(".recentCommentsHeader").addClass("hide");
                    $(".basicAddCommentBlock").insertAfter('.commentsBody');
                    $(".basicEditCommentBlock").insertAfter('.commentsBody');
                    $(".commentTitle").insertAfter('.commentsBody');
                    $(".commentTitle").addClass("hide");
                    $( "#is_private").prop('checked', true);
                    $("#widgetPicklistComments").insertAfter('.addCommentBlock');
                    $(".hidWidgetPicklistComments").remove();

                    jQuery('body').delegate('#commentTextGoBack,#commentReplyGoBack','click',function(){
                        $(".commentTitle").removeClass("show").addClass("hide");
                        $(".replyArea").removeClass("show").addClass("hide");
                        $("#replyInfo").removeClass("hide").addClass("show");
                        var eleShowckeditor = jQuery('.showckeditor');
                        if(eleShowckeditor.length==0) {
                            var btnCancel = '<input type="hidden" value="1" name="showckeditor" class="showckeditor"/>';
                            $("#replyInfo").before(btnCancel);
                        }
                        $(".commentTextGoBack").remove();
                        var div_advancecomment = $('.cke_editor_vteAdvanceComment');
                        div_advancecomment.remove();
                        var noteContentElement = jQuery('[name="commentcontent"]');
                        noteContentElement.css({'visibility': '', 'display': ''});
                        jQuery('[name="showckeditor"]').val(1);
                    });
                    jQuery('#composeEmailForm').on('click', function(e){
                        $("#replyInfo").removeClass("show").addClass("hide");
                        $(".replyArea").removeClass("hide").addClass("show");
                    });
                    jQuery('body').delegate('#moreInteractions','click',function(){
                        jQuery("#moreInteractions").hide();
                        $(".commentDetails").removeClass("hide").addClass("show");
                    });
                </script>
            {/literal}
            {else}
            {literal}
                <script>
                    var ele = $('.widgetContainer_comments div.widget_header');
                    var hasWHeaderR = ele.find('.w_header_r');
                    if(hasWHeaderR.length==0) {
                        ele.append('<div class="col-lg-8 pull-right w_header_r"><div class="w_header"></div></div>');
                        $(".commentHeader").insertAfter('.w_header').css({'padding-top': '0px'});
                        $(".commentHeader").removeClass("col-lg-5").addClass("col-lg-8");
                    }
                    $(".recentComments").css({'margin-top':'25px'});
                    if(jQuery('#rollupcomments').attr('rollup-status') == 1) {
                        jQuery('#rollupcomments').bootstrapSwitch('state', true, true);

                    }else{
                        jQuery('#rollupcomments').bootstrapSwitch('state', false, true);
                    }
                    $( "#is_private").prop('checked', true);
                    $(".recentCommentsHeader").addClass("hide");
                    var btnSearch = '<button id="vteSearchCommentButton" type="button" data-pupup-visible="false" class="p-o-btn vteSearchCommentButton"><i class="fa fa-search" aria-hidden="true"></i></button>';
                    var eleSearchButton =  $('.commentHeader').find('#vteSearchCommentButton');
                    if (eleSearchButton.length==0)
                        $('.commentHeader').find('.display-inline-block').before(btnSearch);
                    $(".basicAddCommentBlock").insertAfter('.commentsBody');
                    $(".basicEditCommentBlock").insertAfter('.commentsBody');
                    var item = $(".commentTitle");
                    item.insertAfter('.commentsBody');
                    $("#widgetPicklistComments").insertAfter('.addCommentBlock');
                    $(".hidWidgetPicklistComments").remove();
                    jQuery('body').delegate('#moreInteractions','click',function(){
                        jQuery("#moreInteractions").hide();
                        $(".commentDetails").removeClass("hide").addClass("show");
                    });
                </script>
            {/literal}
            {/if}
        {/if}
    </div>
    <div class="hide basicAddCommentBlock container-fluid" style="min-height: 110px;">
        <div class="commentTextArea row">
            <textarea name="commentcontent" class="commentcontent col-lg-12" placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
        </div>
        <div class="pull-right row">
            {if in_array($MODULE_NAME, $PRIVATE_COMMENT_MODULES)}
                <div class="checkbox">
                    <label>
                        <input type="checkbox" id="is_private">&nbsp;&nbsp;{vtranslate('LBL_INTERNAL_COMMENT')}&nbsp;&nbsp;
                    </label>
                </div>
            {/if}
            <button class="btn btn-success btn-sm saveComment" type="button" data-mode="add">{vtranslate('LBL_POST', $MODULE_NAME)}</button>
            <a href="javascript:void(0);" class="cursorPointer closeCommentBlock cancelLink" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
        </div>
    </div>

    <div class="hide basicEditCommentBlock container-fluid" style="min-height: 150px;">
        <div class="row commentArea" >
            <input style="width:100%;height:30px;" type="text" name="reasonToEdit" placeholder="{vtranslate('LBL_REASON_FOR_CHANGING_COMMENT', $MODULE_NAME)}" class="input-block-level"/>
        </div>
        <div class="row" style="padding-bottom: 10px;">
            <div class="commentTextArea">
                <textarea name="commentcontent" class="commentcontenthidden col-lg-12" placeholder="{vtranslate('LBL_ADD_YOUR_COMMENT_HERE', $MODULE_NAME)}" rows="{$COMMENT_TEXTAREA_DEFAULT_ROWS}"></textarea>
            </div>
        </div>
        <input type="hidden" name="is_private">
        <div class="pull-right row">
            <button class="btn btn-success btn-sm saveComment" type="button" data-mode="edit">{vtranslate('LBL_POST', $MODULE_NAME)}</button>
            <a href="javascript:void(0);" class="cursorPointer closeCommentBlock cancelLink" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
        </div>
    </div>
{/strip}