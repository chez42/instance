/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("DataExportTracking_Js",{

},{

    handleExportData:function(){
        var thisInstance = this;
        jQuery("#exportForm :button.btn-success").on("click",function(){
            var action_type = 1;
            var url = "index.php?module=DataExportTracking&action=ActionAjax&mode=isTracking";
            var actionParams = {
                type:"POST",
                url:url,
                dataType:"html",
                data : {'action_type':action_type}
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    var response = jQuery.parseJSON(data);
                    if(response.result.is_track == 1){
                        var form_data = jQuery("#exportForm").serializeArray();
                        var data_transfer = {};
                        jQuery.each( form_data, function( key, obj_value ) {
                            data_transfer[obj_value.name] = obj_value.value;
                        });
                        data_transfer['action_type'] = action_type;
                        url = "index.php?module=DataExportTracking&action=ActionAjax&mode=saveDataExportTrackingLog";
                        var actionParams = {
                            "type":"POST",
                            "url":url,
                            "dataType":"html",
                            "data" : data_transfer
                        };
                        AppConnector.request(actionParams).then(
                            function(data) {
                                var response = jQuery.parseJSON(data);
                                if(response.success == 1) jQuery("#exportForm").submit();
                            }
                        );
                        //if(tracking == 1) jQuery("#exportForm").submit();
                    }
                    else{
                        jQuery("#exportForm").submit();
                    }
                    return false;
                }
            );
            return false;
        });
    },
    handleExportReportData:function(){
        var thisInstance = this;
        jQuery("#detailView :button.reportActions").not(':first').on("click",function(){
            var url_export = jQuery(this).data('href');
            var current_url = window.location.href.split('?');
            var is_csv_excel_export = url_export.indexOf("mode=GetCSV") != -1 || url_export.indexOf("mode=GetXLS") != -1;
            if(is_csv_excel_export){
                var action_type = 2;
                var url = "index.php?module=DataExportTracking&action=ActionAjax&mode=isTracking";
                var actionParams = {
                    type:"POST",
                    url:url,
                    dataType:"html",
                    data : {'action_type':action_type}
                };
                AppConnector.request(actionParams).then(
                    function(data) {
                        var response = jQuery.parseJSON(data);
                        if(response.result.is_track == 1){
                            url = "index.php?module=DataExportTracking&action=ActionAjax&mode=saveExportReportTrackingLog";
                            var obj_advanced_filter = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',jQuery('div.contentsDiv')));
                            var advanced_filter = obj_advanced_filter.getValues();
                            var obj_action_url = thisInstance.getQueryParams(current_url[1]);
                            var actionParams = {
                                "type":"POST",
                                "url":url,
                                "dataType":"html",
                                "data" : {
                                    'url_export':url_export,
                                    'current_url':current_url[1],
                                    'advanced_filter':JSON.stringify(advanced_filter),
                                    'report_id':obj_action_url.record
                                }
                            };
                            AppConnector.request(actionParams).then(
                                function(data) {
                                    var response = jQuery.parseJSON(data);
                                    if(response.success == 1) return true;
                                }
                            );
                        }
                        else{
                            return true;
                        }
                        return false;
                    }
                );
                return false;
            }
        });
    },
    getQueryParams:function(qs) {
        if(typeof(qs) != 'undefined' ){
            qs = qs.split('+').join(' ');

            var params = {},
                tokens,
                re = /[?&]?([^=]+)=([^&]*)/g;

            while (tokens = re.exec(qs)) {
                params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
            }
            return params;
        }
    },
    trackingExport:function(data){
        var tracked = 0;
        var url = "index.php?module=DataExportTracking&action=ActionAjax&mode=saveDataExportTrackingLog";
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"html",
            "data" : data
        };
        AppConnector.request(actionParams).then(
            function(data) {
                var response = jQuery.parseJSON(data);
                console.log(response.success);
                if(response.success == 1) tracked = 1;
            }
        );
        return tracked;
    },
    isTracking:function(action_type){
        var is_tracking = false;
        AppConnector.request({
            type:       'GET', // POST
            url:        'index.php',
            dataType:   'html',
            data: {
                module: 'DataExportTracking', // Module name
                action: 'ActionAjax',
                mode:   'isTracking',
                action_type:action_type
            }
        }).then(function (result) {
                var response = jQuery.parseJSON(result);
                if(response.result.is_track) is_tracking = true;
            }
        );
        return is_tracking;
    },
    handleCopy:function(){
        var thisInstance = this;
        jQuery(document).on("copy", function(e){
            var txt_clipboard = thisInstance.getSelectionText()
            if (txt_clipboard.length > 0){
                var action_type = 4;
                var url = "index.php?module=DataExportTracking&action=ActionAjax&mode=isTracking";
                var actionParams = {
                    type:"POST",
                    url:url,
                    dataType:"html",
                    data : {'action_type':action_type}
                };
                AppConnector.request(actionParams).then(
                    function(data) {
                        var response = jQuery.parseJSON(data);
                        if(response.result.is_track == 1){
                            var top_url = window.location.href.split('?');
                            var data_transfer = {};
                            data_transfer['link'] = top_url[1];
                            data_transfer['txt_clipboard'] = txt_clipboard;
                            data_transfer['action_type'] = action_type;
                            url = "index.php?module=DataExportTracking&action=ActionAjax&mode=saveDataExportTrackingLog";
                            var actionParams = {
                                "type":"POST",
                                "url":url,
                                "dataType":"html",
                                "data" : data_transfer
                            };
                            AppConnector.request(actionParams).then(
                                function(data) {
                                    //var response = jQuery.parseJSON(data);
                                    //if(response.success == 1) jQuery("#exportForm").submit();
                                }
                            );
                            //if(tracking == 1) jQuery("#exportForm").submit();
                        }
                        return true;
                    }
                );
                return true;
            }

        });
    },
    getSelectionText: function(){
        var selectedText = ""
        if (window.getSelection){
            selectedText = window.getSelection().toString()
        }
        return selectedText
    }
});
jQuery(document).ready(function(){
    var instance = new DataExportTracking_Js();
    instance.handleExportData();
    instance.handleExportReportData();
    instance.handleCopy();
});
// Listen post ajax event for add product action
jQuery( document ).ajaxComplete(function(event, xhr, settings) {
    //var url = settings.data;
    //var instance = new DataExportTracking_Js();
    //var top_url = window.location.href.split('?');
    //var obj_url = instance.getQueryParams(top_url[1]);
    //if(typeof obj_url == 'undefined') return false;
    ////console.log(xhr.responseText);
    //var  view = obj_url.view;
    //return false;
    //
    //if(view == 'Export'){
    //    var data = {'module':obj_url.module,'page':obj_url.page,'viewname':obj_url.viewname,'page':obj_url.page,'search_params':obj_url.search_params,'log':xhr.responseText};
    //    instance.trackingExport(data);
    //}
});