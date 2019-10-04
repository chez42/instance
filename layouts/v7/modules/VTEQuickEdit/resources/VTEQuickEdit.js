/* ********************************************************************************
 * The content of this file is subject to the VTEQuickEdit ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEQuickEdit_Js",{
    instance:false,
    getInstance: function(){
        if(VTEQuickEdit_Js.instance == false){
            var instance = new VTEQuickEdit_Js();
            VTEQuickEdit_Js.instance = instance;
            return instance;
        }
        return VTEQuickEdit_Js.instance;
    }
},{
    recordId: null,
    moduleEditName: null,
    controlElementTd: null,
    dataEdit: null,
    VTEQuickEditCallBacks: [],
    registerVTEQuickEditEvent: function(){
        var thisInstance=this;
        jQuery('span.createReferenceRecord').each(function(){
            jQuery(this).closest('div.referencefield-wrapper').css({"width":"100%"});
            jQuery('<span class="add-on cursorPointer vTEEditReferenceRecord" style="float: left;margin-left: 5px;margin-top: 3px;border: 1px solid #DDDDDD;padding: 3px 7px;text-align: center;color: #666;background: #F3F3F3;"><i class="fa fa-pencil" title="Edit"></i></span>').insertBefore( jQuery(this) );
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
            params.name = data._recordLabel;
            params.id = data._recordId;
            thisInstance.setReferenceFieldValue(container, params);
        }

        var referenceModuleName = this.getReferencedModuleName(container);
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
        if(quickCreateNode.length <= 0) {
            app.helper.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
        }

        //var viewEditUrl = "index.php?module=VTEQuickEdit&view=QuickEditAjax&record="+thisInstance.recordId+"&moduleEditName="+thisInstance.moduleEditName;
        var viewEditUrl = "module=VTEQuickEdit&view=QuickEditAjax&record="+thisInstance.recordId+"&moduleEditName="+thisInstance.moduleEditName;
        var params= {'callbackFunction':postVTEQuickEditSave,'noCache':true};
        thisInstance.getVTEQuickEditForm(viewEditUrl, referenceModuleName, params).then(function(data) {
            thisInstance.handleVTEQuickEditData(data, params);
            app.helper.hideProgress();
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
            if (typeof app.helper.quickCreateModuleCache['edit_'+moduleName] != 'undefined') {
                aDeferred.resolve(app.helper.quickCreateModuleCache['edit_'+moduleName]);
                return aDeferred.promise();
            }
        }
        requestParams = url;
        if (typeof params.data != "undefined") {
            var requestParams = {};
            requestParams['data'] = params.data;
            requestParams['url'] = url;
        }
        app.request.post({'data':requestParams}).then(
            function(err,data){
                if(err === null) {
                    if ((!params.noCache) || (typeof (params.noCache) == "undefined")) {
                        app.helper.quickCreateModuleCache['edit_'+moduleName] = data;
                    }
                    aDeferred.resolve(data);
                }else{
                }
            }
        );

        return aDeferred.promise();
    },
    handleVTEQuickEditData: function(data, params) {
        if (typeof params == 'undefined') {
            params = {};
        }
        var thisInstance = this;
        app.helper.showModal(data,{'cb' : function (data){
            var quickEditForm = data.find('form[name="VTEQuickEdit"]');
            var moduleName = quickEditForm.find('[name="module"]').val();
            var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName(moduleName);
            editViewInstance.registerBasicEvents(quickEditForm);
            quickEditForm.vtValidate(app.validationEngineOptions);

            if (typeof params.callbackPostShown != "undefined") {
                params.callbackPostShown(quickEditForm);
            }
            thisInstance.registerVTEQuickEditPostLoadEvents(quickEditForm, params);
            var quickCreateContent = quickEditForm.find('.quickCreateContent');
            var quickCreateContentHeight = quickCreateContent.height();
            var contentHeight = parseInt(quickCreateContentHeight);
            if (contentHeight > 300) {
                app.showScrollBar(jQuery('.quickCreateContent'), {
                    'height': '300px'
                });
            }
        }});
    },
    registerVTEQuickEditPostLoadEvents: function(form, params) {
        var thisInstance = this;
        var submitSuccessCallbackFunction = params.callbackFunction;
        var goToFullFormCallBack = params.goToFullFormcallback;
        if (typeof submitSuccessCallbackFunction == 'undefined') {
            submitSuccessCallbackFunction = function() {
            };
        }
        form.find("button[name='vteQuickEditSaveButton']").on('click', function(e) {
            var form = jQuery(e.currentTarget).closest('form');
            var module = form.find('[name="module"]').val();
            var aDeferred = jQuery.Deferred();
            var params = {
                submitHandler: function (frm) {
                    jQuery("button[name='vteQuickEditSaveButton']").attr("disabled", "disabled");
                    if (this.numberOfInvalids() > 0) {
                        return false;
                    }
                    var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                    app.event.trigger(e);
                    if (e.isDefaultPrevented()) {
                        return false;
                    }
                    var formData = jQuery(frm).serialize();
                    app.helper.showProgress();
                    app.request.post({data: formData}).then(function (err, data) {
                        if (!err) {
                            aDeferred.resolve(data);
                            var parentModule=app.getModuleName();
                            var viewname=app.getViewName();
                            if((module == parentModule) && (viewname=="List")){
                                var listinstance = new Vtiger_List_Js();
                                listinstance.getListViewRecords();
                            }
                            submitSuccessCallbackFunction(data);
                        } else {
                            app.helper.showErrorNotification({"message": err});
                        }
                        app.helper.hideModal();
                        app.helper.hideProgress();
                    });
                }
            };
            form.vtValidate(params);
            form.submit();
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
        
        //fieldDisplayElement.vtValidate('closePrompt',fieldDisplayElement);
    },
    /**
     * Function to save the quicklinkedit module
     * @param accepts form element as parameter
     * @return returns deferred promise
     */
    quickEditSave: function(form) {
        var aDeferred = jQuery.Deferred();
        var quickEditSaveUrl = form.serializeFormData();
        app.request.post({'data':quickEditSaveUrl}).then(
            function(err,data){
                if(err === null) {
                    aDeferred.resolve(data);
                }else{
                    //aDeferred.reject(textStatus, errorThrown);
                }
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
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_VTEQuickEdit();
    }, 5000);
});
function initData_VTEQuickEdit() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTEQuickEdit')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var vteQuickEdit = new VTEQuickEdit_Js();
    var params = {};
    params.action = 'ActionAjax';
    params.module = 'VTEQuickEdit';
    params.mode = 'checkEnable';
    app.request.post({data:params}).then(
        function (err,data) {
            if(err == null) {
                if (data.status == '1') {
                    vteQuickEdit.registerVTEQuickEditEvent();
                }
            }
        }
    );
}