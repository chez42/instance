/* ********************************************************************************
 * The content of this file is subject to the Related Record Update ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Settings_Workflows_Edit_Js.prototype.preSaveRelatedRecordUpdateTask = function(tasktype) {
    var values = this.getVTEValues(tasktype);
    jQuery('[name="field_value_mapping"]').val(JSON.stringify(values));
};

Settings_Workflows_Edit_Js.prototype.getRelatedRecordUpdateTaskFieldList = function() {
    return new Array('fieldname', 'related_fieldname');
};
Settings_Workflows_Edit_Js.prototype.getVTEValues = function(tasktype) {
    var thisInstance = this;
    var conditionsContainer = jQuery('#save_fieldvaluemapping');
    var fieldListFunctionName = 'get'+tasktype+'FieldList';
    if(typeof thisInstance[fieldListFunctionName] != 'undefined' ){
        var fieldList = thisInstance[fieldListFunctionName].apply()
    }

    var values = [];
    var conditions = jQuery('.mappingRow', conditionsContainer);
    conditions.each(function(i, conditionDomElement) {
        var rowElement = jQuery(conditionDomElement);
        var fieldSelectElement = jQuery('[name="fieldname"]', rowElement);
        var valueSelectElement = jQuery('[name="related_fieldname"]', rowElement);
        
        //To not send empty fields to server
        if(thisInstance.isEmptyFieldSelected(fieldSelectElement)) {
            return true;
        }
        if(thisInstance.isEmptyFieldSelected(valueSelectElement)) {
            return true;
        }
        var rowValues = {};
        rowValues['fieldname']=fieldSelectElement.find('option:selected').val();
        rowValues['related_fieldname']=valueSelectElement.find('option:selected').val();
        values.push(rowValues);
    });
    return values;
};

Settings_Workflows_Edit_Js.prototype.RelatedRecordUpdateTaskCustomValidation = function () {
    var result = true;
    return result;
};
Settings_Workflows_Edit_Js.prototype.registerRelatedRecordUpdateTaskEvents = function () {
    this.registerAddMappingButton();
    this.registerDeleteMappingEvent();
};
Settings_Workflows_Edit_Js.prototype.registerAddMappingButton = function () {
    var thisInstance=this;
    jQuery('#saveTask').on('click','#addMappingButton',function(e) {
        var newAddFieldContainer = jQuery('.basicAddFieldContainer').clone(true,true).removeClass('basicAddFieldContainer hide').addClass('mappingRow');
        jQuery('select',newAddFieldContainer).addClass('select2');
        jQuery('#save_fieldvaluemapping').append(newAddFieldContainer);
        //change in to chosen elements
        vtUtils.applyFieldElementsView(newAddFieldContainer);
        vtUtils.applyFieldElementsView(newAddFieldContainer.find('.select2'));
    });
};
Settings_Workflows_Edit_Js.prototype.registerDeleteMappingEvent = function () {
    jQuery('#saveTask').on('click','.deleteMappingButton',function(e) {
        jQuery(e.currentTarget).closest('.mappingRow').remove();
    })
};