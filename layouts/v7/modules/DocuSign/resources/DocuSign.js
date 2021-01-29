Vtiger.Class("DocuSign_Js",{
	
	connected: false,
	
	ValidateToken: function(){
		
		var thisInstance = this;
		
		var params = {};
		
		params.module = 'DocuSign';
		
		params.action = 'ValidateToken';
		
		app.request.post({data:params}).then(
			
			function(err,data){
				
				if(err === null) {
					
					thisInstance.connected = true;
					app.event.on('post.relatedListLoad.click', function () {
						thisInstance.registerEventForSendEnvelopeButton();
					});
					
				} else {
					
					thisInstance.connected = false;	
					
				}
				
			}
		);
	
	},
	
	triggerDocusignEmailList : function(massActionUrl){
	
		var thisInstance = this;
		
		if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': 'Invalid Token! Do you want to Reconnect?'}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/DocuSign/connect.php');
					var win = window.open(url,'','height=600,width=600,channelmode=1');
					window.RefreshPage = function() {
						thisInstance.ValidateToken();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		}else{
			
			var listInstance = window.app.controller();
			
			var listSelectParams = listInstance.getListSelectAllParams(true);
			
			var selectedRecordCount = listInstance.getSelectedRecordCount();
			
			if (selectedRecordCount > 15) {
				app.helper.showErrorNotification({message: app.vtranslate('Only 15 Records Selected at a time.')});
				return;
			}
			if (listSelectParams) {
				
				var postData = listInstance.getDefaultParams();
				
				delete postData.module;
				delete postData.view;
				delete postData.parent;
				
				var data = app.convertUrlToDataParams(massActionUrl);
				
				postData = jQuery.extend(postData, data);
				postData = jQuery.extend(postData, listSelectParams);
				postData['srcmodule'] = app.getModuleName();
				
				app.helper.showProgress();
				
				app.request.get({'data': postData}).then(
					
					function (err, data) {
						
						app.helper.hideProgress();
						
						if (data) {
							
							var overlayParams = {/*'backdrop': 'static', */'keyboard': false};
							app.helper.loadPageContentOverlay(data, overlayParams).then(function (modal) {
								var docusignForm = jQuery('#massSaveSendEnvelope');
								if(docusignForm.length){
									var noteContentElement = docusignForm.find('[name="envelope_content"]');
									if(noteContentElement.length > 0){
										noteContentElement.addClass('ckEditorSource');
										var ckEditorInstance = new Vtiger_CkEditor_Js();
										ckEditorInstance.loadCkEditor(noteContentElement);
									}
									thisInstance.registerTemplateChangeEvent(docusignForm);	
									thisInstance.registerFillMailContentEvent(docusignForm);
									thisInstance.registerModeChangeEvent(docusignForm);
									docusignForm.vtValidate({
										submitHandler: function (form) {
											thisInstance.sendEmailSave(jQuery(form));
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
			
		}
	
	},
	
	
	triggerDocusignEmail : function(massurl){
		var thisInstance = this;
		
		if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': 'Invalid Token! Do you want to Reconnect?'}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/DocuSign/connect.php');
					var win = window.open(url,'','height=600,width=600,channelmode=1');
					window.RefreshPage = function() {
						thisInstance.ValidateToken();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		}else{
			
			var data = app.convertUrlToDataParams(massurl);
			data['record'] = app.getRecordId();
			data['srcmodule'] = app.getModuleName();
	
			app.helper.showProgress();
			
			app.request.post({'data': data}).then(
				
				function (err, data) {
					
					app.helper.hideProgress();
					if (data) {
						
						var overlayParams = {'backdrop': 'static', 'keyboard': false};
						app.helper.loadPageContentOverlay(data, overlayParams).then(function (modal) {
							var docusignForm = jQuery('#massSaveSendEnvelope');
							if(docusignForm.length){
								var noteContentElement = docusignForm.find('[name="envelope_content"]');
								if(noteContentElement.length > 0){
									noteContentElement.addClass('ckEditorSource');
									var ckEditorInstance = new Vtiger_CkEditor_Js();
									ckEditorInstance.loadCkEditor(noteContentElement);
								}
								thisInstance.registerTemplateChangeEvent(docusignForm);	
								thisInstance.registerFillMailContentEvent(docusignForm);
								thisInstance.registerModeChangeEvent(docusignForm);
								docusignForm.vtValidate({
									submitHandler: function (form) {
										thisInstance.sendEmailToSigner(jQuery(form));
										return false;
									}
								});
							}
						});
					}
				}
			);
		}
	},
	registerFillMailContentEvent: function (docusignForm) {
		docusignForm.on('change', '#selected_contacts', function (e) {
			var textarea = CKEDITOR.instances.envelope_content;
			var value = jQuery(e.currentTarget).val();
			if (textarea != undefined) {
				textarea.insertHtml(value);
			} else if (jQuery('textarea[name="envelope_content"]')) {
				var textArea = jQuery('textarea[name="envelope_content"]');
				textArea.insertAtCaret(value);
			}
		});
	},
	
	registerTemplateChangeEvent : function(docusignForm){
		
		docusignForm.on('change', '#templateid', function(){
			app.helper.showProgress();
			var data = new FormData(docusignForm[0]);
			
			jQuery.each(data, function (key, value) {
				data.append(key, value);
			});
			data.append('mode', 'getEmailContent');
			
			var postData = { 
				'url': 'index.php', 
				'type': 'POST', 
				'data': data, 
				processData: false, 
				contentType: false 
			};
			app.request.post(postData).then(function(err, data){
				if (err == null) {
					CKEDITOR.instances.envelope_content.setData(data);
				}
				app.helper.hideProgress();
			});
		});
		
	},
	
	registerModeChangeEvent :function(docusignForm){
		
		docusignForm.on('change', '[name="receiver_mode"]', function(){
			if($(this).val() == 'single') {
				docusignForm.find('.multiple_con').attr('style','display:none');
				docusignForm.find('.single_con').attr('style','display:block');
				
			}
			else if($(this).val() == 'multiple') {
				docusignForm.find('.single_con').attr('style','display:none');
				docusignForm.find('.multiple_con').attr('style','display:block');
				
			}
		});
		
	},
	
	sendEmailToSigner: function (form) {
		
		var thisInstance = this;
		
		var params = {};
		
		var formData = form.serializeFormData();
		
		var data = jQuery.extend(formData, params);
		
		var data = new FormData(form[0]);
		
		jQuery.each(data, function (key, value) {
			data.append(key, value);
		});
		data.append('envelope_content', CKEDITOR.instances.envelope_content.getData());
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
					app.helper.hidePageContentOverlay();
					app.helper.showSuccessNotification({message: 'Message Sent Successfully'});
				} else {
					app.helper.showErrorNotification({message: app.vtranslate(data.message)})
				}
				
			} 
		});
	},
	
	sendEmailSave: function (form) {
		
		var thisInstance = this;
		
		var listInstance = window.app.controller();
		var listSelectParams = listInstance.getListSelectAllParams(false);
		
		if (listSelectParams) {
			
			var formData = form.serializeFormData();
			
			var data = jQuery.extend(formData, listSelectParams);
			
			var data = new FormData(form[0]);
			
			jQuery.each(data, function (key, value) {
				data.append(key, value);
			});
			data.append('envelope_content', CKEDITOR.instances.envelope_content.getData());
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
						app.helper.hidePageContentOverlay();
						listInstance.loadListViewRecords().then(function (e) {
							listInstance.clearList();
							app.helper.showSuccessNotification({message: 'Message Sent Successfully'});
						});
					} else {
						app.helper.showErrorNotification({message: app.vtranslate(data.message)})
					}
				} 
			});
		}
	},
	
    
    registerEventForSendEnvelopeButton : function(){
    	var url = window.location.href;
    	var params = app.convertUrlToDataParams(url);
    	if(app.getModuleName() == 'Accounts' && app.getViewName() == 'Detail' && params['relatedModule'] == "Contacts"){
	    	var html = '<button type="button" class="btn btn-default sendenveloperelated" id="Accounts_reletedlistView_massAction_send_envelope" title="Send Envelope" disabled="disabled" >'+
	            '<i class="fa fa-envelope" title="Send Envelope"></i>'+
	            '</button>';
	    	var container = $('.relatedContainer .relatedlistViewActionsContainer button:last');
	    	$(html).insertAfter(container);
    	}
    },
	
},{
	
	registerEventForConnectButton : function(){
		if(app.getModuleName() == 'QuotingTool' && app.getViewName() == 'List' ){
			var html = '&nbsp;&nbsp;'+
             '<button id="Contacts_listView_basicAction_LBL_CONNECT_DOCUMENTDESIGNER" class="btn connect_designer">'+
             	'<strong>Connect Document Designer</strong>'+
         	'</button>';
			$('.addButton').parent().append(html);
		}
	},
	
	registerEventsForConnect :function(){
    	var thisinstance = this;
    	$(document).on('click', '.connect_designer',  function(){
    		var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/DocuSign/connect.php');
			var win = window.open(url,'','height=600,width=600,channelmode=1');
			window.RefreshPage = function() {
				this.Class.ValidateToken();
				location.reload();
			}
    	});
    },

    
	registerEvents : function(){
//		this.registerEventForConnectButton();
//		this.registerEventsForConnect();
		this.Class.ValidateToken();
	},
	

});

jQuery(document).ready(function(){
	
	obj = new DocuSign_Js();
	obj.registerEvents();
	
});