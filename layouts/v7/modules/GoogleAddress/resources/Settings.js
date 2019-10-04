/* ********************************************************************************
 * The content of this file is subject to the Google Address ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("GoogleAddress_Settings_Js",{
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
                                document.location.href="index.php?module=GoogleAddress&parent=Settings&view=Settings&mode=step3";
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
                        document.location.href = "index.php?module=GoogleAddress&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    app.helper.hideProgress();
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
        app.helper.showProgress();

        var data = form.serializeFormData();
        data['module'] = app.getModuleName();
        data['action'] = 'SaveAjax';
        data['mode'] = 'saveAddressDetail';

        app.request.post({data:data}).then(
            function(err, data) {
                if(data) {
                    app.helper.hideProgress();
                    app.helper.hideModal();
                    app.helper.showSuccessNotification({'message':'Address Detail Saved'});
                    thisInstance.loadListViewContents();
                }
            },
            function(error) {
                app.helper.hideProgress();
                //TODO : Handle error
            }
        );
    },
    /**
     * This function will save the address details
     */
    saveCountries : function(form) {
        var thisInstance = this;
        app.helper.showProgress();

        var data = form.serializeFormData();
        data['module'] = app.getModuleName();
        data['action'] = 'SaveAjax';
        data['mode'] = 'saveCountries';

        app.request.post({data:data}).then(
            function(err, data) {
                if(data) {
                    app.helper.hideProgress();
                    app.helper.hideModal();
                    app.helper.showSuccessNotification({'message':'Countries List Saved'});
                }
            },
            function(error) {
                app.helper.hideProgress();
                //TODO : Handle error
            }
        );
    },

    /**
     * This function will load the listView contents after Add/Edit address
     */
    loadListViewContents : function() {
        var thisInstance = this;
        app.helper.showProgress();

        var params = {};
        params['module'] = app.getModuleName();
        params['view'] = 'List';

        app.request.post({data:params}).then(
            function(error, data) {
                app.helper.hideProgress();
                if(data) {
                    //replace the new list view contents
                    jQuery('#listViewContents').html(data);
                    //thisInstance.triggerDisplayTypeEvent();
                }
            }
        );
    },

    deleteRecord : function(recordId) {
        var thisInstance = this;
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        app.helper.showConfirmationBox({'message' : message}).then(
            function(e) {
                var module = app.getModuleName();
                var postData = {
                    "module": module,
                    "action": "DeleteAjax",
                    "record": recordId
                };
                var deleteMessage = app.vtranslate('JS_RECORD_GETTING_DELETED');
                app.helper.showProgress(deleteMessage);
                app.request.post({data:postData}).then(
                    function(err,data){
                        app.helper.hideProgress();
                        if(data) {
                            thisInstance.loadListViewContents();
                        } else {
                            var  params = {
                                message : app.vtranslate(data.error.message),
                                title : app.vtranslate('JS_LBL_PERMISSION')
                            };
                            app.helper.showErrorNotification(params);
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
            app.helper.showProgress();
            app.request.post({url:url}).then(
                function(err, data) {
                    app.helper.hideProgress();
                    if(data) {
                        var callBackFunction = function(data) {
                            var form = jQuery('#editCountries');

                            var params = {
                                submitHandler: function() {
                                    // to Prevent submit if already submitted
                                    form.find('[name="saveButton"]').attr('disabled',true);
                                    if(this.numberOfInvalids() > 0) {
                                        return false;
                                    }
                                    thisInstance.saveCountries(form);
                                },
                            };
                            form.vtValidate(params);
                        };
                        app.helper.showModal(data, {'cb' : function(modal){
                            if(typeof callBackFunction == 'function'){
                                callBackFunction(modal);
                            }
                        }});
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
        app.helper.showProgress();
        app.request.post({"url":url}).then(
            function(err, data) {
                app.helper.hideProgress();
                if(err === null) {
                    var callBackFunction = function(data) {
                        var form = jQuery('#editAddress');

                        var params = {
                            submitHandler: function() {
                                // to Prevent submit if already submitted
                                form.find('[name="saveButton"]').attr('disabled',true);
                                if(this.numberOfInvalids() > 0) {
                                    return false;
                                }
                                thisInstance.saveAddressDetails(form);
                            },
                        };
                        form.vtValidate(params);
                    };
                    app.helper.showModal(data, {'cb' : function(modal){
                        if(typeof callBackFunction == 'function'){
                            callBackFunction(modal);
                        }
                        thisInstance.registerPopupEvents();
                    }});
                }
            }
        );
    },

    registerSelectModuleEvent:function(container) {
        container.on("change",'[name="select_module"]', function(e) {
            app.helper.showProgress();
            var select_module=jQuery(this).val();

            var actionParams = {
                "data" : {
                    "select_module" : select_module,
                    "module": "GoogleAddress",
                    "view":"EditAjax",
                    "mode":"getMappingFields",
                },
            };
            app.request.post(actionParams).then(
                function(err,data) {
                    app.helper.hideProgress();
                    if(data) {
                        container.find('.mapping_fields').remove();
                        container.find('.add_mapping_field').remove();
                        container.find('.module_row').after(data);

                        //container.find('#mapping_fields').html(data);
                        vtUtils.applyFieldElementsView(container);
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
            jQuery('.mapping_fields:last').after(newMapping);
            vtUtils.applyFieldElementsView(newMapping);
        });

        // Add name for field of vtiger
        container.on('change', '.google_field', function(element){
            var google_field = element.currentTarget;
            var google_field_name = google_field.value;
            var parent = jQuery(google_field).closest('.mapping_fields');
            var vtiger_field = parent.find('select.vtiger_field');
            jQuery(vtiger_field).attr("name",google_field_name);
        });

        // Remove row of googleAddress
        container.on('click', '.deleteRecordButton', function(e){
            var parentRow = jQuery(e.currentTarget).closest('.mapping_fields');
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
            app.helper.showProgress();
            app.request.post({url:url}).then(
                function(err,data) {
                    app.helper.hideProgress();
                    if(data) {
                        var callBackFunction = function(data) {
                            var form = jQuery('#editGoogleApiKey');

                            var params = {
                                submitHandler: function() {
                                    // to Prevent submit if already submitted
                                    form.find('[name="saveButton"]').attr('disabled',true);
                                    if(this.numberOfInvalids() > 0) {
                                        return false;
                                    }
                                    thisInstance.saveGoogleApiKey(form);
                                },
                            };
                            form.vtValidate(params);
                        };

                        app.helper.showModal(data, {'cb' : function(modal){
                            if(typeof callBackFunction == 'function'){
                                callBackFunction(modal);
                            }
                        }});
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
        app.helper.showProgress();

        var data = form.serializeFormData();
        data['module'] = app.getModuleName();
        data['action'] = 'SaveAjax';
        data['mode'] = 'saveGoogleApiKey';

        app.request.post({data:data}).then(
            function(error, data) {
                if(data) {
                    var params = {};
                    app.helper.hideProgress();
                    app.helper.hideModal();
                    app.helper.showSuccessNotification({'message':data.message});
                }
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

jQuery(document).ready(function() {
    var instance = new GoogleAddress_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});
