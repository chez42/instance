/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("VTEForecast_AddChildren_Js",{

},{
    //updatedBlockSequence : {},
	registerAddChildrenEvent : function() {
		 var thisInstance = this;		
		 
		 var btnSave = jQuery('#btnSave');
		 btnSave.on('click',function(){			
				var progressIndicatorElement = jQuery.progressIndicator({
					 'position' : 'html',
					 'blockInfo' : {
						 'enabled' : true
					 }
				});				
				var parrentString = jQuery('#parrent_id').val();
				var childrenString = '';
				jQuery("input:checkbox[name=chk]:checked").each(function(){
					childrenString += "," +jQuery(this).val();
				});
				var actionParams = {
					"type":"POST",           
					"dataType":"json",
					"data" : {
						'parrent':parrentString,
						'children':childrenString,
						'module':app.getModuleName(),
						'action':'SaveAjax',						
					}
				};
				AppConnector.request(actionParams).then(
					 function(data) {
						 if(data['success']) {
							 progressIndicatorElement.progressIndicator({'mode' : 'hide'});                     
							 var params = {};
							 params.text = app.vtranslate('User has been assigned');
							 Settings_Vtiger_Index_Js.showMessage(params);    
							 window.location.href = "index.php?module=VTEForecast&parent=Settings&view=Settings&tab=hierarchy";			 
						 }
					 },
					 function(error) {
						 progressIndicatorElement.progressIndicator({'mode' : 'hide'});
						 
						 //TODO : Handle error
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