/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Settings_Users_MsExchange_Js",{},{
    
	
	registerEventForMsExchange : function(){
		$(document).on('click', '.syncNow', function(e) {
			var module = $(this).data('module');
            var params = {
                module : 'MSExchange',
                view : 'Sync',
                source_module : module
            }
            app.helper.showProgress();
            app.request.post({data: params}).then(function(error, data){
                app.helper.hideProgress();
				if(data.success){
					app.helper.showSuccessNotification({"message":'Sync Successfully'});
                } else {
                	app.helper.showErrorNotification({message : data.error});
				}
            });
        });
		
		$(document).on("click", ".revokeMSAccount", function(e){
			var module = $(this).data('module');
			var params = {
				module : 'MSExchange',
				view : 'List',
				operation : 'deleteSync',
				sourcemodule : module
			};
			app.helper.showProgress();
			app.request.post({data: params}).then(function(error, data){
				app.helper.hideProgress();
				window.location.reload();
			});       
		});
	},
	
	
	registerEventForSubmitMsExchnageForm : function(){
		
		$('.MsExchangeSettingEditViewSave').on('click', function(e){
			e.preventDefault();
			
			if($('[name="user_principal_name"]').val()){
				
				var params = {
						'module': app.getModuleName(),
						'action' : "CheckExchange",
						'record' : app.getRecordId(),
						'user_principal_name' : $('[name="user_principal_name"]').val(),
					}
				app.helper.showProgress();
				app.request.post({data:params}).then(
					function(err,data) {
						if(data){
							if(data.success){
								$('.MsExchangeSettingEditView').submit();
								app.helper.hideProgress();
							}else{
								app.helper.hideProgress();
								app.helper.showErrorNotification({
									title:app.vtranslate(data.message),
									message :app.vtranslate(data.error)+' For MsExchange'
								});
							}
						}
					}
				);
			}
		});
		
	},
	
	/**
	 * register Events for my preference
	 */
	registerEvents : function(){
		this.registerEventForMsExchange();
		this.registerEventForSubmitMsExchnageForm();
	},

	
});

jQuery(document).ready(function(){
	
	var instance = new Settings_Users_MsExchange_Js();
	instance.registerEvents();
	
})