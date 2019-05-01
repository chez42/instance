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
    var instance = FileAdministration_Module_Js.getInstanceByView();
    instance.registerEvents();
});