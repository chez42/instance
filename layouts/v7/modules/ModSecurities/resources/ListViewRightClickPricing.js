jQuery.Class("EOD_Delayed_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new EOD_Delayed_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;
    },

    contextMenuRegister : function(){
        var self=this;
        $(document).on('mouseenter', '.listViewEntries', function(e){
            $(this).qtip({
                show:{
                    ready:true,
                    solo:true
                },
                hide: {
                    effect:function(){
                        $(this).fadeOut();
                    },
                    fixed:true,
                    delay:0
                },
                content: {
                    text: function(event, api) {
                        var url = api.elements.target.data('recordurl');
                        var id = api.elements.target.data('id');
                        api.set('content.text', 'Loading...');
    //                    var symbol = api.elements.target.attr('id');
    //                    var account = api.elements.target.attr('data-account');
    //                    var progressInstance = jQuery.progressIndicator();

                        $.ajax({
                            url:"index.php?module=ModSecurities&action=EODActions&todo=getdelayedpopup&recordid=" + id
                        }).then(function(content){
    //                        progressInstance.hide();
                            api.set('content.text', content);
                        });

                        return "Loading...";
                    }
                },
                position: {
                    my: 'bottom right',
                    at: 'top left',
                    target:'mouse',
                    viewport: $(window),
                    adjust:{mouse:false}
    //                viewport: $(window)
                },
                style: {classes: 'qtip-dark qtip-shadow'}
            });
        });
    },

    registerEvents : function() {
//        this.ClickEvents();
//        this.HoverEvents();
        this.contextMenuRegister();
    }
});

var instance = EOD_Delayed_Js.getInstanceByView();
instance.registerEvents();