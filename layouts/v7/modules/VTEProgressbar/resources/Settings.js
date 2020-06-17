/* ********************************************************************************
 * The content of this file is subject to the Progressbar/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEProgressbar_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(VTEProgressbar_Settings_Js.instance == false){
            var instance = new VTEProgressbar_Settings_Js();
            VTEProgressbar_Settings_Js.instance = instance;
            return instance;
        }
        return VTEProgressbar_Settings_Js.instance;
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
                                document.location.href="index.php?module=VTEProgressbar&parent=Settings&view=Settings&mode=step3";
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
            data['module'] = 'VTEProgressbar';
            data['action'] = 'Activate';
            data['mode'] = 'valid';
            app.request.post({data:data}).then(
                function (err,data) {
                    if(err == null){
                        app.helper.hideProgress();
                        if (data) {
                            document.location.href = "index.php?module=VTEProgressbar&parent=Settings&view=Settings";
                        }
                    }
                }
            );
        });
    },
    /* For License page - End */
    registerSwitchStatus:function(){
        jQuery("input[name='progressbar_status']").bootstrapSwitch();
        jQuery('input[name="progressbar_status"]').on('switchChange.bootstrapSwitch', function (e) {
            var currentElement = jQuery(e.currentTarget);
            var active = false;
            var progressbar_module = currentElement.data('module');
            var record_id = currentElement.data('id');
            if(currentElement.val() == 'on'){
                currentElement.attr('value','off');
            } else {
                active = true;
                currentElement.attr('value','on');
            }
            var params = {
                module : 'VTEProgressbar',
                'action' : 'ActionAjax',
                'mode' : 'UpdateStatus',
                'record' : record_id,
                'progressbar_module' :progressbar_module,
                'status' : currentElement.val()
            }
            AppConnector.request(params).then(function(data){
                if(data){
                    app.helper.showSuccessNotification({
                        message : app.vtranslate('Status changed successfully.')
                    });
                    if(active){
                        location.reload();
                    }
                }
            });
        });
    },
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
            var text="Progressbar Disabled";
            if(element.checked) {
                value=1;
                text = "Progressbar Enabled";
            }
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'VTEProgressbar';
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
    registerDeleteProgressbar:function(){
        $('a#vtprogressbar_delete').on('click',function(e){
            var currentElement = jQuery(e.currentTarget);
            app.helper.showConfirmationBox({
                message: 'Do you want delete this record ?'
            }).then(function () {
                var params = {
                    module : 'VTEProgressbar',
                    'action' : 'ActionAjax',
                    'mode' : 'DeleteRecord',
                    'record' : currentElement.data('id')
                }
                AppConnector.request(params).then(function(data){
                    if(data){
                        window.location.reload();
                    }
                });
            });
        });
    },
    registerRowClick:function(){
        $('tr.listViewEntries td:not(:first-child):not(:last-child)').on('click',function(e){
            var currentElement = jQuery(e.currentTarget);
            var url = currentElement.parent().data('url');
            window.location = url;
        });
    },
    registerEvents: function(){
        this.registerEnableModuleEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerSwitchStatus();
        this.registerDeleteProgressbar();
        this.registerRowClick();
    }
});
jQuery(document).ready(function() {
    var instance = new VTEProgressbar_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});