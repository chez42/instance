/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("VTEForecast_EditCategoryOpptType_Js",{

},{
    //updatedBlockSequence : {},
    registerAddChildrenEvent : function() {
        var thisInstance = this;

        var btnSave = jQuery('#btnSave');
        btnSave.on('click',function(){
            app.helper.showProgress();
            var categoryIdString = jQuery('#category_id').val();
            var categoryNameString = jQuery('#category_name').val();
            var categoryColorString = jQuery('#category_color').val();

            var chkString = '';
            jQuery("input:checkbox[name=chk]:checked").each(function(){
                chkString += "," +jQuery(this).val();
            });

            if(categoryNameString==''){
                var params = {};
                params.message = app.vtranslate('Please Enter VTEForecast Category Name');
                app.helper.showErrorNotification(params);
                app.helper.hideProgress();
                jQuery('#category_name').focus();
                return false;
            }

            if(chkString==''){
                var params = {};
                params.message = app.vtranslate('You must be select at least a Opportunity Type');
                app.helper.showErrorNotification(params);
                app.helper.hideProgress();
                return false;
            }

            var actionParams = {
                'category_id':categoryIdString,
                'category_name':categoryNameString,
                'opportunity_type':chkString,
                'category_color':categoryColorString,
                'module':app.getModuleName(),
                'action':'ActionAjax',
                'mode':'saveCategoryOpptType'
            };
            app.request.post({'data':actionParams}).then(
                function (err, data) {
                    if(err === null) {
                        app.helper.hideProgress();
                        var params = {};
                        params.message = app.vtranslate('Saved');
                        app.helper.showSuccessNotification(params);
                        window.location.href = "index.php?module=VTEForecast&parent=Settings&view=Settings&tab=opportunityyype";
                    }
                    else {
                        app.helper.hideProgress();

                        //TODO : Handle error
                    }
                }
            );
            return false;
        });

    },

    registerEvents : function() {
        this.registerAddChildrenEvent();
    }
});

jQuery(document).ready(function(){
    var instance = new VTEForecast_EditCategoryOpptType_Js();
    instance.registerEvents();
    
    // Fix issue not display menu
    Vtiger_Index_Js.getInstance().registerEvents();
});