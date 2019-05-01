/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_RelatedList_Js("PortfolioInformation_RelatedList_Js",{},{
	
	getRelatedPageCount : function(){alert("HERE2");
		var aDeferred = jQuery.Deferred();
		var params = {};
		params['action'] = "RelationAjax";
		params['module'] = this.parentModuleName;
		params['record'] = this.getParentId(),
		params['relatedModule'] = this.relatedModulename,
		params['tab_label'] = this.selectedRelatedTabElement.data('label-key');
		params['mode'] = "getRelatedListPageCount"
		params['search_params'] = JSON.stringify(this.getRelatedListSearchParams());
		
		var element = jQuery('#totalPageCount');
		var totalCountElem = jQuery('#totalCount');
		var totalPageNumber = element.text();
		if(totalPageNumber == ""){
			element.progressIndicator({});
			AppConnector.request(params).then(
				function(data) {
					var pageCount = data['result']['page'];
					var numberOfRecords = data['result']['numberOfRecords'];
					totalCountElem.val(numberOfRecords);
					element.text(pageCount);
					element.progressIndicator({'mode': 'hide'});
					aDeferred.resolve();
				},
				function(error,err){

				}
			);
		}else{
			aDeferred.resolve();
		}
		return aDeferred.promise();
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
		params['mode'] = "showRelatedList",
		params['search_params'] = JSON.stringify(this.getRelatedListSearchParams());
		params['tab_label'] = this.selectedRelatedTabElement.data('label-key');
		return params;
	},
	
	getRelatedListSearchParams : function(){
	    var listViewPageDiv = this.detailViewContainer;
	    var listViewTable = listViewPageDiv.find('.listViewEntriesTable');
	    var searchParams = new Array();
	    listViewTable.find('.listSearchContributor').each(function(index,domElement){
	    	var searchInfo = new Array();
	        var searchContributorElement = jQuery(domElement);
	        var fieldInfo = searchContributorElement.data('fieldinfo');
	        var fieldName = searchContributorElement.attr('name');
	
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
	        if(fieldInfo.type == "date" || fieldInfo.type == "datetime") {
	            searchOperator = 'bw';
	         }else if (  fieldInfo.type == "boolean" || fieldInfo.type == "picklist" || fieldInfo.type == 'owner') {
	            searchOperator = 'e';
	        }else if( fieldInfo.type == 'currency'  || fieldInfo.type == "double" || 
	            fieldInfo.type == 'percentage' || fieldInfo.type == "integer"  ||
	            fieldInfo.type == "number"){
	            if(searchValue.substring(0,2) == '>=' ) {
	                searchOperator = 'h';
	            } else if ( searchValue.substring(0,2)== '<=') { 
	                searchOperator = 'm';   
	            } else   if(searchValue.substring(0,1) == '>' ) { 
	                searchOperator = 'g';
	            } else if ( searchValue.substring(0,1)== '<') {
	                searchOperator = 'l';   
	            } else {
	                searchOperator = 'e';
	            }
	        }
	        searchInfo.push(fieldName);
	        searchInfo.push(searchOperator);
	        searchInfo.push(searchValue);
	        searchParams.push(searchInfo);
	    });
	   
	    return new Array(searchParams);
	},
	
	loadRelatedList : function(params){
		var aDeferred = jQuery.Deferred();
		var thisInstance = this;
		if(typeof this.relatedModulename== "undefined" || this.relatedModulename.length <= 0 ) {
			return;
		}
		var progressIndicatorElement = jQuery.progressIndicator({
			'position' : 'html',
			'blockInfo' : {
				'enabled' : true
			}
		});
		var completeParams = this.getCompleteParams();
		jQuery.extend(completeParams,params);
		
		if(completeParams.relatedModule == "Transactions"){
			AppConnector.requestPjax(completeParams).then(
				function(responseData){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					})
					thisInstance.relatedTabsContainer.find('li').removeClass('active');
					thisInstance.selectedRelatedTabElement.addClass('active');
					thisInstance.relatedContentContainer.html(responseData);
					responseData = thisInstance.relatedContentContainer.html();
					//thisInstance.triggerDisplayTypeEvent();
					Vtiger_Helper_Js.showHorizontalTopScrollBar();
					jQuery('.pageNumbers',thisInstance.relatedContentContainer).tooltip();
					aDeferred.resolve(responseData);
					jQuery('input[name="currentPageNum"]', thisInstance.relatedContentContainer).val(completeParams.page);
					// Let listeners know about page state change.
					app.notifyPostAjaxReady();
				},
				
				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		} else {
			AppConnector.request(completeParams).then(
				function(responseData){
					progressIndicatorElement.progressIndicator({
						'mode' : 'hide'
					})
					thisInstance.relatedTabsContainer.find('li').removeClass('active');
					thisInstance.selectedRelatedTabElement.addClass('active');
					thisInstance.relatedContentContainer.html(responseData);
					responseData = thisInstance.relatedContentContainer.html();
					//thisInstance.triggerDisplayTypeEvent();
					Vtiger_Helper_Js.showHorizontalTopScrollBar();
					jQuery('.pageNumbers',thisInstance.relatedContentContainer).tooltip();
					aDeferred.resolve(responseData);
					jQuery('input[name="currentPageNum"]', thisInstance.relatedContentContainer).val(completeParams.page);
					// Let listeners know about page state change.
					app.notifyPostAjaxReady();
				},
				
				function(textStatus, errorThrown){
					aDeferred.reject(textStatus, errorThrown);
				}
			);
		}
		
		return aDeferred.promise();
	},
	
})