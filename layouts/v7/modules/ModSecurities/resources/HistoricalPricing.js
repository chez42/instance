jQuery.Class("Historical_Pricing_Js",{
	currentInstance : false,
        
	getInstanceByView : function(){
            var instance = new Historical_Pricing_Js();
	    return instance;
	}
},{ 
    ShowMore : function(){
        $(".show_more").click(function(e){
            $(".hidden").removeClass('hidden');
            $(this).html('');
        });
    },
        
    HistoricalPrice : function(){        
        $(".historical_price").change(function(e){
            e.stopImmediatePropagation();
            var id = $(this).data('id');
            var price = $(this).val();
            var edit_box = $(this);
            var record_id = $('#record_id').val();
            $.post("index.php", {module:'ModSecurities', action:'PriceInteraction', todo:'SavePrice', price_id:id, price:price, record:record_id}, function(response){
                var message = $.parseJSON(response);
                if(message.result == 1)
                    edit_box.css('background-color','#CCFF99');
                else{
//                    alert(message.result);
                    edit_box.val(edit_box.data('original'));
                    edit_box.css('background-color','#FFB8B8');
                }
            });
        });
    },
    
    registerEvents : function() {
        this.HistoricalPrice();
        this.ShowMore();
    }
});

jQuery(document).ready(function($) {
    var instance = Historical_Pricing_Js.getInstanceByView();
    instance.registerEvents();
});