jQuery.Class("AjaxDynamicZoomChart",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AjaxDynamicZoomChart();
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

    CreateChart : function(chartDiv, chart_type, start_date, end_date, backgroundColor, graphFillColor, selectedFillColor){
        var chartData;
        if(backgroundColor === undefined)
            backgroundColor = '#404040';
        if(graphFillColor === undefined)
            graphFillColor = '#7575a3';
        if(selectedFillColor === undefined)
            selectedFillColor = '#666699';

        var chart = AmCharts.makeChart(chartDiv, {
            "type": "serial",
            "theme": "light",
            "marginRight": 80,
            "autoMarginOffset": 20,
            "marginTop": 7,
//            "dataProvider": chartData,
            "valueAxes": [{
                "axisAlpha": 0.2,
                "dashLength": 1,
                "position": "left"
            }],
            "mouseWheelZoomEnabled": true,
            "graphs": [{
                "id": "g1",
                "balloonText": "$[[value]]",
                "bullet": "none",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "value",
                "useLineColorForBulletBorder": true,
                "balloon":{
                    "drop":false
                },
                "balloonFunction": function(item, graph) {
                    var result = graph.balloonText;
                    for (var key in item.dataContext) {
                        if (item.dataContext.hasOwnProperty(key) && !isNaN(item.dataContext[key])) {
                            var formatted = AmCharts.formatNumber(item.dataContext[key], {
                                precision: chart.precision,
                                decimalSeparator: chart.decimalSeparator,
                                thousandsSeparator: chart.thousandsSeparator
                            }, 2);
                            result = result.replace("[[" + key + "]]", formatted);
                        }
                    }
                    return result;
                }
            }],
            "chartScrollbar": {
                "autoGridCount": true,
                "graph": "g1",
                "scrollbarHeight": 40,
                "backgroundColor": backgroundColor,
                "graphFillColor": graphFillColor,
                "selectedGraphFillColor": selectedFillColor
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

        $.ajax({
            type: 'POST',
            url: "index.php?module=PortfolioInformation&action=GetChartValues",
            data: {'chart_type': chart_type, 'start_date':start_date, 'end_date':end_date},
//            dataType: 'json',
            success: function(data) {
                chartData = $.parseJSON(data);
                chart.dataProvider = chartData;
//                chart.addListener("rendered", zoomChart);
//                zoomChart();
                chart.validateData();
                var date = new Date();
                var month = date.getMonth();
                var day = date.getDay();
                var year = date.getFullYear();
                chart.zoomToDates(new Date(year-1, month, day), new Date(year, month, day));
            }
        });

// this method is called when chart is first inited as we listen for "rendered" event
        function zoomChart() {
            // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
            chart.zoomToIndexes(chartData.length - 13, chartData.length - 1);
        }
    }
});
