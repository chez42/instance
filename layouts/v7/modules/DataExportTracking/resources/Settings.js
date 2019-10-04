/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_Index_Js("DataExportTracking_Settings_Js",{

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
                var errorMsg = "License Key cannot be empty";
                license_key.validationEngine('showPrompt', errorMsg , 'error','bottomLeft',true);
                aDeferred.reject();
                return aDeferred.promise();
            }else{
                app.helper.showProgress();
                var actionParams = {
                    "type": "POST",
                    "url": "index.php",
                    "dataType": "json",
                    "data": {
                        'module': app.getModuleName(),
                        'action': 'Activate',
                        'mode': 'activate',
                        'license': license_key.val()
                    }
                };
                app.request.post(actionParams).then(
                    function (err, data) {
                        app.helper.hideProgress();

                        if (err === null) {
                            if (data) {
                                var message = data.message;
                                if (message != 'Valid License') {
                                    jQuery('#error_message').html(message).show();
                                } else {
                                    document.location.href = "index.php?module=DataExportTracking&parent=Settings&view=Settings&mode=step3";
                                }
                            }
                            return false;
                        } else {
                            console.log(err);
                        }
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            app.helper.showProgress();
            var actionParams = {
                "type": "POST",
                "url": "index.php",
                "dataType": "json",
                "data": {
                    'module': app.getModuleName(),
                    'action': 'Activate',
                    'mode': 'valid'
                }
            };
            app.request.post(actionParams).then(
                function (err, data) {
                    app.helper.hideProgress();

                    if (err === null) {
                        document.location.href = "index.php?module=DataExportTracking&parent=Settings&view=Settings";
                    } else {
                        console.log(err);
                    }
                }
            );
        });
    },
    /* For License page - End */

    //updatedBlockSequence : {},
    registerEditButtonEvent: function () {
        var thisInstance=this;
        jQuery('#btn_edit').on("click",function(e) {
            app.helper.showProgress();
            var setting_id = jQuery('#setting_id').val();
            var actionParams = {
                "type": "POST",
                "url": "index.php",
                "dataType": "html",
                "data": {
                    'module': 'DataExportTracking',
                    'view': 'Settings',
                    'mode': 'EditSettings',
                    'setting_id': setting_id
                }
            };
            app.request.post(actionParams).then(
                function (err, data) {
                    if (err === null) {
                        jQuery('.settingsPageDiv').html(data);
                        thisInstance.registerSaveButtonEvent();
                        app.helper.hideProgress();
                        return false;
                    } else {
                        console.log(err);
                    }
                }
            );

        });
    },
    registerSaveButtonEvent: function () {
        var thisInstance=this;
        jQuery('#btn_save').on("click",function(e) {
            app.helper.showProgress();
            var setting_id = jQuery('#setting_id').val();
            var track_listview_exports  = jQuery('#track_listview_exports').is(':checked');
            var track_report_exports    = jQuery('#track_report_exports').is(':checked');
            var track_scheduled_reports = jQuery('#track_scheduled_reports').is(':checked');
            var track_copy_records      = jQuery('#track_copy_records').is(':checked');
            var notification_email      = jQuery('#notification_email').val();
            var actionParams = {
                "type": "POST",
                "url": "index.php",
                "dataType": "html",
                "data": {
                    'module': 'DataExportTracking',
                    'view': 'Settings',
                    'mode': 'SaveSettings',
                    'setting_id': setting_id,
                    'track_listview_exports': track_listview_exports,
                    'track_report_exports': track_report_exports,
                    'track_scheduled_reports': track_scheduled_reports,
                    'track_copy_records': track_copy_records,
                    'notification_email': notification_email
                }
            };
            app.request.post(actionParams).then(
                function (err, data) {
                    if (err === null) {
                        jQuery('.settingsPageDiv').html(data);
                        thisInstance.registerEditButtonEvent();
                        thisInstance.registerBackEvent();
                        var message = app.vtranslate('Settings saved');
                        app.helper.hideProgress();
                        app.helper.showSuccessNotification({'message': message});
                        return false;
                    } else {
                        console.log(err);
                    }
                }
            );

        });
    },
    registerBackEvent:function(){
        jQuery('#btn_back').on('click',function(){
            var link = "index.php?module=DataExportTracking&parent=Settings&view=ListData";
            window.location.href = link;
        });
    },
	registerEvents : function() {
        this._super();
        this.registerEditButtonEvent();
        this.registerBackEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
	 }
});

jQuery(document).ready(function() {
    var instance = new DataExportTracking_Settings_Js();
    instance.registerEvents();
});
