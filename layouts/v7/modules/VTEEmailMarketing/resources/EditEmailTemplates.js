/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("VTEEmailMarketing_EditEmailTemplates_Js",{},{

    /**
     * Use encodeURIComponent to encode characters outside of the Latin1 range
     * @link http://stackoverflow.com/questions/23223718/failed-to-execute-btoa-on-window-the-string-to-be-encoded-contains-characte
     * @param string
     * @returns {string}
     */
    base64Encode: function (string) {
        if (typeof string === 'undefined' || string === null) {
            return '';
        }

        return window.btoa(unescape(encodeURIComponent(string)));
    },

    /**
     * Use encodeURIComponent to encode characters outside of the Latin1 range
     * @link http://stackoverflow.com/questions/23223718/failed-to-execute-btoa-on-window-the-string-to-be-encoded-contains-characte
     * @param string
     * @returns {string}
     */
    base64Decode: function (string) {
        if (typeof string === 'undefined' || string === null) {
            return '';
        }

        return decodeURIComponent(escape(window.atob(string)));
    },

    /**
     * Function to register event for ckeditor for description field
     */
    registerDesignEmailTemplate:function () {
        var thisInstance = this;
        var idtemplate = jQuery('input[name="record"]').val();
        if(idtemplate == ""){
            thisInstance.clearTemplateEdit();
        }
        //Reload iframe new Design
        jQuery(document).on('click','.btn-reload-iframe',function () {
            var message = app.vtranslate('Your current template will not be saved. Do you wish to continue?');
            app.helper.showConfirmationBox({'message': message}).then(
                function () {
                    thisInstance.clearTemplateEdit();
                    $('#iframe-content').attr('src', 'test/mosaico');
                    var iframe = document.getElementById("iframe-content");
                    iframe.src = iframe.src;
                }
            );
        });

        //copy and Paste template Code into CkEditor
        jQuery(document).on('click','.btn-copy-paste-template',function () {
            var iframe = $('#iframe-content').contents();
            iframe = $(iframe);
            var body = iframe.find('body');
            var saveTemplate = body.find('.saveTemplate');
            saveTemplate.find('.ui-button-text').click();
            var content = jQuery("#iframe-content").contents().find("#output_template");
            //Check render template
            if(content.length > 0){
                app.helper.showProgress("<h4>Please wait.. <br/>Copying and Creating Thumbnail <br/> it might take up to 10 seconds</h4>");
                content = content[0].innerHTML;
                //Click show content edit html
                jQuery(document).find('#cke_38.cke_button_off').off('click');

                CKEDITOR.instances['templatecontent'].setData(content);
                //Click show template type preview
                jQuery(document).find('#cke_38').on('click');
                //get string image base64
                thisInstance.genScreenshot();
                var containerEdit = jQuery('#EditView');
                var keyTemplate =  localStorage.getItem('editting');
                var metadata = thisInstance.base64Encode(localStorage.getItem('metadata-'+keyTemplate));
                var template = thisInstance.base64Encode(localStorage.getItem('template-'+keyTemplate));
                var thumbnailUrl = localStorage.getItem('thumbnailUrl-'+keyTemplate);
                containerEdit.find('[name="keytemplate"]').attr("value",keyTemplate);
                containerEdit.find('[name="metadata"]').text(metadata);
                containerEdit.find('[name="template"]').text(template);
                containerEdit.find('[name="thumbnailUrl"]').text(thumbnailUrl);
            }else{
                thisInstance.registerRenderTemplate(true);
            }
        });

        jQuery(document).on('click','.saveButton,.cancelLink',function (e) {
            thisInstance.clearTemplateEdit();
        });

        jQuery(document).on('click','.btn-emailtemplate-builder',function () {
            app.helper.showProgress('<h4>Please wait.. <br/>Loading Mosaico ... <br/> it might take up to 15 seconds</h4>');
            var keyTemplate = localStorage.getItem('editting');
            if(keyTemplate != null && localStorage.getItem('template-'+keyTemplate)){
                $('#iframe-content').attr('src', 'test/mosaico/editor.html#'+keyTemplate);
            }else{
                $('#iframe-content').attr('src', 'test/mosaico');
            }
            var iframe = document.getElementById("iframe-content");
            iframe.src = iframe.src;
            thisInstance.registerRenderTemplate();
            app.helper.hideProgress();

        });
    },

    clearTemplateEdit:function () {
        var keyTemplate = localStorage.getItem('editting');
        localStorage.removeItem('edits');
        localStorage.removeItem('editting');
        localStorage.removeItem('metadata-' + keyTemplate);
        localStorage.removeItem('template-' + keyTemplate);
        localStorage.removeItem('thumbnail-' + keyTemplate);
        localStorage.removeItem('thumbnailUrl-' + keyTemplate);
    },

    registerLoadTemplateMosaico:function () {
        app.helper.showProgress('<h4>Please wait.. <br/>Loading template<br/> it might take up to 10 seconds</h4>');
        var thisIntances = this;
        var idtemplate = jQuery('input[name="record"]').val();
        if(idtemplate != ""){
            var params = {
                'module': 'VTEEmailMarketing',
                'action': 'ActionAjax',
                'mode': 'getKeyMosaicoTemplateEdit',
                'idTemplate': idtemplate
            };
            AppConnector.request(params).then(function (data) {
                if (data.success) {
                    var keytemplate = data.result.keytemplate;
                    if(keytemplate != null && keytemplate != ""){
                        var thumbnailUrl = "";
                        if(data.result.thumbnailUrl != undefined){
                            thumbnailUrl = data.result.thumbnailUrl;
                        }
                        localStorage.setItem('metadata-'+keytemplate,thisIntances.base64Decode(data.result.metadata));
                        localStorage.setItem('template-'+keytemplate,thisIntances.base64Decode(data.result.template));
                        localStorage.setItem('editting',keytemplate);
                        localStorage.setItem('thumbnailUrl-'+keytemplate,thumbnailUrl);
                        localStorage.setItem('edits','["'+keytemplate+'"]');
                    }
                    app.helper.hideProgress();
                }
            });
        }else{
            app.helper.hideProgress();
        }
    },

    genScreenshot:function () {
        html2canvas(jQuery("#iframe-content").contents().find("#output_template > center"), {
            onrendered: function(canvas) {
                var thumbnail = canvas.toDataURL('image/jpeg', 0.5);
                var bast64Image = jQuery('#EditView').find('[name="base64image"]');
                bast64Image.text(thumbnail);
                $(function() {
                    checkImageFind();
                    function checkImageFind() {
                        myInterVal = setInterval(checkImageFind,1000);
                        if(bast64Image.text().length > 0){
                            app.helper.hideProgress();
                            clearInterval(myInterVal);
                        }
                    }
                });
                jQuery('.btn-close').trigger('click');
            },
            useCORS: true
        });
    },

    registerRenderTemplate:function (trigger) {
        $('#iframe-content').load(function(){
            var iframe = $('#iframe-content').contents();
            iframe = $(iframe);
            var body = iframe.find('body');
            setTimeout(function () {
                body.find('.saveTemplate').click(function(){
                    app.helper.showProgress();
                    app.helper.hideProgress();
                });

                body.find('.renderTemplate').click(function(){
                    app.helper.showProgress("<h4>Please wait.. <br/>Template is being rendered <br/> it might take up to 60 seconds</h4>");
                });
            }, 2000);
            var checkLoadClick = localStorage.getItem('editting');
            if(checkLoadClick != undefined){
                $('.btn-copy-paste-template').trigger("click");
                app.helper.hideProgress();
            }
        });

        if(trigger){
            var iframe = $('#iframe-content').contents();
            iframe = $(iframe);
            var body = iframe.find('body');
            body.find('.renderTemplate').trigger("click");
        }
    },

    registerEvents : function() {
        this.registerDesignEmailTemplate();
        this.registerLoadTemplateMosaico();
    }
});

// Extend Js from EmailTemplates_Edit_Js
jQuery(document).ready(function() {
    var instance = new EmailTemplates_Edit_Js();
    instance.registerEvents();

    //tooltip
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

});