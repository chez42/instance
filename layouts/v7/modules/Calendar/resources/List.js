/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


Vtiger_List_Js("Calendar_List_Js", {
	
	triggerCalendarMassEdit : function(url) {
		var listInstance = window.app.controller();
		var selectedRecordCount = listInstance.getSelectedRecordCount();
		if (selectedRecordCount > 500) {
			app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
			return;
		}
		app.event.trigger('post.listViewMassEdit.click', url);
		var params = listInstance.getListSelectAllParams(true);
		if (params) {
			app.helper.showProgress();
			app.request.get({url: url, data: params}).then(function (error, data) {
				var overlayParams = {'backdrop': 'static', 'keyboard': false};
				app.helper.loadPageContentOverlay(data, overlayParams).then(function (container) {
					app.event.trigger('post.listViewMassEdit.loaded', container);
				})
				app.helper.hideProgress();
			});
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
	},
	triggerMassEdit : function(url) {
		var listInstance = window.app.controller();
		
		if(!app.getAdminUser()){
			var selectedRecordCount = listInstance.getSelectedRecordCount();
			if (selectedRecordCount > 500) {
				app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
				return;
			}
		}
		app.event.trigger('post.listViewMassEdit.click', url);
		var params = listInstance.getListSelectAllParams(true);
		if (params) {
			
			app.helper.showProgress();
			app.request.get({url: url, data: params}).then(function (error, data) {
				var overlayParams = {'backdrop': 'static', 'keyboard': false};
				app.helper.loadPageContentOverlay(data, overlayParams).then(function (container) {
					app.event.trigger('post.listViewMassEdit.loaded', container);
				})
				app.helper.hideProgress();
			});
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
	}
}, {

	registerDeleteRecordClickEvent :function() {
		var thisInstance = this;

		jQuery('#page').on('click', '.deleteRecordButton', function(e){
			var elem = jQuery(e.currentTarget);
			var originalDropDownMenu = elem.closest('.dropdown-menu').data('original-menu');
			var parent = app.helper.getDropDownmenuParent(originalDropDownMenu);
			var row  = parent.closest('tr');
			var recordId = row.data('id');
			var isRecurringEnabled = row.data('recurringEnabled');
			if(isRecurringEnabled === 1) {
				app.helper.showConfirmationForRepeatEvents().then(function(postData) {
					thisInstance._deleteRecord(recordId,postData);
				});
			} else {
				thisInstance.deleteRecord(recordId);
			}
			e.stopPropagation();
		});
	},

	/*registerRowDoubleClickEvent: function () {
		return true;
	},*/
	
	validateAndSaveInlineEdit: function (currentTrElement) {
		var listViewContainer = this.getListViewContainer();
		var thisInstance = this;
		var tdElements = jQuery('.listViewEntryValue', currentTrElement);
		var record = currentTrElement.data('id');
		var values = {};
		for (var i = 0; i < tdElements.length; i++) {
			var tdElement = jQuery(tdElements[i]);
			var newValueElement = jQuery('.inputElement', tdElement);
			var fieldName = tdElement.data("name");
			values[fieldName] = thisInstance.getInlineEditedFieldValue(tdElement, newValueElement);
		}
		if(currentTrElement.find('[name="time_end"]').length)
			values['time_end'] = currentTrElement.find('[name="time_end"]').val();
		if(currentTrElement.find('[name="time_start"]').length)
			values['time_start'] = currentTrElement.find('[name="time_start"]').val();
		
		var params = {
			'ignore': ".listSearchContributor,input[type='hidden']",
			submitHandler: function (form) {
				// NOTE : hack added, submit was getting triggered for 2nd and 3rd click on save, need to debug this.
				if (this.numberOfInvalids() > 0) {
					this.form();
					return false;
				}
				var params = {
					'module': thisInstance.getModuleName(),
					'action': 'SaveAjax',
					'record': record
				};
				var params = jQuery.extend(values, params);
				app.helper.showProgress();
				jQuery('.inline-save', currentTrElement).find('button').attr('disabled', 'disabled');
				app.request.post({data: params}).then(function (err, result) {
					if (result) {
						jQuery('.inline-save', currentTrElement).find('button').removeAttr('disabled');
						var params = {};
						thisInstance.loadListViewRecords(params).then(function (data) {
							thisInstance.toggleInlineEdit(currentTrElement);
							app.helper.hideProgress();
							app.helper.showSuccessNotification({"message": ''});
							//Register Event to show quick preview for reference field.
							app.event.trigger('onclick.referenceField.quickPreview', currentTrElement);
						});
					} else {
						app.helper.hideProgress();
						app.helper.showErrorNotification({"message": err});
						jQuery('.inline-save', currentTrElement).find('button').removeAttr('disabled');
						return false;
					}
				});
				return false;  // blocks regular submit since you have ajax
			}
		};
		validateAndSubmitForm(listViewContainer.find('#listedit'), params);
	},
	
	registerEvents: function() {
		this._super();
	}
});