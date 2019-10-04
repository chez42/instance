/* ********************************************************************************
 * The content of this file is subject to the Custom Forms & Views ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_Index_Js("CustomFormsViews_Settings_Js",{
    editInstance:false,
    getInstance: function(){
        if(CustomFormsViews_Settings_Js.editInstance == false){
            var instance = new CustomFormsViews_Settings_Js();
            CustomFormsViews_Settings_Js.editInstance = instance;
            return instance;
        }
        return CustomFormsViews_Settings_Js.editInstance;
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
                                document.location.href="index.php?module=CustomFormsViews&parent=Settings&view=Settings&mode=step3";
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
                        document.location.href = "index.php?module=CustomFormsViews&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    app.helper.hideProgress();
                }
            );
        });
    },
     /* For License page - End */
    updatedBlockSequence : {},
    registerAddButtonEvent: function () {
        jQuery('.container-fluid').on("click",'.addCustomForms, .editRecordButton, .listViewEntries', function(e) {
            document.location.href=jQuery(e.currentTarget).data('url');
        });
    },
     registerDeleteEvent: function () {
         var thisInstance = this;
         var contents = jQuery('.listViewEntriesDiv');
         contents.on('click','.deleteRecordButton', function(e) {
             e.stopImmediatePropagation();
             var element=jQuery(e.currentTarget);
             var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
             app.helper.showConfirmationBox({'message' : message}).then(
                 function(e) {
                     var blockId = jQuery(element).data('id');
                     var params = {};
                     params['module'] = 'CustomFormsViews';
                     params['action'] = 'ActionAjax';
                     params['mode'] = 'delete';
                     params['record'] = blockId;

                     app.request.post({'data' : params}).then(
                         function(err,data){
                             if(err === null) {
                                 thisInstance.loadListBlocks();
                             }
                         });
                 }

             );
             e.preventDefault();
         });
     },

     loadListBlocks: function() {
         var thisInstance = this;
         app.helper.showProgress();
         var params = {};
         params['module'] = 'CustomFormsViews';
         params['view'] = 'MassActionAjax';
         params['mode'] = 'reloadList';

         app.request.post({'data' : params}).then(
             function(err,data){
                 if(err === null) {
                     app.helper.hideProgress();
                     var contents = jQuery('.listViewEntriesDiv');
                     contents.html(data);
                 }
             }
         );
     },
     registerEvents : function() {
         this._super();
         this.registerAddButtonEvent();
         this.registerDeleteEvent();
         /* For License page - Begin */
         this.registerActivateLicenseEvent();
         this.registerValidEvent();
         /* For License page - End */
     }
});
jQuery(document).ready(function() {
    var instance = new CustomFormsViews_Settings_Js();
    instance.registerEvents();
});