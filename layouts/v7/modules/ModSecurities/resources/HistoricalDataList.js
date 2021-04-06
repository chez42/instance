/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("Vtiger_HistoricalDataList_Js",{

	getInstance : function() {
		return new Vtiger_HistoricalDataList_Js();
	},
	
},{
	
	registerEvents : function() {
		//this._super();
		var container = jQuery('.historicalDataRelatedContainer');
		vtUtils.applyFieldElementsView(container);
		this.initializeHistoricalDataPaginationEvents();
		this.registerRelatedListSearch();
		this.updateRelatedRecordsCount();
		
	},
	
	updateRelatedRecordsCount: function () {
		var detailInstance = new Vtiger_Detail_Js();
		detailInstance.updateRelatedRecordsCount();
	},

	initializeHistoricalDataPaginationEvents : function() {
		var thisInstance = this;
		var paginationObj = new Vtiger_Pagination_Js;
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		
		paginationObj.initialize(historicalDataListContainer);
		
		app.event.on(paginationObj.nextPageButtonClickEventName, function(){
			thisInstance.nextPageHandler().then(function(data){
				var pageNumber = historicalDataListContainer.find('#pageNumber').val();
				historicalDataListContainer.find('#pageToJump').val(pageNumber);
				thisInstance.updatePagination();
			});
		});
		
		app.event.on(paginationObj.previousPageButtonClickEventName, function(){
			thisInstance.previousPageHandler().then(function(data){
				var pageNumber = historicalDataListContainer.find('#pageNumber').val();
				historicalDataListContainer.find('#pageToJump').val(pageNumber);
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
	
	
	getHistoricalDataDefaultParams: function () {
		
		var container =  jQuery('.historicalDataRelatedContainer');
		var pageNumber = container.find('#pageNumber').val();
		var module = app.getModuleName();
		var parent = '';
		var cvId = container.find('[name="cvid"]').val();
		
		var params = {
			'module': module,
			'page': pageNumber,
			'view': "HistoricalDataList",
			'mode':'recentHistoricals',
			'record':app.getRecordId(),
		}
		
		return params;
	},
	
	getHistoricalPageCount: function () {
		var aDeferred = jQuery.Deferred();
		var pageCountParams = this.getHistoricalDataDefaultParams();
		var params = {
			"type": "GET",
			"data": pageCountParams
		}
		params['view'] = "HistoricalDataList";
		params['mode'] = "getHistoricalPageCount";

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
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var element = historicalDataListContainer.find('#totalPageCount');
		var totalPageNumber = element.text();
		var pageCount;
		
		if(totalPageNumber === ""){
			var totalCountElem = historicalDataListContainer.find('#totalCount');
			var totalRecordCount = totalCountElem.val();
			if(totalRecordCount !== '') {
				var recordPerPage = historicalDataListContainer.find('#pageLimit').val();
				if(recordPerPage === '0') recordPerPage = 1;
				pageCount = Math.ceil(totalRecordCount/recordPerPage);
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
				return;
			}

			thisInstance.getHistoricalPageCount().then(function(data){
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
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var currentPageElement = jQuery('#pageNumber', historicalDataListContainer);
		var currentPageNumber = parseInt(currentPageElement.val());
		var newPageNumber = parseInt(jQuery('#pageToJump',historicalDataListContainer).val());
		var totalPages = parseInt(jQuery('#totalPageCount', historicalDataListContainer).text());
		
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
		
		var urlParams = thisInstance.getHistoricalDataDefaultParams();
		urlParams['page'] = newPageNumber;
		
		this.getPageRecords(urlParams).then(
			function(data){
				jQuery('.btn-group', historicalDataListContainer).removeClass('open');
				jQuery('#pageNumber',historicalDataListContainer).val(newPageNumber);
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
                jQuery('.historicalDataRelatedContainer').html(data);
                vtUtils.applyFieldElementsView(jQuery('.historicalDataRelatedContainer'));
                aDeferred.resolve(data);
            }
        );
		return aDeferred.promise();
	},
	
	totalNumOfRecords : function (currentEle) {
		var thisInstance = this;
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var totalRecordsElement = historicalDataListContainer.find('#totalCount');
		var totalNumberOfRecords = totalRecordsElement.val();
		currentEle.addClass('hide');

		if(totalNumberOfRecords === '') {
			thisInstance.getHistoricalPageCount().then(function(data){
				totalNumberOfRecords = data.numberOfRecords;
				totalRecordsElement.val(totalNumberOfRecords);
				historicalDataListContainer.find('ul#listViewPageJumpDropDown #totalPageCount').text(data.page);
				thisInstance.showPagingInfo();
			});
		}else{
			thisInstance.showPagingInfo();
		}
	},
	
	showPagingInfo : function(){
		var thisInstance = this;
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var totalNumberOfRecords = jQuery('#totalCount', historicalDataListContainer).val();
		var pageNumberElement = jQuery('.pageNumbersText', historicalDataListContainer);
		var pageRange = pageNumberElement.text();
		var newPagingInfo = pageRange.trim()+" "+app.vtranslate('of')+" "+totalNumberOfRecords;
		var listViewEntriesCount = parseInt(jQuery('#noOfEntries', historicalDataListContainer).val());
		
		if(listViewEntriesCount !== 0){
			jQuery('.pageNumbersText', historicalDataListContainer).html(newPagingInfo);
		} else {
			jQuery('.pageNumbersText', historicalDataListContainer).html("");
		}
	},
	
	updatePagination : function(){
        var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
        app.helper.hideProgress();
		var previousPageExist = jQuery('#previousPageExist',historicalDataListContainer).val();
		var nextPageExist = jQuery('#nextPageExist',historicalDataListContainer).val();
		var previousPageButton = jQuery('#PreviousPageButton',historicalDataListContainer);
		var nextPageButton = jQuery('#NextPageButton',historicalDataListContainer);
		var listViewEntriesCount = jQuery('#noOfEntries',historicalDataListContainer).val();
		var pageStartRange = jQuery('#pageStartRange',historicalDataListContainer).val();
		var pageEndRange = jQuery('#pageEndRange',historicalDataListContainer).val();
		var totalNumberOfRecords = jQuery('.totalNumberOfRecords',historicalDataListContainer);
		var pageNumbersTextElem = jQuery('.pageNumbersText',historicalDataListContainer);         
		
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
        
	},
	
	 /**
	 * Function to handle next page navigation
	 */
	nextPageHandler : function(){
		var aDeferred = jQuery.Deferred();
        var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var pageLimit = jQuery('#pageLimit',historicalDataListContainer).val();
		var noOfEntries = jQuery('#noOfEntries',historicalDataListContainer).val();
		
		if(noOfEntries == pageLimit){
			var pageNumber = jQuery('#pageNumber',historicalDataListContainer).val();
			var nextPageNumber = parseInt(pageNumber) + 1;
			var pagingParams = {
					"page": nextPageNumber
				}
			var completeParams = this.getCompleteParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',historicalDataListContainer).val(nextPageNumber);
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
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var pageNumber = jQuery('#pageNumber',historicalDataListContainer).val();
		var previousPageNumber = parseInt(pageNumber) - 1;
		if(pageNumber > 1){
			var pagingParams = {
				"page": previousPageNumber
			}
			var completeParams = this.getCompleteParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',historicalDataListContainer).val(previousPageNumber);
					aDeferred.resolve(data);
				}
			);
		}
		return aDeferred.promise();
	},
	
	/**
	 * Function to register the related list search event
	 */
	registerRelatedListSearch : function() {
		var thisInstance = this;
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		historicalDataListContainer.on('click','[data-trigger="relatedListSearch"]',function(e){
			var params = {'page' : '1'};
			thisInstance.loadRelatedList(params);
		});
		historicalDataListContainer.on('keypress','input.listSearchContributor',function(e){
			if(e.keyCode == 13){
				var element = jQuery(e.currentTarget);
				var parentElement = element.closest('tr');
				var searchTriggerElement = parentElement.find('[data-trigger="relatedListSearch"]');
				searchTriggerElement.trigger('click');
			}
		});
	},
	
	getCompleteParams : function(){
		
		var params = {};
		params['view'] = "HistoricalDataList";
		params['module'] = app.getModuleName();
		params['record'] = app.getRecordId();
		params['page'] = jQuery('input[name="currentPageNum"]',jQuery('.historicalDataRelatedContainer')).val();
		params['mode'] = "recentHistoricals";

		
		params['tab_label'] = 'Historical Data';
        var searchParams = JSON.stringify(this.getRelatedListSearchParams());
        params['search_params'] = searchParams;
        params['nolistcache'] = (jQuery('#noFilterCache').val() == 1) ? 1 : 0;
		return params;
	},
    
    loadRelatedList : function(params){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		
		var completeParams = this.getCompleteParams();
		jQuery.extend(completeParams,params);
        app.helper.showProgress();
        
        app.request.get({data:completeParams}).then(
			function(error,responseData){
                app.helper.hideProgress();
				
				container = jQuery('div.details');
                container.html(responseData);

                thisInstance.registerEvents();
                
				aDeferred.resolve(responseData);
			},
			
			function(textStatus, errorThrown){
                app.helper.hideProgress();
				aDeferred.reject(textStatus, errorThrown);
			}
		);
		return aDeferred.promise();
	},
    
	/**
	 * Function to fetch search params
	 */
	getRelatedListSearchParams : function() {
		var historicalDataListContainer = jQuery('.historicalDataRelatedContainer');
		var relatedListTable = historicalDataListContainer.find('.searchRow');
		var searchParams = [];
		var currentSearchParams = [];
		if(jQuery('#currentSearchParams').val()) {
			currentSearchParams = JSON.parse(jQuery('#currentSearchParams').val());
		}
		relatedListTable.find('.listSearchContributor').each(function(index,domElement){
			var searchInfo = [];
			var searchContributorElement = jQuery(domElement);
			var fieldName = searchContributorElement.attr('name');
			var fieldInfo = searchContributorElement.data('field-type');

			if(fieldName in currentSearchParams) {
				delete currentSearchParams[fieldName];
			}

			var searchValue = searchContributorElement.val();

			if(typeof searchValue == "object") {
				if(searchValue == null) {
					searchValue = "";
				}else{
					searchValue = searchValue.join(',');
				}
			}
			searchValue = searchValue.trim();
			if(searchValue.length <=0 ) {
				//continue
				return true;
			}
			var searchOperator = 'c';
			if(fieldInfo == "date" || fieldInfo == "datetime") {
				searchOperator = 'bw';
			}else if (fieldInfo == 'percentage' || fieldInfo == "double" || fieldInfo == "integer"
				|| fieldInfo == 'currency' || fieldInfo == "number" || fieldInfo == "boolean" ||
				fieldInfo == "picklist") {
				searchOperator = 'e';
			}
			var storedOperator = searchContributorElement.parent().parent().find('.operatorValue').val();
			if(storedOperator) {
				searchOperator = storedOperator;
				storedOperator = false;
			}
			searchInfo.push(fieldName);
			searchInfo.push(searchOperator);
			searchInfo.push(searchValue);
			searchInfo.push(fieldInfo);
			searchParams.push(searchInfo);
		});
		for(var i in currentSearchParams) {
			var fieldName = currentSearchParams[i]['fieldName'];
			var searchValue = currentSearchParams[i]['searchValue'];
			var searchOperator = currentSearchParams[i]['comparator'];
			if(fieldName== null || fieldName.length <=0 ){
				continue;
			}
			var searchInfo = [];
			searchInfo.push(fieldName);
			searchInfo.push(searchOperator);
			searchInfo.push(searchValue);
			searchParams.push(searchInfo);
		}
		var params = [];
		params.push(searchParams);
		return params;
	},
	
	
});

