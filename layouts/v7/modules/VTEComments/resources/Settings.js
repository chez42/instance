/* ********************************************************************************
 * The content of this file is subject to the Comments (Advanced) ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEComments_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(VTEComments_Settings_Js.instance == false){
            var instance = new VTEComments_Settings_Js();
            VTEComments_Settings_Js.instance = instance;
            return instance;
        }
        return VTEComments_Settings_Js.instance;
    }
},{
    /* For License page - Begin */
    init : function() {
        this.initiate();
    },
    /*
     * Function to initiate the step 1 instance
     */
    initiate : function(){
        var step=jQuery(".installationContents").find('.step').val();
        this.initiateStep(step);
    },
    /*
     * Function to initiate all the operations for a step
     * @params step value
     */
    initiateStep : function(stepVal) {
        var step = 'step'+stepVal;
        this.activateHeader(step);
    },

    activateHeader : function(step) {
        var headersContainer = jQuery('.crumbs ');
        headersContainer.find('.active').removeClass('active');
        jQuery('#'+step,headersContainer).addClass('active');
    },

    registerActivateLicenseEvent : function() {
        var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
            var license_key=jQuery('#license_key');
            if(license_key.val()=='') {
                app.helper.showAlertBox({message:"License Key cannot be empty"});
                aDeferred.reject();
                return aDeferred.promise();
            }else{
                app.helper.showProgress();
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();

                app.request.post({data:params}).then(
                    function(err,data) {
                        app.helper.hideProgress();
                        if(err == null){
                            var message=data['message'];
                            if(message !='Valid License') {
                                app.helper.hideProgress();
                                app.helper.hideModal();
                                app.helper.showAlertNotification({'message':data['message']});
                            }else{
                                document.location.href="index.php?module=VTEComments&parent=Settings&view=Settings&mode=step3";
                            }
                        }
                    },
                    function(error) {
                        app.helper.hideProgress();
                    }
                );
            }
        });
    },
    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            app.helper.showProgress();
            var data = {};
            data['module'] = 'VTEComments';
            data['action'] = 'Activate';
            data['mode'] = 'valid';
            app.request.post({data:data}).then(
                function (err,data) {
                    if(err == null){
                        app.helper.hideProgress();
                        if (data) {
                            document.location.href = "index.php?module=VTEComments&parent=Settings&view=Settings";
                        }
                    }
                }
            );
        });
    },
    /* For License page - End */

    registerEnableModuleEvent:function() {
        jQuery('.summaryWidgetContainer').find('#enable_module').change(function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });

            var element=e.currentTarget;
            var value=0;
            var text="Comments (Advanced) Disabled";
            if(element.checked) {
                value=1;
                text = "Comments (Advanced) Enabled";
            }
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'VTEComments';
            params.value = value;
            params.mode = 'enableModule';
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {};
                    params['text'] = text;
                    Settings_Vtiger_Index_Js.showMessage(params);
                },
                function(error){
                    //TODO : Handle error
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                }
            );
        });
    },
    registerChangeCheckBoxToSwitch: function(){
        var ele = $("input[name='picklist_checkbox']");
        if(ele.length>0) {
            ele.bootstrapSwitch();
            ele = $("input[name='text_field_checkbox']").bootstrapSwitch();
            ele = $("input[name='enable_richtext']").bootstrapSwitch();
            ele = $("input[name='tag_feature']").bootstrapSwitch();
            ele = $("input[name='row_to_show']").bootstrapSwitch();
            ele = $("input[name='always_show']").bootstrapSwitch();
            ele = $("input[name='email_ticket']").bootstrapSwitch();
        }
        $('.enable-rich-text-tooltip').popover({container: 'body'});
        $('.enable-mention-tag-tooltip').popover({container: 'body'});
        $('.enable-email-templates-tooltip').popover({container: 'body'});
        $('.enable-show-default-tooltip').popover({container: 'body'});
        $('.limit-characters-tooltip').popover({container: 'body'});
        $('.order-by-comments-tooltip').popover({container: 'body'});
        $('.comments-picklist-tooltip').popover({container: 'body'});
        $('.comments-text-tooltip').popover({container: 'body'});
        $('.always-show-tooltip').popover({container: 'body'});
    },
    registerEventForSwitch: function(){
        $("input[name='picklist_checkbox']").on('switchChange.bootstrapSwitch',function(event,state){
            var fieldid = this.attributes['data-field-id'].value;
            var fieldName = this.attributes['data-field-name'].value;
            var picklistLabel = $('input.picklistLabel[data-field-name="'+fieldName+'"]');
            var buttonSavePicklistLabel = $('button.savePickListLabel[data-field-name="'+fieldName+'"]');
            var linkToSettingPicklist = $('a.linkToSettingPicklist[data-field-name="'+fieldName+'"]');
            var params = {};
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'eventEnableDisablePicklist';
            params['state'] = state;
            params['fieldid'] = fieldid;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                        if(state == true){
                            picklistLabel.removeAttr('disabled');
                            buttonSavePicklistLabel.removeAttr('disabled');
                            linkToSettingPicklist.removeAttr('disabled');
                        }else{
                            picklistLabel.attr('disabled','disabled');
                            buttonSavePicklistLabel.attr('disabled','disabled');
                            linkToSettingPicklist.attr('disabled','disabled');
                        }
                    }
                }
            );
        });
        $("input[name='text_field_checkbox']").on('switchChange.bootstrapSwitch',function(event,state){
            var fieldid = this.attributes['data-field-id'].value;
            var fieldName = this.attributes['data-field-name'].value;
            var picklistLabel = $('input.text_fieldLabel[data-field-name="'+fieldName+'"]');
            var buttonSavePicklistLabel = $('button.saveTextFieldLabel[data-field-name="'+fieldName+'"]');
            var params = {};
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'eventEnableDisablePicklist';
            params['state'] = state;
            params['fieldid'] = fieldid;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                        if(state == true){
                            picklistLabel.removeAttr('disabled');
                            buttonSavePicklistLabel.removeAttr('disabled');
                        }else{
                            picklistLabel.attr('disabled','disabled');
                            buttonSavePicklistLabel.attr('disabled','disabled');
                        }
                    }
                }
            );
        });
    },
    registerEventForSavePicklistLabel : function(){
        $('button.savePickListLabel').on('click', function(){
            var fieldid = this.attributes['data-field-id'].value;
            var fieldName = this.attributes['data-field-name'].value;
            var label = $('input.picklistLabel[data-field-name="'+fieldName+'"]').val();
            var params = {};
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'eventSavePicklistLabel';
            params['label'] = label;
            params['fieldid'] = fieldid;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                }
            );
        });
        $('button.saveTextFieldLabel').on('click', function(){
            var fieldid = this.attributes['data-field-id'].value;
            var fieldName = this.attributes['data-field-name'].value;
            var label = $('input.text_fieldLabel[data-field-name="'+fieldName+'"]').val();
            var params = {};
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'eventSavePicklistLabel';
            params['label'] = label;
            params['fieldid'] = fieldid;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                }
            );
        });
    },
    registerEventEnableRichText: function(){
        $("input[name='enable_richtext']").on('switchChange.bootstrapSwitch',function(event,state){
            var params = {};
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'doEnableRichText';
            params['state'] = state;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                },
                function(error){
                    //TODO : Handle error
                }
            );
        });
    },
    registerEventEnableTagFeature: function(){
        $("input[name='tag_feature']").on('switchChange.bootstrapSwitch',function(event,state){
            var params = {};
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'doEnableTagFeature';
            params['state'] = state;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                },
                function(error){
                    //TODO : Handle error
                }
            );
        });
    },
    registerEventRowToShow: function(){
        $("input[name='row_to_show']").on('switchChange.bootstrapSwitch',function(event,state){
            var params = {};
            var text_length = jQuery(".text_length").val();
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'doUpdateStatus';
            params['state'] = state;
            params['field_name'] = 'row_to_show';
            params['text_length'] = text_length;
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                },
                function(error){
                    //TODO : Handle error
                }
            );
        });
        $("input[name='always_show']").on('switchChange.bootstrapSwitch',function(event,state){
            var params = {};
            var text_length = jQuery(".text_length").val();
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'doUpdateStatus';
            params['state'] = state;
            params['field_name'] = 'always_show';
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                },
                function(error){
                    //TODO : Handle error
                }
            );
        });
        $("input[name='email_ticket']").on('switchChange.bootstrapSwitch',function(event,state){
            var params = {};
            var text_length = jQuery(".email_ticket").val();
            params['action']= 'ActionAjax';
            params['module'] = 'VTEComments';
            params['mode'] = 'doUpdateStatus';
            params['state'] = state;
            params['field_name'] = 'email_ticket';
            app.request.post({data:params}).then(
                function (err,data) {
                    if(err == null) {
                        var status = data.status;
                        app.helper.showSuccessNotification({'message': status});
                    }
                },
                function(error){
                    //TODO : Handle error
                }
            );
        });
    },
    registerDownLoadAPILib: function(){
        $('#download_api_button').on('click', function(){
            app.helper.showProgress(app.vtranslate('Downloading...'));
            //
            var params = {
                type: 'GET',
                url: 'index.php',
                dataType: 'json',
                data: {
                    module: 'VTEComments',
                    action: 'ActionAjax',
                    mode: 'downLoaddompdf'
                }
            };
            app.request.post(params).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if (err === null) {
                        window.location.reload();
                    } else {
                        app.helper.showErrorNotification({message:'DOWNLOAD_ERROR'});
                    }
                }
            );
        });
    },
    registerSaveSettingModuleEvent:function() {
        jQuery('#btnSettingSave').click(function() {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            app.helper.showProgress();
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'VTEComments';
            params.text_length = jQuery(".text_length").val();
            params.order_by = jQuery("#slbOrderBy").val();
            params.mode = 'doSaveSetting';
            app.request.post({data:params}).then(
                function (errs, res) {
                    if (errs === null){
                        app.helper.hideProgress();
                        app.helper.hideModal();
                        app.helper.showSuccessNotification({'message':'Saved!'});
                    }
                }
            );
        });
    },
    registerEvents: function(){
        this.registerEnableModuleEvent();
        /* For License page - Begin */
        this.registerChangeCheckBoxToSwitch();
        this.registerEventForSwitch();
        this.registerEventForSavePicklistLabel();
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerEventEnableRichText();
        this.registerEventEnableTagFeature();
        this.registerEventRowToShow();
        this.registerDownLoadAPILib();
        this.registerSaveSettingModuleEvent();
    }
});
jQuery(document).ready(function() {
    var instance = new VTEComments_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});