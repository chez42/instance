/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("PortfolioInformation_List_Js", {
	
	ReCalculate : function() {
    	var self = app.controller();
        self.ReCalculate();
    },

	
}, {
	
	ReCalculate : function() {
        
		var listInstance = this;
		
		var params = {};
		
		var url = 'index.php?module=PortfolioInformation&action=ReCalculate';
		
		var paramArray = url.slice(url.indexOf('?') + 1).split('&');
		
		for (var i = 0; i < paramArray.length; i++) {
			var param = paramArray[i].split('=');
			params[param[0]] = param[1];
		}
		
		var listSelectParams = listInstance.getListSelectAllParams(true);
		
		listSelectParams = jQuery.extend(listSelectParams, params);
		
		if (listSelectParams) {
			
			listSelectParams['module'] = app.getModuleName();
			
			listSelectParams['action'] = 'ReCalculate';
			
			listSelectParams['search_params'] = JSON.stringify(listInstance.getListSearchParams());
			
			app.helper.showProgress();
			
			app.request.post({data: listSelectParams}).then(function (error, result) {
				app.helper.hideProgress();
				if(!error){
					listInstance.loadListViewRecords().then(function (e) {
						listInstance.clearList();
						app.helper.showSuccessNotification({message: 'Portfolios ReCalculate Successfully'});
					});
				} else {
					app.helper.showSuccessNotification({message: 'Error!!'});
				}
            });
			
			
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
	 
});