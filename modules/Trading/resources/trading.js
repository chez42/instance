jQuery(document).ready(function($){

    function AjaxCall(urlAddress, requestType){
      $.ajax({
          type: "POST",
          url: urlAddress,
          data: requestType,
          success: function(data){
            alert(data);
            location.reload();
          },
          error: function(x, t, m) {
                if(t==="timeout") {
                    alert("Timed Out");
                } else {
                    alert(t);
                    alert(m);
                }
            }
      });
    };

    $("#give_access").click(function(){
        var url = "index.php?module=Trading&action=HandleAccess&hui=1";
        var user_id = $("#give_access_select").val();
        var action = "give_access";
        var data = {userid:user_id, trade_action:action}
        AjaxCall(url, data);
    });

    $("#remove_access").click(function(){
        var url = "index.php?module=Trading&action=HandleAccess&hui=1";
        var user_id = $("#remove_access_select").val();
        var action = "remove_access";
        var data = {userid:user_id, trade_action:action}
        AjaxCall(url, data);
    });

    $('#dividends_table').on('click', ".send_button", function(){
        var id = $(this).attr('id');
        var frequency_id = "#t_"+id;
        var frequency = $(frequency_id).val();
        $.ajax({
            type: "POST",
            url: "index.php?module=Trading&action=save_dividend_intervals",
            data:{hui:1, frequency:frequency, symbol_id:id},
            success: function(data){
                alert(data);
            }
        });
    });
        
    $("#enable_table").click(function(){
        $("#dividends_table_wrapper").show();
        $.ajax({
            type: "POST",
            url: "index.php?module=Trading&action=send_dividend_intervals",
            data:{hui:1},
            success: function(data){
              var x = $.parseJSON(data);
              for(var i = 0; i < x.length; i++)
              {
                  $("#dividends_table").append("<tr><td>"+x[i]['security_symbol']+"</td><td><input type='text' id='t_"+x[i]['symbol_id']+"' value='"+x[i]['frequency']+"' /></td><td><input type='button' class=send_button id='"+x[i]['symbol_id']+"' value='Save'</td></tr>");
              }
            }
        });
    });
        
    $("#login").on("click", function(e){
        $.post('index.php',{'module':'Trading', 'hui':1, 'action':'Bridge', 'task':'login'}, function(response)
        {  
            if(response.length > 0){
                alert(response);
                $("#login").attr("disabled", "disabled");
                $("#logout").removeAttr("disabled");
                $("#get_users").removeAttr("disabled");
                $("#get_quote").removeAttr("disabled");
                $("#symbol").removeAttr("disabled");
                $("#get_transactions").removeAttr("disabled");
                $("#get_accounts").removeAttr("disabled");
            }
        });                            
    });
    $("#logout").on("click", function(e){
        $.post('index.php',{'module':'Trading', 'hui':1, 'action':'Bridge', 'task':'logout'}, function(response)
        {  
            alert(response);
            $("#login").removeAttr("disabled");
            $("#logout").attr("disabled", "disabled");
            $("#get_users").attr("disabled", "disabled");
            $("#get_quote").attr("disabled", "disabled");
            $("#symbol").attr("disabled", "disabled");
            $("#get_transactions").attr("disabled", "disabled");
            $("#get_accounts").attr("disabled", "disabled");
        });
    });
    $("#get_users").on("click", function(e){
        $.post('index.php',{'module':'Trading', 'hui':1, 'action':'Bridge', 'task':'get_users'}, function(response)
        {  
            alert(response);
        });
    });
    $("#get_quote").on("click", function(e){
        var symbol = $("#symbol").val();
        $.post('index.php',{'module':'Trading', 'hui':1, 'view':'Quote', 'task':'get_quote', 'symbol':symbol}, function(response)
        {  
            var headerInstance = new Vtiger_Header_Js();
                                    headerInstance.handleQuickCreateData(response, {callbackFunction:function(response){
                                    }});
        });
    });
    $("#get_transactions").on("click", function(e){
        var symbol = $("#symbol").val();
        $.post('index.php',{'module':'Trading', 'hui':1, 'action':'Bridge', 'task':'get_transactions'}, function(response)
        {  
            alert(response);
        });
    });
    $("#get_accounts").on("click", function(e){
        $.post('index.php',{'module':'Trading', 'hui':1, 'action':'Bridge', 'view':'accounts', 'task':'get_accounts'}, function(response)
        {  
            alert(response);
        });
    });  
});