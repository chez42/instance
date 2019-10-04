Vtiger_List_Js("Vtiger_GraphFilterList_Js", {
	
	getInstance: function(){
		if(Vtiger_GraphFilterList_Js.listInstance == false){
			var instance = new window['Vtiger_GraphFilterList_Js']();
			Vtiger_GraphFilterList_Js.listInstance = instance;
			return instance;
		}
		return Vtiger_GraphFilterList_Js.listInstance;
	},
	
	triggerMassAction: function (massActionUrl) {

		var listInstance = Vtiger_GraphFilterList_Js.getInstance();
		var listSelectParams = listInstance.getListSelectAllParams(true);
		if (listSelectParams) {
			var postData = listInstance.getDefaultParams();
			delete postData.module;
			delete postData.view;
			delete postData.parent;
			var data = app.convertUrlToDataParams(massActionUrl);
			postData = jQuery.extend(postData, data);
			postData = jQuery.extend(postData, listSelectParams);
			app.helper.showProgress();
			app.request.get({'data': postData}).then(
					function (err, data) {
						app.helper.hideProgress();
						if (data) {
							app.helper.showModal(data, {'cb': function (modal) {
									if (postData.mode === "showAddCommentForm") {
										var vtigerInstance = Vtiger_Index_Js.getInstance();
										vtigerInstance.registerMultiUpload();
									}
									app.event.trigger('post.listViewMassAction.loaded', modal);
								}
							});
						}
					}
			);
		} else {
			listInstance.noRecordSelectedAlert();
		}
	},

	triggerMassEdit: function (url) {
		
		var listInstance = Vtiger_GraphFilterList_Js.getInstance();
		
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
	},

	massDeleteRecords: function (url, instance) {
		var listInstance = Vtiger_GraphFilterList_Js.getInstance();
		listInstance.performMassDeleteRecords(url);
	},
	
	
	/*
	 * function to trigger send Email
	 * @params: send email url , module name.
	 */
	triggerSendEmail: function (massActionUrl, module, params) {
		var listInstance = Vtiger_GraphFilterList_Js.getInstance();
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
			Vtiger_Index_Js.showComposeEmailPopup(postData);
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
	},
	
	triggerTransferOwnership: function (massActionUrl) {
		var listInstance = Vtiger_GraphFilterList_Js.getInstance();
		var listSelectParams = listInstance.getListSelectAllParams();
		if (listSelectParams) {
			app.helper.showProgress();
			app.request.get({'url': massActionUrl}).then(
					function (error, data) {
						app.helper.hideProgress();
						if (data) {
							var callback = function (data) {
								var chagneOwnerForm = jQuery('#changeOwner');
								chagneOwnerForm.vtValidate({
									submitHandler: function (form) {
										listInstance.transferOwnershipSave(jQuery(form));
										return false;
									}
								});
							}
							var params = {};
							params.cb = callback
							app.helper.showModal(data, params);
						}
					}
			);
		} else {
			listInstance.noRecordSelectedAlert();
		}
	},

	transferOwnershipSave: function (form) {
		var listInstance = Vtiger_GraphFilterList_Js.getInstance();
		var listSelectParams = listInstance.getListSelectAllParams(false);
		if (listSelectParams) {
			var formData = form.serializeFormData();
			var data = jQuery.extend(formData, listSelectParams);
			app.helper.showProgress();
			app.request.post({'data': data}).then(function (err, data) {
				app.helper.hideProgress();
				if (err == null) {
					jQuery('.vt-notification').remove();
					app.helper.hideModal();
					listInstance.loadListViewRecords().then(function (e) {
						listInstance.clearList();
						app.helper.showSuccessNotification({message: app.vtranslate('JS_RECORDS_TRANSFERRED_SUCCESSFULLY')});
					});
				} else {
					app.event.trigger('post.save.failed', err);
					jQuery(form).find("button[name='saveButton']").removeAttr('disabled');
				}
			});
		}
	},
},{
	
	getDefaultParams: function () {
		var container = this.getListViewContainer();
		var pageNumber = container.find('#pageNumber').val();
		var module = this.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = container.find('[name="orderBy"]').val();
		var sortOrder = container.find('[name="sortOrder"]').val();
		var appName = container.find('#appName').val();
		var view = app.getViewName();
		
		if(typeof view == 'undefined' || view == '')
			view = 'GraphFilterList';
		
		var params = {
			'module': module,
			'parent': parent,
			'page': pageNumber,
			'view': view,
			'viewname': cvId,
			'orderby': orderBy,
			'sortorder': sortOrder,
			'app': appName
		}
		params.search_params = JSON.stringify(this.getListSearchParams());
		params.tag_params = JSON.stringify(this.getListTagParams());
		params.nolistcache = (container.find('#noFilterCache').val() == 1) ? 1 : 0;
		params.starFilterMode = container.find('.starFilter li.active a').data('type');
		params.list_headers = container.find('[name="list_headers"]').val();
		params.tag = container.find('[name="tag"]').val();
		return params;
	},
	
	
	
	getListSearchParams : function(){
		return JSON.parse(jQuery("#search_params").val());
	},
	
	getCurrentCvId : function(){
		return jQuery('#customFilter').val();
	},
	
	

	
	
	registerEvents : function(){
		this._super();
	}
});
