/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

 var Vtiger_ListviewColorsVTE_Js = {

    records : null,

    init : function(){
        if(this.validListViewData()){
            var aDeferred = jQuery.Deferred();
            var thisInstance = this;
            var records = [];
            var urlParams = {};
            jQuery('.listViewEntriesCheckBox').each(function(){
               records.push(jQuery(this).val());
            });
            urlParams['pmodule'] = app.getModuleName();
            urlParams['module'] = 'ListviewColors';
            urlParams['view'] = 'ColorListItems';
            urlParams['records'] = records;
            AppConnector.request(urlParams).then(
                function(data){
                    var response = jQuery.parseJSON(data);
                    if(response.success){
                        if(response.result){
                            thisInstance.records = response.result;
                            thisInstance.setColorRows();
                        }
                    }
                    aDeferred.resolve(data);
                },

                function(error){
                    aDeferred.reject(error);
                }
            );

            return aDeferred.promise();
        }
    },

    validListViewData : function(){
        var viewName = app.getViewName();
        if(viewName == 'List'){
            if(jQuery('#listViewContents .listViewEntriesTable tr.listViewEntries').length > 0){
                this.listViewContainer = jQuery('#listViewContents');
                return true;
            }
        }
        return false;
    },

    setColorRows : function(){
        if(this.records.length > 0){
            for(var i in this.records){
                jQuery('.listViewEntriesTable').find('tr[data-id='+this.records[i].record+']').css('background-color', this.records[i].bg_color);
                jQuery('.listViewEntriesTable').find('tr[data-id='+this.records[i].record+']').css('color', this.records[i].text_color);
                jQuery('.listViewEntriesTable').find('tr[data-id='+this.records[i].record+'] a').css('color', this.records[i].related_record_color);
            }
        }
    }



}

jQuery(document).ready(function () {
    Vtiger_ListviewColorsVTE_Js.init();
    app.listenPostAjaxReady(function() {
        Vtiger_ListviewColorsVTE_Js.init();
    });
});