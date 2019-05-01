//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_RealTimeFieldFormulas();
    }, 1000);
});
function initData_RealTimeFieldFormulas() {
    var getRequest = {
        params : [],
        init : function () {
            var thisInstance = this;
            var currentUrlParams = $(location).attr('search');
            currentUrlParams = currentUrlParams.substr(1);
            currentUrlParams = currentUrlParams.split("&");
            var keys = {}
            $.each(currentUrlParams, function (key,param) {
                var paramArr = param.split("=")
                keys[paramArr[0]] = paramArr[1];
            });
            thisInstance.params = keys;
        },
        getParam : function (key) {
            var params = this.params;
            return params[key];
        },
        getAllParams : function () {
            return this.params;
        },
    };getRequest.init();

    var RealTimeFieldFormulas = {
        fieldStore : null,
        registerEventGetSettingWF : function (modulename,formEditContainerObj) {
            if (typeof modulename == 'undefined' || modulename == ''){
                var modulename = app.getModuleName();
            }

            if (typeof formEditContainerObj == 'undefined' || formEditContainerObj == ''){
                var formEditContainerObj = $('div[name="editContent"]');
            }

            var thisInstance = this;
            var params = {
                'module' : 'RealTimeFieldFormulas',
                'action' : 'ActionAjax',
                'mode'   : 'getSettingWF',
                'source' : modulename,
            }

            app.request.post({data:params}).then(
                function(err, res) {
                    if (err === null){
                        if (res.license){
                            var fields = {};
                            var data = res.data;
                            $.each(data, function (key,item) {
                                var condition = item.condition;
                                if (typeof condition.and != "undefined") {
                                    $.each(condition.and,function (keyCondition, itemCondition) {
                                        var fieldElementFocus = formEditContainerObj.find('[name="'+itemCondition.fieldname+'"]');
                                        fieldElementFocus.on('change '+Vtiger_Edit_Js.referenceSelectionEvent,function () {
                                            var allFields = formEditContainerObj.find('[name]');
                                            $.each(allFields, function (key, item) {
                                                fields[item.name] = $(item).val();
                                            });

                                            var params = {
                                                'record': jQuery('[name="record"]').val(),
                                                'module': 'RealTimeFieldFormulas',
                                                'action': 'ActionAjax',
                                                'mode': 'getValuesForUpdateFieldsRealtimeFormulas',
                                                'source': modulename,
                                                'field': JSON.stringify(fields),
                                                'store': JSON.stringify(thisInstance.fieldStore),
                                            }

                                            app.request.post({data:params}).then(function(err, res) {
                                                if (err === null){
                                                    if (typeof res != 'undefined'){
                                                        thisInstance.registerUpdateFields(res,formEditContainerObj);
                                                        thisInstance.fieldStore = thisInstance.registerEventGetCurrentFieldValues(formEditContainerObj);
                                                    }
                                                }
                                            });
                                        });
                                    });
                                }
                                if (typeof condition.any != 'undefined'){
                                    $.each(condition.any,function (keyCondition, itemCondition) {
                                        var fieldElementFocus = formEditContainerObj.find('[name="'+itemCondition.fieldname+'"]');
                                        fieldElementFocus.on('change '+Vtiger_Edit_Js.referenceSelectionEvent,function () {
                                            var allFields = formEditContainerObj.find('[name]');
                                            $.each(allFields, function (key, item) {
                                                fields[item.name] = $(item).val();
                                            });

                                            var params = {
                                                'record': jQuery('[name="record"]').val(),
                                                'module': 'RealTimeFieldFormulas',
                                                'action': 'ActionAjax',
                                                'mode': 'getValuesForUpdateFieldsRealtimeFormulas',
                                                'source': modulename,
                                                'field': JSON.stringify(fields),
                                                'store': JSON.stringify(thisInstance.fieldStore),
                                            }

                                            app.request.post({data:params}).then(function(err, res) {
                                                if (err === null){
                                                    if (typeof res != 'undefined'){
                                                        thisInstance.registerUpdateFields(res,formEditContainerObj);
                                                        thisInstance.fieldStore = thisInstance.registerEventGetCurrentFieldValues(formEditContainerObj);
                                                    }
                                                }
                                            });
                                        });
                                    });
                                }
                            });
                            return true;
                        }
                    }
                }
            );
        },

        registerUpdateFields : function (result,formEditContainerObj) {
            var res = result;
            $.each(res, function (keyResult, itemResult) {
                var itemResult = JSON.parse(itemResult);
                $.each(itemResult, function (keyField, keyFieldParams) {
                    if (typeof keyFieldParams.ActiveRealTimeField != 'undefined' && keyFieldParams.ActiveRealTimeField){
                        var fieldElementChange = formEditContainerObj.find('[name="'+keyFieldParams.fieldname+'"]');
                        if (fieldElementChange.length > 0){
                            if (fieldElementChange.length == 1){
                                fieldElementChange.val($.trim(keyFieldParams.value));
                                fieldElementChange.trigger("change");
                            }else{
                                fieldElementChange = fieldElementChange[1];
                                if($(fieldElementChange).data('fieldtype') == 'checkbox'){
                                    if ($.trim(keyFieldParams.value) == 1){
                                        $(fieldElementChange).prop('checked',true);
                                    }else {
                                        $(fieldElementChange).prop('checked',false);
                                    }
                                }
                            }
                        }
                    }
                });
            });
        },

        registerEventGetCurrentFieldValues : function (formEditContainerObj) {
            var fieldStore = {};
            var allFieldsElementFocus = formEditContainerObj.find('[name]');
            $.each(allFieldsElementFocus, function (keyField, fieldParams) {
                fieldStore[fieldParams.name] = $(fieldParams).val();
            });
            return fieldStore;
        },

        registerEvenForViewDetail : function () {
            var thisInstance = this;
            if (app.getViewName() == 'Detail'){
                $(document).ajaxComplete( function (event, request, settings) {
                    var formIdEditViewObj = $(document).find('form#EditView');
                    if (formIdEditViewObj.length > 0){
                        var formEditContainerObj = $(formIdEditViewObj).find('div[name="editContent"]');
                        var inputHiddenModuleObj = $(formIdEditViewObj).find('input[name="module"]');
                        var modulename = $(inputHiddenModuleObj).val();
                        if (formEditContainerObj.length > 0){
                            $(event.currentTarget).unbind('ajaxComplete');
                            var formEditContainerObj = $('div[name="editContent"]');
                            thisInstance.fieldStore = thisInstance.registerEventGetCurrentFieldValues(formEditContainerObj);
                            thisInstance.registerEventGetSettingWF(modulename,formEditContainerObj);
                            var divDataClassObj = $(formIdEditViewObj).closest('div.data');
                            var buttonClose= $(divDataClassObj).find('button.close');
                            $(buttonClose).on('click',function () {
                                thisInstance.registerEvenForViewDetail();
                            });

                            var buttonSave=$(formIdEditViewObj).find('button.saveButton');
                            $(buttonSave).on('click',function () {
                                var eventSetInterval = setInterval(function(){
                                    var formEditContainerObj = $(formIdEditViewObj).find('div[name="editContent"]');
                                    if (formEditContainerObj.length == 0){
                                        clearInterval(eventSetInterval);
                                        // $(event.currentTarget).bind('ajaxComplete');
                                        thisInstance.registerEvenForViewDetail();
                                    }
                                }, 1000);
                            });

                            var buttonCancel=$(formIdEditViewObj).find('a.cancelLink');
                            $(buttonCancel).on('click',function () {
                                thisInstance.registerEvenForViewDetail();
                            });
                        }
                    }
                });
            }
        },

        registerEvents : function () {
            var thisInstance = this;
            if (app.getViewName() == 'Edit'){
                var modulename = app.getModuleName();
                var formEditContainerObj = $('div[name="editContent"]');
                thisInstance.fieldStore = thisInstance.registerEventGetCurrentFieldValues(formEditContainerObj);
                var loadSettingWF = thisInstance.registerEventGetSettingWF(modulename,formEditContainerObj);
                var eventSetInterval = setInterval(function(){
                    if (thisInstance.fieldStore != null){
                        clearInterval(eventSetInterval);
                        var allParams = getRequest.getAllParams();
                        if (typeof allParams['__vtrftk'] != 'undefined' && allParams['__vtrftk'] != '') {
                            delete allParams['view'];
                            delete allParams['module'];
                            delete allParams['popupReferenceModule'];
                            console.log('loadSettingWF',loadSettingWF);
                            $.each(allParams, function (fieldname, fieldvalue) {
                                var fieldElement = formEditContainerObj.find('[name="'+fieldname+'"]');
                                $(fieldElement).trigger('change');
                            });
                        }
                    }
                }, 1000);
            }

            thisInstance.registerEvenForViewDetail();


            $('.quickCreateModule').on('click',function () {
                var moduleName = $(this).data('name');
                $(document).ajaxComplete( function (event, request, settings) {
                    var formQuickCreate = $(document).find('.quickCreateContent');
                    if (formQuickCreate.length > 0){
                        thisInstance.fieldStore = thisInstance.registerEventGetCurrentFieldValues(formQuickCreate);
                        thisInstance.registerEventGetSettingWF(moduleName,formQuickCreate);
                        $(event.currentTarget).unbind('ajaxComplete');
                    }
                });
            });
        },
    };RealTimeFieldFormulas.registerEvents();
}