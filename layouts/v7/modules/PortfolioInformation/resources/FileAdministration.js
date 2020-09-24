/**
 * Created by ryansandnes on 2017-05-24.
 */

function UpdateStatus(code, element){
    $.post("GetStatus.php", {code:code}, function(response) {
        $(element).html(response);
        if(response !== 'finished')
            setTimeout(UpdateStatus(code, element), 5000);
    });
}

jQuery.Class("FileAdministration_Module_Js",{
    currentInstance : false,
    table: Array(),

    getInstanceByView : function(){
        var instance = new FileAdministration_Module_Js();
        return instance;
    },

},{
    ClickEvents: function(){
        var self = this;

        $("#PullRecalculate").click(function(e){
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'PullRecalculate', custodian:'TD'}, function(response) {

            });
            UpdateStatus('TDUPDATER', '.current-status');
        });

        $("#add-row").click(function(e){
            self.table.addRow({});
            $(".tabulator-cell").css("height","27px");
        });

        $("#RecalculateHomepageWidgets").click(function(e){
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'RecalculateHomepageWidgets'}, function(response) {
//                alert(response);
            });
            UpdateStatus('TDUPDATER', '.current-status');
        });

        $("#ClearReconciledTransactions").click(function(e){
            $.post("index.php", {module:'Transactions', action:'FixTransaction', todo:'ClearReconciledTransactions'}, function(response) {
                alert(response + " transactions removed");
            });
        });

        $("#RecalculateAllHistoricalBalances").click(function(e){
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'RecalculateAllHistoricalBalances'}, function(response) {
                alert(response);
            });
            UpdateStatus('TDBALANCEUPDATE', '.calculation-status');
        });
    },

    parseButton : function(value, data, cell, row, options){
        var self = this;
        var id = value.getRow().getData().id;
        $(".parseData").on("click", function(e){
            e.stopPropagation();
            e.stopImmediatePropagation();
            var parseID = $(this).data('id');
            var num_days = $(this).siblings('.num_days').val();

            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'ParseData', parseID:parseID, parse_type:'parse_all', num_days:num_days}, function(response) {
                console.log(response);
            });
            UpdateStatus('MANUALPARSING', '.parse-status');
        });
        return "<button class='parseData' data-id="+id+">Parse Data</button> <input class='num_days' type='text' value='7' />";
    },

    RenderTable : function(){
        var self = this;
        $.post("index.php", {module:'PortfolioInformation', action:'FileAdministration', todo:'getlocations'}, function(response){
//        alert(response);
            var table = new Tabulator("#file-locations-table", {
                data:$.parseJSON(response),
                addRowPos:"top",
                layout:"fitColumns",
                columns:[
                    {title:"ID", field:"id", sorter:"number"},
                    {title:"Custodian", field:"custodian", editor:"select", editorParams:{values:{"TD":"TD", "Fidelity":"Fidelity", "Fidelity(FTP)":"FidelityFTP", "Schwab":"Schwab", "Pershing":"Pershing", "RaymondJames":"Raymond James", "Disabled":"Disabled"}}},
                    {title:"Rep Code", field:"rep_code", editor:true},
                    {title:"Omni Code", field:"omni_code", editor:true},
                    {title:"Active", field:"currently_active", editor:"select", formatter:"tickCross", sorter:"number", editorParams:{values:{"0":"No", "1":"Yes"}}},
                    {title:"Parse", formatter:self.parseButton}
                ],
                cellEdited:function(cell){
                    var row = cell.getRow();
                    var data = row.getData();
                    $.post("index.php", {module:'PortfolioInformation', action:'FileAdministration', todo:'UpdateFileField', RowData:data}, function(response){
                        var id = response;
                        if(id > 0) {
                            row.update({id: id});
                        }
                    });

                },
            });
            self.table = table;
        });
    },

    registerEvents : function() {
        this.RenderTable();
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {
    var instance = FileAdministration_Module_Js.getInstanceByView();
    instance.registerEvents();
});