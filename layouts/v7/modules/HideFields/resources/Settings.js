/* ********************************************************************************
 * The content of this file is subject to the Table Block ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("HideFields_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(HideFields_Settings_Js.instance == false){
            var instance = new HideFields_Settings_Js();
            HideFields_Settings_Js.instance = instance;
            return instance;
        }
        return HideFields_Settings_Js.instance;
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
                                document.location.href="index.php?module=HideFields&parent=Settings&view=Settings&mode=step3";
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
            data['module'] = 'HideFields';
            data['action'] = 'Activate';
            data['mode'] = 'valid';
            app.request.post({data:data}).then(
                function (err,data) {
                    if(err == null){
                        app.helper.hideProgress();
                        if (data) {
                            document.location.href = "index.php?module=HideFields&parent=Settings&view=Settings";
                        }
                    }
                }
            );
        });
    },
    /* For License page - End */
    //updatedBlockSequence : {},
    registerAddButtonEvent: function () {
        var thisInstance=this;
        jQuery('.hide-field-action').on("click",'.addButton', function(e) {
            var source_module = jQuery('#HideFieldsModules').val();
            if(source_module !='' && source_module !='All') {
                var url=jQuery(e.currentTarget).data('url') + '&source_module='+source_module;
            }else {
                var url=jQuery(e.currentTarget).data('url');
            }
            thisInstance.showEditView(url);
        });
    },
    registerEditButtonEvent: function() {
        var thisInstance=this;
        jQuery(document).on("click",".editBlockDetails", function(e) {
            var url = jQuery(this).data('url');
            thisInstance.showEditView(url);
        });
    },
    /*
     * function to show editView for Add/Edit block
     * @params: url - add/edit url
     */
    showEditView : function(url) {
        var thisInstance = this;
        var actionParams = app.convertUrlToDataParams(url);
        app.request.post({'data':actionParams}).then(
            function(err,data){
                if(err === null) {
                    app.helper.hideProgress();
                    var callBackFunction = function(data) {
                        $('a.remove_symbol i').removeClass('icon-remove-sign');
                        $('a.remove_symbol i').addClass('fa fa-times-circle');
                        $('a.remove_symbol i').css('vertical-align','top');
                        var form = jQuery('#HideFieldss_form');
                        form.find('button[name="saveButton"]').on('click',function(){
                            var params = {
                                submitHandler: function (frm) {
                                    if (this.numberOfInvalids() > 0) {
                                        return false;
                                    }
                                    var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                                    app.event.trigger(e);
                                    if (e.isDefaultPrevented()) {
                                        return false;
                                    }
                                    thisInstance.saveHideFields(form);
                                }
                            };
                            form.vtValidate(params);
                            form.submit();
                        });
                    };
                    app.helper.showModal(data,{'cb' : function (data){
                        if(typeof callBackFunction == 'function'){
                            callBackFunction(data);
                        }
                        thisInstance.registerPopupEvents();
                    }});
                }else{
                    app.helper.hideProgress();
                }
            }
        );
    },

    /**
     * This function will save the hidefield setting record
     */
    saveHideFields : function(form) {
        var thisInstance = this;
        thisInstance.updateFieldOrder(form);

        var check_valid = true;
        var arr_symbol_value = [];
        form.find('input[class="symbol"]').each(function(){
                if (typeof jQuery(this).val() !== "undefined") {
                    arr_symbol_value.push(jQuery(this).val());
                }
            }
        );

        var count = {};
        for(i in arr_symbol_value){
            count[arr_symbol_value[i]]=(count[arr_symbol_value[i]]||0)+1;
        }
        for(i in arr_symbol_value){
            var no_loop = count[arr_symbol_value[i]];
            if(no_loop > 1){
                var error_mss = app.vtranslate("Symbol '"+arr_symbol_value[i]+"' duplicated");
                var ErrorParam = {
                    text: error_mss,
                    type: 'error'
                };
                Settings_Vtiger_Index_Js.showMessage(ErrorParam);
                var this_input = form.find('input[value="'+arr_symbol_value[i]+'"]')[0];
                this_input.focus();
                check_valid =  false;
                return false;
            }
        }
        if(check_valid){
            var data = form.serializeFormData();
            data['module'] = app.getModuleName();
            data['action'] = 'SaveAjax';
            data['mode'] = 'saveHideFields';
            data['symbol_array'] = JSON.stringify(arr_symbol_value);
            if(data.selectedFieldsList.length > 0){
                app.request.post({'data':data}).then(
                    function(err,data){
                        if(err === null) {
                            app.hideModalWindow();
                            var params = {};
                            params['text'] = 'HideFields Saved';
                            Settings_Vtiger_Index_Js.showMessage(params);
                            thisInstance.loadListHideFields();
                            app.helper.hideProgress();
                        }else{
                            app.helper.hideProgress();
                        }
                    }
                );
            }
            else{
                app.helper.showNotify('Fields is required');
            }
        }
    },
    /**
     * Function which will handle the registrations for the elements
     */
    registerPopupEvents: function() {
        var container=jQuery('#massEditContainer');
        this.registerSelectModuleEvent(container);
        this.registerAddSymbolEvent();
        this.arrangeSelectChoicesInOrder();
        this.registerSelectFieldChoice();
        var record = jQuery('#record').val();
        if(record == ''){
            jQuery('#hfModuleSelect').trigger("change");
        }
        else{
            //jQuery('#hfModuleSelect').attr("disabled","disabled");
            //jQuery('#hfModuleSelect').attr("disabled", "disabled").trigger('liszt:updated');
            jQuery('.symbol').attr('placeholder', '');
            jQuery('#s2id_hfModuleSelect').prop('disabled',true);
        }
    },
    registerSelectModuleEvent: function(container) {
        var thisInstance = this;
        jQuery("#HideFieldss_form").on("change",'[name="select_module"]', function(e) {
            var select_module=jQuery(this).val();
            var actionParams = {
                'module': 'HideFields',
                'view': 'EditAjax',
                'mode': 'getFields',
                'select_module': select_module
            };
            app.request.post({'data':actionParams}).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        container.find('#fields').html(data);
                        // TODO Make it better with jQuery.on
                        app.changeSelectElementView(container);
                        //register all select2 Elements
                        app.showSelect2ElementView(container.find('select.select2'));
                        thisInstance.registerSelectFieldChoice();
                    }else{
                        app.helper.hideProgress();
                    }
                }
            );
        })
    },
    updateFieldOrder : function(container) {
        var selectedValuesByOrder = {};
        var selectElement = container.find('#selected_fields');
        var select2Element = app.getSelect2ElementFromSelect(selectElement);
        var selectedOptions = selectElement.find('option:selected');
        var orderedSelect2Options = select2Element.find('li.select2-search-choice').find('div');
        var i = 1;
        orderedSelect2Options.each(function(index,element){
            var chosenOption = jQuery(element);
            selectedOptions.each(function(optionIndex, domOption){
                var option = jQuery(domOption);
                if(option.html() == chosenOption.html()) {
                    selectedValuesByOrder[i++] = option.val();
                    return false;
                }
            });
        });
        container.find('input[name="selectedFieldsList"]').val(JSON.stringify(selectedValuesByOrder));
    },
    registerDeleteHideFieldsEvent: function () {
        var thisInstance = this;
        var contents = jQuery('.listViewEntriesDiv');
        contents.on('click','.deleteBlock', function(e) {
            var element=jQuery(e.currentTarget);
            var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
            app.helper.showConfirmationBox({'message' : message}).then(
                function(e) {
                    var id = jQuery(element).data('id');
                    var params = {
                        'module' : 'HideFields',
                        'action' : 'ActionAjax',
                        'mode' : 'deleteHideField',
                        'record' : id
                    };
                    app.helper.showProgress();
                    app.request.post({'data':params}).then(
                        function(err,data){
                            if(err === null) {
                                thisInstance.loadListHideFields();
                                app.helper.hideProgress();
                            }else{
                                app.helper.hideProgress();
                            }
                        }
                    );
                },
                function(error, err){
                }
            );
        });
    },
    loadListHideFields: function() {
        var thisInstance = this;
        var tableBlockModules = jQuery('#tableBlockModules').val();
        var params = {
            'module' : 'HideFields',
            'view' : 'MassActionAjax',
            'mode' : 'reloadListHideFields',
            'source_module' : tableBlockModules,
        };
        app.request.post({'data':params}).then(
            function(err,data){
                if(err === null) {
                    var contents = jQuery('.listViewEntriesDiv');
                    contents.html(data);
                    app.helper.hideProgress();
                }else{
                    app.helper.hideProgress();
                }
            }
        );
    },
    /**
     * Function which will arrange the selected element choices in order
     */
    arrangeSelectChoicesInOrder : function() {
        var container=jQuery('#massEditContainer');
        var selectElement = container.find('#selected_fields');
        var select2Element = app.getSelect2ElementFromSelect(selectElement);

        var choicesContainer = select2Element.find('ul.select2-choices');
        var choicesList = choicesContainer.find('li.select2-search-choice');
        var selectedOptions = selectElement.find('option:selected');

        var selectedOrder = JSON.parse(jQuery('input[name="topFieldIdsList"]', container).val());
        var selectedValuesByOrder = {};
        for(var index=selectedOrder.length ; index > 0 ; index--) {
            var selectedValue = selectedOrder[index-1];
            var option = selectedOptions.filter('[value="'+selectedValue+'"]');
            choicesList.each(function(choiceListIndex,element){
                var liElement = jQuery(element);
                if(liElement.find('div').html() == option.html()){
                    selectedValuesByOrder[index-1] = selectedValue;
                    choicesContainer.prepend(liElement);
                    return false;
                }
            });
        }
        container.find('input[name="selectedFieldsList"]').val(JSON.stringify(selectedValuesByOrder));
    },
    registerSelectFieldChoice: function(){
        var just_select_all = false;
        jQuery('#selected_fields').select2()
            .on("change", function(e) {
                var select_value = [];
                if(typeof e.val !='undefined') select_value = e.val;
                if(select_value[0] == 0){
                    just_select_all = true;
                    jQuery("#selected_fields > option").attr('selected','selected').parent().trigger('liszt:updated');
                    var container=jQuery('#massEditContainer');
                    var selectElement = container.find('#selected_fields');
                    var select2Element = app.getSelect2ElementFromSelect(selectElement);
                    var choicesContainer = select2Element.find('ul.select2-choices');
                    var choicesList = choicesContainer.find('li.select2-search-choice');
                    choicesList.each(function(choiceListIndex,element){
                        var liElement = jQuery(element);
                        if(liElement.find('div').html() != '--All--'){
                            liElement.hide();
                        }
                    });
                }
                else{
                    if(just_select_all || select_value.length === 0){
                        just_select_all = false;
                        jQuery("#hfModuleSelect").trigger('change');
                    }
                }
            });
    },
    registerAddSymbolEvent: function() {
        jQuery('.modelContainer').on("click",'.add_more', function() {
            var new_symbol = ' <input type="text" class="symbol" name="symbol[]" placeholder="[_X_]" maxlength="4" value=""/>' +
                ' <a class="remove_symbol" title="Remove" href="javascript:void(0);"><i title="Remove" style="vertical-align: top;" class="fa fa-times-circle alignTop1"></i></a>';
            jQuery('#s_symbol').append(new_symbol);
            jQuery(this).prev().focus();
        });
        jQuery('.modelContainer').on("click",'.remove_symbol', function() {
            jQuery(this).prev().remove();
            jQuery(this).remove();
        });
        //this.registerSymbolChangeEvent();
    },
    registerEvents : function() {
        this.registerAddButtonEvent();
        this.registerEditButtonEvent();
        this.registerSelectModuleEvent();
        this.registerDeleteHideFieldsEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
    }
});
jQuery(document).ready(function() {
    var instance = new HideFields_Settings_Js();
    instance.registerEvents();
    Vtiger_Index_Js.getInstance().registerEvents();
});