/*********************************************************************************
 * The content of this file is subject to the VTE Reports license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is Vtiger Experts s.r.o.
 * Portions created by Vtiger Experts s.r.o. are Copyright(C) Vtiger Experts s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

Vtiger_Detail_Js("VTEReports_Detail_Js",{},{
    advanceFilterInstance : false,
    detailViewContentHolder : false,
    HeaderContentsHolder : false,


    getContentHolder : function() {
        if(this.detailViewContentHolder == false) {
            this.detailViewContentHolder = jQuery('div.contentsDiv');
        }
        return this.detailViewContentHolder;
    },

    getHeaderContentsHolder : function(){
        if(this.HeaderContentsHolder == false) {
            this.HeaderContentsHolder = jQuery('div.reportsDetailHeader ');
        }
        return this.HeaderContentsHolder;
    },
    calculateValues : function(){
        var return_filters = "";
        var escapedOptions = new Array('account_id', 'contactid', 'contact_id', 'product_id', 'parent_id', 'campaignid', 'potential_id', 'assigned_user_id1', 'quote_id', 'accountname', 'salesorder_id', 'vendor_id', 'time_start', 'time_end', 'lastname');
        var conditionColumns = vt_getElementsByName('tr', "conditionColumn");
        var criteriaConditions = [];
        var sel_fields = JSON.parse(document.getElementById("sel_fields").value);
        for (var i = 0; i < conditionColumns.length; i++) {
            var columnRowId = conditionColumns[i].getAttribute("id");
            var columnRowInfo = columnRowId.split("_");
            var columnGroupId = columnRowInfo[1];
            var columnIndex = columnRowInfo[2];

            if (columnGroupId != "0")
                ctype = "f";
            else
                ctype = "g";

            var columnId = ctype + "col" + columnIndex;
            var columnObject = getObj(columnId);
            var selectedColumn = columnObject.value;
            var selectedColumnIndex = columnObject.selectedIndex;
            var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;
            if (columnObject.options[selectedColumnIndex].value != "none") {
                var comparatorId = ctype + "op" + columnIndex;
                var comparatorObject = getObj(comparatorId);
                var comparatorValue = comparatorObject.value;

                var valueId = ctype + "val" + columnIndex;
                var valueObject = getObj(valueId);
                var specifiedValue = valueObject.value;

                var extValueId = ctype + "val_ext" + columnIndex;
                var extValueObject = getObj(extValueId);
                if (extValueObject) {
                    extendedValue = extValueObject.value;
                }

                var glueConditionId = ctype + "con" + columnIndex;
                var glueConditionObject = getObj(glueConditionId);
                var glueCondition = '';
                if (glueConditionObject) {
                    glueCondition = glueConditionObject.value;
                }
                var col = selectedColumn.split(":");

                var std_filter_columns = document.getElementById("std_filter_columns").value;
                if (typeof std_filter_columns != 'undefined' && std_filter_columns!=""){
                    var std_filter_columns_arr = std_filter_columns.split('<%jsstdjs%>');
                }else{
                    var std_filter_columns_arr = new Array();
                }

                if (std_filter_columns_arr.indexOf(selectedColumn) > -1) {
                    if(comparatorValue=="custom"){
                        if (!emptyCheckVte("jscal_field_sdate_val_"+columnIndex, " Column ", "text")){
                            return false;
                        }
                    }
                    if(comparatorValue=="custom"){
                        if (!emptyCheckVte("jscal_field_edate_val_"+columnIndex, " Column ", "text")){
                            return false;
                        }
                    }
                    if(comparatorValue=="custom"){
                        var start_date = document.getElementById("jscal_field_sdate_val_"+columnIndex).value;
                        var end_date = document.getElementById("jscal_field_edate_val_"+columnIndex).value;
                    }else{
                        if(document.getElementById("jscal_field_sdate"+columnIndex)) var start_date = document.getElementById("jscal_field_sdate"+columnIndex).value;
                        if(document.getElementById("jscal_field_edate"+columnIndex)) var end_date = document.getElementById("jscal_field_edate"+columnIndex).value;
                    }
                    var specifiedValue = start_date+"<;@STDV@;>"+end_date;
                }else{
                    if(col[1]=="currency_id"){
                        if (!emptyCheckVte(columnId, " Column ", "text")){
                            return false;
                        }
                    }else if (escapedOptions.indexOf(col[3]) == -1) {
                        if (col[4] == 'T') {
                            var datime = specifiedValue.split(" ");
                            if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH"))
                                return false
                            if (datime.length > 1)
                                if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                                    return false
                        }
                        else if (col[4] == 'D')
                        {
                            if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                return false
                            if (extValueObject) {
                                if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                    return false
                            }
                        } else if (col[4] == 'I')
                        {
                            if (!intValidate(valueId, selectedColumnLabel + " (Integer Criteria)" + i))
                                return false
                        } else if (col[4] == 'N')
                        {
                            if (!numValidate(valueId, selectedColumnLabel + " (Number) ", "any", true))
                                return false
                        } else if (col[4] == 'E')
                        {
                            if (!patternValidate(valueId, selectedColumnLabel + " (Email Id)", "EMAIL"))
                                return false
                        }

                        if (sel_fields[selectedColumn]) {


                            if(comparatorValue=="e" || comparatorValue=="n"){
                                var selectElement = jQuery('#s_fval'+columnIndex);
                                specifiedValue = selectElement.val();
                            }else{
                                var selectElement = jQuery('#fval'+columnIndex);
                                specifiedValue = selectElement.val();
                            }
                        }

                    }
                }
                if (extValueObject && extendedValue != null && extendedValue != '')
                    specifiedValue = specifiedValue + ',' + extendedValue;
                criteriaConditions[columnIndex] = {"groupid": columnGroupId,
                    "columnname": selectedColumn,
                    "comparator": comparatorValue,
                    "value": specifiedValue,
                    "column_condition": glueCondition
                };

            }
        }
        var advft_criteria_value = JSON.stringify(criteriaConditions);

        return_filters += "advft_criteria="+advft_criteria_value;

        var conditionGroups = vt_getElementsByName('div', "conditionGroup");
        var criteriaGroups = [];
        for (var i = 0; i < conditionGroups.length; i++)
        {
            var groupTableId = conditionGroups[i].getAttribute("id");
            var groupTableInfo = groupTableId.split("_");
            var groupIndex = groupTableInfo[1];

            var groupConditionId = "gpcon" + groupIndex;
            var groupConditionObject = getObj(groupConditionId);
            var groupCondition = '';
            if (groupConditionObject) {
                groupCondition = groupConditionObject.value;
            }
            criteriaGroups[groupIndex] = {"groupcondition": groupCondition};
        }
        var advft_criteria_groups_value = JSON.stringify(criteriaGroups);

        return_filters += "&advft_criteria_groups="+advft_criteria_groups_value;


        var GroupconditionColumns = vt_getElementsByName('tr', "groupconditionColumn");
        var GroupcriteriaConditions = [];

        for (var i = 0; i < GroupconditionColumns.length; i++) {

            var columnRowId = GroupconditionColumns[i].getAttribute("id");
            var columnRowInfo = columnRowId.split("_");
            var columnGroupId = columnRowInfo[1];
            var columnIndex = columnRowInfo[2];

            if (columnGroupId != "0")
                ctype = "f";
            else
                ctype = "g";

            var columnId = ctype + "groupcol" + columnIndex;
            var columnObject = getObj(columnId);
            var selectedColumn = columnObject.value;
            var selectedColumnIndex = columnObject.selectedIndex;
            var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;
            if (columnObject.options[selectedColumnIndex].value != "none") {
                var comparatorId = ctype + "groupop" + columnIndex;
                var comparatorObject = getObj(comparatorId);
                var comparatorValue = comparatorObject.value;
                var valueId = ctype + "groupval" + columnIndex;
                var valueObject = getObj(valueId);
                var specifiedValue = valueObject.value;

                var glueConditionId = ctype + "groupcon" + columnIndex;
                var glueConditionObject = getObj(glueConditionId);
                var glueCondition = '';
                if (glueConditionObject) {
                    glueCondition = glueConditionObject.value;
                }

                if(GroupconditionColumns.length>1){
                    if (!emptyCheckVte(columnId, " Column ", "text")){
                        return false;
                    }
                    if (!emptyCheckVte(comparatorId, selectedColumnLabel + " Option", "text")){
                        return false;
                    }
                }

                var col = selectedColumn.split(":");
                if (escapedOptions.indexOf(col[3]) == -1) {
                    if (col[4] == 'T') {
                        var datime = specifiedValue.split(" ");
                        if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH"))
                            return false
                        if (datime.length > 1)
                            if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                                return false
                    }
                    else if (col[4] == 'D')
                    {
                        if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                            return false
                        if (extValueObject) {
                            if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                return false
                        }
                    } else if (col[4] == 'I')
                    {
                        if (!intValidate(valueId, selectedColumnLabel + " (Integer Criteria)" + i))
                            return false
                    } else if (col[4] == 'N')
                    {
                        if (!numValidate(valueId, selectedColumnLabel + " (Number) ", "any", true))
                            return false
                    } else if (col[4] == 'E')
                    {
                        if (!patternValidate(valueId, selectedColumnLabel + " (Email Id)", "EMAIL"))
                            return false
                    }
                }
                //Added to handle yes or no for checkbox fields in reports advance filters.
                if (col[4] == "C") {
                    if (specifiedValue == "1")
                        specifiedValue = getObj(valueId).value = 'yes';
                    else if (specifiedValue == "0")
                        specifiedValue = getObj(valueId).value = 'no';
                }
                if (extValueObject && extendedValue != null && extendedValue != '')
                    specifiedValue = specifiedValue + ',' + extendedValue;

                GroupcriteriaConditions[columnIndex] = {"groupid": columnGroupId,
                    "columnname": selectedColumn,
                    "comparator": comparatorValue,
                    "value": specifiedValue,
                    "column_condition": glueCondition
                };
            }
        }
        //var groupft_criteria_value = JSON.stringify(GroupcriteriaConditions);
        var groupft_criteria_value = GroupcriteriaConditions;
        //return_filters[2] = groupft_criteria_value;
        return_filters += "&groupft_criteria="+groupft_criteria_value;
        // groupconditioncolumn end

        return return_filters;
    },
    initForFirstRun: function(){
        var thisInstance = this;
        var advFilterCondition = thisInstance.calculateValues();
        if(advFilterCondition!==false){
            var advFilterCondition_arr = advFilterCondition.split("&");
            var advft_criteria_tmp = advFilterCondition_arr[0].split("=");
            var advft_criteria = advft_criteria_tmp[1];
            var advft_criteria_groups_tmp = advFilterCondition_arr[1].split("=");
            var advft_criteria_groups = advft_criteria_groups_tmp[1];
            var groupft_criteria_tmp = advFilterCondition_arr[2].split("=");
            var groupft_criteria = groupft_criteria_tmp[1];

            var recordId = thisInstance.getRecordId();
            var currentMode = "generate";
            jQuery('#advft_criteria').val(advft_criteria);

            jQuery('#advft_criteria_groups').val(advft_criteria_groups);
            jQuery('#groupft_criteria').val(groupft_criteria);
            jQuery('#currentMode').val(currentMode);
            //jQuery('#detailView').submit();
        }
        return false;
    },
    registerSaveOrGenerateReportEvent : function(){
        var thisInstance = this;
        jQuery('.generateReport').on('click',function(e){
            var advFilterCondition = thisInstance.calculateValues();
            if(advFilterCondition!==false){
                var advFilterCondition_arr = advFilterCondition.split("&");
                var advft_criteria_tmp = advFilterCondition_arr[0].split("=");
                var advft_criteria = advft_criteria_tmp[1];
                var advft_criteria_groups_tmp = advFilterCondition_arr[1].split("=");
                var advft_criteria_groups = advft_criteria_groups_tmp[1];
                var groupft_criteria_tmp = advFilterCondition_arr[2].split("=");
                var groupft_criteria = groupft_criteria_tmp[1];

                var recordId = thisInstance.getRecordId();
                var currentMode = jQuery(e.currentTarget).data('mode');
                jQuery('#advft_criteria').val(advft_criteria);

                jQuery('#advft_criteria_groups').val(advft_criteria_groups);
                jQuery('#groupft_criteria').val(groupft_criteria);
                jQuery('#currentMode').val(currentMode);
                document.getElementById('detailView').submit();
            }
            return false;
        });

        jQuery('.addWidget').on('click',function(e){
            var thisInstance = this;
            var aDeferred = jQuery.Deferred();
            var this_btn = jQuery(this);
            var type = jQuery(this).data('type');
            app.helper.showProgress();
            var recordId = app.getRecordId();
            var params = {};
            params.module = 'VTEReports';
            params.action = 'IndexAjax';
            params.mode = 'addWidget';
            params.record = recordId;
            params.type = type;
            app.request.post({data:params}).then(
                function(err,data){
                    if(err === null) {
                        var textMessage = {
                            message: data['message']
                        };
                        app.helper.showSuccessNotification(textMessage);
                        app.helper.hideProgress();
                        this_btn.hide();
                    }else{
                        aDeferred.reject(err);
                    }
                }
            );
            return aDeferred.promise();
        });
        jQuery('.addWidgetList').on('click',function(e){
            var thisControl = jQuery(this);
            var aDeferred = jQuery.Deferred();
            var thisInstance = this;

            app.helper.showProgress();

            var recordId = jQuery("#recordId").val();
            var amethod = thisControl.data('mode');

            var url = "index.php?module=VTEReports&action=IndexAjax&mode=addwidgetList&record="+recordId + "&amethod=" +amethod;
            var actionParams = {
                url: url
            };
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        var params = {
                            text: data['result']['message']
                        };
                        app.helper.showSuccessNotification(params);
                        if(data['result']['mode'] == "ADD"){
                            thisControl.data('mode','removewidgetList');
                            thisControl.val(data['result']['html']);
                            thisControl.html('<strong>'+data['result']['html']+'</strong>');
                        }
                        else{
                            thisControl.data('mode','addwidgetList');
                            thisControl.val(data['result']['html']);
                            thisControl.html('<strong>'+data['result']['html']+'</strong>');
                        }
                        app.helper.hideProgress();
                    }else{
                        aDeferred.reject(err);
                    }
                }
            );
            return aDeferred.promise();
        });

    },
    registerEvents : function(){
        //this._super();
        this.registerSaveOrGenerateReportEvent();
        var container = this.getContentHolder();
        this.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',container));
        this.initForFirstRun();
        jQuery('.detailview-header-block').hide();
        jQuery('#modules-menu').hide();
    }
});

