/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Settings_Vtiger_Index_Js('Settings_MailConverter_Edit_Js', {
	firstStep: function (e) {
		var form = jQuery('#mailBoxEditView');
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				form.find('[name="saveButton"]').attr('disabled', 'disabled');
				Settings_MailConverter_Edit_Js.saveMailBox(form);
			}
		}
		form.vtValidate(params);

		form.submit(function (e) {
			e.preventDefault();
		});
	},

	saveMailBox: function (form) {
		var params = form.serializeFormData();
		
		var errParams = {
				position: {
				'my' : 'bottom left',
				'at' : 'top left',
				'container' : jQuery('#mailBoxEditView')
		}};
		var errorMsg = app.vtranslate('JS_REQUIRED_FIELD');
		if(form.find('#serverMailType').val() == "") {
			vtUtils.showValidationMessage(form.find('#serverMailType'), errorMsg, errParams);
			return false;
		} else {
			vtUtils.hideValidationMessage(form.find('#serverMailType'));
		}

		params.scannername = jQuery('input[name="scannername"]').val();
		params.module = app.getModuleName();
		params.parent = app.getParentModuleName();
		params.action = 'SaveMailBox';

		app.helper.showProgress();
		app.request.post({'data': params}).then(function (err, data) {
			app.helper.hideProgress();
			if (typeof data != 'undefined') {
				var create = jQuery("#create").val();
				window.location.href = 'index.php?module='+app.getModuleName()+'&parent='+app.getParentModuleName()+'&view=Edit&mode=step2&create='+create+'&record='+data.id;
			} else {
				app.helper.showErrorNotification({'message': err['message']});
			}
		});
	},

	secondStep: function (e) {
		var form = jQuery('#mailBoxEditView');
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				var checked = jQuery('input[type=checkbox][name=folders]:checked').length;
				if (checked < 1) {
					app.helper.showAlertNotification({'message': app.vtranslate('JS_SELECT_ONE_FOLDER')});
					return false;
				} else {
					form.find('[name="saveButton"]').attr('disabled', 'disabled');
					var selectedFolders = jQuery('input[name=folders]:checked').map(function () {
						return jQuery(this).val();
					}).get();
					Settings_MailConverter_Edit_Js.saveFolders(selectedFolders);
				}
			}
		}
		form.vtValidate(params);

		form.submit(function (e) {
			e.preventDefault();
		});
	},

	saveFolders: function (selectedFolders) {
		var create = jQuery('#create').val();
		var id = jQuery('#recordId').val();
		var url = 'module='+app.getModuleName()+'&parent='+app.getParentModuleName()+'&action=SaveFolders&folders='+selectedFolders+'&create='+create+'&record='+id;

		app.helper.showProgress();
		app.request.post({'url': url}).then(function (err, data) {
			app.helper.hideProgress();
			if (typeof data != 'undefined') {
				var fallbackUrl = 'index.php?module='+app.getModuleName()+'&parent='+app.getParentModuleName()+'&view=List&record='+data.id;
				if (create == 'new') {
					fallbackUrl = 'index.php?module='+app.getModuleName()+'&parent='+app.getParentModuleName()+'&view=Edit&mode=step3&create='+create+'&record='+data.id;
				}
				window.location.href = fallbackUrl;
			} else {
				app.helper.showErrorNotification({'message': err['message']});
			}
		});
	},

	thirdStep: function (e) {
		var form = jQuery('#ruleSave');
		var params = {
			submitHandler: function (form) {
				var form = jQuery(form);
				form.find('[name="saveButton"]').attr('disabled', 'disabled');
				Settings_MailConverter_Edit_Js.saveRule(form);
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
				window.location.href = 'index.php?module='+app.getModuleName()+'&parent='+app.getParentModuleName()+'&view=List&record='+data.scannerId;
			} else {
				app.helper.showErrorNotification({'message': err['message']});
			}
		});
	},

	/*
	 * Function to activate the header based on the class
	 * @params class name
	 */
	activateHeader: function () {
		var step = jQuery('#step').val();
		jQuery('#'+step).addClass('active');
	}

},{
	
	handleSettingsMailBoxEvents : function() {
		var settingContainer = jQuery(document);
		
		settingContainer.on('change', '#serverMailType', function(e) {
			var element = jQuery(e.currentTarget);
			var serverType = element.val();
			var useServer = '', useProtocol = '', useSSLType = '', useCert = '';
			if(serverType == 'gmail' || serverType == 'yahoo') {
				useServer = 'imap.gmail.com';
				if(serverType == 'yahoo') {
					useServer = 'imap.mail.yahoo.com';
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
			} else {
				settingContainer.find('.settings_details').addClass('hide');
			}

			settingContainer.find('[name="username"]').val('');
			settingContainer.find('[name="password"]').val('');
			
			if(useProtocol != '') {
				settingContainer.find('[name="server"]').val(useServer);
				settingContainer.find('[name="protocol"]').each(function(node) {
					if(jQuery(node).val() == useProtocol) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('[name="ssltype"]').each(function(node) {
					if(jQuery(node).val() == useSSLType) {
						jQuery(node).attr('checked', true);
					}
				});
				settingContainer.find('[name="sslmethod"]').each(function(node) {
					if(jQuery(node).val() == useCert) {
						jQuery(node).attr('checked', true);
					}
				});
			}
		});
	},
	
	registerEvents: function () {
		this._super();
		Settings_MailConverter_Edit_Js.firstStep();
		Settings_MailConverter_Edit_Js.activateHeader();
		this.handleSettingsMailBoxEvents();
	}
});