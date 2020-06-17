jQuery.browser = {};
(function () {
    jQuery.browser.msie = false;
    jQuery.browser.version = 0;
    if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
        jQuery.browser.msie = true;
        jQuery.browser.version = RegExp.$1;
    }
})();

jQuery(document).ready(function() {
    setTimeout(function () {
        initData_TooltipManager();
    }, 3000);
});
function initData_TooltipManager() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('TooltipManager')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    //add stylesheet
    //jQuery('head').append('<link rel="stylesheet" href="modules/DuplicateCheckMerge/css/style.css" type="text/css" media="screen" />');
    // ================ Handle EditView to pre-popular data =============================== //
    var current_url = jQuery.url();
    var current_module = current_url.param('module');
    var current_view = current_url.param('view');
    var current_requestMode = current_url.param('requestMode');
    if(current_module == 'Calendar'){
        current_module = current_url.param('mode');
    }

    showToolTips(current_view,current_requestMode);
    window.init_ttm_ajax = 0;

    jQuery('.quickCreateModule').click(function(){
        function refresh() {
            if(jQuery('.quickCreateContent').length > 0) {
                showToolTips('quickCreate',null,jQuery('.quickCreateContent').parent().find('input[name=module]').val());
            } else {
                setTimeout(function(){
                    refresh();
                }, 50);
            }
        }
        refresh();
    });

    jQuery( document ).ajaxComplete(function(a,b,c) {
        var ajax_url = c.url;
        if(ajax_url.indexOf('view=Detail')>-1 && ajax_url.indexOf('mode=showDetailViewByMode')>-1 && ajax_url.indexOf('http') == -1) {
                //setTimeout(function(){showToolTips('Detail',ajax_url.indexOf('requestMode=summary')>-1 ? 'summary' : 'full')}, 2000);
            function refresh() {
                if(jQuery('#detailView').length > 0) {
                    showToolTips('Detail',ajax_url.indexOf('requestMode=summary')>-1 ? 'summary' : 'full')
                } else {
                    setTimeout(function(){
                        refresh();
                    }, 50);
                }
            }
            refresh();
        }
    });

    function showToolTips(currentview,requestMode,for_module) {
        if(currentview=='Detail' || currentview == 'Edit' || currentview == 'quickCreate'){
            if(currentview=='quickCreate')
                var currentmodule = for_module;
            else
                var currentmodule = current_module;
            jQuery.ajax({
                type: "POST",
                url: "index.php",
                data: {
                    module: "TooltipManager",
                    action: "getFieldTooltipInfo",
                    pmodule: currentmodule,
                    pview: currentview
                },
                success:function (response) {
                    if(response.result.success){
                        var data = response.result.data;
                        for(var i = 0; i<data.length; i++){
							if(data[i].helpinfo==null || data[i].helpinfo=='' ) continue; // haph86@gmail.com - #4502 - 01132014
                            data_decoded = jQuery('<div/>').html(data[i].helpinfo).text();
                            if(currentview == 'Detail') {
                                var img = '<img class="ttm_icon" id="qtip_'+data[i].fieldid+'" src="'+data[i].icon+'" width="16px;" height="16px;" style="vertical-align: middle; margin-right:5px; float:left;cursor: pointer;">';

                                // haph86@gmail.com - #5455 - 01202014
                                if(requestMode=='summary') {
                                    jQuery('.summaryView').find('[data-name="'+data[i].fieldname+'"]').closest('tr.summaryViewEntries').find('td.fieldLabel > label').prepend(img);
                                    // haph86@gmail.com - #17635 - 10192015
                                    jQuery('.muted').css('padding-left','32px');
                                    if(data[i].preview_type== 1){
                                        jQuery('#qtip_'+data[i].fieldid).attr("i", i).off("click").on("click", function(){
                                            var text_decoded = jQuery('<div/>').html(data[jQuery(this).attr("i")].helpinfo).text();
                                            // haph86@gmail.com - #17635 - 10142015
                                            app.helper.showPopup('<div class="modal-dialog modal-lg" style="width: 600px;"><div class="modal-content"><div class="modal-body"><div>'+ text_decoded +'</div></div></div></div>');

                                        });
                                    }
                                    else

                                        jQuery('#qtip_'+data[i].fieldid).qtip(
                                            {
                                                content: data_decoded,
                                                show: { when: { event: 'click' } },
                                                style: {
                                                    width:"auto",
                                                    background: '#E5F6FE',
                                                    border: {
                                                        width: 2,
                                                        radius: 1,
                                                        color: '#dddddd'
                                                    },
                                                    tip: 'topLeft'
                                                } // haph86@gmail.com - #17635

                                                , hide: {when: {event: "unfocus"}}

                                            });
                                } else {
                                    var default_view = response.result.default_record_view;
                                    if(requestMode==undefined) { // haph86@gmail.com - #22901 - 11202015
                                        if(default_view=="summary") {
                                            var summmaryFields = jQuery('.summaryView').find('[data-name="'+data[i].fieldname+'"]').closest('tr.summaryViewEntries').find('td.fieldLabel > label');
                                            var detailFields = jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span');
                                            if(summmaryFields.length == 0 && detailFields.length == 1){
                                                detailFields.prepend(img);
                                            }else{
                                                summmaryFields.prepend(img);
                                            }
                                            // jQuery('.muted').css('padding-left','32px');
                                        } else {
                                            jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span').prepend(img);
                                        }
                                    } else {
                                        jQuery('#'+current_module+'_detailView_fieldLabel_'+data[i].fieldname+' span').prepend(img);
                                    }

                                    //jQuery('#_detailView_fieldLabel_'+data[i].fieldname+' label').prepend(img);

                                    if(data[i].preview_type== 1){
                                        jQuery('#qtip_'+data[i].fieldid).attr("i", i).off("click").on("click", function(){
                                            var text_decoded = jQuery('<div/>').html(data[jQuery(this).attr("i")].helpinfo).text();
                                            // haph86@gmail.com - #17635 - 10142015
                                            app.helper.showPopup('<div class="modal-dialog modal-lg" style="width: 600px;"><div class="modal-content"><div class="modal-body"><div>'+ text_decoded +'</div></div></div></div>');

                                        });
                                    }
                                    else

                                        jQuery('#qtip_'+data[i].fieldid).qtip(
                                            {
                                                content: data_decoded,
                                                show: { when: { event: 'click' } },
                                                style: {
                                                    width:"auto",
                                                    background: '#E5F6FE',
                                                    border: {
                                                        width: 2,
                                                        radius: 1,
                                                        color: '#dddddd'
                                                    },
                                                    tip: 'topLeft'
                                                }// haph86@gmail.com - #17635
                                               /* ,
                                                position: {
                                                    corner: {
                                                        target: 'topRight',
                                                        tooltip: 'bottomLeft'
                                                    }
                                                }*/
                                                , hide: {when: {event: "unfocus"}}

                                            });
                                }


                            } else if(currentview == 'Edit') {
                                var img = '<img id="qtip_'+data[i].fieldid+'" src="'+data[i].icon+'" width="16px;" height="16px;" style="vertical-align: middle; margin-right:5px;">';
//                            jQuery('#'+current_module+'_editView_fieldName_'+data[i].fieldname).closest('.fieldValue').prev('td').find('label').prepend(img);
                                jQuery('textarea[name="'+data[i].fieldname+'"]').closest('.fieldValue').prev('div').prepend(img);
                                jQuery('input[name="'+data[i].fieldname+'"]').closest('.fieldValue').prev('div').prepend(img);
                                jQuery('select[name="'+data[i].fieldname+'"]').closest('.fieldValue').prev('div').prepend(img);
                                if(data[i].preview_type== 1){
                                    jQuery('#qtip_'+data[i].fieldid).attr("i", i).off("click").on("click", function(){
                                        var text_decoded = jQuery('<div/>').html(data[jQuery(this).attr("i")].helpinfo).text();
                                        // haph86@gmail.com - #17635 - 10142015
                                        app.helper.showPopup('<div class="modal-dialog modal-lg" style="width: 600px;"><div class="modal-content"><div class="modal-body"><div>'+ text_decoded +'</div></div></div></div>');

                                    });
                                }
                                else

                                jQuery('#qtip_'+data[i].fieldid).qtip(
                                    {
                                        content: data_decoded,
                                        show: { when: { event: 'click' } },
                                        style: {
                                            width:"auto",
                                            background: '#E5F6FE',
                                            border: {
                                                width: 2,
                                                radius: 1,
                                                color: '#dddddd'
                                            },
                                            tip: 'topLeft'
                                        }// haph86@gmail.com - #17635

                                        , hide: {when: {event: "unfocus"}}

                                    });
                            } else {
                                var img = '<img id="qtip2_'+data[i].fieldid+'" src="'+data[i].icon+'" width="16px;" height="16px;" style="vertical-align: middle; margin-right:5px;">';
                                jQuery('form[name="QuickCreate"] textarea[name="'+data[i].fieldname+'"]').closest('.fieldValue').prev('td').find('label').prepend(img);
                                jQuery('form[name="QuickCreate"] input[name="'+data[i].fieldname+'"]').closest('.fieldValue').prev('td').find('label').prepend(img);
                                jQuery('form[name="QuickCreate"] select[name="'+data[i].fieldname+'"]').closest('.fieldValue').prev('td').find('label').prepend(img);

                                if(data[i].preview_type== 1){
                                    jQuery('#qtip2_'+data[i].fieldid).attr("i", i).off("click").on("click", function(){
                                        var text_decoded = jQuery('<div/>').html(data[jQuery(this).attr("i")].helpinfo).text();
                                        // haph86@gmail.com - #17635 - 10142015
                                        app.helper.showPopup('<div class="modal-dialog modal-lg" style="width: 600px;"><div class="modal-content"><div class="modal-body"><div>'+ text_decoded +'</div></div></div></div>');

                                    });
                                }
                                else {

                                    jQuery('#qtip2_' + data[i].fieldid).qtip(
                                        {
                                            content: data_decoded,
                                            show: {when: {event: 'click'}},
                                            style: {
                                                width: "auto",
                                                background: '#E5F6FE',
                                                border: {
                                                    width: 2,
                                                    radius: 1,
                                                    color: '#dddddd'
                                                },
                                                tip: 'topLeft'
                                            }// haph86@gmail.com - #17635
                                            , hide: {when: {event: "unfocus"}}

                                        });
                                }
                            }
                        }
                    }
                }
            });
        }
    }
}



