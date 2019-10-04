Settings_Workflows_Edit_Js.prototype.registerRealTimeFieldFormulasTaskEvents = function () {
    var thisInstance = this;
    this.registerAddFieldEvent();
    this.registerDeleteConditionEvent();
    this.registerFieldChange();
    this.fieldValueMap = false;
    if (jQuery('#fieldValueMapping').val() != '') {
        this.fieldValueReMapping();
    }
    var fields = jQuery('#save_fieldvaluemapping').find('select[name="fieldname"]');
    jQuery.each(fields, function (i, field) {
        thisInstance.loadFieldSpecificUiRealTimeFieldFormulasTask(jQuery(field));
    });
    this.getPopUp(jQuery('#saveTask'));
};
Settings_Workflows_Edit_Js.prototype.registerAddFieldEvent = function () {
    jQuery('#addFieldBtn').on('click', function (e) {
        var newAddFieldContainer = jQuery('.basicAddFieldContainer').clone(true, true).removeClass('basicAddFieldContainer hide').addClass('conditionRow');
        jQuery('select', newAddFieldContainer).addClass('select2');
        jQuery('#save_fieldvaluemapping').append(newAddFieldContainer);
        vtUtils.showSelect2ElementView(newAddFieldContainer.find('.select2'));
    });
};
Settings_Workflows_Edit_Js.prototype.registerDeleteConditionEvent = function () {
    jQuery('#saveTask').on('click', '.deleteCondition', function (e) {
        jQuery(e.currentTarget).closest('.conditionRow').remove();
    })
};

Settings_Workflows_Edit_Js.prototype.getRealTimeFieldFormulasTaskFieldList = function () {
    var taskType = jQuery('input[name="taskType"]').val();
    if(taskType == 'RealTimeFieldFormulasTask'){
        return new Array('fieldname', 'value', 'valuetype','ActiveRealTimeField');
    }
    return new Array('fieldname', 'value', 'valuetype');
};
Settings_Workflows_Edit_Js.prototype.preSaveRealTimeFieldFormulasTask = function (tasktype) {
    var values = this.getValuesRealTimeFieldFormulasTask(tasktype);
    jQuery('[name="field_value_mapping"]').val(JSON.stringify(values));
};

Settings_Workflows_Edit_Js.prototype.RealTimeFieldFormulasTaskCustomValidation = function() {
    return this.checkDuplicateFieldsSelected();
};


Settings_Workflows_Edit_Js.prototype.loadFieldSpecificUiRealTimeFieldFormulasTask = function (fieldSelect) {
    var selectedOption = fieldSelect.find('option:selected');
    var row = fieldSelect.closest('div.conditionRow');
    var fieldUiHolder = row.find('.fieldUiHolder');
    var fieldInfo = selectedOption.data('fieldinfo');
    var fieldValueMapping = this.getFieldValueMapping();
    var fieldValueMappingKey = fieldInfo.name;
    var taskType = jQuery('#taskType').val();
    if (taskType == "RealTimeFieldFormulasTask") {
        fieldValueMappingKey = fieldInfo.workflow_columnname;
        if (fieldValueMappingKey === undefined || fieldValueMappingKey === null){
            fieldValueMappingKey = selectedOption.val();
        }
    }
    if (fieldValueMapping != '' && typeof fieldValueMapping[fieldValueMappingKey] != 'undefined') {
        fieldInfo.value = fieldValueMapping[fieldValueMappingKey]['value'];
        fieldInfo.workflow_valuetype = fieldValueMapping[fieldValueMappingKey]['valuetype'];
    } else {
        fieldInfo.workflow_valuetype = 'rawtext';
    }

    if(fieldInfo.type == 'reference' || fieldInfo.type == 'multireference') {
        fieldInfo.referenceLabel = fieldUiHolder.find('[name="referenceValueLabel"]').val();
        fieldInfo.type = 'string';
    }

    var moduleName = this.getModuleName();

    var fieldModel = Vtiger_Field_Js.getInstance(fieldInfo, moduleName);
    this.fieldModelInstance = fieldModel;
    var fieldSpecificUi = this.getFieldSpecificUi(fieldSelect);

    //remove validation since we dont need validations for all eleements
    // Both filter and find is used since we dont know whether the element is enclosed in some conainer like currency
    var fieldName = fieldModel.getName();
    if (fieldModel.getType() == 'multipicklist') {
        fieldName = fieldName + "[]";
    }
    fieldSpecificUi.filter('[name="' + fieldName + '"]').attr('data-value', 'value').attr('data-workflow_columnname', fieldInfo.workflow_columnname);
    fieldSpecificUi.find('[name="' + fieldName + '"]').attr('data-value', 'value').attr('data-workflow_columnname', fieldInfo.workflow_columnname);
    fieldSpecificUi.filter('[name="valuetype"]').addClass('ignore-validation');
    fieldSpecificUi.find('[name="valuetype"]').addClass('ignore-validation');

    //If the workflowValueType is rawtext then only validation should happen
    var workflowValueType = fieldSpecificUi.filter('[name="valuetype"]').val();
    if (workflowValueType != 'rawtext' && typeof workflowValueType != 'undefined') {
        fieldSpecificUi.filter('[name="' + fieldName + '"]').addClass('ignore-validation');
        fieldSpecificUi.find('[name="' + fieldName + '"]').addClass('ignore-validation');
    }

    fieldUiHolder.html(fieldSpecificUi);

    if (fieldSpecificUi.is('input.select2')) {
        var tagElements = fieldSpecificUi.data('tags');
        var params = {tags: tagElements, tokenSeparators: [","]}
        vtUtils.showSelect2ElementView(fieldSpecificUi, params)
    } else if (fieldSpecificUi.is('select')) {
        if (fieldSpecificUi.hasClass('select2')) {
            vtUtils.showSelect2ElementView(fieldSpecificUi)
        } else {
            vtUtils.showSelect2ElementView(fieldSpecificUi);
        }
    } else if (fieldSpecificUi.is('input.dateField')) {
        var calendarType = fieldSpecificUi.data('calendarType');
        if (calendarType == 'range') {
            var customParams = {
                calendars: 3,
                mode: 'range',
                className: 'rangeCalendar',
                onChange: function (formated) {
                    fieldSpecificUi.val(formated.join(','));
                }
            }
            app.registerEventForDatePickerFields(fieldSpecificUi, false, customParams);
        } else {
            app.registerEventForDatePickerFields(fieldSpecificUi);
        }
    }
    return this;
};

Settings_Workflows_Edit_Js.prototype.getValuesRealTimeFieldFormulasTask = function (tasktype) {
    var thisInstance = this;
    var conditionsContainer = jQuery('#save_fieldvaluemapping');
    var fieldListFunctionName = 'get' + tasktype + 'FieldList';
    if (typeof thisInstance[fieldListFunctionName] != 'undefined') {
        var fieldList = thisInstance[fieldListFunctionName].apply()
    }

    var values = [];
    var conditions = jQuery('.conditionRow', conditionsContainer);
    conditions.each(function (i, conditionDomElement) {
        var rowElement = jQuery(conditionDomElement);
        var fieldSelectElement = jQuery('[name="fieldname"]', rowElement);
        var valueSelectElement = jQuery('[data-value="value"]', rowElement);
        //To not send empty fields to server
        if (thisInstance.isEmptyFieldSelected(fieldSelectElement)) {
            return true;
        }
        var fieldDataInfo = fieldSelectElement.find('option:selected').data('fieldinfo');
        var fieldType = fieldDataInfo.type;
        var rowValues = {};
        if (fieldType == 'owner') {
            for (var key in fieldList) {
                var field = fieldList[key];
                if (field == 'value' && valueSelectElement.is('select')) {
                    rowValues[field] = valueSelectElement.find('option:selected').val();
                } else {
                    rowValues[field] = jQuery('[name="' + field + '"]', rowElement).val();
                }
            }
        } else if (fieldType == 'picklist' || fieldType == 'multipicklist') {
            for (var key in fieldList) {
                var field = fieldList[key];
                if (field == 'value' && valueSelectElement.is('input')) {
                    var commaSeperatedValues = valueSelectElement.val();
                    var pickListValues = valueSelectElement.data('picklistvalues');
                    var valuesArr = commaSeperatedValues.split(',');
                    var newvaluesArr = [];
                    for (i = 0; i < valuesArr.length; i++) {
                        if (typeof pickListValues[valuesArr[i]] != 'undefined') {
                            newvaluesArr.push(pickListValues[valuesArr[i]]);
                        } else {
                            newvaluesArr.push(valuesArr[i]);
                        }
                    }
                    var reconstructedCommaSeperatedValues = newvaluesArr.join(',');
                    rowValues[field] = reconstructedCommaSeperatedValues;
                } else if (field == 'value' && valueSelectElement.is('select') && fieldType == 'picklist') {
                    rowValues[field] = valueSelectElement.val();
                } else if (field == 'value' && valueSelectElement.is('select') && fieldType == 'multipicklist') {
                    var value = valueSelectElement.val();
                    if (value == null) {
                        rowValues[field] = value;
                    } else {
                        rowValues[field] = value.join(',');
                    }
                } else {
                    rowValues[field] = jQuery('[name="' + field + '"]', rowElement).val();
                }
            }

        } else if (fieldType == 'text') {
            for (var key in fieldList) {
                var field = fieldList[key];
                if (field == 'value') {
                    rowValues[field] = rowElement.find('textarea').val();
                } else {
                    rowValues[field] = jQuery('[name="' + field + '"]', rowElement).val();
                }
            }
        } else {
            for (var key in fieldList) {
                var field = fieldList[key];
                if (field == 'value') {
                    rowValues[field] = valueSelectElement.val();
                } else {
                    rowValues[field] = jQuery('[name="' + field + '"]', rowElement).val();
                }
            }
        }
        if (jQuery('[name="valuetype"]', rowElement).val() == 'false' || (jQuery('[name="valuetype"]', rowElement).length == 0)) {
            rowValues['valuetype'] = 'rawtext';
        }

        if (jQuery('input[type=checkbox]').attr('checked')) {
            rowValues['ActiveRealTimeField'] = '1';
        }else{
            rowValues['ActiveRealTimeField'] = '0';
        }

        values.push(rowValues);
    });
    return values;
};