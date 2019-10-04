jQuery.Class("GainLoss_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new GainLoss_Js();
        return instance;
    }
},{
    submitForm : function(){
        $("#export").submit();
    },

    submitPrint : function(){
        $('[name=print_pdf]').click(function(e){
            GainLoss_Js.getInstanceByView().submitForm();
        });
    },

    registerClickEvents : function(){
        $('.createtransaction').click(function(e){
            var tr = $(this).closest('tr');
            var transaction_id = tr.data('transaction_id');
            var quantity = tr.find("td:nth-child(6)").html();
            $.magnificPopup.open({
                items:{
                    type:'ajax',
                    closeOnBgClick: false,
                    src:"index.php?module=Transactions&action=FixTransaction&todo=fixtransaction&transactionid="+transaction_id+"&quantity="+quantity
                }
            });
        });

        $('.edittransaction').click(function(e) {
            var tr = $(this).closest('tr');
            var id = tr.data('transaction_id');

            var params = {mode:'showDetailViewByMode', requestMode:'full', displayMode:'overlay', record:id, module:'Transactions', view:'Detail'};

            app.helper.showProgress();
            app.request.get({data: params}).then(function(err, response) {
                app.helper.hideProgress();
                var overlayParams = {'backdrop' : 'static', 'keyboard' : false};
                app.helper.loadPageContentOverlay(response, overlayParams).then(function(container) {
                    var detailjs = Vtiger_Detail_Js.getInstanceByModuleName(params.module);
                    detailjs.showScroll(jQuery('.overlayDetail .modal-body'));
                    detailjs.setModuleName(params.module);
                    detailjs.setOverlayDetailMode(true);
                    detailjs.setContentHolder(container.find('.overlayDetail'));
                    detailjs.setDetailViewContainer(container.find('.overlayDetail'));
                    detailjs.registerOverlayEditEvent();
                    detailjs.registerBasicEvents();
                    detailjs.registerClickEvent();
                    detailjs.registerHeaderAjaxEditEvents(container.find('.overlayDetailHeader'));
                    detailjs.registerEventToReloadRelatedListOnCloseOverlay(parentRecordId);
                    app.event.trigger('post.overlay.load', parentRecordId, params);
                    container.find('form#detailView').on('submit', function(e) {
                        e.preventDefault();
                    });
                });
            });
        });
    },

    CollapTable: function(){
        $('.GainLossTable').aCollapTable({
// the table is collapased at start
            startCollapsed: true,
// the plus/minus button will be added like a column
            addColumn: true,
// The expand button ("plus" +)
            plusButton: '<span class="i">+</span>',
// The collapse button ("minus" -)
            minusButton: '<span class="i">-</span>'
        });
    },

    MakeRowInteractive: function(){
        $("td").each(function() {
            var tr = $(this).closest('tr');
            if($(this).data('systemgenerated') == 1) {
                tr.addClass("system_generated_transaction");
                tr.css('background-color', 'yellow');
                tr.find("td:nth-child(2)").html("<span class='createtransaction'>[FIX]</span><span class='edittransaction'> - [Edit]</span>");
                tr.find("td:nth-child(4)").html("<strong>System Generated Transaction</strong>");
            }
            if($(this).data('transactionsid')) {
                //alert($(this).data('transactionsid'));
                tr.attr('data-transaction_id', $(this).data('transactionsid'));
            }
        });
    },

    ClickEvents: function(){
        var self = this;
    },

    registerEvents : function() {
//        this.ClickEvents();
        this.CollapTable();
        this.MakeRowInteractive();
        this.submitPrint();
        this.registerClickEvents();
        var vtigerInstance = Vtiger_Index_Js.getInstance();
    	vtigerInstance.registerEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = GainLoss_Js.getInstanceByView();
    instance.registerEvents();
});