/* ********************************************************************************
 * The content of this file is subject to the Related Record Update ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger_Index_Js("VTEEmailMarketing_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(VTEEmailMarketing_Settings_Js.instance == false){
            var instance = new VTEEmailMarketing_Settings_Js();
            VTEEmailMarketing_Settings_Js.instance = instance;
            return instance;
        }
        return VTEEmailMarketing_Settings_Js.instance;
    }
},{
    /* For License page - Begin */
    init : function() {
        this.initiate();
    },
    /*`
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
                    function(err, data) {
                        app.helper.hideProgress();
                        if(data) {
                            var message=data.message;
                            if(message !='Valid License') {
                                jQuery('#error_message').html(message);
                                jQuery('#error_message').show();
                            }else{
                                document.location.href="index.php?module=VTEEmailMarketing&parent=Settings&view=Settings&mode=step3";
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
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';

            app.request.post({'data':params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if(err === null) {
                        document.location.href = "index.php?module=VTEEmailMarketing&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    app.helper.hideProgress();
                }
            );
        });
    },
    /* For License page - End */
    registerEnableModuleEvent:function() {
        jQuery('.summaryWidgetContainer').find('#enable_module').change(function(e) {
            app.helper.showProgress();
            var element=e.currentTarget;
            var value=0;
            var text="VTE Email Marketing Disabled";
            if(element.checked) {
                value=1;
                text = "VTE Email Marketing Enabled";
            }
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'VTEEmailMarketing';
            params.value = value;
            params.mode = 'enableModule';
            app.request.post({'data' : params}).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        var params = {};
                        params['text'] = text;
                        Settings_Vtiger_Index_Js.showMessage(params);
                    }else{
                        //TODO : Handle error
                        app.helper.hideProgress();
                    }
                }
            );
        });
    },
    registerEvents: function(){
        this._super();
        this.registerEnableModuleEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});
jQuery(document).ready(function() {
    var instance = new VTEEmailMarketing_Settings_Js();
    instance.registerEvents();
});