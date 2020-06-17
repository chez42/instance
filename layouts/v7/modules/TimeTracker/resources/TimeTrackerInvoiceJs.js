/**
 * Created by Administrator on 23/9/2015.
 */
Vtiger_Edit_Js("TimeTrackerInvoiceJs", {}, {
    arrActivityid : [],
    registerButtonReviewTimeLog: function(quoterStatus){
        var themeSelected = jQuery('.themeSelected');
        var reviewTimeLogsColor = themeSelected.css("background-color");
        
        var tableItemDetailInvoice = jQuery('table#lineItemTab');
        if(quoterStatus == true){
            var secondTdTableItemDetailInvoice = tableItemDetailInvoice.find('th').eq(0);
        }else{
            var secondTdTableItemDetailInvoice = tableItemDetailInvoice.find('td').eq(1);
        }
        var html = '<a id="openListEvent" href="javascript:void(0)" class="btn btn-primary btn-xs pull-right">Review Time Logs</a>';
        secondTdTableItemDetailInvoice.append(html);
    },
    registerEventForReviewTimeLogButton: function(){
        var thisInstance=this;
        jQuery('#openListEvent').on('click', function () {
            //#661346
            var mode = app.getModuleName() == 'Invoice' ? 'getEventForInvoice' : 'getEventForSalesOrder';
            var actionParams = {
                'module':'TimeTracker',
                'view':'MassActionAjax',
                'mode':mode
            };
            var contactid = jQuery("#EditView input[name=contact_id]").val();
            var accountid = jQuery("#EditView input[name=account_id]").val();

            if(typeof contactid != 'undefined'){
                actionParams.contactid = contactid;
            }

            if(typeof accountid != 'undefined'){
                actionParams.accountid = accountid;
            }
            app.request.post({data:actionParams}).then(
                function(err, res){
                    if (err === null){
                        app.helper.showModal(res,{'cb' : res});

                        // app.showScrollBar(jQuery('.quickCreateContent'), {
                        //     'height': '300px'
                        // });
                        var obj =jQuery('#frmEventsList table .listViewEntriesCheckBox');
                        obj.each(function(){
                            var recordid = jQuery(this).val();
                            if(jQuery.inArray(recordid,instance.arrActivityid) != -1){
                                jQuery(this).attr('checked',true);
                            }
                        });
                        thisInstance.registerEventForSaveEventButton();
                        thisInstance.registerEventForMainCheckbox();
                        vtUtils.showSelect2ElementView($("#time_tracker_search_selected_module"));
                        thisInstance.registerEventForSearch();
                    }
                }
            );
        });
    },
    registerEventForSearch:function(){
        var self = this;
        $('#time_tracker_search_button').on('click',function(){
            var time_tracker_search_selected_module = $('#time_tracker_search_selected_module').val();
            var time_tracker_search_title = $('#time_tracker_search_title').val();
            var time_tracker_search_related = $('#time_tracker_search_related')[0].checked;
            var contactid = jQuery("#EditView input[name=contact_id]").val();
            var accountid = jQuery("#EditView input[name=account_id]").val();
            var actionParams = {
                'module':'TimeTracker',
                'view':'MassActionAjax',
                'mode':'search',
                'selected_module':time_tracker_search_selected_module,
                'title':time_tracker_search_title,
                'related':time_tracker_search_related,
                'contact_id':contactid,
                'account_id':accountid,
            };
            app.helper.showProgress('');
            app.request.post({data:actionParams}).then(
                function(err, res){
                    if (err === null){
                        var form = $('#frmEventsList');
                        form.find('table.listViewEntriesTable > tbody').html(res);
                        app.helper.hideProgress();
                        self.registerEventForSaveEventButton();
                    }
                }
            );
        })
    },
    registerEventForMainCheckbox: function (){
        jQuery('#masCheckBox').on('click',function(){
            var obj =jQuery('#frmEventsList table .listViewEntriesCheckBox');
            if(jQuery(this).is(":checked")){
                obj.each(function(){
                    jQuery(this).attr('checked',true);
                });
            }else{
                obj.each(function(){
                    jQuery(this).attr('checked',false);
                });
            }
        });
    },
    registerEventForSaveEventButton: function (){
        var obj =jQuery('#frmEventsList table .listViewEntriesCheckBox');
        jQuery('#btnSaveEvent').on('click',function(){

            if(jQuery('#productName1').val() !== 'undefined'){
                if(jQuery('#productName1').val()== ''){
                    jQuery('#row1').remove();
                }
            }

            var record = [];
            var inventoryInstance = new Inventory_Edit_Js();

            var lineItem = jQuery('#lineItemTab .lineItemRow');
            var activityidExist = [];
            lineItem.each(function(index) {
                var id = index + 1;
                var serviceId = jQuery('#hdnProductId' + id).val();
                var activityid = jQuery(this).find("input[name='activityid[]']").val();
                activityidExist.push(activityid);
                if (typeof  serviceId == 'undefined') {
                    jQuery(this).remove();
                }
            });

            //var activityid
            instance.arrActivityid = [];
            var params = {};
            params.currentTarget = jQuery('button#addService');
            obj.each(function(){
                if(jQuery(this).is(":checked")){
                    var recordid = jQuery(this).val();
                    if(jQuery.inArray(recordid,activityidExist) == -1){
                        //add new row
                        var lineItemTable = inventoryInstance.getLineItemContentsContainer();
                        var newLineItem = inventoryInstance.getNewLineItem(params).addClass(inventoryInstance.rowClass);
                        newLineItem.find('input.listPrice').attr('list-info','[]');
                        newLineItem = newLineItem.appendTo(inventoryInstance.lineItemsHolder);
                        newLineItem.find('input.productName').addClass('autoComplete');
                        newLineItem.find('.ignore-ui-registration').removeClass('ignore-ui-registration');
                        var newRow = newLineItem;


                        // jQuery('.lineItemPopup[data-module-name="Products"]',newRow).remove();
                        // var sequenceNumber = inventoryInstance.getLineItemNextRowNumber();
                        // newRow = newRow.appendTo(lineItemTable);
                        // inventoryInstance.checkLineItemRow();
                        // newRow.find('input.rowNumber').val(sequenceNumber);
                        // console.log(newRow);
                        // inventoryInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
                        // newRow.find('input.productName').addClass('autoComplete');
                        // inventoryInstance.registerLineItemAutoComplete(newRow);

                        //Bill data
                        /*var serviceName = jQuery("#frmEventsList input[name='service_name']").val();
                        var serviceId = jQuery("#frmEventsList input[name='service_id']").val();
                        var serviceType = jQuery("#frmEventsList input[name='service_type']").val();
                        var servicePrice = jQuery("#frmEventsList input[name='service_price']").val();*/

                        var currentRow = jQuery(this).closest('tr');
                        var data = currentRow.data('info');

                        var servicePrice = data.unit_price;
                        var start_date_time = data.start_date_time;
                        start_date_time = start_date_time.split(' ')[0];
                        var rowNumber = newRow.find('.rowNumber').val();
                        var dataDescription = 'Date: ' + start_date_time + "\n" + 'Description: ' + data.description + "\n" + 'Subject: ' + data.subject;
                        newRow.find('#productName'+rowNumber).val(data.service_name);
                        newRow.find('#comment'+rowNumber).val(dataDescription);
                        newRow.find('#comment'+rowNumber).css({'width':'164px','height':'53px'});
                        if (data.quantity == '0'){
                            data.quantity = '0.1';
                        }
                        newRow.find('#qty'+rowNumber).val(data.quantity);
                        newRow.find('#listPrice'+rowNumber).val(Math.round(servicePrice * 1000)/1000);
                        newRow.find('#productTotal'+rowNumber).html(Math.round(data.quantity*servicePrice * 1000)/1000);
                        newRow.find('#netPrice'+rowNumber).html(Math.round(data.quantity*servicePrice * 1000)/1000);
                        newRow.find('#hdnProductId'+rowNumber).val(data.serviceid);
                        newRow.find('#lineItemType'+rowNumber).val('Services');
                        var element = "<input type='hidden' name='activityid[]' value='"+ recordid + "'/>";
                        newRow.append(element);
                        inventoryInstance.lineItemToTalResultCalculations();
                    }
                    instance.arrActivityid.push(recordid);


                }
            });
            lineItem.each(function(index){
                var activityid = jQuery(this).find("input[name='activityid[]']").val();
                if(typeof activityid != 'undefined' && jQuery.inArray(activityid,instance.arrActivityid) == -1){
                    jQuery(this).remove();
                }
            });
            app.helper.hideModal();
            //jQuery.unblockUI();

        });
    }
});

jQuery(document).ready(function() {
    setTimeout(function () {
        initData_TimeTrackerInvoice();
    }, 5000);
});
function initData_TimeTrackerInvoice() {
    var sPageURL = window.location.search.substring(1);
    var targetModule = '';
    var targetView = '';
    var targetRecord = '';
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == 'module') {
            targetModule = sParameterName[1];
        }
        else if (sParameterName[0] == 'view') {
            targetView = sParameterName[1];
        }else if (sParameterName[0] == 'record') {
            targetRecord = sParameterName[1];
        }
    }
    if((targetModule == 'Invoice' || targetModule =='SalesOrder') && targetView == 'Edit') {
        var mode = targetModule == 'Invoice' ? 'getModuleConfigForInvoice' : 'getModuleConfigForSalesOrder';
        app.helper.showProgress();
        // Check config
        var params = {};
        params.action = 'ActionAjax';
        params.module = 'TimeTracker';
        params.mode = mode;
        app.request.post({data:params}).then(
            function(err,res){
                if (err === null){
                    var isAllowBilling = res.allow_bill_event_invoice;
                    var quoterStatus = res.quoterStatus;
                    if(isAllowBilling == 1){
                        instance = new TimeTrackerInvoiceJs();
                        instance.registerButtonReviewTimeLog(quoterStatus);
                        instance.registerEventForReviewTimeLogButton();
                    }
                    app.helper.hideProgress();
                }
            }
        );
    }
}