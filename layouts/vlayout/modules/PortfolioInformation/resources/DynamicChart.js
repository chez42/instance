jQuery.Class("DynamicChart_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new DynamicChart_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        $(".ExportReport").click(function(e){
            e.stopImmediatePropagation();
// Define IDs of the charts we want to include in the report
            var ids = ["dynamic_chart_holder"];//, "chartdiv2", "chartdiv3", "chartdiv4"];

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
                                if(this.config.fileName == "dynamic_pie_holder") {
                                    $("#chart_image").val(encodeURIComponent(data));
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
            /* var account_number = $("#account_number").val();
            $(".ExportReport").val("Print PDF");
            $(".ExportReport").css("background-color", "lightgreen");
            if($("#pie_image").val() != '' && $("#graph_image").val() != '')
                $("#export").submit(); */
        });
    },

    CreateChart: function(holder, value_source){
        if($("#"+holder).length == 0)
            return;

        var chart;
        var chartData = $.parseJSON($("#"+value_source).val());

        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "intervalenddateformatted";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var graph = new AmCharts.AmGraph();
        graph.valueField = "intervalendvalue";
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
        chart.write(holder);
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

/*jQuery(document).ready(function($) {
    var instance = DynamicChart_Js.getInstanceByView();
    instance.registerEvents();
    instance.CreateChart("dynamic_chart_holder", "t12_balances");
});*/