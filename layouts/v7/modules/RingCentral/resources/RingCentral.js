/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("RingCentral_RingCentral_Js",{
	
	triggerRingCentralConnect: function(url){
		
		var win = window.open(url,'','height=600,width=600,channelmode=1');
		
		window.RefreshPage = function() {
			
			var params = {
				'module':'Contacts',
				'view' :'Extension',
				'extensionModule':'RingCentral',
				'extensionView':'Index'
				
			}
			
			app.helper.showProgress();
			
			app.request.get({data:params}).then(function(error, data) {
				
				app.helper.hideProgress();
				
				$("#ringcentral").html(data);
				
			});
					
		}
	}
	
},{
	
	saveSettings: function(){
		
		$("#ringcentral").on("click", "#saveSettings", function(e){
			if($("#from_no").val()){
				app.helper.showProgress();
				var params = {
					'module': 'RingCentral', 
					'action': 'SaveSettings',
					'from_no': $("#from_no").val()
				}
				
				app.request.post({data:params}).then(function(error, data) {
					app.helper.hideProgress();
					if(data.success)
						app.helper.showSuccessNotification({message: app.vtranslate(data.message)});
					else
						app.helper.showErrorNotification({message: app.vtranslate(data.message)});
				});
			}else{
				app.helper.showErrorNotification({message: app.vtranslate('From number cant be empty')});
			}
		});
		
	},
	
	registerEvents: function() {
		var thisInstance = this;
		this.saveSettings();
		this.buttonClickEvents();
		this.initializeRingCentralPaginationEvents();
		setTimeout(function () {
			thisInstance.registerFloatingThead();
		}, 1000);
	},
	
	buttonClickEvents : function(){
		
		$(document).on('click','.updateStatus',function(){
			var self = this;
			app.helper.showProgress();
			
			var params = {
				'module': 'RingCentral', 
				'action': 'UpdateStatus',
				'ringid': $(this).data('ringcentralid'),
				'crmid' : $(this).data('crmid')
			}
			
			app.request.post({data:params}).then(function(error, data) {
				app.helper.hideProgress();
				if(data.success){
					app.helper.showSuccessNotification({message: app.vtranslate(data.message)});
					$(self).closest('.listViewEntries').find('.status').text(data.status);
				}else{
					app.helper.showErrorNotification({message: app.vtranslate(data.message)});
				}
			});
		});
		
	},
	
	initializeRingCentralPaginationEvents : function() {
		var thisInstance = this;
		var paginationObj = new Vtiger_Pagination_Js;
		var logListContainer = jQuery('.extensionContents');
		
		paginationObj.initialize(logListContainer);
		
		app.event.on(paginationObj.nextPageButtonClickEventName, function(){
			thisInstance.nextPageHandler().then(function(data){
				var pageNumber = logListContainer.find('#pageNumber').val();
				logListContainer.find('#pageToJump').val(pageNumber);
				thisInstance.updatePagination();
			});
		});
		
		app.event.on(paginationObj.previousPageButtonClickEventName, function(){
			thisInstance.previousPageHandler().then(function(data){
				var pageNumber = logListContainer.find('#pageNumber').val();
				logListContainer.find('#pageToJump').val(pageNumber);
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
	
	
	getRingCentralDefaultParams: function () {
		
		var container =  jQuery('.extensionContents');
		var pageNumber = container.find('#pageNumber').val();
		var module = app.getModuleName();
		var parent = '';
	
		
		var params = {
			'extensionModule': 'RingCentral',
			'page': pageNumber,
			'view': "Extension",
			'mode':'recentLogs',
			'extensionView':'Index',
			'record':app.getRecordId(),
		}
		
		return params;
	},
	
	getRingCentralPageCount: function () {
		var aDeferred = jQuery.Deferred();
		var pageCountParams = this.getRingCentralDefaultParams();
		var params = {
			"type": "GET",
			"data": pageCountParams
		}
		params['view'] = "Extension";
		params['extensionView'] = "Index";
		params['mode'] = "getRingCentralPageCount";

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
		var logListContainer = jQuery('.extensionContents');
		var element = logListContainer.find('#totalPageCount');
		var totalPageNumber = element.text();
		var pageCount;
		
		if(totalPageNumber === ""){
			var totalCountElem = logListContainer.find('#totalCount');
			var totalRecordCount = totalCountElem.val();
			if(totalRecordCount !== '') {
				var recordPerPage = logListContainer.find('#pageLimit').val();
				if(recordPerPage === '0') recordPerPage = 1;
				pageCount = Math.ceil(totalRecordCount/recordPerPage);
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
				return;
			}

			thisInstance.getRingCentralPageCount().then(function(data){
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
		var logListContainer = jQuery('.extensionContents');
		var currentPageElement = jQuery('#pageNumber', logListContainer);
		var currentPageNumber = parseInt(currentPageElement.val());
		var newPageNumber = parseInt(jQuery('#pageToJump',logListContainer).val());
		var totalPages = parseInt(jQuery('#totalPageCount', logListContainer).text());
		
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
		
		var urlParams = thisInstance.getRingCentralDefaultParams();
		urlParams['page'] = newPageNumber;
		
		this.getPageRecords(urlParams).then(
			function(data){
				jQuery('.btn-group', logListContainer).removeClass('open');
				jQuery('#pageNumber',logListContainer).val(newPageNumber);
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
                jQuery('.extensionContents').html(data);
                vtUtils.applyFieldElementsView(jQuery('.extensionContents'));
                aDeferred.resolve(data);
            }
        );
		return aDeferred.promise();
	},
	
	totalNumOfRecords : function (currentEle) {
		var thisInstance = this;
		var logListContainer = jQuery('.extensionContents');
		var totalRecordsElement = logListContainer.find('#totalCount');
		var totalNumberOfRecords = totalRecordsElement.val();
		currentEle.addClass('hide');

		if(totalNumberOfRecords === '') {
			thisInstance.getRingCentralPageCount().then(function(data){
				totalNumberOfRecords = data.numberOfRecords;
				totalRecordsElement.val(totalNumberOfRecords);
				logListContainer.find('ul#listViewPageJumpDropDown #totalPageCount').text(data.page);
				thisInstance.showPagingInfo();
			});
		}else{
			thisInstance.showPagingInfo();
		}
	},
	
	showPagingInfo : function(){
		var thisInstance = this;
		var logListContainer = jQuery('.extensionContents');
		var totalNumberOfRecords = jQuery('#totalCount', logListContainer).val();
		var pageNumberElement = jQuery('.pageNumbersText', logListContainer);
		var pageRange = pageNumberElement.text();
		var newPagingInfo = pageRange.trim()+" "+app.vtranslate('of')+" "+totalNumberOfRecords;
		var listViewEntriesCount = parseInt(jQuery('#noOfEntries', logListContainer).val());
		
		if(listViewEntriesCount !== 0){
			jQuery('.pageNumbersText', logListContainer).html(newPagingInfo);
		} else {
			jQuery('.pageNumbersText', logListContainer).html("");
		}
	},
	
	updatePagination : function(){
        var logListContainer = jQuery('.extensionContents');
        app.helper.hideProgress();
		var previousPageExist = jQuery('#previousPageExist',logListContainer).val();
		var nextPageExist = jQuery('#nextPageExist',logListContainer).val();
		var previousPageButton = jQuery('#PreviousPageButton',logListContainer);
		var nextPageButton = jQuery('#NextPageButton',logListContainer);
		var listViewEntriesCount = jQuery('#noOfEntries',logListContainer).val();
		var pageStartRange = jQuery('#pageStartRange',logListContainer).val();
		var pageEndRange = jQuery('#pageEndRange',logListContainer).val();
		var totalNumberOfRecords = jQuery('.totalNumberOfRecords',logListContainer);
		var pageNumbersTextElem = jQuery('.pageNumbersText',logListContainer);         
		
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
        var logListContainer = jQuery('.extensionContents');
		var pageLimit = jQuery('#pageLimit',logListContainer).val();
		var noOfEntries = jQuery('#noOfEntries',logListContainer).val();
		
		if(noOfEntries == pageLimit){
			var pageNumber = jQuery('#pageNumber',logListContainer).val();
			var nextPageNumber = parseInt(pageNumber) + 1;
			var pagingParams = {
					"page": nextPageNumber
				}
			var completeParams = this.getCompleteParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',logListContainer).val(nextPageNumber);
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
		var logListContainer = jQuery('.extensionContents');
		var pageNumber = jQuery('#pageNumber',logListContainer).val();
		var previousPageNumber = parseInt(pageNumber) - 1;
		if(pageNumber > 1){
			var pagingParams = {
				"page": previousPageNumber
			}
			var completeParams = this.getCompleteParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',logListContainer).val(previousPageNumber);
					aDeferred.resolve(data);
				}
			);
		}
		return aDeferred.promise();
	},
	
	getCompleteParams : function(){
		
		var params = {};
		params['view'] = "Extension";
		params['extensionModule'] = 'RingCentral';
		params['extensionView'] = 'Index'
		params['record'] = app.getRecordId();
		params['page'] = jQuery('input[name="currentPageNum"]',jQuery('.extensionContents')).val();
		params['mode'] = "recentLogs";

		return params;
	},
	
	getListViewContentHeight: function () {
		var windowHeight = jQuery(window).height();
		var listViewContentHeight = windowHeight * 0.76103500761035;
		var listViewTable = jQuery('#log-table');
		if (listViewTable.length) {
			if (!listViewTable.find('tr.emptyRecordsDiv').length) {
				var listTableHeight = jQuery('#log-table').height();
				if (listTableHeight < listViewContentHeight) {
					listViewContentHeight = listTableHeight + 3;
				}
			}
		}
		if(listViewContentHeight < 50 || listViewContentHeight > 500)
			listViewContentHeight = 500;
		
		return listViewContentHeight + 'px';
	},
	
	getListViewContentWidth: function () {
		return '100%';
	},
	
	registerFloatingThead: function () {
		
		if (typeof $.fn.perfectScrollbar !== 'function' || typeof $.fn.floatThead !== 'function') {
			return;
		}
		var $table = jQuery('#log-table');
		if (!$table.length)
			return;
		
		var height = this.getListViewContentHeight();
		var width = this.getListViewContentWidth();
		var tableContainer = $table.closest('.table-container');
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
				return $table.closest('.table-container');
			}
		});
	},

});

jQuery(document).ready(function(){
	var instance = new RingCentral_RingCentral_Js();
	instance.registerEvents();
});