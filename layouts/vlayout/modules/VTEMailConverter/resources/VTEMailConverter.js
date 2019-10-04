/* ********************************************************************************
 * The content of this file is subject to the VTEMailConverter("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

var Vtiger_VTEMailConverter_Js = {
    addMailConverterActionInRule: function () {
        var thisInstance = this;
        $('body').on('change', function () {
            if(app.getModuleName() == 'MailConverter' && $('form#ruleSave').length){
                if($('form#ruleSave').length){
                    var form = $('form#ruleSave');
                    var record = form.find('input[name=record]').val();
                    if(form.hasClass('vtemailconverter-handled')){
                        //do not anything
                    }else {
                        form.addClass('vtemailconverter-handled');
                        //add custom action to action list field
                        var params = {};
                        params['module'] = 'VTEMailConverter';
                        params['action'] = 'GetActions';
                        params['mode'] = 'GetVTEMailConverterActions';
                        var aDeferred = jQuery.Deferred();
                        AppConnector.request(params).then(
                            function(data){
                                if(data.success){
                                    var list_actions = data.result;
                                    if(list_actions.length>0){
                                        var custom_actions = '';
                                        for(var i=0; i<list_actions.length; i++){
                                            var action_item = list_actions[i];
                                            custom_actions += '<option value="'+action_item.action+'">'+action_item.action_name+'</option>'
                                        }
                                        var action_element = form.find('select#actions');
                                        action_element.append(custom_actions).val('').trigger('change');
                                        if(record){
                                            thisInstance.setSelectedAction(form, record);
                                        }
                                    }
                                }
                            }
                        );

                        return aDeferred.promise();
                    }
                }
            }
        });
    },

    setSelectedAction: function (form, record) {
        if(form.length>0 && record){
            var params = {};
            params['module'] = 'VTEMailConverter';
            params['action'] = 'GetActions';
            params['mode'] = 'GetMailConverterActionSelected';
            params['record'] = record;
            var aDeferred = jQuery.Deferred();
            AppConnector.request(params).then(
                function(data){
                    if(data.success){
                        var action = data.result;
                        form.find('select#actions').val(action).trigger('change');
                    }
                }
            );

            return aDeferred.promise();
        }
    },

    registerEvents: function () {
        this.addMailConverterActionInRule();
    }
}

//On Page Load
jQuery(document).ready(function() {
    Vtiger_VTEMailConverter_Js.registerEvents();
});
