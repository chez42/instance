/**
 * Created by ryansandnes on 2017-05-26.
 */
jQuery.Class("TDSecuritiesMapping_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new TDSecuritiesMapping_Module_Js();
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
            $.post("index.php", {module:'ModSecurities', action:'Administration', todo:'UpdateTDMappingField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });
    },
    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = TDSecuritiesMapping_Module_Js.getInstanceByView();
    instance.registerEvents();
});