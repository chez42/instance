/* ********************************************************************************
 * The content of this file is subject to the Data Export Tracking ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_Index_Js("DataExportTracking_Js",{

},{

    handleExportData:function(){
        var thisInstance = this;
        jQuery(document).on("click", "#exportForm :button.btn-success",function(){
            var action_type = 1;
            var actionParams = {
                "type": "POST",
                "url": "index.php",
                "dataType": "json",
                "data": {
                    'module': 'DataExportTracking',
                    'action': 'ActionAjax',
                    'mode': 'isTracking',
                    'action_type': action_type
                }
            };
            app.request.post(actionParams).then(
                function (err, data) {
                    if (err === null) {
                        if(data.is_track == 1){
                            var form_data = jQuery("#exportForm").serializeArray();
                            var data_transfer = {};
                            jQuery.each( form_data, function( key, obj_value ) {
                                data_transfer[obj_value.name] = obj_value.value;
                            });
                            data_transfer['action_type'] = action_type;
                            data_transfer['module'] = 'DataExportTracking';
                            data_transfer['action'] = 'ActionAjax';
                            data_transfer['mode'] = 'saveDataExportTrackingLog';

                            var actionParams = {
                                "type": "POST",
                                "url": "index.php",
                                "dataType": "json",
                                "data": data_transfer
                            };
                            app.request.post(actionParams).then(
                                function (err, data) {
                                    if (err === null) {
                                        if(data.success == 1) jQuery("#exportForm").submit();
                                    } else {
                                        console.log(err);
                                    }
                                }
                            );
                            //if(tracking == 1) jQuery("#exportForm").submit();
                        }
                        else{
                            jQuery("#exportForm").submit();
                        }
                        return false;
                    } else {
                        console.log(err);
                    }
                }
            );
            return false;
        });
    },
    handleExportReportData:function(){
        var thisInstance = this;
        jQuery(document).on("click", "#detailView :button.reportActions:not(:first)",function(){
            var url_export = jQuery(this).data('href');
            var current_url = window.location.href.split('?');
            var is_csv_excel_export = url_export.indexOf("mode=GetCSV") != -1 || url_export.indexOf("mode=GetXLS") != -1;
            if(is_csv_excel_export){
                var action_type = 2;
                var actionParams = {
                    "type": "POST",
                    "url": "index.php",
                    "dataType": "json",
                    "data": {
                        'module': 'DataExportTracking',
                        'action': 'ActionAjax',
                        'mode': 'isTracking',
                        'action_type': action_type
                    }
                };
                app.request.post(actionParams).then(
                    function (err, data) {
                        if (err === null) {
                            if(data.is_track == 1){
                                var obj_advanced_filter = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',jQuery('.settingsPageDiv')));
                                var advanced_filter = obj_advanced_filter.getValues();
                                var obj_action_url = thisInstance.getQueryParams(current_url[1]);
                                var actionParams = {
                                    "type": "POST",
                                    "url": "index.php",
                                    "dataType": "json",
                                    "data": {
                                        'module': 'DataExportTracking',
                                        'action': 'ActionAjax',
                                        'mode': 'saveExportReportTrackingLog',
                                        'url_export': url_export,
                                        'current_url': current_url[1],
                                        'advanced_filter': JSON.stringify(advanced_filter),
                                        'report_id': obj_action_url.record
                                    }
                                };
                                app.request.post(actionParams).then(
                                    function (err, data) {
                                        if (err === null) {
                                            if(data.success == 1) return true;
                                        } else {
                                            console.log(err);
                                        }
                                    }
                                );
                            }
                            else{
                                return true;
                            }
                            return false;
                        } else {
                            console.log(err);
                        }
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
        data['module'] = 'DataExportTracking';
        data['action'] = 'ActionAjax';
        data['mode'] = 'saveDataExportTrackingLog';
        var actionParams = {
            "type": "POST",
            "url": "index.php",
            "dataType": "json",
            "data": data
        };
        app.request.post(actionParams).then(
            function (err, data) {
                if (err === null) {
                    if(data.success == 1) tracked = 1;
                } else {
                    console.log(err);
                }
            }
        );
        return tracked;
    },
    isTracking:function(action_type){
        var is_tracking = false;
        var actionParams = {
            "type": 'GET', // POST
            "url": 'index.php',
            "dataType": 'json',
            "data": {
                "module": 'DataExportTracking', // Module name
                "action": 'ActionAjax',
                "mode": 'isTracking',
                "action_type": action_type
            }
        };
        app.request.post(actionParams).then(
            function (err, data) {
                if (err === null) {
                    if(data.is_track) is_tracking = true;
                } else {
                    console.log(err);
                }
            }
        );
        return is_tracking;
    },
    handleCopy:function(){
        var thisInstance = this;
        jQuery(document).on("copy", function(e){
            var txt_clipboard = thisInstance.getSelectionText();
            if (txt_clipboard.length > 0){
                var action_type = 4;
                var actionParams = {
                    "type": "POST",
                    "url": "index.php",
                    "dataType": "json",
                    "data": {
                        'module': 'DataExportTracking',
                        'action': 'ActionAjax',
                        'mode': 'isTracking',
                        'action_type': action_type
                    }
                };
                app.request.post(actionParams).then(
                    function (err, data) {
                        if (err === null) {
                            if(data.is_track == 1){
                                var top_url = window.location.href.split('?');
                                var data_transfer = {};
                                data_transfer['link'] = top_url[1];
                                data_transfer['txt_clipboard'] = txt_clipboard;
                                data_transfer['action_type'] = action_type;
                                data_transfer['module'] = 'DataExportTracking';
                                data_transfer['action'] = 'ActionAjax';
                                data_transfer['mode'] = 'saveDataExportTrackingLog';
                                var actionParams = {
                                    "type": "POST",
                                    "url": "index.php",
                                    "dataType": "json",
                                    "data": data_transfer
                                };
                                app.request.post(actionParams).then(
                                    function (err, data) {
                                        if (err === null) {
                                            //if(data.success == 1) jQuery("#exportForm").submit();
                                        } else {
                                            console.log(err);
                                        }
                                    }
                                );
                                //if(tracking == 1) jQuery("#exportForm").submit();
                            }
                            return true;
                        } else {
                            console.log(err);
                        }
                    }
                );
                return true;
            }

        });
    },
    getSelectionText: function(){
        var selectedText = "";
        if (window.getSelection){
            selectedText = window.getSelection().toString()
        }
        return selectedText
    },

});
//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_DataExportTracking();
    }, 10000);
});
function initData_DataExportTracking() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('DataExportTracking')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var instance = new DataExportTracking_Js();
    instance.handleExportData();
    instance.handleExportReportData();
    instance.handleCopy();
}
// Listen post ajax event for add product action
