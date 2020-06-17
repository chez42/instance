jQuery.Class("OmniSol_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new OmniSol_Module_Js();
        return instance;
    }
},{
    FirstLoad: function(){
        var date = new Date();
        date.setDate(date.getDate() - 1);
        var FormattedEnd = $.datepicker.formatDate( "mm/dd/yy", date );

        date.setDate(date.getDate() - 1);
        var FormattedStart = $.datepicker.formatDate( "mm/dd/yy", date);

        $("#start_date").val(FormattedStart);
        $("#start_date").datepicker({
            defaultDate: "-2d",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            onClose: function (selectedDate) {
            }
        });

        $("#end_date").val(FormattedEnd);
        $("#end_date").datepicker({
            defaultDate: "-1d",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 1,
            onClose: function (selectedDate) {
            }
        });
    },

    ClickEvents: function(){
        var self = this;
        $("#submit_status").click(function(){
            var tmpDate = $("#start_date").datepicker("getDate");
            var sdate = $.datepicker.formatDate("yy-mm-dd", tmpDate);

            tmpDate = $("#end_date").datepicker("getDate");
            var edate = $.datepicker.formatDate("yy-mm-dd", tmpDate);
            $.post("index.php", {module:'PortfolioInformation', action:'OmniSol', todo:'compare_status', start:sdate, end:edate}, function(response){
                var data = $.parseJSON(response);
                $("#td_closed").text(data.td.old_count);
                $("#td_new").text(data.td.new_count);

                $("#schwab_closed").text(data.schwab.old_count);
                $("#schwab_new").text(data.schwab.new_count);

                $("#pershing_closed").text(data.pershing.old_count);
                $("#pershing_new").text(data.pershing.new_count);

                $("#fidelity_closed").text(data.fidelity.old_count);
                $("#fidelity_new").text(data.fidelity.new_count);
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
        this.FirstLoad();
    }
});

jQuery(document).ready(function($) {
    var instance = OmniSol_Module_Js.getInstanceByView();
    instance.registerEvents();
});