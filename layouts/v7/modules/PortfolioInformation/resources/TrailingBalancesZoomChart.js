jQuery.Class("TrailingBalancesZoomChart",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new TrailingBalancesZoomChart();
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

    numWithComma: function(a) {
        return a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },

    CreateChart : function(chartDiv, chart_type, backgroundColor, graphFillColor, selectedFillColor){
        var self = this;
        var chartData;
        if(backgroundColor === undefined)
            backgroundColor = '#404040';
        if(graphFillColor === undefined)
            graphFillColor = '#7575a3';
        if(selectedFillColor === undefined)
            selectedFillColor = '#666699';

        var chart = AmCharts.makeChart(chartDiv, {
            "type": "serial",
            "zoomOutText": '',
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
            "listeners":[{
                "event":"zoomed",
                "method": function(e) {
//                    console.log(e);
                    var start_value = chart.dataProvider[e.chart.startIndex].total_value;
                    var end_value = chart.dataProvider[e.chart.endIndex].total_value;

                    var start = new Date(chart.dataProvider[e.chart.startIndex].date);//e.startDate);
                    var end = new Date(chart.dataProvider[e.chart.endIndex].date);//e.endDate);

                    var formattedStart = $.datepicker.formatDate("MM, yy", start);
                    var formattedEnd = $.datepicker.formatDate("MM, yy", end);

                    var difference = self.numWithComma(parseFloat(end_value - start_value).toFixed(0));

//                    $(".TrailingBalances").find(".dashboardTitle").html("<strong>Trailing Balances: $" + self.numWithComma(end_value - start_value) + " Difference</strong>");
                    $("#trailing_balances_info").html("<strong>From " + formattedStart + " to " + formattedEnd + " ($" + difference + ")</strong>");//Trailing Balances: $" + self.numWithComma(end_value - start_value) + " Difference</strong>");
                    //document.getElementById('info').innerHTML = "Selected: " + start.toLocaleTimeString() + " -- " + end.toLocaleTimeString()*/
                }
            }],
            "mouseWheelZoomEnabled": true,
            "graphs": [{
                "id": "g1",
                "balloonText": "$[[total_value]]",
                "bullet": "none",
                "bulletBorderAlpha": 1,
                "bulletColor": "#FFFFFF",
                "hideBulletsCount": 50,
                "title": "red line",
                "valueField": "total_value",
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
            data: {'chart_type': chart_type},
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
