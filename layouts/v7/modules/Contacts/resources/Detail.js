/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Contacts_Detail_Js", {
	
	triggerPortalResetPassword : function(url){
		
		var thisInstance = this;
		
		thisInstance.isPortalEnableAndActive().then(
			function(data){
				if(data.success){
					
					var portalInfo = data.result;
					
					if(portalInfo.isenable == '1' && portalInfo.isactive == '1'){
						app.helper.showProgress();
						var params = app.convertUrlToDataParams(url);
						
						app.request.post({data:params}).then(
							function(error, data) {
								app.helper.hideProgress();
								
								if(data) {
									app.helper.showModal(data);
									var form = jQuery('form#portal_reset_password');
									var isFormExists = form.length;
									if(isFormExists){
										thisInstance.savePortalPassword(form);
									}
								}
							}
						);
					} else {
						
						if(portalInfo.isenable != '1')
							var message = "Please enable Portal";
						else if(portalInfo.isactive != '1')
							var message = "Inactive Portal";
						
						app.helper.showErrorNotification({message:app.vtranslate(message)});
					}
				} else {
					app.helper.showErrorNotification({message:app.vtranslate("Oops! something went wrong while reset password")});
				}
			}
		);
	},
	
	savePortalPassword : function(form){
		 form.on("click","button[name='saveButton']",function(e){
			e.preventDefault();
			var rules = {};
			rules["new_password"] = {'required' : true};
			rules["confirm_password"] = {'required' : true};
			var params = {
				rules : rules,
				submitHandler: function(form) {
					// to Prevent submit if already submitted
					var new_password  = jQuery(form).find('[name="new_password"]');
					var confirm_password = jQuery(form).find('[name="confirm_password"]');
					var record = jQuery(form).find('[name="record"]').val();
					
					if(new_password.val() == confirm_password.val()){
						
						jQuery(form).find("button[name='saveButton']").attr("disabled","disabled");
						if(this.numberOfInvalids() > 0) {
							return false;
						}
						
						var reqParams = {
							'module': app.getModuleName(),
							'action' : "SavePortalPassword",
							'new_password' : new_password.val(),
							'record' : record
						};
						app.request.post({data:reqParams}).then(
							function(error,data) {
								if(error === null){
									app.helper.hideModal();
									if(data.success){
										app.helper.showSuccessNotification({message:app.vtranslate(data.message)});
									} else {
										app.helper.showErrorNotification({"message":app.vtranslate(data.message)});
									}
	                            } else {
									app.event.trigger('post.save.failed', error);
									jQuery(form).find("button[name='saveButton']").removeAttr('disabled');
								}
							}
						);
					}else {
						var errorMessage = app.vtranslate('JS_PASSWORD_MISMATCH_ERROR');
						app.helper.showErrorNotification({"message":errorMessage});
						return false;
					}
				}
			};
			validateAndSubmitForm(form,params);
		 });
	},
	
	isPortalEnableAndActive : function(){
		
		var thisInstance = this;
		
		var params = {
			module : "Contacts",
			action : "SavePortalPassword",
			mode : "isPortalEnableAndActive",
			record : jQuery('#recordId').val()
		};
		
		var aDeferred = jQuery.Deferred();
		
		AppConnector.request(params).then(
			function(data){
				aDeferred.resolve(data);
			},
			function(){
                aDeferred.reject(false);
			}
		);
        return aDeferred.promise();
	},
}, {
	registerAjaxPreSaveEvents: function (container) {
		var thisInstance = this;
		app.event.on(Vtiger_Detail_Js.PreAjaxSaveEvent, function (e) {
			if (!thisInstance.checkForPortalUser(container)) {
				e.preventDefault();
			}
		});
	},
	/**
	 * Function to check for Portal User
	 */
	checkForPortalUser: function (form) {
		var element = jQuery('[name="portal"]', form);
		var response = element.is(':checked');
		var primaryEmailField = jQuery('.fieldValue [data-name="email"]');
		var primaryEmailValue = primaryEmailField.data('value');
		if (response) {
			if (primaryEmailField.length == 0) {
				app.helper.showErrorNotification({message: app.vtranslate('JS_PRIMARY_EMAIL_FIELD_DOES_NOT_EXISTS')});
				return false;
			}
			if (primaryEmailValue == "") {
				app.helper.showErrorNotification({message: app.vtranslate('JS_PLEASE_ENTER_PRIMARY_EMAIL_VALUE_TO_ENABLE_PORTAL_USER')});
				return false;
			}
		}
		return true;
	},
	
	registerInlineEditForPortalBlock : function(){
		
		var thisInstance = this;
		$(document).on('click', '.editPortalAction', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = currentTarget.closest('td');
			var editedLength = jQuery('table.portalTable .ajaxEditPortal').length;
			if(editedLength === 0) { 
				currentTarget.addClass('hide');
				currentTdElement.find('.editPortal').removeClass('hide').addClass('ajaxEditPortal');
				currentTdElement.find('.portalFieldValue').addClass('hide');
				currentTdElement.find('.portalSaveButton').removeClass('hide');
				thisInstance.registerPortalSaveOnEnterEvent(jQuery('.editPortal',currentTdElement));
				
			}
		});
		
	},
	
	registerAjaxPortalEditCancelEvent : function(){
		var thisInstance = this;
		
		$(document).on('click','.inlinePortalAjaxCancel',function(e){
			e.preventDefault();
			e.stopPropagation();
			var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = currentTarget.closest('td');
			currentTdElement.find('.editPortalAction').removeClass('hide');
			currentTdElement.find('.editPortal').addClass('hide').removeClass('ajaxEditPortal');
			currentTdElement.find('.portalFieldValue').removeClass('hide');
			currentTdElement.find('.portalSaveButton').addClass('hide');
		});
	},
	
	registerAjaxPortalEditSaveEvent : function(){
		var thisInstance = this;

		$(document).on('click','.inlinePortalAjaxSave',function(e){
			e.preventDefault();
			e.stopPropagation();
			var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = currentTarget.closest('td'); 
			var editElement = jQuery('.editPortal',currentTdElement);
			var fieldBasicData = jQuery('.fieldBasicData', editElement);
			var fieldName = fieldBasicData.data('name');
			var previousValue = jQuery.trim(fieldBasicData.data('displayvalue'));
			
			var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			var ajaxEditNewValue = fieldElement.val();

			 // ajaxEditNewValue should be taken based on field Type
			if(fieldElement.is('input:checkbox')) {
				if(fieldElement.is(':checked')) {
					ajaxEditNewValue = '1';
				} else {
					ajaxEditNewValue = '0';
				}
				fieldElement = fieldElement.filter('[type="checkbox"]');
			}
			
			var fieldValue = ajaxEditNewValue;
			
			if(previousValue == ajaxEditNewValue) {
				currentTdElement.find('.editPortalAction').removeClass('hide');
				currentTdElement.find('.editPortal').addClass('hide').removeClass('ajaxEditPortal');
				currentTdElement.find('.portalFieldValue').removeClass('hide');
				currentTdElement.find('.portalSaveButton').addClass('hide');
			}else{
				var fieldNameValueMap = {};
				fieldNameValueMap['value'] = fieldValue;
				fieldNameValueMap['field'] = fieldName;
				jQuery(currentTdElement).find('.input-group-addon').addClass('disabled');
				app.helper.showProgress();
				thisInstance.savePortalFieldValues(fieldNameValueMap).then(function(err, response) {
					app.helper.hideProgress();
					if (err == null) {
						if(response.success){
							currentTdElement.find('.editPortalAction').removeClass('hide');
							currentTdElement.find('.editPortal').addClass('hide').removeClass('ajaxEditPortal');
							if(response.value == 1)
								var value = 'Yes';
							else if(response.value == 0)
								var value = 'No';
							currentTdElement.find('.portalFieldValue').removeClass('hide').html(value);
							fieldBasicData.attr('data-displayvalue',response.value);
							currentTdElement.find('.portalSaveButton').addClass('hide');
							jQuery(currentTdElement).find('.input-group-addon').removeClass('disabled');
						}else{
							jQuery(currentTdElement).find('.input-group-addon').removeClass('disabled');
							return true;
						}
					}
				});
			}
			
		});
		
	},
	
	savePortalFieldValues : function (fieldDetailList) {
		var aDeferred = jQuery.Deferred();
		
		var recordId = this.getRecordId();

		var data = {};
		if(typeof fieldDetailList != 'undefined'){
			data = fieldDetailList;
		}

		data['record'] = recordId;
		data['module'] = this.getModuleName();
		data['action'] = 'SavePortalPermissions';
		
		app.request.post({data:data}).then(
			function(err, reponseData){
				if(err === null){
					if(reponseData.success)
						app.helper.showSuccessNotification({"message":app.vtranslate('JS_RECORD_UPDATED')});
				}
				aDeferred.resolve(err, reponseData);
			}
		);

		return aDeferred.promise();
	},
	
	registerPortalSaveOnEnterEvent: function(editElement) {
		editElement.find('.inputElement:not(textarea)').on('keyup', function(e) {
			var textArea = editElement.find('textarea');
			var ignoreList = ['reference','picklist','multipicklist','owner'];
			var fieldType = jQuery(e.target).closest('.ajaxEditPortal').find('.fieldBasicData').data('type');
			if(ignoreList.indexOf(fieldType) !== -1) return;
			if(!textArea.length){
				(e.keyCode || e.which) === 13  && editElement.closest('td').find('.inlinePortalAjaxSave').trigger('click');
			}
		});
	},
	
	/**
	 * Ajax Edit Save Event
	 * @param {type} currentTdElement
	 * @returns {undefined}
	 */
	registerAjaxEditSaveEvent : function(contentHolder){
		var thisInstance = this;
		if(typeof contentHolder === 'undefined') {
			contentHolder = this.getContentHolder();
		}

		contentHolder.on('click','.inlineAjaxSave',function(e){
			e.preventDefault();
			e.stopPropagation();
			var currentTarget = jQuery(e.currentTarget);
			var currentTdElement = thisInstance.getInlineWrapper(currentTarget); 
			var detailViewValue = jQuery('.value',currentTdElement);
			var editElement = jQuery('.edit',currentTdElement);
			var actionElement = jQuery('.editAction', currentTdElement);
			var fieldBasicData = jQuery('.fieldBasicData', editElement);
			var fieldName = fieldBasicData.data('name');
			var fieldType = fieldBasicData.data("type");
			var previousValue = jQuery.trim(fieldBasicData.data('displayvalue'));

			var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);
			var ajaxEditNewValue = fieldElement.val();

			 // ajaxEditNewValue should be taken based on field Type
			if(fieldElement.is('input:checkbox')) {
				if(fieldElement.is(':checked')) {
					ajaxEditNewValue = '1';
				} else {
					ajaxEditNewValue = '0';
				}
				fieldElement = fieldElement.filter('[type="checkbox"]');
			} else if(fieldType == 'reference'){
				currentTdElement.prev('td').find('.referenceSelect').addClass('hide');
				if(currentTdElement.prev('td').find('.referenceSelect').length)
					currentTdElement.prev('td').find('.muted').removeClass('hide');
				ajaxEditNewValue = fieldElement.data('value');
			}

			// prev Value should be taken based on field Type
			var customHandlingFields = ['owner','ownergroup','picklist','multipicklist','reference','boolean']; 
			if(jQuery.inArray(fieldType, customHandlingFields) !== -1){
				previousValue = fieldBasicData.data('value');
			}

			// Field Specific custom Handling
			if(fieldType === 'multipicklist'){
				var multiPicklistFieldName = fieldName.split('[]');
				fieldName = multiPicklistFieldName[0];
			} 

			var fieldValue = ajaxEditNewValue;

			//Before saving ajax edit values we need to check if the value is changed then only we have to save
			if(previousValue == ajaxEditNewValue) {
				detailViewValue.css('display', 'inline-block');
				editElement.addClass('hide');
				editElement.removeClass('ajaxEdited');
				jQuery('.editAction').removeClass('hide');
				actionElement.show();
			}else{
				var fieldNameValueMap = {};
				fieldNameValueMap['value'] = fieldValue;
				fieldNameValueMap['field'] = fieldName;
				var form = currentTarget.closest('form');
				var params = {
					'ignore' : 'span.hide .inputElement,input[type="hidden"]',
					submitHandler : function(form){
						var preAjaxSaveEvent = jQuery.Event(Vtiger_Detail_Js.PreAjaxSaveEvent);
						app.event.trigger(preAjaxSaveEvent,{form:jQuery(form),triggeredFieldInfo:fieldNameValueMap});
						if(preAjaxSaveEvent.isDefaultPrevented()) {
							return false;
						}

						jQuery(currentTdElement).find('.input-group-addon').addClass('disabled');
						app.helper.showProgress();
						thisInstance.saveFieldValues(fieldNameValueMap).then(function(err, response) {
							app.helper.hideProgress();
							if (err !== null) {
								app.event.trigger('post.save.failed', err);
								jQuery(currentTdElement).find('.input-group-addon').removeClass('disabled');
								return true;
							}
							jQuery('.vt-notification').remove();
							var postSaveRecordDetails = response;
							if(fieldBasicData.data('type') == 'picklist' && app.getModuleName() != 'Users') {
								if(typeof postSaveRecordDetails[fieldName].colormap !== 'undefined') {
									var color = postSaveRecordDetails[fieldName].colormap[postSaveRecordDetails[fieldName].value];
									if(color) {
										var contrast = app.helper.getColorContrast(color);
										var textColor = (contrast === 'dark') ? 'white' : 'black';
										var picklistHtml = '<span class="picklist-color" style="background-color: ' + color + '; color: '+ textColor + ';">' +
																postSaveRecordDetails[fieldName].display_value + 
															'</span>';
									} else {
										var picklistHtml = '<span class="picklist-color">' +
																postSaveRecordDetails[fieldName].display_value + 
															'</span>';
									}
									
								} else {
									var picklistHtml = '<span class="picklist-color">' +
																postSaveRecordDetails[fieldName].display_value + 
															'</span>';
								}
								detailViewValue.html(picklistHtml);
							} else if(fieldBasicData.data('type') == 'multipicklist' && app.getModuleName() != 'Users') {
								var picklistHtml = '';
								var rawPicklistValues = postSaveRecordDetails[fieldName].value;
								rawPicklistValues = rawPicklistValues.split('|##|');
								var picklistValues = postSaveRecordDetails[fieldName].display_value;
									picklistValues = picklistValues.split(',');
								for(var i=0; i< rawPicklistValues.length; i++) {
									var color = postSaveRecordDetails[fieldName].colormap[rawPicklistValues[i].trim()];
									if(color) {
										var contrast = app.helper.getColorContrast(color);
										var textColor = (contrast === 'dark') ? 'white' : 'black';
										picklistHtml = picklistHtml +
														'<span class="picklist-color" style="background-color: ' + color + '; color: '+ textColor + ';">' +
															 picklistValues[i] + 
														'</span>';
									} else {
										picklistHtml = picklistHtml +
														'<span class="picklist-color">' + 
															 picklistValues[i] + 
														'</span>';
									}
									if(picklistValues[i+1]!==undefined)
										picklistHtml+=' , ';
								}
								detailViewValue.html(picklistHtml);
							} else if(fieldBasicData.data('type') == 'currency' && app.getModuleName() != 'Users') {
								detailViewValue.find('.currencyValue').html(postSaveRecordDetails[fieldName].display_value);
								contentHolder.closest('.detailViewContainer').find('.detailview-header-block').find('.'+fieldName).html(postSaveRecordDetails[fieldName].display_value);
							}else {
								detailViewValue.html(postSaveRecordDetails[fieldName].display_value);
								//update namefields displayvalue in header
								if(contentHolder.hasClass('overlayDetail')) {
									contentHolder.find('.overlayDetailHeader').find('.'+fieldName)
									.html(postSaveRecordDetails[fieldName].display_value);
								} else {
									contentHolder.closest('.detailViewContainer').find('.detailview-header-block')
									.find('.'+fieldName).html(postSaveRecordDetails[fieldName].display_value);
							}
							}
							fieldBasicData.data('displayvalue',postSaveRecordDetails[fieldName].display_value);
							fieldBasicData.data('value',postSaveRecordDetails[fieldName].value);
							
							if(fieldName == 'portal'){
								var passEle = jQuery('[data-name="portal_password"]');
								passEle.closest('td').find('.value').html(postSaveRecordDetails['portal_password'].display_value);
								passEle.data('displayvalue', postSaveRecordDetails['portal_password'].display_value)
								passEle.data('value', postSaveRecordDetails['portal_password'].value)
							}
							
							jQuery(currentTdElement).find('.input-group-addon').removeClass("disabled");

							detailViewValue.css('display', 'inline-block');
							editElement.addClass('hide');
							editElement.removeClass('ajaxEdited');
							jQuery('.editAction').removeClass('hide');
							actionElement.show();
							var postAjaxSaveEvent = jQuery.Event(Vtiger_Detail_Js.PostAjaxSaveEvent);
							app.event.trigger(postAjaxSaveEvent, fieldBasicData, postSaveRecordDetails, contentHolder);
							//After saving source field value, If Target field value need to change by user, show the edit view of target field.
							if(thisInstance.targetPicklistChange) {
								var sourcePicklistname = thisInstance.sourcePicklistname;
								thisInstance.targetPicklist.find('.editAction').trigger('click');
								thisInstance.targetPicklistChange = false;
								thisInstance.targetPicklist = false;
								thisInstance.handlePickListDependencyMap(sourcePicklistname);
								thisInstance.sourcePicklistname = false;
							}
						});
					}
				};
				validateAndSubmitForm(form,params);
			}
		});
	},

	/**
	 * Function which will register all the events
	 */
	registerEvents: function () {
		var form = this.getForm();
		this._super();
		this.registerAjaxPreSaveEvents(form);
		jQuery(document).find(".portalSwitch").bootstrapSwitch();
		this.registerInlineEditForPortalBlock();
		this.registerAjaxPortalEditCancelEvent();
		this.registerAjaxPortalEditSaveEvent();
		this.registerEventForChangePortalModuleState();
	},
	
	loadSelectedTabContents: function(tabElement, urlAttributes){
		var self = this;
		var detailViewContainer = this.getDetailViewContainer();
		var url = tabElement.data('url');
		if(url){
			self.loadContents(url,urlAttributes).then(function(data){
				self.deSelectAllrelatedTabs();
				self.markRelatedTabAsSelected(tabElement);
				var container = jQuery('.relatedContainer');
				app.event.trigger("post.relatedListLoad.click",container.find(".searchRow"));
				// Added this to register pagination events in related list
				var relatedModuleInstance = self.getRelatedController();
				//Summary tab is clicked
				if(tabElement.data('linkKey') == self.detailViewSummaryTabLabel) {
					self.registerSummaryViewContainerEvents(detailViewContainer);
					self.registerEventForPicklistDependencySetup(self.getForm());
				}

				//Detail tab is clicked
				if(tabElement.data('linkKey') == self.detailViewDetailTabLabel) {
					self.triggerDetailViewContainerEvents(detailViewContainer);
					self.registerEventForPicklistDependencySetup(self.getForm());
				}

				// Registering engagement events if clicked tab is History
				if(tabElement.data('labelKey') == self.detailViewHistoryTabLabel){
					var engagementsContainer = jQuery(".engagementsContainer");
					if(engagementsContainer.length > 0){
						app.event.trigger("post.engagements.load");
					}
				}

				relatedModuleInstance.initializePaginationEvents();
				
				if(!container.length){
					var joucontainer = jQuery('.journalsrelatedContainer');
					if(joucontainer.length){
						var instance =   Vtiger_Journal_Js.getInstance();
						instance.registerEvents();
					}
				}
				//prevent detail view ajax form submissions
				jQuery('form#detailView').on('submit', function(e) {
					e.preventDefault();
				});
				if(!jQuery(document).find('.bootstrapSwitch').length && jQuery(document).find(".portalSwitch").length)
					jQuery(document).find(".portalSwitch").bootstrapSwitch();
			});
		}
	},
	
   registerEventForChangePortalModuleState: function () {
	   var self = this;
		var detailViewContainer = this.getDetailViewContainer();
        jQuery(detailViewContainer).on('switchChange.bootstrapSwitch', ".portalSwitch", function (e) {
            var currentElement = jQuery(e.currentTarget);
            
            if(currentElement.val() == '1'){
                currentElement.attr('value','0');
            } else {
                currentElement.attr('value','1');
            }
            
           
            var fieldNameValueMap = {};
			fieldNameValueMap['value'] = currentElement.val();
			fieldNameValueMap['field'] = currentElement.attr('name');
			
			app.helper.showProgress();
			self.savePortalFieldValues(fieldNameValueMap).then(function(err, response) {
				app.helper.hideProgress();
				if (err == null) {
					//console.log(response)
				}
			});
        });
    },
})