jQuery.Class("AccountActivity_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AccountActivity_JS();
        return instance;
    }
},{
    registerEvents : function() {
        var chart = AjaxActivityChart.getInstanceByView();
        chart.CreateChart("account_activity", "accountActivity", "#ffff80", "#ffd699", "#ffb84d");
    }
});

jQuery(document).ready(function($) {
    var instance = AccountActivity_JS.getInstanceByView();
    instance.registerEvents();
//    chart.CreateChart("trailing12revenue", "2018-01-01", "2018-02-28");
    //var instance = TrailingRevenue_Js.getInstanceByView();
});