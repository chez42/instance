/* ********************************************************************************
 * The content of this file is subject to the Custom Forms & Views ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
 
 if (typeof CustomFormsViews_Js == "undefined"){
Vtiger.Class("CustomFormsViews_Js",{
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
                var btnGroup=jQuery(".relatedHeader").find('button.addButton').closest('.btn-group:last-child');

                app.helper.showProgress();
                app.request.post(actionParams).then(
                    function(err,data){
                        if(err === null) {
                            var formsList = data.formlist;
                            var relationFieldName = data.fieldname;
                            if (!jQuery.isEmptyObject(formsList)&& formsList.hasOwnProperty('result') != true) {
                                // debugger;
                                btnGroup.hide();
                                var currentBtn = btnGroup.find('button.addButton');
                                var addText = currentBtn.text();
                                var btnID = currentBtn.attr('id');

                                var customButtons = '<button style="margin: 0; background: #f3f3f3" id="'+btnID+'" type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="caret"></span>' + addText + '&nbsp;</button><ul class="dropdown-menu">';
                                var parentModule = app.getModuleName();
                                var recordId = app.getRecordId();
                                var fieldName = jQuery(".relatedHeader").find('button.addButton').data('name');

                                //get related tab label
                                var detailViewInstance = Vtiger_Detail_Js.getInstance();
                                var selectedTabElement = detailViewInstance.getSelectedTab();
                                var tabLable = selectedTabElement.data('label-key');
                                var relationId = selectedTabElement.data('relation-id');    

                                jQuery.each(formsList, function (i, v) {
                                    customButtons += '<li class="customFormView" data-id="' + i + '"><a onclick=\'window.location.href="index.php?module=CustomFormsViews&view=Edit&currentModule=' + relatedModule + '&customviewid=' + i + '&sourceModule=' + parentModule + '&sourceRecord=' + recordId + '&relationOperation=true&' + fieldName + '=' + recordId + '&returnmode=showRelatedList&returntab_label='+tabLable+'&returnrecord='+recordId+'&returnmodule='+parentModule+'&returnview=Detail&returnrelatedModuleName='+relatedModule+'&returnrelationId='+relationId+'"\'>' + v + '</a></li>';
                                });
                                customButtons += '</ul>';
                                btnGroup.html(customButtons);
                                btnGroup.show();
                            }
                        }
                    }
                );
                app.helper.hideProgress();
            }
        }else if(sPageURL.indexOf('module=') != -1 && (sPageURL.indexOf('view=List') != -1 || sPageURL.indexOf('view=Detail') != -1)|| (sPageURL === '') || (sPageURL === null)){
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
            if(relatedModule=='' || relatedModule=='CustomFormsViews'){
                relatedModule= _META.module;
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
            var btnGroup=jQuery(".module-action-bar").find('[id*="_basicAction_LBL_ADD_RECORD"]').closest('li');
            
            var btnGroup=jQuery("#appnav").find('[id*="_basicAction_LBL_ADD_RECORD"]').parent();
   
            if (btnGroup.length > 0){
	          
                app.helper.showProgress();
                app.request.post(actionParams).then(
                    function(err,data){
                        if(err === null) {
                            var formsList = data.formlist;
                            if (!jQuery.isEmptyObject(formsList) && formsList.hasOwnProperty('result') != true) {
                                btnGroup.hide();
                                var currentBtn = btnGroup.find('[id*="_basicAction_LBL_ADD_RECORD"]');
                                var addText = currentBtn.find("span").text();
                                var btnID = currentBtn.attr('id');
								currentBtn.remove();
                                var customButtons = '<div class="dropdown" style="float:left;"><button id="'+btnID+'" type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">add</i>' + addText + '</button><ul class="dropdown-menu">';
                                jQuery.each(formsList, function (i, v) {
                                    customButtons += '<li class="customFormView" data-id="' + i + '"><a onclick=\'window.location.href="index.php?module=CustomFormsViews&view=Edit&currentModule=' + relatedModule + '&customviewid=' + i + '"\'>' + v + '</a></li>';
                                });
                                customButtons += '</ul></div>';
                                btnGroup.html(customButtons+btnGroup.html());
                                btnGroup.show();
                            }
                        }
                    }
                );
                app.helper.hideProgress();
            }
        }
    },
    setDetailLink: function() {
        var recordList=[];
        jQuery(document).find("tr.listViewEntries").each(function(e) {
            var element=jQuery(this);
            /*element.find('td:last .actionImages a:first').bind('click', false);
            element.find('td:last .actionImages a:eq(1)').bind('click', false);*/
            //Enable comment do not click edit and detail view on relatedlist
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

            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if(data) {
                            var result=data.views;
                            var default_record_view=data.default_record_view;
                            jQuery(document).find("tr.listViewEntries").each(function(e) {
                                var element=jQuery(this);
                                var record=element.data('id');
                                if(typeof result != 'undefined' && typeof result[record] != 'undefined') {
                                    var detailLink=element.find('.listViewEntryValue:first a').attr('href');
                                    if (typeof detailLink != 'undefined') {
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
                                        element.find("td.listViewRecordActions li a[href*='view=Detail']").attr('href','index.php?'+detailLink);
                                        element.find("td.listViewRecordActions li a[name*='editlink']").attr('href','index.php?module=CustomFormsViews&view=Edit&record='+record+'&currentModule=' + currentModule + '&customviewid=' + result[record]);
                                        element.find("td.listViewRecordActions li a[name*='editlink']").attr('data-url','index.php?module=CustomFormsViews&view=Edit&record='+record+'&currentModule=' + currentModule + '&customviewid=' + result[record]);
                                        if(default_record_view == 'Detail') {
                                            element.attr('data-recordurl','index.php?'+detailLink);
                                            element.data('recordurl','index.php?'+detailLink);
                                            alinks.each(function(e) {
                                                jQuery(this).attr('href','index.php?'+detailLink);
                                            });
                                        }
                                    }
                                }
                            });
                        }
                    }
                }
            );
        }
        jQuery(document).find("tr.listViewEntries").each(function(e) {
            var element=jQuery(this);
            element.find("td.listViewRecordActions li a[href*='view=Detail']").unbind('click', false);
            element.find("td.listViewRecordActions li a[name*='editlink']").unbind('click', false);

        });
    },
    setRelatedLink:function() {
        var recordList2=[];
        var element = jQuery(document).find('li[data-link-key="LBL_RECORD_DETAILS"]');
        var recordId=jQuery(document).find('#recordId').val();

        if(element.length==0) return;

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
            app.request.post(actionParams).then(
                function(err,data){
                    if(err === null) {
                        if(data != null) {
                            var result=data.views;
                            var default_record_view=data.default_record_view;
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
                                if (typeof result != 'undefined') {
                                    if (typeof result[recordId] != 'undefined'){
                                        var sDetailPageURL = window.location.search.substring(1);
                                        if(sDetailPageURL.indexOf('module=CustomFormsViews') == -1) {
                                            app.helper.showProgress();
                                        }
                                        oldCurrentModule=currentModule;
                                        detailLink = detailLink.replace(currentModule, 'CustomFormsViews') + '&currentModule=' + currentModule + '&customviewid=' + result[recordId];
                                        element.attr('data-url', 'index.php?' + detailLink);
                                        element.data('url', 'index.php?' + detailLink);
                                        jQuery(document).find('input[data-currentviewlabel="LBL_COMPLETE_DETAILS"]').attr('data-full-url', 'index.php?' + detailLink);
                                        jQuery(document).find('input[data-currentviewlabel="LBL_COMPLETE_DETAILS"]').data('full-url', 'index.php?' + detailLink);
                                        if(sDetailPageURL.indexOf('module=CustomFormsViews') == -1) {
                                            element.find('a').trigger('click');
                                            app.helper.hideProgress();
                                        }
                                        jQuery("[id*='_detailView_basicAction_LBL_EDIT']").click(function(e) {
                                            e.preventDefault();
                                            window.location.href='index.php?module=CustomFormsViews&view=Edit&record='+recordId+'&currentModule=' + oldCurrentModule + '&customviewid=' + result[recordId];
                                        });
                                    }
                                }
                            }else{
                                jQuery("[id*='_detailView_basicAction_LBL_EDIT']").click(function(e) {
                                    e.preventDefault();
                                    window.location.href='index.php?module=CustomFormsViews&view=Edit&record='+recordId+'&currentModule=' + oldCurrentModule + '&customviewid=' + result[recordId];
                                });
                            }
                        }
                    }
                }
            );
        }
    },
    getQueryParams:function(qs) {
        if(typeof(qs) != 'undefined' ){
            qs = qs.toString().split('+').join(' ');
            var params = {},
                tokens,
                re = /[?&]?([^=]+)=([^&]*)/g;
            while (tokens = re.exec(qs)) {
                params[decodeURIComponent(tokens[1])] = decodeURIComponent(tokens[2]);
            }
            return params;
        }
    },
},{

});

//document.location.href = "http://localhost/test/vtiger610/index.php?module=Potentials&view=Detail&record=45";
jQuery(document).ready(function(){
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('CustomFormsViews')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    var currentModule = app.getModuleName();
    var illegalModules = ['ControlLayoutFields', 'CustomFormsViews', 'QuotingTool', 'SummaryReport'];
    if (illegalModules.indexOf(currentModule) > -1){
        return;
    }
    
    CustomFormsViews_Js.getConfig();
    CustomFormsViews_Js.setDetailLink();
    CustomFormsViews_Js.setRelatedLink();
    app.event.on("post.relatedListLoad.click", function() {
        CustomFormsViews_Js.getConfig();
        CustomFormsViews_Js.setDetailLink();
        CustomFormsViews_Js.setRelatedLink();
    });

    jQuery( document ).ajaxComplete(function(event, xhr, settings) {
        // Only load when loadHeaderScript=1 BEGIN #241208
        if (typeof VTECheckLoadHeaderScript == 'function') {
            if (!VTECheckLoadHeaderScript('CustomFormsViews')) {
                return;
            }
        }
        // Only load when loadHeaderScript=1 END #241208

        if(typeof settings.data == 'string'){
            var url = settings.data;
        }
        if(typeof url == 'undefined' && settings.url) url = settings.url;
        url = url.replace('index.php?', '');
        var top_url = window.location.href.split('?');
        var array_url = CustomFormsViews_Js.getQueryParams(top_url[1]);
        if(typeof array_url == 'undefined') return false;
        var other_url = CustomFormsViews_Js.getQueryParams(url);
        if (other_url.displayMode == "overlay" && other_url.mode == "showDetailViewByMode"){
            var params = {
                module: "CustomFormsViews",
                view: "Detail",
                record: other_url.record,
                mode: "showDetailViewByMode",
                requestMode: other_url.requestMode,
                currentModule: other_url.module,
                customviewid: -1
            };
            app.request.post({'data': params}).then(function (err, data) {
                if (err === null) {
                    app.helper.hideProgress();
                    if ($(data).find("input").length > 1 && other_url.module != 'SignedRecord'){
                        $(".modal-content .detailViewContainer").html(data);
                        $(".overlayDetail .modal-content").css({"height": "auto"});
                        $(".mCustomScrollbar").mCustomScrollbar();
                    }
                } else {
                    app.helper.hideProgress();
                }
            });
        }
    });
});
 }