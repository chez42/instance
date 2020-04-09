/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("HelpDesk_List_Js", {
	
	triggerExportTimeSheetAction: function (exportActionUrl) {
		var listInstance = window.app.controller();
		listInstance.performExportTimeSheetAction(exportActionUrl);
	},
	
}, {

	performExportTimeSheetAction: function (url) {
		var listInstance = this;
		var listViewContainer = this.getListViewContainer();
		var pageNumber = listViewContainer.find('#pageNumber').val();
		var postData = listInstance.getDefaultParams();

		var params = app.convertUrlToDataParams(url);
		postData = jQuery.extend(postData, params);
		var listSelectAllParams = listInstance.getListSelectAllParams(true);
		listSelectAllParams['search_params'] = JSON.stringify(listInstance.getListSearchParams());
		postData = jQuery.extend(postData, listSelectAllParams);

		app.helper.showProgress();
		app.request.get({data: postData}).then(function (error, data) {
			app.helper.loadPageContentOverlay(data).then(function (container) {
				container.find('form#exportForm').on('submit', function () {
					jQuery(this).find('button[type="submit"]').attr('disabled', 'disabled');
					app.helper.hidePageContentOverlay();
				});
			});
			app.helper.hideProgress();
		});
	},
});