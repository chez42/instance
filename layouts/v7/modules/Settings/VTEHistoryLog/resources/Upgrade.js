/* * *******************************************************************************
 * The content of this file is subject to the VTE History Log ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger.Class("VTE_HistoryLog_Upgrade_Js",{

},{
    registerEventForUpgradeButton: function () {
        jQuery('button[name="btnUpgrade"]').on('click', function (e) {
            app.helper.showProgress();

            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Upgrade';
            params['mode'] = 'upgradeModule';

            app.request.post({'data':params}).then(
                function(err,data){
                    app.helper.hideProgress();
                    app.helper.showSuccessNotification({'message':'Module Upgraded'});
                    if (data) {
                        // app.helper.showSuccessNotification({'message':'Module Upgraded'});
                    }
                },
                function (error) {
                    app.helper.hideProgress();
                }


            );
        });
    },

    registerEventForReleaseButton: function () {
        jQuery('button[name="btnRelease"]').on('click', function (e) {
            app.helper.showProgress();
            var params = {};
            params['module'] = app.getModuleName();
            params['action'] = 'Upgrade';
            params['mode'] = 'releaseLicense';

            app.request.post({'data':params}).then(
                function(err,data){
                    app.helper.hideProgress();
                    if (data) {
                        app.helper.showSuccessNotification({'message':'License Released'});
                        document.location.href="index.php?module=VTEHistoryLog&parent=Settings&view=Settings&mode=step2";
                    }
                },
                function (error) {
                    app.helper.hideProgress();
                }
            );
        });
    },

    registerEvents: function(){
        this.registerEventForUpgradeButton();
        this.registerEventForReleaseButton();
    }
});

jQuery(document).ready(function() {
    var instance = new VTE_HistoryLog_Upgrade_Js();
    instance.registerEvents();
});
