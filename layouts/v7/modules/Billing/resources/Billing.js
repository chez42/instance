Vtiger.Class("Billing_Js",{
	
	triggerBillingReportPdf: function(url){
		
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
		
	},
	
	
},{});