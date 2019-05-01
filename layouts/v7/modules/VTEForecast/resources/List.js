/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEForecast_List_Js",{

},{
    expandAll: false,
    btnExpandAllCall: false,
	registerDomEvent : function(){
		var thisInstance=this;
        /* Hide left panel */
        var leftPanel = jQuery('#leftPanel');
        var rightPanel = jQuery('#rightPanel');
        var toggleButton = jQuery('#toggleButton');
        if (!leftPanel.hasClass("hide")) {
            leftPanel.addClass('hide');
            toggleButton.addClass('hide');
            rightPanel.removeClass('col-lg-10').addClass('col-lg-12');

        }else{
            toggleButton.addClass('hide');
        }

        jQuery('#toggleButtonF').unbind("click");
        jQuery('#toggleButtonF').click(function(e){
            e.preventDefault();
            var leftPanel = jQuery('#leftPanelF');
            var rightPanel = jQuery('#rightPanelF');
            var tButtonImage = jQuery('#tButtonImageF');
            if (!leftPanel.hasClass("hide")) {
                var leftPanelshow = 1;
                leftPanel.addClass('hide');
                rightPanel.removeClass('col-lg-10').addClass('col-lg-12');
                tButtonImage.removeClass('glyphicon-chevron-left').addClass("glyphicon-chevron-right");
            } else {
                var leftPanelshow = 0;

                leftPanel.removeClass('hide');
                rightPanel.removeClass('col-lg-12').addClass('col-lg-10');
                tButtonImage.removeClass('glyphicon-chevron-right').addClass("glyphicon-chevron-left");
            }
        });
        /* End hide left panel */

		jQuery('#financial_time_summary').on('change', function(){
			//thisInstance.loadSummary();
            thisInstance.loadSummaryChart();
		});
		jQuery('#financial_time_from').on('change', function(){
			thisInstance.loadDashboardFrom();    
		});
		jQuery('#financial_time_to').on('change', function(){
			thisInstance.loadDashboardFrom();    
		});
		jQuery('#forecastDashboardFrom').on('click','.spanExpand', function(event, expandAll){
			var userId= jQuery(this).attr('data-node-id');
           /* console.log(event);
            console.log(expandAll);*/
            if(expandAll == undefined) thisInstance.btnExpandAllCall = false;
			if(jQuery(this).hasClass('collapse')){
			//Get data to expand
				thisInstance.loadDashboardFromChildren(userId);
				jQuery(this).removeClass('collapse').addClass('expand');				
			}else{			
				thisInstance.registerCollapse(userId);	
				jQuery(this).removeClass('expand').addClass('collapse');				
			}
			return false;	
		});
        jQuery('#btnExpandAll').on('click', function(){
            thisInstance.btnExpandAllCall = true;
            if(thisInstance.expandAll){
                thisInstance.expandAll = false;
                jQuery(this).html('Expand All');
            }else{
                thisInstance.expandAll = true;
                jQuery(this).html('Collapse All');
            }
            thisInstance.doExpandCollapseAll();
        });
        jQuery('#forecastDashboardFrom').on('click','.show-detail-dash', function(){
                if(jQuery(this).hasClass('expand')){
                    var nextTr =jQuery(this).closest('tr').next();
                    console.log(nextTr);
                    while(nextTr.hasClass('sale_stage_detail')){
                        nextTr.remove();
                        parrentNodeId = jQuery(this).attr('data-node-parrent');
                        rowSpan = parseInt(jQuery('#'+parrentNodeId).attr('rowSpan'));
                        rowSpan -= 1;
                        jQuery('#'+parrentNodeId).attr('rowSpan', rowSpan);
                        jQuery(this).removeClass('expand');
                        thisInstance.AjustLeftPanelFHeight();
                        nextTr =jQuery(this).closest('tr').next();
                    }
                }else{
                    var userIds= jQuery(this).attr('data-node-users');
                    var stageCondition= jQuery(this).next('input').val();
                    thisInstance.loadDashboardSaleStageDetail(jQuery(this),userIds,stageCondition);
                }
        });

	},
    doExpandCollapseAll: function(){
        var thisInstance=this;
        if(thisInstance.btnExpandAllCall){
            if(thisInstance.expandAll){
                jQuery('.spanExpand').each(function(){
                    if(jQuery(this).hasClass('collapse')){
                        jQuery(this).trigger('click',[true]);
                    }
                });
            }else{
                jQuery('.spanExpand').each(function(){
                    if(jQuery(this).hasClass('expand')){
                        jQuery(this).trigger('click',[true]);
                    }
                });
            }
        }
    },
	registerCollapse: function(userId){
		var thisInstance = this;
		jQuery('tr.parent'+userId).each(function(){
					//Close all children 					
			if(jQuery(this).hasClass('hadChildren')){
				var childrenId= jQuery(this).attr('data-node-id');
				thisInstance.registerCollapse(childrenId);
			}				
			jQuery(this).remove();					
		})			
		
	},
    loadSummaryChart : function(){
        var user_id = jQuery('#user_id').val();
        var forecast_period = jQuery('#forecast_period').val();
        var financial_time_summary = jQuery('#financial_time_summary').val();
        app.helper.showProgress();
        var actionParams = {
            'user_id':user_id,
            'forecast_period':forecast_period,
            'financial_time_summary':financial_time_summary,
            'module':app.getModuleName(),
            'action':'ActionAjax',
            'mode':'loadSummaryChart'
        };
        app.request.post({'data': actionParams}).then(
            function (err, data) {
                if(err === null) {
                    app.helper.hideProgress();
                    jQuery('#forecastSummary').html(data);
                }
                else {
                    app.helper.hideProgress();
                }
            }
        );
    },
	loadSummary : function(){
		var user_id = jQuery('#user_id').val();
		var forecast_period = jQuery('#forecast_period').val();
		var financial_time_summary = jQuery('#financial_time_summary').val();
        app.helper.showProgress();
		var actionParams = {
            'user_id':user_id,			
            'forecast_period':forecast_period,										
            'financial_time_summary':financial_time_summary,										
            'module':app.getModuleName(),
            'action':'ActionAjax',
            'mode':'loadSummary'
		};
        app.request.post({'data': actionParams}).then(
            function (err, data) {
                if(err === null) {
                    app.helper.hideProgress();
                    jQuery('#forecastSummary').html(data);
                }
                else {
                    app.helper.hideProgress();
                }
            }
        );
	},
	loadDashboardFrom : function(){
		var thisInstance = this;
		var user_id = jQuery('#user_id').val();
		var forecast_period = jQuery('#forecast_period').val();
		var financial_time_from = jQuery('#financial_time_from').val();
		var financial_time_to = jQuery('#financial_time_to').val();
        app.helper.showProgress();
        var actionParams = {
            'user_id':user_id,			
            'forecast_period':forecast_period,										
            'financial_time_from':financial_time_from,		
            'financial_time_to':financial_time_to,										
            'module':app.getModuleName(),
            'action':'ActionAjax',
            'mode':'loadDashboardFrom'
		};
        app.request.post({'data': actionParams}).then(
            function (err, data) {
                if(err === null) {
                    app.helper.hideProgress();
                    jQuery('#forecastDashboardFrom').html(data);

                    thisInstance.AjustLeftPanelFHeight();
                }
                else {
                    app.helper.hideProgress();
                }
            }
        );	
	},
    AjustLeftPanelFHeight : function(){
        var leftPanelF = jQuery('#leftPanelF');
        var rightPanelF = jQuery('#rightPanelF');
        var setheightpanel = rightPanelF.height();
        leftPanelF.css("min-height",setheightpanel + 'px');
        jQuery('div.spTree').each(function(){
            var tdObjectName = jQuery(this).attr('data-node-parrent');
            jQuery(this).css('height',20);
            jQuery(this).css('height',jQuery('#'+tdObjectName).height());

        });
    },
	loadDashboardFromChildren : function(userId){
		var thisInstance = this;
		
		var forecast_period = jQuery('#forecast_period').val();
		var financial_time_from = jQuery('#financial_time_from').val();
		var financial_time_to = jQuery('#financial_time_to').val();
        app.helper.showProgress();
		var actionParams = {
            'user_id':userId,			
            'forecast_period':forecast_period,										
            'financial_time_from':financial_time_from,		
            'financial_time_to':financial_time_to,										
            'module':app.getModuleName(),
            'action':'ActionAjax',
            'mode':'loadDashboardFromChildren'
		};
        app.request.post({'data':actionParams}).then(
            function (err, data) {
                if(err === null) {
                    app.helper.hideProgress();
                    // append to table				
                    // console.log(jQuery('#spanExpand'+userId).closest('tr'));
                     var totalTrID = jQuery('#spanExpand'+userId).attr('data-total-row-id');

                     jQuery(data).insertAfter(jQuery('#'+totalTrID));
                     thisInstance.doExpandCollapseAll();
                     thisInstance.AjustLeftPanelFHeight();
                     //thisInstance.registerExpand();
                }
                else {
                    app.helper.hideProgress();
                }
            }
        );
	},
    loadDashboardSaleStageDetail: function(currentDomObject, userIds, stageCondition){
        var thisInstance = this;

        var forecast_period = jQuery('#forecast_period').val();
        var financial_time_from = jQuery('#financial_time_from').val();
        var financial_time_to = jQuery('#financial_time_to').val();
        app.helper.showProgress();
        var actionParams = {
            'user_id':userIds,
            'stageCondition':stageCondition,
            'forecast_period':forecast_period,
            'financial_time_from':financial_time_from,
            'financial_time_to':financial_time_to,
            'module':app.getModuleName(),
            'action':'ActionAjax',
            'mode':'loadDashboardSaleStageDetail'
        };

        app.request.post({'data':actionParams}).then(
            function (err, data) {
                if(err === null) {
                    app.helper.hideProgress();
                    //Adjust First Columns
                    parrentNodeId = currentDomObject.attr('data-node-parrent');
                    rowSpan = parseInt(jQuery('#'+parrentNodeId).attr('rowSpan'));
                    //var aStageArray =   stageCondition.split(',');
                    rowSpan += 2;//aStageArray.length-1;
                    jQuery('#'+parrentNodeId).attr('rowSpan', rowSpan);
                    jQuery(data).insertAfter(currentDomObject.closest('tr'));
                    //Change current Icon
                    currentDomObject.removeClass('collapse').addClass('expand');
                    thisInstance.AjustLeftPanelFHeight();
                    //thisInstance.registerExpand();
                }
                else {
                    app.helper.hideProgress();
                }
            }
        );
    },
    registerLoadReportByUser : function() {
        var thisInstance = this;
        jQuery('.userNode').on('click',function(){
            var nodeid=jQuery(this).attr('data-nodeid');
            var nodeName=jQuery(this).attr('data-nodename');
			jQuery('div.treeView a.active').removeClass('active');
			jQuery(this).addClass('active');
            jQuery('#user_id').val(nodeid);
            jQuery('#divNodeName').html("<h3>"+nodeName+"'s Forecast</h3>");
            thisInstance.loadSummaryChart();
			thisInstance.loadDashboardFrom(); 	
        });
    },

    registerEvents : function() {
		this.registerDomEvent();
        this.registerLoadReportByUser();
    }
});
jQuery(document).ready(function(){
    var instance = new VTEForecast_List_Js();
    //instance.loadSummary();
    instance.loadSummaryChart();
	instance.loadDashboardFrom(); 	
	//instance.registerExpand();
    
    // Fix issue not display menu
    Vtiger_Index_Js.getInstance().registerEvents();
});