jQuery(document).ready(function($) {
    jQuery('.stellarnav').stellarNav({
        theme: 'plain',
        breakpoint: 960,
        position: 'center',
//        phoneBtn: '18009997788',
//        locationBtn: 'https://www.google.com/maps'
    });

    jQuery('.report_selection').click(function(e){
        var record = $('[name=record_id]').val();
        var report = $(this).data('report');
        var newWindow = window.open("", "_blank");

        $.post("index.php", {module:'PortfolioInformation', action:'GetAccountNumbersFromRecord', record:record}, function(response){
            var accounts = $.parseJSON(response);
            var url = "index.php?module=PortfolioInformation&view="+report+"&account_number="+accounts+"&calling_record="+record;
//            window.open("index.php?module=PortfolioInformation&view="+report+"&account_number="+accounts+"&calling_record="+record, "_blank");
            return newWindow.location = url;
        });
    });
});