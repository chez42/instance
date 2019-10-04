Vtiger_List_Js("VTEReports_List_Js", {listInstance:!1, addReport:function(a) {
    window.location.href = a + "&folder=" + VTEReports_List_Js.listInstance.getCurrentCvId();
}, init:function() {
    this.initiate();
    this.registerActivateLicenseEvent();
    this.registerValidEvent();
    this.filterListReports();
}, initiate:function() {
    var a = jQuery(".installationContents").find(".step").val();
    this.initiateStep(a);
}, initiateStep:function(a) {
    this.activateHeader("step" + a);
}, activateHeader:function(a) {
    var b = jQuery(".crumbs ");
    b.find(".active").removeClass("active");
    jQuery("#" + a, b).addClass("active");
}, registerActivateLicenseEvent:function() {
    var a = jQuery.Deferred();
    jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
        var b = jQuery("#license_key");
        if ("" == b.val()) {
            return b.validationEngine("showPrompt", "License Key cannot be empty", "error", "bottomLeft", !0), a.reject(), a.promise();
        }
        var c = jQuery.progressIndicator({position:"html", blockInfo:{enabled:!0}}), d = {};
        d.module = app.getModuleName();
        d.action = "Activate";
        d.mode = "activate";
        d.license = b.val();
        AppConnector.request(d).then(function(a) {
            c.progressIndicator({mode:"hide"});
            a.success && (a = a.result.message, "Valid License" != a ? (jQuery("#error_message").html(a), jQuery("#error_message").show()) : document.location.href = "index.php?module=VTEReports&view=List&mode=step3");
        }, function(a) {
            c.progressIndicator({mode:"hide"});
        });
    });
}, registerValidEvent:function() {
    jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
        var a = jQuery.progressIndicator({position:"html", blockInfo:{enabled:!0}}), b = {};
        b.module = app.getModuleName();
        b.action = "Activate";
        b.mode = "valid";
        AppConnector.request(b).then(function(b) {
            a.progressIndicator({mode:"hide"});
            b.success && (document.location.href = "index.php?module=VTEReports&view=List");
        }, function(b) {
            a.progressIndicator({mode:"hide"});
        });
    });
}, deleteRecord:function(a) {
    if ("undefined" == typeof a) {
        return !1;
    }
    var b = VTEReports_List_Js.listInstance, c = app.vtranslate("LBL_DELETE_CONFIRMATION");
    Vtiger_Helper_Js.showConfirmationBox({message:c}).then(function(d) {
        d = "index.php?module=VTEReports&action=DeleteVteReports&record=" + a;
        var c = app.vtranslate("JS_RECORDS_ARE_GETTING_DELETED"), e = jQuery.progressIndicator({message:c, position:"html", blockInfo:{enabled:!0}});
        AppConnector.request(d).then(function(a) {
            e.progressIndicator({mode:"hide"});
            a && b.massActionPostOperations(a);
        });
    }, function(a, b) {
    });
},
    filterListReports:function(){
        var thisInstance = this;
        jQuery("#btn_vte_report_filter").on("click",function(){
            //Get param
            var searchParams = new Array();
            jQuery('.vte_fillter_row').find('.listSearchContributor').each(function(index,domElement){
                var searchInfo = new Array();
                var searchContributorElement = jQuery(domElement);
                var fieldName = searchContributorElement.attr('name');
                var searchValue = searchContributorElement.val();
                var searchOperator = 'c';
                searchInfo.push(fieldName);
                searchInfo.push(searchOperator);
                searchInfo.push(searchValue);
                searchParams.push(searchInfo);
            });
            thisInstance.getListViewRecords(searchParams);
        });
    },
    massDelete:function(a) {
    var b = VTEReports_List_Js.listInstance;
    if (1 != b.checkListRecordSelected()) {
        var c = b.readSelectedIds(!0), d = b.readExcludedIds(!0), f = b.getCurrentCvId(), e = app.vtranslate("LBL_DELETE_CONFIRMATION");
        Vtiger_Helper_Js.showConfirmationBox({message:e}).then(function(e) {
            e = a + "&viewname=" + f + "&selected_ids=" + c + "&excluded_ids=" + d;
            var g = app.vtranslate("JS_RECORDS_ARE_GETTING_DELETED"), h = jQuery.progressIndicator({message:g, position:"html", blockInfo:{enabled:!0}});
            AppConnector.request(e).then(function(a) {
                h.progressIndicator({mode:"hide"});
                a && b.massActionPostOperations(a);
            });
        }, function(a, b) {
        });
    } else {
        b.noRecordSelectedAlert();
    }
}, massMove:function(a) {
    var b = VTEReports_List_Js.listInstance;
    if (1 != b.checkListRecordSelected()) {
        var c = b.readSelectedIds(!0), d = b.readExcludedIds(!0), f = b.getCurrentCvId();
        AppConnector.request({url:a, data:{selected_ids:c, excluded_ids:d, viewname:f}}).then(function(a) {
            app.showModalWindow(a, function(a) {
                (new VTEReports_List_Js).moveReports().then(function(a) {
                    a && b.massActionPostOperations(a);
                });
            });
        });
    } else {
        b.noRecordSelectedAlert();
    }
}}, {init:function() {
    VTEReports_List_Js.listInstance = this;
}, moveReports:function() {
    var a = jQuery.Deferred();
    jQuery("#moveReports").on("submit", function(b) {
        var c = jQuery(b.currentTarget).serializeFormData();
        AppConnector.request(c).then(function(b) {
            a.resolve(b);
        });
        b.preventDefault();
    });
    return a.promise();
}, constructOptionElement:function(a) {
    return '<option data-editable="' + a.isEditable + '" data-deletable="' + a.isDeletable + '" data-editurl="' + a.editURL + '" data-deleteurl="' + a.deleteURL + '" class="filterOptionId_' + a.folderId + '" id="filterOptionId_' + a.folderId + '" value="' + a.folderId + '" data-id="' + a.folderId + '">' + a.folderName + "</option>";
}, massActionPostOperations:function(a) {
    var b = this, c = this.getCurrentCvId();
    a.success ? (a = app.getModuleName(), AppConnector.request("index.php?module=" + a + "&view=List&viewname=" + c).then(function(a) {
        jQuery("#recordsCount").val("");
        jQuery("#totalPageCount").text("");
        app.hideModalWindow();
        b.getListViewContentContainer().html(a);
        jQuery("#deSelectAllMsg").trigger("click");
        b.calculatePages().then(function() {
            b.updatePagination();
        });
    })) : (app.hideModalWindow(), c = {title:app.vtranslate("JS_LBL_PERMISSION"), text:a.error.message + " : " + a.error.code}, Vtiger_Helper_Js.showPnotify(c));
}, deleteFolder:function(a, b) {
    var c = this;
    AppConnector.request(b).then(function(b) {
        if (b.success) {
            b = jQuery(a.currentTarget).closest(".select2-result-selectable");
            c.getSelectOptionFromChosenOption(b).remove();
            b = c.getFilterSelectElement();
            b.trigger("liszt:updated");
            var f = b.find("option:first").val();
            b.select2("val", f);
            b.trigger("change");
        } else {
            app.hideModalWindow(), b = {title:app.vtranslate("JS_INFORMATION"), text:b.error.message}, Vtiger_Helper_Js.showPnotify(b);
        }
    });
}, registerDeleteRecordClickEvent:function() {
    this.getListViewContentContainer().on("click", ".deleteRecordButton", function(a) {
        var b = jQuery(a.currentTarget).closest("tr").data("id");
        VTEReports_List_Js.deleteRecord(b);
        a.stopPropagation();
    });
}, registerEditFilterClickEvent:function() {
    var a = this;
    this.getFilterBlock().on("mouseup", "li i.editFilter", function(b) {
        var c = jQuery(b.currentTarget).closest(".select2-result-selectable"), c = a.getSelectOptionFromChosenOption(c).data("editurl");
        VTEReports_List_Js.triggerAddFolder(c);
        b.stopPropagation();
    });
}, registerDeleteFilterClickEvent:function() {
    var a = this;
    this.getFilterBlock().on("mouseup", "li i.deleteFilter", function(b) {
        a.getFilterSelectElement().data("select2").close();
        var c = jQuery(b.currentTarget).closest(".select2-result-selectable"), d = app.vtranslate("JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE");
        Vtiger_Helper_Js.showConfirmationBox({message:d}).then(function(d) {
            d = a.getSelectOptionFromChosenOption(c).data("deleteurl");
            a.deleteFolder(b, d);
        }, function(a, b) {
        });
        b.stopPropagation();
    });
}, registerEvents:function() {
    this.registerDeleteRecordClickEvent();
    this.registerRowClickEvent();
    this.registerPageNavigationEvents();
    this.registerMainCheckBoxClickEvent();
    this.registerCheckBoxClickEvent();
    this.registerSelectAllClickEvent();
    this.registerDeselectAllClickEvent();
    this.registerHeadersClickEvent();
    this.registerMassActionSubmitEvent();
    this.registerEventForAlphabetSearch();
    this.changeCustomFilterElementView();
    this.registerChangeCustomFilterEvent();
    this.registerCreateFilterClickEvent();
    this.registerEditFilterClickEvent();
    this.registerDeleteFilterClickEvent();
    this.registerApproveFilterClickEvent();
    this.registerDenyFilterClickEvent();
    this.registerCustomFilterOptionsHoverEvent();
    this.registerEmailFieldClickEvent();
    this.registerPhoneFieldClickEvent();
    Vtiger_Helper_Js.showHorizontalTopScrollBar();
    this.registerUrlFieldClickEvent();
    this.registerEventForTotalRecordsCount();
    var a = this.getListViewContentContainer();
    a.find("#listViewEntriesMainCheckBox,.listViewEntriesCheckBox").prop("checked", !1);
    this.registerListSearch();
}});
