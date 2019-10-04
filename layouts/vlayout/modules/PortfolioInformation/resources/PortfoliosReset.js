jQuery.Class("PortfoliosReset_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new PortfoliosReset_Module_Js();
        return instance;
    }
},{
    BeginResetProcess: function(){
        var accounts = $("#accounts").val();
        $.post("index.php", {module:'PortfolioInformation', action:'PortfoliosReset', todo:'ResetAccounts', account_numbers:accounts}, function(response) {
            data = $.parseJSON(response);
            $.each( data.success, function( key, value ) {
                $("#currently_resetting").append("<p>" + value + "</p>");
            });
            $("#currently_resetting").append("<p>Reset complete.  You may now close this page or click the back button</p>");
            console.log(data);
        }).fail(function() {
            alert( "Error During Transaction Reset" );
            $("#currently_resetting").append("<p>There was an error resetting transactions</p>");
        });
    },

    registerEvents : function() {
        this.BeginResetProcess();
    }
});

jQuery(document).ready(function($) {
    var instance = PortfoliosReset_Module_Js.getInstanceByView();
    instance.registerEvents();
});