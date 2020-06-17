/**
 * Created by ryansandnes on 2017-05-26.
 */
jQuery.Class("TransactionMapping_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new TransactionMapping_Module_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;

        $("input[type=text]").change(function(e){
            var id = $(this).data("id");
            var value = $(this).val();
            var field = $(this).prop("name");
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateMappingField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });

        $(".addrow").click(function(e){
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'CreateTransactionRow'}, function(response){
                progressInstance.hide();
                var id = response;
                $('.TransactionMappingTable tr:last').after('<tr> ' +
                    '<td><input type="text" value="'+id+'" name="id" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="transaction_type" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="transaction_activity" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="report_as_type" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="td" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="fidelity" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="schwab" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="pershing" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="pc" data-id="'+id+'" /></td> ' +
                    '</tr>');
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = TransactionMapping_Module_Js.getInstanceByView();
    instance.registerEvents();
});