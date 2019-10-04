jQuery.Class('Macros',{
    initData_Macros: function () {
        var current_url = $.url();
        var module = current_url.param('module');
        var view = current_url.param('view');
        var action = current_url.param('action');
        var parent = current_url.param('parent');
        var record = current_url.param('record');
        // Only load when loadHeaderScript=1 END #241208
        // add Macros button
        if(view=="Detail" && record != undefined) {
            var progressIndicatorInstance = $.progressIndicator({});
            var actionParams = {
                "action":"MacrosAjax",
                "mode":"getMacrosWorkflows",
                "module":"Macros",
                "record": record,
                "source_module": module
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    if(data) {
                        var size = 0;
                        $.each(data.result, function(id){
                            size++;
                        });
                        if(size>0) {
                            var html =
                                '<div class="modal-header">'
                                +'<button type="button" class="close" data-dismiss="modal" aria-label="Close"'
                                +'<span aria-hidden="true">&times;</span>'
                                +'</button>'
                                +'<h4 class="modal-title">Macros</h4></div>'
                                +'<div class="modal-body">'+
                                '<table class="table" style="width: 500px">'+
                                '<tbody>';
                            $.each(data.result, function (k, detail) {
                                html +=
                                    '<tr>' +
                                    '<td style="width: 90%; font-weight: bold">' +'<a href="javascript: void(0)" style="font-weight: bold" onclick="runMacro(' + detail.id + ',' + record + ')">' + detail.name + '</a>'  + '</td>' +
                                    '<td>' + '<button style="float:right" type="button" onclick="runMacro(' + detail.id + ',' + record + ')" class="btn btn-danger">Run</button>'
                                    +'</td>' +
                                    '</tr>';
                            });
                            html += '</body>' +
                                '</table>' +
                                '</div>';

                            app.showModalWindow(html);
                        }else {
                            alert(app.vtranslate('Macros has to be creating using workflows. Please go to Other Settings > Workflows, create new/edit existing workflow and you see under "Specify when to execute this Workflow" there will be new option "Macro"'));
                        }
                        progressIndicatorInstance.hide();

                    }
                },
                function(error,err){

                }
            );

            //console.log(detailViewButtoncontainer);
        }
    }
}, {});

(function($){
    $(function(){
        //document ready
        var current_url = $.url();
        var module = current_url.param('module');
        var view = current_url.param('view');
        var action = current_url.param('action');
        var parent = current_url.param('parent');
        var record = current_url.param('record');

        // add Macros option to Create/Edit Workflow view
        if(module=='Workflows' && parent=='Settings' && view=="Edit") {
            var last_execution_condition = $('[name="execution_condition"]').last();
            var macro_option = $('<div><label><input type="radio" value="7" name="execution_condition" class="alignTop">&nbsp;&nbsp;Macro</label><br><div class="clearfix"></div></div>');

            if(record != undefined) {
                var progressIndicatorInstance = $.progressIndicator({});
                var actionParams = {
                    "action":"MacrosAjax",
                    "mode":"getExcConValue",
                    "module":"Macros",
                    "record": record
                };
                AppConnector.request(actionParams).then(
                    function(data) {
                        if(data) {
                            if(data.result.execcond==7) {
                                macro_option = $('<div><label><input type="radio" value="7" name="execution_condition" class="alignTop" checked>&nbsp;&nbsp;Macro</label><br><div class="clearfix"></div></div>');
                            }
                            last_execution_condition.parent().parent().after(macro_option);
                            progressIndicatorInstance.hide();
                        }
                    },
                    function(error,err){

                    }
                );
            } else {
                last_execution_condition.parent().parent().after(macro_option);
            }
        }
        setTimeout(function () {
            jQuery('[name="execution_condition"]').click(function () {
                jQuery('[name="execution_condition"]').attr('checked',false);
                jQuery(this).attr('checked',true);
            });
        },1000);
        $( document ).ajaxComplete(function(a,b,c) {
            if(c.data != undefined) {
                var ajax_url = c.data;
            } else if(c.url != undefined) {
                var ajax_url = c.url;
            }
            if(ajax_url == undefined) {
                return;
            }

            // skip step 2
            if(ajax_url.indexOf('view=Edit')>-1 && ajax_url.indexOf('module=Workflows')>-1 && ajax_url.indexOf('mode=Step2')>-1 && ajax_url.indexOf('execution_condition=7')>-1 ) {
                function refresh() {
                    if($('#workflow_step2').length > 0 && $('#workflow_step2').find('button[type="submit"][class="btn btn-success"]:not(.cancel)').length > 0) {
                        //$('#workflow_step2').find('button[type="submit"][class="btn btn-success"]:not(.cancel)').trigger('click');
                        var SettingsWorkflowsEdit2Instance = new Settings_Workflows_Edit2_Js();
                        var opts = app.validationEngineOptions;
                        // to prevent the page reload after the validation has completed
                        opts['onValidationComplete'] = function(form,valid) {
                            //returns the valid status
                            return valid;
                        };
                        opts['promptPosition'] = "bottomRight";
                        $('#workflow_step2').validationEngine(opts);

                        var container = SettingsWorkflowsEdit2Instance.getContainer();
                        // When you come to step2 we should remove validation for condition values other than rawtwxt
                        app.changeSelectElementView(container);
                        SettingsWorkflowsEdit2Instance.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance($('.filterContainer',container));
                        SettingsWorkflowsEdit2Instance.getPopUp();
                        if($('[name="filtersavedinnew"]',container).val() == '5'){
                            SettingsWorkflowsEdit2Instance.registerEnableFilterOption();
                        }
                        var aDeferred = $.Deferred();
                        var form = SettingsWorkflowsEdit2Instance.getContainer();
                        SettingsWorkflowsEdit2Instance.calculateValues();
                        var formData = form.serializeFormData();
                        var progressIndicatorElement = $.progressIndicator({
                            'position' : 'html',
                            'blockInfo' : {
                                'enabled' : true
                            }
                        });
                        AppConnector.request(formData).then(
                            function(data) {
                                form.hide();
                                if(data.result) {
                                    //Settings_Vtiger_Index_Js.showMessage({text : app.vtranslate('JS_WORKFLOW_SAVED_SUCCESSFULLY')});
                                    var workflowRecordElement = $('[name="record"]',form);
                                    if(workflowRecordElement.val() == '') {
                                        workflowRecordElement.val(data.result.id);
                                    }
                                    var params = {
                                        module : app.getModuleName(),
                                        parent : app.getParentModuleName(),
                                        view : 'Edit',
                                        mode : 'Step3',
                                        record : data.result.id
                                    };
                                    AppConnector.request(params).then(function(data) {
                                        //aDeferred.resolve(data);
                                        //console.log(aDeferred.state());
                                        var fieldUiHolders = jQuery('.fieldUiHolder');
                                        for(var i=0; i<fieldUiHolders.length;i++){
                                            var fieldUiHolder  = fieldUiHolders[i];
                                            var fieldValueElement = jQuery('.getPopupUi',fieldUiHolder);
                                            var valueType = jQuery('[name="valuetype"]',fieldUiHolder).val();
                                            if(valueType != 'rawtext'){
                                                fieldValueElement.removeAttr('data-validation-engine');
                                                fieldValueElement.removeClass('validate[funcCall[Vtiger_Base_Validator_Js.invokeValidation]]');
                                            }
                                        }
                                        $('.workFlowContents').append(data);
                                        $('#step2').removeClass().addClass('step');
                                        $('#step3').removeClass().addClass('step last active');
                                        var SettingsWorkflowsEdit3Instance = new Settings_Workflows_Edit3_Js();
                                        SettingsWorkflowsEdit3Instance.registerEvents();
                                    });
                                }
                                progressIndicatorElement.progressIndicator({
                                    'mode' : 'hide'
                                })
                            },
                            function(error,err){

                            }
                        );
                    } else {
                        setTimeout(function(){
                            refresh();
                        }, 50);
                    }
                }
                refresh();

            }

            // replace Back button in step 3
            if(ajax_url.indexOf('view=Edit')>-1 && ajax_url.indexOf('module=Workflows')>-1 && ajax_url.indexOf('mode=Step3')>-1 && ajax_url.indexOf('record=')>-1 ) {
                function keepLoadingScript() {
                    //var html123 = '<div style="z-index: 1000000000000; border: medium none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background-color: rgb(0, 0, 0); opacity: 1; cursor: default; position: fixed;" class="blockUI blockOverlay"></div>';
                    //$('body').append(html123);
                    if($('#workflow_step3').length > 0 && $('#workflow_step3').find('button[type="button"][class="btn btn-danger backStep"]').length > 0) {
                        cancel_button = $('#workflow_step3').find('button[type="button"][class="btn btn-danger backStep"]');
                        finish_button = $('#workflow_step3').find('button[type="button"][class="btn btn-success"]');
                        var params_ = ajax_url.split("record=");
                        var pr1 = params_[1];
                        if(pr1.indexOf('&')>-1) {
                            var last = pr1.split('&');
                            var record_id = last[0];
                        } else {
                            var record_id = pr1;
                        }
                        var progressIndicatorInstance = $.progressIndicator({});
                        var actionParams = {
                            "action":"MacrosAjax",
                            "mode":"getExcConValue",
                            "module":"Macros",
                            "record": record_id
                        };
                        AppConnector.request(actionParams).then(
                            function(data) {
                                if(data) {
                                    if(data.result.execcond==7) {
                                        var moduleFocus = $('[name="module_name"]').val();
                                        var params = {
                                            'module': 'Macros',
                                            'action': 'MacrosAjax',
                                            'mode': 'addDetailViewBasic',
                                            'module_focus': moduleFocus
                                        }
                                        AppConnector.request(params).then(
                                            function(data) {
                                            if (data) {
                                                console.log('success');
                                            }
                                        });

                                        cancel_button.replaceWith('<strong ><a href="index.php?module=Workflows&parent=Settings&view=Edit&record='+record_id+'" style="color:#fff" class="btn btn-danger">Back</a></strong>');
                                        finish_button.replaceWith('<button onclick="finishmacros();" type="button" class="btn btn-success"><strong>Finish</strong></button>');
                                    }
                                    if(data.result.execcond != 7) {
                                        var moduleFocus = $('[name="module_name"]').val();
                                        var params = {
                                            'module': 'Macros',
                                            'action': 'MacrosAjax',
                                            'mode': 'removeDetailViewLink',
                                            'module_focus': moduleFocus,
                                        }
                                        AppConnector.request(params).then(
                                            function(data) {
                                                if (data) {
                                                    console.log('success');
                                                }
                                            });
                                    }
                                    progressIndicatorInstance.hide();
                                }
                            },
                            function(error,err){

                            }
                        );
                    } else {
                        setTimeout(function(){
                            keepLoadingScript();
                        }, 50);
                    }
                }
                keepLoadingScript();

            }

            if(ajax_url.indexOf('module=Workflows')>-1 && ajax_url.indexOf('parent=Settings')>-1 &&ajax_url.indexOf('view=List')>-1) {
                function changeLabel() {
                    if($('.listViewEntriesTable').length > 0) {
                        $('.listViewEntriesTable .listViewEntries').each(function(){
                            var lastchild = $(this).find('td:nth-last-child(2)');
                            if(lastchild.html()=='&nbsp;System') {
                                lastchild.html('&nbsp;Macro');
                            }

                        });
                    } else {
                        setTimeout(function(){
                            changeLabel();
                        }, 50);
                    }
                }
                changeLabel();
            }

            var module = '';
            var parent = '';
            var workflowname = '';
            var module_name = '';
            var action = '';
            var workflow_trigger = '';
            var mode = '';
            var record = '';
            var status = '';
            ajax_url = ajax_url.replace('index.php?', '');
            var sURLVariables = ajax_url.split('&');
            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == 'module') {
                    module = sParameterName[1];
                }
                else if (sParameterName[0] == 'parent') {
                    parent = sParameterName[1];
                }
                else if (sParameterName[0] == 'workflowname') {
                    workflowname = sParameterName[1];
                } else if (sParameterName[0] == 'module_name') {
                    module_name = sParameterName[1];
                } else if (sParameterName[0] == 'action') {
                    action = sParameterName[1];
                } else if (sParameterName[0] == 'workflow_trigger') {
                    workflow_trigger = sParameterName[1];
                } else if (sParameterName[0] == 'mode') {
                    mode = sParameterName[1];
                } else if (sParameterName[0] == 'record') {
                    record = sParameterName[1];
                }  if (sParameterName[0] == 'status') {
                    status = sParameterName[1];
                }
            }
            if(module == 'Workflows' && parent == 'Settings'&& action == 'DeleteAjax' && record != ''){
                var listIdMacros =jQuery('#listViewContents').data('list-macros');
                if (listIdMacros[record] != undefined) {
                    var moduleFocus = listIdMacros[record];
                    var newparams = {
                        'module' : 'Macros',
                        'action' : 'MacrosAjax',
                        'mode' : 'removeDetailViewLink',
                        'module_focus' : moduleFocus
                    }
                    AppConnector.request(newparams).then(
                        function(data) {
                            if(data) {
                            var listViewContainer = jQuery('#listViewContents');
                            listViewContainer.data('list-macros', data.result);
                        }
                    });
                }
            }



        });

        // // add Macros button
        // if(view=="Detail" && record != undefined) {
        //     var have_macros = false;
        //     var progressIndicatorInstance = $.progressIndicator({});
        //     var actionParams = {
        //         "action":"MacrosAjax",
        //         "mode":"getMacrosWorkflows",
        //         "module":"Macros",
        //         "record": record,
        //         "source_module": module
        //     };
        //     AppConnector.request(actionParams).then(
        //         function(data) {
        //             if(data) {
        //                 var size = 0;
        //                 $.each(data.result, function(id){
        //                     size++;
        //                 });
        //                 if(size>0) {
        //                     var macros_button = '<span class="btn-group"><img width="28px" class="macro_done" style="display:none" src="layouts/vlayout/modules/Macros/resources/images/success.png"></span>'+'<span class="btn-group">' +
        //                         '<button href="javascript:void(0);" data-toggle="dropdown" class="btn dropdown-toggle">' +
        //                         '<strong>Macros</strong>&nbsp;&nbsp;<i class="caret"></i>' +
        //                         '</button>' +
        //                         '<ul class="dropdown-menu pull-left">';
        //                     $.each(data.result, function(k,detail){
        //                         macros_button += '<li id="Accounts_detailView_moreAction_Delete_Organization">' +
        //                             '<a href="javascript: void(0)" onclick="runMacro('+detail.id+','+record+')">' +
        //                             detail.name +
        //                             '</a>' +
        //                             '</li>';
        //                     });
        //                     macros_button += '</ul></span>';
        //                     $(".detailViewButtoncontainer:first > .btn-toolbar > span:nth-child(1)").before(macros_button);
        //                 }
        //                 progressIndicatorInstance.hide();
        //
        //             }
        //         },
        //         function(error,err){
        //
        //         }
        //     );
        //
        //     //console.log(detailViewButtoncontainer);
        // }

        // Change Execution Condition label on Workflow List view
        if(module=="Workflows" && parent=="Settings" && view=="List") {
            function changeLabel() {
                if($('.listViewEntriesTable').length > 0) {
                    $('.listViewEntriesTable .listViewEntries').each(function(){
                        var lastchild = $(this).find('td:nth-last-child(2)');
                        if(lastchild.html()=='&nbsp;System') {
                            lastchild.html('&nbsp;Macro');
                        }

                    });
                } else {
                    setTimeout(function(){
                        changeLabel();
                    }, 50);
                }
            }
            function checkSaveMacros() {
                var actionParams = {
                    "action":"MacrosAjax",
                    "mode":"checkMacrosIsSaved",
                    "module":"Macros"
                };
                AppConnector.request(actionParams).then(
                    function(data) {
                        if(data.result[0] == 1) {
                            Settings_Vtiger_Index_Js.showMessage({text : app.vtranslate('Macro saved successfully')});
                        }
                    },
                    function(error,err){

                    }
                );
            }
            function getMacrosWorkflow() {
                var listViewContainer = jQuery('#listViewContents');
                var newparams = {
                    'module' : 'Macros',
                    'action' : 'MacrosAjax',
                    'mode' : 'getMacrosWorkflow',
                }
                AppConnector.request(newparams).then(
                    function(data) {
                        if(data) {
                            listViewContainer.data('list-macros', data.result);
                        }
                    });
            }
            getMacrosWorkflow();
            changeLabel();
            checkSaveMacros();
        }



    });

})($);

function runMacro(wid,rid) {
    var progressIndicatorInstance = $.progressIndicator({});
    var actionParams = {
        "action":"MacrosAjax",
        "mode":"runMacro",
        "module":"Macros",
        "workflow_id": wid,
        "record":rid
    };
    AppConnector.request(actionParams).then(
        function(data) {
            if(data) {
                var detail_instance = Vtiger_Detail_Js.getInstance();
                var module_name = app.getModuleName();
                var params = {};
                params['record'] = rid;
                params['view'] = 'Detail';
                params['module'] = module_name;
                params['mode'] = 'getActivities';

                AppConnector.request(params).then(
                    function(data) {
                        var activitiesWidget = jQuery('#relatedActivities');
                        activitiesWidget.html(data);
                        app.changeSelectElementView(activitiesWidget);
                        detail_instance.registerEventForActivityWidget();
                    },
                    function(error,err){
                        return false;
                    }
                );
                var params_massage = {
                    text: 'Run successfully!',
                    type: 'success'
                };
                Vtiger_Helper_Js.showMessage(params_massage)
                app.hideModalWindow();
                // var macro_done = '.macro_done' + wid;
                // jQuery(macro_done).show();
                // setTimeout(function(){jQuery(macro_done).fadeToggle('slow','linear')},8000);

                var summaryViewContainer = detail_instance.getContentHolder();
                var updatesWidget = summaryViewContainer.find("[data-name='LBL_UPDATES']");
                detail_instance.loadWidget(updatesWidget);

                jQuery('.detailViewContainer *[data-link-key="LBL_RECORD_SUMMARY"]').each(function(){
                    if(jQuery(this).attr('class')=='active')
                        jQuery(this).trigger('click');
                });
                jQuery('*[data-link-key="LBL_RECORD_DETAILS"]').each(function(){
                    if(jQuery(this).attr('class')=='active')
                        jQuery(this).trigger('click');
                });
                jQuery('.detailViewContainer *[data-label-key="Activities"]').each(function(){
                    if(jQuery(this).attr('class')=='active')
                        jQuery(this).trigger('click');
                });
                // jQuery('.imageHolder').hide();
                // jQuery('.macro_done').show();
                // setTimeout(function(){jQuery('.macro_done').fadeToggle('slow','linear')},8000);
                progressIndicatorInstance.hide();
            }
        },
        function(error,err){

        }
    );
}

function finishmacros() {
    var actionParams = {
        "action":"MacrosAjax",
        "mode":"noteMacrosIsSaved",
        "module":"Macros"
    };
    AppConnector.request(actionParams).then(
        function(data) {
            if(data)
                window.history.back();
        },
        function(error,err){

        }
    );
}