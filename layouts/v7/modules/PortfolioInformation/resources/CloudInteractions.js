jQuery.Class("CloudInteractions_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new CloudInteractions_Module_Js();
        return instance;
    }
},{
/*    CreateTable: function(data){
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
    },*/

    ClickEvents: function(){
        var self = this;

        $("#update_transactions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var date = $("#date").val();
            var comparitor = $("#comparitor").val();
            var newonly = 0;
            if($("#new_only").is(":checked"))
                newonly = 1;

            $.post("index.php", {module:'Transactions', action:'ConvertCustodian', custodian:custodian, convert_table:'transactions', date:date, comparitor:comparitor, newonly:newonly}, function(response){
                progressInstance.hide();
                alert(response);
                window.location.reload();
                /*                var data = $.parseJSON(response);
                 self.CreateTable(data.positions);
                 self.CreatePortfolioTable(data.portfolios);*/
            });
        });

        $("#assign_transactions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#account_number").val();
            $.post("index.php", {module:'Transactions', action:'ConvertCustodian', convert_table:'assign_transactions', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#update_securities").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var symbol = $("#symbol").val();
            var date = $("#date").val();
            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', custodian:custodian, symbol:symbol, date:date, convert_table:'update_securities'}, function(response){
                progressInstance.hide();
                alert(response);
                /*                var data = $.parseJSON(response);
                 self.CreateTable(data.positions);
                 self.CreatePortfolioTable(data.portfolios);*/
            });
        });

        $("#update_security_type").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var symbol = $("#symbol").val();
            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', custodian:custodian, symbol:symbol, convert_table:'update_security_type'}, function(response){
                progressInstance.hide();
                alert(response);
                /*                var data = $.parseJSON(response);
                 self.CreateTable(data.positions);
                 self.CreatePortfolioTable(data.portfolios);*/
            });
        });

        $("#get_new_securities").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', custodian:custodian, convert_table:'securities'}, function(response){
                progressInstance.hide();
                alert(response);
/*                var data = $.parseJSON(response);
                self.CreateTable(data.positions);
                self.CreatePortfolioTable(data.portfolios);*/
            });
        });

        $("#asset_type_update").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', custodian:custodian, convert_table:'asset_type_update'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#add_symbols").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', convert_table:'update_symbol'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#update_prices").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var symbol = $("#symbol").val();
            var custodian= $("#custodian").val();

            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', convert_table:'update_prices', symbol:symbol, custodian:custodian}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#new_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian= $("#custodian").val();
            var date = $("#date").val();
            var account_number = $("#account_number").val();

            $.post("index.php", {module:'PositionInformation', action:'ConvertCustodian', custodian:custodian, convert_table:'new_positions', date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#update_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian= $("#custodian").val();
            var date = $("#date").val();
            var account_number = $("#account_number").val();

            $.post("index.php", {module:'PositionInformation', action:'ConvertCustodian', custodian:custodian, convert_table:'update_positions', date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#new_portfolios").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var date = $("#date").val();
            var account_number = $("#account_number").val();

            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', custodian:custodian, convert_table:'portfolios', date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#update_portfolios").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var date = $("#date").val();
            var account_number = $("#account_number").val();

            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', custodian:custodian, convert_table:'update_portfolios', date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
        
        $("#link_portfolios").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian',  convert_table:'link_portfolios'}, function(response){
                progressInstance.hide();
                alert(response);
            });            
        });
        
        $("#global_summary").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'GlobalSummary'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#remove_dupe_positions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#account_number").val();
            $.post("index.php", {module:'PositionInformation', action:'ConvertCustodian', convert_table:'remove_dupes', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#assign_portfolios").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', convert_table:'assign_portfolios', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
        $("#calculate_portfolios").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var account_number = $("#account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', convert_table:'calculate_portfolios', account_number:account_number, custodian:custodian}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#show_bad").click(function(e){
            e.stopImmediatePropagation();
            $("#integrity_table tbody tr").each(function() {
                if($(this).data("value") == "green")
                    $(this).hide();
            });
        });

        $("#remove_bad_dupes").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var bad_accounts = [];
            $("#integrity_table tbody tr").each(function() {
                if($(this).data("value") == "red"){
                    bad_accounts.push($(this).data("account"))
                }
            });
            $.post("index.php", {module:'PositionInformation', action:'ConvertCustodian', account_number:bad_accounts, convert_table:'remove_dupes'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#recalculate_bad").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var bad_accounts = [];
            $("#integrity_table tbody tr").each(function() {
                if($(this).data("value") == "red"){
                    bad_accounts.push($(this).data("account"))
                }
            });
            var custodian = $("#custodian").val();
            $.post("index.php", {module:'PositionInformation', action:'ConvertCustodian', custodian:custodian, convert_table:'update_positions', account_number:bad_accounts}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#update_portfolio_center").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', convert_table:'update_portfolio_center'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#update_balances").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var date = $("#date").val();
            var account_number = $("#account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', convert_table:'update_balances', custodian:custodian, date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#index_update").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var index_symbol = $("#index_symbol").val();
            var index_sdate =  $("#index_start").val();
            var index_edate =  $("#index_end").val();
            $.post("index.php", {module:'ModSecurities', action:'ConvertCustodian', convert_table:'update_index', symbol:index_symbol, sdate:index_sdate, edate:index_edate}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });

        $("#integrity_check").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var custodian = $("#custodian").val();
            var date = $("#date").val();
            $("#integrity_table tbody").empty();
            $.post("index.php", {module:'PortfolioInformation', action:'ConvertCustodian', convert_table:'integrity_check', custodian:custodian, date:date}, function(response){
                progressInstance.hide();
                console.log(response);
                var data = $.parseJSON(response);
                console.log(data);
                data.info.forEach(function(obj){
                    var account_number = "<td>" + obj.account_number + "</td>";
                    var crm = "<td>" + obj.crm_value + "</td>";
                    var cust = "<td>" + obj.custodian_value + "</td>";
                    var difference = "<td>" + obj.difference + "</td>";
                    var row = "<tr style='background-color: " + obj.color + "' data-value='" + obj.color + "' data-account='" + obj.account_number + "'>" + account_number + crm + cust + difference + "</tr>";
                    $("#integrity_table tbody").append(row);
                });
/*                $("<div id='integrity_div' style='width:640px; height:480px; display:block; overflow:auto;'><table id='integrity_table' style='width:100%;'><thead><tr><td>Account Number</td><td>CRM Value</td><td>Custodian Value</td><td>Difference</td></tr></thead></table></div>").dialog({
                    modal:true,
                    title:"Integrity Results",
                    width:640,
                    height:480,
                    open: function(event, ui){
                        data.info.forEach(function(obj){
                            var account_number = "<td>" + obj.account_number + "</td>";
                            var crm = "<td>" + obj.crm_value + "</td>";
                            var custodian = "<td>" + obj.custodian_value + "</td>";
                            var difference = "<td>" + obj.difference + "</td>";
                            var row = "<tr style='background-color: " + obj.color + "'>" + account_number + crm + custodian + difference + "</tr>";
                            $("#integrity_table").append(row);
                        });
                    }
                });*/
            });
        });
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = CloudInteractions_Module_Js.getInstanceByView();
    instance.registerEvents();
});