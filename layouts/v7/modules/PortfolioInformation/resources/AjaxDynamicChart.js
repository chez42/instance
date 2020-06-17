jQuery.Class("AjaxDynamicChart",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AjaxDynamicChart();
        return instance;
    }
},{
    CreateChart: function(chart_type, start_date, end_date){
        var self = this;

//        var chartData = $.parseJSON($("#historical_fees").val());
        var chart;
        chart = new AmCharts.AmSerialChart();
//        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 180;
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
        catAxis.labelRotation = 90;

        $.ajax({
            type: 'POST',
            url: "index.php?module=PortfolioInformation&action=GetChartValues",
            data: {'chart_type': 'trailing12revenue', 'start_date':start_date, 'end_date':end_date},
            dataType: 'json',
            success: function(data) {
                chart.dataProvider = data;//$.parseJSON(data);
                catAxis.gridCount = data.length;
                chart.validateNow();
                chart.write("management_fees");

            }
        });
    }
});