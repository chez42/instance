/* ********************************************************************************
 * The content of this file is subject to the VTEForecast ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("VTEForecast_EditCategoryOpptType_Js",{

},{
    //updatedBlockSequence : {},
    registerAddChildrenEvent : function() {
        var thisInstance = this;

        var btnSave = jQuery('#btnSave');
        btnSave.on('click',function(){
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            var categoryIdString = jQuery('#category_id').val();
            var categoryNameString = jQuery('#category_name').val();
            var categoryColorString = jQuery('#category_color').val();

            var chkString = '';
            jQuery("input:checkbox[name=chk]:checked").each(function(){
                chkString += "," +jQuery(this).val();
            });

            if(categoryNameString==''){
                var params = {};
                params.text = app.vtranslate('Please Enter VTEForecast Category Name');
                params.type = 'error';
                Settings_Vtiger_Index_Js.showMessage(params);
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                jQuery('#category_name').focus();
                return false;
            }

            if(chkString==''){
                var params = {};
                params.text = app.vtranslate('You must be select at least a Opportunity Type');
                params.type = 'error';
                Settings_Vtiger_Index_Js.showMessage(params);
                progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                return false;
            }

            var actionParams = {
                "type":"POST",
                "dataType":"json",
                "data" : {
                    'category_id':categoryIdString,
                    'category_name':categoryNameString,
                    'opportunity_type':chkString,
                    'category_color':categoryColorString,
                    'module':app.getModuleName(),
                    'action':'ActionAjax',
                    'mode':'saveCategoryOpptType'
                }
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    if(data['success']) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        var params = {};
                        params.text = app.vtranslate('Saved');
                        Settings_Vtiger_Index_Js.showMessage(params);
                        window.location.href = "index.php?module=VTEForecast&parent=Settings&view=Settings&tab=opportunityyype";
                    }
                },
                function(error) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});

                    //TODO : Handle error
                }
            );
            return false;
        });

    },

    registerEvents : function() {
        this.registerAddChildrenEvent();
    }
});