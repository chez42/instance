jQuery.Class("Consolidated_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new Consolidated_Js();
        return instance;
    }
},{
    /**
     * Function to register event for Faq Popup
     */
    FillCharts : function(){
        var vals = $("#consolidated_chart").data("vals");
        if(typeof(vals) !== "undefined")
        {
            this.AssetChart(vals);
        }
    },

    registerEvents : function() {
        this.FillCharts();
    },

    AssetChart : function(chartData){
        var chart = AmCharts.makeChart("consolidated_chart", {
            "pathToImages" : "libraries/amcharts/amcharts/images/",
            "type": "serial",
            "theme": "light",
            "marginRight": 80,
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
                "bullet": "round",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "date",
                "valueField": "value",
                "useLineColorForBulletBorder": true,
                "balloon":{
                    "drop":true
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
            },
            "export": {
                "enabled": true,
                "libs": {
                    "path": "libraries/amcharts/amcharts/plugins/export/libs/"
                },
                "menu": [ {
                    "class": "export-main",
                    "menu": [ {
                        "label": "Download",
                        "menu": [ "PNG", "JPG", "CSV" ]
                    }, {
                        "label": "Annotate",
                        "action": "draw",
                        "menu": [ {
                            "class": "export-drawing",
                            "menu": [ "PNG", "JPG" ]
                        } ]
                    } ]
                } ]
            }
        });

        chart.addListener("rendered", zoomChart);
        zoomChart();

// this method is called when chart is first inited as we listen for "rendered" event
        function zoomChart() {
            // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
            chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
        }
    }
});
//REMOVED THE READY REQUIREMENT SO THIS LOADS IN A WIDGET EVEN AFTER A REFRESH
//jQuery(document).ready(function($) {
var instance = Consolidated_Js.getInstanceByView();
instance.registerEvents();
//});