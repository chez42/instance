/**
 * Created by Administrator on 23/9/2015.
 */
Vtiger_Edit_Js("TimeTrackerInvoiceJs", {}, {
    arrActivityid : [],
    registerButtonReviewTimeLog: function(){
        var themeSelected = jQuery('.themeSelected');
        var reviewTimeLogsColor = themeSelected.css("background-color");

        var html = '<a  id="openListEvent" href="javascript:void(0)" class="btn" style="float: right;margin: 7px;">Review Time Logs</a>';
        var firstCol = jQuery('#lineItemTab thead tr:first th:first');
        if(firstCol.length == 0) {
            firstCol = jQuery('#lineItemTab tbody tr:first th:first');
        }
        firstCol.append(html);
        jQuery('#openListEvent').css("background-color", reviewTimeLogsColor);
        jQuery('#openListEvent').css("color", '#fff');
        jQuery('#openListEvent').css("text-shadow", 'none');
    },
    registerEventForReviewTimeLogButton: function(){
        var thisInstance=this;
        jQuery('#openListEvent').on('click', function () {
            var actionParams = {
                'module':'TimeTracker',
                'view':'MassActionAjax',
                'mode':'getEventForInvoice'
            };
            var contactid = jQuery("#EditView input[name=contact_id]").val();
            var accountid = jQuery("#EditView input[name=account_id]").val();

            if(typeof contactid != 'undefined'){
                actionParams.contactid = contactid;
            }

            if(typeof accountid != 'undefined'){
                actionParams.accountid = accountid;
            }
            AppConnector.request(actionParams).then(
                function(data) {
                    app.showModalWindow(data,function(data){
                        app.showScrollBar(jQuery('.quickCreateContent'), {
                            'height': '300px'
                        });
                        var obj =jQuery('#frmEventsList table .listViewEntriesCheckBox');
                        obj.each(function(){
                            var recordid = jQuery(this).val();
                            if(jQuery.inArray(recordid,instance.arrActivityid) != -1){
                                jQuery(this).attr('checked',true);
                            }
                        });
                        thisInstance.registerEventForSaveEventButton();
                        thisInstance.registerEventForMainCheckbox();

                    });
                }
            );
        });
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
            obj.each(function(){
                if(jQuery(this).is(":checked")){
                    var recordid = jQuery(this).val();
                    if(jQuery.inArray(recordid,activityidExist) == -1){
                        //add new row
                        var lineItemTable = inventoryInstance.getLineItemContentsContainer();
                        var newRow = inventoryInstance.getBasicRow().addClass(inventoryInstance.rowClass);
                        jQuery('.lineItemPopup[data-module-name="Products"]',newRow).remove();
                        var sequenceNumber = inventoryInstance.getNextLineItemRowNumber();
                        newRow = newRow.appendTo(lineItemTable);
                        inventoryInstance.checkLineItemRow();
                        newRow.find('input.rowNumber').val(sequenceNumber);
                        inventoryInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
                        newRow.find('input.productName').addClass('autoComplete');
                        inventoryInstance.registerLineItemAutoComplete(newRow);

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
                        var dataDescription = 'Date: ' + start_date_time + "\n" + 'Description: ' + data.description;
                        newRow.find('#productName'+rowNumber).val(data.service_name);
                        newRow.find('#comment'+rowNumber).val(dataDescription);
                        newRow.find('#qty'+rowNumber).val(data.quantity);
                        newRow.find('#listPrice'+rowNumber).val(Math.round(servicePrice * 1000)/1000);
                        newRow.find('#productTotal'+rowNumber).html(Math.round(data.quantity*servicePrice * 1000)/1000);
                        newRow.find('#netPrice'+rowNumber).html(Math.round(data.quantity*servicePrice * 1000)/1000);
                        newRow.find('#hdnProductId'+rowNumber).val(data.serviceid);
                        newRow.find('#lineItemType'+rowNumber).val('Services');
                        var element = "<input type='hidden' name='activityid[]' value='"+ recordid + "'/>";
                        newRow.append(element);

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
            app.hideModalWindow();
            //jQuery.unblockUI();

        });
    }
});

jQuery(document).ready(function () {
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
    if(targetModule == 'Invoice' && targetView == 'Edit') {
        // Check config
        var params = {};
        params.action = 'ActionAjax';
        params.module = 'TimeTracker';
        params.mode = 'getModuleConfigForInvoice';
        AppConnector.request(params).then(
            function(data){
                var isAllowBilling = data.result.allow_bill_event_invoice;
                var quoterStatus = data.result.quoterStatus;
                if(isAllowBilling == 1){
                    instance = new TimeTrackerInvoiceJs();
                    if(quoterStatus == false) {
                        instance.registerButtonReviewTimeLog();
                        instance.registerEventForReviewTimeLogButton();
                    }
                }
            }
        );

    }




});