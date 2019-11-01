var Consolidated_Js = {
    /**
     * Function to register event for Faq Popup
     */
    FillCharts : function(){
        var vals = $("#consolidated_chart").data("vals");
        if(vals === 0){
            this.RemoveWidget();
        }else if(typeof(vals) !== "undefined")
        {
            this.AssetChart(vals);
        }
    },

    registerEvents : function() {
        this.FillCharts();
    },

    RemoveWidget : function() {
        $("#consolidated_chart").closest(".summaryWidgetContainer").hide();
    },

    AssetChart : function(chartData){
        var chart = AmCharts.makeChart("consolidated_chart", {
            "pathToImages" : "images/",
            "type": "serial",
            "theme": "light",
            "marginRight": 20,
            "autoMarginOffset": 20,
            "marginTop": 7,
            "dataProvider": chartData,
            "valueAxes": [{
                "axisAlpha": 0.2,
                "dashLength": 1,
                "position": "left",
                "unit":"$",
                "unitPosition":"left"
            }],
            "mouseWheelZoomEnabled": true,
            "graphs": [{
                "id": "g1",
                "balloonText": "$[[value]]",
                "bullet": "none",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "date",
                "valueField": "value",
                "useLineColorForBulletBorder": true,
                "balloon":{
                    "drop":false
                }
            }],
            "chartScrollbar": {
                "autoGridCount": true,
                "graph": "g1",
                "scrollbarHeight": 40
            },
            "chartCursor": {
                "limitToGraph":"g1"
            },
            "categoryField": "date",
            "categoryAxis": {
                "parseDates": true,
                "axisColor": "#DADADA",
                "dashLength": 1,
                "minorGridEnabled": true
            }
        });

        chart.addListener("rendered", zoomChart);
        zoomChart();

        function zoomChart() {
            chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
        }
    }
};
jQuery(document).ready(function($) {
	Consolidated_Js.registerEvents();
});