/* ********************************************************************************
 * The content of this file is subject to the Custom Expenses/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTECustomExpenses_Edit_Js",{
    instance:false,
    getInstance: function(){
        if(VTECustomExpenses_Edit_Js.instance == false){
            var instance = new VTECustomExpenses_Edit_Js();
            VTECustomExpenses_Edit_Js.instance = instance;
            return instance;
        }
        return VTECustomExpenses_Edit_Js.instance;
    }
},{
    registerEventSelectModule:function(){
        var self = this;
        $('#custom_expenses_module').on('change',function(){
            var element = $(this)
            var moduleSelected = element.val();
            var params = {
                module : 'VTECustomExpenses',
                action : 'ActionAjax',
                mode : 'selectModule',
                'moduleSelected' : moduleSelected
            };
            AppConnector.request(params).then(
                function (data) {
                    //picklistField
                    var picklistField = $('#custom_expenses_quantity');
                    self.addValueForPickLists(picklistField,data);
                    var picklistField = $('#custom_expenses_price');
                    self.addValueForPickLists(picklistField,data);
                    var picklistField = $('#custom_expenses_description');
                    self.addValueForPickLists(picklistField,data);
                },
                function(error){

                }
            );
        });
    },
    addValueForPickLists:function(picklistField,data){
        picklistField.siblings('div').find('.select2-chosen').html('Select an Option');
        var result = data.result;
        var html = '<option value="" selected="selected">Select an Option</option>';
        $.each(result,function (fieldname,fieldlabel) {
            html += '<option value="'+fieldname+'">'+fieldlabel+'</option>';
        });
        picklistField.html(html);
    },
    registerEventSelectDefaultItem:function(){
        var self = this;
        $('#custom_expenses_default_item_module').on('change',function(){
            var module = this.value;
            var popupReferenceModule = $('input[name="popupReferenceModule"]');
            popupReferenceModule.val(module);
            $('input[name="custom_expenses_default_item_value"]').val('');
            $('input[name="custom_expenses_default_item_value_display"]').val('');
        });
    },
    registerEvents: function(){
        this.registerEventSelectModule();
        this.registerEventSelectDefaultItem();
    }
});
jQuery(document).ready(function() {
    var instance = new VTECustomExpenses_Edit_Js();
    instance.registerEvents();
});