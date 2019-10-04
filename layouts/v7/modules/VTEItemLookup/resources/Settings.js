/* ********************************************************************************
 * The content of this file is subject to the Item Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEItemLookup_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(VTEItemLookup_Settings_Js.instance == false){
            var instance = new VTEItemLookup_Settings_Js();
            VTEItemLookup_Settings_Js.instance = instance;
            return instance;
        }
        return VTEItemLookup_Settings_Js.instance;
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
                    function(err,data) {
                        app.helper.hideProgress();
                        if(err == null){
                            var message=data['message'];
                            if(message !='Valid License') {
                                app.helper.hideProgress();
                                app.helper.hideModal();
                                app.helper.showAlertNotification({'message':data['message']});
                            }else{
                                document.location.href="index.php?module=VTEItemLookup&parent=Settings&view=Settings&mode=step3";
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
            var data = {};
            data['module'] = 'VTEItemLookup';
            data['action'] = 'Activate';
            data['mode'] = 'valid';
            app.request.post({data:data}).then(
                function (err,data) {
                    if(err == null){
                        app.helper.hideProgress();
                        if (data) {
                            document.location.href = "index.php?module=VTEItemLookup&parent=Settings&view=Settings";
                        }
                    }
                }
            );
        });
    },
    /* For License page - End */
    registerEnableModuleEvent:function() {
        jQuery('.summaryWidgetContainer').find('#enable_module').change(function(e) {
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });

            var element=e.currentTarget;
            var value=0;
            var text="Item Lookup Disabled";
            if(element.checked) {
                value=1;
                text = "Item Lookup Enabled";
            }
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'VTEItemLookup';
            params.value = value;
            params.mode = 'enableModule';
            AppConnector.request(params).then(
                function(data){
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var params = {};
                    params['text'] = text;
                    Settings_Vtiger_Index_Js.showMessage(params);
                },
                function(error){
                    //TODO : Handle error
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                }
            );
        });
    },
    updateConfigureField:function(value,fieldName){
        var params = {
            module : 'VTEItemLookup',
            'action' : 'ActionAjax',
            'mode' : 'updateConfigureField',
            'value' : value,
            'fieldName' : fieldName
        }
        AppConnector.request(params).then(function(data){
            console.log(data);
            if(data){
                app.helper.showSuccessNotification({
                    message : app.vtranslate('Field changed successfully.')
                });
            }
        });
    },
    registerSwitchField:function(){
        var self = this;
        jQuery("input.switch-input").bootstrapSwitch();
        jQuery('input.switch-input').on('switchChange.bootstrapSwitch', function (e) {
            var currentElement = jQuery(e.currentTarget);
            if(currentElement.val() == 'on'){
                currentElement.attr('value','off');
            } else {
                currentElement.attr('value','on');
            }
            var value = currentElement.val();
            value = value == 'on' ? 1 : 0;
            var fieldName = currentElement[0].name;
            self.updateConfigureField(value,fieldName);
        });
    },
    registerPickListField:function(){
        var self = this;
        var picklist = $('select.picklist-field');
        vtUtils.showSelect2ElementView(picklist);
        picklist.on('change',function(e){
            var currentElement = jQuery(e.currentTarget);
            var value = currentElement.val();
            var fieldName = currentElement[0].name;
            self.updateConfigureField(value,fieldName);
        });
    },
    registerTextField:function(){
        var self = this;
        var input = $('input.text-field');
        input.on('change',function(e){
            var currentElement = jQuery(e.currentTarget);
            var value = currentElement.val();
            var fieldName = currentElement[0].name;
            self.updateConfigureField(value,fieldName);
        });
        $('.field-configure-label').popover({container: 'body'});
    },
    registerEvents: function(){
        this.registerEnableModuleEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
        this.registerSwitchField();
        this.registerPickListField();
        this.registerTextField();
    }
});
jQuery(document).ready(function() {
    var instance = new VTEItemLookup_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});