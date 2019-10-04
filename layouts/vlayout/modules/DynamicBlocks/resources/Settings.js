/* ********************************************************************************
 * The content of this file is subject to the Dynamic Blocks ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

 jQuery.Class("DynamicBlocks_Settings_Js",{
    editInstance:false,
    getInstance: function(){
        if(DynamicBlocks_Settings_Js.editInstance == false){
            var instance = new DynamicBlocks_Settings_Js();
            DynamicBlocks_Settings_Js.editInstance = instance;
            return instance;
        }
        return DynamicBlocks_Settings_Js.editInstance;
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
                                 document.location.href="index.php?module=DynamicBlocks&parent=Settings&view=Settings&mode=step3";
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
                         document.location.href = "index.php?module=DynamicBlocks&parent=Settings&view=Settings";
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
        jQuery('.contentsDiv').on("click",'.addButton', function(e) {
            var source_module = jQuery('#dynamicBlockModules').val();
            if(source_module !='' && source_module !='All') {
                document.location.href=jQuery(e.currentTarget).data('url') + '&source_module='+source_module;
            }else {
                document.location.href=jQuery(e.currentTarget).data('url');
            }
        });
    },

     registerSortableEvent : function() {
         var thisInstance = this;
         var contents = jQuery('.listViewEntriesDiv');
         var table = contents.find('.blockSortable');
         contents.sortable({
             'containment' : contents,
             'items' : table,
             'revert' : true,
             'tolerance':'pointer',
             'cursor' : 'move',
             'update' : function(e, ui) {
                 thisInstance.updateBlockSequence();
             }
         });
     },

     /**
      * Function which will update block sequence
      */
     updateBlockSequence : function() {
         var thisInstance = this;
         var progressIndicatorElement = jQuery.progressIndicator({
             'position' : 'html',
             'blockInfo' : {
                 'enabled' : true
             }
         });

         var sequence = JSON.stringify(thisInstance.updateBlocksListByOrder());
         progressIndicatorElement.progressIndicator({'mode' : 'hide'});
         var params = {};
         params['module'] = 'DynamicBlocks';
         params['action'] = 'ActionAjax';
         params['mode'] = 'updateSequenceNumber';
         params['sequence'] = sequence;

         AppConnector.request(params).then(
             function(data) {
                 progressIndicatorElement.progressIndicator({'mode' : 'hide'});
             },
             function(error) {
                 progressIndicatorElement.progressIndicator({'mode' : 'hide'});
             }
         );
     },

     /**
      * Function which will arrange the sequence number of blocks
      */
     updateBlocksListByOrder : function() {
         var thisInstance = this;
         var contents = jQuery('.listViewEntriesDiv');
         contents.find('.blockSortable').each(function(index,domElement){
             var blockTable = jQuery(domElement);

             var blockId = blockTable.data('id');
             var actualBlockSequence = blockTable.data('sequence');
             var expectedBlockSequence = (index+1);

             if(expectedBlockSequence != actualBlockSequence) {
                 blockTable.data('sequence', expectedBlockSequence);
             }
             thisInstance.updatedBlockSequence[blockId] = expectedBlockSequence;
         });
         return thisInstance.updatedBlockSequence;
     },

     registerDeleteBlockEvent: function () {
         var thisInstance = this;
         var contents = jQuery('.listViewEntriesDiv');
         contents.on('click','.deleteBlock', function(e) {
             var element=jQuery(e.currentTarget);
             var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
             Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                 function(e) {
                     var blockId = jQuery(element).data('id');
                     var params = {};
                     params['module'] = 'DynamicBlocks';
                     params['action'] = 'ActionAjax';
                     params['mode'] = 'deleteBlock';
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
         });
     },
     registerSelectModuleEvent: function () {
         var thisInstance = this;
         var contents=jQuery('.contentsDiv');
         contents.on('change','#dynamicBlockModules', function() {
             thisInstance.loadListBlocks();
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
         params['module'] = 'DynamicBlocks';
         params['view'] = 'MassActionAjax';
         params['mode'] = 'reloadListBlock';
         params['source_module'] = jQuery('#dynamicBlockModules').val();

         AppConnector.request(params).then(
             function(data) {
                 progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                 var contents = jQuery('.listViewEntriesDiv');
                 contents.html(data);
                 thisInstance.registerSortableEvent();
             }
         );
     },
     registerEvents : function() {
         this.registerSortableEvent();
         this.registerAddButtonEvent();
         this.registerDeleteBlockEvent();
         this.registerSelectModuleEvent();
         /* For License page - Begin */
         this.registerActivateLicenseEvent();
         this.registerValidEvent();
         /* For License page - End */
     }
});