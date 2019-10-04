/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEForecast_Settings_Js",{

},{
    /* For License page - Begin */
    init : function() {
        this.initiate();
    },
    /*
     * Function to initiate the step 1 instance
     */
    initiate : function(){
        var step=jQuery(".installationContents").find('.step').val();
        this.initiateStep(step);
    },
    /*
     * Function to initiate all the operations for a step
     * @params step value
     */
    initiateStep : function(stepVal) {
        var step = 'step'+stepVal;
        this.activateHeader(step);
    },

    activateHeader : function(step) {
        var headersContainer = jQuery('.crumbs ');
        headersContainer.find('.active').removeClass('active');
        jQuery('#'+step,headersContainer).addClass('active');
    },

    registerActivateLicenseEvent : function() {
        var aDeferred = jQuery.Deferred();
        jQuery(".installationContents").find('[name="btnActivate"]').click(function() {
            var license_key=jQuery('#license_key');
            if(license_key.val()=='') {
                app.helper.showAlertBox({message:"License Key cannot be empty"});
                aDeferred.reject();
                return aDeferred.promise();
            }else{
                app.helper.showProgress();
                var params = {};
                params['module'] = app.getModuleName();
                params['action'] = 'Activate';
                params['mode'] = 'activate';
                params['license'] = license_key.val();

                app.request.post({data:params}).then(
                    function(err, data) {
                        if(err === null) {
                            var message=data.message;
                            if(message !='Valid License') {
                                jQuery('#error_message').html(message);
                                jQuery('#error_message').show();
                            }else{
                                document.location.href="index.php?module=VTEForecast&parent=Settings&view=Settings&mode=step3";
                            }
                            app.helper.hideProgress();
                        }
                        else {
                            app.helper.hideProgress();
                        }
                    }
                );
            }
        });
    },

    registerValidEvent: function () {
        jQuery(".installationContents").find('[name="btnFinish"]').click(function() {
            app.helper.showProgress();
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Activate';
            params['mode'] = 'valid';

            app.request.post({'data':params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if(err === null) {
                        document.location.href = "index.php?module=VTEForecast&parent=Settings&view=Settings";
                    }
                    else {
                        app.helper.hideProgress();
                    }
                }
            );
        });
    },
    /* For License page - End */

    targetYear : null,
    targetLoaded : false,
    targetInputLastOpen : null,
    targetDivLastOpen: null,
    //updatedBlockSequence : {},
    registerSettingEvent: function () {
        var thisInstance=this;		
        jQuery('ul.nav > li > a').on("click", function(e) {			
			 //alert('tét');
            jQuery('ul.nav > li').each(function(){				
				jQuery(this).removeClass('active');
				var rolePanel =  jQuery(this).attr('role');
				jQuery('#'+rolePanel).hide();
			});
			var rolePanel = jQuery(this).parent().attr('role');
			jQuery('#'+rolePanel).show();
			jQuery(this).parent().addClass('active');
            if(rolePanel==='panelTarget'){
                //Check load content
                //Not load content for target
                if(!thisInstance.targetLoaded){
                    thisInstance.loadTargetTab();
                }

            }
        });
		
		jQuery('.toolbar').hide();
		
		jQuery('.toolbar-handle').bind('mouseover', function(e){
			var target = $(e.currentTarget);
			jQuery('.toolbar', target).css({display: 'inline'});
		});
		jQuery('.toolbar-handle').bind('mouseout', function(e){
			var target = $(e.currentTarget);
			jQuery('.toolbar', target).hide();
		});
		jQuery('a.btnRemove').on('click', function(e){
			if(confirm('Are you sure?')){
				app.helper.showProgress();
				var nodeId = jQuery(this).attr('data-id');					
				// console.log(cat_id);
				var params = {
                    'id':nodeId,						
                    'module':app.getModuleName(),
                    'action':'ActionAjax',
                    'mode':'removeNode'
				};
						
                app.request.post({data:params}).then(
                    function(err, data) {				 
						if(err == null) {
							app.helper.hideProgress();
							var params = {};
							params.message = app.vtranslate('Deleted');
                            app.helper.showSuccessNotification(params);
							window.location.href = "index.php?module=VTEForecast&parent=Settings&view=Settings&tab=hierarchy";
							
						}
                        else {
                            app.helper.hideProgress();
                        }
					 }
				);
			}
		});
		
		//Configuration Tab		
		jQuery('input[type=radio][name=forecast_period]').on("change", function(e) {			
			if(this.value == 0){
				//monthly
				jQuery('#number_of_periods_month').show();
				jQuery('#number_of_periods_quarterly').hide();
                jQuery('#number_of_periods_yearly').hide();
			}else if(this.value == 1){
                //quarterly
                jQuery('#number_of_periods_month').hide();
                jQuery('#number_of_periods_quarterly').show();
                jQuery('#number_of_periods_yearly').hide();
                }else{
                jQuery('#number_of_periods_month').hide();
                jQuery('#number_of_periods_quarterly').hide();
                jQuery('#number_of_periods_yearly').show();
			}
        });
				
		jQuery('#btnSave').on('click',function(){
            app.helper.showProgress();
            var form_data = jQuery('#configuration_form').serializeArray();
            
            var actionParams = {
                'form_data':form_data,						
                'module':app.getModuleName(),
                'action':'ActionAjax',
                'mode':'saveConfig'
            };

            app.request.post({'data':actionParams}).then(
                function (err, data) {
                    if(err === null) {
                        app.helper.hideProgress();
                        var params = {};
                        params.message = app.vtranslate('Saved');
                        app.helper.showSuccessNotification(params);
                        //update Target
                        thisInstance.targetLoaded = false;
                    }
                    else {
                        app.helper.hideProgress();
                    }
                }
            );
			return false;
		});
		jQuery('a.delete').on('click',function(){
            if(confirm('Are you sure?')){
                app.helper.showProgress();
                var currentDom = jQuery(this);
                var cat_id = currentDom.attr('data-id');
                // console.log(cat_id);
                var params = {
                    'id':cat_id,
                    'module':app.getModuleName(),
                    'action':'ActionAjax',
                    'mode':'deleteCategory'
                };
                
                app.request.post({data:params}).then(
                    function(err, data) {
                        if(err === null) {
                            app.helper.hideProgress();
                            var params = {};
                            params.message = app.vtranslate('Deleted');
                            app.helper.showSuccessNotification(params);
                            //remove table row
                            // console.log(currentDom.parent().parent("tr:first").html());
                            currentDom.parent().parent("tr:first").remove()

                        }
                        else {
                            app.helper.hideProgress();
                        }
                    }
                );
            }
        });
        jQuery('a.deleteoppt').on('click',function(){
            if(confirm('Are you sure?')){
                app.helper.showProgress();
                var currentDom = jQuery(this);
                var cat_id = currentDom.attr('data-id');
                // console.log(cat_id);
                var params = {
                    'id':cat_id,
                    'module':app.getModuleName(),
                    'action':'ActionAjax',
                    'mode':'deleteCategoryOpptType'
                };
                
                app.request.post({data:params}).then(
                    function(err, data) {
                        if(err === null) {
                            app.helper.hideProgress();
                            var params = {};
                            params.message = app.vtranslate('Deleted');
                            app.helper.showSuccessNotification(params);
                            //remove table row
                            // console.log(currentDom.parent().parent("tr:first").html());
                            currentDom.parent().parent("tr:first").remove()

                        }
                        else {
                            app.helper.hideProgress();
                        }
                    }
                );
            }
        });

        /**
         * Target actions
         */
        jQuery('#forecastTargetTab').on('click','.target_value',function(){
            //alert('target_value click');
            thisInstance.targetInputLastOpen = jQuery(this).next();
            thisInstance.targetInputLastOpen.show();
            thisInstance.targetInputLastOpen.children("input:first").focus();
            thisInstance.targetDivLastOpen = jQuery(this);
            thisInstance.targetDivLastOpen.hide();
        });
        jQuery('#forecastTargetTab').on('focusout','.targetInputBox',function(){
            if(thisInstance.targetInputLastOpen!= null){
                thisInstance.targetInputLastOpen.hide();
                thisInstance.updateTargetBunchJs();
                thisInstance.targetDivLastOpen.show();
            }
        });
        jQuery('#btnTargetSave').on('click',function(){
            app.helper.showProgress();
            var form_data = jQuery('#target_form').serializeArray();
            var params = {
                'form_data':form_data,
                'module':app.getModuleName(),
                'action':'ActionAjax',
                'mode':'saveTarget'
            };
            
            app.request.post({data:params}).then(
                function(err, data) {
                    if(err === null) {
                        app.helper.hideProgress();
                        var params = {};
                        params.message = app.vtranslate('Saved');
                        app.helper.showSuccessNotification(params);
                        thisInstance.loadTargetTab();
                    }
                    else {
                        app.helper.hideProgress();
                    }
                }
            );
            return false;
        });
        jQuery('#btnTargetPrev').on('click',function(){
            thisInstance.targetYear = parseInt(jQuery('#targetYearHidden').val())-1;
            thisInstance.loadTargetTab();
        });
        jQuery('#btnTargetNext').on('click',function(){
            thisInstance.targetYear = parseInt(jQuery('#targetYearHidden').val())+1;
            thisInstance.loadTargetTab();
        });
    },
    loadTargetTab : function(){
        var thisInstance = this;
        app.helper.showProgress();
        var params = {
            'target_year':thisInstance.targetYear,
            'module':app.getModuleName(),
            'action':'ActionAjax',
            'mode':'loadTargetTab'
        };
        app.request.post({data:params}).then(
            function(err, data) {
                app.helper.hideProgress();
                jQuery('#forecastTargetTab').html(data);
                thisInstance.targetLoaded = true;
            }
        )
    },
    updateTargetBunchJs: function(){
        var thisInstance = this;
        if(thisInstance.targetInputLastOpen!=null){
            //get ID check if Organization Target
            var targetInputBox = thisInstance.targetInputLastOpen.children("input:first");
            var targetInputBoxOldValue = targetInputBox.data('old');
            if(targetInputBoxOldValue != targetInputBox.val() ){
                targetInputBoxOldValue = targetInputBox.val();

                var targetBoxId = targetInputBox.attr('id');
                var targetUserArray =   jQuery('#targetUsersArrayHidden').val().split('_');
                var targetTimeFrameArray =   jQuery('#targetTimeframeArrayHidden').val().split('_');
                var arr = targetBoxId.split('_');
                var params = {
                    'p1':targetInputBoxOldValue,
                    'p2':targetUserArray.length,
                    'p3':targetTimeFrameArray.length,
                    'module':app.getModuleName(),
                    'action':'CurrencyAjax'
                };
                app.request.post({data:params}).then(
                    function(err, data) {
                        //console.log(data);
                        if(arr[2]==0){
                            var targetColumnValue = data['v2'];
                            var targetTimeFrameColumnValue = data['v3'];
                            var targetColumnValueDisplay = data['d2'];
                            var targetTimeFrameColumnValueDisplay = data['d3'];
                            //Organization
                            if(arr[0].length>4){
                                //Time Frame
                                jQuery.each(targetUserArray, function( index, value ) {
                                    if(parseInt(value)>0){
                                        var inputBoxTemp = jQuery('#'+arr[0]+'_'+arr[1]+'_'+value+'_'+arr[3]);
                                        inputBoxTemp.val(targetColumnValue);
                                        //inputBoxTemp.attr('data-old',targetColumnValue);
                                        inputBoxTemp.closest('div').prev().html(targetColumnValueDisplay);
                                    }
                                });
                            }else{
                                //Target
                                jQuery.each(targetUserArray, function( index, value ) {
                                    if(parseInt(value)>0){
                                        var inputBoxTemp = jQuery('#'+arr[0]+'_'+arr[1]+'_'+value+'_'+arr[3]);
                                        inputBoxTemp.val(targetColumnValue);
                                        //inputBoxTemp.attr('data-old',targetColumnValue);
                                        inputBoxTemp.closest('div').prev().html(targetColumnValueDisplay);

                                        jQuery.each(targetTimeFrameArray, function( index1, value1 ) {
                                            var inputBoxTemp1 = jQuery('#'+value1+'_'+arr[1]+'_'+value+'_'+arr[3]);
                                            inputBoxTemp1.val(targetTimeFrameColumnValue);
                                            //inputBoxTemp1.attr('data-old',targetTimeFrameColumnValue);
                                            inputBoxTemp1.closest('div').prev().html(targetTimeFrameColumnValueDisplay);
                                        });
                                    }
                                });
                            }
                        }else{
                            if(arr[0].length<=4){
                                //Time Frame
                                var targetTimeFrameColumnValue = data['v4'];
                                var targetTimeFrameColumnValueDisplay = data['d4'];
                                jQuery.each(targetTimeFrameArray, function( index1, value1 ) {
                                    var inputBoxTemp1 = jQuery('#'+value1+'_'+arr[1]+'_'+arr[2]+'_'+arr[3]);
                                    inputBoxTemp1.val(targetTimeFrameColumnValue);
                                    //inputBoxTemp1.attr('data-old',targetTimeFrameColumnValue);
                                    inputBoxTemp1.closest('div').prev().html(targetTimeFrameColumnValueDisplay);
                                });
                            }
                        }
                        targetInputBox.attr('data-old',data['v1']);
                        targetInputBox.val(data['v1']);
                        thisInstance.targetDivLastOpen.html(data['d1']);
                    }
                )
            }

        }
    },
    registerButtonEvent: function() {
         var thisInstance=this;
         jQuery(document).on("click",".btnViewForecast", function(e) {
             var url = jQuery(this).data('url');
			 //alert(url);
             window.location.href = url;
         });
     },
    
	registerEvents : function() {
         this.registerSettingEvent();
         this.registerButtonEvent();
         // this.registerDeleteBlockEvent();
         // this.registerSelectModuleEvent();
        /* For License page - Begin */
        this.registerActivateLicenseEvent();
        this.registerValidEvent();
        /* For License page - End */
     }
});
jQuery(document).ready(function(){
    var instance = new VTEForecast_Settings_Js();
    instance.registerEvents();
    
    // Fix issue not display menu
    Vtiger_Index_Js.getInstance().registerEvents();
});