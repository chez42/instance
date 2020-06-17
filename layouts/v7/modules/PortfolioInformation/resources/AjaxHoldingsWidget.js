jQuery.Class("AjaxHoldingsWidget_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AjaxHoldingsWidget_JS();
        return instance;
    }
},{
    ConfigurePiechart: function(){
        var provider = $("#widget_container").data("pie");
        var chart = AmCharts.makeChart("HoldingsWidgetPie", {
            type: "pie",
            valueField: "value",
            titleField: "title",
            colorField: 'color',
            numberFormatter: {precision:2, decimalSeparator:".", thousandsSeparator:","},
            depth3D: 14,
            angle: 25,
            outlineColor: "#363942",
            outlineAlpha: 0.8,
            outlineThickness: 1,
            startDuration:3,
            labelsEnabled: false,
            autoMargins: false,
            marginTop: 100,
            marginBottom: 100,
            marginLeft: 100,
            marginRight: 100,
            pullOutRadius: 10,
            dataProvider:provider[0],
            legend:{divId: 'HoldingsWidgetLegend', align: 'left', markerType: 'square', maxColumns : 2,
                    position: 'right', labelText: '[[title]]', valueWidth: 100},
            export: {
                "enabled": false,
                "legend":{"position":"bottom"}
            }
        });

/*        var legend;
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
        chart.addLegend(legend);//, 'report_top_pie_legend');*/

        return chart;
    },

    GetChartDataAndRender: function(chart, recordID, group_type){
        var provider = $("#widget_container").data("pie");
        chart.dataProvider = provider[group_type];
        chart.validateData();
        chart.animateAgain();
/*        $.ajax({
            type: 'POST',
            url: "index.php?module=PortfolioInformation&action=GetChartValues",
            data: {'chart_type': 'holdings_widget', 'record_id': recordID},
            success: function(data) {
                chartData = $.parseJSON(data);
                chart.animateData(chartData, { duration: 1000 });
                $("#HoldingsWidgetPie").fadeIn("slow");
            }
        });*/
    },

    HandleEvents:function(chart){
        var self = this;
        $("#pie_type").change(function(e){
//            $("#HoldingsWidgetPie").fadeOut("slow", function(){
                var selected = $('#pie_type').find(':selected');
                var record = $("#recordId").val();
                self.GetChartDataAndRender(chart, record, selected.val());
//            });
        });

        $("#depthRange").on('input', function(){
            var depth = parseInt($("#depthRange").val());
            chart.depth3D = depth;
            chart.validateData();
        });

        $("#angleRange").on('input', function(){
            var angle = parseInt($("#angleRange").val());
            chart.angle = angle;
            chart.validateData();
        });
    },

    registerEvents : function() {
        var chart = this.ConfigurePiechart();
        this.HandleEvents(chart);
    }
});

//jQuery(document).ready(function($) {
    var instance = AjaxHoldingsWidget_JS.getInstanceByView();
    instance.registerEvents();
//});