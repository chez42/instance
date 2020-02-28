var filters = [];
var function_running = false;

jQuery.Class("Tools_Js",{
    currentInstance : false,
    getInstanceByView : function(){
        var instance = new Tools_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = $(this);
        $("#daily_intervals").click(function(){
            if(function_running == true) {
                alert("Process already running.  Wait until it is finished loading and try again");
                return;
            }
            function_running = true;

            $("#inception_loader").show();
            var selected_rep_codes = new Array();
            $.each( $(".rep_code_select").select2('data'), function( key, value ) {
                selected_rep_codes.push(value.id);
            });
            var inception = 0;
            if($("#inception").is(':checked'))
                inception = 1;

            $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'daily_intervals',
                                 inception:inception, rep_codes:selected_rep_codes}, function(response){
                function_running = false;
                $("#inception_loader").hide();
                alert("Finished Running Daily Intervals" + response);
            });
        });

        $("#parse").click(function(){
            if(function_running == true) {
                alert("Process already running.  Wait until it is finished loading and try again");
                return;
            }
            function_running = true;

            $("#inception_loader").show();
            var selected_custodian = $(".custodian_select").select2('data');
            var selected_parse_type = $(".parse_files").select2('data');
            var num_days = $("#num_days").val();
//            console.log(selected_custodian);
            $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'parse_files',
                custodian:selected_custodian.id, parse_type:selected_parse_type.id, num_days:num_days}, function(response){
                function_running = false;
                $("#inception_loader").hide();
                alert(response);
//                alert("Finished parsing " +  selected_parse_type.text + " for " + selected_custodian.text);
            });
        });

        $("#push").click(function(){
            if(function_running == true) {
                alert("Process already running.  Wait until it is finished loading and try again");
                return;
            }
            function_running = true;

            $("#inception_loader").show();
            var selected_custodian = $(".custodian_select_push").select2('data');
            var selected_push_type = $(".push_files").select2('data');

            $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'push_files',
                custodian:selected_custodian.id, push_type:selected_push_type.id}, function(response){
                function_running = false;
                $("#inception_loader").hide();
                alert(response);
//                alert("Finished parsing " +  selected_parse_type.text + " for " + selected_custodian.text);
            });
        });
    },

    SelectEvents: function(){
        $(".rep_code_select").select2({
            placeholder: "Select rep code(s)"
        });

        $(".custodian_select").select2({
            placeholder: "Select custodian"
        });

        $(".custodian_select_push").select2({
            placeholder: "Custodian to push"
        });

        $(".parse_files").select2({
            placeholder: "Parse Type"
        });

        $(".push_files").select2({
            placeholder: "Push Operation"
        });

        $("#select_start_date").datepicker({
            defaultDate: "01/01/2019",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function (selectedDate) {
            }
        });

        $("#select_end_date").datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function (selectedDate) {
            }
        }).datepicker("setDate", new Date())
    },

    registerEvents : function() {
        this.ClickEvents();
        this.SelectEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = Tools_Js.getInstanceByView();
    instance.registerEvents();
});