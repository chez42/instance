/* ********************************************************************************
 * The content of this file is subject to the Item Lookup ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEItemLookup_Js", {
    instance: false,
    getInstance: function () {
        if (VTEItemLookup_Js.instance == false) {
            var instance = new VTEItemLookup_Js();
            VTEItemLookup_Js.instance = instance;
            return instance;
        }
        return VTEItemLookup_Js.instance;
    }
},{

    default_module : false,
    product_bundles : 0,
    hide_add_product_button : false,
    hide_add_service_button : false,
    product_filter_field_1 : false,
    product_filter_field_2 : false,
    product_filter_field_3 : false,
    product_show_inactive_filter : false,
    product_show_instock_filter : false,
    product_show_picture_column : false,
    product_show_picture_size_height : false,
    product_show_picture_size_width : false,
    service_filter_field_1 : false,
    service_filter_field_2 : false,
    service_filter_field_3 : false,
    service_show_inactive_filter : false,

    popupInstance: false,
    numOfCurrencyDecimals:false,
    currencyElement:false,
    taxTypeElement:false,
    regionElement:false,
    selectedRecords:false,

    setPopupInstance:function(popupInstance){
        this.popupInstance = popupInstance;
    },
    getConfigureAndInit:function(){
        var self = this;
        var params = {};
        params['module'] = 'VTEItemLookup';
        params['action'] = 'ActionAjax';
        params['mode'] = 'getConfigure';
        app.request.post({data:params}).then(
            function(err,result) {
                if(err == null){
                    var data = result.data;
                    // init
                    self.default_module = data.default_module;
                    self.product_bundles = data.product_bundles;
                    self.hide_add_product_button = data.hide_add_product_button;
                    self.hide_add_service_button = data.hide_add_service_button;
                    self.product_filter_field_1 = data.product_filter_field_1;
                    self.product_filter_field_2 = data.product_filter_field_2;
                    self.product_filter_field_3 = data.product_filter_field_3;
                    self.product_show_inactive_filter = data.product_show_inactive_filter;
                    self.product_show_instock_filter = data.product_show_instock_filter;
                    self.product_show_picture_column = data.product_show_picture_column;
                    self.product_show_picture_size_height = data.product_show_picture_size_height;
                    self.product_show_picture_size_width = data.product_show_picture_size_width;
                    self.service_filter_field_1 = data.service_filter_field_1;
                    self.service_filter_field_2 = data.service_filter_field_2;
                    self.service_filter_field_3 = data.service_filter_field_3;
                    self.service_show_inactive_filter = data.service_show_inactive_filter;
                    //run
                    self.addItemLookUpButton();
                    self.hideAddProductButton();
                    self.hideAddServiceButton();
                }
            },
            function(error) {
            }
        );
    },
    addItemLookUpButton:function(){
        var self = this;
        var form = $('#EditView');
        var btn_group = form.find('div.editViewBody div.editViewContents .btn-group');
        var btn_toolbar = btn_group.parent();
        var params = {
            module:'VTEItemLookup',
            view:'LookUpButton'
        };
        app.request.get({data:params}).then(
            function(err,result) {
                if(err == null){
                    btn_toolbar.append(result);
                    self.registerEventForLookUpButton();
                }
            },
            function(error) {
            }
        );
    },
    openItemLookUpModal:function(){
        var self = this;
        var default_module = self.default_module;
        var product_bundles = self.product_bundles;
        var params = {};
        params.module = 'VTEItemLookup';
        params.current_selected_item_modlue = default_module;
        params.product_bundles = product_bundles;
        params.view = 'Popup';
        params.multi_select = true;
        var sourceFieldElement = $('input.sourceField');
        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if(prePopupOpenEvent.isDefaultPrevented()) {
            return ;
        }
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params,Vtiger_Edit_Js.popupSelectionEvent,function() {
            var  viewPortHeight= $(window).height()-120;
            var params = {setHeight: (viewPortHeight)+'px'};
            var params2 = {setHeight: (viewPortHeight-125)+'px'};
            var params2_1 = {setHeight: (viewPortHeight-100)+'px'};
            app.helper.showVerticalScroll(jQuery('#itemLookUpPopupModal').find('.modal-body'), params);
            app.helper.showVerticalScroll(jQuery('#itemLookUpPopupModal').find('.lockup-item-main'), params2_1);
            app.helper.showVerticalScroll(jQuery('#itemLookUpPopupModal').find('.popupFillContainer_filter_fields_scroll'), params2);
            var container = jQuery('.iTL-listViewEntriesTable');
            var thead_h = container.find('thead').height();
            var params3 = {setHeight: (viewPortHeight-125-thead_h)+'px'};
            app.helper.showVerticalScroll(container.find('tbody'), params3);
        });
        self.setPopupInstance(popupInstance);

    },
    registerEventForLookUpButton: function(){
        var self = this;
        $('#item_lookup_button').on('click',function(){
            self.openItemLookUpModal();
        });
    },
    hideAddProductButton:function(){
        if(this.hide_add_product_button == 1){
            $('#addProduct').hide();
        }
    },
    hideAddServiceButton:function(){
        if(this.hide_add_service_button == 1){
            $('#addService').hide();
        }
    },
    registerEvents: function(){
        this.getConfigureAndInit();
    }
});

jQuery(document).ready(function () {
    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    if((moduleName == 'Invoice' || moduleName == 'PurchaseOrder' || moduleName == 'SalesOrder' || moduleName == 'Quotes') && viewName == 'Edit'){
        var instance = new VTEItemLookup_Js();
        instance.registerEvents();
    }
});
