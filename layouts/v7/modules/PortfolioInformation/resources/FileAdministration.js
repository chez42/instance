/**
 * Created by ryansandnes on 2017-05-24.
 */
jQuery.Class("FileAdministration_Module_Js",{
    currentInstance : false,
    table: Array(),

    getInstanceByView : function(){
        var instance = new FileAdministration_Module_Js();
        return instance;
    }
},{
    UpdateStatus: function(){
        var self = this;
        $.post("GetStatus.php", {code:'TDUPDATER'}, function(response) {
            $(".current-status").html(response);
            if(response !== 'finished')
                setTimeout(self.UpdateStatus(), 5000);
        });
    },

    ClickEvents: function(){
        var self = this;

        $("#PullRecalculate").click(function(e){
            $.post("index.php", {module:'PortfolioInformation', action:'CustodianInteractions', todo:'PullRecalculate', custodian:'TD'}, function(response) {

            });
            //936980564
            self.UpdateStatus();
        });

        $("#add-row").click(function(e){
            self.table.addRow({});
            $(".tabulator-cell").css("height","27px");
        });

/*
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

        $(".addrow").click(function(e){
            var progressInstance = jQuery.progressIndicator();
            $.post("index.php", {module:'PortfolioInformation', action:'Administration', todo:'CreateFileLocation'}, function(response){
                progressInstance.hide();
                var id = response;
                $('.FileLocationsTable tr:last').after('<tr> ' +
                    '<td><input type="text" value="'+id+'" name="id" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="custodian" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="tenant" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="file_location" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="rep_code" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="master_rep_code" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="omni_code" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="prefix" data-id="'+id+'" /></td> ' +
                    '<td><input type="text" value="" name="suffix" data-id="'+id+'" /></td> ' +
                    '</tr>');
            });
        });*/
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
                    {title:"Active", field:"currently_active", editor:"select", sorter:"number", editorParams:{values:{"0":"No", "1":"Yes"}}}
                ],
                cellEdited:function(cell){
                    var row = cell.getRow();
                    var data = row.getData();
                    $.post("index.php", {module:'PortfolioInformation', action:'FileAdministration', todo:'UpdateFileField', RowData:data}, function(response){
                        console.log(response);
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