/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("VTEEmailMarketing_Edit_Js", {},
    {
        registerMakeDisableFields : function (form) {
            var fieldsName = ['vtecampaigns','subject','sender','scheduled','batch_delivery','total','queued','sent','failed_to_send','unique_open','unopened','unsubcribes','vteemailmarketing_status','assigned_user_id','smtp_server'];
            $.each(fieldsName,function (i,field) {
                if(field == 'assigned_user_id' || field == 'vteemailmarketing_status'){
                    form.find('[name="'+field+'"]').attr('readonly','readonly');
                }else{
                    form.find('[name="'+field+'"]').addClass('chzn-disabled').attr('disabled','disabled');

                }
            })

        },

        registerUnsetDefaultEvents:function (){
            $('.btn.addButton.btn-default.createFilter').on('click',function (){
                $(function() {
                    removeUncheckSetAsDefault();
                    myInterVal = setInterval( function(){ removeUncheckSetAsDefault() },1000);
                    function removeUncheckSetAsDefault() {
                        let setAsDefault = $(document).find('#CustomView [name="setdefault"]');
                        if(setAsDefault.length > 0){
                            setAsDefault.prop('checked', false);
                            clearInterval(myInterVal);
                        }
                    }
                });
            });
        },

        registerEvents : function () {
            this._super();
            var form = $('#EditView');
            this.registerMakeDisableFields(form);
            this.registerUnsetDefaultEvents();
        }
    });

