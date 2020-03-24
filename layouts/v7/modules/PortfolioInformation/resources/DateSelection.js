jQuery.Class("DateSelection",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new DateSelection();
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

/*        $("#fromfield").datepicker({
            format: 'yyyy-mm-dd'
        }).on('changeDate', function(e){
            updateZoom();
            var start = $(this).val();
            var end = $("#tofield").val();
            zoomToDatesCustom(start, end);
        });
        */
        $("#select_start_date").datepicker({
                format: 'yyyy-mm-dd',
                onClose: function (selectedDate) {
            }
        });

        $("#select_end_date").datepicker({
            format: 'yyyy-mm-dd',
            onClose: function (selectedDate) {
            }
        });

        /*        $("#select_end_date").datepicker({
                    defaultDate: "+1w",
                    changeMonth: true,
                    changeYear: true,
                    changeDay: true,
                    numberOfMonths: 3,
                    onClose: function (selectedDate) {
                    }
                });*/

        $("#report_date_selection").change(function(e){
            e.stopImmediatePropagation();

            var selected = $("#report_date_selection").find(':selected');
            var start_date = selected.data('start_date');
            var end_date = selected.data('end_date');

            $("#select_start_date").val(start_date);
            $("#select_end_date").val(end_date);
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
            edate = $("#select_end_date").val();
            sdate = $("#select_start_date").val();
            var loc = window.location.href;
            loc += "&report_start_date=" + sdate + "&report_end_date=" + edate;
            window.location.href = loc;
        });
    }
});

jQuery(document).ready(function($) {
    var instance = DateSelection.getInstanceByView();
    instance.registerEvents();
    instance.firstLoad();
});