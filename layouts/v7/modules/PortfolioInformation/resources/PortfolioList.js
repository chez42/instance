var reports_left = 4;

jQuery(document).ready(function($){

    
	$(".hide_widget").closest(".summaryWidgetContainer").hide();
    
    /*if ( $( ".hide_widget" ).length ) {
    	$( ".stellarnav" ).hide();
	}*/

    if($("[name=ACCOUNT_NUMBERS]").length) {
        if ($("[name=ACCOUNT_NUMBERS]").val().length > 5) {
            $(".report_links").show();
            $("#HoldingsReport").attr("data-account", $("[name=ACCOUNT_NUMBERS]").val());
            $("#HoldingsReport").attr("data-calling", $("[name=CALLING_RECORD]").val());
            $("#OmniviewReport").attr("data-account", $("[name=ACCOUNT_NUMBERS]").val());
            $("#OmniviewReport").attr("data-calling", $("[name=CALLING_RECORD]").val());
        }
    }

    function blink(selector, stop){
    if(!stop)
        $(selector).fadeOut('slow', function(){
            $(this).fadeIn('slow', function(){
                blink(this);
            });
        });
    }

    function StatusUpdates(module, record, generate){
        blink('.generating', 0);
        $.post("index.php", {'module':'PortfolioInformation','view':'MultiReport','calling_module':module, 
                     'calling_record':record}, function(response){
            $("#report_wrapper").html(response);
        });
/*
        $.post("index.php", {'module':'PortfolioInformation','action':'MultiReport','calling_module':module, 
                             'calling_record':record, 'generate':generate}, function(response){
            $("#report_wrapper").append('<br />' + response);
            reports_left -= 1;
            $("#report_wrapper").append(' (Reports Left ' + reports_left + ')');
            if(reports_left <= 0){
                reports_left = 4;
                $.post("index.php", {'module':'PortfolioInformation','view':'MultiReport','calling_module':module, 
                             'calling_record':record}, function(response){
                    $("#report_wrapper").html(response);
                });
            }
        });*/
    }

    function ConvertArrayToGet(val){
        var accounts = val;
        var account = [];
        var x;
        for(x = 0; x < accounts.length; x++){
            var obj = {"name":"account_number[]",
                "value": accounts[x]};
            account[x] = obj;
        }

        return $.param(account);
    }

//    $('#HoldingsReport').click(function(e){
    $("#reportselect").change(function(e){
        e.stopImmediatePropagation();
        var selected = $("#reportselect").find(':selected');
        var accounts = selected.data('account');
        var calling_record = selected.data('calling');
        var report = selected.val();

        if(report == "0")
            return;

        $("#reportselect").val('');
        window.open("index.php?module=PortfolioInformation&view="+report+"&account_number="+accounts+"&calling_record="+calling_record, "_blank");
    });

/*    $(document).off("#reportselect").on('click', "#reportselect", function(e){
        e.stopImmediatePropagation();

        var selected = $("#reportselect").find(':selected');
        var accounts = selected.data('account');
        var calling_record = selected.data('calling');
        var report = selected.val();

        if(report == "0")
            return;

        $("#reportselect").val('');
        window.open("index.php?module=PortfolioInformation&view="+report+"&account_number="+accounts+"&calling_record="+calling_record, "_blank");
    });*/

    $(document).off('#HoldingsReport').on("click", "#HoldingsReport", function(e){
        e.stopImmediatePropagation();
        var accounts = $(this).data('account');
        var account = [];
        var x;
        for(x = 0; x < accounts.length; x++){
            var obj = {"name":"account_number[]",
                "value": accounts[x]};
            account[x] = obj;
        }

        var str = $.param(account);
        var calling = $(this).data('calling');
        var progressInstance = jQuery.progressIndicator();

        window.open("index.php?module=PortfolioInformation&view=AssetAllocationReport&"+str+"&calling_record="+calling, "_blank");

        progressInstance.hide();

        /*            $.post("index.php", {module:'PortfolioInformation', view:'HoldingsReport', account_number:account, calling_record:calling}, function(response){
         progressInstance.hide();
         var newWindow = window.open("", "new window", "width=800, height=800");
         //write the data to the document of the newWindow
         newWindow.document.write(response);
         });*/
    });

    $(document).off('#OmniviewReport').on("click", "#OmniviewReport", function(e){
        e.stopImmediatePropagation();
        var accounts = $(this).data('account');
        var account = [];
        var x;
        for(x = 0; x < accounts.length; x++){
            var obj = {"name":"account_number[]",
                "value": accounts[x]};
            account[x] = obj;
        }

        var str = $.param(account);
        var calling = $(this).data('calling');
        var progressInstance = jQuery.progressIndicator();

        window.open("index.php?module=PortfolioInformation&view=HoldingsReport&"+str+"&calling_record="+calling, "_blank");

        progressInstance.hide();
    });

    $(document).off('.load_report').on("click", ".load_report", function(e){
        e.stopImmediatePropagation();
        var module = $('[name=MODULE]').val();
        var record = $('[name=RECORD]').val();
        var selected = $(this).val();
        var report = '';
        var accounts = ConvertArrayToGet($("#HoldingsReport").data('account'));
        switch(selected){
            case "Holdings":
                report = 'holdings';
                break;
            case "Income":
                window.open("index.php?module=PortfolioInformation&view=MonthlyIncome&"+accounts+"&calling_record="+record, "_blank");
                report = "monthly_income";
                break;
            case "Performance":
                report = 'performance';
                break;
            case "Overview":
                window.open("index.php?module=PortfolioInformation&view=Overview&"+accounts+"&calling_record="+record, "_blank");
                report = 'overview';
                break;
        }
/*
        var progressInstance = jQuery.progressIndicator();
        $.post("index.php", {'module':'PortfolioInformation','action':'LoadPortfolioReport','calling_module':module, 'calling_record':record,
                'report':report}, function(response){
                progressInstance.hide();
            var headerInstance = new Vtiger_Header_Js();
                app.showModalWindow(response, function(data) {
//                var chart = $.parseJSON(data.find('.chartdata').val());
                switch(report){
                    case "holdings":
                        var holdingsChart = $.parseJSON(data.find('[name=holdingschart]').val());
                        $.CreateHoldingsChart(holdingsChart);
                        break;
                    case "monthly_income":
                        var historyChart = $.parseJSON(data.find('[name=history_chart]').val());
                        var futureChart = $.parseJSON(data.find('[name=future_chart]').val());
                        $.CreateIncomeChart(historyChart, 'history_chart');
                        $.CreateIncomeChart(futureChart, 'future_chart');
                        break;
                    case "performance":
                        var pids = data.find('[name=pids]').val();
                        $.CalculatePerformance(pids);
                        break;
                    case "overview":
                        var pids = data.find('[name=pids]').val();
                        var holdingsChart = $.parseJSON(data.find('[name=holdingschart]').val());
                        var incomeChart = $.parseJSON(data.find('[name=incomechart]').val());
                        $.CreateHoldingsChart(holdingsChart);
                        $.CreateIncomeChart(incomeChart, 'income_chart');
                        $.CalculatePerformance(pids);
                        break;
                }
            }, {'overflow':'auto', 'width':'50%', 'height':'900px', 'position':'relative'});
        });*/
    });
});