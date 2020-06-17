jQuery.Class("GHReport_Js",{
    currentInstance : false,
    chartInfo : [],
    getInstanceByView : function(){
        var instance = new GHReport_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;

        $(".ExportReport").click(function(e){
            e.stopImmediatePropagation();
            self.chartInfo.exporting.getImage("jpg").then(function(imgData){
                $("#pie_image").val(encodeURIComponent(imgData));
                //console.log(imgData);
                $("#export").submit();
            });
//            $("#pie_image").val(imgData);
//            console.log($("#pie_image").val());
//            $("#export").submit();
        });
/*
        $("#statement_settings").click(function(e){
            $.ajax({
                'url': "index.php?module=PortfolioInformation&view=Statements",
                'success': function success(data, textStatus, xhr) {
                    $("<div>" + data + "</div>").dialog({
                        "width": "auto",
                        "height": "auto",
                        "close": function (e, ui) { $(this).remove(); }
                    });
                }
            });
        });*/

        am4core.options.commercialLicense = true;
        var chart = am4core.create("dynamic_pie_holder", am4charts.PieChart3D);
        var chartData = $.parseJSON($("#holdings_values").val());

        chart.data = chartData;

// Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries3D());
        pieSeries.slices.template.stroke = am4core.color("#555354");
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "title";
        pieSeries.fontSize = 14;

        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;

//        pieSeries.labels.template.disabled = true;
//        pieSeries.ticks.template.disabled = true;

//        pieSeries.slices.template.states.getKey("hover").properties.shiftRadius = 0;
//        pieSeries.slices.template.states.getKey("hover").properties.scale = 1.1;

        var colorSet = new am4core.ColorSet();
        var colors = [];
        $.each(chartData,function(){
            var element = jQuery(this);
            colors.push(element["0"].color);
        });

        colorSet.list = colors.map(function(color) {
            return new am4core.color(color);
        });
        pieSeries.colors = colorSet;

        self.chartInfo = chart;
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = GHReport_Js.getInstanceByView();
    instance.registerEvents();
    /*
        var pie = DynamicPie_Js.getInstanceByView();
        pie.registerEvents();
    //    var chart = DynamicChart_JS.getInstanceByView();

        pie.CreatePie("dynamic_pie_holder", "holdings_values");
        pie.CreatePie("sector_pie_holder", "sector_values", false);
        pie.CreateGraph("dynamic_chart_holder", "t12_balances", "intervalenddateformatted", "intervalendvalue");

    //    chart.CreateChart("dynamic_chart_holder", "t12_balances");*/
});