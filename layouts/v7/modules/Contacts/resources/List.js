/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("Contacts_List_Js", {
    
	updatePortalPermissions : function(url) {
    	var listInstance = window.app.controller();
		
		if(!app.getAdminUser()){
			var selectedRecordCount = listInstance.getSelectedRecordCount();
			if (selectedRecordCount > 500) {
				app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
				return;
			}
		}
		
		var params = listInstance.getListSelectAllParams(true);
		if (params) {
			app.helper.showProgress();
			app.request.get({url: url, data: params}).then(function (error, data) {
				app.helper.hideProgress();
				if (!error) {
					app.helper.showModal(data, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerUpdatePortalPermissions(modalContainer);
                        }
                    });
				}
			});
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
        
    },
    
    
}, {
	 
	registerUpdatePortalPermissions: function(modalContainer){
		var self = this;
		modalContainer.find('[name="saveButton"]').on('click',function(){
		
			jQuery('#updatePortalPermission').vtValidate({
				
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
							self.loadListViewRecords();
							self.clearList();
						}
					});
					return false;
				}
			});
		});
    	
    },
   
    
});