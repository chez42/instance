jQuery.Class("PositionsWidget_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new PositionsWidget_Js();
        return instance;
    }
},{
    ClickEvents: function(){
        var self = this;

/*        $("#reportselect").click(function(e){
            e.stopImmediatePropagation();

            var selected = $("#reportselect").find(':selected');
            var accounts = selected.data('account');
            var calling_record = selected.data('calling');
            var orientation = selected.data('orientation');
            var report = selected.val();

            $("#reportselect").val(0);
            if(report == "0" || report == undefined)
                return;

            window.open("index.php?module=PortfolioInformation&view="+report+"&account_number="+accounts+"&calling_record="+calling_record+"&orientation="+orientation, "_blank");
        });*/

        $('#HoldingsReport').click(function(e){
            e.stopImmediatePropagation();
            var accounts = $(this).data('account');
            var account = [];
            var x;
            for(x = 0; x < accounts.length; x++){
                var obj = {"name":"account_number[]",
                           "value": accounts[x]};
                account[x] = obj;
            }

            var str = $.param(account);
            var calling = $(this).data('calling');
            var progressInstance = jQuery.progressIndicator();

            window.open("index.php?module=PortfolioInformation&view=AssetAllocationReport&"+str+"&calling_record="+calling, "_blank");

            progressInstance.hide();

            /*            $.post("index.php", {module:'PortfolioInformation', view:'HoldingsReport', account_number:account, calling_record:calling}, function(response){
                            progressInstance.hide();
                            var newWindow = window.open("", "new window", "width=800, height=800");
                            //write the data to the document of the newWindow
                            newWindow.document.write(response);
                        });*/
        });

        $('#OmniviewReport').click(function(e){
            e.stopImmediatePropagation();
            var accounts = $(this).data('account');
            var account = [];
            var x;
            for(x = 0; x < accounts.length; x++){
                var obj = {"name":"account_number[]",
                    "value": accounts[x]};
                account[x] = obj;
            }

            var str = $.param(account);
            var calling = $(this).data('calling');
            var progressInstance = jQuery.progressIndicator();

            window.open("index.php?module=PortfolioInformation&view=HoldingsReport&"+str+"&calling_record="+calling, "_blank");

            progressInstance.hide();
        });

        $('#IncomeReport').click(function(e){
            var str = $(this).data('account');
            window.open("index.php?module=PortfolioInformation&view=MonthlyIncome&account_number="+str, "_blank");
        });

        $('#OverviewReport').click(function(e){
            var str = $(this).data('account');
            var calling = $(this).data('calling');
            window.open("index.php?module=PortfolioInformation&view=OmniOverview&account_number="+str+"&calling_record="+calling, "_blank");
        });
/*
        $('#HoldingsReport').qtip({
            content:{
                title:"Holdings",
                text: function(event, api){
                    var account = api.elements.target.attr('data-account');
                    var calling = api.elements.target.attr('data-calling');
                    var progressInstance = jQuery.progressIndicator();
                    $.ajax({
                        url:"index.php?module=PortfolioInformation&view=HoldingsReport&account_number="+account+"&calling_record="+calling
                    }).then(function(content){
                        progressInstance.hide();
                        api.set('content_text', content);
                        $("html, body").animate({scrollTop: 200});
                    });
                    return "Loading...";
                }
            },
            position: {
                my: 'center',
                at: 'center',
                target: $(window)
            },
            show: {
                event: 'click',
                solo: true,
                modal: {
                    on: true
                }
            },
            hide: {
                event: false
            },
            style: 'custom-grey qtip-modal qtip-shadow qtip-rounded'
        });*/
    },

    HoverEvents: function(){
        $('.hover_symbol').qtip({
            show: {solo: true},
            hide: {
                effect:function(){
                    $(this).fadeOut();
                },
                fixed:true,
                delay:2000
            },
            content: {
                text: function(event, api) {
                    var symbol = api.elements.target.attr('id');
                    var account = api.elements.target.attr('data-account');
                    var progressInstance = jQuery.progressIndicator();

                    $.ajax({
                        url:"index.php?module=PositionInformation&view=PositionDetails&symbol=" + symbol + "&account=" + account
                    }).then(function(content){
                        progressInstance.hide();
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
            style: {classes: 'custom-blue qtip-rounded qtip-shadow'}
        });
    },

    contextMenuRegister : function(){
        var self=this;
        $('.hover_symbol').contextmenu(function(e){
            e.preventDefault();
            var element = $(this);
            var source_module = $('#source_module').val();
            var source_record = $('#source_record').val();

            $('<p>Symbol Navigation</p>').dialog({
                resizable: false,
                modal: true,
                buttons: {
                    "Edit Security Information": function() {
                        $( this ).dialog( "close" );
                        var record = element.data('security_record');
                        window.location.href = "index.php?module=ModSecurities&view=Edit&record=" + record + "&sourceModule=" + source_module + "&sourceRecord=" + source_record + "&redirectOperation=true";
//                        sourceModule=Contacts&sourceRecord=14155234
                    },
                    "Edit Position Information": function() {
                        $( this ).dialog( "close" );
                        var record = element.data('position_record');
                        window.location.href = "index.php?module=PositionInformation&view=Edit&record=" + record + "&sourceModule=" + source_module + "&sourceRecord=" + source_record + "&redirectOperation=true";
                    }
                }
            });
        });
    },
/*
    HoverEvents: function(){
        var hoverHTMLDemoBasic = '<div class="hover_content"></div>';
        $(".hover_symbol").hovercard({
            detailsHTML: hoverHTMLDemoBasic,
            width: 800,
            openOnTop:true,
//            openOnLeft:true,
            onHoverIn: function () {
                var e = $(this).parents('tr').find(".hover_symbol");
                var symbol = e.attr("id");
                var account = e.attr("data-account");
                var progressInstance = jQuery.progressIndicator();
                $.post("index.php", {module:'PositionInformation', view:'PositionDetails', symbol:symbol, account:account}, function(response){
                    progressInstance.hide();
                    $(".hover_content").html(response);
                });
            }
        });
    },
*/
    registerEvents : function() {
        this.ClickEvents();
        this.HoverEvents();
        this.contextMenuRegister();
    }
});
//REMOVED DOCUMENT READY REQUIREMENT SO THIS WORKS IN A WIDGET REFRESH
//jQuery(document).ready(function($) {
/*    $.contextMenu({
        selector: '.context_stuff',
        callback: function(key, options) {
            var m = "clicked: " + key;
            window.console && console.log(m) || alert(m);
        },
        items: {
            "edit": {name: "Edit", icon: "edit"},
            "cut": {name: "Cut", icon: "cut"},
            copy: {name: "Copy", icon: "copy"},
            "paste": {name: "Paste", icon: "paste"},
            "delete": {name: "Delete", icon: "delete"},
            "sep1": "---------",
            "quit": {name: "Quit", icon: function(){
                return 'context-menu-icon context-menu-icon-quit';
            }}
        }
    });*/

    var instance = PositionsWidget_Js.getInstanceByView();
    instance.registerEvents();
//});