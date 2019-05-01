/* ********************************************************************************
 * The content of this file is subject to the VTEMailConverter("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_List_Js("VTEMailConverter_List_Js", {

    configureActionRule: function () {
        var thisInstance = this;
        var moduleName = app.getModuleName();
        var url = 'index.php?module='+moduleName+'&view=ConfigureAction';
        app.helper.showProgress();
        app.request.get({'url': url}).then(function (error, data) {
            if (data) {
                app.helper.hideProgress();
                app.helper.showModal(data, {'cb': function (container) {
                    container.find('.vte-email-converter-close-action').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        app.helper.hideModal();
                    });
                    container.find('select[name=current_action_module]').unbind('change').on('change', function (e) {
                        e.preventDefault();
                        thisInstance.changeAction(container, $(this).val());
                    });
                    container.find('select.field-name').unbind('change').on('change', function (e) {
                        e.preventDefault();
                        var trElement = $(this).closest('tr');
                        thisInstance.changeFieldName(trElement);
                    });
                    container.find('.vte-email-converter-save-action').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        var saveBtn = $(this);
                        thisInstance.saveRule(saveBtn);
                    });
                    container.find('.vte-email-converter-delete-action').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        thisInstance.deleteAction();
                    });
                    container.find('.addButton').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        thisInstance.addField(container);
                    });
                    container.find('.deleteRecordButton').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        thisInstance.removeField(container, $(this));
                    });
                    container.find('.create-new-action-form').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        app.helper.hideModal().then(function () {
                            thisInstance.createAction();
                        });
                    });
                    container.find('.advanced-options-btn').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        var thElement = $(this).closest('th');
                        var tableElement = $(this).closest('table');
                        var thColspan = thElement.attr('colspan');
                        if(thColspan==3){
                            thElement.removeAttr('colspan').attr('colspan', 6);
                            tableElement.find('td.advanced-options').show();
                            $(this).html(app.vtranslate('LBL_HIDE_ADVANCED_OPTIONS'));
                        }else{
                            thElement.removeAttr('colspan').attr('colspan', 3);
                            tableElement.find('td.advanced-options').hide();
                            $(this).html(app.vtranslate('LBL_SHOW_ADVANCED_OPTIONS'));
                        }
                    });
                    container.find('.advanced-options-btn').trigger('click');

                    thisInstance.registerShowTooltip(container);
                }});
            }
        });
    },

    createAction: function () {
        var thisInstance = this;
        var moduleName = app.getModuleName();
        var url = 'index.php?module='+moduleName+'&view=CreateAction';
        app.helper.showProgress();
        app.request.get({'url': url}).then(function (error, data) {
            if (data) {
                app.helper.hideProgress();
                app.helper.showModal(data, {'cb': function (container) {
                    container.find('.vte-email-converter-cancel-action').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        app.helper.hideModal().then(function () {
                            thisInstance.configureActionRule();
                        });
                    });

                    container.find('input[name=action_name]').unbind('change').on('change', function (e) {
                        e.preventDefault();
                        thisInstance.updateActionKey(container);
                    });

                    container.find('select#action_type').unbind('change').on('change', function (e) {
                        e.preventDefault();
                        thisInstance.updateActionKey(container);
                    });

                    container.find('select[name=modulename1]').unbind('change').on('change', function (e) {
                        e.preventDefault();
                        thisInstance.updateActionKey(container);
                    });

                    container.find('.vte-email-converter-create-action-btn').unbind('click').on('click', function (e) {
                        e.preventDefault();
                        var saveBtn = $(this);
                        var form = saveBtn.closest('form');
                        //valid rules
                        var action_name = $.trim(form.find('input[name=action_name]').val());
                        if(action_name===''){
                            app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_ACTION_NAME')});
                            return;
                        }
                        if(form.find('select#action_type').val()==''){
                            app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_ACTION_TYPE')});
                            return;
                        }
                        if(form.find('select[name=modulename1]').val()==''){
                            app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_ACTION_MODULE')});
                            return;
                        }
                        if(form.find('input[name=action_key]').val()===''){
                            app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_ACTION_KEY')});
                            return;
                        }
                        saveBtn.attr('disabled', true);
                        var params = form.serialize();
                        var aDeferred = jQuery.Deferred();
                        form.progressIndicator();
                        app.request.post({data: params}).then(function (err, res) {
                            if(err==null){
                                if(res.status == 1){
                                    app.helper.hideModal().then(function () {
                                        thisInstance.configureActionRule();
                                    });
                                }else{
                                    app.helper.showErrorNotification({'message': res.message})
                                    form.progressIndicator({'mode': 'hide'});
                                    saveBtn.removeAttr('disabled');
                                }
                            }
                        });
                        return aDeferred.promise();
                    });
                }});
            }
        });
    },

    updateActionKey: function(container){
        var action_key = '';
        var modulename1 = container.find('select[name=modulename1]').val();
        var action_name = container.find('input[name=action_name]').val();
        var action_type = container.find('select#action_type').val();
        action_name = $.trim(action_name);
        if(modulename1 != '' && action_name != '' && action_type != ''){
            action_name = action_name.replace(/[^\w\s]/gi, '');
            action_name = action_name.replace(/\s/g,'');
            action_name = action_name.replace( /_/g,'');
            action_name = action_name.replace( /-/g,'');
            action_key = action_type+'_VTEMailConverter_'+modulename1+action_name;
        }

        container.find('input[name=action_key]').val(action_key);
    },

    RunCron: function (btn) {
        var thisInstance = this;
        var url = $(btn).data('url');
        app.helper.showProgress(app.vtranslate('CRON_RUNNING'));
        app.request.get({'url': url}).then(
            function (err, data) {
                if (err == null) {
                    app.helper.hideProgress();
                    app.helper.showSuccessNotification({'message': app.vtranslate('CRON_RUN_SUCCESS')});
                    var vtigerListInstance = new Vtiger_List_Js();
                    var params = {};
                    vtigerListInstance.loadListViewRecords(params);
                } else {
                    app.helper.hideProgress();
                    app.helper.showErrorNotification({'message': app.vtranslate(err.message)})
                }
            }
        );
    },

    deleteAction: function () {
        var thisInstance = this;
        var message = app.vtranslate('JS_ARE_YOU_SURE_TO_DELETE_ACTION_CONFIGURATION');
        app.helper.showConfirmationBox({'message': message}).then(function (e) {
            var params = {};
            params['module'] = 'VTEMailConverter';
            params['action'] = 'DeleteRule';
            params['config_action'] = $('form[name=vte-email-converter-configure-action-form] select[name=current_action_module]').val();

            app.helper.showProgress();
            app.request.post({data: params}).then(
                function (error, result) {
                    app.helper.hideProgress();
                    if (error) {
                        app.helper.showErrorNotification({'message': app.vtranslate(err.message)});
                    }
                    app.helper.hideModal();
                    thisInstance.configureActionRule();
                }
            );
        });
    },

    saveRule: function (saveBtn) {
        var form = saveBtn.closest('form');
        //valid rules
        if(form.find('select[name=current_action_module]').val()==''){
            app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_ACTION')});
            return;
        }
        if(form.find('tr.listViewEntries').length==0){
            app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_RULE')});
            return;
        }else{
            var identifier_num = 0;
            var end_with_empty = 0;
            form.find('tr.listViewEntries').each(function(){
                if($(this).find('.vte-email-converter-identifier').val() != ''){
                    identifier_num++;
                    if($(this).find('.end-with').val() == ''){
                        end_with_empty++;
                    }
                }
            });
            if(identifier_num==0){
                app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_IDENTIFIER_LEAST_ONE')});
                return;
            }
            if(end_with_empty>0){
                app.helper.showErrorNotification({'message': app.vtranslate('JS_REQUIRED_END_WITH')});
                return;
            }
        }

        saveBtn.attr('disabled', true);
        var params = form.serialize();
        var aDeferred = jQuery.Deferred();
        app.helper.showProgress(app.vtranslate('JS_SAVING_RULE'));
        app.request.post({data: params}).then(
            function (err, data) {
                if(err===null) {
                    app.helper.hideProgress();
                    app.helper.hideModal();
                }else{
                    app.helper.hideProgress();
                    app.helper.showErrorNotification({'message': err});
                }
            }
        );

        return aDeferred.promise();
    },

    changeAction: function (container, current_action_module) {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;
        var params = {};
        params['module'] = app.getModuleName();
        params['view'] = 'ConfigureAction';
        params['current_action_module'] = current_action_module;
        params['ajax'] = true;

        app.request.post({data: params}).then(
            function (error, data) {
                container.find('tbody').html(data);
                container.find('select.field-name').unbind('change').on('change', function (e) {
                    e.preventDefault();
                    var trElement = $(this).closest('tr');
                    thisInstance.changeFieldName(trElement);
                });
                container.find('.deleteRecordButton').unbind('click').on('click', function (e) {
                    e.preventDefault();
                    thisInstance.removeField(container, $(this));
                });
                thisInstance.updateDefaultValueFieldType(container);
                //hide Advanced Options
                container.find('.advanced-options-btn').closest('th').removeAttr('colspan').attr('colspan', 3);
                container.find('td.advanced-options').hide();
                container.find('.advanced-options-btn').html(app.vtranslate('LBL_SHOW_ADVANCED_OPTIONS'));

                //if current action is CREATE action then hide Match Field,
                //show Option allow Create new Record when is UPDATE
                if(current_action_module != '') {
                    var current_action_module_arr = current_action_module.split('_');
                    if(current_action_module_arr[0]=='CREATE'){
                        container.find('td.match-field select').val(0);
                        container.find('td.match-field').hide();
                        container.find('div.create-if-not-existed select').val(0);
                        container.find('div.create-if-not-existed').hide();
                    }else{
                        container.find('td.match-field').show();
                        var create_if_not_existed_value = container.find('#create_if_not_existed_value').val();
                        if(create_if_not_existed_value == ''){
                            create_if_not_existed_value = 0;
                        }
                        container.find('div.create-if-not-existed').css('display', 'inline-block');
                        container.find('div.create-if-not-existed select').val(create_if_not_existed_value);
                    }
                }else{
                    container.find('td.match-field').show();
                    var create_if_not_existed_value = container.find('#create_if_not_existed_value').val();
                    if(create_if_not_existed_value == ''){
                        create_if_not_existed_value = 0;
                    }
                    container.find('div.create-if-not-existed').css('display', 'inline-block');
                    container.find('div.create-if-not-existed select').val(create_if_not_existed_value);
                }

                vtUtils.applyFieldElementsView(container);
                thisInstance.registerAutoCompleteEndWith(container);
                thisInstance.registerAutoCompleteSeparator(container);
            }
        );

        return aDeferred.promise();
    },

    updateDefaultValueFieldType: function (container) {
        var thisInstance = this;
        if(container.find('tr.listViewEntries').length > 0){
            container.find('tr.listViewEntries').each(function(){
                var trElement = $(this);
                var default_value = trElement.find('.default_value_field').val();
                thisInstance.changeFieldName(trElement, default_value);
            });
        }
    },

    changeFieldName: function (trElement, default_value) {
        if(typeof default_value == 'undefined'){
            default_value = null;
        }
        var tableElement = trElement.closest('table');
        var field_name = trElement.find('select.field-name').val();
        var allDefaultValuesContainer = tableElement.find('#defaultValuesElementsContainer');
        if($('#'+field_name+'_defaultvalue_container', allDefaultValuesContainer).length>0) {
            var current_time_stamp = Date.now();
            var selectedFieldDefValueContainer = $('#' + field_name + '_defaultvalue_container', allDefaultValuesContainer);
            var new_selectedFieldDefValueContainer = selectedFieldDefValueContainer.clone(true, true);
            new_selectedFieldDefValueContainer.attr('id', field_name+'_defaultvalue_container'+'_'+current_time_stamp)
            trElement.find('td.default-value').html(new_selectedFieldDefValueContainer);
            //register date fields event to show mini calendar on click of element
            trElement.find('select').addClass('select2');
            trElement.find('.default_value_field').attr('name', 'rules[default_value][]');
            var field_type = trElement.find('.default_value_field').data('fieldtype');
            if(field_type=='date' || field_type=='datetime' || field_type=='dateTimeField'){
                trElement.find('.default_value_field').addClass('dateField');
            }else if(field_type=='time'){
                trElement.find('.default_value_field').addClass('timepicker-default');
            }
            trElement.find('.default_value_field').attr('id', field_name+'_defaultvalue'+'_'+current_time_stamp);

            //update value
            if(default_value != null) {
                if (trElement.find('.default_value_field').is('input')) {
                    var elementType = trElement.find('.default_value_field').attr('type');
                    if (elementType == 'checkbox') {
                        trElement.find('.default_value_field').val(1);
                        if (default_value == 'on' || default_value == 1) {
                            trElement.find('.default_value_field').attr('checked', true);
                        }
                    } else {
                        trElement.find('.default_value_field').val(default_value);
                    }
                } else if (trElement.find('.default_value_field').is('select')) {
                    trElement.find('.default_value_field').addClass('select2');
                    trElement.find('.default_value_field').val(default_value);
                }
            }

            vtUtils.applyFieldElementsView(trElement.find('td.default-value'));
        }
    },

    addField: function (container) {
        container.find('select.select2').select2("destroy");
        container.find('input.autoComplete').autocomplete("destroy");

        var trFirstElement = container.find('.listViewEntries:first');
        var newField = trFirstElement.clone(true);
        newField.find('input.vte-email-converter-identifier').val('');
        newField.find('input.end-with').val('');
        newField.find('td.default-value').html('<input name="rules[default_value][]" class="inputElement input-medium" />');
        newField.find('td.advanced-options input').val('');
        newField.appendTo(container.find('tbody'));
        vtUtils.applyFieldElementsView(container);
        this.registerAutoCompleteEndWith(container);
        this.registerAutoCompleteSeparator(container);
    },

    removeField: function (container, btnElement) {
        if(container.find('tr.listViewEntries').length > 1){
            var trElement = btnElement.closest('tr');
            trElement.remove();
        }
    },

    changeEndWithToSelect2: function(container){
        container.find('.end-with').select2({
            tags: true,
            multiple: false,
            createTag: function (params) {
                return {
                    id: params.term,
                    text: params.term,
                    newOption: true
                }
            },
            templateResult: function (data) {
                var $result = $("<span></span>");
                $result.text(data.text);
                if (data.newOption) {
                    $result.append(" <small>(new)</small>");
                }
                return $result;
            }
        });
    },

    registerAutoCompleteEndWith : function(container) {
        var source = [
            {label: app.vtranslate('LBL_LINE_BREAK'), value: 'EP_LINEBREAK'},
            {label: app.vtranslate('LBL_SPACE'), value: 'EP_SPACE'},
            {label: app.vtranslate('LBL_END_OF_BODY'), value: 'EP_END_OF_BODY'}
        ];
        container.find('input.end-with').autocomplete({
            'minLength' : 0,
            'source' : source
        }).blur(function(){
            $(this).autocomplete('enable');
        }).focus(function () {
            $(this).autocomplete('search', '');
        });
    },

    registerAutoCompleteSeparator : function(container) {
        var source = [
            {label: app.vtranslate('LBL_LINE_BREAK'), value: 'EP_LINEBREAK'},
            {label: app.vtranslate('LBL_SPACE'), value: 'EP_SPACE'}
        ];
        container.find('input.advanced-separator').autocomplete({
            'minLength' : 0,
            'source' : source
        }).blur(function(){
            $(this).autocomplete('enable');
        }).focus(function () {
            $(this).autocomplete('search', '');
        });
    },

    registerShowTooltip: function(container){
        var options = {};
        options['animation'] = true;
        options['html'] = true;
        options['trigger'] = 'hover';
        var tooltipElement = container.find('.ep-tooltip');
        tooltipElement.tooltip(options);
    }

} ,{

});