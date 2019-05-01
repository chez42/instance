/**
 * Created by ryansandnes on 2017-06-23.
 */
jQuery.Class("IntervalsDaily_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new IntervalsDaily_Js();
        return instance;
    }
},{
    ZoomChart : function(){
        var s = $(".amcharts-start-date-input").val();
        var e = $(".amcharts-end-date-input").val();
        var returns = new Array();
        try {
            var start = $.datepicker.parseDate("m/d/yy", s);
            var end = $.datepicker.parseDate("m/d/yy", e);
            $('#IntervalTable tbody tr').each(function() {
                $(this).children('td, th').css('backgroundColor', '#DADADB');
            });

            $('.end_date').each(function(i, obj) {
                var cur = $.datepicker.parseDate("m-d-yy", $(obj).text());
                if(cur <= end && cur >= start){
                    $(this).closest('tr').children('td, th').css('background-color','#98FB98');
                    var val = $(obj).siblings('.net_return').data('net_return');
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
    },
    LineBarChart : function(){
        var funcs = this;
        var accounts = $("#account_numbers").val();
        var data = new Array();
        $.post("index.php", {module:'PortfolioInformation', action:'IntervalJSON', todo:'endvaluesdaily', account_numbers:accounts}, function(response){
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
                    dateFormat: "MM/DD/YYYY",
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
                        funcs.ZoomChart();
                    }
                }, {
                    "event": "rendered",
                    "method": function (e) {
                        $(".amcharts-start-date-input").shieldDatePicker({
                            events: {
                                change: function (e) {
                                    funcs.ZoomChart();
                                }
                            }
                        });
                        $(".amcharts-end-date-input").shieldDatePicker({
                            events: {
                                change: function (e) {
                                    funcs.ZoomChart();
                                }
                            }
                        });
                    }
                }],
            });
        });
    },

    registerEvents : function() {
        this.LineBarChart();
//        this.Testing();
        var vtigerInstance = Vtiger_Index_Js.getInstance();
    	vtigerInstance.registerEvents();
    },


});
//REMOVED THE READY REQUIREMENT SO THIS LOADS IN A WIDGET EVEN AFTER A REFRESH
jQuery(document).ready(function($) {
    var instance = IntervalsDaily_Js.getInstanceByView();
    instance.registerEvents();
});