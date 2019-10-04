jQuery.Class("DynamicPie_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new DynamicPie_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        $(".ExportReport").click(function(e){
            e.stopImmediatePropagation();
// Define IDs of the charts we want to include in the report
            var ids = ["dynamic_pie_holder", "dynamic_chart_holder", "sector_pie_holder"];//, "chartdiv2", "chartdiv3", "chartdiv4"];

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
							this.toPNG({multiplier: 2}, function(data) {
								if(this.config.fileName == "dynamic_pie_holder") {
									$("#pie_image").val(encodeURIComponent(data));
								}
                                if(this.config.fileName == "sector_pie_holder") {
                                    $("#sector_pie_image").val(encodeURIComponent(data));
                                }
                                if(this.config.fileName == "dynamic_chart_holder") {
                                    $("#graph_image").val(encodeURIComponent(data));
                                }
								charts_remaining--;	
								if (charts_remaining == 0) {
									$("#export").submit();
								}
							});
						});
					}
				}
			}
        });
    },

    CreatePie: function(holder, value_source, showLegend){
        if($("#"+holder).length == 0)
            return;

        if(showLegend == undefined)
            showLegend = true;

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
        chart.labelText = "";//"[[percents]]";
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
            //legend.valueText = "$[[value]]";
            legend.valueText = "";
            legend.valueWidth = 100;
            legend.switchable = false;
            legend.labelText = "[[title]]";
            chart.addLegend(legend);//, 'report_top_pie_legend');
        }
        // WRITE
        chart.write(holder);
    },

    CreateGraph: function(holder, value_source, category_field, value_field){
        if($("#"+holder).length == 0)
            return;

        var chart;
        var chartData = $.parseJSON($("#"+value_source).val());

        chart = new AmCharts.AmSerialChart();

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

    registerEvents : function() {
        this.ClickEvents();
//        this.CreateChart("dynamic_pie_holder", "estimate_pie_values");
    }
});
/*
jQuery(document).ready(function($) {
    var instance = DynamicPie_Js.getInstanceByView();
    instance.registerEvents();
});*/