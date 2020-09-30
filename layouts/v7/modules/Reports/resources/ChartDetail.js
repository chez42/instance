/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Reports_Detail_Js("Reports_ChartDetail_Js", {
	
	/**
	 * Function used to display message when there is no data from the server
	 */
	displayNoDataMessage: function () {
		$('#chartcontent').html('<div>' + app.vtranslate('JS_NO_CHART_DATA_AVAILABLE') + '</div>').css(
				{'text-align': 'center', 'position': 'relative', 'top': '100px'});
	},
	
	/**
	 * Function returns if there is no data from the server
	 */
	isEmptyData: function () {
		var jsonData = jQuery('input[name=data]').val();
		var data = JSON.parse(jsonData);
		var values = data['values'];
		if (jsonData == '' || values == '') {
			return true;
		}
		return false;
	}
	
}, {
	
	/**
	 * Function returns instance of the chart type
	 */
	getInstance: function () {
		var chartType = jQuery('input[name=charttype]').val();
		var chartClassName = chartType.toLowerCase().replace(/\b[a-z]/g, function(letter) {
			return letter.toUpperCase();
		});
		var chartClass = window["Report_" + chartClassName + "_Js"];

		var instance = false;
		if (typeof chartClass != 'undefined')
			instance = new chartClass();
		return instance;
	},
	
	registerSaveOrGenerateReportEvent: function () {
		var thisInstance = this;
		jQuery('.generateReportChart').on('click', function (e) {
			var advFilterCondition = thisInstance.calculateValues();
			var recordId = thisInstance.getRecordId();
			var currentMode = jQuery(e.currentTarget).data('mode');
			var groupByField = jQuery('#groupbyfield').val();
			var dataField = jQuery('#datafields').val();
			if(dataField == null || dataField == '') {
				vtUtils.showValidationMessage(jQuery('#datafields').parent().find('.select2-choices'), app.vtranslate('JS_REQUIRED_FIELD'));
				return false;
			} else {
				vtUtils.hideValidationMessage(jQuery('#datafields').parent().find('.select2-choices'));
			}
			
			if(groupByField == null || groupByField == "") {
				vtUtils.showValidationMessage(jQuery('#groupbyfield').parent().find('.select2-container'), app.vtranslate('JS_REQUIRED_FIELD'));
				return false;
			} else {
				vtUtils.hideValidationMessage(jQuery('#groupbyfield').parent().find('.select2-container'));
			}
			
			var orderField = jQuery('#orderby').val();
			
			var postData = {
				'advanced_filter': advFilterCondition,
				'record': recordId,
				'view': "ChartSaveAjax",
				'module': app.getModuleName(),
				'mode': currentMode,
				'charttype': jQuery('input[name=charttype]').val(),
				'groupbyfield': groupByField,
				'datafields': dataField,
				'orderby':orderField
			};

			var reportChartContents = thisInstance.getContentHolder().find('#reportContentsDiv');
			app.helper.showProgress();
			e.preventDefault();
			app.request.post({data: postData}).then(
					function (error, data) {
						app.helper.hideProgress();
						reportChartContents.html(data);
						thisInstance.registerEventForChartGeneration();
						jQuery('.reportActionButtons').addClass('hide');
					}
			);
		});
	},
	
	registerEventForChartGeneration: function () {
		var thisInstance = this;
		try {
			thisInstance.getInstance();	// instantiate the object and calls init function
			jQuery('#chartcontent').trigger(Vtiger_Widget_Js.widgetPostLoadEvent);
		} catch (error) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return;
		}
	},
	
        savePinToDashBoard : function(customParams) {
            var element = jQuery('button.pinToDashboard');
            var recordId = this.getRecordId();
            var primarymodule = jQuery('input[name="primary_module"]').val();
            var widgetTitle = 'ChartReportWidget_' + primarymodule + '_' + recordId;
            var params = {
                    module: 'Reports',
                    action: 'ChartActions',
                    mode: 'pinChartToDashboard',
                    reportid: recordId,
                    title: widgetTitle
            };
            params = jQuery.extend(params, customParams);
            app.request.post({data: params}).then(function (error,data) {
                    if (data.duplicate) {
                            var params = {
                                    message: app.vtranslate('JS_CHART_ALREADY_PINNED_TO_DASHBOARD', 'Reports')
                            };
                            app.helper.showSuccessNotification(params);
                    } else {
                            var message = app.vtranslate('JS_CHART_PINNED_TO_DASHBOARD', 'Reports');
                            app.helper.showSuccessNotification({message:message});
                            element.find('i').removeClass('vicon-pin');
                            element.find('i').addClass('vicon-unpin');
                            element.removeClass('dropdown-toggle').removeAttr('data-toggle');
                            element.attr('title', app.vtranslate('JSLBL_UNPIN_CHART_FROM_DASHBOARD'));
                    }
            });
        },
	
	registerEventForPinChartToDashboard: function () {
		var thisInstance = this;
		jQuery('button.pinToDashboard').on('click', function (e) {
			var element = jQuery(e.currentTarget);
			var recordId = thisInstance.getRecordId();
			var pinned = element.find('i').hasClass('vicon-pin');
			if(pinned) {
                                if(element.is('[data-toggle]')){
                                    return;
                                }else{
                                    thisInstance.savePinToDashBoard();
                                }
			} else {
				var params = {
					module: 'Reports',
					action: 'ChartActions',
					mode: 'unpinChartFromDashboard',
					reportid: recordId
				};
				app.request.post({data: params}).then(function (error,data) {
					if(data.unpinned) {
						var message = app.vtranslate('JS_CHART_REMOVED_FROM_DASHBOARD', 'Reports');
						app.helper.showSuccessNotification({message:message});
						element.find('i').removeClass('vicon-unpin');
						element.find('i').addClass('vicon-pin');
                                                if(element.data('dashboardTabCount') >1) {
                                                    element.addClass('dropdown-toggle').attr('data-toggle','dropdown');
                                                }
						element.attr('title', app.vtranslate('JSLBL_PIN_CHART_TO_DASHBOARD'));
					}
				});
			}
		});
                
                jQuery('button.pinToDashboard').closest('.btn-group').find('.dashBoardTab').on('click',function(e){
                    var dashBoardTabId = jQuery(e.currentTarget).data('tabId');
                    thisInstance.savePinToDashBoard({'dashBoardTabId':dashBoardTabId});
                });
	},
	
	registerEvents: function () {
		this._super();
		this.registerEventForChartGeneration();
		Reports_ChartEdit3_Js.registerFieldForChosen();
		Reports_ChartEdit3_Js.initSelectValues();
		this.registerEventForPinChartToDashboard();
		var chartEditInstance = new Reports_ChartEdit3_Js();
		chartEditInstance.lineItemCalculationLimit();
	}
});

/*
Vtiger_Pie_Widget_Js('Report_Piechart_Js', {}, {
	
	postInitializeCalls: function () {
		var thisInstance = this;
		var clickThrough = jQuery('input[name=clickthrough]', this.getContainer()).val();
		if (clickThrough != '') {
			thisInstance.getContainer().off('vtchartClick').on('vtchartClick', function (e, data) {
				if (data.url)
					thisInstance.openUrl(data.url);
			});
		}
	},
	
	postLoadWidget: function () {
		if (!Reports_ChartDetail_Js.isEmptyData()) {
			this.loadChart();
		} else {
			this.positionNoDataMsg();
		}
		this.postInitializeCalls();
		this.restrictContentDrag();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		if (widgetContent.length) {
			if (!jQuery('input[name=clickthrough]', this.getContainer()).val()) {
				var adjustedHeight = this.getContainer().height() - 50;
				app.helper.showVerticalScroll(widgetContent, {'height': adjustedHeight});
			}
			widgetContent.css({height: widgetContent.height() - 100});
		}
	},
	
	positionNoDataMsg: function () {
		Reports_ChartDetail_Js.displayNoDataMessage();
	},
	
	getPlotContainer: function (useCache) {
		if (typeof useCache == 'undefined') {
			useCache = false;
		}
		if (this.plotContainer == false || !useCache) {
			var container = this.getContainer();
			this.plotContainer = jQuery('div[name="chartcontent"]', container);
		}
		return this.plotContainer;
	},
	
	init: function (parent) {
		if (parent) {
			this._super(parent);
		} else {
			this._super(jQuery('#reportContentsDiv'));
		}
	},
	
	generateData: function () {
		if (Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}

		var jsonData = jQuery('input[name=data]', this.getContainer()).val();
		var data = this.data = JSON.parse(jsonData);
		var values = data['values'];

		var chartData = [];
		for (var i in values) {
			chartData[i] = [];
			chartData[i].push(data['labels'][i]);
			chartData[i].push(values[i]);
		}
		return {'chartData': chartData,
			'labels': data['labels'],
			'data_labels': data['data_labels'],
			'data_type'	: data['data_type'],
			'title': data['graph_label']};
	},
	
	generateLinks: function () {
		var jData = jQuery('input[name=data]', this.getContainer()).val();
		var statData = JSON.parse(jData);
		var links = statData['links'];
		return links;
	}

});

Vtiger_Barchat_Widget_Js('Report_Verticalbarchart_Js', {}, {
	
	postInitializeCalls: function () {
		var thisInstance = this;
		var clickThrough = jQuery('input[name=clickthrough]', this.getContainer()).val();
		if (clickThrough != '') {
			thisInstance.getContainer().off('vtchartClick').on('vtchartClick', function (e, data) {
				if (data.url)
					thisInstance.openUrl(data.url);
			});
		}
	},
	
	postLoadWidget: function () {
		if (!Reports_ChartDetail_Js.isEmptyData()) {
			this.loadChart();
		} else {
			this.positionNoDataMsg();
		}
		this.postInitializeCalls();
		this.restrictContentDrag();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		if (widgetContent.length) {
			if (!jQuery('input[name=clickthrough]', this.getContainer()).val()) {
				var adjustedHeight = this.getContainer().height() - 50;
				app.helper.showVerticalScroll(widgetContent, {'height': adjustedHeight});
			}
			widgetContent.css({height: widgetContent.height() - 100});
		}
	},
	
	positionNoDataMsg: function () {
		Reports_ChartDetail_Js.displayNoDataMessage();
	},
	
	getPlotContainer: function (useCache) {
		if (typeof useCache == 'undefined') {
			useCache = false;
		}
		if (this.plotContainer == false || !useCache) {
			var container = this.getContainer();
			this.plotContainer = jQuery('div[name="chartcontent"]', container);
		}
		return this.plotContainer;
	},
	
	init: function (parent) {
		if (parent) {
			this._super(parent);
		} else {
			this._super(jQuery('#reportContentsDiv'));
		}
	},
	
	generateChartData: function () {
		if (Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}

		var jsonData = jQuery('input[name=data]', this.getContainer()).val();
		var data = this.data = JSON.parse(jsonData);
		var values = data['values'];

		var chartData = [];
		var yMaxValue = 0;

		if (data['type'] == 'singleBar') {
			chartData[0] = [];
			for (var i in values) {
				var multiValue = values[i];
				for (var j in multiValue) {
					chartData[0].push(multiValue[j]);
					if (multiValue[j] > yMaxValue)
						yMaxValue = multiValue[j];
				}
			}
		} else {
			for (var i in values) {
				var multiValue = values[i];
				var info = [];
				for (var j in multiValue) {
					if (typeof chartData[j] != 'undefined') {
						chartData[j].push(multiValue[j]);
					} else {
						chartData[j] = [];
						chartData[j].push(multiValue[j]);
					}
					if (multiValue[j] > yMaxValue)
						yMaxValue = multiValue[j];
				}
			}
		}
		yMaxValue = yMaxValue + (yMaxValue * 0.15);

		return {'chartData': chartData,
			'yMaxValue': yMaxValue,
			'labels': data['labels'],
			'data_labels': data['data_labels'],
			'data_type'	: data['data_type'],
			'title': data['graph_label']
		};
	},
	
	generateLinks: function () {
		var jData = jQuery('input[name=data]', this.getContainer()).val();
		var statData = JSON.parse(jData);
		var links = statData['links'];
		return links;
	}
});

Report_Verticalbarchart_Js('Report_Horizontalbarchart_Js', {}, {
	
	generateChartData: function () {
		if (Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}
		var jsonData = jQuery('input[name=data]', this.getContainer()).val();
		var data = this.data = JSON.parse(jsonData);
		var values = data['values'];

		var chartData = [];
		var yMaxValue = 0;

		if (data['type'] == 'singleBar') {
			for (var i in values) {
				var multiValue = values[i];
				chartData[i] = [];
				for (var j in multiValue) {
					chartData[i].push(multiValue[j]);
					chartData[i].push(parseInt(i) + 1);
					if (multiValue[j] > yMaxValue) {
						yMaxValue = multiValue[j];
					}
				}
			}
			chartData = [chartData];
		} else {
			chartData = [];
			for (var i in values) {
				var multiValue = values[i];
				for (var j in multiValue) {
					if (typeof chartData[j] != 'undefined') {
						chartData[j][i] = [];
						chartData[j][i].push(multiValue[j]);
						chartData[j][i].push(parseInt(i) + 1);
					} else {
						chartData[j] = []
						chartData[j][i] = [];
						chartData[j][i].push(multiValue[j]);
						chartData[j][i].push(parseInt(i) + 1);
					}

					if (multiValue[j] > yMaxValue) {
						yMaxValue = multiValue[j];
					}
				}
			}
		}
		yMaxValue = yMaxValue + (yMaxValue * 0.15);

		return {'chartData': chartData,
			'yMaxValue': yMaxValue,
			'labels': data['labels'],
			'data_labels': data['data_labels'],
			'data_type'	: data['data_type'],
			'title': data['graph_label']
		};

	},
	
	loadChart: function () {
		var data = this.generateChartData();
		var chartOptions = {
			renderer: 'horizontalbar'
		};
		if (this.data['links'])
			chartOptions.links = this.data['links'];
		this.getPlotContainer().vtchart(data, chartOptions);
		jQuery('table.jqplot-table-legend').css('width', '95px');
	}
});


Report_Verticalbarchart_Js('Report_Linechart_Js', {}, {
	
	generateData: function () {
		if (Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}

		var jsonData = jQuery('input[name=data]', this.getContainer()).val();
		var data = this.data = JSON.parse(jsonData);
		var values = data['values'];

		var chartData = [];
		var yMaxValue = 0;

		for (var i in values) {
			var value = values[i];
			for (var j in value) {
				if (typeof chartData[j] != 'undefined') {
					chartData[j].push(value[j]);
				} else {
					chartData[j] = []
					chartData[j].push(value[j]);
				}
			}
		}
		yMaxValue = yMaxValue + yMaxValue * 0.15;

		return {'chartData': chartData,
			'yMaxValue': yMaxValue,
			'labels': data['labels'],
			'data_labels': data['data_labels'],
			'data_type'	: data['data_type'],
			'title': data['graph_label']
		};
	},
	loadChart: function () {
		var data = this.generateData();
		var chartOptions = {
			renderer: 'linechart'
		};
		if (this.data['links'])
			chartOptions.links = this.data['links'];
		this.getPlotContainer().vtchart(data, chartOptions);
		jQuery('table.jqplot-table-legend').css('width', '95px');
	}
});*/

Vtiger_Pie_Widget_Js('Report_Piechart_Js',{},{

	postInitializeCalls: function () {
		var thisInstance = this;
		var clickThrough = jQuery('input[name=clickthrough]', this.getContainer()).val();
		if (clickThrough != '') {
			thisInstance.getContainer().off('vtchartClick').on('vtchartClick', function (e, data) {
				if (data.url)
					thisInstance.openUrl(data.url);
			});
		}
	},
	
	postLoadWidget: function () {
		if (!Reports_ChartDetail_Js.isEmptyData()) {
			this.loadChart();
		} else {
			this.positionNoDataMsg();
		}
		this.postInitializeCalls();
		this.restrictContentDrag();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		if (widgetContent.length) {
			if (!jQuery('input[name=clickthrough]', this.getContainer()).val()) {
				var adjustedHeight = this.getContainer().height() - 50;
				app.helper.showVerticalScroll(widgetContent, {'height': adjustedHeight});
			}
			widgetContent.css({height: widgetContent.height() - 100});
		}
	},
	
	positionNoDataMsg: function () {
		Reports_ChartDetail_Js.displayNoDataMessage();
	},
	
	getPlotContainer: function (useCache) {
		if (typeof useCache == 'undefined') {
			useCache = false;
		}
		if (this.plotContainer == false || !useCache) {
			var container = this.getContainer();
			this.plotContainer = jQuery('div[name="chartcontent"]', container);
		}
		return this.plotContainer;
	},
	
	init: function (parent) {
		if (parent) {
			this._super(parent);
		} else {
			this._super(jQuery('#reportContentsDiv'));
		}
	},
	
	generateChartData : function() {
		
		if(Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}

		var jsonData = jQuery('input[name=data]', this.getContainer()).val();
		
		jsonData = JSON.parse(jsonData);
	
		var data = {};
			
		if(!jQuery.isEmptyObject(jsonData)){
			
			data.graph_label = jsonData.graph_label;
			
			data.chartData = [];
			
			var labels = jsonData.labels;
			var values = jsonData.values;
			var links = jsonData.links;
			
			jQuery(labels).each(function(index, label){
				var obj = {
					title : label,
					value : values[index],
					url   : links[index]
				};
				data['chartData'].push(obj);
			});
		}
		
		this.data = data;
		
		return data;
	},
    
	loadChart : function() {
		
		var chartData = this.generateChartData();
		
		var graph_label = chartData.graph_label;
		
		chartData = chartData.chartData;

		if(!chartData) return false;
		
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        
		chart.titles=  [{
			"text" : graph_label,
			"size" : 15
		}];
		
		chart.theme = "light";
		
		chart.labelRadius = 30;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 15;
        chart.angle = 30;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;

		chart.urlField = "url";
		chart.urlTarget = "_blank";

		chart.radius = "25%";
		
        legend = new AmCharts.AmLegend();
        legend.position = "right";
		legend.horizontalGap = 10;
		legend.labelText = "[[title]]:";
		legend.valueWidth = 100;
        chart.addLegend(legend);
        
        if($("#"+this.getPlotContainer().attr('id'), this.getContainer()).length > 0) {
            chart.write(this.getPlotContainer().attr('id'), this.getContainer());
        }
	}
});

Vtiger_Barchat_Widget_Js('Report_Verticalbarchart_Js', {},{

	postInitializeCalls: function () {
		var thisInstance = this;
		var clickThrough = jQuery('input[name=clickthrough]', this.getContainer()).val();
		if (clickThrough != '') {
			thisInstance.getContainer().off('vtchartClick').on('vtchartClick', function (e, data) {
				if (data.url)
					thisInstance.openUrl(data.url);
			});
		}
	},
	
	postLoadWidget: function () {
		if (!Reports_ChartDetail_Js.isEmptyData()) {
			this.loadChart();
		} else {
			this.positionNoDataMsg();
		}
		this.postInitializeCalls();
		this.restrictContentDrag();
		var widgetContent = jQuery('.dashboardWidgetContent', this.getContainer());
		if (widgetContent.length) {
			if (!jQuery('input[name=clickthrough]', this.getContainer()).val()) {
				var adjustedHeight = this.getContainer().height() - 50;
				app.helper.showVerticalScroll(widgetContent, {'height': adjustedHeight});
			}
			widgetContent.css({height: widgetContent.height() - 100});
		}
	},
	
	positionNoDataMsg: function () {
		Reports_ChartDetail_Js.displayNoDataMessage();
	},
	
	getPlotContainer: function (useCache) {
		if (typeof useCache == 'undefined') {
			useCache = false;
		}
		if (this.plotContainer == false || !useCache) {
			var container = this.getContainer();
			this.plotContainer = jQuery('div[name="chartcontent"]', container);
		}
		return this.plotContainer;
	},
	
	init: function (parent) {
		if (parent) {
			this._super(parent);
		} else {
			this._super(jQuery('#reportContentsDiv'));
		}
	},

	generateChartData : function() {
		
		if(Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}

		var jsonData = jQuery('input[name=data]', this.getContainer()).val();
		
		jsonData = JSON.parse(jsonData);
		
		var data = {};
		
		if(!jQuery.isEmptyObject(jsonData)){
			
			data.graph_label = jsonData.graph_label;
			
			data.chartData = [];
			
			var labels = jsonData.labels;
			var values = jsonData.values;
			var links = jsonData.links;
			
			data.data_labels = jsonData.data_labels;
			
			data.bars = values[0].length;
			
			data.rotate = false;

			data.graph_type = "column";

			jQuery(labels).each(function(index, label){
				
				var obj = {
					title : label,
					url   : links[index],
				};
				
				jQuery(values[index]).each(function(key, val){
					obj["value"+key] = val;
				});
				
				data['chartData'].push(obj);
			});
		}
		
		this.data = data;
		
		return data;
	},
    
	loadChart : function() {

		var chartData = this.generateChartData();
		
		var graph_label = chartData.graph_label;
		
		var totalGraphs = chartData.bars;
		
		var legendLabels = chartData.data_labels;
		
		var chartRotate = chartData.rotate;
		
		var type = chartData.graph_type;
		
		chartData = chartData.chartData;
	
		if(!chartData) return false;
		
        var chart;
        
		var legend;

		var chart = new AmCharts.AmSerialChart();
		
		chart.rotate = chartRotate;
			
		chart.theme = "light";
		
		chart.categoryField = "title";
		
		if(chartRotate)
			chartData = chartData.sort().reverse();
		
		chart.dataProvider = chartData;
		
		var valueAxis = new AmCharts.ValueAxis();
        
		valueAxis.minimum = 0;
        
		chart.addValueAxis(valueAxis);
		
		for(i=0; i< totalGraphs; i++){
			
			var graph = new AmCharts.AmGraph();
			
			graph.valueField = "value"+i;
			graph.type = type;
			graph.title = legendLabels[i];
			graph.urlField = "url";
			graph.urlTarget = "_blank";
			
			if(type == 'line'){
				graph.bullet = 'round';
			} else {
				graph.lineAlpha = 0;
				graph.fillAlphas = 1;
			}
			
			chart.addGraph(graph);
		}
		
		chart.angle = 30;
        
		chart.depth3D = 15;
		
		chart.addTitle(graph_label);

		legend = new AmCharts.AmLegend();
        /*legend.align = "left";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "right";
        legend.marginRight = 20;
        legend.valueWidth = 100;
        legend.switchable = false;*/
        
		chart.addLegend(legend, 'legenddiv');
		
		var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 45;
		catAxis.gridAlpha = 0;
		catAxis.tickPosition = "start";
		catAxis.tickLength = 20;
		
		if($("#"+this.getPlotContainer().attr('id'), this.getContainer()).length > 0) {
            chart.write(this.getPlotContainer().attr('id'), this.getContainer());
        }
	}
});


Report_Verticalbarchart_Js('Report_Horizontalbarchart_Js', {},{
	
	generateChartData : function() {
		var data = this._super();
		data.rotate = true;
		return data;
	}
});


Report_Verticalbarchart_Js('Report_Linechart_Js', {},{

	generateChartData : function() {
		var data = this._super();
		data.graph_type = "line";
		return data;
	}
});
 

Vtiger_Widget_Js('Report_Heatchart_Js',{},{

	postLoadWidget : function() {
		if(!Reports_ChartDetail_Js.isEmptyData()) {
			this.loadChart();
		}else{
			this.positionNoDataMsg();
		}
	},

	positionNoDataMsg : function() {
		Reports_ChartDetail_Js.displayNoDataMessage();
	},

	init : function() {
		this._super(jQuery('#reportContentsDiv'));
	},

	generateChartData : function() {
		
		if(Reports_ChartDetail_Js.isEmptyData()) {
			Reports_ChartDetail_Js.displayNoDataMessage();
			return false;
		}

		var jsonData = jQuery('input[name=data]').val();
		
		data = JSON.parse(jsonData);
		
		this.data = data;
		
		return data;
	},
    
	loadChart : function() {
		
		var chartData = this.generateChartData();

		var graph_label = chartData.graph_label;
		
		chartData = chartData.chartData;

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
		
		var map = AmCharts.makeChart("chartcontent", {
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