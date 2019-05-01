/*+***********************************************************************************
 * The content of this file is subject to the VTE Duplicate Check & Merge ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger.Class("VTE_DuplicateCheckMerge_Js",{

    options : null,
    init: function(){
        if(!this.options){
            var current_url = jQuery.url();
            this.options = {
                current_module: current_url.param('module'),
                current_view: current_url.param('view'),
                current_record: current_url.param('record'),
                dcm_setting_array: jQuery.parseJSON(dcm_setting),
                dcm_setting_array_values: new Array()
            };
        }
    },

    /**
     * Function to get fields setting
     */
    getFieldInfoFromSetting : function(fieldname, current_module){
        if(!current_module){
            current_module = this.options.current_module;
        }
        var moduleFields = this.options.dcm_setting_array[current_module];
        if(typeof(moduleFields) != 'undefined') {
            var len = moduleFields.length;
            for(var i=0; i<len; i++){
                if( moduleFields[i].fieldname==fieldname){
                    return i;
                }
            }
        }
        return false;
    },

    validDetaiViewLoaded : function(){
        if(!current_module || !current_record || !current_view){
            var current_url = jQuery.url();
            var current_module = current_url.param('module');
            var current_view = current_url.param('view');
            var current_record = current_url.param('record');
        }
        var dcm_setting_array = jQuery.parseJSON(dcm_setting);
        if(typeof dcm_setting_array[current_module] == 'undefined'){
            return;
        }
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            data: {
                module: "DuplicateCheckMerge",
                action: "ValidDetailViewLoaded",
                pmodule: current_module,
                pview: current_view,
                record: current_record
            },
            success:function (response) {
                if(response.result.data.length > 0){
                    var data = response.result.data;
                    var len = data.length;
                    jQuery('[class^="dcm_items_"],[id^="dcm_items_"]').remove();
                    for(var i=0; i<len; i++ ){
                        if(data[i].passive == 1){
                            jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span').find('.dcm_items_refresh').remove();
                            jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span').prepend('<img class="dcm_items_refresh" data-fieldname="'+data[i].fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/refresh.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                            jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' span').prepend('<img class="dcm_items_refresh" data-fieldname="'+data[i].fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/refresh.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                        }else if(data[i].passive == 0 && data[i].rtotr == 1){
                            if(data[i].duplicate_items.length > 0 || data[i].crosscheck.length > 0){
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span').prepend('<img class="dcm_items_valid" data-fieldname="'+data[i].fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' span').prepend('<img class="dcm_items_valid" data-fieldname="'+data[i].fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');

                                var html_buildin = '';
                                if(data[i].duplicate_items.length > 0){
                                    // add duplicate items
                                    html_buildin = app.vtranslate(data[i].fieldlabel + ' ' + response.result.message + '.<br />');
                                    html_buildin += '<ul style="max-height: 200px; overflow-y: auto;">';
                                    for(var j = 0; j < data[i].duplicate_items.length; j++){
                                        html_buildin += '<li>';
                                        html_buildin +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data[i].duplicate_items[j].id+'&mode=showDetailViewByMode&requestMode=full">';
                                        html_buildin +=         data[i].duplicate_items[j].entityName;
                                        html_buildin +=     '</a>';
                                        html_buildin += '</li>';
                                    }
                                    html_buildin += '</ul>';
                                    if(data[i].duplicate_merge==1){
                                        html_buildin += '<a style="color:#7cfc00;" target="_blank" href="index.php?module=DuplicateCheckMerge&pmodule='+current_module+'&view=FindDuplicates&fields[]='+data[i].fieldname+'&ignoreEmpty=on&value_dupes['+data[i].fieldname+']='+data[i].fieldvalue+'">Merge Records</a>';
                                    }
                                }
                                if(data[i].duplicate_items.length > 0 && data[i].crosscheck.length > 0){
                                    html_buildin += '<hr />';
                                }

                                if(data[i].crosscheck.length > 0){
                                    html_buildin += 'Cross Check for: ' + data[i].fieldlabel + '<br />';
                                    html_buildin += '<ul style="max-height: 200px; overflow-y: auto;">';
                                    for(var k = 0; k<data[i].crosscheck.length; k++){
                                        html_buildin += '<li>';
                                        html_buildin +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+data[i].crosscheck[k].module+'&view=Detail&record='+data[i].crosscheck[k].id+'&mode=showDetailViewByMode&requestMode=full">';
                                        html_buildin +=         data[i].crosscheck[k].entityName + ' (<b>'+data[i].crosscheck[k].alias+'</b>)';
                                        html_buildin +=     '</a>';
                                        html_buildin += '</li>';
                                    }
                                    html_buildin += '</ul>';
                                }

                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname).prepend('<div style="display:none;" id="dcm_items_'+data[i].fieldname+'">'+html_buildin+'</div>');
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname).prepend('<div style="display:none;" id="dcm_items_'+data[i].fieldname+'">'+html_buildin+'</div>');
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span img.dcm_items_not_valid').remove();
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' span img.dcm_items_not_valid').remove();
                            }else{
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span').prepend('<img class="dcm_items_not_valid" src="layouts/v7/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' span').prepend('<img class="dcm_items_not_valid" src="layouts/v7/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                                jQuery('#dcm_items_'+data[i].fieldname).remove();
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span img.dcm_items_valid').remove();
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' span img.dcm_items_valid').remove();
                            }
                        }
                    }
                }
            }
        });
    },

    handleFields : function(field_setting, fieldvalue, isReference){
        var VTE_Instance = this
        var current_url = jQuery.url();
        var current_module = current_url.param('module');
        var current_view = current_url.param('view');
        var current_record = current_url.param('record');
        if(field_setting.rtayt == 0 && field_setting.ad == 1){
            return;
        }
        jQuery.ajax({
            type: "POST",
            async:false,
            url: "index.php",
            data: {
                module: "DuplicateCheckMerge",
                action: "ValidField",
                pmodule: current_module,
                pview: current_view,
                fieldname: field_setting.fieldname,
                fieldvalue: fieldvalue,
                record: current_record
            },
            success:function (response) {
                if(response.result.duplicate){
                    var data = response.result.data;
                    var html = '';
                    if(data.entities.length > 0){
                        html += data.fieldlabel + ' ' + response.result.message + '.<br />';
                        html += '<ul style="max-height: 200px; overflow-y: auto;">';
                        for(var i = 0; i<data.entities.length; i++){
                            html += '<li>';
                            html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                            html +=         data.entities[i].entityName;
                            html +=     '</a>';
                            html += '</li>';
                        }
                        html += '</ul>';
                        if(data.duplicate_merge==1 && data.entities.length > 1){
                            html += '<a style="color:#7cfc00;" target="_blank" href="index.php?module=DuplicateCheckMerge&pmodule='+current_module+'&view=FindDuplicates&fields[]='+data.fieldname+'&ignoreEmpty=on&value_dupes['+data.fieldname+']='+fieldvalue+'">Merge Records</a>';
                        }
                    }
                    if(data.entities.length > 0 && data.crosscheck.length > 0){
                        html += '<hr />';
                    }
                    if(data.crosscheck.length > 0){
                        html += 'Cross Check for: ' + data.fieldlabel + '<br />';
                        html += '<ul style="max-height: 200px; overflow-y: auto;">';
                        for(var i = 0; i<data.crosscheck.length; i++){
                            html += '<li>';
                            html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                            html +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                            html +=     '</a>';
                            html += '</li>';
                        }
                        html += '</ul>';
                    }
                    //console.log(current_module+'_editView_fieldName_'+field_setting.fieldname);
                    // jQuery('#'+current_module+'_editView_fieldName_'+field_setting.fieldname).validationEngine('showPrompt', html , 'error','bottomLeft',true);
                    if(VTE_Instance.options.dcm_setting_array_values.indexOf(data.fieldname) === -1) VTE_Instance.options.dcm_setting_array_values.push(data.fieldname);
                    var displayField = '';
                    if(isReference == true) {
                        displayField = field_setting.fieldname + '_display';
                    }
                    VTE_Instance.showDuplicateItems(jQuery('#'+current_module+'_editView_fieldName_'+field_setting.fieldname),html,displayField);
                }
                else {
                    VTE_Instance.options.dcm_setting_array_values = jQuery.grep(VTE_Instance.options.dcm_setting_array_values, function(value) {
                        return value !=  field_setting.fieldname;
                    });
                }
            }
     });
},

handleFieldsInQuickCreate : function(field_setting, fieldvalue){
    var VTE_Instance = this;
    var current_module = jQuery('form[name=QuickCreate] input[name=module]').val();
    var current_view = 'Edit';
    var current_record = '';
    if(field_setting.rtayt == 0 && field_setting.ad == 1){
        return;
    }

    jQuery.ajax({
        type: "POST",
        async: false,
        url: "index.php",
        data: {
            module: "DuplicateCheckMerge",
            action: "ValidField",
            pmodule: current_module,
            pview: current_view,
            fieldname: field_setting.fieldname,
            fieldvalue: fieldvalue,
            record: current_record
        },
        success:function (response) {
            jQuery('.mCSB_inside,.mCSB_container').css('position','initial');
            if(response.result.duplicate){
                var data = response.result.data;
                var html = '';
                if(data.entities.length > 0){
                    html += data.fieldlabel + ' ' + response.result.message + '.<br />';
                    html += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.entities.length; i++){
                        html += '<li>';
                        html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html +=         data.entities[i].entityName;
                        html +=     '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }
                //console.log(current_module+'_editView_fieldName_'+field_setting.fieldname);
                if(data.entities.length > 0 && data.crosscheck.length > 0){
                    html += '<hr />';
                }
                if(data.crosscheck.length > 0){
                    html += 'Cross Check for: ' + data.fieldlabel + '<br />';
                    html += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.crosscheck.length; i++){
                        html += '<li>';
                        html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                        html +=     '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }
                if(VTE_Instance.options.dcm_setting_array_values.indexOf(data.fieldname) === -1) VTE_Instance.options.dcm_setting_array_values.push(data.fieldname);
                VTE_Instance.showDuplicateItems(jQuery('form[name= QuickCreate] #'+current_module+'_editView_fieldName_'+field_setting.fieldname),html);
            }
            else{
                VTE_Instance.options.dcm_setting_array_values = jQuery.grep(VTE_Instance.options.dcm_setting_array_values, function(value) {
                    return value !=  field_setting.fieldname;
                });
            }
        }
    });
},

handleFieldsDetailView : function(field_setting, fieldvalue){
    var current_module = this.options.current_module;
    var current_view = this.options.current_view;
    var current_record = this.options.current_record;
    var dcm_setting_array = this.options.dcm_setting_array;
    this.removeIcon(current_module, field_setting.fieldname)
    jQuery.ajax({
        type: "POST",
        async:false,
        url: "index.php",
        data: {
            module: "DuplicateCheckMerge",
            action: "ValidField",
            pmodule: current_module,
            pview: current_view,
            fieldname: field_setting.fieldname,
            fieldvalue: fieldvalue,
            record: current_record
        },
        success:function (response) {
            jQuery('[class^="dcm_items_"],[id^="dcm_items_"]').remove();
            if(response.result.duplicate){
                var data = response.result.data;
                var html = '';
                var title = '';
                if(data.ad == "0" || data.do_not_allow) {
                    var fieldid = current_module+'_detailView_fieldValue_'+field_setting.fieldname;
                    var this_field = jQuery("input[name='"+field_setting.fieldname+"']");
                    var currentTdElement = jQuery("#"+fieldid);
                    var this_field_name = this_field.data('label');
                    var oldValue = jQuery('input[data-name="'+field_setting.fieldname+'"]').data('value');
                    jQuery(this_field).val(oldValue);
                    jQuery('.inlineAjaxCancel',currentTdElement).trigger('click');
                    var html = '';
                    html += '<div>';
                    html +=     '<table><tr>';

                    html +=     '<td>' + this_field_name + '</td><td> ('+fieldvalue+')</td>';
                    html +=     '</tr></table>';
                    html += '</div>'
                    var title = app.vtranslate('Duplicate records are not allowed:');
                    var params = {
                        title: title,
                        icon : 'fa fa-exclamation-triangle',
                        message: app.vtranslate(html)

                    };
                    jQuery.notify(params);
                    return false;
                }
                //html += data.fieldlabel + ' ' + response.result.message + '.<br />';
                if(data.entities.length > 0){
                    title =data.fieldlabel + ' ' + response.result.message;
                    html += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.entities.length; i++){
                        html += '<li style="list-style-type: none;font-size: 11px;">';
                        html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html +=         data.entities[i].entityName;
                        html +=     '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }
                if(data.entities.length > 0 && data.crosscheck.length > 0){
                    html += '<hr />';
                }
                if(data.crosscheck.length > 0){
                    title = 'Cross Check for: ' + data.fieldlabel;
                    html += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.crosscheck.length; i++){
                        html += '<li style="list-style-type: none;font-size: 11px;">';
                        html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                        html +=     '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }

                var params = {
                    title: title,
                    icon : 'fa fa-exclamation-triangle',
                    message: app.vtranslate(html)
                };
                jQuery.notify(params,{mouse_over:'pause'});

                jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname+' span').prepend('<img class="dcm_items_valid" data-fieldname="'+field_setting.fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                jQuery('#_detailView_fieldLabel_'+field_setting.fieldname+' span').prepend('<img class="dcm_items_valid" data-fieldname="'+field_setting.fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;cursor: pointer;">');
                // add duplicate items
                var html_buildin = '';
                if(data.entities.length > 0){
                    html_buildin = app.vtranslate(data.fieldlabel + ' ' + response.result.message + '.<br />');
                    html_buildin += '<ul>';
                    for(var i = 0; i<data.entities.length; i++){
                        html_buildin += '<li>';
                        html_buildin +=     '<a style="color: #7cfc00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html_buildin +=         data.entities[i].entityName;
                        html_buildin +=     '</a>';
                        html_buildin += '</li>';
                    }
                    html_buildin += '</ul>';
                    if(data.duplicate_merge==1){
                        html_buildin += '<a style="color:#7cfc00;" target="_blank" href="index.php?module=DuplicateCheckMerge&pmodule='+current_module+'&view=FindDuplicates&fields[]='+field_setting.fieldname+'&fieldvalue='+fieldvalue+'&ignoreEmpty=on&value_dupes['+data.fieldname+']='+fieldvalue+'">Merge Records</a>';
                    }
                }
                if(data.entities.length > 0 && data.crosscheck.length > 0){
                    html_buildin += '<hr />';
                }
                if(data.crosscheck.length > 0){
                    html_buildin += 'Cross Check for: ' + data.fieldlabel + '<br />';
                    html_buildin += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.crosscheck.length; i++){
                        html_buildin += '<li>';
                        html_buildin +=     '<a style="color: #7cfc00" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html_buildin +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                        html_buildin +=     '</a>';
                        html_buildin += '</li>';
                    }
                    html_buildin += '</ul>';
                }

                jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname).prepend('<div style="display:none;" id="dcm_items_'+field_setting.fieldname+'">'+html_buildin+'</div>');
                jQuery('#_detailView_fieldLabel_'+field_setting.fieldname).prepend('<div style="display:none;" id="dcm_items_'+field_setting.fieldname+'">'+html_buildin+'</div>');

            }else{
                jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname+' span').prepend('<img class="dcm_items_not_valid" src="layouts/v7/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                jQuery('#_detailView_fieldLabel_'+field_setting.fieldname+' span').prepend('<img class="dcm_items_not_valid" src="layouts/v7/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                jQuery('#dcm_items_'+field_setting.fieldname).remove();
                jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname+' span img.dcm_items_valid').remove();
                jQuery('#_detailView_fieldLabel_'+field_setting.fieldname+' span img.dcm_items_valid').remove();
            }
        }
    });
},

showDuplicateItems : function(obj,html, isReference){
    var template = '<div class="popover" role="tooltip" style="background: red">' +
        '<style>' +
        '.popover.bottom>.arrow:after{border-bottom-color:red;2px solid #ddd}' +
        '.popover-content{font-size: 11px}' +
        '.popover-content ul{padding: 5px 5px 0 10px}' +
        '.popover-content li{list-style-type: none}' +
        '.popover{border: 2px solid #ddd;color: #fff;box-shadow: 0 0 6px #000; -moz-box-shadow: 0 0 6px #000;-webkit-box-shadow: 0 0 6px #000; -o-box-shadow: 0 0 6px #000;padding: 4px 10px 4px 10px;border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; -o-border-radius: 6px;}' +
        '</style><div class="arrow">' +
        '</div>' +
        '<h3 class="popover-title"></h3><div class="popover-content"></div></div>';
    if(isReference != '') {
        jQuery('#'+isReference).popover({
            content: html,
            placement: 'bottom',
            html: true,
            template:template,
            trigger: 'onKeyPress',
        });
        jQuery('#'+isReference).popover('show');
    }else {
        jQuery(obj).popover({
            content: html,
            placement: 'bottom',
            html: true,
            template:template,
            trigger: 'onKeyPress',
        });
        jQuery(obj).popover('show');
    }
},

checkDuplicateItem : function(obj, fieldname){
    var current_url = jQuery.url();
    var current_module = current_url.param('module');
    var current_view = current_url.param('view');
    var current_record = current_url.param('record');
    this.removeIcon(current_module, fieldname);
    jQuery.ajax({
        type: "POST",
        url: "index.php",
        data: {
            module: "DuplicateCheckMerge",
            action: "ValidField",
            pmodule: current_module,
            pview: current_view,
            type: "RefreshValidField",
            fieldname: fieldname,
            fieldvalue: jQuery('[data-name="'+fieldname+'"]').data('value'),
            record: current_record
        },
        success:function (response) {
            if(response.result.duplicate){
                var data = response.result.data;
                var html = '';
                var title = '';
                //html += data.fieldlabel + ' ' + response.result.message + '.<br />';
                if(data.entities.length > 0){
                    title += data.fieldlabel + ' ' + response.result.message;
                    html += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.entities.length; i++){
                        html += '<li style="list-style-type: none;font-size: 11px;">';
                        html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html +=         data.entities[i].entityName;
                        html +=     '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }
                if(data.entities.length > 0 && data.crosscheck.length > 0){
                    html += '<hr />';
                }
                if(data.crosscheck.length > 0){
                    title += 'Cross Check for: ' + data.fieldlabel;
                    html += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.crosscheck.length; i++){
                        html += '<li style="list-style-type: none;font-size: 11px;">';
                        html +=     '<a style="color: #ffff00" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                        html +=     '</a>';
                        html += '</li>';
                    }
                    html += '</ul>';
                }

                var params = {
                    title: title,
                    icon : 'fa fa-exclamation-triangle',
                    message: app.vtranslate(html)
                };
                // app.helper.showAlertNotification(params,{delay: 5000});
                jQuery.notify(params,{mouse_over:'pause'});
                jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' span').prepend('<img class="dcm_items_valid" data-fieldname="'+fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;cursor: pointer;">');
                jQuery('#_detailView_fieldLabel_'+fieldname+' span').prepend('<img class="dcm_items_valid" data-fieldname="'+fieldname+'" src="layouts/v7/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;cursor: pointer;">');
                // add duplicate items
                var html_buildin = '';
                if(data.entities.length > 0){
                    html_buildin = data.fieldlabel + ' ' + response.result.message + '.<br />';
                    html_buildin += '<ul>';
                    for(var i = 0; i<data.entities.length; i++){
                        html_buildin += '<li>';
                        html_buildin +=     '<a style="color: #7cfc00" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html_buildin +=         data.entities[i].entityName;
                        html_buildin +=     '</a>';
                        html_buildin += '</li>';
                    }
                    html_buildin += '</ul>';
                    if(data.duplicate_merge==1){
                        console.log(jQuery('[data-name="'+fieldname+'"]'));
                        html_buildin += '<a style="color:#7cfc00;" target="_blank" href="index.php?module=DuplicateCheckMerge&pmodule='+current_module+'&view=FindDuplicates&fields[]='+fieldname+'&value_dupes['+fieldname+']='+jQuery('[data-name="'+fieldname+'"]').data('value')+'&ignoreEmpty=on">Merge Records</a>';
                    }
                }
                if(data.crosscheck.length > 0 && data.entities.length > 0){
                    html_buildin += '<hr />';
                }
                if(data.crosscheck.length > 0){
                    html_buildin += 'Cross Check for: ' + data.fieldlabel + '<br />';
                    html_buildin += '<ul style="max-height: 200px; overflow-y: auto;">';
                    for(var i = 0; i<data.crosscheck.length; i++){
                        html_buildin += '<li>';
                        html_buildin +=     '<a style="color: #7cfc00" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                        html_buildin +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                        html_buildin +=     '</a>';
                        html_buildin += '</li>';
                    }
                    html_buildin += '</ul>';
                }

                jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname).prepend('<div style="display:none;" id="dcm_items_'+fieldname+'">'+html_buildin+'</div>');
                jQuery('#_detailView_fieldLabel_'+fieldname).prepend('<div style="display:none;" id="dcm_items_'+fieldname+'">'+html_buildin+'</div>');

            }else{
                jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' span').prepend('<img class="dcm_items_not_valid" src="layouts/v7/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;cursor: pointer;">');
                jQuery('#_detailView_fieldLabel_'+fieldname+' span').prepend('<img class="dcm_items_not_valid" src="layouts/v7/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;cursor: pointer;">');
            }
        }
    });
},

removeIcon : function(current_module, fieldname){
    jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' span img.dcm_items_not_valid').remove();
    jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' span img.dcm_items_refresh').remove();
    jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' span img.dcm_items_valid').remove();
    jQuery('#_detailView_fieldLabel_'+fieldname+' span img.dcm_items_not_valid').remove();
    jQuery('#_detailView_fieldLabel_'+fieldname+' span img.dcm_items_refresh').remove();
    jQuery('#_detailView_fieldLabel_'+fieldname+' span img.dcm_items_valid').remove();
},

registerDetailViewLoaded : function(){
    this.validDetaiViewLoaded();
},

registerFieldChangeOnEditView : function(current_module){
    var VTE_Instance = this;
    jQuery(document).on('change', '#EditView input', function(event) {
        var dataFieldType = jQuery(this).data('fieldtype');
        var fieldname = jQuery(this).attr('name');
        if(dataFieldType == 'reference'){
            return;
        }
        jQuery(this).popover('destroy');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
        }
    });
    jQuery(document).on(Vtiger_Edit_Js.postReferenceSelectionEvent, '#EditView input', function(event) {
        var fieldname = jQuery(this).attr('name');
         var isReference = true;
        jQuery(this).popover('destroy');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value, isReference);
        }
    });
    jQuery(document).on(Vtiger_Edit_Js.referenceDeSelectionEvent, '#EditView .clearReferenceSelection', function(event) {
        var inputField = jQuery(this).closest('div').find('.sourceField');
        var displayField = inputField.attr('name') + '_display';
        $('#' + displayField).popover('destroy');
    });

    jQuery(document).on('change', '#EditView select', function(event) {
        jQuery(this).popover('destroy');
        var fieldname = jQuery(this).attr('name');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
        }
    });

    jQuery(document).on('change', '#EditView textarea', function(event) {
        jQuery(this).popover('destroy');
        var fieldname = jQuery(this).attr('name');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
        }
    });
},

registerFieldChangeOnQuickCreate : function(current_module){
    var VTE_Instance = this;
    jQuery(document).off('change', 'form[name=QuickCreate] input').on('change', 'form[name=QuickCreate] input', function(event) {
        jQuery(this).popover('destroy');
        var current_module = jQuery('form[name=QuickCreate] input[name=module]').val();
        var fieldname = jQuery(this).attr('name');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname, current_module);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFieldsInQuickCreate(VTE_Instance.options.dcm_setting_array[current_module][key], this.value);
        }
    });
    jQuery(document).off('change', 'form[name=QuickCreate] select').on('change', 'form[name=QuickCreate] select', function(event) {
        jQuery(this).popover('destroy');
        var current_module = jQuery('form[name=QuickCreate] input[name=module]').val();
        var fieldname = jQuery(this).attr('name');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname, current_module);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFieldsInQuickCreate(VTE_Instance.options.dcm_setting_array[current_module][key], this.value);
        }
    });
},

registerFieldChangeOnDetail : function(){
    var VTE_Instance = this;
    jQuery(document).off('change','#detailView input').on('change', '#detailView input', function(event) {
        jQuery(this).popover('destroy');
        var fieldname = jQuery(this).attr('name');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFieldsDetailView(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
        }
    });

    jQuery(document).off('change','#detailView select').on('change', '#detailView select', function(event) {
        jQuery(this).popover('destroy');
        var fieldname = jQuery(this).attr('name');
        var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
        if(this.value != '' && key !== false){
            VTE_Instance.handleFieldsDetailView(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
        }
    });
},

registerRefreshEvent : function(){
    var VTE_Instance = this;
    jQuery(document).off('click', 'img.dcm_items_refresh').on('click', 'img.dcm_items_refresh', function(event) {
        event.preventDefault();
        VTE_Instance.checkDuplicateItem(this, jQuery(this).data('fieldname'));
    });
},

registerShowDuplicateItems : function(){
    var VTE_Instance = this;
    jQuery(document).on('click', 'img.dcm_items_valid', function(event) {
        event.preventDefault();
        var fieldName = jQuery(this).data('fieldname');
        var html = jQuery('#dcm_items_'+fieldName).html();
        VTE_Instance.showDuplicateItems(this,html);
    });
},

registerChangeDetailViewMode : function(){
    var VTE_Instance = this;
    jQuery(document).on('click', '.changeDetailViewMode', function(event) {
        event.preventDefault();
        setTimeout(function(){VTE_Instance.validDetaiViewLoaded('', '', '')}, 3000);
    });
},

registerHandleRelatedTabEvent : function(){
    var VTE_Instance = this;
    jQuery('ul.nav-pills li').on('click',function(event){
        var url = jQuery.url(jQuery(this).attr('data-url'));
        var current_module = url.param('module');
        var current_view = url.param('view');
        var current_record = url.param('record');
        var current_requestMode = url.param('requestMode');
        if(current_view=='Detail' && current_requestMode=='full'){
            setTimeout(function(){VTE_Instance.validDetaiViewLoaded(current_module, current_record, current_view)}, 3000);
        }
    });
},
registerSubmitEvents: function(){
    var VTE_Instance = this;
    jQuery(document).on("click", "form#EditView button[type='submit'],form[name='QuickCreate'] button[type='submit']", function () {
        var this_form = jQuery(this).closest('form');
        var html = '';
        var is_duplicate = false;
        jQuery.each(VTE_Instance.options.dcm_setting_array,function(index,value){
            jQuery.each(value,function(i,v){
                if(v.ad == 0 && jQuery.inArray(v.fieldname,VTE_Instance.options.dcm_setting_array_values) !== -1){
                    var this_field = this_form.find("input[name='"+v.fieldname+"']");
                    var this_field_name = this_field.closest('td').prev().html();
                    var field_val = this_field.val();
                    var this_ui = "ui-element-" + v.fieldname;
                    if(html.indexOf(this_ui) === -1){
                        html += '<div id="'+this_ui+'">';
                        html += '<div>';
                        html +=     '<table><tr>';
                        html +=     '<td>' + this_field_name + '</td><td> ('+field_val+')</td>';
                        html +=     '</tr></table>';
                        html +='</div>';
                        html += '</div>';
                        is_duplicate = true;
                    }
                }
            });
        });
        if(is_duplicate){
            var title = app.vtranslate('Duplicate records are not allowed:');
            var params = {
                title: title,
                icon : 'fa fa-exclamation-triangle',
                message: app.vtranslate(html)

            };
            jQuery.notify(params);
            return false;
        }
        else{
            return true;
        }
    });
},
registerEvents : function(){
    this.registerHandleRelatedTabEvent();
    this.registerChangeDetailViewMode();
    this.registerFieldChangeOnDetail();
    this.registerFieldChangeOnQuickCreate();
    this.registerFieldChangeOnEditView();
    this.registerSubmitEvents()
    this.registerDetailViewLoaded();
    this.registerRefreshEvent();
    this.registerShowDuplicateItems();
}
});

//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_DuplicateCheckMerge();
    }, 3000);
});
function initData_DuplicateCheckMerge() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('DuplicateCheckMerge')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    // Only load when view is Detail or Edit
    if(app.view()!='Detail' && app.view()!='Edit' && app.view()!='List') return;
    var VTE_Instance = new VTE_DuplicateCheckMerge_Js();
    VTE_Instance.init();
    VTE_Instance.registerEvents();
    jQuery('#detailView table td').css('opacity','1');
    jQuery(document).ajaxComplete( function (event, request, settings) {
        var url = settings.url;
        if(url == undefined) return;
        var targetModule = '';
        var targetView = '';
        var sourceModule = '';
        var mode = '';
        var viewMode = '';
        var sURLVariables = url.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == 'module') {
                targetModule = sParameterName[1];
            }
            else if (sParameterName[0] == 'view') {
                targetView = sParameterName[1];
            }
            else if (sParameterName[0] == 'sourceModule') {
                sourceModule = sParameterName[1];
            }
            else if (sParameterName[0] == 'mode') {
                mode = sParameterName[1];
            }
            else if (sParameterName[0] == 'requestMode') {
                viewMode = sParameterName[1];
            }
        }
        if (targetView == 'Detail' && (mode == 'showDetailViewByMode' || mode == '') && viewMode == 'full') {

            var VTE_Instance = new VTE_DuplicateCheckMerge_Js();
            VTE_Instance.init();
            VTE_Instance.registerEvents();
            jQuery('#detailView table td').css('opacity','1');
        }
    });

    // Allow click to outside to close alert
    $(document).mouseup(function(e){
        var container = $(".popover");
        if(parent.length >0){
            if (container.prev().is("img") && container.prev().hasClass("dcm_items_valid")){
                if (!container.is(e.target) && container.has(e.target).length === 0){
                    container.remove();
                }
            }
        }
    });
}


$(document).ready(function(){
    $('body').on('click', function (e) {
        if ($(e.target).hasClass('dcm_items_valid')){
            return;
        }
        if ($(e.target).data('toggle') !== 'popover'
            && $(e.target).parents('.popover.in').length === 0) { 
            $('.dcm_items_valid').popover('hide');
        }
        if ($(e.target).data('toggle') !== 'popover'
            && $(e.target).parents('[data-toggle="popover"]').length === 0
            && $(e.target).parents('.popover.in').length === 0) { 
            $('.dcm_items_valid').popover('hide');
        }
    });
});