/* ********************************************************************************
 * The content of this file is subject to the Multiple SMTP ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("MultipleSMTP_Js",{
    instance:false,
    getInstance: function(){
        if(MultipleSMTP_Js.instance == false){
            var instance = new MultipleSMTP_Js();
            MultipleSMTP_Js.instance = instance;
            return instance;
        }
        return MultipleSMTP_Js.instance;
    }
},{
    registerEventForOutgoingButton: function () {
        var thisInstance=this;
        jQuery('.btnOutgoingServer').on('click', function (e) {
            var url = jQuery(this).data('url');
            thisInstance.showListView(url);
        });
    },

    registerEventForEditButtons: function() {
        var thisInstance=this;
        jQuery('body').on('click','.edit_info',function(){
            var url = jQuery(this).data('url');
            thisInstance.showEditView(url);
        })
    },

    registerEventForDeleteRecord: function(){
        var thisInstance=this;
        jQuery('body').on('click','.delete_server',function(){
            var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
            var url = jQuery(this).data('url');
            var userid = jQuery(this).data('userid');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(){
                    thisInstance.deleteServer(url,userid);
                }
            );
        })
    },

    showEditView: function (url) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator();
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"html",
            "data" : {}
        };
        AppConnector.request(actionParams).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data) {
                    var callBackFunction = function(data) {
                        var form = jQuery('#outgoingMassEditContainer');
                        var params = app.validationEngineOptions;
                        params.onValidationComplete = function(form, valid){
                            if(valid) {
                                thisInstance.saveOutgoingServer(form);
                                return valid;
                            }
                        };
                        form.validationEngine(params);

                        form.submit(function(e) {
                            e.preventDefault();
                        })
                    };
                    app.showModalWindow(data, function(data){
                        if(typeof callBackFunction == 'function'){
                            callBackFunction(data);
                        }
                    }, {'width':'500px'})
                }
            }
        );
    },

    // haph86@gmail.com - #41367
    showListView: function (url) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator();
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"html",
            "data" : {}
        };
        AppConnector.request(actionParams).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data) {
                    app.showModalWindow(data, function(data){
                        if(typeof callBackFunction == 'function'){
                            callBackFunction(data);
                        }
                    }, {'width':'700px'});
                    thisInstance.loadListUserServer();
                }
            }
        );
    },

    // haph86@gmail.com - #41367
    deleteServer: function (url,userid) {
        var thisInstance = this;
        var progressIndicatorElement = jQuery.progressIndicator();
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"html",
            "data" : {}
        };
        AppConnector.request(actionParams).then(
            function(data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                if(data) {
                    var new_url = 'index.php?module=MultipleSMTP&view=MassActionAjax&mode=showListview&userid='+userid;
                    thisInstance.showListView(new_url);
                }
            }
        );
    },

    saveOutgoingServer : function(form) {
        var thisInstance = this;

        var progressIndicatorElement = jQuery.progressIndicator({
            'position' : 'html',
            'blockInfo' : {
                'enabled' : true
            }
        });

        var data = form.serializeFormData();
        data['module'] = 'MultipleSMTP';
        data['action'] = 'SaveAjax';
        AppConnector.request(data).then(
            function(data) {
                if(data['success']) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    app.hideModalWindow();
                    var params = {};
                    params.text = app.vtranslate('Outgoing Server Saved');
                    params.animation = "show";
                    params.type = 'info';
                    params.title = app.vtranslate('JS_MESSAGE');
                    Vtiger_Helper_Js.showPnotify(params);
                }else{
                    progressIndicatorElement.progressIndicator({'mode':'hide'});
                    app.hideModalWindow();
                    var params = {};
                    params.text = app.vtranslate("Can't save data. Error occurred while sending mail");
                    params.animation = "show";
                    params.type = 'error';
                    params.title = app.vtranslate('JS_MESSAGE');
                    Vtiger_Helper_Js.showPnotify(params);
                    jQuery('.errorMessage', form).removeClass('hide');
                }
            },
            function(error) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                //TODO : Handle error
            }
        );
    },
    loadListUserServer: function () {
        var thisInstance = this;
        var blocks = jQuery('.blocksSortable');
        blocks.sortable({
            'revert': true,
            'connectWith': ".blocksSortable",
            'tolerance': 'pointer',
            'cursor': 'move',
            'placeholder': "state-highlight",
            'stop': function (event, ui) {
                thisInstance.updateSequence();
            }
        });
    },
    updateSequence: function () {
        var thisInstance = this;
        var params = {};
        $(".blockSortable").each(function (index) {
            //alert(index);
            params[$(this).data('id')] = {'index': index, 'column': $(this).closest('.blocksSortable').data('column')};
        });
        var progress = $.progressIndicator({
            'message': app.vtranslate('Saving changes'),
            'blockInfo': {
                'enabled': true
            }
        });
        thisInstance.registerSaveEvent('updateSequence', {
            'data': params
        });
        progress.progressIndicator({'mode': 'hide'});
    },
    registerSaveEvent: function (mode, data) {
        var resp = '';
        var params = {};
        params.data = {
            module: 'MultipleSMTP',
            action: 'ActionAjax',
            mode: mode,
            params: data
        };
        if (mode == 'saveMultipleSMTP') {
            params.async = false;
        } else {
            params.async = true;
        }
        params.dataType = 'json';
        AppConnector.request(params).then(
            function (data) {
                var response = data['result'];
                var params = {
                    text: response['message'],
                    animation: 'show',
                    type: 'success'
                };

                Vtiger_Helper_Js.showPnotify(params);
                resp = response['success'];

            },
            function (data, err) {
            }
        );
    },
    registerEvents: function(){
        this.registerEventForOutgoingButton();
        this.registerEventForEditButtons();
        this.registerEventForDeleteRecord();
    }
});

jQuery(document).ready(function () {
    // Check enable
    var params = {};
    params.action = 'ActionAjax';
    params.module = 'MultipleSMTP';
    params.mode = 'checkEnable';
    AppConnector.request(params).then(
        function(data) {
            if (data.result.enable == '1') {

                var sPageURL = window.location.search.substring(1);
                var targetModule = '';
                var targetView = '';
                var targetRecord = '';
                var sURLVariables = sPageURL.split('&');
                for (var i = 0; i < sURLVariables.length; i++) {
                    var sParameterName = sURLVariables[i].split('=');
                    if (sParameterName[0] == 'module') {
                        targetModule = sParameterName[1];
                    }
                    else if (sParameterName[0] == 'view') {
                        targetView = sParameterName[1];
                    }
                    else if (sParameterName[0] == 'record') {
                        targetRecord = sParameterName[1];
                    }
                }
                if (targetModule == 'Users' && (targetView == 'Detail' || targetView == 'PreferenceDetail')) {
                    var buttonContainer = jQuery('.detailViewButtoncontainer');
                    var btnToolBar = buttonContainer.find('.btn-toolbar');
                    //var outGoingServerBtn = jQuery('<div class="btn-group"><button data-url="index.php?module=MultipleSMTP&view=MassActionAjax&mode=showMassEditForm&userid=' + targetRecord + '" type="button" class="btn btnOutgoingServer"><strong>Outgoing Server</strong></button></div>');
                    var outGoingServerBtn = jQuery('<div class="btn-group"><button data-url="index.php?module=MultipleSMTP&view=MassActionAjax&mode=showListview&userid=' + targetRecord + '" type="button" class="btn btnOutgoingServer"><strong>Outgoing Server</strong></button></div>'); // haph86@gmail.com - #41367
                    btnToolBar.find('.btn-group:last').before(outGoingServerBtn);
                    var instance = new MultipleSMTP_Js();
                    instance.registerEvents();
                } else if (targetView == 'ComposeEmail' || targetView == 'SendEmail') {
                    var form=jQuery('#massEmailForm');
                    form.find('[name="module"]').val('MultipleSMTP');

                    // haph86@gmail.com
                    var progressIndicatorElement = jQuery.progressIndicator({
                        'position' : 'html',
                        'blockInfo' : {
                            'enabled' : true
                        }
                    });
                    var actionParams = {
                        "view":"MassActionAjax",
                        "mode":"showTOField",
                        "module":"MultipleSMTP",
                        "parent_id":jQuery('[name="parent_id"]').val()
                    };
                    AppConnector.request(actionParams).then(
                        function(data) {
                            if(data) {
                                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                if(targetModule == 'EMAILMaker'){
                                    form.find('[name="toEmail"]').closest("div").before(data);
                                }
                                else {
                                    form.find('.toEmailField').before(data);
                                    jQuery('.from_field').chosen();
                                }
                            }
                        },
                        function(error,err){

                        }
                    );
                }

                jQuery(document).ajaxComplete( function (event, request, settings) {
                    var url = settings.data;
                    if (url && url.constructor.name=='String' && url.indexOf('module=QuotingTool') > -1 && url.indexOf('view=EmailPreviewTemplate') > -1){
                        var module = "Emails";
                        var recordId = jQuery('#recordId').val();
                        var focusModule= 'QuotingTool';
                        var actionParams = {
                            "view":"MassActionAjax",
                            "mode":"showTOField",
                            "module":"MultipleSMTP",
                            'module_focus': focusModule
                        };
                        AppConnector.request(actionParams).then(
                            function(data) {
                                if(data) {
                                    var form = jQuery('#quotingtool_emailtemplate');
                                    var from_serveremailid = form.find("select[name='from_serveremailid']");
                                    if(from_serveremailid.length <= 0) {
                                        form.find("[name='multip_SMTP']").html(data);
                                        // $('[name="from_serveremailid"]').select2();
                                        // $('[name="from_serveremailid"]').trigger('liszt:updated');
                                    }
                                }
                            },
                            function(error,err){

                            }
                        );
                    }
                })

            }
        }
    );
    if(typeof Emails_MassEdit_Js !== 'undefined') {
        Emails_MassEdit_Js.prototype.registerEventsForToField = function () {
            var thisInstance = this;
            this.getMassEmailForm().on('click','.selectEmail',function(e){
                var moduleSelected = jQuery('.emailModulesList').val();
                var parentElem = jQuery(e.target).closest('.toEmailField');
                var sourceModule = jQuery('[name=module]').val();
                var params = {
                    'module' : moduleSelected,
                    'src_module' : 'Emails',
                    'view': 'EmailsRelatedModulePopup'
                };
                var popupInstance =Vtiger_Popup_Js.getInstance();
                popupInstance.show(params, function(data){
                    var responseData = JSON.parse(data);
                    for(var id in responseData){
                        var data = {
                            'name' : responseData[id].name,
                            'id' : id,
                            'emailid' : responseData[id].email
                        };
                        thisInstance.setReferenceFieldValue(parentElem, data);
                        thisInstance.addToEmailAddressData(data);
                        thisInstance.appendToSelectedIds(id);
                        thisInstance.addToEmails(data);
                    }
                },'relatedEmailModules');
            });

            this.getMassEmailForm().on('click','[name="clearToEmailField"]',function(e){
                var element = jQuery(e.currentTarget);
                element.closest('div.toEmailField').find('.sourceField').val('');
                thisInstance.getMassEmailForm().find('[name="toemailinfo"]').val(JSON.stringify([]));
                thisInstance.getMassEmailForm().find('[name="selected_ids"]').val(JSON.stringify([]));
                thisInstance.getMassEmailForm().find('[name="to"]').val(JSON.stringify([]));

                var preloadData = [];
                thisInstance.setPreloadData(preloadData);
                thisInstance.getMassEmailForm().find('#emailField').select2('data', preloadData);
            });
            thisInstance.registerAutoCompleteFields(thisInstance.getMassEmailForm());
        };
    }
    if(typeof Settings_Workflows_Edit_Js !== 'undefined') {
        Settings_Workflows_Edit_Js.prototype.registerMultipregisterMultipleSMTPEmailTaskEventsleSMTPEmailTaskEvents = function () {
            var textAreaElement = jQuery('#content');
            var ckEditorInstance = this.getckEditorInstance();
            ckEditorInstance.loadCkEditor(textAreaElement);
            this.registerFillMailContentEvent();
            this.registerFillTaskFromEmailFieldEvent();
            this.registerCcAndBccEvents();

            jQuery('#saveTask').on('submit',function(){
                var textAreaElement = jQuery('#content');
                textAreaElement.val(CKEDITOR.instances['content'].getData());
            });
        };
    }
	
	if(typeof Vtiger_Helper_Js !== 'undefined') { // ha@vtexperts.com - #41367 - 07282016 - http://vtedev.com/Screenshots2/2016-07-24_05-33-38.mp4
        Vtiger_Helper_Js.getInternalMailer = function(selectedId,fieldname,fieldmodule) {
            var module = 'MultipleSMTP';
            var cacheResponse = Vtiger_Helper_Js.checkServerConfigResponseCache;
            var  checkServerConfigPostOperations = function (data) {
                if(data == true){
                    Vtiger_Helper_Js.requestToShowComposeEmailForm(selectedId,fieldname,fieldmodule);
                } else {
                    alert(app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION'));
                }
            };
            if(cacheResponse === ''){
                var checkServerConfig = Vtiger_Helper_Js.checkServerConfig(module);
                checkServerConfig.then(function(data){
                    Vtiger_Helper_Js.checkServerConfigResponseCache = data;
                    checkServerConfigPostOperations(Vtiger_Helper_Js.checkServerConfigResponseCache);
                });
            } else {
                checkServerConfigPostOperations(Vtiger_Helper_Js.checkServerConfigResponseCache);
            }
        };
    }
    
    if (typeof Vtiger_Helper_Js !== 'undefined'){
        Vtiger_Helper_Js.checkServerConfig = function(module){
            var aDeferred = jQuery.Deferred();
            var actionParams = {
                "action": 'CheckServerInfo',
                'module' : 'MultipleSMTP'
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    var state = false;
                    if(data.result){
                        state = true;
                    } else {
                        state = false;
                    }
                    aDeferred.resolve(state);
                }
            );
            return aDeferred.promise();
        };
    }
});