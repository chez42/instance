Vtiger_Detail_Js("ModSecurities_Detail_Js",{
	
	syncFromYahooFinance : function(symbol){
		
		var element = jQuery('<div></div>');
		
		app.helper.showProgress();
		var recordId =  jQuery('#recordId').val();
		var moduleName = app.getModuleName();
			
		var params = {
			action : 'syncFromYahooFinance',
			record : recordId,
			module : moduleName,
		};

		AppConnector.request(params).then(
			function(data) {
				
				app.helper.hideProgress();
				if(data){
					var response = data.result;
					var message = response.message;
					if(response.success){
						 app.helper.showSuccessNotification({message:app.vtranslate(message)});
						 jQuery('li[data-label-key="Security Details"]').trigger('click');
					} else {
						app.helper.showErrorNotification({'message': app.vtranslate(message)})
					}
				}
			}
		);
						
	}
},{
});
	
