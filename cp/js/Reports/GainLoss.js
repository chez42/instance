var GainLoss_Js = {
    
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


    registerEvents : function() {

        this.CollapTable();
        this.HighlightSystemGenerated();
       
    }
};

jQuery(document).ready(function($) {
    GainLoss_Js.registerEvents();
});