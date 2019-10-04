/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_CustomView_Js("VTEEmailMarketing_CustomView_Js",{},{

    saveFilter : function() {
        var aDeferred = jQuery.Deferred();
        var formElement = jQuery("#CustomView");
        var formData = formElement.serializeFormData();

        app.helper.showProgress();

        app.request.post({'data':formData}).then(
            function(error,data){
                if(error === null){
                    window.onbeforeunload = null;
                    aDeferred.resolve(data);
                }
                else{
                    app.helper.hideProgress();
                    aDeferred.reject();
                    app.helper.showErrorNotification({'message': app.vtranslate('JS_VIEW_ALREADY_EXISTS')});
                }
            }
        );
        return aDeferred.promise();
    },

    saveAndViewFilter : function(){
        this.saveFilter().then(function (response) {
            if (typeof response != "undefined") {
                var cvId = response.id;
                var params = {
                    'module' : 'VTEEmailMarketing',
                    'action' : 'ActionAjax',
                    'mode' : 'getRecordNewFilterStep2',
                    'cvId' : cvId
                }
                AppConnector.request(params).then(
                    function (data) {
                        if(data.success == true){
                            app.helper.hideProgress();
                            app.helper.hidePageContentOverlay();
                            app.helper.showSuccessNotification({'message':app.vtranslate('JS_LIST_SAVED')});
                            var table = jQuery('#EmailMarketingStep2').find('#table-list-filter');
                            var tbody = table.find('tbody');
                            var result = data.result;
                            var iconsModulde = '';
                            if(result.moduleName == 'Leads'){
                                iconsModulde += '<h5> <i class="vicon-leads module-icon"></i>Leads</h5>';
                                ;
                            }
                            else if(result.moduleName == 'Contacts'){
                                iconsModulde += '<h5> <i class="vicon-contacts module-icon"></i>Contacts</h5>';
                            }
                            else{
                                iconsModulde += '<h5> <i class="vicon-accounts module-icon"></i>Accounts</h5>';

                            }
                            var html = '';
                            html += '<tr style="background: #fbfadb" module="'+result.moduleName+'">';
                            html += '<td>'+iconsModulde+'</td>';
                            html += '<td><h4>'+result.filterName+'</h4>' + '<i><span class="marketing-list-user">( '+result.name+' )</span></i></td>';
                            html += '<td><h5 class="text-center">'+result.count+'</h5>' + '<h5 class="text-center">Records</h5></td>';
                            html += '<td><button class="btn addButton btn-default btn-success btn-load-filter" value="'+cvId+'" action="0" >Load</button></td>';
                            html += '<td><h4 class="text-center filter-loaded-record"></h4></td>';
                            html += '<td><h5 class="show-info-load-0-record"></h5></td>';
                            html += '</tr>';
                            tbody.prepend(html);
                        }
                    }
                )
            } else {
                app.helper.showErrorNotification({message: app.vtranslate('JS_FAILED_TO_SAVE')});
            }
        });
    },

    registerEvents: function () {
    	this._super();
	}
});

$(document).ready(function () {
    var customView = new VTEEmailMarketing_CustomView_Js();
    customView.registerEvents();
});