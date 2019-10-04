jQuery(document).ready(function($){
    $(document).off('[name=security_symbol]').on("click", "[name=security_symbol]", function(e){
        e.stopImmediatePropagation();
        var symbol = $.trim($(this).text());
        var progress = jQuery.progressIndicator();
        $.post("index.php", {'module':'Trading', 'view':'Quote', 'task':'get_quote', 'symbol':symbol}, function(response){
            progress.progressIndicator({'mode':'hide'});
            app.showModalWindow(response);
/*            var headerInstance = new Vtiger_Header_Js();
                $('<div class="td_results">'+response+'</div>').dialog({
                    modal:false,
                    dialogClass: 'td_results',
                    width: '600',
                    title:"Security Pricing"
                });
//                headerInstance.handleQuickCreateData(response,{callbackFunction:function(data){
//            }});*/
        });
    });
});