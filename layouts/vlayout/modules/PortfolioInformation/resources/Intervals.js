/**
 * Created by ryansandnes on 2017-06-23.
 */
jQuery.Class("Intervals_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new Intervals_Js();
        return instance;
    }
},{
    /**
     * Function to register event for Faq Popup
     */
/*    FillBarChart : function(){
        var chart = AmCharts.makeChart( "chartdiv", {
            "type": "serial",
            "theme": "light",
            "dataLoader": {
                "url": "index.php?module=PortfolioInformation&action=IntervalJSON&todo=endvalues",
                "format":"json"
            },
            "gridAboveGraphs": true,
            "startDuration": 1,
            "graphs": [ {
                "balloonText": "[[category]]: <b>[[value]]</b>",
                "fillAlphas": 0.8,
                "lineAlpha": 0.2,
                "type": "column",
                "valueField": "end_value"
            } ],
            "chartCursor": {
                "categoryBalloonEnabled": false,
                "cursorAlpha": 0,
                "zoomable": false
            },
            "categoryField": "end_date",
            "categoryAxis": {
                "gridPosition": "start",
                "labelRotation": 45,
                "gridAlpha": 0,
                "tickPosition": "start",
                "tickLength": 20
            },
        } );
    },

    FillLineChart : function(){
        var chart = AmCharts.makeChart("chartdiv", {
            'pathToImages': 'libraries/amcharts/amcharts/images/',
            "type": "serial",
            "theme": "light",
            "marginTop":0,
            "marginRight": 80,
            "dataLoader": {
                "url": "index.php?module=PortfolioInformation&action=IntervalJSON&todo=endvalues",
                "format":"json"
            },
            "graphs": [{
                "id":"g1",
                "balloonText": "[[category]]<br><b><span style='font-size:14px;'>$[[value]]</span></b>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#d1655d",
                "lineThickness": 2,
                "negativeLineColor": "#637bb6",
                "type": "smoothedLine",
                "valueField": "end_value"
            }],
            "chartScrollbar": {
                "graph":"g1",
                "gridAlpha":0,
                "color":"#888888",
                "scrollbarHeight":55,
                "backgroundAlpha":0,
                "selectedBackgroundAlpha":0.1,
                "selectedBackgroundColor":"#888888",
                "graphFillAlpha":0,
                "autoGridCount":true,
                "selectedGraphFillAlpha":0,
                "graphLineAlpha":0.2,
                "graphLineColor":"#c2c2c2",
                "selectedGraphLineColor":"#888888",
                "selectedGraphLineAlpha":1

            },
            "dataDateFormat": "MM-DD-YYYY",
            "categoryField": "end_date",
            "categoryAxis": {
                "minPeriod": "MM",
                "parseDates": true,
                "minorGridAlpha": 0.1,
                "minorGridEnabled": true
            }
        });
    },

    FillMultiLineChart : function(){
        var haszoomed = 0;
        var chart = AmCharts.makeChart("chartdiv", {
            'pathToImages': 'libraries/amcharts/amcharts/images/',
            "type": "serial",
            "theme": "light",
            "legend": {
                "useGraphSettings": true
            },
            "listeners": [{
                "event": "zoomed",
                "method": function(e) {
                    if (typeof e.chart.zoomToIndexes === "function") {
                        if(typeof(e.chart.endIndex) !== 'undefined') {
                            if(haszoomed == 0) {
                                e.chart.zoomToIndexes(e.chart.endIndex - 6, e.chart.endIndex);
                                haszoomed = 1;
                            }
                        }
                    }
                }
            }],
            "marginTop":0,
            "marginRight": 80,
            "dataLoader": {
                "url": "index.php?module=PortfolioInformation&action=IntervalJSON&todo=endvalues",
                "format":"json"
            },
            "synchronizeGrid":true,
            "valueAxes": [{
                "id":"v1",
                "axisColor": "#72d15d",
                "axisThickness": 2,
                "axisAlpha": 1,
                "position": "left",
                "title": "Value",
                "categoryAxis": {
                    "gridPosition": "end"
                }
            }, {
                "id":"v2",
                "axisColor": "#637bb6",
                "axisThickness": 2,
                "axisAlpha": 1,
                "position": "right",
                "title": "Net Flow",
                "categoryAxis": {
                    "gridPosition": "end"
                }
            }],
            "graphs": [{
                "title":"Value",
                "valueAxis":"v1",
                "id":"g1",
                "balloonText": "[[category]]<br><b><span style='font-size:14px;'>$[[value]]</span></b>",
                "bullet": "round",
                "bulletSize": 8,
                "lineColor": "#72d15d",
                "lineThickness": 2,
                "fillColors": [
                    "#72d15d",
                    "#d1655d"
                ],
//                "lineAlpha": 0,
//                "fillAlphas": 0.8,
                "negativeLineColor": "#d1655d",
                "type": "line",
                "valueField": "end_value"
            }, {
                "title":"Net Flow",
                "valueAxis":"v2",
                "id":"g2",
                "balloonText": "[[category]]<br><b><span style='font-size:14px;'>$[[value]]</span></b>",
//                "bullet": "round",
//                "bulletSize": 8,
                "lineColor": "#637bb6",
                "lineThickness": 2,
                "fillColors": [
                    "#637bb6"
                ],
                "lineAlpha": 0,
                "fillAlphas": 0.8,
                "negativeLineColor": "#d1655d",
                "negativeFillColors": ["#d1655d"],
                "type": "column",
                "valueField": "net_flow"
            }, {
                "title":"Investment Return",
                "valueAxis":"v2",
                "id":"g3",
                "balloonText": "[[category]]<br><b><span style='font-size:14px;'>$[[value]]</span></b>",
//                "bullet": "round",
//                "bulletSize": 8,
                "lineColor": "#BE33FF",
                "lineThickness": 2,
                "fillColors": [
                    "#BE33FF"
                ],
                "lineAlpha": 0,
                "fillAlphas": 0.8,
                "negativeLineColor": "#BE33FF",
                "negativeFillColors": ["#BE33FF"],
                "type": "column",
                "valueField": "period_return"
            }],
            "chartScrollbar": {
                "graph":"g1",
                "gridAlpha":0,
                "color":"#888888",
                "scrollbarHeight":55,
                "backgroundAlpha":0,
                "selectedBackgroundAlpha":0.1,
                "selectedBackgroundColor":"#888888",
                "graphFillAlpha":0,
                "autoGridCount":true,
                "selectedGraphFillAlpha":0,
                "graphLineAlpha":0.2,
                "graphLineColor":"#c2c2c2",
                "selectedGraphLineColor":"#888888",
                "selectedGraphLineAlpha":1
            },
            "dataDateFormat": "MM-DD-YYYY",
            "categoryField": "end_date",
            "categoryAxis": {
                "minPeriod": "MM",
                "parseDates": true,
                "minorGridAlpha": 0.1,
                "minorGridEnabled": true
            },
            export: {
                enabled: true
            }
        });
    },

    FillStockChart : function(){
        var accounts = $("#account_numbers").val();
        var data = new Array();
        $.post("index.php", {module:'PortfolioInformation', action:'IntervalJSON', todo:'endvalues', account_numbers:accounts}, function(response){
           data = $.parseJSON(response);
            var chart = AmCharts.makeChart("chartdiv", {
                type: "stock",
                theme: "light",
                pathToImages: 'libraries/amcharts/amstockchart/images/',
                dataDateFormat: "MM-DD-YYYY",
                dataSets: [{
                    title: "Month End Value",
                    fieldMappings: [{
                        fromField: "end_value",
                        toField: "end_value"
                    }],
                    dataProvider: data,
                    categoryField: "end_date"
                },
                    {
                        title: "Net Flow Amount",
                        fieldMappings: [{
                            fromField: "net_flow",
                            toField: "value2"
                        }],
                        dataProvider: data,
                        categoryField: "end_date",
                        compared: true
                    },
                    {
                        title: "Investment Return",
                        fieldMappings: [{
                            fromField: "investment_return",
                            toField: "value3"
                        }],
                        dataProvider: data,
                        categoryField: "end_date",
                        compared: true
                    }],

                panels: [{
                    showCategoryAxis: false,
                    title: "Month End Values",
                    recalculateToPercents: "never",
                    stockGraphs: [{
                        id: "g1",
                        valueField: "end_value",
                        comparable: true
                    }],

                    stockLegend: {

                    }
                }, {
                    showCategoryAxis: true,
                    title: "Net Flow",
                    recalculateToPercents: "never",
                    stockGraphs: [{
                        id: "g2",
                        valueField: "net_flow",
                        compareField: "value2",
                        comparable: true,
                        visibleInLegend: false
                    }, {
                        id: "g3",
                        valueField: "investment_return",
                        compareField: "value3",
                        comparable: true,
                        visibleInLegend: false
                    }],

                    stockLegend: {

                    }
                }],

                chartScrollbarSettings: {
                    graph: "g1"
                },

                chartCursorSettings: {
                    valueBalloonsEnabled: true,
                    fullWidth:true,
                    cursorAlpha:0.1
                },

                periodSelector: {
                    dateFormat: "MM-DD-YYYY",
                    periods: [{
                        period: "MM",
                        count: 1,
                        label: "1 Month"
                    }, {
                        period: "MM",
                        count: 3,
                        label: "3 Months"
                    }, {
                        period: "MM",
                        count: 6,
                        selected: true,
                        label: "6 Months"
                    }, {
                        period: "YYYY",
                        count: 1,
                        label: "1 Year"
                    }, {
                        period: "YTD",
                        label: "YTD"
                    }, {
                        period: "MAX",
                        label: "MAX"
                    }]
                },
                "export": {
                    libs: { "path": "libraries/amcharts/amstockchart/plugins/export/libs/" },
                    "enabled": true,
                    "menu": [ {
                        "class": "export-main",
                        "menu": [ {
                            "label": "Download as image",
                            "menu": [ "PNG", "JPG", "SVG" ]
                        }, {
                            "label": "Download data",
                            "menu": [ "CSV", "XLSX" ]
                        }, {
                            "label": "Download Report",
                            "click": function(){
                                chart.AmExport.capture( {}, function() {
                                    // SAVE TO JPG
                                    this.toJPG( {}, function( base64 ) {
                                        // LOG IMAGE DATA
                                        var image = encodeURIComponent(base64);
                                        var input = $("<input>")
                                            .attr("type", "hidden")
                                            .attr("name", "image").val(image);
                                        $('#IntervalForm').append($(input));
//                                    alert($("#IntervalForm").html());
                                        $("#IntervalForm").submit();

//                                    console.log( base64 );
                                    } );
                                } );
                            }
                        } ]
                    } ]
                },
                "listeners": [{
                    "event": "zoomed",
                    "method": function(e) {
                        var s = $(".amcharts-start-date-input").val();
                        var e = $(".amcharts-end-date-input").val();
                        var returns = new Array();
                        try {
                            var start = $.datepicker.parseDate("m-d-yy", s);
                            var end = $.datepicker.parseDate("m-d-yy", e);
                            $('#IntervalTable tbody tr').each(function() {
                                $(this).children('td, th').css('backgroundColor', '#DADADB');
                            });

                            $('.end_date').each(function(i, obj) {
                                var cur = $.datepicker.parseDate("m-d-yy", $(obj).text());
                                if(cur <= end && cur >= start){
                                    $(this).closest('tr').children('td, th').css('background-color','#98FB98');
                                    var val = $(obj).siblings('.investment_return').data('investment_return');
                                    returns.push(val/100);
                                }
                            });

                            function CalculateReturn(r){
                                var val = 1;
                                $.each(r, function(k, v){
                                    val = val * (1+v);
                                });
                                val = (val - 1) * 100;
                                return(val.toFixed(2));
                            }
                            var r = CalculateReturn(returns);
                            $(".calculated_return").text(r + "%");
                        }catch(err){

                        }
                    }
                }],
            });
        });
    },*/

    LineBarChart : function(){
        var accounts = $("#account_numbers").val();
        var data = new Array();
        $.post("index.php", {module:'PortfolioInformation', action:'IntervalJSON', todo:'endvalues', account_numbers:accounts}, function(response){
            data = $.parseJSON(response);
            var chart = AmCharts.makeChart("chartdiv", {
                type: "stock",
                theme: "light",
            pathToImages: 'libraries/amcharts/amstockchart/images/',
                dataDateFormat: "MM-DD-YYYY",
                dataSets: [{
                    fieldMappings: [{
                        fromField: "end_value",
                        toField: "end_value"
                    },{
                        fromField: "net_flow",
                        toField: "value2"
                    },{
                        fromField: "investment_return",
                        toField: "value3"
                    },{
                        fromField: "period_return",
                        toField: "value4"
                    }],

                    dataProvider: data,
                    categoryField: "end_date"
                }],
                panels: [{
//                    title: "Month End Values / Deposits & Withdrawls",
                    showCategoryAxis: false,
                    percentHeight: 70,
                    recalculateToPercents: "never",
                    depth3D: 14,
                    angle: 25,
                    valueAxes: [ {
                        id: "v1",
                        dashLength: 5,
                        unit: "$",
                        unitPosition: "left",
                        stackType: "regular"
                    } ],
                    categoryAxis: {
                        dashLength: 5
                    },
                    stockGraphs: [{
                        title: "Month End Value",
                        type: "line",
                        id: "g1",
                        valueField: "end_value",
                        balloonText: "$[[value]]",
                        comparable: true
                    },{
                        title: "Deposits / Withdrawals",
                        balloonText: "$[[value]]",
                        type: "column",
                        id: "g2",
                        valueField: "value2",
                        useDataSetColors: false,
                        fillAlphas: 0.3,
                        negativeFillColors:"#a30013",
                        negativeLineColor:"#a30013",
                        fillColors:"#00d111",
                        lineColor:"#00d111",
                        comparable: true
                    }],
                    stockLegend: {
                    }
                },{
                    showCategoryAxis: true,
//                    title: "Investment Return $",
                    recalculateToPercents: "never",
                    depth3D: 14,
                    angle: 25,
                    valueAxes: [ {
                        unit: "$",
                        unitPosition: "left",
                        dashLength: 5,
                        stackType: "regular"
                    } ],
                    stockGraphs: [ {
                        title: "Investment Return",
                        balloonText: "$[[value]]",
                        type: "column",
                        id: "g3",
                        valueField: "value3",
                        fillAlphas: 0.3,
                        comparable: true,
                        useDataSetColors: false,
                        negativeFillColors:"#a30013",
                        negativeLineColor:"#a30013",
                        fillColor:"#00d111",
                        lineColor:"#00d111"
                    }],
                    stockLegend: {
                    }
                },{
                    showCategoryAxis: true,
//                    title: "Period Return %",
                    depth3D: 14,
                    angle: 25,
                    recalculateToPercents: "never",
                    valueAxes: [ {
                        unit: "%",
                        unitPosition: "right",
                        dashLength: 5,
                        stackType: "regular"
                    } ],
                    stockGraphs: [ {
                        title: "Period Return",
                        balloonText: "[[value]]%",
                        type: "column",
                        id: "g4",
                        valueField: "value4",
                        fillAlphas: 0.3,
                        comparable: true,
                        useDataSetColors: false,
                        negativeFillColors:"#a30013",
                        negativeLineColor:"#a30013",
                        fillColor:"#00d111",
                        lineColor:"#00d111"
                    }],
                    stockLegend: {
                    }
                }],

                chartScrollbarSettings: {
                    graph: "g1"
                },

                chartCursorSettings: {
                    valueBalloonsEnabled: true,
                    fullWidth:true,
                    cursorAlpha:0.1
                },

                periodSelector: {
                    dateFormat: "MM-DD-YYYY",
                    periods: [{
                        period: "MM",
                        count: 1,
                        label: "1 Month"
                    }, {
                        period: "MM",
                        count: 3,
                        label: "3 Months"
                    }, {
                        period: "MM",
                        count: 6,
                        selected: true,
                        label: "6 Months"
                    }, {
                        period: "YYYY",
                        count: 1,
                        label: "1 Year"
                    }, {
                        period: "YTD",
                        label: "YTD"
                    }, {
                        period: "MAX",
                        label: "MAX"
                    }]
                },
                "export": {
                    libs: { "path": "libraries/amcharts/amstockchart/plugins/export/libs/" },
                    "enabled": true,
                    "menu": [ {
                        "class": "export-main",
                        "menu": [ {
                            "label": "Download as image",
                            "menu": [ "PNG", "JPG", "SVG" ]
                        }, {
                            "label": "Download data",
                            "menu": [ "CSV", "XLSX" ]
                        }, {
                            "label": "Download Report",
                            "click": function(){
                                chart.AmExport.capture( {}, function() {
                                    // SAVE TO JPG
                                    this.toJPG( {}, function( base64 ) {
                                        var s = $(".amcharts-start-date-input").val();
                                        var e = $(".amcharts-end-date-input").val();
                                        var start = $.datepicker.parseDate("m-d-yy", s);
                                        var end = $.datepicker.parseDate("m-d-yy", e);
                                        var FormattedStart = $.datepicker.formatDate( "yy-mm-dd", new Date( start ) );
                                        var FormattedEnd = $.datepicker.formatDate( "yy-mm-dd", new Date( end ) );

                                        $('#start_date').val(FormattedStart);
                                        $("#end_date").val(FormattedEnd);
                                        $("#calculated_return").val($(".calculated_return").text());

                                        // LOG IMAGE DATA
                                        var image = encodeURIComponent(base64);
                                        var input = $("<input>")
                                            .attr("type", "hidden")
                                            .attr("name", "image").val(image);
                                        $('#IntervalForm').append($(input));
                                        $("#IntervalForm").submit();
                                    } );
                                } );
                            }
                        } ]
                    } ]
                },
                "listeners": [{
                    "event": "zoomed",
                    "method": function(e) {
                        var s = $(".amcharts-start-date-input").val();
                        var e = $(".amcharts-end-date-input").val();
                        var returns = new Array();
                        try {
                            var start = $.datepicker.parseDate("m-d-yy", s);
                            var end = $.datepicker.parseDate("m-d-yy", e);
                            $('#IntervalTable tbody tr').each(function() {
                                $(this).children('td, th').css('backgroundColor', '#DADADB');
                            });

                            $('.end_date').each(function(i, obj) {
                                var cur = $.datepicker.parseDate("m-d-yy", $(obj).text());
                                if(cur <= end && cur >= start){
                                    $(this).closest('tr').children('td, th').css('background-color','#98FB98');
                                    var val = $(obj).siblings('.period_return').data('period_return');
                                    returns.push(val/100);
                                }
                            });

                            function CalculateReturn(r){
                                var val = 1;
                                $.each(r, function(k, v){
                                    val = val * (1+v);
                                });
                                val = (val - 1) * 100;
                                return(val.toFixed(2));
                            }
                            var r = CalculateReturn(returns);
                            $(".calculated_return").text(r + "%");
                        }catch(err){

                        }
                    }
                }],
            });
        });
    },

    registerEvents : function() {
        this.LineBarChart();
//        this.Testing();
    },


});
//REMOVED THE READY REQUIREMENT SO THIS LOADS IN A WIDGET EVEN AFTER A REFRESH
jQuery(document).ready(function($) {
    var instance = Intervals_Js.getInstanceByView();
    instance.registerEvents();
});