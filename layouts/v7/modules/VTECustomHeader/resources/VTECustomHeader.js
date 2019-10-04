/* ********************************************************************************
 * The content of this file is subject to the Custom Header/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTECustomHeader_Js", {
    instance: false,
    getInstance: function () {
        if (VTECustomHeader_Js.instance == false) {
            var instance = new VTECustomHeader_Js();
            VTECustomHeader_Js.instance = instance;
            return instance;
        }
        return VTECustomHeader_Js.instance;
    }
},{
    registerShowOnDetailView:function(){
        var self = this;
        var params = {};
        params['module'] = 'VTECustomHeader';
        params['view'] = 'HeaderIcon';
        params['record'] = app.getRecordId();
        params['moduleSelected'] = app.getModuleName();
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null){
                    var detailview_header = jQuery('.detailview-header .row:first');
                    detailview_header.append(data);
                    $("#div_custome_header").fadeIn(700);
                }
            },
            function(error) {
            }
        );

    },
    registerEvents: function(){
        this.registerShowOnDetailView();
    }
});

jQuery(document).ready(function () {
	// Only load when loadHeaderScript=1 BEGIN #241208
	if (typeof VTECheckLoadHeaderScript == 'function') {
		if (!VTECheckLoadHeaderScript('VTECustomHeader')) {
			return;
		}
	}
	// Only load when loadHeaderScript=1 END #241208
	
    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    if(viewName == 'Detail'){
        var instance = new VTECustomHeader_Js();
        instance.registerEvents();
    }
});
