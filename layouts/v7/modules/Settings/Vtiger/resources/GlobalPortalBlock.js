/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("Settings_Vtiger_GlobalPortalBlock_Js",{},{
	
    saveGlobalPoratlPermissions : function(form) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var data = form.serializeFormData();
		var formData = new FormData(form[0]);
		var params = {
			url: "index.php",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false
		};
		app.helper.showProgress();
		app.request.post(params).then(
			function (err, data) {
                app.helper.showProgress();
                
                if(data.success == true){
					
                    var globalPortlDetailUrl = form.data('url');
                	thisInstance.loadContents(globalPortlDetailUrl).then(function(data) {
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
	
	
	registerEditViewEvents : function(e) {
		var thisInstance = this;
		var form = jQuery('#defaultPortalPermission');
		var cancelLink = jQuery('.cancelLink', form);
       
		var params = {
            submitHandler : function(form) {
            	app.helper.showProgress();
                var form = jQuery(form);
				thisInstance.saveGlobalPoratlPermissions(form);
            }
		};
		if (form.length) {
        	form.vtValidate(params);
		 	form.on('submit', function(e){
            	e.preventDefault();
            	return false;
        	});
		}
		
		cancelLink.click(function(e) {
			var globalPortlDetailUrl = form.data('url');
			
			thisInstance.loadContents(globalPortlDetailUrl).then(
				function(data) {
                     jQuery('.editViewPageDiv').html(data);
					//after loading contents, register the events
					thisInstance.registerDetailViewEvents();
				}
			);
		});
	},
	
	
	registerDetailViewEvents : function() {
		var thisInstance = this;
		var container = jQuery('#globalportal');
		var editButton = jQuery('.editButton', container);
		editButton.click(function(e) {
			app.helper.showProgress();

			var url = editButton.data('url');
			thisInstance.loadContents(url).then(
				function(data) {
                    jQuery('.settingsPageDiv ').html(data);
                    app.helper.hideProgress();
					thisInstance.registerEditViewEvents();
                    
				}
			);
		});
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
	    
    
	registerEvents: function() {
		var thisInstance = this;
		console.log('fdsf')
		thisInstance.registerEditViewEvents();
		thisInstance.registerDetailViewEvents();
		thisInstance.registerEnableDisablePortalReportsEvent();
		
	}

});

jQuery(document).ready(function() {
    var instance = new Settings_Vtiger_GlobalPortalBlock_Js();
    instance.registerEvents();
});
