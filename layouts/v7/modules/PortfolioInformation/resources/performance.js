jQuery(document).ready(function($){
    jQuery.BlinkCalculate = function BlinkCalculate(){
        $("[name=QTR_CALCULATING]").animate({opacity:0},600,"linear",function(){
          $(this).animate({opacity:1},600,scroll);
        });

        $("[name=YTD_CALCULATING]").animate({opacity:0},600,"linear",function(){
          $(this).animate({opacity:1},600);
        });

        $("[name=TRAILING_CALCULATING]").animate({opacity:0},600,"linear",function(){
          $(this).animate({opacity:1},600);
        });
        
        $("[name=INCEPTION_CALCULATING]").animate({opacity:0},600,"linear",function(){
          $(this).animate({opacity:1},600);
        });
    }
    
    jQuery.CalculatePerformance = function CalculatePerformance(pids){
        $.BlinkCalculate();
        $.post('TWRExecution.php',{'pids':pids}, function(data)
        {
            var x = $.parseJSON(data);
            $("[name=TWR_INCEPTION]").html(x.INCEPTION+"%");
            if(x.TRAILING_WARNING)
                $("[name=TWR_TRAILING]").html("<span style='color:red;'>"+x.TRAILING_WARNING+"</span>"+x.TRAILING+"%");
            else
                $("[name=TWR_TRAILING]").html(x.TRAILING+"%");
            
			if(x.TRAILING_WARNING)
            {
            	$("[name=WARNING]").parent("tr").removeClass("hide");
                $("[name=WARNING]").html("* The inception is less than one year").show(); //Change 5May,2016
                $("[name=TWR_WARNING]").html("*");
            } else {
            	if(!$("[name=WARNING]").parent("tr").hasClass("hide"))
            		$("[name=WARNING]").parent("tr").addClass("hide");
            }
            $("[name=TWR_QTR]").html(x.QTR+"%");
            $("[name=TWR_YTD]").html(x.YTD+"%");

            $("[name=YTD_INCEPTION]").html(x.YTD+"%");

            $("#pTWR_INCEPTION").val(x.INCEPTION);
            $("#pTWR_QTR").val(x.QTR);
            $("#pTWR_TRAILING").val(x.TRAILING);
            $("#pTWR_YTD").val(x.YTD);
            $("#submit").removeAttr("disabled");

            $("[name=TWR_INCEPTION_TYPE]").html("("+x.INCEPTION_TYPE+")");
            $("[name=TWR_TRAILING_TYPE]").html("("+x.TRAILING_TYPE+")");
            $("[name=TWR_YTD_TYPE]").html("("+x.YTD_TYPE+")");
            $("[name=TWR_QTR_TYPE]").html("("+x.QTR_TYPE+")");
        });
/*
        $.ajax({
            type: "POST",
            url: "index.php?module=PortfolioInformation&action=RunTWR0",
            data:{pids:pids},
            beforeSend:function(){
                $.BlinkCalculate();
            },
            success: function(data){
                console.log("DATA: " + data);
                var x = $.parseJSON(data);
                $("[name=TWR_INCEPTION]").html(x.INCEPTION+"%");
                if(x.TRAILING_WARNING)
                  $("[name=TWR_TRAILING]").html("<span style='color:red;'>"+x.TRAILING_WARNING+"</span>"+x.TRAILING+"%");
                else
                  $("[name=TWR_TRAILING]").html(x.TRAILING);
                if(x.TRAILING_WARNING)
                {
                    $("[name=WARNING]").html("* The inception is less than one year").show(); //Change 5May,2016
                    $("[name=TWR_WARNING]").html("*");
                }
                $("[name=TWR_QTR]").html(x.QTR+"%");
                $("[name=TWR_YTD]").html(x.YTD+"%");

                $("[name=YTD_INCEPTION]").html(x.YTD+"%");

                $("#pTWR_INCEPTION").val(x.INCEPTION);
                $("#pTWR_QTR").val(x.QTR);
                $("#pTWR_TRAILING").val(x.TRAILING);
                $("#pTWR_YTD").val(x.YTD);
                $("#submit").removeAttr("disabled");

                $("[name=TWR_INCEPTION_TYPE]").html("("+x.INCEPTION_TYPE+")");
                $("[name=TWR_TRAILING_TYPE]").html("("+x.TRAILING_TYPE+")");
                $("[name=TWR_YTD_TYPE]").html("("+x.YTD_TYPE+")");
                $("[name=TWR_QTR_TYPE]").html("("+x.QTR_TYPE+")");
              }
        });*/
    };
    
    var value = ($(document).find("[name=pids]").val());
    if(typeof(value) !== "undefined")
        $.CalculatePerformance(value);
});