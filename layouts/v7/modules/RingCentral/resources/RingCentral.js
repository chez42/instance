Vtiger.Class("RingCentral_Js",{
	
	webPhone: null,
	
	session: null,
	
	from_no : '',
	
	app_key : '',
	
	lineItemPopOverTemplate : '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-content"></div></div>',
	
	connected: false,
	
	ValidateTokenAndGetSIP: function(){
		
		var thisInstance = this;
		
		var params = {};
		
		params.module = 'RingCentral';
		
		params.action = 'ValidateTokenAndGetSIP';
		
		app.request.post({data:params}).then(
			
			function(err,data){
				
				if(err === null) {
					
					thisInstance.connected = true;
					
					thisInstance.from_no = data.from_no;
					
					thisInstance.app_key = data.client_id;
					
					if(data.sip){
						//thisInstance.initializeSIP(data.sip);
					}
					
				} else {
					thisInstance.connected = false;	
				}
				
			}
		);
	
	},
	
	triggerRingCentral: function (massActionUrl) {
		
		var thisInstance = this;
		
		if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': "Let's connect with Ring Central"}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/RingCentral/connect.php');
					
					var win = window.open(url,'','height=600,width=600,channelmode=1');
		
					window.RefreshPage = function() {
						thisInstance.ValidateTokenAndGetSIP();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		} else {
		
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
				postData['srcmodule'] = app.getModuleName();
				
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
		
		}
	},
	
	sendSmsSave: function (form, single=false) {
		
		var thisInstance = this;
		
		var listInstance = window.app.controller();
		if(app.getViewName() == 'List')
			var listSelectParams = listInstance.getListSelectAllParams(false);
		else
			var listSelectParams = {};
		
		if (listSelectParams || single == true) {
			
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
						if(app.getViewName() == 'List'){
							listInstance.loadListViewRecords().then(function (e) {
								listInstance.clearList();
								app.helper.showSuccessNotification({message: 'Message Sent Successfully'});
							});
						}else{
							app.helper.showSuccessNotification({message: 'Message Sent Successfully'});
						}
					} else {
						app.helper.showErrorNotification({message: app.vtranslate(data.message)})
					}
					
				} else {
					
					thisInstance.connected =  false;
	
					app.helper.showConfirmationBox({'message': "Let's connect with Ring Central"}).then(
						function(data) {
							var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/RingCentral/connect.php');
							var win = window.open(url,'','height=600,width=600,channelmode=1');
	
							window.RefreshPage = function() {
								thisInstance.ValidateTokenAndGetSIP();
							}
						},
				
						function(error, err) {}
					);
				
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
	
	hangUp: function(){
		
		this.session.terminate();
		
	},
	
	clickToCall : function(e) {
		
		var thisInstance = this;
		
		if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': "Let's connect with Ring Central"}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/RingCentral/connect.php');
					
					var win = window.open(url,'','height=600,width=600,channelmode=1');
		
					window.RefreshPage = function() {
						thisInstance.ValidateTokenAndGetSIP();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		} else {
			
			// Fire Ajax Call to Retrieve HTML here
			/*if( $( "body" ).hasClass( "show_sidebar3" )){
				app.helper.showErrorNotification({message: 'Call in Progress!'});
				return true;
			}*/
			
			var element = jQuery(e);
			
			var record = element.data('id');
			
			var number = element.data('number') + '';
			
			number = number.replace(/\D/g,'');
			
			var url = decodeURIComponent(window.location.href.split('?', 1) + '?module=RingCentral&view=Call&record='+record+'&number='+number);
			
			var x = window.open(url,'','height=650,width=600,location=no,toolbar=0');
			
			/*if( !$( "body" ).hasClass( "show_sidebar3" )){
				var params = {
					'module' : 'RingCentral',
					'record' : record,
					'action' : 'GetRecordDetails',
					'mode' : 'fetchDetails'
				};
				app.request.post({'data':params}).then(function (err, data) {
					
					if (err == null) {
						
						//If Number Length is Greator than 10 then append + for country code to work
						if(number.length > 10){
							number = '+' + number;
						}
						
						thisInstance.session = thisInstance.webPhone.userAgent.invite(number, {
							fromNumber: thisInstance.from_no,
							homeCountryId: 1
						});
						
						var html ='<div class="ringcentral_details"  style="padding:10px;">'+
							'<span class="close" style="opacity: 0.6;font-size:15px !important;padding-top: 0px!important;">' +
								'<i class="fa fa-times closeOutgoing"></i>'+
							'</span>' +
							'<div class = "row"><div class="col-md-12" style="text-align:center;">' ;
						
						if(data.imagepath){
							html += '<img src="'+data.imagepath+'" style="border-radius:50%!important;width:50px!important;height:50px!important;" >';
						}else{
							html+= '<i class="vicon-contacts" style="font-size: 70px;"></i>';
						}
						
						html+='</div></div><div class = "row"><div class="col-md-12" style="font-size:15px !important;text-align:center;color:black;font-weight:600;padding-top:15px;">'+data.lastname+'&nbsp;'+data.firstname+'<br>'+
							'</div></div><hr/>';
						
						$.each(data.fields,function(inx,val){
							html += '<div class = "row" style = "font-size:13px;padding:5px;"><div class = "col-md-12" style = "text-align:left;"><label style = "font-weight:100;color:#6f6f6f;padding-right:10px;">'+inx+': </label><span style = "color:black;">'+val+'</span></div></div>';
						});
						
						html+= '<div class = "row"><div class="col-md-12" style = "text-align:left;">'+
								'<textarea class="pull-left" style="width:80%;height:50px;font-size:12px !important;border:1px solid #DDDDDD;" placeholder="Leave notes" name="callnotes"></textarea>'+
								'<img data-id="'+record+'" class = "sendnotes" style = "width:40px;margin-left:10px;" src = "modules/RingCentral/resources/end-call.png"/>'
							'</div></div>'+
						'</div>' ;
						
						$(document).find('.ringcentral_details').replaceWith(html);
						$("body").toggleClass("show_sidebar3");
					}
				});
				
			}*/
			
		}
	},
	
	clickToSms : function(e) {
		
		var thisInstance = this;
		
		if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': "Let's connect with Ring Central"}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/RingCentral/connect.php');
					
					var win = window.open(url,'','height=600,width=600,channelmode=1');
		
					window.RefreshPage = function() {
						thisInstance.ValidateTokenAndGetSIP();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		} else {
			
			// Fire Ajax Call to Retrieve HTML here
			
			var element = jQuery(e);
			
			var record = element.data('id');
			
			var number = element.data('number') + '';
			
			number = number.replace(/\D/g,'');
			
			var params = {
				mode : "showSendSMSForm",
				module : "RingCentral",
				view : "MassActionAjax",
				record : record,
				number : number,
				srcmodule : app.getModuleName()
			};
			app.helper.showProgress();
			
			app.request.get({'data': params}).then(
				
				function (err, data) {
					
					app.helper.hideProgress();
					
					if (data) {
						
						app.helper.showModal(data, {'cb': function (modal) {
							var ringcentralForm = jQuery('#massSaveRingCentral');
							if(ringcentralForm.length)
								thisInstance.registerFaxFileElementChangeEvent(ringcentralForm);
								ringcentralForm.vtValidate({
									submitHandler: function (form) {
										thisInstance.sendSmsSave(jQuery(form),true);
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
	
	clickToFax : function(e) {
		
		var thisInstance = this;
		
		if(!thisInstance.connected){
			
			app.helper.showConfirmationBox({'message': 'Invalid Token! Do you want to Reconnect?'}).then(
				
				function(data) {
					
					var url = decodeURIComponent(window.location.href.split('index.php', 1) + 'modules/RingCentral/connect.php');
					
					var win = window.open(url,'','height=600,width=600,channelmode=1');
		
					window.RefreshPage = function() {
						thisInstance.ValidateTokenAndGetSIP();
					}
					
				},
				
				function(error, err) {}
				
			);
		
		} else {
			
			// Fire Ajax Call to Retrieve HTML here
			
			var element = jQuery(e);
			
			var record = element.data('id');
			
			var number = element.data('number') + '';
			
			number = number.replace(/\D/g,'');
			
			var params = {
				mode : "showSendFaxForm",
				module : "RingCentral",
				view : "MassActionAjax",
				record : record,
				number : number,
				srcmodule : app.getModuleName()
			};
			app.helper.showProgress();
			
			app.request.get({'data': params}).then(
				
				function (err, data) {
					
					app.helper.hideProgress();
					
					if (data) {
						
						app.helper.showModal(data, {'cb': function (modal) {
							var ringcentralForm = jQuery('#massSaveRingCentral');
							if(ringcentralForm.length)
								thisInstance.registerFaxFileElementChangeEvent(ringcentralForm);
								ringcentralForm.vtValidate({
									submitHandler: function (form) {
										thisInstance.sendSmsSave(jQuery(form),true);
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
	
	triggerSendRingCentralSms: function(detailActionUrl) {
		var self = this;
		self.sendRingCentralSMS(detailActionUrl);
	},
	
	sendRingCentralSMS: function(detailActionUrl){
		var self = this;
		var cb = function(container) {
			$('#phoneFormatWarningPop').popover();
			self.registerChangePhoneField();
			var ringcentralForm = jQuery('#massSaveRingCentral');
			ringcentralForm.vtValidate({
				submitHandler: function (form) {
					RingCentral_Js.sendSmsSave(jQuery(form),true);
					return false;
				}
			});
		}
		self.sendRingCentralSMSAction(detailActionUrl, cb);
	},
	
	sendRingCentralSMSAction: function(detailActionUrl, callBackFunction) {
		var self = this;
		
		var postData = {
			"record": app.getRecordId()
		};
		app.request.post({url:detailActionUrl, data:postData, dataType:"html"}).then(
			function(err, data) {
				if (data) {
					app.helper.showModal(data);
					if (typeof callBackFunction == 'function') {
						callBackFunction(data);
					}
				}
			}
		);
	},
	
	registerChangePhoneField : function(){
		jQuery(document).on('change', '.phoneField', function(){
			
			var selectedNo = $(this).children("option:selected").data('phoneno');
			
			var number = selectedNo + '';
			
			number = number.replace(/\D/g,'');
			
			jQuery(document).find('.ringcentral_phoneno').val(number);
		});
	},

},{
	
	registerEventForMouse :function(){
		
		var view = app.getViewName();
		
		$("[data-field-type=phone]").each(function(){
		
			if (!$(this).hasClass("listSearchContributor")) {
			
				var element = $(this);
				
				element.popover('destroy');
				
				var phoneCallContainer = jQuery('.phoneCallContainer').clone(true).removeClass('phoneCallContainer');
				
				if(view == 'List'){
					
					if(jQuery(element).parents('#helpPageOverlay').length > 0){
						
						var recordId = $(this).attr("data-recordid");
						var number = $.trim($(this).text());
						
					} else {
						
						var parentElem = jQuery(element).closest('td');
						
						if($(this).attr("data-rawvalue") != ''){
							var number = $(this).attr("data-rawvalue");
							var recordId = jQuery(element).closest('tr').data('id');
						}
						
					}
					
				} else if(view == 'Detail'){
					
					if(jQuery(element).parents('.info-row').length > 0){
						var parentElem = jQuery(element).parents('.info-row');
					} else {
						var parentElem = jQuery(element).closest('td');
					}
					
					if($.trim($(this).text()) != ''){
						var number = $.trim($(this).text());
						var recordId = app.getRecordId();
					}
					
					if(jQuery(element).parents('.relatedContents').length){
						var parentElem = jQuery(element).closest('td');
						if($.trim($(this).text()) != ''){
							var number = $.trim($(this).text());
							var recordId = jQuery(element).closest('tr').data('id');
						}
					}
				}
				if(jQuery(element).parents('.searchResults').length){
					var parentElem = jQuery(element).closest('td');
					if($(this).attr("data-rawvalue") != ''){
						var number = $(this).attr("data-rawvalue");
						var recordId = jQuery(element).closest('tr').data('id');
					}
				}
					
				if (number !== '' && typeof number !== "undefined") {
					var template = jQuery(RingCentral_Js.lineItemPopOverTemplate);
					phoneCallContainer.find('#call,#sms,#fax').attr('data-number', number);
					phoneCallContainer.find('#call,#sms,#fax').attr('data-id', recordId);
					
					if(view == 'Detail'){
						parentElem.popover({
							'content' : phoneCallContainer,
							'width'	:'72',
							'html' : true,
							'placement' : 'left',
							'trigger' : 'hover',
							'template' : template,
							'container' : parentElem,
						});
					} else {
						element.popover({
							'content' : phoneCallContainer,
							'width'	:'72',
							'html' : true,
							'placement' : 'left',
							'trigger' : 'hover',
							'template' : template,
							'container' : element,
						});
					}
				}   
			}      
		
		});
		
	},
	
	init : function(){
		
		var thisInstance = this;
		
		thisInstance.appendHTML();
		
		$.getScript('modules/RingCentral/resources/sip.js', function(){
			
			$.getScript('modules/RingCentral/resources/ringcentral-web-phone.js', function(){
				
				thisInstance.registerEventForMouse();
				
				thisInstance.Class.ValidateTokenAndGetSIP();
			
			});
			
		});
		
		
		app.event.on('post.listViewFilter.click', function () {
			thisInstance.registerEventForMouse();
		});	
	
		app.event.on('post.relatedListLoad.click', function () {
			thisInstance.registerEventForMouse();
		});
	
		thisInstance.registerEventsForUserPrefrence();
	
		//var view = app.getViewName();
			
			jQuery('#overlayPage').one('shown.bs.modal',function(){
				thisInstance.registerEventForMouse();
			});
			
			jQuery('#helpPageOverlay').one('shown.bs.modal', function () {
				thisInstance.registerEventForMouse();
			});
			
		jQuery('#overlayPageContent').one('shown.bs.modal', function () {
			thisInstance.registerEventForMouse();
		});

		
		$(document).on('click','.closeOutgoing',function(){
			thisInstance.hideOutgoingCallWindow();
		});
		
		$(document).on('click','.sendnotes',function(){
			
			var recordId = $(this).data('id');
			
			var comment = $(document).find('[name="callnotes"]').val();
			
			if(comment){
				
				var params = {
					'module' : 'RingCentral',
					'record' : recordId,
					'action' : 'GetRecordDetails',
					'mode' : 'addComment',
					'comment' : comment
				};
				
				app.request.post({'data':params}).then(function (err, data) {
					
					if (err == null) {
						
						$(document).find('[name="callnotes"]').val('');
						
						app.helper.showSuccessNotification({message:'Notes Saved Successfully'});
						
						RingCentral_Js.hangUp();
						
						thisInstance.hideOutgoingCallWindow();
						
                    }
					
				});
				
			} else {
				
				RingCentral_Js.hangUp()	
				
				thisInstance.hideOutgoingCallWindow();
						
			}
			
		});
	
	},
	
	hideOutgoingCallWindow: function(){
		
		if( $( "body" ).hasClass( "show_sidebar3" )){
			$("body").toggleClass("show_sidebar3");
		}
		
	},

	
	initializeSIP: function(sipInfo){
		
		var thisInstance = this;
		
		var remoteVideoElement = document.getElementById('remoteVideo');
		
		var localVideoElement = document.getElementById('localVideo');
		
		thisInstance.Class.webPhone = new RingCentral.WebPhone(sipInfo, {
			appKey: thisInstance.Class.app_key,
			logLevel: parseInt(1, 10),
			audioHelper: {
				enabled: true,
				incoming: 'modules/RingCentral/resources/incoming.ogg',
				outgoing: 'modules/RingCentral/resources/outgoing.ogg'
			},
			
			media: {
                remote: remoteVideoElement,
                local: localVideoElement
            },
		});
					
		thisInstance.Class.webPhone.userAgent.audioHelper.setVolume(0.5);
	
		thisInstance.Class.webPhone.userAgent.on('registered', function() {
			console.log("Registered");
		});
		
	},
	
	appendHTML: function(){
		
		var view = app.getViewName();
		
		if(view == 'List'){
			var html = '<div id="phone_panel_call" class="phoneCallContainer js-reference-display-value">'+
						'<button title="Call" type="button" style="border-radius: 5px !important;"class="btn btn-success js-reference-display-value" id="call" onclick="RingCentral_Js.clickToCall(this)"><i class="fa fa-phone js-reference-display-value" aria-hidden="true"></i></button>&nbsp;'+
						'<button title="SMS" type="button" style="border-radius: 5px !important;"class="btn btn-success js-reference-display-value" id="sms" onclick="RingCentral_Js.clickToSms(this)"><i class="fa fa-envelope js-reference-display-value" aria-hidden="true"></i></button>&nbsp;'+
						'<button title="Fax" type="button" style="border-radius: 5px !important;"class="btn btn-success js-reference-display-value" id="fax" onclick="RingCentral_Js.clickToFax(this)"><i class="fa fa-fax js-reference-display-value" aria-hidden="true"></i></button>&nbsp;'+
						'</div>'; 
		} else {
			if(!jQuery('#page.ps').length){
				var html = '<div id="phone_panel_call" class="phoneCallContainer">'+
				'<button title="Call" type="button" style="border-radius: 5px !important;"class="btn btn-success" id="call" onclick="RingCentral_Js.clickToCall(this)"><i class="fa fa-phone" aria-hidden="true"></i></button>&nbsp;'+
				'<button title="SMS" type="button" style="border-radius: 5px !important;"class="btn btn-success" id="sms" onclick="RingCentral_Js.clickToSms(this)"><i class="fa fa-envelope" aria-hidden="true"></i></button>&nbsp;'+
				'<button title="Fax" type="button" style="border-radius: 5px !important;"class="btn btn-success" id="fax" onclick="RingCentral_Js.clickToFax(this)"><i class="fa fa-fax" aria-hidden="true"></i></button>&nbsp;'+
				'</div>'; 
			}else{
				var html = '<div id="phone_panel_call" class="phoneCallContainer">'+
				'<button title="Call" type="button" style="padding: 6px 6px 6px 3px !important;border-radius: 5px !important;font-size: 1rem !important;width: 30% !important;"class="btn btn-success" id="call" onclick="RingCentral_Js.clickToCall(this)"><i class="fa fa-phone" aria-hidden="true"></i></button>&nbsp;'+
				'<button title="SMS" type="button" style="padding: 6px 6px 6px 3px !important;border-radius: 5px !important;font-size: 1rem !important;width: 30% !important;"class="btn btn-success" id="sms" onclick="RingCentral_Js.clickToSms(this)"><i class="fa fa-envelope" aria-hidden="true"></i></button>&nbsp;'+
				'<button title="Fax" type="button" style="padding: 6px 6px 6px 3px !important;border-radius: 5px !important;font-size: 1rem !important;width: 30% !important;"class="btn btn-success" id="fax" onclick="RingCentral_Js.clickToFax(this)"><i class="fa fa-fax" aria-hidden="true"></i></button>'+
				'</div>'; 
			}
		}
		var dummy_html = '<div style = "display:none;" id = "dummy-div"></div>'
		jQuery('.app-footer').append(dummy_html);
		jQuery('#dummy-div').append(html);
		
		var caller_html = '<video id="remoteVideo" hidden="hidden"></video><video id="localVideo" hidden="hidden" muted="muted"></video><style>body.show_sidebar #push_sidebar{left:80%}#push_sidebarphone{-webkit-background-size:cover;-moz-background-size:cover;-o-background-size:cover;background-size:cover;background-color:#fff;border:1px solid;border-radius:5px;position:fixed;z-index:99999999;width:20%;right:0;bottom:-200%;text-align:center;-webkit-transition:all .5s ease;-moz-transition:all .5s ease;-ms-transition:all .5s ease;-o-transition:all .5s ease;transition:all .5s ease;font-size:16px!important}body.show_sidebar3 #push_sidebarphone{bottom:0}</style>';
		
		caller_html += '<div id="push_sidebarphone" style = "border:1px solid #DDDDDD;width:auto;min-width:310px !important;">'+
			'<div class="ringcentral_details"></div>'+
		'</div>';
		
		$('.app-footer').after(caller_html);
		
	},
	
	registerEventsForUserPrefrence : function(){
		var self = this;
		
		if(app.getModuleName() == 'Users' && app.getViewName() == 'PreferenceDetail'){
			
			var params = {};
			
			params.module = 'RingCentral';
			params.action = 'GetUserActions';
			params.mode = 'checkConnection';
			params.record = app.getRecordId();
			
			app.request.post({data:params}).then(function(err,data){
				if(err === null) {
					if(data.success){
						var buttonContainer = jQuery('.detailViewContainer');
                        var btnToolBar = buttonContainer.find('.btn-group');
                        var outGoingServerBtn = jQuery('<button type="button" class="btn btn-default btndisconnect">Disconnect</button>');
                        btnToolBar.find('.btn:first').before(outGoingServerBtn);
                        self.registerEventForDisconnectButton();
					}
				}
			});
		}
	},
	
	registerEventForDisconnectButton : function(){
		var self = this;
		$(document).on('click', '.btndisconnect', function(){
			var btn = this;
			var params = {};
			
			params.module = 'RingCentral';
			params.action = 'GetUserActions';
			params.mode = 'revokeToken';
			params.record = app.getRecordId();
			
			app.request.post({data:params}).then(function(err,data){
				if(err === null) {
					if(data.success)
						$(btn).remove();
				}
			});
			
		});
	},

});

jQuery(document).ready(function(){
	new RingCentral_Js();
});