jQuery.Class("TransactionsNavigation_Js",{
	currentInstance : false,
        
	getInstanceByView : function(){
            var instance = new TransactionsNavigation_Js();
	    return instance;
	},
},{
    registerEventOpenCloserClick : function(){
        $('.title_bar').click(function(e){
            $(this).siblings('.closer_item').slideToggle("slow", function(){});
        });
    },
    registerEventDateClick : function(){
        $("#transaction_filter_start_date").datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
//                $( "#transaction_filter_date_range_end" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $("#transaction_filter_end_date").datepicker({
            changeMonth: true,
            changeYear: true,
            numberOfMonths: 3,
            onClose: function( selectedDate ) {
//                $( "#transaction_filter_date_range_end" ).datepicker( "option", "minDate", selectedDate );
            }
        });
    },
    
    registerEventFilterClick : function(){
        $('[name=filter_transactions]').click(function(e){
            var num=$('.transactions_navigation_account_numbers').map(function(){
                    return $(this).val();
                }).get();
            var selected_activities = [];
            var selected_symbols = [];
            var selected_security_types = [];
            var start_date = $('#transaction_filter_start_date').val();
            var end_date = $('#transaction_filter_end_date').val();
            
            $('#transaction_filter_activities_value :selected').each(function(i, selected){ 
              selected_activities[i] = $(selected).val();
            });
            
            $('#transaction_filter_symbols_value :selected').each(function(i, selected){ 
              selected_symbols[i] = $(selected).val();
            });
            
            $('#transaction_filter_security_types_value :selected').each(function(i, selected){ 
              selected_security_types[i] = $(selected).val();
            });
            
            $.post('index.php',{'module':'PortfolioInformation', 'action':'FilterTransactions', 'account_numbers':num,
                                'selected_activities':selected_activities, 'selected_symbols':selected_symbols,
                                'selected_security_types':selected_security_types, 'start_date':start_date, 
                                'end_date':end_date}, function(response){
                $(".transactions_navigation_content").html(response);
            });
        });
    },
    
    registerEvents : function() {
        this.registerEventFilterClick();
        this.registerEventDateClick();
        this.registerEventOpenCloserClick();
    }
});

jQuery(document).ready(function($) {
	var instance = TransactionsNavigation_Js.getInstanceByView();
	instance.registerEvents();
})