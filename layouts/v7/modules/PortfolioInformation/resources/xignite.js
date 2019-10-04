jQuery.Class("XIgnite_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new XIgnite_Module_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;

        $("#GetFundamentals").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var symbol = $("#symbol").val();

            $.post("index.php", {module:'PortfolioInformation', action:'xignite', todo:'GetFundamentals', symbol:symbol}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#sectors").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'xignite', todo:'GetSectors'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#map_sectors").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'xignite', todo:'MapSectors'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#populate_unclassified").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'xignite', todo:'PopulateUnclassified'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = XIgnite_Module_Js.getInstanceByView();
    instance.registerEvents();
});