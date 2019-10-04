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
            app.request.post({'data': urlParams}).then(
                function(err,data){
                    if(err === null) {
                        var response = data;
                        // if(response.success){
                            if(response){
                                thisInstance.records = response;
                                thisInstance.setColorRows();
                            }
                        // }
                        aDeferred.resolve(data);
                    }else{
                        aDeferred.reject(error);
                    }
                }
            );

            return aDeferred.promise();
        }
    },

    validListViewData : function(){
        var viewName = app.view();
        if(viewName == 'List'){
            if(jQuery('#table-content .listview-table tr.listViewEntries').length > 0){
                this.listViewContainer = jQuery('#table-content');
                return true;
            }
        }
        return false;
    },

    setColorRows : function(){
        
        if(this.records.length > 0){
            // console.log(this.records);
            jQuery('.listview-table .listViewEntries:hover').css('background-color',"#FFF !important");
            for(var i in this.records){
                var bg_color = this.records[i].bg_color;
                jQuery('.listview-table').find('tr[data-id='+this.records[i].record+']').data('list-color',bg_color);
                jQuery('.listview-table').find('tr[data-id='+this.records[i].record+']').hover(function(){
                    $(this).css("background-color", "#FFF");

                },function(){
                    var bgcolor = jQuery(this).data('list-color');
                    $(this).css("background-color", bgcolor)
                });
                jQuery('.listview-table').find('tr[data-id='+this.records[i].record+']').css('background-color', this.records[i].bg_color);
                jQuery('.listview-table').find('tr[data-id='+this.records[i].record+'] span').css('color', this.records[i].text_color);
                jQuery('.listview-table').find('tr[data-id='+this.records[i].record+'] .fieldValue .value a').attr('style','color:' + this.records[i].related_record_color + ' !important');
            }
        }
    }



};

jQuery(document).ready(function () {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('ListviewColors')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    Vtiger_ListviewColorsVTE_Js.init();
    app.event.on("post.listViewFilter.click", function() {
        Vtiger_ListviewColorsVTE_Js.init();
    });

});