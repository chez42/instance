Vtiger_Detail_Js("VTEEmailMarketing_Detail_Js",{},{

    registerRecordRowClickEvent: function() {
        var table = jQuery('#table-record-sent-email');
        table.on('click','.relatedListEntryValues a',function(e){
            e.preventDefault();
        });
        table.on('click','.listViewEntries',function(e){
            var selection = window.getSelection().toString();
            if(selection.length == 0) {
                var elem = jQuery(e.currentTarget);
                var recordUrl = elem.data('recordurl');
                if(typeof recordUrl != "undefined"){
                    var params = app.convertUrlToDataParams(recordUrl);
                    //Display Mode to show details in overlay
                    params['mode'] = 'showDetailViewByMode';
                    params['requestMode'] = 'full';
                    params['displayMode'] = 'overlay';
                    var parentRecordId = app.getRecordId();
                    app.helper.showProgress();
                    app.request.get({data: params}).then(function(err, response) {
                        app.helper.hideProgress();
                        var overlayParams = {'backdrop' : 'static', 'keyboard' : false};
                        app.helper.loadPageContentOverlay(response, overlayParams).then(function(container) {
                            var detailjs = Vtiger_Detail_Js.getInstanceByModuleName(params.module);
                            detailjs.showScroll(jQuery('.overlayDetail .modal-body'));
                            detailjs.setModuleName(params.module);
                            detailjs.setOverlayDetailMode(true);
                            detailjs.setContentHolder(container.find('.overlayDetail'));
                            detailjs.setDetailViewContainer(container.find('.overlayDetail'));
                            detailjs.registerOverlayEditEvent();
                            detailjs.registerBasicEvents();
                            detailjs.registerClickEvent();
                            detailjs.registerHeaderAjaxEditEvents(container.find('.overlayDetailHeader'));
                            detailjs.registerEventToReloadRelatedListOnCloseOverlay(parentRecordId);
                            app.event.trigger('post.overlay.load', parentRecordId, params);
                            container.find('form#detailView').on('submit', function(e) {
                                e.preventDefault();
                            });
                        });
                    });
                }
            }
        });
    },

    registerNextPageButtonClick : function() {
        var thisInstance = this;
        var div = thisInstance.getContentHolder();
        div.on('click','#NextPageButton',function(e){
            var currentPage = div.find('#currentPage').val();
            var nextPage = parseInt(currentPage) + 1;
            var totalPage = parseInt(div.find('#totalPage').val());
            if(nextPage <= totalPage){
                thisInstance.registerChangePage(nextPage);
            }
        });
    },

    registerPreviousPageButtonClick : function() {
        var thisInstance = this;
        var div = thisInstance.getContentHolder();
        div.on('click','#PreviousPageButton',function(e) {
            var currentPage = div.find('#currentPage').val();
            var previousPage = parseInt(currentPage) - 1;
            if(previousPage > 0){
                thisInstance.registerChangePage(previousPage);
            }
        });
    },

    registerPageJumpButtonClick : function() {
        var thisInstance = this;
        var div = thisInstance.getContentHolder();
        div.on('click', '#pageToJumpSubmit', function(e){
            div.find('#PageJumpDropDown').hide();
            var page = div.find('#pageToJump').val();
            var totalPage = parseInt(div.find('#totalPage').val());
            if( 1 <= page <= totalPage){
                thisInstance.registerChangePage(page);
            }
        });
    },

    registerClickShowJump:function () {
        var thisInstance = this;
        var div = thisInstance.getContentHolder();
        div.on('click', '#PageJump', function(e){
            div.find('#PageJumpDropDown').show();
        });
    },

    registerClickPageToJump:function () {
        var thisInstance = this;
        var div = thisInstance.getContentHolder();
        div.on('click', '#pageToJump', function(e){
            div.find('#PageJumpDropDown').show();
        });
    },

    registerChangePage:function(page){
        var thisInstance = this;
        var div = thisInstance.getContentHolder();
        var totalPage = parseInt(div.find('#totalPage').val());
        var recordId = jQuery('#recordId').val();
        if(page == totalPage){
            div.find('#PreviousPageButton').removeAttr('disabled');
            div.find('#NextPageButton').attr('disabled','disabled');
        }else if(page > 1 && page < totalPage){
            div.find('#PreviousPageButton').removeAttr('disabled');
            div.find('#NextPageButton').removeAttr('disabled');
        } else if(page == 1){
            div.find('#PreviousPageButton').attr('disabled','disabled');
            if(page < totalPage){
                div.find('#NextPageButton').removeAttr('disabled');
            }
        }
        var params = {
            module: 'VTEEmailMarketing',
            action: 'ActionAjax',
            mode: 'paggingDetailRelatedRecord',
            dispayType: $("#hfDispayType").val(),
            "recordId": recordId,
            "page":page
        };
        app.helper.showProgress();
        AppConnector.request(params).then(
            function (data) {
                app.helper.hideProgress();
                if(data.success){
                    var html = '';
                    var tableBody = jQuery('#table-record-sent-email tbody');
                    for(var i = 0; i < data.result.list.length;i++){
                        var sentOn = data.result.list[i].sent_on;
                        if(sentOn == 'null'){
                            sentOn = '';
                        }
                        html+= '<tr data-id="'+data.result.list[i].record_id+'">' +
                            '<td style="border-right: none !important;"><div class="table-actions">' +
                            '<span class="quickView fa fa-eye icon action listViewEntries" data-recordurl="'+data.result.list[i].data_url+'" data-app="MARKETING" title="Quick View"></span>' +
                            '</div></td><td class="relatedListEntryValues" style="vertical-align: middle;border-left: none !important">'+app.vtranslate(data.result.list[i].record_type,data.result.list[i].record_type)+'</td>' +
                            '<td class="relatedListEntryValues" style="vertical-align: middle">'+data.result.list[i].name+'</td><td class="relatedListEntryValues" style="vertical-align: middle">'+data.result.list[i].email+'</td>' +
                            '<td class="relatedListEntryValues" style="vertical-align: middle ; border-right: none !important;">'+sentOn+'</td>' +
                            '<td class="relatedListEntryValues" style="vertical-align: middle ; border-left: none !important;">'+data.result.list[i].error_info+'</td></tr>'
                    }
                    tableBody.html(html);
                    div.find('#pageStartRange').val(data.result.pagging.startRecord);
                    div.find('#currentPage').val(data.result.pagging.currentPage);
                    div.find('#totalPage').val(data.result.pagging.totalPage);
                    div.find('#noOfEntries').val(data.result.pagging.totalRecord);
                    div.find('.pageNumbersText').text(data.result.pagging.startRecord + ' to ' + data.result.pagging.endRecord);
                    div.find('.totalNumberOfRecords').text(' of ' + data.result.pagging.totalRecord);
                }
                tableBody.find('.error_info').tooltip();
            }
        );
    },

    registerActionScheduler : function () {
        jQuery(document).on('click','.action_scheduler',function () {
            var recordId = app.getRecordId();
            var status = $(this).val();
            var params = {
                module: 'VTEEmailMarketing',
                action: 'ActionAjax',
                mode: 'actionSchedulerOnDetailView',
                "recordId": recordId,
                "status" : status
            };
            if(status == 'Retry Failed'){
                var failed_to_send = $('[name="failed_to_send"]').val();
                var message = 'There are <b>'+failed_to_send+' emails that failed to send</b>. To retry sending '+failed_to_send+' emails again, please click Yes.</br></br>Note: Only emails that failed will be added to queue and will be sent out next time the scheduler runs.';
                params['failed_to_send'] = failed_to_send;
                app.helper.showConfirmationBox({'message' : message}).then(
                    function (e) {
                        app.helper.showProgress();
                        AppConnector.request(params).then(
                            function (data) {
                                if(data.success == true){
                                    app.helper.hideProgress();
                                    var message = data.result;
                                    bootbox.alert(message, function(){
                                        location.reload();
                                    });
                                }
                            }
                        )
                    });
            }else{
                app.helper.showProgress();
                AppConnector.request(params).then(
                    function (data) {
                        if(data.success == true){
                            app.helper.hideProgress();
                            var message = data.result;
                            bootbox.alert(message, function(){
                                location.reload();
                            });
                        }
                    }
                )
            }
        });

    },

    registerResubcribes : function () {
        jQuery(document).on('click','.resubcribe',function () {
            var relatedId = $(this).attr('relatedid');
            var recordid = $('[name="record_id"]').val();
            var params = {
                module: 'VTEEmailMarketing',
                action: 'ActionAjax',
                mode: 'actionResubcribes',
                "crmid": relatedId,
                "recordId": recordid,
            };

            app.helper.showProgress();
            AppConnector.request(params).then(
                function (data) {
                    if(data.result.success == true){
                        $(document).find('#unsubcribes_email').text(data.result.unsubcribe);
                        app.helper.hideProgress();
                        var message = data.result;
                        bootbox.alert("Resubcribe success !",function () {
                            $('[data-type="unsubcribes"]').trigger('click');
                        });
                    }
                }
            )
        });

    },

    registerPreviewEmailTemplate: function () {
        var thisInstance = this;
        jQuery(document).on('click','#PreviewEmailTemplate', function () {
            var record = jQuery('input[name="emailTemplateId"]').val();
            var params = {
                'module': 'EmailTemplates',
                'view': "ListAjax",
                "mode": "previewTemplate",
                "record": record
            };
            app.helper.showProgress();
            app.request.post({data: params}).then(function (error, data) {
                app.helper.loadPageContentOverlay(data).then(function () {
                    thisInstance.showTemplateContent(record);

                });
            });
        });
    },
    
    showTemplateContent: function (record) {
        var params = {
            "module": "EmailTemplates",
            "action": "ShowTemplateContent",
            "mode": "getContent",
            "record": record
        };
        app.request.post({data: params}).then(function (error, data) {
            var templateContent = data.content;
            jQuery('#TemplateIFrame').contents().find('html').html(templateContent);
            app.helper.hideProgress();
        });
    },
    
    registerChangeDisplyType: function () {
        var thisInstance = this;
        $("body").delegate(".DisplayType", "click", function(){
            var displayType = $(this).attr("data-type");
            var currentDisplayType = $("#hfDispayType").val();
            if (displayType != currentDisplayType){
                $(".DisplayType").removeClass("selectedDT");
                $("#hfDispayType").val(displayType);
                $(this).addClass("selectedDT");
            }
            thisInstance.registerChangePage(1);
        });
    },

    registerEvents : function () {
        this._super();
        this.registerRecordRowClickEvent();
        this.registerNextPageButtonClick();
        this.registerPreviousPageButtonClick();
        this.registerPageJumpButtonClick();
        this.registerClickShowJump();
        this.registerClickPageToJump();
        this.registerActionScheduler();
        this.registerPreviewEmailTemplate();
        this.registerChangeDisplyType();
        this.registerResubcribes();
    }
});
