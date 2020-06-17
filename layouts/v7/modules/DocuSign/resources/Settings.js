/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Settings_DocuSign_Js",{},{
	
	/*
	 * function to Save the Outgoing Server Details
	 */
	saveDocuSignDetails : function(form) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var data = form.serializeFormData();
		var params = {
		'module' : app.getModuleName(),
		'action': 'SaveAuthSettings'
			};
          
       jQuery.extend(params,data);
		app.request.post({'data' : params}).then(
			function(err, data) {
                app.helper.showProgress();
                
                if(data.success == true){
					
                    var docusignDetailUrl = form.data('url');
                	thisInstance.loadContents(docusignDetailUrl).then(function(data) {
						jQuery('.settingsPageDiv').html(data);
						thisInstance.registerDetailViewEvents();
                        app.helper.hideProgress();
					});
                	aDeferred.resolve(data);
                }else {
                	app.helper.hideProgress();
					jQuery('.errorMessage', form).removeClass('hide');
                    aDeferred.reject();
				}
			}
		);
        return aDeferred.promise();
	},
	
    
	loadContents : function(url) {
		var aDeferred = jQuery.Deferred();
		app.request.pjax({"url" : url}).then(
			function(err, data){
                if(err === null){
				jQuery('.settingsPageDiv ').html(data);
				aDeferred.resolve(data);
            }
			},
			function(error, err){
				aDeferred.reject();
			}
		);
		return aDeferred.promise();
	},
	
	/*
	 * function to register the events in editView
	 */
	registerEditViewEvents : function(e) {
		var thisInstance = this;
		var form = jQuery('#updatedetails');
		var cancelLink = jQuery('.cancelLink', form);
       
		//register validation engine
		var params = {
            submitHandler : function(form) {
            	app.helper.showProgress();
                var form = jQuery(form);
				thisInstance.saveDocuSignDetails(form);
            }
		};
		if (form.length) {
        	form.vtValidate(params);
		 	form.on('submit', function(e){
            	e.preventDefault();
            	return false;
        	});
		}
		
		//register click event for cancelLink
		cancelLink.click(function(e) {
			var docusignDetailUrl = form.data('url');
			
			thisInstance.loadContents(docusignDetailUrl).then(
				function(data) {
                     jQuery('.editViewPageDiv').html(data);
					//after loading contents, register the events
					thisInstance.registerDetailViewEvents();
				}
			);
		});
	},
	
	/*
	 * function to register the events in DetailView
	 */
	registerDetailViewEvents : function() {
		var thisInstance = this;
		//Detail view container
		var container = jQuery('#docusign');
		var editButton = jQuery('.editButton', container);
		//register click event for edit button
		editButton.click(function(e) {
			app.helper.showProgress();

			var url = editButton.data('url');
			thisInstance.loadContents(url).then(
				function(data) {
                    jQuery('.settingsPageDiv ').html(data);
                    app.helper.hideProgress();
					//after load the contents register the edit view events
					thisInstance.registerEditViewEvents();
                    
				}
			);
		});
	},
   
    
	registerEvents: function() {
		var thisInstance = this;
		thisInstance.registerEditViewEvents();
		thisInstance.registerDetailViewEvents();
		
	}

});


jQuery(document).ready(function(){
	var instance = new Settings_DocuSign_Js();
	instance.registerEvents();
	
	var vtigerInstance = Vtiger_Index_Js.getInstance();
	vtigerInstance.registerEvents();
})
