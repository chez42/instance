jQuery(document).ready(function($){
    
    function FilterTransactions(order, filter, account_number, time)
    {
        if(!order)
            order = "security_symbol";
        var direction = $("#portfolios [name=direction]").val();
        if(direction == "DESC")
            direction = "ASC";
        else
            direction = "DESC";
        
        $.post('index.php',{'module':'PortfolioInformation', 'action':'LoadBottomReport', 'display':'1', 'report_display':'transactions',
                            'order_by':order, 'direction':direction, 'filter':filter, 'account_number':account_number}, function(response)
        {  
            $(".ReportBottom").html(response);
            var currentdate = new Date();
            time.html((currentdate.getMonth()+1)+"/"
                      +currentdate.getDate()+"/"
                      +currentdate.getFullYear());
        });
    }    
//    var topHandle = $(".TopReportInfo");
    var bottomHandle = null;
    var tmp = "";
//    if("{/literal}{$MODULE}{literal}" == "Portfolios")
//        tmp = ".bottomNav";
//    else
    tmp = "#ReportBottom";

    bottomHandle = $(tmp);//bottomHandle based on the module we are in... This is needed for "top navigation" from households vs regular navigation from the PortfolioInformation module

    $(document).on("click", "a.toggle", function(event){
        event.stopImmediatePropagation();
        if ('[-]' == $(this).text( )) {
            $("#hide").hide();
            $(this).text('[+]');
            } else {
            $("#hide").show();
            $(this).text('[-]');
        }

    $(this).parents(".article").find(".hide").toggle("normal");

    // Stop the link click from doing its normal thing
    return false;
    }); // END TOGGLE
    
    $(document).on("click",".sorter",function(e){
        var order = $(this).closest(".lvtCol").find(".order_by").val();
        var filter = $(this).closest(".lvtCol").find(".filter").val();
        var account_number = $(this).closest(".lvtCol").find(".account_number").val();
        FilterTransactions(order, filter, account_number);
    });
    
    $(document).on("click", "#transaction_filter_date_range_start", function(){
        $("#transaction_filter_date_range_start").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#transaction_filter_date_range_end" ).datepicker( "option", "minDate", selectedDate );
            }
        });
    });
    $(document).on("click", "#transaction_filter_date_range_end", function(){
        $("#transaction_filter_date_range_end").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
                $( "#transaction_filter_date_range_start" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
    });
    
    function isChecked(e, name)
    {
        var t = e.closest("#transactions_filter_wrapper");
        if(t.find("[name="+name+"]").prop("checked"))
            return true;
        return false;
    }
    
    function getSelected(e, id)
    {
        var tmp = [];
        var t = e.closest("#transactions_filter_wrapper");
        t.find("#"+id+" :selected").each(function(i, selected){
            tmp[i]=$(selected).text();
        });
        return tmp;
    }
    
    $(document).on("click", "[name=transaction_filter_submit]", function(e){
//        [name=assigntype]:checked
        var t = $(this).closest("#transactions_filter_wrapper");
        var symbols = '';
        var actions = '';
        var detail = '';
        var security = '';
        var date_start = '';
        var date_end = '';
        if(isChecked($(this),"transaction_filter_symbols"))
            symbols = t.find("[name=transaction_filter_symbols_value]").val();// [name=transaction_filter_symbols_value]").val();
        if(isChecked($(this),"transaction_filter_actions"))
            actions = getSelected($(this), "transaction_filter_actions_value");
        if(isChecked($(this),"transaction_filter_descriptions"))
            detail = getSelected($(this), "transaction_filter_descriptions_value");
        if(isChecked($(this),"transaction_filter_security_types"))
            security = getSelected($(this), "transaction_filter_security_types_value");
        if(isChecked($(this),"transaction_filter_date_range"))
        {
            date_start = t.find("#transaction_filter_date_range_start").val();
            date_end = t.find("#transaction_filter_date_range_end").val();
        }
        var acct_num = $("#portfolios [name=account_number]").val();
        var hh_acct = '';
        if(!hh_acct)
            hh_acct = $.cookie("household_account");
        if(!acct_num)
            acct_num = $.cookie("account_number");
        
        $.post('index.php',{'module':'PortfolioInformation', 'action':'LoadBottomReport', 'report_display':'transactions',
                            'transaction_filter_symbols_value':symbols,
                            'transaction_filter_actions_value':actions,
                            'transaction_filter_descriptions_value':detail,
                            'transaction_filter_security_types_value':security,
                            'transaction_filter_date_range_start':date_start,
                            'transaction_filter_date_range_end':date_end,
                            account_number:acct_num}, function(response)
        {  
            $(".ReportBottom").html(response);
        });
//        alert(actions);
    });
    $(document).on("click", "[name=transaction_filter_reset]", function(e){
        ChangePage($(this), 1, 1);
    });
    
    function ChangePage(e, pageNumber, reset)
    {
        var record = '';
        var acct_number = $("#portfolios [name=account_number]").val();
        var direction = 'desc';
        var pagenumber = pageNumber;
        var searchtype = e.find(".searchtype").val();
        var searchtext = e.find(".searchtext").val();
        var order_by = e.find(".order_by").val();
        var filter = e.find(".filter").val();
        var numresults = $("[name=numresults]").val();
        if(numresults != e.find(".numresults").val())
            pagenumber = 1;

        if(!record)
            record = $.cookie("household_account");
        if(!acct_number)
            acct_number = $.cookie("account_number");
        $.post('index.php',{'module':'PortfolioInformation', 'action':'LoadBottomReport', 'report_display':'transactions',
                            order_by:order_by, record:record, acct_number:acct_number, direction:direction,
                            pagenumber:pagenumber, searchtype:searchtype, searchtext:searchtext, filter:filter,
                            numresults:numresults, transaction_filter_reset:reset,
                            account_number:acct_number,
                            household_account:record}, function(response)
        {
//            $("#BottomReportInfoContent").html(response);
            $(".ReportBottom").html(response);
        });
    }
        
    $(document).on("click",".pagination", function(e){
        var nav = $(this).find('.nav_arrow').val();        
        var pagenumber = 1;
        switch(nav)
        {
            case "first":
                pagenumber = $(this).find('.page_number').val();
                break;
            case "back":
                pagenumber = $(this).find('.page_number').val();
                break;
        }
        ChangePage($(this), pagenumber);
    });
        
    $(document).on("change", "#pagenumber", function(e){
        var pagenumber = $(this).val();
        var par = $(this).closest("#directselect");
        ChangePage(par, pagenumber);
    });
        
    $(document).on("click", "#showresults", function(e){
        var par = $(this).closest(".showresults");
        ChangePage(par, 1);
    });
    
    $(document).on("click", "#listViewNextPageButton", function(e){
    	console.log("listViewNextPageButton");
    });
    $("#listViewNextPageButton").click(function(){
    	
    	console.log("rerere");
    });
});