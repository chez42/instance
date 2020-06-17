/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Settings_Vtiger_List_Js("Settings_VTESLAPolicies_List_Js", {

    triggerCreate : function(url) {
        var selectedModule = jQuery('#moduleFilter').val();
        if(selectedModule.length > 0) {
            url += '&source_module='+selectedModule
        }
        window.location.href = url;
    }
}, {

    registerFilterChangeEvent: function () {
        var thisInstance = this;
        var container = this.getListViewContainer();
        container.on('change', '#moduleFilter', function (e) {
            jQuery('#pageNumber').val("1");
            jQuery('#pageToJump').val('1');
            jQuery('#orderBy').val('');
            jQuery("#sortOrder").val('');
            var params = {
                module: app.getModuleName(),
                parent: app.getParentModuleName(),
                sourceModule: jQuery(e.currentTarget).val()
            }
            thisInstance.loadListViewRecords(params);
        });
    },

    loadListViewRecords : function(urlParams) {
        var self = this;
        var aDeferred = jQuery.Deferred();
        var defParams = this.getDefaultParams();
        if(typeof urlParams == "undefined") {
            urlParams = {};
        }
        urlParams = jQuery.extend(defParams, urlParams);
        app.helper.showProgress();
        app.request.pjax({data:urlParams}).then(function(err, res){
            self.placeListContents(res);
            app.helper.hideProgress();
            jQuery("input[name='workflowstatus']").bootstrapSwitch();
            aDeferred.resolve(res);
        });
        return aDeferred.promise();
    },

    registerRowClickEvent : function(){
        var listViewContentDiv = this.getListViewContainer();

        listViewContentDiv.on('click','.listViewEntries',function(e){
            var elem = jQuery(e.currentTarget);
            var targetElem = jQuery(e.target);
            if(targetElem.closest('.bootstrap-switch').length != 0){
                return false;
            }
            if(targetElem.closest('.deleteRecordButton').length != 0){
                return;
            }
            var recordUrl = elem.data('recordurl');
            if(typeof recordUrl == 'undefined') {
                return;
            }
            window.location.href = recordUrl;
        });
    },

    getListViewContainer: function () {
        if (this.listViewContainer === false) {
            this.listViewContainer = jQuery('#list-content');
        }
        return this.listViewContainer;
    },

    placeListContents : function(contents) {
        var container = this.getListViewContainer();
        container.html(contents);
    },

    registerEventForChangePolicyStatus: function (listViewContainer) {
        jQuery(listViewContainer).on('switchChange.bootstrapSwitch', "input[name='workflowstatus']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            if(currentElement.val() == 'on'){
                currentElement.attr('value','off');
            } else {
                currentElement.attr('value','on');
            }
            var params = {
                module : 'VTESLAPolicies',
                parent : 'Settings',
                'action' : 'ActionAjax',
                'mode' : 'UpdateStatus',
                'record' : currentElement.data('id'),
                'status' : currentElement.val()
            }

            AppConnector.request(params).then(function(data){
                if(data){
                    app.helper.showSuccessNotification({
                        message : app.vtranslate('SLA Policy status changed successfully.')
                    });
                }
            });
        });
    },

    getDefaultParams : function() {
        var container = this.getListViewContainer();
        var pageNumber = container.find('#pageNumber').val();
        var module = 'VTESLAPolicies';
        var parent = 'Settings';
        var params = {
            'module': module,
            'parent': parent,
            'page' : pageNumber,
            'view' : "List",
            'search_value' : jQuery('.searchWorkflows').val(),
            'search_key' : jQuery('.searchWorkflows').val()
        }
        return params;
    },

    registerSearch : function() {
        var thisInstance = this;
        var container = this.getListViewContainer();
        container.on('keyup', '.searchWorkflows', function(e) {
            if(e.which == 13) {
                thisInstance.loadListViewRecords({page: 1});
            }
        });
    },
    /**
     * Function shows and hide when user enter on a row and leave respectively
     * @returns {undefined}
     */
    registerShowDeleteActionOnHover: function(){
        var listViewContentDiv = this.getListViewContainer();
        listViewContentDiv.on('mouseover','tr.listViewEntries',function(e){
            jQuery(e.currentTarget).find('.deleteRecordButton').css('opacity',0.6);
        }).on('mouseleave','tr.listViewEntries',function(e){
            jQuery(e.currentTarget).find('.deleteRecordButton').css('opacity',0);
        });
    },

    registerDeleteRecordClickEvent: function () {
        var thisInstance = this;
        jQuery('#page').on('click', '.deleteRecordButton', function (e) {
            var elem = jQuery(e.currentTarget);
            var parent = elem;
            var recordId = parent.closest('tr').data('id');
            var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
            app.helper.showConfirmationBox({'message': message}).then(function () {
                app.helper.showProgress();
                var params = {
                    module : 'VTESLAPolicies',
                    parent : 'Settings',
                    action : 'ActionAjax',
                    mode : 'DeleteRecord',
                    record : recordId
                }
                AppConnector.request(params).then(function (data) {
                    if(data.success == true){
                        app.helper.hideProgress();
                        thisInstance.loadListViewRecords();
                    }
                });
            });
        });
    },

    registerEventShowModalBusiness: function(){
        var container = $('#listViewContent');
        var thisInstance = this;
        container.on('click','button.btn-business',function () {
            var element = $(this);
            var dataUrl = element.attr('data-url');
            if(dataUrl) {
                var url = "index.php?" + dataUrl;
                var postParams = app.convertUrlToDataParams(url);
                app.request.post({data: postParams}).then(function (err, data) {
                    if (err === null) {
                        jQuery('.popupModal').remove();
                        var ele = jQuery('<div class="modal popupModal"></div>');
                        ele.append(data);
                        jQuery('body').append(ele);
                        thisInstance.showpopupModal();
                        thisInstance.setValueCurentDateTimeHeader();
                        thisInstance.changeCheckedInput();
                        thisInstance.registerAddRowHolidays();
                        thisInstance.registerEventDatePicker();
                        thisInstance.registerSaveBussinessHours();
                        thisInstance.registerDeleteRowHolidays();
                        thisInstance.setValueDateCurrent();
                        //thisInstance.setValueCheckInputSpan();
                    }
                });
            }
        });
    },
    setValueDateCurrent :function(){
        var date = new Date();

        //var day = date.getDate();
        var day = "01";
        //var month = date.getMonth() + 1;
        var month = "01";
        var year = date.getFullYear()+1;

        var today = year + "-" + month + "-" + day;


        document.getElementById('dateValue1').value = today;


        var day = "25";
        //var month = date.getMonth() + 1;
        var month = "12";
        var year = date.getFullYear();

        var today1 = year + "-" + month + "-" + day ;
        document.getElementById('dateValue2').value = today1;
    },
    setValueCurentDateTimeHeader : function(){
        var date = new Date();
        var day = date.getDate();
        var month = date.getMonth()+1;
        var year = date.getFullYear();
        // var hour = date.getHours();
        // var minutes = date.getMinutes();
        // var hours = date.getHours();
        // var minutes = date.getMinutes();
        // var ampm = hours >= 12 ? 'pm' : 'am';
        // hours = hours % 12;
        // hours = hours ? hours : 12; // the hour '0' should be '12'
        // minutes = minutes < 10 ? '0'+minutes : minutes;
        // var strTime = hours + ':' + minutes + ' ' + ampm;
        // return strTime;

        var seconds = date.getSeconds();
        var today2 = year + "-"+ month + "-"+day+"      "+ date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
        // +" "+hour +":"+minutes+":"+seconds
        document.getElementById('curentTimeHeader').value = today2;

    },
    showpopupModal : function(){
        var thisInstance = this;
        vtUtils.applyFieldElementsView(jQuery('.popupModal'));
        jQuery('.popupModal').modal({backdrop: 'static'});
        jQuery('.popupModal').on('shown.bs.modal', function() {
            jQuery('.myModal').css('opacity', .5);
            jQuery('.myModal').unbind();

        });
    },
    changeCheckedInput : function(){
        $('#table-weekday tr .timepicker-default:last').prevAll().each(function() {
            $(this).find('input').attr('disabled', 'disabled');
        });
        $('#table-weekday tr .check-dayofweek').change(function() {
            if ($(this).is(":checked")) {
                $(this).closest('tr').find('.timepicker-default').removeAttr('disabled');
                $(this).closest('tr').find('.status_check').attr('value','1');
                // $('#table-weekday tr .status_check').val(1);
            }else {
                //$('.status_check').val('0');
                $(this).closest('tr').find('.timepicker-default').attr('disabled', 'disabled');
                $(this).closest('tr').find('.status_check').attr('value','0');
            }
        });
    },

    registerDeleteRowHolidays : function(){
        var thisInstance = this;
        $(document).on('click', 'button.deleterow', function () {
            var element = $(this);
            var trElement = element.closest('tr');
            var holidayActionsId = trElement.attr('sla-holiday-record-id');
            var module = app.getModuleName();
            if(holidayActionsId) {
                var params = {
                    module: module,
                    action: 'ActionAjax',
                    mode: 'deleteHoliday',
                    parent: 'Settings',
                    record: holidayActionsId
                };

                AppConnector.request(params).then(
                    function (data) {
                        if (data.success == true) {
                            trElement.remove();
                            return false;
                        }
                    }
                );
            }else{
                trElement.remove();
                return false;
            }
        });
    },
    registerEventDatePicker : function(){
        $('[data-name="Campaigns_editView_fieldName_closingdate"]').click(function() {
            $(this).datepicker().datepicker("show");
        });

    },
    registerAddRowHolidays : function(){
        var thisInstance = this;
        $('#addholiday').on('click',function(){
            var markup = "<tr>" +
                "<td style='width: 10%; text-align: center; color: red' >" +
                "<button type='button' class='deleterow' style='border: none; background: #ffffff' >x</button" +
                "<input type='hidden' name='rowno[]' value='{$INDEX}'></td><td style='width: 60%; '>" +
                "<input type='text' name='sla_holiday_name[]' value='' style='border-left: none;border-right: none; height: 30px; border-bottom: 1px solid #d9d9d9; border-top: 1px solid #d9d9d9;font-family: 'OpenSans-Regular', sans-serif; font-size: 15px '>" +
                "</td>" +
                "<td style='width: 30%'><div class='input-group inputElement' style='margin-bottom: 3px'>" +
                "<input data-name='Campaigns_editView_fieldName_closingdate' value='' type='text' class='dateField form-control' data-fieldname='closingdate' data-fieldtype='date' name='sla_holiday_date[]' data-date-format='yyyy-mm-dd' data-rule-required='true' data-rule-date='true' aria-required='true'>" +
                "<span class='input-group-addon'><i class='fa fa-calendar'></i></span>" +
                "</div></td>" +
                "</tr>";
            $(".table-holiday").append(markup);
            thisInstance.registerEventDatePicker();
        });


    },


    registerSaveBussinessHours: function(){
        var form = $('#AddBusinessHours');
        form.on('submit',function (e) {
            app.helper.showProgress();
            e.preventDefault();
            var params = form.serialize();
            AppConnector.request(params).then(function(data){
                app.helper.hideProgress();
                jQuery('.popupModal').modal('hide');
            });
        })
    },
    registerEvents: function () {
        var thisInstance = this;
        this._super();
        this.registerRowClickEvent();
        this.registerFilterChangeEvent();
        this.registerDeleteRecordClickEvent();
        var listViewContainer = this.getListViewContainer();
        this.registerShowDeleteActionOnHover();
        this.registerEventShowModalBusiness();
        if (listViewContainer.length > 0) {
            jQuery("input[name='workflowstatus']").bootstrapSwitch();
            this.registerEventForChangePolicyStatus(listViewContainer);
            this.registerSearch();
        }
    }
});