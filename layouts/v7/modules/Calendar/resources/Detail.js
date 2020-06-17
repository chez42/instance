Vtiger_Detail_Js("Calendar_Detail_Js", {
    
}, {
    
    _delete : function(deleteRecordActionUrl) {
        var params = app.convertUrlToDataParams(deleteRecordActionUrl+"&ajaxDelete=true");
        app.helper.showProgress();
        app.request.post({data:params}).then(
        function(err,data){
            app.helper.hideProgress();
            if(err === null) {
                if(typeof data !== 'object') {
                    window.location.href = data;
                } else {
                    app.helper.showAlertBox({'message' : data.prototype.message});
                }
            } else {
                app.helper.showAlertBox({'message' : err});
            }
        });
    },
    
    /**
    * To Delete Record from detail View
    * @param URL deleteRecordActionUrl
    * @returns {undefined}
    */
    remove : function(deleteRecordActionUrl) {
        var thisInstance = this;
        var isRecurringEvent = jQuery('#addEventRepeatUI').data('recurringEnabled');
        if(isRecurringEvent) {
            app.helper.showConfirmationForRepeatEvents().then(function(postData) {
                deleteRecordActionUrl += '&' + jQuery.param(postData);
                thisInstance._delete(deleteRecordActionUrl);
            });
        } else {
            this._super(deleteRecordActionUrl);
        }
    },    
    
    registerReferenceEvent : function(){
		var vtigerEditInstance = new Vtiger_Edit_Js;
		vtigerEditInstance.registerBasicEvents(jQuery("#detailView"));
	},
	
	registerEvents : function(){
		this._super();
		this.registerReferenceEvent();
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
			
			if(fieldName == 'date_start')
				ajaxEditNewValue += ' '+ jQuery('[name="time_start"]', editElement).val();
			else if(fieldName == 'time_start')
				ajaxEditNewValue = jQuery('[name="date_start"]', editElement).val() + ' ' + ajaxEditNewValue;
			else if(fieldName == 'due_date')
				ajaxEditNewValue += ' '+ jQuery('[name="time_end"]', editElement).val();
			else if(fieldName == 'time_end')
				ajaxEditNewValue = jQuery('[name="due_date"]', editElement).val() + ' ' + ajaxEditNewValue;
			
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
				
				if(fieldName == 'reminder_time'){
					if(currentTdElement.find('input:checkbox').length){
						if(currentTdElement.find('input:checkbox').is(':checked')) {
							fieldNameValueMap['set_reminder'] = 'Yes';
							fieldNameValueMap['remdays'] = currentTdElement.find('[name="remdays"]').val();
							fieldNameValueMap['remhrs'] = currentTdElement.find('[name="remhrs"]').val();
							fieldNameValueMap['remmin'] = currentTdElement.find('[name="remmin"]').val();
						} else {
							fieldNameValueMap['set_reminder'] = 'No';
						}
					}
				}
				
				if(fieldType == 'multireference' && fieldName == 'contact_id'){
					var cntId = fieldValue.split(',');
					fieldNameValueMap['contactidlist'] = cntId.join(';');
				}
				
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
							jQuery(currentTdElement).find('.input-group-addon').removeClass("disabled");
							
							if(fieldName == 'reminder_time'){
								
								if(currentTdElement.find('input:checkbox').length){
									if(currentTdElement.find('input:checkbox').is(':checked')) {
										var disVal = '';
										if(currentTdElement.find('[name="remdays"]').val()){
											disVal += currentTdElement.find('[name="remdays"]').val()+' days ';
										}
										if(currentTdElement.find('[name="remhrs"]').val()){
											disVal += currentTdElement.find('[name="remhrs"]').val()+' hours ';
										}
										if(currentTdElement.find('[name="remmin"]').val()){
											disVal += currentTdElement.find('[name="remmin"]').val()+' minutes';
										}
										fieldBasicData.data('displayvalue',disVal);
										detailViewValue.html(disVal+' Before Event');
									} else {
										fieldBasicData.data('displayvalue','No');
									}
								}
							}
							
							detailViewValue.css('display', 'inline-block');
							editElement.addClass('hide');
							editElement.removeClass('ajaxEdited');
							jQuery('.editAction').removeClass('hide');
							actionElement.show();
							if(fieldName == 'eventstatus' && postSaveRecordDetails[fieldName].value == 'Held'){
								
								thisInstance.registerChangeOnStatus();
							}
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
	 * Handling Ajax Edit 
	 * @param {type} currentTdElement
	 * @returns {undefined}
	 */
	ajaxEditHandling : function(currentTdElement){
		var thisInstance = this;
		var detailViewValue = jQuery('.value',currentTdElement);
		var editElement = jQuery('.edit',currentTdElement);
		var fieldBasicData = jQuery('.fieldBasicData', editElement);
		var fieldName = fieldBasicData.data('name');
		var fieldType = fieldBasicData.data('type');
		var value = fieldBasicData.data('displayvalue');
		var rawValue = fieldBasicData.data('value');
		var self = this;
		var fieldElement = jQuery('[name="'+ fieldName +'"]', editElement);

		// If Reference field has value, then we are disabling the field by default
		if(fieldElement.attr('disabled') == 'disabled' && fieldType != 'reference'){
			return;
		} 

		if(editElement.length <= 0) {
			return;
		}

		if(editElement.is(':visible')){
			return;
		}

		if(fieldType === 'multipicklist') {
			var multiPicklistFieldName = fieldName.split('[]');
			fieldName = multiPicklistFieldName[0];
		}

		var customHandlingFields = ['owner','ownergroup','picklist','multipicklist','reference','currencyList','text'];
		if(jQuery.inArray(fieldType, customHandlingFields) !== -1){
			value = rawValue;
		}
		if(jQuery('.editElement',editElement).length === 0){
			var fieldInfo;
			if(self.getOverlayDetailMode() == true){
				fieldInfo = related_uimeta.field.get(fieldName);
			}
			else{
				 fieldInfo = uimeta.field.get(fieldName);
			}
			fieldInfo['value'] = value;
			
			if(fieldName == 'contact_id'){
				fieldInfo['type'] = 'multireference';
			}
			var fieldObject = Vtiger_Field_Js.getInstance(fieldInfo);
			var fieldModel = fieldObject.getUiTypeModel();

			var ele = jQuery('<div class="input-group editElement"></div>');
			var actionButtons = '<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlineAjaxSave"><i class="fa fa-check"></i></span>';
			actionButtons += '<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlineAjaxCancel"><i class="fa fa-close"></i></span>';
			//wrapping action buttons with class called input-save-wrap
			var inlineSaveWrap=jQuery('<div class="input-save-wrap"></div>');
			inlineSaveWrap.append(actionButtons);
			// we should have atleast one submit button for the form to submit which is required for validation
			ele.append(fieldModel.getUi()).append(inlineSaveWrap);
			ele.find('.inputElement').addClass('form-control');
			editElement.append(ele);
		}

		// for reference fields, actual value will be ID but we need to show related name of that ID
		if(fieldType === 'reference'){
			currentTdElement.prev('td').find('.referenceSelect').removeClass('hide');
			if(currentTdElement.prev('td').find('.referenceSelect').length)
				currentTdElement.prev('td').find('.muted').addClass('hide');
			if(value !== 0 && value != ''){
				jQuery('input[name="'+fieldName+'"]',editElement).prop('value',jQuery.trim(detailViewValue.text()));
				var referenceElement = jQuery('input[name="'+fieldName+'"]',editElement);
				if(!referenceElement.attr('disabled')) {
					referenceElement.attr('disabled','disabled');
					editElement.find('.clearReferenceSelection').removeClass('hide')
				}
			}
		}
		
		detailViewValue.css('display', 'none');
		editElement.removeClass('hide').show().children().filter('input[type!="hidden"]input[type!="image"],select').filter(':first').focus();
		vtUtils.applyFieldElementsView(currentTdElement);
		var contentHolder = jQuery('.detailViewContainer');
		var vtigerInstance = Vtiger_Index_Js.getInstance();
		vtigerInstance.registerAutoCompleteFields(contentHolder);
		if(fieldType === 'multireference'){
			thisInstance.referenceModulePopupRegisterEvent(contentHolder);
		}else{
			vtigerInstance.referenceModulePopupRegisterEvent(contentHolder);
		}
		editElement.addClass('ajaxEdited');
		thisInstance.registerSaveOnEnterEvent(editElement);
		jQuery('.editAction').addClass('hide');
		if(fieldType === 'multireference'){
			thisInstance.registerRelatedContactSpecificEvents(editElement);
		}
		if(fieldType == 'picklist' || fieldType == 'ownergroup' || fieldType == 'owner') {
			var sourcePicklistFieldName = thisInstance.getDependentSourcePicklistName(fieldName);
			if(sourcePicklistFieldName) {
				thisInstance.handlePickListDependencyMap(sourcePicklistFieldName);
			}
		}
	},
	
	relatedContactElement : false,

	getRelatedContactElement : function(ele) {
		this.relatedContactElement =  jQuery('[name="contact_id"]',ele);
		return this.relatedContactElement;
	},
	
	registerRelatedContactSpecificEvents : function(ele) {
		var thisInstance = this;
		
		this.getRelatedContactElement(ele).select2({
			 minimumInputLength: 3,
			 ajax : {
				'url' : 'index.php?module=Contacts&action=BasicAjax&search_module=Contacts',
				'dataType' : 'json',
				'data' : function(term,page){
					 var data = {};
					 data['search_value'] = term;
					 return data;
				},
				'results' : function(data){
					data.results = data.result;
					for(var index in data.results ) {

						var resultData = data.result[index];
						resultData.text = resultData.label;
					}
					return data
				},
				 transport : function(params){
					return jQuery.ajax(params);
				 }
			 },
			 multiple : true,
			 //To Make the menu come up in the case of quick create
			 dropdownCss : {'z-index' : '10001'}
		});
		
		//To add multiple selected contact from popup
		jQuery('[name="contact_id"]',ele).on(Vtiger_Edit_Js.refrenceMultiSelectionEvent,function(e,result){
			thisInstance.addNewContactToRelatedList(result,ele);
		});

		this.fillRelatedContacts(ele);
	},
	
	/**
	 * Function which will fill the already saved contacts on load
	 */
	fillRelatedContacts : function(ele) {
		
		var relatedContactValue = jQuery('[name="relatedContactInfo"]',ele).data('value');
		
		for(var contactId in relatedContactValue) {
			var info = relatedContactValue[contactId];
			info.text = info.name;
			relatedContactValue[contactId] = info;
		}
		this.getRelatedContactElement(ele).select2('data',relatedContactValue);
	},


	addNewContactToRelatedList : function(newContactInfo, ele){
		
		var resultentData = new Array();

		var element =  jQuery('[name="contact_id"]',ele);
		var selectContainer = jQuery(element.data('select2').container, ele);
		var choices = selectContainer.find('.select2-search-choice');
		choices.each(function(index,element){
			resultentData.push(jQuery(element).data('select2-data'));
		});
		var select2FormatedResult = newContactInfo.data;
		for(var i=0 ; i < select2FormatedResult.length; i++) {
		  var recordResult = select2FormatedResult[i];
		  recordResult.text = recordResult.name;
		  resultentData.push( recordResult );
		}
		element.select2('data',resultentData);
		
	},
	
	
	/**
	 * Funtion to register popup search event for reference field
	 * @param <jQuery> container
	 */
	referenceModulePopupRegisterEvent : function(container) {
		var thisInstance = this;
		container.off('click', '.relatedPopup');
		container.on("click",'.relatedPopup',function(e) {
			thisInstance.openPopUp(e);
		});
	},
	
	openPopUp : function(e){
		var thisInstance = this;
		var vtigerInstance = Vtiger_Index_Js.getInstance();
		var parentElem = vtigerInstance.getParentElement(jQuery(e.target));

		var params = vtigerInstance.getPopUpParams(parentElem);
		params.view = 'Popup';

		var isMultiple = false;
		if(params.multi_select) {
				isMultiple = true;
		}

		var sourceFieldElement = jQuery('.sourceField',parentElem);
		
		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);
		
		if(prePopupOpenEvent.isDefaultPrevented()) {
				return ;
		}
		var popupInstance = Vtiger_Popup_Js.getInstance();
		
		app.event.off(Vtiger_Edit_Js.popupSelectionEvent);
		app.event.one(Vtiger_Edit_Js.popupSelectionEvent,function(e,data){
			var responseData = JSON.parse(data);
			var dataList = new Array();
			for(var id in responseData){
					var data = {
							'name' : responseData[id].name,
							'id' : id
					}
					dataList.push(data);
					if(!isMultiple) {
						vtigerInstance.setReferenceFieldValue(parentElem, data);
					}
			}
			
			if(isMultiple) {
				sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
			sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},
	
	registerAjaxEditCancelEvent : function(contentHolder){
		var thisInstance = this;
		if(typeof contentHolder === 'undefined') {
			contentHolder = this.getContentHolder();
		}
		contentHolder.on('click','.inlineAjaxCancel',function(e){
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
			
			if(fieldType == 'reference'){
				currentTdElement.prev('td').find('.referenceSelect').addClass('hide');
				if(currentTdElement.prev('td').find('.referenceSelect').length)
					currentTdElement.prev('td').find('.muted').removeClass('hide');
			}
			
			detailViewValue.css('display', 'inline-block');
			editElement.addClass('hide');
			editElement.find('.inputElement').trigger('Vtiger.Validation.Hide.Messsage')
			editElement.removeClass('ajaxEdited');
			jQuery('.editAction').removeClass('hide');
			actionElement.show();
		});
	},
	
	registerChangeOnStatus : function(){
		var thisInstance = this;
		var requestParams = {
			'module': 'Calendar',
			'view': 'QuickCreateFollowupAjax',
			'record': app.getRecordId()
		};
		app.helper.showProgress();
		app.request.get({'data': requestParams}).then(function (err, resp) {
			app.helper.hideProgress();
			if (!err && resp) {
				app.helper.showModal(resp, {
					'cb': function (modalContainer) {
						thisInstance.registerCreateFollowUpEvent(modalContainer);
					}
				});
			}
		});
			
	},
	
	registerCreateFollowUpEvent: function (modalContainer) {
		var thisInstance = this;
		var params = {
			submitHandler: function (form) {
				form = jQuery(form);
				form.find('[type="submit"]').attr('disabled', 'disabled');
				var formData = form.serializeFormData();
				app.helper.showProgress();
				app.request.post({'data': formData}).then(function (err, res) {
					app.helper.hideProgress();
					app.helper.hideModal();
					if (!err && res['created']) {
						jQuery('.vt-notification').remove();
					} else {
						app.event.trigger('post.save.failed', err);
					}
				});
			}
		};
		modalContainer.find('form#followupQuickCreate').vtValidate(params);
	},	
	
});