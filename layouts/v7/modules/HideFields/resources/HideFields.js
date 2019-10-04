/* ********************************************************************************
 * The content of this file is subject to the Table Block ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("HideFields_Js",{
    instance:false,
    getInstance: function(){
        if(HideFields_Js.instance == false){
            var instance = new HideFields_Js();
            HideFields_Js.instance = instance;
            return instance;
        }
        return HideFields_Js.instance;
    }
},{

    registerEvents: function() {
        var thisInstance = this;
        var array_url = jQuery.url().param();
        if(typeof array_url == 'undefined') return false;
        var moduleName = app.getModuleName();
        var requestMode = array_url.requestMode;
        var view = app.getViewName();
        if(view =="Detail"){
            //Get hidefield value
            var actionParams = {
                'module':'HideFields',
                'action':'ActionAjax',
                'mode':'getHideFieldsValue',
                'record':app.getRecordId(),
                'current_module':moduleName
            };
            app.request.post({'data':actionParams}).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        var json_data_fields = data.fields;
                        json_data_fields = jQuery.parseJSON(json_data_fields);
                        var must_keep = false;
                        jQuery.each(json_data_fields, function(index1, value1) {
                            var json_data_symbol = value1.symbol;
                            if(json_data_symbol !='') {
                                json_data_symbol = jQuery.parseJSON(json_data_symbol);
                            }
                            jQuery.each(value1.fields, function(index, value) {
                                if((typeof requestMode != 'undefined' && requestMode == 'full')){
                                    var target_td = jQuery("#"+moduleName+"_detailView_fieldValue_" + index);
                                    var target_value = target_td.children('span:first').html();
                                    if(target_td.children('span:first').data('field-type') == 'url' || target_td.children('span:first').data('field-type') == 'owner' || target_td.children('span:first').data('field-type') == 'reference'){
                                        target_value = target_td.children('span:first').children('a').html();
                                    }else if(target_td.children('span:first').data('field-type') == 'currency' || target_td.children('span:first').data('field-type') == 'picklist'){
                                        target_value = target_td.children('span:first').children('span').html();
                                    }
                                    if(!target_value) target_value = '';
                                    jQuery.each(json_data_symbol, function(ind,sym) {
                                        if(target_value.trim() == '' || target_value.trim().toUpperCase() == sym){
                                            target_td.prev().html("");
                                            target_td.html("");
                                            var tr = target_td.closest('tr');
                                            var table = tr.closest('table');
                                            var empty_tr = true;
                                            var count_item = 0;
                                            var saved_td = [];
                                            jQuery.each(tr.find('td'), function() {
                                                count_item++;
                                                if(jQuery(this).html().trim()!='') {
                                                    empty_tr = false;
                                                    saved_td[count_item] = jQuery(this).html();
                                                }

                                            });
                                            if(empty_tr) tr.hide();
                                        }
                                    });
                                }
                                else{
                                    var target_name = jQuery("span:contains('"+app.vtranslate(value)+"')").closest('td');
                                    var target_span = jQuery("span:contains('"+app.vtranslate(value)+"')").closest('td').next().find('span.value');
                                    var target_value = target_span.text();
                                    if(target_span.find('span:first').length > 0){
                                        target_value = target_span.find('span:first').html();
                                    }
                                    jQuery.each(json_data_symbol, function(ind,sym) {
                                        if(target_name.length > 1) {
                                            jQuery.each(target_name, function (idx, val) {
                                                var focus = $(this);
                                                var taget_spanFocus = focus.closest('td').next().find('span.value');
                                                var target_valueFocus = taget_spanFocus.text();
                                                if(target_valueFocus.trim() == '' || target_valueFocus.trim().toUpperCase() == sym){
                                                    focus.closest('td').hide();
                                                    taget_spanFocus.closest('td').hide();
                                                }
                                            })
                                        }else{
                                            if(target_value.trim() == '' || target_value.trim().toUpperCase() == sym){
                                                target_span.closest('td').hide();
                                                target_name.closest('td').hide();
                                            }
                                        }
                                    });
                                }

                            });
                        });
                        //remove all empty block
                        /*jQuery.each(jQuery('#detailView').find('.detailview-table'), function() {
                            var row_count = jQuery(this).find('tr').length;
                            if(row_count == 1) jQuery(this).hide();
                        });*/
                        //redraw table without empty value
                        //#818546
                        // disable code below to NOT allow redraw table
                        /*jQuery.each(jQuery('#detailView').find('.detailview-table'), function() {
                         $list_row = [];
                         $row = jQuery('<tr/>');
                         var index_row = 0;
                         var index_col = 0;
                         var this_table = jQuery(this);
                         var number_of_td = $(this).find('tr:first > td').length;
                         jQuery.each(this_table.find('tr td'), function(index) {
                         if(jQuery(this).html() != '') {
                         $row.append(this);
                         index_col++;
                         if(index_col == number_of_td){
                         index_row ++;
                         index_col = 0;
                         $list_row.push($row);
                         $row = jQuery('<tr/>');
                         }
                         }
                         });
                         $list_row.push($row);//Push last row
                         this_table.find('tbody').html('');
                         jQuery.each($list_row, function() {
                         this_table.find('tbody').append(this);
                         });
                         });*/

                        return false;
                    }else{
                        app.helper.hideProgress();
                    }
                }
            );
        }
    } ,
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
    }
});
jQuery(document).ready(function(){
    var instance = new HideFields_Js();
    var top_url = window.location.href.split('?');
    if(top_url.length == 0) return false;
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('HideFields')) {
            return;
        }
    }
    var array_url = instance.getQueryParams(top_url[1]);
    if(array_url.view == "Detail"){
        // Only load when loadHeaderScript=1 END #241208
        if (typeof VTECheckLoadHeaderScript == 'function') {
            if (VTECheckLoadHeaderScript('VTETabs') && VTECheckLoadHeaderScript('VTEColumns')) {
                return;
            }
        }
        instance.registerEvents();
    }
    else{
        return;
    }
});
// Listen post ajax event for add product action
jQuery( document ).ajaxComplete(function(event, xhr, settings) {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('HideFields')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var url = settings.data;
    if(typeof url == 'undefined' && settings.url) url = settings.url;

    var instance = new HideFields_Js();
    var top_url = window.location.href.split('?');
    var array_url = instance.getQueryParams(top_url[1]);
    var other_url = instance.getQueryParams(url);

    if(typeof array_url == 'undefined') return false;
    var innner_url = 'current_module='+array_url.module+'&module=HideFields&action=ActionAjax&mode=getHideFieldsValue';
    //console.log(other_url);
    if(other_url.module == 'VTETabs' && other_url.view == 'DetailViewAjax' && other_url.mode == 'showModuleDetailView') {
        instance.registerEvents();
    }
    else if(other_url.module == 'VTEColumns' && other_url.view == 'DetailViewAjax' && other_url.mode == 'getBlockDetailView') {
        instance.registerEvents();
    }
    else if(url != innner_url && array_url.action == 'SaveAjax'){
        instance.registerEvents();
    }
    else if(url != innner_url && other_url.mode == 'showDetailViewByMode'){
        instance.registerEvents();
    }
});