/* ********************************************************************************
 * The content of this file is subject to the Columns ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTEColumns_Js",{

},{
    registerAddColumnsEvent: function () {
        jQuery('#moduleBlocks .editFieldsTable').each(function() {
            var block_id = jQuery(this).data('block-id');
            var first_child_div = jQuery(this).find('.blockActions');
            if(first_child_div.find(".cb_num_of_columns").length == 0){
                var actionParams = {
                    module:"VTEColumns",
                    view:"Columns",
                    mode:"showColumns",
                    block_id:block_id
                };
                app.request.post({data:actionParams}).then(
                    function(err,data) {
                        if(err == null && data) {
                            first_child_div.before(data);
                        }
                    }
                );
            }

        });
    },
    registerShowViewAsColumnsEvent: function (overlay) {
        var moduleName = app.getModuleName();
        var thisInstance = this;
        //For show detail from related list
        var top_url = window.location.href.split('?');
        var array_url = this.getQueryParams(top_url[1]);
        if(array_url.mode == "showRelatedList"){
            moduleName = array_url.relatedModule;
        }
        var view = app.getViewName();
        if(overlay !== null) view = overlay;
        if(view == "Detail"){
            var selectContainer = jQuery("#detailView");
            var block = selectContainer.find('.block:not(.combine),.block-child');
            var record_id = jQuery('#recordId').val();
            block.each(function(index,element){
                var this_block = jQuery(element);
                var block_id = this_block.find('img.blockToggle').data('id');
                //For working with vtetabs
                //for combine tab
                if(typeof block_id == "undefined"){
                    block_id = this_block.data('id');
                }
                if(typeof block_id == "undefined"){
                    var block_id_from_tabs = this_block.attr('id');
                    block_id_from_tabs = block_id_from_tabs.split("-");
                    block_id = block_id_from_tabs[1];
                }
                var actionParams = {
                    module:"VTEColumns",
                    view:"DetailViewAjax",
                    mode:"getBlockDetailView",
                    related_module_name:moduleName,
                    block_id:block_id,
                    record_id:record_id
                };
                //app.helper.showProgress(app.vtranslate("Loading columns..."));
                app.request.post({data:actionParams}).then(
                    function(err,data) {
                        if(err == null && data) {
                            //app.helper.hideProgress();
                            if(data !== "DEFAULT_COLUMN"){
                                if(jQuery('div.blockContent'+block_id).length > 0) jQuery('div.blockContent'+block_id).html(data);
                                else this_block.find('div.blockData').html(data);
                            }
                        }
                    }
                );
            });
        }
        if(view == "Edit"){
            var selectContainer = jQuery("#EditView");
            var block_labels = selectContainer.find('h4.fieldBlockHeader');
            var record_id = jQuery('[name="record"]').val();
            block_labels.each(function(index,element){
                var this_h4 = jQuery(element);
                var label = this_h4.text();
                var actionParams = {
                    module:"VTEColumns",
                    view:"Edit",
                    mode:"getBlockEditView",
                    related_module_name:moduleName,
                    block_label: label,
                    record:record_id
                };
                //app.helper.showProgress(app.vtranslate("Switching to tabs view"));
                app.request.post({data:actionParams}).then(
                    function(err,data) {
                        if(err == null && data) {
                            //app.helper.hideProgress();
                            if(data !== "DEFAULT_COLUMN"){
                                this_h4.next('hr').next('table.table').remove();
                                this_h4.next('hr').after(data);
                                var form = jQuery("#EditView");
                                var Edit_Js = new Vtiger_Edit_Js();
                                Edit_Js.registerEventForPicklistDependencySetup(form);
                                Edit_Js.registerFileElementChangeEvent(form);
                                Edit_Js.registerAutoCompleteFields(form);
                                Edit_Js.registerClearReferenceSelectionEvent(form);
                                Edit_Js.referenceModulePopupRegisterEvent(form);
                                Edit_Js.registerPostReferenceEvent(Edit_Js.getEditViewContainer());
                                Edit_Js.registerEventForImageDelete();
                                Edit_Js.registerImageChangeEvent();
                                vtUtils.applyFieldElementsView(form);
                                //Show MoreCurrencies link
                                var divMoreCurrencies = jQuery('#divMoreCurrencies');
                                var moreCurrencies = divMoreCurrencies.html();
                                if(divMoreCurrencies.length > 0){
                                    divMoreCurrencies.remove();
                                    jQuery('input[name="unit_price"]').after(moreCurrencies);
                                    var Prod_Js = new Products_Edit_Js();
                                    Prod_Js.registerEventForMoreCurrencies();
                                    Prod_Js.registerEventForUnitPrice();
                                    Prod_Js.registerRecordPreSaveEvent();
                                }
                            }
                        }
                    }
                );
            });
        }
    },
    getQueryParams:function(qs) {
        var result = {};
        if(typeof qs == "string"){
            var params = qs.split(/\?|\&/);
            params.forEach( function(it) {
                if (it) {
                    var param = it.split("=");
                    result[param[0]] = param[1];
                }
            });
        }
        return result;
    },
    registerSelectNumberColumnChange:function(){
        jQuery(document).on("change", ".cb_num_of_columns", function() {
            var next_link = jQuery(this).next(".custom_layout");
            next_link.trigger('click');
        });
    },
    registerShowTooltip:function(){
        var thisInstance = this;
        jQuery(document).on("hover", ".vtecolumn-tooltip", function() {
            var html =      'Standard VTiger uses 2 columns as standard layout. To change to 2 > columns, select number of columns and drag & drop fields to achieve desired layout.'
                +'</br></br>'
                +'<b><u>Important: You must click "Save Layout" for it to take affect.</b></u>'
                +'</br></br>'
                +' To change it back to 2 column layout, select 2 and click "Save Layout".';
            thisInstance.showVteColumnTooltip(jQuery(this),html);
        });
    },
    registerCustomLayoutClick:function(){
        var thisInstance = this;
        jQuery(document).on("click", ".custom_layout", function() {
            var url = jQuery(this).data("url");
            var block_id = jQuery(this).data("id");
            thisInstance.customLayout(url,block_id);
        });
    },
    customLayout: function(url,block_id) {
        var thisInstance = this;
        var module_name = jQuery("select[name='layoutEditorModules']").val();
        var num_of_columns = jQuery("#num_of_columns_"+block_id).val();
        var actionParams = {
            url: url,
            data:{num_of_columns :num_of_columns,selected_module:module_name},
            async: false
        };
        app.request.post(actionParams).then(
            function (err,data) {
                if(err === null) {
                    var callBackFunction = function () {
                        thisInstance.customLayoutSubmit();
                    };
                    var params = {};
                    params.cb = callBackFunction;
                    app.helper.showModal(data, params);
                    thisInstance.makeBlocksListSortable();
                    jQuery('#comnineTabContainer').draggable();
                    jQuery('#comnineTabContainer').css('cursor','move');
                }
            }
        );
    },
    customLayoutSubmit : function (){
        var instance = this;
        jQuery('#btnSaveCombine').on("click",function(e) {
            var block_id = jQuery("#block_id").val();
            var block_sequenced = [];
            var num_of_columns = jQuery("#num_of_columns_"+block_id).val();
            var contents = jQuery('#saveLayout').find('.blockSortable');
            contents.find('.editFields:visible').each(function (index, domElement) {
                var blockTable = jQuery(domElement);
                var field_id = blockTable.data('field-id');
                block_sequenced.push(field_id);
            });
            var module_name = jQuery("select[name='layoutEditorModules']").val();
            var actionParams = {
                url: "index.php?module=VTEColumns&action=ActionAjax&mode=saveLayout",
                data: {'block_id': block_id,'num_of_columns':num_of_columns,'block_sequenced':JSON.stringify(block_sequenced),'module_name':module_name}
            };
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        app.helper.showSuccessNotification({
                            message : data.message
                        });
                        app.helper.hideModal();
                    }
                }
            );
        });
    },
    /**
     * Function to regiser the event to make the blocks sortable
     */
    makeBlocksListSortable: function () {
        var thisInstance = this;
        var contents = jQuery('#saveLayout').find('.blockSortable');
        var table = contents.find('.editFields');
        contents.sortable({
            'containment': contents,
            'items': table,
            'revert': false,
            'tolerance': 'pointer',
            'cursor': 'move'
        });
        contents.disableSelection();
    },
    showVteColumnTooltip : function(obj,html){
        //var target_on_quick_form = jQuery("#QuickCreate").find(obj);
        var template = '<div class="popover" role="tooltip" style="background: #003366">' +
            '<style>' +
            '.popover.bottom > .arrow:after{border-bottom-color:red;2px solid #ddd}' +
            '.popover-content{font-size: 11px}' +
            '.popover-title{background: red;text-align:center;color:#f4f12e;font-weight: bold;}' +
            '.popover-content ul{padding: 5px 5px 0 10px}' +
            '.popover-content li{list-style-type: none}' +
            '.popover{border: 2px solid #ddd;z-index:99999999;color: #fff;box-shadow: 0 0 6px #000; -moz-box-shadow: 0 0 6px #000;-webkit-box-shadow: 0 0 6px #000; -o-box-shadow: 0 0 6px #000;padding: 4px 10px 4px 10px;border-radius: 6px; -moz-border-radius: 6px; -webkit-border-radius: 6px; -o-border-radius: 6px;}' +
            '</style><div class="arrow">' +
            '</div>' +
            '<div class="popover-content"></div></div>';
        obj.popover({
            content: html,
            animation : true,
            placement: 'auto top',
            html: true,
            template:template,
            container: 'body',
            trigger: 'focus'
        });
        jQuery(obj).popover('show');
        jQuery('.popover').on('mouseleave',function () {
            jQuery(obj).popover('hide');
        });
    }
});
jQuery(document).ready(function(){
    var instance = new VTEColumns_Js();
    var top_url = window.location.href.split('?');
    if(top_url.length == 0) return false;
    var array_url = instance.getQueryParams(top_url[1]);
    var vtetabs = jQuery("script[src*='VTETabs.js']");
    if(typeof array_url == 'undefined') return false;

    if(array_url.module =='LayoutEditor' && typeof array_url._pjax  == 'undefined') {
        instance.registerAddColumnsEvent();
        instance.registerCustomLayoutClick();
        instance.registerSelectNumberColumnChange();
        instance.registerShowTooltip();
    }

    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTEColumns')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208
    
    if(array_url.view == "Detail"){
        if (vtetabs.length == 0){
            var summaryViewEntries = jQuery('.summaryView');
            if(summaryViewEntries.length === 0) instance.registerShowViewAsColumnsEvent('Detail');
        } else {
            if (typeof VTECheckLoadHeaderScript == 'function') {
                if (!VTECheckLoadHeaderScript('VTETabs')) {
                    instance.registerShowViewAsColumnsEvent('Detail');
                }
            }
        }
    }
    
    if(array_url.view == "Edit"){
        if (vtetabs.length == 0){
            instance.registerShowViewAsColumnsEvent("Edit");
        } else {
            if (typeof VTECheckLoadHeaderScript == 'function') {
                if (!VTECheckLoadHeaderScript('VTETabs')) {
                    instance.registerShowViewAsColumnsEvent("Edit");
                }
            }
        }
    }

    jQuery( document ).ajaxComplete(function(event, xhr, settings) {
        var url = settings.data;
        if(typeof url == 'undefined' && settings.url) url = settings.url;
        if(typeof array_url == 'undefined') return false;
        var other_url = instance.getQueryParams(url);

        if((other_url._pjax || other_url.displayMode == 'overlay') && vtetabs.length == 0) {
            instance.registerShowViewAsColumnsEvent('Detail');
        }
        if(other_url.view == 'Edit' && other_url.returnmode == 'showRelatedList' && other_url.displayMode == 'overlay') {
            instance.registerShowViewAsColumnsEvent('Edit');
        }
        //Working with VTETabs
        if(other_url.module == 'VTETabs' && other_url.view == 'Edit' && other_url.mode == 'showModuleEditView') {
            instance.registerShowViewAsColumnsEvent('Edit');
        }
        if(other_url.module == 'VTETabs' && other_url.view == 'DetailViewAjax' && other_url.mode == 'showModuleDetailView') {
            instance.registerShowViewAsColumnsEvent('Detail');
        }
        if(other_url.module =='LayoutEditor' && other_url._pjax) {
            instance.registerAddColumnsEvent();
        }
    });
});


