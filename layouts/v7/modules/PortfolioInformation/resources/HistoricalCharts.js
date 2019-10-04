jQuery.Class("PortfolioInformation_Module_Js",{
	currentInstance : false,
        
	getInstanceByView : function(){
            var instance = new PortfolioInformation_Module_Js();
	    return instance;
	}
},{
    /**
     * Function to register event for filling in the pie chart
     */
    FillCharts : function(){
        var pie = $("#asset_pie").val();
        var revenue_value = $("#trailing_12_revenue").val();
        var aum_value = $("#trailing_12_aum").val();
        
        if(typeof(pie) !== "undefined")
        {
            var chartData = $.parseJSON(pie);
            if(chartData === 0){
                this.RemoveWidget();
            }
            this.AssetChart(chartData);
        }
/*
        if(typeof(revenue_value) !== "undefined")
        {
            var chartData = $.parseJSON(revenue_value);
            this.RevenueChart(chartData, 'filtered_revenue_graph');
        }
        
        if(typeof(aum_value) !== "undefined")
        {
            var values = $.parseJSON(aum_value);
            var chartData = values;
            this.LineChart(chartData, 'filtered_assets_graph');
        }*/
    },

    RemoveWidget : function() {
        $("#AssetAllocationWidget").closest(".summaryWidgetContainer").hide();
    },
    
    registerEvents : function() {
        this.FillCharts();
    },
    
    AssetChart : function(chartData){
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.radius = 75;
        chart.labelText = "[[percents]]%";
        chart.hideLabelsPercent = 100;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 15;
        chart.angle = 30;
        chart.outlineColor = "#ffffff";
        chart.outlineAlpha = 0.4;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;
        chart.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};

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
        chart.addLegend(legend, 'legenddiv');
        
        // WRITE
        if($("#filtered_pie").length > 0) {
            chart.write("filtered_pie");
        }   
    },
    
    RevenueChart : function(chartData, element){
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
        graph.bullet = "none";
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
        chart.write(element);
    },
    
    LineChart : function(chartData, element){
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
        graph.balloonText="Total: $[[value]]";
        graph.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph.type = "line";
        graph.lineColor = "#000033";
        graph.bullet = 'round';
        chart.addGraph(graph);

        var graph2 = new AmCharts.AmGraph();
        graph2.valueField = "cash_value";
        graph2.balloonText="Cash: $[[value]]";
        graph2.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph2.type = "line";
        graph2.lineColor = '#02B90E';
        graph2.bullet = 'round';
        chart.addGraph(graph2);
        
        var graph3 = new AmCharts.AmGraph();
        graph3.valueField = "fixed_income";
        graph3.balloonText="Fixed Income: $[[value]]";
        graph3.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph3.type = "line";
        graph3.lineColor = '#8383ff';
        graph3.bullet = 'round';
        chart.addGraph(graph3);
        
        var graph4 = new AmCharts.AmGraph();
        graph4.valueField = "equities";
        graph4.balloonText="Equities: $[[value]]";
        graph4.numberFormatter = {precision:2, decimalSeparator:".", thousandsSeparator:","};
        graph4.type = "line";
        graph4.lineColor = '#6bd7d6';
        graph4.bullet = 'round';
        chart.addGraph(graph4);
        
        chart.write(element);
    }
});
//REMOVED THE READY REQUIREMENT SO THIS LOADS IN A WIDGET EVEN AFTER A REFRESH
//jQuery(document).ready(function($) {
	var instance = PortfolioInformation_Module_Js.getInstanceByView();
	instance.registerEvents();
//});