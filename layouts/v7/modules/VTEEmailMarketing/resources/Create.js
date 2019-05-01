/* ********************************************************************************
 * The content of this file is subject to the Email Marketing ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEEmailMarketing_Create_Js", {
    instance: false,
    initStep: 1,
    getInstance: function () {
        if(VTEEmailMarketing_Create_Js.instance == false) {
            var instance = new VTEEmailMarketing_Create_Js();
            VTEEmailMarketing_Create_Js.instance = instance;
            return instance;
        }
        return VTEEmailMarketing_Create_Js.instance;
    }
}, {

    init: function () {
        this.addComponents();
    },

    addComponents: function () {
        this.addModuleSpecificComponent('CustomView');
    },

    initiateStep: function (stepVal) {
        var step = 'step' + stepVal;
        this.activateHeader(step);
        this.activateContent(step);
    },

    //active li
    activateHeader: function (step) {
        var headersContainer = jQuery('.crumbs ');
        headersContainer.find('.active').removeClass('active');
        jQuery('#' + step, headersContainer).addClass('active');
    },

    //active tab
    activateContent: function (step) {
        var ContentContainer = jQuery('.email_marketing_content ');
        ContentContainer.find('.active').removeClass('active');
        jQuery('.' + step, ContentContainer).addClass('active');
    },

    //getStepActive
    getStepActive: function () {
        var headersContainer = jQuery('.crumbs ');
        var stepStr = headersContainer.find('.active').data('value');
        return parseInt(stepStr);
    },

    //click button next
    registerButtonNextStep1: function () {
        var thisInstance = this;
        var currentStep = thisInstance.getStepActive();
        var nextStep = thisInstance.getStepActive() + 1;
        var formCurrentStep = jQuery('#EmailMarketingStep' + currentStep);
        var formNextStep = jQuery('#EmailMarketingStep' + nextStep);
        if (nextStep <= 4) {
            thisInstance.initiateStep(nextStep);
            formCurrentStep.addClass('hide');
            formNextStep.removeClass('hide');
            jQuery('input[name="step"]').val("Step" + nextStep);
            thisInstance.registerShowHideFilter();
            thisInstance.registerShowPopupSelectingRelatedRecord();
            thisInstance.registerEventCreateFilter();
            thisInstance.registerSelectingRelatedRecordComplete();
            thisInstance.loadFilterRelatedRecord();
            thisInstance.registerShowPopupLoadOtherEmailMarketing();
            thisInstance.registerAutoCompleteFields();
            thisInstance.registerRemoveValueAutoCompeleteField();
        }
    },

    registerButtonNextStep2: function () {
        var thisInstance = this;
        var currentStep = thisInstance.getStepActive();
        var nextStep = thisInstance.getStepActive() + 1;
        var formCurrentStep = jQuery('#EmailMarketingStep' + currentStep);
        var formNextStep = jQuery('#EmailMarketingStep' + nextStep);
        if (nextStep <= 4) {
            thisInstance.initiateStep(nextStep);
            formCurrentStep.addClass('hide');
            formNextStep.removeClass('hide');
            jQuery('input[name="step"]').val("Step" + nextStep);


        }
    },

    registerButtonNextStep3: function () {
        var thisInstance = this;
        var currentStep = thisInstance.getStepActive();
        var nextStep = thisInstance.getStepActive() + 1;
        var formCurrentStep = jQuery('#EmailMarketingStep' + currentStep);
        var formNextStep = jQuery('#EmailMarketingStep' + nextStep);
        if (nextStep <= 4) {
            thisInstance.initiateStep(nextStep);
            formCurrentStep.addClass('hide');
            formNextStep.removeClass('hide');
            jQuery('input[name="step"]').val("Step" + nextStep);
            thisInstance.registerViewStep4();
            thisInstance.registerSendNow();
        }
    },

    //click button back
    registerButtonBack: function () {
        var thisInstance = this;
        jQuery('.btnBack').on('click', function () {
            var backStep = thisInstance.getStepActive() - 1;
            if (backStep >= 1) {
                thisInstance.initiateStep(backStep);
            }
        });
    },

    //click button Cancel
    registerButtonCancel: function () {
        jQuery('.btnCancel').on('click', function () {
            document.location.href = "index.php?module=VTEEmailMarketing&view=List&app=MARKETING";

        });
    },

    /** -- Step 1 -- **/
    registerEventNextStep1: function () {
        var thisInstance = this;
        var form = jQuery('#EmailMarketingStep1').find('#formStep1');
        form.on('click', '.btnNext', function (e) {
            var vteCampaignName = jQuery('input[name="vtecampaign_name"]').val();
            var vteFrom_Name = jQuery('input[name="vtefrom_name"]').val();
            var vteFrom_Email = jQuery('input[name="vtefrom_email"]').val();
            var assignedTo = jQuery('select[name="assigned_user_id"]').val();
            var from_serveremailid = jQuery('select[name="from_serveremailid"]').val();
            var formEdit = jQuery('#EditView');
            var idEmailMarketing = formEdit.find('input[name="idEmailMarketing"]').val();
            var params = {
                submitHandler: function () {
                    app.helper.showProgress();
                    form = jQuery(form);
                    var postParams = {
                        "module": 'VTEEmailMarketing',
                        "action": 'ActionAjax',
                        "mode": 'saveEmailMarketing',
                        "vteCampaignName": vteCampaignName,
                        "vteFrom_Name": vteFrom_Name,
                        "vteFrom_Email": vteFrom_Email,
                        "idEmailMarketing": idEmailMarketing,
                        "assignedTo": assignedTo
                    };
                    AppConnector.request(postParams).then(
                        function (data) {
                            if (data.success == true) {
                                app.helper.hideProgress();
                                var form = jQuery('#EditView');
                                form.find('input[name="idEmailMarketing"]').val(data.result);
                                form.find('input[name="campagin_name"]').val(vteCampaignName);
                                form.find('input[name="from_name"]').val(vteFrom_Name);
                                form.find('input[name="from_email"]').val(vteFrom_Email);
                                form.find('input[name="from_serveremailid"]').val(from_serveremailid);
                                thisInstance.registerButtonNextStep1();
                            }
                        }
                    );
                }
            };
            form.vtValidate(params);
        });
    },

    /** -- End Step 1 -- **/

    /** -- Step 2 -- **/
    registerShowHideFilter: function(){
        var step2 = jQuery('#EmailMarketingStep2');
        jQuery(document).on('click','.btn-on-off-filter',function () {
            var btn = jQuery(this);
            var module = btn.attr('module');
            var check = btn.attr('aria-pressed');
            var table = step2.find('#table-list-filter');
            if(check == 'true'){
                table.find('tr[module="'+module+'"]').removeClass('hide');
            }else{
                table.find('tr[module="'+module+'"]').addClass('hide');
            }
        })

    },
    getTotalRelated : function () {
        var recordId = jQuery('input[name="idEmailMarketing"]').val();
        var params = {
            'module' : app.getModuleName(),
            'action' : 'ActionAjax',
            'mode' : 'getTotalRelated',
            'recordId' : recordId
        };
        AppConnector.request(params).then(function (data) {
            if(data.success == true){
                var step2 = jQuery('#EmailMarketingStep2');
                var totalRecord = step2.find('#total-record');
                var formTotalRecord = jQuery('#EditView').find('[name="totalRecord"]');
                if(data.result != 0){
                    totalRecord.html(data.result);
                    formTotalRecord.val(data.result);
                }else{
                    totalRecord.html('0');
                }

            }
        });
    },
    registerSelectingRelatedRecordComplete : function () {
        var thisInstance = this;
        app.event.on("post.RecordList.click", function(event, data) {
            var responseData = JSON.parse(data);
            var idList = new Array();
            for (var id in responseData) {
                idList.push(id);
            }
            var action = jQuery('#EmailMarketingStep2').find('input[name="actionPopup"]').val();
            if(action == 'select') {
                var relatedController = thisInstance.getRelatedController();
                app.helper.hideModal();
                if (relatedController) {
                    relatedController.addRelations(idList).then(function () {
                        relatedController.loadRelatedList();
                    });
                }
                thisInstance.getTotalRelated();

            }else {
                thisInstance.LoadOtherEmailMarketing(idList);
            }
        });
    },
    // --- Filter
    registerEventCreateFilter : function () {
        var step2 = jQuery('#EmailMarketingStep2');
        step2.on('click','.createFilter',function(e){
            var element = jQuery(e.currentTarget);
            element.trigger('post.CreateFilter.click',{'url':element.data('url')});
        });
    },
    loadFilterRelatedRecord : function () {
        var thisInstance = this;
        var step2 = jQuery('#EmailMarketingStep2');
        step2.on('click','.btn-load-filter',function (e) {
            var thisBtn = jQuery(this);
            var action = thisBtn.attr('action');
            var relModule = thisBtn.closest('tr').attr('module');
            var recordId =  jQuery('input[name="idEmailMarketing"]').val();
            var cvid =  thisBtn.val();
            if(action == 1){
                e.preventDefault();
                return false;
            }
            app.helper.showProgress();
            var params = {
                'module' : 'VTEEmailMarketing',
                'action' : 'ActionAjax',
                'mode' : 'saveRelatedRecord',
                'recordId' : recordId,
                'relModule' : relModule,
                'cvid' : cvid
            };
            AppConnector.request(params).then(function (data) {
                if(data.success == true){
                    app.helper.hideProgress();
                    thisBtn.attr('action',1);
                    thisBtn.removeClass('btn-success');
                    thisBtn.addClass('btn-primary');
                    thisBtn.html('Loaded');
                    thisInstance.getTotalRelated();
                    thisBtn.closest('tr').css("background", "#e8f2db");
                    var result = data.result;
                    thisBtn.closest('tr').find('td .filter-loaded-record').html(result);
                    if(result == 0){
                        var messageToolTip = "It's showing 0 records because those records have already been loaded from different list";
                        var questionLoadedFilter = '<span class="glyphicon glyphicon-info-sign pull-right" style="cursor: pointer; color: black;margin-top:12px" data-toggle="tooltip" data-placement="top" title="'+messageToolTip+'"></span>';
                        thisBtn.closest('tr').find('td .show-info-load-0-record').append(questionLoadedFilter);
                        thisBtn.closest('tr').find('td .show-info-load-0-record [data-toggle="tooltip"]').tooltip();
                    }
                }
            });
        })
    },
    // --- End Filter

    // --- Select Record
    getRelatedController : function(relatedModuleName) {
        var thisInstance = this;
        var recordId = jQuery('input[name="idEmailMarketing"]').val();
        var moduleName = 'VTEEmailMarketing';
        var selectedTabElement = thisInstance.getSelectedTab();

        if (typeof relatedModuleName == 'undefined') {
            var relatedModuleName = jQuery('#EmailMarketingStep2').find('input[name="module_related"]').val();
        }
        var relatedListClass = 'Vtiger_RelatedList_Js';
        if(typeof window[relatedListClass] != 'undefined'){

            return Vtiger_RelatedList_Js.getInstance(recordId, moduleName, selectedTabElement, relatedModuleName);
        }
        return null;
    },
    getSelectedTab : function() {
        var tabContainer = jQuery('div.related-tabs');
        return tabContainer.find('li.active');
    },
    registerShowPopupSelectingRelatedRecord : function() {
        var step2 = jQuery('#EmailMarketingStep2');
        step2.on('click','.selectRelation',function (e) {
            var module = jQuery(this).attr('module');
            var moduleRel = jQuery(this).attr('moduleRel');
            var recordId = jQuery('input[name="idEmailMarketing"]').val();
            step2.find('input[name="actionPopup"]').val('select');
            if(moduleRel == 'Leads'){
                var relationId = step2.find('input[name="relationIdLeads"]').val();
                step2.find('input[name="module_related"]').val('Leads');
            }
            else if(moduleRel == 'Contacts'){
                var relationId = step2.find('input[name="relationIdContacts"]').val();
                step2.find('input[name="module_related"]').val('Contacts');

            }
            else{
                var relationId = step2.find('input[name="relationIdAccounts"]').val();
                step2.find('input[name="module_related"]').val('Accounts');

            }
            var popupParams = {
                'module' : moduleRel,
                'src_module' : module,
                'src_record' : recordId,
                'multi_select' : true,
                'view' : 'Popup',
                'relationId' : relationId
            };
            var popupjs = new Vtiger_Popup_Js();
            popupjs.showPopup(popupParams,"post.RecordList.click");

        });
    },
    // --- End Select Record

    // -- Load Record From Other VTE Email Marketing
    registerShowPopupLoadOtherEmailMarketing : function () {
        var step2 = jQuery('#EmailMarketingStep2');
        step2.on('click','.loadOtherEmailMarketing',function (e) {
            var module = "VTEEmailMarketing";
            var idEmailMarketing = jQuery('#EditView').find('[name="idEmailMarketing"]').val();
            var relmodule = "VTEEmailMarketing";
            step2.find('input[name="actionPopup"]').val('loadOtherEmailMarketing');

            var popupParams = {
                'module' : module,
                'view' : 'Popup',
                'relmodule' : relmodule,
                'recordId' : idEmailMarketing,
            };
            var popupjs = new Vtiger_Popup_Js();
            popupjs.showPopup(popupParams,"post.RecordList.click");

        });
    },
    LoadOtherEmailMarketing : function (idEmailMarketing) {
        var thisInstance = this;
        app.helper.showProgress();
        var recordId = jQuery('input[name="idEmailMarketing"]').val();
        var params = {};
        params['module'] = 'VTEEmailMarketing';
        params['action'] = 'RelationAjax';
        params['mode'] = 'loadOtherEmailMarketing';
        params['recordId'] = recordId;
        params['idEmailMarketing'] = idEmailMarketing;
        AppConnector.request(params).then(function (data) {
            if(data.success = true){
                var input = jQuery('input#load_other_email_marketing');
                input.val(data.result);
                input.attr('disabled','disabled').addClass('chzn-disabled');
                jQuery('#deleteValueEmailMarketing').removeClass('hide');
                app.helper.hideProgress();
                thisInstance.getTotalRelated();
            }
        });
    },
    // Auto Complete
    getReferenceSearchParams : function(){
        var params = {};
        var searchModule =  jQuery('#EmailMarketingStep2').find('[name="popupReferenceModule"]').val();
        params.search_module = searchModule;
        return params;
    },
    searchModuleNames : function(params) {
        var aDeferred = jQuery.Deferred();

        if(typeof params.module == 'undefined') {
            params.module = app.getModuleName();
        }

        if(typeof params.action == 'undefined') {
            params.action = 'BasicAjax';
        }

        if(typeof params.base_record == 'undefined') {
            var record = jQuery('[name="idEmailMarketing"]');
            var recordId = app.getRecordId();
            if(record.length) {
                params.base_record = record.val();
            } else if(recordId) {
                params.base_record = recordId;
            } else if(app.view() == 'List') {
                var editRecordId = jQuery('#listview-table').find('tr.listViewEntries.edited').data('id');
                if(editRecordId) {
                    params.base_record = editRecordId;
                }
            }
        }
        app.request.get({data:params}).then(
            function(err, res){
                aDeferred.resolve(res);
            },
            function(error){
                //TODO : Handle error
                aDeferred.reject();
            }
        );
        return aDeferred.promise();
    },
    registerAutoCompleteFields : function(container) {
        if(typeof container == 'undefined'){
            container = jQuery('#EmailMarketingStep2');
        }
        var thisInstance = this;
        container.find('input.autoComplete').autocomplete({
            'minLength' : '3',
            'source' : function(request, response){
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var searchValue = request.term;
                var params = thisInstance.getReferenceSearchParams();
                params.module = app.getModuleName();
                if (jQuery('#QuickCreate').length > 0) {
                    params.module = container.find('[name="module"]').val();
                }
                params.search_value = searchValue;
                //Search default module VTEEmailMarketing
                params.search_module = "VTEEmailMarketing";
                if(params.search_module && params.search_module!= 'undefined') {
                    thisInstance.searchModuleNames(params).then(function(data){
                        var reponseDataList = new Array();
                        var serverDataFormat = data;
                        if(serverDataFormat.length <= 0) {
                            jQuery(inputElement).val('');
                            serverDataFormat = new Array({
                                'label' : 'No Results Found',
                                'type'	: 'no results'
                            });
                        }
                        for(var id in serverDataFormat){
                            var responseData = serverDataFormat[id];
                            reponseDataList.push(responseData);
                        }
                        response(reponseDataList);
                    });
                } else {
                    jQuery(inputElement).val('');
                    serverDataFormat = new Array({
                        'label' : 'No Results Found',
                        'type'	: 'no results'
                    });
                    response(serverDataFormat);
                }
            },
            'select' : function(event, ui ){
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if(typeof selectedItemData.type != 'undefined' && selectedItemData.type=="no results"){
                    return false;
                }
                var element = jQuery(this);
                element.attr('disabled','disabled').addClass('chzn-disabled');;
                var idVTEEmailMarketing = selectedItemData.id;
                thisInstance.LoadOtherEmailMarketing(idVTEEmailMarketing);
                container.find('#deleteValueEmailMarketing').removeClass('hide');

            }
        });
    },
    registerRemoveValueAutoCompeleteField : function () {
        var step2 = jQuery('#EmailMarketingStep2');
        var btn = step2.find('#deleteValueEmailMarketing');
        btn.on('click','.fa-close',function () {
            step2.find('.autoComplete').val('');
            step2.find('.autoComplete').removeAttr('disabled').removeClass('chzn-disabled');
            btn.addClass('hide');
        });
    },
    // -- End Load Record From Email Marketing
    registerEventNextStep2: function () {
        var thisInstance = this;
        jQuery('#EmailMarketingStep2').on('click', '.btnNext', function (e) {
            app.helper.showProgress();
            var container = thisInstance.getListViewContainer();
            var appName = container.find('#appName').val();
            var urlParams = {
                "module": "VTEEmailMarketing",
                "view": "TemplatesListAjax",
                "viewType": "grid",
                "app": appName
            };

            AppConnector.request(urlParams).then(function(data){
                if(data.success == true){
                    jQuery('#EmailMarketingStep3 #listViewContent').html(data.result);
                    thisInstance.registerButtonNextStep2();
                    var templateEmail = jQuery('[name="templateEmail"]').val();
                    // check if, templateemail selected auto select template
                    if(templateEmail != ""){
                        jQuery('.selectTemplate[data-value="'+templateEmail+'"]').trigger('click');
                    }
                    app.helper.hideProgress();
                }
            });


        });
    },
    /** -- End Step 2 -- **/


    /** -- Step 3 -- **/
    ContentEmailMarketingStep3:function(){
        return jQuery('#EmailMarketingStep3');
    },

    registerEventNextStep3: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '.btnNext', function (e) {
            thisInstance.registerButtonNextStep3();
        });
    },

    getDefaultParams: function () {
        var container = this.getListViewContainer();
        var pageNumber = container.find('#pageNumber').val();
        var module = "VTEEmailMarketing";

        var parent = app.getParentModuleName();
        var cvId = this.getCurrentCvId();
        var orderBy = container.find('[name="orderBy"]').val();
        var sortOrder = container.find('[name="sortOrder"]').val();
        var appName = container.find('#appName').val();
        var params = {
            'module': module,
            'parent': parent,
            'page': pageNumber,
            'view': "TemplatesListAjax",
            'viewname': cvId,
            'orderby': orderBy,
            'sortorder': sortOrder,
            'app': appName
        }
        params.viewname = 1;
        params.search_params = JSON.stringify(this.getListSearchParams());
        params.tag_params = JSON.stringify(this.getListTagParams());
        params.nolistcache = (container.find('#noFilterCache').val() == 1) ? 1 : 0;
        params.starFilterMode = container.find('.starFilter li.active a').data('type');
        params.list_headers = container.find('[name="list_headers"]').val();
        params.tag = container.find('[name="tag"]').val();
        params.viewType = container.find('[name="viewType"]').val();
        params._pjax = '#pjaxContainer';
        return params;
    },


    registerViewType: function () {

        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '.viewType', function (e) {
            var mode = jQuery(e.currentTarget).data('mode');
            //If template view is in thumbnail mode, delete icon should be hided
            if(mode == 'grid'){
                jQuery('.fa-trash').parents('div.btn-group').addClass('hide');
                jQuery('button[data-mode="grid"]').prop('disabled', true);
                jQuery('button[data-mode="list"]').prop('disabled', false);
            } else {
                jQuery('.fa-trash').parents('div.btn-group').removeClass('hide');
                jQuery('button[data-mode="grid"]').prop('disabled', false);
                jQuery('button[data-mode="list"]').prop('disabled', true);

            }

            EmailMarketingStep3.find('input[name="viewType"]').val(mode);
            var listViewInstance = Vtiger_List_Js.getInstance();
            var urlParams = thisInstance.getDefaultParams();
            thisInstance.loadListViewRecords(urlParams).then(function () {
                listViewInstance.updatePagination();
            });
        });
    },

    registerViewRefresh: function () {

        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '#EmailTemplates_listView_basicAction_refresh', function (e) {
            var mode = EmailMarketingStep3.find('input[name="viewType"]').val();

            EmailMarketingStep3.find('input[name="viewType"]').val(mode);
            var listViewInstance = Vtiger_List_Js.getInstance();
            var urlParams = thisInstance.getDefaultParams();
            thisInstance.loadListViewRecords(urlParams).then(function () {
                listViewInstance.updatePagination();
            });
        });
    },

    registerNextPage: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '#NextPageButton', function (e) {

            var mode = EmailMarketingStep3.find('input[name="viewType"]').val();
            var currentPage = EmailMarketingStep3.find('#pageNumber').val();
            currentPage = parseInt(currentPage) + 1;
            EmailMarketingStep3.find('#pageNumber').val(currentPage)
            EmailMarketingStep3.find('input[name="viewType"]').val(mode);
            var urlParams = thisInstance.getDefaultParams();
            thisInstance.loadListViewRecords(urlParams).then(function () {
                thisInstance.updatePagination();
            });
        });
    },

    registerPrePage: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '#PreviousPageButton', function (e) {
            var listViewContainer = jQuery('#page');
            var pageNumber = listViewContainer.find('#pageNumber').val();
            var previousPageNumber = parseInt(parseFloat(pageNumber)) - 1;

            if (pageNumber > 1) {
                var urlParams = {};
                listViewContainer.find('#pageNumber').val(previousPageNumber);
                thisInstance.loadListViewRecords(urlParams).then(function () {
                    thisInstance.updatePagination();
                });

            }
        });
    },

    registerPageJump: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on("click",'#PageJump',function () {
            EmailMarketingStep3.find('#PageJumpDropDown').show();
            thisInstance.pageJump();
        });
    },

    /**
     * Function to get Page Jump Params
     */
    getPageJumpParams: function () {
        var params = this.getDefaultParams();
        params['view'] = "TemplatesListAjax";
        params['mode'] = "getPageCount";

        return params;
    },

    getPageCount: function () {
        var aDeferred = jQuery.Deferred();
        var pageCountParams = this.getPageJumpParams();
        var params = {
            "type": "GET",
            "data": pageCountParams
        }

        app.request.get(params).then(
            function (err, data) {
                var response;
                // if (typeof data !== "object") {
                //     response = JSON.parse(data);
                // } else {
                response = data;
                // }
                aDeferred.resolve(response);
            }
        );
        return aDeferred.promise();
    },

    registerTotalNumOfRecords:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on("click",'.totalNumberOfRecords',function () {
            thisInstance.totalNumOfRecords();
        });
    },
    totalNumOfRecords_performingAsyncAction: false,
    totalNumOfRecords: function (currentEle) {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        var totalRecordsElement = EmailMarketingStep3.find('#totalCount');
        var totalNumberOfRecords = totalRecordsElement.val();
        if (thisInstance.totalNumOfRecords_performingAsyncAction) {
            return;
        }
        currentEle.find('.showTotalCountIcon').addClass('hide');
        if (totalNumberOfRecords === '') {
            thisInstance.totalNumOfRecords_performingAsyncAction = true;
            thisInstance.getPageCount().then(function (data) {
                currentEle.addClass('hide');
                totalNumberOfRecords = data.numberOfRecords;
                totalRecordsElement.val(totalNumberOfRecords);
                EmailMarketingStep3.find('ul#listViewPageJumpDropDown #totalPageCount').text(data.page);
                thisInstance.showPagingInfo();
                thisInstance.totalNumOfRecords_performingAsyncAction = false;
            });
        } else {
            currentEle.addClass('hide');
            thisInstance.showPagingInfo();
        }
    },

    pageJump: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        var element = EmailMarketingStep3.find('#totalPageCount');
        var totalPageNumber = element.text();
        var pageCount;

        if (totalPageNumber === "") {
            var totalCountElem = EmailMarketingStep3.find('#totalCount');
            var totalRecordCount = totalCountElem.val();
            if (totalRecordCount !== '') {
                var recordPerPage = listViewContainer.find('#pageLimit').val();
                if (recordPerPage === '0')
                    recordPerPage = 1;
                pageCount = Math.ceil(totalRecordCount / recordPerPage);
                if (pageCount === 0) {
                    pageCount = 1;
                }
                element.text(pageCount);
                return;
            }

            thisInstance.getPageCount().then(function (data) {
                var pageCount = data.page;
                totalCountElem.val(data.numberOfRecords);
                if (pageCount === 0) {
                    pageCount = 1;
                }
                element.text(pageCount);
            });
        }
    },

    registerpageJumpOnSubmit:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on("click",'#pageToJumpSubmit',function () {
            thisInstance.pageJumpOnSubmit();
        });
    },

    pageJumpOnSubmit: function (element) {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        var currentPageElement = EmailMarketingStep3.find('#pageNumber');
        var currentPageNumber = parseInt(currentPageElement.val());
        var newPageNumber = parseInt(EmailMarketingStep3.find('#pageToJump').val());
        var totalPages = parseInt(EmailMarketingStep3.find('#totalPageCount').text());
        if (newPageNumber > totalPages) {
            var message = app.vtranslate('JS_PAGE_NOT_EXIST');
            app.helper.showErrorNotification({'message': message})
            return;
        }

        if (newPageNumber === currentPageNumber) {
            var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER') + " " + newPageNumber;
            app.helper.showAlertNotification({'message': message});
            return;
        }

        var urlParams = thisInstance.getPagingParams();
        urlParams['page'] = newPageNumber;
        thisInstance.loadListViewRecords(urlParams).then(function (data) {
            EmailMarketingStep3.find('#PageJumpDropDown').hide();
            thisInstance.updatePagination();

        });
    },

    getPagingParams: function () {
        var thisInstance = this;
        var params = {
            "orderby": jQuery('#orderBy').val(),
            "sortorder": jQuery("#sortOrder").val(),
            "viewname": thisInstance.getCurrentCvId()
        };
        return params;
    },

    getCurrentCvId: function () {
        var listViewContainer = this.getListViewContainer();
        return listViewContainer.find('[name="cvid"]').val();
    },

    updatePagination : function(){
        var thisInstance = this;
        setTimeout(function () {
            var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
            var previousPageExist = EmailMarketingStep3.find('#previousPageExist').val();
            var nextPageExist = EmailMarketingStep3.find('#nextPageExist').val();
            var previousPageButton = EmailMarketingStep3.find('#PreviousPageButton');
            var nextPageButton = EmailMarketingStep3.find('#NextPageButton');
            var pageJumpButton = EmailMarketingStep3.find('#PageJump');
            var listViewEntriesCount = parseInt(EmailMarketingStep3.find('#noOfEntries').val());
            var pageStartRange = parseInt(EmailMarketingStep3.find('#pageStartRange').val());
            var pageEndRange = parseInt(EmailMarketingStep3.find('#pageEndRange').val());
            var pages = EmailMarketingStep3.find('#totalPageCount').text();
            var totalNumberOfRecords = EmailMarketingStep3.find('.totalNumberOfRecords');
            var pageNumbersTextElem = EmailMarketingStep3.find('.pageNumbersText');

            if(pages > 1){
                pageJumpButton.removeAttr('disabled');
            }

            if(previousPageExist != ""){
                previousPageButton.removeAttr('disabled');
            } else if(previousPageExist == "") {
                previousPageButton.attr("disabled","disabled");
            }

            if((nextPageExist != "")){
                nextPageButton.removeAttr('disabled');
            } else if((nextPageExist == "")) {
                nextPageButton.attr("disabled","disabled");
            }
            if(listViewEntriesCount != 0){
                var pageNumberText = pageStartRange+" "+app.vtranslate('to')+" "+pageEndRange;
                pageNumbersTextElem.html(pageNumberText);
                totalNumberOfRecords.removeClass('hide');
            } else {
                pageNumbersTextElem.html("<span>&nbsp;</span>");
                if(!totalNumberOfRecords.hasClass('hide')){
                    totalNumberOfRecords.addClass('hide');
                }
            }
        }, 400);
    },

    registerSearchTemplate: function () {

        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '.search-template', function (e) {

            var searchValue = EmailMarketingStep3.find('.search_value').val();
            var arrParam = [[["templateName","c",searchValue]]];
            arrParam = JSON.stringify(arrParam);
            EmailMarketingStep3.find('#currentSearchParams').val(arrParam);
            EmailMarketingStep3.find('[name="list_headers"]').val('["templateName"]');
            EmailMarketingStep3.find('[name="search_value"]').val(searchValue);

            var listViewInstance = Vtiger_List_Js.getInstance();
            var urlParams = thisInstance.getDefaultParams();
            thisInstance.loadListViewRecords(urlParams).then(function () {
                listViewInstance.updatePagination();
            });

        });
    },

    /**
     * Function to show on mouseover and to hide on mouseleave
     */
    registerThumbnailHoverActionEvent: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('mouseover', '.thumbnail, .templateActions', function (e) {
            jQuery(e.currentTarget).find('div').eq(1).removeClass('hide').addClass('templateActions');
        });
        EmailMarketingStep3.on('mouseleave', '.thumbnail, .templateActions', function (e) {
            jQuery(e.currentTarget).find('div').eq(1).removeClass('templateActions').addClass('hide');
        });
    },

    registerTemplateSelectFromListView:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        //listViewEntriesCheckBox = checkbox template in listview
        EmailMarketingStep3.on('click', '.listViewEntriesCheckBox', function () {
            //check checkbox= true => run
            if(jQuery(this).prop('checked')){
                var idTemplate = this.value;
                if(idTemplate != undefined){
                    Vtiger_Helper_Js.showPnotify({title: 'Template selected'});
                    jQuery('input[name="templateEmail"]').val(idTemplate);
                    jQuery('input[name="step"]').val("step4");
                    //remove Distabled
                    EmailMarketingStep3.find('.btnNext').prop("disabled", false);
                }
            }else{
                EmailMarketingStep3.find('.btnNext').prop("disabled", true);
            }
        });
    },

    /**
     * Function to create the template or edit the existing template
     */
    registerTemplateSelectEvent: function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        //editTemplate = class Of button Select
        EmailMarketingStep3.on('click', '.selectTemplate', function (e) {
            var focus = jQuery(this);
            //Change backSelected
            EmailMarketingStep3.find('.thumbnail').css('border','1px solid #ddd');
            focus.closest('.thumbnail').css('border','4px solid green');
            Vtiger_Helper_Js.showPnotify({title: 'Template selected'});
            var idTemplate = focus.data('value');
            if(idTemplate != undefined){
                jQuery('input[name="templateEmail"]').val(idTemplate);
                jQuery('input[name="step"]').val("step4");
                //remove Distabled
                EmailMarketingStep3.find('.btnNext').prop("disabled", false);
            }else{
                EmailMarketingStep3.find('.btnNext').prop("disabled", true);
            }
        });
    },

    registerClickThumbnail:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click', '.thumbnail', function (e) {
            var focus = jQuery(this);
            //Change backSelected
            EmailMarketingStep3.find('.thumbnail').css('border','1px solid #ddd');
            focus.css('border','4px solid green');
            Vtiger_Helper_Js.showPnotify({title: 'Template selected'});
            var idTemplate = focus.data('value');
            if(idTemplate != undefined){
                jQuery('input[name="templateEmail"]').val(idTemplate);
                jQuery('input[name="step"]').val("step4");
                //remove Distabled
                EmailMarketingStep3.find('.btnNext').prop("disabled", false);
            }
        });
    },

    registerDuplicateTemplate:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click','.duplicatorTemplate',function () {
            var focus = jQuery(this);
            var idTemplate = focus.data('value');
            var params = {
                'module':app.getModuleName(),
                'action':'ActionAjax',
                'mode':'duplicateTemplate',
                'idtemlate':idTemplate
            };
            //todo
            app.helper.showProgress();
            AppConnector.request(params).then(
                function (data) {
                    if (data.success == true) {
                        app.helper.hideProgress();
                        EmailMarketingStep3.find('#EmailTemplates_listView_basicAction_refresh').trigger('click');
                    }
                }
            );
        });
    },

    loadListViewRecords : function(urlParams) {
        var self = this;
        var aDeferred = jQuery.Deferred();
        var defParams = this.getDefaultParams();
        if(typeof urlParams == "undefined") {
            urlParams = {};
        }
        if(typeof urlParams.search_params == "undefined") {
            urlParams.search_params = JSON.stringify(this.getListSearchParams(false));
        }
        urlParams = jQuery.extend(defParams, urlParams);
        app.helper.showProgress();

        app.request.post({data:urlParams}).then(function(err, res){
            aDeferred.resolve(res);
            self.placeListContents(res);
            app.event.trigger('post.listViewFilter.click', jQuery('.searchRow'));
            app.helper.hideProgress();
            self.markSelectedIdsCheckboxes();
            self.registerDynamicListHeaders();
        });
        return aDeferred.promise();
    },

    /**
     * Function to preview existing email template
     * @returns {undefined}
     */
    registerPreviewTemplateEvent: function(){
        var thisInstance = this;
        jQuery('#listViewContent').on('click','.previewTemplate',function(e){
            var record = jQuery(e.currentTarget).data('value');
            var params = {
                'module': 'EmailTemplates',
                'view'  : "ListAjax",
                "mode"  : "previewTemplate",
                "record": record
            };
            app.helper.showProgress();
            app.request.post({data: params}).then(function (error, data) {
                app.helper.loadPageContentOverlay(data).then(function(){
                    thisInstance.showTemplateContent(record);
                });
            });
        });
    },

    /**
     * Function to show template content
     * @param {type} record
     * @returns {undefined}
     */

    //Custom
    getListViewContainer : function() {
        this.listViewContainer = jQuery('div#page');
        return this.listViewContainer;
    },

    getListSearchParams : function(includeStarFilters) {
        var searchParams = JSON.parse(jQuery('#listViewContent').find('[name="currentSearchParams"]').val());

        for(var index in searchParams) {
            if(isNaN(index)) {
                delete searchParams[index];
            }
        }
        if(includeStarFilters) {
            searchParams = this.addStarSearchParams(searchParams);
        }
        return searchParams;
    },

    getListTagParams: function () {
        var listViewPageDiv = this.getListViewContainer();
        var currentTagParams = new Array();
        var tagParams = new Array();

        if (listViewPageDiv.find('#currentTagParams').val()) {
            currentTagParams = JSON.parse(listViewPageDiv.find('#currentTagParams').val());
        }

        for (var i in currentTagParams) {
            var fieldName = currentTagParams[i]['fieldName'];
            var searchValue = currentTagParams[i]['searchValue'];
            var searchOperator = currentTagParams[i]['comparator'];
            if (fieldName == null || fieldName.length <= 0) {
                continue;
            }
            var tagInfo = new Array();
            tagInfo.push(fieldName);
            tagInfo.push(searchOperator);
            tagInfo.push(searchValue);
            tagParams.push(tagInfo);
        }
        if (tagParams.length > 0) {
            var listTagParams = new Array(tagParams);
        } else {
            var listTagParams = new Array();
        }
        return listTagParams;
    },

    placeListContents: function (contents) {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        var container = EmailMarketingStep3.find("#listViewContent");
        container.html(contents);
    },

    markSelectedIdsCheckboxes: function () {
        var self = this;

        var recordSelectTrackerObj = self.getRecordSelectTrackerInstance();
        var selectAllMode = recordSelectTrackerObj.getSelectAllMode();

        var excludedIds = recordSelectTrackerObj.getExcludedIds();
        var excludedIdsAreEmpty = self.checkIdsAreEmpty(excludedIds);
        var selectedIds = recordSelectTrackerObj.getSelectedIds();

        var currentViewId = self.getCurrentCvId();
        var recordTackerCvId = recordSelectTrackerObj.getCvid();
        var rows = jQuery('tr.listViewEntries');

        if (selectAllMode == true) {
            jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').addClass('show');
            //jQuery(".listViewEntriesMainCheckBox").prop('checked', true);
            if (this.isStarFilterMode()) {
                rows = this.getStarRecordRows();
            } else if (this.isUnStarFilterMode()) {
                rows = this.getUnStarRecordRows();
            }
            if (excludedIdsAreEmpty) {
                rows.each(function (i, elem) {
                    jQuery(elem).find(".listViewEntriesCheckBox").prop('checked', true);
                });
            }
            else {
                rows.each(function (i, elem) {
                    var rowId = $(elem).data('id');
                    jQuery(elem).find('.listViewEntriesCheckBox').prop('checked', true);

                    for (var j = 0; j < excludedIds.length; j++) {
                        var excludedRecordIdValue = excludedIds[j];
                        if (excludedRecordIdValue == rowId) {
                            jQuery('.listViewEntriesCheckBox[value="' + excludedRecordIdValue + '"]').prop('checked', false);
                            jQuery(".listViewEntriesMainCheckBox").prop('checked', false);
                        }
                    }
                });
            }
        } else {
            var isEmpty = self.checkIdsAreEmpty(selectedIds);
            if (!isEmpty) {
                rows.each(function (i, elem) {
                    var rowId = $(elem).data('id');
                    for (var j = 0; j < selectedIds.length; j++) {
                        var selectedRecordIdValue = selectedIds[j];
                        if (selectedRecordIdValue == rowId) {
                            jQuery('.listViewEntriesCheckBox[value="' + selectedRecordIdValue + '"]').prop('checked', true);
                        }
                    }
                });
                var listViewPageDiv = self.getListViewContainer();
                if (listViewPageDiv.find('.listViewEntriesCheckBox').not(":checked").length == 0) {
                    listViewPageDiv.find('.listViewEntriesMainCheckBox').prop("checked", true)
                } else {
                    listViewPageDiv.find('.listViewEntriesMainCheckBox').prop("checked", false)
                }
            }
        }
    },
    recordSelectTrackerInstance: false,
    getRecordSelectTrackerInstance: function () {
        if (this.recordSelectTrackerInstance === false) {
            this.recordSelectTrackerInstance = Vtiger_RecordSelectTracker_Js.getInstance();
            this.recordSelectTrackerInstance.setCvId(this.getCurrentCvId());
        } else {
            this.recordSelectTrackerInstance.setCvId(this.getCurrentCvId());
        }
        return this.recordSelectTrackerInstance;
    },

    checkIdsAreEmpty: function (val) {
        return (val == undefined || val == null || val.length <= 0) ? true : false;
    },

    registerDynamicListHeaders: function () {
        var self = this;
        var listViewContainer = this.getListViewContainer();
        if (jQuery('#filterListColumns').length > 0) {
            jQuery('#filterListColumns').instaFilta({
                targets: '.listColumnFilter .list-group-item',
                sections: '.listColumnFilter .block-item',
                hideEmptySections: true,
                beginsWith: false,
                caseSensitive: false,
                typeDelay: 0
            });
        }
        jQuery('.listColumnFilter .dropdown-menu').on('click', function (e) {
            e.stopPropagation();
        });
        var params = {
            setHeight: ''
        };
        app.helper.showVerticalScroll(jQuery('.viewColumnsList'), params);

        jQuery('.viewColumnsList').on('click', 'input[type="checkbox"]', function (e, params) {
            var listHeaderFieldEle = $('input[name="list_headers"]');
            var listHeaderFields = JSON.parse(listHeaderFieldEle.val());
            if ($(this).is(':checked')) {
                listHeaderFields = app.helper.array_merge(listHeaderFields, [$(this).val()]);
            } else {
                var index = app.helper.array_search($(this).val(), listHeaderFields);
                delete listHeaderFields[index];
            }
            listHeaderFieldEle.val(JSON.stringify(listHeaderFields));
        });

        jQuery('#updateListing').on('click', function (e, params) {
            if (typeof params === "undefined" || !params.noFilterLoad) {
                self.loadListViewRecords();
            }
        });
    },

    registerDropdownPosition: function () {
        var container = this.getListViewContainer();
        jQuery('.table-actions').on('click', '.dropdown', function (e) {
            var containerTarget = jQuery(this).closest(container);
            var dropdown = jQuery(e.currentTarget);
            if (dropdown.find('[data-toggle]').length <= 0) {
                return;
            }
            var dropdown_menu = dropdown.find('.dropdown-menu');

            var dropdownStyle = dropdown_menu.find('li a');
            dropdownStyle.css('padding', "0 6px", 'important');

            var fixed_dropdown_menu = dropdown_menu.clone(true);
            fixed_dropdown_menu.data('original-menu', dropdown_menu);
            dropdown_menu.css('position', 'relative');
            dropdown_menu.css('display', 'none');
            var currtargetTop;
            var currtargetLeft;
            var ftop = 'auto';
            var fbottom = 'auto';

            var ctop = container.offset().top;
            currtargetTop = dropdown.offset().top-ctop+dropdown.height()+100;
            currtargetLeft = dropdown.offset().left-15;
            var dropdownftop = dropdown.position().top-dropdown_menu.height()+dropdown.height()+100;
            var windowBottom = jQuery(window).height()-dropdown.offset().top;
            if (windowBottom < 250) {
                ftop = dropdownftop+'px';
                fbottom = 'auto';
            } else {
                ftop = currtargetTop+'px';
                fbottom = "auto";
            }
            fixed_dropdown_menu.css({
                'display': 'block',
                'position': 'absolute',
                'top': ftop,
                'left': currtargetLeft+'px',
                'bottom': fbottom
            }).appendTo(containerTarget);

            dropdown.on('hidden.bs.getListViewContainerdropdown', function () {
                dropdown_menu.removeClass('invisible');
                fixed_dropdown_menu.remove();
            });
        });
    },

    ButtonBackStep3: function () {
        var thisInstance = this;
        var currentStep = thisInstance.getStepActive() + 1;
        var nextStep = thisInstance.getStepActive();
        var formCurrentStep = jQuery('#EmailMarketingStep' + currentStep);
        var formNextStep = jQuery('#EmailMarketingStep' + nextStep);
        if (nextStep == 2) {
            var EmailMarketingStep3 = thisInstance.ContentEmailMarketingStep3();
            EmailMarketingStep3.find('#listViewContent').html('<span></span>');
            thisInstance.initiateStep(nextStep);
            formCurrentStep.addClass('hide');
            formNextStep.removeClass('hide');
            jQuery('input[name="step"]').val("Step" + nextStep);
            thisInstance.registerShowHideFilter();
            thisInstance.registerShowPopupSelectingRelatedRecord();
            thisInstance.registerEventCreateFilter();
            thisInstance.registerSelectingRelatedRecordComplete();
            thisInstance.loadFilterRelatedRecord();
            thisInstance.registerShowPopupLoadOtherEmailMarketing();
            thisInstance.registerAutoCompleteFields();
            thisInstance.registerRemoveValueAutoCompeleteField();

        }
    },

    registerButtonBackStep3:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on("click",'.btnBack',function () {
            jQuery('#EmailMarketingStep2').removeClass('hide');
            EmailMarketingStep3.addClass('hide');
            thisInstance.ButtonBackStep3();
        });
    },

    registerEditTemplate:function () {
        var thisInstance = this;
        var EmailMarketingStep3 =  thisInstance.ContentEmailMarketingStep3();
        EmailMarketingStep3.on('click','.editVTEEmailTemplate',function () {
            var checkboxSelected = jQuery(document).find('.listViewEntriesCheckBox:checked');
            if(checkboxSelected.length == 1){
                var recordId = checkboxSelected.val();
                window.open("index.php?module=VTEEmailMarketing&view=EditEmailTemplates&record="+recordId,'_blank')

            }else{
                alert('Select one record to Edit');
            }
        });
    },

    // -- End Step 3 --

    /** -- Step 4 -- **/

    registerViewStep4: function () {
        var idTemplateEmail = jQuery('input[name="templateEmail"]').val();
        var idEmailMarketing = jQuery('input[name="idEmailMarketing"]').val();
        var thisInstance = this;
        app.helper.showProgress();
        var params = {
            'module': app.getModuleName(),
            'action': "ActionAjax",
            "mode": "viewStep4",
            "idTemplateEmail": idTemplateEmail,
            "idEmailMarketing": idEmailMarketing
        };
        AppConnector.request(params).then(
            function (data) {
                app.helper.hideProgress();
                if (data.success == true) {
                    form = jQuery('#EmailMarketingStep4');
                    var campaign = data.result.campaign;
                    var template = data.result.template;
                    var tableList = '';
                    if (campaign.countLeads != 0) {
                        tableList += '<tr>' +
                            '<td>Leads</td>' +
                            '<td>' + campaign.countLeads + '</td>' +
                            '</tr>';
                    }
                    if (campaign.countOrganization != 0) {
                        tableList += '<tr>' +
                            '<td>Organization</td>' +
                            '<td>' + campaign.countOrganization + '</td>' +
                            '</tr>';
                    }
                    if (campaign.countContacts != 0) {
                        tableList += '<td>Contacts</td>' +
                            '<td>' + campaign.countContacts + '</td>' +
                            '</tr>';
                    }
                    form.find('#campaignName').html('<h4>' + campaign.campaignName + '</h4>');
                    form.find('#sender').html('<h4>' + campaign.sender + '</h4>');
                    form.find('#list').html(tableList);
                    form.find('#subject').html('<h4>' + template.subject + '</h4>');
                    form.find('#subject').html('<h4>' + template.subject + '</h4>');
                    form.find('#subject').html('<h4>' + template.subject + '</h4>');
                    var modalTestMail = jQuery('#modalTestEmail');


                    modalTestMail.find('input[name="subject"]').val(template.subject);

                    modalTestMail.find('textarea[name="description"]').text(template.body);

                    var totalRecord = jQuery('#EditView').find('[name="totalRecord"]').val();

                    var modalScheduleLater = jQuery('#modalScheduleLater');

                    modalScheduleLater.find('#total').html(totalRecord);


                    thisInstance.RegisterPreviewRecordVTEEmailMarketing();
                    thisInstance.registerPreviewEmailTemplate();
                    thisInstance.registerShowModalTestEmail();
                    thisInstance.registerShowModalScheduleLater();

                }

            }
        );


    },

    RegisterPreviewRecordVTEEmailMarketing: function () {
        var thisInstance = this;
        jQuery('#PreviewRecord').on('click', function (e) {
            var html = jQuery('#ModalPreviewRecord').html();
            app.helper.loadPageContentOverlay(html);
            thisInstance.ContentPreviewRecordVTEEmailMarketing();

        });
    },
    ContentPreviewRecordVTEEmailMarketing : function () {
        app.helper.showProgress();
        var recordId = jQuery('input[name="idEmailMarketing"]').val();
        var container = jQuery('#overlayPageContent');
        var pageNumber = container.find('#pageToJumpCampains').val();
        if(pageNumber == ""){
            pageNumber = 1;
        }
        var params = {
            'module' : 'VTEEmailMarketing',
            'action' : 'ActionAjax',
            'mode' : 'getRelatedRecordVTEEMailMarketing',
            'recordId' : recordId,
            'pageNumber': pageNumber
        };
        AppConnector.request(params).then(function (data) {
            if(data.success){
                app.helper.hideProgress();
                var tbody = jQuery('#table-preview-record').find('tbody');
                var result = data.result.list;
                var html = '';
                for(var i =0; i < result.length ; i++){
                    html += '<tr>';
                    html += '<td><h5>'+app.vtranslate(result[i].module,'VTEEmailMarketing')+'</h5></td>';
                    if(result[i].module == 'Leads'){
                        html += '<td><h5>'+result[i].label+'</h5></td>';
                        html += '<td><h5>'+result[i].lead_email+'</h5></td>';
                    }
                    else if(result[i].module == 'Contacts'){
                        html += '<td><h5>'+result[i].label+'</h5></td>';
                        html += '<td><h5>'+result[i].contacts_email+'</h5></td>';
                    }else{
                        html += '<td><h5>'+result[i].label+'</h5></td>';
                        html += '<td><h5>'+result[i].account_email+'</h5></td>';
                    }
                    html += '</tr>';
                }
                tbody.html(html);

                //update number page
                var pagging = data.result.pagging;
                container.find('.pageNumbersText').text(pagging["startIndex"]+" to "+pagging["endIndex"]);
                container.find('#totalPageCount').text(pagging["maxPage"]);
                container.find('.totalNumberOfRecords').text("of " + pagging["maxRecord"]);
                container.find('.maxRecord').text("of " + pagging["maxPage"]);
                container.find('.pageCurrent').text("of " + pageNumber);
                container.find('#pageToJumpCampains').val(pageNumber);
                //update distable next and prepage
                container.find('#PreviousPageButtonCampains').attr("disabled","disabled");
                container.find('#NextPageButton').attr("disabled","disabled");
                if(pageNumber == 1){
                    if(pagging["maxPage"] > 1){
                        container.find('#NextPageButton').removeAttr("disabled");
                    }
                }else if(pageNumber > 1 ){
                    if(pageNumber  < pagging["maxPage"]){
                        container.find('#PreviousPageButtonCampains').removeAttr("disabled");
                        container.find('#NextPageButton').removeAttr("disabled");
                    }

                    if(pagging["maxPage"] == pageNumber){
                        container.find('#PreviousPageButtonCampains').removeAttr("disabled");
                        container.find('#NextPageButton').attr("disabled","disabled");
                    }
                }
            }
        });
    },
    registerPreviewEmailTemplate: function () {
        var thisInstance = this;
        jQuery('#PreviewEmailTemplate').on('click', function () {
            var record = jQuery('input[name="templateEmail"]').val();
            var params = {
                'module': 'EmailTemplates',
                'view': "ListAjax",
                "mode": "previewTemplate",
                "record": record
            };
            app.helper.showProgress();
            app.request.post({data: params}).then(function (error, data) {
                app.helper.loadPageContentOverlay(data).then(function () {
                    thisInstance.showTemplateContent(record);

                });
            });
        });
    },
    showTemplateContent: function (record) {
        var params = {
            "module": "EmailTemplates",
            "action": "ShowTemplateContent",
            "mode": "getContent",
            "record": record
        };
        app.request.post({data: params}).then(function (error, data) {
            var templateContent = data.content;
            jQuery('#TemplateIFrame').contents().find('html').html(templateContent);
            app.helper.hideProgress();
        });
    },
    //pagging PreviewRecordVTEEmailMarketing
    registerPaggingRecordVTEEmailMarketing:function (container) {
        var thisIntances = this;
        container = jQuery('#overlayPageContent');
        //Click button ...
        container.on('click','#PageJumpCampains',function () {
            container.find('#PageJumpDropDownCampains').toggle();
        });

        //Click Go submit
        container.on('click','#pageToJumpSubmitCampains',function () {
            var container = jQuery('#overlayPageContent');
            var pageNumber = parseInt(container.find('#pageToJumpCampains').val()) ;
            var maxPage = parseInt(container.find('#totalPageCount').text()) ;
            if(pageNumber <= maxPage){
                thisIntances.ContentPreviewRecordVTEEmailMarketing();
                container.find('#PageJumpDropDownCampains').hide();
            }else{
                alert('Max page '+maxPage + " Please Enter again !");
            }
        });

        //Click PrePage
        container.on('click','#PreviousPageButtonCampains',function () {
            var pagerCurrent = container.find('#pageToJumpCampains');
            var pagerCurrentVal = parseInt(pagerCurrent.val()) - 1;
            pagerCurrent.val(pagerCurrentVal);
            thisIntances.ContentPreviewRecordVTEEmailMarketing();
        });

        //Click NextPage
        container.on('click','#NextPageButton',function () {
            var pagerCurrent = container.find('#pageToJumpCampains');
            var pagerCurrentVal = parseInt(pagerCurrent.val()) + 1;
            pagerCurrent.val(pagerCurrentVal);
            thisIntances.ContentPreviewRecordVTEEmailMarketing();
        })
    },




    // Test Send Mail
    registerShowModalTestEmail: function () {
        var thisInstance = this;
        jQuery('button[name="TestEmail"]').on('click', function () {
            var html = jQuery('#modalTestEmail').html();
            app.helper.showModal(html);
            thisInstance.registerSendTestEmail();
        });
    },
    registerSendTestEmail: function () {
        var form = jQuery('div.myModal').find("#massEmailForm");
        form.on('click', '#sendEmail', function (e) {
            var outgoingServer = $('#EmailMarketingStep1').find('[name="from_serveremailid"]').val();
            var params = {
                submitHandler: function () {
                    app.helper.hideModal();
                    app.helper.showProgress();
                    var templateEmail = jQuery('[name="templateEmail"]').val();
                    var from_name = jQuery('[name="from_name"]').val();
                    var from_email = jQuery('[name="from_email"]').val();
                    var to = form.find('[name="to"]').val();

                    var postParams = {
                        'module': 'VTEEmailMarketing',
                        'action': 'ActionAjax',
                        'mode': 'TestSendMail',
                        'templateEmail': templateEmail,
                        'from_name': from_name,
                        'from_email': from_email,
                        'to' : to,
                        'outgoing_server' : outgoingServer,
                    };
                    AppConnector.request(postParams).then(function (data) {
                        app.helper.hideProgress();
                        app.helper.showModal(data.result);
                    });
                }
            };
            form.vtValidate(params);
        });
    },
    // End Test Send Mail

    // Schedule Later
    registerShowModalScheduleLater: function () {
        var thisInstance = this;
        jQuery('button[name="ScheduleLater"]').on('click', function () {
            var formEdit = jQuery('form#EditView');
            formEdit.find('[name="status"]').val('Sending');
            var html = jQuery('#modalScheduleLater').html();
            app.helper.showModal(html);
            thisInstance.registerShowHideWithBatchDelivery();
            thisInstance.registerSaveScheduleLater();
        });
    },
    registerShowHideWithBatchDelivery: function () {
        var form = jQuery('div.myModal > #formScheduleLater');
        var config = form.find('.schedule-config-send');
        var checkBatchDelivery = form.find('input[name="schedule_batch_delivery"]');
        if (checkBatchDelivery.val() == 1 ) {
            config.removeClass('hide');
        } else {
            config.addClass('hide');
        }
        jQuery(document).on('click', '#btn-On-Off', function () {
            var check = jQuery(this).attr("aria-pressed");
            if (check == 'true') {
                config.removeClass('hide');
                checkBatchDelivery.val(1);
            } else {
                config.addClass('hide');
                checkBatchDelivery.val(0);
            }
        });
    },
    registerSaveScheduleLater: function () {
        var form = jQuery('div.myModal > #formScheduleLater');
        form.on('click', '#saveScheduleLater', function (e) {
            var record = jQuery('.editContainer').find('input[name="idEmailMarketing"]').val();
            var schedule_date = form.find('input[name="schedule_date"]').val();
            var schedule_time = form.find('input[name="schedule_time"]').val();
            var schedule_batch_delivery = form.find('input[name="schedule_batch_delivery"]').val();
            var schedule_number_email = form.find('input[name="schedule_number_email"]').val();
            var schedule_frequency = form.find('select[name="schedule_frequency"]').val();
            var total_email =  jQuery('.editContainer').find('input[name="totalRecord"]').val();
            var templateEmail = jQuery('[name="templateEmail"]').val();
            var from_name = jQuery('[name="from_name"]').val();
            var from_email = jQuery('[name="from_email"]').val();
            var countEmail = schedule_number_email;
            var from_serveremailid = jQuery('input[name="from_serveremailid"]').val();
            if (schedule_number_email == "" && schedule_batch_delivery == 1) {
                app.helper.showErrorNotification({message: app.vtranslate('NUMBER_EMAIL_EMPTY')});
                return false;
            }
            if (schedule_batch_delivery == 0) {
                app.helper.hideModal();
                app.helper.showProgress();
                var params = {
                    module: 'VTEEmailMarketing',
                    action: 'ActionAjax',
                    mode: 'saveScheduleLater',
                    "schedule_date": schedule_date,
                    "schedule_time": schedule_time,
                    "schedule_batch_delivery": schedule_batch_delivery,
                    "schedule_number_email": schedule_number_email,
                    "schedule_frequency": schedule_frequency,
                    "recordId": record,
                    "total_email": total_email,
                    "templateEmail": templateEmail,
                    "from_name": from_name,
                    "from_email": from_email,
                    "from_serveremailid": from_serveremailid,
                };
                AppConnector.request(params).then(
                    function (data) {
                        if (data.success == true) {
                            var formEdit = jQuery('form#EditView');
                            formEdit.submit();
                        }
                    }
                );
            }
            else {
                var time = parseInt(schedule_frequency)/60;
                var message = '<b>' + total_email + '</b> ' + app.vtranslate('Emails will be queued and send on set schedule (<b>' + countEmail +'</b> Emails every '+ time +' minutes)');
                app.helper.showConfirmationBox({'message': message}).then(
                    function (e) {
                        app.helper.hideModal();
                        app.helper.showProgress();
                        var params = {
                            module: 'VTEEmailMarketing',
                            action: 'ActionAjax',
                            mode: 'saveScheduleLater',
                            "schedule_date": schedule_date,
                            "schedule_time": schedule_time,
                            "schedule_batch_delivery": schedule_batch_delivery,
                            "schedule_number_email": schedule_number_email,
                            "schedule_frequency": schedule_frequency,
                            "recordId": record,
                            "total_email": total_email,
                            "templateEmail": templateEmail,
                            "from_name": from_name,
                            "from_email": from_email,
                            "from_serveremailid": from_serveremailid
                        };
                        AppConnector.request(params).then(
                            function (data) {
                                if (data.success == true) {
                                    app.helper.hideProgress();
                                    var formEdit = jQuery('form#EditView');
                                    bootbox.alert('Your emails have been queued and will be sent shortly', function(){
                                        formEdit.submit();
                                    });
                                }
                            }
                        );
                    }
                );
            }
        });
    },
    // End Schedule Later

    // Send Now

    registerSendNow : function () {
        var step4 = jQuery('#EmailMarketingStep4');
        var btn = step4.find('[name="SendNow"]');
        btn.on('click',function () {
            var formEdit = jQuery('form#EditView');
            formEdit.find('[name="status"]').val('Sending');
            var idEmailMarketing = jQuery('input[name="idEmailMarketing"]').val();
            var from_name = jQuery('input[name="from_name"]').val();
            var from_email = jQuery('input[name="from_email"]').val();
            var idTemplateEmail = jQuery('input[name="templateEmail"]').val();
            var total_email = jQuery('input[name="totalRecord"]').val();
            var from_serveremailid = jQuery('input[name="from_serveremailid"]').val();
            var message = '<b>' + total_email + '</b> ' + app.vtranslate('NUMBER_EMAIL_QUEUED_AND_SENT_IMMEDIATELY');
            app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    app.helper.showProgress();
                    var params = {
                        module: 'VTEEmailMarketing',
                        action: 'ActionAjax',
                        mode: 'saveScheduleLater',
                        "recordId": idEmailMarketing,
                        "total_email": total_email,
                        "templateEmail": idTemplateEmail,
                        "from_name": from_name,
                        "from_email": from_email,
                        "from_serveremailid": from_serveremailid
                    };
                    AppConnector.request(params).then(
                        function (data) {
                            if(data.success == true){
                                app.helper.hideProgress();
                                bootbox.alert('Your emails have been queued and will be sent shortly', function(){
                                    formEdit.submit();
                                });
                            }
                        }
                    );
                }
            );
            jQuery('.bootbox-confirm .confirm-box-ok').text("Proceed to Queue/Send Emails");
        });
    },

    //End Send Now

    /** -- End Step 4 -- **/


    registerEvents: function () {
        this._super();
        this.registerButtonBack();
        this.registerButtonCancel();
        this.registerEventNextStep1();
        this.registerEventNextStep2();
        this.registerEventNextStep3();
        var thisInstance = this;



        //Function step 3 Start
        this.registerViewType();
        this.registerViewRefresh();
        this.registerNextPage();
        this.registerPrePage();
        this.registerPageJump();
        this.registerpageJumpOnSubmit();
        this.registerTotalNumOfRecords();
        this.registerSearchTemplate();
        this.registerThumbnailHoverActionEvent();
        this.registerTemplateSelectEvent();
        this.registerPreviewTemplateEvent();
        this.registerButtonBackStep3();
        this.registerEditTemplate();
        this.registerDuplicateTemplate();
        //Function step 3 End

        //step4
        this.registerPaggingRecordVTEEmailMarketing();


    }
});

jQuery(document).ready(function () {
    var instance = new VTEEmailMarketing_Create_Js();
    instance.registerEvents();
});