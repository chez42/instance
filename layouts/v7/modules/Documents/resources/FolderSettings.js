/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger.Class('Documents_FolderSettings_Js', {

	fileObj : false,
	referenceCreateMode : false,
	referenceFieldName : '',

	getInstance : function() {
		return new Documents_FolderSettings_Js();
	},
	
	
	registerSettingsRowDoubleClickEvent: function(id){
		var instance = Documents_FolderSettings_Js.getInstance();
		instance.registerSettingsRowDoubleClickEvent(id);
	},
	
	registerFolderInlineSave: function(id){
		var instance = Documents_FolderSettings_Js.getInstance();
		instance.registerFolderInlineSave(id);
	},
	
	registerFolderInlineCancel: function(id){
		var instance = Documents_FolderSettings_Js.getInstance();
		instance.registerFolderInlineCancel(id);
	},
	
	registerDeleteFolderEvent: function(id){
		var instance = Documents_FolderSettings_Js.getInstance();
		instance.registerDeleteFolderEvent(id);
	},

}, {

	
	/*
	 * Function to register the list view row double click event
	 */
	registerSettingsRowDoubleClickEvent: function (id) {
		var thisInstance = this;
		
		var listViewContentDiv =  jQuery('.documentsSettingsContainer');
		
		listViewContentDiv.on('dblclick', '.folderListViewEntries', function (e) {
			var currentele = jQuery(e.currentTarget);
			
			if(currentele.length){
				
				currentele.find('.permissions').addClass('hide');
				currentele.find('.title').addClass('hide');
				currentele.find('.hide_from_portal').addClass('hide');
				currentele.find('.default_for_all_users').addClass('hide');
				currentele.find('.deleteBtn').addClass('hide');
				currentele.find('.edit').removeClass('hide');
				currentele.find('.inline-save').removeClass('hide');
				
			}
			
		});
		
	},
	
	registerFolderInlineSave: function(id){
		
		var listViewContentDiv =  jQuery('.documentsSettingsContainer');
		
		var element = jQuery(event.currentTarget).closest('.folderListViewEntries');
		
		var folderName = element.find('[name="folderName"]').val();
		
		if(!folderName){
			app.helper.showErrorNotification({message: 'Folder name cant be empty'});
			return false;
		}
		
		var permission_ids = element.find('[name="view_permissions[]"]').val();
		
		if(element.find('[name="hide_from_portal"]'). prop("checked"))
			var portalValue = 1;
		else
			var portalValue = 0;
		
		if(element.find('[name="default_for_all_users"]'). prop("checked"))
			var defaultForUsers = 1;
		else
			var defaultForUsers = 0;
		
		var params = {
				'module' : 'DocumentFolder',
				'record' : id,
				'view_permissions' : permission_ids,
				'action' : 'ViewPermissions',
				'portalvalue' : portalValue,
				'default_for_all_users' : defaultForUsers,
				'foderName' : folderName,
		};
		
		app.helper.showProgress();
		
		app.request.post({'data':params}).then(function(e,res) {
			
			app.helper.hideProgress();
			
			if(res.success){
				
				element.find('.title').removeClass('hide');
				element.find('.permissions').removeClass('hide');
				element.find('.hide_from_portal').removeClass('hide');
				element.find('.default_for_all_users').removeClass('hide');
				element.find('.deleteBtn').removeClass('hide');
				element.find('.edit').addClass('hide');
				element.find('.inline-save').addClass('hide');
				element.find('.permissions').find('.value').text(res.users);
				element.find('.hide_from_portal').find('.value').text(res.portal);
				element.find('.default_for_all_users').find('.value').text(res.defaultForUsers);
				element.find('.title').find('.value').text(res.folderName);
			
			}else{
				
				app.helper.showErrorNotification({message: res.message});
				
			}
			
		});
		
	},
	
	registerFolderInlineCancel: function(id){
		
		var element = jQuery(event.currentTarget).closest('.folderListViewEntries');
		
		element.find('.title').removeClass('hide');
		element.find('.permissions').removeClass('hide');
		element.find('.hide_from_portal').removeClass('hide');
		element.find('.default_for_all_users').removeClass('hide');
		element.find('.deleteBtn').removeClass('hide');
		element.find('.edit').addClass('hide');
		element.find('.inline-save').addClass('hide');
		
	},
	
	getFolderDefaultParams: function () {
		
		var container =  jQuery('.documentsSettingsContainer');
		var pageNumber = container.find('#pageNumber').val();
		var module = 'Documents';
		var parent = '';
		var cvId = container.find('[name="cvid"]').val();
		
		var params = {
			'module': module,
			'parent': parent,
			'page': pageNumber,
			'view': "ListAjax",
			'viewname': cvId,
			'mode':'settings'
		}
		
		return params;
	},
	
	getFolderPageCount: function () {
		var aDeferred = jQuery.Deferred();
		var pageCountParams = this.getFolderDefaultParams();
		var params = {
			"type": "GET",
			"data": pageCountParams
		}
		params['view'] = "ListAjax";
		params['mode'] = "getPageCount";

		app.request.get(params).then(
				function (err, data) {
					var response;
					if (typeof data !== "object") {
						response = JSON.parse(data);
					} else {
						response = data;
					}
					aDeferred.resolve(response);
				}
		);
		return aDeferred.promise();
	},
	
	pageJump : function() {
		var thisInstance = this;
		var folderListContainer = jQuery('.documentsSettingsContainer');
		var element = folderListContainer.find('#totalPageCount');
		var totalPageNumber = element.text();
		var pageCount;
		
		if(totalPageNumber === ""){
			var totalCountElem = folderListContainer.find('#totalCount');
			var totalRecordCount = totalCountElem.val();
			if(totalRecordCount !== '') {
				var recordPerPage = folderListContainer.find('#pageLimit').val();
				if(recordPerPage === '0') recordPerPage = 1;
				pageCount = Math.ceil(totalRecordCount/recordPerPage);
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
				return;
			}

			thisInstance.getFolderPageCount().then(function(data){
				var pageCount = data.page;
				totalCountElem.val(data.numberOfRecords);
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
			});
		}
	},
	
	pageJumpOnSubmit : function(element) {
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var folderListContainer = jQuery('.documentsSettingsContainer');
		var currentPageElement = jQuery('#pageNumber', folderListContainer);
		var currentPageNumber = parseInt(currentPageElement.val());
		var newPageNumber = parseInt(jQuery('#pageToJump',folderListContainer).val());
		var totalPages = parseInt(jQuery('#totalPageCount', folderListContainer).text());
		
		if(newPageNumber > totalPages){
			var message = app.vtranslate('JS_PAGE_NOT_EXIST');
			app.helper.showErrorNotification({'message':message})
			return aDeferred.reject();
		}

		if(newPageNumber === currentPageNumber){
			var message = app.vtranslate('JS_YOU_ARE_IN_PAGE_NUMBER')+" "+newPageNumber;
			app.helper.showAlertNotification({'message': message});
			return aDeferred.reject();
		}
		
		var urlParams = thisInstance.getFolderDefaultParams();
		urlParams['page'] = newPageNumber;
		this.getPageRecords(urlParams).then(
			function(data){
				jQuery('.btn-group', folderListContainer).removeClass('open');
				jQuery('#pageNumber',folderListContainer).val(newPageNumber);
				aDeferred.resolve(data);
			}
		);
		return aDeferred.promise();
	},
	
	/**
	 * Function to get Page Records
	 */
	getPageRecords : function(params){
		var aDeferred = jQuery.Deferred();
                app.helper.showProgress();
		Vtiger_BaseList_Js.getPageRecords(params).then(
            function(data){
                jQuery('.documentsSettingsContainer').html(data);
                vtUtils.applyFieldElementsView(jQuery('.documentsSettingsContainer'));
                aDeferred.resolve(data);
            }
        );
		return aDeferred.promise();
	},
	
	totalNumOfRecords : function (currentEle) {
		var thisInstance = this;
		var folderListContainer = jQuery('.documentsSettingsContainer');
		var totalRecordsElement = folderListContainer.find('#totalCount');
		var totalNumberOfRecords = totalRecordsElement.val();
		currentEle.addClass('hide');

		if(totalNumberOfRecords === '') {
			thisInstance.getFolderPageCount().then(function(data){
				totalNumberOfRecords = data.numberOfRecords;
				totalRecordsElement.val(totalNumberOfRecords);
				folderListContainer.find('ul#listViewPageJumpDropDown #totalPageCount').text(data.page);
				thisInstance.showPagingInfo();
			});
		}else{
			thisInstance.showPagingInfo();
		}
	},
	
	showPagingInfo : function(){
		var thisInstance = this;
		var folderListContainer = jQuery('.documentsSettingsContainer');
		var totalNumberOfRecords = jQuery('#totalCount', folderListContainer).val();
		var pageNumberElement = jQuery('.pageNumbersText', folderListContainer);
		var pageRange = pageNumberElement.text();
		var newPagingInfo = pageRange.trim()+" "+app.vtranslate('of')+" "+totalNumberOfRecords;
		var listViewEntriesCount = parseInt(jQuery('#noOfEntries', folderListContainer).val());
		
		if(listViewEntriesCount !== 0){
			jQuery('.pageNumbersText', folderListContainer).html(newPagingInfo);
		} else {
			jQuery('.pageNumbersText', folderListContainer).html("");
		}
	},
	
	
	initializeFolderPaginationEvents : function() {
		var thisInstance = this;
		var paginationObj = new Vtiger_Pagination_Js;
		var folderListContainer = jQuery('.documentsSettingsContainer');
		paginationObj.initialize(folderListContainer);
		
		app.event.on(paginationObj.nextPageButtonClickEventName, function(){
			thisInstance.nextPageHandler().then(function(data){
				var pageNumber = folderListContainer.find('#pageNumber').val();
				folderListContainer.find('#pageToJump').val(pageNumber);
				thisInstance.updatePagination();
			});
		});
		
		app.event.on(paginationObj.previousPageButtonClickEventName, function(){
			thisInstance.previousPageHandler().then(function(data){
				var pageNumber = folderListContainer.find('#pageNumber').val();
				folderListContainer.find('#pageToJump').val(pageNumber);
				thisInstance.updatePagination();
			});
		});
		
		app.event.on(paginationObj.pageJumpButtonClickEventName, function(event, currentEle){
			thisInstance.pageJump();
		});
		
		app.event.on(paginationObj.totalNumOfRecordsButtonClickEventName, function(event, currentEle){
			thisInstance.totalNumOfRecords(currentEle);
		});
		
		app.event.on(paginationObj.pageJumpSubmitButtonClickEvent, function(event, currentEle){
			thisInstance.pageJumpOnSubmit().then(function(data){
				thisInstance.updatePagination();
			});
		});
	},
	
	updatePagination : function(){
        var folderListContainer = jQuery('.documentsSettingsContainer');
        app.helper.hideProgress();
		var previousPageExist = jQuery('#previousPageExist',folderListContainer).val();
		var nextPageExist = jQuery('#nextPageExist',folderListContainer).val();
		var previousPageButton = jQuery('#PreviousPageButton',folderListContainer);
		var nextPageButton = jQuery('#NextPageButton',folderListContainer);
		var listViewEntriesCount = jQuery('#noOfEntries',folderListContainer).val();
		var pageStartRange = jQuery('#pageStartRange',folderListContainer).val();
		var pageEndRange = jQuery('#pageEndRange',folderListContainer).val();
		var totalNumberOfRecords = jQuery('.totalNumberOfRecords',folderListContainer);
		var pageNumbersTextElem = jQuery('.pageNumbersText',folderListContainer);         
		
        if(previousPageExist !== ""){
			previousPageButton.removeClass('disabled');
		} else if(previousPageExist === "") {
			previousPageButton.addClass('disabled');
		}

		if((nextPageExist !== "")){
			nextPageButton.removeClass('disabled');
		} else if((nextPageExist === "")) {
			nextPageButton.addClass('disabled');
		}
		
		if(listViewEntriesCount !== 0){
			var pageNumberText = pageStartRange+" "+app.vtranslate('to')+" "+pageEndRange;
			pageNumbersTextElem.html(pageNumberText);
			totalNumberOfRecords.removeClass('hide');
		} else {
			pageNumbersTextElem.html("<span>&nbsp;</span>");
			if(!totalNumberOfRecords.hasClass('hide')){
				totalNumberOfRecords.addClass('hide');
			}
		}
		
		this.registerFloatingThead();
        
	},
	
	 /**
	 * Function to handle next page navigation
	 */
	nextPageHandler : function(){
		var aDeferred = jQuery.Deferred();
        var folderListContainer = jQuery('.documentsSettingsContainer');
		var pageLimit = jQuery('#pageLimit',folderListContainer).val();
		var noOfEntries = jQuery('#noOfEntries',folderListContainer).val();
		
		if(noOfEntries == pageLimit){
			var pageNumber = jQuery('#pageNumber',folderListContainer).val();
			var nextPageNumber = parseInt(pageNumber) + 1;
			var pagingParams = {
					"page": nextPageNumber
				}
			var completeParams = this.getFolderDefaultParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',folderListContainer).val(nextPageNumber);
					aDeferred.resolve(data);
				}
			);
		}
		return aDeferred.promise();
	},
	
	/**
	 * Function to handle Previous page navigation
	 */
	previousPageHandler : function(){
		var aDeferred = jQuery.Deferred();
		var folderListContainer = jQuery('.documentsSettingsContainer');
		var pageNumber = jQuery('#pageNumber',folderListContainer).val();
		var previousPageNumber = parseInt(pageNumber) - 1;
		if(pageNumber > 1){
			var pagingParams = {
				"page": previousPageNumber
			}
			var completeParams = this.getFolderDefaultParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',folderListContainer).val(previousPageNumber);
					aDeferred.resolve(data);
				}
			);
		}
		return aDeferred.promise();
	},
	
	
	registerEvents: function(){
		var thisInstance = this;
		
		thisInstance.initializeFolderPaginationEvents();
		
		var $table = jQuery('.documentsSettingsContainer').find('#folder-table');
		var scrollContainers = $table.closest('.folder-table-container');
		
		// var scrollContainers = filters.find(".scrollContainer#sharedList");
        jQuery.each(scrollContainers,function(key,scroll){
        	
        	var scroll = jQuery(scroll);
        	var listcontentHeight = scrollContainers.height();
        	
        	if(listcontentHeight > 300 || listcontentHeight <= 0)
        		listcontentHeight = 300;
        	
            scroll.css("height",listcontentHeight);
            scroll.perfectScrollbar({});
            
        });
        
        setTimeout(function () {
			thisInstance.registerFloatingThead();
		}, 1000);
        
	},
	
	getListViewContentHeight: function () {
		var windowHeight = jQuery(window).height();
		//list height should be 76% of window height
		var listViewContentHeight = windowHeight * 0.76103500761035;
		var listViewTable = jQuery('#folder-table');
		if (listViewTable.length) {
			if (!listViewTable.find('tr.emptyRecordsDiv').length) {
				var listTableHeight = jQuery('#folder-table').height();
				if (listTableHeight < listViewContentHeight) {
					listViewContentHeight = listTableHeight + 3;
				}
			}
		}
		if(listViewContentHeight < 50 || listViewContentHeight > 300)
			listViewContentHeight = 300;
		
		return listViewContentHeight + 'px';
	},
	getListViewContentWidth: function () {
		return '100%';
	},
	registerFloatingThead: function () {
		
		if (typeof $.fn.perfectScrollbar !== 'function' || typeof $.fn.floatThead !== 'function') {
			return;
		}
		var $table = jQuery('#folder-table');
		if (!$table.length)
			return;
		
		var height = this.getListViewContentHeight();
		var width = this.getListViewContentWidth();
		var tableContainer = $table.closest('.folder-table-container');
		tableContainer.css({
			'position': 'relative',
			'height': height,
			'width': width
		});

		tableContainer.perfectScrollbar({
			'wheelPropagation': true
		});

		$table.floatThead({
			scrollContainer: function ($table) {
				//console.log($table);
				return $table.closest('.folder-table-container');
			}
		});
	},
	
	registerDeleteFolderEvent :function(id){
		  
		var element = jQuery(event.currentTarget).closest('.folderListViewEntries');
  
		app.helper.showPromptBox({
			'message' : app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE')
		}).then(function() {
			var folderId = id;
			var params = {
				module : app.getModuleName(),
				mode  : 'delete_folder',
				action : 'GetTreeData',
				id : folderId
			};
     
			app.helper.showProgress();
			app.request.post({'data' : params}).then(function(e,res) {
				app.helper.hideProgress();
				if(!e) {
					if(res.success){
						element.remove();
						app.helper.showSuccessNotification({
							'message' : res.message
						});
					}else{
						app.helper.showAlertNotification({
							'message' : res.message
						});
					}
				}
			});
		});
	},
	
});

jQuery(document).ready(function () {
	
	var instance = Documents_FolderSettings_Js.getInstance();
	instance.registerEvents();
	
	
});