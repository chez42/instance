function remove_widget_on_dashboard(link_run){
    var li_parent = jQuery(link_run).closest('li')
    var link_id = li_parent.attr('id');
    var params = {
        'module' : 'VTEReports',
        'action' : "IndexAjax",
        'mode' : "removeDeletedWidgetOnDahboard",
        'link_id' : link_id
    }
    app.helper.showProgress();
    app.request.post({data:params}).then(
        function(err,data){
            if(err === null) {
                app.helper.hideProgress();
                li_parent.remove();
                params = {
                    title: app.vtranslate('Widget has been removed from Dashboard'),
                    type: 'info'
                };
                app.helper.showSuccessNotification(params);

            }else{
                // to do
            }
        }
    );
}
Vtiger.Class("VTEReports_Menu_Js",{},{
    registerEvents : function (){
        var thisInstance = this;
        var params = {
            'module' : 'VTEReports',
            'action' : "CheckPermission",
        }
        app.request.post({data:params}).then(
            function(err,res){
                if(err === null) {
                    if (res==true){
                        thisInstance.registerEventAddMenuItemVTEReportToMenubar();
                        thisInstance.registerEventAddMenuItemVTEReportToTopbar();
                    }
                }
            }
        );
    },
    registerEventAddMenuItemVTEReportToTopbar : function(){
        var navbar = jQuery("#navbar");
        var link_fa_bar_chart = navbar.find("a.fa-bar-chart");
        if(link_fa_bar_chart.length == 0){
            link_fa_bar_chart = navbar.find("a.fa-calendar");
        }
        var link_vte_reports = '<li><div><a href="index.php?module=VTEReports&view=List" class="fa fa-area-chart" title="'+app.vtranslate('VTEReports')+'" aria-hidden="true"></a></div></li>';
        if(link_fa_bar_chart.length == 0){
            link_fa_bar_chart = navbar.find("span.fa-user");
            link_fa_bar_chart.closest('li').before(link_vte_reports)
        }else{
            link_fa_bar_chart.closest('li').after(link_vte_reports);
        }
    },
    registerEventAddMenuItemVTEReportToMenubar : function(){
        var appListElement = jQuery('.app-list');
        var appListDivier = appListElement.find('.app-list-divider');
        var menuItemVTEReportsElemnt = appListElement.find('.vtereport-menu-item');
        if (appListDivier.length > 0 && menuItemVTEReportsElemnt.length == 0){
            var menuItemHtmlForVTEReports =
                '<div class="menu-item app-item app-item-misc vtereport-menu-item" data-default-url="index.php?module=VTEReports&view=List">' +
                '   <div class="menu-items-wrapper">' +
                '       <span class="app-icon-list fa fa-bar-chart"></span>' +
                '       <span class="app-name textOverflowEllipsis"> VTE Reports </span>' +
                '   </div>' +
                '</div>';
            $(menuItemHtmlForVTEReports).insertAfter(appListDivier);

            // re-register event for class app-item
            jQuery('.app-item').on('click', function() {
                var url = jQuery(this).data('defaultUrl');
                if(url) {
                    window.location.href = url;
                }
            });
        }
    },
});

jQuery(document).ready(function() {
    var instance= new VTEReports_Menu_Js();
	instance.registerEvents();

    registerAddWidget();
    app.event.on("post.DashBoardTab.load", function () {
        registerAddWidget();
    });
    //var link_vte_reports = navbar.find("a.fa-bar-chart");
});
function registerAddWidget(){
    jQuery('.widgetsList li a').on('click',function () {
        var activeTabId = Vtiger_DashBoard_Js.currentInstance.getActiveTabId();
        jQuery('.widgetsList li').show();

        jQuery(this).hide();
        jQuery('.dashBoardTabContents li.dashboardWidget',activeTabId).each(function () {
            var wgName = jQuery(this).data('name');
            var wgId = jQuery(this).atte('id');
            jQuery('.widgetsList li a[data-name="'+wgName+'"]').filter('[data-linkid="'+wgId+'"]').parent().hide();
        });
    });
}
