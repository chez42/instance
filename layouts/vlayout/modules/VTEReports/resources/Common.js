function remove_widget_on_dashboard(link_run){
    var li_parent = jQuery(link_run).closest('li')
    var link_id = li_parent.attr('id');
    var params = {
        'module' : 'VTEReports',
        'action' : "IndexAjax",
        'mode' : "removeDeletedWidgetOnDahboard",
        'link_id' : link_id
    }
    var progressIndicatorElement = jQuery.progressIndicator({
        'position' : 'html',
        'blockInfo' : {
            'enabled' : true
        }
    });
    AppConnector.request(params).then(
        function(data) {
            progressIndicatorElement.progressIndicator({
                'mode' : 'hide'
            });
            li_parent.remove();
            params = {
                title: app.vtranslate('Widget has been removed from Dashboard'),
                type: 'info'
            };
            Vtiger_Helper_Js.showPnotify(params);
        },
        function(error,err){
            //aDeferred.reject();
        }
    );
}