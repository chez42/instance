/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_Popup_Js("VTEItemLookup_Popup_Js",{},{
    selectedRecordItem : new Array(),
    getAnphabetFilterButtonValue : function() {
        return this.getPopupPageContainer().find('#selectedAnphabet').val();
    },
    getCurrentSelectedItemModlue : function() {
        return this.getPopupPageContainer().find('#current_selected_item_modlue').val();
    },
    get_product_bundles : function() {
        return this.getPopupPageContainer().find('#product_bundles').val();
    },
    get_product_filter_field_1_value : function() {
        return this.getPopupPageContainer().find('#product_filter_field_1_value').val();
    },
    get_product_filter_field_2_value : function() {
        return this.getPopupPageContainer().find('#product_filter_field_2_value').val();
    },
    get_product_filter_field_3_value : function() {
        return this.getPopupPageContainer().find('#product_filter_field_3_value').val();
    },
    get_service_filter_field_1_value : function() {
        return this.getPopupPageContainer().find('#service_filter_field_1_value').val();
    },
    get_service_filter_field_2_value : function() {
        return this.getPopupPageContainer().find('#service_filter_field_2_value').val();
    },
    get_service_filter_field_3_value : function() {
        return this.getPopupPageContainer().find('#service_filter_field_3_value').val();
    },
    get_product_show_instock_filter : function() {
        return this.getPopupPageContainer().find('#product_show_instock_filter').val();
    },
    get_product_show_inactive_filter : function() {
        return this.getPopupPageContainer().find('#product_show_inactive_filter').val();
    },
    get_product_show_bundles_filter : function() {
        return this.getPopupPageContainer().find('#product_show_bundles_filter').val();
    },
    get_service_show_inactive_filter : function() {
        return this.getPopupPageContainer().find('#service_show_inactive_filter').val();
    },
    get_products_get_bundles_id : function() {
        return this.getPopupPageContainer().find('#products_get_bundles_id').val();
    },
    getModuleName : function() {
        return this.getPopupPageContainer().find('#vte_module').val();
    },
    /**
     * Function to get complete params
     */
    getCompleteParams : function(){
        var params = {};
        params['view'] = this.getView();
        params['src_module'] = this.getSourceModule();
        params['src_record'] = this.getSourceRecord();
        params['src_field'] = this.getSourceField();
        params['search_key'] =  this.getSearchKey();
        params['search_value'] =  this.getSearchValue();
        params['orderby'] =  this.getOrderBy();
        params['sortorder'] =  this.getSortOrder();
        params['page'] = this.getPageNumber();
        params['related_parent_module'] = this.getRelatedParentModule();
        params['related_parent_id'] = this.getRelatedParentRecord();
        params['module'] = this.getModuleName();
        params['anphabetFilter'] = this.getAnphabetFilterButtonValue();
        params['current_selected_item_modlue'] = this.getCurrentSelectedItemModlue();
        params['product_bundles'] = this.get_product_bundles();
        params['product_filter_field_1_value'] = this.get_product_filter_field_1_value();
        params['product_filter_field_2_value'] = this.get_product_filter_field_2_value();
        params['product_filter_field_3_value'] = this.get_product_filter_field_3_value();
        params['service_filter_field_1_value'] = this.get_service_filter_field_1_value();
        params['service_filter_field_2_value'] = this.get_service_filter_field_2_value();
        params['service_filter_field_3_value'] = this.get_service_filter_field_3_value();

        params['product_show_instock_filter'] = this.get_product_show_instock_filter();
        params['product_show_inactive_filter'] = this.get_product_show_inactive_filter();
        params['product_show_bundles_filter'] = this.get_product_show_bundles_filter();
        params['service_show_inactive_filter'] = this.get_service_show_inactive_filter();
        params['products_get_bundles_id'] = this.get_products_get_bundles_id();

        params.search_params = JSON.stringify(this.getPopupListSearchParams());
        if(this.isMultiSelectMode()) {
            params['multi_select'] = true;
        }
        params['relationId'] = this.getRelationId();
        return params;
    },
    getPopupListSearchParams : function(){
        var listViewPageDiv = jQuery('div.iTL-popupEntriesDiv');
        var listViewTable = listViewPageDiv.find('.listViewEntriesTable');
        var searchParams = new Array();
        var currentSearchParams = new Array();
        if(jQuery('#currentSearchParams').val())
            currentSearchParams = JSON.parse(jQuery('#currentSearchParams').val());
        listViewTable.find('.listSearchContributor').each(function(index,domElement){
            var searchInfo = new Array();
            var searchContributorElement = jQuery(domElement);
            var fieldName = searchContributorElement.attr('name');
            var fieldInfo = searchContributorElement.data('fieldinfo');
            if(fieldName in currentSearchParams) {
                delete currentSearchParams[fieldName];
            }

            var searchValue = searchContributorElement.val();

            if(typeof searchValue == "object") {
                if(searchValue == null) {
                    searchValue = "";
                }else{
                    searchValue = searchValue.join(',');
                }
            }
            searchValue = searchValue.trim();
            if(searchValue.length <=0 ) {
                //continue
                return true;
            }
            var searchOperator = 'c';
            if(fieldInfo.type == "date" || fieldInfo.type == "datetime") {
                searchOperator = 'bw';
            }else if (fieldInfo.type == 'percentage' || fieldInfo.type == "double" || fieldInfo.type == "integer"
                || fieldInfo.type == 'currency' || fieldInfo.type == "number" || fieldInfo.type == "boolean" ||
                fieldInfo.type == "picklist") {
                searchOperator = 'e';
            }
            searchInfo.push(fieldName);
            searchInfo.push(searchOperator);
            searchInfo.push(searchValue);
            searchParams.push(searchInfo);
        });
        for(var i in currentSearchParams) {
            var fieldName = currentSearchParams[i]['fieldName'];
            var searchValue = currentSearchParams[i]['searchValue'];
            var searchOperator = currentSearchParams[i]['comparator'];
            if(fieldName== null || fieldName.length <=0 ){
                continue;
            }
            var searchInfo = new Array();
            searchInfo.push(fieldName);
            searchInfo.push(searchOperator);
            searchInfo.push(searchValue);
            searchParams.push(searchInfo);
        }
        return new Array(searchParams);
    },

    changeItemFilterHandler : function(){
        var self = this;
        var aDeferred = jQuery.Deferred();
        var popupContainer = this.getPopupPageContainer();
        var nextPageNumber = 1;
        var pagingParams = {
            "page": nextPageNumber
        }
        var completeParams = this.getCompleteParams();
        jQuery.extend(completeParams,pagingParams);
        this.getPageRecords(completeParams).then(
            function(data){
                jQuery('#pageNumber',popupContainer).val(nextPageNumber);
                aDeferred.resolve(data);
                self.updatePagination();
                self.highlightSelectedRecord();
                self.registerEventForProductImage();
                self.registerEventFixWithColumn();
                app.helper.hideProgress();
                self.updatePopUpContenTableHeight();
            }
        );
        return aDeferred.promise();
    },
    updatePopUpContenTableHeight:function(){
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
    },
    clearLeftFilter:function(){
        $('#product_filter_field_1_value').val('');
        $('#product_filter_field_2_value').val('');
        $('#product_filter_field_3_value').val('');
        $('#service_filter_field_1_value').val('');
        $('#service_filter_field_2_value').val('');
        $('#service_filter_field_3_value').val('');
        $('#product_show_instock_filter').val('');
        $('#product_show_inactive_filter').val('');
        $('#product_show_bundles_filter').val('');
        $('#products_get_bundles_id').val('');
        $('#service_show_inactive_filter').val('');
        var inputs = $('input.checkbox-item-module-filter-fields')
        inputs.each(function(k,item){item.checked = false});
        var inputs = $('input.checkbox-miscellaneous');
        inputs.each(function(k,item){item.checked = false});
        var inputs = $('input.checkbox-prod-get-sub');
        inputs.each(function(k,item){item.checked = false});
    },
    changeItemModuleHandler : function(value){
        var self = this;
        var aDeferred = jQuery.Deferred();
        var popupContainer = this.getPopupPageContainer();
        var nextPageNumber = 1;
        var pagingParams = {
            "page": nextPageNumber
        }

        self.clearLeftFilter();
        $('#currentSearchParams').val('');
        $('#selectedAnphabet').val('');
        var search_inputs = $('input.listSearchContributor');
        search_inputs.each(function(k,item){
            item.value='';
        });
        var completeParams = this.getCompleteParams();
        jQuery.extend(completeParams,pagingParams);
        completeParams.view = 'LeftFilterAjax';
        completeParams.search_params = '';
        completeParams.anphabetFilter = '';
        app.request.get({'data':completeParams}).then(
            function(err,data){
                if(err === null) {
                    $('#popupFillContainer_filter_fields').html(data);
                    self.changeItemFilterHandler();
                    self.registerEventForProductImage();
                }else{

                }
            }
        );
        return aDeferred.promise();


    },
    getPopupContainer:function(){
        var PopupContainer =  jQuery('#itemLookUpPopupContainer');
        return PopupContainer;
    },
    registerEventForAnphabetButton:function(){
        var self = this;
        var popupPageContentsContainer = self.getPopupContainer();
        popupPageContentsContainer.on('click','button.anphabet-filter-button', function(e){
            var element = jQuery(e.currentTarget);
            var value = element.data('value');
            if(value == 'all'){
                $('#selectedAnphabet').val('all');
                $('input.listSearchContributor[name="productname"]').val('');
            }else{
                $('#selectedAnphabet').val(value);
            }
            self.changeItemFilterHandler();
        });
    },
    registerEventForButtonChangeItemModule:function(){
        var self = this;
        var popupPageContentsContainer = self.getPopupContainer();
        popupPageContentsContainer.on('click','button.button-change-item-module', function(e){
            var element = jQuery(e.currentTarget);
            var value = element.data('module');
            var bundles = element.data('bundles');
            $('#current_selected_item_modlue').val(value);
            if(bundles > 0){
                $('#product_bundles').val(bundles);
            }else{
                $('#products_get_bundles_id').val('');
                $('#product_bundles').val(0);
            }
            $('button.button-change-item-module').attr('class','button-change-item-module btn btn-default');
            $(this).attr('class','button-change-item-module btn btn-primary');
            self.changeItemModuleHandler(value);
            self.updatePopUpContenTableHeight();
        });
    },
    recalculationOtherFilterField:function(e){
        var element = jQuery(e.currentTarget);
        var value = element.data('filter-value');
        var itemModule = element.data('filter-module');
        var field = element.data('filter-field');
        var filterName = element.data('name');
        var caculte = element[0].checked;
        var completeParams = this.getCompleteParams();
        var data = {};
        data['module'] = 'VTEItemLookup';
        data['action'] = 'ActionAjax';
        data['mode'] = 'recalculationOtherFilterField';
        data['value'] = value;
        data['current_selected_item_modlue'] = itemModule;
        data['field'] = field;
        data['filterName'] = filterName;
        data['caculte'] = caculte;
        if(itemModule == 'Products'){
            data['product_filter_field_1_value'] = completeParams.product_filter_field_1_value;
            data['product_filter_field_2_value'] = completeParams.product_filter_field_2_value;
            data['product_filter_field_3_value'] = completeParams.product_filter_field_3_value;
        }else if(itemModule == 'Services'){
            data['service_filter_field_1_value'] = completeParams.service_filter_field_1_value;
            data['service_filter_field_2_value'] = completeParams.service_filter_field_2_value;
            data['service_filter_field_3_value'] = completeParams.service_filter_field_3_value;
        }
        app.request.post({data:data}).then(
            function (err,result) {
                if(err == null){
                    var data = result.data;
                    var span = $('.item-lookup-field-value-count');
                    span.each(function(k,item){
                        var module = $(item).data('module');
                        var value = $(item).data('value');
                        var filter = $(item).data('filter');
                        var count = data[module][filter][value];
                        if(count > 0){
                            $(item).html('('+data[module][filter][value]+')');
                            if(filterName != filter){
                                $(item).closest('li').show();
                            }
                        }else{
                            $(item).html('(0)');
                            if(filterName != filter){
                                $(item).closest('li').hide();
                            }
                        }
                    });
                }
            }
        );
    },
    registerEventForItemModuleCheckBoxSelect:function(){
        var self = this;
        var popupPageContentsContainer = self.getPopupContainer();
        popupPageContentsContainer.on('click','input.checkbox-item-module-filter-fields', function(e){
            var element = jQuery(e.currentTarget);
            var fieldName = element.data('name');
            var checkboxes = $('input[data-name="'+fieldName+'"]');
            var values = '';
            checkboxes.each(function(key,item){
                if(item.checked == true){
                    values = values == '' ? item.value : values+','+item.value;
                }
            });
            $('#'+fieldName+'_value').val(values);
            self.changeItemFilterHandler();
            self.recalculationOtherFilterField(e);

        });
        popupPageContentsContainer.on('click','input.checkbox-prod-get-sub', function(e){
            var checkboxes = $('input.checkbox-prod-get-sub');
            var values = '';
            checkboxes.each(function(key,item){
                if(item.checked == true){
                    values = values == '' ? item.value : values+','+item.value;
                }
            });
            $('#products_get_bundles_id').val(values);
            self.changeItemFilterHandler();
        });
        popupPageContentsContainer.on('click','input.checkbox-miscellaneous', function(e){
            var element = jQuery(e.currentTarget);
            var fieldName = element.data('name');
            var checked = element[0].checked;
            if(checked == true){
                $('#'+fieldName).val(1);
            }else{
                $('#'+fieldName).val('');
            }
            self.changeItemFilterHandler();
        });
    },
    clearAnphabetFilter:function(){
        $('#selectedAnphabet').val('all');
        $('input.listSearchContributor[name="productname"]').val('');
    },
    clearStandarFilter:function(){
        $('#currentSearchParams').val('');
        var search_inputs = $('input.listSearchContributor');
        search_inputs.each(function(k,item){
            item.value='';
        });
    },
    registerEventForButtonClearLeftFilterItem:function(){
        var self = this;
        var popupPageContentsContainer = self.getPopupContainer();
        popupPageContentsContainer.on('click','button.button-clear-left-filter-item', function(e){
            self.clearLeftFilter();
            self.clearAnphabetFilter();
            self.clearStandarFilter();
            self.changeItemFilterHandler();
        });
    },
    registerEventForProductImage:function(){
        $('.itemLookUp-product-image').ibox();
    },
    pushSelectedRecordToStack:function(recordId,rowId,qty,price){
        this.selectedRecordItem.push({'row':rowId,'record':recordId,'qty':qty, 'price':price});
    },
    addItem:function(e,tr,module){
        var self = this;
        e.preventDefault();
        var preEvent = jQuery.Event('pre.popupSelect.click');
        app.event.trigger(preEvent);
        if(preEvent.isDefaultPrevented()){
            return;
        }
        var thisInstance = this;
        var dataUrl = tr.data('url');
        var recordId = tr.data('id');
        var qty = tr.find('input.add-item-qty').val();
        var price = tr.find('input.add-item-price').val();
        if(typeof dataUrl != 'undefined'){
            dataUrl = dataUrl+'&currency_id='+jQuery('#currencyId').val();
            app.request.post({"url":dataUrl}).then(
                function(err,data){
                    if(module == 'Products'){
                        $('#addProduct').trigger('click');
                    }else{
                        $('#addService').trigger('click');
                    }
                    for(var id in data){
                        if(typeof data[id] == "object"){
                            var recordData = data[id];
                        }
                    }
                    var row = $('#lineItemTab tr.lineItemRow:last-child');
                    var rowId = row[0].id;
                    var element = row.find('input.autoComplete');
                    var itemRow = element.closest('tr.lineItemRow');
                    var tdElement = element.closest('td');
                    var selectedModule = tdElement.find('.lineItemPopup').data('moduleName');
                    itemRow.find('.lineItemType').val(selectedModule);
                    var Inventory = new Inventory_Edit_Js();
                    Inventory.mapResultsToFields(itemRow, data[0]);
                    self.pushSelectedRecordToStack(recordId,rowId,qty,price);
                    row.find('input.qty.inputElement').val(qty);
                    var decimalSeparator = $('#decimalSeparator').val();
                    var digitGroupingSeparator = $('#digitGroupingSeparator').val();
                    price = price.replace(digitGroupingSeparator, '');
                    price = price.replace(decimalSeparator, '.');
                    price = price.replace(/[^0-9\.]/g, '');
                    row.find('input.listPrice.inputElement').val(price);
                    row.find('input.listPrice.inputElement').focus().focusout();
                    self.mapFieldValueFromQuoter(recordId,module,row);
                    self.removeFirstBlankRow();
                });
            e.preventDefault();
        } else {

        }
    },
    removeFirstBlankRow:function(){
        var first_row = $('#row1.lineItemRow');
        var first_input_name = first_row.find('input[name="productName1"]');
        if(first_input_name != undefined && first_input_name.val() == ''){
            first_row.find('i.deleteRow').trigger('click');
        }
    },
    mapFieldValueFromQuoter:function(recordId,module,row){
        var quoter = jQuery("script[src*='Quoter.js']");
        if(quoter.length > 0){
            var quoterInstance = new Quoter_Js();
            if(quoterInstance != undefined){
                var ids = [];
                ids.push(recordId);
                var targetModule = app.getModuleName();
                if (targetModule == 'PSTemplates') {
                    targetModule = jQuery('[name="target_module"]').val();
                }
                var itemModule = module;
                var newParams = {
                    module: 'Quoter',
                    action: 'ActionAjax',
                    mode: 'getCustomFieldValue',
                    targetModule: targetModule,
                    currency_id: jQuery('#currency_id').val(),
                    record: ids,
                    viewType: itemModule+'Popup'
                };
                AppConnector.request(newParams).then(
                    function (data) {
                        var customValues = data.result;
                        var referenceModule = itemModule;
                        jQuery.each(customValues, function (i, customValue) {
                            jQuery.each(customValue, function (name, value) {
                                if(name != 'item_name'){
                                    row.find('input.'+name+'.inputElement').val(value);
                                    row.find('input.listPrice.inputElement').trigger('change');
                                }
                            });
                        });
                    }
                );
            }
        }

        row.find('input.listPrice.inputElement').trigger('change');
    },
    removeItem:function(recordID){
        var self = this;
        var selectedRecords = this.selectedRecordItem;
        selectedRecords.forEach(function(item,key){
            if(item.record == recordID){
                var tr = $('tr#'+item.row);
                tr.find('.deleteRow').trigger('click');
                self.selectedRecordItem[key] = '';
            }
        });
    },
    registerEventForAddAnItem:function(){
        var self = this;
        var popupPageContentsContainer = self.getPopupPageContainer();
        popupPageContentsContainer.off('click', '.vteLookUpAddAnItem');
        popupPageContentsContainer.on('click','.vteLookUpAddAnItem',function(e){
            var tr = $(this).closest('tr');
            var td = $(this).closest('td');
            var checkbox = tr.find('input[type="checkbox"]');
            var module = $(this).data('item-module');
            var value = $(this).data('item-value');
            var actionInput = td.find('input.action');
            var action = actionInput.val();
            if(action == 'add'){
                actionInput.val('remove');
                tr.css('background-color','#d3ffd5');
                checkbox[0].checked = false;
                self.addItem(e,tr,module);
                app.helper.showSuccessNotification({message:value+' has been added'});
                $(this).html('Remove');
                tr.find('input.entryCheckBox[type="checkbox"]').attr('disabled','disabled');
            }else{
                actionInput.val('add');
                tr.css('background-color','#fff');
                var recordID = tr.data('id');
                self.removeItem(recordID);
                app.helper.showSuccessNotification({message:value+' has been removed'});
                $(this).html('Add');
                tr.find('input.entryCheckBox[type="checkbox"]').removeAttr('disabled');
            }
        });
    },
    registerEventForAddAllRecord:function(){
        var self = this;
        var thisInstance = this;
        var popupPageContentsContainer = self.getPopupPageContainer();
        popupPageContentsContainer.off('click', '#item_lookup_select');
        popupPageContentsContainer.on('click','#item_lookup_select',function(e){
            var all_filter_record_id = $('#all_filter_record_id').val();
            var arr = all_filter_record_id.split(',');
            var module = $('#current_selected_item_modlue').val();
            arr.forEach(function(id){
                var dataUrl = 'index.php?module=Inventory&action=GetTaxes&record='+id;
                dataUrl = dataUrl+'&currency_id='+jQuery('#currencyId').val();
                var recordId = id;
                var qty = $('tr.itemLookUp-listViewEntries[data-id="'+recordId+'"] input.add-item-qty').val();
                var price = $('tr.itemLookUp-listViewEntries[data-id="'+recordId+'"] input.add-item-price').val();
                app.request.post({"url":dataUrl}).then(
                    function(err,data){
                        if(module == 'Products'){
                            $('#addProduct').trigger('click');
                        }else{
                            $('#addService').trigger('click');
                        }
                        var row = $('#lineItemTab tr.lineItemRow:last-child');
                        var rowId = row[0].id;
                        var element = row.find('input.autoComplete');
                        var itemRow = element.closest('tr.lineItemRow');
                        var tdElement = element.closest('td');
                        var selectedModule = tdElement.find('.lineItemPopup').data('moduleName');
                        itemRow.find('.lineItemType').val(selectedModule);
                        var Inventory = new Inventory_Edit_Js();
                        Inventory.mapResultsToFields(itemRow, data[0]);
                        self.pushSelectedRecordToStack(recordId,rowId,qty,price);
                        row.find('input.qty.inputElement').val(qty);
                        var decimalSeparator = $('#decimalSeparator').val();
                        var digitGroupingSeparator = $('#digitGroupingSeparator').val();
                        price = price.replace(digitGroupingSeparator, '');
                        price = price.replace(decimalSeparator, '.');
                        price = price.replace(/[^0-9\.]/g, '');
                        row.find('input.listPrice.inputElement').val(price);
                        row.find('input.listPrice.inputElement').focus().focusout();
                        self.highlightSelectedRecord();
                        self.mapFieldValueFromQuoter(recordId,module,row);
                });
            });
            self.removeFirstBlankRow();
        });
    },

    registerPostPopupLoadEvents : function(){
        //var popupContainer = jQuery('#popupModal');
        //var Options= {
        //    axis:"x",
        //    scrollInertia: 200,
        //    mouseWheel:{ enable: false }
        //};
        //app.helper.showVerticalScroll(popupContainer.find('.popupEntriesDiv'), Options);
    },
    highlightSelectedRecord:function(){
        var self = this;
        var selected = self.selectedRecordItem;
        selected.forEach(function(item){
            var record = item.record;
            var qty = item.qty;
            var price = item.price;
            var tr = $('tr.itemLookUp-listViewEntries[data-id="'+record+'"]');
            var actionInput = tr.find('input.action');
            var addButton = tr.find('button.vteLookUpAddAnItem');
            tr.css('background-color','#d3ffd5');
            actionInput.val('remove');
            addButton.html('Remove');
            tr.find('input.entryCheckBox[type="checkbox"]').attr('disabled','disabled');
            tr.find('input.add-item-qty').val(qty);
            tr.find('input.add-item-price').val(price);
        });
    },
    registerEventAfterStandarSearch:function(){
        var self = this;
        $(document).ajaxComplete(function(event, xhr, settings){
            if(settings.data != undefined && settings.data.indexOf('view=PopupAjax') != -1 && settings.data.indexOf('module=VTEItemLookup') != -1){
                self.highlightSelectedRecord();
                self.registerEventFixWithColumn();
            }
        });
    },
    registerEventScrollModal:function(){
        //var self = this;
        //$('#itemLookUpPopupModal div.modal-body').on('scroll',function(){
        //    var content = $('div.popupEntriesDiv');
        //    var navigation = $('div.lookup-item-popup-navigation');
        //    var popupFillContainer_filter_fields_scroll = $('div.popupFillContainer_filter_fields_scroll').closest('div.col-md-12');
        //    var table = content.find('table.listViewEntriesTable');
        //    var thead = table.find('thead');
        //    var search = table.find('tbody tr.searchRow');
        //    var scroll = $(this).scrollTop();
        //    var top =scroll -34;
        //    var positon = top - 1;
        //    var zIndex = 9999;
        //    if(scroll > 147){
        //        thead.css({"position": "absolute","top": (positon + 3)+'px',"background-color": '#fff',"z-index":zIndex});
        //        search.css({"position": "absolute","top": (positon + 43)+'px',"background-color": '#fff',"z-index":zIndex});
        //        navigation.css({"position": "relative","top": (positon + 19)+'px',"background-color": '#fff',"z-index":zIndex + 1});
        //    }else{
        //        thead.attr('style','');
        //        search.attr('style','');
        //        navigation.attr('style','');
        //    }
        //    if(scroll > 97){
        //        popupFillContainer_filter_fields_scroll.css({"position": "relative","top": positon+'px',"background-color": '#fff',"z-index":zIndex});
        //    }else{
        //        popupFillContainer_filter_fields_scroll.attr('style','');
        //    }
        //});
    },
    registerEventFixWithColumn:function(){
        var columns = [{name:'qty_per_unit',width:70},
            {name:'qtyinstock',width:70},
            {name:'productcode',width:70},
            {name:'vendor_id',width:70},
            {name:'unit_price',width:70}];
        columns.forEach(function(item){
            $('input.listSearchContributor.inputElement[name="'+item.name+'"]').css({'width':item.width+'px','min-width':item.width+'px'});
        });
        var th = $('table.iTL-listViewEntriesTable thead tr:nth-child(1) th');
        th.each(function(k,item){
            var w = $(item).outerWidth();
            var tr =$('table.iTL-listViewEntriesTable tr');
            tr.each(function(j,item_tr){
                var td = $(item_tr).find('td:nth-child('+(k+1)+')');
                var td_w = td.outerWidth();
                var set_W;
                if(td_w < w){
                    set_W = w;
                }else{
                    set_W = td_w;
                }
                //set_W = set_W + 1;
                td.css({'width':set_W+'px','min-width':set_W+'px'});
                $(item).css({'width':set_W+'px','min-width':set_W+'px'});
            });
        });
        var input = $('input.inputElement[data-type="qty"]:first');
        input.focus();
    },
    updateAddedItem:function(recordID,type,value,itemName){
        var self = this;
        var selectedRecords = this.selectedRecordItem;
        selectedRecords.forEach(function(item,key){
            if(item.record == recordID){
                var tr = $('tr#'+item.row);
                var changeSelected = self.selectedRecordItem[key];
                if(type == 'qty'){
                    tr.find('input.inputElement.qty').val(value);
                    changeSelected.qty = value;
                }else{
                    var decimalSeparator = $('#decimalSeparator').val();
                    var digitGroupingSeparator = $('#digitGroupingSeparator').val();
                    value = value.replace(digitGroupingSeparator, '');
                    value = value.replace(decimalSeparator, '.');
                    value = value.replace(/[^0-9\.]/g, '');
                    tr.find('input.inputElement.listPrice').val(value);
                    changeSelected.price = value;
                }
                tr.find('input.inputElement.listPrice').focus().focusout();
                self.selectedRecordItem[key] = changeSelected;

                var field = type == 'qty' ? 'Qty' : 'Price';
                app.helper.showSuccessNotification({message:itemName+' '+field+' updated to '+value});
                tr.find('input.listPrice.inputElement').trigger('change');
            }
        });
    },
    registerEventForChangeCustomQtyPrice:function(){
        var self = this;
        var popupPageContentsContainer = self.getPopupPageContainer();
        popupPageContentsContainer.off('change', '.inputElement.add-item-price, .inputElement.add-item-qty');
        popupPageContentsContainer.on('change','.inputElement.add-item-price, .inputElement.add-item-qty',function(){
            var tr = $(this).closest('tr');
            var recordID = tr.data('id');
            var value = $(this).val();
            var type = $(this).data('type');
            var itemName = $(this).data('item_name');
            self.updateAddedItem(recordID,type,value,itemName);
        });
    },
    addTopScroll:function(){
        //var popupContainer = jQuery('#popupModal');
        //var popupEntriesDivTopScroll = $('div.popupEntriesDivTopScroll');
        //var w = popupContainer.find('.popupEntriesDiv').width();
        //popupEntriesDivTopScroll.height(40);
        //popupEntriesDivTopScroll.width(w);
        //var Options= {
        //    axis:"yx",
        //    scrollInertia: 0,
        //    setWidth: w,
        //    mouseWheel:{ enable: false }
        //};
        //app.helper.showHorizontalScroll(popupEntriesDivTopScroll, Options);
    },
    itemLookUpregisterEvents: function(){
        this.registerEventForAnphabetButton();
        this.registerEventForButtonChangeItemModule();
        this.registerEventForItemModuleCheckBoxSelect();
        this.registerEventForButtonClearLeftFilterItem();
        this.registerEventForProductImage();
        this.registerEventForAddAnItem();
        this.registerEventForAddAllRecord();
        this.registerEventAfterStandarSearch();
        //this.registerEventScrollModal();
        this.registerEventFixWithColumn();
        this.registerEventForChangeCustomQtyPrice();
        this.highlightSelectedRecord();
        this.addTopScroll();
    }
});

jQuery(document).ready(function() {
    app.event.on("post.Popup.Load",function(event,params){
        vtUtils.applyFieldElementsView(jQuery('.myModal'));
        var popupInstance = VTEItemLookup_Popup_Js.getInstance(params.module);
        var eventToTrigger = params.eventToTrigger;
        if(typeof eventToTrigger != "undefined"){
            popupInstance.setEventName(params.eventToTrigger);
        }
        if(eventToTrigger == 'Vtiger.Reference.Popup.Selection'){
            popupInstance.itemLookUpregisterEvents();
            popupInstance.registerPostPopupLoadEvents();
        }
        /*if(app.getModuleName()=='Invoice'||app.getModuleName()=='SalesOrder'||app.getModuleName()=='PurchaseOrder'||app.getModuleName()=='Quotes') {
            $('body').css({overflow: 'hidden'});
            $('#popupModal').on('hidden.bs.modal', function () {
                $('body').css({overflow: 'scroll'});
            })
        }*/
    });
});
(function($) {
    $.fn.ibox = function() {

        // set zoom ratio //
        resize = 80;
        ////////////////////
        var img = this;
        img.parent().append('<div id="ibox" style="position:absolute;overflow-y:none;background:#fff;border:1px solid #ccc;z-index:1001;display:none;padding:4px;-webkit-box-shadow: 0px 0px 6px 0px #bbb;-moz-box-shadow: 0px 0px 6px 0px #bbb;box-shadow: 0px 0px 6px 0px #bbb; " />');
        var ibox = $('#ibox');
        var elX = 0;
        var elY = 0;

        img.each(function() {
            var el = $(this);

            el.mouseenter(function() {

                ibox.html('');
                var elH = el.height();
                elX = el.position().left - 6; // 6 = CSS#ibox padding+border
                elY = el.position().top - 6;
                var h = el.height();
                var w = el.width();
                var wh;
                checkwh = (h < w) ? (wh = (w / h * resize) / 2) : (wh = (w * resize / h) / 2);

                $(this).clone().prependTo(ibox);
                ibox.css({
                    top: elY + 'px',
                    left: elX + 'px'
                });

                ibox.stop().fadeTo(200, 1, function() {
                    $(this).animate({top: '-='+(resize/2), left:'-='+wh},400).children('img').animate({height:'+='+resize,width:'+='+resize},400);
                });

            });

            ibox.mouseleave(function() {
                ibox.html('').hide();

            });
        });
    };
})(jQuery);

