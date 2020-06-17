jQuery.Class("Finworx_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new Finworx_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;

        $("#finworx_link").click(function(e){
            var link = $(this).data("link");
            $("#finworx_report").load(link);
        });
    },

    AutoRefresh: function(){
        var link = $('#finworx_window').data('link');
        $('#finworx_window').attr('src', link);
    },

    registerEvents : function() {
        this.ClickEvents();
        this.AutoRefresh();
    }
});

jQuery(document).ready(function($) {
    var instance = Finworx_Js.getInstanceByView();
    instance.registerEvents();
});