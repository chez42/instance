jQuery.Class("Administration_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new Administration_JS();
        return instance;
    }
},{
    registerEvents : function() {
        $("#generate_transactions").click(function(e){
            var account_number = $('#account_number_selection').find(':selected').val();
            var val = confirm("Are you sure you want to generate transactions for account " + account_number + "?  This will destroy TWR if not used correctly");
            if(val === true){
                $.ajax({
                    type: 'POST',
                    url: "index.php?module=Transactions&action=FixTransaction",
                    data: {'todo': 'generatetd', 'account_number':account_number},
//                    dataType: 'json',
                    success: function(data) {
                        var result = $.parseJSON(data);
                        alert(result.result.message);
                    }
                });
            }
            else
                alert("Cancelled");
        });
    }
});

jQuery(document).ready(function($) {
    var instance = Administration_JS.getInstanceByView();
    instance.registerEvents();
//    chart.CreateChart("trailing12revenue", "2018-01-01", "2018-02-28");
    //var instance = TrailingRevenue_Js.getInstanceByView();
});