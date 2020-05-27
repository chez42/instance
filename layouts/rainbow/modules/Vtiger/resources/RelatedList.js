/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

jQuery.Class("Vtiger_RelatedList_Js",{
	loaded : true,
	
	getInstance: function(parentId, parentModule, selectedRelatedTabElement, relatedModuleName) {
		var moduleClassName = parentModule+"_RelatedList_Js";
		var fallbackClassName = Vtiger_RelatedList_Js;
		if(typeof window[moduleClassName] != 'undefined') {
			var instance = new window[moduleClassName](parentId, parentModule, selectedRelatedTabElement, relatedModuleName);
		} else {
			var instance = new fallbackClassName(parentId, parentModule, selectedRelatedTabElement, relatedModuleName);
		}

		return instance;
	}

},{
	
	selectedRelatedTabElement : false,
	parentRecordId : false,
	parentModuleName : false,
	relatedModulename : false,
	relatedTabsContainer : false,
	detailViewContainer : false,
	relatedContentContainer : false,
    parentId : false,
	
	setSelectedTabElement : function(tabElement) {
		this.selectedRelatedTabElement = tabElement;
	},
	
	getSelectedTabElement : function(){
		return this.selectedRelatedTabElement;
	},
	
	triggerDisplayTypeEvent : function() {
		var widthType = app.cacheGet('widthType', 'narrowWidthType');
		if(widthType) {
			var elements = jQuery('.listViewEntriesTable').find('td,th');
			elements.attr('class', widthType);
		}
	},
    
	updateRelatedRecordsCount: function (relationId) {
		return true;
		var recordId = app.getRecordId();
		var moduleName = app.getModuleName();
		var detailInstance = new Vtiger_Detail_Js();
		detailInstance.getRelatedRecordsCount(recordId, moduleName, relationId).then(function (data) {
			var relatedRecordsCount = data[relationId];
			var element = new Object(jQuery("a", "li[data-relation-id=" + relationId + "]"));
			// we should only show if there are any related records
			var numberEle = element.find('.numberCircle');
			numberEle.text(relatedRecordsCount);
			if (relatedRecordsCount > 0) {
				numberEle.removeClass('hide');
			} else {
				numberEle.addClass('hide');
			}
			element.attr("recordscount", relatedRecordsCount);
		});
	},

	getCurrentPageNum : function() {
		return jQuery('input[name="currentPageNum"]',this.relatedContentContainer).val();
	},
	
	setCurrentPageNumber : function(pageNumber){
		jQuery('input[name="currentPageNum"]').val(pageNumber);
	},
	
	/**
	 * Function to get Order by
	 */
	getOrderBy : function(){
		return jQuery('#orderBy').val();
	},
	
	/**
	 * Function to get Sort Order
	 */
	getSortOrder : function(){
			return jQuery("#sortOrder").val();
	},
	
	getCompleteParams : function(){
		var params = {};
		params['view'] = "Detail";
		params['module'] = this.parentModuleName;
		params['record'] = this.getParentId(),
		params['relatedModule'] = this.relatedModulename,
		params['sortorder'] =  this.getSortOrder(),
		params['orderby'] =  this.getOrderBy(),
		params['page'] = this.getCurrentPageNum();
		params['mode'] = "showRelatedList";
	
		var docTree = $(document).find('.documentTree');
		
		if(docTree.length){
			params['mode'] = "folderViewForDocs";
		}
		
		params['tab_label'] = this.selectedRelatedTabElement.data('label-key');
        var detailInstance = Vtiger_Detail_Js.getInstance();
        var searchParams = JSON.stringify(detailInstance.getRelatedListSearchParams());
        params['search_params'] = searchParams;
        params['nolistcache'] = (jQuery('#noFilterCache').val() == 1) ? 1 : 0;
		return params;
	},
    
    loadRelatedList : function(params){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		if(typeof this.relatedModulename== "undefined" || this.relatedModulename.length <= 0 ) {
			return;
		}
        
		var completeParams = this.getCompleteParams();
		jQuery.extend(completeParams,params);
        app.helper.showProgress();
        
		app.event.trigger('pre.relatedListLoad.click');
		
        app.request.get({data:completeParams}).then(
			function(error,responseData){
                app.helper.hideProgress();
				thisInstance.relatedTabsContainer.find('li').removeClass('active');
				thisInstance.selectedRelatedTabElement.addClass('active');
				container = jQuery('div.details');
                container.html(responseData);
            	
                var docTree = $(document).find('.documentTree');
        		
        		if(docTree.length){
        			thisInstance.createTreeFolderPopup();
        			thisInstance.registerEventForShowHiddenFolders();
        		}
        		thisInstance.registerEventForShowHiddenFolders();
        		
               // vtUtils.applyFieldElementsView(container);
                
				thisInstance.initializePaginationEvents();
                thisInstance.triggerRelationAdditionalActions();
                thisInstance.registerEvents();
				app.event.trigger('post.relatedListLoad.click', container);
                
				aDeferred.resolve(responseData);
			},
			
			function(textStatus, errorThrown){
                app.helper.hideProgress();
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},
    
    getParentId : function(){
		return this.parentRecordId;
	},
        setParentId : function(parentId){
            this.parentRecordId = parentId;
        },
       
    /**
	 * Function to select related record for the module
	 */
    showSelectRelationPopup : function(){
        var popupParams = this.getPopupParams(); 
        var popupjs = new Vtiger_Popup_Js();
        popupjs.showPopup(popupParams,"post.RecordList.click");
	},
    
    /**
	 * Function to fetch popup params
	 */
    getPopupParams : function(){
		var parameters = {};
		var relationId = this.getSelectedTabElement().data('relationId');
		
		var docTree = $(document).find('.documentTree');
		
		if(docTree.length){
			relationId = $(document).find('[name="link_id"]').val();
		}
		var parameters = {
			'module' : this.relatedModulename,
			'src_module' : this.parentModuleName,
			'src_record' : this.parentRecordId,
			'multi_select' : true,
            'view' : 'Popup',
            'relationId' : relationId
		};
		return parameters;
	},

	/**
	 * Function to add related record for the module
	 */
	addRelatedRecord : function(element , callback){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		var	referenceModuleName = this.relatedModulename;
		var parentId = this.getParentId();
		var parentModule = this.parentModuleName;
		var quickCreateParams = {};
		var relatedParams = {};
		var relatedField = element.data('name');
		var fullFormUrl = element.data('url');
		relatedParams[relatedField] = parentId;
		var eliminatedKeys = new Array('view', 'module', 'mode', 'action');

        app.event.one('post.QuickCreateForm.show',function(event,data){
            var index,queryParam,queryParamComponents;
			
			//To handle switch to task tab when click on add task from related list of activities
			//As this is leading to events tab intially even clicked on add task
            /*
             * Not required as we are now showing only one button for adding activities
			if(typeof fullFormUrl != 'undefined' && fullFormUrl.indexOf('?')!== -1) {
				var urlSplit = fullFormUrl.split('?');
				var queryString = urlSplit[1];
				var queryParameters = queryString.split('&');
				for(index=0; index<queryParameters.length; index++) {
					queryParam = queryParameters[index];
					queryParamComponents = queryParam.split('=');
					if(queryParamComponents[0] == 'mode' && queryParamComponents[1] == 'Calendar'){
						data.find('a[data-tab-name="Task"]').trigger('click');
                        data.find('[name="calendarModule"]').val('Calendar');
					}
				}
			}
            */
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
			for(index=0; index<queryParameters.length; index++) {
				queryParam = queryParameters[index];
				queryParamComponents = queryParam.split('=');
				if(jQuery.inArray(queryParamComponents[0], eliminatedKeys) == '-1' && data.find('[name="'+queryParamComponents[0]+'"]').length == 0) {
					jQuery('<input type="hidden" name="'+queryParamComponents[0]+'" value="'+queryParamComponents[1]+'" />').appendTo(data);
				}
			}
            if(typeof callback !== 'undefined') {
                callback();
            }
        });
        
        app.event.one('post.QuickCreateForm.save',function(event,data){
            //After adding Event to related list, reverting related module name back to Calendar from Events 
            if(thisInstance.relatedModulename === 'Events'){
                thisInstance.relatedModulename = 'Calendar';
			}
            thisInstance.loadRelatedList().then(function(data){
                var selectedTabElement = thisInstance.selectedRelatedTabElement;
                if(thisInstance.relatedModulename == 'Calendar'){
                    var params = thisInstance.getPageJumpParams();
                    app.request.post(params).then(function(error, data){
                        var numberOfRecords = data.numberOfRecords;
                        // we should only show if there are any related records
                        var numberEle = selectedTabElement.find('.numberCircle');
                        numberEle.text(numberOfRecords);
                        if(numberOfRecords > 0) {
                            numberEle.removeClass('hide');
                        }else{
                            numberEle.addClass('hide');
                        }
                    });
                } else {
                    thisInstance.updateRelatedRecordsCount(selectedTabElement.data('relation-id'),[1],true);
                }
                aDeferred.resolve(data);
            });
        });
		
		//If url contains params then seperate them and make them as relatedParams
		if(typeof fullFormUrl != 'undefined' && fullFormUrl.indexOf('?')!== -1) {
			var urlSplit = fullFormUrl.split('?');
			var queryString = urlSplit[1];
			var queryParameters = queryString.split('&');
			for(var index=0; index<queryParameters.length; index++) {
				var queryParam = queryParameters[index];
				var queryParamComponents = queryParam.split('=');
				if(jQuery.inArray(queryParamComponents[0], eliminatedKeys) == '-1') {
					relatedParams[queryParamComponents[0]] = queryParamComponents[1];
				}
			}
		}
		
		quickCreateParams['data'] = relatedParams;
		quickCreateParams['noCache'] = true;
		var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="'+ referenceModuleName +'"]');
		if(quickCreateNode.length <= 0) {
			Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
		}
		quickCreateNode.trigger('click',quickCreateParams);
		return aDeferred.promise();
	},
    
    deleteRelation : function(relatedIdList, customParams) {
		var aDeferred = jQuery.Deferred();
        var thisInstance = this;
		var params = {};
		params['mode'] = "deleteRelation";
		params['module'] = this.parentModuleName;
		params['action'] = 'RelationAjax';

        var selectedTabElement = this.getSelectedTabElement();
        var relationId = selectedTabElement.data('relationId');
		params['related_module'] = this.relatedModulename;
        params['relationId'] = relationId;
        if(this.relatedModulename == 'Emails' && this.parentId != false) {
            params['src_record'] = this.parentId;
        } else {
            params['src_record'] = this.parentRecordId;
        }
		params['related_record_list'] = JSON.stringify(relatedIdList);
		
		if(typeof customParams != 'undefined') {
			params = jQuery.extend(params,customParams);
		}
		app.request.post({"data":params}).then(
			function(err,responseData){
                thisInstance.updateRelatedRecordsCount(relationId,relatedIdList,false);
				aDeferred.resolve(responseData);
			},

			function(textStatus, errorThrown){
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},
    
    addRelations : function(idList){
        var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var sourceRecordId = this.parentRecordId;
		var sourceModuleName = this.parentModuleName;
		var relatedModuleName = this.relatedModulename;
        var selectedTabElement = this.getSelectedTabElement();
        if(selectedTabElement.length > 0){
            var relationId = selectedTabElement.data('relationId');
        }

		var params = {};
		params['mode'] = "addRelation";
		params['module'] = sourceModuleName;
		params['action'] = 'RelationAjax';
		params['relationId'] = relationId;
		params['related_module'] = relatedModuleName;
		params['src_record'] = sourceRecordId;
		params['related_record_list'] = JSON.stringify(idList);

        app.helper.showProgress();
        
		app.request.post({"data":params}).then(
			function(responseData){
                thisInstance.updateRelatedRecordsCount(relationId,idList,true);
                app.helper.hideProgress();
				aDeferred.resolve(responseData);
			},

			function(textStatus, errorThrown){
                app.helper.hideProgress();
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},
    
    
    
    triggerRelationAdditionalActions : function() {
	},
	
	registerScrollForRollupComments : function() {
        jQuery('div#page').scroll(function() {
        	
            if ($('div#page').scrollTop() + $(window).height() >= $(document).height() - 30
                && jQuery('div.commentContainer').length > 0 
                && jQuery('.widgetContainer_comments').length === 0
                && jQuery('#rollupcomments').attr('rollup-status') > 0) {
				
                if(Vtiger_RelatedList_Js.loaded && jQuery('#rollupcomments').attr('hascomments') == 1) {
                    Vtiger_RelatedList_Js.loaded = false;
					app.helper.showProgress();
                    var currentTarget = jQuery('#rollupcomments');
                    var moduleName = currentTarget.attr('module');
                    var recordId = currentTarget.attr('record');
                    var rollupId = currentTarget.attr('rollupid');
                    var rollupstatus = currentTarget.attr('rollup-status');
                    var startindex = parseInt(currentTarget.attr('startindex'));

                    var url = 'index.php?module=Vtiger&view=ModCommentsDetailAjax&parent='+
                      moduleName+'&parentId='+recordId+'&rollupid='+rollupId+'&rollup_status='+rollupstatus
                      +'&startindex='+startindex+'&mode=getNextGroupOfRollupComments';

                    var params = {
						'type' : 'GET',
						'url' : url
					};
					
                    app.request.get(params).then(function(err, data){
						Vtiger_RelatedList_Js.loaded = true;
						app.helper.hideProgress();
						if(data) {
							jQuery('#rollupcomments').attr('startindex', startindex + 10);
							jQuery('.commentsBody ul.unstyled:first').append(jQuery(data).children());
						}else {
							jQuery('#rollupcomments').attr('hascomments', '0');
						}
                    });
                }
            }
        });
    },
    
    getPageJumpParams: function() {
        var thisInstance = this;
        var detailInstance = Vtiger_Detail_Js.getInstance();
        var searchParams = JSON.stringify(detailInstance.getRelatedListSearchParams());
        var params = {
			'type' : 'POST',
			'data' : {
				'action' : "RelationAjax",
				'module' : thisInstance.parentModuleName,
				'record' : thisInstance.getParentId(),
				'relatedModule' : thisInstance.relatedModulename,
				'tab_label' : thisInstance.selectedRelatedTabElement.data('label-key'),
				'mode' : "getRelatedListPageCount",
				'search_params' : searchParams
			}
		};
        
        return params;
    },
	
	pageJump : function(){
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
        var params = this.getPageJumpParams();
		
		var element = jQuery('#totalPageCount');
		var totalCountElem = jQuery('.relatedContainer').find('#totalCount');
		var totalPageNumber = element.text();
		
		if(totalPageNumber === ""){
			app.request.post(params).then(
				function(err, data) {
					var response;
					if(typeof data !== "object"){
						response = JSON.parse(data);
					} else{
						response = data;
					}
					
					var pageCount = data.page;
					var numberOfRecords = data.numberOfRecords;
					totalCountElem.val(numberOfRecords);
					element.text(pageCount);
					aDeferred.resolve(response);
				}
			);
		}else{
			aDeferred.resolve();
		}
		return aDeferred.promise();
	},
	
	totalNumOfRecords : function (curEle) {
		var thisInstance = this;
		var element = jQuery('.relatedContainer').find('#totalCount');
		var totalPageNumber = element.text();
		var pageCount;
		if(curEle.attr('id') !== 'relatedViewPageJump') curEle.addClass('hide');

		if(totalPageNumber === ""){
			var totalCountElem = jQuery('.relatedContainer').find('#totalCount');
			var totalRecordCount = totalCountElem.val();

			if(totalRecordCount !== '') {
				var recordPerPage = jQuery('#pageLimit').val();
				if(recordPerPage === '0') recordPerPage = 1;
				pageCount = Math.ceil(totalRecordCount/recordPerPage);
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
				if(curEle.attr('id') !== 'PageJump') {
					thisInstance.showPagingInfo();
				}
				return;
			}

			thisInstance.pageJump().then(function(data){
				var pageCount = data.page;
				var numOfrecords = data.numberOfRecords;
				if(numOfrecords === 0) {
					numOfrecords = 1;
				}
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
				totalCountElem.val();
				if(curEle.attr('id') !== 'PageJump') {
					thisInstance.showPagingInfo();
				}
			});
		}
	},
	
	showPagingInfo : function(){
		var totalNumberOfRecords = jQuery('.relatedContainer').find('#totalCount').val();
		var pageNumberElement = jQuery('.pageNumbersText');
		var pageRange = pageNumberElement.text();
		var newPagingInfo = pageRange.trim()+" "+app.vtranslate('of')+" "+totalNumberOfRecords+"  ";
		var listViewEntriesCount = parseInt(jQuery('#noOfEntries').val());
		
		if(listViewEntriesCount !== 0){
			jQuery('.pageNumbersText').html(newPagingInfo);
		} else {
			jQuery('.pageNumbersText').html("");
		}
	},
	
	pageJumpOnSubmit : function(element) {
		var thisInstance = this;
		
		var currentPageElement = jQuery('.relatedContainer').find('#pageNumber');
		var currentPageNumber = parseInt(currentPageElement.val());
		var newPageNumber = parseInt(jQuery('#pageToJump').val());
		var totalPages = parseInt(jQuery('.relatedContainer').find('#totalPageCount').text());

		if(newPageNumber > totalPages){
			var message = app.vtranslate('JS_PAGE_NOT_EXIST');
			app.helper.showErrorNotification({'message':message})
			return;
		}

		if(newPageNumber === currentPageNumber){
			var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+newPageNumber;
			app.helper.showAlertNotification({'message': message});
			return;
		}

		var urlParams = {
			"page" : newPageNumber
		};

		thisInstance.loadRelatedList(urlParams).then(
			function(data){
				element.closest('.btn-group ').removeClass('open');
			});
		return false;
		
	},
    
	initializePaginationEvents : function() {
		/*$('#listview-table tr td').css("padding",0);
		$('#listview-table tr th').css("padding",0);
		$('#listview-table tr:first th').each(function (idx, ele) { 
			var tdWidth = $('#listview-table tr td').eq(idx).width();
			var thWidth = $(ele).width(); 
			if(thWidth > tdWidth){
				$('#listview-table tr td').eq(idx).css("width",thWidth + "px");
				$('#listview-table tr td').eq(idx).css("min-width", thWidth + "px");
				$("#listview-table tr th").eq(idx).css("width",thWidth + "px");
				$("#listview-table tr th").eq(idx).css("min-width", thWidth + "px");
			} else{
				$('#listview-table tr td').eq(idx).css("width",tdWidth + "px");
				$('#listview-table tr td').eq(idx).css("min-width", tdWidth + "px");
				$("#listview-table tr th").eq(idx).css("width",tdWidth + "px");
				$("#listview-table tr th").eq(idx).css("min-width", tdWidth + "px");
			}
		});
		
		if($("#listview-table thead tr:first").width()<$("#listview-table").width()){
			var fixWidth = $("#listview-table thead tr th:last-child").width()+($("#listview-table").width()-$("#listview-table thead tr:first").width()+10);
			$("#listview-table tbody tr td:last-child, #listview-table thead tr th:last-child").css("min-width", fixWidth + "px");
		}
				
		if($('#table-content').length > 0){
			const list_w = new PerfectScrollbar('#table-content', {suppressScrollY: true});
			new PerfectScrollbar('#table-content',{suppressScrollY: true});
			new PerfectScrollbar('#table-content tbody',{suppressScrollX: true});
		}
		*/
		
		var thisInstance = this;
		var paginationObj = new Vtiger_Pagination_Js;
        var relatedViewContainer = jQuery('.relatedContainer');
		paginationObj.initialize(relatedViewContainer);
		
		app.event.on(paginationObj.nextPageButtonClickEventName, function(){
			var pageLimit = relatedViewContainer.find('#pageLimit').val();
            var noOfEntries = relatedViewContainer.find('#noOfEntries').val();
            var nextPageExist = relatedViewContainer.find('#nextPageExist').val();
			var pageNumber = relatedViewContainer.find('#pageNumber').val();
			var nextPageNumber = parseInt(parseFloat(pageNumber)) + 1;
			
            if(noOfEntries === pageLimit && nextPageExist){
				var urlParams = {};
                thisInstance.setCurrentPageNumber(nextPageNumber);
				relatedViewContainer.find("#pageNumber").val(nextPageNumber);
				thisInstance.loadRelatedList(urlParams);
			}
		});
		
		app.event.on(paginationObj.previousPageButtonClickEventName, function(){
			var pageNumber = relatedViewContainer.find('#pageNumber').val();
			var previousPageNumber = parseInt(parseFloat(pageNumber)) - 1;
			
			if(pageNumber > 1) {
				var urlParams = {};
                thisInstance.setCurrentPageNumber(previousPageNumber);
				relatedViewContainer.find('#pageNumber').val(previousPageNumber);
				thisInstance.loadRelatedList(urlParams);
			}
		});
		
		app.event.on(paginationObj.pageJumpButtonClickEventName, function(event, currentEle){
			thisInstance.pageJump();
		});
		
		app.event.on(paginationObj.totalNumOfRecordsButtonClickEventName, function(event, currentEle){
			thisInstance.totalNumOfRecords(currentEle);
		});
		
		app.event.on(paginationObj.pageJumpSubmitButtonClickEvent, function(event, currentEle){
			thisInstance.pageJumpOnSubmit(currentEle);
		});
	},
    
    registerEditLink : function() {
		var relatedContainer =  jQuery('.relatedContainer');;
		relatedContainer.on('click', 'a.relationEdit', function(e) {
			var element = jQuery(e.currentTarget);
			var url = element.attr('href');
			var detailInstance = Vtiger_Detail_Js.getInstance();
			var postData = detailInstance.getDefaultParams();
			for(var key in postData) {
				if(postData[key]) {
                    if(key == 'relatedModule') {
                        postData['returnrelatedModuleName'] = postData[key];
                    } else {
                        postData['return'+key] = postData[key];
                    }
					delete postData[key];
				} else {
					delete postData[key];
				}
			}
			e.preventDefault();
			e.stopPropagation();
			window.location.href = url +'&'+ $.param(postData);
		});
	},
        
	init : function(parentId, parentModule, selectedRelatedTabElement, relatedModuleName) {
		this.selectedRelatedTabElement = selectedRelatedTabElement;
		this.parentRecordId = parentId;
		this.parentModuleName = parentModule;
		this.relatedModulename = relatedModuleName;
		this.relatedTabsContainer = jQuery(selectedRelatedTabElement).closest('div.related-tabs');
		this.detailViewContainer = this.relatedTabsContainer.closest('div.detailViewContainer');
		this.relatedContentContainer = jQuery('div.details', this.detailViewContainer);

		this.registerEditLink();
		var instance = this;
			
		$(document).on('click','.changeMode',function(){
			
			var name = $(this).attr('name');
			
			if(name == 'list'){
				
				var params = {};
				params['mode'] = "showRelatedList";
				
				instance.loadRelatedList(params);
				
			}else if(name == 'folder'){
				
				var params = {};
				params['mode'] = "folderViewForDocs";
				
				instance.loadRelatedList(params);
				
			}
		});
		
		var docTree = $(document).find('.documentTree');
		
		if(docTree.length){
			this.createTreeFolderPopup();
			this.registerEventForShowHiddenFolders();
		}
		this.registerEventForShowHiddenFolders();
		this.clearList();
		this.registerEvents();
		
    },
	    
registerEventForShowHiddenFolders : function(){
	var thisInstance = this;
	$('.showHiddenFolders').on('change',function(){
		var value = '';
		if($(this). prop("checked") == true){
			value = 1;
		}
		else if($(this). prop("checked") == false){
			value = 0;
		}
		var params = {
			'module' : 'Documents',
			'action' : "GetTreeData",
			'mode' : 'updateShowfolder',
			'folder_value' : value,
			'userid' : app.getUserId()
		}
		app.request.post({data:params}).then(function(err,data) {
			var urlParams = {};
			urlParams['mode'] = "folderViewForDocs";
			urlParams['show_empty_folders'] = (value == 1) ? 'Yes' : 'No';
      	  	thisInstance.loadRelatedList(urlParams);
		});
		
	});
	
},

getTreeData : function(){
	var aDeferred = jQuery.Deferred();
	var params = {
			'module' : 'Documents',
			'action' : "GetTreeData",
			'mode' : 'get_data',
			'record' : app.getRecordId()
			}
	app.request.post({data:params}).then(
		function(err,data) {
			var response;
			if(typeof data !== "object"){
				response = JSON.parse(data);
			} else{
				response = data;
			}
			aDeferred.resolve(response);
		}
	);
	return aDeferred.promise();
},

createTreeFolderPopup : function() {
	
	var thisInstance = this;
	app.helper.showProgress();
	var record = app.getRecordId();
	
	var data ='';
	
	thisInstance.getTreeData().then(function(data){
		data = data;
	
    	$(function () {
			
    		jQuery('#tree_folder')
				.jstree({
					'core' : {
						
						/*'data' : {
							
							'url' : 'index.php?module=Documents&action=GetTreeData&record='+record+'&mode=get_data',
							"dataType" : "json"
								
						},*/
						'data' : data,
						'check_callback' : function(o, n, p, i, m) {
							
							
							if(m && m.dnd && m.pos !== 'i') { return false; }
							if(o === "move_node" || o === "copy_node") {
								if(this.get_node(n).parent === this.get_node(p).id) { return false; }
								if( this.get_node(n).type != 'file' || this.get_node(p).type == 'file'){
									//app.helper.showErrorNotification({title: 'Error', message: 'Enable to Move the Folder.'});
									return false;
								}
							}
							return true;
							
						},
						'themes' : {
							
							'responsive' : false,
							'variant' : 'small',
							//'stripes' : true
							
						}
					},
					'sort' : function(a, b) {
						
						return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
						
					},

					'types' : {
						
						'default' : { 'icon' : 'jstree-folder' },
						'file' : { 'valid_children' : [], 'icon' : 'jstree-file' }
						
					},
					'unique' : {
						
						'duplicate' : function (name, counter) {
							return name + ' ' + counter;
						}
					
					},
					
					'plugins' : ['state','dnd','sort','types','unique']//,'contextmenu'
					
				});
    		
    			jQuery('#tree_folder').on("open_node.jstree", function (e, data) {
					
				    $('#tree_folder li').each(function (index,value) {
				    	
				    	var node = $("#tree_folder").jstree().get_node("[id='"+this.id+"']");
				        if(node.type == 'default'){
				        	
				        	if(!$('.showHiddenFolders'). prop("checked")){
					        	if(node.children.length <1){
					        		
					        		$(this).addClass('jstree-hidden');
					        	
					        	}else{
					        		
					        		var children = node.children_d;
					        		var child = children.toString();
					        		var str = $('input[name="module_entity_id"]').val();
					        		if(child.indexOf(str+'x') == '-1'){
					        			$(this).addClass('jstree-hidden');
					        		}
					        		
					        	}
				        	}
				        }else if(node.type == 'file'){
				        	
				        	$(this).on('click',function(e){
				        		e.preventDefault();
				        		e.stopImmediatePropagation();
				        		var href = $(this).find('a').attr('href');
    				        	 
	   	    				    window.open(href);
	   	    				     
				        	});
				        	
				        }
				          
				    });
				   
				});
    			
    			jQuery('#tree_folder').on("ready.jstree",function(){
    				
					$(this).jstree("open_node", "ul > li:first");
				    $('#tree_folder li').each(function (index,value) {
				    	
				    	var node = $("#tree_folder").jstree().get_node("[id='"+this.id+"']");
				        
				        if(node.type == 'default'){
				        	
				        	if(!$('.showHiddenFolders'). prop("checked")){
					        	if(node.children.length <1){
					        		
					        		$(this).addClass('jstree-hidden');
					        	
					        	}else{
					        		
					        		var children = node.children_d;
					        		var child = children.toString();
					        		var str = $('input[name="module_entity_id"]').val();
					        		
					        		if(child.indexOf(str+'x') == '-1'){
					        			$(this).addClass('jstree-hidden');
					        		}
					        		
					        	}
				        	}
				        }
				          
				    });
				    $(this).jstree().close_all();
				    app.helper.hideProgress();
				});
    			jQuery('#tree_folder').bind("hover_node.jstree", function (e, data){
					
					var node = data.node.type;

					if(node == 'file'){
						
						 $(document).find('.hover,.relationDelete').remove();
						 
						 $(document).on({
							 mouseenter: function(){
								 $(document).find('.hover,.relationDelete').css("display", "inline"); 
							 },
							 mouseleave: function () {
								 $(document).find('.hover,.relationDelete').hide();
							 }
						 }, '.jstree-node');
						
						 var nodeid = data.node.id;
						 
						 var recordId = nodeid.split('x');
						 
						 var Editurl ='index.php?module=Documents&view=Edit&record='+recordId[1];
						 
						 $('#'+data.node.id+'_anchor').append('<a name="relationEdit" onclick="editFunction()" style="display:none;" class="hover"  data-url="'+Editurl+'">&nbsp;&nbsp;<i title="Edit" class="fa fa-pencil"></i> &nbsp;</a> <a class="relationDelete" style="display:none;"onclick="unlinkFunction();" data-id='+recordId[1]+'><i title="Unlink" class="vicon-linkopen"></i>&nbsp;&nbsp;</a> ');
						
					}
        		    
        		});
    			
    			jQuery('#tree_folder').on('move_node.jstree', function (e, data) {
					jQuery.get('?module=Documents&action=GetTreeData&mode=move_node', { 'id' : data.node.id, 'parent' : data.parent })
						.done(function (d) {
							if(d.result.success){
								app.helper.showSuccessNotification({message:app.vtranslate('File Moved Successfully')});
								 var urlParams = {};
				            	 thisInstance.loadRelatedList(urlParams);
								jQuery('#tree_folder').jstree().close_all();
							}else{
								if(d.result.message)
									app.helper.showErrorNotification({'message': d.result.message});
								$("#tree_folder").jstree().refresh();
							}
						})
						.fail(function () {
							data.instance.refresh();
						});
				});
			
    		});
    	});
	
	},
	
	registerRelatedListViewMainCheckBoxClickEvent: function () {
		
		var self = this;
		var relatedViewContainer = this.detailViewContainer;
		
		relatedViewContainer.on('click', '.relatedlistViewEntriesMainCheckBox', function (e) {
			
			e.stopPropagation();
			var element = jQuery(e.currentTarget);
			if (element.is(':checked')) {
				var rows = relatedViewContainer.find('tr.listViewEntries');
				
				rows.find('.relatedlistViewEntriesCheckBox').each(function (e) {
					jQuery(this).prop('checked', true);
					var row = jQuery(this).closest('.listViewEntries');
					row.trigger('Post.ListRow.Checked', {"id": row.data('id')});
					self.registerPostLoadListViewActions();
				});
				if (self.getSelectAllMode() == true) {
					
					self.markSelectedIdsCheckboxes();
					
				} else {
					// If it is not select all mode then only do this
					self.showSelectAll();
				}
			}
			else {
				if (self.getSelectAllMode() == true) {
					
					self.showDeSelectAllMsgDiv();
				}
				else {
					self.deSelectAllWithNoMessage();
				}
				jQuery('.relatedlistViewEntriesCheckBox').each(function (e) {
					jQuery(this).prop('checked', false);
					var row = jQuery(this).closest('.listViewEntries');
					row.trigger('Post.ListRow.UnChecked', {"id": row.data('id')});
					self.registerPostLoadListViewActions();
				});
			}
		});
	},
	
	registerPostLoadListViewActions: function () {
		var self = this;
		var recordSelectTrackerObj = self.getRecordSelectTrackerInstance();

		var selectedIds = recordSelectTrackerObj.getSelectedIds();

		if (selectedIds != '') {
			self.enableListViewActions();
		} else {
			self.disableListViewActions();
		}

	},
	enableListViewActions: function () {
		jQuery('.btn-group.relatedlistViewActionsContainer').find('button').removeAttr('disabled');
		jQuery('.btn-group.relatedlistViewActionsContainer').find('li').removeClass('hide');
	},
	disableListViewActions: function () {
		jQuery('.btn-group.relatedlistViewActionsContainer').find('button').attr('disabled', "disabled");
		jQuery('.btn-group.relatedlistViewActionsContainer').find('.dropdown-toggle').removeAttr("disabled");
		jQuery('.btn-group.relatedlistViewActionsContainer').find('li').addClass('hide');
		var selectFreeRecords = jQuery('.btn-group.relatedlistViewActionsContainer').find('li.selectFreeRecords');
		selectFreeRecords.removeClass('hide');
		if (selectFreeRecords.length == 0) {
			jQuery('.btn-group.relatedlistViewActionsContainer').find('.dropdown-toggle').attr('disabled', "disabled");
		}
	},
	
	recordSelectTrackerInstance: false,
	
	getRecordSelectTrackerInstance: function () {
		if (this.recordSelectTrackerInstance === false) {
			
			this.recordSelectTrackerInstance = Vtiger_RecordSelectTracker_Js.getInstance();
			this.recordSelectTrackerInstance.setCvId(this.getCurrentCvId());
		} else {
			
			this.recordSelectTrackerInstance.setCvId(this.getCurrentCvId());
		}
		return this.recordSelectTrackerInstance;
	},
	
	getCurrentCvId: function () {
		var relatedViewContainer = this.detailViewContainer;
		return relatedViewContainer.find('[name="cvid"]').val();
	},
	
	registerCheckBoxClickEvent: function () {
		var self = this;
		var relatedViewContainer = this.detailViewContainer;
		var recordSelectTrackerInstance = self.getRecordSelectTrackerInstance();

		relatedViewContainer.on('click', '.relatedlistViewEntriesCheckBox', function (e) {
			var element = relatedViewContainer.find(e.currentTarget);
			var row = element.closest('.listViewEntries');

			if (element.is(':checked')) {
				row.trigger('Post.ListRow.Checked', {"id": row.data('id')});
			
				self.registerPostLoadListViewActions();
				if (recordSelectTrackerInstance.selectAllMode) {
					var excludedIds = recordSelectTrackerInstance.getExcludedIds();
					if (self.checkIdsAreEmpty(excludedIds)) {
						relatedViewContainer.find('.relatedlistViewEntriesMainCheckBox').prop("checked", true);
					}
				}
			} else {
				row.trigger('Post.ListRow.UnChecked', {"id": row.data('id')});
				self.registerPostLoadListViewActions();
				if (recordSelectTrackerInstance.selectAllMode) {
					relatedViewContainer.find('.relatedlistViewEntriesMainCheckBox').prop("checked", false)
				}
			}
			if (relatedViewContainer.find('.relatedlistViewEntriesCheckBox').not(":checked").length == 0) {
				relatedViewContainer.find('.relatedlistViewEntriesMainCheckBox').prop("checked", true)
			} else {
				relatedViewContainer.find('.relatedlistViewEntriesMainCheckBox').prop("checked", false)
			}
		});

	},
	
	getSelectAllMode: function () {
		var recordTracker = this.getRecordSelectTrackerInstance();
		return recordTracker.getSelectAllMode();
	},
	
	showSelectAll: function () {
		var self = this;
		app.helper.showProgress();
		var params = this.getPageJumpParams();
		app.request.post(params).then(
			function(err, data) {
			self.showSelectAllMsgDiv();
			jQuery('#totalRecordsCount').text(data['numberOfRecords']);
			app.helper.hideProgress();
		})
	},
	
	showSelectAllMsgDiv: function () {
		jQuery("#deSelectAllMsgDiv").closest('div.messageContainer').removeClass('show');
		jQuery("#deSelectAllMsgDiv").closest('div.messageContainer').addClass('hide');
		jQuery("#selectAllMsgDiv").closest('div.messageContainer').addClass("show");
	},
	showDeSelectAllMsgDiv: function () {
		jQuery('#selectAllMsgDiv').closest('div.messageContainer').removeClass("show");
		jQuery('#selectAllMsgDiv').closest('div.messageContainer').addClass("hide");
		jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').addClass('show');
	},
	deSelectAllWithNoMessage: function () {
		jQuery('#selectAllMsgDiv').closest('div.messageContainer').removeClass("show");
		jQuery('#selectAllMsgDiv').closest('div.messageContainer').addClass("hide");
		jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').removeClass("show");
		jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').addClass("hide");
	},
	
	registerSelectAllClickEvent: function () {
		var self = this;
		var relatedListContainer = this.detailViewContainer;
		relatedListContainer.on('click', '#selectAllMsgDiv', function (e) {
			self.showDeSelectAllMsgDiv();
			var cvId = self.getCurrentCvId();
			relatedListContainer.trigger('Post.ListSelectAll', {"mode": true, "cvId": cvId});
		});
		self.markSelectedIdsCheckboxes();
	},
	
	registerDeSelectAllClickEvent: function () {
		var self = this;
		var relatedListContainer = this.detailViewContainer;
		relatedListContainer.on('click', '#deSelectAllMsgDiv', function (e) {
			
			jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').removeClass('show');
			jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').addClass("hide");
			relatedListContainer.trigger('Post.ListDeSelectAll', {"mode": false});
			self.registerPostLoadListViewActions();
			jQuery('.relatedlistViewEntriesMainCheckBox').prop('checked', false);
			jQuery('.relatedlistViewEntriesCheckBox').each(function (e) {
				jQuery(this).prop('checked', false);
			});
		});
	},
	
	checkIdsAreEmpty: function (val) {
		return (val == undefined || val == null || val.length <= 0) ? true : false;
	},
	
	markSelectedIdsCheckboxes: function () {
		var self = this;

		var recordSelectTrackerObj = self.getRecordSelectTrackerInstance();
		var selectAllMode = recordSelectTrackerObj.getSelectAllMode();

		var excludedIds = recordSelectTrackerObj.getExcludedIds();
		var excludedIdsAreEmpty = self.checkIdsAreEmpty(excludedIds);
		var selectedIds = recordSelectTrackerObj.getSelectedIds();

		var currentViewId = self.getCurrentCvId();
		var recordTackerCvId = recordSelectTrackerObj.getCvid();
		var rows = jQuery('tr.listViewEntries');
		
		if (selectAllMode == true) {
			jQuery('#deSelectAllMsgDiv').closest('div.messageContainer').addClass('show');
			//jQuery(".listViewEntriesMainCheckBox").prop('checked', true);
			
			if (excludedIdsAreEmpty) {
				rows.each(function (i, elem) {
					jQuery(elem).find(".relatedlistViewEntriesCheckBox").prop('checked', true);
				});
			}
			else {
				rows.each(function (i, elem) {
					var rowId = $(elem).data('id');
					jQuery(elem).find('.relatedlistViewEntriesCheckBox').prop('checked', true);

					for (var j = 0; j < excludedIds.length; j++) {
						var excludedRecordIdValue = excludedIds[j];
						if (excludedRecordIdValue == rowId) {
							jQuery('.relatedlistViewEntriesCheckBox[value="' + excludedRecordIdValue + '"]').prop('checked', false);
							jQuery(".relatedlistViewEntriesMainCheckBox").prop('checked', false);
						}
					}
				});
			}
		} else {
			var isEmpty = self.checkIdsAreEmpty(selectedIds);
			if (!isEmpty) {
				rows.each(function (i, elem) {
					var rowId = $(elem).data('id');
					for (var j = 0; j < selectedIds.length; j++) {
						var selectedRecordIdValue = selectedIds[j];
						if (selectedRecordIdValue == rowId) {
							jQuery('.relatedlistViewEntriesCheckBox[value="' + selectedRecordIdValue + '"]').prop('checked', true);
						}
					}
				});
				var relatedListContainer = this.detailViewContainer;
				if (relatedListContainer.find('.relatedlistViewEntriesCheckBox').not(":checked").length == 0) {
					relatedListContainer.find('.relatedlistViewEntriesMainCheckBox').prop("checked", true)
				} else {
					relatedListContainer.find('.relatedlistViewEntriesMainCheckBox').prop("checked", false)
				}
			}
		}
	},
	
	getSelectedRecordCount: function () {
		var count = 0;
		var selectedRecords = this.readSelectedIds();
		if (selectedRecords) {
			if (selectedRecords != 'all') {
				count = selectedRecords.length;
			} else {
				var excludedIdsCount = this.readExcludedIds().length;
				var totalRecords = jQuery('#totalRecordsCount').text();
				count = totalRecords - excludedIdsCount;
			}
		}

		return count;
	},
	
	readSelectedIds: function (jsonDecode) {
		var recordTracker = this.getRecordSelectTrackerInstance();
		var selectedIds = recordTracker.getSelectedIds();
		if (jsonDecode) {
			if (typeof selectedIds == 'object') {
				return JSON.stringify(selectedIds);
			}
		}
		return selectedIds;
	},
	
	readExcludedIds: function (jsonDecode) {
		var recordTracker = this.getRecordSelectTrackerInstance();
		var excludedIds = recordTracker.getExcludedIds();
		if (jsonDecode) {
			return JSON.stringify(excludedIds);
		}
		return excludedIds;
	},
	
	noRecordSelectedAlert: function () {
		return app.helper.showAlertBox({message: app.vtranslate('JS_PLEASE_SELECT_ONE_RECORD')});
	},
	
	getListSelectAllParams: function (jsonDecode) {
		
		var self = this;
		var detailInstance = Vtiger_Detail_Js.getInstance();
		var recordSelectTrackerInstance = self.getRecordSelectTrackerInstance();
		var params = recordSelectTrackerInstance.getSelectedAndExcludedIds(jsonDecode);
		if(!params){
			params={};
		}
		params.search_params = JSON.stringify(detailInstance.getRelatedListSearchParams());
		params.parentRecord = app.getRecordId();
		params.parentModule = app.getModuleName();
		
		return params;
	},
	
	clearList: function () {
		var recordSelectTracker = this.getRecordSelectTrackerInstance();
		recordSelectTracker.clearList();
	},
	
	performMassDeleteRecords: function (url) {
		
		var listInstance = this;
		var params = {};
		var paramArray = url.slice(url.indexOf('?') + 1).split('&');
		for (var i = 0; i < paramArray.length; i++) {
			var param = paramArray[i].split('=');
			params[param[0]] = param[1];
		}
		var listSelectParams = listInstance.getListSelectAllParams(true);
		listSelectParams = jQuery.extend(listSelectParams, params);
		
		if (listSelectParams) {
			var message = app.vtranslate('Are You Sure Want to delete the selected records!');
			app.helper.showPromptBox({'message': message}).then(function (e) {
				listSelectParams['action'] = 'RelatedMassDelete';
				
				app.helper.showProgress();
				app.request.post({data: listSelectParams}).then(
					function (error, result) {
						app.helper.hideProgress();
						if (error) {
							app.helper.showErrorNotification();
							return;
						}
						listInstance.clearList();
						var params = listInstance.getPageJumpParams();
						app.request.post(params).then(function(err, data) {
							
							var pageCount = parseInt(data.page);
							var container =  listInstance.detailViewContainer;
							var currentPageElement = container.find('#pageNumber');
							var currentPageNumber = parseInt(currentPageElement.val());
							var params = {};
							listInstance.loadRelatedList(params);
						});
					}
				);
			});
		}
		else {
			listInstance.noRecordSelectedAlert();
		}
	},
	performExportAction: function (url) {
		var listInstance = this;
		
		var listViewContainer = this.detailViewContainer;
		var pageNumber = listViewContainer.find('#pageNumber').val();
		var postData = listInstance.getCompleteParams();

		var params = app.convertUrlToDataParams(url);
		postData = jQuery.extend(postData, params);
		var listSelectAllParams = listInstance.getListSelectAllParams(true);
		
		postData = jQuery.extend(postData, listSelectAllParams);
		
		app.helper.showProgress();
		app.request.get({data: postData}).then(function (error, data) {
			app.helper.loadPageContentOverlay(data).then(function (container) {
				container.find('form#exportForm').on('submit', function () {
					jQuery(this).find('button[type="submit"]').attr('disabled', 'disabled');
					app.helper.hidePageContentOverlay();
				});
			});
			app.helper.hideProgress();
		});
	},
	
	
	registerRelatedListViewBasicActions: function () {
		var thisInstance = this;
		var listViewContainer = this.detailViewContainer;

		var isOwnerChanged = false;
		app.event.on('post.relatedlistViewMassEdit.loaded', function (e, container) {
			
			var offset = container.find('.modal-body .datacontent').offset();
			var viewPortHeight = $(window).height() - 60;

			var params = {
				setHeight: (viewPortHeight - offset['top']) + 'px'
			};

			container.find('[name="assigned_user_id"]').on('click', function() {
				isOwnerChanged = true;
			});
			app.helper.showVerticalScroll(container.find('.modal-body .datacontent'), params);
			var editInstance = Vtiger_Edit_Js.getInstance();
			editInstance.registerBasicEvents(container);
			var form_original_data = $("#massEdit").serialize();
			
			$('#massEdit').on('submit', function (event) {
				thisInstance.saveMassedit(event, form_original_data, isOwnerChanged);
				isOwnerChanged = false;
			});
			app.helper.registerLeavePageWithoutSubmit($("#massEdit"));
			app.helper.registerModalDismissWithoutSubmit($("#massEdit"));
		});

		app.event.on('post.relatedlistViewMassAction.loaded', function (e, container) {
			
			jQuery('#phoneFormatWarningPop').popover();
			jQuery('#massSave').vtValidate({
				// Note : JQuery Validator is not working with multi file upload fields
				ignore: "input[type='file'].multi",
				submitHandler: function (form) {
					var domForm = jQuery(form);
					var formData = jQuery(form).serializeFormData();

					var formData = new FormData(domForm[0]);
					if (Vtiger_Index_Js.files) {
						formData.append("filename", Vtiger_Index_Js.files);
						delete Vtiger_Index_Js.files;
					}
					var params = {
						url: "index.php",
						type: "POST",
						data: formData,
						processData: false,
						contentType: false
					};

					app.helper.showProgress();
					app.request.post(params).then(function (err, data) {
						app.helper.hideProgress();
						app.helper.hideModal();
						if (jQuery(form).find('[name="module"]').val() == 'SMSNotifier') {
							var statusDetails = data.statusdetails;
							var status = statusDetails.status;
							if (status == 'Failed') {
								var errorMsg = statusDetails.statusmessage + '<br>' + app.vtranslate('JS_PHONEFORMAT_ERROR');
								app.helper.showErrorNotification({'title': status, 'message': errorMsg});
							} else {
								var msg = statusDetails.statusmessage;
								app.helper.showSuccessNotification({'title': status, 'message': msg});
							}
						}
						app.event.trigger('post.relatedlistViewMassEditSave');
						if (err) {
							return;
						}
					});
					return false;
				}
			});
		});
	},
	
	saveMassedit: function (event, form_original_data, isOwnerChanged) {
		event.preventDefault();
		var form = $('#massEdit');
		var form_new_data = form.serialize();
		app.helper.showProgress();
		if (form_new_data !== form_original_data || isOwnerChanged) {
			var originalData = app.convertUrlToDataParams(form_original_data);
			var newData = app.convertUrlToDataParams(form_new_data);

			for (var key in originalData) {
				if ((form.find('[name="' + key + '"]').is("select")
						|| form.find('[name="' + key + '"]').is("input[type='checkbox']"))
						&& (originalData[key] == newData[key])) {
					delete newData[key];
				}
			}

			if (!newData['assigned_user_id'] && isOwnerChanged) {
				newData['assigned_user_id'] = originalData['assigned_user_id'];
			}

			var form_update_data = '';
			for (var key in newData) {
				form_update_data += key + '=' + newData[key] + '&';
			}
			form_update_data = form_update_data.slice(0, -1);
			app.request.post({data: form_update_data}).then(function (err, data) {
				app.helper.hideProgress();
				if (data) {
					jQuery('.vt-notification').remove();
					app.helper.hidePageContentOverlay();
					window.onbeforeunload = null;
					app.event.trigger('post.relatedlistViewMassEditSave');
				} else {
					app.event.trigger('post.save.failed', err);
				}
			});
		} else {
			app.helper.hideProgress();
			app.helper.showAlertBox({'message': app.vtranslate('NONE_OF_THE_FIELD_VALUES_ARE_CHANGED_IN_MASS_EDIT')});
		}
	},
	
	registerEvents :function(){
		  var thisInstance = this;
		  thisInstance.registerRelatedListViewMainCheckBoxClickEvent();
          thisInstance.registerCheckBoxClickEvent();
      		
          thisInstance.registerSelectAllClickEvent();
          thisInstance.registerDeSelectAllClickEvent();
          thisInstance.registerPostLoadListViewActions();
      		
          thisInstance.registerRelatedListViewBasicActions();
          
          var recordSelectTrackerObj = thisInstance.getRecordSelectTrackerInstance();
          recordSelectTrackerObj.registerEvents();
          
          app.event.on('post.relatedlistViewMassEditSave', function () {
        	  var urlParams = {};
        	  thisInstance.loadRelatedList(urlParams);
        	  thisInstance.clearList();
          });
      		
	},
	
	relatedtriggerMassEdit: function (url) {
		
		var self = this;
		var listViewContainer = self.detailViewContainer;
		
		listViewContainer.on('click','.relatededit',function(){
			
			url = $(this).attr('href');
			
    		var selectedRecordCount = self.getSelectedRecordCount();
    		if (selectedRecordCount > 500) {
    			app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
    			return;
    		}
    		app.event.trigger('post.relatedlistViewMassEdit.click', url);
    		
    		var params = self.getListSelectAllParams(true);
    		
    		if (params) {
    			app.helper.showProgress();
    			app.request.get({url: url, data: params}).then(function (error, data) {
    				var overlayParams = {'backdrop': 'static', 'keyboard': false};
    				app.helper.loadPageContentOverlay(data, overlayParams).then(function (container) {
    					app.event.trigger('post.relatedlistViewMassEdit.loaded', container);
    					
    				})
    				
    				app.helper.hideProgress();
    			});
    		}
    		else {
    			self.noRecordSelectedAlert();
    		}
    		
		});
	},
	
	relatedtriggerMassAction: function (massActionUrl) {
		
		var self = this;
		var listViewContainer = self.detailViewContainer;
		
		listViewContainer.on('click','.relatedcomment',function(){
			
			massActionUrl = $(this).attr('href');
			
			var relatedlistSelectParams = self.getListSelectAllParams(true);
    		if (relatedlistSelectParams) {
    			var postData = {};
    			var data = app.convertUrlToDataParams(massActionUrl);
    			postData = jQuery.extend(postData, data);
    			postData = jQuery.extend(postData, relatedlistSelectParams);
    			
    			app.helper.showProgress();
    			app.request.get({'data': postData}).then(
    					function (err, data) {
    						app.helper.hideProgress();
    						if (data) {
    							app.helper.showModal(data, {'cb': function (modal) {
    									if (postData.mode === "showAddCommentForm") {
    										var vtigerInstance = Vtiger_Index_Js.getInstance();
    										vtigerInstance.registerMultiUpload();
    									}
    									app.event.trigger('post.relatedlistViewMassAction.loaded', modal);
    									//app.event.trigger('post.listViewMassEditSave');
    								}
    							});
    						}
    					}
    			);
    		} else {
    			self.noRecordSelectedAlert();
    		}
    		
		});
	},
	
	relatedmassDeleteRecords: function (url, instance) {
		
		var self = this;
		
		var listViewContainer = self.detailViewContainer;
		
		listViewContainer.on('click','.relateddelete',function(){
			
			url = $(this).attr('href');
			self.performMassDeleteRecords(url);
		
		});
		
	},
	
	relatedtriggerExportAction: function (exportActionUrl) {
		
		var self = this;
		
		var listViewContainer = self.detailViewContainer;
		
		listViewContainer.on('click','.relatedexport',function(){
			
			exportActionUrl = $(this).attr('href');
			self.performExportAction(exportActionUrl);
		
		});
		
	},
	
	checkListRecordSelected: function () {
		var selectedIds = this.readSelectedIds();
		if (typeof selectedIds == 'object' && selectedIds.length <= 0) {
			return true;
		}
		return false;
	},
	
	relatedtriggerExportZipAction: function (exportzipActionUrl) {
		
		var self = this;
		
		var listViewContainer = self.detailViewContainer;
		
		listViewContainer.on('click','.relatedexportzip',function(){
			
			exportzipActionUrl = $(this).attr('href');
			self.exportZip(exportzipActionUrl);
		
		});
		
	},
	
	exportZip : function(url) {
        var self = this;
        var listInstance = self;
		var validationResult = listInstance.checkListRecordSelected();
		if(!validationResult){
			
            var listSelectAllParams = listInstance.getListSelectAllParams(true);
            
			var params = {
				"url":url,
				"data" : listSelectAllParams
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                            self.registerExportFileModalEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			self.noRecordSelectedAlert();
		}
    },
    
    registerExportFileModalEvents : function(container) {
        var self = this;
        var addFolderForm = jQuery('#exportFiles');
        addFolderForm.vtValidate({
            submitHandler: function(form) {
            	
                var formData = addFolderForm.serializeFormData();
                app.helper.showProgress();
                form.submit();
				app.helper.hideModal();
				app.helper.hideProgress();
					
            }
        });
    },
    
    registerEventForSendEnvelopeRelatedList : function(){
		var thisInstance = this;
		$(document).on('click', '.sendenveloperelated', function(){
			
			var selectedRecordCount = thisInstance.getSelectedRecordCount();
			if (!selectedRecordCount) {
				app.helper.showAlertBox({message: app.vtranslate('JS_PLEASE_SELECT_ONE_RECORD')});
    			return;
    		}

			if (selectedRecordCount > 500) {
    			app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
    			return;
    		}
			
			var params = thisInstance.getListSelectAllParams();
			params['module'] = 'DocuSign';
			params['view'] = 'MassActionAjax';
			params['mode'] = 'showSendEmailFromRelated';
			params['src_module'] = 'Contacts';
			
			app.helper.showProgress();
			
			app.request.post({'data': params}).then(
				function (err, data) {
					app.helper.hideProgress();
					if (data) {
						app.helper.showModal(data, {'cb': function (modal) {
							var docusignForm = jQuery('#sendEnvelope');
							if(docusignForm.length){
								 
								var noteContentElement = docusignForm.find('[name="envelope_content"]');
								if(noteContentElement.length > 0){
									noteContentElement.addClass('ckEditorSource');
									var ckEditorInstance = new Vtiger_CkEditor_Js();
									ckEditorInstance.loadCkEditor(noteContentElement);
								}
								thisInstance.registerTemplateChangeEvent(docusignForm);	
								thisInstance.registerFillMailContentEvent(docusignForm);
								docusignForm.vtValidate({
									submitHandler: function (form) {
										thisInstance.sendEmailToRecords(jQuery(form));
										return false;
									}
								});
							}
							}
						});
					}
				}
			);
		});
	},
	
	registerFillMailContentEvent: function (docusignForm) {
		docusignForm.on('change', '#selected_contacts', function (e) {
			var textarea = CKEDITOR.instances.envelope_content;
			var value = jQuery(e.currentTarget).val();
			if (textarea != undefined) {
				textarea.insertHtml(value);
			} else if (jQuery('textarea[name="envelope_content"]')) {
				var textArea = jQuery('textarea[name="envelope_content"]');
				textArea.insertAtCaret(value);
			}
		});
	},
	
	registerTemplateChangeEvent : function(docusignForm){
		
		docusignForm.on('change', '#templateid', function(){
			app.helper.showProgress();
			var data = new FormData(docusignForm[0]);
			
			jQuery.each(data, function (key, value) {
				data.append(key, value);
			});
			data.append('mode', 'getEmailContent');
			
			var postData = { 
				'url': 'index.php', 
				'type': 'POST', 
				'data': data, 
				processData: false, 
				contentType: false 
			};
			app.request.post(postData).then(function(err, data){
				if (err == null) {
					CKEDITOR.instances.envelope_content.setData(data);
				}
				app.helper.hideProgress();
			});
		});
		
	},
	
	sendEmailToRecords :function(form){
		var thisInstance = this;
		var formData = form.serializeFormData();
		formData['mode'] = 'SendEmail';
		
		var data = new FormData(form[0]);
		
		jQuery.each(data, function (key, value) {
			data.append(key, value);
		});
		data.append('mode', 'SendEmail');
		data.append('envelope_content', CKEDITOR.instances.envelope_content.getData());
		
		var postData = { 
			'url': 'index.php', 
			'type': 'POST', 
			'data': data, 
			processData: false, 
			contentType: false 
		};
		
		app.helper.showProgress();
		app.request.post(postData).then(function (err, data) {
			
			app.helper.hideProgress();
			
			if (err == null) {
				
				if(data.success){
					app.helper.hideModal();
					var urlParams = {};
	            	thisInstance.loadRelatedList(urlParams);
	            	thisInstance.clearList();
	            	app.helper.showSuccessNotification({message: 'Message Sent Successfully'});
				} else {
					app.helper.showErrorNotification({message: app.vtranslate(data.message)})
				}
			} 
			
		});
	
    },
    
    registerEventForSendEmailRelatedList : function(){
		var thisInstance = this;
		$(document).on('click', '.relatedEmail', function(){
			
			var selectedRecordCount = thisInstance.getSelectedRecordCount();
			if (!selectedRecordCount) {
				app.helper.showAlertBox({message: app.vtranslate('JS_PLEASE_SELECT_ONE_RECORD')});
    			return;
    		}

			if (selectedRecordCount > 500) {
    			app.helper.showErrorNotification({message: app.vtranslate('JS_MASS_EDIT_LIMIT')});
    			return;
    		}
			
			var massActionUrl = $(this).attr('href');
			
			var postData =  thisInstance.getListSelectAllParams();
			
			delete postData.module;
			delete postData.view;
			delete postData.parent;
			
			var data = app.convertUrlToDataParams(massActionUrl);
			jQuery.extend(postData, data);
			
			Vtiger_Index_Js.showComposeEmailPopup(postData);
			
		});
	},
    
})

jQuery(document).ready(function(){
	var recordId = app.getRecordId();
	var moduleName = app.getModuleName();
    var detailViewInstance = Vtiger_Detail_Js.getInstance();
    var selectedTabElement = detailViewInstance.getSelectedTab();
    var relatedModuleName = detailViewInstance.getRelatedModuleName();
    var instance = Vtiger_RelatedList_Js.getInstance(recordId, moduleName, selectedTabElement, relatedModuleName);
	
	instance.initializePaginationEvents();
	instance.relatedtriggerMassEdit();
	instance.relatedtriggerMassAction();
	instance.relatedmassDeleteRecords();
	instance.relatedtriggerExportAction();
	instance.relatedtriggerExportZipAction();
	instance.registerEventForSendEnvelopeRelatedList();
	instance.registerEventForSendEmailRelatedList();
});