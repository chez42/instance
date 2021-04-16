/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("Billing_List_Js", {
    
    exportTDAFormat : function(url) {
    	var self = app.controller();
        self.exportTDAFormat(url);
    },
    
	exportFidelityFormat : function(url) {
    	var self = app.controller();
        self.exportFidelityFormat(url);
    },
	
}, {
    
    exportTDAFormat : function(url) {
        
		var listInstance = this;
        
		var validationResult = listInstance.checkListRecordSelected();
		
		if(!validationResult){
			
			var postData = listInstance.getListSelectAllParams(true);

			var params = {
				"url":url,
				"data" : postData
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerExportFileModalEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
	
	exportFidelityFormat : function(url) {
        
		var listInstance = this;
        
		var validationResult = listInstance.checkListRecordSelected();
		
		if(!validationResult){
			
			var postData = listInstance.getListSelectAllParams(true);

			var params = {
				"url":url,
				"data" : postData
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerExportFileModalEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
	
	registerExportFileModalEvents : function(container) {
        
		var self = this;
        
		var addFolderForm = jQuery('#exportFile');
		
        addFolderForm.vtValidate({
            
			submitHandler: function(form) {
            	
                var formData = addFolderForm.serializeFormData();
                app.helper.showProgress();
                form.submit();
				app.helper.hideModal();
				app.helper.hideProgress();
					
            }
        });
    },
	
	
	
	registerEvents: function() {
        this._super();
	}

});