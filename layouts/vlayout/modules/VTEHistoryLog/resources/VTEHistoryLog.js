/* * *******************************************************************************
 * The content of this file is subject to the VTE History Log ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class('Vtiger_VTEAHistoryLog_Js', {
    showUpdates : function(element){
        var currentElement = jQuery(element);
        var parentElement = currentElement.closest('.vteHistoryButtons');
        parentElement.find("button.btn_history_updates").removeAttr("disabled").addClass("btn-success");
        currentElement.attr("disabled","disabled").removeClass("btn-success");

        var recordId = jQuery('#recordId').val();

        var params = {};
        params['dataType'] = 'html';
        params['module'] = app.getModuleName();
        params['view'] = 'Detail';
        params['mode'] = 'showRecentActivities';
        params['record'] = recordId;
        params['tab_label'] = 'LBL_UPDATES';
        var aDeferred = jQuery.Deferred();
        var progressIndicatorElement = jQuery.progressIndicator({});
        AppConnector.request(params).then(
            function(data){
                if(data) {
                    var detailViewInstance = new Vtiger_Detail_Js();
                    var detailContentsHolder = detailViewInstance.getContentHolder();
                    var content = $(data).html();
                    detailContentsHolder.find('#updates').html(content);
                }
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                aDeferred.resolve(data);
            },
            function(textStatus, errorThrown){
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                aDeferred.reject(textStatus, errorThrown);
            }
        );

        return aDeferred.promise();
    },

    showHistoryLog: function (element) {
        $(element).attr('disabled', true).removeClass('btn-success');
        var thisInstance = this;
        var currentModule = app.getModuleName();
        var detailViewInstance = new Vtiger_Detail_Js();
        var detailContentsHolder = detailViewInstance.getContentHolder();
        var container = detailContentsHolder.find('#updates');
        var recordId = jQuery('#recordId').val();
        if (container.length > 0) {
            var aDeferred = jQuery.Deferred();
            var params = {
                module: 'VTEHistoryLog',
                view: 'HistoryAjax',
                mode: 'showHistoryLog',
                sourceModule: currentModule,
                record: recordId
            };
            var progressIndicatorElement = jQuery.progressIndicator({});
            AppConnector.request(params).then(
                function(data){
                    container.html(data);
                    detailContentsHolder.find('.vteHistoryButtons button.btn_updates').removeAttr('disabled').addClass('btn-success');
                    thisInstance.registerModuleQtips(container);
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    aDeferred.resolve(data);
                },
                function(textStatus, errorThrown){
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    aDeferred.reject(textStatus, errorThrown);
                }
            );

            return aDeferred.promise();
        }
    },

    showHistoryLogMore: function (el) {
        var thisInstance = this;
        var container = $(el).closest('#updates');
        var currentPage = container.find("#updatesCurrentPage").val();
        var recordId = jQuery('#recordId').val();
        var sourceModule = app.getModuleName();
        var nextPage = parseInt(currentPage) + 1;

        var params = {};
        params['module'] = 'VTEHistoryLog';
        params['view'] = 'HistoryAjax';
        params['record'] = recordId;
        params['sourceModule'] = sourceModule;
        params['mode'] = 'showHistoryLogMore';
        params['page'] = nextPage;
        params['tab_label'] = 'LBL_UPDATES';

        var aDeferred = jQuery.Deferred();
        var progressIndicatorElement = jQuery.progressIndicator({});
        AppConnector.request(params).then(
            function(data){
                container.find("#updatesCurrentPage").val(nextPage);
                container.find('#more_button').remove();
                container.find('ul.updates_timeline').append(data);
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                thisInstance.registerModuleQtips(container);
                $('html, body').animate({
                    scrollTop: $('ul.updates_timeline li:last-child').offset().top
                }, 3000);
            },
            function(textStatus, errorThrown){
                progressIndicatorElement.progressIndicator({'mode':'hide'});
                aDeferred.reject(textStatus, errorThrown);
            }
        );

        return aDeferred.promise();
    },

    registerModuleQtips : function(container) {
        container.find('.module-qtip').tooltip({
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
            window.open(url, '_blank');
        }
    },

    showComposeEmail: function (record) {
        var popupInstance = Vtiger_Popup_Js.getInstance();
        var parentId = jQuery('#recordId').val();
        var params = {};
        params['module'] = "Emails";
        params['view'] = "ComposeEmail";
        params['mode'] = "emailPreview";
        params['record'] = record;
        params['parentId'] = parentId;
        params['relatedLoad'] = true;
        popupInstance.show(params);
    }

}, {
    initialize: function(){
        var detailViewInstance = new Vtiger_Detail_Js();
        var detailContentsHolder = detailViewInstance.getContentHolder();
        var currentModule = app.getModuleName();
        if(detailContentsHolder.find('#updates').length>0) {
            if (detailContentsHolder.find('.vteHistoryButtons').length == 0) {
                var aDeferred = jQuery.Deferred();
                var params = {
                    module: 'VTEHistoryLog',
                    view: 'HistoryAjax',
                    mode: 'addButton',
                    page: 1,
                    sourceModule: currentModule
                };
                AppConnector.request(params).then(
                    function (data) {
                        if (data) {
                            if (detailContentsHolder.find('.vteHistoryButtons').length == 0) {
                                detailContentsHolder.prepend(data);
                                var automatically_show = $(data).find('button.btn_history_updates').data('automatically-show');
                                if (automatically_show === 1) {
                                    setTimeout(function () {
                                        detailContentsHolder.find('.vteHistoryButtons .btn_history_updates').trigger('click');
                                    }, 1500);
                                }
                            }
                        }
                        aDeferred.resolve(data);
                    },
                    function (error, err) {

                    }
                );
                return aDeferred.promise();
            }
        }
	},



});

$(document).ready(function () {
    jQuery( document ).ajaxComplete(function(event, request, settings) {
        var request_url = settings.url;
        if(request_url.indexOf('mode=showRecentActivities') != -1 && request_url.indexOf('view=Detail') != -1 && request_url.indexOf('LBL_UPDATES') != -1){
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
    if(current_url.indexOf('mode=showRecentActivities') != -1 && current_url.indexOf('view=Detail') != -1 && current_url.indexOf('LBL_UPDATES') != -1){
        var instance = new Vtiger_VTEAHistoryLog_Js();
        instance.initialize();
    }
});