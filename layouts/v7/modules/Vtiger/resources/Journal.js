/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger.Class("Vtiger_Journal_Js",{

	getInstance : function() {
		return new Vtiger_Journal_Js();
	},
	
},{
	
	registerEvents : function() {
		//this._super();
		var container = jQuery('.journalsrelatedContainer');
		vtUtils.applyFieldElementsView(container);
		this.initializeJournalPaginationEvents();
		this.registerRelatedRowClickEvent();
		this.registerEventForEmailsRelatedRecord();
		this.registerRelatedListSearch();
		this.relatedtriggerExportAction();
		this.updateRelatedRecordsCount();
	},
	
	updateRelatedRecordsCount: function () {
		var detailInstance = new Vtiger_Detail_Js();
		detailInstance.updateRelatedRecordsCount();
	},

	initializeJournalPaginationEvents : function() {
		var thisInstance = this;
		var paginationObj = new Vtiger_Pagination_Js;
		var journalListContainer = jQuery('.journalsrelatedContainer');
		
		paginationObj.initialize(journalListContainer);
		
		app.event.on(paginationObj.nextPageButtonClickEventName, function(){
			thisInstance.nextPageHandler().then(function(data){
				var pageNumber = journalListContainer.find('#pageNumber').val();
				journalListContainer.find('#pageToJump').val(pageNumber);
				thisInstance.updatePagination();
			});
		});
		
		app.event.on(paginationObj.previousPageButtonClickEventName, function(){
			thisInstance.previousPageHandler().then(function(data){
				var pageNumber = journalListContainer.find('#pageNumber').val();
				journalListContainer.find('#pageToJump').val(pageNumber);
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
	
	
	getJournalDefaultParams: function () {
		
		var container =  jQuery('.journalsrelatedContainer');
		var pageNumber = container.find('#pageNumber').val();
		var module = app.getModuleName();
		var parent = '';
		var cvId = container.find('[name="cvid"]').val();
		
		var params = {
			'module': module,
			'page': pageNumber,
			'view': "Journal",
			'mode':'recentJournals',
			'record':app.getRecordId(),
		}
		
		return params;
	},
	
	getJournalPageCount: function () {
		var aDeferred = jQuery.Deferred();
		var pageCountParams = this.getJournalDefaultParams();
		var params = {
			"type": "GET",
			"data": pageCountParams
		}
		params['view'] = "Journal";
		params['mode'] = "getJournalPageCount";

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
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var element = journalListContainer.find('#totalPageCount');
		var totalPageNumber = element.text();
		var pageCount;
		
		if(totalPageNumber === ""){
			var totalCountElem = journalListContainer.find('#totalCount');
			var totalRecordCount = totalCountElem.val();
			if(totalRecordCount !== '') {
				var recordPerPage = journalListContainer.find('#pageLimit').val();
				if(recordPerPage === '0') recordPerPage = 1;
				pageCount = Math.ceil(totalRecordCount/recordPerPage);
				if(pageCount === 0){
					pageCount = 1;
				}
				element.text(pageCount);
				return;
			}

			thisInstance.getJournalPageCount().then(function(data){
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
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var currentPageElement = jQuery('#pageNumber', journalListContainer);
		var currentPageNumber = parseInt(currentPageElement.val());
		var newPageNumber = parseInt(jQuery('#pageToJump',journalListContainer).val());
		var totalPages = parseInt(jQuery('#totalPageCount', journalListContainer).text());
		
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
		
		var urlParams = thisInstance.getJournalDefaultParams();
		urlParams['page'] = newPageNumber;
		
		this.getPageRecords(urlParams).then(
			function(data){
				jQuery('.btn-group', journalListContainer).removeClass('open');
				jQuery('#pageNumber',journalListContainer).val(newPageNumber);
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
                jQuery('.journalsrelatedContainer').html(data);
                vtUtils.applyFieldElementsView(jQuery('.journalsrelatedContainer'));
                aDeferred.resolve(data);
            }
        );
		return aDeferred.promise();
	},
	
	totalNumOfRecords : function (currentEle) {
		var thisInstance = this;
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var totalRecordsElement = journalListContainer.find('#totalCount');
		var totalNumberOfRecords = totalRecordsElement.val();
		currentEle.addClass('hide');

		if(totalNumberOfRecords === '') {
			thisInstance.getJournalPageCount().then(function(data){
				totalNumberOfRecords = data.numberOfRecords;
				totalRecordsElement.val(totalNumberOfRecords);
				journalListContainer.find('ul#listViewPageJumpDropDown #totalPageCount').text(data.page);
				thisInstance.showPagingInfo();
			});
		}else{
			thisInstance.showPagingInfo();
		}
	},
	
	showPagingInfo : function(){
		var thisInstance = this;
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var totalNumberOfRecords = jQuery('#totalCount', journalListContainer).val();
		var pageNumberElement = jQuery('.pageNumbersText', journalListContainer);
		var pageRange = pageNumberElement.text();
		var newPagingInfo = pageRange.trim()+" "+app.vtranslate('of')+" "+totalNumberOfRecords;
		var listViewEntriesCount = parseInt(jQuery('#noOfEntries', journalListContainer).val());
		
		if(listViewEntriesCount !== 0){
			jQuery('.pageNumbersText', journalListContainer).html(newPagingInfo);
		} else {
			jQuery('.pageNumbersText', journalListContainer).html("");
		}
	},
	
	updatePagination : function(){
        var journalListContainer = jQuery('.journalsrelatedContainer');
        app.helper.hideProgress();
		var previousPageExist = jQuery('#previousPageExist',journalListContainer).val();
		var nextPageExist = jQuery('#nextPageExist',journalListContainer).val();
		var previousPageButton = jQuery('#PreviousPageButton',journalListContainer);
		var nextPageButton = jQuery('#NextPageButton',journalListContainer);
		var listViewEntriesCount = jQuery('#noOfEntries',journalListContainer).val();
		var pageStartRange = jQuery('#pageStartRange',journalListContainer).val();
		var pageEndRange = jQuery('#pageEndRange',journalListContainer).val();
		var totalNumberOfRecords = jQuery('.totalNumberOfRecords',journalListContainer);
		var pageNumbersTextElem = jQuery('.pageNumbersText',journalListContainer);         
		
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
        var journalListContainer = jQuery('.journalsrelatedContainer');
		var pageLimit = jQuery('#pageLimit',journalListContainer).val();
		var noOfEntries = jQuery('#noOfEntries',journalListContainer).val();
		
		if(noOfEntries == pageLimit){
			var pageNumber = jQuery('#pageNumber',journalListContainer).val();
			var nextPageNumber = parseInt(pageNumber) + 1;
			var pagingParams = {
					"page": nextPageNumber
				}
			var completeParams = this.getCompleteParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',journalListContainer).val(nextPageNumber);
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
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var pageNumber = jQuery('#pageNumber',journalListContainer).val();
		var previousPageNumber = parseInt(pageNumber) - 1;
		if(pageNumber > 1){
			var pagingParams = {
				"page": previousPageNumber
			}
			var completeParams = this.getCompleteParams();
			jQuery.extend(completeParams,pagingParams);
			this.getPageRecords(completeParams).then(
				function(data){
					jQuery('#pageNumber',journalListContainer).val(previousPageNumber);
					aDeferred.resolve(data);
				}
			);
		}
		return aDeferred.promise();
	},
	
	/**
	 * Function to register event for related list row click
	 */
	registerRelatedRowClickEvent: function() {
		var journalListContainer = jQuery('.journalsrelatedContainer');
		journalListContainer.on('click','.relatedListEntryValues a',function(e){
			e.preventDefault();
		});
		journalListContainer.on('click','.listViewEntries',function(e){
			var selection = window.getSelection().toString();
			if(selection.length == 0) { 
				var targetElement = jQuery(e.target, jQuery(e.currentTarget));
				if(targetElement.hasClass('js-reference-display-value')) return;
				if(targetElement.is('td:first-child') && (targetElement.children('input[type="checkbox"]').length > 0)) return;
				if(jQuery(e.target).is('input[type="checkbox"]')) return;
					var elem = jQuery(e.currentTarget);
					var recordUrl = elem.data('recordurl');
				if(typeof recordUrl != "undefined"){
						var params = app.convertUrlToDataParams(recordUrl);
						//Display Mode to show details in overlay
						params['mode'] = 'showDetailViewByMode';
						params['requestMode'] = 'full';
						params['displayMode'] = 'overlay';
						var parentRecordId = app.getRecordId();
						app.helper.showProgress();
						app.request.get({data: params}).then(function(err, response) {
							app.helper.hideProgress();
							var overlayParams = {'backdrop' : 'static', 'keyboard' : false};
							app.helper.loadPageContentOverlay(response, overlayParams).then(function(container) {
								var detailjs = Vtiger_Detail_Js.getInstanceByModuleName(params.module);
								detailjs.showScroll(jQuery('.overlayDetail .modal-body'));
								detailjs.setModuleName(params.module);
								detailjs.setOverlayDetailMode(true);
								detailjs.setContentHolder(container.find('.overlayDetail'));
								detailjs.setDetailViewContainer(container.find('.overlayDetail'));
								detailjs.registerOverlayEditEvent();
								detailjs.registerBasicEvents();
								detailjs.registerClickEvent();
								detailjs.registerHeaderAjaxEditEvents(container.find('.overlayDetailHeader'));
								detailjs.registerEventToReloadRelatedListOnCloseOverlay(parentRecordId);
								app.event.trigger('post.overlay.load', parentRecordId, params); 
								container.find('form#detailView').on('submit', function(e) {
									e.preventDefault();
							});
						});
					});
				}
			}
		});
	},
	
	registerEventForEmailsRelatedRecord : function(){
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var parentId = app.getRecordId();

		var params = {};
		params['module'] = "Emails";
		params['view'] = "ComposeEmail";
		params['parentId'] = parentId;
		params['relatedLoad'] = true;

		journalListContainer.on('click','[name="emailsRelatedRecord"], [name="emailsDetailView"]',function(e){
			e.stopPropagation();
			var element = jQuery(e.currentTarget);
			var recordId = element.data('id');
			if(element.data('emailflag') == 'SAVED') {
				var mode = 'emailEdit';
			} else {
				mode = 'emailPreview';
				params['parentModule'] = app.getModuleName();
			}
			params['mode'] = mode;
			params['record'] = recordId;
			app.helper.showProgress();
			app.request.post({data:params}).then(function(err,data){
				app.helper.hideProgress();
				if(err === null){
					var dataObj = jQuery(data);
					var descriptionContent = dataObj.find('#iframeDescription').val();
					app.helper.showModal(data,{cb:function(){
						if(mode === 'emailEdit'){
							var editInstance = new Emails_MassEdit_Js();
							editInstance.registerEvents();
						}else {
							var previewInstance = new Vtiger_EmailPreview_Js();
							previewInstance.registerEventsForActionButtons();
						}
						jQuery('#emailPreviewIframe').contents().find('html').html(descriptionContent);
						jQuery("#emailPreviewIframe").height(jQuery('.email-body-preview').height());
						jQuery('#emailPreviewIframe').contents().find('html').find('a').on('click', function(e) {
							e.preventDefault();
							var url = jQuery(e.currentTarget).attr('href');
							window.open(url, '_blank');
						});
						//jQuery("#emailPreviewIframe").height(jQuery('#emailPreviewIframe').contents().find('html').height());
					}});
				}
			});
		})

		journalListContainer.on('click','[name="emailsEditView"]',function(e){
			e.stopPropagation();
			var module = "Emails";
			app.helper.checkServerConfig(module).then(function(data){
				if(data == true){
					var element = jQuery(e.currentTarget);
					var closestROw = element.closest('tr');
					var recordId = closestROw.data('id');
					var parentRecord = new Array();
					parentRecord.push(parentId);

					params['mode'] = "emailEdit";
					params['record'] = recordId;
					params['selected_ids'] = parentRecord;
					app.helper.showProgress();
					app.request.post({'data':params}).then(function(err,data){
						app.helper.hideProgress();
						if(err === null){
							app.helper.showModal(data);
							var editInstance = new Emails_MassEdit_Js();
							editInstance.registerEvents();
						}
					});
				} else {
					app.helper.showErrorMessage(app.vtranslate('JS_EMAIL_SERVER_CONFIGURATION'));
				}
			})
		})
	},
	
	/**
	 * Function to register the related list search event
	 */
	registerRelatedListSearch : function() {
		var thisInstance = this;
		var journalListContainer = jQuery('.journalsrelatedContainer');
		journalListContainer.on('click','[data-trigger="relatedListSearch"]',function(e){
			var params = {'page' : '1'};
			thisInstance.loadRelatedList(params);
		});
		journalListContainer.on('keypress','input.listSearchContributor',function(e){
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
		params['view'] = "Journal";
		params['module'] = app.getModuleName();
		params['record'] = app.getRecordId();
		params['page'] = jQuery('input[name="currentPageNum"]',jQuery('.journalsrelatedContainer')).val();
		params['mode'] = "recentJournals";

		
		params['tab_label'] = 'Journal';
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
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var relatedListTable = journalListContainer.find('.searchRow');
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
	
	relatedtriggerExportAction: function (exportActionUrl) {
		var self = this;
		var journalListContainer = jQuery('.journalsrelatedContainer');
		journalListContainer.on('click','.journalexport',function(){
			exportActionUrl = $(this).attr('href');
			self.performExportAction(exportActionUrl);
		});
		
	},
	
	performExportAction: function (url) {
		var journalInstance = this;
		
		var journalListContainer = jQuery('.journalsrelatedContainer');
		var pageNumber = journalListContainer.find('#pageNumber').val();
		var postData = journalInstance.getCompleteParams();

		var params = app.convertUrlToDataParams(url);
		postData = jQuery.extend(postData, params);
		
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
	
	
});

