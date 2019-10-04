/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEForecast_AddChildren_Js",{

},{
    //updatedBlockSequence : {},
	registerAddChildrenEvent : function() {
		 var thisInstance = this;		
		 
		 var btnSave = jQuery('#btnSave');
		 btnSave.on('click',function(){
            app.helper.showProgress();	
            var parrentString = jQuery('#parrent_id').val();
            var childrenString = '';
            jQuery("input:checkbox[name=chk]:checked").each(function(){
                childrenString += "," +jQuery(this).val();
            });
            var actionParams = {
                'parrent':parrentString,
                'children':childrenString,
                'module':app.getModuleName(),
                'action':'SaveAjax'
            };

            app.request.post({'data':actionParams}).then(
                function (err, data) {
                    if(err === null) {
                        app.helper.hideProgress();               
                        var params = {};
                        params.message = app.vtranslate('User has been assigned');
                        app.helper.showSuccessNotification(params); 
                        window.location.href = "index.php?module=VTEForecast&parent=Settings&view=Settings&tab=hierarchy";			 
                    }
                    else {
                        app.helper.hideProgress();
                    }
                }
            );
			return false;
		 });
		 
	},
	saveAction : function() {				
      
		
	},
	
    registerEvents : function() {
        this.registerAddChildrenEvent();         
     }
});
jQuery(document).ready(function(){
    var instance = new VTEForecast_AddChildren_Js();
    instance.registerEvents();
    
    // Fix issue not display menu
    Vtiger_Index_Js.getInstance().registerEvents();
});