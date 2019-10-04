
/*Vtiger.Class('Vtiger_Billing_Js',{}, {
        currentInstance: false,

        getInstanceByView: function () {
            var instance = new OmniOverview_Module_Js();
            return instance;
        }
    },{
    registerDatePicker : function() {
        alert("HERE");
        $("#as_of").datepicker();
    },

    registerEvents : function(){
        this.registerDatePicker();
    }
});*/

$("#as_of").datepicker();//Just registers the text area as datepicker

$("#as_of").change(function(e){
    recalculateBilling();
});

function recalculateBilling(){
    var date = $("#as_of").val();
    var recordmodels = $("#recordmodels").val();
    $.post("index.php", {module:'Vtiger', action:'Billing', recordmodels:recordmodels, as_of:date}, function(response){
        console.log(response);
        data = $.parseJSON(response);
        $.each(data, function(k, val){
            if(val !== null) {
                $("#rowid_" + val.account_number + " td.total_value").text(localeString(val.total_value, ','));
                $("#rowid_" + val.account_number + " td.bill_amount").text(localeString(val.bill_amount, ','));
//                $("#rowid_" + val.account_number + " td.production_number").text(val.production_number, ',');
                $("#rowid_" + val.account_number + " td.as_of").text($("#as_of").val());
//                    console.log(val);
            }
        });
    });
};

function localeString(x, sep, grp) {
    var sx = (''+x).split('.'), s = '', i, j;
    sep || (sep = ' '); // default seperator
    grp || grp === 0 || (grp = 3); // default grouping
    i = sx[0].length;
    while (i > grp) {
        j = i - grp;
        s = sep + sx[0].slice(j, i) + s;
        i = j;
    }
    s = sx[0].slice(0, i) + s;
    sx[0] = s;
    return sx.join('.');
};

