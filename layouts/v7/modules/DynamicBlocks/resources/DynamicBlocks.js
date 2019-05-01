/* ********************************************************************************
 * The content of this file is subject to the Dynamic Blocks ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("DynamicBlocks_Js",{
    editInstance:false,
    getInstance: function(){
        if(DynamicBlocks_Js.editInstance == false){
            var instance = new DynamicBlocks_Js();
            DynamicBlocks_Js.editInstance = instance;
            return instance;
        }
        return DynamicBlocks_Js.editInstance;
    },
    doDynamicBlocks: function() {
        var recordId ='';
        var frm = jQuery("#detailView");
        var fieldBlockHeader = ".fieldBlockHeader";
        var fieldBlockContainer = ".fieldBlockContainer";
        var view = app.view();

        // Only load when view is Detail or Edit
        if(view!='Detail' && view!='Edit') return;

        if (frm.length == 0){
            frm = jQuery("#EditView");
            recordId = frm.find('[name="record"]').val();
        } else {
            recordId = jQuery(document).find('#recordId').val();
            fieldBlockHeader = ".textOverflowEllipsis";
            fieldBlockContainer = ".block";
        }
        var module = _META.module;
        var url ='index.php?module=DynamicBlocks&action=ActionAjax&mode=getModuleConfigBlock';
        var actionParams = {
            "type":"POST",
            "url":url,
            "dataType":"json",
            "data" : {
                'source_module':module,
                'source_record':recordId
            }
        };
        if(module == 'Calendar'){
            var requestMode=app.convertUrlToDataParams(location.href).mode;
            actionParams.data.source_mode = requestMode;
        }
        app.request.post(actionParams).then(
            function(err,data){
                if(err === null) {
                    var configBlocks=data;
                    if(configBlocks != null && jQuery.isEmptyObject(configBlocks)==false && configBlocks!='' && typeof (configBlocks.result) == 'undefined') {
                        jQuery.each(configBlocks, function(field, configs) {
                            var fieldElm= jQuery(document).find('[name*="'+field+'"]');
                            if(fieldElm.length > 1 || (typeof fieldElm.attr('multiple') !== 'undefined' && fieldElm.attr('multiple') !== false)) {
                                var selectFld = field+'[]';
                                jQuery(document).on('change','[name="'+selectFld+'"]', function(e) {
                                    DynamicBlocks_Js.showHideBlocks(configBlocks, fieldBlockHeader, fieldBlockContainer);
                                });
                                jQuery(document).find('[name="'+selectFld+'"]').trigger("change");
                            }else{
                                jQuery(document).on('change','[name="'+field+'"]', function(e) {
                                    DynamicBlocks_Js.showHideBlocks(configBlocks, fieldBlockHeader, fieldBlockContainer);
                                });
                                jQuery(document).find('[name="'+field+'"]').trigger("change");
                            }
                            // For detail
                            DynamicBlocks_Js.showHideBlocksInDetail(module,configBlocks, fieldBlockHeader, fieldBlockContainer);
                        });
                    }
                    // to do
                }else{
                    // to do
                }
            }
        );
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
    showHideBlocks : function (configBlocks, fieldBlockHeader, fieldBlockContainer) {
        // Set block data-show
        jQuery.each(configBlocks, function(field, configs) {
            var fieldElm= jQuery(document).find('[name*="'+field+'"]');
            jQuery.each(configs, function(values,config) {
                var found = false;
                values = jQuery('<span>').html(values).html();
                var picklist_values=values.split("|##|");
                var fieldValue = fieldElm.val();
                if(fieldValue !== null && fieldValue !='') {
                    if (fieldElm.length > 1 || (typeof fieldElm.attr('multiple') !== 'undefined' && fieldElm.attr('multiple') !== false)) {
                        jQuery.each(fieldValue, function (i, v) {
                            if (jQuery.inArray(v, picklist_values) !== -1) {
                                found = true;
                            }
                        });
                    } else {
                        if (jQuery.inArray(fieldValue, picklist_values) !== -1) {
                            found = true;
                        }
                    }
                }else{
                    if (jQuery.inArray('__blank__', picklist_values) !== -1) {
                        found = true;
                    }
                }
                if(found) {
                    jQuery.each(config, function (blocklbl, show) {
                        blocklbl = blocklbl.replace('Related List','');
                        blocklbl = blocklbl.replace(':','');
                        blocklbl = blocklbl.trim();
                        var blockHeader = jQuery(document).find(fieldBlockHeader + ':contains("'+blocklbl+'")');
                        jQuery.each(blockHeader, function(i,e) {
                            if(jQuery(e).text().trim() == blocklbl.trim()) {
                                var table = jQuery(e).closest(fieldBlockContainer);
                                if(show != 2) {
                                    table.attr('data-show', show);
                                }
                            }
                        });
                    });
                }
            });
        });

        // show/hide block based on data-show
        jQuery(document).find('div'+fieldBlockContainer).each(function (idx, el) {
            var table = jQuery(el);
            var dataShow=table.attr('data-show');
            if(typeof dataShow != 'undefined') {
                if(dataShow == '1') {
                    table.show();
                    // table.next().show();
                }else if(dataShow == '0') {
                    table.hide();
                    // table.next().hide();
                }
            }
        })
    },
    showHideBlocksInDetail : function (module, configBlocks, fieldBlockHeader, fieldBlockContainer) {
        // Set block data-show
        jQuery.each(configBlocks, function(field, configs) {
            jQuery.each(configs, function(values,config) {
                var found = false;
                values = jQuery('<span>').html(values).html();
                var picklist_values=values.split("|##|");
                var fieldValue = '';
                if(module == 'Calendar') {
                    var fieldElm= jQuery(document).find('[id$="detailView_fieldValue_'+field+'"]');
                    fieldValue=jQuery(document).find('[id$="detailView_fieldValue_'+field+'"]').text();
                }else{
                    var fieldElm= jQuery(document).find('[name*="'+field+'"]');
                    if(fieldElm.length > 1 || (typeof fieldElm.attr('multiple') !== 'undefined' && fieldElm.attr('multiple') !== false)){
                        fieldValue=jQuery(document).find('[name="'+field+'[]"]').val();
                    }else if(fieldElm.length >0) {
                        fieldValue=jQuery(document).find('[name="'+field+'"]').val();
                    }
                    else{
                        var fieldElm = jQuery(document).find('[id$="detailView_fieldValue_'+field+'"]');
                        fieldValue=jQuery(document).find('[id$="detailView_fieldValue_'+field+'"]').text();
                    }
                }
                fieldValue=jQuery.trim(fieldValue);
                if(fieldValue !== null && fieldValue !='') {
                    if(jQuery.isArray(fieldValue)) {
                        jQuery.each(fieldValue, function (i, v) {
                            if (jQuery.inArray(v, picklist_values) !== -1) {
                                found = true;
                            }
                        });
                    } else {
                        if (jQuery.inArray(fieldValue, picklist_values) !== -1) {
                            found = true;
                        }
                    }
                }else{
                    if (jQuery.inArray('__blank__', picklist_values) !== -1) {
                        found = true;
                    }
                }
                if(found) {
                    jQuery.each(config, function (blocklbl, show) {
                        blocklbl = blocklbl.replace('Related List','');
                        blocklbl = blocklbl.replace(':','');
                        blocklbl = blocklbl.trim();
                        var blockHeader = jQuery(document).find(fieldBlockHeader + ':contains("'+blocklbl+'")');
                        jQuery.each(blockHeader, function(i,e) {
                            if(jQuery(e).text().trim() == blocklbl.trim()) {
                                var table = jQuery(e).closest(fieldBlockContainer);
                                var dataShow=table.attr('data-show');
                                var addDataShow = true;
                                if(typeof dataShow != 'undefined' && dataShow == '0') {
                                    addDataShow =false;
                                }
                                if(show != 2) {
                                    if(addDataShow){
                                        table.attr('data-show', show);
                                    }else if(dataShow == 0 && show == 1 && addDataShow == false){
                                        table.attr('data-show', show);
                                    }
                                }
                            }
                        });
                    });
                }
            });
        });

        // show/hide block based on data-show
        jQuery(document).find('div'+fieldBlockContainer).each(function (idx, el) {
            var table = jQuery(el);
            var dataShow=table.attr('data-show');
            if(typeof dataShow != 'undefined') {
                if(dataShow == '1') {
                    table.show();
                    // table.next().show();
                }else if(dataShow == '0') {
                    table.hide();
                    // table.next().hide();
                }
            }
        })
    },
},{
    registerEvent : function () {
        app.event.on("post.relatedListLoad.click", function() {
            DynamicBlocks_Js.doDynamicBlocks();
        });
    },
});

jQuery(document).ready(function() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('DynamicBlocks')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    DynamicBlocks_Js.doDynamicBlocks();
    var instance= new DynamicBlocks_Js();
    instance.registerEvent();
    jQuery( document ).ajaxComplete(function(event, xhr, settings) {
        // Only load when loadHeaderScript=1 BEGIN #241208
        if (typeof VTECheckLoadHeaderScript == 'function') {
            if (!VTECheckLoadHeaderScript('DynamicBlocks')) {
                return;
            }
        }
        // Only load when loadHeaderScript=1 END #241208

        var url = settings.data;
        if(typeof url == 'undefined' && settings.url) url = settings.url;
        var other_url = DynamicBlocks_Js.getQueryParams(url);
        if(other_url.module == 'RelatedBlocksLists' && other_url.view == 'MassActionAjax') {
            setTimeout(function () {
                DynamicBlocks_Js.doDynamicBlocks();
            }, 500);
        }
    });
});