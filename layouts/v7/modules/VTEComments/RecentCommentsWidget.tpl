
{strip}
    <style type="text/css">
        .commentInfoContentBlock img{
            width: 80% !important;height: auto!important;
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
            cursor: pointer; margin-bottom: 2px; position: relative; padding: 0px; border-top: none; border-bottom: none;
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
            <div class="row" id="widgetPicklistComments" style="margin-top: 15px;">
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
                    <div class="col-lg-6">
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
                    var wheaderR = ele.find('.w_header_r');
                    if(wheaderR.length==0) {
                        ele.append('<div class="col-lg-8 pull-right w_header_r"><div class="w_header"></div></div>');
                        $(".commentHeader").insertAfter('.w_header').css({'padding-top':'0px'});
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
                    $(".recentCommentsHeader").addClass("hide");
                    $(".commentTitle").addClass("hide");
                    $("#widgetPicklistComments").remove();
                    $("#widgetPicklistComments").insertAfter('.addCommentBlock');
                    $(".hidWidgetPicklistComments").remove();
                    jQuery('#commentTextArea').on('click', function(e){
                        $("#replyInfo").removeClass("show").addClass("hide");
                        $(".commentTitle").removeClass("hide").addClass("show");
                        $("#commentTextGoBack").remove();
                        var btnCancel = '<button class="cancelLink t-btn-sm btn btn-success btn-sm" type="button" id="commentTextGoBack">Cancel</button>';
                        $(".detailViewSaveComment,.saveComment").before(btnCancel);
                    });
                    jQuery('body').delegate('#commentTextGoBack,#commentReplyGoBack','click',function(){
                        $(".commentTitle").removeClass("show").addClass("hide");
                        $(".replyArea").removeClass("show").addClass("hide");
                        $("#replyInfo").removeClass("hide").addClass("show");
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
                {foreach key=index item=COMMENT from=$COMMENTS}
                    <div class="commentDetails">
                        <div class="singleComment" >
                            <input type="hidden" name='is_private' value="{$COMMENT->get('is_private')}">
                            {assign var=PARENT_COMMENT_MODEL value=$COMMENT->getParentCommentModel()}
                            {assign var=CHILD_COMMENTS_MODEL value=$COMMENT->getChildComments()}
                            {assign var=COMMENTOR value=$COMMENT->getCommentedByModel()}
                            <div class="row">
                                <div class="">
                                    <div class="media" style="background-color: {if $COMMENT->get('color')}{$COMMENT->get('color')}{else}{$COMMENT->get('color_picklist_two')}{/if}">
                                        <div style="padding: 10px 0px;margin: 0 20px;">
                                            <div class="media-left title">
                                                <div class="col-lg-2 recordImage commentInfoHeader" data-commentid="{$COMMENT->getId()}" data-parentcommentid="{$COMMENT->get('parent_comments')}" data-relatedto = "{$COMMENT->get('related_to')}">
                                                    {assign var=IMAGE_PATH value=$COMMENT->getImagePath()}
                                                    {if !empty($IMAGE_PATH)}
                                                        <img src="{$IMAGE_PATH}" width="40px" height="40px" align="left">
                                                    {else}
                                                        {assign var=CREATOR_NAME  value=$COMMENTOR->getName()}
                                                        <div class="name"><span><strong> {$CREATOR_NAME|mb_substr:0:2|escape:"html"} </strong></span></div>
                                                    {/if}
                                                </div>
                                            </div>
                                            <div class="media-body" style="width:100%">
                                                <div class="comment" style="line-height:1;">
                                                <span class="creatorName">
                                                    {$CREATOR_NAME}
                                                </span>
                                                    &nbsp;&nbsp;&nbsp;&nbsp;{*<a id="{$COMMENT->getId()}" class="btn_show_hide_comment_content btn btn-xs" style="font-size: 10px; border: 0px; background-color: #fff;"><i class="caret"></i> Hide</a>*}
                                                    &nbsp;&nbsp;
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
                                                    <span class="commentTime text-muted cursorDefault">
                                                        <small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getCommentedTime())}">{Vtiger_Util_Helper::formatDateDiffInStrings($COMMENT->getCommentedTime())}</small>
                                                    </span>

                                                    <div class="commentInfoContentBlock">
                                                        {if $COMMENT->get('module') eq 'Cases' and !$COMMENT->get('is_private')}
                                                            {assign var=COMMENT_CONTENT value={decode_html($COMMENT->get('commentcontent'))}}
                                                        {else}
                                                            {assign var=COMMENT_CONTENT value={nl2br($COMMENT->get('commentcontent'))}}
                                                        {/if}
                                                        {if $COMMENT_CONTENT}
                                                            {assign var=DISPLAYNAME value={decode_html($COMMENT_CONTENT)}}
                                                            <span data-maxlength="200" class="commentInfoContent" id="commentInfoContent_{$COMMENT->getId()}" style="display: block" data-fullComment="{$COMMENT_CONTENT|escape:"html"}" data-shortComment="{$DISPLAYNAME|mb_substr:0:200|escape:"html"}..." data-more='{vtranslate('LBL_SHOW_MORE',$MODULE)}' data-less='{vtranslate('LBL_SHOW',$MODULE)} {vtranslate('LBL_LESS',$MODULE)}'>

                                                            </span>
                                                        {/if}
                                                    </div>
                                                    {assign var="FILE_DETAILS" value=$COMMENT->getFileNameAndDownloadURL()}
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
                                                                        <i title="{vtranslate('LBL_DOWNLOAD_FILE',$MODULE_NAME)}" class=" fa fa-download alignMiddle" ></i>
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
                                                            {if $PARENT_COMMENT_MODEL neq false or $CHILD_COMMENTS_MODEL neq null}<span>&nbsp;|&nbsp;</span>{/if}
                                                            <a href="javascript:void(0);" class="cursorPointer replyComment feedback" style="color: blue;">
                                                                {vtranslate('LBL_REPLY',$MODULE_NAME)}
                                                            </a>
                                                        {/if}
                                                                {if $CURRENTUSER->getId() eq $COMMENT->get('userid') && $IS_EDITABLE}
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
                                                    {if $COMMENT->getCommentedTime() neq $COMMENT->getModifiedTime()}
                                                        <br>
                                                        <div class="row commentEditStatus marginBottom10px" name="editStatus">
                                                            {assign var="REASON_TO_EDIT" value=$COMMENT->get('reasontoedit')}
                                                            {if REASON_TO_EDIT}
                                                                <span class="col-lg-5 col-md-5 col-sm-5 hide">
                                                        <small>{vtranslate('LBL_EDIT_REASON',$MODULE_NAME)} : <span name="editReason" class="textOverflowEllipsis">{nl2br($REASON_TO_EDIT)}</span></small>
                                                    </span>
                                                            {/if}
                                                            <span {if $REASON_TO_EDIT}class="col-lg-7 col-md-7 col-sm-7"{/if}>
                                                            <p class="text-muted pull-right">
                                                                <small><em>{vtranslate('LBL_MODIFIED',$MODULE_NAME)}</em></small>&nbsp;
                                                                <small title="{Vtiger_Util_Helper::formatDateTimeIntoDayString($COMMENT->getModifiedTime())}" class="commentModifiedTime">{Vtiger_Util_Helper::formatDateDiffInStrings($COMMENT->getModifiedTime())}</small>
                                                            </p>
                                                        </span>

                                                        </div>
                                                    {/if}
                                                    <div class="row marginBottom10px">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <p class="text-muted">
                                                                <small>
                                                                    <span name="editReason" class="wordbreak">{nl2br($REASON_TO_EDIT)}</span>
                                                                </small>
                                                            </p>
                                                        </div>
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
                        <hr style="margin-top:0; margin-bottom: 0px;">
                    {/if}
                {/foreach}
            </div>
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
                    }
                    $(".recentComments").css({'margin-top':'25px'});
                    if(jQuery('#rollupcomments').attr('rollup-status') == 1) {
                        jQuery('#rollupcomments').bootstrapSwitch('state', true, true);

                    }else{
                        jQuery('#rollupcomments').bootstrapSwitch('state', false, true);
                    }
                    $( "#is_private").prop('checked', true);
                    $(".recentCommentsHeader").addClass("hide");
                    $(".commentTitle").insertAfter('.recentCommentsBody');
                    $(".commentTitle").addClass("hide");
                    $( "#is_private").prop('checked', true);
                    $("#widgetPicklistComments").insertAfter('.addCommentBlock');
                    $(".hidWidgetPicklistComments").remove();
                    jQuery('#commentTextArea').on('click', function(e){
                        $("#replyInfo").removeClass("show").addClass("hide");
                        $(".commentTitle").removeClass("hide").addClass("show");
                        $("#commentTextGoBack").remove();
                        var btnCancel = '<button class="cancelLink t-btn-sm btn btn-success btn-sm" type="button" id="commentTextGoBack">Cancel</button>';
                        $(".detailViewSaveComment,.saveComment").before(btnCancel);
                    });
                    jQuery('body').delegate('#commentTextGoBack,#commentReplyGoBack','click',function(){
                        $(".commentTitle").removeClass("show").addClass("hide");
                        $(".replyArea").removeClass("show").addClass("hide");
                        $("#replyInfo").removeClass("hide").addClass("show");
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
                    var item = $(".commentTitle");
                    item.insertAfter('.recentCommentsBody');
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

        {literal}
            <script>
                var div_advancecomment = $('#cke_vteAdvanceComment');
                div_advancecomment.remove();
                var commentList = $('.commentDetails');
                for(var i=0;i < commentList.length; i++){
                    var curComment = jQuery(commentList[i]).find('.commentInfoContent');
                    var fullcomment = curComment.attr("data-fullcomment");
                    if(typeof fullcomment == 'undefined'){
                        fullcomment = curComment.html();
                        curComment.attr("data-fullcomment", fullcomment);
                    }
                    if(typeof fullcomment != 'undefined'){
                        $(curComment).html($("<div />").html(fullcomment).text());
                    }else {
                        $(curComment).html($("<div />").html($(curComment).html()).text());
                    }
                }
            </script>
        {/literal}

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
                <button class="btn btn-success btn-sm detailViewSaveComment" type="button" data-mode="add">{vtranslate('LBL_POST', $MODULE_NAME)}</button>
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
                <button class="btn btn-success btn-sm detailViewSaveComment" type="button" data-mode="edit">{vtranslate('LBL_POST', $MODULE_NAME)}</button>
                <a href="javascript:void(0);" class="cursorPointer closeCommentBlock cancelLink" type="reset">{vtranslate('LBL_CANCEL', $MODULE_NAME)}</a>
            </div>
        </div>
    </div>
{/strip}