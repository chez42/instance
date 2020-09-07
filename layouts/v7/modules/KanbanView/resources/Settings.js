/* ********************************************************************************
 * The content of this file is subject to the Kanban View ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
Vtiger_Index_Js("Settings_KanbanView_Settings_Js",{
    instance:false,
    getInstance: function(){
        if(Settings_KanbanView_Settings_Js.instance == false){
            var instance = new Settings_KanbanView_Settings_Js();
            Settings_KanbanView_Settings_Js.instance = instance;
            return instance;
        }
        return Settings_KanbanView_Settings_Js.instance;
    }
},{
   
    init : function() {
        this.initiate();
    },
    /*
     * Function to initiate the step 1 instance
     */
    initiate : function(){
        var step=jQuery(".installationContents").find('.step').val();
        this.initiateStep(step);
    },
    /*
     * Function to initiate all the operations for a step
     * @params step value
     */
    initiateStep : function(stepVal) {
        var step = 'step'+stepVal;
        this.activateHeader(step);
    },

    activateHeader : function(step) {
        var headersContainer = jQuery('.crumbs ');
        headersContainer.find('.active').removeClass('active');
        jQuery('#'+step,headersContainer).addClass('active');
    },

    registerEnableModuleEvent:function() {
        jQuery('.summaryWidgetContainer').find('#enable_module').change(function(e) {
            app.helper.showProgress();
            var element=e.currentTarget;
            var value=0;
            var text="Kanban View Disabled";
            if(element.checked) {
                value=1;
                text = "Kanban View Enabled";
            }
            var params = {};
            params.action = 'ActionAjax';
            params.module = 'KanbanView';
            params.value = value;
            params.mode = 'enableModule';
            app.request.post({data:params}).then(
                function(err,data){
                    if(err == null){
                        app.helper.hideProgress();
                        app.helper.showSuccessNotification({message:text});
                    }
                }
            );
        });
    },
    
    registerEvents: function(){
        this._super();
        this.registerEnableModuleEvent();
    }
    
});