/* ********************************************************************************
 * The content of this file is subject to the Custom Expenses/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTECustomExpenses_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(VTECustomExpenses_Settings_Js.instance == false){
            var instance = new VTECustomExpenses_Settings_Js();
            VTECustomExpenses_Settings_Js.instance = instance;
            return instance;
        }
        return VTECustomExpenses_Settings_Js.instance;
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
                                document.location.href="index.php?module=VTECustomExpenses&parent=Settings&view=Settings&mode=step3";
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
            data['module'] = 'VTECustomExpenses';
            data['action'] = 'Activate';
            data['mode'] = 'valid';
            app.request.post({data:data}).then(
                function (err,data) {
                    if(err == null){
                        app.helper.hideProgress();
                        if (data) {
                            document.location.href = "index.php?module=VTECustomExpenses&parent=Settings&view=Settings";
                        }
                    }
                }
            );
        });
    },
    /* For License page - End */
    registerSwitchStatus:function(){
        jQuery("input[name='custom_expenses-status']").bootstrapSwitch();
        jQuery('input[name="custom_expenses-status"]').on('switchChange.bootstrapSwitch', function (e) {
            var currentElement = jQuery(e.currentTarget);
            if(currentElement.val() == 'on'){
                currentElement.attr('value','off');
            } else {
                currentElement.attr('value','on');
            }
            var params = {
                module : 'VTECustomExpenses',
                'action' : 'ActionAjax',
                'mode' : 'UpdateStatus',
                'record' : currentElement.data('id'),
                'status' : currentElement.val()
            }
            AppConnector.request(params).then(function(data){
                console.log(data);
                if(data){
                    app.helper.showSuccessNotification({
                        message : app.vtranslate('Status changed successfully.')
                    });
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
            var text="Custom Expenses/Bills Disabled";
            if(element.checked) {
                value=1;
                text = "Custom Expenses/Bills Enabled";
            }
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'VTECustomExpenses';
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
    registerDeleteCustomExpenses:function(){
        $('a#vtecustom_expenses_delete').on('click',function(e){
            var currentElement = jQuery(e.currentTarget);
            app.helper.showConfirmationBox({
                message: 'Do you want delete this record ?'
            }).then(function () {
                var params = {
                    module : 'VTECustomExpenses',
                    'action' : 'ActionAjax',
                    'mode' : 'DeleteRecord',
                    'record' : currentElement.data('id')
                }
                AppConnector.request(params).then(function(data){
                    console.log(data);
                    if(data){
                        window.location.reload();
                    }
                });
            });
        });
    },
    registerEvents: function(){
        this.registerEnableModuleEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerSwitchStatus();
        this.registerDeleteCustomExpenses();
    }
});
jQuery(document).ready(function() {
    var instance = new VTECustomExpenses_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});