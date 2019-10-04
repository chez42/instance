/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Edit_Js("Contacts_Edit_Js",{},{
	
	//Will have the mapping of address fields based on the modules
	addressFieldsMapping : {'Accounts' :
									{
										'mailingstreet' : 'ship_street',  
										'mailingcity' : 'ship_city',
										'mailingstate' : 'ship_state',
										'mailingzip' : 'ship_code'
									}
							},
							
	//Address field mapping within module
	addressFieldsMappingInModule : {
										'otherstreet' : 'mailingstreet',
										'otherpobox' : 'mailingpobox',
										'othercity' : 'mailingcity',
										'otherstate' : 'mailingstate',
										'otherzip' : 'mailingzip',
										'othercountry' : 'mailingcountry'
								},
	
        /* Function which will register event for Reference Fields Selection
        */
	registerReferenceSelectionEvent : function(container) {
            var thisInstance = this;

           jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent,function(e,data){
                thisInstance.referenceSelectionEventHandler(data, container);
            });
	},
		
	/**
	 * Reference Fields Selection Event Handler
	 * On Confirmation It will copy the address details
	 */
	referenceSelectionEventHandler :  function(data, container) {
		var thisInstance = this;
		var message = app.vtranslate('OVERWRITE_EXISTING_MSG1')+app.vtranslate('SINGLE_'+data['source_module'])+' ('+data['selectedName']+') '+app.vtranslate('OVERWRITE_EXISTING_MSG2');
		app.helper.showConfirmationBox({'message' : message}).then(function(e){
			thisInstance.copyAddressDetails(data, container);
		},
		function(error,err){});
	},
	
	/**
	 * Function which will copy the address details - without Confirmation
	 */
	copyAddressDetails : function(data, container) {
		var thisInstance = this;
		var sourceModule = data['source_module'];
		thisInstance.getRecordDetails(data).then(
			function(response){
				thisInstance.mapAddressDetails(thisInstance.addressFieldsMapping[sourceModule], response['data'], container);
			},
			function(error, err){

			});
	},
	
	/**
	 * Function which will map the address details of the selected record
	 */
	mapAddressDetails : function(addressDetails, result, container) {
		for(var key in addressDetails) {
            if(container.find('[name="'+key+'"]').length == 0) {
                var create = container.append("<input type='hidden' name='"+key+"'>");
            }
			container.find('[name="'+key+'"]').val(result[addressDetails[key]]);
			container.find('[name="'+key+'"]').trigger('change');
		}
	},
	
	/**
	 * Function to swap array
	 * @param Array that need to be swapped
	 */ 
	swapObject : function(objectToSwap){
		var swappedArray = {};
		var newKey,newValue;
		for(var key in objectToSwap){
			newKey = objectToSwap[key];
			newValue = key;
			swappedArray[newKey] = newValue;
		}
		return swappedArray;
	},
	
	/**
	 * Function to copy address between fields
	 * @param strings which accepts value as either odd or even
	 */
	copyAddress : function(swapMode, container){
		var thisInstance = this;
		var addressMapping = this.addressFieldsMappingInModule;
		if(swapMode == "false"){
			for(var key in addressMapping) {
				var fromElement = container.find('[name="'+key+'"]');
				var toElement = container.find('[name="'+addressMapping[key]+'"]');
				toElement.val(fromElement.val());
				if((jQuery("#massEditContainer").length) && (toElement.val()!= "") && (typeof(toElement.attr('data-validation-engine')) == "undefined")){
					toElement.attr('data-validation-engine', toElement.data('invalidValidationEngine'));
				}
			}
		} else if(swapMode){
			var swappedArray = thisInstance.swapObject(addressMapping);
			for(var key in swappedArray) {
				var fromElement = container.find('[name="'+key+'"]');
				var toElement = container.find('[name="'+swappedArray[key]+'"]');
				toElement.val(fromElement.val());
				if((jQuery("#massEditContainer").length) && (toElement.val()!= "")  && (typeof(toElement.attr('data-validation-engine')) == "undefined")){
					toElement.attr('data-validation-engine', toElement.data('invalidValidationEngine'));
				}
			}
		}
	},
	
	
	/**
	 * Function to register event for copying address between two fileds
	 */
	registerEventForCopyingAddress : function(container){
		var thisInstance = this;
		var swapMode;
		jQuery('[name="copyAddress"]').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var target = element.data('target');
			if(target == "other"){
				swapMode = "false";
			} else if(target == "mailing"){
				swapMode = "true";
			}
			thisInstance.copyAddress(swapMode, container);
		})
	},

	/**
	 * Function to check for Portal User
	 */
	checkForPortalUser: function (form) {
		var element = jQuery('[name="portal"]', form);
		var response = element.is(':checked');
		var primaryEmailField = jQuery('[name="email"]');
		var primaryEmailValue = primaryEmailField.val();
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
	/**
	 * Function to register recordpresave event
	 */
	registerRecordPreSaveEvent: function (form) {
		var thisInstance = this;
		if (typeof form == 'undefined') {
			form = this.getForm();
		}

		app.event.on(Vtiger_Edit_Js.recordPresaveEvent, function (e) {
			var result = thisInstance.checkForPortalUser(form);
			if (!result) {
				e.preventDefault();
			}
		});

	},

	registerBasicEvents: function (container) {
		this._super(container);
		this.registerEventForCopyingAddress(container);
		this.registerRecordPreSaveEvent(container);
		this.registerReferenceSelectionEvent(container);
		this.registerEventForEnablingPortal();
	},
	
	registerEvents : function(){
		this._super();
		this.registerEnableDisablePortalReportsEvent();
	},
	
	registerEnableDisablePortalReportsEvent : function(){
		
		jQuery(".mainmodule").on("click", function(e){
			e.preventDefault();
			var className = $(this).data('value');
			var check = '';
			
			jQuery("."+className).each(function(index, elem){
				if($(this). prop("checked") == false){
					check = true;
				}
			});
			jQuery("."+className).each(function(index, elem){
				if($(this). prop("checked") == true){
					check = false;
				}
			});
			
			if(check == true){
				 jQuery("."+className).each(function(index, elem){
					$(this).prop('checked', true);
				 });
			}else if(check == false){
				jQuery("."+className).each(function(index, elem){
					$(this).prop('checked', false);
				});
			}
			
		});
	},
	
	/**
	 * Function to register event for enabling portal
	 * When portal is enabled some of the fields need
	 * to be check for mandatory validation
	 */
	registerEventForEnablingPortal : function(){
		var thisInstance = this;
		var form = this.getForm();
		var enablePortalField = form.find('[name="portal"]');
        
		var validationToggleFields = form.find('[name="portal_password"]');
		enablePortalField.on('change',function(e){
			var element = jQuery(e.currentTarget);
			var addValidation;
			if(element.is(':checked')){
				addValidation = true;
			}else{
				addValidation = false;
			}
			
			//If validation need to be added for new elements,then we need to detach and attach validation
			//to form
			if(addValidation){
				thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, true);
			}else{
				thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, false);
			}
		})
		if(!enablePortalField.is(":checked")){
			thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, false);
		}else if(enablePortalField.is(":checked")){
			thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, true);
		}
	},
	
	AddOrRemoveRequiredValidation : function(dependentFieldsForValidation, addValidation) {
		jQuery(dependentFieldsForValidation).each(function(key,value){
			var relatedField = jQuery(value);
			if(addValidation) {
				relatedField.removeClass('ignore-validation').data('rule-required', true);
				if(relatedField.is("select")) {
					relatedField.attr('disabled',false);
				}else {
					relatedField.removeAttr('disabled');
				}
			} else if(!addValidation) {
				relatedField.addClass('ignore-validation').removeAttr('data-rule-required');
				if(relatedField.is("select")) {
					relatedField.attr('disabled',true).trigger("change");
					var select2Element = app.helper.getSelect2FromSelect(relatedField);
					select2Element.trigger('Vtiger.Validation.Hide.Messsage');
					select2Element.find('a').removeClass('input-error');
				}else {
					relatedField.attr('disabled','disabled').trigger('Vtiger.Validation.Hide.Messsage').removeClass('input-error');
				}
			}
		});
	},
	
})