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

    HighlightSystemGenerated: function(){
        $("td").each(function() {
            if($(this).data('systemgenerated') == 1)
                $(this).closest('tr').css('background-color', 'yellow');
        });
    },

    ClickEvents: function(){
        var self = this;
    },

    registerEvents : function() {
//        this.ClickEvents();
        this.CollapTable();
        this.HighlightSystemGenerated();
        this.submitPrint();
        var vtigerInstance = Vtiger_Index_Js.getInstance();
    	vtigerInstance.registerEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = GainLoss_Js.getInstanceByView();
    instance.registerEvents();
});