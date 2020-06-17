Vtiger.Class("MSExchange_Settings_Js", {
    
    postSettingsLoad : "MSExchange.Settings.load"
    
}, {
    
    getListContainer : function() {
        var container = jQuery('.listViewPageDiv');
        if(app.getParentModuleName() === 'Settings') {
            container = jQuery('.settingsPageDiv');
        }
        return container;
    },
    
    registerBasicEvents : function() {
        this.registerFieldMappingClickEvent();
        vtUtils.applyFieldElementsView(this.getListContainer());
    },
    
    registerEvents : function() {
    	var thisInstance = this;
        app.event.on(MSExchange_Settings_Js.postSettingsLoad,function(){
            thisInstance.registerBasicEvents();
            thisInstance.registerEventForSaveSettingsForSync();
        });
    },
    
    saveSettings : function(){
    		
    	var aDeferred = jQuery.Deferred();
		
    	var form = jQuery("[name='settingsForm']");
    	
    	var params = form.serializeFormData();
    	
    	params.action = "SaveAjax";
    	
    	app.request.post({data: params}).then(function (err, res) {
    		aDeferred.resolve(true);
    	});
            
    	return aDeferred.promise();
    },
    
    registerFieldMappingClickEvent : function() {
        var thisInstance = this;
        jQuery('a#syncSetting').on('click',function(e) {
			var syncModule = jQuery(e.currentTarget).data('syncModule');
            var params = {
                module : 'MSExchange',
                view : 'Setting',
                sourcemodule : syncModule
            }
            
            app.helper.showProgress();
            app.request.post({data: params}).then(function(error, data) {
                app.helper.hideProgress();
                var callBackFunction = function() {
                	app.helper.showVerticalScroll(jQuery('.contactFMEdit .contactsFieldMappingContainer'), {'autoHideScrollbar': true, 'setHeight' : '400px'});
                    var container = jQuery('.contactFMEdit');
                    thisInstance.registerEditContactsFieldMappingEvent(container);
                    thisInstance.registerFieldMappingAddMappingClickEvent(container);
                    thisInstance.registerEventToDeleteMapping(container);
                }
                var modalData = {
                    cb: callBackFunction
                };
                app.helper.showModal(data, modalData);
            });
        });
    },
    
    registerEditContactsFieldMappingEvent : function(container){
    	
    	container.find("#editContactMapping").on("click", function(){
    		container.find("#exchangeDetailFieldMapping").addClass("hide");
    		container.find("#exchangeEditFieldMapping").removeClass("hide");
    		container.find(".modal-footer").removeClass("hide");
    		jQuery("#cancelEditing", container).on("click", function(){
    			container.find("#exchangeDetailFieldMapping").removeClass("hide");
        		container.find("#exchangeEditFieldMapping").addClass("hide");
        		container.find(".modal-footer").addClass("hide");
    		});
    		jQuery("#save_field_mapping", container).on("click", function(){
    			app.helper.showProgress();
    			var form = jQuery("form[name='contactsyncsettings']");
    			var params = form.serializeFormData();
    	    	app.request.post({data: params}).then(function (err, res) {
    	    		app.helper.hideModal();
    	    		app.helper.hideProgress();
    	    		app.helper.showSuccessNotification({'message' : app.vtranslate('Save successfully')});
    	    	});
    		});
    		app.helper.showVerticalScroll(jQuery('.contactFMEdit .contactsFieldMappingContainer'), {'autoHideScrollbar': true, 'setHeight' : '400px'});
        });
    },
    
    registerFieldMappingAddMappingClickEvent : function(container){
    	
    	container.find("#msexchangesync_addcustommapping").on("click", function(){
    		var lastSequenceNumber = jQuery("#contactExchangeMapping").find('tr.customMapping').length;
			var newSequenceNumber = parseInt(lastSequenceNumber)+1;
			var newMapping = jQuery('.newMapping').clone(true,true);
			newMapping.find('select.crm_fields').attr("name",'mapping['+newSequenceNumber+'][CRM]');
			newMapping.find('select.exchange_fields').attr("name",'mapping['+newSequenceNumber+'][MSExchange]');
			newMapping.removeClass('hide newMapping').addClass("customMapping");
			newMapping.appendTo(jQuery("#contactExchangeMapping"));
			newMapping.find('.exchange_fields').addClass('select2');
			newMapping.find('.crm_fields').addClass('select2');
			var select2Elements = newMapping.find('.select2');
			vtUtils.showSelect2ElementView(select2Elements);
		});
    },
    
    registerEventToDeleteMapping : function(container){
		container.on('click','.deleteMapping',function(e){
			var element = jQuery(e.currentTarget);
			var mappingContainer = element.closest('tr.customMapping');
			mappingContainer.remove();
			if(container.find("tr.customMapping").length > 0){
				container.find("tr.customMapping").each(function(index, trElem){
					var newSequenceNumber = parseInt(index)+1;
					$(trElem).find('select.crm_fields').attr("name",'mapping['+newSequenceNumber+'][CRM]');
					$(trElem).find('select.exchange_fields').attr("name",'mapping['+newSequenceNumber+'][MSExchange]');
				});
			}
		});
	},
	
	registerEventForSaveSettingsForSync : function(){
		
		$('#saveSettings').on('click', function(e){
			e.preventDefault();
			var fieldName = 'impersonation_identifier';
			
			if($("[name="+fieldName+"]").val()){
				var params = {
						'module': 'Users',
						'action' : "CheckExchange",
						'record' : app.getUserId(),
						'user_principal_name' : $("[name="+fieldName+"]").val(),
					}
				app.helper.showProgress();
				app.request.post({data:params}).then(
					function(err,data) {
						if(data){
							if(data.success){
								$('#saveSettings').submit();
								app.helper.hideProgress();
							}else{
								app.helper.hideProgress();
								app.helper.showErrorNotification({
									title:app.vtranslate(data.message),
									message :app.vtranslate(data.error)+' For MsExchange'
								});
							}
						}
					}
				);
			}
		});
		
	},
	
});