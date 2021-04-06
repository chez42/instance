/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("Group_List_Js", {
	
	triggerGroupBillingReportPdf: function(url){
		
		var listInstance = window.app.controller();
		
		//if(!app.getAdminUser()){
			var selectedRecordCount = listInstance.getSelectedRecordCount();
			if (selectedRecordCount > 500) {
				app.helper.showErrorNotification({message: app.vtranslate('Please select Max 500 records')});
				return;
			}
		//}
		
		var params = listInstance.getListSelectAllParams(true);
		
		if (params) {
			app.helper.showProgress();
			app.request.get({url: url, data: params}).then(function (error, data) {
				app.helper.hideProgress();
				if (!error) {
					window.location.href = data.link;
				}
			});
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
		
	},
	
}, {
	 
});