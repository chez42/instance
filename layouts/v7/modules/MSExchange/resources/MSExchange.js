jQuery.Class("MSExchange_Index_Js",{
	
	getInstance : function() {
		return new MSExchange_Index_Js();
	},
	
},{
	
	registerExchangeFormSubmitEvent : function() {
		
		if(jQuery('#exchangeSettingForm').length > 0){
			
			var form = jQuery('#exchangeSettingForm');
	        
			var params = {
				submitHandler : function(form) {
					var form = jQuery(form);
					app.helper.showProgress();
					var formData = form.serializeFormData();
					app.request.post({data: formData}).then(
						function(err, data) {
							app.helper.hideProgress();
							if(data.success){
								window.location.href = 'index.php?module='+formData.module+'&parent=Settings&view=Extension&extensionModule='+formData.module+'&extensionView=Index&mode=GlobalSettings'; 
							} else {
								app.helper.showErrorNotification({message: app.vtranslate(data.message)});
							}
						}
					);
				}
	        };
	        
	        form.vtValidate(params);
		}
		
        jQuery(".extensionEditContents").on('click', '.cancelLink', function(e){
        	$(".extensionEditContents").addClass("hide");
			$(".extensionDetailContents").removeClass("hide");
		});
    },
    
	registerEditMSExchangeConfigEvent : function(){
		jQuery('.editMSExchangeConfigButton', $(".extensionDetailContents")).on("click", function(){
			$(".extensionDetailContents").addClass("hide");
			$(".extensionEditContents").removeClass("hide");
			vtUtils.applyFieldElementsView($(".extensionEditContents"));
		});
	},
	
	registerExchangeImpersonationChangeEvent : function(){
		var self = this;
		jQuery('.extensionEditContents').find('input[name="ms_exchange_user_impersonation"]').on('change', function(e){
			 var element =jQuery(e.currentTarget);
			 if(element.is(':checked')) {
				 jQuery('.extensionEditContents #user_impersonation_types').trigger("click");
				 jQuery(".extensionEditContents").find(".userAdminCredx").removeClass("hide");
			 } else {
				 jQuery(".extensionEditContents").find(".userAdminCredx").addClass("hide");
			 }
		});
	},
	
	registerImpersonationTypeChangeEvent : function(){
		var self = this;
		jQuery('.extensionEditContents #user_impersonation_types').on('change', function(e){
			self.triggerImpersionationFieldLabelChangeEvent();
		});
	},
	
	triggerImpersionationFieldLabelChangeEvent : function(){
		var parentElement = jQuery(".extensionEditContents #user_impersonation_types").parents(".userAdminCredx").next(".userAdminCredx");
		var selectedImpersonationType = jQuery(".extensionEditContents #user_impersonation_types").val();
		if(selectedImpersonationType == 'upn'){
			parentElement.find("label").html("User Principle Name<span class='redColor'>*</span>");
		} else if(selectedImpersonationType == 'smtp_address'){
			parentElement.find("label").html("SMTP email address<span class='redColor'>*</span>");
		} else {
			parentElement.find("label").html("SID" + '<span class="redColor">*</span>');
		}
	},
	
	registerEvents: function(){
		this.registerEditMSExchangeConfigEvent();
		this.registerExchangeFormSubmitEvent();
		this.registerExchangeImpersonationChangeEvent();
		this.registerImpersonationTypeChangeEvent();
		this.triggerImpersionationFieldLabelChangeEvent();
		jQuery('.extensionEditContents').find('input[name="ms_exchange_user_impersonation"]').trigger('change');
		if($(".installationContents").length > 0){
    		this.registerActivateLicenseEvent();
    	}
		this.registerLicenseSettingEditEvent();
	},
	
	registerActivateLicenseEvent : function() {
        var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
            var license_key=jQuery('#license_key');
            if(license_key.val()=='') {
                app.helper.showAlertBox({message:"License Key cannot be empty"});
                aDeferred.reject();
                return aDeferred.promise();
            }else{
                app.helper.showProgress();
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();

                app.request.post({data:params}).then(
                    function(err, data) {
                    	if(err === null) {
                            var message=data.message;
                            if(message == 'Valid License') {
                            	window.location.href = "index.php?module=MSExchange&parent=Settings&view=Extension&extensionModule=MSExchange&extensionView=Index&mode=GlobalSettings";
                            }else{
                                app.helper.showErrorNotification({"message": message});
                            }
                            app.helper.hideProgress();
                        }
                        else {
                            app.helper.hideProgress();
                        }
                    }
                );
            }
        });
    },
    
    registerLicenseSettingEditEvent : function(){
    	var self = this;
    	jQuery("#licenseSettingButton").on("click", function(){
    		var params = {
    			module : app.getModuleName(),
    			view : 'Index',
    			mode : 'showLicenseSettings'
    		};
    		app.request.post({data:params}).then(
    			function(err, data) {
    				if(err === null) {
                    	jQuery(".extensionDetailContents").html(data);
                    	self.registerUpgradeOfficeLicenseEvent();
                    }
    			}
    		);
    	});
    },
    
    registerUpgradeOfficeLicenseEvent : function(){
    	var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnReActivate"]').click(function() {
            var license_key=jQuery('#license_key');
            if(license_key.val()=='') {
                app.helper.showAlertBox({message:"License Key cannot be empty"});
                aDeferred.reject();
                return aDeferred.promise();
            }else{
                app.helper.showProgress();
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'upgrade';
                params['license'] = license_key.val();

                app.request.post({data:params}).then(
                    function(err, data) {
                    	if(err === null) {
                            var message=data.message;
                            if(message == 'Valid License') {
                                app.helper.showSuccessNotification({"message": message});
                                location.reload();
                            }else{
                            	app.helper.showErrorNotification({"message": message});
                            }
                            app.helper.hideProgress();
                        } else {
                            app.helper.hideProgress();
                        }
                    }
                );
            }
        });
    }
});

jQuery("document").ready(function(){
	var instance = MSExchange_Index_Js.getInstance();
	//instance.registerEvents();
});