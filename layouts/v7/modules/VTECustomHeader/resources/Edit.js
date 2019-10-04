/* ********************************************************************************
 * The content of this file is subject to the Custom Header/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTECustomHeader_Edit_Js",{
    instance:false,
    getInstance: function(){
        if(VTECustomHeader_Edit_Js.instance == false){
            var instance = new VTECustomHeader_Edit_Js();
            VTECustomHeader_Edit_Js.instance = instance;
            return instance;
        }
        return VTECustomHeader_Edit_Js.instance;
    }
},{
    registerEventSelectModule:function(){
        var self = this;
        $('#custom_module').on('change',function(){
            var element = $(this)
            var moduleSelected = element.val();
            var params = {
                module : 'VTECustomHeader',
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
    registerEventSelectIcons : function () {
        var modal = $("#ModalIcons");
        modal.find('.cell-icon').on('click',function () {
            var group = ".cell-icon";
            $(group).css("background", "#FFFFFF");
            $(group).removeClass("iconChecked");
            $(this).css("background", "cyan");
            $(this).addClass("iconChecked");
        })
        //submit icon module
        modal.find(".btn-submit").on('click', function () {
            var spanIcon  = modal.find('.iconChecked').find('span');
            var dataInfo = spanIcon.data('info');
            var classspanIcon = spanIcon.attr('class');
            var spanSelected = $('.icon-section').find('#icon-module');
            spanSelected.removeClass();
            spanSelected.addClass(classspanIcon);
            var class_icon = classspanIcon.replace('icon-module','');
            $('input[name="icon"]').val(class_icon.trim());
            $('.header-preview-section').find('span#icon-span').removeClass();
            $('.header-preview-section').find('span#icon-span').toggleClass(class_icon.trim());
            modal.modal('toggle');
        })
    },
    registerEventSetPickColor : function () {
        $('#header-colorpicker').ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                $(colpkr).fadeIn(500);
                return false;
            },
            onHide: function (colpkr) {
                $(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                //$('#header-colorpicker').find('div').css('background-color','#'+hex);
                $('#header-colorpicker p').css('backgroundColor', '#' + hex);
                $('.header-preview-section').find('span#icon-span').css('color', '#' + hex);
                //border: 2px solid #{$RECORDENTRIES['color']
                $('.header-preview-section').find('div.rcorners').css('border', '2px solid #' + hex);
                $('#color').val(hex);
            }
        });
    },
    registerHeaderChange : function () {
        $('#header').on("change",function(){
            $('span.l-header').text($(this).val());
        });
    },
    registerEvents: function(){
        this.registerEventSelectModule();
        this.registerFieldSelectChange();
        this.registerEventSelectIcons();
        this.registerEventSetPickColor();
        this.registerHeaderChange();

    }
});
jQuery(document).ready(function() {
    var instance = new VTECustomHeader_Edit_Js();
    instance.registerEvents();
});