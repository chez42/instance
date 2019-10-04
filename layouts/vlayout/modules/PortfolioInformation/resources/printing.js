jQuery(document).ready(function($){
    jQuery.PrintReport = function PrintReport(report_type){
//        alert("PRINT ME");
    }
    
    $(document).on("click", "[name=PRINT]", function(e){
        $.PrintReport("overview");
    });
});