/**
 * Created by ryansandnes on 2017-05-24.
 */
jQuery.Class("FileAdministration_Module_Js",{
    currentInstance : false,

    getInstanceByView : function(){
        var instance = new FileAdministration_Module_Js();
        return instance;
    }
},{
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
        });
    },

    registerEvents : function() {
        this.ClickEvents();
    }
});

jQuery(document).ready(function($) {

/*    var tabledata = [
        {id:1, name:"Billy Bob", age:12, gender:"male", height:95, col:"red", dob:"14/05/2010"},
        {id:2, name:"Jenny Jane", age:42, gender:"female", height:142, col:"blue", dob:"30/07/1954"},
        {id:3, name:"Steve McAlistaire", age:35, gender:"male", height:176, col:"green", dob:"04/11/1982"},
    ];*/

//var tabledata = [{"id":"3","custodian":"schwab","tenant":"Omniscient","file_location":"\/mnt\/lanserver2n\/Schwab\/08134583","rep_code":"08134583","master_rep_code":"","omni_code":"NORCAP","prefix":"08134583_","suffix":null,"last_filename":"\/mnt\/lanserver2n\/Schwab\/08134583\/CRS20200731.RLY","last_filedate":"2020-08-02 03:30:40","currently_active":"1"},{"id":"4","custodian":"fidelity","tenant":"Omniscient","file_location":"\/mnt\/lanserver2n\/Fidelity\/Sowell\/LighthouseGH1","rep_code":"GH1","master_rep_code":"","omni_code":"GH1","prefix":"gh1_","suffix":null,"last_filename":"\/mnt\/lanserver2n\/Fidelity\/Sowell\/LighthouseGH1\/gh1account-(20200909-03h-54m-44s).acc","last_filedate":"2020-09-09 03:54:44","currently_active":"1"},{"id":"5","custodian":"fidelity","tenant":"Omniscient","file_location":"\/mnt\/lanserver2n\/Fidelity\/Sowell\/BonneyGH3","rep_code":"GH3","master_rep_code":"","omni_code":"GH3","prefix":"gh3_","suffix":null,"last_filename":"\/mnt\/lanserver2n\/Fidelity\/Sowell\/BonneyGH3\/gh3account-(20200909-04h-02m-15s).acc","last_filedate":"2020-09-09 04:02:15","currently_active":"1"},{"id":"6","custodian":"fidelity","tenant":"Omniscient","file_location":"\/mnt\/lanserver2n\/Fidelity\/Sowell\/BuskirkGH2","rep_code":"GH2","master_rep_code":"","omni_code":"GH2","prefix":"gh2_","suffix":null,"last_filename":"\/mnt\/lanserver2n\/Fidelity\/Sowell\/BuskirkGH2\/gh2account-(20200909-04h-21m-27s).acc","last_filedate":"2020-09-09 04:21:27","currently_active":"1"},{"id":"7","custodian":"TD","tenant":"Omniscient","file_location":"\/mnt\/lanserver2n\/TDA_FTP\/16NCP","rep_code":"16NCP","master_rep_code":"","omni_code":"NORCAP","prefix":"16NCP_\rtd","suffix":"","last_filename":"\/mnt\/lanserver2n\/TDA_FTP\/16NCP\/16NCPTD200106.TRN","last_filedate":"2020-01-07 02:25:34","currently_active":null}];

    $.post("index.php", {module:'PortfolioInformation', action:'FileAdministration', todo:'getlocations'}, function(response){
//        alert(response);
        var table = new Tabulator("#example-table", {
            data:$.parseJSON(response),
            columns:[
                {title:"id", field:"id", width:200},
                {title:"custodian", field:"custodian", formatter:"progress", sorter:"number"},
                {title:"rep_code", field:"rep_code"},
                {title:"omni_code", field:"omni_code", formatter:"star", hozAlign:"center", width:100},
                {title:"currently_active", field:"currently_active", formatter:"tickCross"},
            ],
//        ajaxURL:"index.php",//"index.php?module=PortfolioInformation&action=FileAdministration",
//        ajaxParams:{module:"PortfolioInformation", action:"FileAdministration", todo:"getlocations"},
//        ajaxContentType:"json"
//        ajaxConfig:"POST",
//            autoColumns:true,
        });

    });

//define table
/*    var table = new Tabulator("#example-table", {
        data:tabledata,
//        ajaxURL:"index.php",//"index.php?module=PortfolioInformation&action=FileAdministration",
//        ajaxParams:{module:"PortfolioInformation", action:"FileAdministration", todo:"getlocations"},
//        ajaxContentType:"json"
//        ajaxConfig:"POST",
        autoColumns:true,
    });
    */
//    var instance = FileAdministration_Module_Js.getInstanceByView();
//    instance.registerEvents();
});