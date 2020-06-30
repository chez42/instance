/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_List_Js("MailManager_List_Js", {}, {

	getContainer : function() {
		return jQuery('.main-container');
	},

	loadFolders : function(folder, accountid) {
		app.helper.showProgress(app.vtranslate("JSLBL_Loading_Please_Wait")+"...");
		var self = this;
		var params = {
			'module' : app.getModuleName(),
			'view' : 'Index',
			'_operation' : 'folder',
			'_operationarg' : 'getFoldersList',
			'account_id' : accountid
		}
		app.request.post({"data" : params}).then(function(error, responseData) {
			app.helper.hideProgress();
			self.getContainer().find('#folders_list').html(responseData);
			self.getContainer().find('#extraFolderList').mCustomScrollbar({
				setHeight: 550,
				autoExpandScrollbar: true,
				scrollInertia: 200,
				autoHideScrollbar: true,
				theme : "dark-3"
			});
			self.registerFolderClickEvent();
			if(folder) {
				self.openFolder(folder);
			} else {
				self.openFolder('INBOX');
			}
			self.registerAutoRefresh();
		});
	},

	registerAutoRefresh : function() {
		var self = this;
		var container = self.getContainer();
		var timeout = parseInt(container.find('#refresh_timeout').val());
		var folder = container.find('.mm_folder.active').data('foldername');
		if(timeout > 0) {
			setTimeout(function() {
				var thisInstance = new MailManager_List_Js();
				if(folder && typeof folder != "undefined") {
					thisInstance.loadFolders(folder);
				} else {
					thisInstance.loadFolders();
				}
			}, timeout);
		}
	},

	registerFolderClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.mm_folder').click(function(e) {
			var folderElement = jQuery(e.currentTarget);
			var folderName = folderElement.data('foldername');
			container.find('.mm_folder').each(function(i, ele) {
				jQuery(ele).removeClass('active');
			});
			folderElement.addClass('active');
			if(folderName == 'vt_drafts') {
				self.openDraftFolder();
			} else {
				self.openFolder(folderName);
			}
		});
	},

	registerComposeEmail : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mail_compose').click(function() {
			var params = {
				step : "step1",
				module : "MailManager",
				view : "MassActionAjax",
				mode : "showComposeEmailForm",
				selected_ids : "[]",
				excluded_ids : "[]"
			};
			self.openComposeEmailForm(null, params);
		});
	},

	registerSettingsEdit : function() {
		var self = this;
		var container = this.getContainer();
		container.find('.mailbox_setting').click(function() {
			var accountId = $(this).data('boxid');
			app.helper.showProgress(app.vtranslate("JSLBL_Loading_Please_Wait")+"...");
			var params = {
				'module' : 'MailManager',
				'view' : 'Index',
				'_operation' : 'settings',
				'_operationarg' : 'edit',
				'account_id' : accountId
			};
			var popupInstance = Vtiger_Popup_Js.getInstance();
			popupInstance.showPopup(params, '', function(data) {
				app.helper.hideProgress();
				self.handleSettingsEvents(data);
				//self.registerDeleteMailboxEvent(data);
				self.registerSaveMailboxEvent(data);
				self.registerPortChangeEvent(data);
			});
		});
	},

	handleSettingsEvents : function(data) {
		var settingContainer = jQuery(data);
		settingContainer.find('#serverType').on('change', function(e) {
			var element = jQuery(e.currentTarget);
			var serverType = element.val();
			var useServer = '', useProtocol = '', useSSLType = '', useCert = '';
			if(serverType == 'gmail' || serverType == 'yahoo' || serverType == 'office365') {
				useServer = 'imap.gmail.com';
				useSmtp = 'ssl://smtp.gmail.com:465';
				if(serverType == 'yahoo') {
					useServer = 'imap.mail.yahoo.com';
					useSmtp = 'ssl://smtp.mail.yahoo.com:465';
				}
				if(serverType == 'office365'){
					useServer = 'outlook.office365.com';
					useSmtp = 'tls://smtp.office365.com:587';
				}
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').addClass('hide');
				settingContainer.find('.smtpPort').hide();
			} else if(serverType == 'fastmail') {
				useServer = 'mail.messagingengine.com';
				useSmtp = 'ssl://smtp.fastmail.com:465';
				useProtocol = 'IMAP2';
				useSSLType = 'tls';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').addClass('hide');
				settingContainer.find('.smtpPort').hide();
			} else if(serverType == 'other') {
				useServer = '';
				useSmtp = '';
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').removeClass('hide');
				settingContainer.find('.smtpPort').show();
				console.log(settingContainer.find('.smtpPort'))
			}else if(serverType == 'omniExchange') {
				useServer = 'mail.omnisrv.com';
				useSmtp = 'tls://mail.omnisrv.com:587';
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').addClass('hide');
				settingContainer.find('.smtpPort').hide();
			}  else {
				settingContainer.find('.settings_details').addClass('hide');
				settingContainer.find('.smtpPort').hide();
			}

			settingContainer.find('.refresh_settings').show();
			settingContainer.find('#_mbox_user').val('');
			settingContainer.find('#_mbox_pwd').val('');
			settingContainer.find('#_mbox_from_name').val('');
			settingContainer.find('#_mbox_from_email').val('');
			settingContainer.find('[name="_mbox_sent_folder"]').val('');
			settingContainer.find('.selectFolderValue').addClass('hide');
			settingContainer.find('.selectFolderDesc').removeClass('hide');
			if(useProtocol != '') {
				settingContainer.find('#_mbox_server').val(useServer);
				settingContainer.find('#_mbox_smtp_server').val(useSmtp);
				settingContainer.find('.mbox_protocol').each(function(i, node) {
					if(jQuery(node).val() == useProtocol) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('.mbox_ssltype').each(function(i, node) {
					if(jQuery(node).val() == useSSLType) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('.mbox_certvalidate').each(function(i, node) {
					if(jQuery(node).val() == useCert) {
						jQuery(node).attr('checked', true);
					}
				});
			}
		});
	},

	registerDeleteMailboxEvent : function(data) {
		var settingContainer = jQuery(document);
		settingContainer.on('click', '#deleteMailboxBtn', function(e) {
			var accountId = $(this).data('boxid');
			e.preventDefault();
			app.helper.showProgress(app.vtranslate("JSLBL_Deleting")+"...");
			var params = {
				'module' : 'MailManager',
				'view' : 'Index',
				'_operation' : 'settings',
				'_operationarg' : 'remove',
				'account_id' : accountId
			};
			app.request.post({"data" : params}).then(function(error, responseData) {
				app.helper.hideProgress();
				if(responseData.status) {
					window.location.reload();
				}
			});
		});
	},

	registerSaveMailboxEvent : function(data) {
		var settingContainer = jQuery(data);
		settingContainer.find('#saveMailboxBtn').click(function(e) {
			e.preventDefault();
			var form = settingContainer.find('#EditView');
			var params = {
	            submitHandler : function(form) {
	                  app.helper.showProgress();
	                var form = jQuery(form);
	                var data = form.serializeFormData();
	                app.helper.showProgress(app.vtranslate("JSLBL_Saving_And_Verifying")+"...");
	    			var params = {
	    				'module' : 'MailManager',
	    				'view' : 'Index',
	    				'_operation' : 'settings',
	    				'_operationarg' : 'save'
	    			};
	    			jQuery.extend(params, data);
	    			app.request.post({"data" : params}).then(function(error, responseData) {
	    				app.helper.hideModal();
	    				app.helper.hideProgress();
	    				if(error) {
	    					app.helper.showAlertNotification({'message' : error.message});
	    				} else if(responseData.mailbox) {
	    					window.location.reload();
	    				}
	    			});
	            }
			};
			validateAndSubmitForm(form,params);
		});
	},

	registerInitialLayout : function() {
		var self = this;
		var container = self.getContainer();
		if(container.find('#isMailBoxExists').val() == "0") {
			container.find('#modnavigator').addClass('hide');
			container.find('#listViewContent').addClass('paddingLeft0');
		}
	},

	openFolder : function(folderName, page, query, type, date) {
		var self = this;
		app.helper.showProgress(app.vtranslate("JSLBL_Loading_Please_Wait")+"...");
		if(!page) {
			page = 0;
		}
		var container = self.getContainer();
		vtUtils.hideValidationMessage(container.find('#mailManagerSearchbox'));
		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'folder',
			'_operationarg' : 'open',
			'_folder' : folderName,
			'_page' : page
		};
		if(query) {
			params['q'] = query;
		}
		if(type) {
			params['type'] = type;
		}
		if(date){
			params['date'] = date;
		}
		app.request.post({"data" : params}).then(function(error, responseData) {
			container.find('#mails_container').removeClass('col-lg-12');
			container.find('#mails_container').addClass('col-lg-5');
			container.find('#mailPreviewContainer').removeClass('hide');
			container.find('#mails_container').html(responseData);
			app.helper.hideProgress();
			self.registerMoveMailDropdownClickEvent();
			self.registerMailCheckBoxClickEvent();
			self.registerScrollForMailList();
			self.registerMainCheckboxClickEvent();
			self.registerPrevPageClickEvent();
			self.registerNextPageClickEvent();
			self.registerSearchEvent();
			self.registerFolderMailDeleteEvent();
			self.registerMoveMailToFolder();
			self.registerMarkMessageAsUnread();
			self.registerMailClickEvent();
			self.registerMarkMessageAsRead();
			self.clearPreviewContainer();
			self.loadMailContents(folderName);
			container.find('#searchType').trigger('change');
			self.registerEventForSingleMailActions();
			self.registerAutoCompleteSearchFields();
			
			var userName = jQuery(document).find('#isMailUserName').val();
			jQuery('.mailUserName').text(userName);
			
		});
	},

	/**
	 * Function to load the body of all mails in folder list
	 * @param {type} folderName
	 * @returns {undefined}
	 */
	loadMailContents : function(folderName){
		var mailids = jQuery('input[name="folderMailIds"]').val();
		if (typeof mailids !== 'undefined') {
			mailids = mailids.split(",");
			var params = {
				'module' : 'MailManager',
				'action' : 'Folder',
				'mode' : 'showMailContent',
				'mailids' : mailids,
				'folderName':folderName
			};
			app.request.post({"data" : params}).then(function(error, responseData) {
				for(var k in responseData){
					var messageContent = responseData[k];
					var messageEle = jQuery('#mmMailEntry_'+k);
					messageEle.find('.mmMailDesc').html(messageContent);
				}
			});
		}
	},

	registerFolderMailDeleteEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmDeleteMail').click(function(e) {
			var folder = jQuery(e.currentTarget).data('folder');
			var msgNos = new Array();
			container.find('.mailCheckBox').each(function(i, ele) {
				var element = jQuery(ele);
				if(element.is(":checked")) {
					msgNos.push(element.closest('.mailEntry').find('.msgNo').val());
				}
			});
			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showPromptBox({'message' : app.vtranslate('LBL_DELETE_CONFIRMATION')}).then(function() {
					app.helper.showProgress(app.vtranslate("JSLBL_Deleting")+"...");
					var params = {
						'module' : 'MailManager',
						'view' : 'Index',
						'_operation' : 'mail',
						'_operationarg' : 'delete',
						'_folder' : folder,
						'_msgno' : msgNos.join(',')
					};
					app.request.post({data : params}).then(function(err,data) {
						app.helper.hideProgress();
						if(data.status) {
							app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_DELETED')});
							self.updateUnreadCount("-"+self.getUnreadCountByMsgNos(msgNos), folder);
							self.updatePagingCount(msgNos.length);
							for(var i = 0; i < msgNos.length; i++) {
								container.find('#mmMailEntry_'+msgNos[i]).remove();
							}
							var openedMsgNo = container.find('#mmMsgNo').val();
							if(jQuery.inArray(openedMsgNo, msgNos) !== -1) {
								self.clearPreviewContainer();
							}
						}
					});
				});
			}
		});
	},

	updatePagingCount : function(deletedCount) {
		var pagingDataElement = jQuery('.pageInfoData');
		var pagingElement = jQuery('.pageInfo');
		if(pagingDataElement.length != 0){
			var total = pagingDataElement.data('total');
			var start = pagingDataElement.data('start');
			var end = pagingDataElement.data('end');
			var labelOf = pagingDataElement.data('label-of');
			total = total - deletedCount;
			pagingDataElement.data('total', total);
			pagingElement.html(start+' '+'-'+' '+end+' '+labelOf+' '+total+'&nbsp;&nbsp;');
		}
	},

	registerMoveMailToFolder : function() {
		var self = this;
		var container = self.getContainer();
		var moveToDropDown = container.find('#mmMoveToFolder');
		moveToDropDown.on('click','a',function(e) {
			var element = jQuery(e.currentTarget);
			var moveToFolder = element.closest('li').data('movefolder');
			var folder = element.closest('li').data('folder');
			var msgNos = new Array();
			container.find('.mailCheckBox').each(function(i, ele) {
				var element = jQuery(ele);
				if(element.is(":checked")) {
					msgNos.push(element.closest('.mailEntry').find('.msgNo').val());
				}
			});
			if(msgNos.length <= 0) {
				container.find('.moveToFolderDropDown').removeClass('open');
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showProgress(app.vtranslate("JSLBL_MOVING")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'move',
					'_folder' : folder,
					'_moveFolder' : moveToFolder,
					'_msgno' : msgNos.join(',')
				};
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAIL_MOVED')});
						var unreadCount = self.getUnreadCountByMsgNos(msgNos);
						self.updateUnreadCount("-"+unreadCount, folder);
						self.updateUnreadCount("+"+unreadCount, moveToFolder);
						for(var i = 0; i < msgNos.length; i++) {
							container.find('#mmMailEntry_'+msgNos[i]).remove();
						}
						container.find('.moveToFolderDropDown').removeClass('open');
					}
				});
			}
		});
	},

	registerMarkMessageAsUnread : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmMarkAsUnread').click(function(e) {
			var folder = jQuery(e.currentTarget).data('folder');
			var msgNos = new Array();
			container.find('.mailCheckBox').each(function(i, ele) {
				var element = jQuery(ele);
				if(element.is(":checked")) {
					msgNos.push(element.closest('.mailEntry').find('.msgNo').val());
				}
			});
			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showProgress(app.vtranslate("JSLBL_Updating")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'mark',
					'_folder' : folder,
					'_msgno' : msgNos.join(','),
					'_markas' : 'unread'
				};
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_MARKED_UNREAD')});
						self.markMessageUnread(msgNos);
						self.updateUnreadCount("+"+self.getUnreadCountByMsgNos(msgNos), folder);
					}
				});
			}
		});
	},

	registerMarkMessageAsRead : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmMarkAsRead').click(function(e) {
			var folder = jQuery(e.currentTarget).data('folder');
			var msgNos = new Array();
			container.find('.mailCheckBox').each(function(i, ele) {
				var element = jQuery(ele);
				if(element.is(":checked")) {
					msgNos.push(element.closest('.mailEntry').find('.msgNo').val());
				}
			});
			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showProgress(app.vtranslate("JSLBL_Updating")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'mark',
					'_folder' : folder,
					'_msgno' : msgNos.join(','),
					'_markas' : 'read'
				};
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_MARKED_READ')});
						self.markMessageRead(msgNos);
						self.updateUnreadCount("-"+self.getUnreadCountByMsgNos(msgNos), folder);
					}
				});
			}
		});
	},

	registerSearchEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mailManagerSearchbox').keyup(function (event) {
			if(event.keyCode == 13){
				jQuery("#mm_searchButton").click();
			}
		});
		container.find('#mm_searchButton').click(function() {
			var query = container.find('#mailManagerSearchbox').val();
			var date = container.find('[name="date"]').val();
			
			if(query.trim() == '' && date == '') {
				vtUtils.showValidationMessage(container.find('#mailManagerSearchbox'));
				vtUtils.showValidationMessage(container.find('[name="date"]'));
				app.helper.showErrorNotification({'message': 'Atleast one field is required.'});
				return false;
			} else {
				vtUtils.hideValidationMessage(container.find('#mailManagerSearchbox'));
				vtUtils.hideValidationMessage(container.find('[name="date"]'));
			}
			var folder = container.find('#mailManagerSearchbox').data('foldername');
			var type = container.find('#searchType').val();
			
			self.openFolder(folder, 0, query, type, date);
		});
	},

	markMessageUnread : function(msgNos) {
		var self = this;
		var container = self.getContainer();
		if(typeof msgNos == "string") {
			msgNos = new Array(msgNos);
		}
		if(typeof msgNos == "object") {
			for(var i = 0; i < msgNos.length; i++) {
				var msgNo = msgNos[i];
				var msgEle = container.find('#mmMailEntry_'+msgNo);
				msgEle.removeClass('mmReadEmail');
				msgEle.data('read', "0");
				var nameSubject = "<strong>" + msgEle.find('.nameSubjectHolder').html() + "</strong>";
				msgEle.find('.nameSubjectHolder').html(nameSubject);
			}
		}
	},

	markMessageRead : function(msgNos) {
		var self = this;
		var container = self.getContainer();
		if(typeof msgNos == "string") {
			msgNos = new Array(msgNos);
		}
		if(typeof msgNos == "object") {
			for(var i = 0; i < msgNos.length; i++) {
				var msgNo = msgNos[i];
				var msgEle = container.find('#mmMailEntry_'+msgNo);
				msgEle.addClass('mmReadEmail');
				msgEle.data('read', "1");
				var nameSubject = msgEle.find('.nameSubjectHolder').find('strong').html();
				msgEle.find('.nameSubjectHolder').html(nameSubject);
			}
		}
	},

	getUnreadCountByMsgNos : function(msgNos) {
		var count = 0;
		var self = this;
		var container = self.getContainer();
		for(var i = 0; i < msgNos.length; i++) {
			var isRead = parseInt(container.find('#mmMailEntry_'+msgNos[i]).data('read'));
			if(isRead == 0) {
				count++;
			}
		}
		return count;
	},

	registerMailCheckBoxClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.mailCheckBox').click(function(e) {
			var element = jQuery(e.currentTarget);
			if(element.is(":checked")) {
				element.closest('.mailEntry').addClass('highLightMail');
				element.closest('.mailEntry').removeClass('fontBlack');
				element.closest('.mailEntry').addClass('whiteFont');
				element.closest('.mailEntry').removeClass('mmReadEmail');
				element.closest('.mailEntry').find('.mmDateTimeValue').addClass('mmListDateDivSelected');
			} else {
				var isRead = element.closest('.mailEntry').data('read');
				if(parseInt(isRead)) {
					element.closest('.mailEntry').addClass('mmReadEmail');
					element.closest('.mailEntry').removeClass('highLightMail');
				} else {
					element.closest('.mailEntry').removeClass('highLightMail');
				}
				element.closest('.mailEntry').find('.mmDateTimeValue').removeClass('mmListDateDivSelected');
				element.closest('.mailEntry').addClass('fontBlack');
			}
		});
	},

	registerMoveMailDropdownClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.moveToFolderDropDown').click(function(e) {
			e.stopImmediatePropagation();
			var element = jQuery(e.currentTarget);
			element.addClass('open');
		});
	},

	registerScrollForMailList : function() {
		var self = this;
		self.getContainer().find('#emailListDiv').mCustomScrollbar({
			setHeight: 600,
			autoExpandScrollbar: true,
			scrollInertia: 200,
			autoHideScrollbar: true,
			theme : "dark-3"
		});
	},

	registerMainCheckboxClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mainCheckBox').click(function(e) {
			var element = jQuery(e.currentTarget);
			if(element.is(":checked")) {
				container.find('.mailCheckBox').each(function(i, ele) {
					jQuery(ele).prop('checked', true);
					jQuery(ele).closest('.mailEntry').addClass('highLightMail');
					jQuery(ele).closest('.mailEntry').removeClass('fontBlack');
					jQuery(ele).closest('.mailEntry').addClass('whiteFont');
					jQuery(ele).closest('.mailEntry').removeClass('mmReadEmail');
					jQuery(ele).closest('.mailEntry').find('.mmDateTimeValue').addClass('mmListDateDivSelected');
				});
			} else {
				container.find('.mailCheckBox').each(function(i, ele) {
					jQuery(ele).prop('checked', false);
					var isRead = jQuery(ele).closest('.mailEntry').data('read');
					if(parseInt(isRead)) {
						jQuery(ele).closest('.mailEntry').addClass('mmReadEmail');
						jQuery(ele).closest('.mailEntry').removeClass('highLightMail');
					} else {
						jQuery(ele).closest('.mailEntry').removeClass('highLightMail');
					}
					jQuery(ele).closest('.mailEntry').find('.mmDateTimeValue').removeClass('mmListDateDivSelected');
					jQuery(ele).closest('.mailEntry').addClass('fontBlack');
				});
			}
		});
	},

	registerPrevPageClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#PreviousPageButton').click(function(e) {
			var element = jQuery(e.currentTarget);
			var folder = element.data('folder');
			var page = element.data('page');
			self.openFolder(folder, page, jQuery('#mailManagerSearchbox').val(), jQuery('#searchType').val(), jQuery('[name="date"]').val());
		});
	},

	registerNextPageClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#NextPageButton').click(function(e) {
			var element = jQuery(e.currentTarget);
			var folder = element.data('folder');
			var page = element.data('page');
			self.openFolder(folder, page, jQuery('#mailManagerSearchbox').val(), jQuery('#searchType').val(), jQuery('[name="date"]').val());
		});
	},

	registerMailClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.mmfolderMails').click(function(e) {
			
			if(jQuery(e.target).closest('span').parent().hasClass('singleMailActions'))
				return;
			
			var emailElement = jQuery(e.currentTarget);
			var parentEle = emailElement.closest('.mailEntry');
			var msgNo = emailElement.find('.msgNo').val();
			var params = {
				'module' : 'MailManager',
				'view' : 'Index',
				'_operation' : 'mail',
				'_operationarg' : 'open',
				'_folder' : parentEle.data('folder'),
				'_msgno' : msgNo
			};
			app.helper.showProgress(app.vtranslate("JSLBL_Opening")+"...");
			app.request.post({data : params}).then(function(err, data) {
				app.helper.hideProgress();
				var uiContent = data.ui;
				var unreadCount = self.getUnreadCountByMsgNos(new Array(msgNo));
				jQuery(parentEle).addClass('mmReadEmail');
				jQuery(parentEle).data('read', "1");
				var nameSubject = jQuery(parentEle).find('.nameSubjectHolder').find('strong').html();
				jQuery(parentEle).find('.nameSubjectHolder').html(nameSubject);
				container.find('#mailPreviewContainer').html(uiContent);
				self.highLightMail(msgNo);
				self.registerMailDeleteEvent();
				self.registerForwardEvent();
				self.registerPrintEvent();
				self.registerReplyEvent();
				self.registerReplyAllEvent();
				self.showRelatedActions();
				self.registerMailPaginationEvent();
				container.find('.emailDetails').popover({html: true});
				self.updateUnreadCount("-"+unreadCount, jQuery(parentEle).data('folder'));
				self.loadContentsInIframe(container.find('#mmBody'));
			});
		});
	},

	loadContentsInIframe : function(element) {
		var bodyContent = element.html();
		element.html('<iframe id="bodyFrame" style="width: 100%; border: none;"></iframe>');
		var frameElement = jQuery("#bodyFrame")[0].contentWindow.document;
		frameElement.open();
		frameElement.close();
		jQuery('#bodyFrame').contents().find('html').html(bodyContent);
		jQuery('#bodyFrame').contents().find('html').find('a').on('click', function(e) {
			e.preventDefault();
			var url = jQuery(e.currentTarget).attr('href');
			window.open(url, '_blank');
		});
	},

	highLightMail : function(msgNo) {
		var self = this;
		var container = self.getContainer();
		container.find('.mailEntry').each(function(i, ele) {
			var element = jQuery(ele);
			var isRead = element.data('read');
			if(parseInt(isRead)) {
				element.addClass('mmReadEmail');
				element.removeClass('highLightMail');
			} else {
				element.removeClass('highLightMail');
			}
			element.find('.mmDateTimeValue').removeClass('mmListDateDivSelected');
			element.addClass('fontBlack');
		});
		var selectedMailEle = container.find('#mmMailEntry_'+msgNo);
		selectedMailEle.addClass('highLightMail');
		selectedMailEle.removeClass('fontBlack');
		selectedMailEle.addClass('whiteFont');
		selectedMailEle.removeClass('mmReadEmail');
		selectedMailEle.find('.mmDateTimeValue').addClass('mmListDateDivSelected');
	},

	registerMailPaginationEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.mailPagination').click(function(e) {
			var element = jQuery(e.currentTarget);
			var msgNo = element.data('msgno');
			var folder = element.data('folder');
			var params = {
				'module' : 'MailManager',
				'view' : 'Index',
				'_operation' : 'mail',
				'_operationarg' : 'open',
				'_folder' : folder,
				'_msgno' : msgNo
			};
			app.helper.showProgress(app.vtranslate("JSLBL_Opening")+"...");
			app.request.post({data : params}).then(function(err, data) {
				app.helper.hideProgress();
				var uiContent = data.ui;
				container.find('#mmMailEntry_'+msgNo).addClass('mmReadEmail');
				container.find('#mmMailEntry_'+msgNo).data('read', "1");
				var nameSubject = container.find('#mmMailEntry_'+msgNo).find('.nameSubjectHolder').find('strong').html();
				container.find('#mmMailEntry_'+msgNo).find('.nameSubjectHolder').html(nameSubject);
				container.find('#mailPreviewContainer').html(uiContent);
				self.registerMailDeleteEvent();
				self.registerForwardEvent();
				self.registerReplyEvent();
				self.registerReplyAllEvent();
				self.showRelatedActions();
				self.registerMailPaginationEvent();
				self.highLightMail(msgNo);
				self.loadContentsInIframe(container.find('#mmBody'));
			});
		});
	},

	registerMailDeleteEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmDelete').click(function() {
			var msgNo = jQuery('#mmMsgNo').val();
			var folder = jQuery('#mmFolder').val();
			app.helper.showPromptBox({'message' : app.vtranslate('LBL_DELETE_CONFIRMATION')}).then(function() {
				app.helper.showProgress(app.vtranslate("JSLBL_Deleting")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'delete',
					'_folder' : folder,
					'_msgno' : msgNo
				};
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						container.find('#mmMailEntry_'+msgNo).remove();
						var previewHtml = '<div class="mmListMainContainer">\n\
										<center><strong>'+app.vtranslate('JSLBL_NO_MAIL_SELECTED_DESC')+'</center></strong></div>';
						jQuery('#mailPreviewContainer').html(previewHtml);
					}
				});
			});
		});
	},

	registerForwardEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmForward').click(function() {
			app.helper.showProgress(app.vtranslate("JSLBL_Loading")+"...");
			var msgNo = jQuery('#mmMsgNo').val();
			var from = jQuery('#mmFrom').val();
			var to = jQuery('#mmTo').val();
			var cc = jQuery('#mmCc').val() ? jQuery('#mmCc').val() : '';
			var subject = JSON.parse(jQuery('#mmSubject').val());
			var body = jQuery('#mmBody').find('iframe#bodyFrame').contents().find('html').html();
			var date = jQuery('#mmDate').val();
			var folder = jQuery('#mmFolder').val();

			var fwdMsgMetaInfo = app.vtranslate('JSLBL_FROM') + from + '<br/>'+
					app.vtranslate('JSLBL_DATE') + date + '<br/>'+
					app.vtranslate('JSLBL_SUBJECT') + subject;
			if (to != '' && to != null) {
				fwdMsgMetaInfo += '<br/>'+app.vtranslate('JSLBL_TO') + to;
			}
			if (cc != '' && cc != null) {
				fwdMsgMetaInfo += '<br/>'+app.vtranslate('JSLBL_CC') + cc;
			}
			fwdMsgMetaInfo += '<br/>';

			var fwdSubject = (subject.toUpperCase().indexOf('FWD:') == 0) ? subject : 'Fwd: ' + subject;
			var fwdBody = '<p></p><p>'+app.vtranslate('JSLBL_FORWARD_MESSAGE_TEXT')+'<br/>'+fwdMsgMetaInfo+'</p>'+body;
			var attchmentCount = parseInt(container.find('#mmAttchmentCount').val());
			if(attchmentCount) {
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'forward',
					'messageid' : encodeURIComponent(msgNo),
					'folder' : encodeURIComponent(folder),
					'subject' : encodeURIComponent(fwdSubject),
					'body' : encodeURIComponent(fwdBody)
				};
				app.request.post({'data' : params}).then(function(err, data) {
					var draftId = data.emailid;
					var newParams = {
						'module' : 'Emails',
						'view' : 'ComposeEmail',
						'mode' : 'emailEdit',
						'record' : draftId
					};
					app.request.post({data : newParams}).then(function(err,data) {
						app.helper.hideProgress();
						if(err === null) {
							var dataObj = jQuery(data);
							var descriptionContent = dataObj.find('#iframeDescription').val();
							app.helper.showModal(data, {cb : function() {
								var editInstance = new Emails_MassEdit_Js();
								editInstance.registerEvents();
								jQuery('#emailPreviewIframe').contents().find('html').html(descriptionContent);
								jQuery("#emailPreviewIframe").height(jQuery('#emailPreviewIframe').contents().find('html').height());
							}});
						}
					});
				});
			} else {
				app.helper.hideProgress();
				var params = {
					'step' : "step1",
					'module' : "MailManager",
					'view' : "MassActionAjax",
					'mode' : "showComposeEmailForm",
					'selected_ids' : "[]",
					'excluded_ids' : "[]",
				}
				self.openComposeEmailForm("forward", params, {'subject' : fwdSubject, 'body' : fwdBody});
			}
		});
	},

	registerPrintEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmPrint').click(function() {
			var subject = JSON.parse(container.find('#mmSubject').val());
			var from = container.find('#mmFrom').val();
			var to = container.find('#mmTo').val();
			var cc = container.find('#mmCc').val();
			var date = container.find('#mmDate').val();
			var body = jQuery('#mmBody').find('iframe#bodyFrame').contents().find('html').html();

			var content = window.open();
			content.document.write("<b>"+subject+"</b><br>");
			content.document.write(app.vtranslate("JSLBL_FROM")+" "+from +"<br>");
			content.document.write(app.vtranslate("JSLBL_TO")+" "+to+"<br>");
			if(cc) {
				content.document.write(app.vtranslate("JSLBL_CC")+" "+cc+"<br>");
			}
			content.document.write(app.vtranslate("JSLBL_DATE")+" "+date+"<br>");
			content.document.write("<br><br>"+body);
			content.print();
		});
	},

	registerReplyEvent : function() {
		var self = this;
		self.getContainer().find('#mmReply').click(function() {
			self.openReplyEmail(false);
		});
	},

	registerReplyAllEvent : function() {
		var self = this;
		self.getContainer().find('#mmReplyAll').click(function() {
			self.openReplyEmail(true);
		});
	},

	openReplyEmail : function(all) {
		var self = this;
		if (typeof(all) == 'undefined') {
			all = true;
		}
		var mUserName = jQuery('#mmUserName').val();
		var from = jQuery('#mmFrom').val();
		var to = all ? jQuery('#mmTo').val() : '';
		var cc = all ? jQuery('#mmCc').val() : '';

		var mailIds = '';
		if(to != null) {
			mailIds = to;
		}
		if(cc != null) {
			mailIds = mailIds ? mailIds+','+cc : cc;
		}

		mailIds = mailIds.replace(/\s+/g, '');

		var emails = mailIds.split(',');
		for(var i = 0; i < emails.length ; i++) {
			if(emails[i].indexOf(mUserName) != -1){
				emails.splice(i,1);
			}
		}
		mailIds = emails.join(',');

		mailIds = mailIds.replace(',,', ',');
		if(mailIds.charAt(mailIds.length-1) == ',') {
			mailIds = mailIds.slice(0, -1);
		} else if(mailIds.charAt(0) == ','){
			mailIds = mailIds.slice(1);
		}

		var subject = JSON.parse(jQuery('#mmSubject').val());
		var body = jQuery('#mmBody').find('iframe#bodyFrame').contents().find('html').html();
		var date = jQuery('#mmDate').val();

		var replySubject = (subject.toUpperCase().indexOf('RE:') == 0) ? subject : 'Re: ' + subject;
		var replyBody = '<p></br></br></p><p style="margin:0;padding:0;">On '+date+', '+from+' wrote :</p><blockquote style="border:0;margin:0;border-left:1px solid gray;padding:0 0 0 2px;">'+body+'</blockquote><br />';
		var parentRecord = new Array();
		var linktoElement = jQuery('[name=_mlinkto]');
		linktoElement.each(function(index){
			var value = jQuery(this).val();
			if(value) {
				parentRecord.push(value);
			}
		});
		var params = {
			'step' : "step1",
			'module' : "MailManager",
			'view' : "MassActionAjax",
			'mode' : "showComposeEmailForm",
			'linktomodule' : 'true', 
			'excluded_ids' : "[]",
			'to' : '["'+from+'"]'
		}
		if(parentRecord.length) {
			params['selected_ids'] = parentRecord;
		} else {
			params['selected_ids'] = "[]";
		}
		if(mailIds) {
			self.openComposeEmailForm("replyall", params, {'subject' : replySubject, 'body' : replyBody, 'ids' : mailIds});
		} else {
			self.openComposeEmailForm("reply", params, {'subject' : replySubject, 'body' : replyBody});
		}
	},

	showRelatedActions : function() {
		var self = this;
		var container = self.getContainer();
		var from = container.find('#mmFrom').val();
		var to = container.find('#mmTo').val();
		var folder = container.find('#mmFolder').val();
		var msgNo = container.find('#mmMsgNo').val();
		var msgUid = container.find('#mmMsgUid').val();

		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'relation',
			'_operationarg' : 'find',
			'_mfrom' : from,
			'_mto' : to,
			'_folder' : folder,
			'_msgno' : msgNo,
			'_msguid' : msgUid
		};

		app.request.post({data : params}).then(function(err, data) {
			container.find('#relationBlock').html(data.ui);
			self.handleRelationActions();
			app.helper.showVerticalScroll(container.find('#relationBlock .recordScroll'), {autoHideScrollbar: true});
			var iframeHeight = jQuery('#mails_container').height() - (200 + jQuery('#mailManagerActions').height());
			var contentHeight = jQuery('#bodyFrame').contents().find('html').height();
			if (contentHeight > iframeHeight) {
				jQuery('#bodyFrame').css({'height': iframeHeight});
			} else {
				jQuery('#bodyFrame').css({'height': contentHeight});
			}
		});
	},

	openDraftFolder : function(page, query, type) {
		var self = this;
		app.helper.showProgress(app.vtranslate("JSLBL_Loading_Please_Wait")+"...");
		if(!page) {
			page = 0;
		}
		var container = self.getContainer();
		vtUtils.hideValidationMessage(container.find('#mailManagerSearchbox'));
		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'folder',
			'_operationarg' : 'drafts',
			'_page' : page
		};
		if(query) {
			params['q'] = query;
		}
		if(type) {
			params['type'] = type;
		}
		app.request.post({"data" : params}).then(function(error, responseData) {
			container.find('#mails_container').removeClass('col-lg-5');
			container.find('#mails_container').addClass('col-lg-12');
			container.find('#mails_container').html(responseData);
			container.find('#mailPreviewContainer').addClass('hide');
			app.helper.hideProgress();
			self.registerMoveMailDropdownClickEvent();
			self.registerMailCheckBoxClickEvent();
			self.registerScrollForMailList();
			self.registerMainCheckboxClickEvent();
			self.registerDraftPrevPageClickEvent();
			self.registerDraftNextPageClickEvent();
			self.registerDraftMailClickEvent();
			self.registerDraftSearchEvent();
			self.registerDraftDeleteEvent();
			self.clearPreviewContainer();
		});
	},

	registerDraftPrevPageClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#PreviousPageButton').click(function(e) {
			var element = jQuery(e.currentTarget);
			var page = element.data('page');
			self.openDraftFolder(page);
		});
	},

	registerDraftNextPageClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#NextPageButton').click(function(e) {
			var element = jQuery(e.currentTarget);
			var page = element.data('page');
			self.openDraftFolder(page);
		});
	},

	registerDraftMailClickEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.draftEmail').click(function(e) {
			e.preventDefault();
			var element = jQuery(e.currentTarget);
			var msgNo = element.find('.msgNo').val();
			var params = {
				'module' : 'Emails',
				'view' : 'ComposeEmail',
				'mode' : 'emailEdit',
				'record' : msgNo
			};
			app.helper.showProgress(app.vtranslate("JSLBL_Opening")+"...");
			app.request.post({data : params}).then(function(err,data) {
				app.helper.hideProgress();
				if(err === null) {
					var dataObj = jQuery(data);
					var descriptionContent = dataObj.find('#iframeDescription').val();
					app.helper.showModal(data, {cb:function() {
						var editInstance = new Emails_MassEdit_Js();
						editInstance.registerEvents();
						jQuery('#emailPreviewIframe').contents().find('html').html(descriptionContent);
						jQuery("#emailPreviewIframe").height(jQuery('.email-body-preview').height());
					}});
				}
			});
		});
	},

	registerDraftSearchEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mm_searchButton').click(function() {
			var query = container.find('#mailManagerSearchbox').val();
			if(query.trim() == '') {
				vtUtils.showValidationMessage(container.find('#mailManagerSearchbox'), app.vtranslate('JSLBL_ENTER_SOME_VALUE'));
				return false;
			} else {
				vtUtils.hideValidationMessage(container.find('#mailManagerSearchbox'));
			}
			var type = container.find('#searchType').val();
			self.openDraftFolder(0, query, type);
		});
	},

	registerDraftDeleteEvent : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#mmDeleteMail').click(function() {
			var msgNos = new Array();
			container.find('.mailCheckBox').each(function(i, ele) {
				var element = jQuery(ele);
				if(element.is(":checked")) {
					msgNos.push(element.closest('.mailEntry').find('.msgNo').val());
				}
			});
			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showPromptBox({'message' : app.vtranslate('LBL_DELETE_CONFIRMATION')}).then(function() {
					app.helper.showProgress(app.vtranslate("JSLBL_Deleting")+"...");
					var params = {
						'module' : 'MailManager',
						'view' : 'Index',
						'_operation' : 'mail',
						'_operationarg' : 'delete',
						'_folder' : '__vt_drafts',
						'_msgno' : msgNos.join(',')
					};
					app.request.post({data : params}).then(function(err,data) {
						app.helper.hideProgress();
						if(data.status) {
							self.openDraftFolder();
							app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_DELETED')});
						}
					});
				});
			}
		});
	},

	updateUnreadCount : function(count, folder) {
		var self = this;
		var container = self.getContainer();
		if(!folder) {
			folder = container.find('.mm_folder.active').data('foldername');
		}
		var newCount;
		if(typeof count == "number") {
			newCount = parseInt(count);
		} else {
			var oldCount = parseInt(container.find('.mm_folder[data-foldername="'+folder+'"]').find('.mmUnreadCountBadge').text());
			if(count.substr(0, 1) == "+") {
				newCount = oldCount + (parseInt(count.substr(1, (count.length - 1))));
			} else if(count.substr(0, 1) == "-") {
				newCount = oldCount - (parseInt(count.substr(1, (count.length - 1))));
			} else {
				newCount = parseInt(count);
			}
		}
		container.find('.mm_folder[data-foldername="'+folder+'"]').find('.mmUnreadCountBadge').text(newCount);
		if(newCount > 0) {
			container.find('.mm_folder[data-foldername="'+folder+'"]').find('.mmUnreadCountBadge').removeClass("hide");
		} else {
			container.find('.mm_folder[data-foldername="'+folder+'"]').find('.mmUnreadCountBadge').addClass("hide");
		}
	},

	handleRelationActions : function() {
		var self = this;
		var container = self.getContainer();
		container.find('#_mlinktotype').on('change', function(e) {
			var element = jQuery(e.currentTarget);
			var actionType = element.data('action');
			var module = element.val();
			var relatedRecord = self.getRecordForRelation();
			if(relatedRecord !== false) {
				if(actionType == "associate") {
					if(module == 'Emails') {
						self.associateEmail(relatedRecord);
					} else if(module == "ModComments") {
						self.associateComment(relatedRecord);
					} else if(module) {
						self.createRelatedRecord(module);
					}
				} else if(module) {
					self.createRelatedRecord(module);
				}
			}
			self.resetRelationDropdown();
		});
	},

	associateEmail : function(relatedRecord) {
		var self = this;
		var container = self.getContainer();
		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'relation',
			'_operationarg' : 'link',
			'_mlinkto' : relatedRecord,
			'_mlinktotype' : 'Emails',
			'_folder' : container.find('#mmFolder').val(),
			'_msgno' : container.find('#mmMsgNo').val()
		}
		app.helper.showProgress(app.vtranslate('JSLBL_Associating')+'...');
		app.request.post({data : params}).then(function(err,data) {
			if (err === null) {
				app.helper.showSuccessNotification({'message':''});
				app.helper.hideProgress();
			} else {
				app.helper.showErrorNotification({"message": err});
			}
		});
	},

	associateComment : function(relatedRecord) {
		var self = this;
		var container = self.getContainer();
		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'relation',
			'_operationarg' : 'commentwidget',
			'_mlinkto' : relatedRecord,
			'_mlinktotype' : 'ModComments',
			'_folder' : container.find('#mmFolder').val(),
			'_msgno' : container.find('#mmMsgNo').val()
		}
		app.helper.showProgress(app.vtranslate('JSLBL_Loading')+'...');
		app.request.post({data : params}).then(function(err, data) {
			app.helper.hideProgress();
			app.helper.showModal(data, {'cb' : function(data) {
				jQuery('[name="saveButton"]', data).on('click',function(e){
					e.preventDefault();
					self.saveComment(data);
				});
			}});
		});
	},

	createRelatedRecord : function(module) {
		var self = this;
		var container = self.getContainer();
		var relatedRecord = self.getRecordForRelation();
		var msgNo = container.find('#mmMsgNo').val();
		var folder = container.find('#mmFolder').val();
		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'relation',
			'_operationarg' : 'create_wizard',
			'_mlinktotype' : module,
			'_folder' : folder,
			'_msgno' : msgNo
		};
		if(relatedRecord && relatedRecord !== null) {
			params['_mlinkto'] = relatedRecord;
		}
		app.helper.showProgress(app.vtranslate('JSLBL_Loading')+'...');
		app.request.post({data : params}).then(function(err, data) {
			app.helper.hideProgress();
			app.helper.showModal(data);
			var form = jQuery('form[name="QuickCreate"]');
			app.event.trigger('post.QuickCreateForm.show',form);
			form.find('.modal-body').mCustomScrollbar({
				setHeight: 400,
				autoExpandScrollbar: true,
				scrollInertia: 200,
				autoHideScrollbar: true,
				theme : "dark-3"
			});
			vtUtils.applyFieldElementsView(form);
			var moduleName = form.find('[name="module"]').val();
			var targetClass = app.getModuleSpecificViewClass('Edit', moduleName);
			var targetInstance = new window[targetClass]();
			targetInstance.registerBasicEvents(form);
			var newParams = {};
			newParams.callbackFunction = function() {
				app.helper.hideModal();
				self.showRelatedActions();
			};
			newParams.requestParams = params;
			self.quickCreateSave(form, newParams);
			app.helper.hideProgress();
		});
	},

	/**
	 * Register Quick Create Save Event
	 * @param {type} form
	 * @returns {undefined}
	 */
	quickCreateSave : function(form,invokeParams){
		var container = this.getContainer();
		var params = {
			submitHandler: function(form) {
				// to Prevent submit if already submitted
				jQuery("button[name='saveButton']").attr("disabled","disabled");
				if(this.numberOfInvalids() > 0) {
					return false;
				}
				var formData = jQuery(form).serialize();
				var requestParams = invokeParams.requestParams;

				// replacing default parameters for custom handlings in mail manager
				formData = formData.replace('module=', 'xmodule=').replace('action=', 'xaction=');
				if(requestParams) {
					requestParams['_operationarg'] = 'create';
					if(requestParams['_mlinktotype'] == 'Events') {
						requestParams['_mlinktotype'] = 'Calendar';
					}
					jQuery.each(requestParams, function(key, value){
						formData += "&"+key+"="+value;
					});
				}

				app.request.post({data:formData}).then(function(err,data){
                    if(err === null) {
						if (!data.error) {
							jQuery('.vt-notification').remove();
							app.event.trigger("post.QuickCreateForm.save",data,jQuery(form).serializeFormData());
							app.helper.hideModal();
							app.helper.showSuccessNotification({"message":app.vtranslate('JS_RECORD_CREATED')});
							invokeParams.callbackFunction(data, err);
						} else {
							jQuery("button[name='saveButton']").removeAttr('disabled');
							app.event.trigger('post.save.failed', data);
						}
                    }else{
						app.event.trigger("post.QuickCreateForm.save",data,jQuery(form).serializeFormData());
                        app.helper.showErrorNotification({"message":err});
                    }
                });
			}
		};
		form.vtValidate(params);
	},

	saveComment : function(data) {
		var _mlinkto = jQuery('[name="_mlinkto"]', data).val();
		var _mlinktotype = jQuery('[name="_mlinktotype"]', data).val();
		var _msgno = jQuery('[name="_msgno"]', data).val();
		var _folder = jQuery('[name="_folder"]', data).val();
		var commentcontent = jQuery('[name="commentcontent"]', data).val();
		if(commentcontent.trim() == "") {
			var validationParams = {
				position: {
					'my' : 'bottom left',
					'at' : 'top left',
					'container' : jQuery('#commentContainer', data)
				}
			};
			var errorMsg = app.vtranslate('JSLBL_CANNOT_ADD_EMPTY_COMMENT');
			vtUtils.showValidationMessage(jQuery('[name="commentcontent"]', data), errorMsg, validationParams);
			return false;
		} else {
			vtUtils.hideValidationMessage(jQuery('[name="commentcontent"]', data));
		}
		var params = {
			'module' : 'MailManager',
			'view' : 'Index',
			'_operation' : 'relation',
			'_operationarg' : 'create',
			'commentcontent' : commentcontent,
			'_mlinkto' : _mlinkto,
			'_mlinktotype' : _mlinktotype,
			'_msgno' : _msgno,
			'_folder' : _folder
		}
		app.helper.showProgress(app.vtranslate('JSLBL_Saving')+'...');
		app.request.post({'data' : params}).then(function(err, response) {
			app.helper.hideProgress();
			if(response.ui) {
				app.helper.showSuccessNotification({'message':''});
				app.helper.hideModal();
			} else {
				app.helper.showAlertBox({'message' : app.vtranslate("JSLBL_FAILED_ADDING_COMMENT")});
			}
		});
	},

	getRecordForRelation : function() {
		var self = this;
		var container = self.getContainer();
		var element = container.find('[name="_mlinkto"]');
		if(element.length > 0) {
			if(element.length == 1) {
				element.attr('checked', true);
				return element.val();
			} else {
				selected = false;
				element.each(function(i, ele) {
					if(jQuery(ele).is(":checked")) {
						selected = true;
					}
				});
				if(selected) {
					return container.find('[name="_mlinkto"]:checked').val();
				} else {
					app.helper.showAlertBox({'message' : app.vtranslate("JSLBL_PLEASE_SELECT_ATLEAST_ONE_RECORD")});
					return false;
				}
			}
		} else {
			return null;
		}
	},

	resetRelationDropdown : function() {
		this.getContainer().find('#_mlinktotype').val("");
	},

	openComposeEmailForm : function(type, params, data) {
		Vtiger_Index_Js.showComposeEmailPopup(params, function(response) {
			var descEle = jQuery(response).find('#description');
			if(type == "reply" || type == "forward") {
				jQuery('#subject', response).val(data.subject);
				descEle.val(data.body);
				jQuery('[name="cc"]', response).val("");
				jQuery('.ccContainer', response).addClass("hide");
				jQuery('#ccLink', response).css("display", "");
			} else if(type == "replyall") {
				jQuery('#subject', response).val(data.subject);
				descEle.val(data.body);
				var mailIds = data.ids;
				if(mailIds) {
					jQuery('.ccContainer', response).removeClass("hide");
					jQuery('#ccLink', response).css("display", "none");
					jQuery('[name="cc"]', response).val(mailIds);
				}
			} else {
				jQuery('#subject', response).val("");
				descEle.val("");
				jQuery('[name="cc"]', response).val("");
				jQuery('.ccContainer', response).addClass("hide");
				jQuery('#ccLink', response).css("display", "");
			}
		});
	},

	clearPreviewContainer : function() {
		var previewHtml = '<div class="mmListMainContainer">\n\
							<center><strong>'+app.vtranslate('JSLBL_NO_MAIL_SELECTED_DESC')+'</center></strong></div>';
		this.getContainer().find('#mailPreviewContainer').html(previewHtml);
	},

	registerRefreshFolder : function() {
		var self = this;
		var container = self.getContainer();
		container.find('.mailbox_refresh').click(function() {
			var folder = container.find('.mm_folder.active').data('foldername');
			if(folder == 'vt_drafts') {
				self.openDraftFolder();
			} else {
				self.openFolder(folder);
			}
		});
	},

	registerSearchTypeChangeEvent : function() {
		var container = this.getContainer();
		container.on('change', '#searchType', function(e){
			var element = jQuery(e.currentTarget);
			var searchBox = jQuery('#mailManagerSearchbox');
			if(element.val() == 'ON'){
				searchBox.addClass('dateField');
				searchBox.attr("data-date-format", "dd-mm-yyyy");
				searchBox.attr("data-calendar-type", "range");
				searchBox.parent().append('<span class="date-addon input-group-addon"><i class="fa fa-calendar"></i></span>');
				vtUtils.registerEventForDateFields(searchBox);
			} else {
				searchBox.datepicker('remove');
				searchBox.removeClass('dateField');
				searchBox.parent().find('.date-addon').remove();
			}
			vtUtils.registerEventForDateFields(jQuery('[name="date"]'));
		});
	},

	registerPostMailSentEvent: function () {
		app.event.on('post.mail.sent', function (event, data) {
			var resultEle = jQuery(data);
			var success = resultEle.find('.mailSentSuccessfully');
			if (success.length > 0) {
				app.helper.showModal(data);
				setTimeout(function(){
				  $('.myModal').modal('hide')
				}, 2000);
			}
		});
	},
	
	registerClickLinkToEvent: function(){
		var thisInstance = this;
		var container = this.getContainer();
		
		container.on('click','.linkTo',function(e){
			
			if($(this).closest('span').parent().hasClass('singleMailActions'))
				return;
			
			var msgNos = new Array();
			container.find('.mailCheckBox').each(function(i, ele) {
				var element = jQuery(ele);
				if(element.is(":checked")) {
					msgNos.push(element.closest('.mailEntry').find('.msgNo').val());
				}
			});
			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			}
			var folder = container.find('.mm_folder.active').data('foldername');
			
			var params = {
				'module' : 'MailManager',
				'view' : 'LinkTo',
				'msgno' : msgNos,
				'folder' : folder,
			};
			app.helper.showProgress();
			app.request.post({data : params}).then(function(err,data) {
				app.helper.hideProgress();
	            if(!err) {
	                app.helper.showModal(data, {
	                    'cb' : function(modalContainer) {
	                    	thisInstance.referenceFieldChangeEvent(modalContainer.find('form'));
	                    	thisInstance.registerLinkToModalEvents(modalContainer);
	                    }
	                });
	            }
			});
			
		});
		
	},

	registerLinkToModalEvents : function(container) {
        var self = this;
        var addLinkToForm = jQuery('#addLinkTo');
        addLinkToForm.vtValidate({
            submitHandler: function(form) {
                var formData = addLinkToForm.serializeFormData();
                app.helper.showProgress();
                app.request.post({'data': formData}).then(function(e, res) {
                    
                    if (!e) {
                        if(res.message)
                        	var message = res.message;
                        if(res.success){
                        	app.helper.hideModal();
	                        app.helper.showSuccessNotification({
	                            'message': message
	                        });
	                        
	                        $(document).find('.mailbox_refresh').trigger('click');
                        }else{
                        	app.helper.showErrorNotification({
                                'message': message
                            });
                        }
                        app.helper.hideProgress();
                    }
                    if (e) {
                        app.helper.showErrorNotification({
                            'message': e
                        });
                    }
                });
            }
        });
    },
    
	referenceFieldChangeEvent : function(modalContainer){
		var vtigerInstance = Vtiger_Index_Js.getInstance();
		vtigerInstance.registerAutoCompleteFields(modalContainer);
		vtigerInstance.registerClearReferenceSelectionEvent(modalContainer);
		vtigerInstance.referenceModulePopupRegisterEvent(modalContainer);
	},

	registerEvents : function() {
		var self = this;
		self.loadFolders();
		self.registerComposeEmail();
		self.registerSettingsEdit();
		self.registerInitialLayout();
		self.registerRefreshFolder();
		self.registerSearchTypeChangeEvent();
		self.registerPostMailSentEvent();
		self.registerClickLinkToEvent();
		self.registerMailConverterButton();
		self.registerAddMailboxButon();
		self.scanMailBox();
		self.deleteMailBox();
		self.registerDeleteMailboxEvent();
		self.registerOpenMailMangerId();
	},
	
	registerEventForSingleMailActions : function(){
		
		var thisInstance = this;
		var container = thisInstance.getContainer();
			
		jQuery(document).on('click', '#mmMarkAsReadSingle', function(){
			
			var folder = $(this).data('folder');
			var msgId = $(this).data('msgno');
			var msgNos = new Array();
			msgNos.push(msgId);
				
			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showProgress(app.vtranslate("JSLBL_Updating")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'mark',
					'_folder' : folder,
					'_msgno' : msgNos.join(','),
					'_markas' : 'read'
				};
				
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_MARKED_READ')});
						thisInstance.markMessageRead(msgNos);
						thisInstance.updateUnreadCount("-"+thisInstance.getUnreadCountByMsgNos(msgNos), folder);
					}
				});
			}
		});
		
		jQuery(document).on('click', '#mmMarkAsUnreadSingle', function(){
			var folder = $(this).data('folder');
			var msgId = $(this).data('msgno');
			var msgNos = new Array();
			msgNos.push(msgId);

			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showProgress(app.vtranslate("JSLBL_Updating")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'mark',
					'_folder' : folder,
					'_msgno' : msgNos.join(','),
					'_markas' : 'unread'
				};
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_MARKED_UNREAD')});
						thisInstance.markMessageUnread(msgNos);
						thisInstance.updateUnreadCount("+"+thisInstance.getUnreadCountByMsgNos(msgNos), folder);
					}
				});
			}
		});
		
		jQuery(document).on('click', '#mmDeleteMailSingle', function(){
			var folder = $(this).data('folder');
			var msgId = String($(this).data('msgno'));
			var msgNos = new Array();
			msgNos.push(msgId);

			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showPromptBox({'message' : app.vtranslate('LBL_DELETE_CONFIRMATION')}).then(function() {
					app.helper.showProgress(app.vtranslate("JSLBL_Deleting")+"...");
					var params = {
						'module' : 'MailManager',
						'view' : 'Index',
						'_operation' : 'mail',
						'_operationarg' : 'delete',
						'_folder' : folder,
						'_msgno' : msgNos.join(',')
					};
					app.request.post({data : params}).then(function(err,data) {
						app.helper.hideProgress();
						if(data.status) {
							app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAILS_DELETED')});
							thisInstance.updateUnreadCount("-"+thisInstance.getUnreadCountByMsgNos(msgNos), folder);
							thisInstance.updatePagingCount(msgNos.length);
							for(var i = 0; i < msgNos.length; i++) {
								var nextDiv = container.find('#mmMailEntry_'+msgNos[i]).next("div");
								container.find('#mmMailEntry_'+msgNos[i]).remove();
							}
							var openedMsgNo = container.find('#mmMsgNo').val();
							if(jQuery.inArray(openedMsgNo, msgNos) !== -1) {
								nextDiv.find('.mmfolderMails').trigger('click');
								//thisInstance.clearPreviewContainer();
							}
						}
					});
				});
			}
		});
		
		jQuery(document).on('click', '.linkToSingle', function(){

			var folder = $(this).data('folder');
			var msgId = $(this).data('msgno');
			var msgNos = new Array();
			msgNos.push(msgId);

			if(msgNos.length <= 0) {
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			}
			
			var params = {
				'module' : 'MailManager',
				'view' : 'LinkTo',
				'msgno' : msgNos,
				'folder' : folder,
			};
			app.helper.showProgress();
			app.request.post({data : params}).then(function(err,data) {
				app.helper.hideProgress();
	            if(!err) {
	                app.helper.showModal(data, {
	                    'cb' : function(modalContainer) {
	                    	thisInstance.referenceFieldChangeEvent(modalContainer.find('form'));
	                    	thisInstance.registerLinkToModalEvents(modalContainer);
	                    }
	                });
	            }
			});
		});

				
		var moveFolder = container.find('#mmMoveToFolderSingle');
		
		moveFolder.on('click', 'a', function(e){
			var element = jQuery(e.currentTarget);
			var moveToFolder = element.closest('li').data('movefolder');
			var folder = element.closest('li').data('folder');
			var msgId = $(this).data('msgno');
			var msgNos = new Array();
			msgNos.push(msgId);
			
			if(msgNos.length <= 0) {
				container.find('.moveToFolderDropDown').removeClass('open');
				app.helper.showAlertBox({message:app.vtranslate('JSLBL_NO_EMAILS_SELECTED')});
				return false;
			} else {
				app.helper.showProgress(app.vtranslate("JSLBL_MOVING")+"...");
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'move',
					'_folder' : folder,
					'_moveFolder' : moveToFolder,
					'_msgno' : msgNos.join(',')
				};
				app.request.post({data : params}).then(function(err,data) {
					app.helper.hideProgress();
					if(data.status) {
						app.helper.showSuccessNotification({'message': app.vtranslate('JSLBL_MAIL_MOVED')});
						var unreadCount = self.getUnreadCountByMsgNos(msgNos);
						thisInstance.updateUnreadCount("-"+unreadCount, folder);
						thisInstance.updateUnreadCount("+"+unreadCount, moveToFolder);
						for(var i = 0; i < msgNos.length; i++) {
							container.find('#mmMailEntry_'+msgNos[i]).remove();
						}
						container.find('.moveToFolderDropDown').removeClass('open');
					}
				});
			}
		});
		
		jQuery(document).on('click', '#mmReplySingle', function(){

			var msgNo = $(this).data('msgno');
			var folder = $(this).data('folder');
			thisInstance.openReplySingleEmail(false, msgNo, folder);
		});
		
		jQuery(document).on('click', '#mmReplyAllSingle', function(){

			var msgNo = $(this).data('msgno');
			var folder = $(this).data('folder');
			thisInstance.openReplySingleEmail(true, msgNo, folder);
		});
		
		jQuery(document).on('click', '#mmForwardSingle', function(){
			var msgNo = $(this).data('msgno');
			var folder = $(this).data('folder');
			thisInstance.registerSingleForwardEvent(msgNo, folder);
		});
		
	},
	
	openReplySingleEmail : function(all, msgNo, folder) {
		var self = this;
		if (typeof(all) == 'undefined') {
			all = true;
		}
		
		var params = {
			'module' : 'MailManager',
			'action' : 'Folder',
			'mode' : 'emailContentForEmail',
			'folder' : folder,
			'msgno' : msgNo
		};
		app.helper.showProgress(app.vtranslate("JSLBL_Loading")+"...");
		app.request.post({data : params}).then(function(err, data) {
			
			var mUserName = data.userName;
			var from = data.from;
			var to = all ? data.to : '';
			var cc = all ? data.cc : '';
	
			var mailIds = '';
			if(to != null) {
				mailIds = to;
			}
			if(cc != null) {
				mailIds = mailIds ? mailIds+','+cc : cc;
			}
	
			mailIds = mailIds.replace(/\s+/g, '');
	
			var emails = mailIds.split(',');
			for(var i = 0; i < emails.length ; i++) {
				if(emails[i].indexOf(mUserName) != -1){
					emails.splice(i,1);
				}
			}
			mailIds = emails.join(',');
	
			mailIds = mailIds.replace(',,', ',');
			if(mailIds.charAt(mailIds.length-1) == ',') {
				mailIds = mailIds.slice(0, -1);
			} else if(mailIds.charAt(0) == ','){
				mailIds = mailIds.slice(1);
			}
	
			var subject = JSON.parse(data.subject);
			var body = data.body;
			var date = data.date;
	
			var replySubject = (subject.toUpperCase().indexOf('RE:') == 0) ? subject : 'Re: ' + subject;
			var replyBody = '<p></br></br></p><p style="margin:0;padding:0;">On '+date+', '+from+' wrote :</p><blockquote style="border:0;margin:0;border-left:1px solid gray;padding:0 0 0 2px;">'+body+'</blockquote><br />';
			
			var params = {
				'step' : "step1",
				'module' : "MailManager",
				'view' : "MassActionAjax",
				'mode' : "showComposeEmailForm",
				'linktomodule' : 'true', 
				'excluded_ids' : "[]",
				'to' : '["'+from+'"]'
			}
			params['selected_ids'] = "[]";

			if(mailIds) {
				self.openComposeEmailForm("replyall", params, {'subject' : replySubject, 'body' : replyBody, 'ids' : mailIds});
			} else {
				self.openComposeEmailForm("reply", params, {'subject' : replySubject, 'body' : replyBody});
			}
			
		});
	},
	
	
	registerSingleForwardEvent : function(msgNo, folderName) {
		var self = this;
		var params = {
			'module' : 'MailManager',
			'action' : 'Folder',
			'mode' : 'emailContentForEmail',
			'folder' : folderName,
			'msgno' : msgNo
		};
		app.helper.showProgress(app.vtranslate("JSLBL_Loading")+"...");
		app.request.post({data : params}).then(function(err, data) {
			var msgNo = data.msgno;
			var from = data.from;
			var to = data.to;
			var cc = data.cc ? data.cc : '';
			var subject = JSON.parse(data.subject);
			var body = data.body;
			var date = data.date;
			var folder = data.folder;

			var fwdMsgMetaInfo = app.vtranslate('JSLBL_FROM') + from + '<br/>'+
					app.vtranslate('JSLBL_DATE') + date + '<br/>'+
					app.vtranslate('JSLBL_SUBJECT') + subject;
			if (to != '' && to != null) {
				fwdMsgMetaInfo += '<br/>'+app.vtranslate('JSLBL_TO') + to;
			}
			if (cc != '' && cc != null) {
				fwdMsgMetaInfo += '<br/>'+app.vtranslate('JSLBL_CC') + cc;
			}
			fwdMsgMetaInfo += '<br/>';

			var fwdSubject = (subject.toUpperCase().indexOf('FWD:') == 0) ? subject : 'Fwd: ' + subject;
			var fwdBody = '<p></p><p>'+app.vtranslate('JSLBL_FORWARD_MESSAGE_TEXT')+'<br/>'+fwdMsgMetaInfo+'</p>'+body;
			var attchmentCount = parseInt(data.att_count);
			if(attchmentCount) {
				var params = {
					'module' : 'MailManager',
					'view' : 'Index',
					'_operation' : 'mail',
					'_operationarg' : 'forward',
					'messageid' : encodeURIComponent(msgNo),
					'folder' : encodeURIComponent(folder),
					'subject' : encodeURIComponent(fwdSubject),
					'body' : encodeURIComponent(fwdBody)
				};
				app.request.post({'data' : params}).then(function(err, data) {
					var draftId = data.emailid;
					var newParams = {
						'module' : 'Emails',
						'view' : 'ComposeEmail',
						'mode' : 'emailEdit',
						'record' : draftId
					};
					app.request.post({data : newParams}).then(function(err,data) {
						app.helper.hideProgress();
						if(err === null) {
							var dataObj = jQuery(data);
							var descriptionContent = dataObj.find('#iframeDescription').val();
							app.helper.showModal(data, {cb : function() {
								var editInstance = new Emails_MassEdit_Js();
								editInstance.registerEvents();
								jQuery('#emailPreviewIframe').contents().find('html').html(descriptionContent);
								jQuery("#emailPreviewIframe").height(jQuery('#emailPreviewIframe').contents().find('html').height());
							}});
						}
					});
				});
			} else {
				app.helper.hideProgress();
				var params = {
					'step' : "step1",
					'module' : "MailManager",
					'view' : "MassActionAjax",
					'mode' : "showComposeEmailForm",
					'selected_ids' : "[]",
					'excluded_ids' : "[]",
				}
				self.openComposeEmailForm("forward", params, {'subject' : fwdSubject, 'body' : fwdBody});
			}
		});
	},
	
	registerAutoCompleteSearchFields : function() {
		var thisInstance = this;
		jQuery(document).find('input#mailManagerSearchbox').autocomplete({
			'minLength' : '3',
			'source' : function(request, response){
				//element will be array of dom elements
				//here this refers to auto complete instance
				var inputElement = jQuery(this.element[0]);
				var searchValue = request.term;
				var params = {};
				params.module = app.getModuleName();
				params.search_value = searchValue;
				params.action = 'BasicAjax';
				app.request.get({data:params}).then(function(err, res){
					var reponseDataList = new Array();
					var serverDataFormat = res;
					if(serverDataFormat.length <= 0) {
							//jQuery(inputElement).val('');
							serverDataFormat = new Array({
									'label' : 'No Results Found',
									'type'	: 'no results'
							});
					}
					for(var id in serverDataFormat){
							var responseData = serverDataFormat[id];
							reponseDataList.push(responseData);
					}
					response(reponseDataList);
				});
				
			},
			'select' : function(event, ui ){
				var selectedItemData = ui.item;
				//To stop selection if no results is selected
				if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
						return false;
				}
				
				selectedItemData.selectedName = selectedItemData.value;
			}
		});
	},
	
	
	registerMailConverterButton : function(){
		var thisInstance = this;
		jQuery(document).on('click', '.addMailConverter', function(){
			var scanner = $(this).data('scannerid');
			var params = {};
			params['module'] = app.getModuleName();
			params['view'] = 'MailBoxEdit';
			params['mode'] = 'mailBoxList';
			params['record'] = scanner;
			
			app.request.post({data : params}).then(function(err,data) {
				var overlayParams = {'backdrop' : 'static', 'keyboard' : false};
				app.helper.loadPageContentOverlay(data, overlayParams).then(function(container) {


				});
			});
		});
		
	},
	
	registerAddMailboxButon : function(){
		var thisInstance = this;
		jQuery(document).on('click', '.addMailBox', function(){
			
			var params = {};
			params['module'] = app.getModuleName();
			params['view'] = 'MailBoxEdit';
			params['mode'] = 'step1';
			
			if($(this).data('scannerid')){
				params['create'] = 'existing';
				params['record'] = $(this).data('scannerid');
			}else{
				params['create'] = 'new';
			}
			app.helper.showProgress();
			app.request.post({data : params}).then(function(err,data) {
				app.helper.showModal(data, {cb : function() {
					thisInstance.firstStep();
					thisInstance.handleSettingsMailBoxEvents();
					thisInstance.registerExistingMailBox();
					thisInstance.activateHeader();
					app.helper.hideProgress();
					
				}});
			});
			
		});
		
	},
	
	firstStep: function (e) {
		var thisInstance = this;
		var form = jQuery('#mailBoxEditView');
		
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				form.find('[name="saveButton"]').attr('disabled', 'disabled');
				thisInstance.saveMailBox(form);
			}
		}
		form.vtValidate(params);

		form.submit(function (e) {
			e.preventDefault();
		});
	},

	saveMailBox: function (form) {
		var thisInstance = this;
		
		var params = {
            submitHandler : function(form) {
               app.helper.showProgress();
                var form = jQuery(form);
                var params = form.serializeFormData();
        		
    			
        		params.scannername = jQuery('input[name="scannername"]').val();
        		params.module = 'MailConverter';
        		params.parent = 'Settings';
        		params.action = 'SaveMailBox';

        		app.helper.showProgress();
        		app.request.post({'data': params}).then(function (err, data) {
        			
        			if (typeof data != 'undefined') {
        				var create = jQuery("#create").val();
        				
        				var params = {};
        				params['module'] = app.getModuleName();
        				params['view'] = 'MailBoxEdit';
        				params['mode'] = 'step2';
        				params['create'] = create;
        				params['record'] = data.id;
        				
        				app.request.post({data : params}).then(function(err,data) {
        					jQuery('#step').val('step2');
        					jQuery('#recordId').val(params['record']);
        					jQuery('.addMailBoxStep').replaceWith(data);
        					
        					thisInstance.secondStep();
        					if(create != 'new'){
        						jQuery('.nextStep').text('Finish');
        					}
        					form.append("<input type='hidden' name='mailbox_name' value='"+scannerName+"' />");
        					thisInstance.activateHeader();
        					app.helper.hideProgress();
        				});
        				
        			} else {
        				app.helper.hideProgress();
        				app.helper.showErrorNotification({'message': err['message']});
        			}
        		});
            }
		};
		validateAndSubmitForm(form,params);
		
	},

	secondStep: function (e) {
		var thisInstance = this;
		var form = jQuery('#mailBoxEditView');
		vtUtils.applyFieldElementsView(form);
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				var selectedFolders = form.find('[name="folders[]"]').val();

				if (selectedFolders.length < 1) {
					app.helper.showAlertNotification({'message': app.vtranslate('You must select atleast one folder.')});
					return false;
				} else {
					form.find('[name="saveButton"]').attr('disabled', 'disabled');
					thisInstance.saveFolders(selectedFolders);
				}
				return false;
			}
		}
		form.vtValidate(params);

		form.submit(function (e) {
			e.preventDefault();
		});
	},

	saveFolders: function (selectedFolders) {
		var thisInstance = this;
		var create = jQuery('#create').val();
		var id = jQuery('#recordId').val();
		
		var url = 'module=MailConverter&parent=Settings&action=SaveFolders&folders='+selectedFolders+'&create='+create+'&record='+id;
		var scannerName = jQuery('[name="mailbox_name"]').val();
		
		app.helper.showProgress();
		app.request.post({'url': url}).then(function (err, data) {
			app.helper.hideProgress();
			
			if (typeof data != 'undefined') {
				if (create == 'new') {
					var params = {};
					params['module'] = app.getModuleName();
					params['view'] = 'MailBoxEdit';
					params['mode'] = 'step3';
					params['create'] = create;
					params['record'] = data.id;
					
					app.request.post({data : params}).then(function(err,data) {
						jQuery('#step').val('step3');
						jQuery('.fieldBlockContainer').replaceWith(data);
						thisInstance.thirdStep();
						jQuery(document).find('#ruleSave').append("<input type='hidden' name='mailbox_name' value='"+scannerName+"' />");
						thisInstance.activateHeader();
						app.helper.hideProgress();
					});
				}else{
					app.helper.hideProgress();
					app.helper.hideModal();
				}
			} else {
				app.helper.hideProgress();
				app.helper.showErrorNotification({'message': err['message']});
			}
		});
	},

	thirdStep: function (e) {
		var thisInstance = this;
		var form = jQuery('#ruleSave');
		vtUtils.applyFieldElementsView(form);
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				form.find('[name="saveButton"]').attr('disabled', 'disabled');
				thisInstance.saveRule(form);
			}
		}
		form.vtValidate(params);

		form.submit(function (e) {
			e.preventDefault();
		});
	},

	saveRule: function (form) {
		app.helper.showProgress();
		//var params = form.serializeFormData();
		var params = form.serialize();
		params.record = '';
		app.request.post({'data': params}).then(function (err, data) {
			app.helper.hideProgress();
			
			if (typeof data != 'undefined') {
				app.helper.hideProgress();
				app.helper.hideModal();
				var scannername = form.find('[name="mailbox_name"]').val();
				var scannerid = data.scannerId;
				var html ='<li>'+
					'<a href="#" data-scannerid="'+scannerid+'"> '+scannername+''+
						'<i class="fa fa-trash pull-right deleteMailBox" title="Delete MailBox" data-scannerid="'+scannerid+'"></i>&nbsp;'+
						'<i class="fa fa-plus pull-right addMailConverter" title="Add Rules MailBox" data-scannerid="'+scannerid+'"></i>&nbsp;'+
						//'<i class="fa fa-folder pull-right addMailBox selectFolder" title="Select Folders" data-scannerid="'+scannerid+'"></i>&nbsp;'+
						'<i class="fa fa-pencil pull-right addMailBox" title="Edit MailBox" data-scannerid="'+scannerid+'"></i>&nbsp;'+
						'<i class="fa fa-refresh pull-right scanMailBox" title="Scan MailBox" data-scannerid="'+scannerid+'"></i>&nbsp;'+
					'</a>'+
				'</li>';
				jQuery(document).find('.mailBoxDropDown').append(html);
			} else {
				app.helper.hideProgress();
				app.helper.showErrorNotification({'message': err['message']});
			}
		});
	},

	/*
	 * Function to activate the header based on the class
	 * @params class name
	 */
	activateHeader: function () {
		jQuery('.step.active').removeClass('active');
		var step = jQuery('#step').val();
		jQuery('#'+step).addClass('active');
	},
	

	scanMailBox: function (url) {
		var thisInstance = this;
		
		jQuery(document).on('click', '.scanMailBox', function(){
			var params = {};
			params['module'] = 'MailConverter';
			params['parent'] = 'Settings';
			params['view'] = 'ScanNowAjax';
			params['scannerid'] = $(this).data('scannerid');
			app.helper.showProgress();
			app.request.post({'data': params}).then(function (err, data) {
				app.helper.showModal(data, {cb : function() {
					app.helper.hideProgress();
				}});
			});
		});
		
		/*jQuery(document).on('click', '.scanMailBox', function(){
			var scannerId = $(this).data('scannerid');
			var url = "index.php?module=MailConverter&parent=Settings&record="+scannerId+"&action=ScanNow";
			app.helper.showProgress();
			app.request.post({'url': url}).then(function (err, data) {
				app.helper.hideProgress();
				if (typeof data != 'undefined') {
					app.helper.showSuccessNotification({'message': data.message});
				} else {
					app.helper.showErrorNotification({'message': err['message']});
				}
			});
		});*/
	},

	deleteMailBox: function (url) {
		var thisInstance = this;
		jQuery(document).on('click', '.deleteMailBox', function(e){
			
			var scannerId = $(this).data('scannerid');
			var element = jQuery(e.currentTarget).parent().parent();
			
			var url = "index.php?module=MailConverter&parent=Settings&record="+scannerId+"&action=DeleteMailBox";
			app.helper.showConfirmationBox({'message': app.vtranslate('LBL_DELETE_CONFIRMATION')}).then(function () {
				app.helper.showProgress();
				app.request.post({'url': url}).then(function (err, data) {
					element.remove();
					app.helper.showSuccessNotification({'message': app.vtranslate('MailBox deleted Successfully')});
					app.helper.hideProgress();
				});
			});
			
		});
	},

	registerOpenMailMangerId: function(){
		var thisInstance = this;
		jQuery(document).on('click', '.openMailId', function(e){
			var target = jQuery(e.target); 
			if(target.hasClass('mailbox_setting'))return;
			if(target.hasClass('deleteMailManager'))return;
			
			var accountId = $(this).data('boxid');
			thisInstance.loadFolders('',accountId);
			
		});
		
	},
	
	handleSettingsMailBoxEvents : function() {
		var settingContainer = jQuery(document);
		settingContainer.on('change', '#serverMailType', function(e) {
			var element = jQuery(e.currentTarget);
			var serverType = element.val();
			var useServer = '', useProtocol = '', useSSLType = '', useCert = '';
			if(serverType == 'gmail' || serverType == 'yahoo' || serverType == 'office365') {
				useServer = 'imap.gmail.com';
				if(serverType == 'yahoo') {
					useServer = 'imap.mail.yahoo.com';
				}
				if(serverType == 'office365'){
					useServer = 'outlook.office365.com';
				}
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').addClass('hide');
			} else if(serverType == 'fastmail') {
				useServer = 'mail.messagingengine.com';
				useProtocol = 'IMAP2';
				useSSLType = 'tls';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').addClass('hide');
			} else if(serverType == 'other') {
				useServer = '';
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').removeClass('hide');
			}else if(serverType == 'omniExchange') {
				useServer = 'mail.omnisrv.com';
				useProtocol = 'IMAP4';
				useSSLType = 'ssl';
				useCert = 'novalidate-cert';
				settingContainer.find('.settings_details').removeClass('hide');
				settingContainer.find('.additional_settings').addClass('hide');
				settingContainer.find('.smtpPort').hide();
			}  else {
				settingContainer.find('.settings_details').addClass('hide');
			}

			settingContainer.find('[name="username"]').val('');
			settingContainer.find('[name="password"]').val('');
			
			if(useProtocol != '') {
				settingContainer.find('[name="server"]').val(useServer);
				settingContainer.find('[name="protocol"]').each(function(i, node) {
					if(jQuery(node).val() == useProtocol) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('[name="ssltype"]').each(function(i, node) {
					if(jQuery(node).val() == useSSLType) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('[name="sslmethod"]').each(function(i, node) {
					if(jQuery(node).val() == useCert) {
						jQuery(node).attr('checked', true);
					}
				});
			}
		});
	},

	registerExistingMailBox : function(){
		
		var settingContainer = jQuery(document);
		settingContainer.on('change', '#selectExistingMailBox', function(){
			
			var params = {};
			params['module'] = app.getModuleName();
			params['view'] = 'MailBoxEdit';
			params['mode'] = 'getMailBoxData';
			params['accountid'] = $(this).val();
			
			app.request.post({data : params}).then(function(err,data) {
				
				var server     = data.server;
				var serverName = data.serverName;
				var userName   = data.userName;
				var password   = data.password;
				var protocol   = data.protocol;
				var sslType    = data.sslType;
				var sslMethod  = data.sslMethod;
				
				settingContainer.find('#serverMailType').val(server).trigger('change');
				settingContainer.find('[name="server"]').val(serverName);
				settingContainer.find('[name="username"]').val(userName);
				settingContainer.find('[name="password"]').val(password);
				settingContainer.find('[name="protocol"]').each(function(i, node) {
					if(jQuery(node).val() == protocol) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('[name="ssltype"]').each(function(i, node) {
					if(jQuery(node).val() == sslType) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('[name="sslmethod"]').each(function(i, node) {
					if(jQuery(node).val() == sslMethod) {
						jQuery(node).attr('checked', true);
					}
				});
				
			});
			
		});
		
	},
	

	registerPortChangeEvent : function(data){
		var settingContainer = jQuery(data);
		settingContainer.find('#smtpPort').on('change', function(e) {
			var element = jQuery(e.currentTarget);
			var portType = element.val();
			var value = element.closest('td').find('#_mbox_smtp_server').val().replace('ssl://','').replace('tls://','');
			value = value.replace(':465','').replace(':587','');
			if(portType == 'plain'){
				element.closest('td').find('#_mbox_smtp_server').val(value);
			}else if(portType == 'tls'){
				element.closest('td').find('#_mbox_smtp_server').val('tls://'+value+':587');
			}else if(portType == 'ssl'){
				element.closest('td').find('#_mbox_smtp_server').val('ssl://'+value+':465');
			}
		});
	},
	
});
