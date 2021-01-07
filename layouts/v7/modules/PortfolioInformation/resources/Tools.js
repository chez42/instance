var filters = [];
var active_rep_codes = [];
var file_info = [];
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

        $("#find").click(function(){
            if(function_running == true) {
                alert("Process already running.  Wait until it is finished loading and try again");
                return;
            }
            function_running = true;

            $("#inception_loader").show();
            var sdate = $("#type_sdate").val();
            var edate = $("#type_edate").val();
            var file_type = $(".type_select").select2('data');
            var data = new Array();

            $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'find_missing',
                file_sdate:sdate, file_edate:edate, file_type:file_type.id}, function(response){
                $("#inception_loader").hide();
                function_running = false;
                data = $.parseJSON(response);
                $(".missing_files").empty();
                data.forEach(function(i) {
                    $(".missing_files").show();
                    $(".missing_files").append("<p>" + i.directory + "</p>");
                });
            });
        });

/*        $('.rep_code').on('click', function(e){
            alert("HERE");
        });*/
    },

    SelectEvents: function(){
        $(".rep_code_select").select2({
            placeholder: "Select rep code(s)"
        });

        $(".type_select").select2({
            placeholder: "Select file type"
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
            defaultDate: "01/01/2020",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function (selectedDate) {
            }
        });

        $("#type_sdate").datepicker({
            altFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function (selectedDate) {
            }
        }).datepicker("setDate", new Date());

        $("#type_edate").datepicker({
            altFormat: "yy-mm-dd",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function (selectedDate) {
            }
        }).datepicker("setDate", new Date());
    },

    SetupCalendar: function(){
        var self = this;
        $('#missing_files_calendar').fullCalendar({
            weekends: true, // will hide Saturdays and Sundays
            dayClick: function() {
                var data = $.parseJSON($(this).attr("missing"));
                $(".missing_files_section").show();
                $(".missing_files_list").empty();
                $(".missing_date").html($(this).data('date'));
                $.each(data, function(k, v){
                    $(".missing_files_list").append('<li class="rep_code" data-value="'+v+'">' + v + '</li>');
                });
            },

            dayRender: function(date, cell){
//                cell.css("background-color", "red");
//                cell.attr('hey', 'now');
                var ymd = date.format();
/*                $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'get_rep_codes_for_date',
                    file_info_date:ymd}, function(response){
                    function_running = false;
                    console.log(response);
                });*/
            },

            eventAfterAllRender:function(){
                var start = $('#missing_files_calendar').fullCalendar('getView').start.format();
                var end = $('#missing_files_calendar').fullCalendar('getView').end.format();
                $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'get_active_rep_codes',
                    start_date:start, end_date:end}, function(response){
                    function_running = false;
                    active_rep_codes = $.parseJSON(response);

                    $.post("index.php", {module:'PortfolioInformation', action:'Tools', todo:'get_file_info',
                        start_date:start, end_date:end}, function(response){
                        file_info = $.parseJSON(response);
//                        console.log(active_rep_codes);
//                        console.log(file_info);
                        self.MarkCalendar();
                    });
                });
            }
        });
    },

    CompareArray: function(input, target){
        var found;
        for (var prop in input) {
            if(input[prop] == target){
                found = prop;
            }
        };

        return found;
    },

    MarkCalendar: function(){
        var self = this;
        var days = $(".fc-day");
        var missing = new Array();
//        console.log(file_info);
        days.each(function(index, element) {
//            console.log($(this).data("date"));
            var missing_repcodes = JSON.parse(JSON.stringify(active_rep_codes));
            var day = $(this).data("date");
            $.each(file_info,function(k, v){
                var d1 = new Date(k);
                var d2 = new Date(day);

                if(d1.getTime() === d2.getTime()){
                    $.each(v,function(a,b){
                        var match = self.CompareArray(missing_repcodes, b.rep_code);
                        delete missing_repcodes[match];
                    });
                }
            });

            if($(missing_repcodes).length > 0){
                $(element).css("background-color", "red");
            }
            $(element).attr("missing",JSON.stringify(missing_repcodes));
        });
        $(".fc-future").css("background-color", "yellow");
    },

    registerEvents : function() {
        this.ClickEvents();
        this.SelectEvents();
        this.SetupCalendar();
    }
});

jQuery(document).ready(function($) {
    var instance = Tools_Js.getInstanceByView();
    instance.registerEvents();
});