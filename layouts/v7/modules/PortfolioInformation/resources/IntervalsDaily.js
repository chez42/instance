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
    CalculateTWR : function(sdate, edate){
        var self = this;
//        var s = $(".amcharts-start-date-input").val();
//        var e = $(".amcharts-end-date-input").val();
//        var s = $("#fromfield").val();
//        var e = $("#tofield").val();
        var returns = new Array();
        var selected_elements = new Array();
        try {
            var count = 0;
            var average = 0;
            var annual = 0;
            var start = $.datepicker.parseDate("yy-mm-dd", sdate);
            var end = $.datepicker.parseDate("yy-mm-dd", edate);

            /*            $('#IntervalTable tbody tr').each(function() {
                            $(this).children('td, th').css('backgroundColor', '#DADADB');
                        });*/

            $('.data_end_date').each(function(i, obj) {
                var cur = $.datepicker.parseDate("m-d-yy", $(obj).text());
                if(cur <= end && cur >= start){
//                    $(this).closest('tr').children('td, th').css('background-color','#98FB98');
                    $(this).closest('tr').show();
                    var begin_value = $(obj).siblings('.data_begin_value');
                    var flow = $(obj).siblings('.data_net_flow');
                    var income = $(obj).siblings('.data_incomeamount');
                    var expense = $(obj).siblings('.data_expense_amount');
                    var investment = $(obj).siblings('.data_investmentreturn');
                    var end_value = $(obj).siblings('.data_end_value').data('end_value');
                    var net_return = $(obj).siblings('.data_net_return').data('net_return');
                    var twr = $(obj).siblings('.data_twr');

                    if(net_return > 1.15 || net_return < 0.85){
//                        console.log(val);
                        $(obj).siblings('.data_net_return').css('background-color','red');
                    }
                    var tmp = {begin_value: begin_value, flow: flow, income: income, expense: expense, end_value: end_value, net_return: net_return, twr: twr};
                    returns.push(tmp);
                    selected_elements.push($(obj).parent());
//                    returns.push(val/100);
                }else{
                    $(this).closest('tr').hide();
                }
            });

            function CalculateReturn(r){
                var val = 1;
                $.each(r, function(k, v){
                    val = val * (v.net_return);
                    var tmp = (val - 1) * 100;
                    v.twr.text(tmp.toFixed(2));
                    if(tmp > 0) {
                        v.twr.removeClass('red');
                        v.twr.addClass('green');
                    }
                    if(tmp < 0) {
                        v.twr.removeClass('green');
                        v.twr.addClass('red');
                    }
                    count+=1;
                });
                val = (val - 1) * 100;
                /*
                var val = 0;
                $.each(r, function(k, v){
                    console.log(v);
//                    val = val + v;
                    val = parseFloat(val) + parseFloat(v);
                    count+=1;
                });
                console.log("VALUE IS NOW " + val);*/
                return(val.toFixed(2));
            }
            selected_elements.reverse();
            returns.reverse();
            var r = CalculateReturn(returns);
            average = (r / count).toFixed(2);
            annual = (average * 12).toFixed(2);
            $(".selected_twr").text(r + "%");
            $(".average_return").text(average + "%");
            $(".annual_return").text(annual + "%");
            self.SetCalculatedText(selected_elements);
        }catch(err){
            console.log(err);
        }
    },

    SetCalculatedText: function(elements){
        var self = this;
//        console.log(elements);
//        console.log(elements[0].find("td:eq(0)").data('date'));
        var begin_value = parseFloat(elements[0].children('.data_begin_value').data('begin_value'));
        var end_value = parseFloat(elements.slice(-1).pop().children('.data_end_value').data('end_value'));//Get last array element, slide it, and pop it from the stack
        var flow = 0;
        var income = 0;
        var expense = 0;
        var investment = 0;

        $.each(elements, function(k, v){
            var tmp_begin = v.children('.data_begin_value').data('begin_value');
            var tmp_flow = parseFloat(v.children('.data_net_flow').data('net_flow'));
            var tmp_income = parseFloat(v.children('.data_incomeamount').data('incomeamount'));
            var tmp_expense = parseFloat(v.children('.data_expense_amount').data('expense_amount'));
            var tmp_investment = v.children('.data_investmentreturn').data('investmentreturn');
            var tmp_end = v.children('.data_end_value').data('end_value');
            var tmp_net_return = v.children('.data_net_return').data('net_return');
            var tmp_twr = v.children('.data_twr').data('twr');

            flow += tmp_flow;
            income += tmp_income;
            expense += tmp_expense;
        });

        $(".begin_value").text("$" + begin_value.toLocaleString());//Set the begin value text
        $(".selected_flows").text("$" + flow.toLocaleString());//Set the end value text
        $(".selected_income").text("$" + income.toLocaleString());//Set the end value text
        $(".selected_expenses").text("$" + expense.toLocaleString());//Set the end value text
        $(".end_value").text("$" + end_value.toLocaleString());//Set the end value text
    },

    /*    LineBarChart : function(){
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

        DateSelection: function(){
            var start = moment().subtract(29, 'days');
            var end = moment();

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

    /*        $("#fromfield").shieldDatePicker({
                events: {
                    change: function (e) {
    //                    funcs.ZoomChart();
                    }
                }
            });
            $("#tofield").shieldDatePicker({
                events: {
                    change: function (e) {
    //                    funcs.ZoomChart();
                    }
                }
            });*/

    /*        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
                var returns = new Array();
                try {
                    var count = 0;
                    var start_date = picker.startDate.format('MMMM D, YYYY');
                    var end_date = picker.endDate.format('MMMM D, YYYY');
                    var sday = new Date(start_date);
                    var eday = new Date(end_date);

                    $('#IntervalTable tbody tr').each(function() {
                        $(this).children('td, th').css('backgroundColor', '#DADADB');
                    });

                    $('.end_date').each(function(i, obj) {
                        var cur = $.datepicker.parseDate("m-d-yy", $(obj).text());
                        var dat = new Date(cur);
                        if(dat <= eday && dat >= sday){
                            $(this).closest('tr').children('td, th').css('background-color','#98FB98');
                            var val = $(obj).siblings('.net_return').data('net_return');
                            if(val > 1.15 || val < 0.85) {
    //                            console.log(val);
                                $(obj).siblings('.net_return').css('background-color', 'red');
                            }
                            returns.push(val);
                        }
                    });

                    function CalculateReturn(r){
                        var val = 1;
                        $.each(r, function(k, v){
                            val = val * (v);
                            count+=1;
                        });
                        val = (val - 1) * 100;
                        return(val.toFixed(2));
                    }
                    var r = CalculateReturn(returns);
                    average = (r / count).toFixed(2);
                    annual = (average * 12).toFixed(2);
                    $(".calculated_return").text(r + "%");
                    $(".average_return").text(average + "%");
                    $(".annual_return").text(annual + "%");
                }catch(err){

                }
            });

            $('#reportrange').daterangepicker({
                startDate: start,
                endDate: end,
                maxDate: moment(),
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    '2019': [moment("20190101","YYYYMMDD"), moment("20191231","YYYYMMDD")],
                    'Inception': [moment("19000101","YYYYMMDD"), moment()]
                }
            }, cb);

            cb(start, end);
        },
    */
    DateSelection: function() {
    },

    TimelineChart2: function(){
        var self = this;
        am4core.useTheme(am4themes_animated);
        am4core.useTheme(am4themes_dark);
        var chart = am4core.create("linechartdiv", am4charts.XYChart);
        chart.padding(0, 15, 0, 15);
        chart.colors.step = 3;
//TODO: DEFAULT TO 3 MONTHS
        var mydata = {};
        var symbols = new Array("GSPC", "SP500BDT");
        var symbol_info;
        var final = [];

        $($(".data_net_return").get().reverse()).each(function(e) {
            var date = $(this).siblings(".data_end_date").data('date');
            var tmp = {
                date:$(this).siblings(".data_end_date").data('date'),
                twr:$(this).siblings(".data_twr").data('twr'),
                end_value:$(this).siblings(".data_end_value").data('end_value'),
                net_return:$(this).data('net_return')
            }
            mydata[date] = tmp;
        });

        //Get the index prices
        //TODO:  sdate and edate need to be dynamic, not this hardcoded nonsense
        $.post("index.php", {module:'ModSecurities', action:'PriceInteraction', todo:'getprice', symbol:symbols, sdate:'2018-01-01', edate:'2020-12-31'}, function(response) {
            symbol_info = $.parseJSON(response);
            var count = 0;
            $.each(symbol_info, function(a, symbol){
                $.each(symbol, function(k, v){
                    var parsed_date = $.datepicker.parseDate("yy-m-d", v.date)
                    var formatted = $.datepicker.formatDate( "mm-dd-yy", parsed_date);
                    if(typeof mydata[formatted] !== 'undefined') {
                        var tmp = mydata[formatted];
                        tmp['symbol_'+count] = v.value;
                        mydata[formatted] = tmp;
                    }
                });
                count = count + 1;
            });

            $.each(mydata, function(k, v){
                v.amcharts_date = new Date($.datepicker.parseDate("m-d-yy", v.date));
//                console.log(v.symbol_1);
                if(typeof(v.symbol_1) === 'undefined'){
                    v.symbol_1 = 0;
                }
                final.push(v);
            });

        }).done(function(){
//            console.log(data);
//            console.log(final);///omni8439   felipe.luna@omnisrv.com
            chart.data = final;

// the following line makes value axes to be arranged vertically.
            chart.leftAxesContainer.layout = "vertical";

// uncomment this line if you want to change order of axes
//chart.bottomAxesContainer.reverseOrder = true;
            var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
            dateAxis.renderer.grid.template.location = 0;
            dateAxis.renderer.ticks.template.length = 8;
            dateAxis.renderer.ticks.template.strokeOpacity = 0.1;
            dateAxis.renderer.grid.template.disabled = true;
            dateAxis.renderer.ticks.template.disabled = false;
            dateAxis.renderer.ticks.template.strokeOpacity = 0.2;
            dateAxis.renderer.minLabelPosition = 0.01;
            dateAxis.renderer.maxLabelPosition = 0.99;
            dateAxis.keepSelection = true;

            dateAxis.groupData = true;
            dateAxis.groupCount = 600;
            dateAxis.minZoomCount = 1;

//            console.log(dateAxis.mainBaseInterval);

// these two lines makes the axis to be initially zoomed-in
// dateAxis.start = 0.7;
// dateAxis.keepSelection = true;
            var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
            valueAxis.tooltip.disabled = true;
            valueAxis.zIndex = 1;
            valueAxis.renderer.baseGrid.disabled = true;
// height of axis
            valueAxis.height = am4core.percent(65);

            valueAxis.renderer.gridContainer.background.fill = am4core.color("#000000");
            valueAxis.renderer.gridContainer.background.fillOpacity = 0.05;
            valueAxis.renderer.inside = true;
            valueAxis.renderer.labels.template.verticalCenter = "bottom";
            valueAxis.renderer.labels.template.padding(2, 2, 2, 2);

//valueAxis.renderer.maxLabelPosition = 0.95;
            valueAxis.renderer.fontSize = "0.8em"

            var series1 = chart.series.push(new am4charts.LineSeries());
            series1.dataFields.dateX = "amcharts_date";
            series1.dataFields.valueY = "twr";
            series1.dataFields.valueX = "end_value";
//        series1.dataFields.valueYShow = "change";
//            series1.tooltipText = "{name}: {valueY.change.formatNumber('[#0c0]+#.00|[#c00]#.##|0')}%";
//            series1.tooltipText = "{name}: {valueY.formatNumber('[#0c0]+#.00|[#c00]#.##|0')}%, (${endValue.formatNumber('###,###.##')})";//";
            series1.tooltipText = "Acct Value: (${valueX.formatNumber('###,###.##')})";//";
            series1.name = "TWR";
            series1.tooltip.getFillFromObject = false;
            series1.tooltip.getStrokeFromObject = true;
            series1.tooltip.background.fill = am4core.color("#fff");
            series1.tooltip.background.strokeWidth = 2;
            series1.tooltip.label.fill = series1.stroke;
            series1.groupFields.valueY = "open";
            series1.dataItems.template.locations.dateX = 0;

            var series2 = chart.series.push(new am4charts.LineSeries());
            series2.dataFields.dateX = "amcharts_date";
            series2.dataFields.valueY = "symbol_0";
            series2.dataFields.valueYShow = "changePercent";
            series2.tooltipText = "{name}: {valueY.changePercent.formatNumber('[#0c0]+#.00|[#c00]#.##|0')}%, ({valueY.formatNumber('###,###.##')})";
            series2.name = "S&P 500";
            series2.tooltip.getFillFromObject = false;
            series2.tooltip.getStrokeFromObject = true;
            series2.tooltip.background.fill = am4core.color("#fff");
            series2.tooltip.background.strokeWidth = 2;
            series2.tooltip.label.fill = series2.stroke;
            series2.groupFields.valueY = "open";
            series2.dataItems.template.locations.dateX = 0;
            /*
                        var series3 = chart.series.push(new am4charts.LineSeries());
                        series3.dataFields.dateX = "amcharts_date";
                        series3.dataFields.valueY = "symbol_1";
            //            series3.dataFields.valueYShow = "changePercent";
                        series3.tooltipText = "{name}: {valueY.changePercent.formatNumber('[#0c0]+#.00|[#c00]#.##|0')}%";
                        series3.name = "SP500BDT";
                        series3.tooltip.getFillFromObject = false;
                        series3.tooltip.getStrokeFromObject = true;
                        series3.tooltip.background.fill = am4core.color("#fff");
                        series3.tooltip.background.strokeWidth = 2;
                        series3.tooltip.label.fill = series3.stroke;
            */

            /*
                        var valueAxis2 = chart.yAxes.push(new am4charts.ValueAxis());
                        valueAxis2.tooltip.disabled = true;
                // height of axis
                        valueAxis2.height = am4core.percent(35);
                        valueAxis2.zIndex = 3
                // this makes gap between panels
                        valueAxis2.marginTop = 30;
                        valueAxis2.renderer.baseGrid.disabled = true;
                        valueAxis2.renderer.inside = true;
                        valueAxis2.renderer.labels.template.verticalCenter = "bottom";
                        valueAxis2.renderer.labels.template.padding(2, 2, 2, 2);
                //valueAxis.renderer.maxLabelPosition = 0.95;
                        valueAxis2.renderer.fontSize = "0.8em";

                        valueAxis2.renderer.gridContainer.background.fill = am4core.color("#000000");
                        valueAxis2.renderer.gridContainer.background.fillOpacity = 0.05;
            */
            /*        var volumeSeries = chart.series.push(new am4charts.StepLineSeries());
                    volumeSeries.fillOpacity = 1;
                    volumeSeries.fill = series1.stroke;
                    volumeSeries.stroke = series1.stroke;
                    volumeSeries.dataFields.dateX = "date";
                    volumeSeries.dataFields.valueY = "quantity";
                    volumeSeries.yAxis = valueAxis2;
                    volumeSeries.tooltipText = "Volume: {valueY.value}";
                    volumeSeries.name = "Series 2";
            // volume should be summed
            //        volumeSeries.groupFields.valueY = "sum";
                    volumeSeries.tooltip.label.fill = volumeSeries.stroke;*/
            chart.cursor = new am4charts.XYCursor();

            /*            var scrollbarX = new am4charts.XYChartScrollbar();
                        scrollbarX.series.push(series1);
                        scrollbarX.marginBottom = 20;
                        var sbSeries = scrollbarX.scrollbarChart.series.getIndex(0);
                        sbSeries.dataFields.valueYShow = undefined;
                        chart.scrollbarX = scrollbarX;*/


            /**
             * Set up external controls
             */

// Date format to be used in input fields
            var inputFieldFormat = "yyyy-MM-dd";

            function SetButtonColor(element, color){
                $(".amcharts-input").css('backgroundColor', 'lightgray');
                element.css('backgroundColor', color);
            }

            document.getElementById("lyr").addEventListener("click", function() {
                zoomToDatesCustom("2019-01-01", "2019-12-31");
                SetButtonColor($(this), 'lightgreen');
            });

            document.getElementById("b1m").addEventListener("click", function() {
                var d = GetDateMinusMonths(1);
                var start = chart.dateFormatter.format(d, "yyyy-MM-dd");
                var end = chart.dateFormatter.format(Date(), "yyyy-MM-dd");
                SetButtonColor($(this), 'lightgreen');
                zoomToDatesCustom(start, end);
                /*                var max = dateAxis.groupMax["day1"];
                                var date = new Date(max);
                                am4core.time.add(date, "month", -1);
                                zoomToDates(date);
                                */
            });

            document.getElementById("b3m").addEventListener("click", function() {
                var d = GetDateMinusMonths(3);
                var start = chart.dateFormatter.format(d, "yyyy-MM-dd");
                var end = chart.dateFormatter.format(Date(), "yyyy-MM-dd");
                SetButtonColor($(this), 'lightgreen');
                zoomToDatesCustom(start, end);
            });

            document.getElementById("b6m").addEventListener("click", function() {
                var d = GetDateMinusMonths(6);
                var start = chart.dateFormatter.format(d, "yyyy-MM-dd");
                var end = chart.dateFormatter.format(Date(), "yyyy-MM-dd");
                SetButtonColor($(this), 'lightgreen');
                zoomToDatesCustom(start, end);
            });

            document.getElementById("b1y").addEventListener("click", function() {
                var d = GetDateMinusMonths(12);
                var start = chart.dateFormatter.format(d, "yyyy-MM-dd");
                var end = chart.dateFormatter.format(Date(), "yyyy-MM-dd");
                SetButtonColor($(this), 'lightgreen');
                zoomToDatesCustom(start, end);
            });

            document.getElementById("bytd").addEventListener("click", function() {
                var d = GetDateMinusMonths(1);
                var year = d.getFullYear();
                var start = chart.dateFormatter.format(d, year + "-01-01");
                var end = chart.dateFormatter.format(Date(), "yyyy-MM-dd");
                SetButtonColor($(this), 'lightgreen');
                zoomToDatesCustom(start, end);
            });

            document.getElementById("bmax").addEventListener("click", function() {
                var min = new Date(dateAxis.groupMin.day1);//Get the minimum date from the chart
                var max = new Date(dateAxis.groupMax.day1);
                var start = chart.dateFormatter.format(min, "yyyy-MM-dd");
                var end = chart.dateFormatter.format(max, "yyyy-MM-dd");
                SetButtonColor($(this), 'lightgreen');
//                console.log(start);
//                console.log(end);
                zoomToDatesCustom(start, end);
//                console.log(chart.dateFormatter.format(min, "yyyy-MM-dd"));
                /*
                var min = dateAxis.groupMin["day1"];
                var date = new Date(min);
                zoomToDates(date);*/
            });

            dateAxis.events.on("selectionextremeschanged", function() {
                updateFields();
            });

//            dateAxis.events.on("extremeschanged", updateFields);

            $("#fromfield").datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function(e){
                updateZoom();
                var start = $(this).val();
                var end = $("#tofield").val();
                zoomToDatesCustom(start, end);
            });

            $("#tofield").datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function(e){
                updateZoom();
                var start = $("#fromfield").val();
                var end = $(this).val();
                zoomToDatesCustom(start, end);
            });

            /*            $("#fromfield").shieldDateTimePicker({
                            format:"{0:yyyy-MM-dd}",
                            textTemplate: "{0:yyyy-MM-dd}",
                            editable: true,
                            events: {
                                change: function (e) {
                                    updateZoom();
                                }
                            }
                        });

                        $("#tofield").shieldDateTimePicker({
                            format:"{0:yyyy-MM-dd}",
                            textTemplate: "{0:yyyy-MM-dd}",
                            editable: true,
                            events: {
                                change: function (e) {
                                    updateZoom();
                                }
                            }
                        });
            */
            //Returns the Date object of today - num months
            function GetDateMinusMonths(num_months){
                var dt = new Date();
                var month = dt.getMonth();
                month = month - num_months;
                dt.setMonth(month);
                return dt;
            }

            function updateFields() {
                var minZoomed = dateAxis.minZoomed + am4core.time.getDuration(dateAxis.mainBaseInterval.timeUnit, dateAxis.mainBaseInterval.count) * 0.5;
                document.getElementById("fromfield").value = chart.dateFormatter.format(minZoomed, inputFieldFormat);
                document.getElementById("tofield").value = chart.dateFormatter.format(new Date(dateAxis.maxZoomed), inputFieldFormat);
            }

            document.getElementById("fromfield").addEventListener("keyup", updateZoom);
            document.getElementById("tofield").addEventListener("keyup", updateZoom);

            var zoomTimeout;
            function updateZoom() {
                if (zoomTimeout) {
                    clearTimeout(zoomTimeout);
                }
                zoomTimeout = setTimeout(function() {
                    var start = document.getElementById("fromfield").value;
                    var end = document.getElementById("tofield").value;

                    if ((start.length < inputFieldFormat.length) || (end.length < inputFieldFormat.length)) {
                        return;
                    }
                    var startDate = chart.dateFormatter.parse(start, inputFieldFormat);
                    var endDate = chart.dateFormatter.parse(end, inputFieldFormat);

                    if (startDate && endDate) {
                        dateAxis.zoomToDates(startDate, endDate);
                    }
                }, 500);
            }

            function zoomToDates(date) {
                var min = dateAxis.groupMin["day1"];
                var max = dateAxis.groupMax["day1"];
                dateAxis.keepSelection = true;
                //dateAxis.start = (date.getTime() - min)/(max - min);
                //dateAxis.end = 1;

                dateAxis.zoom({start:(date.getTime() - min)/(max - min), end:1});
            }

            function zoomToDatesCustom(start, end){
                dateAxis.zoomToDates(start, end);
                self.CalculateTWR(start, end);
            }

            chart.events.on("ready", function () {
                var d = GetDateMinusMonths(3);
                var start = chart.dateFormatter.format(d, "yyyy-MM-dd");
                var end = chart.dateFormatter.format(Date(), "yyyy-MM-dd");
                SetButtonColor($("#b3m"), "lightgreen");
                console.log('auto zoom');

                zoomToDatesCustom(start, end);

                /*                dateAxis.zoomToDates(
                                    new Date(2018, 3, 23),
                                    new Date(2018, 3, 26)
                                );*/
            });

        });
    },

    Clock : function(){
        am4core.useTheme(am4themes_animated);
//        am4core.useTheme(am4themes_dark);

// create chart
        var chart = am4core.create("linechartdiv", am4charts.GaugeChart);


        chart.exporting.menu = new am4core.ExportMenu();

        chart.startAngle = -90;
        chart.endAngle = 270;

        var axis = chart.xAxes.push(new am4charts.ValueAxis());
        axis.min = 0;
        axis.max = 12;
        axis.strictMinMax = true;

        axis.renderer.line.strokeWidth = 8;
        axis.renderer.line.strokeOpacity = 1;
        axis.renderer.minLabelPosition = 0.05; // hides 0 label
        axis.renderer.inside = true;
        axis.renderer.labels.template.radius = 30;
        axis.renderer.grid.template.disabled = true;
        axis.renderer.ticks.template.length = 12;
        axis.renderer.ticks.template.strokeOpacity = 1;

// serves as a clock face fill
        var range = axis.axisRanges.create();
        range.value = 0;
        range.endValue = 12;
        range.grid.visible = false;
        range.tick.visible = false;
        range.label.visible = false;

        var axisFill = range.axisFill;
        axisFill.fillOpacity = 0.8;
        axisFill.disabled = false;
        axisFill.fill = am4core.color("#FFFFFF");

// hands
        var hourHand = chart.hands.push(new am4charts.ClockHand());
        hourHand.radius = am4core.percent(60);
        hourHand.startWidth = 10;
        hourHand.endWidth = 10;
        hourHand.rotationDirection = "clockWise";
        hourHand.pin.radius = 8;
        hourHand.zIndex = 1;

        var minutesHand = chart.hands.push(new am4charts.ClockHand());
        minutesHand.rotationDirection = "clockWise";
        minutesHand.startWidth = 7;
        minutesHand.endWidth = 7;
        minutesHand.radius = am4core.percent(78);
        minutesHand.zIndex = 2;

        var secondsHand = chart.hands.push(new am4charts.ClockHand());
        secondsHand.fill = am4core.color("#DD0000");
        secondsHand.stroke = am4core.color("#DD0000");
        secondsHand.radius = am4core.percent(85);
        secondsHand.rotationDirection = "clockWise";
        secondsHand.zIndex = 3;
        secondsHand.startWidth = 1;

        updateHands();

        setInterval(function () {
            updateHands();
        }, 1000)

        function updateHands() {
            // get current date
            var date = new Date();
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var seconds = date.getSeconds();

            // set hours
            hourHand.showValue(hours + minutes / 60, 0);
            // set minutes
            minutesHand.showValue(12 * (minutes + seconds / 60) / 60, 0);
            // set seconds
            secondsHand.showValue(12 * date.getSeconds() / 60, 300);
        }
    },

    FloatHead : function(){
        $('#IntervalTable').floatThead({
            position: 'fixed'
        });

        /*        $('a#change-dom').click(function(){ //click to remove
                    $(this).parent().remove();
                    //DOM has changed. must reflow floatThead
                    $demo1.floatThead('reflow');
                });*/
    },

    registerEvents : function() {
        this.DateSelection();
//        this.FloatHead();
//        this.Clock();
        if (am4core.isReady) {
            this.TimelineChart2();
        } else {
            am4core.ready(this.TimelineChart2());
        }
//        this.LineBarChart();
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


/*

<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
    <i class="fa fa-calendar"></i>&nbsp;
    <span></span> <i class="fa fa-caret-down"></i>
</div>

<script type="text/javascript">
$(function() {

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);

});
</script>


 */