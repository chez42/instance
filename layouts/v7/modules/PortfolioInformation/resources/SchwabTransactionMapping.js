/**
 * Created by ryansandnes on 2017-05-26.
 */
jQuery.Class("SchwabTransactionMapping_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new SchwabTransactionMapping_Module_Js();
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
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateSchwabMappingField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });

        $("select[name=omniscient_activity]").change(function(e){
            var id = $(this).data("id");
            var value = $(this).val();
            var field = $(this).prop("name");
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateSchwabMappingField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });


        $(".addrow").click(function(e){
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'CreateSchwabMappingRow'}, function(response){
                progressInstance.hide();
                var id = response;
                $('.SchwabTransactionMappingTable tr:last').after('<tr> ' +
                    '<td><input type="text" value="'+id+'" name="id" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="source_code" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="type_code" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="subtype_code" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="direction" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="transaction_activity" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="omniscient_category" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="omniscient_activity" data-id="'+id+'" /></td> ' +
                    '</tr>');
            });
        });
    },
    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = SchwabTransactionMapping_Module_Js.getInstanceByView();
    instance.registerEvents();
});