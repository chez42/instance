/* ********************************************************************************
 * The content of this file is subject to the Table Block ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
var controlLayoutFields_HideFields = false;
jQuery.Class("HideFields_Js",{

},{

    registerEvents: function() {
        var thisInstance = this;
        var url = window.location.href.split('?');
        var array_url = thisInstance.getQueryParams(url[1]);
        if(typeof array_url == 'undefined') return false;
        var moduleName = array_url.module;
        var requestMode = array_url.requestMode;
        var view = app.getViewName();
        if(view =="Detail"){
            //Get hidefield value
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var url = "index.php?module=HideFields&action=ActionAjax&mode=getHideFieldsValue";
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {"current_module":moduleName}
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    if(data) {
                        var json_data = jQuery.parseJSON(data);
                        var json_data_fields = jQuery.parseJSON(json_data.result.fields);
                        var must_keep = false;
                        jQuery.each(json_data_fields, function(index1, value1) {
                            var json_data_symbol = jQuery.parseJSON(value1.symbol);
                            jQuery.each(value1.fields, function(index, value) {
                                if(typeof requestMode != 'undefined' && requestMode == 'full'){
                                    var target_td = jQuery("#"+moduleName+"_detailView_fieldValue_" + index);
                                    var target_value = target_td.children('span:first').html();
                                    if(target_td.children('span:first').data('field-type') == 'url'){
                                        target_value = target_td.children('span:first').children('a').html();
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
                                            if(empty_tr) tr.remove();
                                        }
                                    });
                                }
                                else{
                                    var target_name = jQuery("label:contains('"+app.vtranslate(value)+"')").closest('td');
                                    var target_span = jQuery("label:contains('"+app.vtranslate(value)+"')").closest('td').next().find('span.value');
                                    var target_value = target_span.text();
                                    jQuery.each(json_data_symbol, function(ind,sym) {
                                        if(target_name.length > 1) {
                                            jQuery.each(target_name, function (idx, val) {
                                                var focus = $(this);
                                                var taget_spanFocus = focus.closest('td').next().find('span.value');
                                                var target_valueFocus = taget_spanFocus.text();
                                                if(target_valueFocus.trim() == '' || target_valueFocus.trim().toUpperCase() == sym){
                                                    focus.closest('td').remove();
                                                    taget_spanFocus.closest('td').remove();
                                                }
                                            })
                                        }else{
                                            if(target_value.trim() == '' || target_value.trim().toUpperCase() == sym){
                                                target_span.closest('td').remove();
                                                target_name.closest('td').remove();
                                            }
                                        }
                                    });
                                }

                            });
                        });
                        //remove all empty block
                        jQuery.each(jQuery('#detailView').find('.detailview-table'), function() {

                            var row_count = jQuery(this).find('tr').length;
                            if(row_count == 1) jQuery(this).hide();
                        });
                        //redraw table without empty value
                        jQuery.each(jQuery('#detailView').find('.detailview-table'), function() {
                            $list_row = [];
                            $row = jQuery('<tr/>');
                            var index_row = 0;
                            var index_col = 0;
                            var this_table = jQuery(this);
                            jQuery.each(this_table.find('tr td'), function(index) {
                                if(jQuery(this).html() != '') {
                                    $row.append(this);
                                    index_col++;
                                    if(index_col == 4){
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
                        });
                        controlLayoutFields_HideFields = true;
                        return false;
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
    instance.registerEvents();
});
// Listen post ajax event for add product action
jQuery( document ).ajaxComplete(function(event, xhr, settings) {

    var url = settings.data;
    if(typeof url == 'undefined' && settings.url) url = settings.url;

    var instance = new HideFields_Js();
    var top_url = window.location.href.split('?');
    var array_url = instance.getQueryParams(top_url[1]);
    var other_url = instance.getQueryParams(url);

    if(typeof array_url == 'undefined') return false;
    var innner_url = 'current_module='+array_url.module+'&module=HideFields&action=ActionAjax&mode=getHideFieldsValue';

    if(url != innner_url && array_url.action == 'SaveAjax'){
        instance.registerEvents();
    }
    if(url != innner_url && other_url.mode == 'showDetailViewByMode'){
        instance.registerEvents();
    }
});