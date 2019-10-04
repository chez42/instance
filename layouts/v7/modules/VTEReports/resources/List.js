/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
Vtiger_List_Js("VTEReports_List_Js",{

    listInstance : false,

    addReport : function(url){
        var listInstance = VTEReports_List_Js.listInstance;
        window.location.href=url+'&folder='+listInstance.getCurrentCvId();
    },

    triggerAddFolder : function(url) {
        var params = url;
        var progressIndicatorElement = jQuery.progressIndicator();
        app.request.get(params).then(
            function(err,data) {
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                var callBackFunction = function(data){
                    jQuery('#addFolder').validationEngine({
                        // to prevent the page reload after the validation has completed
                        'onValidationComplete' : function(form,valid){
                            return valid;
                        }
                    });
                    VTEReports_List_Js.listInstance.folderSubmit().then(function(data){
                        if(data.success){
                            var result = data.result;
                            if(result.success){
                                //TODO use pines alert for showing folder has saved
                                app.hideModalWindow();
                                var info = result.info;
                                VTEReports_List_Js.listInstance.updateCustomFilter(info);
                            } else {
                                var result = result.message;
                                var folderNameElement = jQuery('#foldername');
                                folderNameElement.validationEngine('showPrompt', result , 'error','topLeft',true);
                            }
                        } else {
                            app.hideModalWindow();
                            var params = {
                                title : app.vtranslate('JS_ERROR'),
                                text : data.error.message
                            }
                            app.helper.showPnotify(params);
                        }
                    });
                };
                app.showModalWindow(data,function(data){
                    if(typeof callBackFunction == 'function'){
                        callBackFunction(data);
                    }
                });
            }
        )
    },
    massDelete : function(url) {
        var listInstance = VTEReports_List_Js.listInstance;
        var selectedIds = listInstance.readSelectedIds(true);
        var excludedIds = listInstance.readExcludedIds(true);
        var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
        app.helper.showConfirmationBox({'message' : message}).then(
            function(e) {
                var deleteURL = url+'&selected_ids='+selectedIds+'&excluded_ids='+excludedIds;
                var deleteMessage = app.vtranslate('JS_RECORDS_ARE_GETTING_DELETED');
                app.helper.showProgress(deleteMessage);
                var actionParams = {
                    url: deleteURL
                };
                app.request.post(actionParams).then(
                    function(err,data) {
                        app.helper.hideProgress();
                        if(err === null){
                            location.reload();
                        }
                    });
            },
            function(error, err){
            }
        );
    },
    massMove : function(url){
        var listInstance = VTEReports_List_Js.listInstance;
        var validationResult = listInstance.checkListRecordSelected();
        if(validationResult != true){
            var selectedIds = listInstance.readSelectedIds(true);
            var excludedIds = listInstance.readExcludedIds(true);
            var postData = {
                "selected_ids":selectedIds,
                "excluded_ids" : excludedIds
            };

            var params = {
                "url":url,
                "data" : postData
            };
            app.helper.showProgress();
            app.request.post(params).then(
                function(err,data) {
                    app.helper.hideProgress();
                    var callBackFunction = function() {
                        var reportsListInstance = new VTEReports_List_Js();
                        reportsListInstance.moveReports();
                    }
                    //app.showModalWindow(data,callBackFunction);
                    var params = {};
                    params.cb = callBackFunction
                    //app.helper.showModal(data, params);
                    app.helper.showModal(data, params);
                }
            )
        } else{
            listInstance.noRecordSelectedAlert();
        }

    }

},{

    init : function(){
        this._super();
        VTEReports_List_Js.listInstance = this;
        VTEReports_List_Js.listInstance.registerDeleteRecordClickEvent();
        VTEReports_List_Js.listInstance.registerActivateLicenseEvent();
        VTEReports_List_Js.listInstance.registerValidEvent();
        VTEReports_List_Js.listInstance.initiate();
    },
    initiate:function() {
        var a = jQuery(".installationContents").find(".step").val();
        this.initiateStep(a);
    },
    initiateStep:function(a) {
        this.activateHeader("step" + a);
    },
    activateHeader:function(a) {
        var b = jQuery(".crumbs ");
        b.find(".active").removeClass("active");
        jQuery("#" + a, b).addClass("active");
    },
    registerActivateLicenseEvent:function() {
        var a = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
            var b = jQuery("#license_key");
            if ("" == b.val()) {
                //return b.validationEngine("showPrompt", "License Key cannot be empty", "error", "bottomLeft", !0), a.reject(), a.promise();
                params = {
                    title: app.vtranslate('License Key cannot be empty'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                jQuery("#license_key").focus();
                return false;
            }
            app.helper.showProgress();
            var params = {
                "url":"index.php?module=VTEReports&action=Activate&mode=activate",
                "data" : {license:b.val()}
            };
            app.request.post(params).then(
                function(err,a){
                    if(err === null) {
                        app.helper.hideProgress();
                        if(a.message != "Valid License" ){
                            params = {
                                title: app.vtranslate('Invalid License'),
                                type: 'error'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            jQuery("#license_key").focus();
                        }
                        else{
                            document.location.href = "index.php?module=VTEReports&view=List&mode=step3"
                        }
                    }
                }
            );
        });
    }, registerValidEvent:function() {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            app.helper.showProgress();
            var params = {
                "url":"index.php?module=VTEReports&action=Activate&mode=valid"
            };
            app.request.post(params).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        console.log(data);
                        //var result = jQuery.parseJSON(a);
                        if(data == "success"){
                            document.location.href = "index.php?module=VTEReports&view=List"
                        } 
                    }
                }
            );
        });
    },
    deleteVteReport:function(a) {
        if ("undefined" == typeof a) {
            return false;
        }
        var b = VTEReports_List_Js.listInstance, c = app.vtranslate("LBL_DELETE_CONFIRMATION");
        app.helper.showConfirmationBox({message:c}).then(function(d) {
            var deleteURL = "index.php?module=VTEReports&action=DeleteVteReports&record=" + a;
            var deleteMessage = app.vtranslate("JS_RECORDS_ARE_GETTING_DELETED");
            app.helper.showProgress(deleteMessage);
            var actionParams = {
                url: deleteURL
            };
            app.request.post(actionParams).then(function(e,a) {
                app.helper.hideProgress();
                location.reload();
            });
        }, function(a, b) {
        });
    },
    folderSubmit : function(){
        var aDeferred = jQuery.Deferred();
        jQuery('#addFolder').on('submit',function(e){
            var validationResult = jQuery(e.currentTarget).validationEngine('validate');
            if(validationResult == true){
                var formData = jQuery(e.currentTarget).serializeFormData();
                AppConnector.request(formData).then(
                    function(data){
                        aDeferred.resolve(data);
                    }
                );
            }
            e.preventDefault();
        });
        return aDeferred.promise();
    },

    moveReports : function(){
        var aDeferred = jQuery.Deferred();
        jQuery('#moveReports').on('submit',function(e){
            var formData = jQuery(e.currentTarget).serializeFormData();
            //var selected_ids =
            var params = {
                "url":"index.php?module=VTEReports&action=MoveReports",
                "data" : formData
            };
            app.request.post(params).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideModal();
                        location.reload();
                    }
                }
            );
            e.preventDefault();
        });
        return aDeferred.promise();
    },

    updateCustomFilter : function (info){
        var folderId = info.folderId;
        var customFilter =  jQuery("#customFilter");
        var constructedOption = this.constructOptionElement(info);
        var optionId = 'filterOptionId_'+folderId;
        var optionElement = jQuery('#'+optionId);
        if(optionElement.length > 0){
            optionElement.replaceWith(constructedOption);
            customFilter.trigger("liszt:updated");
        } else {
            customFilter.find('#foldersBlock').append(constructedOption).trigger("liszt:updated");
        }
    },

    constructOptionElement : function(info){
        return '<option data-editable="'+info.isEditable+'" data-deletable="'+info.isDeletable+'" data-editurl="'+info.editURL+'" data-deleteurl="'+info.deleteURL+'" class="filterOptionId_'+info.folderId+'" id="filterOptionId_'+info.folderId+'" value="'+info.folderId+'" data-id="'+info.folderId+'">'+info.folderName+'</option>';

    },
    /*
     * function to delete the folder
     */
    deleteFolder : function(event,url){
        var thisInstance =this;
        AppConnector.request(url).then(
            function(data){
                if(data.success) {
                    var chosenOption = jQuery(event.currentTarget).closest('.select2-result-selectable');
                    var selectOption = thisInstance.getSelectOptionFromChosenOption(chosenOption);
                    selectOption.remove();
                    var customFilterElement = thisInstance.getFilterSelectElement();
                    customFilterElement.trigger("liszt:updated");
                    var defaultCvid = customFilterElement.find('option:first').val();
                    customFilterElement.select2("val", defaultCvid);
                    customFilterElement.trigger('change');
                } else {
                    app.hideModalWindow();
                    var params = {
                        title : app.vtranslate('JS_INFORMATION'),
                        text : data.error.message
                    }
                    app.helper.showPnotify(params);
                }
            }
        )
    },
    getDefaultParams : function() {
        var pageNumber = jQuery('#pageNumber').val();
        var module = app.getModuleName();
        var parent = app.getParentModuleName();
        var cvId = this.getCurrentCvId();
        var orderBy = jQuery('#orderBy').val();
        var sortOrder = jQuery("#sortOrder").val();
        var params = {
            'module': module,
            'parent' : parent,
            'page' : pageNumber,
            'view' : "List",
            'viewname' : cvId,
            'orderby' : orderBy,
            'sortorder' : sortOrder
        }
        params.search_params = JSON.stringify(this.getListSearchParams());
        return params;
    },
    getListSearchParams : function(){
        var listViewPageDiv = this.getListViewContainer();
        var listViewTable = listViewPageDiv.find('.listViewEntriesTable');
        var searchParams = new Array();
        listViewTable.find('.listSearchContributor').each(function(index,domElement){
            var searchInfo = new Array();
            var searchContributorElement = jQuery(domElement);
            var fieldInfo = searchContributorElement.data('fieldinfo');
            var fieldName = searchContributorElement.attr('name');

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
            }else if (  fieldInfo.type == "boolean" || fieldInfo.type == "picklist") {
                searchOperator = 'e';
            }else if( fieldInfo.type == 'currency'  || fieldInfo.type == "double" ||
                fieldInfo.type == 'percentage' || fieldInfo.type == "integer"  ||
                fieldInfo.type == "number"){
                if(searchValue.substring(0,2) == '>=' ) {
                    searchOperator = 'h';
                } else if ( searchValue.substring(0,2)== '<=') {
                    searchOperator = 'm';
                } else   if(searchValue.substring(0,1) == '>' ) {
                    searchOperator = 'g';
                } else if ( searchValue.substring(0,1)== '<') {
                    searchOperator = 'l';
                } else {
                    searchOperator = 'e';
                }
            }
            searchInfo.push(fieldName);
            searchInfo.push(searchOperator);
            searchInfo.push(searchValue);
            searchParams.push(searchInfo);
        });
        return new Array(searchParams);
    },
    /*
     * Function to register the click event for edit filter
     */
    registerEditFilterClickEvent : function(){
        var thisInstance = this;
        var listViewFilterBlock = this.getFilterBlock();
        listViewFilterBlock.on('mouseup','li i.editFilter',function(event){
            var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
            var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
            var editUrl = currentOptionElement.data('editurl');
            VTEReports_List_Js.triggerAddFolder(editUrl);
            event.stopPropagation();
        });
    },

    /*
     * Function to register the click event for delete filter
     */
    registerDeleteFilterClickEvent: function(){
        var thisInstance = this;
        var listViewFilterBlock = this.getFilterBlock();
        //used mouseup event to stop the propagation of customfilter select change event.
        listViewFilterBlock.on('mouseup','li i.deleteFilter',function(event){
            // To close the custom filter Select Element drop down
            thisInstance.getFilterSelectElement().data('select2').close();
            var liElement = jQuery(event.currentTarget).closest('.select2-result-selectable');
            var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
            app.helper.showConfirmationBox({'message' : message}).then(
                function(e) {
                    var currentOptionElement = thisInstance.getSelectOptionFromChosenOption(liElement);
                    var deleteUrl = currentOptionElement.data('deleteurl');
                    thisInstance.deleteFolder(event,deleteUrl);
                },
                function(error, err){
                }
            );
            event.stopPropagation();
        });
    },
    registerEvents : function(){
        this._super();
    }
});
jQuery(document).on('click','.toggleFilterSize',function(e){
    var currentTarget = jQuery(e.currentTarget);
    currentTarget.closest('.list-group').find('li.filterHidden').toggleClass('hide');
    if(currentTarget.closest('.list-group').find('li.filterHidden').hasClass('hide')) {
        currentTarget.html(currentTarget.data('moreText'));
    }else{
        currentTarget.html(currentTarget.data('lessText'));
    }
});
jQuery(document).on('click','.vteViewByFolder',function(e){
    var currentTarget = jQuery(e.currentTarget);
    var folderId = currentTarget.data('filter-id');
    var customFilterElement = jQuery('select[name="folderid"]');
    if(folderId != "All"){
        customFilterElement.select2("val",folderId);
        customFilterElement.trigger("liszt:updated");
    }
    else{
        //customFilterElement.select2();
        customFilterElement.val('').trigger('change');
        customFilterElement.trigger("liszt:updated");
        //val(null).trigger("change")
    }
    jQuery('button[data-trigger="listSearch"]').trigger("click");
});
var filters = jQuery('#module-filters');
filters.find('.search-list').on('keyup',function(e){
    var element = jQuery(e.currentTarget);
    var val = element.val().toLowerCase();
    filters.find('.toggleFilterSize').removeClass('hide');
    jQuery('li.listViewFilter').each(function(){
        var filterEle = jQuery(this);
        var filterName = filterEle.find('a.filterName').html();
        var listsMenu = filterEle.closest('ul.lists-menu');
        if(typeof filterName != 'undefined') {
            filterName = filterName.toLowerCase();
            if(filterName.indexOf(val) === -1){
                filterEle.addClass('filter-search-hide').removeClass('filter-search-show');
                if(listsMenu.find('li.listViewFilter').filter(':visible').length == 0) {
                    listsMenu.closest('.list-group').addClass('hide');
                }
                if(jQuery('#module-filters').find('ul.lists-menu li').filter(':visible').length == 0) {
                    jQuery('#module-filters').find('.noLists').removeClass('hide');
                }
            }else{
                if(val) {
                    listsMenu.closest('.list-group').find('.toggleFilterSize').addClass('hide');
                }
                filterEle.removeClass('filter-search-hide').addClass('filter-search-show');
                listsMenu.closest('.list-group').removeClass('hide');
                jQuery('#module-filters').find('.noLists').addClass('hide');
            }
        }
    });
})

