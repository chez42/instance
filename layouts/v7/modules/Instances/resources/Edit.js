/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Instances_Edit_Js",{
   
},{
	
	setDomainName: function(){
		
		$('[name="name"]').on("change", function(){
			$('[name="domain"]').val( "https://" + $(this).val().replace(/[^A-Za-z]+/g, '').toLowerCase() + ".360vew.com" );
		});
		
	},
	/**
	 * Function which will register basic events which will be used in quick create as well
	 *
	 */
	registerBasicEvents : function(container) {
		this._super(container);
		this.setDomainName();
		this.registerLogoChangeEvent();
        this.registerLogoElementChangeEvent(container);
	},
	
	registerLogoChangeEvent : function() {
        var formElement = this.getForm();
        formElement.find('input[name="portalfavicon[]"]').on('change',function() {
            var deleteImageElement = jQuery(this).closest('td.fieldValue').find('.imageDelete');
            if(deleteImageElement.length) deleteImageElement.trigger('click');
        });
    },
    
    
    registerLogoElementChangeEvent : function(container) {
        var thisInstance = this;
        container.on('change', 'input[name="portalfavicon[]"], input[name="instance_logo[]"], input[name="instance_background[]"]', function(e){
            if(e.target.type == "text") return false;
            var moduleName = jQuery('[name="module"]').val();
            if(moduleName == "Products") return false;
            Vtiger_Edit_Js.file = e.target.files[0];
            var element = $(e.target);
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
		});
	},
});