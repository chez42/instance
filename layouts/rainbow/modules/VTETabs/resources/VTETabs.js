/* ********************************************************************************
 * The content of this file is subject to the Tabs ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTETabs_Js",{

},{
    registerAddTabsEvent: function () {
        jQuery('#moduleBlocks .editFieldsTable').each(function() {
            var block_id = jQuery(this).data('block-id');
            var first_child_div = jQuery(this).find('.blockActions');
            if(first_child_div.find("input[name='is_tab']").length == 0){
                var actionParams = {
                    module:"VTETabs",
                    view:"TabButton",
                    mode:"showTabButton",
                    block_id:block_id
                };
                app.request.post({data:actionParams}).then(
                    function(err,data) {
                        if(err == null && data) {
                            first_child_div.before(data);
                            jQuery("input[name='is_tab']").bootstrapSwitch();
                        }
                    }
                );
            }

        });
        this.registerCombineTabClick();
    },
    registerEventForConvertToTab: function () {
        var listViewContainer = jQuery('#moduleBlocks');
        jQuery(listViewContainer).on('switchChange.bootstrapSwitch', "input[name='is_tab']", function (e) {
            var currentElement = jQuery(e.currentTarget);
            var is_tab = currentElement.val();
            var module_name = jQuery("select[name='layoutEditorModules']").val();
            var params = {
                module : "VTETabs",
                parent : app.getParentModuleName(),
                'action' : 'ActionAjax',
                'mode' : 'switchToTab',
                'block_id' : currentElement.data('block-id'),
                'module_name' : module_name,
                'is_tab' : is_tab
            }
            app.helper.showProgress('');
            app.request.post({
                data:params
            }).then(function(error,data){
                if(data){
                    app.helper.hideProgress();
                    if(data.switch){
                        app.helper.showSuccessNotification({
                            message : data.massage
                        });
                    }
                    else{
                        var params = {
                            title: data.massage,
                            type: 'error'
                        };
                        Vtiger_Helper_Js.showPnotify(params);
                        return false;
                    }
                }
            });
        });
    },
    registerBlockAnimationEvent : function(){
        var detailContentsHolder = jQuery(".tab-content");
        detailContentsHolder.on('click','.cusBlockToggle',function(e){
            var currentTarget =  jQuery(e.currentTarget);
            var blockId = currentTarget.data('id');
            var closestBlock = currentTarget.closest('div');
            var bodyContents = jQuery('.blockContent'+blockId + ' table tbody');
            var data = currentTarget.data();
            var module = app.getModuleName();
            var hideHandler = function() {
                bodyContents.hide('slow');
                app.storage.set(module+'.'+blockId, 0);
            }
            var showHandler = function() {
                bodyContents.removeClass('hide').show();
                app.storage.set(module+'.'+blockId, 1);
            }
            if(data.mode == 'show'){
                hideHandler();
                currentTarget.hide();
                closestBlock.find("[data-mode='hide']").removeClass('hide').show();
            }else{
                showHandler();
                currentTarget.hide();
                closestBlock.find("[data-mode='show']").removeClass('hide').show();
            }
        });

    },
    registerShowViewAsTabsEvent: function (overlay) {
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
            var record = app.getRecordId();
            var actionParams = {
                module:"VTETabs",
                view:"DetailViewAjax",
                mode:"showModuleDetailView",
                related_module_name:moduleName,
                record:record
            };
            //app.helper.showProgress(app.vtranslate("Switching to tabs view"));
            app.request.post({data:actionParams}).then(
                function(err,data) {
                    if(err == null && data) {
                        //app.helper.hideProgress();
                        data = data.trim();
                        if(data !== "NO_TAB"){
                            jQuery("form#detailView .details").remove();
                            jQuery("form#detailView .block").remove();
                            jQuery("form#detailView br").remove();
                            jQuery("form#detailView").append(data);
                            thisInstance.registerClickToTab();
                            thisInstance.registerBlockAnimationEvent();
                        }
                    }
                }
            );
        }
        if(view == "Edit"){
            var record = jQuery('[name="record"]').val();
            var actionParams = {
                module:"VTETabs",
                view:"Edit",
                mode:"showModuleEditView",
                related_module_name:moduleName,
                record:record
            };
            //app.helper.showProgress(app.vtranslate("Switching to tabs view"));
            app.request.post({data:actionParams}).then(
                function(err,data) {
                    if(err == null && data) {
                        //app.helper.hideProgress();
                        data = data.trim();
                        if(data !== "NO_TAB"){
                            var form = jQuery("#EditView");
                            form.find("[name='editContent']:first").html(data);
                            var Edit_Js = new Vtiger_Edit_Js();
                            Edit_Js.registerEventForPicklistDependencySetup(form);
                            Edit_Js.registerFileElementChangeEvent(form);
                            Edit_Js.registerAutoCompleteFields(form);
                            Edit_Js.registerClearReferenceSelectionEvent(form);
                            //Edit_Js.registerReferenceCreate(form);
                            Edit_Js.referenceModulePopupRegisterEvent(form);
                            Edit_Js.registerPostReferenceEvent(Edit_Js.getEditViewContainer());
                            Edit_Js.registerEventForImageDelete();
                            Edit_Js.registerImageChangeEvent();
                            vtUtils.applyFieldElementsView(form);
                            thisInstance.registerClickToTab();
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
        }
    },
    registerClickToTab:function(){
        jQuery('.tabs .tab-links a').on('click', function(e) {
            var currentAttrValue = jQuery(this).attr('href');
            // Show/Hide Tabs
            jQuery(currentAttrValue).show().siblings('.fieldBlockContainer').hide();
            jQuery(currentAttrValue).show().siblings('.block').hide();
            // Change/remove current tab to active
            jQuery(this).parent('li').addClass('active').siblings().removeClass('active');
            e.preventDefault();
        });
        jQuery('.tab-links li:first a').trigger("click");
    },
    getQueryParams:function(qs) {
        if(typeof(qs) != 'undefined' ){
            qs = qs.toString().split('+').join(' ');
            var params = {},
                tokens,
                re = /[?&]?([^=]+)=([^&]*)/g;
            while (tokens = re.exec(qs)) {
                params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
            }
            return params;
        }
    },
    registerCombineTabClick:function(){
        var thisInstance = this;
        jQuery(document).on("click", ".combine_tab", function() {
            var url = jQuery(this).data("url");
            thisInstance.combineTab(url);
        });
    },
    combineTab: function(url) {
    var thisInstance = this;
    var actionParams = {
        url: url,
        async: false
    };
    app.request.post(actionParams).then(
        function (err,data) {
            if(err === null) {
                var callBackFunction = function () {
                    thisInstance.combineTabSubmit();
                };
                var params = {};
                params.cb = callBackFunction;
                app.helper.showModal(data, params);
            }
        }
    );
    },
    combineTabSubmit : function (){
        var instance = this;
        jQuery('#btnSaveCombine').on("click",function(e) {
            var block_id = jQuery("#block_id").val();
            var tab_name = jQuery("#tab_name").val();
            var module_name = jQuery("select[name='layoutEditorModules']").val();
            var actionParams = {
                url: "index.php?module=VTETabs&action=ActionAjax&mode=combineTab",
                data: {'block_id': block_id,'tab_name':tab_name,'module_name':module_name}
            };
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if(data.combine){
                            var parent_tab_id = data.parent_tab_id;
                            jQuery("#is_tab_"+block_id).bootstrapSwitch('state', true,true);
                            if(tab_name !=''){
                                jQuery("#combine_tab_"+block_id).attr('title',app.vtranslate('This block is child of ' + tab_name));
                            }
                            app.helper.hideModal();
                        }
                        else{
                            var message = data.message;
                            var params = {
                                title: message,
                                type: 'error'
                            };
                            Vtiger_Helper_Js.showPnotify(params);
                            jQuery("#tab_name").focus();
                            return false;
                        }
                    }
                }
            );
        });
    },
    registerShowTooltip:function(){
        var thisInstance = this;
        jQuery(document).on("hover", ".vtetab-tooltip", function() {
            var html =      'To Convert ONE Block into ONE Tab - just turn the block on (set to YES).'
                +'</br></br>'
                +'To Convert MULTIPLE Blocks into ONE Tab - you will need to turn the block on (set to yes) and click on the "Gear" icon and follow instructions.';
            thisInstance.showVteColumnTooltip(jQuery(this),html);
        });
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
    },
    registerEvents: function() {
        this.registerAddTabsEvent();
        this.registerEventForConvertToTab();
        this.registerShowViewAsTabsEvent();
    }
});

jQuery(document).ready(function(){
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTETabs')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var instance = new VTETabs_Js();
    var top_url = window.location.href.split('?');
    var array_url = instance.getQueryParams(top_url[1]);
    if(typeof array_url == 'undefined') return false;
     if((array_url.view == "Detail" && array_url.mode == 'showDetailViewByMode' && array_url.requestMode == 'full') || (array_url.view == "Detail" && typeof  array_url.mode == "undefined") ){
        var summaryViewEntries = jQuery('.summaryView');
        if(summaryViewEntries.length === 0) instance.registerShowViewAsTabsEvent('Detail');
    }
    if(array_url.view == "Edit"){
        instance.registerShowViewAsTabsEvent("Edit");
    }
    if(array_url.module =='LayoutEditor') {
        //instance.registerAddTabsEvent();
        //instance.registerEventForConvertToTab();
        instance.registerShowTooltip();
    }
    jQuery( document ).ajaxComplete(function(event, xhr, settings) {
        var url = settings.data;
        if(typeof url == 'undefined' && settings.url) url = settings.url;
        if(typeof array_url == 'undefined') return false;
        var other_url = instance.getQueryParams(url);
        if(other_url.view == 'Detail' && other_url.mode == 'showDetailViewByMode' && other_url.requestMode == 'full' && other_url._pjax == '#pjaxContainer') {
            instance.registerShowViewAsTabsEvent('Detail');
        }
        if(other_url.view == 'Edit' && other_url.returnmode == 'showRelatedList' && other_url.displayMode == 'overlay') {
            instance.registerShowViewAsTabsEvent('Edit');
        }
        if((other_url.parent == "Settings" && other_url.view =="Index" && other_url._pjax ==  "#pjaxContainer") || (other_url.module == "LayoutEditor" && other_url.action == "Block" && other_url.mode == "save")) {
            instance.registerAddTabsEvent();
            instance.registerEventForConvertToTab();
            //location.reload();
        }
    });
});


