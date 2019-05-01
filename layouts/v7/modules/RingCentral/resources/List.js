/*********************************************************************************
 * The content of this file is subject to the PDF Maker license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 ********************************************************************************/
Vtiger_List_Js("RingCentral_List_Js",{
	
	triggerRingCental: function (massActionUrl) {
		var thisInstance = this;
		var listInstance = window.app.controller();
		var listSelectParams = listInstance.getListSelectAllParams(true);
		if (listSelectParams) {
			var postData = listInstance.getDefaultParams();
			delete postData.module;
			delete postData.view;
			delete postData.parent;
			var data = app.convertUrlToDataParams(massActionUrl);
			postData = jQuery.extend(postData, data);
			postData = jQuery.extend(postData, listSelectParams);
			app.helper.showProgress();
			app.request.get({'data': postData}).then(
				function (err, data) {
					app.helper.hideProgress();
					if (data) {
						
						app.helper.showModal(data, {'cb': function (modal) {
								var ringcentralForm = jQuery('#massSaveRingCentral');
								if(ringcentralForm.length)
									thisInstance.registerFaxFileElementChangeEvent(ringcentralForm);
								ringcentralForm.vtValidate({
									submitHandler: function (form) {
										thisInstance.sendSmsSave(jQuery(form));
										return false;
									}
								});
							}
						});
					}
				}
			);
		} else {
			listInstance.noRecordSelectedAlert();
		}
	},
	
	sendSmsSave: function (form) {
		var listInstance = window.app.controller();
		var listSelectParams = listInstance.getListSelectAllParams(false);
		if (listSelectParams) {
			
			var formData = form.serializeFormData();
			var data = jQuery.extend(formData, listSelectParams);
			
			var data = new FormData(form[0]); 
			jQuery.each(data, function (key, value) {
				data.append(key, value);
			});
			
			var postData = { 
				'url': 'index.php', 
				'type': 'POST', 
				'data': data, 
				processData: false, 
				contentType: false 
			};
			
			app.helper.showProgress();
			app.request.post(postData).then(function (err, data) {
				app.helper.hideProgress();
				if (err == null) {
					if(data.success){
						app.helper.hideModal();
						listInstance.loadListViewRecords().then(function (e) {
							listInstance.clearList();
							app.helper.showSuccessNotification({message: app.vtranslate(data.message)});
						});
					}else{
						app.helper.showErrorNotification({message: app.vtranslate(data.message)})
					}
				} else {
					app.event.trigger('post.save.failed', err);
					jQuery(form).find("button[name='saveButton']").removeAttr('disabled');
				}
			});
		}
	},

	registerFaxFileElementChangeEvent : function(container) {
        var thisInstance = this;
        Vtiger_Index_Js.files = '';
        container.on('change', 'input[name="faxfile"]', function(e){
        	
            if(e.target.type == "text") return false;
            var moduleName = jQuery('[name="module"]').val();
            if(moduleName == "Products") return false;
            Vtiger_Index_Js.files = e.target.files[0];
            var element = container.find('[name="faxfile"]');
            //ignore all other types than file 
            if(element.attr('type') != 'file'){
                    return ;
            }
            var uploadFileSizeHolder = element.closest('.fileUploadContainer').find('.uploadedFileSize');
            var fileSize = e.target.files[0].size;
            var fileName = e.target.files[0].name;
            var maxFileSize = container.find('.maxUploadSize').data('value');
            
            if(fileSize > maxFileSize) {
                alert(app.vtranslate('JS_EXCEEDS_MAX_UPLOAD_SIZE'));
                element.val('');
                uploadFileSizeHolder.text('');
            }else{
                uploadFileSizeHolder.text(fileName+' '+thisInstance.convertFileSizeInToDisplayFormat(fileSize));
            }
			
			jQuery(e.currentTarget).addClass('ignore-validation');
        });
	},
	
	convertFileSizeInToDisplayFormat: function (fileSizeInBytes) {
		var i = -1;
		var byteUnits = [' kB', ' MB', ' GB', ' TB', 'PB', 'EB', 'ZB', 'YB'];
		do {
			fileSizeInBytes = fileSizeInBytes / 1024;
			i++;
		} while (fileSizeInBytes > 1024);

		return Math.max(fileSizeInBytes, 0.1).toFixed(1) + byteUnits[i];

	},
	
},{
	
	
	
	
})

