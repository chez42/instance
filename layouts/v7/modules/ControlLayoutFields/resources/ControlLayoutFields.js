/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger.Class("Control_Layout_Fields_Js",{
},{
    fieldValuesCache: {},
    /*
     * Function to register the change module filter
     */
    registerModuleFilterChange:function(){
        jQuery('#clfModuleFilter').on('change',function(){
            var filter_value = jQuery(this).val();
            window.location.href = 'index.php?module=ControlLayoutFields&parent=Settings&view=ListAll&mode=listAll&ModuleFilter=' + filter_value;
        });
    },
    registerPagingAction:function(){
        var current_page = jQuery('#current_page').val();
        var filter_value = jQuery('#clfModuleFilter').val();
        jQuery('#clfListViewNextPageButton').on('click',function(){
            window.location.href = 'index.php?module=ControlLayoutFields&parent=Settings&view=ListAll&mode=listAll&ModuleFilter=' + filter_value+'&page='+(parseInt(current_page) + 1);
        });
        jQuery('#clfListViewPreviousPageButton').on('click',function(){
            window.location.href = 'index.php?module=ControlLayoutFields&parent=Settings&view=ListAll&mode=listAll&ModuleFilter=' + filter_value+'&page='+(parseInt(current_page) - 1);
        });

    },
    registerDeleteAction:function(){
        var current_page = jQuery('#current_page').val();
        var filter_value = jQuery('#clfModuleFilter').val();
        jQuery('.removeCLF').on('click',function(){
            var recordId = jQuery(this).data('id');
            var message = app.vtranslate('Are you sure you want to delete this row?');
            app.helper.showConfirmationBox({'message' : message}).then(
                function(){
                    window.location.href = 'index.php?module=ControlLayoutFields&parent=Settings&view=ListAll&mode=delete&record='+recordId+'&ModuleFilter=' + filter_value+'&page='+current_page;
                },
                function(error, err){
                }
            );
        });
    },
    registerEventClickOnRow:function(){
        jQuery("[id^='ControlLayoutFields_listView_row_'] td").on('click',function(e){
            if (e.target.nodeName == 'TD'){
                var url = $(this).closest("tr").attr("data-recordurl");
                window.location.href = url;
            }
        });
    },
    displayByClf:function(module,field_name_changed){
        var thisInstance = this;
        //to integrate with Custom View & Form
        if(module == "CustomFormsViews"){
            var top_url = window.location.href.split('?');
            var array_url = thisInstance.getQueryParams(top_url[1]);
            module = array_url.currentModule;
        }
        var params = {
            module : 'ControlLayoutFields',
            action : 'ActionAjax',
            mode : 'checkCLFForModule',
            current_module : module
        };
        app.request.post({'data': params}).then(
            function(err,data){
                if(err === null) {
                    if(!jQuery.isEmptyObject(data)){
                        var role_id = data.role_id;
                        jQuery.each(data.clf_info,function(k,v){
                            var all_condition = v.condition.all;
                            var any_condition = v.condition.any;
                            var actions = v.actions;
                            var condition_key = k;
                            var check_condition = thisInstance.checkConditionToForm(all_condition,any_condition,field_name_changed,'',role_id);
                            if(check_condition){
                                jQuery.each(actions,function(key,value){
                                    var field_name_action = value.field;
                                    var form_element = jQuery('#EditView').find('[name="'+field_name_action+'"]');
                                    //for Multiple Value control
                                    if(form_element.attr('type') == 'hidden'){
                                        form_element = jQuery('#EditView').find('[name="'+field_name_action+'[]"]');
                                        if(form_element.length === 0){
                                            //for reference control uitype 10
                                            form_element = jQuery('#EditView').find('[name="'+field_name_action+'_display"]');

                                            if(form_element.length === 0){
                                                //for Checkbox control
                                                form_element = jQuery('#EditView').find('[name="'+field_name_action+'"]').last();

                                            }
                                        }
                                    }
                                    var data_info = form_element.data('fieldname');
                                    var this_td = form_element.closest('td');
                                    if(value.option == 'mandatory'){
                                        this_td.show();
                                        this_td.prev().show();
                                        form_element.attr('data-rule-required','true');
                                        form_element.addClass(condition_key+'-clf-mandatory');
                                        var field_label = form_element.closest('td').prev();
                                        if(!field_label.find('span').length) field_label.append('<span class="redColor">*</span>');

                                    }else if(value.option == 'read_only'){
                                        this_td.show();
                                        this_td.prev().show();
                                        form_element.attr('readonly','readonly');
                                        form_element.css('background','rgb(235, 235, 228)');
                                        form_element.addClass(condition_key+'-clf-read-only');
                                        if (typeof data_info != 'undefined'){
                                            if(data_info.type == 'reference'){
                                                var parent_span = form_element.closest('span');
                                                parent_span.find('div:first').hide();
                                            }else if(data_info.type == 'multipicklist'){
                                                form_element.select2('disable');
                                            }
                                        }
                                    }else if(value.option == 'hide'){
                                        form_element.addClass(condition_key+'-clf-hide');
                                        this_td.hide();
                                        this_td.prev().hide();
                                        var this_tr = this_td.closest('tr');
                                        thisInstance.hideTr(this_tr);
                                    }
                                    if(form_element.is('select')) form_element.trigger('liszt:updated');
                                });
                            }
                            else{
                                jQuery.each(actions,function(key,value){
                                    var field_name_action = value.field;
                                    var form_element = jQuery('#EditView').find('[name="'+field_name_action+'"]');
                                    //for Multiple Value control
                                    if(form_element.attr('type') == 'hidden'){
                                        form_element = jQuery('#EditView').find('[name="'+field_name_action+'[]"]');
                                        if(form_element.length === 0){
                                            //for reference control uitype 10
                                            form_element = jQuery('#EditView').find('[name="'+field_name_action+'_display"]');
                                            if(form_element.length === 0){
                                                //for Checkbox control
                                                form_element = jQuery('#EditView').find('[name="'+field_name_action+'"]').last();
                                            }
                                        }
                                    }
                                    var data_info = form_element.data('fieldinfo');
                                    if(form_element.hasClass(condition_key+'-clf-mandatory')){
                                        form_element.removeAttr('data-rule-required');
                                        form_element.removeAttr('aria-required');
                                        form_element.removeAttr('aria-invalid');
                                        var field_label = form_element.closest('td').prev().find('span');
                                        if(field_label.length) field_label.remove();
                                        form_element.removeClass(condition_key+'-clf-mandatory');
                                    }
                                    if(form_element.hasClass(condition_key+'-clf-read-only')){
                                        form_element.removeAttr('readonly');
                                        form_element.css('background','white');
                                        form_element.removeClass(condition_key+'-clf-read-only');
                                        if (typeof data_info != 'undefined'){
                                            if(data_info.type == 'reference'){
                                                var parent_span = form_element.closest('span');
                                                parent_span.find('div:first').show();
                                            }else if(data_info.type == 'multipicklist'){
                                                form_element.select2('enable');
                                            }
                                        }

                                    }
                                    if(form_element.hasClass(condition_key+'-clf-hide')){
                                        form_element.removeClass(field_name_action+'-clf-hide');
                                        var this_td = form_element.closest('td');
                                        //this_td.find('div:first').show();
                                        this_td.show();
                                        this_td.children().show();
                                        this_td.prev().show();
                                        this_td.prev().children().show();
                                        if(form_element.hasClass('chzn-select') || form_element.hasClass('select2')) form_element.hide();
                                        // Handler for custom upload field
                                        if(this_td.find('#frm_'+field_name_action).length > 0) {
                                            form_element.hide();
                                        }
                                        var this_tr = this_td.closest('tr');
                                        thisInstance.hideTr(this_tr);
                                    }
                                    if(form_element.is('select')) form_element.trigger('liszt:updated');
                                });
                            }
                            condition_key++;
                        });
                    }
                }else{
                    // to do
                }
            }
        );
    },
    hideTr:function(this_tr){
        var count_td_hide = 1;
        this_tr.find('td').each (function() {
            if ( jQuery(this).children(":first").css('display') == 'none' || jQuery(this).children().length == 0){
                count_td_hide ++;
            }
        });
        if(count_td_hide >=5)  this_tr.hide();
        else  this_tr.show();
    },
    displayByClfOnDetail:function(moduleName,requestMode,record_id){
        var thisInstance = this;
        //to integrate with Custom View & Form
        if(moduleName == "CustomFormsViews"){
            var top_url = window.location.href.split('?');
            var array_url = thisInstance.getQueryParams(top_url[1]);
            moduleName = array_url.currentModule;
        }


        var params = {
            module : 'ControlLayoutFields',
            action : 'ActionAjax',
            mode : 'checkCLFForModule',
            current_module : moduleName,
            record_id:record_id
        };
        app.request.post({'data': params}).then(
            function(err,data){
                if(err === null) {
                    if (!jQuery.isEmptyObject(data)) {
                        var record_info = data.record_info;
                        var role_id = data.role_id;
                        //console.log(data.clf_info);
                        jQuery('<input>').attr({
                            type: 'hidden',
                            id: 'hd_clf_info',
                            value:JSON.stringify(data.clf_info)
                        }).appendTo(jQuery('#detailView'));
                        jQuery.each(data.clf_info,function(k,v) {
                            var all_condition = v.condition.all;
                            var any_condition = v.condition.any;
                            var actions = v.actions;
                            var condition_key = k;
                            var check_condition = thisInstance.checkConditionToForm(all_condition, any_condition, 'clf_details',record_info,role_id);
                            if (check_condition) {
                                jQuery.each(actions, function (index, value) {
                                    if (typeof requestMode != 'undefined' && requestMode == 'full') {
                                        var target_td = jQuery("#" + moduleName + "_detailView_fieldValue_" + value.field);
                                        var target_value = target_td.children('span:first').html();
                                        if (target_td.children('span:first').data('field-type') == 'url') {
                                            target_value = target_td.children('span:first').children('a').html();
                                        }
                                        if (!target_value) target_value = '';
                                        if (value.option == 'hide') {
                                            target_td.prev().html("");
                                            target_td.html("");
                                            var tr = target_td.closest('tr');
                                            var table = tr.closest('table');
                                            var empty_tr = true;
                                            var count_item = 0;
                                            var saved_td = [];
                                            jQuery.each(tr.find('td'), function () {
                                                count_item++;
                                                if (jQuery(this).html().trim() != '') {
                                                    empty_tr = false;
                                                    saved_td[count_item] = jQuery(this).html();
                                                }

                                            });
                                            if (empty_tr) tr.remove();
                                        }
                                        else if (value.option == 'read_only') {
                                            if(target_td.children('span:first').length >0) {
                                                target_td.html(target_value);
                                            }
                                        }
                                        else if (value.option == 'mandatory') {
                                            var target_element = jQuery('[data-name='+value.field+']');
                                            if(target_element.length > 0){
                                                target_element.attr('data-rule-required', 'true');
                                                if(target_element.is('select')) target_element.trigger('liszt:updated');
                                            }
                                        }
                                    }
                                    else {
                                        var target_td = jQuery("#" + moduleName + "_detailView_fieldValue_" + value.field);
                                        if(target_td.length == 0){
                                            //Summary view
                                            var target_element = jQuery('[data-name='+value.field+']');
                                            var target_span = target_element.closest('span');
                                            if (value.option == 'hide') {
                                                //target_span.closest('tr').remove();
                                                var parent_tr = target_span.closest('tr');
                                                if(parent_tr.find('td').length < 4){
                                                    target_span.closest('tr').remove();
                                                }
                                            }
                                            else if (value.option == 'read_only') {
                                                target_span.next().remove();
                                                target_span.remove();
                                            }
                                            else if (value.option == 'mandatory') {
                                                target_element.attr('data-rule-required', 'true');
                                            }
                                        }else{
                                            //Detail View
                                            var target_td = jQuery("#" + moduleName + "_detailView_fieldValue_" + value.field);
                                            var target_value = target_td.children('span:first').html();
                                            if (target_td.children('span:first').data('field-type') == 'url') {
                                                target_value = target_td.children('span:first').children('a').html();
                                            }
                                            if (!target_value) target_value = '';
                                            if (value.option == 'hide') {
                                                target_td.prev().html("");
                                                target_td.html("");
                                                var tr = target_td.closest('tr');
                                                var table = tr.closest('table');
                                                var empty_tr = true;
                                                var count_item = 0;
                                                var saved_td = [];
                                                jQuery.each(tr.find('td'), function () {
                                                    count_item++;
                                                    if (jQuery(this).html().trim() != '') {
                                                        empty_tr = false;
                                                        saved_td[count_item] = jQuery(this).html();
                                                    }

                                                });
                                                if (empty_tr) tr.remove();
                                            }
                                            else if (value.option == 'read_only') {
                                                if(target_td.children('span:first').length >0) {
                                                    target_td.html(target_value);
                                                }
                                            }
                                            else if (value.option == 'mandatory') {
                                                var target_element = $('[data-name='+value.field+']');
                                                target_element.attr('data-rule-required', 'true');
                                                if(target_element.is('select')) target_element.trigger('liszt:updated');
                                            }
                                        }
                                    }
                                });
                                //remove all empty block
                                jQuery.each(jQuery('#detailView').find('.detailview-table'), function () {
                                    var row_count = jQuery(this).find('tr').length;
                                    if (row_count == 0) jQuery(this).hide();
                                });

                                //return false;
                            }
                        });
                    }
                }else{
                }
            }
        );
    },
    checkCondition: function(form_element_value,comparator,field_value,field_name_changed,field_name){
        var thisInstace = this;
        switch(comparator) {
            case 'is':
                return (form_element_value == field_value);
                break;
            case 'is not':
                return (form_element_value != field_value);
                break;
            case 'contains':
                return ( form_element_value.indexOf(field_value) != -1 );
                break;
            case 'does not contain':
                return ( form_element_value.indexOf(field_value) == -1 );
                break;
            case 'starts with':
                return (form_element_value.startsWith(field_value));
                break;
            case 'ends with':
                return (form_element_value.endsWith(field_value));
                break;
            case 'is empty':
                return (form_element_value == '');
                break;
            case 'is not empty':
                return (form_element_value != '');
                break;
            case 'has changed':
                return (field_name_changed == field_name);
                break;
            case 'has changed to':
                return (form_element_value == field_value);
                break;
            case 'equal to':
                return (parseFloat(form_element_value) == parseFloat(field_value));
                break;
            case 'less than':
                return (parseFloat(form_element_value) < parseFloat(field_value));
                break;
            case 'greater than':
                return (parseFloat(form_element_value) > parseFloat(field_value));
                break;
            case 'does not equal':
                return (parseFloat(form_element_value) != parseFloat(field_value));
                break;
            case 'less than or equal to':
                return (parseFloat(form_element_value) <= parseFloat(field_value));
                break;
            case 'greater than or equal to':
                return (parseFloat(form_element_value) >= parseFloat(field_value));
                break;
            //date
            case 'between':
                var arr_date = field_value.split(",");
                return ((thisInstace.newDate(form_element_value) >= thisInstace.newDate(arr_date[0])) && (thisInstace.newDate(form_element_value) <= thisInstace.newDate(arr_date[1])));
                break;
            case 'before':
                return (thisInstace.newDate(form_element_value) < thisInstace.newDate(field_value));
                break;
            case 'after':
                return (thisInstace.newDate(form_element_value) > thisInstace.newDate(field_value));
                break;
            case 'is today':
                return (thisInstace.newDate(form_element_value) == thisInstace.newDate());
                break;
            case 'less than days ago':
                var num_day = parseInt(field_value);
                var date_inputed = thisInstace.newDate(form_element_value);
                var today = thisInstace.newDate();
                var date_check = thisInstace.newDate(today.getFullYear(), today.getMonth(), today.getDate() - num_day);
                return (date_inputed >= date_check);
                break;
            case 'more than days ago':
                var num_day = parseInt(field_value);
                var date_inputed = thisInstace.newDate(form_element_value);
                var today = thisInstace.newDate();
                var date_check = thisInstace.newDate(today.getFullYear(), today.getMonth(), today.getDate() + num_day);
                return (date_inputed >=date_check);
                break;
            case 'days ago':
                var num_day = parseInt(field_value);
                var date_inputed = thisInstace.newDate(form_element_value);
                var today = thisInstace.newDate();
                var date_check = thisInstace.newDate(today.getFullYear(), today.getMonth(), today.getDate() - num_day);
                return (date_inputed >date_check);
                break;
            case 'days later':
                var num_day = parseInt(field_value);
                var date_inputed = thisInstace.newDate(form_element_value);
                var today = thisInstace.newDate();
                var date_check = thisInstace.newDate(today.getFullYear(), today.getMonth(), today.getDate() + num_day);
                return (date_inputed >date_check);
                break;
            case 'in less than':
                return (thisInstace.newDate(form_element_value) <= thisInstace.newDate(field_value));
                break;
            case 'in more than':
                return (thisInstace.newDate(form_element_value) >= thisInstace.newDate(field_value));
                break;

        }
    },
    newDate:function(_date){
        var _format = "mm-dd-yyyy";
        var _delimiter = "-";
        var formatLowerCase=_format.toLowerCase();
        var formatItems=formatLowerCase.split(_delimiter);
        var dateItems=_date.split(_delimiter);
        var monthIndex=formatItems.indexOf("mm");
        var dayIndex=formatItems.indexOf("dd");
        var yearIndex=formatItems.indexOf("yyyy");
        var month=parseInt(dateItems[monthIndex]);
        month-=1;
        return new Date(dateItems[yearIndex],month,dateItems[dayIndex]);
    },
    //this function to check condition from config to dispay control on form
    checkConditionToForm:function(all_condition,any_condition,field_name_changed,record_info,role_id){
        var thisInstance = this;
        var is_all = true;
        var is_any = false;
        //if(all_condition.length == 0) is_all = false;
        if(all_condition.length == 0 && any_condition.length > 0 ) is_all = false;
        jQuery.each(all_condition,function(key,value){
            var field_name = value.columnname;
            var field_value =  value.value;
            var comparator =  value.comparator;
            var main_form = jQuery('#EditView');
            var form_element = main_form.find('[name="'+field_name+'"]');
            if(field_name_changed == 'clf_details'){
                main_form = jQuery('#detailView');
                form_element = main_form.find('[data-name="'+field_name+'"]');
            }
            if(typeof form_element == 'undefined' && field_name == 'total'){
                form_element = jQuery('#EditView').find('[name="grandTotal"]');
            }
            if(!form_element.length){
                form_element = jQuery('[data-name="' +field_name+ '"]');
                form_element.val(form_element.attr('data-value'));
            }
            if(!form_element.length){
                return;
            }
            var form_element_value = form_element.val();
            if(field_name_changed == 'clf_details') {
                form_element_value = form_element.data("value");
            }
            if(typeof form_element_value == 'undefined' && field_name_changed == 'clf_details'){
                //var record_info = thisInstance.getRecordIdAndModule();
                if(typeof thisInstance.fieldValuesCache[field_name] == 'undefined') {
                    form_element_value = record_info[field_name];
                    thisInstance.fieldValuesCache[field_name] = form_element_value;
                }else{
                    form_element_value = thisInstance.fieldValuesCache[field_name];
                }
            }
            //for Multiple Value control
            if(form_element.attr('type') == 'hidden'){
                form_element = form_element.next();
                if(!form_element.is('input')){
                    form_element = form_element.next('select');
                    if(form_element.val()) form_element_value = form_element.val().join(',');
                }
                else{
                    if(form_element.attr('type') == 'checkbox'){
                        if (form_element.is(":checked"))
                        {
                            form_element_value = 1;
                        }
                        else{
                            form_element_value = 0;
                        }

                    }
                }
            }
            if(field_name == "roleid"){
                form_element_value = role_id;
            }
            var result = thisInstance.checkCondition(form_element_value,comparator,field_value,field_name_changed,field_name);
            if(!result){
                is_all = false;
                return false;
            }
        });
        jQuery.each(any_condition,function(key,value){
            var field_name = value.columnname;
            var field_value =  value.value;
            var comparator =  value.comparator;
            var form_element = jQuery('#EditView').find('[name="'+field_name+'"]');
            if(field_name_changed == 'clf_details'){
                form_element = jQuery('#detailView').find('[name="'+field_name+'"]');
            }
            if(typeof form_element == 'undefined' && field_name == 'total'){
                form_element = jQuery('#detailView').find('[name="grandTotal"]');
            }
            if(!form_element.length){
                form_element = jQuery('[data-name="' +field_name+ '"]');
                form_element.val(form_element.attr('data-value'));
            }
            if(!form_element.length){
                //return; //687370 - need to get value from record_info
            }
            var form_element_value = form_element.val();
            if(typeof form_element_value == 'undefined' && field_name_changed == 'clf_details'){
                var record_info = thisInstance.getRecordIdAndModule();
                if(typeof thisInstance.fieldValuesCache[field_name] == 'undefined') {
                    //form_element_value = thisInstance.getFieldValue(field_name,record_info[0],record_info[1]);
                    jQuery.each(record_info,function(key,value){
                        if(key == field_name) form_element_value = value;
                    });
                    thisInstance.fieldValuesCache[field_name] = form_element_value;
                }else{
                    form_element_value = thisInstance.fieldValuesCache[field_name];
                }
            }
            //for Multiple Value control
            if(form_element.attr('type') == 'hidden'){
                form_element = form_element.next();
                if(!form_element.is('input')){
                    form_element = form_element.next('select');
                    if(form_element.val()) form_element_value = form_element.val().join(',');
                }
                else{
                    if(form_element.attr('type') == 'checkbox'){
                        if (form_element.is(":checked"))
                        {
                            form_element_value = 1;
                        }
                        else{
                            form_element_value = 0;
                        }

                    }
                }
            }
            var result = thisInstance.checkCondition(form_element_value,comparator,field_value,field_name_changed,field_name);
            if(result){
                is_any = true;

            }
        });
        return is_all || is_any;
    },
    registerFormChange:function(module){
        var thisInstance = this;
        jQuery("#EditView").on("change","input,select,textarea", function () {
            var field_name = jQuery(this).attr('name');
            thisInstance.displayByClf(module,field_name);
        });
    },
    getRecordIdAndModule: function(){
        var return_arr = [];
        var url = window.location.href.split('?');
        var array_url = this.getQueryParams(url[1]);
        return_arr.push(array_url.module);
        return_arr.push(array_url.record);
        return return_arr;
    },
    getQueryParams:function(qs) {
        if(typeof(qs) != 'undefined' ){
            qs = qs.toString().split('+').join(' ');
            var params = {},
                tokens,
                re = /[?&]?([^=]+)=([^&]*)/g;
            while (tokens = re.exec(qs)) {
                params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
            }
            return params;
        }
    },

    /*
     * Function to register the list view row click event
     */
    registerRowClickEvent: function(){
        var listViewContentDiv = jQuery('.listViewEntriesTable');
        listViewContentDiv.on('click','.listViewEntryValue',function(e){
            var editUrl = jQuery(this).closest('tr').data('recordurl');
            window.location.href = editUrl;
        });
    },
    /*
     * Function to register inlineAjaxSave click event
     */
    registerInlineAjaxSaveClickEvent: function(){
        var listViewContentDiv = jQuery('.fieldValue ');
        listViewContentDiv.on('click','.inlineAjaxSave',function(e){
            var rule_required_field = jQuery(this).closest('td').find('input.fieldBasicData').data('rule-required');
            var field_id = jQuery(this).closest('td').find('input.fieldBasicData').data('name');
            var this_field = jQuery(this).closest('td').find('[name="'+field_id+'"]');
            if(rule_required_field){
                if(this_field.val()==''){
                    this_field.attr('aria-required','true');
                    this_field.attr('aria-invalid','true');
                    this_field.addClass('input-error');
                    var msg = app.vtranslate('JS_REQUIRED_FIELD');
                    var params = {};
                    params.position = {
                        my: 'bottom left',
                        at: 'top left',
                        container:jQuery("#detailView")
                    };
                    vtUtils.showValidationMessage(this_field,msg,params);
                    return false;
                }
                else{
                    vtUtils.hideValidationMessage(this_field);
                    return true;
                }
            }
            else{
                return true;
            }
        });
        listViewContentDiv.on('click','.inlineAjaxSave',function(e){

        });
    },
    registerEvents : function(){
        this.registerModuleFilterChange();
        this.registerPagingAction();
        this.registerDeleteAction();
        this.registerEventClickOnRow();
    }

});
jQuery(document).ready(function(){
    // Only load when loadHeaderScript=1 BEGIN #241208
    var vtetabdonotworking = false; 
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('ControlLayoutFields')) {
            return;
        }
	if (!VTECheckLoadHeaderScript('VTETabs')) {
             vtetabdonotworking = true;        
	}
    }
    // Only load when loadHeaderScript=1 END #241208

    var clfInstance = new Control_Layout_Fields_Js();
    clfInstance.registerEvents();
    var view = app.view();
    var module = app.getModuleName();
    var vtetabs = jQuery("script[src*='VTETabs.js']");
    if(view == 'Edit' && vtetabs.length == 0){
        clfInstance.displayByClf(module,false);
        clfInstance.registerFormChange(module);
    }
    else if(vtetabs.length == 1){
		console.log("vtetabdonotworking:" +vtetabdonotworking);
		if(vtetabdonotworking){
			clfInstance.displayByClf(module,false);
        		clfInstance.registerFormChange(module);
	
		}
	}    	
    if(view == 'Detail' && vtetabs.length == 0){
        var url = window.location.href.split('?');
        var array_url = clfInstance.getQueryParams(url[1]);
        if(typeof array_url == 'undefined') return false;
        var request_mode = array_url.requestMode;
        var record_id = jQuery('#recordId').val();
        clfInstance.displayByClfOnDetail(module,request_mode,record_id);
        clfInstance.registerInlineAjaxSaveClickEvent();
    }
});
// Listen post ajax event for add product action
jQuery( document ).ajaxComplete(function(event, xhr, settings) {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('ControlLayoutFields')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208
    
    var url = settings.data;
    if(typeof url == 'undefined' && settings.url) url = settings.url;
    var instance = new Control_Layout_Fields_Js();
    var top_url = window.location.href.split('?');
    var array_url = instance.getQueryParams(top_url[1]);
    if(typeof array_url == 'undefined') return false;
    var other_url = instance.getQueryParams(url);
    var request_mode = array_url.requestMode;
    if(array_url.view == 'Detail' && other_url.action == 'SaveAjax'){
        var modified_field = other_url.field;
        var is_on_condition = false;
        var hd_clf_info = jQuery('#hd_clf_info').val();
        if(hd_clf_info){
            var list_condition = jQuery.parseJSON(hd_clf_info);
            jQuery.each(list_condition,function(key,value){
                var condition_all = value.condition.all;
                var condition_any = value.condition.any;
                jQuery.each(condition_all,function(index,val){
                    if(val.columnname == modified_field){
                        is_on_condition = true;
                        return false;
                    }
                });
                jQuery.each(condition_any,function(index,val){
                    if(val.columnname == modified_field){
                        is_on_condition = true;
                        return false;
                    }
                });
            });
        }
        if(is_on_condition){
            var link_active = jQuery('ul.nav').find('li.active');
            //link_active.trigger('click');
        }
    }
    else if(array_url.view == 'Detail'&& other_url.mode == 'showDetailViewByMode'){
        var record_id = jQuery('#recordId').val();
        instance.displayByClfOnDetail(array_url.module,request_mode,record_id);
        instance.registerInlineAjaxSaveClickEvent();
    }
    else{
        //Working with VTETabs	
        if(other_url.module == 'VTETabs' && other_url.view == 'Edit' && other_url.mode == 'showModuleEditView') {
            instance.displayByClf(array_url.module,false);
            instance.registerFormChange(array_url.module);
        }
        if(other_url.module == 'VTETabs' && other_url.view == 'DetailViewAjax' && other_url.mode == 'showModuleDetailView') {
            var record_id = jQuery('#recordId').val();
            instance.displayByClfOnDetail(array_url.module,request_mode,record_id);
            instance.registerInlineAjaxSaveClickEvent();
        }
    }
});
