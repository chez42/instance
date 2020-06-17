/* ********************************************************************************
 * The content of this file is subject to the VTEAuthnet("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

var Settings_ANCustomers_Js = {
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
        jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
            var license_key=jQuery('#license_key');
            if(license_key.val()=='') {
                var errorMsg = "License Key cannot be empty";
                app.helper.showErrorNotification({"message":errorMsg});
                return false;
            }else{
                app.helper.showProgress();
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['parent'] = 'Settings';
                params['license'] = license_key.val();

                app.request.post({'data' : params}).then(
                    function(err,data){
                        if(err === null) {
                            app.helper.hideProgress();
                            if(data) {
                                var message=data.message;
                                if(message !='Valid License') {
                                    jQuery('#error_message').html(message);
                                    jQuery('#error_message').show();
                                }else{
                                    document.location.href="index.php?module=ANCustomers&parent=Settings&view=Settings&mode=step3";
                                }
                            }
                        }else{
                            app.helper.hideProgress();;
                        }
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';
            params['parent'] = 'Settings';
            app.helper.showProgress();
            app.request.post({'data' : params}).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        if (data) {
                            document.location.href = "index.php?module=ANCustomers&parent=Settings&view=Settings";
                        }
                    }else{
                        app.helper.hideProgress();
                    }
                }
            );
        });
    },
    /* For License page - End */

    downloadAuthNetLib: function () {
        var params = {};
        params['module'] = app.getModuleName();
        params['action'] = 'Install';
        params['mode'] = 'DownloadAuthnetLib';
        params['parent'] = 'Settings';
        app.helper.showProgress();
        app.request.post({'data' : params}).then(
            function(err,data){
                if(err === null) {
                    app.helper.showInfoMessage({'message': app.vtranslate('Success')});
                    window.location.reload();
                }else{
                    app.helper.hideProgress();
                    app.helper.showErrorNotification({'message': err});
                }
            }
        );
    },

    /**
     * Function to register form for validation
     */
    registerSubmitEvent : function(){
        var editViewForm = $('#EditAuthnetSetting');
        editViewForm.submit(function(e){
            //Form should submit only once for multiple clicks also
            if(typeof editViewForm.data('submit') != "undefined") {
                return false;
            } else {
                var mode = editViewForm.find('#integrate_authorize_net_mode').val();
                var validation_mode = editViewForm.find('#integrate_authorize_net_validation_mode').val();
                var merchant_login_id = editViewForm.find('#integrate_authorize_net_merchant_login_id').val();
                var merchant_transaction_key = editViewForm.find('#integrate_authorize_net_merchant_transaction_key').val();
                if($.trim(mode)==''){
                    var msg = app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_MODE_REQUIRED');
                    app.helper.showErrorNotification({'message': msg});
                    return false;
                }else if($.trim(validation_mode)==''){
                    var msg = app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_VALIDATION_MODE_REQUIRED');
                    app.helper.showErrorNotification({'message': msg});
                    return false;
                }else if($.trim(merchant_login_id)==''){
                    var msg = app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_MERCHANT_LOGIN_ID_REQUIRED');
                    app.helper.showErrorNotification({'message': msg});
                    return false;
                }else if($.trim(merchant_transaction_key)==''){
                    var msg = app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_MERCHANT_TRANSACTION_KEY_REQUIRED');
                    app.helper.showErrorNotification({'message': msg});
                    return false;
                }
            }
        })
    },

    registerEvents: function () {
        /* For License page - Begin */
        this.init();
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerSubmitEvent();
        var instance = new Vtiger_Index_Js();
        instance.registerAppTriggerEvent();
    }

};
jQuery(document).ready(function () {
    Settings_ANCustomers_Js.registerEvents();
});