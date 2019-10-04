/* ********************************************************************************
 * The content of this file is subject to the Global Search ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("GlobalSearch_Settings_Js",{
    editInstance:false,
    getInstance: function(){
        if(GlobalSearch_Settings_Js.editInstance == false){
            var instance = new GlobalSearch_Settings_Js();
            GlobalSearch_Settings_Js.editInstance = instance;
            return instance;
        }
        return GlobalSearch_Settings_Js.editInstance;
    }
},{
    updatedBlockSequence : {},
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
    registerSelectModuleChange:function() {
        jQuery("#global_search_settings").on("change","#search_module", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator();
            var searchModule=jQuery(this).val();
            var selectedFields=jQuery("#global_search_settings").find("#selectedFields");
            if(searchModule !='') {
                var params= {
                    "type": "POST",
                    "url": "index.php?module=GlobalSearch&parent=Settings&view=SettingsAjax&search_module="+searchModule,
                    "dataType":"html",
                    "data" : {}
                };
                AppConnector.request(params).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        selectedFields.html(data);
                        app.changeSelectElementView(selectedFields);
                        //register all select2 Elements
                        app.showSelect2ElementView(selectedFields.find('select.select2'));
                    }
                )
            }else {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                selectedFields.html('');
            }
        });
    },
    registerSelectFieldsEvent:function() {
        jQuery('#global_search_settings').on("change","#moduleFields", function(e) {
            jQuery('#global_search_settings').find('input[name="active"]').attr("checked","checked");
        })
    },
    registerSaveSettings:function() {
        jQuery("#global_search_settings").on("click",".btnSaveSettings", function(e) {
            var progressIndicatorElement = jQuery.progressIndicator();
            var form=jQuery("#global_search_settings").find("#Settings");
            var saveUrl = form.serializeFormData();
            AppConnector.request(saveUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                }
            );
        });
    },

    registerSortableEvent : function() {
        var thisInstance = this;
        var contents = jQuery('#arrangeModules');
        var table = contents.find('.searchModule');
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
     * Function which will update module sequence
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
        params['module'] = 'GlobalSearch';
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
     * Function which will arrange the sequence number of modules
     */
    updateBlocksListByOrder : function() {
        var thisInstance = this;
        var contents = jQuery('#arrangeModules')
        contents.find('.searchModule').each(function(index,domElement){
            var blockTable = jQuery(domElement);

            var blockId = blockTable.data('module');
            var actualBlockSequence = blockTable.data('sequence');
            var expectedBlockSequence = (index+1);

            if(expectedBlockSequence != actualBlockSequence) {
                blockTable.data('sequence', expectedBlockSequence);
            }
            thisInstance.updatedBlockSequence[blockId] = expectedBlockSequence;
        });
        return thisInstance.updatedBlockSequence;
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
                                document.location.href="index.php?module=GlobalSearch&parent=Settings&view=Settings&mode=step3";
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
                        document.location.href = "index.php?module=GlobalSearch&parent=Settings&view=Settings";
                    }
                },
                function (error) {
                    progressIndicatorElement.progressIndicator({'mode': 'hide'});
                }
            );
        });
    },
    /**
     * Function which will handle the registrations for the elements
     */
    registerEvents : function() {
        this.registerSelectModuleChange();
        this.registerSaveSettings();
        this.registerSelectFieldsEvent();
        this.registerSortableEvent();
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
    }
});

/*
jQuery(document).ready(function () {

});*/
