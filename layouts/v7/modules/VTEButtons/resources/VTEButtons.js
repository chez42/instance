/* ********************************************************************************
 * The content of this file is subject to the Custom Header/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEButtons_Js", {
    instance: false,
    getInstance: function () {
        if (VTEButtons_Js.instance == false) {
            var instance = new VTEButtons_Js();
            VTEButtons_Js.instance = instance;
            return instance;
        }
        return VTEButtons_Js.instance;
    }
},{
    registerShowOnDetailView:function(){
        var self = this;
        var params = {};
        params['module'] = 'VTEButtons';
        params['view'] = 'HeaderIcon';
        params['record'] = app.getRecordId();
        params['moduleSelected'] = app.getModuleName();
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null){
                    var detailview_header = jQuery('.detailview-header .row:first');
                    detailview_header.append(data);
                    $("#div_vtebuttons").fadeIn(700);
                    self.registerEventsQuickUpdate();
                    var custom_header = $("#div_custome_header");
                    if(custom_header.length>0) {
                        $("#div_custome_header").insertAfter('#div_vtebuttons');
                        var detailview_header_w = detailview_header.width();
                        var offset = $("#div_vtebuttons").offset();
                        var offset1 = custom_header.offset();
                        if (offset.left != offset1.left) {
                            var left = detailview_header_w * 0.22 + offset.left - 45;
                            $("#div_custome_header").css({'left': left + 'px'});
                        }
                    }
                }
            },
            function(error) {
            }
        );

    },
    registerShowVTEButtons:function(){
        var self = this;
        var params = {};
        params['module'] = 'VTEButtons';
        params['view'] = 'HeaderIcon';
        params['record'] = app.getRecordId();
        params['moduleSelected'] = app.getModuleName();
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null){
                    var detailview_header = jQuery('.detailview-header .row:first');
                    detailview_header.append(data);
                    $("#div_vtebuttons").fadeIn(700);
                    var custom_header = $("#div_custome_header");
                    if(custom_header.length>0) {
                        $("#div_custome_header").insertAfter('#div_vtebuttons');
                        var detailview_header_w = detailview_header.width();
                        var offset = $("#div_vtebuttons").offset();
                        var offset1 = custom_header.offset();
                        if (offset.left != offset1.left) {
                            var left = detailview_header_w * 0.22 + offset.left - 45;
                            $("#div_custome_header").css({'left': left + 'px'});
                        }
                    }
                }
            },
            function(error) {
            }
        );

    },
    registerEventsQuickUpdate:function(){
        var thisInstance = this;
        var postVTEButtonsSave  = function(data) {
            var viewHeight = jQuery('.detailview-header').height();
            jQuery('.detailview-header').css({'height':viewHeight+'px'})
            jQuery('#div_vtebuttons').remove();
            thisInstance.registerShowVTEButtons();
        }
        jQuery('body').delegate('.vteButtonQuickUpdate','click',function(e){
            var target = jQuery(e.currentTarget);
            var vteButtonId = target.data('vtebuttonid');
            var viewEditUrl = "module=VTEButtons&view=QuickEditAjax&record="+app.getRecordId()+"&moduleEditName="+app.getModuleName()+"&vteButtonId="+vteButtonId;
            var params= {'callbackFunction':postVTEButtonsSave,'noCache':true};
            thisInstance.getVTEButtonsQuickEditForm(viewEditUrl, app.getModuleName(), params).then(function(data) {
                thisInstance.handleVTEButtonsQuickEditData(data, params);
                var form = jQuery("#recordEditView");
                var Edit_Js = new Vtiger_Edit_Js();
                Edit_Js.registerEventForPicklistDependencySetup(form);
                Edit_Js.registerFileElementChangeEvent(form);
                Edit_Js.registerAutoCompleteFields(form);
                Edit_Js.registerClearReferenceSelectionEvent(form);
                Edit_Js.referenceModulePopupRegisterEvent(form);
                Edit_Js.registerPostReferenceEvent(Edit_Js.getEditViewContainer());
                Edit_Js.registerEventForImageDelete();
                Edit_Js.registerImageChangeEvent();
                vtUtils.applyFieldElementsView(form);
                app.helper.hideProgress();
            });
        });
    },
    getVTEButtonsQuickEditForm: function(url, moduleName, params) {
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        var requestParams;
        if (typeof params == 'undefined') {
            params = {};
        }
        if ((!params.noCache) || (typeof (params.noCache) == "undefined")) {
            if (typeof app.helper.quickCreateModuleCache['edit_'+moduleName] != 'undefined') {
                aDeferred.resolve(app.helper.quickCreateModuleCache['edit_'+moduleName]);
                return aDeferred.promise();
            }
        }
        requestParams = url;
        if (typeof params.data != "undefined") {
            var requestParams = {};
            requestParams['data'] = params.data;
            requestParams['url'] = url;
        }
        app.request.post({'data':requestParams}).then(
            function(err,data){
                if(err === null) {
                    if ((!params.noCache) || (typeof (params.noCache) == "undefined")) {
                        app.helper.quickCreateModuleCache['edit_'+moduleName] = data;
                    }
                    aDeferred.resolve(data);
                }else{
                }
            }
        );

        return aDeferred.promise();
    },
    handleVTEButtonsQuickEditData: function(data, params) {
        if (typeof params == 'undefined') {
            params = {};
        }
        var thisInstance = this;
        app.helper.showModal(data,{'cb' : function (data){
            var quickEditForm = data.find('form[name="vteButtonQuickEdit"]');
            var moduleName = quickEditForm.find('[name="module"]').val();
            var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName(moduleName);
            editViewInstance.registerBasicEvents(quickEditForm);
            quickEditForm.vtValidate(app.validationEngineOptions);

            if (typeof params.callbackPostShown != "undefined") {
                params.callbackPostShown(quickEditForm);
            }
            thisInstance.registerVTEButtonsPostLoadEvents(quickEditForm, params);
            var quickCreateContent = quickEditForm.find('.quickCreateContent');
            var quickCreateContentHeight = quickCreateContent.height();
            var contentHeight = parseInt(quickCreateContentHeight);
            if (contentHeight > 300) {
                app.helper.showVerticalScroll(quickCreateContent, {setHeight: '300px'});
            }
        }});
    },
    registerVTEButtonsPostLoadEvents: function(form, params) {
        var thisInstance = this;
        var submitSuccessCallbackFunction = params.callbackFunction;
        var goToFullFormCallBack = params.goToFullFormcallback;
        if (typeof submitSuccessCallbackFunction == 'undefined') {
            submitSuccessCallbackFunction = function() {
            };
        }
        form.find("button[name='vteButtonsSave']").on('click', function(e) {
            var form = jQuery(e.currentTarget).closest('form');
            var module = form.find('[name="module"]').val();
            var aDeferred = jQuery.Deferred();
            var params = {
                submitHandler: function (frm) {
                    jQuery("button[name='vteButtonsSave']").attr("disabled", "disabled");
                    if (this.numberOfInvalids() > 0) {
                        return false;
                    }
                    var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                    app.event.trigger(e);
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    var formData = jQuery(frm).serialize();
                    app.helper.showProgress();
                    app.request.post({data: formData}).then(function (err, data) {
                        if (!err) {
                            aDeferred.resolve(data);
                            var parentModule=app.getModuleName();
                            var viewname=app.getViewName();
                            if((module == parentModule) && (viewname=="List")){
                                var listinstance = new Vtiger_List_Js();
                                listinstance.getListViewRecords();
                            }
                            submitSuccessCallbackFunction(data);
                        } else {
                            app.helper.showErrorNotification({"message": err});
                        }
                        app.helper.hideModal();
                        app.helper.hideProgress();
                    });
                }
            };
            form.vtValidate(params);
            form.submit();
        });
    },
    registerEvents: function(){
        this.registerShowOnDetailView();

    }
});

jQuery(document).ready(function () {
	// Only load when loadHeaderScript=1 BEGIN #241208
	if (typeof VTECheckLoadHeaderScript == 'function') {
		if (!VTECheckLoadHeaderScript('VTEButtons')) {
			return;
		}
	}
	// Only load when loadHeaderScript=1 END #241208
	
    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    if(viewName == 'Detail'){
        var instance = new VTEButtons_Js();
        instance.registerEvents();
    }
});

jQuery( document ).ajaxComplete(function(event, xhr, settings) {
    var url = settings.data;
    if(typeof url == 'undefined' && settings.url) url = settings.url;
    if(Object.prototype.toString.call(url) =='[object String]') {
        if (url.indexOf('module=VTEButtons') != -1 && url.indexOf('action=ActionAjax') != -1 && url.indexOf('mode=doUpdateFields') > -1){
            app.helper.showSuccessNotification({'message':'Record Updated!'});
            $('li.active').trigger('click');
        }
        if (url.indexOf('module=VTEButtons') != -1 && url.indexOf('view=HeaderIcon') != -1){
            setTimeout(function() {
                var detailview_header = jQuery('.detailview-header .row:first');
                var custom_header = $("#div_custome_header");
                if(custom_header.length>0) {
                    $("#div_custome_header").insertAfter('#div_vtebuttons');
                    var detailview_header_w = detailview_header.width();
                    var offset = $("#div_vtebuttons").offset();
                    var offset1 = custom_header.offset();
                    if (offset.left != offset1.left) {
                        var left = detailview_header_w * 0.22 + offset.left - 45;
                        $("#div_custome_header").css({'left': left + 'px'});
                    }
                }
            }, 1000);
        }
    }
})

