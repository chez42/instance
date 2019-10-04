/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("DataExportTracking_Settings_Js",{

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
                                document.location.href="index.php?module=DataExportTracking&parent=Settings&view=Settings&mode=step3";
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

            AppConnector.request(params).then(
                function (data) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                    if (data.success) {
                        document.location.href = "index.php?module=DataExportTracking&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }
            );
        });
    },
    /* For License page - End */

    //updatedBlockSequence : {},
    registerEditButtonEvent: function () {
        var thisInstance=this;
        jQuery('#btn_edit').on("click",function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var url = "index.php?module=DataExportTracking&view=Settings&mode=EditSettings";
            var setting_id = jQuery('#setting_id').val();
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {'setting_id':setting_id}
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    if(data) {
                        jQuery('.contentsDiv').html(data);
                        thisInstance.registerSaveButtonEvent();
                        return false;
                    }
                }
            );
        });
    },
    registerSaveButtonEvent: function () {
        var thisInstance=this;
        jQuery('#btn_save').on("click",function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var url = "index.php?module=DataExportTracking&view=Settings&mode=SaveSettings";
            var setting_id = jQuery('#setting_id').val();
            var track_listview_exports  = jQuery('#track_listview_exports').is(':checked');
            var track_report_exports    = jQuery('#track_report_exports').is(':checked');
            var track_scheduled_reports = jQuery('#track_scheduled_reports').is(':checked');
            var track_copy_records      = jQuery('#track_copy_records').is(':checked');
            var notification_email      = jQuery('#notification_email').val();
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {'setting_id':setting_id,'track_listview_exports':track_listview_exports,'track_report_exports':track_report_exports,'track_scheduled_reports':track_scheduled_reports,'track_copy_records':track_copy_records,'notification_email':notification_email}
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    if(data) {
                        jQuery('.contentsDiv').html(data);
                        thisInstance.registerEditButtonEvent();
                        thisInstance.registerBackEvent();
                        var message = app.vtranslate('Settings saved');
                        params = {
                            text: message,
                            type: 'success'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return false;
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
         this.registerEditButtonEvent();
		 this.registerBackEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
	 }
});