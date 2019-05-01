jQuery.Class("OmniIncome_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new OmniIncome_Module_Js();
        return instance;
    }
},{
    CollapTable: function(){
        $('.collap_income').aCollapTable({
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

    ClickEvents: function(){
        var self = this;

        $(document).on("change", "input[type=text]", function(e){
//                $("input[type=text]").change(function(e){
            var id = $(this).data("id");
            var value = $(this).val();
            var field = $(this).prop("name");
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'UpdateFileField', id:id, value:value, field:field}, function(response){
                progressInstance.hide();
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
        this.CollapTable();
    }
});

jQuery(document).ready(function($) {
    var instance = OmniIncome_Module_Js.getInstanceByView();
    instance.registerEvents();

    var pie = DynamicPie_Js.getInstanceByView();
    pie.registerEvents();
//    var chart = DynamicChart_JS.getInstanceByView();

    pie.CreateGraph("dynamic_chart_holder", "estimate_graph_values", "category", "value");

//    chart.CreateChart("dynamic_chart_holder", "t12_balances");
});