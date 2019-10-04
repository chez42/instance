/* ********************************************************************************
 * The content of this file is subject to the Export To XLS ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEExportToXLS_Js", {
    instance: false,
    getInstance: function () {
        if (VTEExportToXLS_Js.instance == false) {
            var instance = new VTEExportToXLS_Js();
            VTEExportToXLS_Js.instance = instance;
            return instance;
        }
        return VTEExportToXLS_Js.instance;
    },
},{
    registerAddExportButton : function(){
        // Check enable
        var params = {};
        params['action']= 'ActionAjax';
        params['module'] = 'VTEExportToXLS';
        params['mode'] = 'checkEnable';

        app.request.post({data:params}).then(
            function (err,data) {
                if(err == null) {
                    if (data.enable == '1') {
                        var link_id = app.getModuleName()+'_listView_advancedAction_LBL_EXPORT_EXCEL';
                        var link_url = 'Vtiger_List_Js.triggerExportAction("index.php?module=VTEExportToXLS&sourceModule='+app.getModuleName()+'&view=Export")';
                        $('div.listViewMassActions > ul.dropdown-menu').append('<li class="selectFreeRecords"><a id='+link_id+' onclick='+link_url+' >Export To Excel</a></li>');
                    }
                }
            }
        );
    },
    registerPostListLoadListener : function () {
        var thisInstance = this;
        app.event.on('post.listViewFilter.click', function () {
            thisInstance.registerAddExportButton();
        });
    },

    registerEvents: function(){
        this.registerAddExportButton();
        this.registerPostListLoadListener();
    }
});

//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_VTEExportToXLS();
    }, 10000);
});
function initData_VTEExportToXLS() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTEExportToXLS')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    // Does not load on other views
    if(app.view() !='List') return;
    var instance = new VTEExportToXLS_Js();
    instance.registerEvents();
}
