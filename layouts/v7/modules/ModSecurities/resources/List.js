/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("ModSecurities_List_Js", {
	
	addNewPrice : function() {
    	var self = app.controller();
        self.showNewPriceSecurityForm();
    },

	
}, {
	
	showNewPriceSecurityForm : function() {
        
		var listInstance = window.app.controller();
		
		var selectedRecordCount = listInstance.getSelectedRecordCount();
		
		if (selectedRecordCount > 1) {
			app.helper.showErrorNotification({message: 'Please select one Symbol at a Time'});
			return;
		} else if (!selectedRecordCount) {
			listInstance.noRecordSelectedAlert();
			return;
		}
		
		var listSelectParams = listInstance.getListSelectAllParams(true);
		
		listSelectParams['module'] = app.getModuleName();
		listSelectParams['view'] = 'MassActionAjax';
		listSelectParams['mode'] = 'showNewSecurityPriceForm';
		
		app.helper.showProgress();
			
		app.request.get({data: listSelectParams}).then(function (error, data) {
			app.helper.hideProgress();
			if(!error){
				var callback = function (data) {
						
					var addpriceform = jQuery("#addpriceform");
					vtUtils.applyFieldElementsView(addpriceform);
					
					addpriceform.vtValidate({
								
						submitHandler: function (form) {
						
							jQuery("button[name='saveButton']").attr("disabled","disabled");
						
							var formData = jQuery(form).serialize();
						
							app.request.post({data:formData}).then(function(err,data){
								app.helper.hideProgress();
								if(err === null) {
									app.helper.hideModal();
									app.helper.showSuccessNotification({"message":'Price Saved Successfully'},{delay:4000});
								} else {
									jQuery("button[name='saveButton']").removeAttr('disabled');
									app.helper.showErrorNotification({"message":err.message},{delay:4000});
								}
							});
						}
					});
				}
				var params = {};
				params.cb = callback;
				app.helper.showModal(data, params);
			}
		});
			
		
    },
	 
});