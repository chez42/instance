/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class('Vtiger_Widget_Js',{

	widgetPostLoadEvent : 'Vtiget.Dashboard.PostLoad',
	widgetPostRefereshEvent : 'Vtiger.Dashboard.PostRefresh',
    widgetPostResizeEvent : 'Vtiger.DashboardWidget.PostResize',

	getInstance : function(container, widgetName, moduleName) {
		if(typeof moduleName == 'undefined') {
			moduleName = app.getModuleName();
		}
		var widgetClassName = widgetName;
		var moduleClass = window[moduleName+"_"+widgetClassName+"_Widget_Js"];
		var fallbackClass = window["Vtiger_"+widgetClassName+"_Widget_Js"];
		var basicClass = Vtiger_Widget_Js;
		if(typeof moduleClass != 'undefined') {
			var instance = new moduleClass(container);
		}else if(typeof fallbackClass != 'undefined') {
			var instance = new fallbackClass(container);
		} else {
			var instance = new basicClass(container);
		}
		return instance;
	}
},{

	container : false,
	plotContainer : false,

	init : function (container) {
		this.setContainer(jQuery(container));
		this.registerWidgetPostLoadEvent(container);
		this.registerWidgetPostRefreshEvent(container);
        this.registerWidgetPostResizeEvent(container); 
	},

	getContainer : function() {
		return this.container;
	},

	setContainer : function(element) {
		this.container = element;
		return this;
	},

	isEmptyData : function() {
		var container = this.getContainer();
		return (container.find('.noDataMsg').length > 0) ? true : false;
	},

	getUserDateFormat : function() {
		return jQuery('#userDateFormat').val();
	},


	getPlotContainer : function(useCache) {
		if(typeof useCache == 'undefined'){
			useCache = false;
		}
		if(this.plotContainer == false || !useCache) {
			var container = this.getContainer();
			this.plotContainer = container.find('.widgetChartContainer');
		}
		return this.plotContainer;
	},

	restrictContentDrag : function(){
		this.getContainer().on('mousedown.draggable', function(e){
			var element = jQuery(e.target);
			var isHeaderElement = element.closest('.dashboardWidgetHeader').length > 0 ? true : false;
            var isResizeElement = element.is(".gs-resize-handle") ? true : false;
			if(isHeaderElement || isResizeElement){
				return;
			}
			//Stop the event propagation so that drag will not start for contents
			e.stopPropagation();
		})
	},

	convertToDateRangePicketFormat : function(userDateFormat) {
		if(userDateFormat == 'yyyy-mm-dd') {
			return 'yyyy-MM-dd';
		}else if( userDateFormat == 'mm-dd-yyyy') {
			return 'MM-dd-yyyy';
		}else if(userDateFormat == 'dd-mm-yyyy') {
			return 'dd-MM-yyyy';
		}
	},

	loadChart : function() {

	},

	positionNoDataMsg : function() {
		var container = this.getContainer();
		var widgetContentsContainer = container.find('.dashboardWidgetContent');
        widgetContentsContainer.height(container.height()- 50);
		var noDataMsgHolder = widgetContentsContainer.find('.noDataMsg');
		noDataMsgHolder.position({
				'my' : 'center center',
				'at' : 'center center',
				'of' : widgetContentsContainer
		})
	},
    
    postInitializeCalls : function() {},

	//Place holdet can be extended by child classes and can use this to handle the post load
	postLoadWidget : function() {
		if(!this.isEmptyData()) {
			this.loadChart();
            this.postInitializeCalls();
		}else{
			//this.positionNoDataMsg();
		}
		this.registerFilter();
		this.registerFilterChangeEvent();
		this.restrictContentDrag();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        widgetContent.css({height: widgetContent.height()-40});
	},
    
	postResizeWidget : function() {
		if(!this.isEmptyData()) {
			this.loadChart();
            this.postInitializeCalls();
		}else{
			//this.positionNoDataMsg();
		}
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        widgetContent.css({height: widgetContent.height()-40});
	},

	postRefreshWidget : function() {
		if(!this.isEmptyData()) {
			this.loadChart();
            this.postInitializeCalls();
		}else{
//			this.positionNoDataMsg();
		}
	},

	getFilterData : function() {
		return {};
	},

	refreshWidget : function() {
		var self = this;
		var parent = this.getContainer();
		var element = parent.find('a[name="drefresh"]');
		var url = element.data('url');
		
		var tab = parent.closest('.tab-pane').data('tabid');
		
        var contentContainer = parent.find('.dashboardWidgetContent');
		var params = {};
        params.url = url;
		var widgetFilters = parent.find('.widgetFilter');
		if(widgetFilters.length > 0) {
			params.url = url;
			params.data = {};
			widgetFilters.each(function(index, domElement){
				var widgetFilter = jQuery(domElement);
                //Filter unselected checkbox, radio button elements
                if((widgetFilter.is(":radio") || widgetFilter.is(":checkbox")) && !widgetFilter.is(":checked")){
                    return true;
                }
				if(widgetFilter.is('.dateRange')){
					var name = widgetFilter.attr('name');
                    var start = widgetFilter.find('input[name="start"]').val();
                    var end = widgetFilter.find('input[name="end"]').val();
                    if(start.length <= 0 || end.length <= 0  ){
                        return true;
                    } 
                    
					params.data[name] = {};
					params.data[name].start = start;
					params.data[name].end = end;
				}else{
					var filterName = widgetFilter.attr('name');
					var filterValue = widgetFilter.val();
					params.data[filterName] = filterValue;
				}
			});
		}
		var filterData = this.getFilterData();
		if(! jQuery.isEmptyObject(filterData)) {
			if(typeof params == 'string') {
				url = params;
				params = {};
				params.url = url;
				params.data = {};
			}
			params.data = jQuery.extend(params.data, this.getFilterData())
		}
		params.data['tab'] = tab;
		//Sending empty object in data results in invalid request
		if(jQuery.isEmptyObject(params.data)) {
			delete params.data;
		}
		
		parent.waitMe({effect : 'orbit',text : 'Please wait...' });
		app.request.post(params).then(
			function(err,data){
                //app.helper.hideProgress();
				parent.waitMe('hide');
				if(contentContainer.closest('.mCustomScrollbar').length) {
					contentContainer.mCustomScrollbar('destroy');
					contentContainer.html(data);
					var adjustedHeight = parent.height()-100;
					app.helper.showVerticalScroll(contentContainer,{'setHeight' : adjustedHeight});
				}else {
					contentContainer.html(data);
				}
                
                /**
                 * we are setting default height in DashBoardWidgetContents.tpl
                 * need to overwrite based on resized widget height 
                 */ 
                var widgetChartContainer = contentContainer.find(".widgetChartContainer");
                if(widgetChartContainer.length > 0){
                    widgetChartContainer.css("height",parent.height() - 60);
                }
				contentContainer.trigger(Vtiger_Widget_Js.widgetPostRefereshEvent);
			}
		);
		
	},

	registerFilter : function() {
		var thisInstance = this;
		var container = this.getContainer();
		var dateRangeElement = container.find('.input-daterange');
		if(dateRangeElement.length <= 0) {
			return;
		}
		
		dateRangeElement.addClass('dateField');
		
		var pickerParams = {
            format : thisInstance.getUserDateFormat(),
        };
		vtUtils.registerEventForDateFields(dateRangeElement, pickerParams);
		
        dateRangeElement.on("changeDate", function(e){
           var start = dateRangeElement.find('input[name="start"]').val();
           var end = dateRangeElement.find('input[name="end"]').val();
           if(start != '' && end != '' && start !== end){
               container.find('a[name="drefresh"]').trigger('click');
           }
        });
		dateRangeElement.attr('data-date-format',thisInstance.getUserDateFormat());
	},

	registerFilterChangeEvent : function() {
		this.getContainer().on('change', '.widgetFilter, .reloadOnChange', function(e) {
			var target = jQuery(e.currentTarget);
			if(target.hasClass('dateRange')) {
				var start = target.find('input[name="start"]').val();
				var end = target.find('input[name="end"]').val();
				if(start == '' || end == '') return false;
			}
			
			var widgetContainer = target.closest('li');
			widgetContainer.find('a[name="drefresh"]').trigger('click');
		})
	},

	registerWidgetPostLoadEvent : function(container) {
		var thisInstance = this;
		container.off(Vtiger_Widget_Js.widgetPostLoadEvent).on(Vtiger_Widget_Js.widgetPostLoadEvent, function(e) {
			thisInstance.postLoadWidget();
		})
	},

	registerWidgetPostRefreshEvent : function(container) {
		var thisInstance = this;
		container.on(Vtiger_Widget_Js.widgetPostRefereshEvent, function(e) {
			thisInstance.postRefreshWidget();
		});
	},
    
    registerWidgetPostResizeEvent : function(container){
        var thisInstance = this;
		container.on(Vtiger_Widget_Js.widgetPostResizeEvent, function(e) { 
			thisInstance.postResizeWidget();
		});
    },
    
    openUrl : function(url) {
        var win = window.open(url, '_blank');
        win.focus();
    },
    
    registerWidgetConditions : function(params){
    	var parent = this.getContainer();
    	var actionParams = {};
    	if(!jQuery.isEmptyObject(params.data)) {
			actionParams['cond'] = params.data;
		}
    	actionParams['module'] = 'Vtiger';
    	actionParams['action'] = 'SaveWidgetCondition';
		
		var link = params.url;
		var data = link.split("&");

		var link_id = data[3].split("=")[1];
		actionParams['linkid'] = link_id;
		
		parent.waitMe({effect : 'orbit',text : 'Please wait...' });
		app.request.post({'data':actionParams}).then(
			function(err,data){
				parent.waitMe('hide');
			}
		);
    }
    
});


Vtiger_Widget_Js('Vtiger_KeyMetrics_Widget_Js', {}, {
    postLoadWidget: function() {
		this._super();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		widgetContent.css({height: widgetContent.height()-40});
	},

	postResizeWidget: function () {
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
		var adjustedHeight = this.getContainer().height() - 20;
		widgetContent.css({height: adjustedHeight});
		slimScrollDiv.css({height: adjustedHeight});
	}
});

Vtiger_Widget_Js('Vtiger_TopPotentials_Widget_Js', {}, {
    
   postLoadWidget: function() {
		this._super();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		widgetContent.css({height: widgetContent.height()-40});
	}
});

Vtiger_Widget_Js('Vtiger_History_Widget_Js', {}, {

	postLoadWidget: function() {
		this._super();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
		widgetContent.css({height: widgetContent.height()-40});
        //this.initSelect2Elements(widgetContent);
		this.registerLoadMore();
	},
    
    postResizeWidget: function() {
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
        var adjustedHeight = this.getContainer().height()-100;
        widgetContent.css({height: adjustedHeight});
        slimScrollDiv.css({height: adjustedHeight});
	},
        
	initSelect2Elements : function(widgetContent) {
		var container = widgetContent.closest('.dashboardWidget');
		var select2Elements = container.find('.select2');
		if(select2Elements.length > 0 && jQuery.isArray(select2Elements)) {
			select2Elements.each(function(index, domElement){
				domElement.chosen();
			});
		}else{
			select2Elements.chosen();
		}
	},

	postRefreshWidget: function() {
		this._super();
		this.registerLoadMore();
	},

	registerLoadMore: function() {
		var thisInstance  = this;
		var parent = thisInstance.getContainer();
		var contentContainer = parent.find('.dashboardWidgetContent');
		
		var tab = parent.closest('.tab-pane').data('tabid');
		
		var loadMoreHandler = contentContainer.find('.load-more');
		loadMoreHandler.off('click');
		loadMoreHandler.click(function(){
			var parent = thisInstance.getContainer();
			var element = parent.find('a[name="drefresh"]');
			var url = element.data('url');
			var params = url;

			var widgetFilters = parent.find('.widgetFilter');
			if(widgetFilters.length > 0) {
				params = { url: url, data: {}};
				widgetFilters.each(function(index, domElement){
					var widgetFilter = jQuery(domElement);
					//Filter unselected checkbox, radio button elements
					if((widgetFilter.is(":radio") || widgetFilter.is(":checkbox")) && !widgetFilter.is(":checked")){
						return true;
					}
					
					if(widgetFilter.is('.dateRange')) {
						var name = widgetFilter.attr('name');
						var start = widgetFilter.find('input[name="start"]').val();
						var end = widgetFilter.find('input[name="end"]').val();
						if(start.length <= 0 || end.length <= 0  ){
							return true;
						} 

						params.data[name] = {};
						params.data[name].start = start;
						params.data[name].end = end;
					} else {
						var filterName = widgetFilter.attr('name');
						var filterValue = widgetFilter.val();
						params.data[filterName] = filterValue;
					}
				});
			}

			var filterData = thisInstance.getFilterData();
			if(! jQuery.isEmptyObject(filterData)) {
				if(typeof params == 'string') {
					params = { url: url, data: {}};
				}
				params.data = jQuery.extend(params.data, thisInstance.getFilterData())
			}

			// Next page.
			params.data['page'] = loadMoreHandler.data('nextpage');
			
			params.data['tab'] = tab;
			
            parent.waitMe({effect : 'orbit',text : 'Please wait...'});
			app.request.post(params).then(function(err,data){
				parent.waitMe('hide');
				loadMoreHandler.parent().parent().replaceWith(jQuery(data).html());
				thisInstance.registerLoadMore();
			}, function(){
				parent.waitMe('hide');
			});
		});
	}

});


Vtiger_Widget_Js('Vtiger_Funnel_Widget_Js',{},{

    postInitializeCalls: function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },
    
    generateLinks : function() {
        var data = this.getContainer().find('.widgetData').val();
        var parsedData = JSON.parse(data);
        var linksData = [];
        for(var index in parsedData) {
            var newData = {};
            var itemDetails = parsedData[index];
            newData.name = itemDetails[0];
            newData.links = itemDetails[3];
            linksData.push(newData);
        }
        return linksData;
    },

	loadChart : function() {
		var container = this.getContainer();
		var data = container.find('.widgetData').val();
        var chartOptions = {
            renderer:'funnel',
            links: this.generateLinks()
        };
        this.getPlotContainer(false).vtchart(data,chartOptions);
	}
    
});



Vtiger_Widget_Js('Vtiger_Pie_Widget_Js',{},{

	/**
	 * Function which will give chart related Data
	 */
	generateData : function() {
		var container = this.getContainer();
		var jData = container.find('.widgetData').val();
		var data = JSON.parse(jData);
		var chartData = [];
		for(var index in data) {
			var row = data[index];
			var rowData = [row.last_name, parseFloat(row.amount), row.id];
			chartData.push(rowData);
		}
		return {'chartData':chartData};
	},
    
    generateLinks : function() {
        var jData = this.getContainer().find('.widgetData').val();
        var statData = JSON.parse(jData);
        var links = [];
        for(var i = 0; i < statData.length ; i++){
            links.push(statData[i]['links']);
        }
        return links;
    },
    
    postInitializeCalls: function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },

	loadChart : function() {
		var chartData = this.generateData();
        var chartOptions = {
            renderer:'pie',
            links: this.generateLinks()
        };
        this.getPlotContainer(false).vtchart(chartData,chartOptions);
	}
});


Vtiger_Widget_Js('Vtiger_Barchat_Widget_Js',{},{

	generateChartData : function() {
		var container = this.getContainer();
		var jData = container.find('.widgetData').val();
		var data = JSON.parse(jData);
		var chartData = [];
		var xLabels = new Array();
		var yMaxValue = 0;
		for(var index in data) {
			var row = data[index];
			row[0] = parseFloat(row[0]);
			xLabels.push(app.getDecodedValue(row[1]));
			chartData.push(row[0]);
			if(parseInt(row[0]) > yMaxValue){
				yMaxValue = parseInt(row[0]);
			}
		}
        // yMaxValue Should be 25% more than Maximum Value
		yMaxValue = yMaxValue + 2 + (yMaxValue/100)*25;
		return {'chartData':[chartData], 'yMaxValue':yMaxValue, 'labels':xLabels};
	},
    
    generateLinks : function() {
        var container = this.getContainer();
        var jData = container.find('.widgetData').val();
        var statData = JSON.parse(jData);
        var links = [];
        for(var i = 0; i < statData.length ; i++){
            links.push(statData[i]['links']);
        }
        return links;
    },
    
    postInitializeCalls : function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },

	loadChart : function() {
		var data = this.generateChartData();
        var chartOptions = {
            renderer:'bar',
            links: this.generateLinks()
        };
        this.getPlotContainer(false).vtchart(data,chartOptions);
	}
    
});

Vtiger_Widget_Js('Vtiger_MultiBarchat_Widget_Js',{

	/**
	 * Function which will give char related Data like data , x labels and legend labels as map
	 */
	getCharRelatedData : function() {
		var container = this.getContainer();
		var data = container.find('.widgetData').val();
		var users = new Array();
		var stages = new Array();
		var count = new Array();
		for(var i=0; i<data.length;i++) {
			if($.inArray(data[i].last_name, users) == -1) {
				users.push(data[i].last_name);
			}
			if($.inArray(data[i].sales_stage, stages) == -1) {
				stages.push(data[i].sales_stage);
			}
		}

		for(j in stages) {
			var salesStageCount = new Array();
			for(i in users) {
				var salesCount = 0;
				for(var k in data) {
					var userData = data[k];
					if(userData.sales_stage == stages[j] && userData.last_name == users[i]) {
						salesCount = parseInt(userData.count);
						break;
					}
				}
				salesStageCount.push(salesCount);
			}
			count.push(salesStageCount);
		}
		return {
			'data' : count,
			'ticks' : users,
			'labels' : stages
		}
	},
    
    postInitializeCalls : function() {
        var thisInstance = this;
        this.getPlotContainer(false).off('vtchartClick').on('vtchartClick',function(e,data){
            if(data.url)
                thisInstance.openUrl(data.url);
        });
    },
    
	loadChart : function(){
		var chartRelatedData = this.getCharRelatedData();
        var chartOptions = {
            renderer:'multibar',
            links:chartRelatedData.links
        };
        this.getPlotContainer(false).data('widget-data',JSON.stringify(this.getCharRelatedData()));
        this.getPlotContainer(false).vtchart(chartRelatedData,chartOptions);
	}

});

// NOTE Widget-class name camel-case convention
Vtiger_Widget_Js('Vtiger_MiniList_Widget_Js', {
    
    registerMoreClickEvent : function(e) {
        var moreLink = jQuery(e.currentTarget);
        var linkId = moreLink.data('linkid');
        var widgetId = moreLink.data('widgetid');
        var currentPage = jQuery('#widget_'+widgetId+'_currentPage').val();
        var nextPage = parseInt(currentPage) + 1;
        var params = {
            'module' : app.getModuleName(),
            'view' : 'ShowWidget',
            'name' : 'MiniList',
            'linkid' : linkId,
            'widgetid' : widgetId,
            'content' : 'data',
            'currentPage' : currentPage
        }
        app.request.post({"data":params}).then(function(err,data) {
            var htmlData = jQuery(data);
            var htmlContent = htmlData.find('.miniListContent');
            moreLink.parent().before(htmlContent);
            jQuery('#widget_'+widgetId+'_currentPage').val(nextPage);
            var moreExists = htmlData.find('.moreLinkDiv').length;
            if(!moreExists) {
                moreLink.parent().remove();
            }
        });
    }
    
}, {
	postLoadWidget: function() {
        app.helper.hideModal();
        this.restrictContentDrag();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
        widgetContent.css({height: widgetContent.height()-40});
	},
    
    postResizeWidget: function() {
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
        var adjustedHeight = this.getContainer().height()-100;
        widgetContent.css({height: adjustedHeight});
        slimScrollDiv.css({height: adjustedHeight});
	}
});

Vtiger_Widget_Js('Vtiger_TagCloud_Widget_Js',{},{

	postLoadWidget : function() {
		this._super();
		this.registerTagCloud();
		this.registerTagClickEvent();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
        widgetContent.css({height: widgetContent.height()-40});
	},

	registerTagCloud : function() {
		jQuery('#tagCloud').find('a').tagcloud({
			size: {
			  start: parseInt('12'),
			  end: parseInt('30'),
			  unit: 'px'
			},
			color: {
			  start: "#0266c9",
			  end: "#759dc4"
			}
		});
	},

	registerChangeEventForModulesList : function() {
		jQuery('#tagSearchModulesList').on('change',function(e) {
			var modulesSelectElement = jQuery(e.currentTarget);
			if(modulesSelectElement.val() == 'all'){
				jQuery('[name="tagSearchModuleResults"]').removeClass('hide');
			} else{
				jQuery('[name="tagSearchModuleResults"]').removeClass('hide');
				var selectedOptionValue = modulesSelectElement.val();
				jQuery('[name="tagSearchModuleResults"]').filter(':not(#'+selectedOptionValue+')').addClass('hide');
			}
		});
	},

	registerTagClickEvent : function(){
		var thisInstance = this;
		var container = this.getContainer();
		container.on('click','.tagName',function(e) {
			var tagElement = jQuery(e.currentTarget);
			var tagId = tagElement.data('tagid');
			var params = {
				'module' : app.getModuleName(),
				'view' : 'TagCloudSearchAjax',
				'tag_id' : tagId,
				'tag_name' : tagElement.text()
			}
			app.request.post({"data":params}).then(
				function(err,data) {
                    app.helper.showModal(data);
                    vtUtils.applyFieldElementsView(jQuery(".myModal"));
					thisInstance.registerChangeEventForModulesList();
				}
			)
		});
	},

	postRefreshWidget : function() {
		this._super();
		this.registerTagCloud();
	},

	postResizeWidget: function () {
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
		var adjustedHeight = this.getContainer().height() - 20;
		widgetContent.css({height: adjustedHeight});
		slimScrollDiv.css({height: adjustedHeight});
	}
});

/* Notebook Widget */
Vtiger_Widget_Js('Vtiger_Notebook_Widget_Js', {

}, {

	// Override widget specific functions.
	postLoadWidget: function() {
		this.reinitNotebookView();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
        //widgetContent.css({height: widgetContent.height()-40});
	},
    
    postResizeWidget: function() {
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var slimScrollDiv = jQuery('.slimScrollDiv', this.getContainer());
        var adjustedHeight = this.getContainer().height()-100;
        widgetContent.css({height: adjustedHeight});
        slimScrollDiv.css({height: adjustedHeight});
        widgetContent.find('.dashboard_notebookWidget_viewarea').css({height:adjustedHeight});
	},
    
    postRefreshWidget : function() {
        this.reinitNotebookView();
        var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
        var adjustedHeight = this.getContainer().height()-50;
        app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
    },
    
	reinitNotebookView: function() {
		var self = this;
		jQuery('.dashboard_notebookWidget_edit', this.container).click(function(){
			self.editNotebookContent();
		});
		jQuery('.dashboard_notebookWidget_save', this.container).click(function(){
			self.saveNotebookContent();
		});
	},

	editNotebookContent: function() {
		jQuery('.dashboard_notebookWidget_text', this.container).show();
		jQuery('.dashboard_notebookWidget_view', this.container).hide();
	},

	saveNotebookContent: function() {
		var self = this;
		var refreshContainer = this.container.find('.refresh');
		var textarea = jQuery('.dashboard_notebookWidget_textarea', this.container);

		var url = this.container.data('url');
		var params = url + '&content=true&mode=save&contents=' + textarea.val();

		app.helper.showProgress();
		app.request.post({"url":params}).then(function(err,data) {
            app.helper.hideProgress();
			var parent = self.getContainer();
			var widgetContent = parent.find('.dashboardWidgetContent');
			widgetContent.mCustomScrollbar('destroy');
			widgetContent.html(data);
			var adjustedHeight = parent.height() - 50;
			app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight});
			
			self.reinitNotebookView();
		});
	},
	
	refreshWidget : function() {
		var parent = this.getContainer();
		var element = parent.find('a[name="drefresh"]');
		var url = element.data('url');

        var contentContainer = parent.find('.dashboardWidgetContent');
		var params = {};
        params.url = url;
		
        parent.waitMe({effect : 'orbit',text : 'Please wait...'});
		app.request.post(params).then(
			function(err,data){
				parent.waitMe('hide');
				
				if(contentContainer.closest('.mCustomScrollbar').length) {
					contentContainer.mCustomScrollbar('destroy');
					contentContainer.html(data);
					var adjustedHeight = parent.height()-50;
					app.helper.showVerticalScroll(contentContainer,{'setHeight' : adjustedHeight});
				}else {
					contentContainer.html(data);
				}
                
				contentContainer.trigger(Vtiger_Widget_Js.widgetPostRefereshEvent);
			}
		);
	},
});

Vtiger_Widget_Js('Vtiger_Trailingrevenue_Widget_Js',{},{

    generateChartData : function() {
        var revenueWidgetContainer = $(document).find("#trailing_revenue_chart");

        var assetChartData = ($(revenueWidgetContainer).find(".widgetData").val());

        if(typeof(assetChartData) !== "undefined")
            var assetChartData = $.parseJSON(assetChartData);
        else
            assetChartData = false;

        return assetChartData;
    },

    postLoadWidget: function() {
        this._super();
        var thisInstance = this;
    },

    loadChart : function() {

        var chartData = this.generateChartData();

        if(!chartData) return false;

        var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var graph = new AmCharts.AmGraph();
        graph.valueField = "value";
        graph.balloonText="[[category]]: $[[value]]";
        graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph.type = "column";
        graph.lineAlpha = 0;
        graph.fillAlphas = 0.6;
        graph.fillColors = "#02B90E";
        chart.addGraph(graph);
        chart.angle = 0;
        chart.depth3D = 0;

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;
        chart.write("portfolio_trailing_revenue");
    }

});

Vtiger_History_Widget_Js('Vtiger_OverdueActivities_Widget_Js', {}, {

	registerLoadMore: function() {
		var thisInstance  = this;
		var parent = thisInstance.getContainer();
        parent.off('click', 'a[name="history_more"]'); 
        parent.on('click','a[name="history_more"]', function(e) {
			var parent = thisInstance.getContainer();
            var element = jQuery(e.currentTarget);
            var type = parent.find("[name='type']").val();
			var url = element.data('url');
			var params = url+'&content=true&type='+type;
            app.request.post({"url":params}).then(function(err,data) {
                element.parent().remove();
				var widgetContent = jQuery('.dashboardWidgetContent', parent);
				var dashboardWidgetData = parent.find('.dashboardWidgetContent .dashboardWidgetData');
				var scrollTop = dashboardWidgetData.height() * dashboardWidgetData.length - 100;
				widgetContent.mCustomScrollbar('destroy');
                parent.find('.dashboardWidgetContent').append(data);
				
				var adjustedHeight = parent.height()-100;
				app.helper.showVerticalScroll(widgetContent,{'setHeight' : adjustedHeight, 'setTop' : scrollTop+'px'});
				
            });
		});
	}

});

Vtiger_OverdueActivities_Widget_Js('Vtiger_CalendarActivities_Widget_Js', {}, {});


Vtiger_Widget_Js('Vtiger_AssetAllocation_Widget_Js',{},{

	generateChartData : function() {
		
		var assetAllocationWidgetContainer = $(document).find("#asset_allocation_chart");
	
		var assetChartData = ($(assetAllocationWidgetContainer).find(".widgetData").val());
    
		if(typeof(assetChartData) !== "undefined")
			var assetChartData = $.parseJSON(assetChartData);
		else
			assetChartData = false;
			
		return assetChartData;
	},
    
	loadChart : function() {
		
		var chartData = this.generateChartData();

		if(!chartData) return false;
		
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.radius = 70;
        chart.labelText = "[[percents]]%";
        chart.hideLabelsPercent = 100;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 0;
        chart.angle = 0;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;

		var legend = new AmCharts.AmLegend();
		legend.align = "center";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "bottom";
        legend.valueText = "$[[value]]";
        legend.valueWidth = 100;
        legend.switchable = false;
        legend.labelText = "[[title]]:";
        legend.labelWidth = 100;
		
        chart.addLegend(legend);
       
        if($("#portfolio_asset_allocation").length > 0) {
            chart.write("portfolio_asset_allocation");
        }
				
	}

});

Vtiger_Widget_Js('Vtiger_AccountActivity_Widget_Js',{},{

	generateChartData : function() {
		
		var accountActivityWidgetContainer = $(document).find("#account_activity_chart");
	
		var assetChartData = ($(accountActivityWidgetContainer).find(".widgetData").val());
    
		if(typeof(assetChartData) !== "undefined")
			var assetChartData = $.parseJSON(assetChartData);
		else
			assetChartData = false;
			
		return assetChartData;
	},
    

	loadChart : function() {
		
		var chartData = this.generateChartData();

		if(!chartData) return false;
		
        var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;
        chart.mouseWheelZoomEnabled = true;
        chart.sequencedAnimation = false;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;

        var graph = new AmCharts.AmGraph();
        graph.valueField = "value";
        graph.balloonText="Currently Active Accounts: [[value]]\r\n[[new_accounts]] new accounts\r\n[[closed_accounts]] Closed accounts";
        graph.type = "line";
        graph.lineColor = "#000033";
        graph.bullet = 'round';
        chart.addGraph(graph);
        chart.write("portfolio_account_activity");
   }

});

Vtiger_Widget_Js('Vtiger_TrailingRevenue_Widget_Js',{},{

	generateChartData : function() {
		
		var revenueWidgetContainer = $(document).find("#trailing_revenue_chart");
	
		var assetChartData = ($(revenueWidgetContainer).find(".widgetData").val());
    
		if(typeof(assetChartData) !== "undefined")
			var assetChartData = $.parseJSON(assetChartData);
		else
			assetChartData = false;
			
		return assetChartData;
	},
    
     postLoadWidget: function() {
		this._super();
        var thisInstance = this;
	},

	loadChart : function() {
		
		var chartData = this.generateChartData();

		if(!chartData) return false;
		
		var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var graph = new AmCharts.AmGraph();
        graph.valueField = "value";
        graph.balloonText="[[category]]: $[[value]]";
        graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph.type = "column";
        graph.lineAlpha = 0;
        graph.fillAlphas = 0.6;
        graph.fillColors = "#02B90E";
        chart.addGraph(graph);
        chart.angle = 0;
        chart.depth3D = 0;

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;
        chart.write("portfolio_trailing_revenue");
	}

});

Vtiger_Widget_Js('Vtiger_USHeatMap_Widget_Js',{},{

	generateChartData : function() {
		
		var revenueWidgetContainer = $(document).find(".contact_heat_map");
	
		var assetChartData = ($(revenueWidgetContainer).find(".widgetData").val());
    
		if(typeof(assetChartData) !== "undefined")
			var assetChartData = $.parseJSON(assetChartData);
		else
			assetChartData = false;
			
		return assetChartData;
	},
    

	loadChart : function() {
		
		var chartData = this.generateChartData();

		if(!chartData) return false;
		
		var territories = ["divider1", "divider2", "AS", "GU", "MP", "PR", "VI"];
		
		for ( index in chartData ) {
			
			var state = chartData[ index ];
			
			var USState = state.id.split( '-' ).pop();
			
			if(jQuery.inArray(USState, territories) > -1){
				state.id = USState;
				chartData[ index ] = state;
			}
		}
		
		var validUSStates = ["divider1", "divider2", "AS", "GU", "MP", "PR", "VI", "US-AK","US-AL","US-AR","US-AZ","US-CA","US-CO","US-CT","US-DC","US-DE","US-FL","US-GA","US-HI","US-IA","US-ID","US-IL","US-IN","US-KS","US-KY","US-LA","US-MA","US-MD","US-ME","US-MI","US-MN","US-MO","US-MS","US-MT","US-NC","US-ND","US-NE","US-NH","US-NJ","US-NM","US-NV","US-NY","US-OH","US-OK","US-OR","US-PA","US-RI","US-SC","US-SD","US-TN","US-TX","US-UT","US-VA","US-VT","US-WA","US-WI","US-WV","US-WY"];
		var USStateLatLon = {};
		
		USStateLatLon['divider1'] = {'latitude': '25.2127', 'longitude': '-107.8954'};
		USStateLatLon['divider2'] = {'latitude': '22.9278', 'longitude': '-83.3092'};
		USStateLatLon['AS'] = {'latitude': '22.6527', 'longitude': '-90.4402'};
		USStateLatLon['GU'] = {'latitude': '18.2358', 'longitude': '-75.0879'};
		USStateLatLon['MP'] = {'latitude': '25.641', 'longitude': '-74.5465'};
		USStateLatLon['PR'] = {'latitude': '18.4856', 'longitude': '-90.6273'};
		USStateLatLon['VI'] = {'latitude': '18.2733', 'longitude': '-81.9925'};
		USStateLatLon['US-AK'] = {'latitude': '21.1874', 'longitude': '-115.2528'};
		USStateLatLon['US-AL'] = {'latitude': '32.6185', 'longitude': '-86.6382'};
		USStateLatLon['US-AR'] = {'latitude': '34.7462', 'longitude': '-92.111'};
		USStateLatLon['US-AZ'] = {'latitude': '34.1712', 'longitude': '-111.8983'};
		USStateLatLon['US-CA'] = {'latitude': '37.3724', 'longitude': '-119.252'};
		USStateLatLon['US-CO'] = {'latitude': '38.9833', 'longitude': '-105.5145'};
		USStateLatLon['US-CT'] = {'latitude': '41.4829', 'longitude': '-72.7044'};
		USStateLatLon['US-DC'] = {'latitude': '38.8424', 'longitude': '-76.9783'};
		USStateLatLon['US-DE'] = {'latitude': '39.1087', 'longitude': '-75.3801'};
		USStateLatLon['US-FL'] = {'latitude': '28.0247', 'longitude': '-83.8065'};
		USStateLatLon['US-GA'] = {'latitude': '32.6495', 'longitude': '-83.2089'};
		USStateLatLon['US-HI'] = {'latitude': '22.3377', 'longitude': '-102.9687'};
		USStateLatLon['US-IA'] = {'latitude': '41.9251', 'longitude': '-93.3518'};
		USStateLatLon['US-ID'] = {'latitude': '45.5751', 'longitude': '-114.0915'};
		USStateLatLon['US-IL'] = {'latitude': '39.7428', 'longitude': '-89.4772'};
		USStateLatLon['US-IN'] = {'latitude': '39.7567', 'longitude': '-86.3895'};
		USStateLatLon['US-KS'] = {'latitude': '38.4684', 'longitude': '-98.2939'};
		USStateLatLon['US-KY'] = {'latitude': '37.7684', 'longitude': '-85.6996'};
		USStateLatLon['US-LA'] = {'latitude': '31.0517', 'longitude': '-91.699'};
		USStateLatLon['US-MA'] = {'latitude': '42.1455', 'longitude': '-71.6529'};
		USStateLatLon['US-MD'] = {'latitude': '38.7909', 'longitude': '-77.2016'};
		USStateLatLon['US-ME'] = {'latitude': '45.2737', 'longitude': '-69.0588'};
		USStateLatLon['US-MI'] = {'latitude': '44.6214', 'longitude': '-86.3698'};
		USStateLatLon['US-MN'] = {'latitude': '46.4956', 'longitude': '-93.3799'};
		USStateLatLon['US-MO'] = {'latitude': '38.3109', 'longitude': '-92.4148'};
		USStateLatLon['US-MS'] = {'latitude': '32.5741', 'longitude': '-89.8245'};
		USStateLatLon['US-MT'] = {'latitude': '46.7042', 'longitude': '-110.0064'};
		USStateLatLon['US-NC'] = {'latitude': '35.1871', 'longitude': '-79.9871'};
		USStateLatLon['US-ND'] = {'latitude': '47.476', 'longitude': '-100.2824'};
		USStateLatLon['US-NE'] = {'latitude': '41.4785', 'longitude': '-99.654'};
		USStateLatLon['US-NH'] = {'latitude': '43.9813', 'longitude': '-71.5527'};
		USStateLatLon['US-NJ'] = {'latitude': '40.1166', 'longitude': '-74.695'};
		USStateLatLon['US-NM'] = {'latitude': '34.169', 'longitude': '-105.9912'};
		USStateLatLon['US-NV'] = {'latitude': '38.5676', 'longitude': '-116.982'};
		USStateLatLon['US-NY'] = {'latitude': '42.8193', 'longitude': '-75.8144'};
		USStateLatLon['US-OH'] = {'latitude': '40.1801', 'longitude': '-82.6341'};
		USStateLatLon['US-OK'] = {'latitude': '35.2876', 'longitude': '-98.6836'};
		USStateLatLon['US-OR'] = {'latitude': '44.1417', 'longitude': '-120.4674'};
		USStateLatLon['US-PA'] = {'latitude': '40.9676', 'longitude': '-77.5616'};
		USStateLatLon['US-RI'] = {'latitude': '41.6339', 'longitude': '-71.4487'};
		USStateLatLon['US-SC'] = {'latitude': '33.5718', 'longitude': '-80.9134'};
		USStateLatLon['US-SD'] = {'latitude': '44.2456', 'longitude': '-100.2251'};
		USStateLatLon['US-TN'] = {'latitude': '35.7736', 'longitude': '-85.9281'};
		USStateLatLon['US-TX'] = {'latitude': '31.271', 'longitude': '-100.0268'};
		USStateLatLon['US-UT'] = {'latitude': '39.5007', 'longitude': '-111.5086'};
		USStateLatLon['US-VA'] = {'latitude': '37.9401', 'longitude': '-79.4881'};
		USStateLatLon['US-VT'] = {'latitude': '43.8435', 'longitude': '-72.4281'};
		USStateLatLon['US-WA'] = {'latitude': '47.2986', 'longitude': '-120.6355'};
		USStateLatLon['US-WI'] = {'latitude': '44.7161', 'longitude': '-89.8982'};
		USStateLatLon['US-WV'] = {'latitude': '38.8959', 'longitude': '-80.1191'};
		USStateLatLon['US-WY'] = {'latitude': '43.0265', 'longitude': '-107.5332'};
		
		var areaImages = [];
		
		for ( index in chartData ) {
			
			var state = chartData[ index ];
			
			if(jQuery.inArray(state.id, validUSStates) > -1){
				
				var stateLatLong = USStateLatLon[state.id];
				
				areaImages.push({
					id: state.id,
					value: state.value,
					"label": state.id.split( '-' ).pop(),
					"labelPosition": "center",
					latitude : stateLatLong['latitude'],	
					longitude : stateLatLong['longitude'],
					balloonText :""
				});
			}
		}
		
		var map = AmCharts.makeChart("us_heat_map_chart", {
			type: "map",
			colorSteps: 10,
			theme: "light",
			dataProvider: {
				map: "usaTerritoriesLow",
				areas:chartData,
				getAreasFromMap: true,
				images: areaImages
			},
			"areasSettings": {
				"autoZoom": false,
				"balloonText": "[[title]]: <b>[[value]]</b>",
				"rollOverColor" : undefined,
			},
			"imagesSettings": {
				"labelColor": "#fff",
				"labelPosition": "middle",
				"labelFontSize": 8
			},
			"valueLegend": {
				"right": 10,
				"minValue": "little",
				"maxValue": "a lot!"
			},
			"export": {
				"enabled": true
			},
		});
	}
});


Vtiger_Widget_Js('Vtiger_Trailingaum_Widget_Js',{},{


	generateChartData : function() {
		
		var revenueWidgetContainer = $(document).find("#trailing_aum_chart");
	
		var assetChartData = ($(revenueWidgetContainer).find(".widgetData").val());
    
		if(typeof(assetChartData) !== "undefined")
			var assetChartData = $.parseJSON(assetChartData);
		else
			assetChartData = false;
			
		return assetChartData;
	},
    
     postLoadWidget: function() {
		this._super();
        var thisInstance = this;
	},

	loadChart : function() {
	
		var chartData = this.generateChartData();

		if(!chartData) return false;
		
		var chart;
		chart = new AmCharts.AmSerialChart();
		chart.dataProvider = chartData;
		chart.categoryField = "date";
		chart.marginTop = 25;
		chart.marginBottom = 80;
		chart.marginLeft = 50;
		chart.marginRight = 100;
		chart.startDuration = 1;
		chart.sequencedAnimation = false;

		var valueAxis = new AmCharts.ValueAxis();
		valueAxis.minimum = 0;
		chart.addValueAxis(valueAxis);

		var catAxis = chart.categoryAxis;
		catAxis.gridPosition = "start";
		catAxis.gridCount = chartData.length;
		catAxis.labelRotation = 90;

		var graph = new AmCharts.AmGraph();
		graph.valueField = "value";
		graph.balloonText = "Total: $[[value]]";
		graph.numberFormatter = {precision: 2, decimalSeparator:".", thousandsSeparator:","};
		graph.type = "line";
		graph.lineColor = "#000033";
		graph.bullet = 'round';
		chart.addGraph(graph);

		var graph2 = new AmCharts.AmGraph();
		graph2.valueField = "cash_value";
		graph2.balloonText = "Cash: $[[value]]";
		graph2.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
		graph2.type = "line";
		graph2.lineColor = '#02B90E';
		graph2.bullet = 'round';
		chart.addGraph(graph2);

		var graph3 = new AmCharts.AmGraph();
		graph3.valueField = "fixed_income";
		graph3.balloonText = "Fixed Income: $[[value]]";
		graph3.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
		graph3.type = "line";
		graph3.lineColor = '#8383ff';
		graph3.bullet = 'round';
		chart.addGraph(graph3);

		var graph4 = new AmCharts.AmGraph();
		graph4.valueField = "equities";
		graph4.balloonText = "Equities: $[[value]]";
		graph4.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
		graph4.type = "line";
		graph4.lineColor = '#6bd7d6';
		graph4.bullet = 'round';
		chart.addGraph(graph4);

		chart.write("position_trailing_aum");
	}
});


Vtiger_Widget_Js('Vtiger_AmChartFunnel_Widget_Js',{},{
	
	generateChartData : function() {
		
		var container = this.getContainer();
		var jsonData = container.find('.widgetData').val();
		
		if(typeof jsonData != 'undefined' && jsonData != '')
			jsonData = JSON.parse(jsonData);
		else	
			jsonData = {};
		
		return jsonData;
	},
    
	loadChart : function() {

		var chartData = this.generateChartData();

		if(!chartData) return false;
		
		var chart;
		
		chart = new AmCharts.AmFunnelChart();
		chart.theme = "light";
		chart.titleField = "title";
		chart.marginRight = 30;
		chart.valueField = "value";
		chart.dataProvider = chartData;
		chart.neckWidth = "40%";
		chart.startAlpha = 0;
		chart.valueRepresents = "area";
                
		chart.urlField = "url";
		chart.urlTarget = "_blank";

		chart.balloonText = "[[title]]:<b>[[value]]</b>";

		chart.labelsEnabled = false;

		legend = new AmCharts.AmLegend();
        legend.position = "right";
		legend.horizontalGap = 10;
		legend.labelText = "[[title]]:";
		legend.valueWidth = 100;
        chart.addLegend(legend);

		chart.write("funnel_chart_container");
	}
});
	

Vtiger_Widget_Js('Vtiger_AmChartPie_Widget_Js',{},{

	generateChartData : function() {
		
		var container = this.getContainer();
		var jData = container.find('.widgetData').val();
		var chartData = JSON.parse(jData);
		return chartData;
	},
    
     postLoadWidget: function() {
		this._super();
        var thisInstance = this;
	},

	loadChart : function() {
		
		var container = this.getContainer();
		
		var chartData = this.generateChartData();
		
		var chartDiv = container.find(".widgetChartContainer").attr("id");
        
		var chart;
        var legend;

        chart = new AmCharts.AmPieChart();
        chart.type = "pie";
        chart.theme = "light";
		chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
		chart.colorField = "color";
        chart.balloonText = "[[title]]<br><span style='font-size:14px'><b>[[value]]</b> ([[percents]]%)</span>";
        chart.outlineColor = "#FFFFFF";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;

		chart.urlField = "url";
		chart.urlTarget = "_blank";
		chart.labelText = "";
		chart.responsive = {
			"enabled": false
		};
		chart.radius = "45%";
		
		chart.numberFormatter = {
		  precision:2,decimalSeparator:".",thousandsSeparator:","
		};
		
		legend = new AmCharts.AmLegend();
		legend.horizontalGap = 10;
		legend.position = "right";
		legend.valueText = "$[[value]]";
		legend.valueAlign = "left";
		legend.marginRight = 120;
		legend.autoMargins = false;
		chart.addLegend(legend);
		
		chart.write(chartDiv);
    }

});
