/* ********************************************************************************
 * The content of this file is subject to the Field Autofill ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("FieldAutofill_Js",{
        editInstance:false,
        getInstance: function(){
            if(FieldAutofill_Js.editInstance == false){
                var instance = new FieldAutofill_Js();
                FieldAutofill_Js.editInstance = instance;
                return instance;
            }
            return FieldAutofill_Js.editInstance;
        },
        autoFillData: function(container,mapping, arrFields) {
            var unavailableFields='';
            for (felement in mapping) {
                if(container.find('[name="'+felement+'"]').length>0) {
                    if(container.find('[name="'+felement+'"]').is('select.chzn-select')) {
                        container.find('[name="'+felement+'"]').val(mapping[felement]).trigger('liszt:updated');
                    }
                    else if(container.find('[name^="'+felement+'"]').is('select.select2')) {

                        var values=mapping[felement].split(' |##| ');
                        jQuery.each(mapping[felement].split(" |##| "), function(i,e){
                            container.find('[name^="'+felement+'"] option[value="'+e+'"]').prop("selected", true);
                        });
                        container.find('[name^="'+felement+'"]').trigger("change");
                    }
                    else if(container.find('[name="'+felement+'"]').is(':checkbox')) {
                        if(mapping[felement] == 1) {
                            container.find('[name^="'+felement+'"]').prop("checked", true);
                        }
                        if(mapping[felement] == 0) {
                            container.find('[name^="'+felement+'"]').prop("checked", false);
                        }
                    }
                    else {
                        container.find('[name="'+felement+'"]').val(mapping[felement]);
                    }
                    if(jQuery.inArray(felement,arrFields) !== -1) {
						var refield=felement;
                        var actionParams = {
                            "type":"POST",
                            "url":'index.php?module=FieldAutofill&action=ActionAjax&mode=getReferenceName',
                            "dataType":"json",
                            "data" : {
                                'record':mapping[refield],
                                'field' : refield
                            }
                        };
                        app.request.post(actionParams).then(
                            function(err,data){
                                if(err === null) {
                                    if (data != null) {
                                        var selField=data.field;
                                        var decoded = $("<textarea/>").html(data.display_value).text();
                                        container.find('[name="'+selField+'_display"]').val(decoded).attr('readonly',true);
                                    }
                                }else{
                                    // to do
                                }
                            }
                        );
                    }
                }else{
                    unavailableFields +='<input type="hidden" name="'+felement+'" value="'+mapping[felement]+'"/>';
                }
            }
            container.append(unavailableFields);
        }
    },
    {

        registerEventForAddingRelatedRecord : function(){
            if($.url().param('view') == "Detail"){
                if(typeof Vtiger_Detail_Js !== 'undefined') {
                    var thisInstance = new Vtiger_Detail_Js();
                    var detailContentsHolder = thisInstance.getContentHolder();
                    detailContentsHolder.off('click', '[name="addButton"]');
                    detailContentsHolder.on('click', '[name="addButton"]', function (e) {
                        e.stopPropagation();
                        var element = jQuery(e.currentTarget);
                        var selectedTabElement = thisInstance.getSelectedTab();
                        var relatedModuleName = element.attr('module');
                        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="' + relatedModuleName + '"]');
                        if (quickCreateNode.length <= 0) {
                            window.location.href = element.data('url');
                            return;
                        }

                        var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), $.url().param('module'), selectedTabElement, relatedModuleName);
                        relatedController.addRelatedRecord(element);
                    });
                }
            }
        }
    }
);
//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_FieldAutofill();
    }, 2000);
});
function initData_FieldAutofill() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('FieldAutofill')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var container=jQuery('#EditView');
    // Get reference fields
    var arrFields=[];
    container.find('.sourceField').each(function(e) {
        arrFields.push(jQuery(this).attr('name'));

    });
    var module=container.find('input[name="module"]').val();
    if (module != undefined && module != ''){
        var url ='index.php?module=FieldAutofill&action=ActionAjax&mode=getReferenceFields';
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"json",
            "data" : {
                'edit_module':module
            }
        };
        app.request.post(actionParams).then(
            function(err,data){
                if(err === null) {
                    if(data != null) {
                        var result = data;
                        for (var field in result) {
                            // register event
                            container.find('input[name="'+field+'"]').unbind(Vtiger_Edit_Js.referenceSelectionEvent);
                            container.on(Vtiger_Edit_Js.referenceSelectionEvent,'input[name="'+field+'"]', function(e,data) {
                                data['sec_module']= module;
                                var crfield = jQuery(e.currentTarget).attr('name');
                                var actionParams = {
                                    "type":"POST",
                                    "url":'index.php?module=FieldAutofill&action=ActionAjax&mode=getMappingFields',
                                    "dataType":"json",
                                    "data" : data
                                };
                                // Get mapping fields
                                app.request.post(actionParams).then(
                                    function(err,data){
                                        if(err === null) {
                                            if (data != null) {
                                                var mapping = data.mapping;
                                                var showPopup = data.showPopup;
                                                var selectedName = data.selectedName;
                                                var moduleLabel = data.moduleLabel;
                                                if (showPopup == 1) {
                                                    var message = 'Overwrite the existing fields with the selected ' + moduleLabel + ' (' + selectedName + ') ' + 'details';
                                                    app.helper.showConfirmationBox({'message': message}).then(
                                                        function (e) {
                                                            FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                                                        },
                                                        function (error, err) {
                                                        });
                                                } else {
                                                    FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                                                }
                                            }
                                        }else{
                                            // error
                                        }
                                    }
                                );
                            });
                        }
                    }
                }else{
                    // to do
                }
            }
        );
    }
    // Auto fill from relation
    var sPageURL = window.location.search.substring(1);
    if(sPageURL.indexOf('&relationOperation=true') != -1) {
        // Get reference fields
        var arrFields=[];
        container.find('.sourceField').each(function(e) {
            arrFields.push(jQuery(this).attr('name'));

        });

        var sourceModule='';
        var sourceRecord='';
        var returnmodule='';
        var returnrecord='';
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++)
        {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == 'sourceModule')
            {
                sourceModule = sParameterName[1];
            }
            else if(sParameterName[0] == 'sourceRecord') {
                sourceRecord = sParameterName[1];
            }
            else if(sParameterName[0] == 'returnrecord') {
                returnrecord = sParameterName[1];
            }
            else if(sParameterName[0] == 'returnmodule') {
                returnmodule = sParameterName[1];
            }
        }

        if(sourceModule == '' && sourceRecord == '') {
            sourceModule = returnmodule;
            sourceRecord = returnrecord;
        }

        if(sourceModule !='' && sourceRecord !='') {
            var actionParams = {
                "type":"POST",
                "url":'index.php?module=FieldAutofill&action=ActionAjax&mode=getMappingFields',
                "dataType":"json",
                "data" : {
                    'source_module':sourceModule,
                    'record':sourceRecord,
                    'sec_module':module
                }
            };
            // Get mapping fields
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if (data != null) {
                            var mapping = data.mapping;
                            var showPopup = data.showPopup;
                            var selectedName = data.selectedName;
                            var moduleLabel = data.moduleLabel;
                            if (showPopup == 1) {
                                var message = 'Overwrite the existing fields with the selected ' + moduleLabel + ' (' + selectedName + ') ' + 'details';
                                app.helper.showConfirmationBox({'message': message}).then(
                                    function (e) {
                                        FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                                    },
                                    function (error, err) {
                                    });
                            } else {
                                FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                            }
                        }
                    }else{
                        // to do
                    }
                }
            );
        }
    }
    else {
        var sourceModule='';
        var sourceRecord='';
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++)
        {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == 'salesorder_id')
            {
                sourceRecord = sParameterName[1];
                sourceModule ='SalesOrder';
            }
            else if(sParameterName[0] == 'quote_id') {
                sourceRecord = sParameterName[1];
                sourceModule ='Quotes';
            }
        }

        if(sourceModule !='' && sourceRecord !='') {
            var actionParams = {
                "type":"POST",
                "url":'index.php?module=FieldAutofill&action=ActionAjax&mode=getMappingFields',
                "dataType":"json",
                "data" : {
                    'source_module':sourceModule,
                    'record':sourceRecord,
                    'sec_module':module
                }
            };
            // Get mapping fields
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if (data != null) {
                            var mapping = data.mapping;
                            var showPopup = data.showPopup;
                            var selectedName = data.selectedName;
                            var moduleLabel = data.moduleLabel;
                            if (showPopup == 1) {
                                var message = 'Overwrite the existing fields with the selected ' + moduleLabel + ' (' + selectedName + ') ' + 'details';
                                app.helper.showConfirmationBox({'message': message}).then(
                                    function (e) {
                                        FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                                    },
                                    function (error, err) {
                                    });
                            } else {
                                FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                            }
                        }
                    }else{
                        // to do
                    }
                }
            );
        }
    }

    var instance = FieldAutofill_Js.getInstance();
    instance.registerEventForAddingRelatedRecord();

    // Auto fill on Quick Create form
    app.event.on('post.QuickCreateForm.show', function(even, data) {
        setTimeout(function () {
            var form=jQuery('form[name="QuickCreate"]');
            // Get reference fields
            var arrFields=[];
            form.find('.sourceField').each(function(e) {
                arrFields.push(jQuery(this).attr('name'));

            });
            var sourceModule=form.find('input[name="sourceModule"]').val();
            var sourceRecord=form.find('input[name="sourceRecord"]').val();
            var module=form.find('input[name="module"]').val();
            var actionParams = {
                "type":"POST",
                "url":'index.php?module=FieldAutofill&action=ActionAjax&mode=getMappingFields',
                "dataType":"json",
                "data" : {
                    'source_module':sourceModule,
                    'record':sourceRecord,
                    'sec_module':module
                }
            };
            // Get mapping fields
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if(data != null) {
                            var mapping=data.mapping;
                            var showPopup=data.showPopup;
                            var selectedName=data.selectedName;
                            var moduleLabel=data.moduleLabel;
                            if(showPopup == 1) {
                                var message = 'Overwrite the existing fields with the selected ' +moduleLabel+' ('+selectedName+') '+ 'details';
                                app.helper.showConfirmationBox({'message' : message}).then(
                                    function(e) {
                                        FieldAutofill_Js.autoFillData(form,mapping, arrFields)
                                    },
                                    function(error, err){
                                    });
                            }else {
                                FieldAutofill_Js.autoFillData(form,mapping, arrFields)
                            }
                        }
                    }else{
                        // to do
                    }
                }

            );

            // Register related fields event
            var container=form;
            var module=container.find('input[name="module"]').val();
            var url ='index.php?module=FieldAutofill&action=ActionAjax&mode=getReferenceFields';
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"json",
                "data" : {
                    'edit_module':module
                }
            };
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if(data != null) {
                            var result = data;
                            for (var field in result) {
                                container.find('input[name="' + field + '"]').unbind(Vtiger_Edit_Js.referenceSelectionEvent);
                                container.on(Vtiger_Edit_Js.referenceSelectionEvent, 'input[name="' + field + '"]', function (e, data) {
                                    data['sec_module'] = module;

                                    var crfield = jQuery(e.currentTarget).attr('name');
                                    var actionParams = {
                                        "type": "POST",
                                        "url": 'index.php?module=FieldAutofill&action=ActionAjax&mode=getMappingFields',
                                        "dataType": "json",
                                        "data": data
                                    };
                                    // Get mapping fields
                                    app.request.post(actionParams).then(
                                        function(err,data){
                                            if(err === null) {
                                                if (data != null) {
                                                    var mapping = data.mapping;
                                                    var showPopup = data.showPopup;
                                                    var selectedName = data.selectedName;
                                                    var moduleLabel = data.moduleLabel;
                                                    if (showPopup == 1) {
                                                        var message = 'Overwrite the existing fields with the selected ' + moduleLabel + ' (' + selectedName + ') ' + 'details';
                                                        app.helper.showConfirmationBox({'message': message}).then(
                                                            function (e) {
                                                                FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                                                            },
                                                            function (error, err) {
                                                            });
                                                    } else {
                                                        FieldAutofill_Js.autoFillData(container, mapping, arrFields)
                                                    }
                                                }
                                            }else{
                                                // to do
                                            }
                                        }

                                    );
                                });
                            }
                        }
                    }else{
                        // to do
                    }
                }
            );
        }, 1000);

    });
    //jQuery(document).one('mouseenter','form[name="QuickCreate"]', function() {
    //    var form=jQuery(this);
    //
    //    form.unbind('mouseenter mouseleave');
    //});

}
