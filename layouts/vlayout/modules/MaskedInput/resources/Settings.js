/* ********************************************************************************
 * The content of this file is subject to the Masked Input ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("MaskedInput_Settings_Js",{
    editInstance:false,
    getInstance: function(){
        if(MaskedInput_Settings_Js.editInstance == false){
            var instance = new MaskedInput_Settings_Js();
            MaskedInput_Settings_Js.editInstance = instance;
            return instance;
        }
        return MaskedInput_Settings_Js.editInstance;
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
                                document.location.href="index.php?module=MaskedInput&parent=Settings&view=Settings&mode=step3";
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
                        document.location.href = "index.php?module=MaskedInput&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }
            );
        });
    },
    /* For License page - End */
    /**
     * This function will save the address details
     */
    saveRecordDetails : function(form) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });

        var data = form.serializeFormData();
        data['module'] = app.getModuleName();
        data['action'] = 'SaveAjax';


        AppConnector.request(data).then(
            function(data) {
                if(data['success']) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    app.hideModalWindow();
                    var params = {};
                    params.text = data.result._message;
                    Settings_Vtiger_Index_Js.showMessage(params);
                    thisInstance.loadListViewContents(data.result._type);
                }
            },
            function(error) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                //TODO : Handle error
            }
        );
    },

    /**
     * This function will load the listView contents after Add/Edit address
     */
    loadListViewContents : function(type) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });

        var params = {};
        params['module'] = app.getModuleName();
        params['view'] = 'ListAjax';
        params['mode'] = 'get'+type;

        AppConnector.request(params).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                //replace the new list view contents
                jQuery('#'+type+'List').html(data);
                //thisInstance.triggerDisplayTypeEvent();
            }, function(error, err) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            }
        );
    },

    deleteRecord : function(recordId,type) {
        var thisInstance = this;
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "DeleteAjax",
                    "record": recordId,
                    "mode": 'delete'+type
                };
                var deleteMessage = app.vtranslate('JS_RECORD_GETTING_DELETED');
                var progressIndicatorElement = jQuery.progressIndicator({
                    'message' : deleteMessage,
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(postData).then(
                    function(data){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        if(data.success) {
                            thisInstance.loadListViewContents(type);
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

    registerAddRecordEvent:function() {
        var thisInstance=this;
        jQuery(document).on("click",".addRecordButton", function(e) {
            var url = jQuery(this).data('url');
            thisInstance.showEditView(url);
        });
    },

    registerEditRecordEvent: function() {
        var thisInstance=this;
        jQuery(document).on("click",".editRecordButton", function(e) {
            var url = jQuery(this).data('url');
            thisInstance.showEditView(url);
        });
    },
    /*
     * function to show editView for Add/Edit Currency
     * @params: id - currencyId
     */
    showEditView : function(url) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator();
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"html",
            "data" : {}
        };
        AppConnector.request(actionParams).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data) {
                    var callBackFunction = function(data) {
                        var form = jQuery('#editForm');
                        var params = app.validationEngineOptions;
                        params.onValidationComplete = function(form, valid){
                            if(valid) {
                                thisInstance.saveRecordDetails(form);
                                return valid;
                            }
                        };
                        form.validationEngine(params);

                        form.submit(function(e) {
                            e.preventDefault();
                        })
                    };
                    app.showModalWindow(data, function(data){
                        if(typeof callBackFunction == 'function'){
                            callBackFunction(data);
                        }
                        thisInstance.registerPopupEvents();
                    }, {'width':'600px'})
                }
            }
        );
    },

    registerSelectModuleEvent:function(container) {
        container.on("change",'[name="select_module"]', function(e) {
            var progressIndicatorElement = jQuery.progressIndicator();
            var select_module=jQuery(this).val();
            var actionParams = {
                "type":"POST",
                "url": "index.php?module=MaskedInput&view=EditAjax&mode=getFields",
                "dataType":"html",
                "data" : {
                    "select_module" : select_module
                }
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data) {
                        container.find('#fields').html(data);
                        // TODO Make it better with jQuery.on
                        app.changeSelectElementView(container);
                        //register all select2 Elements
                        app.showSelect2ElementView(container.find('select.select2'));
                    }
                }
            );
        })
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
            var type = elem.closest('tr').data('type');
            thisInstance.deleteRecord(recordId,type);
            e.stopPropagation();
        });
    },

    /**
     * Function which will handle the registrations for the elements
     */
    registerPopupEvents: function() {
        var container=jQuery('#massEditContainer');
        this.registerSelectModuleEvent(container);
    },
    registerEvents : function() {
        this.registerAddRecordEvent();
        this.registerEditRecordEvent();
        this.registerDeleteRecordClickEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});