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
        var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
            var license_key=jQuery('#license_key');
            if(license_key.val()=='') {
                errorMsg = "License Key cannot be empty";
                license_key.validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
                aDeferred.reject();
                return aDeferred.promise();
            }else{
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['parent'] = 'Settings';
                params['license'] = license_key.val();

                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if(data.success) {
                            var message=data.result.message;
                            if(message !='Valid License') {
                                jQuery('#error_message').html(message);
                                jQuery('#error_message').show();
                            }else{
                                document.location.href="index.php?module=ANCustomers&view=Settings&parent=Settings&mode=step3";
                            }
                        }
                    },
                    function(error) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';
            params['parent'] = 'Settings';

            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if (data.success) {
                        document.location.href = "index.php?module=ANCustomers&view=Settings&parent=Settings";
                    }
                },
                function (error) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
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
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        AppConnector.request(params).then(
            function (data) {
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                var n_params = {
                    title : app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate(app.vtranslate('Success')),
                    animation: 'show',
                    type: 'info'
                };
                Vtiger_Helper_Js.showPnotify(n_params);
                window.location.reload();
            },
            function (error) {
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                var n_params = {
                    title : app.vtranslate('JS_MESSAGE'),
                    text: app.vtranslate(error),
                    animation: 'show',
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(n_params);
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
                    var n_params = {
                        title : app.vtranslate('JS_MESSAGE'),
                        text: app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_MODE_REQUIRED'),
                        animation: 'show',
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(n_params);
                    return false;
                }else if($.trim(validation_mode)==''){
                    var n_params = {
                        title : app.vtranslate('JS_MESSAGE'),
                        text: app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_VALIDATION_MODE_REQUIRED'),
                        animation: 'show',
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(n_params);
                    return false;
                }else if($.trim(merchant_login_id)==''){
                    var n_params = {
                        title : app.vtranslate('JS_MESSAGE'),
                        text: app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_MERCHANT_LOGIN_ID_REQUIRED'),
                        animation: 'show',
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(n_params);
                    return false;
                }else if($.trim(merchant_transaction_key)==''){
                    var n_params = {
                        title : app.vtranslate('JS_MESSAGE'),
                        text: app.vtranslate('JS_INTEGRATE_AUTHORIZE_NET_MERCHANT_TRANSACTION_KEY_REQUIRED'),
                        animation: 'show',
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(n_params);
                    return false;
                }
            }
        })
    },

    /**
     * Function to register events
     */
    registerEvents : function(){
        /* For License page - Begin */
        this.init();
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerSubmitEvent();
    }

};
jQuery(document).ready(function () {
    Settings_ANCustomers_Js.registerEvents();
});