/* ********************************************************************************
 * The content of this file is subject to the Time Tracker ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */


jQuery.Class("TimeTracker_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(TimeTracker_Settings_Js.instance == false){
            var instance = new TimeTracker_Settings_Js();
            TimeTracker_Settings_Js.instance = instance;
            return instance;
        }
        return TimeTracker_Settings_Js.instance;
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
                    function(err, data) {
                        app.helper.hideProgress();
                        if(data) {
                            var message=data.message;
                            if(message !='Valid License') {
                                jQuery('#error_message').html(message);
                                jQuery('#error_message').show();
                            }else{
                                document.location.href="index.php?module=TimeTracker&parent=Settings&view=Settings&mode=step3";
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
                        document.location.href = "index.php?module=TimeTracker&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    app.helper.hideProgress();
                }
            );
        });
    },
    /* For License page - End */
    registerEventToSaveSettings : function () {
        jQuery('#btnSaveSettings').on('click', function(e) {
            e.preventDefault();
            var progressIndicatorElement = app.helper.showProgress();
            var params={};
            params = jQuery("#formSettings").serializeFormData();
            var selected_modules=[];
            jQuery('input.selectedModules').each(function() {
                if(jQuery(this).is(':checked')) {
                    selected_modules.push(jQuery(this).val());
                }
            });
            params.selected_modules = selected_modules;
            app.request.post({data:params}).then(
                function(err) {
                    if (err === null){
                        app.helper.hideProgress();
                        var params = {};
                        params['message'] = 'Settings Saved';
                        app.helper.showSuccessNotification(params);
                    }
                },
                function(error){
                    app.helper.hideProgress();
                }
            );
        });
    },

    registerSelect2ForSettingFields : function () {
        jQuery('.chzn-select').select2();
    },

    registerEvents: function(){
        this.registerEventToSaveSettings();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerSelect2ForSettingFields();
    }
});

jQuery(document).ready(function() {
    var instance = new TimeTracker_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});