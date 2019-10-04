/**
 * Created by ryansandnes on 2017-05-26.
 */
jQuery.Class("FidelityTransactionMapping_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new FidelityTransactionMapping_Module_Js();
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
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateFidelityMappingField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });

        $("select[name=omniscient_activity]").change(function(e){
            var id = $(this).data("id");
            var value = $(this).val();
            var field = $(this).prop("name");
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateFidelityMappingField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });

    },
    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = FidelityTransactionMapping_Module_Js.getInstanceByView();
    instance.registerEvents();
});