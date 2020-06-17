jQuery.Class("MonthSelection",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new MonthSelection();
        return instance;
    }
},{

    ClickEvents: function(){
        var self = this;
    },

    registerEvents : function() {
        this.ClickEvents();
    },

    firstLoad : function(){
        $("#select_start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                $("#ui-datepicker-div").hide();
            },
            onChangeMonthYear: function(year, month, inst){
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                $("#ui-datepicker-div").hide();
            }
        });

        $("#select_end_date").datepicker({
            inline:true,
            maxDate:new Date,
            changeMonth: true,
            changeYear: true,
            onClose: function(dateText, inst) {
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                $("#ui-datepicker-div").hide();
            },
            onChangeMonthYear: function(year, month, inst){
                var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                $(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
                $("#ui-datepicker-div").hide();
            }
        });

        $("#select_end_date").focus(function () {
            $(".ui-datepicker-calendar").hide();
            $(".ui-datepicker-prev").hide();
            $(".ui-datepicker-next").hide();

            $("#ui-datepicker-div").position({
                my: "center top",
                at: "center bottom",
                of: $(this)
            });
        });

        $("#select_start_date").focus(function () {
            $(".ui-datepicker-calendar").hide();
            $(".ui-datepicker-prev").hide();
            $(".ui-datepicker-next").hide();

            $("#ui-datepicker-div").position({
                my: "center top",
                at: "center bottom",
                of: $(this)
            });
        });

        $("#report_date_selection").change(function(e){
            e.stopImmediatePropagation();

            var selected = $("#report_date_selection").find(':selected');
            var start_date = selected.data('start_date');
            var end_date = selected.data('end_date');

            if(start_date.length === 0 || end_date.length === 0)
                return;

//            $("#select_start_date").val(start_date);
//            $("#select_end_date").val(end_date);
            $("#select_start_date").swidget().value(new Date(start_date+"-01T08:05:00"));
            $("#select_end_date").swidget().value(new Date(end_date+"-01T08:05:00"));
/*            $("#ui-datepicker-div").hide();
            /*            var accounts = selected.data('account');
                        var calling_record = selected.data('calling');
                        var orientation = selected.data('orientation');
                        var report = selected.val();

                        if(report == "0")
                            return;

                        $("#reportselect").val('');
                        window.open("index.php?module=PortfolioInformation&view="+report+"&account_number="+accounts+"&calling_record="+calling_record+"&orientation="+orientation, "_blank");
            */
        });

        $("#calculate_report").click(function(e){
            e.stopImmediatePropagation();
            sdate = $("#select_start_date").val();
            edate = $("#select_end_date").val();
            var loc = window.location.href;
            loc += "&report_start_date=" + sdate + "&report_end_date=" + edate;
            window.location.href = loc;
        });
    }
});

jQuery(document).ready(function($) {
    var d = new Date();
    d.setMonth(d.getMonth() - 1);

    var start = $("#select_start_date").val();
    var end = $("#select_end_date").val();

    $("#select_start_date").shieldMonthYearPicker({
        max: d,
        openOnFocus: true,
        editable: true,
        value: new Date(start)
    });

    $("#select_end_date").shieldMonthYearPicker({
        max: d,
        openOnFocus: true,
        editable: true,
        value: new Date(end)
    });

    var instance = MonthSelection.getInstanceByView();
    instance.registerEvents();
    instance.firstLoad();
});