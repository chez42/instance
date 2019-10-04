/*********************************************************************************
 * The content of this file is subject to the PDF Maker license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
Vtiger_List_Js("Emails_List_Js",{
	
	triggerResendEmail: function (massActionUrl, module, params) {
		var listInstance = window.app.controller();
		var currentModule = "Emails";
		app.helper.showProgress();
		app.helper.checkServerConfig(currentModule).then(function(data){
			if(data == true){
				var listSelectParams = listInstance.getListSelectAllParams(false);
				if (listSelectParams) {
					var postData = listInstance.getDefaultParams();
					delete postData.module;
					delete postData.view;
					delete postData.parent;
					jQuery.extend(postData, listSelectParams);
					var data = app.convertUrlToDataParams(massActionUrl);
					jQuery.extend(postData, data);
					if (params) {
						jQuery.extend(postData, params);
					}
					app.request.post({data:postData}).then(function(err,data){
						if(err === null){
							app.helper.showModal(data);
						}
					});
				}
				else {
					listInstance.noRecordSelectedAlert();
				}
			} else {
				app.helper.showAlertBox({'message':app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION')});
			}
			app.helper.hideProgress();
		});
	},
	
},{
	
	
	
	
})

