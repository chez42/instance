/* ********************************************************************************
 * The content of this file is subject to the Custom Forms & Views ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

 jQuery.Class("CustomFormsViews_Settings_Js",{
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
                                 document.location.href="index.php?module=CustomFormsViews&parent=Settings&view=Settings&mode=step3";
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
                         document.location.href = "index.php?module=CustomFormsViews&parent=Settings&view=Settings";
                     }
                 },
                 function (error) {
                     progressIndicatorElement.progressIndicator({'mode': 'hide'});
                 }
             );
         });
     },
     /* For License page - End */
    updatedBlockSequence : {},
    registerAddButtonEvent: function () {
        jQuery('.contentsDiv').on("click",'.addButton, .editRecordButton, .listViewEntries', function(e) {
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
             Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                 function(e) {
                     var blockId = jQuery(element).data('id');
                     var params = {};
                     params['module'] = 'CustomFormsViews';
                     params['action'] = 'ActionAjax';
                     params['mode'] = 'delete';
                     params['record'] = blockId;

                     AppConnector.request(params).then(
                         function(data) {
                             thisInstance.loadListBlocks();
                         }
                     );
                 },
                 function(error, err){
                 }
             );
             e.preventDefault();
         });
     },

     loadListBlocks: function() {
         var thisInstance = this;
         var progressIndicatorElement = jQuery.progressIndicator({
             'position' : 'html',
             'blockInfo' : {
                 'enabled' : true
             }
         });
         var params = {};
         params['module'] = 'CustomFormsViews';
         params['view'] = 'MassActionAjax';
         params['mode'] = 'reloadList';

         AppConnector.request(params).then(
             function(data) {
                 progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                 var contents = jQuery('.listViewEntriesDiv');
                 contents.html(data);
             }
         );
     },
     registerEvents : function() {
         this.registerAddButtonEvent();
         this.registerDeleteEvent();
         /* For License page - Begin */
         this.registerActivateLicenseEvent();
         this.registerValidEvent();
         /* For License page - End */
     }
});