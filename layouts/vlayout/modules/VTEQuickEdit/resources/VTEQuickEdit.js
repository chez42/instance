/* ********************************************************************************
 * The content of this file is subject to the VTEQuickEdit ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("VTEQuickEdit_Js",{

},{
    recordId: null,
    moduleEditName: null,
    controlElementTd: null,
    dataEdit: null,
    VTEQuickEditCallBacks: [],
    registerVTEQuickEditEvent: function(){
        var thisInstance=this;
        jQuery('span.createReferenceRecord').each(function(){
            jQuery('<span class="add-on cursorPointer vTEEditReferenceRecord"><i class="icon-pencil" title="Edit"></i></span>').insertBefore( jQuery(this) );

        });
        jQuery('span.vTEEditReferenceRecord').on('click',function(e){
            var element = jQuery(e.currentTarget);
            thisInstance.controlElementTd = element.closest('td');
            //Check have id show
            //If record is not linked(selected) and user clicks on pencil - show tooltip "No linked record!"
            var sourceFieldValue =  thisInstance.controlElementTd.find('[class=sourceField]').val();
            if(sourceFieldValue>0){
                thisInstance.moduleEditName =  thisInstance.getReferencedModuleName(thisInstance.controlElementTd );
                thisInstance.recordId = sourceFieldValue;
                thisInstance.referenceVTEEditHandler(thisInstance.controlElementTd );
            }else{
                alert(app.vtranslate('No linked record!'));
            }

        });
    },
    referenceVTEEditHandler: function(container){
        var thisInstance = this;
        var postVTEQuickEditSave  = function(data) {
            var params = {};
            params.name = data.result._recordLabel;
            params.id = data.result._recordId;
            thisInstance.setReferenceFieldValue(container, params);
        }

        var referenceModuleName = this.getReferencedModuleName(container);
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
        if(quickCreateNode.length <= 0) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
        }

        var viewEditUrl = "index.php?module=VTEQuickEdit&view=QuickEditAjax&record="+thisInstance.recordId+"&moduleEditName="+thisInstance.moduleEditName;
        var params= {'callbackFunction':postVTEQuickEditSave,'noCache':true};
        var progress = jQuery.progressIndicator();
        thisInstance.getVTEQuickEditForm(viewEditUrl, referenceModuleName, params).then(function(data) {
            thisInstance.handleVTEQuickEditData(data, params);
            progress.progressIndicator({
                'mode': 'hide'
            });
        });
    },
    getReferencedModuleName : function(parenElement){
        return jQuery('input[name="popupReferenceModule"]',parenElement).val();
    },
    getVTEQuickEditForm: function(url, moduleName, params) {
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        var requestParams;
        if (typeof params == 'undefined') {
            params = {};
        }
        if ((!params.noCache) || (typeof (params.noCache) == "undefined")) {
            if (typeof Vtiger_Header_Js.quickCreateModuleCache['edit_'+moduleName] != 'undefined') {
                aDeferred.resolve(Vtiger_Header_Js.quickCreateModuleCache['edit_'+moduleName]);
                return aDeferred.promise();
            }
        }
        requestParams = url;
        if (typeof params.data != "undefined") {
            var requestParams = {};
            requestParams['data'] = params.data;
            requestParams['url'] = url;
        }
        AppConnector.request(requestParams).then(function(data) {
            if ((!params.noCache) || (typeof (params.noCache) == "undefined")) {
                Vtiger_Header_Js.quickCreateModuleCache['edit_'+moduleName] = data;
            }

            aDeferred.resolve(data);
        });

        return aDeferred.promise();
    },
    handleVTEQuickEditData: function(data, params) {
        if (typeof params == 'undefined') {
            params = {};
        }
        var thisInstance = this;
        app.showModalWindow(data, function(data) {
            var quickEditForm = data.find('form[name="VTEQuickEdit"]');
            var moduleName = quickEditForm.find('[name="module"]').val();
            var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName(moduleName);
            editViewInstance.registerBasicEvents(quickEditForm);
            quickEditForm.validationEngine(app.validationEngineOptions);

            if (typeof params.callbackPostShown != "undefined") {
                params.callbackPostShown(quickEditForm);
            }

            thisInstance.registerVTEQuickEditPostLoadEvents(quickEditForm, params);
            app.registerEventForDatePickerFields(quickEditForm);
            var quickCreateContent = quickEditForm.find('.quickCreateContent');
            var quickCreateContentHeight = quickCreateContent.height();
            var contentHeight = parseInt(quickCreateContentHeight);
            if (contentHeight > 300) {
                app.showScrollBar(jQuery('.quickCreateContent'), {
                    'height': '300px'
                });
            }
        });
    },
    registerVTEQuickEditPostLoadEvents: function(form, params) {
        var thisInstance = this;
        var submitSuccessCallbackFunction = params.callbackFunction;
        var goToFullFormCallBack = params.goToFullFormcallback;
        if (typeof submitSuccessCallbackFunction == 'undefined') {
            submitSuccessCallbackFunction = function() {
            };
        }

        form.on('submit', function(e) {
            var form = jQuery(e.currentTarget);
            var module = form.find('[name="module"]').val();
            //Form should submit only once for multiple clicks also
            if (typeof form.data('submit') != "undefined") {
                return false;
            } else {
                var invalidFields = form.data('jqv').InvalidFields;

                if (invalidFields.length > 0) {
                    //If validation fails, form should submit again
                    form.removeData('submit');
                    form.closest('#globalmodal').find('.modal-header h3').progressIndicator({
                        'mode': 'hide'
                    });
                    e.preventDefault();
                    return;
                } else {
                    //Once the form is submiting add data attribute to that form element
                    form.data('submit', 'true');
                    form.closest('#globalmodal').find('.modal-header h3').progressIndicator({
                        smallLoadingImage: true,
                        imageContainerCss: {
                            display: 'inline',
                            'margin-left': '18%',
                            position: 'absolute'
                        }
                    });
                }
                var recordPreSaveEvent = jQuery.Event(Vtiger_Edit_Js.recordPreSave);
                form.trigger(recordPreSaveEvent, {
                    'value': 'edit',
                    'module': module
                });

                if (!(recordPreSaveEvent.isDefaultPrevented())) {

                    var targetInstance = thisInstance;
                    var moduleInstance = Vtiger_Edit_Js.getInstanceByModuleName(module);
                    if(typeof(moduleInstance.quickEditSave) === 'function'){

                        targetInstance = moduleInstance;
                    }

                    targetInstance.quickEditSave(form).then(
                        function(data) {
                            app.hideModalWindow();

                            //fix for Refresh list view after Quick create
                            var parentModule=app.getModuleName();
                            var viewname=app.getViewName();
                            if((module == parentModule) && (viewname=="List")){
                                var listinstance = new Vtiger_List_Js();
                                listinstance.getListViewRecords();
                            }
                            submitSuccessCallbackFunction(data);

                            var registeredCallBackList = thisInstance.quickEditCallBacks;
                            for (var index = 0; index < registeredCallBackList.length; index++) {
                                var callBack = registeredCallBackList[index];
                                callBack({
                                    'data': data,
                                    'name': form.find('[name="module"]').val()
                                });
                            }

                        },
                        function(error, err) {
                        }
                    );
                } else {
                    //If validation fails in recordPreSaveEvent, form should submit again
                    form.removeData('submit');
                    form.closest('#globalmodal').find('.modal-header h3').progressIndicator({
                        'mode': 'hide'
                    });
                }
                e.preventDefault();
            }
        });

        form.find('#goToFullForm').on('click', function(e) {
            var form = jQuery(e.currentTarget).closest('form');
            var editViewUrl = jQuery(e.currentTarget).data('editViewUrl');
            if (typeof goToFullFormCallBack != "undefined") {
                goToFullFormCallBack(form);
            }
            thisInstance.quickEditGoToFullForm(form, editViewUrl);
        });

    },
    setReferenceFieldValue : function(container, params) {
        var sourceField = container.find('input[class="sourceField"]').attr('name');
        var fieldElement = container.find('input[name="'+sourceField+'"]');
        var sourceFieldDisplay = sourceField+"_display";
        var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
        var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

        var selectedName = params.name;
        var id = params.id;

        fieldElement.val(id)
        fieldDisplayElement.val(selectedName).attr('readonly',true);
        fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});

        fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
    },
    /**
     * Function to save the quicklinkedit module
     * @param accepts form element as parameter
     * @return returns deferred promise
     */
    quickEditSave: function(form) {
        // console.log(form);
        var aDeferred = jQuery.Deferred();
        var quickEditSaveUrl = form.serializeFormData();
        AppConnector.request(quickEditSaveUrl).then(
            function(data) {
                //TODO: App Message should be shown
                aDeferred.resolve(data);
            },
            function(textStatus, errorThrown) {
                aDeferred.reject(textStatus, errorThrown);
            }
        );
        return aDeferred.promise();
    },
    /**
     * Function to navigate from quicklinkedit to editView Fullform
     * @param accepts form element as parameter
     */
    quickEditGoToFullForm: function(form, editViewUrl) {
        var formData = form.serializeFormData();
        //As formData contains information about both view and action removed action and directed to view
        delete formData.module;
        delete formData.action;
        //var formDataUrl = jQuery.param(formData);
        var completeUrl = editViewUrl + "&record="+this.recordId;
        window.location.href = completeUrl;
    },
    registerEvents: function() {
        this.registerVTEQuickEditEvent();
    }
});
jQuery(document).ready(function(){
    var vteQuickEdit = new VTEQuickEdit_Js();
    var params = {};
    params.action = 'ActionAjax';
    params.module = 'VTEQuickEdit';
    params.mode = 'checkEnable';
    AppConnector.request(params).then(
        function(data) {
            if (data.result.status == '1') {
                vteQuickEdit.registerVTEQuickEditEvent();
            }
        }
    );
});