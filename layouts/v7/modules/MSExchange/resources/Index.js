/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/  

Vtiger_ExtensionCommon_Js("MSExchange_Index_Js", {}, {

    init : function() {
        this.addComponents();
    },
    
    addComponents : function() {
        this.addComponent('MSExchange_Settings_Js');
    },
    
    registerSyncNowButton : function(container) {
        container.on('click', '.syncNow', function(e) {
            var params = {
                module : 'MSExchange',
                view : 'Sync',
                source_module : jQuery("#source_module").val()
            }
            app.helper.showProgress();
            app.request.post({data: params}).then(function(error, data){
                app.helper.hideProgress();
				if(data.success){
					var hasMoreVtigerRecords = false;
					var hasMoreGoogleRecords = false;
					jQuery.each(data, function(module, syncInfo){
						if(module == 'success')return true;
						hasMoreVtigerRecords = false;
						hasMoreGoogleRecords = false;
						if(syncInfo['msexchange'].more === true) {
							hasMoreGoogleRecords = true;
							app.helper.showAlertNotification({message : app.vtranslate('JS_MORE_MSEXCHANGE')});
						}
						if(syncInfo['vtiger'].more === true) {
							hasMoreVtigerRecords = true;
							app.helper.showAlertNotification({message : app.vtranslate('JS_MORE_VTIGER')});
						}
					});
					if(hasMoreVtigerRecords || hasMoreGoogleRecords) {
						setTimeout(3000);
					}
					window.location.reload();
                } else {
                	app.helper.showErrorNotification({message : data.error});
				}
            });
        });
    },
    
    registerSettingsMenuClickEvent : function(container) {
    	var self = this;
    	container.on('click', '.settingsPage', function(e, returnToLogs) {
            var element = jQuery(e.currentTarget);
            var url = element.data('url');
            if(!url) {
                return;
            }
            
            if(returnToLogs == 'No'){
            	var settingParams = app.convertUrlToDataParams(url);
            	delete settingParams.returnToLogs;
            	var url = 'index.php?'+jQuery.param(settingParams);
            }
            
            var params = {
                url : url
            }
            app.helper.showProgress();
            app.request.pjax(params).then(function(error, data){
                app.helper.hideProgress();
                if(data) {
                    container.html(data);
                    app.event.trigger(MSExchange_Settings_Js.postSettingsLoad, container);
                    self.registerRevokeMSAccountClickEvent();
                }
            });
        });
    },
    
    registerRevokeMSAccountClickEvent : function(){
		
		jQuery(".revokeMSAccount").on("click", function(e){
			
			e.preventDefault();
			
			var targetElement = jQuery(e.currentTarget).parents(".ext-actions");
			
			var elemData = jQuery(e.currentTarget).data();
			
			var params = {
				module : jQuery("#ext-module").val(),
				view : 'List',
				operation : 'deleteSync',
				sourcemodule : jQuery("#source_module").val()
			};
			
			app.request.post({data: params}).then(function(error, data){
				
				if(elemData.refreshPage)
					window.location.reload();
				else
					jQuery(".settingsPage", targetElement).trigger("click",['No']);
			});       
		});
	},
    
	registerAjaxEvents : function(container) {
		var self = this;
        container.on('click', '.navigationLink', function(e) {
            var element = jQuery(e.currentTarget);
            var url = element.data('url');
            
            if(!url) {
                return;
            }
            
            var params = {
                url : url
            }
            app.helper.showProgress();
            app.request.pjax(params).then(function(error, data){
                app.helper.hideProgress();
                if(data) {
                    container.html(data);
                    self.registerRevokeMSAccountClickEvent();
                }
            });
        });
    },
    
    registerEvents : function() {
    	this._super();
        var container = this.getListContainer();
        this.registerSyncNowButton(container);
        this.registerRevokeMSAccountClickEvent();
        app.event.trigger(MSExchange_Settings_Js.postSettingsLoad, container);
    }
});
