/* ********************************************************************************
 * The content of this file is subject to the Dynamic Blocks ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
jQuery.Class("DynamicBlocks_Js",{
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
        //var module=jQuery(document).find('input[name="module"]').val();
        var recordId ='';
        var form = jQuery(document).find('form');
        if(app.getViewName() == 'Edit') {
             form = jQuery(document).find('form#EditView');
            recordId=form.find('[name="record"]').val();
        }else if(app.getViewName() == 'Detail') {
            form = jQuery(document).find('form#detailView');
            recordId=jQuery(document).find('#recordId').val();
        }

        var module=form.find('input[name="module"]').val();
        if(typeof module == 'undefined') {
            module=jQuery(document).find('input[name="module"]').val();
        }

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
        AppConnector.request(actionParams).then(
            function(data) {
                var configBlocks=data.result;
                if(configBlocks != null) {
                    jQuery.each(configBlocks, function(field, configs) {
                        var fieldElm= jQuery(document).find('[name*="'+field+'"]');
                        if(fieldElm.length > 1 || (typeof fieldElm.attr('multiple') !== 'undefined' && fieldElm.attr('multiple') !== false)) {
                            var selectFld = field+'[]';
                            jQuery(document).on('change','[name="'+selectFld+'"]', function(e) {
                                DynamicBlocks_Js.showHideBlocks(configBlocks);
                                DynamicBlocks_Js.showHideBlocksInDetail(module,configBlocks);
                            });
                            jQuery(document).find('[name="'+selectFld+'"]').trigger("change");
                        }else{
                            jQuery(document).on('change','[name="'+field+'"]', function(e) {
                                DynamicBlocks_Js.showHideBlocks(configBlocks);
                                DynamicBlocks_Js.showHideBlocksInDetail(module,configBlocks);
                            });
                            jQuery(document).find('[name="'+field+'"]').trigger("change");
                        }
                        DynamicBlocks_Js.showHideBlocksInDetail(module,configBlocks);
                    });
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
        jQuery(document).find('.blockHeader').closest('table').attr('data-show', '');
        // jQuery(document).find('.blockHeader').closest('table').css('display', 'block');
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

                        var blockHeader = jQuery(document).find('.blockHeader:contains("'+blocklbl+'")');
                        jQuery.each(blockHeader, function(i,e) {
                            var RBLHeader= '';
                            if(blockHeader.find(".RBLHeader").length >0) {
                                RBLHeader = blockHeader.find(".RBLHeader").text();
                            }
                            if(jQuery(e).text().trim() == blocklbl.trim()|| RBLHeader.trim() == blocklbl.trim()) {
                                var table = jQuery(e).closest('table');
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
        jQuery(document).find('table').each(function (idx, el) {
            var table = jQuery(el);
            var dataShow=table.attr('data-show');
            if(typeof dataShow != 'undefined') {
                if(dataShow == '1') {
                    table.show();
                    table.next().show();
                }else if(dataShow == '0') {
                    table.hide();
                    table.next().hide();
                    jQuery('.mainContainer').css("min-height", "500px");
                    jQuery('#leftPanel').css("min-height", "500px");
                    jQuery('#rightPanel').css("min-height", "500px");
                }
            }
        })
    },
    showHideBlocksInDetail : function (module, configBlocks, fieldBlockHeader, fieldBlockContainer) {
        jQuery(document).find('.blockHeader').closest('table').attr('data-show', '');
        // jQuery(document).find('.blockHeader').closest('table').css('display', 'block');
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
                        var blockHeader = jQuery(document).find('.blockHeader:contains("'+blocklbl+'")');
                        jQuery.each(blockHeader, function(i,e) {
                            var RBLHeader= '';
                            if(blockHeader.find(".RBLHeader").length >0) {
                                RBLHeader = blockHeader.find(".RBLHeader").text();
                            }
                            if(jQuery(e).text().trim() == blocklbl.trim() || RBLHeader.trim() == blocklbl.trim()) {
                                var table = jQuery(e).closest('table');
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
        jQuery(document).find('table').each(function (idx, el) {
            var table = jQuery(el);
            var dataShow=table.attr('data-show');
            if(typeof dataShow != 'undefined') {
                if(dataShow == '1') {
                    table.show();
                    table.next().show();
                }else if(dataShow == '0') {
                    table.hide();
                    table.next().hide();
                    jQuery('.mainContainer').css("min-height", "500px");
                    jQuery('#leftPanel').css("min-height", "500px");
                    jQuery('#rightPanel').css("min-height", "500px");
                }
            }
        })
    },
},{

});
jQuery(document).ready(function(){
    DynamicBlocks_Js.doDynamicBlocks();
    app.listenPostAjaxReady(function() {
        DynamicBlocks_Js.doDynamicBlocks();
    });
    jQuery( document ).ajaxComplete(function(event, xhr, settings) {
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