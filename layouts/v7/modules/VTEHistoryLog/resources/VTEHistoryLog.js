/* * *******************************************************************************
 * The content of this file is subject to the VTE History Log ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class('Vtiger_VTEAHistoryLog_Js', {
    showUpdates : function(element){
        var parentElement = $(element).closest('.details');

        parentElement.find(".historyButtons").find("button").removeAttr("disabled").removeClass("btn-success");
        var currentElement = jQuery(element);
        currentElement.attr("disabled","disabled").addClass("btn-success");

        var params = [];
        var recordId = jQuery('#recordId').val();
        params.url = "index.php?view=Detail&module="+app.getModuleName()+"&mode=showRecentActivities&record="+recordId;

        app.helper.showProgress();
        app.request.get(params).then(function(error,response){
            parentElement.html(response);
            //add button again
            var aDeferred = jQuery.Deferred();
            var params = {
                module: 'VTEHistoryLog',
                view: 'HistoryAjax',
                mode: 'addButton',
                page: 1,
                recordId: app.getRecordId(),
                sourceModule: app.getModuleName()
            };
            app.request.post({data: params}).then(function(err, data) {
                if(err===null){
                    if(parentElement.find('.vteHistoryButtons').length==0) {
                        parentElement.find('.recentActivitiesContainer').prepend(data);
                        parentElement.find('.historyButtons button').attr('disabled', true);
                        parentElement.find('.vteHistoryButtons button').removeAttr('disabled');
                        parentElement.find('.vteHistoryButtons').next(".btn-group").remove();
                    }
                }
                app.helper.hideProgress();
            });
            return aDeferred.promise();
        });
    },

    showHistoryLog: function (element) {
        $(element).attr('disabled', true);
        var thisInstance = this;
        var currentModule = app.getModuleName();
        var VTEAHistoryLog_Js_Instance = new Vtiger_VTEAHistoryLog_Js();
        var current_vtiger_version = VTEAHistoryLog_Js_Instance.getCurrentVtigerVersion();
        var filterModule = $(".active_modules li.active");
        if (filterModule.length > 0){
            filterModule = filterModule.data("module");
        } else {
            filterModule = '';
        }
        if((currentModule=='Leads' || currentModule=='Accounts' || currentModule=='Contacts') && current_vtiger_version != '7.1.0') {
            var container = $('.details>.HistoryContainer');
            var recordId = jQuery('#recordId').val();
            if (container.find('.data-body').length > 0) {
                var aDeferred = jQuery.Deferred();
                var params = {
                    module: 'VTEHistoryLog',
                    view: 'HistoryAjax',
                    mode: 'showHistoryLog',
                    sourceModule: app.getModuleName(),
                    record: recordId,
                    filterModule: filterModule
                };
                app.helper.showProgress();
                app.request.post({data: params}).then(function (err, data) {
                    if (err === null) {
                        container.find(".data-body").html(data);
                        container.find('.historyButtons button').removeAttr('disabled');
                        app.helper.hideProgress();
                        thisInstance.registerModuleQtips(container);
                    }
                });
                return aDeferred.promise();
            }
        }else{
            var detailViewInstance = new Vtiger_Detail_Js();
            var detailViewContainer = detailViewInstance.getDetailViewContainer();
            var container = detailViewContainer.find('.recentActivitiesContainer').closest('.details');
            var recordId = jQuery('#recordId').val();
            if (container.length > 0) {
                var aDeferred = jQuery.Deferred();
                var params = {
                    module: 'VTEHistoryLog',
                    view: 'HistoryAjax',
                    mode: 'showHistoryLog',
                    sourceModule: app.getModuleName(),
                    record: recordId,
                    filterModule: filterModule
                };
                app.helper.showProgress();
                app.request.post({data: params}).then(function (err, data) {
                    if (err === null) {
                        container.html(data);
                        container.find('.historyButtons button').removeAttr('disabled');

                        thisInstance.registerModuleQtips(container);

                        //add button again
                        var aDeferred = jQuery.Deferred();
                        var params = {
                            module: 'VTEHistoryLog',
                            view: 'HistoryAjax',
                            mode: 'addButton',
                            page: 1,
                            recordId: app.getRecordId(),
                            filterModule: filterModule,
                            sourceModule: app.getModuleName()
                        };
                        app.request.post({data: params}).then(function(err, data) {
                            if(err===null){
                                if(container.find('.vteHistoryButtons').length==0) {
                                    container.find('.recentActivitiesContainer').prepend(data);
                                    container.find('.vteHistoryButtons button').attr('disabled', true);
                                    container.find('.historyButtons button').removeAttr('disabled');
                                }
                            }
                            app.helper.hideProgress();
                        });
                        return aDeferred.promise();
                    }
                });
                return aDeferred.promise();
            }
        }
    },

    showHistoryLogMore: function (el) {
        var thisInstance = this;
        var container = $(el).closest('#updates');
        app.helper.showProgress();
        var currentPage = container.find("#updatesCurrentPage").val();
        var recordId = app.getRecordId();
        var sourceModule = app.getModuleName();
        var nextPage = parseInt(currentPage) + 1;
        var url = "index.php?module=VTEHistoryLog&view=HistoryAjax&record="
            + recordId + "&sourceModule="+sourceModule+"&mode=showHistoryLogMore&page="
            + nextPage + "&tab_label=LBL_UPDATES";
        var postParams  = app.convertUrlToDataParams(url);

        app.request.post({data:postParams}).then(function(err,data){
            container.find("#updatesCurrentPage").val(nextPage);
            container.find('#more_button').remove();
            container.find('ul.updates_timeline').append(data);
            app.helper.hideProgress();
            thisInstance.registerModuleQtips(container);
        });
    },

    registerModuleQtips : function(container) {
        container.find('.module-qtip').qtip({
            position: {
                my: 'left center',
                at: 'center right',
                adjust: {
                    y: 1
                }
            },
            style: {
                classes: 'qtip-dark qtip-shadow module-name-tooltip'
            },
            show: {
                delay: 500
            }
        });
    },

    showHistoryLogMoreField: function (el) {
        var container = $(el).closest('.update_info_block_content');
        container.find('.updateInfoContainer').each(function () {
           if($(this).hasClass('hide')){
               $(this).addClass('show-more-fields');
           };
        });
        container.find('.updateInfoContainer-show-more').addClass('hide');
        container.find('.updateInfoContainer-show-less').removeClass('hide');
    },

    showHistoryLogLessField: function (el) {
        var container = $(el).closest('.update_info_block_content');
        container.find('.updateInfoContainer').each(function () {
            if($(this).hasClass('hide')){
                $(this).removeClass('show-more-fields');
            };
        });
        container.find('.updateInfoContainer-show-more').removeClass('hide');
        container.find('.updateInfoContainer-show-less').addClass('hide');
    },

    showHistoryLogMoreCharacter: function (el) {
        var container = $(el).closest('.update_info_block_content');
        var full_content = container.data('fullcontent');
        container.find('.updateInfoContainer-full-content').text(full_content);
        container.find('.updateInfoContainer-show-more').addClass('hide');
        container.find('.updateInfoContainer-show-less').removeClass('hide');
    },

    showHistoryLogLessCharacter: function (el) {
        var container = $(el).closest('.update_info_block_content');
        var less_content = container.data('lesscontent');
        container.find('.updateInfoContainer-full-content').text(less_content);
        container.find('.updateInfoContainer-show-more').removeClass('hide');
        container.find('.updateInfoContainer-show-less').addClass('hide');
    },

    showHistoryLogMoreEmailCharacter: function (el) {
        var container = $(el).closest('.update_info_block_content');
        var full_content = container.find('.updateInfoContainer-show-more').data('fullcontent');
        container.find('.created-emails-description').text(full_content);
        container.find('.updateInfoContainer-show-more').addClass('hide');
        container.find('.updateInfoContainer-show-less').removeClass('hide');
    },

    showHistoryLogLessEmailCharacter: function (el) {
        var container = $(el).closest('.update_info_block_content');
        var less_content = container.find('.updateInfoContainer-show-less').data('lesscontent');
        container.find('.created-emails-description').text(less_content);
        container.find('.updateInfoContainer-show-more').removeClass('hide');
        container.find('.updateInfoContainer-show-less').addClass('hide');
    },

    showDetailOverlay: function (url) {
        if(typeof url != "undefined"){
            var params = app.convertUrlToDataParams(url);
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
    },

    showComposeEmail: function (record) {
        var params = {};
        params['module'] = "Emails";
        params['view'] = "ComposeEmail";
        //params['parentId'] = 2;
        params['relatedLoad'] = true;
        //params['parentModule'] = app.getModuleName();
        params['mode'] = 'emailPreview';
        params['record'] = record;
        app.helper.showProgress();
        app.request.post({data:params}).then(function(err,data){
            app.helper.hideProgress();
            if(err === null){
                var dataObj = jQuery(data);
                var descriptionContent = dataObj.find('#iframeDescription').val();
                app.helper.showModal(data,{cb:function(){
                    app.event.trigger('post.EmailPreview.load',null);
                    jQuery('#emailPreviewIframe').contents().find('html').html(descriptionContent);
                    jQuery("#emailPreviewIframe").height(jQuery('.email-body-preview').height());
                    jQuery('#emailPreviewIframe').contents().find('html').find('a').on('click', function(e) {
                        e.preventDefault();
                        var url = jQuery(e.currentTarget).attr('href');
                        window.open(url, '_blank');
                    });
                }});
            }
        });
    }

}, {
    initialize: function(){
        var detailViewInstance = new Vtiger_Detail_Js();
        var detailViewContainer = detailViewInstance.getDetailViewContainer();
        var currentModule = app.getModuleName();
        var current_vtiger_version = this.getCurrentVtigerVersion();
        if((currentModule=='Leads' || currentModule=='Accounts' || currentModule=='Contacts') && current_vtiger_version != '7.1.0'){
            var container = detailViewContainer.find('.HistoryContainer');
            if(container.find('.vteHistoryButtons').length==0){
                var aDeferred = jQuery.Deferred();
                var params = {
                    module: 'VTEHistoryLog',
                    view: 'HistoryAjax',
                    mode: 'addButton',
                    page: 1,
                    recordId: app.getRecordId(),
                    sourceModule: app.getModuleName()
                };
                app.request.post({data: params}).then(function(err, data) {
                    if(err===null){
                        if(container.find('.vteHistoryButtons').length==0) {
                            container.find("div:first-child").after(data);
                            var automatically_show = $(data).find('button').data('automatically-show');
                            if(automatically_show===1){
                                container.find('.vteHistoryButtons button').trigger('click');
                            }
                        }
                    }
                });
                return aDeferred.promise();
            }
        }else{
            var container = detailViewContainer.find('.recentActivitiesContainer');
            if(container.find('.vteHistoryButtons').length==0){
                var aDeferred = jQuery.Deferred();
                var params = {
                    module: 'VTEHistoryLog',
                    view: 'HistoryAjax',
                    mode: 'addButton',
                    page: 1,
                    recordId: app.getRecordId(),
                    sourceModule: app.getModuleName()
                };
                app.request.post({data: params}).then(function(err, data) {
                    if(err===null){
                        if(container.find('.vteHistoryButtons').length==0) {
                            container.prepend(data);
                            var automatically_show = container.find('.vteHistoryButtons button').data('automatically-show');
                            if(automatically_show===1){
                                container.find('.vteHistoryButtons button').trigger('click');
                            }
                        }
                    }
                });
                return aDeferred.promise();
            }
        }
    },

    getCurrentVtigerVersion: function () {
        var js_script_params = this.getParams('layouts/v7/modules/VTEHistoryLog/resources/VTEHistoryLog.js');
        if(jQuery.isEmptyObject(js_script_params)){
            js_script_params.v = '7.0.1';
        }

        return js_script_params.v;
    },

    // Extract "GET" parameters from a JS include querystring
    getParams: function (script_name) {
        // Find all script tags
        var scripts = document.getElementsByTagName("script");

        // Look through them trying to find ourselves
        for(var i=0; i<scripts.length; i++) {
            if(scripts[i].src.indexOf(script_name) > -1) {
                // Get an array of key=value strings of params
                var pa = scripts[i].src.split("?").pop().split("&");

                // Split each key=value into array, the construct js object
                var p = {};
                for(var j=0; j<pa.length; j++) {
                    var kv = pa[j].split("=");
                    p[kv[0]] = kv[1];
                }
                return p;
            }
        }

        // No scripts match
        return {};
    }

});

//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_VTEHistoryLog();
    }, 10000);
});
function initData_VTEHistoryLog() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTEHistoryLog')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208
        
    // Event for filter module
    $("body").undelegate(".active_modules li", "click");
    $("body").delegate(".active_modules li", "click", function(){
        if ($(this).hasClass("active")){
            $(".active_modules li").removeClass("active");
        } else {
            $(".active_modules li").removeClass("active");
            $(this).addClass("active");
        }
        Vtiger_VTEAHistoryLog_Js.showHistoryLog(".vteHistoryButtons button");
    });
        
    // Event for filter module
    $("body").undelegate(".btnResetFilter", "click");
    $("body").delegate(".btnResetFilter", "click", function(){
        if ($(".active_modules li.active").length > 0){
            $(".active_modules li").removeClass("active");
            Vtiger_VTEAHistoryLog_Js.showHistoryLog(".vteHistoryButtons button");
        }
    });

    jQuery( document ).ajaxComplete(function(event, request, settings) {
        var request_url = settings.url;
        if(request_url.indexOf('index.php?view=Detail&mode=showHistory') != -1 || (request_url.indexOf('mode=showRecentActivities') != -1 && request_url.indexOf('view=Detail') != -1 && request_url.indexOf('LBL_UPDATE') != -1)){
            if(request.status==200){
                var instance = new Vtiger_VTEAHistoryLog_Js();
                instance.initialize();
            }
        }
        if(request_url.indexOf('mode=showRecentActivities') != -1 && request_url.indexOf('view=Detail') != -1){
            if($('.vteHistoryButtons').length>0) {
                $('.vteHistoryButtons button').removeAttr('disabled');
            }
        }
    });

    //execute VTEHistory function when refresh page
    var current_url = window.location.href;
    if(current_url.indexOf('index.php?view=Detail&mode=showHistory') != -1 || (current_url.indexOf('mode=showRecentActivities') != -1 && current_url.indexOf('view=Detail') != -1 && current_url.indexOf('LBL_UPDATE') != -1)){
        var instance = new Vtiger_VTEAHistoryLog_Js();
        instance.initialize();
    }
}