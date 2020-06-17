jQuery.Class("FixTransactions_Js",{
    currentInstance : false,
    price: 0,
    adjustment: 1,
    cost_basis: 0,
    account_number: '',
    asset_class: '',
    security_type: '',
    quantity: '',
    symbol: '',
    trade_date: '',
    data: '',

    getInstanceByView : function(){
        var instance = new FixTransactions_Js();
        return instance;
    }
},{
    currency: function(){
        $.fn.digits = function(){
            return this.each(function(){
                $(this).text( $(this).text().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,") );
            })
        }
    },

    DateSelection: function(){
        $("#date_select").datepicker({
            onSelect: function(dateText) {
                var date_val = this.value;
                symbol = $("#security_symbol").val();
                $.post("index.php", {module:'Transactions', action:'FixTransaction', todo:'getsymbolinfo', date:date_val, symbol:symbol}, function(response){
                    quantity = $(".quantity").val();
                    quantity = quantity.replace(/,/g, "");
                    data = $.parseJSON(response);
                    price = data.security_pricing.price;
                    adjustment = data.security_info.security_price_adjustment;
                    $("#price").val(price);
                    cost_basis = price * adjustment * quantity;
                    account_number = $("#account_number").val();
                    asset_class = data.security_info.aclass;
                    security_type = data.security_info.securitytype;
                    trade_date = date_val;
                    $("#cost_basis").val(cost_basis.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
                });
            }
        });
    },

    OnUpdates: function(){
        $(".quantity").change(function(e){
            quantity = $(".quantity").val();
            quantity = quantity.replace(/,/g, "");
            price = $("#price").val();
            cost_basis = price * adjustment * quantity;
            $("#cost_basis").val(cost_basis.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
        });

        $("#price").change(function(e){
            quantity = $(".quantity").val();
            quantity = quantity.replace(/,/g, "");
            price = $("#price").val();
            cost_basis = price * adjustment * quantity;
            $("#cost_basis").val(cost_basis.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2}));
        });
    },

    ClickEvents: function(){
        $("#save_transaction").click(function(e){
            if($("#date_select").val().length == 0)
                alert("Date must be selected");
            else {
                $.post("index.php", {
                    module: 'Transactions',
                    action: 'FixTransaction',
                    todo: 'savetransaction',
                    account_number: account_number,
                    symbol: symbol,
                    date: trade_date,
                    quantity: quantity,
                    price: price,
                    cost_basis: cost_basis,
                    asset_class: asset_class,
                    security_type: security_type
                }, function (response) {
                    if (response == 1) {
                        var magnificPopup = $.magnificPopup.instance;
                        magnificPopup.close();
                        location.reload();
                    }
                });
            }
        });

        $("#cancel_transaction").click(function(e){
            var magnificPopup = $.magnificPopup.instance;
            magnificPopup.close();
        });
    },

    registerEvents : function() {
        this.currency();
        this.DateSelection();
        this.ClickEvents();
        this.OnUpdates();
    }
});

jQuery(document).ready(function($) {
    var instance = FixTransactions_Js.getInstanceByView();
    instance.registerEvents();
});