var Portfolio_Account_JS = {
	
	sendXMLHttpRequest : function(params) {

		var aDeferred = jQuery.Deferred();

		var xhttp;

		if (window.XMLHttpRequest) {
			xhttp = new XMLHttpRequest();
		} else {
			xhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}

		xhttp.onreadystatechange = function() {
			if (xhttp.readyState == 4 && xhttp.status == 200) {
				aDeferred.resolve(xhttp.responseText);
			}
		};

		xhttp.open("POST", "portalAjax.php", true);

		xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

		xhttp.send(params);

		return aDeferred.promise();
	},
	
	registerAccountReportActionEvent : function(){
		
		var thisInstance = this;
		
		jQuery(".load_reports a").on("click", function(e){
		
			e.preventDefault();
				
			App.blockUI({animate: true});
				
			var loadReport = jQuery(e.currentTarget).attr("id");
			
			var account_number = jQuery("#portfolio_account_number").val();
			
			var params = {
				"module" : "Reports",
				"action" : "loadPortalReports",
				"loadReport" : loadReport,
				"account_number" : account_number,
			};
			
			thisInstance.sendXMLHttpRequest($.param(params)).then(function(data){
				
				App.unblockUI();
				
				jQuery(".portlet-body").html(data);
				
					if(loadReport == "holdings" ){
						//thisInstance.registerReportPieChartEvent();
						thisInstance.registerHoldingsDynamicPieChart();
						thisInstance.registerReportAUMChartEvent();
						thisInstance.registerReportHistoricalChartEvent();
						thisInstance.registerReportRevenueChartEvent();
					
					}else if( loadReport == "assetclassreport"){
						thisInstance.registerHoldingsDynamicPieChart();
						thisInstance.registerAssetReportFilterEvent();
						thisInstance.registerFilterReportClickEvent();
					}
				 else if(loadReport == "overview"){
					thisInstance.registerHoldingsDynamicPieChart();
					thisInstance.CreateGraph("dynamic_chart_holder", "t12_balances", "intervalenddateformatted", "intervalendvalue");
				} else if(loadReport == 'incomelastyear' || loadReport == 'omniprojected' || loadReport == 'omniincome'){
					thisInstance.CreateGraph("dynamic_chart_holder", "estimate_graph_values", "category", "value");
				} else if(loadReport == "ghreport" || loadReport == 'gh2report'){
					thisInstance.CreatePie("dynamic_pie_holder", "holdings_values");
					thisInstance.CreatePie("sector_pie_holder", "sector_values", 'table');
					thisInstance.registerGHReportFilterEvent();
					thisInstance.registerFilterReportClickEvent();
				}
			});

		});
	},
	
	registerReportPieChartEvent: function(){
        if($("#report_top_pie").length == 0)
            return;
        var chart;
        var legend;

        var chartData = $.parseJSON($("#pie_values").val());

        chart = new AmCharts.AmPieChart();
        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = -30;
        chart.radius = 100;
        chart.labelText = "";//"[[percents]]";
        chart.textColor= "#FFFFFF";
        chart.depth3D = 14;
        chart.angle = 25;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;
        chart.labelsEnabled = false;
        chart.autoMargins = false;
        chart.marginTop = 0;
        chart.marginBottom = 0;
        chart.marginLeft = 0;
        chart.marginRight = 0;
        chart.pullOutRadius = 0;
        chart.responsive = {
		  "enabled": true
		};
        legend = new AmCharts.AmLegend();
        legend.align = "left";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "right";
        legend.valueText = "$[[value]]";
        legend.valueWidth = 100;
        legend.switchable = false;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend);
        chart.write("report_top_pie");
		
		this.registerReportPieChartDescriptionTop();
    },

    registerReportAUMChartEvent: function(){
		
		 if($("#trailing_aum").length == 0)
            return;
        
		var chart;
		
		var chartData = $.parseJSON($("#trailing_aum_values").val());
		
		if(typeof chartData == 'undefined')
			return;
			
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;
        chart.sequencedAnimation = false;

        chart.responsive = {
		  "enabled": true
		};
        
        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;

        var graph = new AmCharts.AmGraph();
        graph.title = "Value";
        graph.valueField = "value";
        graph.balloonText="Total: $[[value]]";
        graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph.type = "line";
        graph.lineColor = "#000033";
        graph.lineThickness = 3;
        chart.addGraph(graph);

        var graph2 = new AmCharts.AmGraph();
        graph2.title = "Cash";
        graph2.valueField = "cash_value";
        graph2.balloonText="Cash: $[[value]]";
        graph2.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph2.type = "line";
        graph2.lineColor = '#02B90E';
        graph2.lineThickness = 3;
        chart.addGraph(graph2);

        var graph3 = new AmCharts.AmGraph();
        graph3.title = "Fixed Income";
        graph3.valueField = "fixed_income";
        graph3.balloonText="Fixed Income: $[[value]]";
        graph3.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph3.type = "line";
        graph3.lineColor = '#8383ff';
        graph3.lineThickness = 3;
        chart.addGraph(graph3);

        var graph4 = new AmCharts.AmGraph();
        graph4.title = "Equities";
        graph4.valueField = "equities";
        graph4.balloonText="Equities: $[[value]]";
        graph4.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph4.type = "line";
        graph4.lineColor = '#6bd7d6';
        graph4.lineThickness = 3;
        chart.addGraph(graph4);

        var legend = new AmCharts.AmLegend();
        legend = new AmCharts.AmLegend();
        legend.position = "top";
        legend.align = "center";
        legend.markerType = "square";
        legend.valueWidth = 100;
        chart.addLegend(legend);

        chart.write("trailing_aum");
    },

    registerReportHistoricalChartEvent: function(){
        
		var chartData = $("#history_chart").val();
		
		if(typeof chartData == 'undefined' || !jQuery(document).find("#historical_graph").length)
			return;
			
		chartData = $.parseJSON(chartData);
		
        var chart = AmCharts.makeChart( "historical_graph", {
            "type": "serial",
            "theme": "light",
            "depth3D": 20,
            "angle": 30,
            "dataProvider": chartData,
            "valueAxes": [{
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0,
                "minimum": 0
            }],
            "gridAboveGraphs": true,
            "startDuration": 1,
            "graphs": [ {
                "balloonText": "[[category]]: <b>[[value]]</b>",
                "fillAlphas": 0.8,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "value"
            } ],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "date",
            "categoryAxis": {
                "labelRotation":90,
                "gridPosition": "start",
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
            },
            "export": {
                "enabled": true,
                "fileName": "historyGraph",
                backgroundColor: "transparent",
                backgroundAlpha: 0.3,
                "menu": []
            },
			"responsive": {
				"enabled": true
			}

        });
    },

    registerReportRevenueChartEvent: function(){
        
		var chartData = $("#trailing_revenue_values").val();
		
		if(typeof chartData == 'undefined')
			return;
		
		chartData = $.parseJSON(chartData);
			
        var chart = AmCharts.makeChart("trailing_revenue_graph", {
            "type": "serial",
            "theme": "light",
            "depth3D": 20,
            "angle": 30,
            "dataProvider": chartData,
            "valueAxes": [ {
                "gridColor": "#FFFFFF",
                "gridAlpha": 0.2,
                "dashLength": 0,
                "minimum": 0
            } ],
            "gridAboveGraphs": true,
            "startDuration": 1,
            "graphs": [ {
                "balloonText": "[[category]]: <b>[[value]]</b>",
                "fillAlphas": 0.5,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "value",
            } ],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "date",
            "categoryAxis": {
                "labelRotation":90,
                "gridPosition": "start",
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
            },
            "responsive": {
				"enabled": true
			},
            "export": {
                "enabled": true,
                "fileName": "revenueGraph",
                backgroundColor: "transparent",
                backgroundAlpha: 0.3,
                "menu": []
            }

        } );
    },
	
	registerIncomeChart : function(chartData, element){
		var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;

        chart.responsive = {
  		  "enabled": true
  		};
        
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
        graph.fillColors = "#00FF00";
        chart.addGraph(graph);
		chart.angle = 30;
        chart.depth3D = 15;

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;
        chart.write(element);
	},	
	
	registerMonthlyIncomeReportChart : function(){
		var thisInstance = this;
		var history_value = ($(document).find("[name=history_chart]").val());
		if(typeof(history_value) !== 'undefined' && history_value != ''){
			var history_values = $.parseJSON(history_value);
			var historyChartData = history_values;
			thisInstance.registerIncomeChart(historyChartData, "history_chart");
		}
	},
	
	registerReportPieChartDescriptionTop : function(){
		
		var Legend = jQuery(document).find(".amChartsLegend");
		
		if( Legend.length > 0 ){
			
			var marginTop = parseInt(jQuery(Legend).css("top"), 10);
			
			if(marginTop > 0)
				jQuery(document).find(".report_desc").css("margin-top", marginTop-50+'px');
		}
	},
	
	registerHoldingsDynamicPieChart : function(){
        
		if($("#estimate_dynamic_pie").length == 0)
            return;
        
		var chart;
        var legend;

        var chartData = $("#dynamic_pie_values").val();
		
		if(typeof chartData == 'undefined' || chartData == '' || chartData == 'null'){
			return;
		}
		
		chartData = $.parseJSON(chartData);
		
        chart = new AmCharts.AmPieChart();
        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        chart.labelRadius = -30;
        chart.radius = 80;
        chart.labelText = "";//"[[percents]]";
        chart.textColor= "#FFFFFF";
        chart.depth3D = 14;
        chart.angle = 25;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        
		chart.startDuration = 0;

		chart.responsive = {
  		  "enabled": true
  		};
        
		legend = new AmCharts.AmLegend();
        legend.align = "left";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "right";
        legend.marginRight = 20;
        legend.valueText = "$[[value]]";
        legend.valueWidth = 100;
        legend.switchable = false;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend);//, 'report_top_pie_legend');
        // WRITE
        chart.write('estimate_dynamic_pie');
		
		this.registerReportPieChartDescriptionTop();
    },
	
	registerEvents : function(){
		this.registerAccountReportActionEvent();
		this.registerReportPieChartEvent();
		this.registerReportAUMChartEvent();
		this.registerReportHistoricalChartEvent();
		this.registerReportRevenueChartEvent();
		this.registerHoldingsDynamicPieChart();
		this.registerMonthlyIncomeReportChart(); // Omniver : 2017-05-05 Change for contact allowed reports 
		this.CreateGraph("dynamic_chart_holder", "t12_balances", "intervalenddateformatted", "intervalendvalue");
		this.CreateGraph("dynamic_chart_holder", "estimate_graph_values", "category", "value");
		this.CreatePie("dynamic_pie_holder", "holdings_values");
		this.CreatePie("sector_pie_holder", "sector_values", 'table');
		this.registerGHReportFilterEvent();
		this.registerFilterReportClickEvent();
    },
	
	CreateGraph: function(holder, value_source, category_field, value_field){
        if($("#"+holder).length == 0)
            return;

        var chart;
        var chartData = $("#"+value_source).val();
		
		if(typeof chartData == 'undefined' || chartData == '' || chartData == 'null'){
			return;
		}
		
		chartData = $.parseJSON(chartData);

        chart = new AmCharts.AmSerialChart();

        chart.dataProvider = chartData;
        chart.categoryField = category_field;
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var graph = new AmCharts.AmGraph();
        graph.valueField = value_field;
        graph.balloonText="[[category]]: $[[value]]";
        graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph.type = "column";
        graph.lineAlpha = 0;
        graph.fillAlphas = 1;
        graph.fillColors = "#02B90E";
        chart.addGraph(graph);
        chart.angle = 30;
        chart.depth3D = 10;

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;
        chart.write(holder);
    },
	
	CreatePie: function(holder, value_source, showLegend = true){
		var thisInstance = this;
		if($("#"+holder).length == 0)
            return;

		if(showLegend == 'table'){
			
			AmCharts.addInitHandler(function(chart) {

				if (jQuery("#gh2_AllocationTypesWrapper").length == 0)
					return;
				
				var data = chart.dataProvider;
				
				var tableHolder = jQuery("#gh2_AllocationTypesWrapper");
				
				jQuery("#gh2_AllocationTypesWrapper").addClass("table-responsive");
				
				var table = "<table class='table table-bordered'><thead><tr><th>Description</th><th>Weight</th><th>Value</th></tr></thead><tbody>";

				for (var i = 0; i < chart.dataProvider.length; i++) {
					
					var dp = chart.dataProvider[i];
					
					table += '<tr style="background-color:'+dp.color+'; color:white;">';
					table += '<td>' + dp.title + '</td>';
					table += '<td>' + dp.percentage + '%</td>';
					table += '<td>$' + thisInstance.numberFormat(dp.value, 2, '.', ',') + '</td>';
					table += '</tr>';
				}
				
				table += '</tbody></table>';
				
				tableHolder.html(table);
				
			}, ["pie"]);
		}
		
        var chart;
        var legend;

        var chartData = $.parseJSON($("#"+value_source).val());

        chart = new AmCharts.AmPieChart();
        chart.export = {enabled:"true",
            libs: {
                path: "libraries/amcharts/amcharts/plugins/export/libs/"
            },
            backgroundColor: "transparent",
            backgroundAlpha: 0.3 ,
            menu:[],
            fileName:holder
        };
        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        chart.labelRadius = -30;
        chart.radius = 80;
        chart.labelText = "";
        chart.textColor= "#FFFFFF";
        chart.depth3D = 14;
        chart.angle = 25;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;

        if(showLegend == true) {
            legend = new AmCharts.AmLegend();
            legend.align = "left";
            legend.markerType = "square";
            legend.maxColumns = 1;
            legend.position = "right";
            legend.marginRight = 20;
            legend.valueText = "$[[value]]";
            legend.valueWidth = 100;
            legend.switchable = false;
            legend.labelText = "[[title]]:";
            chart.addLegend(legend);
        }
        // WRITE
        chart.write(holder);
		if(holder == 'dynamic_pie_holder')
			this.registerReportPieChartDescriptionTop();
		
    },
	
	registerGHReportFilterEvent : function(){
		
		jQuery("#select_start_date").datepicker({
			format : 'MM yyyy',
			startView : 1,
			minViewMode: 1,
			maxViewMode: 2,
		});

        jQuery("#select_end_date").datepicker({
            format : 'MM yyyy',
			startView : 1,
			minViewMode: 1,
			maxViewMode: 2,
		});

        $("#report_date_selection").click(function(e){
            e.stopImmediatePropagation();

            var selected = $("#report_date_selection").find(':selected');
            var start_date = selected.data('start_date');
            var end_date = selected.data('end_date');

            $("#select_start_date").val(start_date);
            $("#select_end_date").val(end_date);
        });
	},
	
	registerFilterReportClickEvent : function(){
		
		var thisInstance = this;
		
		$("#calculate_report").click(function(e){
            
			e.stopImmediatePropagation();
            
			App.blockUI({animate: true});
			
			sdate = $("#select_start_date").val();
            
			edate = $("#select_end_date").val();
			
			var account_number = jQuery("#portfolio_account_number").val();
			
			var loadReport = jQuery("#report_type").val();
			
			var params = {
				"module" : "Reports",
				"action" : "loadPortalReports",
				"loadReport" : loadReport,
				"account_number" : account_number,
				"reportStartDate" : sdate,
				"reportEndDate" : edate,
				"selectedDate" : jQuery("#report_date_selection").val()
			};
			
			thisInstance.sendXMLHttpRequest($.param(params)).then(function(data){
				
				App.unblockUI();
				
				jQuery(".portlet-body").html(data);
				
				if(loadReport == 'assetclassreport'){
					thisInstance.registerHoldingsDynamicPieChart();
					thisInstance.registerAssetReportFilterEvent();
					thisInstance.registerFilterReportClickEvent();
				}else{
					thisInstance.CreatePie("dynamic_pie_holder", "holdings_values");
					thisInstance.CreatePie("sector_pie_holder", "sector_values", 'table');
					thisInstance.registerGHReportFilterEvent();
					thisInstance.registerFilterReportClickEvent();
				}
			});
			return false;
        });
	},
	
	registerAssetReportFilterEvent : function(){
		
		jQuery("#select_start_date").datepicker();

        jQuery("#select_end_date").datepicker();

        $("#report_date_selection").click(function(e){
            e.stopImmediatePropagation();

            var selected = $("#report_date_selection").find(':selected');
            var start_date = selected.data('start_date');
            var end_date = selected.data('end_date');

            $("#select_start_date").val(start_date);
            $("#select_end_date").val(end_date);
        });
	},
	 
	numberFormat : function (number, decimals, dec_point, thousands_sep) {
		
        number = parseFloat(number).toFixed(decimals);

        var nstr = number.toString();
        nstr += '';
        x = nstr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? dec_point + x[1] : '';
        var rgx = /(\d+)(\d{3})/;

        while (rgx.test(x1))
            x1 = x1.replace(rgx, '$1' + thousands_sep + '$2');

        return x1 + x2;
    }
};

jQuery("document").ready(function(){
	Portfolio_Account_JS.registerEvents();
});