/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("Settings_Vtiger_QuickCreateMenu_Js",{},{
	
	init : function() {
	       this.addComponents();
	    },
	   
    addComponents : function (){
        this.addModuleSpecificComponent('Index',app.module(), app.getParentModuleName());
    },
    
	registerEventForQuickCreateSeq: function () {
		var thisInstance = this;
		var container = jQuery('.quickCreateMenuDiv');
			
		var form = jQuery('.QuickCreateMenuForm');

		form.find('select').on('change', function () {
			form.find('.formFooter').addClass('show').removeClass('hide');
		});

		form.find('.cancelLink').on('click', function () {
			form.find('.formFooter').addClass('hide').removeClass('show');
		});
		
		var selectElement = form.find('select').addClass('select2');
//		vtUtils.showSelect2ElementView(selectElement,{maximumSelectionSize: 15});
		vtUtils.showSelect2ElementView(selectElement);
		var chozenChoiceElement = jQuery("#s2id_moduleList").find('ul.select2-choices');
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
				
				if ((typeof params['moduleIdsList[]'] == 'undefined') && (typeof params['moduleIdsList'] == 'undefined')) {
					params['moduleIdsList'] = '';
				}
				var selectValueElements = selectElement.select2('data');
				var selectedValues = [];
				for(i=0; i<selectValueElements.length; i++) {
					selectedValues.push(selectValueElements[i].id);
				}
				if(selectedValues.length)
					params['moduleIdsList[]'] = selectedValues;
				
				app.helper.showProgress();
				app.request.post({'data': params}).then(function (error, data) {
					app.helper.hideProgress();
					if (error == null) {
						var message = app.vtranslate('Quick Create Menu sequence updated Successfully');
						app.helper.showSuccessNotification({'message': message});
						form.find('.formFooter').removeClass('show').addClass('hide');
					} else {
						app.helper.showErrorNotification({'message': app.vtranslate('Operation Failed')});
					}
				});
				return false;
			}
		}
		form.vtValidate(params);
	},
    
	registerEvents: function() {
		var thisInstance = this;
		thisInstance.registerEventForQuickCreateSeq();
	}

});

