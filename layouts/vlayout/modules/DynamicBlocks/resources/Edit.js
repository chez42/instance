/* ********************************************************************************
 * The content of this file is subject to the Dynamic Blocks ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
jQuery.Class("DynamicBlocks_Edit_Js",{
    editInstance:false,
    getInstance: function(){
        if(DynamicBlocks_Edit_Js.editInstance == false){
            var instance = new DynamicBlocks_Edit_Js();
            DynamicBlocks_Edit_Js.editInstance = instance;
            return instance;
        }
        return DynamicBlocks_Edit_Js.editInstance;
    }
},{
    registerSelectModulesEvent: function (container) {
        var thisInstance=this;
        container.find('#modulesList').on('change', function(e){
            var progressIndicatorElement = jQuery.progressIndicator();
            var selectedVal=jQuery(e.currentTarget).val();
            var url ='index.php?module=DynamicBlocks&view=MassActionAjax&mode=getFieldBlocks';
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {
                    'source_module':selectedVal
                }
            };

            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var massEditForm = container.find("#field_blocks");
                    massEditForm.html(data);
                    app.changeSelectElementView(massEditForm);
                    app.showSelect2ElementView(massEditForm.find('select.select2'));
                }
            );
        });
    },

    registerSelectFieldEvent :function(container) {
        var thisInstance=this;
        container.on('change','#modulePickList', function(e){
            var progressIndicatorElement = jQuery.progressIndicator();
            var source_module=container.find('#modulesList').val();
            var fieldname=jQuery(e.currentTarget).val();
            var record=container.find('input[name="record"]').val();
            var arrModule=container.find('#arrModule').val();
            var objModuleFields = jQuery.parseJSON(arrModule);
            var selectedModule=container.find('input[name="selectedModule"]').val();
            if(objModuleFields.length >0) {
                if (((record == '' && objModuleFields[source_module].length > 0) || (record != '' && objModuleFields[source_module].length > 1)) && objModuleFields[source_module][0] != fieldname) {
                    alert("You can only configure one field per module.");
                }
            }
            var url ='index.php?module=DynamicBlocks&view=MassActionAjax&mode=getPickListValues';
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"html",
                "data" : {
                    'source_module':source_module,
                    'fieldname':fieldname
                }
            };

            AppConnector.request(actionParams).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    var massEditForm = container.find("#picklistValues");
                    massEditForm.html(data);
                    app.changeSelectElementView(massEditForm);
                    app.showSelect2ElementView(massEditForm.find('select.select2'));
                }
            );
        });
    },
    registerFormSubmitEvent: function(container) {
        container.on('submit', function(e) {
            var source_module=container.find('#modulesList').val();
            var fieldname=container.find('#modulePickList').val();
            var record=container.find('input[name="record"]').val();
            var arrModule=container.find('#arrModule').val();
            var objModuleFields = jQuery.parseJSON(arrModule);
            var selectedModule=container.find('input[name="selectedModule"]').val();

            if(objModuleFields.length >0) {
                if(((record == '' && objModuleFields[source_module].length>0) || (record != '' && objModuleFields[source_module].length>1)) && objModuleFields[source_module][0] != fieldname){
                    alert("You can only configure one field per module.");
                    e.preventDefault();
                }
            }
        });
    },
    registerEvents : function() {
        var container=jQuery('#dynamicblocks_form');
        this.registerSelectModulesEvent(container);
        this.registerSelectFieldEvent(container);
        this.registerFormSubmitEvent(container);

    }
});

jQuery(document).ready(function() {
    var instance = new DynamicBlocks_Edit_Js();
    instance.registerEvents();
});
