var filters = [];
jQuery.Class("Statements_Module_Js",{
    currentInstance : false,
    preferred_ids : [],
    getInstanceByView : function(){
        var instance = new Statements_Module_Js();
        return instance;
    }
},{
    ClickEvents: function(){

    },

    SelectEvents: function(){
        $("#selections").change(function(){
            var selected_id = $("#selections").find(":selected").data('id');
            $.post("index.php", {module:'PortfolioInformation', action:'Statements', todo:'get_prepared_by', prepared_by:selected_id}, function(response){
                $("#statement_preview").html(response);
                CKEDITOR.instances.prepared_by.setData(response);
            });
        });
    },

    CKEditor: function(){
        var editor = CKEDITOR.replace( 'prepared_by',{
            width:"400",
            removeButtons:'Copy,Cut,Paste,Undo,Redo,Print,Form,TextField,Textarea,Button,SelectAll,NumberedList,BulletedList,CreateDiv,Table,PasteText,PasteFromWord,Select,HiddenField,Link,Unlink,Anchor,Image,Source'
        });

        editor.on('change', function(evt){
            var selected_id = $("#selections").find(":selected").data('id');
            var content = CKEDITOR.instances.prepared_by.getData();
            $.post("index.php", {module:'PortfolioInformation', action:'Statements', todo:'save_prepared_by', prepared_by:selected_id, content:content}, function(response){
                $("#statement_preview").html(response);
            });
        });
    },

    RegisterEvents: function(){
        this.ClickEvents();
        this.SelectEvents();
        this.CKEditor();
    }
});

jQuery(document).ready(function($) {
    var instance = Statements_Module_Js.getInstanceByView();
    instance.RegisterEvents();
});