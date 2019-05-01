/* ********************************************************************************
 * The content of this file is subject to the Tabs ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTETabs_Upgrade_Js",{

},{
    registerEventForUpgradeButton: function () {
        jQuery('button[name="btnUpgrade"]').on('click', function (e) {
            app.helper.showProgress('Upgrading...');
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Upgrade';
            params['mode'] = 'upgradeModule';
            app.request.post({'data':params}).then(
                function(err,data){
                    if(err === null) {
                        app.helper.hideProgress();
                        var params = {
                            message: 'Module Upgraded',
                        };
                        app.helper.showSuccessNotification(params);
                    }else{
                        app.helper.hideProgress();
                    }
                }
            );
        });
    },

    registerEventForReleaseButton: function () {
        jQuery('button[name="btnRelease"]').on('click', function (e) {
            app.helper.showProgress('Release license...');
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Upgrade';
            params['mode'] = 'releaseLicense';
            app.request.post({'data':params}).then(
                function(err,data){
                    app.helper.hideProgress();
                    if(err === null) {
                        var params = {
                            message: 'License Released',
                        };
                        app.helper.showSuccessNotification(params);
                        document.location.href="index.php?module=VTETabs&parent=Settings&view=Settings&mode=step2";
                    }
                }
            ); 
        });
    },

    registerEvents: function(){
        this.registerEventForUpgradeButton();
        this.registerEventForReleaseButton();
    }
});

jQuery(document).ready(function(){
    var instance = new VTETabs_Upgrade_Js();
    instance.registerEvents();
    
    // Fix issue not display menu
    Vtiger_Index_Js.getInstance().registerEvents();
});