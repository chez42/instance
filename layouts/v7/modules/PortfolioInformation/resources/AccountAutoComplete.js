jQuery.Class("AccountAutoComplete_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new AccountAutoComplete_JS();
        return instance;
    }
},{
    CompleteAccount: function(){
        var self = this;
        $("#PositionInformation_editView_fieldName_account_number").autocomplete({
            minLength:3,
            source:function(request, response){
                $.ajax({
                    url:'index.php?module=PortfolioInformation&action=GetAccountNumbers',
                    dataType:"json",
                    data:{account_number:request.term},
                    success:function(data){
                        response(data);
                    }
                });
            },
            select: function(event, ui){
                var account_number = ui.item.value;
                $.post("index.php", {module:'PortfolioInformation', action:'GetAccountData', account_number:account_number}, function(response){
                    if(response != 0){
//                        console.log(response);
                        var values = $.parseJSON(response);
//                        $("#PositionInformation_editView_fieldName_portfolio_name").val(values.security_name);
                        $("#PositionInformation_editView_fieldName_custodian").val(values.origination);
                        $("#PositionInformation_editView_fieldName_cf_2662").val(values.manual);
                        $("#PositionInformation_editView_fieldName_custodian_control_number").val(values.production_number);
                        $("#PositionInformation_editView_fieldName_omniscient_control_number").val(values.omniscient_control_number);
                        $("#PositionInformation_editView_fieldName_contact_link").val(values.contact_link);
                        $("#PositionInformation_editView_fieldName_household_account").val(values.household_account);
                    }
                });
//                alert("You selected: " + ui.item.value + ", Now auto fill some other fields like price, etc");
            }
        });
    },

    registerEvents : function() {
        this.CompleteAccount();
    }
});

jQuery(document).ready(function($) {
    var instance = AccountAutoComplete_JS.getInstanceByView();
    instance.registerEvents();
});