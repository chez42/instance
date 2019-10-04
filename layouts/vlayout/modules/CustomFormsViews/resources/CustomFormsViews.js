/* ********************************************************************************
 * The content of this file is subject to the Custom Forms & Views ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
jQuery.Class("CustomFormsViews_Js",{
    editInstance:false,
    getInstance: function(){
        if(CustomFormsViews_Js.editInstance == false) {
            var instance = new CustomFormsViews_Js();
            CustomFormsViews_Js.editInstance = instance;
            return instance;
        }
        return CustomFormsViews_Js.editInstance;
    },
    getConfig: function() {
        var sPageURL = window.location.search.substring(1);
        if(sPageURL.indexOf('&relatedModule=') != -1) {
            var relatedModule='';
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++)
            {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == 'relatedModule')
                {
                    relatedModule = sParameterName[1];
                }
            }
            if(relatedModule !='') {
                var url = 'index.php?module=CustomFormsViews&action=ActionAjax&mode=getModuleConfig';
                var actionParams = {
                    "type": "POST",
                    "url": url,
                    "dataType": "json",
                    "data": {
                        'current_module': app.getModuleName(),
                        'source_module': relatedModule,
                    }
                };
                var btnGroup=jQuery(".relatedHeader").find('.btn-group button[name="addButton"]').closest('.btn-group');
                btnGroup.hide();

                AppConnector.request(actionParams).then(
                    function (data) {
                        var formsList = data.result.formlist;
                        var relationFieldName = data.result.fieldname;
                        if (!jQuery.isEmptyObject(formsList)) {
                            var currentBtn = jQuery(".relatedHeader").find('.btn-group button[name="addButton"]');
                            var addText = currentBtn.text();

                            var customButtons = '<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="caret"></i> &nbsp;<strong>' + addText + '</strong></button><ul class="dropdown-menu pull-right">';
                            var parentModule = app.getModuleName();
                            var recordId = app.getRecordId();
                            var fieldName = currentBtn.data('name');
                            jQuery.each(formsList, function (i, v) {
                                customButtons += '<li class="customFormView" data-id="' + i + '"><a href="' + (currentBtn.data('url').replace(relatedModule, 'CustomFormsViews')) + '&currentModule=' + relatedModule + '&customviewid=' + i + '&'+relationFieldName+'='+ app.getRecordId() + '"\'>' + v + '</a></li>';
                            });
                            customButtons += '</ul>';

                            btnGroup.html(customButtons);
                        }
                        btnGroup.show();
                    }
                );
            }
        }else if(sPageURL.indexOf('module=') != -1 && sPageURL.indexOf('view=List') != -1){
            var relatedModule='';
            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++)
            {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == 'module')
                {
                    relatedModule = sParameterName[1];
                }
            }

            var url = 'index.php?module=CustomFormsViews&action=ActionAjax&mode=getModuleConfig';
            var actionParams = {
                "type": "POST",
                "url": url,
                "dataType": "json",
                "data": {
                    'source_module': relatedModule
                }
            };
            var btnGroup=jQuery(".listViewActionsDiv").find('.btn-group .addButton').closest('.btn-group');
            btnGroup.hide();

            AppConnector.request(actionParams).then(
                function (data) {
                    var formsList = data.result.formlist;
                    if (!jQuery.isEmptyObject(formsList)) {
                        var currentBtn = btnGroup.find('.addButton');
                        var addText = currentBtn.text();

                        var customButtons = '<button class="btn dropdown-toggle" data-toggle="dropdown"><i class="caret"></i>&nbsp;<strong>' + addText + '</strong></button><ul class="dropdown-menu pull-right">';
                        jQuery.each(formsList, function (i, v) {
                            customButtons += '<li class="customFormView" data-id="' + i + '"><a onclick=\'window.location.href="index.php?module=CustomFormsViews&view=Edit&currentModule=' + relatedModule + '&customviewid=' + i + '"\'>' + v + '</a></li>';
                        });
                        customButtons += '</ul>';

                        btnGroup.html(customButtons);
                    }
                    btnGroup.show();
                }
            );
        }
    },
    setDetailLink: function() {
        var recordList=[];
        jQuery(document).find("tr.listViewEntries").each(function(e) {
            var element=jQuery(this);
            element.find('td:last .actionImages a:first').bind('click', false);
            element.find('td:last .actionImages a:eq(1)').bind('click', false);
            var record=element.data('id');
            recordList.push(record)
        });
        if(recordList.length>0) {
            // Get view id of each record
            var url ='index.php?module=CustomFormsViews&action=ActionAjax&mode=getRecordView';
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"json",
                "data" : {
                    'record_list':recordList
                }
            };

            AppConnector.request(actionParams).then(
                function(data) {
                    if(data.result != null) {
                        var result=data.result.views;
                        var default_record_view=data.result.default_record_view;
                        jQuery(document).find("tr.listViewEntries").each(function(e) {
                            var element=jQuery(this);
                            var record=element.data('id');
                            if(typeof result[record] != 'undefined') {
                                var detailLink=element.find('td:last .actionImages a:first').attr('href');
                                var recordurl=element.data('recordurl');

                                var alinks=element.find('a[href="'+recordurl+'"]');

                                detailLink=detailLink.replace('index.php?','');
                                var currentModule='';
                                var sURLVariables = detailLink.split('&');
                                for (var i = 0; i < sURLVariables.length; i++)
                                {
                                    var sParameterName = sURLVariables[i].split('=');
                                    if (sParameterName[0] == 'module')
                                    {
                                        currentModule = sParameterName[1];
                                    }
                                }
                                detailLink=detailLink.replace(currentModule,'CustomFormsViews')+'&currentModule='+currentModule+'&customviewid='+result[record];
                                element.find('td:last .actionImages a:first').attr('href','index.php?'+detailLink);
                                element.find('td:last .actionImages a:eq(1)').attr('href','index.php?module=CustomFormsViews&view=Edit&record='+record+'&currentModule=' + currentModule + '&customviewid=' + result[record]);
                                if(default_record_view == 'Detail') {
                                    element.attr('data-recordurl','index.php?'+detailLink);
                                    element.data('recordurl','index.php?'+detailLink);
                                    alinks.each(function(e) {
                                        jQuery(this).attr('href','index.php?'+detailLink);
                                    });
                                }
                            }
                        });
                    }
                }
            );
        }
        jQuery(document).find("tr.listViewEntries").each(function(e) {
            var element=jQuery(this);
            element.find('td:last .actionImages a:first').unbind('click', false);
            element.find('td:last .actionImages a:eq(1)').unbind('click', false);

        });
    },
    setRelatedLink:function() {
        var recordList2=[];
        var element = jQuery(document).find('li[data-link-key="LBL_RECORD_DETAILS"]');
        var recordId=jQuery(document).find('#recordId').val();

        if(recordId !='' && typeof recordId != 'undefined') {
            recordList2.push(recordId);
            // Get view id of each record
            var url ='index.php?module=CustomFormsViews&action=ActionAjax&mode=getRecordView';
            var actionParams = {
                "type":"POST",
                "url":url,
                "dataType":"json",
                "data" : {
                    'record_list':recordList2
                }
            };
            AppConnector.request(actionParams).then(
                function(data) {
                    if(data.result != null) {
                        var result=data.result.views;
                        var default_record_view=data.result.default_record_view;
                        var detailLink=element.data('url');
                        detailLink=detailLink.replace('index.php?','');
                        var currentModule='';
                        var oldCurrentModule='';
                        var sURLVariables = detailLink.split('&');
                        for (var i = 0; i < sURLVariables.length; i++)
                        {
                            var sParameterName = sURLVariables[i].split('=');
                            if (sParameterName[0] == 'module')
                            {
                                currentModule = sParameterName[1];
                            }else if(sParameterName[0] == 'currentModule') {
                                oldCurrentModule=sParameterName[1];
                            }
                        }
                        if(oldCurrentModule == '') {
                            if (typeof result[recordId] != 'undefined') {
                                var sDetailPageURL = window.location.search.substring(1);
                                if(sDetailPageURL.indexOf('module=CustomFormsViews') == -1) {
                                    var progressIndicatorElement = jQuery.progressIndicator({
                                        'position': 'html',
                                        'blockInfo': {
                                            'enabled': true
                                        },
                                        'message': 'Opening Custom View ...'
                                    });
                                }
                                oldCurrentModule=currentModule;
                                detailLink = detailLink.replace(currentModule, 'CustomFormsViews') + '&currentModule=' + currentModule + '&customviewid=' + result[recordId];
                                element.attr('data-url', 'index.php?' + detailLink);
                                element.data('url', 'index.php?' + detailLink);
                                jQuery(document).find('input[data-currentviewlabel="LBL_COMPLETE_DETAILS"]').attr('data-full-url', 'index.php?' + detailLink);
                                jQuery(document).find('input[data-currentviewlabel="LBL_COMPLETE_DETAILS"]').data('full-url', 'index.php?' + detailLink);
                                if(sDetailPageURL.indexOf('module=CustomFormsViews') == -1) {
                                    element.find('a').trigger('click');
                                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                                }
                                jQuery(document).find('.detailViewButtoncontainer .btn-group .btn:first').click(function(e) {
                                    e.preventDefault();
                                    window.location.href='index.php?module=CustomFormsViews&view=Edit&record='+recordId+'&currentModule=' + oldCurrentModule + '&customviewid=' + result[recordId];
                                });
                            }
                        }else{
                            jQuery(document).find('.detailViewButtoncontainer .btn-group .btn:first').click(function(e) {
                                e.preventDefault();
                                window.location.href='index.php?module=CustomFormsViews&view=Edit&record='+recordId+'&currentModule=' + oldCurrentModule + '&customviewid=' + result[recordId];
                            });
                        }
                    }
                }
            );
        }
    }
},{

});

//document.location.href = "http://localhost/test/vtiger610/index.php?module=Potentials&view=Detail&record=45";
jQuery(document).ready(function(){
    CustomFormsViews_Js.getConfig();
    CustomFormsViews_Js.setDetailLink();
    CustomFormsViews_Js.setRelatedLink();
    app.listenPostAjaxReady(function() {
        CustomFormsViews_Js.getConfig();
        CustomFormsViews_Js.setDetailLink();
        CustomFormsViews_Js.setRelatedLink();
    });
});