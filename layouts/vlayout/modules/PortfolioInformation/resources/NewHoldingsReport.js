jQuery.Class("HoldingsReport_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new HoldingsReport_Module_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        $(".ExportReport").click(function(e){
            e.stopImmediatePropagation();
// Define IDs of the charts we want to include in the report
            var ids = ["historical_graph", "report_top_pie", "trailing_aum", "trailing_revenue_graph"];//, "chartdiv2", "chartdiv3", "chartdiv4"];

            // Collect actual chart objects out of the AmCharts.charts array
            var charts = {}
            for (var i = 0; i < ids.length; i++) {
                for (var x = 0; x < AmCharts.charts.length; x++) {
//                    alert(typeof(AmCharts.charts[x].div.id));
//                    if(typeof(AmCharts.charts) !== 'undefined')
                    try {
                        if (AmCharts.charts[x].div.id == ids[i])
                        //                        console.log("TESTING 444");
                        //                        console.log(AmCharts.charts[x].div.id);
                        //                        alert('equals');
                            charts[ids[i]] = AmCharts.charts[x];
                    }catch(err){

                    }
                }
            }
			var charts_remaining = Object.keys(charts).length;charts;
            
			if(jQuery.isEmptyObject(charts)){
				$("#export").submit();
			} else {
				// Trigger export of each chart
				for (var x in charts) {
					if (charts.hasOwnProperty(x)) {
						var chart = charts[x];
						chart["export"].capture({}, function() {
							this.toPNG({}, function(data) {
								if(this.config.fileName == "AssetAllocation")
									$("#pie_image").val(encodeURIComponent(data));
								if(this.config.fileName == "historyGraph")
									$("#graph_image").val(encodeURIComponent(data));
								if(this.config.fileName == "trailingAUM")
									$("#aum_image").val(encodeURIComponent(data));
								if(this.config.fileName == "revenueGraph")
									$("#revenue_image").val(encodeURIComponent(data));
	//                            window.location.href = "index.php?module=PortfolioInformation&view=HoldingsReport&pdf=1&account_number="+account_number;
	//                            alert("Exporting");
	//                            $("#export").submit();
	//                            $.post("index.php", {'module':'PortfolioInformation','view':'HoldingsReport',image:encodeURIComponent(data)}, function(response){
	//                                alert(response);
	//                            });

									charts_remaining--;	
									if (charts_remaining == 0) {
										$("#export").submit();
									}
									
							});
						});
					}
				}
            }
			/* var account_number = $("#account_number").val();
            $(".ExportReport").val("Print PDF");
			$(".ExportReport").css("background-color", "lightgreen");
            if($("#pie_image").val() != '' && $("#graph_image").val() != '')
                $("#export").submit(); */
        });
    },
    HoverEvents: function(){
//        $('.hover_symbol').each(function(){
            $('.hover_symbol_holdings').qtip({
                show: {solo: false},
                hide: {
                    effect:function(){
                        $(this).fadeOut();
                    },
                    fixed:true,
                    delay:100
                },
                content: {
                    text: function(event, api) {
                         var symbol = api.elements.target.attr('id');
                         var account = api.elements.target.attr('data-account');
                         var progressInstance = jQuery.progressIndicator();

                         $.ajax({
                             url:"index.php?module=PositionInformation&view=PositionDetails&symbol=" + symbol + "&account=" + account
                         }).then(function(content){
                             progressInstance.hide();
                             api.set('content.text', content);
                         });

                        return "Loading...";
                    }
                },
                position: {
                    my: 'bottom right',
                    at: 'top left',
                    target:'mouse',
                    viewport: $(window),
                    adjust:{mouse:false}
//                viewport: $(window)
                },
                style: {classes: 'custom-blue qtip-rounded qtip-shadow'}
            });
    },

    ExportReport: function(){
            console.log("Starting export...");
            alert("Export This");
    },

    CreateChart: function(holder, value_source){
        if($("#"+holder).length == 0)
            return;
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
                        fileName:"AssetAllocation"
                        };
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
        chart.write(holder);
    },

    CreateAUM: function(){
        var chart;
        var chartData = $.parseJSON($("#trailing_aum_values").val());

        chart = new AmCharts.AmSerialChart();
        chart.export = {enabled:"true",
            libs: {
                path: "libraries/amcharts/amcharts/plugins/export/libs/"
            },
            backgroundColor: "transparent",
            backgroundAlpha: 0.3 ,
            menu:[],
            fileName:"trailingAUM"
        };
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

    CreateHistorical: function(){
        console.log("About to generate graph");
        var chartData = $.parseJSON($("#history_chart").val());
//        chartData.push({"date":"never","value":"0"});
        console.log(chartData);
        var chart = AmCharts.makeChart( "historical_graph", {
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
            }

        } );
        console.log("Revenue done");
    },

    CreateRevenue: function(){
        console.log("About to generate graph");
        var chartData = $.parseJSON($("#trailing_revenue_values").val());
//        chartData.push({"date":"never","value":"0"});
        console.log(chartData);
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
//                "lineColor":"#00a8e6",
//                "fillColors":"00a8e6"
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
                "fileName": "revenueGraph",
                backgroundColor: "transparent",
                backgroundAlpha: 0.3,
                "menu": []
            }

        } );
        console.log("Graph done");
    },

	registerPieReportChartTable : function(){
		
		if(jQuery(".amChartsLegend", jQuery(".report_top_pie")).length){
			jQuery("#holdings_summary_wrapper").find("#report_top_table").css("margin-top", jQuery(".amChartsLegend").css("top"));
		}
	},
	
    registerEvents : function() {
        this.ClickEvents();
        this.HoverEvents();
        this.CreateChart("dynamic_pie_holder", "estimate_pie_values");
        this.CreateChart("report_top_pie", "pie_values");
        this.CreateHistorical();
        this.CreateRevenue();
        this.CreateAUM();
		this.registerPieReportChartTable();
    }
});

jQuery(document).ready(function($) {
    var instance = HoldingsReport_Module_Js.getInstanceByView();
    instance.registerEvents();

    $(".togglehit").click(function(e){
        var link = $(this).parent().find(".chevron_toggle");
        $(this).parent().find(".toggler").toggle(function(){
            if ($(this).is(':visible')) {
                link.text('-');
            } else {
                link.text('+');
            }
        });
    });
});