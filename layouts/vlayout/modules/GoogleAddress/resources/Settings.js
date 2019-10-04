/* ********************************************************************************
 * The content of this file is subject to the Google Address ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("GoogleAddress_Settings_Js",{
    editInstance:false,
    getInstance: function(){
        if(GoogleAddress_Settings_Js.editInstance == false){
            var instance = new GoogleAddress_Settings_Js();
            GoogleAddress_Settings_Js.editInstance = instance;
            return instance;
        }
        return GoogleAddress_Settings_Js.editInstance;
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
                                document.location.href="index.php?module=GoogleAddress&parent=Settings&view=Settings&mode=step3";
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
                        document.location.href = "index.php?module=GoogleAddress&parent=Settings&view=Settings";
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
    saveAddressDetails : function(form) {
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
        data['mode'] = 'saveAddressDetail';

        AppConnector.request(data).then(
            function(data) {
                if(data['success']) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    app.hideModalWindow();
                    var params = {};
                    params.text = app.vtranslate('Address Detail Saved');
                    Settings_Vtiger_Index_Js.showMessage(params);
                    thisInstance.loadListViewContents();
                }
            },
            function(error) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                //TODO : Handle error
            }
        );
    },
    /**
     * This function will save the address details
     */
    saveCountries : function(form) {
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
        data['mode'] = 'saveCountries';

        AppConnector.request(data).then(
            function(data) {
                if(data['success']) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    app.hideModalWindow();
                    var params = {};
                    params.text = app.vtranslate('Countries List Saved');
                    Settings_Vtiger_Index_Js.showMessage(params);
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
    loadListViewContents : function() {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });

        var params = {};
        params['module'] = app.getModuleName();
        params['view'] = 'List';

        AppConnector.request(params).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                //replace the new list view contents
                jQuery('#listViewContents').html(data);
                //thisInstance.triggerDisplayTypeEvent();
            }, function(error, err) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
            }
        );
    },

    deleteRecord : function(recordId) {
        var thisInstance = this;
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "DeleteAjax",
                    "record": recordId
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
                            thisInstance.loadListViewContents();
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

    registerAddAddressEvent:function() {
        var thisInstance=this;
        jQuery(document).on("click",".addAddressButton", function(e) {
            var url = jQuery(this).data('url');
            thisInstance.showEditView(url);
        });
    },

    registerEditCountriesEvent:function() {
        var thisInstance=this;
        jQuery(document).on("click",".editCountries", function(e) {

            var url = jQuery(this).data('url');
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
                            var form = jQuery('#editCountries');
                            //thisInstance.saveCountries(form);

                            var params = app.validationEngineOptions;
                            params.onValidationComplete = function(form, valid){
                                if(valid) {
                                    thisInstance.saveCountries(form);
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
                        }, {'width':'600px'})
                    }

                }
            );
        });
    },

    registerEditAddressEvent: function() {
        var thisInstance=this;
        jQuery(document).on("click",".editAddressButton", function(e) {
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
                        var form = jQuery('#editAddress');
                        var params = app.validationEngineOptions;
                        params.onValidationComplete = function(form, valid){
                            if(valid) {
                                thisInstance.saveAddressDetails(form);
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
                "url": "index.php?module=GoogleAddress&view=EditAjax&mode=getMappingFields",
                "dataType":"html",
                "data" : {
                    "select_module" : select_module
                }
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data) {
                        container.find('#mapping_fields').html(data);
                        // TODO Make it better with jQuery.on
                        app.changeSelectElementView(container);
                        //register all select2 Elements
                        app.showSelect2ElementView(container.find('select.select2'));
                    }
                }
            );
        })
    },

    // Function to clone google_address fields
    registerAddGoogleFields: function(container){

        // Add new row for googleAddress
        container.on("click", '#addMore', function(e){
            var newMapping=jQuery('.addMoreGoogleField').clone();
            newMapping.removeClass('hide');
            newMapping.removeClass('addMoreGoogleField');
            newMapping.find('select').addClass('select2');
            jQuery('.block_add_google_fields').append(newMapping);
            //jQuery('.addMoreGoogleField').clone().appendTo('.block_add_google_fields');
            /*container.find('.addMoreGoogleField').first().removeClass('hide');
             container.find('.addMoreGoogleField').first().removeClass('addMoreGoogleField');*/
            app.showSelect2ElementView(jQuery('.block_add_google_fields').find('select.select2'));
        });

        // Add name for field of vtiger
        container.on('change', '.google_field', function(element){
            var google_field = element.currentTarget;
            var google_field_name = google_field.value;
            var parent = jQuery(google_field).closest('.control-group');
            var vtiger_field = parent.find('select.vtiger_field');
            jQuery(vtiger_field).attr("name",google_field_name);
        });

        // Remove row of googleAddress

        container.on('click', '.deleteRecordButton', function(element){
            var currentRow = element.currentTarget;
            var parentRow = jQuery(currentRow).closest('.control-group');
            parentRow.remove();
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


    registerEditGoogleApiKeyEvent:function() {
        var thisInstance=this;
        jQuery(document).on("click",".editGoogleApiKey", function(e) {
            var url = jQuery(this).data('url');
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
                            var form = jQuery('#editGoogleApiKey');
                            //thisInstance.saveCountries(form);

                            var params = app.validationEngineOptions;
                            params.onValidationComplete = function(form, valid){
                                if(valid) {
                                    thisInstance.saveGoogleApiKey(form);
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
                        }, {'width':'600px'})
                    }

                }
            );
        });
    },

    /**
     * This function will save the address details
     */
    saveGoogleApiKey : function(form) {
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
        data['mode'] = 'saveGoogleApiKey';

        AppConnector.request(data).then(
            function(data) {
                if(data['success']) {
                    var params = {};
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    app.hideModalWindow();
                    params.text = app.vtranslate(data.result.message);
                    Settings_Vtiger_Index_Js.showMessage(params);
                }
            },
            function(error) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                //TODO : Handle error
            }
        );
    },

    /**
     * Function which will handle the registrations for the elements
     */
    registerPopupEvents: function() {
        var container=jQuery('#massEditContainer');
        this.registerSelectModuleEvent(container);
        this.registerAddGoogleFields(container);
    },
    registerEvents : function() {
        this.registerAddAddressEvent();
        this.registerEditAddressEvent();
        this.registerDeleteRecordClickEvent();
        this.registerEditCountriesEvent();
        this.registerEditGoogleApiKeyEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});