/* ********************************************************************************
 * The content of this file is subject to the Progressbar/Bills ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEProgressbar_Js", {
    instance: false,
    getInstance: function () {
        if (VTEProgressbar_Js.instance == false) {
            var instance = new VTEProgressbar_Js();
            VTEProgressbar_Js.instance = instance;
            return instance;
        }
        return VTEProgressbar_Js.instance;
    }
    },{
    registerShowOnDetailView:function(){
        var self = this;
        /*var params = {};
        params['module'] = 'VTEProgressbar';
        params['view'] = 'ViewProgressbar';
        params['record'] = app.getRecordId();
        params['moduleSelected'] = app.getModuleName();
        app.request.post({data:params}).then(
            function(err,data) {
                if(err == null && data!=""){
                    var detailview_header = jQuery('.detailview-header .row:first');
                    detailview_header.after(data);*/
                    $("#div_vtprogressbar").fadeIn(700);
                    var current_slide = $('.slide-wrap .onView');
                    if (current_slide.length) {
                        $('.slider-wrap').animate({
                            scrollLeft:current_slide.position().left+20
                        }, 300);
                    }
                    $('.progressbarNext').hide();
                    $('.progressbarPrev').hide();
                    $('.vteProgressBarMiddleContainer').scroll(function () {
                        var $elem=$(this);
                        var newScrollLeft = $elem.scrollLeft(),
                            width=$elem.width(),
                            scrollWidth=$elem.get(0).scrollWidth;
                        var offset = 15;
                        if (scrollWidth - newScrollLeft - width == offset) {
                            $('.progressbarNext').hide();
                        }
                        else{
                            $('.progressbarNext').show();
                        }
                        if (newScrollLeft === 0) {
                            $('.progressbarPrev').hide();
                        }
                        else{
                            $('.progressbarPrev').show();
                        }
                    });
              /*   $('li.active').trigger('click');
                }
            },
            function(error) {
            }
        );*/

    },
    registerProgressBarHeaderClick:function(){
        $(".detailViewContainer").on("click",".vteProgressBarHeaderColumn",function () {
            if(!$(this).children('.vteProgressBarHeaderEmpty').hasClass('vteProgressBar-Active')){
                var params = {};
                var this_li = $(this);
                var fieldName = $(this).data('field-name');
                var fieldLabel = $(this).data('field-label');
                var newValue = $(this).data('value');
                params['module'] = 'VTEProgressbar';
                params['action'] = 'ActionAjax';
                params['mode'] = 'ChangeProgressBar';
                params['record'] = app.getRecordId();
                params['moduleSelected'] = app.getModuleName();
                params['fieldName'] = fieldName;
                params['fieldLabel'] = fieldLabel;
                params['newValue'] = newValue;
                app.helper.showProgress('changing status');
                app.request.post({data:params}).then(
                    function(err,data) {
                        if(err == null && data!=""){
                            app.helper.hideProgress();
                            this_li.closest('div.vteProgressBarHeaderContainer').find('.vteProgressBarHeaderEmpty').removeClass("vteProgressBar-Active");
                            this_li.find('.vteProgressBarHeaderEmpty').addClass("vteProgressBar-Active");
                            app.helper.showSuccessNotification({
                                message : app.vtranslate(data.fieldLabel+' has been updated to '+ data.value)
                            });
                            $('li.active').trigger('click');
                        }
                    },
                    function(error) {
                    }
                );
            }
        })
    },
    registerNextClick:function(){
        $(".detailViewContainer").on("click",".progressbarNext",function () {
            var leftPos = $('.slider-wrap').scrollLeft();
            $(".slider-wrap").animate({
                scrollLeft: leftPos + 200
            }, 'fast');
        });
        $(".detailViewContainer").on("click",".progressbarPrev",function () {
            var leftPos = $('.slider-wrap').scrollLeft();
            $(".slider-wrap").animate({
                scrollLeft: leftPos - 200
            }, 'fast');
        });
    },
    registerEvents: function(){
        this.registerShowOnDetailView();
        this.registerProgressBarHeaderClick();
        this.registerNextClick();
    }
});

jQuery(document).ready(function () {
	// Only load when loadHeaderScript=1 BEGIN #241208
	if (typeof VTECheckLoadHeaderScript == 'function') {
		if (!VTECheckLoadHeaderScript('VTEProgressbar')) {
			return;
		}
	}
	// Only load when loadHeaderScript=1 END #241208
	
    var moduleName = app.getModuleName();
    var viewName = app.getViewName();
    if(viewName == 'Detail'){
        var instance = new VTEProgressbar_Js();
        instance.registerEvents();
    }
});
