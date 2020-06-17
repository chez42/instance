/*********************************************************************************
 * The content of this file is subject to the VTE Reports license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is Vtiger Experts s.r.o.
 * Portions created by Vtiger Experts s.r.o. are Copyright(C) Vtiger Experts s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

Vtiger_Index_Js("VTEReports_Edit_Js",{

    instance : {}

},{
    create_adv_filter_div : true,
    currentInstance : false,

    reportsContainer : false,

    reporttype_step_li_id : "rtypestep",
    steps_count : 11,

    init : function() {
        this.initiateReportType();

        if(jQuery("#mode").val()=="create" && jQuery("#reporttype").val()==""){
            this.registerReportTypesClickEvents();
            // image width_calculation
            var total_width = window.innerWidth;
            var one_row_width = (total_width/5);
            var image_row_width = one_row_width-((one_row_width/100)*30);
            jQuery("#rt1").attr('width',image_row_width+'px');
            jQuery("#rt2").attr('width',image_row_width+'px');
            jQuery("#rt3").attr('width',image_row_width+'px');
            jQuery("#rt4").attr('width',image_row_width+'px');
        }else{
            var statusToProceed = this.proceedRegisterEvents();
            if(!statusToProceed){
                return;
            }
            this.initiate();
        }
    },
    proceedRegisterEvents : function(){
        if(jQuery('.recordEditView').length > 0){
            return true;
        }else{
            return false;
        }
    },
    /**
     * Function to get the container which holds all the reports elements
     * @return jQuery object
     */
    getContainer : function() {
        return this.reportsContainer;
    },

    /**
     * Function to set the reports container
     * @params : element - which represents the reports container
     * @return : current instance
     */
    setContainer : function(element) {
        this.reportsContainer = element;
        return this;
    },


    /*
     * Function to return the instance based on the step of the report
     */
    getInstance : function(step) {
        if(step in VTEReports_Edit_Js.instance ){
            return VTEReports_Edit_Js.instance[step];
        } else {
            var moduleClassName = 'VTEReports_Edit'+step+'_Js' ;
            VTEReports_Edit_Js.instance[step] =  new window[moduleClassName]();
            return VTEReports_Edit_Js.instance[step]
        }
    },

    /*
     * Function to get the value of the step
     * returns 1 or 2 or 3
     */
    getStepValue : function(){
        var container = this.currentInstance.getContainer();
        return jQuery('.step',container).val();
    },

    /*
     * Function to initiate the step 1 instance
     */
    initiate : function(container){
        if(typeof container == 'undefined') {
            container = jQuery('.reportContents');
        }
        if(container.is('.reportContents')) {
            this.setContainer(container);
        }else{
            this.setContainer(jQuery('.reportContents',container));
        }
        this.initiateStep('1');
        this.currentInstance.registerEvents();
    },
    /*
     * Function to initiate all the operations for a step
     * @params step value
     */
    initiateStep : function(stepVal) {
        var step = 'step'+stepVal;
        this.activateHeader(step);
        var currentInstance = this.getInstance(stepVal);
        this.currentInstance = currentInstance;
    },

    /*
     * Function to initiate all the operations for a ReportType settings
     * @params step value
     */
    initiateReportType : function() {
        var reporttype = jQuery("#reporttype").val();
        var reporttype_steps = [];
        reporttype_steps["tabular"] = [1,5,6,7,8,9,10];
        reporttype_steps["summaries"] = [1,4,7,8,9,10,11];
        reporttype_steps["summaries_w_details"] = [1,4,5,7,8,9,10,11];
        reporttype_steps["summaries_matrix"] = [1,4,7,8,9,10,11];

        var actual_steps = reporttype_steps[reporttype];
        for (stepi = 1; stepi <= this.steps_count; stepi++) {
            if ( jQuery("#"+this.reporttype_step_li_id+stepi).length  > 0) {
                if(jQuery.inArray( stepi, actual_steps) < 0){
                    jQuery("#"+this.reporttype_step_li_id+stepi).addClass("disabledTable");
                }
            }
        }
        if(reporttype != "tabular"){
            jQuery("#step5").remove();
        }
        else{
            jQuery("#step4").remove();
            jQuery("#step11").addClass('hide');
        }
        this.initiateAdvReportType();
    },

    /*
     * Function to initiate all the operations for a Additional ReportType settings
     * @params value
     */
    initiateAdvReportType : function() {
        var reporttype = jQuery("#reporttype").val();
        if(reporttype=="summaries_w_details"){
            jQuery("#group2_table_row").addClass("disabledTable");
            jQuery("#group3_table_row").addClass("disabledTable");
        }

        jQuery('#reportypetab').find('.marginRight5px').children('a').on('click',function(e){
            var currentAnchor = jQuery(this);
            var activeStepInfo = currentAnchor.attr("id");

            jQuery('#reportypeInfoTab').find('.reporttypeInfo').removeClass('visible');
            jQuery('#reportypeInfoTab').find('.reporttypeInfo').addClass('disabledTable');
            var reporttype_steps = [];
            reporttype_steps["tabular"] = "reporttype1";
            reporttype_steps["summaries"] = "reporttype2";
            reporttype_steps["summaries_w_details"] = "reporttype3";
            reporttype_steps["summaries_matrix"] = "reporttype4";

            jQuery('#reportypeInfoTab').find('#'+reporttype_steps[activeStepInfo]).removeClass('disabledTable');
            jQuery('#reportypeInfoTab').find('#'+reporttype_steps[activeStepInfo]).addClass('disabledTable');
        })

    },

    /*
     * Function to activate the header based on the class
     * @params class name
     */
    activateHeader : function(step) {
        var headersContainer = jQuery('#reportBreadCrumbs');
        headersContainer.find('.active').removeClass('active');
        jQuery('.'+step,headersContainer).addClass('active');
    },
    /*
     * Function to register the click event for next button
     */
    registerFormSubmitEvent : function(form) {
        var thisInstance = this;
        if(jQuery.isFunction(thisInstance.currentInstance.submit)){
            form.on('submit',function(e){
                var form = jQuery(e.currentTarget);
                var specialValidation = true;
                if(jQuery.isFunction(thisInstance.currentInstance.isFormValidate)){
                    var specialValidation =  thisInstance.currentInstance.isFormValidate();
                }
                if ( form.validationEngine('validate') && specialValidation) {
                    thisInstance.currentInstance.submit().then(function(data){
                        thisInstance.getContainer().append(data);
                        var stepVal = thisInstance.getStepValue();
                        var nextStepVal = parseInt(stepVal) + 1;
                        thisInstance.initiateStep(nextStepVal);
                        thisInstance.currentInstance.initialize();
                        var container = thisInstance.currentInstance.getContainer();
                        thisInstance.registerFormSubmitEvent(container);
                        thisInstance.currentInstance.registerEvents();
                    });

                }
                e.preventDefault();
            })
        }
    },

    back : function(){
        var step = this.getStepValue();
        var prevStep = parseInt(step) - 1;
        this.currentInstance.initialize();
        var container = this.currentInstance.getContainer();
        container.remove();
        this.initiateStep(prevStep);
        this.currentInstance.getContainer().show();
    },

    cancelBtn : function(){
        window.location.href=jQuery("#cancel_btn_url").val();
    },

    /*
     * Function to register the click event for back step
     */
    registerReportTypesClickEvents : function(){
        var buttonBackEl = jQuery('.backStep');
        buttonBackEl.on('click',function(e){
            window.location.href="index.php?module=VTEReports&view=List";
        });
        var buttonBackEl2 = jQuery('#createReport');
        buttonBackEl2.on('click',function(e){
            var reporttype = jQuery('#reportypetab').find('.active').children('a').attr("id");
            window.location.href="index.php?module=VTEReports&view=Edit&folder=&reporttype="+reporttype;
        });
    },

    /*
     * Function to register the click event for back step
     */
    registerBackStepClickEvent : function(){
        var thisInstance = this;

        var buttonBackEl = jQuery('#back_rep_top');
        buttonBackEl.on('click',function(e){
            thisInstance.changeStepsBack();
        });
    },
    registerAddFolderAction: function(){
        var thisInstance = this;
        jQuery(document).on("click", "#addFolder", function() {
            var url = jQuery(this).data("url");
            thisInstance.triggerAddFolder(url);
        });
    },
    triggerAddFolder: function(url) {
        var thisInstance = this;
        var actionParams = {
            url: url
        };
        app.request.post(actionParams).then(
            function (err,data) {
                if(err === null) {
                    var callBackFunction = function () {
                        var form = jQuery('#addFolder');
                        //return false;
                        thisInstance.folderSubmit();
                        //form.submit(function (e) {
                        //    e.preventDefault();
                        //})
                    };
                    //app.helper.showModal(data,{'cb' : function (){
                    //    if (typeof callBackFunction == 'function') {
                    //        callBackFunction();
                    //    }
                    //}}, {'width': '400px'});
                    var params = {};
                    params.cb = callBackFunction;
                    //app.helper.showModal(data, params);
                    app.helper.showModal(data, params);
                }
            }
        );
    },
    folderSubmit : function (){
        var a = jQuery.Deferred();
        jQuery(document).on("click", "#saveFolderButton", function() {
            var foldername = jQuery("#foldername").val();
            if (foldername.trim() == ""){
                params = {
                    title: app.vtranslate('CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                jQuery("#foldername").focus();
                return false;

            }
            else{
                var actionParams = jQuery("#addFolder").serializeArray();
                var actionParams = {
                    "type": "POST",
                    "url": "index.php?module=VTEReports&action=Folder&mode=save",
                    "dataType": "json",
                    "data": {'foldername': foldername,'description':jQuery('#description').val()}
                };
                app.request.post(actionParams).then(
                    function(err,data){
                        if(err === null) {
                            if(data.success){
                                var folderInfo = data.info;
                                var b = folderInfo.folderId, c = jQuery("#reportfolder");
                                var new_opt = '<option  value="' + folderInfo.folderId + '" selected>' + folderInfo.folderName + "</option>";
                                jQuery("#reportfolder").append(new_opt).trigger("liszt:updated");
                                app.hideModalWindow();
                            }
                            else{
                                var message = data.error.message;
                                params = {
                                    title: message,
                                    type: 'error'
                                };
                                Vtiger_Helper_Js.showPnotify(params);
                                jQuery("#foldername").focus();
                                return false;
                            }
                        }
                    }
                );
            }
        });
    },
    updateCustomFilter: function(a) {
        var b = a.folderId, c = jQuery("#customFilter");
        a = this.constructOptionElement(a);
        b = jQuery("#filterOptionId_" + b);
        0 < b.length ? (b.replaceWith(a), c.trigger("liszt:updated")) : c.find("#foldersBlock").append(a).trigger("liszt:updated");
    },
    /*
     * Function to register the click event for back step
     */
    registerNextStepClickEvents : function(){
        var thisInstance = this;
        var buttonNextEl = jQuery('#next_rep_top');
        buttonNextEl.on('click',function(e){
            thisInstance.changeStepsNext();
        });
        var savebtn = jQuery('#savebtn');
        savebtn.on('click',function(e){
            return thisInstance.changeSteps('Save');
        });
        var saverunbtn = jQuery('#saverunbtn');
        saverunbtn.on('click',function(e){
            return thisInstance.changeSteps('Save&Run');
        });
        var cancelbtn = jQuery('#cancelbtn');
        cancelbtn.on('click',function(e){
            thisInstance.cancelBtn();
        });
        var cancelbtn0T = jQuery('#cancelbtn0T');
        cancelbtn0T.on('click',function(e){
            thisInstance.cancelBtn();
        });
        var cancelbtn0B = jQuery('#cancelbtn0B');
        cancelbtn0B.on('click',function(e){
            thisInstance.cancelBtn();
        });


    },

    displayButtons : function(step){
        var reporttype = jQuery("#reporttype").val();
        var last_step = 11;
        if(reporttype=="tabular"){
            var last_step = 10;
        }
        if ((document.getElementById('mode').value != 'create' && document.getElementById('mode').value != '') || step == last_step) {
            document.getElementById('submitbutton').style.display = 'inline';
            document.getElementById('submitbutton0B').style.display = 'none';
        } else {
            document.getElementById('submitbutton').style.display = 'none';
            document.getElementById('submitbutton0B').style.display = 'inline';
        }
    },

    checkEmptyFirstStep : function(step){
        if(document.getElementById('actual_step').value<2){
            var reportname = jQuery('#reportname').val();
            if(reportname==""){
                params = {
                    title: app.vtranslate('MISSING_REPORT_NAME'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                jQuery("#reportname").focus();
                this.markSelectedTab(document.getElementById('actual_step').value);
                return false;
            }
            var primarymodule = jQuery('#primarymodule').val();
            if(primarymodule==""){
                params = {
                    title: app.vtranslate('MISSING_PRIMARYMODULE'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                this.markSelectedTab(document.getElementById('actual_step').value);
                return false;
            }
        }
        return true;
    },
    registerRemoveSelectedField:function(){
        jQuery('.removeSelectedField').on('click',function(){
            var parent_tr = jQuery(this).closest('tr');
            var field_info = parent_tr.children('td.td_field_input_control').children('input:first').attr('id');
            jQuery('.div_column_child').each(function(){
                var this_div = jQuery(this);
                var this_id = this_div.attr('id');
                if(field_info.indexOf(this_id) !== -1){
                    this_div.removeClass('div_column_child_on');
                    this_div.addClass('div_column_child_off');
                }
            });
            parent_tr.remove();
        });
    },

    registerChoiceColumn:function(){
        var reporttype = jQuery("#reporttype").val();
        jQuery(document).on("click", ".div_column_child", function() {
            if(jQuery(this).hasClass('div_column_child_off')){
                jQuery(this).removeClass('div_column_child_off');
                jQuery(this).addClass('div_column_child_on');
                //addToSummSortOrder(availListObj.options[i]);
                var newOptObj = document.createElement("OPTION");
                newOptObj.value = jQuery(this).attr('id');
                newOptObj.text = jQuery(this).find('p').html();
                if(reporttype == "tabular") addToSortOrder(newOptObj);
                else addToSummSortOrder(newOptObj);
            }
            else{
                jQuery(this).removeClass('div_column_child_on');
                jQuery(this).addClass('div_column_child_off');
                //delSummSortOrder(availListObj.options[i]);
                var newOptObj = document.createElement("OPTION");
                newOptObj.value = jQuery(this).attr('id');
                newOptObj.text = jQuery(this).find('p').html();
                if(reporttype == "tabular") delSortOrder(newOptObj);
                else delSummSortOrder(newOptObj);
            }
        });
    },
    changeSteps4U: function(step,stype) {

        var thisInstance = this;
        var reporttype = jQuery("#reporttype").val();
        // can not change step while module and name is not defined !

        if(stype=="step"){
            if(this.checkEmptyFirstStep(step)==false){
                return false;
            }
        }
        document.getElementById('actual_step').value = step;

        var gotoStep = "step" + step;
        if (!gotoStep) {
            return false;
        } else if (gotoStep=="step9" || gotoStep == "step10") {
            if (gotoStep == "step10"){
				
				// enable elemant "selectedRecipients" and "selectedAdditionalsString"
                var activeFrequency = jQuery('input[name="isReportScheduled"]').prop('checked');
                if (activeFrequency == true){
                    jQuery('input#scheduledTime').prop('disabled', '');
                    jQuery('select#selectedRecipients').prop('disabled', '');
                    jQuery('input[name="selectedAdditionalsString"]').prop('disabled', false);
                }else{
                    jQuery('input#scheduledTime').prop('disabled', true);
                    jQuery('select#selectedRecipients').select2('data','');
                    jQuery('select#selectedRecipients').prop('disabled', 'disabled');
                    jQuery('input[name="selectedAdditionalsString"]').val('').prop('disabled', true);
                }

                jQuery('input[name="isReportScheduled"]').on('change',function () {
                    var activeFrequency = jQuery(this).prop('checked');
                    if (activeFrequency == true){
                        jQuery('input#scheduledTime').prop('disabled', '');
                        jQuery('select#selectedRecipients').prop('disabled', '');
                        jQuery('input[name="selectedAdditionalsString"]').prop('disabled', false);
                    }else{
                        jQuery('input#scheduledTime').prop('disabled', true);
                        jQuery('select#selectedRecipients').select2('data','');
                        jQuery('select#selectedRecipients').prop('disabled', 'disabled');
                        jQuery('input[name="selectedAdditionalsString"]').val('').prop('disabled', true);
                    }
                });
				
                //showRecipientsOptions();
                var recipient_li = jQuery("div[id*='selectedRecipients'] > ul > li.select2-search-choice");
                recipient_li.each(function(){
                    var this_text = jQuery(this).children('div:first-child').html();
                    var target_opt_css = jQuery('#selectedRecipients option:contains("' + this_text + '")').attr('class');
                    jQuery(this).css("background-image","none");
                    jQuery(this).addClass(target_opt_css);
                });
            }
            else{
                var recipient_li = jQuery("div[id*='sharingSelectedColumns'] > ul > li.select2-search-choice");
                recipient_li.each(function(){
                    var this_text = jQuery(this).children('div:first-child').html();
                    var target_opt_css = jQuery('#sharingSelectedColumns option:contains("' + this_text + '")').attr('class');
                    jQuery(this).css("background-image","none");
                    jQuery(this).addClass(target_opt_css);
                });
            }
            thisInstance.showTabContainer(gotoStep);
            thisInstance.displayButtons(step);
        } else {
            if (step == 1) {
                jQuery('#back_rep_top').css("background","#c3c3c3");
                jQuery('#back_rep_top').css("cursor","not-allowed");
                thisInstance.registerAddFolderAction();
            } else{
                jQuery('#back_rep_top').css("background","#ffffff");
                jQuery('#back_rep_top').css("cursor","pointer");
                jQuery('#back_rep_top').bind("click");
            }
            var newurl = '';

            var record = document.NewReport.record.value;

            var selectedPrimaryIndex = document.getElementById('primarymodule').selectedIndex;
            var selectedPrimaryModule = document.getElementById('primarymodule').options[selectedPrimaryIndex].value;

            var reportname = jQuery('#reportname').val();
            var reportdesc = jQuery('#reportdesc').val();
            var template_owner = jQuery('#template_owner').val();
            var sharing = jQuery('#sharing').val();

            var selectedFolderIndex = document.getElementById('reportfolder').selectedIndex;
            var selectedFolderValue = document.getElementById('reportfolder').options[selectedFolderIndex].value;

            var curl = this.getCurl();
            var lblurl = this.getLabelsurl();

            var limit = jQuery('#limit').val();
            var isReportScheduled = jQuery('#isReportScheduled').is(':checked');

            var sharingSelectedStr = this.getUpSelectedSharing();
            var postData = {
                "step": gotoStep,
                "record": record,
                "reportmodule": selectedPrimaryModule,
                "primarymodule": selectedPrimaryModule,
                "reportname": reportname,
                "reporttype": reporttype,
                "reportdesc": reportdesc,
                "template_owner": template_owner,
                "sharing": sharing,
                "sharingSelectedColumns": sharingSelectedStr,
                "folderid": selectedFolderValue,
                "curl": curl,
                "lblurl": lblurl,
                "limit": limit,
                "isReportScheduled": isReportScheduled
            };

            var relatedmodules = '';
            var all_related_modules_str = '';
            if(document.getElementById('all_related_modules') !== null) all_related_modules_str = document.getElementById('all_related_modules').value;
            if (all_related_modules_str != '') {
                var all_related_modules = all_related_modules_str.split(":");
                for (i = 0; i <= (all_related_modules.length - 1); i++) {
                    var rel_mod_actual = 'relmodule_' + all_related_modules[i];
                    var actual_rel_module = document.getElementById(rel_mod_actual);
                    if (actual_rel_module.checked)
                        relatedmodules += actual_rel_module.value + ':';
                }
            }

            postData["relatedmodules"] = relatedmodules;
            formSelectedSummariesString();
            postData["selectedSummariesString"] = jQuery("#selectedSummariesString").val();

            for (i = 0; i <= 4; i++) {

                postData["timeline_column"+i] = this.getGroupTimeLineValue(i);
                if (i > 1) postData["timeline_type"+i] = this.getGroupTimeLineType(i);

                var selectedGroup = document.getElementById('Group'+i);
                if (selectedGroup) {
                    var selectedGroupValue = document.getElementById('Group'+i).options[selectedGroup.selectedIndex].value;
                    postData["group"+i] = selectedGroupValue;
                    var return_sort = jQuery('#Sort'+i).val();
                    postData["sort"+i] = return_sort;
                }
            }

            postData["x_group"] = jQuery('#x_group').val();
            postData["charttitle"] = jQuery('#charttitle').val();

            postData["chartType1"] = jQuery('#chartType1').val();
            postData["data_series1"] = jQuery('#data_series1').val();

            postData["chartType2"] = jQuery('#chartType2').val();
            postData["data_series2"] = jQuery('#data_series2').val();

            postData["chartType3"] = jQuery('#chartType3').val();
            postData["data_series3"] = jQuery('#data_series3').val();

            if (document.getElementById('summaries_orderby_column')) {
                var summaries_orderby_selectbox = document.getElementById('summaries_orderby_column');
                var summaries_orderby = summaries_orderby_selectbox.options[summaries_orderby_selectbox.selectedIndex].value;

                var summaries_orderby_type = "";
                var summaries_orderby_Radios = document.getElementsByName('summaries_orderby_type');
                for (var i = 0, length = summaries_orderby_Radios.length; i < length; i++) {
                    if (summaries_orderby_Radios[i].checked) {
                        summaries_orderby_type = summaries_orderby_Radios[i].value;
                        break;
                    }
                }
                if (summaries_orderby_type == "") {
                    summaries_orderby_type = "ASC";
                }

                postData["summaries_orderby"] = summaries_orderby;
                postData["summaries_orderby_type"] = summaries_orderby_type;
            }
            var selectedColumnsStr = "";
            var selectedSummariesString ="";
            jQuery('div.div_column_child_on').each(function(){
                if (selectedColumnsStr != "") {
                    selectedColumnsStr += "<_@!@_>";
                }
                selectedColumnsStr += jQuery(this).attr('id');
                selectedSummariesString += jQuery(this).attr('id') + ";";
            });
            if(step == 7){
                postData["selectedSummariesString"] = selectedSummariesString;
            }
            selectedColumnsStr = replaceAll("&","@AMPKO@",selectedColumnsStr);
            postData["selectedColumnsStr"] = selectedColumnsStr;
            if (gotoStep == 'step5') {
                var url = 'action=IndexAjax&mode=getStep5Columns&module=VTEReports';
            } else {
                var url = 'view=Edit&mode=ChangeSteps&module=VTEReports';
            }

            var actionParams = {
                "type": "POST",
                "url": 'index.php?'+ url,
                "dataType": "html",
                "data": postData
            };
            app.helper.showProgress();
            app.request.post(actionParams).then(
                function(err,data) {
                    if(err === null) {
                        thisInstance.showTabContainer(gotoStep);
                        if (gotoStep == 'step5') {
                            if (document.getElementById('availList'))
                            {
                                document.getElementById('availList').innerHTML = "";
                                document.getElementById('availList').options.length = 0;
                            }
                            var step5_result = data.split("(!A#V_M@M_M#A!)");
                            thisInstance.setStep5Columns(step5_result);
                            var selectedColumnsList = jQuery("#selectedColumnsList").val();
                            var arrSelectedColumnsList = selectedColumnsList.split(";");
                            if(arrSelectedColumnsList.length > 0){
                                jQuery.each(arrSelectedColumnsList, function( index, value ) {
                                    var arrColumns = value.split(":");
                                    var module_names = arrColumns[2];
                                    if(typeof module_names != "undefined"){
                                        var arr_module_names = module_names.split("_");
                                        var find_val = jQuery( "#availModules option:contains('"+app.vtranslate(arr_module_names[0])+"')" ).val();
                                        jQuery( "#availModules").val(find_val);
                                        jQuery( "#availModules").trigger('change');
                                    }
                                });
                            }

                        } else if (gotoStep == 'step8') {
                            var resp_info = data.split("__ADVFTCRI__");

                            var resp_blocks_info = resp_info[0];
                            var criteria_info = JSON.parse(resp_info[1]);
                            document.getElementById("sel_fields").value = resp_info[2];
                            document.getElementById("std_filter_columns").value = resp_info[3];

                            var resp_blocks = resp_blocks_info.split("__BLOCKS__");
                            var FIELD_BLOCKS = resp_blocks[0];
                            // document.getElementById('filter_columns').innerHTML = FIELD_BLOCKS;
                            jQuery("#filter_columns").val(FIELD_BLOCKS);

                            var aviable_fields = FIELD_BLOCKS;
                            var group_fields = FIELD_BLOCKS;
                            if(!document.getElementById("fcol0") && thisInstance.create_adv_filter_div == true){
                                addNewConditionGroup('adv_filter_div');
                                thisInstance.create_adv_filter_div = false;
                            }

                            var sum_group_columns = resp_blocks[1];
                            if(document.getElementById('sum_group_columns') !== null) document.getElementById('sum_group_columns').innerHTML = sum_group_columns;

                            var sortbycolumns = "";
                            if(document.getElementById('SortByColumn') !== null) sortbycolumns = document.getElementById('SortByColumn').innerHTML;
                            var optgroups = FIELD_BLOCKS.split("(|@!@|)");
                            s = document.NewReport.getElementsByTagName('select');

                            for (var i = 0; i < s.length; i++)
                            {
                                if (s[i].name.substring(0, 4) == 'fcol')
                                {
                                    // addNewConditionGroup('adv_filter_div');
                                    var selected_column = "";
                                    var criteria_i = s[i].name.substring(4, 5);
                                    if (criteria_info[criteria_i])
                                        var selected_column = criteria_info[criteria_i]["columnname"];

                                    oldvalue = s[i].value;
                                    s[i].options.length = 0;
                                    s[i].innerHTML = "";
                                    for (k = 0; k < optgroups.length; k++)
                                    {
                                        var optgroup = optgroups[k].split("(|@|)");
                                        if (optgroup[0] != '')
                                        {
                                            var oOptgroup = document.createElement("OPTGROUP");
                                            oOptgroup.label = optgroup[0];

                                            var responseVal = JSON.parse(optgroup[1]);

                                            for (var widgetId in responseVal)
                                            {
                                                if (responseVal.hasOwnProperty(widgetId))
                                                {
                                                    option = responseVal[widgetId];

                                                    var oOption = document.createElement("OPTION");
                                                    oOption.value = option["value"];
                                                    if(option["value"] == selected_column){
                                                        oOption.selected = true;
                                                    }
                                                    oOption.appendChild(document.createTextNode(option["text"]));
                                                    oOptgroup.appendChild(oOption);

                                                }
                                            }
                                            s[i].appendChild(oOptgroup);
                                        }
                                    }
                                    var std_filter_columns = document.getElementById("std_filter_columns").value;
                                    if (typeof std_filter_columns != 'undefined' && std_filter_columns!=""){
                                        var std_filter_columns_arr = std_filter_columns.split('<%jsstdjs%>');
                                    }else{
                                        var std_filter_columns_arr = [];
                                    }
                                    var selectedVal = "";
                                    if(typeof document.getElementById('fop'+criteria_i) != "undefined"){
                                        if (std_filter_columns_arr.indexOf(s[i].value) > -1) {
                                            selectedVal = document.getElementById('fop'+criteria_i).options[document.getElementById('fop'+criteria_i).selectedIndex].value;
                                        }
                                    }
                                    vtereports_updatefOptions(s[i], 'fop'+criteria_i, selectedVal);

                                    s[i].value = oldvalue;
                                }
                                else if (s[i].name.substring(0, 4) == 'gcol')
                                {
                                    oldvalue = s[i].value;

                                    oldselectvalue = getObj("SortByColumn").value;

                                    if (browser_ie)
                                    {
                                        selectonchange = s[i].onchange;
                                        s[i].outerHTML = '<select id="' + s[i].id + '" name="' + s[i].name + '" class="detailedViewTextBox">' + sortbycolumns + '</select>';
                                        s[i].onchange = selectonchange;
                                    }
                                    else
                                    {
                                        s[i].innerHTML = sortbycolumns;
                                    }


                                    s[i].value = oldvalue;
                                }
                            }

                        } else {

                            var stepTabContainer = jQuery("#"+gotoStep);
                            stepTabContainer.html(data);

                        }
                        if (gotoStep == "step4"){
                            thisInstance.setSummariesOrderBy();
                            var selectedSummariesTmp = jQuery("#selectedSummariesTmp").val();
                            if(selectedSummariesTmp != "" && typeof selectedSummariesTmp != "undefined"){
                                var arrSelectedSummariesTmp = selectedSummariesTmp.split(";");
                                if(arrSelectedSummariesTmp.length > 0){
                                    jQuery.each(arrSelectedSummariesTmp, function( index, value ) {
                                        //##$$##
                                        var column_name =  value.split("##$$##");
                                        var arrColumns = column_name[0].split(":");
                                        var module_names = arrColumns[2];
                                        if(typeof module_names != "undefined"){
                                            var arr_module_names = module_names.split("_");
                                            var find_val = jQuery( "#SummariesModules option:contains('"+arr_module_names[0]+"')" ).val();
                                            jQuery( "#SummariesModules").val(find_val);
                                            jQuery( "#SummariesModules").trigger('change');
                                        }
                                    });
                                }
                            }
                            else{
                                jQuery( "#SummariesModules").trigger('change');
                            }
                        }
                        thisInstance.initiateAdvReportType();
                        app.helper.hideProgress();
                        if(gotoStep == "step11") ChartDataSeries(document.getElementById('x_group'));
                    }
                }
            );

            thisInstance.displayButtons(step);
            if(step == 7){
                thisInstance.registerRemoveSelectedField();
            }
            var last_on_tabular = jQuery('#step10');
            if(step == 11){
                jQuery('#next_rep_top').css("background","#c3c3c3");
                jQuery('#next_rep_top').css("cursor","not-allowed");
            }
            else{
                jQuery('#next_rep_top').css("background","#ffffff");
                jQuery('#next_rep_top').css("cursor","pointer");
            }
        }

    },

    showTabContainer : function(gotoStep){
        var reportTabsEl = jQuery('.reportTab');
        reportTabsEl.each(function(index,element){
            //jQuery(element).addClass("hide");
            jQuery(element).addClass("disabledTable");
        });

        jQuery("#"+gotoStep).removeClass("disabledTable");
        var themeColor = jQuery('.themeSelected').css('background-color');
        jQuery("#test-123").css("background",themeColor + "!important");
    },

    registerTabClickEvent : function(){
        var thisInstance = this;

        var reportTabsEl = jQuery('#reportTabs').find('li').find('a');
        reportTabsEl.each(function(index,element){
            var chosenTab = jQuery(element);
            var actual_step = chosenTab.data('step');
            chosenTab.on('click',function(e){
                if(thisInstance.checkEmptyFirstStep(actual_step)==false){
                    return false;
                }else{
                    thisInstance.changeSteps4U(actual_step,"tab");
                }
            });
        });
    },

    getUpSelectedSharing: function(){
        var sharingSelectedStr = "";
        jQuery('#sharingSelectedColumns option').each(function() {
            if (jQuery(this).is(':selected')) {
                sharingSelectedStr += jQuery(this).val() + '|';
            }
        });
        document.getElementById('sharingSelectedColumnsString').value = sharingSelectedStr;
        return sharingSelectedStr;
    },

    getCurl : function() {
        var c = [];
        curl = "";
        c = document.getElementsByTagName('input');
        for (var i = 0; i < c.length; i++)
        {
            if (c[i].type == 'checkbox')
            {
                Control_Data = c[i].name.split(':');
                if (Control_Data[0] == "cb" && c[i].checked == true)
                {
                    if (curl != "") {
                        curl += "$_@_$";
                    }
                    curl += c[i].name;
                }
            }
        }
        jQuery('#curl_to_go').val(curl);
        return curl;
    },

    getLabelsurl : function() {
        var c = [];
        var lblurl = "";
        c = document.getElementsByTagName('input');
        for (var i = 0; i < c.length; i++)
        {
            if(c[i]){
                var id_text = c[i].id;
                var search_for_lc = "_lLbLl_";
                if (id_text.indexOf(search_for_lc) > -1 && id_text.indexOf("hidden_") == -1) {
                    if (lblurl != "") {
                        lblurl += "$_@_$";
                    }
                    var str_lblurl = c[i].id + "_lLGbGLl_" + c[i].value;
                    str_lblurl = replaceAll("&","@AMPKO@",str_lblurl);
                    lblurl += str_lblurl;
                }
            }
        }

        jQuery('#labels_to_go').val(lblurl);
        return lblurl;
    },

    getGroupTimeLineValue : function(group_i){
        var timeline_frequency = "";
        if(document.getElementsByName('TimeLineColumn_Group'+group_i)){
            timeline_frequency = jQuery("#TimeLineColumn_Group"+group_i).val();
        }
        return timeline_frequency;
    },

    getGroupTimeLineType : function(group_i){
        var timeline_type_val = "";
        if(document.getElementById('timeline_type'+group_i)){
            var timeline_type = document.getElementById('timeline_type'+group_i);
            timeline_type_val = timeline_type.options[timeline_type.selectedIndex].value;
        }
        return timeline_type_val;
    },

    setSummariesOrderBy : function(group_i){
        var summaries_orderby_val = jQuery("#summaries_orderby_val").val();
        setSelSummSortOrder(summaries_orderby_val);
    },
    registerEvents : function(){
        this._super();
        this.registerTabClickEvent();
        this.registerChoiceColumn();
        setObjects();
        this.setSummariesOrderBy();
        this.registerBackStepClickEvent();
        this.registerNextStepClickEvents();
        var container = jQuery('#step8');
        this.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer',container));
        this.registerAddFolderAction();
    },

    changeStepsBack : function(){
        var actual_step = jQuery("#actual_step").val();
        var reporttype = jQuery("#reporttype").val();
        if (actual_step != 1)
        {
            var last_step = actual_step - 1;
            var stop_asi = false;

            while( last_step > -1 ){
                if(jQuery("#"+this.reporttype_step_li_id+last_step).hasClass("disabledTable")){
                    last_step--;
                }else{
                    break;
                }
            }
            if (last_step == 3) {
                last_step = last_step - 2;
            }
            if(reporttype == "tabular"){
                if (actual_step == 5) {
                    last_step = 1;
                }
                if (actual_step == 7) {
                    if( !jQuery('#step7_2').hasClass('disabledTable')){
                        jQuery('#step7_2').addClass("disabledTable");
                        this.scrollToDiv('step7');
                        return false;
                    }
                    else{
                        last_step = 5;
                    }

                }
                if (actual_step == 8) {
                    if( jQuery('#step7_2').hasClass('disabledTable')){
                        jQuery('#step7_2').removeClass("disabledTable");
                        jQuery('#step8').addClass("disabledTable");
                        this.scrollToDiv('step7_2');
                        return false;
                    }
                }
            }
            else{
                if (actual_step == 7) {
                    if( !jQuery('#step7_1').hasClass('disabledTable')){
                        jQuery('#step7_1').addClass("disabledTable");
                        jQuery('#step7').removeClass("disabledTable");
                        this.scrollToDiv('step7');
                        return false;
                    }
                    else{
                        last_step = 4;
                    }
                }
                if (actual_step == 8) {
                    if( jQuery('#step7_1').hasClass('disabledTable')){
                        jQuery('#step7_1').removeClass("disabledTable");
                        jQuery('#step8').addClass("disabledTable");
                        this.scrollToDiv('step7_1');
                        this.setOrderByOnEdit();
                        return false;
                    }
                }
            }
            var changedStep = this.changeSteps4U(last_step,"step");
            if(changedStep!=false){
                this.markSelectedTab(last_step);
                if (last_step == 10)
                {
                    jQuery('#next_rep_top').css("background","#ffffff");
                    jQuery('#next_rep_top').css("cursor","pointer");
                }
            }
            this.scrollToDiv("step"+last_step);
        }
    },
    setOrderByOnEdit: function () {
        var a = jQuery("#reporttype").val();
        jQuery(".div_column_child").each(function () {
            if (jQuery(this).hasClass("div_column_child_on")) {
                var b = document.createElement("OPTION");
                b.value = jQuery(this).attr("id");
                b.text = jQuery(this).find("p").html();
                "tabular" == a ? addToSortOrder(b) : addToSummSortOrder(b)
            }
        })
    },

    scrollToDiv:function(div_id){
        jQuery('html,body').animate({scrollTop: jQuery("#"+div_id).offset().top - 100},500);
        if(div_id == "step7_1"){
            jQuery('#step7').addClass("disabledTable");
            var container = jQuery('#step7_1');
            vtUtils.applyFieldElementsView(container);
        }
        return false;
    },
        changeStepsNext : function(){
        var thisInstance = this;
        var actual_step = eval(jQuery("#actual_step").val());
        var reporttype = jQuery("#reporttype").val();
        var go_to_step = actual_step + 1;
        var stop_asi = false;
        if (go_to_step == 2) {
            if(reporttype == "tabular"){
                go_to_step = 5;
            }
            else go_to_step = go_to_step + 2;
        }
        if((actual_step == 4 && reporttype!= "tabular") || (actual_step == 5 && reporttype == "tabular") ){
            go_to_step = 7;
        }

        if(actual_step == 7 && reporttype == "tabular" ){
            jQuery("#step7_1").remove();
            if( jQuery('#step7_2').hasClass('disabledTable')){
                if(jQuery('#step7_2').html()==""){
                    var hdVteTabularGroupAndLimit = jQuery('#div_tabular_limit').html();
                    jQuery('#div_tabular_limit').html('');
                    jQuery('#step7_2').html(hdVteTabularGroupAndLimit);
                }
                jQuery('#step7_2').removeClass("disabledTable");
                thisInstance.scrollToDiv('step7_2');
                return false;
            }
            else{
                jQuery('#step7_2').addClass("disabledTable");
                go_to_step = 8;
            }
        }
        if(actual_step == 7 && reporttype != "tabular" ){
            jQuery("#step7_2").remove();
            if( jQuery('#step7_1').hasClass('disabledTable')){
                if(jQuery('#step7_1').html()==""){
                    var hdVteGroupAndLimit = jQuery('#hdVteGroupAndLimit').html();
                    jQuery('#hdVteGroupAndLimit').html('');
                    jQuery('#step7_1').html(hdVteGroupAndLimit);
                    jQuery('#step7_1').removeClass("disabledTable");
                    thisInstance.scrollToDiv('step7_1');
                }
                jQuery('#step7_1').removeClass("disabledTable");
                thisInstance.scrollToDiv('step7_1');
                thisInstance.setOrderByOnEdit();
                return false;
            }
            else{
                jQuery('#step7_2').addClass("disabledTable");
                go_to_step = 8;
            }
        }
        if(actual_step == 10 && reporttype == "tabular" ){
            go_to_step = 12
        }
        for (asi = go_to_step; asi <= this.steps_count; asi++) {
            if(asi>=go_to_step && stop_asi!=true){
                if(jQuery("#"+this.reporttype_step_li_id+asi).hasClass("disabledTable")){
                    go_to_step = go_to_step + 1;
                }else{
                    stop_asi = true;
                }

            }
        }
        if((go_to_step == 10 && reporttype == "tabular")){
            jQuery('#next_rep_top').css("background","#c3c3c3");
            jQuery('#next_rep_top').css("cursor","not-allowed");
        }
        else{
            jQuery('#next_rep_top').css("background","#ffffff");
            jQuery('#next_rep_top').css("cursor","pointer");
        }
        if (go_to_step != 12)
        {
            var changedStep = this.changeSteps4U(go_to_step,"step");
            if(changedStep!=false){
                this.markSelectedTab(go_to_step);
                this.scrollToDiv("step"+go_to_step);
            }
        }else{
            this.scrollToDiv("submitbutton");
            return false;
        }

    },

    markSelectedTab : function(step){
        var reportTabsEl = jQuery('#reportTabs').find('li');
        reportTabsEl.each(function(index,element){
            var chosenTab = jQuery(element);
            var reportAEl = chosenTab.find('a');
            var tab_step = reportAEl.data('step');
            if(jQuery("#"+this.reporttype_step_li_id+step).hasClass("hide")!=true){
                if(tab_step!=step){
                    chosenTab.removeClass("active");
                }else{
                    chosenTab.addClass("active");
                }
            }
        });
    },

    changeSteps : function(savetype){
        if(!savetype || savetype==""){
            savetype = "Save&Run";
        }
        var report_name_val = jQuery("#reportname").val();
        var activeFrequency = jQuery('input[name="isReportScheduled"]').prop('checked');
        var scheduledTime = jQuery('input[name="scheduledTime"]').val();
        if (activeFrequency && (scheduledTime == '' || scheduledTime.length == 0)){
            params = {
                title: app.vtranslate('MISSING_SCHEDULED_TIME'),
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            jQuery('input[name="scheduledTime"]').focus();
            return false;
        }
        if (report_name_val == "")
        {
            params = {
                title: app.vtranslate('MISSING_REPORT_NAME'),
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            jQuery("#reportname").focus();
            return false;
        }
        else
        {
            var isScheduledObj = getObj("isReportScheduled");
            if (isScheduledObj.checked == true) {
                var selectedRecipientsObj = getObj("selectedRecipients");

                if (selectedRecipientsObj.options.length == 0) {
                    params = {
                        title: app.vtranslate('RECIPIENTS_CANNOT_BE_EMPTY'),
                        type: 'error'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                    return false;
                }

                var selectedUsers = [];
                var selectedGroups = [];
                var selectedRoles = [];
                var selectedRolesAndSub = [];
                jQuery('#selectedRecipients option').each(function() {
                    if (jQuery(this).is(':selected')) {
                        var selectedCol = jQuery(this).val();
                        var selectedColArr = selectedCol.split(":");
                        if (selectedColArr[0] == "Users")
                            selectedUsers.push(selectedColArr[1]);
                        else if (selectedColArr[0] == "Groups")
                            selectedGroups.push(selectedColArr[1]);
                        else if (selectedColArr[0] == "Roles")
                            selectedRoles.push(selectedColArr[1]);
                        else if (selectedColArr[0] == "RoleAndSubordinates")
                            selectedRolesAndSub.push(selectedColArr[1]);
                    }
                });
                var selectedRecipients = {users: selectedUsers, groups: selectedGroups,
                    roles: selectedRoles, rs: selectedRolesAndSub};
                var selectedRecipientsJson = JSON.stringify(selectedRecipients);
                document.NewReport.selectedRecipientsString.value = selectedRecipientsJson;

                var scheduledInterval = {scheduletype: document.NewReport.scheduledType.value,
                    month: document.NewReport.scheduledMonth.value,
                    date: document.NewReport.scheduledDOM.value,
                    day: document.NewReport.scheduledDOW.value,
                    time: document.NewReport.scheduledTime.value
                };

                var scheduledIntervalJson = JSON.stringify(scheduledInterval);
                document.NewReport.scheduledIntervalString.value = scheduledIntervalJson;

                var curl = "";
                c = document.getElementsByTagName('input');
                for (var i = 0; i < c.length; i++)
                {
                    if (c[i].type == 'checkbox')
                    {
                        Control_Data = c[i].name.split(':');
                        if (Control_Data[0] == "cb" && c[i].checked == true)
                        {
                            if (curl != "") {
                                curl += "$_@_$";
                            }
                            curl += c[i].name;
                        }
                    }
                }
                document.NewReport.curl_to_go.value = curl;
            }
            this.getUpSelectedSharing();
            if (savetype != "") {
                document.getElementById("SaveType").value = savetype;
            }
            if (this.saveAndRunReport()) {
                document.NewReport.submit();
            }
        }
    },

    saveAndRunReport : function(){
        var primarymoduleObj = getObj("primarymodule"); // reportfolder
        var selectedColumn = primarymoduleObj.value;
        document.NewReport.report_primarymodule.value = selectedColumn;
        if (jQuery('#include_net_price').is(':checked')){
            document.NewReport.include_net_price.value = 1;
        }

        var selectedColumnsObj = document.getElementById('selectedColumns');
        var selectedSummariesObj = document.getElementById('selectedSummaries');

        var selectedGroup1 = document.getElementById('Group1');
        var selectedGroup1Value = "";
        if(document.getElementById('Group1') !== null) selectedGroup1Value = document.getElementById('Group1').options[selectedGroup1.selectedIndex].value;

        var selectedGroup2 = document.getElementById('Group2');
        var selectedGroup2Value = "";
        if(document.getElementById('Group2') !== null) selectedGroup2Value = document.getElementById('Group2').options[selectedGroup2.selectedIndex].value;
        if(selectedGroup2Value=="none"){
            document.getElementById('timeline_type2').options[0].selected =true;
        }

        var selectedGroup3 = document.getElementById('Group3');
        var selectedGroup3Value = "";
        if(selectedGroup3 !== null) selectedGroup3Value = selectedGroup3.options[selectedGroup3.selectedIndex].value;
        if(selectedGroup3Value=="none"){
            document.getElementById('timeline_type3').options[0].selected =true;
        }
        var selectedGrou4 = document.getElementById('Group4');
        var selectedGroup4Value = "";
        if(selectedGrou4 !== null) selectedGrou4.options[selectedGroup3.selectedIndex].value;
        if(selectedGroup4Value=="none"){
            document.getElementById('timeline_type4').options[0].selected =true;
        }
        var reporttype = jQuery("#reporttype").val();
        if(reporttype=="tabular"){
            //if (selectedColumnsObj.length == 0){
            if (jQuery('div.div_column_child_on').length == 0){
                params = {
                    title: app.vtranslate('COLUMNS_CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
        }else if(reporttype=="summaries_w_details"){
            if(selectedGroup1Value=="none"){
                params = {
                    title: app.vtranslate('CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
            if (selectedColumnsObj.length == 0 && selectedSummariesObj.length == 0){
                params = {
                    title: app.vtranslate('COLUMNS_CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
        }else{
            if(selectedGroup1Value=="none"){
                params = {
                    title: selectedGroup1.name+app.vtranslate('CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
            //if (selectedSummariesObj.length == 0){
            if (jQuery('div.div_column_child_on').length == 0){
                params = {
                    title: app.vtranslate('COLUMNS_CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }
        }
        var relatedmodules = '';
        var all_related_modules_str = "";
        if(document.getElementById('all_related_modules') !== null)   document.getElementById('all_related_modules').value;
        if (all_related_modules_str != '') {
            var all_related_modules = all_related_modules_str.split(":");
            for (i = 0; i <= (all_related_modules.length - 1); i++)
            {
                var rel_mod_actual = 'relmodule_' + all_related_modules[i];
                actual_rel_module = document.getElementById(rel_mod_actual);
                if (actual_rel_module.checked)
                    relatedmodules += actual_rel_module.value + ':';
            }
        }
        document.NewReport.secondarymodule.value = relatedmodules;

        if (document.getElementById('summaries_orderby_column')) {
            var summaries_orderby_selectbox = document.getElementById('summaries_orderby_column');
            var summaries_orderby = summaries_orderby_selectbox.options[summaries_orderby_selectbox.selectedIndex].value;

            var summaries_orderby_type = "";
            var summaries_orderby_Radios = document.getElementsByName('summaries_orderby_type');
            for (var i = 0, length = summaries_orderby_Radios.length; i < length; i++) {
                if (summaries_orderby_Radios[i].checked) {
                    summaries_orderby_type = summaries_orderby_Radios[i].value;
                    break;
                }
            }
            if (summaries_orderby_type == "") {
                summaries_orderby_type = "ASC";
            }
            document.getElementById("summaries_orderby_columnString").value = summaries_orderby;
            document.getElementById("summaries_orderby_typeString").value = summaries_orderby_type;
        }


        var escapedOptions = ['account_id', 'contactid', 'contact_id', 'product_id', 'parent_id', 'campaignid', 'potential_id', 'assigned_user_id1', 'quote_id', 'accountname', 'salesorder_id', 'vendor_id', 'time_start', 'time_end', 'lastname'];

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

                if(conditionColumns.length>1){
                    if (!emptyCheckVte(columnId, " Column ", "text")){
                        // i < conditionColumns.length
                        return false;
                    }
                    if (!emptyCheckVte(comparatorId, selectedColumnLabel + " Option", "text")){
                        return false;
                    }
                }

                var col = selectedColumn.split(":");

                var std_filter_columns = document.getElementById("std_filter_columns").value;
                if (typeof std_filter_columns != 'undefined' && std_filter_columns!=""){
                    var std_filter_columns_arr = std_filter_columns.split('<%jsstdjs%>');
                }else{
                    var std_filter_columns_arr = [];
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
                    //var start_date = document.getElementById("jscal_field_sdate"+columnIndex).value;
                    //var end_date = document.getElementById("jscal_field_edate"+columnIndex).value;
                    var start_date = jQuery("#jscal_field_sdate_val_"+columnIndex).val();
                    var end_date = jQuery("#jscal_field_edate_val_"+columnIndex).val();
                    var specifiedValue = start_date+"<;@STDV@;>"+end_date;
                }else{
                    if (escapedOptions.indexOf(col[3]) == -1) {
                        if (col[4] == 'T') {
                            var datime = specifiedValue.split(" ");
                            if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH"))
                                return false;
                            if (datime.length > 1)
                                if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                                    return false
                        }
                        else if (col[4] == 'D')
                        {
                            if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                return false;
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
                        if (sel_fields[selectedColumn] ) {
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
                //Added to handle yes or no for checkbox fields in reports advance filters.
                if (col[4] == "C") {
                    if (specifiedValue == "1")
                        specifiedValue = getObj(valueId).value = 'yes';
                    else if (specifiedValue == "0")
                        specifiedValue = getObj(valueId).value = 'no';
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
        jQuery('#advft_criteria').val(JSON.stringify(criteriaConditions));
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
        jQuery('#advft_criteria_groups').val(JSON.stringify(criteriaGroups));

        // groupconditioncolumn start
        var GroupconditionColumns = vt_getElementsByName('tr', "groupconditionColumn");
        var GroupcriteriaConditions = [];
        // VTE-CR SlOl 26. 3. 2014 13:26:01 SELECTBOX VALUES INTO FILTERS
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
                        // i < GroupconditionColumns.length
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
                            return false;
                        if (datime.length > 1)
                            if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                                return false
                    }
                    else if (col[4] == 'D')
                    {
                        if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                            return false;
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
        jQuery('#groupft_criteria').val(JSON.stringify(GroupcriteriaConditions));
        formSelectedColumnString();
        formSelectedSummariesString();
        getCurl();
        getLabelsurl();
        if(jQuery('#chartType1').val()!='none' && jQuery('#data_series1').val()=='none'){
            params = {
                title: app.vtranslate('Graph1','VTEReports')+" "+app.vtranslate('LBL_CHART_DataSeries','VTEReports')+" "+app.vtranslate('CANNOT_BE_EMPTY','VTEReports'),
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }
        if(jQuery('#chartType2').val()!='none' && jQuery('#data_series2').val()=='none'){
            params = {
                title: app.vtranslate('Graph2','VTEReports')+" "+app.vtranslate('LBL_CHART_DataSeries','VTEReports')+" "+app.vtranslate('CANNOT_BE_EMPTY','VTEReports'),
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }
        if(jQuery('#chartType3').val()!='none' && jQuery('#data_series3').val()=='none'){
            params = {
                title: app.vtranslate('Graph3','VTEReports')+" "+app.vtranslate('LBL_CHART_DataSeries','VTEReports')+" "+app.vtranslate('CANNOT_BE_EMPTY','VTEReports'),
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            return false;
        }
        //for highlight save
        var arr_highlight = [];
        jQuery('.v_highlight').each(
            function(){
                if(jQuery(this).is(":checked")){
                    arr_highlight.push(jQuery(this).attr('name')) ;
                }
            }
        );
        jQuery("#labels_to_highlights").val(arr_highlight);
        return true;
    },

    setStep5Columns : function(step5_result){
        var thisInstance = this;
        var availablemodules = JSON.parse(step5_result[0]);
        var aviable_fields = step5_result[1];

        var avaimodules_sbox = document.getElementById('availModules');
        avaimodules_sbox.innerHTML = "";
        avaimodules_sbox.options.length = 0;
        for (var widgetId in availablemodules)
        {
            if (availablemodules.hasOwnProperty(widgetId))
            {
                var option = availablemodules[widgetId];
                var oOption = document.createElement("OPTION");
                oOption.value = option["id"];
                if (option["checked"] == "checked") {
                    oOption.checked = true;
                    var option_name = option["name"];
                } else {
                    var option_name = "- " + option["name"];
                }
                oOption.appendChild(document.createTextNode(option_name));
                avaimodules_sbox.appendChild(oOption);
            }
        }
        thisInstance.setAvailableFields(aviable_fields)
    },

    setAvailableFields : function(aviable_fields){
        document.getElementById("availModValues").innerHTML = aviable_fields;

        var mod_groups_a2 = aviable_fields.split("(!#_ID@ID_#!)");
        var module_groupid = mod_groups_a2[0];
        var module_name = mod_groups_a2[1];
        var aviable_fields = mod_groups_a2[2];

        var availModules = document.getElementById("availModules");
        var selectedModule = availModules.options[availModules.selectedIndex].value;
        var reporttype = jQuery("#reporttype").val();
        var selectedColumnsList = jQuery("#selectedColumnsList").val();
        if(reporttype != "tabular") selectedColumnsList = jQuery("#selectedSummariesTmp").val();
        var arrSelectedColumnsList = selectedColumnsList.split(";");
        if (module_groupid == selectedModule) {
            optgroups = aviable_fields.split("(|@!@|)");
            for (i = 0; i < optgroups.length; i++)
            {
                var optgroup = optgroups[i].split("(|@|)");
                if (optgroup[0] != '')
                {
                    //var oOptgroup = document.createElement("OPTGROUP");
                    //oOptgroup.label = optgroup[0];
                    var responseVal = JSON.parse(optgroup[1]);
                    var block_title_div = document.createElement("div");
                    block_title_div.className = "block_title_div";
                    var title_block = optgroup[0];
                    title_block = title_block.substring(3);//Remove - char on first
                    block_title_div.innerHTML = '<p class="block_title_content">'+title_block+'</p>';
                    for (var widgetId in responseVal)
                    {
                        if (responseVal.hasOwnProperty(widgetId))
                        {
                            var option = responseVal[widgetId];
                            var oOption = document.createElement("div");
                            oOption.id = option["value"];
                            var num_pos = jQuery.inArray( option["value"], arrSelectedColumnsList );
                            if(num_pos >= 0){
                                arrSelectedColumnsList.splice(num_pos, 1);
                                oOption.className  = "div_column_child div_column_child_on";
                            }
                            else{
                                oOption.className  = "div_column_child div_column_child_off";
                            }
                            var img = document.createElement("img");
                            img.src = "modules/VTEReports/img/check_ico.png";
                            oOption.appendChild(img);
                            var preOption = document.createElement("p");
                            var txt_div = document.createTextNode(option["text"]);
                            preOption.appendChild(txt_div);
                            oOption.appendChild(preOption);
                            block_title_div.appendChild(oOption);
                            if( document.getElementById(option["value"]) === null) document.getElementById('div_list_fields_column').appendChild(block_title_div);
                        }
                    }
                }
            }
        }
    }

});

