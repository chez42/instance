jQuery(document).ready(function($){
    var show_global_book = $('#show_global_book').val();
    if(show_global_book)
        $('.show_global_book').show();
    else
        $('.show_global_book').hide();
    $('[name=result_total_value]').text($('[name=result_total_value_dynamic]').val());
    $('[name=result_market_value]').text($('[name=result_market_value_dynamic]').val());
    $('[name=result_cash_value]').text($('[name=result_cash_value_dynamic]').val());
    $('[name=result_annual_management_fee]').text($('[name=result_annual_management_fee_dynamic]').val());
    if($('[name=result_total_value_dynamic]').val() == $('.global_total').text())
        $('.show_global_book').hide();

    $('.recalculate').click(function(e){
        var id = $(this).attr("data-id");
        var account = $(this).attr("data-account-number");
        var progressInstance = jQuery.progressIndicator();
        $.post("index.php", {'module':'PortfolioInformation', 'action':'IndividualUpdate', 'account_number':account, 'record':id}, function(response){
            progressInstance.hide();
            $("<div style='width:640px; height:480px; display:block; overflow:auto'>" + response + "</div>").dialog({
                        modal:true,
                        title:"Calculation Results",
                        width:640,
                        height:480
                    });    
        });
        e.stopPropagation();
    });
    
    jQuery.CreateIncomeChart = function CreateIncomeChart(chartData, element){
        var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
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
        catAxis.gridCount = chartData.length;
            catAxis.labelRotation = 90;
        chart.write(element);
    }
    
    jQuery.CreateHoldingsChart = function CreateHoldingsChart(chartData){
        var chart;
        var legend;

        chart = new AmCharts.AmPieChart();

        chart.dataProvider = chartData;
        chart.titleField = "title";
        chart.valueField = "value";
        chart.colorField = 'color';
        chart.labelRadius = 30;
        chart.radius = 75;
        chart.labelText = "[[percents]]%";
        chart.hideLabelsPercent = 100;
        chart.textColor= "#FFFFFF";
        chart.depth3D = 0;
        chart.angle = 0;
        chart.outlineColor = "#363942";
        chart.outlineAlpha = 0.8;
        chart.outlineThickness = 1;
        chart.colors = ["#8383ff","#aade98","#eab378","#9bc9ce","#eddb92","#c8c8fa","#bfe1c3","#dadbb9","#e8cf84","#84b3e8","#d8adec"];
        chart.startDuration = 0;

        legend = new AmCharts.AmLegend();
        legend.align = "left";
        legend.markerType = "square";
        legend.maxColumns = 1;
        legend.position = "right";
        legend.marginRight = 20;
        legend.valueText = "$[[value]]";
        legend.valueWidth = 100;
        legend.switchable = false;
        legend.labelText = "[[title]]:";
        chart.addLegend(legend, 'legenddiv');

        // WRITE
        if($("#filtered_pie").length > 0) {
            chart.write("filtered_pie");
        }
        
    };
    
    jQuery.CreateLineChart = function CreateLineChart(chartData, element){
        if($("#"+element).length) {
            var chart;
            chart = new AmCharts.AmSerialChart();
            chart.dataProvider = chartData;
            chart.categoryField = "date";
            chart.marginTop = 25;
            chart.marginBottom = 80;
            chart.marginLeft = 50;
            chart.marginRight = 100;
            chart.startDuration = 1;
            chart.sequencedAnimation = false;

            var valueAxis = new AmCharts.ValueAxis();
            valueAxis.minimum = 0;
            chart.addValueAxis(valueAxis);

            var catAxis = chart.categoryAxis;
            catAxis.gridPosition = "start";
            catAxis.gridCount = chartData.length;
            catAxis.labelRotation = 90;

            var graph = new AmCharts.AmGraph();
            graph.valueField = "value";
            graph.balloonText = "Total: $[[value]]";
            graph.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
            graph.type = "line";
            graph.lineColor = "#000033";
            graph.bullet = 'round';
            chart.addGraph(graph);

            var graph2 = new AmCharts.AmGraph();
            graph2.valueField = "cash_value";
            graph2.balloonText = "Cash: $[[value]]";
            graph2.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
            graph2.type = "line";
            graph2.lineColor = '#02B90E';
            graph2.bullet = 'round';
            chart.addGraph(graph2);

            var graph3 = new AmCharts.AmGraph();
            graph3.valueField = "fixed_income";
            graph3.balloonText = "Fixed Income: $[[value]]";
            graph3.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
            graph3.type = "line";
            graph3.lineColor = '#8383ff';
            graph3.bullet = 'round';
            chart.addGraph(graph3);

            var graph4 = new AmCharts.AmGraph();
            graph4.valueField = "equities";
            graph4.balloonText = "Equities: $[[value]]";
            graph4.numberFormatter = {precision: 2, decimalSeparator: ".", thousandsSeparator: ","};
            graph4.type = "line";
            graph4.lineColor = '#6bd7d6';
            graph4.bullet = 'round';
            chart.addGraph(graph4);

            chart.write(element);
        }
    };
    
    jQuery.CreateAccountLineChart = function CreateAccountLineChart(chartData, element){
        var chart;
        chart = new AmCharts.AmSerialChart();
        chart.dataProvider = chartData;
        chart.categoryField = "date";
        chart.marginTop = 25;
        chart.marginBottom = 80;
        chart.marginLeft = 50;
        chart.marginRight = 100;
        chart.startDuration = 1;
        chart.mouseWheelZoomEnabled = true;
        chart.sequencedAnimation = false;

        var valueAxis = new AmCharts.ValueAxis();
        valueAxis.minimum = 0;
        chart.addValueAxis(valueAxis);

        var catAxis = chart.categoryAxis;
        catAxis.gridPosition = "start";
        catAxis.gridCount = chartData.length;
        catAxis.labelRotation = 90;

        var graph = new AmCharts.AmGraph();
        graph.valueField = "value";
        graph.balloonText="Currently Active Accounts: [[value]]\r\n[[new_accounts]] new accounts\r\n[[closed_accounts]] Closed accounts";
        graph.type = "line";
        graph.lineColor = "#000033";
        graph.bullet = 'round';
        chart.addGraph(graph);
/*
        var graph2 = new AmCharts.AmGraph();
        graph2.valueField = "new_accounts";
        graph2.balloonText="New Accounts: [[value]]";
        graph2.type = "line";
        graph2.lineColor = '#02B90E';
        graph2.bullet = 'round';
        chart.addGraph(graph2);
        
        var graph3 = new AmCharts.AmGraph();
        graph3.valueField = "closed_accounts";
        graph3.balloonText="Closed Accounts: [[value]]";
        graph3.type = "line";
        graph3.lineColor = '#8383ff';
        graph3.bullet = 'round';
        chart.addGraph(graph3);
        */
        chart.write(element);
    }
    
    var value = ($(document).find("#filtered_pie_value").val());
    if(typeof(value) !== "undefined")
    {
        var values = $.parseJSON(value);
        var chartData = values;
        $.CreateHoldingsChart(chartData);
    }
    
    var revenue_value = ($(document).find("#filtered_revenue_value").val());
    if(typeof(revenue_value) !== "undefined")
    {
        var values = $.parseJSON(revenue_value);
        var chartData = values;
        $.CreateIncomeChart(chartData, 'filtered_revenue_graph');
    }

    var asset_value = ($(document).find("#filtered_assets_value").val());
    if(typeof(asset_value) !== "undefined")
    {
        var values = $.parseJSON(asset_value);
        var chartData = values;
        $.CreateLineChart(chartData, 'filtered_assets_graph');
    }

    var active_value = ($(document).find("#filtered_active_value").val());
    if(typeof(active_value) !== "undefined")
    {
        var values = $.parseJSON(active_value);
        var chartData = values;
        $.CreateAccountLineChart(chartData, 'filtered_active_graph');
    }

});