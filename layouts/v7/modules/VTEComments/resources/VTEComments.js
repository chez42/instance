/* ********************************************************************************
 * The content of this file is subject to the Comments (Advanced) ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEComments_Js",{
    showckeditor: true,
    instance:false,
    getInstance: function(){
        if(VTEComments_Js.instance == false){
            var instance = new VTEComments_Js();
            VTEComments_Js.instance = instance;
            return instance;
        }
        return VTEComments_Js.instance;
    },
    registerEventsForCancelEditComment: function(){
        jQuery(document).ajaxComplete(function(event, xhr, settings){
            jQuery('.cancelLink').on('click',function(){
                var block_PickListforEditComment = $('.block_PickListforEditComment');
                block_PickListforEditComment.remove();
            });
        });
    },
    registerEventsForCancelReplyComment: function(){
        var self = this;
        jQuery('.cancelLink').on('click',function(){
            var block_PickList = $('.block_modulePickList');
            var blockTextField = $('.blockTextField');
            var postCommentContainer_vte_widget = jQuery('div.customwidgetContainer_ div.widget_contents div.commentContainer div.commentTitle div.addCommentBlock div:nth-child(2) div.pull-right > div:nth-child(1)');
            var postCommentContainer = jQuery('div.commentContainer div.commentTitle div.addCommentBlock div:nth-child(2) div.pull-right > div:nth-child(1)');
            if(postCommentContainer_vte_widget.length){
                postCommentContainer_vte_widget.append(block_PickList);
            }else{
                postCommentContainer.append(block_PickList);
                var postCommentForTextField = jQuery('div.commentContainer div.commentTitle div.addCommentBlock div:nth-child(2) div.pull-left > div.fileUploadContainer');
                blockTextField.attr('style','float: right;position: relative;top: -22px;margin-right: 39px;');
                postCommentForTextField.append(blockTextField);
            }
        });
    },
    registerEventsForEditComment :function(){
        var self = this;
        jQuery('body').delegate('.editComment','click',function(){
            var singleComment = $(this).closest('div.singleComment');
            var addPicklistTo = singleComment.find('div.addCommentBlock div.pull-right');
            var block_PickListforEditComment = $('.block_PickListforEditComment');
            block_PickListforEditComment.remove();
            var comment_id = singleComment.find('.commentInfoHeader')[0].attributes['data-commentid'].value;
            var options ={id:'PickListforEditComment',selected_value:'',pick_type:'1',comment_id:comment_id};
            self.registerEventAddTextFieldForEditBlock(addPicklistTo,comment_id);
            self.registerAddPicklist(addPicklistTo,options);
            jQuery('#update_picklist_value_to_comment').val(0);
            self.doCheckEnableRichText(singleComment,comment_id);
        });
    },
    registerEventsForViewThreadComment :function(){
        var self = this;
        jQuery('body').delegate('a.viewThread','click',function(){
            //self.ChangeCommentContentAndColor();
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
        });
    },
    registerEventsForReplyComment :function(){
        var self = this;
        jQuery('body').delegate('.replyComment','click',function(){
            var picklist = $('.block_modulePickList');
            var blockTextField = $('.blockTextField');
            blockTextField.attr('style','float: left;margin-right: 5px;');
            //blockTextField.find('input[name="inputTextField"]').attr('style','width: 100px;')
            var singleComment = $(this).closest('div.singleComment');
            var comment_id = singleComment.find('.commentInfoHeader')[0].attributes['data-commentid'].value;
            var addPicklistTo = singleComment.find('div.addCommentBlock div.pull-right');
            addPicklistTo.append(picklist);
            addPicklistTo.prepend(blockTextField);
            self.registerEventsForCancelReplyComment();
            self.doCheckEnableRichText(singleComment,comment_id,true);
        });
    },
    registerEventsAddInputHidden : function(){
        jQuery('body').append('<input type="hidden" value="" id="post_comment_content_value">');
        jQuery('body').append('<input type="hidden" value="" id="post_comment_picklist_one_value">');
        jQuery('body').append('<input type="hidden" value="" id="post_comment_picklist_two_value">');
        jQuery('body').append('<input type="hidden" value="" id="vte_comment_text_field">');
        jQuery('body').append('<input type="hidden" value="0" id="update_picklist_value_to_comment">');
        jQuery('body').append('<input type="hidden" value="0" id="button_create_click">');
    },

    registerEventAddTextField:function(ele){
        var instance = this;
        var params_ = {};
        params_['action']= 'ActionAjax';
        params_['module'] = 'VTEComments';
        params_['mode'] = 'getTextField';
        app.request.post({data:params_}).then(
            function (err,data) {
                if(err == null) {
                    if(data.presence == 2){
                        var detailContentsHolder = instance.getContentHolder();
                        var inputElement = detailContentsHolder.find('.blockTextField');
                        if (inputElement.length == 0) {
                            ele.append('<div class="blockTextField" style="float: left;margin-top:15px;">' + data.label + ' <input style="width: 100px;" class="inputElement" type="text" value="" name="inputTextField" id="inputTextField"></div>');
                        }
                        $('#inputTextField').on('change',function(){
                            $('#vte_comment_text_field').val(this.value);
                        });
                    }
                }
            }
        );
    },
    registerEventAddTextFieldForEditBlock:function(ele,comment_id){
        var params_ = {};
        params_['action']= 'ActionAjax';
        params_['module'] = 'VTEComments';
        params_['mode'] = 'getTextFieldForEditBlock';
        params_['comment_id'] = comment_id;
        app.request.post({data:params_}).then(
            function (err,data) {
                if(err == null) {
                    var result = data;
                    if(result.textFieldPresence == 2){
                        ele.append('<span style="float: left;">'+result.textFieldLabel+' <input class="inputElement " style="width: 100px;margin-right: 5px;" class="" type="text" value="'+result.textFieldValue+'" name="'+result.textFieldName+'" id="'+result.textFieldName+'"></span>');
                    }
                }
            }
        );
    },

    registerAddPicklist : function(ele,op){
        var params_ = {};
        params_['action']= 'ActionAjax';
        params_['module'] = 'VTEComments';
        params_['mode'] = 'getPicklList';
        params_['op'] = op;
        app.request.post({data:params_}).then(
            function (err,data) {
                if(err == null) {
                    if(op.id == 'modulePickList'){
                        var block_PickList = $('div.block_modulePickList');
                        block_PickList.remove();
                    }else{
                        var block_PickList = $('div.block_PickListforEditComment');
                        block_PickList.remove();
                    }
                    ele.append(data);
                    var picklist_one = jQuery('#'+op.id+'_cf_comment_picklist');
                    var picklist_two = jQuery('#'+op.id+'_cf_comment_picklist_two');
                    vtUtils.showSelect2ElementView(picklist_one);
                    vtUtils.showSelect2ElementView(picklist_two);
                    picklist_one.on('change', function(){
                        val = this.value;
                        jQuery('#post_comment_picklist_one_value').val(val);
                    });
                    picklist_two.on('change', function(){
                        val = this.value;
                        jQuery('#post_comment_picklist_two_value').val(val);
                    });
                }
            }
        );
    },
    /*ChangeCommentContentAndColor : function(){
     var self = this;
     var params = {};
     params['action']= 'ActionAjax';
     params['module'] = 'VTEComments';
     params['mode'] = 'getColorsforComment';
     params['related_to'] = app.getRecordId();
     app.request.post({data:params}).then(
     function (err,data) {
     data = jQuery.parseJSON(data);
     //$('.pick_list_value_in_comment').remove();
     data.forEach(function(value){
     comment_id = value.id;
     comment_createdtime = value.createdtime;
     color = value.color;
     pick_value = value.pick_value;
     textField = value.textField;
     var ele = $('div.commentInfoHeader[data-commentid="'+comment_id+'"]');
     ele = ele.closest('div.media');
     ele.css('background-color',color);
     var elementComment = ele.find('div.media-body div.comment');
     var picklistComment = elementComment.find('.pick_list_value_in_comment');
     if(picklistComment.length==0)
     elementComment.append('<span class="pick_list_value_in_comment" style="color: #000; float: right; position: relative;bottom: 27px;margin-right: 11px;padding-top:20px;"><span style="margin-right: 20px;">'+textField+'</span>'+pick_value+'</span>');
     ele.find('div.media-body div.comment .commentTime small').html(comment_createdtime);
     var actionsContainer = ele.find('div.media-body div.comment div.commentActionsContainer');
     var lblprintComment = actionsContainer.find('.printComment');
     if (lblprintComment.length == 0) {
     var print = '<span class="" style="margin-left: 5px;"><a href="modules/VTEComments/ExportPDF.php?id=' + comment_id + '&record=' + app.getRecordId() + '" target="_blank" class="cursorPointer printComment feedback" style="color: blue;">Print</a></span>';
     ele.find('div.media-body div.comment div.commentActionsContainer').append(print);
     }
     //modified content when edit
     var post_comment = jQuery('#post_comment_content_value');
     var commentContent = post_comment.val();
     if(commentContent !='' && comment_id==post_comment.attr("data-commentid")) {
     var params = {};
     params['action']= 'ActionAjax';
     params['module'] = 'VTEComments';
     params['mode'] = 'DoSendMailUserTaggedComment';
     params['commentid'] = comment_id;
     app.request.post({data:params}).then(
     function (err,data) {
     if(err == null) {
     }
     }
     );
     var commentInfo = ele.find('.commentInfoContent');
     commentInfo.html($("<div />").html(commentContent).html());
     commentContent ='';
     post_comment.val(commentContent);
     }
     });
     }
     );
     },*/
    /*registerEventsChangeCommentContentAndColor : function(){
     var self =this;
     jQuery(document).ajaxComplete(function(event, xhr, settings){
     var url = settings.data;
     if(typeof url == 'undefined' && settings.url) url = settings.url;
     if(Object.prototype.toString.call(url) =='[object String]') {
     if ((url.indexOf('view=DetailAjax') > -1 && url.indexOf('module=ModComments') > -1)
     || (url.indexOf('view=Detail') > -1 && url.indexOf('mode=showRecentComments') > -1)
     || (url.indexOf('relatedModule=ModComments') != -1 && url.indexOf('view=Detail') != -1 && url.indexOf('mode=showRelatedList') != -1)
     || (url.indexOf('module=VTEWidgets') > -1 && url.indexOf('mode=showCommentsWidget') > -1 && url.indexOf('view=SummaryWidget') > -1)) {
     self.ChangeCommentContentAndColor();
     }
     }
     });
     },*/
    /*summaryButtonCreateCommentSubmit : function(){
     var self = this;
     jQuery('button.detailViewSaveComment[data-mode="add"]').on('click', function(){
     $('#button_create_click').val(1);
     jQuery(document).ajaxComplete(function(event, xhr, settings){
     var data = '';
     if(settings.data){
     data = settings.data;
     }
     data = data.toString();
     if((data.indexOf('view=Detail') > -1 && data.indexOf('mode=showRecentComments') > -1)){
     if($('#button_create_click').val() == 1){
     var params = {};
     params['action']= 'ActionAjax';
     params['module'] = 'VTEComments';
     params['mode'] = 'getNewestCommentId';
     app.request.post({data:params}).then(
     function (err,data) {
     if(err == null) {
     var new_comment_id = data;
     var picklist_one_value = jQuery('[name="cf_comment_picklist"]').val();
     var picklist_two_value = jQuery('[name="cf_comment_picklist_two"]').val();
     var vte_comment_text_field = jQuery('[name="cf_comment_text_field"]').val();
     self.updatePicklistValueForComment(new_comment_id,picklist_one_value,picklist_two_value,vte_comment_text_field);
     }
     }
     );
     $('#button_create_click').val(0);
     }
     }
     });
     });
     },
     updatePicklistValueForComment: function(id,picklist_one_value,picklist_two_value,vte_comment_text_field){
     var self = this;
     if(id != jQuery('#update_picklist_value_to_comment').val()){
     var params = {};
     params['action']= 'ActionAjax';
     params['module'] = 'VTEComments';
     params['mode'] = 'updatePicklistValueForComment';
     params['picklist_one_value'] = picklist_one_value;
     params['picklist_two_value'] = picklist_two_value;
     params['vte_comment_text_field'] = vte_comment_text_field;
     params['comment_id'] = id;
     app.request.post({data:params}).then(
     function (err,data) {
     if(err == null) {
     jQuery('#update_picklist_value_to_comment').val(id);
     jQuery('#post_comment_picklist_one_value').val('');
     jQuery('#post_comment_picklist_two_value').val('');
     jQuery('#vte_comment_text_field').val('');
     //self.ChangeCommentContentAndColor();
     var block_PickListforEditComment = $('#block_PickListforEditComment');
     block_PickListforEditComment.remove();
     }
     }
     );
     }
     },*/
    getNewCommentId : function(){
        var comments_header = $('.commentDetails .singleComment div.commentInfoHeader:nth-child(1)');
        var flag = 0;
        comments_header.each(function(key,value){
            commentid = $(value)[0].attributes['data-commentid'].nodeValue;
            flag = Number(flag);
            commentid = Number(commentid);
            if(flag > commentid){
                flag = flag;
            }
            else{
                flag = commentid;
            }
        });
        return flag;
    },
    registerEventsAddShortcutToSettings : function(){
        if(app.getParentModuleName() == 'Settings'){
            var div = $('#LBL_OTHER_SETTINGS');
            var ul = div.find('ul');
            var link = ul.find('li a[data-name="Comments (Advanced)"]');
            if(!link.length){
                ul.append('<li><a data-name="Comments (Advanced)" href="index.php?module=VTEComments&parent=Settings&view=Settings" class="menuItemLabel " target="_blank">Comments (Advanced)<img id="23_menuItem"  class="pinUnpinShortCut cursorPointer pull-right" data-actionurl="" data-pintitle="pin" data-unpintitle="Unpin" data-pinimageurl="layouts/v7/skins/images/pin.png" data-unpinimageurl="layouts/v7/skins/images/unpin.png" title="pin" src="layouts/v7/skins/images/pin.png" data-action="pin"></a></li>');
            }
        }
    },
    registerEventsChosePickListSetting : function(){
        var url = jQuery.url();
        var parent = url.param('parent');
        var view = url.param('view');
        var module = url.param('module');
        var source_module = url.param('source_module');
        var picklist_fieldname = url.param('picklist_fieldname');
        var picklist_value = url.param('picklist_value');
        if(module == 'Picklist' && 'ModComments' == source_module && 'Settings' == parent && 'Index' == view){
            //var ele = vtUtils.showSelect2ElementView($("#modulePickList"));
            $("#modulePickList").val(picklist_value);
            Settings_Picklist_Js.registerModulePickListChangeEvent();
            jQuery('#modulePickList').trigger('change');
        }
    },

    doCheckEnableRichText :function(singleComment,comment_id,replyComment){
        var instance = this;

        var params = {};
        params['action']= 'ActionAjax';
        params['module'] = 'VTEComments';
        params['mode'] = 'doCheckEnableRichText';

        app.request.post({data:params}).then(
            function (err,data) {
                if(err == null) {
                    if (data.enable == '1') {
                        if(typeof singleComment !='undefined'){
                            var noteContentElement = singleComment.find('[name="commentcontent"]');
                            var commentVal = singleComment.find('span.commentInfoContent');
                            noteContentElement.attr("id", "vteAdvanceComment"+comment_id);
                            if(replyComment){
                                noteContentElement.val();
                            }else {
                                noteContentElement.val(commentVal.html());
                            }

                            if (noteContentElement.length > 0) {
                                var ckEditorInstance = new Vtiger_CkEditor_Js();
                                var customConfig = {
                                    skin: 'office2013',
                                    toolbar: [
                                        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup','align','list', 'indent','colors' ,'links'], items: [ 'Bold', 'Italic', 'Underline', '-','TextColor', 'BGColor' ,'-','JustifyLeft', 'JustifyCenter', 'JustifyRight', '-', 'NumberedList', 'BulletedList','-', 'Link', 'Unlink','Image','-','RemoveFormat'] },
                                        { name: 'styles', items: ['Font', 'FontSize' ] },
                                        { name: 'document'/*, items:['Source']*/}
                                    ]};
                                ckEditorInstance.loadCkEditor(noteContentElement, customConfig);
                                var curComment = "vteAdvanceComment"+comment_id;
                                CKEDITOR.instances[curComment].on('change', function () {
                                    var content = CKEDITOR.instances[curComment].getData();
                                    content = content.split("<body")[1].split(">").slice(1).join(">").split("</body>")[0];
                                    singleComment.find('[name="commentcontent"]').val(content);
                                });
                                CKEDITOR.instances[curComment].on("instanceReady", function (event) {
                                    var editor = event.editor;
                                    $('.cke_chrome').css({'border': '1px solid #e1e1e1', 'box-shadow': 'none'});
                                    $('.cke_bottom').css({'background': '#e1e1e1'});
                                    editor.focus();
                                })
                                if (data.tagfeature == '1') {
                                    CKEDITOR.instances[curComment].on('instanceReady', function (evt) {
                                        //CKEDITOR.instances[curComment].execCommand('reloadSuggetionBox', jQuery.parseJSON(data.users));
                                        $('#vteAdvanceComment').textcomplete([
                                            { // html
                                                match: /@(\w{1,})$/,
                                                search: function (term, callback) {
                                                    callback($.map(jQuery.parseJSON(data.users), function (mention) {
                                                        var strName = mention.label.toLowerCase();
                                                        return strName.indexOf(term.toLowerCase()) === 0 ? mention.id : null;
                                                    }));
                                                },
                                                index: 1,
                                                replace: function (mention) {
                                                    return '@' + mention + ' ';
                                                }
                                            }
                                        ]);
                                    });
                                }
                            }
                        }else {
                            $('#cke_vteAdvanceComment').remove();
                            var noteContentElement = jQuery('[name="commentcontent"]');
                            noteContentElement.css({'visibility': '', 'display': ''});
                            jQuery('[name="commentcontent"]').unbind('click');
                            jQuery('[name="commentcontent"]').on('click', function(){
                                noteContentElement.addClass("richTextArea");
                                $("#widgetPicklistComments").removeClass("hide");
                                vtUtils.showSelect2ElementView($("[name='cf_comment_picklist']"));
                                vtUtils.applyFieldElementsView($("[name='cf_comment_picklist']"));
                                vtUtils.showSelect2ElementView($("[name='cf_comment_picklist_two']"));
                                vtUtils.applyFieldElementsView($("[name='cf_comment_picklist_two']"));
                                if(instance.showckeditor) {
                                    noteContentElement.attr("id", "vteAdvanceComment");
                                    if (noteContentElement.length > 0) {
                                        var ckEditorInstance = new Vtiger_CkEditor_Js();
                                        var customConfig = {
                                            skin: 'office2013',
                                            toolbar: [
                                                {
                                                    name: 'basicstyles',
                                                    groups: ['basicstyles', 'cleanup', 'align', 'list', 'indent', 'colors', 'links'],
                                                    items: ['Bold', 'Italic', 'Underline', '-', 'TextColor', 'BGColor', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Image', '-', 'RemoveFormat']
                                                },
                                                {name: 'styles', items: ['Font', 'FontSize']},
                                                {name: 'document'/*, items: ['Source']*/}
                                            ],
                                        };
                                        ckEditorInstance.loadCkEditor(noteContentElement, customConfig);
                                        CKEDITOR.instances.vteAdvanceComment.on("instanceReady", function(event){
                                            var editor = event.editor;
                                            $('.cke_chrome').css({'border': '1px solid #e1e1e1', 'box-shadow': 'none'});
                                            $('.cke_bottom').css({'background': '#e1e1e1'});
                                            editor.focus();
                                        })
                                        if (data.tagfeature == '1') {
                                            CKEDITOR.instances.vteAdvanceComment.on('instanceReady', function (evt) {
                                                $('#vteAdvanceComment').click()
                                                $('#vteAdvanceComment').textcomplete([
                                                    { // html
                                                        match: /@(\w{1,})$/,
                                                        search: function (term, callback) {
                                                            callback($.map(jQuery.parseJSON(data.users), function (mention) {
                                                                var strName = mention.label.toLowerCase();
                                                                return strName.indexOf(term.toLowerCase()) === 0 ? mention.id : null;
                                                            }));
                                                        },
                                                        index: 1,
                                                        replace: function (mention) {
                                                            return '@' + mention + ' ';
                                                        }
                                                    }
                                                ]);
                                            });
                                        }
                                        CKEDITOR.instances.vteAdvanceComment.on('change', function () {
                                            CKEDITOR.instances.vteAdvanceComment.updateElement();
                                            var content = CKEDITOR.instances.vteAdvanceComment.getData();
                                            content = content.split("<body")[1].split(">").slice(1).join(">").split("</body>")[0];
                                            jQuery('#vteAdvanceComment').val(content);
                                        });
                                    }
                                    instance.showckeditor=false;
                                }
                            })
                            jQuery('#commentTextArea').unbind('click');
                            jQuery('body').delegate('#commentTextArea','click',function(){
                                var chkshowckeditor = jQuery('[name="showckeditor"]').val();
                                if(typeof chkshowckeditor !== 'undefined') {
                                    if(chkshowckeditor==1) {
                                        instance.showckeditor = true;
                                        jQuery('[name="showckeditor"]').val(0);
                                    }else {
                                        instance.showckeditor = false;
                                    }
                                }
                                if (instance.showckeditor && app.getModuleName() == 'HelpDesk') {
                                    instance.showckeditor = false;
                                    var eleShowckeditor = jQuery('.showckeditor');
                                    if(eleShowckeditor.length==0) {
                                        var btnCancel = '<input type="hidden" value="0" name="showckeditor" class="showckeditor"/>';
                                        $("#replyInfo").before(btnCancel);
                                    }
                                    var noteContentElement = jQuery('[name="commentcontent"]');
                                    noteContentElement.attr("id", "vteAdvanceComment");
                                    var div_advancecomment = $('.cke_editor_vteAdvanceComment');
                                    div_advancecomment.remove();
                                    $("#replyInfo").removeClass("show").addClass("hide");
                                    $(".commentTitle").removeClass("hide").addClass("show");
                                    $(".commentTextGoBack").remove();
                                    var btnCancel = '<button class="commentTextGoBack t-btn-sm btn btn-success btn-sm" type="button" id="commentTextGoBack">Cancel</button>';
                                    $(".detailViewSaveComment,.saveComment").before(btnCancel);
                                    if (noteContentElement.length > 0) {
                                        var ckEditorInstance = new Vtiger_CkEditor_Js();
                                        var customConfig = {
                                            skin: 'office2013',
                                            toolbar: [
                                                {
                                                    name: 'basicstyles',
                                                    groups: ['basicstyles', 'cleanup', 'align', 'list', 'indent', 'colors', 'links'],
                                                    items: ['Bold', 'Italic', 'Underline', '-', 'TextColor', 'BGColor', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Image', '-', 'RemoveFormat']
                                                },
                                                {name: 'styles', items: ['Font', 'FontSize']},
                                                {name: 'document'/*, items: ['Source']*/}
                                            ],
                                        };
                                        ckEditorInstance.loadCkEditor(noteContentElement, customConfig);
                                        CKEDITOR.instances.vteAdvanceComment.on("instanceReady", function (event) {
                                            var editor = event.editor;
                                            $('.cke_chrome').css({'border': '1px solid #e1e1e1', 'box-shadow': 'none'});
                                            $('.cke_bottom').css({'background': '#e1e1e1'});
                                            editor.focus();
                                        })
                                        if (data.tagfeature == '1') {
                                            CKEDITOR.instances.vteAdvanceComment.on('instanceReady', function (evt) {
                                                var editor = evt.editor;
                                                $('#vteAdvanceComment').click()
                                                $('#vteAdvanceComment').textcomplete([
                                                    { // html
                                                        match: /@(\w{1,})$/,
                                                        search: function (term, callback) {
                                                            callback($.map(jQuery.parseJSON(data.users), function (mention) {
                                                                var strName = mention.label.toLowerCase();
                                                                return strName.indexOf(term.toLowerCase()) === 0 ? mention.id : null;
                                                            }));
                                                        },
                                                        index: 1,
                                                        replace: function (mention) {
                                                            return '@' + mention + ' ';
                                                        }
                                                    }
                                                ]);
                                            });
                                        }
                                        CKEDITOR.instances.vteAdvanceComment.on('change', function () {
                                            var content = CKEDITOR.instances.vteAdvanceComment.getData();
                                            content = content.split("<body")[1].split(">").slice(1).join(">").split("</body>")[0];
                                            jQuery('#vteAdvanceComment').val(content);
                                        });
                                    }
                                }
                            })
                        }
                        instance.saveInternalComment();
                    }else {
                        $("#widgetPicklistComments").removeClass("hide");
                        vtUtils.showSelect2ElementView($("[name='cf_comment_picklist']"));
                        vtUtils.applyFieldElementsView($("[name='cf_comment_picklist']"));
                        vtUtils.showSelect2ElementView($("[name='cf_comment_picklist_two']"));
                        vtUtils.applyFieldElementsView($("[name='cf_comment_picklist_two']"));
                        jQuery('#commentTextArea').unbind('click');
                        jQuery('body').delegate('#commentTextArea','click',function(){
                            $("#replyInfo").removeClass("show").addClass("hide");
                            $(".commentTitle").removeClass("hide").addClass("show");
                            $(".commentTextGoBack").remove();
                            var btnCancel = '<button class="commentTextGoBack t-btn-sm btn btn-success btn-sm" type="button" id="commentTextGoBack">Cancel</button>';
                            $(".detailViewSaveComment,.saveComment").before(btnCancel);
                        })
                    }
                }
            }
        );
    },
    registerDoSendMailUserTaggedComment : function(){
        var params = {};
        params['action']= 'ActionAjax';
        params['module'] = 'VTEComments';
        params['mode'] = 'DoSendMailUserTaggedComment';
        app.request.post({data:params}).then(
            function (err,data) {
                if(err == null) {
                }
            }
        );
    },
    detailViewContentHolder : false,
    getContentHolder : function() {
        if(this.detailViewContentHolder == false) {
            this.detailViewContentHolder = jQuery('div.details');
        }
        return this.detailViewContentHolder;
    },
    registerEventsShowComments: function (data) {
        var self = this;
        $(".basicAddCommentBlock").remove();
        $(".basicEditCommentBlock").remove();
        var detailContentsHolder = self.getContentHolder();
        var recentCommentsBody = detailContentsHolder.find('.commentsBody');
        var params = {}
        params.data = {
            module: 'VTEComments',
            view: 'SummaryWidget',
            mode: 'showCommentsWidget',
            record: app.getRecordId(),
            source_module: app.getModuleName(),
            params: data
        }
        app.helper.showProgress();
        app.request.post(params).then(
            function (err,data) {
                app.helper.hideProgress();
                if(!err) {
                    recentCommentsBody.html(data);
                    vtUtils.showSelect2ElementView($("[name='slbPeriodDuration']"));
                    vtUtils.applyFieldElementsView($("[name='slbPeriodDuration']"));
                    $(".commentsBody").removeClass("hide").addClass("show");
                }
            },
            function (data, err) {
            }
        );
    },

    registerEventsSearchComments:function(){
        var thisInstance=this;
        var detailContentsHolder = thisInstance.getContentHolder();
        detailContentsHolder.on('click','.vteSearchCommentButton', function(e){
            var url = "index.php?module=VTEComments&view=SummaryWidget&mode=showSearchCommentForm";
            app.helper.showProgress();
            app.request.get({'url':url}).then(function(err,resp) {
                app.helper.hideProgress();
                if(err === null) {
                    app.helper.showModal(resp, {'cb' : function(modal) {
                        thisInstance.registerDateFilters(form);
                    }});

                    var form = jQuery('.form-modalSearchComment');
                    form.on("click","button[name='saveButton']",function(e){
                        e.preventDefault();
                        var filterParams = {};
                        var commentContent = jQuery('.otherFilters input[name="commentcontent"]').val();
                        if(commentContent){
                            filterParams["commentcontent"] = commentContent;
                        }

                        var dateFilter = jQuery('.dateFilters button.active');
                        var filterMode = dateFilter.data('filtermode');
                        filterParams["date"] = filterMode;

                        if(filterMode == "range"){
                            var rangeValue = dateFilter.val();
                            var res = rangeValue.split(",");
                            filterParams['startRange'] = res[0];
                            filterParams['endRange'] = res[1];
                        }
                        var userFilter = jQuery('.otherFilters select[name="assigned_user_id"]').val();
                        if(userFilter){
                            filterParams["assigned_user_id"] = userFilter;
                        }
                        var filter = jQuery('.otherFilters select[name="cf_comment_picklist"]').val();
                        if(filter){
                            filterParams["cf_comment_picklist"] = filter;
                        }
                        filter = jQuery('.otherFilters select[name="cf_comment_picklist_two"]').val();
                        if(filter){
                            filterParams["cf_comment_picklist_two"] = filter;
                        }
                        filter = jQuery('.otherFilters input[name="cf_comment_text_field"]').val();
                        if(filter){
                            filterParams["cf_comment_text_field"] = filter;
                        }
                        thisInstance.registerEventsShowComments({'filter':filterParams});
                    });
                }
            });
        });
        detailContentsHolder.on('click','.vteClearSearchComment', function(e){
            var filterParams = {};
            filterParams["commentcontent"] = '';
            filterParams["date"] = 'All';
            filterParams["assigned_user_id"] = '';
            filterParams["cf_comment_picklist"] = "";
            filterParams["cf_comment_picklist_two"] = "";
            filterParams["cf_comment_text_field"] = "";
            filterParams["clear_search"] = "true";
            thisInstance.registerEventsShowComments({'filter':filterParams});
        });
        detailContentsHolder.on('click','.lblPreviousComment', function(e){
            var element = jQuery(e.currentTarget);
            var commentId = element.data('commentid');
            var url = "index.php?module=VTEComments&view=SummaryWidget&mode=showPreviosComment&modcommentsid="+commentId;
            app.helper.showProgress();
            popupShown = true;
            app.request.get({'url':url}).then(function(err,resp) {
                app.helper.hideProgress();
                if(err === null) {
                    app.helper.showModal(resp, {'cb' : function(modal) {
                        popupShown = false;
                    }});
                }
            });
        });
    },
    clearExistingCustomScroll : function(){
        var blocksList = jQuery(".contentsBlock");
        blocksList.each(function(index,blockElement){
            var blockElement = jQuery(blockElement);
            var scrollableElement = blockElement.find('.scrollable');
            scrollableElement.mCustomScrollbar('destroy');
        });
    },
    registerDateFilters : function(form){
        var thisInstance = this;
        var overlay = form.find('#taskManagementContainer');
        overlay.on("click",".dateFilters button",function(e){
            var currentTarget = jQuery(e.currentTarget);
            if(!currentTarget.hasClass('rangeDisplay')){
                jQuery('#taskManagementContainer .dateFilters button').removeClass('active');
                currentTarget.addClass('active');
                thisInstance.clearExistingCustomScroll();
                //thisInstance.loadContents();
                app.helper.hideProgress();
            }
        });

        overlay.on('datepicker-change', 'button[data-calendar-type="range"]', function(e){
            var element = jQuery(e.currentTarget);
            jQuery('#taskManagementContainer .dateFilters button').removeClass('active');
            element.addClass('active');
            var parentContainer = element.closest('.dateFilters');
            parentContainer.find('.selectedRange').html("("+element.val()+")").closest('button').removeClass('hide');
            thisInstance.clearExistingCustomScroll();
            //thisInstance.loadContents();
        });

        overlay.on('click', '.clearRange', function(e){
            var container = jQuery('.dateFilters');
            container.find('[data-filtermode="all"]').trigger('click');
            container.find('.rangeDisplay').addClass('hide');
        });
    },
    registerEventsShowCommentsOneRow: function () {
        var self = this;
        var params = {}
        params.data = {
            module: 'VTEComments',
            action: 'ActionAjax',
            mode: 'checkRowToShow'
        }
        app.request.post(params).then(
            function (err,data) {
                if(err == null) {
                    {
                        var filterParams = {};
                        filterParams["commentcontent"] = '';
                        filterParams["date"] = 'All';
                        filterParams["assigned_user_id"] = '';
                        filterParams["cf_comment_picklist"] = "";
                        filterParams["cf_comment_picklist_two"] = "";
                        filterParams["cf_comment_text_field"] = "";
                        self.registerEventsShowComments({'filter':filterParams});
                    }
                }
            },
            function (data, err) {
            }
        );
    },
    registerEventsShowEmailForm: function () {
        var self = this;
        var detailContentsHolder = self.getContentHolder();
        var recentCommentsBody = detailContentsHolder.find('.commentsBody');
        var params = {}
        params.data = {
            module: 'VTEComments',
            view: 'SummaryWidget',
            mode: 'showEmailForm',
            record: app.getRecordId(),
            source_module: app.getModuleName(),
        }
        app.request.post(params).then(
            function (err,data) {
                if(!err) {
                    var hasForm = $('#composeEmailContainer').find('form');
                    if(hasForm.length==0) {
                        $('#composeEmailContainer').append(data);
                        if (jQuery('#emailbody').length > 0) {
                            self.loadCkEditor(jQuery('#emailbody'));
                        }
                    }
                }
            },
            function (data, err) {
            }
        );
    },
    showpopupModal : function(){
        var thisInstance = this;
        vtUtils.applyFieldElementsView(jQuery('.popupModal'));
        jQuery('.popupModal').modal();
        jQuery('.popupModal').on('shown.bs.modal', function() {
            jQuery('.myModal').css('opacity', .5);
            jQuery('.myModal').unbind();
        });

        jQuery('.popupModal').on('hidden.bs.modal', function() {
            this.remove();
            jQuery('.myModal').css('opacity', 1);
            jQuery('.myModal').removeData("modal").modal(app.helper.defaultModalParams());
            jQuery('.myModal').bind();
        });
    },
    registerBrowseCrmEvent : function(){
        var thisInstance = this;
        jQuery('#vteCommentsBrowseCrm').on('click',function(e){
            var url = jQuery(e.currentTarget).data('url');
            var postParams = app.convertUrlToDataParams("index.php?"+url);

            app.request.post({"data":postParams}).then(function(err,data){
                jQuery('.popupModal').remove();
                var ele = jQuery('<div class="modal popupModal"></div>');
                ele.append(data);
                jQuery('body').append(ele);
                thisInstance.showpopupModal();
                app.event.trigger("post.Popup.Load",{"eventToTrigger":"post.DocumentsList.click"});
            });
        });
    },
    registerRemoveAttachmentEvent : function(){
        var thisInstance = this;
        this.getMassEmailForm().on('click','.removeAttachment',function(e){
            var currentTarget = jQuery(e.currentTarget);
            var id = currentTarget.data('id');
            var fileSize = currentTarget.data('fileSize');
            currentTarget.closest('.MultiFile-label').remove();
            thisInstance.removeDocumentsFileSize(fileSize);
            thisInstance.removeDocumentIds(id);
            if (jQuery('#attachments').is(':empty')){
                jQuery('.MultiFile,.MultiFile-applied').removeClass('removeNoFileChosen');
            }
        });
    },

    getDocumentAttachmentElement : function(selectedFileName,id,selectedFileSize){
        return '<div class="MultiFile-label"><span href="#" class="removeAttachment cursorPointer" data-id='+id+' data-file-size='+selectedFileSize+'>x </span><span>'+selectedFileName+'</span></div>';
    },
    fileAfterSelectHandler : function(element, value, master_element){
        var thisInstance = this;
        var mode = jQuery('[name="emailMode"]').val();
        var existingAttachment = JSON.parse(jQuery('[name="attachments"]').val());
        element = jQuery(element);
        thisInstance.setAttachmentsFileSizeByElement(element);
        var totalAttachmentsSize = thisInstance.getTotalAttachmentsSize();
        var maxUploadSize = thisInstance.getMaxUploadSize();
        if(totalAttachmentsSize > maxUploadSize){
            app.helper.showAlertBox({message:app.vtranslate('JS_MAX_FILE_UPLOAD_EXCEEDS')});
            this.removeAttachmentFileSizeByElement(jQuery(element));
            master_element.list.find('.MultiFile-label:last').find('.MultiFile-remove').trigger('click');
        }else if((mode != "") && (existingAttachment != "")){
            var pattern = /\\/;
            var fileuploaded = value;
            jQuery.each(existingAttachment,function(key,value){
                if((value['attachment'] == fileuploaded) && !(value.hasOwnProperty( "docid"))){
                    var errorMsg = app.vtranslate("JS_THIS_FILE_HAS_ALREADY_BEEN_SELECTED")+fileuploaded;
                    app.helper.showAlertBox({message:app.vtranslate(errorMsg)});
                    thisInstance.removeAttachmentFileSizeByElement(jQuery(element),value);
                    master_element.list.find('.MultiFile-label:last').find('.MultiFile-remove').trigger('click');
                    return false;
                }
            })
        }
        return true;
    },
    ckEditorInstance : false,
    massEmailForm : false,
    saved : "SAVED",
    sent : "SENT",
    attachmentsFileSize : 0,
    documentsFileSize : 0,

    getPreloadData : function() {
        return this.preloadData;
    },

    setPreloadData : function(dataInfo){
        this.preloadData = dataInfo;
        return this;
    },

    getckEditorInstance : function(){
        if(this.ckEditorInstance == false){
            this.ckEditorInstance = new Vtiger_CkEditor_Js();
        }
        return this.ckEditorInstance;
    },
    loadCkEditor : function(textAreaElement){
        var ckEditorInstance = this.getckEditorInstance();
        var customConfig = {
            skin: 'office2013',
            toolbar: [
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup', 'align', 'list', 'indent', 'colors', 'links'],
                    items: ['Bold', 'Italic', 'Underline', '-', 'TextColor', 'BGColor', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', '-', 'NumberedList', 'BulletedList', '-', 'Link', 'Unlink', 'Image', '-', 'RemoveFormat']
                },
                {name: 'styles', items: ['Font', 'FontSize']},
                {name: 'document', items: ['Source']}
            ],
            startupFocus : true
        };
        ckEditorInstance.loadCkEditor(textAreaElement,customConfig);
        CKEDITOR.on("instanceReady", function(event){
            $('.cke_chrome').css({'border': '1px solid #e1e1e1', 'box-shadow': 'none'});
            $('.cke_bottom').css({'background': '#e1e1e1'});
        })
    },

    setAttachmentsFileSizeByElement : function(element){
        this.attachmentsFileSize += element.get(0).files[0].size;
    },

    setAttachmentsFileSizeBySize : function(fileSize){
        this.attachmentsFileSize += parseFloat(fileSize);
    },

    getAttachmentsFileSize : function(){
        return this.attachmentsFileSize;
    },
    setDocumentsFileSize : function(documentSize){
        this.documentsFileSize += parseFloat(documentSize);
    },
    getDocumentsFileSize : function(){
        return this.documentsFileSize;
    },

    getTotalAttachmentsSize : function(){
        return parseFloat(this.getAttachmentsFileSize())+parseFloat(this.getDocumentsFileSize());
    },

    getMaxUploadSize : function(){
        return jQuery('#maxUploadSize').val();
    },

    removeAttachmentFileSizeByElement : function(element) {
        this.attachmentsFileSize -= element.get(0).files[0].size;
    },

    removeDocumentsFileSize : function(documentSize){
        this.documentsFileSize -= parseFloat(documentSize);
    },

    removeAttachmentFileSizeBySize : function(fileSize) {
        this.attachmentsFileSize -= parseFloat(fileSize);
    },
    checkIfExisitingAttachment : function(selectedDocumentId){
        var documentExist;
        var documentPresent;
        var mode = jQuery('[name="emailMode"]').val();
        var selectedDocumentIds = jQuery('#documentIds').val();
        var existingAttachment = JSON.parse(jQuery('[name="attachments"]').val());
        if((mode != "") && (existingAttachment != "")){
            jQuery.each(existingAttachment,function(key,value){
                if(value.hasOwnProperty( "docid")){
                    if(value['docid'] == selectedDocumentId){
                        documentExist = 1;
                        return false;
                    }
                }
            })
            if(selectedDocumentIds != ""){
                selectedDocumentIds = JSON.parse(selectedDocumentIds);
            }
            if((documentExist == 1) || (jQuery.inArray(selectedDocumentId,selectedDocumentIds) != '-1')){
                documentPresent = 1;
            } else {
                documentPresent = 0;
            }
        } else if(selectedDocumentIds != ""){
            selectedDocumentIds = JSON.parse(selectedDocumentIds);
            if((jQuery.inArray(selectedDocumentId,selectedDocumentIds) != '-1')){
                documentPresent = 1;
            } else {
                documentPresent = 0;
            }
        }
        if(documentPresent == 1){
            var errorMsg = app.vtranslate("JS_THIS_DOCUMENT_HAS_ALREADY_BEEN_SELECTED");
            app.helper.showErrorNotification({message: errorMsg});
            return true;
        } else {
            return false;
        }
    },
    writeDocumentIds :function(selectedDocumentId){
        var thisInstance = this;
        var newAttachment;
        var selectedDocumentIds = jQuery('#documentIds').val();
        if(selectedDocumentIds != ""){
            selectedDocumentIds = JSON.parse(selectedDocumentIds);
            var existingAttachment = thisInstance.checkIfExisitingAttachment(selectedDocumentId);
            if(!existingAttachment){
                newAttachment = 1;
            } else {
                newAttachment = 0;
            }
        } else {
            var existingAttachment = thisInstance.checkIfExisitingAttachment(selectedDocumentId);
            if(!existingAttachment){
                newAttachment = 1;
                var selectedDocumentIds = new Array();
            }
        }
        if(newAttachment == 1){
            selectedDocumentIds.push(selectedDocumentId);
            jQuery('#documentIds').val(JSON.stringify(selectedDocumentIds));
            return true;
        } else {
            return false;
        }
    },
    removeDocumentIds : function(removedDocumentId){
        var documentIdsContainer = jQuery('#documentIds');
        var documentIdsArray = JSON.parse(documentIdsContainer.val());
        documentIdsArray.splice( jQuery.inArray('"'+removedDocumentId+'"', documentIdsArray), 1 );
        documentIdsContainer.val(JSON.stringify(documentIdsArray));
    },
    /**
     * Function to calculate upload file size
     */
    calculateUploadFileSize : function(){
        var thisInstance = this;
        var composeEmailForm = this.getMassEmailForm();
        var attachmentsList = composeEmailForm.find('#attachments');
        var attachments = attachmentsList.find('.customAttachment');
        jQuery.each(attachments,function(){
            var element = jQuery(this);
            var fileSize = element.data('fileSize');
            var fileType = element.data('fileType');
            if(fileType == "file"){
                thisInstance.setAttachmentsFileSizeBySize(fileSize);
            } else if(fileType == "document"){
                thisInstance.setDocumentsFileSize(fileSize);
            }
        })
    },

    registerSelectTemplates : function (container) {
        var emailTemplates = $('select[name="slEmailTemplates"]');
        emailTemplates.on('change',function (e) {
            var element = $(e.currentTarget);
            var params = {}
            params.data = {
                module: 'VTEComments',
                action: 'ActionAjax',
                mode: 'getEmailTemplateBody',
                record: element.val(),
            }
            app.request.post(params).then(
                function (err,data) {
                    if(!err) {
                        var editor = CKEDITOR.instances.emailbody;
                        //var edata = editor.getData();
                        //var replaced_text = edata.replace(edata, data.body);
                        //editor.setData($("<div/>").html(data.body).text());
                        editor.insertHtml($("<div/>").html(data.body).text());
                    }
                },
                function (data, err) {
                }
            );
        });
    },
    getMassEmailForm : function(){
        if(this.massEmailForm == false){
            this.massEmailForm = jQuery("#massEmailCommentForm");
        }
        return this.massEmailForm;
    },
    registerSaveDraftOrSendEmailEvent : function(){
        var thisInstance = this;
        var form = this.getMassEmailForm();
        form.on('click','#sendEmailComments',function(e){
            var targetName = jQuery(e.currentTarget).attr('name');
            jQuery('#flag').val(thisInstance.sent);
            var params = {
                submitHandler: function(form) {
                    form = jQuery(form);
                    app.helper.hideModal();
                    app.helper.showProgress();
                    if (CKEDITOR.instances['emailbody']) {
                        form.find('#emailbody').val(CKEDITOR.instances['emailbody'].getData());
                    }

                    var data = new FormData(form[0]);
                    var postParams = {
                        data:data,
                        // jQuery will set contentType = multipart/form-data based on data we are sending
                        contentType:false,
                        // we don’t want jQuery trying to transform file data into a huge query string, we want raw data to be sent to server
                        processData:false
                    };
                    app.request.post(postParams).then(function(err,data){
                        app.helper.hideProgress();
                        var ele = jQuery(data);
                        var success = ele.find('.mailSentSuccessfully');
                        if(success.length <= 0){
                            app.helper.showModal(data);
                        } else {
                            app.event.trigger('post.mail.sent',data);
                        }
                    });
                }
            };
            form.vtValidate(params);
        });
    },
    /*
     * Function to register the events for bcc and cc links
     */
    registerCcAndBccEvents : function(){
        var thisInstance = this;
        jQuery('#ccLink').on('click',function(e){
            jQuery('.ccContainer').removeClass("hide");
            jQuery(e.currentTarget).hide();
        });
        jQuery('#bccLink').on('click',function(e){
            jQuery('.bccContainer').removeClass("hide");
            jQuery(e.currentTarget).hide();
        });
    },
    saveInternalComment:function(){
        var a=this;
        jQuery('button.detailViewSaveComment[data-mode="add"],button.saveComment[data-mode="add"]').on('click', function(){
            var m=jQuery(".commentTextArea").find("textarea");
            var k=m.val();
            if(m.hasClass("richTextArea")){
                var b=m.attr("id");
                m=jQuery("#cke_"+b);
            }else {
                m = jQuery('[name="commentcontent"]');
            }
            if(k.trim()==""){
                var h=app.vtranslate("JS_LBL_COMMENT_VALUE_CANT_BE_EMPTY");
                vtUtils.showValidationMessage(m,h);
                return false;
            }vtUtils.hideValidationMessage(m);
            return true;
        });
    },
    registerEvents: function(){
        var self = this;
        self.registerEventsAddShortcutToSettings();
        self.registerEventsAddInputHidden();
        //self.registerEventsChangeCommentContentAndColor();
        self.registerEventsForEditComment();
        self.registerEventsForReplyComment();
        self.registerEventsForViewThreadComment();
        self.registerEventsForCancelEditComment();
        self.registerEventsSearchComments();
        setTimeout(function() {
            self.registerEventsShowCommentsOneRow();
            jQuery('body').delegate('#composeEmailForm','click',function(){
                $("#replyInfo").removeClass("show").addClass("hide");
                $(".replyArea").removeClass("hide").addClass("show");
                self.registerEventsShowEmailForm();
            });
        },1000);
    }
});

jQuery(document).ready(function () {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTEComments')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    // only load on Detail view
    var instance = new VTEComments_Js();

    if(app.view()!='Detail') return;
    instance.registerEvents();
});

jQuery( document ).ajaxComplete(function(event, xhr, settings) {
    var url = settings.data;
    if(typeof url == 'undefined' && settings.url) url = settings.url;
    if(Object.prototype.toString.call(url) =='[object String]') {
        if ((url.indexOf('view=Detail') != -1 && url.indexOf('mode=showRecentComments') > -1)
            || (url.indexOf('relatedModule=ModComments') != -1 && url.indexOf('view=Detail') != -1 && url.indexOf('mode=showRelatedList') != -1)
            || (url.indexOf('relatedModule=ModComments') > -1 && url.indexOf('mode=showRecentComments') > -1 && url.indexOf('rollup-toggle=1') > -1)){
            $(".commentsBody").addClass("hide");
        }
        if ((url.indexOf('requestMode=summary') > -1 && url.indexOf('mode=showDetailViewByMode') > -1 && url.indexOf('view=Detail') > -1)
            || (url.indexOf('relatedModule=ModComments') != -1 && url.indexOf('view=Detail') != -1 && url.indexOf('mode=showRelatedList') != -1)
            || (url.indexOf('relatedModule=ModComments') > -1 && url.indexOf('mode=showRecentComments') > -1 && url.indexOf('rollup-toggle=1') > -1)){
            var instance = new VTEComments_Js();
            instance.registerDoSendMailUserTaggedComment();
            $('input[name="inputTextField"]').val('');

            var instance = new VTEComments_Js();
            setTimeout(function() {
                instance.registerEventsShowCommentsOneRow();
                instance.registerSelectTemplates();
                jQuery('body').delegate('#composeEmailForm','click',function(){
                    $("#replyInfo").removeClass("show").addClass("hide");
                    $(".replyArea").removeClass("hide").addClass("show");
                    self.registerEventsShowEmailForm();
                });
            }, 1000);
        }
        if (url.indexOf('module=VTEComments') > -1 && url.indexOf('mode=showCommentsWidget') > -1 && url.indexOf('view=SummaryWidget') > -1) {
            var instance = new VTEComments_Js();
            jQuery('.textshortComment').unbind('click');
            jQuery('.interactorDetails').unbind('click');
            jQuery('.textshortComment').on('click', function (e) {
                var element = jQuery(e.currentTarget);
                var commentDetail = element.closest('div.singleComment');
                if ($(this).hasClass('hide')) {
                    $(commentDetail).find(".comment-details-wrapper").slideToggle('fast');
                    $(this).removeClass("hide");
                } else {
                    $(this).addClass("hide");
                    $(commentDetail).find(".comment-details-wrapper").slideToggle('fast');
                }
            });
            jQuery('.interactorDetails').on('click', function (e) {
                var element = jQuery(e.currentTarget);
                var commentDetail = element.closest('div').next();
                var elShortComment = $(commentDetail).find(".textshortComment");

                if (elShortComment.hasClass('hide')) {
                    $(commentDetail).find(".comment-details-wrapper").slideToggle('fast');
                    elShortComment.removeClass("hide");
                } else {
                    elShortComment.addClass("hide");
                    $(commentDetail).find(".comment-details-wrapper").slideToggle('fast');
                }
            });
            var commentList = $('.commentDetails');
            for(var i=0;i < commentList.length; i++){
                var curComment = jQuery(commentList[i]).find('.commentInfoContent');
                var fullcomment = curComment.attr("data-fullcomment");
                if(typeof fullcomment == 'undefined'){
                    fullcomment = curComment.html();
                    curComment.attr("data-fullcomment", fullcomment);
                }
                if(typeof fullcomment != 'undefined'){
                    fullcomment= fullcomment.replace(/&amp;nbsp;/g, ' ');
                    fullcomment= fullcomment.replace(/&amp;/g, '&');
                    //fullcomment= fullcomment.replace(/</g, '&lt;').replace(/>/g,'&gt;');
                    if( /&lt;br \/&gt;/i.test(fullcomment) === true || /\/div&gt;/i.test(fullcomment) === true || /\/span&gt;/i.test(fullcomment) === true || /\/p&gt;/i.test(fullcomment) === true)
                        $(curComment).html($("<div />").html(fullcomment).text());
                    else
                        $(curComment).html($("<div />").html(fullcomment));
                }
            }
            instance.doCheckEnableRichText();
        }
        if (url.indexOf('module=VTEComments') > -1 && url.indexOf('mode=showEmailForm') > -1 && url.indexOf('view=SummaryWidget') > -1) {
            var instance = new VTEComments_Js();
            setTimeout(function() {
                instance.registerRemoveAttachmentEvent();
                instance.registerSaveDraftOrSendEmailEvent();
                instance.registerCcAndBccEvents();
                vtUtils.showSelect2ElementView($("[name='slEmailTemplates']"));
                vtUtils.applyFieldElementsView($("[name='slEmailTemplates']"));
                instance.registerBrowseCrmEvent();
                app.event.off("post.DocumentsList.click");
                app.event.on("post.DocumentsList.click",function(event, data){
                    var responseData = JSON.parse(data);
                    jQuery('.popupModal').modal('hide');
                    app.helper.hideModal();
                    for(var id in responseData){
                        selectedDocumentId = id;
                        var selectedFileName = responseData[id].info['filename'];
                        var selectedFileSize = responseData[id].info['filesize'];
                        var response = instance.writeDocumentIds(selectedDocumentId)
                        if(response){
                            var attachmentElement = instance.getDocumentAttachmentElement(selectedFileName,id,selectedFileSize);
                            //TODO handle the validation if the size exceeds 5mb before appending.
                            jQuery(attachmentElement).appendTo(jQuery('#attachments'));
                            jQuery('.MultiFile-applied,.MultiFile').addClass('removeNoFileChosen');
                            instance.setDocumentsFileSize(selectedFileSize);
                        }
                    }
                });
                jQuery("#multiFile").MultiFile({
                    list: '#attachments',
                    'afterFileSelect' : function(element, value, master_element){
                        var masterElement = master_element;
                        var newElement = jQuery(masterElement.current);
                        newElement.addClass('removeNoFileChosen');
                        instance.fileAfterSelectHandler(element, value, master_element);
                    },
                    'afterFileRemove' : function(element, value, master_element){
                        if (jQuery('#attachments').is(':empty')){
                            jQuery('.MultiFile,.MultiFile-applied').removeClass('removeNoFileChosen');
                        }
                        instance.removeAttachmentFileSizeByElement(jQuery(element));
                    }
                });
                instance.registerSelectTemplates();
                jQuery('#sendEmailComments').removeAttr("style");
            }, 1000);
        }
    }
    if(Object.prototype.toString.call(url) =='[object FormData]') {
        if ((settings.data.get('module')=='ModComments' && settings.data.get('action')=='SaveAjax')
            ||(settings.data.get('module')=='VTEComments' && settings.data.get('view')=='MassSaveAjax')
            ||(settings.data.get('module')=='ModComments' && settings.data.get('action')=='Save')){
            $(".commentsBody").removeClass("show").addClass("hide");
            var instance = new VTEComments_Js();
            instance.registerDoSendMailUserTaggedComment();
            $('input[name="inputTextField"]').val('');
            var eleShowckeditor = jQuery('.showckeditor');
            if(eleShowckeditor.length==0) {
                var btnCancel = '<input type="hidden" value="1" name="showckeditor" class="showckeditor"/>';
                $("#replyInfo").before(btnCancel);
            }
            jQuery('[name="showckeditor"]').val(1);
            var instance = new VTEComments_Js();
            setTimeout(function() {
                instance.registerEventsShowCommentsOneRow();
                instance.registerSelectTemplates();
                jQuery('body').delegate('#composeEmailForm','click',function(){
                    $("#replyInfo").removeClass("show").addClass("hide");
                    $(".replyArea").removeClass("hide").addClass("show");
                    instance.registerEventsShowEmailForm();
                });
            }, 1000);
            $("#widgetPicklistComments").addClass('hide');
        }
    }
})