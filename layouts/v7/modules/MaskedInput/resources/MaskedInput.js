function rtrim(str, charlist) {
    charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
        .replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
    var re = new RegExp('[' + charlist + ']+$', 'g');
    return (str + '')
        .replace(re, '');
}
//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_MaskedInput();
    }, 3000);
});
function initData_MaskedInput() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('MaskedInput')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var module = app.getModuleName();
    var view = app.view();

    // Only load when view is Detail or Edit
    if(view!='Detail' && view!='Edit') return;
    
    // Get config fields
    var actionParams = {
        "type":"POST",
        "url":'index.php?module=MaskedInput&action=ActionAjax&mode=getConfigFields',
        "dataType":"json",
        "data" : {
            'source_module':module
        }
    };
    app.request.post(actionParams).then(
        function(err,data){ 
            if(err === null) {
                var configs=data;
                var arrFields = {};
                $(document).on('click','.content-area',function () {
                    for (field in configs) {
                        var dataConfig=configs[field];
                        var aDeferred = jQuery.Deferred();
                        if(dataConfig['alert_text'] !='No') {
                            var element = jQuery(document).find('input[name="'+field+'"]');
                            var textMsg=dataConfig['alert_text'];
                            element.mask(dataConfig['masked_input'],{
                                incomplete:function(mask){
                                    arrFields[jQuery(this).attr('name')]=mask;
                                    //jQuery(document).unbind('click');
                                    var params = {};
                                    params.text = textMsg;
                                    params.type  = 'error';
                                    // Vtiger_Helper_Js.showMessage(params);
                                    app.helper.showErrorNotification({'message': textMsg});

                                    var fieldVal = this.val();
                                    if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                        this.val('');
                                    } else {
                                        fieldVal = fieldVal.replace(/_/g, "");
                                        //fieldVal = fieldVal.replace('x', '');
                                        //fieldVal=rtrim(fieldVal,'x');
                                        fieldVal = rtrim((fieldVal).trim(), 'x');
                                        fieldVal = rtrim((fieldVal).trim(), '-');
                                        fieldVal = rtrim((fieldVal).trim(), ')');
                                        fieldVal = rtrim((fieldVal).trim(), '(');
                                        fieldVal = rtrim((fieldVal).trim(), ']');
                                        fieldVal = rtrim((fieldVal).trim(), '[');
                                        fieldVal = rtrim((fieldVal).trim(), '}');
                                        fieldVal = rtrim((fieldVal).trim(), '{');
                                        if (mask.indexOf(fieldVal) != -1) {
                                            fieldVal = '';
                                        }
                                        this.val(fieldVal);
                                    }
                                },
                                completed: function () {
                                    jQuery(this).trigger('change');
                                    /*jQuery(document).unbind('click');
                                     var form=jQuery('#EditView');
                                     if(form.length == 0){
                                     var instance=Vtiger_Detail_Js.getInstance();
                                     var currentTd=element.closest('td');
                                     var editElement = jQuery('.edit',currentTd);
                                     //console.log(editElement);
                                     editElement.addClass('hide');
                                     instance.ajaxEditHandling(currentTd);
                                     }*/
                                }
                            });

                        }else {
                            var element = jQuery(document).find('input[name="'+field+'"]');
                            var fieldVal=element.val();
                            element.mask(dataConfig['masked_input'],{
                                incomplete:function(mask){
                                    arrFields[jQuery(this).attr('name')]=mask;
                                    var fieldVal = this.val();
                                    if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                        this.val('');
                                    } else {
                                        fieldVal = fieldVal.replace(/_/g, "");
                                        //fieldVal = fieldVal.replace('x', '');
                                        //fieldVal=rtrim(fieldVal,'x');
                                        fieldVal = rtrim((fieldVal).trim(), 'x');
                                        fieldVal = rtrim((fieldVal).trim(), '-');
                                        fieldVal = rtrim((fieldVal).trim(), ')');
                                        fieldVal = rtrim((fieldVal).trim(), '(');
                                        fieldVal = rtrim((fieldVal).trim(), ']');
                                        fieldVal = rtrim((fieldVal).trim(), '[');
                                        fieldVal = rtrim((fieldVal).trim(), '}');
                                        fieldVal = rtrim((fieldVal).trim(), '{');
                                        if (mask.indexOf(fieldVal) != -1) {
                                            fieldVal = '';
                                        }
                                        this.val(fieldVal);
                                    }
                                },
                                completed: function () {
                                    jQuery(this).trigger('change');
                                }
                            });
                            jQuery(this).val(fieldVal);
                        }
                        jQuery(document).find('input[name="'+field+'"]').focus(function() {
                            this.setSelectionRange(0, 1);
                        });

                        /*element.trigger('blur.mask');*/
                        /*element.onblur(function() {
                         jQuery(this).val(fieldVal);
                         })*/
                    }
                });


                /*jQuery(document).click(function(e){
                 var form = jQuery('#detailView');
                 if(form.length != 0) {
                 jQuery.each(arrFields, function (field, mask) {
                 var fieldVal = form.find('input[name="' + field + '"]').val();
                 if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                 form.find('input[name="' + field + '"]').val('');
                 } else {
                 fieldVal = fieldVal.replace(/_/g, "");
                 //fieldVal = fieldVal.replace('x', '');
                 //fieldVal=rtrim(fieldVal,'x');
                 fieldVal = rtrim((fieldVal).trim(), 'x');
                 fieldVal = rtrim((fieldVal).trim(), '-');
                 fieldVal = rtrim((fieldVal).trim(), ')');
                 fieldVal = rtrim((fieldVal).trim(), '(');
                 fieldVal = rtrim((fieldVal).trim(), ']');
                 fieldVal = rtrim((fieldVal).trim(), '[');
                 fieldVal = rtrim((fieldVal).trim(), '}');
                 fieldVal = rtrim((fieldVal).trim(), '{');
                 if (mask.indexOf(fieldVal) != -1) {
                 fieldVal = '';
                 }
                 form.find('input[name="' + field + '"]').val(fieldVal);
                 }
                 });
                 }
                 });*/
                // Register submit event

                jQuery('#EditView').submit(function (e) {
                    var form = jQuery('#EditView');
                    jQuery.each(arrFields,function(field,mask) {
                        var fieldVal=form.find('input[name="'+field+'"]').val();
                        if(/[a-zA-Z0-9]/.test(fieldVal) == false) {
                            form.find('input[name="'+field+'"]').val('');
                        }else{
                            fieldVal = fieldVal.replace(/_/g,"");
                            //fieldVal = fieldVal.replace('x', '');
                            //fieldVal=rtrim(fieldVal,'x');
                            fieldVal=rtrim((fieldVal).trim(),'x');
                            fieldVal=rtrim((fieldVal).trim(),'-');
                            fieldVal=rtrim((fieldVal).trim(),')');
                            fieldVal=rtrim((fieldVal).trim(),'(');
                            fieldVal=rtrim((fieldVal).trim(),']');
                            fieldVal=rtrim((fieldVal).trim(),'[');
                            fieldVal=rtrim((fieldVal).trim(),'}');
                            fieldVal=rtrim((fieldVal).trim(),'{');
                            if(mask.indexOf(fieldVal) !=-1) {
                                fieldVal='';
                            }
                            form.find('input[name="'+field+'"]').val(fieldVal);
                        }
                    });
                    //e.preventDefault();
                });
                arrFields = {};
            }else{
                // to do
            }
        }
    );
    app.event.on("post.relatedListLoad.click", function() {
        var module = app.getModuleName();
        // Get config fields
        var actionParams = {
            "type":"POST",
            "url":'index.php?module=MaskedInput&action=ActionAjax&mode=getConfigFields',
            "dataType":"json",
            "data" : {
                'source_module':module
            }
        };
        app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        var configs=data;
                        $(document).on('click','.content-area .editAction',function () {
                            for (field in configs) {
                                var dataConfig=configs[field];
                                var aDeferred = jQuery.Deferred();
                                if(dataConfig['alert_text'] !='No') {
                                    var element = jQuery(document).find('input[name="'+field+'"]');
                                    var textMsg=dataConfig['alert_text'];
                                    element.mask(dataConfig['masked_input'],{
                                        incomplete:function(){
                                            //jQuery(document).unbind('click');
                                            // var params = {};
                                            // params.text = textMsg;
                                            // params.type  = 'error';
                                            // Vtiger_Helper_Js.showMessage(params);
                                            app.helper.showErrorNotification({'message': textMsg});


                                            var fieldVal = this.val();
                                            if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                                this.val('');
                                            } else {
                                                fieldVal = fieldVal.replace(/_/g, "");
                                                //fieldVal = fieldVal.replace('x', '');
                                                //fieldVal=rtrim(fieldVal,'x');
                                                fieldVal = rtrim((fieldVal).trim(), 'x');
                                                fieldVal = rtrim((fieldVal).trim(), '-');
                                                fieldVal = rtrim((fieldVal).trim(), ')');
                                                fieldVal = rtrim((fieldVal).trim(), '(');
                                                fieldVal = rtrim((fieldVal).trim(), ']');
                                                fieldVal = rtrim((fieldVal).trim(), '[');
                                                fieldVal = rtrim((fieldVal).trim(), '}');
                                                fieldVal = rtrim((fieldVal).trim(), '{');
                                                if (mask.indexOf(fieldVal) != -1) {
                                                    fieldVal = '';
                                                }
                                                this.val(fieldVal);
                                            }
                                        }
                                        ,
                                        completed: function () {
                                            jQuery(this).trigger('change');
                                        }
                                    });
                                }else {
                                    $(document).on('click','.content-area .editAction',function () {
                                        var element = jQuery(document).find('input[name="'+field+'"]');
                                        element.mask(dataConfig['masked_input'],{
                                            incomplete:function(){
                                                var fieldVal = this.val();
                                                if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                                    this.val('');
                                                } else {
                                                    fieldVal = fieldVal.replace(/_/g, "");
                                                    //fieldVal = fieldVal.replace('x', '');
                                                    //fieldVal=rtrim(fieldVal,'x');
                                                    fieldVal = rtrim((fieldVal).trim(), 'x');
                                                    fieldVal = rtrim((fieldVal).trim(), '-');
                                                    fieldVal = rtrim((fieldVal).trim(), ')');
                                                    fieldVal = rtrim((fieldVal).trim(), '(');
                                                    fieldVal = rtrim((fieldVal).trim(), ']');
                                                    fieldVal = rtrim((fieldVal).trim(), '[');
                                                    fieldVal = rtrim((fieldVal).trim(), '}');
                                                    fieldVal = rtrim((fieldVal).trim(), '{');
                                                    if (mask.indexOf(fieldVal) != -1) {
                                                        fieldVal = '';
                                                    }
                                                    this.val(fieldVal);
                                                }
                                            },
                                            completed: function () {
                                                jQuery(this).trigger('change');
                                            }
                                        });
                                    });
                                }
                            }
                        });
                        
                    }else{
                    }
                }
        );
    });

    jQuery(document).one('mouseenter','form[name="QuickCreate"]', function() {
        var form = jQuery(this);
        var module=app.getModuleName();
        // Get config fields
        var actionParams = {
            "type":"POST",
            "url":'index.php?module=MaskedInput&action=ActionAjax&mode=getConfigFields',
            "dataType":"json",
            "data" : {
                'source_module':module
            }
        };
        var arrFields = new Array();
        app.request.post(actionParams).then(
            function(err,data){
                if(err === null) {
                    var configs=data;
                    for (field in configs) {
                        var dataConfig=configs[field];
                        var aDeferred = jQuery.Deferred();
                        if(dataConfig['alert_text'] !='No') {
                            var element = form.find('input[name="'+field+'"]');
                            var fieldVal=element.val();
                            var textMsg=dataConfig['alert_text'];
                            element.mask(dataConfig['masked_input'],{
                                incomplete:function(mask){
                                    arrFields[jQuery(this).attr('name')]=mask;
                                    //jQuery(document).unbind('click');
                                    var params = {};
                                    params.text = textMsg;
                                    params.type  = 'error';
                                    // Vtiger_Helper_Js.showMessage(params);
                                    app.helper.showErrorNotification({'message': textMsg});


                                    var fieldVal = this.val();
                                    if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                        this.val('');
                                    } else {
                                        fieldVal = fieldVal.replace(/_/g, "");
                                        //fieldVal = fieldVal.replace('x', '');
                                        //fieldVal=rtrim(fieldVal,'x');
                                        fieldVal = rtrim((fieldVal).trim(), 'x');
                                        fieldVal = rtrim((fieldVal).trim(), '-');
                                        fieldVal = rtrim((fieldVal).trim(), ')');
                                        fieldVal = rtrim((fieldVal).trim(), '(');
                                        fieldVal = rtrim((fieldVal).trim(), ']');
                                        fieldVal = rtrim((fieldVal).trim(), '[');
                                        fieldVal = rtrim((fieldVal).trim(), '}');
                                        fieldVal = rtrim((fieldVal).trim(), '{');
                                        if (mask.indexOf(fieldVal) != -1) {
                                            fieldVal = '';
                                        }
                                        this.val(fieldVal);
                                    }

                                },
                                completed: function () {
                                    jQuery(this).trigger('change');
                                }
                            });
                        }else {
                            var element = form.find('input[name="'+field+'"]');
                            var fieldVal=element.val();
                            element.mask(dataConfig['masked_input'],{
                                incomplete:function(mask){
                                    arrFields[jQuery(this).attr('name')]=mask;
                                    var fieldVal = this.val();
                                    if (/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                        this.val('');
                                    } else {
                                        fieldVal = fieldVal.replace(/_/g, "");
                                        //fieldVal = fieldVal.replace('x', '');
                                        //fieldVal=rtrim(fieldVal,'x');
                                        fieldVal = rtrim((fieldVal).trim(), 'x');
                                        fieldVal = rtrim((fieldVal).trim(), '-');
                                        fieldVal = rtrim((fieldVal).trim(), ')');
                                        fieldVal = rtrim((fieldVal).trim(), '(');
                                        fieldVal = rtrim((fieldVal).trim(), ']');
                                        fieldVal = rtrim((fieldVal).trim(), '[');
                                        fieldVal = rtrim((fieldVal).trim(), '}');
                                        fieldVal = rtrim((fieldVal).trim(), '{');
                                        if (mask.indexOf(fieldVal) != -1) {
                                            fieldVal = '';
                                        }
                                        this.val(fieldVal);
                                    }
                                },
                                completed: function () {
                                    jQuery(document).unbind('click');
                                    var form=jQuery('#EditView');
                                    if(form.length == 0){
                                        var instance=Vtiger_Detail_Js.getInstance();
                                        var currentTd=element.closest('td');
                                        var editElement = jQuery('.edit',currentTd);
                                        //console.log(editElement);
                                        editElement.addClass('hide');
                                        instance.ajaxEditHandling(currentTd);
                                    }
                                    jQuery(this).trigger('change');
                                }
                            });
                        }
                        form.find('input[name="'+field+'"]').focus(function() {
                            this.setSelectionRange(0, 1);
                        });
                        /*element.onblur(function() {
                         jQuery(this).val(fieldVal);
                         })*/
                    }
                    // Register submit event
                    jQuery('#QuickCreate').submit(function (e) {
                        var form = jQuery('#QuickCreate');
                        jQuery.each(arrFields,function(field,mask) {
                            var fieldVal=form.find('input[name="'+field+'"]').val();
                            if(/[a-zA-Z0-9]/.test(fieldVal) == false) {
                                form.find('input[name="'+field+'"]').val('');
                            }else{
                                fieldVal = fieldVal.replace(/_/g,"");
                                //fieldVal = fieldVal.replace('x', '');
                                //fieldVal=rtrim(fieldVal,'x');
                                fieldVal=rtrim((fieldVal).trim(),'x');
                                fieldVal=rtrim((fieldVal).trim(),'-');
                                fieldVal=rtrim((fieldVal).trim(),')');
                                fieldVal=rtrim((fieldVal).trim(),'(');
                                fieldVal=rtrim((fieldVal).trim(),']');
                                fieldVal=rtrim((fieldVal).trim(),'[');
                                fieldVal=rtrim((fieldVal).trim(),'}');
                                fieldVal=rtrim((fieldVal).trim(),'{');
                                if(mask.indexOf(fieldVal) !=-1) {
                                    fieldVal='';
                                }
                                form.find('input[name="'+field+'"]').val(fieldVal);
                            }
                        });
                        //e.preventDefault();
                    });
                }else{
                }
            }
        );
    });
}