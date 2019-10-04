jQuery(document).ready(function($){
    jQuery.CreateHoldingsChart = function CreateHoldingsChart(chartData){
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();
        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
//        chart.labelRadius = -30;
        chart.marginLeft = 0;
        chart.marginRight = 0;
        chart.marginTop = 0;
        chart.marginBottom = 0;
        chart.radius = 150;
        chart.labelText = "[[percents]]%";
        chart.textColor= "#FFFFFF";
        chart.depth3D = 10;
        chart.angle = 20;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;
        chart.fontSize = 18;
        chart.numberFormatter = {
            precision:2,decimalSeparator:".",thousandsSeparator:","
        };
        chart["export"] = {
            "enabled": true,
            "divId": "exportdiv"
        };

        legend = new AmCharts.AmLegend();
        legend.align = "center";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "right";
        legend.marginRight = 20;
        legend.autoMargins = false;
        legend.valueWidth = 200;
        legend.switchable = false;
        legend.valueText = "$[[value]]";
        legend.labelText = "[[title]]: ";
        chart.addLegend(legend);

        // WRITE
        if($("#holdings_chart_positions" + name).length > 0) {
            chart.write("holdings_chart_positions");
        }
        
        if($("#holdings_chart_overview" + name).length > 0) {
            chart.write("holdings_chart_overview");
        }
    };
  
    var value = ($(document).find("[name=holdingschart]").val());
    if(typeof(value) !== "undefined")
    {
        var values = $.parseJSON(value);
        var chartData = values;
        $.CreateHoldingsChart(chartData);
    }
});