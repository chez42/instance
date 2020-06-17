jQuery.Class("ManualInteractions_Module_Js",{
	currentInstance : false,
        
	getInstanceByView : function(){
            var instance = new ManualInteractions_Module_Js();
	    return instance;
	}
},{ 
    CopySecurityCodes : function(){
        $("#securityCodes").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'CopySecurityCodes'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    BadPortfolios : function(){
        $("#badPortfolios").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'RemoveBadPortfolios'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    CopyPositions : function(){
        $("#copyPositions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var date = $("#positionDate").val();
            var symbol = $("#positionName").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'CopyPositions', date:date, symbol:symbol}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    ControlNumberTransactionsReset : function(){
        $("#control_number_transactions_reset").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var control_number = $("#control_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'RestAccountTransactionsFromControlNumber', control_number:control_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    PortfolioInformationNumbersReset : function(){
        $("#portfolio_information_numbers_reset").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var control_number = $("#control_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'PortfolioInformationNumbersReset', control_number:control_number}, function(response){
                progressInstance.hide();
                alert(response);
            });            
        });
    },
    
    ResetTransactions : function(){
        $("#transactions_account_reset").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#transactions_account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'ResetAccountTransactions', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    AccountAnnihilation : function(){
        $("#account_annihilation").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#transactions_account_number").val();
            alert("This is an account total annihilation.  It is as destructive as it sounds.  If you truly want to do this, disable the return line under the function AccountAnnihilation in ManualInteraction.js");
            progressInstance.hide();
            return;
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'TotalAnnihilation', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },

    SingleAssetAllocation : function(){
        $("#accountAssetAllocation").click(function(e){
            e.stopImmediatePropagation();
            var account_number = $("#assetAccountNumber").val();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'CalculateAccountAssetAllocation', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    AssetAllocation : function(){
        $("#assetAllocation").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'CalculateAssetAllocation'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },

    SSNReset : function(){
        $("#ssn_reset").click(function(e){
            e.stopImmediatePropagation();
            var account_number = $("#transactions_account_number").val();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'SSNReset', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    IndividualSecurity : function(){
        $("#ModSecuritiesIndividual").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var symbol = $("#ModSecurity").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'PullIndividualSecurity', symbol:symbol}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    ModSecuritiesAll : function(){
        $("#ModSecuritiesAll").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'PullAllSecurities'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    ModSecuritiesPricingAll : function(){
        $("#ModSecuritiesPricingAll").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'UpdateAllPrices'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    ModSecuritiesIndividualPrice : function(){
        $("#ModSecuritiesIndividualPrice").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var symbol = $("#ModSecurity").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'UpdateIndividualPrice', symbol:symbol}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },

    ResetPortfolioTransactions : function(){
        $("#portfolio_id_reset").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var portfolio_id = $("#portfolio_id").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'ResetPortfolioTransactions', portfolio_id:portfolio_id}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    HistoricalUpdate : function(){
        $("#historicalUpdate").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var date = $("#historicalDate").val();
            alert("If you SERIOUSLY want to do this, disable this alert in ManualInteraction.js");
            progressInstance.hide();
            return;
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'HistoricalUpdate', historical_date:date}, function(response){
                progressInstance.hide();
                alert(response);
            });            
        });
    },
    
    AutoCloseAccounts : function(){
        $("#autoCloseAccounts").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'AutoCloseAccounts'}, function(response){
                progressInstance.hide();
                alert(response);
            });                        
        });
    },
    
    PullPrices : function(){
        $("#pullPrices").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'PullPrices'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    ClientContacts : function(){
        $("#ClientContacts").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var reset = 0;
            if($("#ResetClients").is(":checked"))
                reset = 1;

            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'ClientContacts', reset:reset}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    ClientHouseholds : function(){
        $("#ClientHouseholds").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var reset = 0;
            if($("#ResetClients").is(":checked"))
                reset = 1;

            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'ClientHouseholds', reset:reset}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    SMAAccountDescription : function(){
        $("#SMAAccountDescription").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'SMAAccountDescription'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    PullIndividualSecurity : function(){
        $("#pullIndividualSecurity").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var security = $("#individualSecurity").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'CopyIndividualSecurityFromPC', symbol : security}, function(response){
                progressInstance.hide();
                alert(response);
            });            
        });
    },

    PullAllSecurities : function(){
        $("#pullAllSecurities").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'PullAllSecurities'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    CopyCurrentPortfolioInformation : function(){
        $("#copyCurrent").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'CopyCurrentPortfolioInformation'}, function(response){
                progressInstance.hide();
                alert(response);
            });            
        });
    },
    
    ControlNumberHistoricalUpdate : function(){
        $("#control_number_historical_update").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var control_number = $("#control_number").val();
            var date = $("#control_number_historical_date").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'ControlNumberHistoricalUpdate', date:date, control_number:control_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    AccountNumberHistoricalUpdate : function(){
        $("#account_number_historical_update").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#transactions_account_number").val();
            var date = $("#account_number_historical_date").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'AccountNumberHistoricalUpdate', date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    AccountNumberTrailingUpdate : function(){
        $("#account_number_trailing_update").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#transactions_account_number").val();
            var date = $("#account_number_historical_date").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'AccountNumberTrailingUpdate', date:date, account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    PullSecurityPriceHistory : function(){
        $("#pullSecurityPrice").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var security_name = $("#securityPriceName").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'PullSecurityPrices', security_name:security_name}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    CreateSecurityPrice : function(){
        $("#InsertSecurityPriceSubmit").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var price = $("#InsertSecurityPricePrice").val();
            var date  = $("#InsertSecurityPriceDate").val();
            var security = $("#InsertSecurityPriceName").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'InsertSecurityPrice', security_name:security, date:date, price:price}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    UpdateAccountInceptionDate : function(){
        $("#UpdateAccountInceptionDate").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#transactions_account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'UpdateAccountInceptionDate', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },

    FixNullInceptions : function(){
        $("#FixNullInceptions").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'FixNullInceptions'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
/*    
    UpdateAccountInceptionDate : function(){
        $("#UpdateAllInceptionDates").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'UpdateAllInceptionDates'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },*/
    
    UpdateAdvisorControlNumber : function(){
        $("#UpdateAdvisorControlNumber").click(function(e){
            e.stopImmediatePropagation();
            var progressInstance = jQuery.progressIndicator();
            var account_number = $("#transactions_account_number").val();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'UpdateAdvisorControlNumber', account_number:account_number}, function(response){
                progressInstance.hide();
                alert(response);
            });            
        });
    },
    UndefinedSecurityType : function(){
        $("#undefined_security_type").click(function(e){
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'ManualInteractions', todo:'RemoveUndefinedSecurityType'}, function(response){
                progressInstance.hide();
                alert(response);
            });
        });
    },
    
    registerEvents : function() {
        this.CopySecurityCodes();
        this.CopyPositions();
        this.ResetTransactions();
        this.ResetPortfolioTransactions();
        this.HistoricalUpdate();
        this.AccountAnnihilation();
        this.AutoCloseAccounts();
        this.IndividualSecurity();
        this.ModSecuritiesAll();
        this.ModSecuritiesPricingAll();
        this.ModSecuritiesIndividualPrice();
        this.AssetAllocation();
        this.SingleAssetAllocation();
        this.PullPrices();
        this.ControlNumberTransactionsReset();
        this.BadPortfolios();
        this.ClientContacts();
        this.ClientHouseholds();
        this.SMAAccountDescription();
        this.PullIndividualSecurity();
        this.CopyCurrentPortfolioInformation();
        this.PortfolioInformationNumbersReset();
        this.ControlNumberHistoricalUpdate();
        this.AccountNumberHistoricalUpdate();
        this.AccountNumberTrailingUpdate();
        this.PullSecurityPriceHistory();
        this.CreateSecurityPrice();
        this.SSNReset();
        this.UpdateAccountInceptionDate();
        this.UpdateAdvisorControlNumber();
        this.UndefinedSecurityType();
        this.FixNullInceptions();
        this.PullAllSecurities();
    }
});

jQuery(document).ready(function($) {
    var instance = ManualInteractions_Module_Js.getInstanceByView();
    instance.registerEvents();
});