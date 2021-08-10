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
	
	triggerGeneratePerformanceReport : function(type) {
    	var self = app.controller();
        self.showPerformanceReportForm(type);
    },
	
}, {
	
	showPerformanceReportForm : function(type) {
        
		var listInstance = window.app.controller();
		
		var selectedRecordCount = listInstance.getSelectedRecordCount();
		
		if (!selectedRecordCount) {
			listInstance.noRecordSelectedAlert();
			return;
		}
		
		var listSelectParams = listInstance.getListSelectAllParams(true);
		
		listSelectParams['module'] = app.getModuleName();
		listSelectParams['view'] = type;
		listSelectParams['mode'] = 'viewForm';
		
		app.helper.showProgress();
			
		app.request.get({data: listSelectParams}).then(function (error, data) {
			
			app.helper.hideProgress();
			
			if(!error){
				
				var callback = function (data) {
					
					var downloadReport = jQuery("#downloadReport");
					
					vtUtils.applyFieldElementsView(downloadReport);
					
					downloadReport.vtValidate({
								
						/*submitHandler: function (form) {
							
							jQuery("button[name='saveButton']").attr("disabled","disabled");
						
							var formData = jQuery(form).serialize();
							
							app.helper.showProgress();
							
							app.request.post({data:formData}).then(function(err,data){
								app.helper.hideProgress();
								app.helper.hideModal();
							});
						}*/
						
					});
				}
				
				var params = {};
				
				params.cb = callback;
				
				app.helper.showModal(data, params);
			}
		});
			
		
    },
	
	
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