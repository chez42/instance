var filters = [];
jQuery.Class("Downloader_Module_Js",{
    currentInstance : false,
    preferred_ids : [],
    getInstanceByView : function(){
        var instance = new Downloader_Module_Js();
        return instance;
    }
},{
/*    ClickEvents: function(){
        var self = $(this);
        $("#index_list tbody tr").click(function(){
            $(this).toggleClass('preferred');
            self.preferred_ids = [];
            $('.preferred').each(function(i, obj){
                self.preferred_ids.push($(this).data('id'));
            });

            $.post("index.php", {module:'PortfolioInformation', action:'Downloader', todo:'save_preferences', preferred_ids:self.preferred_ids}, function(response){
                console.log(response);
            });
        });

    },

    SelectEvents: function(){
        var data = $.parseJSON($("#capitalization_data").val());
        filters[0] = $('#capitalization_filter').comboTree({
            source: data,
            isMultiple: true
        });

        data = $.parseJSON($("#style_data").val());
        filters[1] = $('#style_filter').comboTree({
            source: data,
            isMultiple: true
        });

        data = $.parseJSON($("#international_data").val());
        filters[2] = $('#international_filter').comboTree({
            source: data,
            isMultiple: true
        });

        data = $.parseJSON($("#sector_data").val());
        filters[3] = $('#sector_filter').comboTree({
            source: data,
            isMultiple: true
        });

        data = $.parseJSON($("#asset_class_data").val());
        filters[4] = $('#asset_class_filter').comboTree({
            source: data,
            isMultiple: true
        });
    },

    registerEvents : function() {
        this.ClickEvents();
        this.SelectEvents();
    },

    fillPreferences: function(){
        var preferences = $.parseJSON($("#user_preferences").val());
        preferences.forEach(function(obj){
            $("#index_list tbody tr").each(function(){
                var row = $(this);
                if(row.data("id") == obj) {
                    row.addClass("preferred");
                }
            });
        });
    }*/
});

function EnableAllRows(){
    $("#index_list tbody tr").each(function(){
        var row = $(this);
        row.attr("data-enabled", 1);
    });
}

function HideShowFilter(){
    EnableAllRows();
    filters.forEach(function(obj){
        var element_id = obj.elemInput.id;
        var selected = obj.getSelectedItemsTitle();
        $("#index_list tbody tr").each(function(){
            var row = $(this);
            row.children("td").each(function() {
                switch(this.className){
                    case element_id:
                        var data_value = $(this).data(element_id);
                        if($.inArray(data_value, selected) === -1 && selected !== false) {
                            var tmp = row.closest('tr');
                            tmp.attr("data-enabled", 0);
                        }
                        break;
                }
            });
        });
    });

    $("#index_list tbody tr").each(function(){
        var row = $(this);
        if(row.attr("data-enabled") == 0)
            row.hide();
        else {
            row.show();
        }
    });
}

jQuery(document).ready(function($) {
    var instance = Downloader_Module_Js.getInstanceByView();
    instance.registerEvents();
    instance.fillPreferences();
});