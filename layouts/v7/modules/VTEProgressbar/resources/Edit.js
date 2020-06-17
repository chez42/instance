/* ********************************************************************************
 * The content of this file is subject to the Progressbar/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEProgressbar_Edit_Js",{
    instance:false,
    getInstance: function(){
        if(VTEProgressbar_Edit_Js.instance == false){
            var instance = new VTEProgressbar_Edit_Js();
            VTEProgressbar_Edit_Js.instance = instance;
            return instance;
        }
        return VTEProgressbar_Edit_Js.instance;
    }
},{
    registerEventSelectModule:function(){
        var self = this;
        $('#custom_module').on('change',function(){
            var element = $(this)
            var moduleSelected = element.val();
            var params = {
                module : 'VTEProgressbar',
                view : 'RelatedFields',
                record : $('#record').val(),
                moduleSelected : moduleSelected
            };
            AppConnector.request(params).then(
                function (data) {
                    //picklistField
                    var picklistField = $('#field_name');
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
        picklistField.html(result);
    },
    registerFieldSelectChange:function(){
        var self = this;
        $('#field_name').on('change',function(e){
            //var field_name = this.text.split(") ");
            var theSelection = $('#field_name').select2('data').text;
            var field_name = theSelection.split(") ");
            var popupReferenceModule = $('.l-value');
            popupReferenceModule.text(field_name[1]);
        });
    },
    registerEvents: function(){
        this.registerEventSelectModule();
        this.registerFieldSelectChange();
    }
});
jQuery(document).ready(function() {
    var instance = new VTEProgressbar_Edit_Js();
    instance.registerEvents();
});