/*+***********************************************************************************
 * The content of this file is subject to the VTE Duplicate Check & Merge ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class("VTE_DuplicateCheckMerge_Js",{

    options : null,

    init: function(){
        if(!this.options){
            var current_url = jQuery.url();
            this.options = {
                current_module: current_url.param('module'),
                current_view: current_url.param('view'),
                current_record: current_url.param('record'),
                dcm_setting_array: jQuery.parseJSON(dcm_setting)
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
                    for(var i=0; i<len; i++ ){
                        if(data[i].passive == 1){
                            jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' label').prepend('<img class="dcm_items_refresh" data-fieldname="'+data[i].fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/refresh.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                            jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' label').prepend('<img class="dcm_items_refresh" data-fieldname="'+data[i].fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/refresh.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                        }else if(data[i].passive == 0 && data[i].rtotr == 1){
                            if(data[i].duplicate_items.length > 0 || data[i].crosscheck.length > 0){
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' label').prepend('<img class="dcm_items_valid" data-fieldname="'+data[i].fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' label').prepend('<img class="dcm_items_valid" data-fieldname="'+data[i].fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');

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
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' label img.dcm_items_not_valid').remove();
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' label img.dcm_items_not_valid').remove();
                            }else{
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' label').prepend('<img class="dcm_items_not_valid" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' label').prepend('<img class="dcm_items_not_valid" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                                jQuery('#dcm_items_'+data[i].fieldname).remove();
                                jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' label img.dcm_items_valid').remove();
                                jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' label img.dcm_items_valid').remove();
                            }
                        }
                    }
                }
            }
        });
    },

    handleFields : function(field_setting, fieldvalue){
        var current_url = jQuery.url();
        var current_module = current_url.param('module');
        var current_view = current_url.param('view');
        var current_record = current_url.param('record');
        if(field_setting.rtayt == 0){
            return;
        }
        jQuery.ajax({
            type: "POST",
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
                        if(data.duplicate_merge==1 && data.entities.length >1){
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
                    jQuery('#'+current_module+'_editView_fieldName_'+field_setting.fieldname).validationEngine('showPrompt', html , 'error','bottomLeft',true);
                }
            }
        });
    },

    handleFieldsInQuickCreate : function(field_setting, fieldvalue){
        var current_module = jQuery('form[name=QuickCreate] input[name=module]').val();
        var current_view = 'Edit';
        var current_record = '';
        if(field_setting.rtayt == 0){
            return;
        }
        jQuery.ajax({
            type: "POST",
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

                    jQuery('form[name= QuickCreate] #'+current_module+'_editView_fieldName_'+field_setting.fieldname).validationEngine('showPrompt', html , 'error','bottomLeft',true);
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
                    var title = '';
                    //html += data.fieldlabel + ' ' + response.result.message + '.<br />';
                    if(data.entities.length > 0){
                        html += '<h4 class="ui-pnotify-title">' + data.fieldlabel + ' ' + response.result.message + '</h4>';
                        html += '<ul style="max-height: 200px; overflow-y: auto;">';
                        for(var i = 0; i<data.entities.length; i++){
                            html += '<li>';
                            html +=     '<a style="color: #0000ff" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
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
                        html += '<h4 class="ui-pnotify-title">Cross Check for: ' + data.fieldlabel + '</h4>';
                        html += '<ul style="max-height: 200px; overflow-y: auto;">';
                        for(var i = 0; i<data.crosscheck.length; i++){
                            html += '<li>';
                            html +=     '<a style="color: #0000ff" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                            html +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                            html +=     '</a>';
                            html += '</li>';
                        }
                        html += '</ul>';
                    }

                    var params = {
                        title: false,
                        text: app.vtranslate(html),
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);

                    jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname+' label').prepend('<img class="dcm_items_valid" data-fieldname="'+field_setting.fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                    jQuery('#_detailView_fieldLabel_'+field_setting.fieldname+' label').prepend('<img class="dcm_items_valid" data-fieldname="'+field_setting.fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
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
                    jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname+' label').prepend('<img class="dcm_items_not_valid" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                    jQuery('#_detailView_fieldLabel_'+field_setting.fieldname+' label').prepend('<img class="dcm_items_not_valid" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: bottom;margin-right: 5px;">');
                    jQuery('#dcm_items_'+field_setting.fieldname).remove();
                    jQuery('#'+current_module+'_detailView_fieldLabel_'+field_setting.fieldname+' label img.dcm_items_valid').remove();
                    jQuery('#_detailView_fieldLabel_'+field_setting.fieldname+' label img.dcm_items_valid').remove();
                }
            }
        });
    },

    showDuplicateItems : function(obj,fieldname){
        jQuery(obj).validationEngine('showPrompt', jQuery('#dcm_items_'+fieldname).html() , 'error','bottomLeft',true);
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
                fieldvalue: jQuery('#'+current_module+'_editView_fieldName_'+fieldname).val(),
                record: current_record
            },
            success:function (response) {
                if(response.result.duplicate){
                    var data = response.result.data;
                    var html = '';
                    //html += data.fieldlabel + ' ' + response.result.message + '.<br />';
                    if(data.entities.length > 0){
                        html += '<h4 class="ui-pnotify-title">' + data.fieldlabel + ' ' + response.result.message + '</h4>';
                        html += '<ul style="max-height: 200px; overflow-y: auto;">';
                        for(var i = 0; i<data.entities.length; i++){
                            html += '<li>';
                            html +=     '<a style="color: #0000ff" target="_blank" href="index.php?module='+current_module+'&view=Detail&record='+data.entities[i].id+'&mode=showDetailViewByMode&requestMode=full">';
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
                        html += '<h4 class="ui-pnotify-title">Cross Check for: ' + data.fieldlabel + '</h4>';
                        html += '<ul style="max-height: 200px; overflow-y: auto;">';
                        for(var i = 0; i<data.crosscheck.length; i++){
                            html += '<li>';
                            html +=     '<a style="color: #0000ff" target="_blank" href="index.php?module='+data.crosscheck[i].module+'&view=Detail&record='+data.crosscheck[i].id+'&mode=showDetailViewByMode&requestMode=full">';
                            html +=         data.crosscheck[i].entityName + ' (<b>'+data.crosscheck[i].alias+'</b>)';
                            html +=     '</a>';
                            html += '</li>';
                        }
                        html += '</ul>';
                    }

                    var params = {
                        title: false,
                        text: app.vtranslate(html),
                        width: '35%'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' label').prepend('<img class="dcm_items_valid" data-fieldname="'+fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;">');
                    jQuery('#_detailView_fieldLabel_'+fieldname+' label').prepend('<img class="dcm_items_valid" data-fieldname="'+fieldname+'" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/error.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;">');
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
                            html_buildin += '<a style="color:#7cfc00;" target="_blank" href="index.php?module=DuplicateCheckMerge&pmodule='+current_module+'&view=FindDuplicates&fields[]='+fieldname+'&value_dupes['+fieldname+']='+jQuery('#'+current_module+'_editView_fieldName_'+fieldname).val()+'&ignoreEmpty=on">Merge Records</a>';
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
                    jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' label').prepend('<img class="dcm_items_not_valid" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;">');
                    jQuery('#_detailView_fieldLabel_'+fieldname+' label').prepend('<img class="dcm_items_not_valid" src="layouts/vlayout/modules/DuplicateCheckMerge/resources/valid.png" width="16px;" height="16px;" style="vertical-align: middle;margin-right: 5px;">');
                }
            }
        });
    },

    removeIcon : function(current_module, fieldname){
        jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' label img.dcm_items_not_valid').remove();
        jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' label img.dcm_items_refresh').remove();
        jQuery('#'+current_module+'_detailView_fieldLabel_'+fieldname+' label img.dcm_items_valid').remove();
        jQuery('#_detailView_fieldLabel_'+fieldname+' label img.dcm_items_not_valid').remove();
        jQuery('#_detailView_fieldLabel_'+fieldname+' label img.dcm_items_refresh').remove();
        jQuery('#_detailView_fieldLabel_'+fieldname+' label img.dcm_items_valid').remove();
    },

    registerDetailViewLoaded : function(){
        this.validDetaiViewLoaded();
    },

    registerFieldChangeOnEditView : function(current_module){
        var VTE_Instance = this;
        jQuery(document).on('change', '#EditView input', function(event) {
            var fieldname = jQuery(this).attr('name');
            var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
            if(this.value != '' && key !== false){
                VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
            }
        });

        jQuery(document).on('change', '#EditView select', function(event) {
            var fieldname = jQuery(this).attr('name');
            var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
            if(this.value != '' && key !== false){
                VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
            }
        });
		
		jQuery(document).on('change', '#EditView textarea', function(event) {
            var fieldname = jQuery(this).attr('name');
            var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
            if(this.value != '' && key !== false){
                VTE_Instance.handleFields(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
            }
        });
    },

    registerFieldChangeOnQuickCreate : function(current_module){
        var VTE_Instance = this;
        jQuery(document).on('change', 'form[name=QuickCreate] input', function(event) {
            var current_module = jQuery('form[name=QuickCreate] input[name=module]').val();
            var fieldname = jQuery(this).attr('name');
            var key = VTE_Instance.getFieldInfoFromSetting(fieldname, current_module);
            if(this.value != '' && key !== false){
                VTE_Instance.handleFieldsInQuickCreate(VTE_Instance.options.dcm_setting_array[current_module][key], this.value);
            }
        });
        jQuery(document).on('change', 'form[name=QuickCreate] select', function(event) {
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
        jQuery(document).on('change', '#detailView input', function(event) {
            var fieldname = jQuery(this).attr('name');
            var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
            if(this.value != '' && key !== false){
                VTE_Instance.handleFieldsDetailView(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
            }
        });

        //jQuery('#detailView select').change(function(){
        jQuery(document).on('change', '#detailView select', function(event) {
            var fieldname = jQuery(this).attr('name');
            var key = VTE_Instance.getFieldInfoFromSetting(fieldname);
            if(this.value != '' && key !== false){
                VTE_Instance.handleFieldsDetailView(VTE_Instance.options.dcm_setting_array[VTE_Instance.options.current_module][key], this.value);
            }
        });
    },

    registerRefreshEvent : function(){
        var VTE_Instance = this;
        jQuery(document).on('click', 'img.dcm_items_refresh', function(event) {
            event.preventDefault();
            VTE_Instance.checkDuplicateItem(this, jQuery(this).data('fieldname'));
        });
    },

    registerShowDuplicateItems : function(){
        var VTE_Instance = this;
        jQuery(document).on('click', 'img.dcm_items_valid', function(event) {
            event.preventDefault();
            VTE_Instance.showDuplicateItems(this, jQuery(this).data('fieldname'));
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

    registerEvents : function(){
        this.registerHandleRelatedTabEvent();
        this.registerChangeDetailViewMode();
        this.registerFieldChangeOnDetail();
        this.registerFieldChangeOnQuickCreate();
        this.registerFieldChangeOnEditView();
        this.registerDetailViewLoaded();
        this.registerRefreshEvent();
        this.registerShowDuplicateItems();
    }
});

//On Page Load
jQuery(document).ready(function() {
    var VTE_Instance = new VTE_DuplicateCheckMerge_Js();
    VTE_Instance.init();
    VTE_Instance.registerEvents();
});