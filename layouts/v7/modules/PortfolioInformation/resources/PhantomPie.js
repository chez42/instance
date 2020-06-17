jQuery(document).ready(function($) {
/*
    if(legendID == undefined)
        legendID = "dynamic_pie_legend";

    if(showLegend == undefined)
        showLegend = true;

    var chart;
    var legend;
*/
    var chartData = $("#phantom_pie").data('value');

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
    chart.addLegend(legend);//, legendID);//, 'report_top_pie_legend');
/*    if(showLegend == true) {
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
        chart.addLegend(legend, legendID);//, 'report_top_pie_legend');
    }*/
    // WRITE
    chart.write("phantom_pie");
});