jQuery.Class("Overview_JS",{
	currentInstance : false,
        
	getInstanceByView : function(){
            var instance = new Overview_JS();
	    return instance;
	},
},{
    
    registerEventFilterClick : function(){
        $('[name=filter]').click(function(e){
            alert("CLICKED");
        });
    },

    savePieImage : function(){
        var deferred = new $.Deferred();
        var chart = AmCharts.charts[0];
        chart.AmExport.capture( {}, function() {
            // SAVE TO JPG
            this.toJPG( {}, function( base64 ) {
                // LOG IMAGE DATA
                var image = encodeURIComponent(base64);
                var input = $("<input>")
                    .attr("id", "pie_image")
                    .attr("type", "hidden")
                    .attr("name", "pie_image").val(image);
                $('#OverviewForm').append($(input));
                deferred.resolve(true);
            } );
        });
        return deferred.promise();
    },

    submitForm : function(){
        $("#OverviewForm").submit();
    },

	submitPrint : function(){
    	$('[name=print_pdf]').click(function(e){
    	    //If the pie image hasn't been saved already, save it and then print when it is ready
            if(!Overview_JS.getInstanceByView().hasPieImageBeenSaved()) {
                Overview_JS.getInstanceByView().savePieImage().then(function () {
                    Overview_JS.getInstanceByView().submitForm();
                });
            }else{
                Overview_JS.getInstanceByView().submitForm();//It has been saved already, so just print
            }
        });
	},

    hasPieImageBeenSaved : function(){
        var attr = $("#pie_image");
        if (attr.length > 0) {
            return true;
        }else{
            return false;
        }
    },
    
    registerEventExpensesClick : function(){
        setTimeout(
        function() 
        {
            $('.enable_expenses').click(function(e){
                var checked = $(this).is(':checked');
                if(checked){
                    $('.print_breakdown').show();
                }
                else
                    $('.print_breakdown').hide();
            });
        }, 0);
    },
    
    loadTransactions : function(){/*
        setTimeout(
        function() 
        {
            var num=$('.overview_account_numbers').map(function(){
                    return $(this).val();
                }).get();

            $.post('index.php',{'module':'PortfolioInformation', 'view':'TransactionsNavigation', 'account_numbers':num}, function(response)
                {
                    $(".transactions_section").html(response);
                    var currentdate = new Date();
                    time.html((currentdate.getMonth()+1)+"/"
                              +currentdate.getDate()+"/"
                              +currentdate.getFullYear());
                });
        }, 0);*/
    },
	
    registerJqueryStepsEvent : function(){

    	var overviewContainer = jQuery(document).find('.PortfolioInformationOverviewReport');
    	
		jQuery(overviewContainer).find('.reports_idealforms').idealforms({
			silentLoad: false,
			steps: {
				buildNavItems : false,
				after : function(currentStep){
					
					var totalSteps = this.$steps.length-1;
					
					if(currentStep == 0 || currentStep < totalSteps){
						jQuery(".next").show();
					}else{
						jQuery(".next").hide();
					}
					
					if(currentStep > 0 && currentStep <= totalSteps)
						$('.previous').show();
					else
						$('.previous').hide();
				}, 
				
			},
		});
		
		$('.previous').click(function(){
		  $('.reports_idealforms').idealforms('prevStep');
		});
		$('.next').click(function(){
		    if(!Overview_JS.getInstanceByView().hasPieImageBeenSaved()) {
                Overview_JS.getInstanceByView().savePieImage().then(function () {
                    $('.reports_idealforms').idealforms('nextStep');
                });
            }else{
                $('.reports_idealforms').idealforms('nextStep');
            }
		});

	},
	
    registerEvents : function() {
        this.loadTransactions();
        this.registerEventFilterClick();
        this.registerEventExpensesClick();
        this.registerJqueryStepsEvent();
        this.submitPrint();
    }
});

jQuery(document).ready(function($) {
	var instance = Overview_JS.getInstanceByView();
	instance.registerEvents();
});