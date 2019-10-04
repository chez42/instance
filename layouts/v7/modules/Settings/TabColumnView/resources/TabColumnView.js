/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class('Settings_TabColumnView_Js', {
}, {
	
	removeModulesArray: false,
	updatedBlocksList: [],
	blockNamesList: [],
	updatedBlockSequence: {},
	getModuleName: function () {
		return 'TabColumnView';
	},
	
	/**
	 * Function to create the array of block names list
	 */
	setBlocksListArray: function (form) {
		var thisInstance = this;
		thisInstance.blockNamesList = [];
		var blocksListSelect = form.find('[name="beforeBlockId"]');
		blocksListSelect.find('option').each(function (index, ele) {
			var option = jQuery(ele);
			var label = option.data('label');
			thisInstance.blockNamesList.push(label);
		})
	},
	
	/**
	 * Function used to display the new custom block ui after save
	 */
	displayNewTab: function (result) {
		var thisInstance = this;
		var contents = jQuery('#tabColumnViewContainer').find('.contents');
		var beforeBlock = contents.find('.ui-droppable').last();
		
		var newTabCopy = contents.find('.newTabCopy').clone(true, true);
		newTabCopy.find('.blockLabel').attr('title',result['tabName'])
		newTabCopy.find('.blockLabel').find('.translatedBlockLabel').append(result['tabName'].substr(0, 6)+'...');
		beforeBlock.after(newTabCopy.removeClass('hide newTabCopy').addClass('editFieldsTable ui-droppable block_'+result['tabId']));
		newTabCopy.attr('data-block', result['tabId']);
		newTabCopy.find('.deleteTab').attr('data-tabid', result['tabId']);
		newTabCopy.height(beforeBlock.innerHeight());
		newTabCopy.css('margin-top',"15px");
		newTabCopy.css('margin-left',"5px");
		newTabCopy.attr('data-sequence', result['sequence']);
		newTabCopy.find('.connectedSortable').addClass('tabModules');
		
	},
	
	/**
	 * Function to register click event for add custom block button
	 */
	registerAddTabEvent: function () {
		var thisInstance = this;
		var contents = jQuery('#tabColumnViewContainer').find('.contents');
		contents.find('.addTab').click(function (e) {
			var addTabContainer = contents.find('.addTabModal').clone(true, true);
			var callBackFunction = function (data) {
				data.find('.addTabModal').removeClass('hide');
				var form = data.find('.addTabForm');
				thisInstance.setBlocksListArray(form);
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
						if (jQuery.inArray(blockLabelValue, thisInstance.blockNamesList) == -1) {
							app.helper.showProgress();
							var params = formData;
							params['module'] = thisInstance.getModuleName();
							params['parent'] = app.getParentModuleName();
							params['sourceModule'] = jQuery('#selectedModuleName').val();
							params['action'] = 'ActionAjax';
							params['mode'] = 'save';
							app.request.post({'data': params}).then(function (err, data) {
								app.helper.hideProgress();
								
								var params = {};
								if (data.success) {
									var result = data;
									thisInstance.displayNewTab(result);
									thisInstance.makeTabSortable();
									thisInstance.makeBlockListSortable();
									params['message'] = data.message;
									app.helper.showSuccessNotification(params);
								} else {
									params['message'] = data.message;
									app.helper.showErrorNotification(params);
								}
							});
							app.helper.hideModal();
						} else {
							var result = app.vtranslate('JS_BLOCK_NAME_EXISTS');
							vtUtils.showValidationMessage(fieldLabel, result, {
								position: {
									my: 'bottom left',
									at: 'top left',
									container: form
								}
							});
							e.preventDefault();
							return;
						}
					}
				};
				form.vtValidate(params);
			};
			var modalParams = {
				cb: callBackFunction
			};
			app.helper.showModal(addTabContainer, modalParams);
		});
	},
	
	
	/**
	 * Function to regiser the event to make the blocks sortable
	 */
	makeBlockListSortable: function () {
		var thisInstance = this;
		var contents = jQuery('#tabColumnViewContainer').find('.contents');
		var table = contents.find('.tabModules').not('.nonTabModules');
		table.sortable({
			'cancel': '.disable-sort',
			'revert': true,
			'tolerance': 'pointer',
			'cursor': 'move',
			'connectWith': '.connectedSortable',
			'helper': "clone",
			'appendTo':document.body,
			'start': function (event, ui) {
				before = '';
				clone = '';
				if($(ui.item).hasClass('mainBlock')){
		            $(ui.item).show();
		            clone = $(ui.item).clone();
		            before = $(ui.item).prev();
		            parent = $(ui.item).parent();
				}

	        },
	        'receive': function (event, ui) {
	        	if(clone){
		            if (before.length) before.after(clone);
		            else parent.prepend(clone);
	        	}
	        },
			'update': function (e, ui) {
				var currentField = ui['item'];
				if(currentField.hasClass('mainBlock')){
					currentField.removeClass('mainBlock')
					var item = currentField.removeClass('col-sm-2').addClass('col-sm-11');
					item.find('.actions').hide();
					item.find('.blockLabel').removeClass('col-sm-8').addClass('col-sm-12 marginBottom10px');
					var title = item.find('.blockLabel').attr('title');
					item.find('.blockLabel').find('.translatedBlockLabel').text(title.substr(0, 15)+'...');
					clone.addClass('disable-sort');
					//clone.find('.blockLabel').addClass('block-tooltip');
				}
				
				var Tab = $(this).closest('.ui-droppable').data('block');
				var blockId = currentField.data('blockId');
				
				thisInstance.updatedBlocksList.push({'tabData':{'tabId':Tab,'blockId':blockId}});
				thisInstance.createUpdatedBlockFieldsList();
				thisInstance.showSaveTabViewButton();
				
			}
		});
		
	},
	
	/**
	 * Function to show the save button of fieldSequence
	 */
	showSaveTabViewButton: function () {
		var thisInstance = this;
		var layout = jQuery('#tabColumnViewContainer');
		var saveButton = layout.find('.saveViewButton');
		if (saveButton.css('opacity') == '0') {
			saveButton.css('opacity', '1');
			app.helper.showAlertNotification({'message': app.vtranslate('JS_SAVE_THE_CHANGES_TO_UPDATE_TABS')})
		}
	},
	
	/**
	 * Function to register the change event for layout editor modules list
	 */
	registerModulesChangeEvent: function () {
		var thisInstance = this;
		var container = jQuery('#tabColumnViewContainer');
		var contentsDiv = container.closest('.settingsPageDiv');

		vtUtils.showSelect2ElementView(container.find('[name="tabColumnViewModules"]'));

		container.on('change', '[name="tabColumnViewModules"]', function (e) {
			var currentTarget = jQuery(e.currentTarget);
			var selectedModule = currentTarget.val();

			if (selectedModule == '') {
				return false;
			}

			thisInstance.getModuleTabColumnView(selectedModule).then(
				function (data) {
					contentsDiv.html(data);
					thisInstance.updatedBlocksList=[];
					thisInstance.registerEvents();
				}
			);
		});

	},
	
	getModuleTabColumnView: function (selectedModule) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		app.helper.showProgress();

		var params = {};
		params['module'] = thisInstance.getModuleName();
		params['parent'] = app.getParentModuleName();
		params['view'] = 'Index';
		params['sourceModule'] = selectedModule;
		params['showFullContents'] = true;
		params['mode'] = jQuery('.selectedMode').val();

		app.request.pjax({'data': params}).then(
			function (err, data) {
				app.helper.hideProgress();
				if (err === null) {
					aDeferred.resolve(data);
				} else {
					aDeferred.reject();
				}
			});
		return aDeferred.promise();
	},
	
	/**
	 * register events for tab view
	 */
	registerEvents: function () {
		var thisInstance = this;
		thisInstance.registerModulesChangeEvent();
		thisInstance.registerEventsForCustomTabView();
		thisInstance.registerAddTabEvent();
		thisInstance.registerChangeColumnEvent();
		thisInstance.registerTabViewSaveClick();
		vtUtils.applyFieldElementsView(jQuery('#tabColumnViewContainer'));
		thisInstance.registerTabDeleteEvent();
		thisInstance.makeBlockListSortable();
	},
	
	
	/**
	 * register events for change column
	 */
	registerChangeColumnEvent: function(){
		var thisInstance = this;
		jQuery('.num_of_columns').on('change',function(){
			var blockId = $(this).data('block');
			var value = $(this).val();
			thisInstance.updatedBlocksList.push({'columnsData':{'block_id':blockId,'columns':value}});
			thisInstance.showSaveTabViewButton();
		});
	},
	
	/**
	 * Function to register click event for save button tab view
	 */
	registerTabViewSaveClick: function () {
		var thisInstance = this;
		var tabLayout = jQuery('#tabColumnViewContainer');

		tabLayout.on('click', '.saveTabView', function () {
			
			var tabData = thisInstance.updatedBlocksList;
			var newArray = $.merge([], tabData);
			
			var params = {
	            module : thisInstance.getModuleName(),
	            parent : app.getParentModuleName(),
	            'action' : 'ActionAjax',
	            'mode' : 'saveTabView',
	            'sourceModule' : jQuery('#selectedModuleName').val()
			}
			params['tabData'] = newArray;
            app.helper.showProgress('');
            app.request.post({'data': params}).then(function (err, data) {
            	app.helper.hideProgress();
            	var params = {};
				if(data.success){
					params['message'] = data.message;
					app.helper.showSuccessNotification(params);
					thisInstance.hideSaveViewButton();
					window.location.reload();
				}else{
					params['message'] = 'Failed to update the tabs.';
					app.helper.showErrorNotification(params);
				}
			});
		});
	},
	
	/**
	 * Function to register click event for delete tab
	 */
	registerTabDeleteEvent: function(){
		
		var thisInstance = this;
		var tabLayout = jQuery('#tabColumnViewContainer');
		tabLayout.on('click', '.deleteTab', function () {
			var tabid = $(this).data('tabid');
			var tabDiv = $(this).closest('.editFieldsTable');
			var params = {
	            module : thisInstance.getModuleName(),
	            parent : app.getParentModuleName(),
	            'action' : 'ActionAjax',
	            'mode' : 'deleteTab',
	            'tabid' : tabid,
	            'sourceModule' : jQuery('#selectedModuleName').val()
			}
			app.helper.showProgress('');
			app.request.post({'data': params}).then(function (err, data) {
				app.helper.hideProgress();
				var params = {};
				if (data.success) {
					params['message'] = data.message;
					app.helper.showSuccessNotification(params);
					tabDiv.remove();
				} else {
					params['message'] = data.message;
					app.helper.showErrorNotification(params);
				}
			 });
		});
	},
	
	/**
	 * Function which will hide the saveViewButton button
	 */
	hideSaveViewButton: function () {
		var tabLayout = jQuery('#tabColumnViewContainer');
		var saveButton = tabLayout.find('.saveViewButton');
		saveButton.css('opacity', '0');
	},
	
	registerShowTooltip:function(){
		var thisInstance = this;
		jQuery(document).on("hover", ".customtab-tooltip", function() {
			var html = 'To Convert ONE Block into ONE Tab - just turn the block on (set to YES).';
			thisInstance.showColumnTooltip(jQuery(this),html);
		});
	},
	
	registerShowBlockTooltip:function(){
		var thisInstance = this;
		jQuery(document).on("hover", ".block-tooltip", function() {
			var html = 'This block is already present in tabs. Please move from there.';
			thisInstance.showColumnTooltip(jQuery(this),html);
		});
	},
	
	showColumnTooltip : function(obj,html){
		var template = '<div class="popover" role="tooltip" style="background: #003366">' +
			'<style>' +
			'.popover.bottom > .arrow:after{border-bottom-color:red;2px solid #ddd}' +
			'.popover-content{font-size: 11px}' +
			'.popover-title{background: red;text-align:center;color:#f4f12e;font-weight: bold;}' +
			'.popover-content ul{padding: 5px 5px 0 10px}' +
			'.popover-content li{list-style-type: none}' +
			'.popover{border: 2px solid #ddd;z-index:99999999;color: #fff;box-shadow: 0 0 6px #000; -moz-box-shadow: 0 0 6px #000;-webkit-box-shadow: 0 0 6px #000; -o-box-shadow: 0 0 6px #000;padding: 4px 10px 4px 10px;border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; -o-border-radius: 6px;}' +
			'</style><div class="arrow">' +
			'</div>' +
			'<div class="popover-content"></div></div>';
		obj.popover({
			content: html,
			animation : true,
			placement: 'auto top',
			html: true,
			template:template,
			container: 'body',
			trigger: 'focus'
		});
		jQuery(obj).popover('show');
		jQuery(obj).on('mouseleave',function () {
			jQuery(obj).popover('hide');
		});
	},
	   
   
	registerEventForConvertToTab: function () {
		var thisInstance = this;
        var tabViewContainer = jQuery('#tabColumnViewContainer');
        jQuery(tabViewContainer).on('switchChange.bootstrapSwitch', "input[name='is_tab']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var is_tab = currentElement.val();
            var module_name = jQuery('#selectedModuleName').val()
            var params = {
                module : thisInstance.getModuleName(),
                parent : app.getParentModuleName(),
                'action' : 'ActionAjax',
                'mode' : 'switchToTab',
                'module_name' : module_name,
                'is_tab' : is_tab
            }
            app.helper.showProgress('');
            app.request.post({data:params}).then(function(error,data){
            	
                if(error === null){
                    app.helper.hideProgress();
                    if(data.success){
                        app.helper.showSuccessNotification({
                            message : data.message
                        });
                        if(data.isTab){
                        	window.location.reload();
                        }else{
	                        var tabContent = tabViewContainer.find('.convertTab');
	                        if(tabContent.hasClass("hide"))
	                        	tabContent.removeClass('hide');
	                        else if(!tabContent.hasClass("hide"))
	                        	tabContent.addClass('hide');
                    	}
                    }else{
                        
                        app.helper.showErrorNotification({'message': data.message});
                        return false;
                    }
                }else{
                	app.helper.showErrorNotification({'message': err.message});
                }
                
            });
        });
    },
    
    registerEventsForCustomTabView : function(){
    	var thisInstance = this;
    	thisInstance.registerShowTooltip();
    	thisInstance.registerShowBlockTooltip();
    	thisInstance.registerEventForConvertToTab();
		jQuery("input[name='is_tab']").bootstrapSwitch();
		thisInstance.makeTabSortable();
		
    },
    
    /**
	 * Function to regiser the event to make the tab sortable
	 */
	makeTabSortable: function () {
		var thisInstance = this;
		var contents = jQuery(document).find('#tabColumnViewContainer').find('.contents');
		var table = contents.find('.ui-droppable');
		contents.sortable({
			'containment': contents,
			'items': table,
			'revert': true,
			'tolerance': 'pointer',
			'cursor': 'move',
			'update': function (e, ui) {
				thisInstance.updateBlockSequence();
			}
		});
	},
	
	/**
	 * Function which will update block sequence
	 */
	updateBlockSequence: function () {
		var thisInstance = this;
		app.helper.showProgress();

		var sequence = JSON.stringify(thisInstance.updateBlocksListByOrder());
		
		var params = {};
		params['module'] = thisInstance.getModuleName();
		params['parent'] = app.getParentModuleName();
		params['action'] = 'ActionAjax';
		params['mode'] = 'updateSequenceNumber';
		params['sequence'] = sequence;
		params['selectedModule'] = jQuery('#selectedModuleName').attr('value');
		
		app.request.post({'data': params}).then(
			function (err, data) {
				app.helper.hideProgress();
				if (err === null) {
					app.helper.showSuccessNotification({'message': app.vtranslate('JS_Tab_SEQUENCE_UPDATED')});
				} else {
					app.helper.showErrorNotification({'message': err.message});
				}
			});
	},
	/**
	 * Function which will arrange the sequence number of blocks
	 */
	updateBlocksListByOrder: function () {
		var thisInstance = this;
		var contents = jQuery(document).find('#tabColumnViewContainer').find('.contents');
		contents.find('.ui-droppable').each(function (index, domElement) {
			var blockTable = jQuery(domElement);
			var blockId = blockTable.data('block');
			var actualBlockSequence = blockTable.data('sequence');
			var expectedBlockSequence = (index+1);

			if (expectedBlockSequence != actualBlockSequence) {
				blockTable.data('sequence', expectedBlockSequence);
			}
			thisInstance.updatedBlockSequence[blockId] = expectedBlockSequence;
		});
		return thisInstance.updatedBlockSequence;
	},
	
	/**
	 * Function to create the list of updated tab with all the blocks and their sequences
	 */
	createUpdatedBlockFieldsList: function () {
		var thisInstance = this;
		var contents = jQuery('#tabColumnViewContainer').find('.contents');
		
		for (var index in thisInstance.updatedBlocksList) {
			var updatedTabId = thisInstance.updatedBlocksList[index].tabData.tabId;
			var updatedTab = contents.find('.block_'+updatedTabId);
			var editblock = updatedTab.find('.editFieldsTable');
			var expectedFieldSequence = 1;
			editblock.each(function (i, domElement) {
				var blockEle = jQuery(domElement);
				var blockId = blockEle.data('blockId');
				thisInstance.updatedBlocksList.push({'fieldData':{'blockid': blockId, 'blockSequence': expectedFieldSequence, 'tabid': updatedTabId}});
				expectedFieldSequence = expectedFieldSequence+1;
			});
		}
	},
	    
});

Settings_TabColumnView_Js('Settings_TabColumnView_Index_Js', {}, {
	init: function () {
		this.addComponents();
	},
	addComponents: function () {
		this.addModuleSpecificComponent('Index', 'Vtiger', app.getParentModuleName());
	}
});
