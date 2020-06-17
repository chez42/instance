/**
 * Created by ryansandnes on 2017-05-26.
 */
jQuery.Class("EODUpdate_JS",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new EODUpdate_JS();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;

        $("#eodUpdateSecurity").hover(function(e){
            $(this).css('cursor','pointer');
        }, function() {
            $(this).css('cursor','auto');
        });

        $("#eodUpdateSecurity").click(function(e){
            var recordID = $("#eodUpdateSecurity").data("id");
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'ModSecurities', action:'EODActions', todo:'UpdateEODSymbol', record:recordID}, function(response){
                console.log(response);
//                location.reload();
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = EODUpdate_JS.getInstanceByView();
    instance.registerEvents();
});