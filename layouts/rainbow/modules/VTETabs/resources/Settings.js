/* ********************************************************************************
 * The content of this file is subject to the VTE Tabs ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTETabs_Settings_Js",{

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
                app.helper.showProgress('');
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();
                app.request.post({'data':params}).then(
                    function(err,data){
                        if(err === null) {
                            app.helper.hideProgress();
                            var message=data.message;
                            if(message !='Valid License') {
                                jQuery('#error_message').html(message);
                                jQuery('#error_message').show();
                            }else{
                                document.location.href="index.php?module=VTETabs&parent=Settings&view=Settings&mode=step3";
                            }
                        }else{
                            app.helper.hideProgress();
                        }
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            app.helper.showProgress('');
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';
            app.request.post({'data':params}).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        document.location.href = "index.php?module=VTETabs&parent=Settings&view=Settings";
                    }else{
                        app.helper.hideProgress();
                    }
                }
            );
        });
    },
    /* For License page - End */
    

    registerEvents : function() {
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});

jQuery(document).ready(function(){
    var instance = new VTETabs_Settings_Js();
    instance.registerEvents();
});