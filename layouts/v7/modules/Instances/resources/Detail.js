/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Instances_Detail_Js",{
	
	triggerGetAllModules: function() {
		
		var self = this;
		var moduleName = app.getModuleName();
		var record = app.getRecordId();
		
		var params = {
			'module' : moduleName,
			'record' : record,
			'view' 	 : 'ModulesList'
		};
		app.helper.showProgress();
		app.request.post({data: params}).then(function(err, data) {
			
			app.helper.showModal(data, {cb : function() {
				self.registerUpdateInstanceModules(data);
				app.helper.hideProgress();
				
			}});
			
		});
	},
	
	registerUpdateInstanceModules : function(data){
		
		var thisInstance = this;
		var form = jQuery('#moduleListEditView');
		
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				form.find('[name="saveButton"]').attr('disabled', 'disabled');
				thisInstance.saveModulesList(form);
			}
		}
		form.vtValidate(params);

		form.submit(function (e) {
			e.preventDefault();
		});
	},
	
	saveModulesList: function (form) {
		
		var thisInstance = this;
		var params = form.serializeFormData();
		
		params.module = 'Instances';
		params.action = 'SaveModulesList';
		params.record = app.getRecordId();
		
		app.helper.showProgress();
		app.request.post({'data': params}).then(function (err, data) {
			
			if (typeof data != 'undefined') {
			
				if(data.success){
					app.helper.hideModal();
					app.helper.hideProgress();
					app.helper.showSuccessNotification({'message': 'Modules updated successfully.'});
				}
				
			} else {
				app.helper.hideProgress();
				app.helper.showErrorNotification({'message': 'Something went wrong try again later.'});
			}
		});
	},
	
	
	triggerGetAllUsers :function(){
		
		var self = this;
		var moduleName = app.getModuleName();
		var record = app.getRecordId();
		
		var params = {
			'module' : moduleName,
			'record' : record,
			'view' 	 : 'ManageInstance'
		};
		app.helper.showProgress();
		app.request.post({data: params}).then(function(err, data) {
			 app.helper.showModal(data,{
             	'cb': function (modal) {
             		app.helper.hideProgress();
             		var form = jQuery(modal).find('#massSaveInstance');
         			var params = {
         	            submitHandler : function(form) {
         	                app.helper.showProgress();
         	                var form = jQuery(form);
         	                var params = form.serializeFormData();
         	                app.request.post({'data': params}).then(function (err, data) {
            	                if (typeof data != 'undefined') {	
            	                	app.helper.hideModal();
            	                	app.helper.hideProgress();
            	                	app.helper.showSuccessNotification({message: 'Value Update Successfully!'});
                	            } else {
                    				app.helper.hideProgress();
                    				app.helper.showErrorNotification({'message': err['message']});
                    			}
         	                });
         	            }
         			};
         			if (form.length) {
         				form.vtValidate(params);
         			 	form.on('submit', function(e){
         	            	e.preventDefault();
         	            	return false;
         	        	});
         			}
             	}
             });
		});
		
	},
	
},{
	
	
	
});