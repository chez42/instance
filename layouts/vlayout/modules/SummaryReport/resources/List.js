/* ********************************************************************************
 * The content of this file is subject to the Summary Report ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("SummaryReport_List_Js",{},{
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
                                document.location.href="index.php?module=SummaryReport&view=List&mode=step3";
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
                        document.location.href = "index.php?module=SummaryReport&view=List";
                    }
                },
                function (error) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }
            );
        });
    },
    /* For License page - End */
    /*
     * Function which will give you all the list view params
     */
    getListViewRecords : function() {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;

        var progressIndicatorElement = jQuery.progressIndicator({});

        var params = {
            'module': 'SummaryReport',
            'view' : "List"
        };

        AppConnector.requestPjax(params).then(
            function(data){
                progressIndicatorElement.progressIndicator({
                    'mode' : 'hide'
                });
                var listViewContentsContainer = jQuery('#listViewContents');
                listViewContentsContainer.html(data);
            },

            function(textStatus, errorThrown){
                aDeferred.reject(textStatus, errorThrown);
            }
        );
        return aDeferred.promise();
    },

    deleteRecord : function(recordId) {
        var thisInstance = this;
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "SaveAjax",
                    "record": recordId,
                    "mode": 'deleteRecord'
                };
                var progressIndicatorElement = jQuery.progressIndicator({});
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success) {
                            thisInstance.getListViewRecords();
                        } else {
                            var  params = {
                                text : app.vtranslate(data.error.message),
                                title : app.vtranslate('JS_LBL_PERMISSION')
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                        }
                    },
                    function(error,err){

                    }
                );
            },
            function(error, err){
            }
        );
    },
    /*
     * Function to register the list view row click event
     */
    registerRowClickEvent: function(){
        var thisInstance = this;
        var listViewContentDiv = jQuery('.listViewContentDiv');
        listViewContentDiv.on('click','.listViewEntries',function(e){
            var elem = jQuery(e.currentTarget);
            var recordUrl = elem.data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
            window.location.href = recordUrl;
        });
    },
    /*
     * Function to register the list view delete record click event
     */
    registerDeleteRecordClickEvent: function(){
        var thisInstance = this;
        var listViewContentDiv = jQuery('.listViewContentDiv');
        listViewContentDiv.on('click','.deleteRecordButton',function(e){
            var elem = jQuery(e.currentTarget);
            var recordId = elem.closest('tr').data('id');
            thisInstance.deleteRecord(recordId);
            e.stopPropagation();
        });
    },
    registerEvents: function () {
        this.registerRowClickEvent();
        this.registerDeleteRecordClickEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});
