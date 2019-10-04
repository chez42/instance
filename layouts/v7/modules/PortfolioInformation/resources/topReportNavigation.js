jQuery(document).ready(function($){
    $.getScript('layouts/vlayout/modules/PortfolioInformation/resources/transactions.js', function(){

    });
    var pagelist = Array("positionsBottom", "transactionsBottom", "holdingsBottom", "monthlyIncomeBottom", "performanceBottom");
 
    $(document).on('click', '.loadReport', function(e){
        e.stopImmediatePropagation();
        var acct = $(this).parent().find(".acct_number").val();
        var progressInstance = jQuery.progressIndicator();
        $.post("index.php", {'module':'PortfolioInformation','action':'LoadTopReport','account_number':acct}, function(response){
            $(".ReportTop").html(response);
            $.post("index.php", {'module':'PortfolioInformation','action':'LoadBottomReport','report_display':'holdings', 'account_number':acct, 'hide_pie':'1'}, function(response){
                progressInstance.hide();
                $(".ReportBottom").html(response);
                reportSlimScroll(); //  Changes 5May,2106
            });
        });
    });
    
    // ========== START :   Changes 5May,2106    ========================= //
    
    $(document).on("click",".nav_report",function(e){
        e.stopImmediatePropagation();
        var token = $(this).find(".nav_page").val();
        var report_display = $(this).find(".report_type").val();
        var record = $(document).find("#recordId").val();
        var acct = $(document).find(".acct_number").val();
        var calling_module = app.getModuleName();
        $.post("index.php", {'module':'PortfolioInformation','action':'LoadBottomReport', 'calling_record':record, 'calling_module': calling_module, 'account_number':acct, 'report_display':report_display, 'hide_pie':'1'}, function(response){
            $(".ReportBottom").html(response);
            if(report_display == 'holdings')
            	reportSlimScroll();
        });
    });

	/**
	 * Function to handle slim scroll for HoldingsBottomReports
	 */
/*	function reportSlimScroll(){
		var element = $('#portfolio_holdings');
		app.showScrollBar(element, {"height" : '280px'});
	}

    // ==========  END :  Changes 5May,2106    ========================= //
  */
    $(".edit_account").click(function(e){
        var record = $(this).attr("data-edit");
        window.location.href = "index.php?module=PortfolioInformation&view=Detail&record=" + record + "&mode=showDetailViewByMode&requestMode=full&tab_label=Portfolio%20Information%20Details";
    });
try {
    $.contextMenu({
        selector: '.context-menu-one',
        callback: function (key, options) {
            var account = $(this).attr('data-acc');
            var calling = $("[name=CALLING_RECORD]").val();
//            alert($(this).data('acc').val());
//            var m = "clicked: " + key;
            switch (key) {
                case "omniview":
                    window.open("index.php?module=PortfolioInformation&view=HoldingsReport&account_number=" + account + "&calling_record=" + calling, "_blank");
                    break;
                case "holdings":
                    window.open("index.php?module=PortfolioInformation&view=AssetAllocationReport&account_number=" + account + "&calling_record=" + calling, "_blank");
                    break;
                case "overview":
                    window.open("index.php?module=PortfolioInformation&view=Overview&account_number[]=" + account + "&calling_record=" + calling, "_blank");
                    break;
                case "income":
                    window.open("index.php?module=PortfolioInformation&view=MonthlyIncome&account_number[]=" + account + "&calling_record=" + calling, "_blank");
                    break;
            }
//            window.console && console.log(m) || alert(m);
        },
        items: {
            "omniview": {name: "OmniVue"},
            "holdings": {name: "Holdings"},
            "income": {name: "Income"},
            "overview": {name: "Overview"},
            "quit": {name: "Cancel"}
        }
    });
}catch(err){
    alert("ERROR");
}

});