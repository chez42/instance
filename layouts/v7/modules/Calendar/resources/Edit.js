/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Calendar_Edit_Js",{

	uploadAndParse : function() {
		if (Vtiger_Import_Js.validateFilePath()) {
			var form = jQuery("form[name='importBasic']");
			jQuery('[name="mode"]').val('importResult');
			var data = new FormData(form[0]);
			var postParams = {
				data: data,
				contentType: false,
				processData: false
			};
			app.helper.showProgress();
			app.request.post(postParams).then(function(err, response) {
				app.helper.loadPageContentOverlay(response);
				app.helper.hideProgress();
			});
		}
		return false;
	},

	handleFileTypeChange: function() {
		var fileType = jQuery('[name="type"]').filter(':checked').val();
		var currentPage = jQuery('#group2');
		var selectedRecords = jQuery('#group1');

		if(fileType == 'ics') {
			currentPage.prop('disabled', true).prop('checked', false);
			selectedRecords.prop('disabled', true).prop('checked', false);
			jQuery('#group3').prop('checked', true);
		} else {
			currentPage.removeAttr('disabled');
			if (jQuery('.isSelectedRecords').val() == 1) {
				selectedRecords.removeAttr('disabled');
			}
		}
	},

	userChangedTimeDiff:false

},{

	relatedContactElement : false,

	recurringEditConfirmation : false,
	
	duplicateCheck: false,
	
	getRelatedContactElement : function(form) {
		if(typeof form == "undefined") {
			form = this.getForm();
		}
		this.relatedContactElement =  jQuery('#contact_id_display', form);
		return this.relatedContactElement;
	},

	openPopUp : function(e){
		var thisInstance = this;
		var parentElem = thisInstance.getParentElement(jQuery(e.target));

		var params = this.getPopUpParams(parentElem);
		params.view = 'Popup';

		var isMultiple = false;
		if(params.multi_select) {
				isMultiple = true;
		}

		var sourceFieldElement = jQuery('input[class="sourceField"]',parentElem);

		var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
		sourceFieldElement.trigger(prePopupOpenEvent);

		if(prePopupOpenEvent.isDefaultPrevented()) {
				return ;
		}
		var popupInstance = Vtiger_Popup_Js.getInstance();
		popupInstance.showPopup(params,function(data){
			var responseData = JSON.parse(data);
			var dataList = new Array();
			for(var id in responseData){
					var data = {
							'name' : responseData[id].name,
							'id' : id
					}
					dataList.push(data);
					if(!isMultiple) {
							thisInstance.setReferenceFieldValue(parentElem, data);
					}
			}

			if(isMultiple) {
				sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
			}
			sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
		});
	},

	registerRelatedContactSpecificEvents : function(form) {
		var thisInstance = this;
		if(typeof form == "undefined") {
			form = this.getForm();
		}
		form.find('[name="contact_id"]').on(Vtiger_Edit_Js.preReferencePopUpOpenEvent,function(e){
			var parentIdElement  = form.find('[name="parent_id"]');
			if(parentIdElement.length <= 0) {
				parentIdElement = form.find('[name="contact_id"]');
			}
			var container = parentIdElement.closest('td');
			var popupReferenceModule = jQuery('input[name="popupReferenceModule"]',container).val();

			if(popupReferenceModule == 'Leads' && parentIdElement.val().length > 0) {
				e.preventDefault();
				app.helper.showErrorNotification({message:app.vtranslate('LBL_CANT_SELECT_CONTACT_FROM_LEADS')});
			}
		})
		//If module is not events then we dont have to register events
		if(!this.isEvents(form)) {
			return;
		}
		this.getRelatedContactElement(form).select2({
			 minimumInputLength: 3,
			 ajax : {
				'url' : 'index.php?module=Contacts&action=BasicAjax&search_module=Contacts',
				'dataType' : 'json',
				'data' : function(term,page){
					 var data = {};
					 data['search_value'] = term;
					 var parentIdElement  = form.find('[name="parent_id"]');
					 if(parentIdElement.length > 0 && parentIdElement.val().length > 0) {
						var closestContainer = parentIdElement.closest('td');
						data['parent_id'] = parentIdElement.val();
						data['parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
					 }
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
		form.find('[name="contact_id"]').on(Vtiger_Edit_Js.refrenceMultiSelectionEvent,function(e,result){
			thisInstance.addNewContactToRelatedList(result,form);
		});

		this.fillRelatedContacts(form);
	},
	/**
	 * Function to get reference search params
	 */
	getReferenceSearchParams : function(element){
		var tdElement = jQuery(element).closest('td');
		var params = {};
		var previousTd = tdElement.prev();
		var multiModuleElement = jQuery('select.referenceModulesList', previousTd);

		var referenceModuleElement;
		if(multiModuleElement.length) {
			referenceModuleElement = multiModuleElement;
		} else {
			referenceModuleElement = jQuery('input[name="popupReferenceModule"]',tdElement).length ?
										jQuery('input[name="popupReferenceModule"]',tdElement) : jQuery('input.popupReferenceModule',tdElement);
		}
		var searchModule =  referenceModuleElement.val();
		params.search_module = searchModule;
		return params;
	},

	isEvents : function(form) {
		if(typeof form === 'undefined') {
			form = this.getForm();
		}
		var moduleName = form.find('[name="module"]').val();
		if(form.find('.quickCreateContent').length > 0 && form.find('[name="calendarModule"]').val()==='Events') {
			return true;
		}
		if(moduleName === 'Events') {
			return true;
		}
		return false;
	},

	getPopUpParams : function(container) {
		var params = this._super(container);
		var sourceFieldElement = jQuery('input[class="sourceField"]',container);

		if(sourceFieldElement.attr('name') == 'contact_id') {
			var form = container.closest('form');
			var parentIdElement  = form.find('[name="parent_id"]');
			var closestContainer = parentIdElement.closest('td');
			var referenceModule = closestContainer.find('[name="popupReferenceModule"]');
			if(parentIdElement.length > 0 && parentIdElement.val().length > 0 && referenceModule.length >0) {
				params['related_parent_id'] = parentIdElement.val();
				params['related_parent_module'] = referenceModule.val();
			}
		}
		return params;
	},

	addInviteesIds : function(form) {
		var thisInstance = this;
		if(thisInstance.isEvents(form)) {
			var inviteeIdsList = jQuery('#selectedUsers').val();
			if(inviteeIdsList) {
				inviteeIdsList = jQuery('#selectedUsers').val().join(';')
			}
			jQuery('<input type="hidden" name="inviteesid" />').
					appendTo(form).
					val(inviteeIdsList);
		}
	},

	resetRecurringDetailsIfDisabled : function(form) {
		var recurringCheck = form.find('input[name="recurringcheck"]').is(':checked');
		//If the recurring check is not enabled then recurring type should be --None--
		if(!recurringCheck) {
			jQuery('#recurringType').append(jQuery('<option value="--None--">None</option>')).val('--None--');
		}
	},

	initializeContactIdList : function(form) {
		var relatedContactElement = this.getRelatedContactElement(form);
		if(this.isEvents(form) && relatedContactElement.length) {
			jQuery('<input type="hidden" name="contactidlist" /> ').appendTo(form).val(relatedContactElement.val().split(',').join(';'));
			form.find('[name="contact_id"]').attr('name','');
		}
	},

	registerRecurringEditOptions : function(e,form,InitialFormData) {
		var currentFormData = form.serialize();
		var editViewContainer = form.closest('.editViewPageDiv').length;
		var recurringEdit = form.find('.recurringEdit').length;
		var recurringEditMode = form.find('[name="recurringEditMode"]');
		var recurringCheck = form.find('input[name="recurringcheck"]').is(':checked');

		if(editViewContainer && InitialFormData === currentFormData && recurringEdit) {
			recurringEditMode.val('current');
		} else if(editViewContainer && recurringCheck && recurringEdit && InitialFormData !== currentFormData) {
			e.preventDefault();

			var recurringEventsUpdateModal = form.find('.recurringEventsUpdation');
			var clonedContainer = recurringEventsUpdateModal.clone(true, true);

			var callback = function(data) {
				var modalContainer = data.find('.recurringEventsUpdation');
				modalContainer.removeClass('hide');
				modalContainer.on('click', '.onlyThisEvent', function() {
					recurringEditMode.val('current');
					app.helper.hideModal();
					form.vtValidate({
						submitHandler : function() {
							return true;
						}
					});
					form.submit();
				});
				modalContainer.on('click', '.futureEvents', function() {
					recurringEditMode.val('future');
					app.helper.hideModal();
					form.vtValidate({
						submitHandler : function() {
							return true;
						}
					});
					form.submit();
				});
				modalContainer.on('click', '.allEvents', function() {
					recurringEditMode.val('all');
					app.helper.hideModal();
					form.vtValidate({
						submitHandler : function() {
							return true;
						}
					});
					form.submit();
				});
			};

			app.helper.showModal(clonedContainer, {
				'cb' : callback
			});
		}
	},

	registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form === "undefined") {
			form = this.getForm();
		}
		var InitialFormData = form.serialize();
		form.on(Vtiger_Edit_Js.recordPresaveEvent,function(e) {
			thisInstance.registerRecurringEditOptions(e,form,InitialFormData);
			thisInstance.addInviteesIds(form);
			thisInstance.resetRecurringDetailsIfDisabled(form);
			thisInstance.initializeContactIdList(form);
		});
	},
	
	/*registerRecordPreSaveEvent : function(form) {
		var thisInstance = this;
		if(typeof form === "undefined") {
			form = this.getForm();
		}
		
		var InitialFormData = form.serialize();
		form.on(Vtiger_Edit_Js.recordPresaveEvent,function(e) {
			if(!(thisInstance.duplicateCheck)) {
				thisInstance.registerEventsForTimeSoltsAvailability(form).then(function(res){
					if(res.success){
						var response = res.data;
						thisInstance.genratePopOverEvent(form,response);
					} else {
						thisInstance.duplicateCheck = true;
						thisInstance.registerRecurringEditOptions(e,form,InitialFormData);
						thisInstance.addInviteesIds(form);
						thisInstance.resetRecurringDetailsIfDisabled(form);
						thisInstance.initializeContactIdList(form);
						form.submit();
					}
				});
			} else {
				return true;
			}
			e.preventDefault();
		});
	},*/

	registerTimeStartChangeEvent : function(container) {
		container.on('changeTime', 'input[name="time_start"]', function() {
			var startDateElement = container.find('input[name="date_start"]');
			var startTimeElement = container.find('input[name="time_start"]');
			var endDateElement = container.find('input[name="due_date"]');
			var endTimeElement = container.find('input[name="time_end"]');

			var activityType = container.find('[name="activitytype"]').val();

			var momentFormat = vtUtils.getMomentCompatibleDateTimeFormat();
			var m = moment(startDateElement.val() + ' ' + startTimeElement.val(), momentFormat);

			var minutesToAdd = container.find('input[name="defaultOtherEventDuration"]').val();
			if(activityType === 'Call') {
				minutesToAdd = container.find('input[name="defaultCallDuration"]').val();
			}
			if(Calendar_Edit_Js.userChangedTimeDiff){
				minutesToAdd = Calendar_Edit_Js.userChangedTimeDiff;
			}
			m.add(parseInt(minutesToAdd), 'minutes');
			if ((container.find('[name="time_start"]').data('userChangedDateTime') !== 1) || (container.find('[name="module"]').val()==='Calendar' || container.find('[name="module"]').val()==='Events')) {
					if(m.format(vtUtils.getMomentDateFormat()) == 'Invalid date') {
						m.format(vtUtils.getMomentDateFormat()) = '';
					}
					endDateElement.val(m.format(vtUtils.getMomentDateFormat()));
				}
			endTimeElement.val(m.format(vtUtils.getMomentTimeFormat()));

			vtUtils.registerEventForDateFields(endDateElement);
			vtUtils.registerEventForTimeFields(endTimeElement);
			endDateElement.valid();
		});
	},


	/**
	 * Function which will fill the already saved contacts on load
	 */
	fillRelatedContacts : function(form) {
		if(typeof form == "undefined") {
			form = this.getForm();
		}
		var relatedContactValue = form.find('[name="relatedContactInfo"]').data('value');
		for(var contactId in relatedContactValue) {
			var info = relatedContactValue[contactId];
			info.text = info.name;
			relatedContactValue[contactId] = info;
		}
		this.getRelatedContactElement(form).select2('data',relatedContactValue);
	},


	addNewContactToRelatedList : function(newContactInfo, form){
		if(form.length <= 0) {
			form = this.getForm();
		}
		 var resultentData = new Array();

			var element =  jQuery('#contact_id_display', form);
			var selectContainer = jQuery(element.data('select2').container, form);
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
			if(form.find('.quickCreateContent').length > 0) {
				form.find('[name="relatedContactInfo"]').data('value', resultentData);
				var relatedContactElement = this.getRelatedContactElement(form);
				if(relatedContactElement.length > 0) {
					jQuery('<input type="hidden" name="contactidlist" /> ').appendTo(form).val(relatedContactElement.val().split(',').join(';'));
					form.find('[name="contact_id"]').attr('name','');
				}
			}
	},

	referenceCreateHandler : function(container) {

		var thisInstance = this;
		var form = thisInstance.getForm();
		var mode = jQuery(form).find('[name="module"]').val();
		if(container.find('.sourceField').attr('name') != 'contact_id'){ 
			this._super(container); 
			return; 
		}
		 var postQuickCreateSave  = function(data) {
			var params = {};
			params.name = data._recordLabel;
			params.id = data._recordId;
			if(mode == "Calendar"){
				thisInstance.setReferenceFieldValue(container, params);
				return;
			}
			thisInstance.addNewContactToRelatedList({'data':[params]}, container);
		}

		var referenceModuleName = this.getReferencedModuleName(container);
		var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
		if(quickCreateNode.length <= 0) {
			return app.helper.showErrorNotification({message:app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED')});

		}
		quickCreateNode.trigger('click',{'callbackFunction':postQuickCreateSave});
	},

	 /**
	 * Function which will register the change event for repeatMonth radio buttons
	 */
	registerRepeatMonthActions : function() {
		var thisInstance = this;
		thisInstance.getForm().find('input[name="repeatMonth"]').on('change', function(e) {
			//If repeatDay radio button is checked then only select2 elements will be enable
			thisInstance.repeatMonthOptionsChangeHandling();
		});
	},

	/**
	 * This function will handle the change event for RepeatMonthOptions
	 */
	repeatMonthOptionsChangeHandling : function() {
		//If repeatDay radio button is checked then only select2 elements will be enable
			if(jQuery('#repeatDay').is(':checked')) {
				jQuery('#repeatMonthDate').attr('disabled', true);
				jQuery('#repeatMonthDayType').select2("enable");
				jQuery('#repeatMonthDay').select2("enable");
			} else {
				jQuery('#repeatMonthDate').removeAttr('disabled');
				jQuery('#repeatMonthDayType').select2("disable");
				jQuery('#repeatMonthDay').select2("disable");
			}
	},

	 /**
	 * Function which will change the UI styles based on recurring type
	 * @params - recurringType - which recurringtype is selected
	 */
	changeRecurringTypesUIStyles : function(recurringType) {
		var thisInstance = this;
		if(recurringType == 'Daily' || recurringType == 'Yearly') {
			jQuery('#repeatWeekUI').removeClass('show').addClass('hide');
			jQuery('#repeatMonthUI').removeClass('show').addClass('hide');
		} else if(recurringType == 'Weekly') {
			jQuery('#repeatWeekUI').removeClass('hide').addClass('show');
			jQuery('#repeatMonthUI').removeClass('show').addClass('hide');
		} else if(recurringType == 'Monthly') {
			jQuery('#repeatWeekUI').removeClass('show').addClass('hide');
			jQuery('#repeatMonthUI').removeClass('hide').addClass('show');
		}
	},

	registerDateStartChangeEvent : function(container) {
		container.find('[name="date_start"]').on('change',function() {
			var timeStartElement = container.find('[name="time_start"]');
			timeStartElement.trigger('changeTime');
		});
	},

	registerTimeEndChangeEvent : function(container) {
		container.find('[name="time_end"]').on('changeTime', function() {
			var startDateElement = container.find('input[name="date_start"]');
			var startTimeElement = container.find('input[name="time_start"]');
			var endDateElement = container.find('input[name="due_date"]');
			var endTimeElement = container.find('input[name="time_end"]');
			var momentFormat = vtUtils.getMomentCompatibleDateTimeFormat();
			var m1 = moment(endDateElement.val() + ' ' + endTimeElement.val(), momentFormat);
			var m2 = moment(startDateElement.val() + ' ' + startTimeElement.val(), momentFormat);
			var newDiff = (m1.unix() - m2.unix())/60;
			Calendar_Edit_Js.userChangedTimeDiff = newDiff;
			container.find('[name="due_date"]').valid();
		});
		if(container.find('[name="record"]')!==''){
			container.find('[name="time_end"]').trigger('changeTime');
		}
	},

	registerDateEndChangeEvent : function(container) {
		container.find('[name="due_date"]').on('change', function() {});
	},

	registerActivityTypeChangeEvent : function(container) {
		container.find('[name="activitytype"]').on('change', function() {
			var time_start_element = container.find('[name="time_start"]');
				time_start_element.trigger('changeTime');
		});
	},

	registerUserChangedDateTimeDetection : function(container) {
		var initialValue;
		container.on('focus',
		'[name="date_start"], [name="due_date"], [name="time_start"], [name="time_end"]',
		function() {
			initialValue = jQuery(this).val();
		});
		container.on('blur',
		'[name="date_start"], [name="due_date"], [name="time_start"], [name="time_end"]',
		function() {
			if(typeof initialValue !== 'undefined' && initialValue !== jQuery(this).val()) {
				container.find('[name="time_start"]').data('userChangedDateTime',1);
			}
		});
	},

	 registerDateTimeHandlersEditView : function(container) {
		var thisInstance = this;
		var registered = false;

		container.on('focus','[name="date_start"],[name="time_start"]',function(){
			if(!registered) {
				thisInstance.registerDateStartChangeEvent(container);
				thisInstance.registerTimeStartChangeEvent(container);
				thisInstance.registerTimeEndChangeEvent(container);
				thisInstance.registerDateEndChangeEvent(container);
				thisInstance.registerUserChangedDateTimeDetection(container);
				thisInstance.registerActivityTypeChangeEvent(container);
				registered = true;
			}
		});
	},

	registerDateTimeHandlers : function(container) {
		var thisInstance = this;
	  if(container.find('[name="record"]').val()===''){
		this.registerDateStartChangeEvent(container);
		this.registerTimeStartChangeEvent(container);
			container.find('[name="time_end"]').on('focus', function () {
				thisInstance.registerTimeEndChangeEvent(container);
			});
		this.registerDateEndChangeEvent(container);
		this.registerUserChangedDateTimeDetection(container);
		this.registerActivityTypeChangeEvent(container);
		}else{
		this.registerDateTimeHandlersEditView(container);
		}
	},

	registerToggleReminderEvent : function(container) {
		container.find('input[name="set_reminder"]').on('change', function(e) {
			var element = jQuery(e.currentTarget);
			var reminderSelectors = element.closest('#js-reminder-controls')
			.find('#js-reminder-selections');
			if(element.is(':checked')) {
				reminderSelectors.css('visibility','visible');
			} else {
				reminderSelectors.css('visibility','collapse');
			}
		})
	},

	 /**
	  * Function register to change recurring type.
	  */

	 registerRecurringTypeChangeEvent: function() {
		 var thisInstance = this;
		jQuery('#recurringType').on('change', function(e){
			var currentTarget = jQuery(e.currentTarget);
			var recurringType = currentTarget.val();
			thisInstance.changeRecurringTypesUIStyles(recurringType);
		});
	 },

	 /**
	  * Function to register recurrenceField checkbox.
	  */
	 registerRecurrenceFieldCheckBox : function(container) {
		 container.find('input[name="recurringcheck"]').on('change', function(e){
		   var element =jQuery(e.currentTarget);
		   var repeatUI = jQuery('#repeatUI');
		   if(element.is(':checked')) {
			   repeatUI.css('display','block');
		   } else {
			   repeatUI.css('display','none');
		   }
		 });
	 },

	registerBasicEvents : function(container) {
		this.registerEventsHoldFollowUpField(container);
		this._super(container);
		this.registerRecordPreSaveEvent(container);
		this.registerDateTimeHandlers(container);
		this.registerToggleReminderEvent(container);
		this.registerRecurrenceFieldCheckBox(container);
		this.registerRecurringTypeChangeEvent();
		this.repeatMonthOptionsChangeHandling();
		this.registerRepeatMonthActions();
		this.registerRelatedContactSpecificEvents(container);
		this.registerEventStatusChangeEvent(container);
		this.registerEventForGetTemplateData();
	},
	
	registerEvents: function(callParent) {
		if(callParent != false){
            this._super();
        }
        this.registerValidation();
    },
	 
	registerEventsForTimeSoltsAvailability :function(form){
		var aDeferred = jQuery.Deferred();
		var timeStart = form.find('[name="time_start"]').val();
		var dateStart = form.find('[name="date_start"]').val();
		var dateEnd = form.find('[name="due_date"]').val();
		var timeEnd = form.find('[name="time_end"]').val();
		var record = form.find('input[name="record"]').val();
		var userId = form.find('[name="assigned_user_id"]').val();
		var params = {
			'module' : 'Calendar',
			'action' : 'CheckTimeAvailability',
			'timestart' : timeStart,
			'datestart' : dateStart,
			'timeend' : timeEnd,
			'dateend' : dateEnd,
			'record' : record,
			'user_id' : userId
		};

		app.request.post({data: params}).then(function(err, data) {
			if (!err)  {
				aDeferred.resolve(data);
			}else{
				aDeferred.reject(err);
			}
		});
		return aDeferred.promise();
	},
	
	genratePopOverEvent :function(form,data){
		
		var element = form.find('input[name="date_start"]');
		var html = '';
		html += 'Conflicts';
		html += '<ul style="max-height: 200px; overflow-y: auto;">';
		jQuery.each(data,function(i,val){
			html += '<li>';
			//style="color: #ffff00"'+val.subject+'
			html += '<a style="color: #15c" target="_blank" href="index.php?module=Calendar&view=Detail&record='+val.activityid+'">('+val.dateStart+' - '+val.dueDate+')</a> </li>';
		});
		html += '</ul>';
		
		var template = '<div class="popover" role="tooltip" style="background: red;">' +
	        '<style>' +
	        '.popover.bottom>.arrow:after{border-bottom-color:red;2px solid #ddd}' +
	        '.popover-content{font-size: 11px}' +
	        '.popover-content ul{padding: 5px 0px 0 0px}' +
	        '.popover-content li{list-style-type: none}' +
	        '.popover{border: 2px solid #ddd;color: #fff;box-shadow: 0 0 6px #000; -moz-box-shadow: 0 0 6px #000;-webkit-box-shadow: 0 0 6px #000; -o-box-shadow: 0 0 6px #000;padding: 4px 10px 4px 10px;border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; -o-border-radius: 6px;}' +
	        '</style><div class="arrow">' +
	        '</div>' +
	        '<h3 class="popover-title"></h3><div class="popover-content"></div></div>';
	    
        jQuery(element).popover({
            content: html,
            placement: 'bottom',
            html: true,
            template:template,
            trigger: 'onKeyPress',
        });

        setTimeout(function(){jQuery(element).popover('show');}, 0);
        jQuery(element).closest('td').css({'opacity': 1});
		
	},
	
	/**
     * Function to Validate and Save Event 
     * @returns {undefined}
     */
    registerValidation : function () {

        var editViewForm = this.getForm();
        this.formValidatorInstance = editViewForm.vtValidate({
        	'ignore' : ".ignore-validation,.select2-input,:not(:visible)",
            submitHandler : function() {
                var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                editViewForm.trigger(e);
                if(e.isDefaultPrevented()) {
                    return false;
                }
				window.onbeforeunload = null;
                editViewForm.find('.saveButton').attr('disabled',true);
                return true;
            },
            errorPlacement: function(error, element) {
    			if($(error).text()) {

    				if(element.hasClass('select2')) {
    					element = app.helper.getSelect2FromSelect(element);
    				}

    				var positionsConf = {
    					my: 'bottom left',
    					at: 'top left'
    				};

    				var positionContainer = element.closest('.editViewContents');
    				if(element.closest('.editViewPageDiv').length){
    					positionContainer = element[0];
    				}
    				if(!positionContainer.length) {
    					if(element.closest('#overlayPageContent').length) {
    						var overlayElement = element.closest('#overlayPageContent');
    						positionsConf.adjust = {
    							x: parseInt(overlayElement.css('margin-left'))
    						};
    						positionContainer = overlayElement;
    					}

    					if(element.closest('.slimScrollDiv').length) {
    						positionContainer = element.closest('.slimScrollDiv');
    					} else if(element.closest('.mCustomScrollbar').length) {
    						positionContainer = element.closest('.mCSB_container');
    					} else if(element.closest('.modal').length){
    						positionContainer = element.closest('.modal');
    					} else {
    						positionContainer = element.closest('form');
    					}
    				}

    				if(positionContainer.length) {
    					positionsConf.container = positionContainer;
    					if(element.closest('#overlayPageContent').length) {
    						var overlayElement = element.closest('#overlayPageContent');
    						positionsConf.adjust = {
    							x: parseInt(overlayElement.css('margin-left'))
    						};
    						positionContainer = overlayElement;
    					}
    				}

    				element.qtip({
    					content: {
    						text: $(error).text()
    					},
    					show: {
    						event: 'Vtiger.Validation.Show.Messsage'
    					},
    					hide: {
    						event: 'Vtiger.Validation.Hide.Messsage'
    					},
    					position: positionsConf,
    					style: {
    						classes: 'qtip-red qtip-shadow'
    					},
    					events : {
    						render: function(event, api) {
    							var tooltip = api.elements.tooltip;
    							setTimeout(function() {
    								tooltip.hide();
    							}, 5000);
    							tooltip.on('click', function(event, api) {
    								tooltip.hide();
    							});
    						}
    					}
    				});
    				
    				if($('.related-tabs').length){
    					var eleId = element.closest('.fieldBlockContainer').data('block');
    					$('.related-tabs ul li').each(function(index,blockEle){
    						var link = $(blockEle).find('.tablinks').attr('href');
    						if(link == '#'+eleId){
    							$(blockEle).find('.tablinks').css('background-color','#ff9e9e !important');
    						}
    					});
    				}
    				element.trigger('Vtiger.Validation.Show.Messsage');
    			}
    			
    		},
            invalidHandler: function (event, validator) {   
    			if($('.related-tabs').length){
    				app.helper.showErrorNotification({message: 'Mandatory field missing'});
    			}
            },
        });
    },
    
    
    registerEventsHoldFollowUpField : function(container){
		
		var showFollowUp = container.find('[name="events_show_follow_up"]').val();
		
		var defaultStatus = container.find('select[name="eventstatus"]').val();
		
		var ContainerElement = container.find('[name="events_show_follow_up"]').closest("td");
		
		var ContainerNextElement = ContainerElement.next("td");
		
		if(typeof showFollowUp != 'undefined' && showFollowUp == 'no' && !ContainerElement.closest("tr").hasClass("followUpContainer")){
			
			if(!ContainerElement.hasClass("followUpContainer"))
				ContainerElement.addClass("hide followUpContainer");
			
			if(!ContainerNextElement.hasClass("followUpContainer"))
				ContainerNextElement.addClass("hide followUpContainer");
			
			if(!ContainerElement.closest("tr").find(".empty_fields").length)
				ContainerElement.closest("tr").append("<td class='empty_fields'>&nbsp;</td><td class='empty_fields'>&nbsp;</td>");
			
			if(defaultStatus == 'Held')
				container.find(".empty_fields").hide();
		}
	},
	

	/**
	 * Function to register the event status change event
	 */
	registerEventStatusChangeEvent : function(container){
		var followupContainer = container.find('.followUpContainer');
		//if default value is set to Held then display follow up container
		var defaultStatus = container.find('select[name="eventstatus"]').val();
		if(defaultStatus == 'Held'){
			followupContainer.show();
		}
		container.find('select[name="eventstatus"]').on('change',function(e){
			var selectedOption = jQuery(e.currentTarget).val();
			if(selectedOption == 'Held'){
				followupContainer.show();
				if(container.find(".empty_fields").length > 0)
					container.find(".empty_fields").hide();
			} else{
				if(container.find(".empty_fields").length > 0)
					container.find(".empty_fields").show();
				followupContainer.hide();
			}
		});
	},
	
	registerEventForGetTemplateData : function(){
		var thisInstance = this;
		jQuery(document).on('change', '[name="template_id"]', function(){
			
			var module = 'CalendarTemplate';
			var tempValue = $(this).val();
			var data = {};
			data['record'] = tempValue;
			data['source_module'] = module;
			thisInstance.getRecordDetails(data).then(function(response){
				var tmpData = response.data;
				
				jQuery('[name="subject"]').val(tmpData.subject);
				jQuery('[name="location"]').val(tmpData.location);

				jQuery('[name="description"]').val(tmpData.description);
				
				if(tmpData.send_mail_reminder){
					jQuery('[name="set_reminder"]').prop('checked',true).trigger('change');
				}
				if(tmpData.send_contact_notification){
					jQuery('[name="sendnotification"]').prop('checked',true);
				}
				
				var str = tmpData.category_default;
				var categeory = str.split(" |##| ");
			
				$('select[name="cf_3294[]"]').val(categeory).trigger("change");

				$('select[name="activitytype"]').val(tmpData.activitytype).trigger("change");
				
				$('select[name="taskpriority"]').val(tmpData.taskpriority).trigger("change");
			},
			function(error, err){

			});
		});
	},
	 
});