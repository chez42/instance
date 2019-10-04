/* ********************************************************************************
 * The content of this file is subject to the Rollup/Calculate Fields ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Settings_Workflows_Edit_Js.prototype.preSaveVTERollupTask = function(tasktype) {
    var values = this.getVTERollupValues(tasktype);
    jQuery('[name="field_value_mapping"]').val(JSON.stringify(values));
};

Settings_Workflows_Edit_Js.prototype.getVTERollupTaskFieldList = function() {
    return new Array('fieldname', 'related_fieldname');
};
Settings_Workflows_Edit_Js.prototype.getVTERollupValues = function(tasktype) {
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
        var targetFieldElement = jQuery('[name="target_field"]', rowElement);
        var sourceFieldElement = jQuery('[name="source_field"]', rowElement);
        var methodElement = jQuery('[name="method_field"]', rowElement);

        //To not send empty fields to server
        if(thisInstance.isEmptyFieldSelected(targetFieldElement)) {
            return true;
        }
        if(thisInstance.isEmptyFieldSelected(sourceFieldElement)) {
            return true;
        }
        if(thisInstance.isEmptyFieldSelected(methodElement)) {
            return true;
        }
        var rowValues = {};
        rowValues['target_field']=targetFieldElement.find('option:selected').val();
        rowValues['source_field']=sourceFieldElement.find('option:selected').val();
        rowValues['method_field']=methodElement.find('option:selected').val();
        values.push(rowValues);
    });
    return values;
};

Settings_Workflows_Edit_Js.prototype.RollupVTERollupCustomValidation = function () {
    var result = true;
    return result;
};
Settings_Workflows_Edit_Js.prototype.registerVTERollupTaskEvents = function () {
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