jQuery.Class("TrailingRevenue_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new TrailingRevenue_Js();
        return instance;
    }
},{
    DisplayHistoricalChart: function(){

    },

    registerEventDateClick : function(){
        $("#revenue_start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            },
            onChangeMonthYear: function(year, month, inst){
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            }
        });
        $("#revenue_end_date").datepicker({
            changeMonth: true,
            changeYear: true,
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            },
            onChangeMonthYear: function(year, month, inst){
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
            }
        });

        $("#revenue_start_date").focus(function () {
            $(".ui-datepicker-calendar").hide();
            $("#ui-datepicker-div").position({
                my: "center top",
                at: "center bottom",
                of: $(this)
            });
        });

        $("#revenue_end_date").focus(function () {
            $(".ui-datepicker-calendar").hide();
            $("#ui-datepicker-div").position({
                my: "center top",
                at: "center bottom",
                of: $(this)
            });
        });

        $("#CalculateRevenue").click(function(e){
            var chart = AjaxDynamicZoomChart.getInstanceByView();
            var start_date = $("#revenue_start_date").val();
            var end_date = $("#revenue_end_date").val();
            chart.CreateChart("management_fees", "trailing12Zoom", start_date, end_date);
        });
    },

    registerEvents : function() {
//        var chart = AjaxDynamicChart.getInstanceByView();
//        this.registerEventDateClick();
        var chart = AjaxDynamicZoomChart.getInstanceByView();
        var start_date = $("#revenue_start_date").val();
        var end_date = $("#revenue_end_date").val();
        chart.CreateChart("management_fees", "trailing12Zoom", start_date, end_date, '#404040', '#00e64d', '#00cc44');

//        this.DisplayHistoricalChart();
    }
});

jQuery(document).ready(function($) {
    var instance = TrailingRevenue_Js.getInstanceByView();
    instance.registerEvents();
//    chart.CreateChart("trailing12revenue", "2018-01-01", "2018-02-28");
    //var instance = TrailingRevenue_Js.getInstanceByView();
});