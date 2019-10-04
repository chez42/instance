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

        app.showModalWindow(null, url, function (container) {
            $('#globalmodal').closest('.blockUI').css({'width': '100%'});
            $('.blockOverlay').unbind('click');

            container.find('.vte-email-converter-close-action').unbind('click').on('click', function (e) {
                e.preventDefault();
                app.hideModalWindow();
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
                thisInstance.createAction();
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
        });
    },

    createAction: function () {
        var thisInstance = this;
        var moduleName = app.getModuleName();
        var url = 'index.php?module='+moduleName+'&view=CreateAction';
        app.hideModalWindow();
        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        app.showModalWindow(null, url, function (container) {
            $('.blockOverlay').unbind('click');

            container.find('.vte-email-converter-cancel-action').unbind('click').on('click', function (e) {
                e.preventDefault();
                app.hideModalWindow();
                thisInstance.configureActionRule();
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
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_ACTION_NAME'));
                    return;
                }
                if(form.find('select#action_type').val()==''){
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_ACTION_TYPE'));
                    return;
                }
                if(form.find('select[name=modulename1]').val()==''){
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_ACTION_MODULE'));
                    return;
                }
                if(form.find('input[name=action_key]').val()===''){
                    Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_ACTION_KEY'));
                    return;
                }
                saveBtn.attr('disabled', true);
                var params = form.serialize();
                var aDeferred = jQuery.Deferred();
                form.progressIndicator();
                AppConnector.request(params).then(
                    function(data){
                        if(data.success){
                            var result = data.result;
                            if(result.status == 1){
                                app.hideModalWindow();
                                thisInstance.configureActionRule();
                            }else{
                                Vtiger_Helper_Js.showPnotify(result.message);
                                form.progressIndicator({'mode': 'hide'});
                                saveBtn.removeAttr('disabled');
                            }
                        }
                    }
                );

                return aDeferred.promise();
            });

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
        var progressIndicatorElement = $.progressIndicator({
            'message' : app.vtranslate('CRON_RUNNING'),
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });
        AppConnector.request(url).then(
            function(response) {
                var vtigerListInstance = new Vtiger_List_Js();
                progressIndicatorElement.progressIndicator({'mode': 'hide'});
                vtigerListInstance.getListViewRecords().then(
                    function(data){
                        vtigerListInstance.updatePagination();
                    },
                    function(textStatus, errorThrown){
                    }
                );
            }
        );
    },

    deleteAction: function () {
        var thisInstance = this;
        var message = app.vtranslate('JS_ARE_YOU_SURE_TO_DELETE_ACTION_CONFIGURATION');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
            function(e) {
                var params = {};
                params['module'] = 'VTEMailConverter';
                params['action'] = 'DeleteRule';
                params['config_action'] = $('form[name=vte-email-converter-configure-action-form] select[name=current_action_module]').val();
                AppConnector.request(params).then(
                    function(response) {
                        app.hideModalWindow();
                        thisInstance.configureActionRule();
                    }
                );
            },
            function(error, err){
            }
        );
    },

    saveRule: function (saveBtn) {
        var form = saveBtn.closest('form');
        //valid rules
        if(form.find('select[name=current_action_module]').val()==''){
            Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_ACTION'));
            return;
        }
        if(form.find('tr.listViewEntries').length==0){
            Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_RULE'));
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
                Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_IDENTIFIER_LEAST_ONE'));
                return;
            }
            if(end_with_empty>0){
                Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_REQUIRED_END_WITH'));
                return;
            }
        }
        saveBtn.attr('disabled', true);
        var params = form.serialize();
        var aDeferred = jQuery.Deferred();
        form.progressIndicator();
        AppConnector.request(params).then(
            function(data){
                app.hideModalWindow();
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

        AppConnector.request(params).then(
            function(data){
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
                if(current_action_module) {
                    thisInstance.updateDefaultValueFieldType(container);
                }
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
                        container.find('div.create-if-not-existed select').val(0).trigger("liszt:updated");;
                        container.find('div.create-if-not-existed').hide();
                    }else{
                        container.find('td.match-field').show();
                        var create_if_not_existed_value = container.find('#create_if_not_existed_value').val();
                        if(create_if_not_existed_value == ''){
                            create_if_not_existed_value = 0;
                        }
                        container.find('div.create-if-not-existed').css('display', 'inline-block');
                        container.find('div.create-if-not-existed select').val(create_if_not_existed_value).trigger("liszt:updated");;
                    }
                }else{
                    container.find('td.match-field').show();
                    var create_if_not_existed_value = container.find('#create_if_not_existed_value').val();
                    if(create_if_not_existed_value == ''){
                        create_if_not_existed_value = 0;
                    }
                    container.find('div.create-if-not-existed').css('display', 'inline-block');
                    container.find('div.create-if-not-existed select').val(create_if_not_existed_value).trigger("liszt:updated");;
                }

                app.changeSelectElementView(container);
                thisInstance.registerAutoCompleteEndWith(container);
                thisInstance.registerAutoCompleteSeparator(container);
            }
        );

        return aDeferred.promise();
    },

    updateDefaultValueFieldType: function (container) {
        var allDefaultValuesContainer = container.find('#defaultValuesElementsContainer');
        if(container.find('tr.listViewEntries').length > 0){
            container.find('tr.listViewEntries').each(function(){
                var trElement = $(this);
                var field_name = trElement.find('.field-name').val();
                var default_value = trElement.find('.default_value_field').val();
                if($('#'+field_name+'_defaultvalue_container', allDefaultValuesContainer).length>0) {
                    var selectedFieldDefValueContainer = $('#' + field_name + '_defaultvalue_container', allDefaultValuesContainer);
                    var new_selectedFieldDefValueContainer = selectedFieldDefValueContainer.clone(true, true);
                    trElement.find('td.default-value').html(new_selectedFieldDefValueContainer);
                    //register date fields event to show mini calendar on click of element
                    if(trElement.find('.default_value_field').is('input')){
                        var elementType = trElement.find('.default_value_field').attr('type');
                        if(elementType=='checkbox'){
                            trElement.find('.default_value_field').val(1);
                            if(default_value=='on' || default_value==1){
                                trElement.find('.default_value_field').attr('checked', true);
                            }
                        }else{
                            trElement.find('.default_value_field').val(default_value);
                        }
                    }else if(trElement.find('.default_value_field').is('select')){
                        trElement.find('.default_value_field').addClass('chzn-select');
                        trElement.find('.default_value_field').val(default_value);
                    }
                    trElement.find('.default_value_field').attr('name', 'rules[default_value][]');
                    app.registerEventForDatePickerFields(trElement);
                    app.changeSelectElementView(trElement);
                }
            });
        }
    },

    changeFieldName: function (trElement) {
        var tableElement = trElement.closest('table');
        var field_name = trElement.find('.field-name').val();
        var allDefaultValuesContainer = tableElement.find('#defaultValuesElementsContainer');
        if($('#'+field_name+'_defaultvalue_container', allDefaultValuesContainer).length>0) {
            var selectedFieldDefValueContainer = $('#' + field_name + '_defaultvalue_container', allDefaultValuesContainer);
            var new_selectedFieldDefValueContainer = selectedFieldDefValueContainer.clone(true, true);
            trElement.find('td.default-value').html(new_selectedFieldDefValueContainer);
            //register date fields event to show mini calendar on click of element
            trElement.find('select').addClass('chzn-select');
            trElement.find('.default_value_field').attr('name', 'rules[default_value][]');
            app.registerEventForDatePickerFields(trElement);
            app.changeSelectElementView(trElement);
        }
    },

    addField: function (container) {
        var thisInstance = this;
        //container.find('input.autoComplete').autocomplete("destroy");
        var trFirstElement = container.find('.listViewEntries:first');
        var newField = trFirstElement.clone(false, false);
        newField.find('td input').val('');
        newField.find('div.chzn-container').remove();
        newField.find('select.chzn-select').val('').removeAttr('id').removeClass('chzn-done').css('display', 'block');
        newField.find('td.default-value').html('<input name="rules[default_value][]" class="input-medium" />');
        container.find('tbody').append(newField);
        app.changeSelectElementView(newField);
        this.registerAutoCompleteEndWith(newField);
        this.registerAutoCompleteSeparator(newField);
        newField.find('.deleteRecordButton').unbind('click').on('click', function (e) {
            e.preventDefault();
            thisInstance.removeField(container, $(this));
        });
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
    /*
     * Function to register List view Page Navigation
     */
    registerPageNavigationEvents : function(){
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;
        jQuery('#listViewNextPageButton').off('click').on('click',function(){
            var pageLimit = jQuery('#pageLimit').val();
            var noOfEntries = jQuery('#noOfEntries').val();
            if(noOfEntries == pageLimit){
                var orderBy = jQuery('#orderBy').val();
                var sortOrder = jQuery("#sortOrder").val();
                var cvId = thisInstance.getCurrentCvId();
                var urlParams = {
                    "orderby": orderBy,
                    "sortorder": sortOrder,
                    "viewname": cvId
                }
                var pageNumber = jQuery('#pageNumber').val();
                var nextPageNumber = parseInt(parseFloat(pageNumber)) + 1;
                jQuery('#pageNumber').val(nextPageNumber);
                jQuery('#pageToJump').val(nextPageNumber);
                thisInstance.getListViewRecords(urlParams).then(
                    function(data){
                        thisInstance.updatePagination();
                        aDeferred.resolve();
                    },

                    function(textStatus, errorThrown){
                        aDeferred.reject(textStatus, errorThrown);
                    }
                );
            }
            return aDeferred.promise();
        });
        jQuery('#listViewPreviousPageButton').off('click').on('click',function(){
            var aDeferred = jQuery.Deferred();
            var pageNumber = jQuery('#pageNumber').val();
            if(pageNumber > 1){
                var orderBy = jQuery('#orderBy').val();
                var sortOrder = jQuery("#sortOrder").val();
                var cvId = thisInstance.getCurrentCvId();
                var urlParams = {
                    "orderby": orderBy,
                    "sortorder": sortOrder,
                    "viewname" : cvId
                }
                var previousPageNumber = parseInt(parseFloat(pageNumber)) - 1;
                jQuery('#pageNumber').val(previousPageNumber);
                jQuery('#pageToJump').val(previousPageNumber);
                thisInstance.getListViewRecords(urlParams).then(
                    function(data){
                        thisInstance.updatePagination();
                        aDeferred.resolve();
                    },

                    function(textStatus, errorThrown){
                        aDeferred.reject(textStatus, errorThrown);
                    }
                );
            }
        });

        jQuery('#listViewPageJump').off('click').on('click',function(e){
            if(typeof Vtiger_WholeNumberGreaterThanZero_Validator_Js.invokeValidation(jQuery('#pageToJump'))!= 'undefined') {
                var pageNo = jQuery('#pageNumber').val();
                jQuery("#pageToJump").val(pageNo);
            }
            jQuery('#pageToJump').validationEngine('hideAll');
            var element = jQuery('#totalPageCount');
            var totalPageNumber = element.text();
            if(totalPageNumber == ""){
                var totalCountElem = jQuery('#totalCount');
                var totalRecordCount = totalCountElem.val();
                if(totalRecordCount != '') {
                    var recordPerPage = jQuery('#pageLimit').val();
                    if(recordPerPage == '0') recordPerPage = 1;
                    pageCount = Math.ceil(totalRecordCount/recordPerPage);
                    if(pageCount == 0){
                        pageCount = 1;
                    }
                    element.text(pageCount);
                    return;
                }
                element.progressIndicator({});
                thisInstance.getPageCount().then(function(data){
                    var pageCount = data['result']['page'];
                    totalCountElem.val(data['result']['numberOfRecords']);
                    if(pageCount == 0){
                        pageCount = 1;
                    }
                    element.text(pageCount);
                    element.progressIndicator({'mode': 'hide'});
                });
            }
        })

        jQuery('#listViewPageJumpDropDown').off('click').on('click','li',function(e){
            e.stopImmediatePropagation();
        }).on('keypress','#pageToJump',function(e){
            if(e.which == 13){
                e.stopImmediatePropagation();
                var element = jQuery(e.currentTarget);
                var response = Vtiger_WholeNumberGreaterThanZero_Validator_Js.invokeValidation(element);
                if(typeof response != "undefined"){
                    element.validationEngine('showPrompt',response,'',"topLeft",true);
                } else {
                    element.validationEngine('hideAll');
                    var currentPageElement = jQuery('#pageNumber');
                    var currentPageNumber = currentPageElement.val();
                    var newPageNumber = parseInt(jQuery(e.currentTarget).val());
                    var totalPages = parseInt(jQuery('#totalPageCount').text());
                    if(newPageNumber > totalPages){
                        var error = app.vtranslate('JS_PAGE_NOT_EXIST');
                        element.validationEngine('showPrompt',error,'',"topLeft",true);
                        return;
                    }
                    if(newPageNumber == currentPageNumber){
                        var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+newPageNumber;
                        var params = {
                            text: message,
                            type: 'info'
                        };
                        Vtiger_Helper_Js.showMessage(params);
                        return;
                    }
                    currentPageElement.val(newPageNumber);
                    thisInstance.getListViewRecords().then(
                        function(data){
                            thisInstance.updatePagination();
                            element.closest('.btn-group ').removeClass('open');
                        },
                        function(textStatus, errorThrown){
                        }
                    );
                }
                return false;
            }
        });
    },

    /**
     * Function to update Pagining status
     */
    updatePagination : function(){
        var previousPageExist = jQuery('#previousPageExist').val();
        var nextPageExist = jQuery('#nextPageExist').val();
        var previousPageButton = jQuery('#listViewPreviousPageButton');
        var nextPageButton = jQuery('#listViewNextPageButton');
        var pageJumpButton = jQuery('#listViewPageJump');
        var listViewEntriesCount = parseInt(jQuery('#noOfEntries').val());
        var pageStartRange = parseInt(jQuery('#pageStartRange').val());
        var pageEndRange = parseInt(jQuery('#pageEndRange').val());
        var pages = jQuery('#totalPageCount').text();
        var totalNumberOfRecords = jQuery('.totalNumberOfRecords');
        var pageNumbersTextElem = jQuery('.pageNumbersText');
        var pageNumber = jQuery('#pageNumber').val();  //551608 core issue

        if(pages > 1){
            pageJumpButton.removeAttr('disabled');
        }
        if(previousPageExist != "" || pageNumber > 1){ //551608 core issue
            previousPageButton.removeAttr('disabled');
        } else if(previousPageExist == "") {
            previousPageButton.attr("disabled","disabled");
        }

        if((nextPageExist != "") && (pages >1)){
            nextPageButton.removeAttr('disabled');
        } else if((nextPageExist == "") || (pages == 1)) {
            nextPageButton.attr("disabled","disabled");
        }
        if(listViewEntriesCount != 0){
            var pageNumberText = pageStartRange+" "+app.vtranslate('to')+" "+pageEndRange;
            pageNumbersTextElem.html(pageNumberText);
            totalNumberOfRecords.removeClass('hide');
        } else {
            pageNumbersTextElem.html("<span>&nbsp;</span>");
            if(!totalNumberOfRecords.hasClass('hide')){
                totalNumberOfRecords.addClass('hide');
            }
        }

    },

    registerEvents : function() {
        this._super();
        this.registerPageNavigationEvents();
    }
});