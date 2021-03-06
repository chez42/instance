/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Users_Edit_Js",{},{
	
	
	duplicateCheckCache : {},
    
	/**
	 * Function to register recordpresave event
	 */
	registerRecordPreSaveEvent : function(form){
		var thisInstance = this;
		app.event.on(Vtiger_Edit_Js.recordPresaveEvent, function(e, data) {
			var userName = jQuery('input[name="user_name"]').val();
			var newPassword = jQuery('input[name="user_password"]').val();
			var confirmPassword = jQuery('input[name="confirm_password"]').val();
			var record = jQuery('input[name="record"]').val();
            var firstName = jQuery('input[name="first_name"]').val();
            var lastName = jQuery('input[name="last_name"]').val();
            var specialChars = /[<\>\"\,]/;
            if((specialChars.test(firstName)) || (specialChars.test(lastName))) {
                app.helper.showErrorNotification({message :app.vtranslate('JS_COMMA_NOT_ALLOWED_USERS')});
                e.preventDefault();
                return false;
            }
			var firstName = jQuery('input[name="first_name"]').val();
			var lastName = jQuery('input[name="last_name"]').val();
			if((firstName.indexOf(',') !== -1) || (lastName.indexOf(',') !== -1)) {
                app.helper.showErrorNotification({message :app.vtranslate('JS_COMMA_NOT_ALLOWED_USERS')});
				e.preventDefault();
				return false;
			}
			if(record == ''){
				if(newPassword != confirmPassword){
                    app.helper.showErrorNotification({message :app.vtranslate('JS_REENTER_PASSWORDS')});
					e.preventDefault();
				}

                if(!(userName in thisInstance.duplicateCheckCache)) {
                    e.preventDefault();
                    thisInstance.checkDuplicateUser(userName).then(
                        function(data,error){
                            thisInstance.duplicateCheckCache[userName] = data;
                            form.submit();
                        }, 
                        function(data){
                            if(data) {
                                thisInstance.duplicateCheckCache[userName] = data;
                                app.helper.showErrorNotification({message :app.vtranslate('JS_USER_EXISTS')});
                            } 
                        }
                    );
                } else {
                    if(thisInstance.duplicateCheckCache[userName] == true){
                        app.helper.showErrorNotification({message :app.vtranslate('JS_USER_EXISTS')});
                        e.preventDefault();
                    } else {
                        delete thisInstance.duplicateCheckCache[userName];
                        return true;
                    }
                }
            }
        })
	},
	
	checkDuplicateUser: function(userName){
		var aDeferred = jQuery.Deferred();
		var params = {
				'module': app.getModuleName(),
				'action' : "SaveAjax",
				'mode' : 'userExists',
				'user_name' : userName
			}
		app.request.post({data:params}).then(
				function(err,data) {
					if(data){
						aDeferred.resolve(data);
					}else{
						aDeferred.reject(data);
					}
				}
			);
		return aDeferred.promise();
	},
	
	/**
	 * Function load the ckeditor for signature field in edit view of my preference page.
	 */
	registerSignatureEvent: function(){
		var templateContentElement = jQuery("#Users_editView_fieldName_signature");
		if(templateContentElement.length > 0) {
			var ckEditorInstance = new Vtiger_CkEditor_Js();
			//Customized toolbar configuration for ckeditor  
			//to support basic operations
			var customConfig = {
				toolbar: [
					{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup','align','list', 'indent','colors' ,'links'], items: [ 'Bold', 'Italic', 'Underline', '-','TextColor', 'BGColor' ,'-','JustifyLeft', 'JustifyCenter', 'JustifyRight', '-', 'NumberedList', 'BulletedList','-', 'Link', 'Unlink','Image','-','RemoveFormat'] },
					{ name: 'styles', items: ['Font', 'FontSize' ] },
                    {name: 'document', items:['Source']}
				]};
			ckEditorInstance.loadCkEditor(templateContentElement,customConfig);
		}
	},
	
	registerEvents : function() {
        this._super();
		var form = this.getForm();
		this.registerRecordPreSaveEvent(form);
        this.registerSignatureEvent();
        Settings_Users_PreferenceEdit_Js.registerChangeEventForCurrencySeparator();
        
        var instance = new Settings_Vtiger_Index_Js(); 
        instance.registerBasicSettingsEvents();
        
        this.registerLogoChangeEvent();
        this.registerLogoElementChangeEvent(form);
        this.registerDocumentFileElementChangeEvent(form);
        this.registerEventForDocumentDelete();
	},
	
	registerLogoChangeEvent : function() {
        var formElement = this.getForm();
        formElement.find('input[name="user_logo[]"]').on('change',function() {
            var deleteImageElement = jQuery(this).closest('td.fieldValue').find('.imageDelete');
            if(deleteImageElement.length) deleteImageElement.trigger('click');
        });
    },
    
    
    registerLogoElementChangeEvent : function(container) {
        var thisInstance = this;
        container.on('change', 'input[name="user_logo[]"]', function(e){
            if(e.target.type == "text") return false;
            var moduleName = jQuery('[name="module"]').val();
            if(moduleName == "Products") return false;
            Vtiger_Edit_Js.file = e.target.files[0];
            var element = container.find('input[name="user_logo[]"]');
            //ignore all other types than file 
            if(element.attr('type') != 'file'){
                    return ;
            }
            var uploadFileSizeHolder = element.closest('.fileUploadContainer').find('.uploadedFileSize');
            var fileSize = e.target.files[0].size;
            var fileName = e.target.files[0].name;
            var maxFileSize = thisInstance.getMaxiumFileUploadingSize(container);
            if(fileSize > maxFileSize) {
                alert(app.vtranslate('JS_EXCEEDS_MAX_UPLOAD_SIZE'));
                element.val('');
                uploadFileSizeHolder.text('');
            }else{
                if(container.length > 1){
                    jQuery('div.fieldsContainer').find('form#I_form').find('input[name="filename"]').css('width','80px');
                    jQuery('div.fieldsContainer').find('form#W_form').find('input[name="filename"]').css('width','80px');
                } else {
                    container.find('input[name="filename"]').css('width','80px');
                }
                uploadFileSizeHolder.text(fileName+' '+thisInstance.convertFileSizeInToDisplayFormat(fileSize));
            }
			
			//jQuery(e.currentTarget).addClass('ignore-validation');
        });
	},
	
	registerDocumentFileElementChangeEvent : function(container) {
		var thisInstance = this;
		container.on('change', 'input[name="brochure_file"]', function(e){
            vtUtils.hideValidationMessage(container.find('input[name="brochure_file"]'));
            if(e.target.type == "text") return false;
            Vtiger_Index_Js.file = e.target.files[0];
            var element = container.find('[name="brochure_file"]');
			//ignore all other types than file 
			if(element.attr('type') != 'file'){
				return ;
			}
			var uploadFileSizeHolder = element.closest('.fileUploadContainer').find('.uploadedFileSize');
			var fileSize = e.target.files[0].size;
            var fileName = e.target.files[0].name;
			var maxFileSize = thisInstance.getMaxiumFileUploadingSize(container);
			if(fileSize > maxFileSize) {
				alert(app.vtranslate('JS_EXCEEDS_MAX_UPLOAD_SIZE'));
				element.val('');
				uploadFileSizeHolder.text('');
			}else{
                if(container.length > 1){
                    jQuery('div.fieldsContainer').find('form#I_form').find('input[name="brochure_file"]').css('width','80px');
                    jQuery('div.fieldsContainer').find('form#W_form').find('input[name="brochure_file"]').css('width','80px');
                } else {
                    container.find('input[name="brochure_file"]').css('width','80px');
                }
				uploadFileSizeHolder.text(fileName+' '+thisInstance.convertFileSizeInToDisplayFormat(fileSize));
			}

		});
	},
	
	/**
	 * Function to register event for image delete
	 */
	registerEventForImageDelete : function(){
		var formElement = this.getForm();
		formElement.find('.imageDelete').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var imageId = element.closest('div').find('img').data().imageId;
			var parentTd = element.closest('td');
			var imageUploadElement = parentTd.find('[type="file"]');
			element.closest('div').remove();
            
			if(formElement.find('[name=imageid]').length !== 0) {
				var imageIdValue = JSON.parse(formElement.find('[name=imageid]').val());
				imageIdValue.push(imageId);
				formElement.find('[name=imageid]').val(JSON.stringify(imageIdValue));
			} else {
				var imageIdJson = [];
				imageIdJson.push(imageId);
				formElement.append('<input type="hidden" name="imgDeleted" value="true" />');
				formElement.append('<input type="hidden" name="imageid" value="'+JSON.stringify(imageIdJson)+'" />');
			}
			
			if(formElement.find('.imageDelete').length === 0 && imageUploadElement.attr('data-rule-required') == 'true'){
				imageUploadElement.removeClass('ignore-validation')
			}
		});
	},
	
	/**
	 * Function to register event for Document delete
	 */
	registerEventForDocumentDelete : function(){
		var formElement = this.getForm();
		formElement.find('.fileDelete').on('click',function(e){
			var element = jQuery(e.currentTarget);
			var parentTd = element.closest('td');
			var imageUploadElement = parentTd.find('[type="file"]');
			element.closest('div').remove();
			
			formElement.append('<input type="hidden" name="fileDeleted" value="true" />');
			
		});
	},

});

// Actually, Users Module is in Settings. Controller in application.js will check for Settings_Users_Edit_Js 
Users_Edit_Js("Settings_Users_Edit_Js");