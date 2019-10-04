/* ********************************************************************************
 * The content of this file is subject to the Custom Expenses/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTECustomExpenses_Js", {
    instance: false,
    getInstance: function () {
        if (VTECustomExpenses_Js.instance == false) {
            var instance = new VTECustomExpenses_Js();
            VTECustomExpenses_Js.instance = instance;
            return instance;
        }
        return VTECustomExpenses_Js.instance;
    }
},{
    popupInstance: false,
    numOfCurrencyDecimals:false,
    currencyElement:false,
    taxTypeElement:false,
    regionElement:false,
    selectedRecords:false,
    registerEventAddCustomExpensesButton:function(){
        var self = this;
        var params = {};
        params['module'] = 'VTECustomExpenses';
        params['action'] = 'ActionAjax';
        params['mode'] = 'getExpenses';
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null){
                    var listViewEntries = data;
                    var modulesExpenses = '';
                    if(listViewEntries.length > 0){
                        listViewEntries.forEach(function(item){
                            modulesExpenses = modulesExpenses+'<li><input name="" value="" class="sourceField" data-displayvalue="" type="hidden"><a class="modules-Expenses" data-description="'+item.description+'" data-price="'+item.price+'" data-quantity="'+item.quantity+'" data-id="'+item.id+'" data-module="'+item.module+'" href="javascript:void(0)">'+item.custom_expenses_name+'</a></li>';
                        });
                    }
                    var form = $('#EditView');
                    var btn_toolbar = form.find('div.editViewBody div.editViewContents div.btn-toolbar');
                    var button = '<span style="margin-left: 10px;" class="dropdown">'+
                        '<button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown"><strong><i class="fa fa-plus"></i> Add Custom Expense</strong>'+
                        ' <span class="caret"></span></button>'+
                        '<ul class="dropdown-menu">'+
                        modulesExpenses+
                        '</ul>'+
                        '</span>';
                    btn_toolbar.append(button);
                    self.registerEventSelectModule();
                }
            },
            function(error) {
            }
        );

    },
    registerEventSelectModule:function(){
        var self = this;
        $('a.modules-Expenses').on('click',function(e){
            var currentElement = jQuery(e.currentTarget);
            var id = currentElement.data('id');
            var module = currentElement.data('module');
            var quantity  = currentElement.data('quantity');
            var price = currentElement.data('price');
            var description = currentElement.data('description');
            var src_field = currentElement.data('src_field');
            var contact = $('#EditView div.editViewContents td.fieldValue input[name="contact_id_display"]').val();
            var account = $('#EditView div.editViewContents td.fieldValue input[name="account_id_display"]').val();
            var params = {};
            params.module = 'VTECustomExpenses';
            params.selectedModule = module;
            params.src_module = 'Invoice';
            params.src_field = src_field;
            params.multi_select = true;
            params.contact = contact;
            params.account = account;
            params.view = 'Popup';

            var isMultiple = false;
            if(params.multi_select) {
                isMultiple = true;
            }

            var sourceFieldElement = currentElement.closest('li').find('input.sourceField');

            var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
            sourceFieldElement.trigger(prePopupOpenEvent);

            if(prePopupOpenEvent.isDefaultPrevented()) {
                return ;
            }
            var popupInstance = Vtiger_Popup_Js.getInstance();

            app.event.off(Vtiger_Edit_Js.popupSelectionEvent);
            app.event.one(Vtiger_Edit_Js.popupSelectionEvent,function(e,data) {
                var responseData = JSON.parse(data);
                var dataList = new Array();
                jQuery.each(responseData, function(key, value){
                    var counter = 0;
                    for(var valuekey in value){
                        if(valuekey == 'name') continue;
                        if(typeof valuekey == 'object') continue;
                        var data = {
                            'name' : value.name,
                            'id' : key
                        }
                        if(valuekey == 'info') {
                            data['name'] = value.name;
                        }
                        dataList.push(data);
                        if(!isMultiple && counter === 0) {
                            counter++;
                            Vtiger_Index_Js.setReferenceFieldValue(parentElem, data);
                        }
                    }
                });

                if(isMultiple) {
                    sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent,{'data':dataList});
                }
                sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent,{'data':responseData});
            });
            popupInstance.showPopup(params,Vtiger_Edit_Js.popupSelectionEvent,function() {

            });
            self.setPopupInstance(popupInstance);
        });
    },
    setPopupInstance:function(popupInstance){
        this.popupInstance = popupInstance;
    },
    getPopupInstance:function(){
        return this.popupInstance;
    },
    registerEventAddExpensesByAddButton:function(){
        var self = this;
        $('body').delegate('#vte-custom-expenses-select','click',function(){
            var popupInstance = self.getPopupInstance();
            var selectedRecordIds = popupInstance.getSelectedRecordIds();
            self.AddExpenses(selectedRecordIds);
        });
    },
    AddExpenses:function(selectedRecordIds){
        var self = this;
        var popupInstance = self.getPopupInstance();
        var selectedModuleName = popupInstance.getModuleName();
        var params = {};
        params['module'] = 'VTECustomExpenses';
        params['action'] = 'ActionAjax';
        params['mode'] = 'getFieldExpensesModuleRecord';
        params['selectedRecordIds'] = selectedRecordIds;
        params['selectedModuleName'] = selectedModuleName;
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null){
                    data.forEach(function(item){
                        self.addLineItemEventHandler(item);
                    });
                    self.hideModal();
                }
            },
            function(error) {
            }
        );
    },
    addLineItemEventHandler:function(item){
        if(item.default_item_module == 'Products'){
            $('#addProduct').trigger('click');
        }else{
            $('#addService').trigger('click');
        }
        this.registerLineItemAutoComplete(item);
    },
    addSelectedRecord:function(record){
        this.selectedRecords.push(record);
    },
    removeSelectedRecord:function(record){
        var index = this.selectedRecords.indexOf(record);
        this.selectedRecords.splice(index, 1);
    },
    registerLineItemAutoComplete:function(item){
        var self = this;
        var productDefault = item.productDefault;
        var proId = productDefault.id;
        var proLabel = productDefault.no+" - "+productDefault.name;
        var proValue = proLabel;
        var selectedItemData = {id: proId,label: proLabel, value: proValue};
        if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
            return false;
        }
        var row = $('#lineItemTab tr.lineItemRow:last-child');
        self.addSelectedRecord(item.record);
        var actionButton = row.find('td:first-child i.deleteRow');
        actionButton.attr('data-expenses-record',item.record);
        actionButton.on('click',function(e){
            var action_ele = jQuery(e.currentTarget);
            var expenses_record = action_ele.data('expenses-record');
            self.removeSelectedRecord(expenses_record);
        });
        var element = row.find('input.autoComplete');
        element.attr('disabled','disabled');
        var tdElement = element.closest('td');
        var selectedModule = tdElement.find('.lineItemPopup').data('moduleName');
        var popupElement = tdElement.find('.lineItemPopup');
        var dataUrl = "index.php?module=Inventory&action=GetTaxes&record="+selectedItemData.id+"&currency_id="+jQuery('#currency_id option:selected').val()+"&sourceModule="+app.getModuleName();
        app.request.get({'url':dataUrl}).then(
            function(error, data){
                if(error == null) {
                    var itemRow = element.closest('tr.lineItemRow');
                    itemRow.find('.lineItemType').val(selectedModule);
                    var Inventory = new Inventory_Edit_Js();
                    Inventory.mapResultsToFields(itemRow, data[0]);
                    itemRow.find('input.qty').val(item.quantity);
                    itemRow.find('input.listPrice').val(item.price);
                    var textarea = itemRow.find('textarea.lineItemCommentBox');
                    textarea.val(item.description);
                    itemRow.find('input.qty').trigger('focusout');
                }
            },
            function(error,err){

            }
        );
    },
    hideModal:function(){
        $('#popupModal').modal('hide');
    },
    init:function(){
        this.numOfCurrencyDecimals = parseInt(jQuery('.numberOfCurrencyDecimal').val());
        this.currencyElement = jQuery('#currency_id');
        this.taxTypeElement = jQuery('#taxtype');
        this.regionElement = jQuery('#region_id');
        this.selectedRecords = new Array();
    },
    registerEventSubmitForm:function(){
        var self = this;
        var form = jQuery('#EditView');
        var params = {
            submitHandler: function(form) {
                var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                app.event.trigger(e);
                if(e.isDefaultPrevented()) {
                    return false;
                }
                // to Prevent submit if already submitted
                if(this.numberOfInvalids() > 0) {
                    return false;
                }
                window.onbeforeunload = null;
                var records = self.selectedRecords;
                var params = {};
                params['module'] = 'VTECustomExpenses';
                params['action'] = 'ActionAjax';
                params['mode'] = 'setSelectedRecords';
                params['records'] = records;
                app.request.post({data:params}).then(
                    function(err,data) {
                        if(err == null){
                            return true;
                        }
                    },
                    function(error) {
                    }
                );
                return true;
            }
        };
        form.vtValidate(params);
    },
    registerEventAddExpensesByClickOnRecord:function(){
        var self = this;
        $('body').delegate('tr.vte-expenses-listViewEntries','click',function(e){
            var RecordId = $(this)[0].attributes['data-id'].value;
            var selectedRecordIds = new Array();
            selectedRecordIds.push(RecordId);
            self.AddExpenses(selectedRecordIds);
        });
    },
    registerEvents: function(){
        this.init();
        this.registerEventAddCustomExpensesButton();
        this.registerEventAddExpensesByAddButton();
        this.registerEventAddExpensesByClickOnRecord();
        this.registerEventSubmitForm();
    }
});

jQuery(document).ready(function () {
    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    if(moduleName == 'Invoice' && viewName == 'Edit'){
        var instance = new VTECustomExpenses_Js();
        instance.registerEvents();
    }
});
