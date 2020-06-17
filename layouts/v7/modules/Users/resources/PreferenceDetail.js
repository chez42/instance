/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Users_Detail_Js("Settings_Users_PreferenceDetail_Js",{
	
	updatePortalPermissions : function(url) {
    	var thisInstance = window.app.controller();
		
		var params = {};
		params['record'] = app.getRecordId();
		if (params) {
			app.helper.showProgress();
			app.request.get({url: url, data: params}).then(function (error, data) {
				app.helper.hideProgress();
				if (!error) {
					app.helper.showModal(data, {
                        'cb' : function(modalContainer) {
                        	thisInstance.registerUpdatePortalPermissions(modalContainer);
                        	thisInstance.registerEnableDisablePortalReportsEvent();
                        }
                    });
				}
			});
		}
        
    },
    
},{
    
	registerUpdatePortalPermissions: function(modalContainer){
		var self = this;
		modalContainer.find('[name="saveButton"]').on('click',function(){
		
			jQuery('#defaultPortalPermission').vtValidate({
				
				submitHandler: function (form) {
					var domForm = jQuery(form);
					var formData = jQuery(form).serializeFormData();
	
					var formData = new FormData(domForm[0]);
					var params = {
						url: "index.php",
						type: "POST",
						data: formData,
						processData: false,
						contentType: false
					};
					app.helper.showProgress();
					app.request.post(params).then(function (err, data) {
						app.helper.hideProgress();
						if (!err) {
							app.helper.hideModal();
							app.helper.showSuccessNotification({message:'Portal Permimssions Saved Successfully!'});
						}
					});
					return false;
				}
			});
		});
    	
    },

    registerEnableDisablePortalReportsEvent : function(){
		
		jQuery(".mainmodule").on("click", function(e){
			e.preventDefault();
			var className = $(this).data('value');
			var check = '';
			
			jQuery("."+className).each(function(index, elem){
				if($(this). prop("checked") == false){
					check = true;
				}
			});
			jQuery("."+className).each(function(index, elem){
				if($(this). prop("checked") == true){
					check = false;
				}
			});
			
			if(check == true){
				 jQuery("."+className).each(function(index, elem){
					$(this).prop('checked', true);
				 });
			}else if(check == false){
				jQuery("."+className).each(function(index, elem){
					$(this).prop('checked', false);
				});
			}
			
		});
	},
	/**
	 * We have to load Settings Index Js but the parent module name will be empty so we are extending this api and passing 
	 * last parameter as settings (This is useful to settings side events like accordion click and settings menu search)
	*/
	addIndexComponent : function() {
            this.addModuleSpecificComponent('Index',app.getModuleName(),'Settings');
	},
    
	/**
	 * register Events for my preference
	 */
	registerEvents : function(){
		this._super();
		Settings_Users_PreferenceEdit_Js.registerChangeEventForCurrencySeparator();
		Settings_Users_PreferenceEdit_Js.registerNameFieldChangeEvent();
	}
});