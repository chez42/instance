/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
jQuery.Class('Settings_Menu_Editor_Js', {}, {

	getContainer : function() {
		return jQuery('#listViewContent');
	},

	registerAddModule : function(container) {
		var thisInstance = this;
		container.on('click', '.menuEditorAddItem', function(e) {
			var element = jQuery(e.currentTarget);
			var params = {
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				view: 'EditAjax',
				mode: 'showAddModule',
				appname: element.data('appname')
			}
			app.helper.showProgress();
			app.request.post({data: params}).then(function(err, data){
				app.helper.hideProgress();
				app.helper.showModal(data, {cb: function(data){
					thisInstance.registerAddModulePreSaveEvents(data);
				}});
			});
		});
	},

	setSaveButtonState : function(container) {
		var appname = container.find('#appname').val();
		if(!container.find('.modulesContainer[data-appname='+appname+']').find('.addModule').length) {
			container.find('[type="submit"]').attr('disabled','disabled');
		} else {
			container.find('[type="submit"]').removeAttr('disabled');
		}
	},

	registerAddModulePreSaveEvents : function(data) {
		var self = this;
		var container = data.find('.addModuleContainer');

		container.on('click', '.addModule', function(e){
			var element = jQuery(e.currentTarget);
			element.toggleClass('selectedModule');
		});

		container.on('click', '.moduleSelection li a', function(){
			var selText = $(this).text();
			var appname = $(this).data('appname');
			$(this).parents('.btn-group').find('.dropdown-toggle').html(selText+'&nbsp;&nbsp; <span class="caret"></span>');
			container.find('.modulesContainer').addClass('hide');
			container.find('.modulesContainer[data-appname='+appname+']').removeClass('hide')
			.find('.addModule').removeClass('selectedModule');
			container.find('#appname').val(appname);
			self.setSaveButtonState(container);
		});

		self.setSaveButtonState(container);

		container.find('[type="submit"]').on('click', function(e) {
			var modulesContainer = container.find('.modulesContainer').not('.hide');
			var modules = modulesContainer.find('.addModule');
			var selectedModules = modules.filter('.selectedModule');
			if(!selectedModules.length) {
				app.helper.showAlertNotification({
					'message' : app.vtranslate('JS_PLEASE_SELECT_A_MODULE')
				});
			} else {
				jQuery(this).attr('disabled','disabled');
				var appname = container.find('#appname').val();
				var sourceModules = [];
				selectedModules.each(function(i, element) {
					var selectedModule = jQuery(element);
					sourceModules.push(selectedModule.data('module'));
				});

				if(sourceModules.length) {
					var params = {
						module: app.getModuleName(),
						parent: app.getParentModuleName(), 
						sourceModules: sourceModules,
						appname: appname,
						action: 'SaveAjax',
						mode: 'addModule'
					};
					app.helper.showProgress();
					app.request.post({data: params}).then(function(err, data) {
						app.helper.showSuccessNotification({message: app.vtranslate('JS_MODULE_ADD_SUCCESS')});
						app.helper.hideProgress();
						window.location.reload();
					});

					app.helper.hideModal();
				}
			}  
		});
	},

	registerRemoveModule : function(container) {
		container.on('click', '.menuEditorRemoveItem', function(e) {
			var element = jQuery(e.currentTarget);
			var parent = element.closest('.modules');
			var params = {
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				action: 'SaveAjax',
				mode: 'removeModule',
				sourceModule: parent.data('module'),
				appname: parent.closest('.appContainer').data('appname')
			}

			app.helper.showProgress();
			app.request.post({data: params}).then(function(err, data){
				app.helper.hideProgress();
				element.closest('.modules').fadeOut(500, function(){ 
					app.helper.showSuccessNotification({message: app.vtranslate('JS_MODULE_REMOVED')});
					jQuery(this).remove(); 
				});
			});
		});
	},

	registerSortModule : function(container) {
		var sortableElement = container.find('.sortable');
		var thisInstance = this;
		var stopSorting = false;
		var move = false;
		sortableElement.sortable({
			items: '.modules',
			'revert' : true,
			receive: function (event, ui) {
				move = true;
				if (jQuery(ui.item).hasClass("noConnect")) {
					stopSorting = true;
					jQuery(ui.sender).sortable("cancel");
				}
			},
			over : function(event, ui){
				stopSorting = false;
			},
			stop: function(e, ui) {
				var element = jQuery(ui.item);
				var parent = element.closest('.sortable');
				parent.find('.menuEditorAddItem').appendTo(parent);
				var appname = parent.data('appname');
				var moduleSequenceArray = {}
				jQuery.each(parent.find('.modules'),function(i,element) {
					moduleSequenceArray[jQuery(element).data('module')] = ++i;
				});
				var moved = move;
				if(move) {
					move = false;
				}
				if(!stopSorting) {
					thisInstance.saveSequence(moduleSequenceArray, appname, moved);
				} else {
					if(!element.hasClass('noConnect')) {
						thisInstance.saveSequence(moduleSequenceArray, appname);
					} else {
						app.helper.showErrorNotification({message: app.vtranslate('JS_MODULE_NOT_DRAGGABLE')});
					}
				}
			}
		});
		sortableElement.disableSelection();
	},

	saveSequence : function(moduleSequenceArray, appname, move) {
		var params = {
			module: app.getModuleName(),
			parent: app.getParentModuleName(),
			action: 'SaveAjax',
			mode: 'saveSequence',
			sequence: JSON.stringify(moduleSequenceArray),
			appname: appname
		}

		app.helper.showProgress();
		app.request.post({data: params}).then(function(err, data){
			if(move) {
				app.helper.showSuccessNotification({message: app.vtranslate('JS_MODULE_MOVED_SUCCESSFULLY')});
			} else {
				app.helper.showSuccessNotification({message: app.vtranslate('JS_MODULE_SEQUENCE_SAVED')})
			}
			app.helper.hideProgress();
			app.event.trigger('POST.MENU.MOVE', params);
		});
	},

	registerEvents : function() {
		var container = this.getContainer();
		this.registerAddModule(container);
		this.registerRemoveModule(container);
		this.registerSortModule(container);
		this.makeAPPSortable(container);
		this.registerAddMenuEvent(container);
		this.registerRemoveMenu(container);
		this.registerEditMenuEvent(container);
		var instance = new Settings_Vtiger_Index_Js();
		instance.registerBasicSettingsEvents();
	},
	
	updatedAPPSequence: {},
	
	 /**
	 * Function to regiser the event to make the APP sortable
	 */
	makeAPPSortable: function (container) {
		
		var thisInstance = this;
		var contents = container;
		var table = contents.find('.appSortable');
		contents.sortable({
			'containment': 'parent',
			'items': table,
			'revert': true,
			'tolerance': 'pointer',
			'cursor': 'move',
			'update': function (e, ui) {
				thisInstance.updateAPPSequence(container);
			}
		});
	},
	/**
	 * Function which will update app sequence
	 */
	updateAPPSequence: function (container) {
		var thisInstance = this;
		app.helper.showProgress();

		var sequence = JSON.stringify(thisInstance.updateAPPListByOrder(container));
		var params = {};
		params['module'] = app.getModuleName();
		params['parent'] = app.getParentModuleName();
		params['action'] = 'SaveAjax';
		params['mode'] = 'updateAPPSequenceNumber';
		params['sequence'] = sequence;
		
		app.request.post({'data': params}).then(
			function (err, data) {
				app.helper.hideProgress();
				if (err === null) {
					app.helper.showSuccessNotification({'message': app.vtranslate('Apps Sequence Updated')});
				} else {
					app.helper.showErrorNotification({'message': err.message});
				}
			});
	},
	
	/**
	 * Function which will arrange the sequence number of app
	 */
	updateAPPListByOrder: function (container) {
		var thisInstance = this;
		var contents = container;
		contents.find('.appSortable').each(function (index, domElement) {
			var blockTable = jQuery(domElement);
			var blockId = blockTable.data('app');
			var actualBlockSequence = blockTable.data('sequence');
			var expectedBlockSequence = (index+1);

			if (expectedBlockSequence != actualBlockSequence) {
				blockTable.data('sequence', expectedBlockSequence);
			}
			thisInstance.updatedAPPSequence[blockId] = expectedBlockSequence;
		});
		return thisInstance.updatedAPPSequence;
	},
	
	/**
	 * Function used to display the new App after save
	 */
	displayNewMenu: function (container, result) {
		var thisInstance = this;
		var contents = container;
		var beforeBlock = contents.find('.appSortable').last();
		
		var newMenuCopy = contents.find('.newAppCopy').clone(true, true);
		newMenuCopy.find('.menuName').find('.textOverflowEllipsis').append(result['appName']);
		newMenuCopy.find('.menuName').find('.textOverflowEllipsis').attr('title',result['appName']);
		newMenuCopy.find('.menuName').attr('data-app-name',result['appName']);
		newMenuCopy.find('.menuName').find('.fa').addClass(result['icon']);
		beforeBlock.after(newMenuCopy.removeClass('hide newAppCopy').addClass('appSortable'));
		newMenuCopy.attr('data-app', result['appName']);
		newMenuCopy.css('margin-top',"15px");
		newMenuCopy.css('margin-left',"5px");
		newMenuCopy.find('.menuName').css('background',result['color']);
		newMenuCopy.attr('data-color', result['color']);
		newMenuCopy.attr('data-icon', result['icon']);
		newMenuCopy.attr('data-sequence', result['sequence']);
		newMenuCopy.find('.sortable').attr('data-appname', result['appName']);
		newMenuCopy.find('.sortable').find('.menuEditorModuleItem').attr('data-appname', result['appName']);
		newMenuCopy.find('.menuEditItem').attr('data-appname', result['appName']);
		newMenuCopy.find('.menuRemoveItem').attr('data-appname', result['appName']);
		thisInstance.makeAPPSortable(container);
	},
	
	registerEventForEditAppColor : function(container) {
		var thisInstance = this;
		var parentElement = container.find('[name="color_code"]');
		
		parentElement.on('click', function(e) {
			parentElement.ColorPicker({
				onSubmit: function(hsb, hex, rgb, el) {
					$(el).val("#"+hex);
					$(el).ColorPickerHide();
				},
				onBeforeShow: function () {
					$(this).ColorPickerSetColor(this.value);
				}
			})
			.bind('keyup', function(){
				$(this).ColorPickerSetColor(this.value);
			});
		})
	},
	
	/**
	 * Function to register click event for add Menu button
	 */
	registerAddMenuEvent: function (container) {
		var thisInstance = this;
		var contents = container;
		contents.find('.addApp').click(function (e) {
			var addAppContainer = contents.find('.addAppModal').clone(true, true);
			var callBackFunction = function (data) {
				data.find('.addAppModal').removeClass('hide');
				var form = data.find('.addMenuForm');
				thisInstance.registerEventForEditAppColor(form);
				var fieldLabel = form.find('[name="label"]');
				var params = {
					submitHandler: function (form) {
						var form = jQuery(form);
						var blockLabelValue = jQuery.trim(fieldLabel.val());
						var specialChars = /[&\<\>\:\'\"\,\_\-]/;
						if (specialChars.test(blockLabelValue)) {
							var errorInfo = app.vtranslate('JS_SPECIAL_CHARACTERS')+" & < > ' \" : , _ - "+app.vtranslate('JS_NOT_ALLOWED');
							vtUtils.showValidationMessage(fieldLabel, errorInfo, {
								position: {
									my: 'bottom left',
									at: 'top left',
									container: form
								}
							});
							return false;
						}
						var formData = form.serializeFormData();
						app.helper.showProgress();
						var params = formData;
						params['module'] = app.getModuleName();
						params['parent'] = app.getParentModuleName();
						params['action'] = 'SaveAjax';
						params['mode'] = 'saveMenu';
						app.request.post({'data': params}).then(function (err, data) {
							app.helper.hideProgress();
							var params = {};
							if (data.success) {
								var result = data;
								thisInstance.displayNewMenu(container, result);
								params['message'] = data.message;
								app.helper.showSuccessNotification(params);
							} else {
								params['message'] = data.message;
								app.helper.showErrorNotification(params);
							}
						});
						app.helper.hideModal();
						
					}
				};
				form.vtValidate(params);
			};
			var modalParams = {
				cb: callBackFunction
			};
			app.helper.showModal(addAppContainer, modalParams);
		});
	},
	
	registerRemoveMenu : function(container) {
		var thisInstance = this;
		container.on('click', '.menuRemoveItem', function(e) {
			var element = jQuery(e.currentTarget);
			var parent = element.closest('.menuContainer');
			var params = {
				module: app.getModuleName(),
				parent: app.getParentModuleName(),
				action: 'SaveAjax',
				mode: 'removeMenu',
				appname: parent.data('app')
			}

			app.helper.showProgress();
			app.request.post({data: params}).then(function(err, data){
				app.helper.hideProgress();
				if(data.success){
					element.closest('.menuContainer').fadeOut(500, function(){ 
						app.helper.showSuccessNotification({message: app.vtranslate('Menu removed successfully')});
						jQuery(this).remove(); 
					});
				}else{
					app.helper.showErrorNotification({message: app.vtranslate('Unable to remove menu')});
				}
			});
		});
	},
	
	/**
	 * Function to register click event for edit Menu button
	 */
	registerEditMenuEvent: function (container) {
		var thisInstance = this;
		var contents = container;
		contents.find('.menuEditItem').click(function (e) {
			var element = jQuery(e.currentTarget);
			var parent = element.closest('.menuContainer');
			var addAppContainer = contents.find('.addAppModal').clone(true, true);
			var callBackFunction = function (data) {
				data.find('.addAppModal').removeClass('hide');
				var form = data.find('.addMenuForm');
				thisInstance.registerEventForEditAppColor(form);
				form.find('[name="label"]').val(parent.data('app'));
				form.find('[name="color_code"]').val(parent.data('color'));
				form.find('[name="icon"]').val(parent.data('icon'));
				var fieldLabel = form.find('[name="label"]');
				var params = {
					submitHandler: function (form) {
						var form = jQuery(form);
						var blockLabelValue = jQuery.trim(fieldLabel.val());
						var specialChars = /[&\<\>\:\'\"\,\_\-]/;
						if (specialChars.test(blockLabelValue)) {
							var errorInfo = app.vtranslate('JS_SPECIAL_CHARACTERS')+" & < > ' \" : , _ - "+app.vtranslate('JS_NOT_ALLOWED');
							vtUtils.showValidationMessage(fieldLabel, errorInfo, {
								position: {
									my: 'bottom left',
									at: 'top left',
									container: form
								}
							});
							return false;
						}
						var formData = form.serializeFormData();
						app.helper.showProgress();
						var params = formData;
						params['module'] = app.getModuleName();
						params['parent'] = app.getParentModuleName();
						params['action'] = 'SaveAjax';
						params['mode'] = 'editMenu';
						params['appName'] = parent.data('app');
						app.request.post({'data': params}).then(function (err, data) {
							app.helper.hideProgress();
							var params = {};
							if (data.success) {
								var result = data;
								thisInstance.displayEditMenu(parent, result);
								params['message'] = data.message;
								app.helper.showSuccessNotification(params);
							} else {
								params['message'] = data.message;
								app.helper.showErrorNotification(params);
							}
						});
						app.helper.hideModal();
						
					}
				};
				form.vtValidate(params);
			};
			var modalParams = {
				cb: callBackFunction
			};
			app.helper.showModal(addAppContainer, modalParams);
		});
	},
	
	/**
	 * Function used to display the Edit App after save
	 */
	displayEditMenu: function (container, result) {
		var thisInstance = this;
		var newMenuEdit = container.closest('.menuContainer');
		newMenuEdit.find('.menuName').find('.textOverflowEllipsis').text(result['appName']);
		newMenuEdit.find('.menuName').find('.textOverflowEllipsis').attr('title',result['appName']);
		newMenuEdit.find('.menuName').attr('data-app-name',result['appName']);
		newMenuEdit.find('.menuName').find('.fa').removeClass(newMenuEdit.data('icon')).addClass(result['icon']);
		newMenuEdit.attr('data-app', result['appName']);
		newMenuEdit.find('.menuName').css('background',result['color']);
		newMenuEdit.attr('data-color', result['color']);
		newMenuEdit.attr('data-icon', result['icon']);
		newMenuEdit.find('.sortable').attr('data-appname', result['appName']);
		newMenuEdit.find('.sortable').find('.menuEditorModuleItem').attr('data-appname', result['appName']);
		newMenuEdit.find('.menuEditItem').attr('data-appname', result['appName']);
		newMenuEdit.find('.menuRemoveItem').attr('data-appname', result['appName']);
		
	},
	
});

window.onload = function() {
	var settingMenuEditorInstance = new Settings_Menu_Editor_Js();
	settingMenuEditorInstance.registerEvents();
};