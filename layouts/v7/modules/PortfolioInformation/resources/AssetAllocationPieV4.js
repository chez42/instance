jQuery.Class("AssetAllocationPieV4",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AssetAllocationPieV4();
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

    ConfigurePiechart: function(chartDiv){
//        var provider = $("#asset_allocation_pie_container").data("pie");
        var chart = AmCharts.makeChart(chartDiv, {
            type: "pie",
            valueField: "value",
            titleField: "base_asset_class",
            colorField: 'color',
            urlField: 'url',
            numberFormatter: {precision:0, decimalSeparator:".", thousandsSeparator:","},
            depth3D: 12,
            angle: 24,
            fontSize: 8,
            urlTarget: '_blank',
            outlineColor: "#363942",
            outlineAlpha: 0.8,
            outlineThickness: 1,
            alignLabels:true,
            labelsEnabled: false,//true,
//            pullOutRadius:0,
//            startDuration:3,
//            labelsEnabled: true,
            autoMargins: true,
//            pullOutRadius: 10,
/*            balloon: {
                "fixedPosition": true
            },*/
            legend:{
                enabled:true,
                align:"center",
                position:"right",
                labelText: '',
                valueWidth:80
//                divId: chartDiv + "_legend",
            }
            /*            legend:{
                            "enabled": true
                        }*/
//            legend:{divId: "asset_allocationv4_legend", horizontalGap: 10, maxColumns: 2, position: "right"}
            //            dataProvider:provider,
/*            legend:{divId: chartDiv + "_legend"}/*, align: 'left', markerType: 'square', maxColumns : 2,
                position: 'bottom', labelText: '[[title]]', valueWidth: 100},
/*            export: {
                "enabled": false,
//                "legend":{"position":"bottom"}
            }*/
        });

//        chart.legend = new AmCharts.Legend();
        return chart;
    },

    ConfigurePieChartAM4: function(chartDiv){
        am4core.useTheme(am4themes_animated);
        var chart = am4core.create(chartDiv, am4charts.PieChart3D);
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
//        chart.dataSource.url = "index.php?module=PortfolioInformation&action=GetChartValues&chart_type=asset_allocationv4";
        chart.innerRadius = am4core.percent(40);
        chart.depth = 20;
//console.log(chart);
//var values = chart.dataSource;
//console.log(chart.data);
        chart.legend = new am4charts.Legend();
        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "value";
        series.dataFields.depthValue = "value";
        series.dataFields.category = "base_asset_class";
        series.slices.template.cornerRadius = 5;

        /* Set tup slice appearance */
        var slice = series.slices.template;
        slice.propertyFields.fill = "color";
        slice.propertyFields.fillOpacity = "opacity";
        slice.propertyFields.stroke = "color";
        slice.propertyFields.strokeDasharray = "strokeDasharray";
        slice.propertyFields.tooltipText = "tooltip";
        slice.propertyFields.url = "url";
        slice.urlTarget = "_blank";
//        series.colors.step = 3;
/*        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "value";
        pieSeries.dataFields.category = "base_asset_class";
        console.log(chart);*/
//        $("#asset_allocationv4").show();
        /*
        chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
        chart.innerRadius = am4core.percent(40);
        chart.depth = 120;
        chart.legend = new am4charts.Legend();

        var dataSource = new am4core.DataSource();
        dataSource.url = "index.php?module=PortfolioInformation&action=GetChartValues&chart_type=asset_allocationv4";
        dataSource.load();
        dataSource.valueField = "value";
        dataSource.categoryField = "base_asset_class";
        dataSource.events.on("done", function(ev) {
            // Data loaded and parsed
            console.log(ev.target.data);
        });
        chart.data = dataSource;

        var series = chart.series.push(new am4charts.PieSeries3D());
        series.dataFields.value = "value";
        series.dataFields.category = "base_asset_class";
        series.dataSource = dataSource;
        series.slices.template.cornerRadius = 5;
        series.colors.step = 3;
*/
        return chart;
    },

    GetChartDataAndRenderAM4: function(chart){
        var self = this;
        $.ajax({
            type: 'POST',
            url: "index.php?module=PortfolioInformation&action=GetChartValues",
            data: {'chart_type': 'asset_allocationv4'},
            success: function(data) {
                var chartData = $.parseJSON(data);
                chart.data = chartData;
                var grand_total = 0;
                $.each( chartData, function( index, value ){
                    grand_total = +grand_total + +value.value;
                });
//                console.log(chartData);

                $(".AssetAllocationHeader").find(".dashboardTitle").html("<strong>Total Assets: $" + self.numWithComma(grand_total) + "</strong>");
//                chart.validateData();
                $("#asset_allocationv4").show();
//                console.log(chartData);
//                chart.animateData(chartData, { duration: 1000 });
//                $("#asset_allocationv4").fadeIn("slow");
            }
        });
    },

    numWithComma: function(a) {
        return a.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    },


    GetChartDataAndRender: function(chart){
        var self = this;
//        var provider = $("#asset_allocation_pie_container").data("pie");
//        chart.dataProvider = provider;
//        chart.validateData();
//        chart.animateAgain();
        $.ajax({
            type: 'POST',
            url: "index.php?module=PortfolioInformation&action=GetChartValues",
            data: {'chart_type': 'asset_allocationv4'},
            success: function(data) {
                var chartData = $.parseJSON(data);
                console.log(chartData);
                chart.dataProvider = chartData;

                var grand_total = 0;
                $.each( chartData, function( index, value ){
                    grand_total = +grand_total + +value.value;
                });

                $(".AssetAllocationHeader").find(".dashboardTitle").html("<strong>Total Assets: $" + self.numWithComma(grand_total) + "</strong>");
                chart.validateData();
                $("#asset_allocationv4").show();
//                console.log(chartData);
//                chart.animateData(chartData, { duration: 1000 });
//                $("#asset_allocationv4").fadeIn("slow");
            }
        });
    }
});