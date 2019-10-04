jQuery.Class("CustodianInteractions_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new CustodianInteractions_Module_Js();
        return instance;
    }
},{
    CreateTable: function(data){
        $("#compare_table tbody").html("");
        $.each(data, function(symbol, v){
            var color = "";
            var security_type = "";

            if((v.csv_quantity - v.omni_quantity > 1) || (v.csv_quantity - v.omni_quantity < -1))
                color = "#FF9D8F";
            else
                color = "#BAFFAF";

            if(v.security_type_id == 11)
                v.security_type = "Cash";

            $("#compare_table tbody").append("<tr>" +
                "<td>"+ v.account_number+"</td>" +
                "<td>"+ v.security_symbol+"</td>"+
                "<td style='background-color:"+color+"'>"+ v.csv_quantity+"</td>"+
                "<td style='background-color:"+color+"'>"+ v.csv_value+"</td>"+
                "<td style='background-color:"+color+"'>"+ v.omni_quantity+"</td>"+
                "<td style='background-color:"+color+"'>"+ v.omni_value+"</td>"+
                "<td>"+ v.security_type+"</td>"+
                "</tr>");
        });
    },

    CreatePortfolioTable: function(data){
        $("#compare_portfolio_table tbody").html("");
        $("#compare_portfolio_table tbody").append("<tr>" +
            "<td>"+ data.account_number+"</td>" +
            "<td>"+ data.csv_total_value+"</td>"+
            "<td>"+ data.csv_market_value+"</td>"+
            "<td>"+ data.csv_cash_value+"</td>"+
            "<td>"+ data.omni_total_value+"</td>"+
            "<td>"+ data.omni_market_value+"</td>"+
            "<td>"+ data.omni_cash_value+"</td>"+
            "</tr>");
    },

    ClickEvents: function(){
        var self = this;
        $("#audit_td_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'AuditTDPositions'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#audit_fidelity_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'AuditFidelityPositions'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#audit_schwab_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'AuditSchwabPositions'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#audit_pershing_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'AuditPershingPositions'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $(".account_select").change(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $("#audit_account_number").val(e.val);
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'CompareToCSV', account_number:e.val}, function(response){
                progressInstance.hide();
                var data = $.parseJSON(response);
                self.CreateTable(data.positions);
                self.CreatePortfolioTable(data.portfolios);
            });

        });

        $(".problem_account_select").change(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $("#audit_account_number").val(e.val);
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'CompareToCSV', account_number:e.val}, function(response){
                progressInstance.hide();
                var data = $.parseJSON(response);
                self.CreateTable(data.positions);
                self.CreatePortfolioTable(data.portfolios);
            });
        });

        $("#repair_all_accounts").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'AutoRepairAccounts'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#audit_reset").click(function(e){
            e.stopImmediatePropagation();
            var account_number = $("#audit_account_number").val();
            var progressInstance = jQuery.progressIndicator();
            if(account_number.length < 5){
                alert("Dummy check.. Invalid account");
                return;
            }
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'ResetAccount', account_number:account_number}, function(response){
                progressInstance.hide();
                if(response != 1){
                    alert(response);
                    return;
                }
                $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'CompareToCSV', account_number:account_number}, function(response){
                    progressInstance.hide();
                    var data = $.parseJSON(response);
                    self.CreateTable(data.positions);
                    self.CreatePortfolioTable(data.portfolios);
                });
            });
        });

        $("#empty_portfolios_table").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'EmptyPortfoliosTable'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#audit_compare_to_csv").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#audit_account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'CompareToCSV', account_number:account_number}, function(response){
                progressInstance.hide();
                var data = $.parseJSON(response);
                self.CreateTable(data.positions);
                self.CreatePortfolioTable(data.portfolios);
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = CustodianInteractions_Module_Js.getInstanceByView();
    instance.registerEvents();
});