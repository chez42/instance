/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("Settings_Vtiger_ConfigurePortalEditableProfileFields_Js",{},{
	

	configurePortalFieldsEvent: function () {
		var thisInstance = this;

		var form = jQuery('.configurePortalFields');

		form.find('select').on('change', function () {
			form.find('.formFooter').addClass('show').removeClass('hide');
		});

		form.find('.cancelLink').on('click', function () {
			form.find('.formFooter').addClass('hide').removeClass('show');
		});
		
		var selectElement = form.find('select').addClass('select2');
		vtUtils.showSelect2ElementView(selectElement,{maximumSelectionSize: 15});
		var chozenChoiceElement = jQuery("#s2id_fieldsList").find('ul.select2-choices');
		chozenChoiceElement.sortable({
			'containment': chozenChoiceElement,
			start: function() { },
			update: function() {
				form.find('.formFooter').addClass('show').removeClass('hide');
			}
		});
		
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				
				var params = form.serializeFormData();
				
				if ((typeof params['fieldIdsList[]'] == 'undefined') && (typeof params['fieldIdsList'] == 'undefined')) {
					params['fieldIdsList'] = '';
				}
				var selectValueElements = selectElement.select2('data');
				var selectedValues = [];
				for(i=0; i<selectValueElements.length; i++) {
					selectedValues.push(selectValueElements[i].id);
				}
				if(selectedValues.length)
					params['fieldIdsList[]'] = selectedValues;
				
				app.helper.showProgress();
				app.request.post({'data': params}).then(function (error, data) {
					app.helper.hideProgress();
					if (error == null) {
						var message = app.vtranslate('Portal fields configure successfully');
						app.helper.showSuccessNotification({'message': message});
						form.find('.formFooter').removeClass('show').addClass('hide');
						window.location.reload();
					} else {
						app.helper.showErrorNotification({'message': app.vtranslate('Operation failed!')});
					}
				});
				return false;
			}
		}
		if (form.length) {
        	form.vtValidate(params);
		 	form.on('submit', function(e){
            	e.preventDefault();
            	return false;
        	});
		}
			
	},
    
	registerEvents: function() {
		var thisInstance = this;
		thisInstance.configurePortalFieldsEvent();
	}

});
