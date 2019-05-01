Vtiger.Class("SummaryWidgets_Js",{
        ___init: function (url) {
            var sPageURL = window.location.search.substring(1);
            var targetModule = '';
            var targetView = '';
            var sourceModule = '';
            var mode = '';

            var sURLVariables = sPageURL.split('&');
            for (var i = 0; i < sURLVariables.length; i++) {
                var sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] == 'module') {
                    targetModule = sParameterName[1];
                }
                else if (sParameterName[0] == 'view') {
                    targetView = sParameterName[1];
                }
                else if (sParameterName[0] == 'sourceModule') {
                    sourceModule = sParameterName[1];
                }
                else if (sParameterName[0] == 'mode') {
                    mode = sParameterName[1];
                }

            }
            var viewMode = '';
            if(jQuery('#detailView [name="viewMode"]').length == 0){
                var viewMode = 'full';
            }
            /*if (targetView == 'Detail') */{
                var instance = new SummaryWidgets_Js();
                instance.registerEvents();
            }
        },
        editInstance:false,
        getInstance: function(){
            if(SummaryWidgets_Js.editInstance == false){
                var instance = new SummaryWidgets_Js();
                SummaryWidgets_Js.editInstance = instance;
                return instance;
            }
            return SummaryWidgets_Js.editInstance;
        },
        registerEventForTextAreaFields : function(parentElement) {
            if(typeof parentElement == 'undefined') {
                parentElement = jQuery('body');
            }
            parentElement = jQuery(parentElement);

            if(parentElement.is('textarea')){
                var element = parentElement;
            }else{
                var element = jQuery('textarea', parentElement);
            }
            if(element.length === 0){
                return;
            }

        },
        loadWidget: function (widgetContainer) {
            var thisInstance = this;
            var aDeferred = jQuery.Deferred();
            var contentHeader = jQuery('.widget_header', widgetContainer);
            var contentContainer = jQuery('.widget_contents', widgetContainer);
            var urlParams = widgetContainer.data('url');
            var relatedModuleName = contentHeader.find('[name="relatedModule"]').val();

            urlParams = 'index.php?' + urlParams;
            var whereCondition = SummaryWidgets_Js.getFilterData(widgetContainer);

            if (jQuery('input[name="columnslist"]', widgetContainer).length > 0) {
                var list = jQuery('input[name="columnslist"]', widgetContainer).val();
                var fieldnamelist = '';
                if (list != '')
                    fieldnamelist = JSON.parse(list);
            }
            if(jQuery('input[name="sortby"]',widgetContainer).length>0){
                var sortby = jQuery('input[name="sortby"]',widgetContainer).val();
                var sorttype = jQuery('input[name="sorttype"]',widgetContainer).val();
            }

            if (typeof fieldnamelist != 'undefined'){
                var params = {
                    type: 'GET',
                    url: urlParams,
                    data: {whereCondition: whereCondition, fieldList: fieldnamelist,sortby:sortby,sorttype:sorttype}
                };
            }else {
                var params = {
                    type: 'GET',
                    url: urlParams,
                };
            }
            app.request.post(params).then(
                function(err, data) {
                    app.helper.hideProgress();
                    if(err === null) {
                        contentContainer.html(data);
                        var container = thisInstance.getInstance();
                        var textAreaElement = jQuery('.commentcontent', container);
                        thisInstance.registerEventForTextAreaFields(textAreaElement);
                        aDeferred.resolve(params);
                    }
                },
                function(error) {
                    app.helper.hideProgress();
                }
            );

            return aDeferred.promise();
        },
        appendWidgets:function(module,record){
            var thisInstance = this;
            var url='index.php?module=VTEWidgets&sourcemodule='+module+'&action=SummaryWidgetContent&mode=getCustomWidgets&record='+record;
            var params = {
                'type' : 'GET',
                'data' : url
            };
            // var instance = Vtiger_Detail_Js.getInstance();
            app.request.get({'url':url}).then(
                function(err, data) {
                    app.helper.hideProgress();
                    if(data != null) {
                       // if (res == undefined) return;
                        var form = jQuery('#detailView');
                        if (form.length <= 0) return;

                        var summaryviewContainer = form.find('div.left-block');
                        if (summaryviewContainer.find('.summaryView').length > 0) {
                            summaryviewContainer.append("<div id='appendwidget_7'> </div>");
                            jQuery('#appendwidget_7').html(data.span7);
                        }
                        var summaryviewContainer = form.find('div.middle-block');
                        if (summaryviewContainer.find('.summaryWidgetContainer').length > 0) {
                            summaryviewContainer.append("<div id='appendwidget_5'> </div>");
                            jQuery('#appendwidget_5').html(data.span5);
                        }

                        var summaryviewContainer = form.find('div.right-block');
                        if (summaryviewContainer.find('.summaryWidgetContainer').length > 0) {
                            summaryviewContainer.append("<div id='appendwidget_3'> </div>");
                            jQuery('#appendwidget_3').html(data.span3);
                        }else {
                            var summaryviewContainer = form.find('div.middle-block');
                            if (summaryviewContainer.find('.summaryWidgetContainer').length > 0) {
                                summaryviewContainer.append("<div id='appendwidget_3'> </div>");
                                jQuery('#appendwidget_3').html(data.span3);
                            }
                        }
                        var widgetList = jQuery('[class^="customwidgetContainer_"]');
                        widgetList.each(function (index, widgetContainerELement) {
                            var widgetContainer = jQuery(widgetContainerELement);
                            SummaryWidgets_Js.loadWidget(widgetContainer);
                        });
                        //#661301
                        var ele = $('div.summaryWidgetContainer select.filterField');
                        vtUtils.showSelect2ElementView(ele);
                        //#661301 end
                    }
                },
                function(error) {
                    app.helper.hideProgress();
                }
            );
        },

        getFilterData : function(summaryWidgetContainer){
            var whereCondition={};
            var name='';
            //#661301
            //summaryWidgetContainer.find('.widget_header .filterField').each(function (index, domElement) {
            summaryWidgetContainer.find('.widget_header select.filterField').each(function (index, domElement) {
                //#661301 end
                var filterElement=jQuery(domElement);
                var fieldInfo = filterElement.data('fieldinfo');
                // var fieldName = filterElement.attr('name');
                var fieldName = filterElement.data('filter');
                var fieldLabel = fieldInfo.label;
                var filtervalue='';

                if (fieldInfo.type == 'checkbox'){
                    if (filterElement.prop('checked')) {
                        filtervalue= filterElement.data('on-val');
                    } else {
                        filtervalue = filterElement.data('off-val');
                    }
                }else

                    filtervalue = filterElement.val();
                if(filtervalue == 'Select '+fieldLabel)   {
                    filtervalue='';
                    return;
                }
                filtervalue = filtervalue.trim();
                {
                    whereCondition[fieldName] = filtervalue;
                }
            });

            return whereCondition;
        }

    },
    {
        toggleRollupComments : function (e) {
            e.stopPropagation();
            e.preventDefault();
            var self = this;
            var currentTarget = jQuery(e.currentTarget);
            var moduleName = currentTarget.attr('module');
            var recordId = currentTarget.attr('record');
            var rollupId = currentTarget.attr('rollupid');
            var rollupstatus = currentTarget.attr('rollup-status');
            var viewtype = currentTarget.data('view');
            var startindex = parseInt(currentTarget.attr('startindex'));
            var contents, url, params;
            var summaryWidgetContainer = currentTarget.closest('.summaryWidgetContainer');
            var widgetHeaderContainer = summaryWidgetContainer.find('.widget_header');
            var relatedlimit = widgetHeaderContainer.find('[name="relatedlimit"]').val();
            if(rollupstatus == 0) {
                url = 'index.php?module=VTEWidgets&view=SummaryWidget&record='+
                    recordId+'&mode=showCommentsWidget'+'&rollupid='+rollupId
                    +'&rollup_status=1&parent='+moduleName+'&sourcemodule='+moduleName+'&rollup-toggle=1&limit='+relatedlimit;
                contents = jQuery('div[data-type="Comments"] div.widget_contents');

                params = {
                    'type' : 'GET',
                    'url' : url
                };

                app.request.get(params).then(function(err, data){

                    app.helper.hideProgress();
                    contents.html(data);
                    self.registerRollupCommentsSwitchEvent();
                    jQuery('#rollupcomments').bootstrapSwitch('state', true, true);
                });
            }else {
                url = 'index.php?module=VTEWidgets&view=SummaryWidget&record='+
                    recordId+'&mode=showCommentsWidget'+'&rollupid='+rollupId
                    +'&rollup_status=0&parent='+moduleName+'&sourcemodule='+moduleName+'&rollup-toggle=1&limit='+relatedlimit;
                contents = jQuery('div[data-type="Comments"] div.widget_contents');
                params = {
                    'type' : 'GET',
                    'url' : url
                };
                app.request.get(params).then(function(err, data){

                    app.helper.hideProgress();
                    contents.html(data);
                    self.registerRollupCommentsSwitchEvent();
                    jQuery('#rollupcomments').bootstrapSwitch('state', false, true);
                });
            }
        },
        registerRollupCommentsSwitchEvent : function() {
            var self = this;
            var commentsRelatedContainer = jQuery('.commentsRelatedContainer');
            if(jQuery('#widrollupcomments').length > 0 && commentsRelatedContainer.length) {
                app.helper.hideProgress();
                commentsRelatedContainer.off('switchChange.bootstrapSwitch')
                    .on('switchChange.bootstrapSwitch','#widrollupcomments', function(e){
                        app.helper.showProgress();
                        self.toggleRollupComments(e);
                    });
                if(jQuery('#widrollupcomments').attr('rollup-status') == 1) {
                    jQuery('#widrollupcomments').bootstrapSwitch('state', true, true);

                }else{
                    jQuery('#widrollupcomments').bootstrapSwitch('state', false, true);
                }
            }
        },
        detailViewContentHolder : false,
        getContentHolder : function() {
            if(this.detailViewContentHolder == false) {
                this.detailViewContentHolder = jQuery('div.details');
            }
            return this.detailViewContentHolder;
        },
        registerEvents : function() {
            this._super();
            var detailContentsHolder = this.getContentHolder();
            var self = this;
            detailContentsHolder.on('click', '#widrollupcomments', function (e) {
                e.stopPropagation();
                e.preventDefault();
                detailContentsHolder.on('switchChange.bootstrapSwitch', '#widrollupcomments', function(e){
                    app.helper.showProgress();
                    self.toggleRollupComments(e);
                });

                if(jQuery('#widrollupcomments').attr('rollup-status') == 1) {
                    jQuery('#widrollupcomments').bootstrapSwitch('state', true, true);

                }else{
                    jQuery('#widrollupcomments').bootstrapSwitch('state', false, true);
                }
            });
            if(jQuery('#widrollupcomments').length > 0) {
                detailContentsHolder.on('switchChange.bootstrapSwitch', '#widrollupcomments', function(e){
                    app.helper.showProgress();
                    self.toggleRollupComments(e);
                });

                if(jQuery('#widrollupcomments').attr('rollup-status') == 1) {
                    jQuery('#widrollupcomments').bootstrapSwitch('state', true, true);

                }else{
                    jQuery('#widrollupcomments').bootstrapSwitch('state', false, true);
                }

            }
            detailContentsHolder.on('click','.detailViewSaveComment',function(e){
                var moduleName=app.getModuleName();
                var currentElement=jQuery(e.currentTarget);
                var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
                var widgetHeaderContainer = summaryWidgetContainer.find('.widget_header');
                var relatedlimit = widgetHeaderContainer.find('[name="relatedlimit"]').val();
                contents = jQuery('div[data-type="Comments"] div.widget_contents');
                var recordId = jQuery('#recordId').val();
                url = 'index.php?module=VTEWidgets&view=SummaryWidget&record='+
                    recordId+'&mode=showCommentsWidget'+'&rollupid=1'
                    +'&parent='+moduleName+'&sourcemodule='+moduleName+'&rollup-toggle=1&limit='+relatedlimit;

                params = {
                    'type' : 'GET',
                    'url' : url
                };
                setTimeout(function() {
                    app.request.get(params).then(function(err, data){
                        app.helper.hideProgress();
                        contents.html(data);
                    });
                }, 300);
            });

            detailContentsHolder.on('click','.selectRelationonWidget',function(e){
                //var instance = Vtiger_Detail_Js.getInstance();
                var currentElement = jQuery(e.currentTarget);
                var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
                var widgetHeaderContainer = summaryWidgetContainer.find('.widget_header');
                var relatedModuleName = widgetHeaderContainer.find('[name="relatedModule"]').val();
                var recordId = jQuery('#recordId').val();
                var module = app.getModuleName();

                var aDeferred = jQuery.Deferred();
                var popupInstance = Vtiger_Popup_Js.getInstance();

                var parameters = {
                    'module' : relatedModuleName,
                    'src_module' : module,
                    'src_record' : recordId,
                    'multi_select' : true
                };

                popupInstance.show(parameters, function(responseString){
                    app.helper.showProgress();
                        var responseData = JSON.parse(responseString);
                        var relatedIdList = Object.keys(responseData);

                        var params = {};
                        params['mode'] = "addRelation";
                        params['module'] = module;
                        params['action'] = 'RelationAjax';

                        params['related_module'] = relatedModuleName;
                        params['src_record'] =recordId;
                        params['related_record_list'] = JSON.stringify(relatedIdList);
                        app.request.post({data:params}).then(
                            function(err, data) {
                                app.helper.hideProgress();
                                if(data != null) {
                                    var widget= summaryWidgetContainer.find('.customwidgetContainer_');
                                    SummaryWidgets_Js.loadWidget(widget);
                                    aDeferred.resolve(data);
                                }
                            },
                            function(error) {
                                app.helper.hideProgress();
                            }
                        );
                    }
                );
                return aDeferred.promise();
            });
        }
    }
);

jQuery(document).ready(function(){
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('VTEWidgets')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

    // Only load on summary page
    var requestMode=app.convertUrlToDataParams(location.href).requestMode;
    if(!(app.view()=='Detail' && (requestMode=='' || requestMode==undefined || requestMode=='summary'))) return;

    var module=app.getModuleName();
    if(module.length <=0) return;
    var record=jQuery('#recordId').val();
    //hide default widget
    var defaultWidgets={};
    var params = {};
    params.data = {
        'module':"VTEWidgets",
        'action':"checkDefaultWidget",
        'sourcemodule':module
    };
    app.request.post(params).then(
        function(err, data) {
            app.helper.hideProgress();
            if(data) {
                defaultWidgets=data;
                var widgetList = jQuery('[class^="widgetContainer_"]');
                widgetList.each(function(index,widgetContainerELement){
                    var widgetContainer = jQuery(widgetContainerELement);
                    var name = widgetContainer.data('name');
                    if(defaultWidgets.all_widget=='1'){
                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                    }
                    else{
                        if(name=='ModComments' && defaultWidgets.comments_widget== '1'){
                            //$(this).addClass('hide');
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        else if(name=='LBL_UPDATES' && defaultWidgets.update_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        if(name=='Documents' && defaultWidgets.document_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        if( module =='Project' ){
                            if(name=='HelpDesk' && defaultWidgets.helpdesk_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            else if(name=='LBL_MILESTONES' && defaultWidgets.milestones_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            else if(name=='LBL_TASKS' && defaultWidgets.tasks_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                        }
                        if(module =='Potentials'){
                            if(name=='LBL_RELATED_CONTACTS' && defaultWidgets.contact_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            else if(name=='LBL_RELATED_PRODUCTS' && defaultWidgets.product_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                        }
                    }
                });

                if(defaultWidgets.activities_widget=='1'){
                    jQuery('#relatedActivities').addClass('hide');
                }
            }
        },
        function(error) {
            app.helper.hideProgress();
        }
    );
    SummaryWidgets_Js.appendWidgets(module,record);

    app.listenPostAjaxReady(function() {
        if(defaultWidgets){
            var widgetList = jQuery('[class^="widgetContainer_"]');
            widgetList.each(function(index,widgetContainerELement){
                var widgetContainer = jQuery(widgetContainerELement);
                var name = widgetContainer.data('name');
                if(defaultWidgets.all_widget=='1'){
                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                }
                else{
                    if(name=='ModComments' && defaultWidgets.comments_widget== '1'){
                        //$(this).addClass('hide');
                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                    }
                    else if(name=='LBL_UPDATES' && defaultWidgets.update_widget== '1'){
                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                    }
                    if(module =='Potentials'|| module=='HelpDesk' || module =='Project' ){
                        if(name=='Documents' && defaultWidgets.document_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                    }
                    if( module =='Project' ){
                        if(name=='HelpDesk' && defaultWidgets.helpdesk_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        else if(name=='LBL_MILESTONES' && defaultWidgets.milestones_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        else if(name=='LBL_TASKS' && defaultWidgets.tasks_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                    }
                    if(module =='Potentials'){
                        if(name=='LBL_RELATED_CONTACTS' && defaultWidgets.contact_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        else if(name=='LBL_RELATED_PRODUCTS' && defaultWidgets.product_widget== '1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                    }
                }
            });

            if(defaultWidgets.activities_widget=='1'){
                jQuery('#relatedActivities').addClass('hide');
            }
        }
        SummaryWidgets_Js.appendWidgets(module,record);
        var instance = new SummaryWidgets_Js();
        instance.registerEvents();
    });
    var instance = new SummaryWidgets_Js();
    instance.registerEvents();
});
jQuery(document).ajaxComplete( function (event, request, settings) {
    var url = settings.url;
    if(url == undefined) return;
    var targetModule = '';
    var targetView = '';
    var sourceModule = '';
    var mode = '';
    var viewMode = '';
    var record = '';
    var sURLVariables = url.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == 'module') {
            targetModule = sParameterName[1];
        }
        else if (sParameterName[0] == 'view') {
            targetView = sParameterName[1];
        }
        else if (sParameterName[0] == 'sourceModule') {
            sourceModule = sParameterName[1];
        }
        else if (sParameterName[0] == 'mode') {
            mode = sParameterName[1];
        }
        else if (sParameterName[0] == 'requestMode') {
            viewMode = sParameterName[1];
        }
        else if (sParameterName[0] == 'record') {
            record = sParameterName[1];
        }
    }
    if (targetView == 'Detail' && (mode == 'showDetailViewByMode' || mode == '') && viewMode == 'summary') {
        var module=app.getModuleName();
        if(module.length <=0) return;
        var record=jQuery('#recordId').val();
        //hide default widget
        var defaultWidgets={};
        var params = {};
        params.data = {
            'module':"VTEWidgets",
            'action':"checkDefaultWidget",
            'sourcemodule':module
        };
        app.request.post(params).then(
            function(err, data) {
                app.helper.hideProgress();
                if(data) {
                    defaultWidgets=data;
                    var widgetList = jQuery('[class^="widgetContainer_"]');
                    widgetList.each(function(index,widgetContainerELement){
                        var widgetContainer = jQuery(widgetContainerELement);
                        var name = widgetContainer.data('name');
                        if(defaultWidgets.all_widget=='1'){
                            widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                        }
                        else{
                            if(name=='ModComments' && defaultWidgets.comments_widget== '1'){
                                //$(this).addClass('hide');
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            else if(name=='LBL_UPDATES' && defaultWidgets.update_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            if(name=='Documents' && defaultWidgets.document_widget== '1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            if( module =='Project' ){
                                if(name=='HelpDesk' && defaultWidgets.helpdesk_widget== '1'){
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                                else if(name=='LBL_MILESTONES' && defaultWidgets.milestones_widget== '1'){
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                                else if(name=='LBL_TASKS' && defaultWidgets.tasks_widget== '1'){
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                            }
                            if(module =='Potentials'){
                                if(name=='LBL_RELATED_CONTACTS' && defaultWidgets.contact_widget== '1'){
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                                else if(name=='LBL_RELATED_PRODUCTS' && defaultWidgets.product_widget== '1'){
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                            }
                        }

                    });
                    if(defaultWidgets){
                        var widgetList = jQuery('[class^="widgetContainer_"]');
                        widgetList.each(function(index,widgetContainerELement){
                            var widgetContainer = jQuery(widgetContainerELement);
                            var name = widgetContainer.data('name');
                            if(defaultWidgets.all_widget=='1'){
                                widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                            }
                            else{
                                if(name=='ModComments' && defaultWidgets.comments_widget== '1'){
                                    //$(this).addClass('hide');
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                                else if(name=='LBL_UPDATES' && defaultWidgets.update_widget== '1'){
                                    widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                }
                                if(module =='Potentials'|| module=='HelpDesk' || module =='Project' ){
                                    if(name=='Documents' && defaultWidgets.document_widget== '1'){
                                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                    }
                                }
                                if( module =='Project' ){
                                    if(name=='HelpDesk' && defaultWidgets.helpdesk_widget== '1'){
                                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                    }
                                    else if(name=='LBL_MILESTONES' && defaultWidgets.milestones_widget== '1'){
                                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                    }
                                    else if(name=='LBL_TASKS' && defaultWidgets.tasks_widget== '1'){
                                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                    }
                                }
                                if(module =='Potentials'){
                                    if(name=='LBL_RELATED_CONTACTS' && defaultWidgets.contact_widget== '1'){
                                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                    }
                                    else if(name=='LBL_RELATED_PRODUCTS' && defaultWidgets.product_widget== '1'){
                                        widgetContainer.parent('div.summaryWidgetContainer').addClass('hide');
                                    }
                                }
                            }
                        });

                        if(defaultWidgets.activities_widget=='1'){
                            jQuery('#relatedActivities').addClass('hide');
                        }
                    }

                    if(defaultWidgets.activities_widget=='1'){
                        jQuery('#relatedActivities').addClass('hide');
                    }
                    SummaryWidgets_Js.appendWidgets(module,record);
                }
            },
            function(error) {
                app.helper.hideProgress();
            }
        );
        SummaryWidgets_Js.appendWidgets(targetModule,record);
        var instance = new SummaryWidgets_Js();
        instance.registerEvents();
    }
});

function waitUntil(waitFor,toDo){
    if(waitFor()) {
        toDo();
    } else {
        setTimeout(function() {
            waitUntil(waitFor, toDo);
        }, 300);
    }
}
jQuery(document).on('change', '.filterField', function(e){

    var currentElement = jQuery(e.currentTarget);
    var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
    var widget = summaryWidgetContainer.find('.customwidgetContainer_');//('.widgetContentBlock');
    var url =  widget.data('url');

    SummaryWidgets_Js.loadWidget(widget);
});
jQuery(document).on('click','.deleteCommentWidget', function(e){
    var element = jQuery(e.currentTarget);
    if(!element.is(":disabled")) {
        var aDeferred = jQuery.Deferred();
        var commentInfoBlock = element.closest('.singleComment');
        var commentInfoHeader = commentInfoBlock.closest('.commentDetails').find('.commentInfoHeader');
        var commentId = commentInfoHeader.data('commentid');

        var postData = {
            'record' : 	commentId,
            'module' : 'ModComments',
            'action' : 'DeleteAjax'
        }
        AppConnector.request(postData).then(
            function(data){
                location.reload();
            }
        );
        return aDeferred.promise();
    }
});
jQuery(document).on('click','.btn_show_hide_comment_content', function(e){
    var element = jQuery(e.currentTarget);
	if(!element.is(":disabled")) {
		var commentId = element[0].id;
		var innerText = element[0].innerText;
		
		if('Hide' == innerText.trim())
		{
			element.html('<i class="caret"></i> Show');
			$('#commentInfoContent_'+commentId).hide();
			$('#commentActionsContainer_'+commentId).hide();
		}
		else if ('Show' == innerText.trim())
		{
			element.html('<i class="caret"></i> Hide');
			$('#commentInfoContent_'+commentId).show();
			$('#commentActionsContainer_'+commentId).show();
		}
		return false;
	}
});
jQuery(document).on('click','.vteWidgetCreateButton',function(e){
    var instance = Vtiger_Detail_Js.getInstance();
    var currentElement = jQuery(e.currentTarget);
    var summaryWidgetContainer = currentElement.closest('.summaryWidgetContainer');
    var widgetHeaderContainer = summaryWidgetContainer.find('.widget_header');
    var referenceModuleName = widgetHeaderContainer.find('[name="relatedModule"]').val();
    var recordId = jQuery('#recordId').val();
    var module = app.getModuleName();
    var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
    var fieldName =currentElement.data('prf');

    var customParams = {};
    customParams[fieldName] = recordId;

    app.event.one('post.QuickCreateForm.show',function(event,data){
        var index,queryParam,queryParamComponents;
        var parentModule=module;
        var parentId=recordId;
        var relatedField=fieldName;
        jQuery('<input type="hidden" name="sourceModule" value="'+parentModule+'" />').appendTo(data);
        jQuery('<input type="hidden" name="sourceRecord" value="'+parentId+'" />').appendTo(data);
        jQuery('<input type="hidden" name="relationOperation" value="true" />').appendTo(data);

        if(typeof relatedField != "undefined"){
            var field = data.find('[name="'+relatedField+'"]');
            //If their is no element with the relatedField name,we are adding hidden element with
            //name as relatedField name,for saving of record with relation to parent record
            if(field.length == 0){
                jQuery('<input type="hidden" name="'+relatedField+'" value="'+parentId+'" />').appendTo(data);
            }
        }
        if(typeof callback !== 'undefined') {
            callback();
        }
    });



    if(quickCreateNode.length <= 0) {
        window.location.href = currentElement.data('url')+'&sourceModule='+module+'&sourceRecord='+recordId+'&relationOperation=true&'+fieldName+'='+recordId;
        return;
    }

    var preQuickCreateSave = function(data){
        instance.addElementsToQuickCreateForCreatingRelation(data,module,recordId);
        jQuery('<input type="hidden" name="'+fieldName+'" value="'+recordId+'" >').appendTo(data);
    };
    var callbackFunction = function() {
        var widget= summaryWidgetContainer.find('.customwidgetContainer_');
        SummaryWidgets_Js.loadWidget(widget);
    };
    var QuickCreateParams = {};
    QuickCreateParams['callbackPostShown'] = preQuickCreateSave;
    QuickCreateParams['callbackFunction'] = callbackFunction;
    QuickCreateParams['data'] = customParams;
    QuickCreateParams['noCache'] = false;

    quickCreateNode.trigger('click', QuickCreateParams);
});

