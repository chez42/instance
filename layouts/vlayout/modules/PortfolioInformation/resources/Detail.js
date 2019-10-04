Vtiger_Detail_Js("PortfolioInformation_Detail_Js", {} ,{
	/**
	 * Function to register event for ckeditor for description field
	 */
	fillCharts : function(){
	},

	registerEvents : function() {
		this._super();
		this.fillCharts();
		
		this.registerRelatedListEvents();
		var thisInstance = this;
		
		app.listenPostAjaxReady(function(){
			
			var module = thisInstance.getURLParameterByName("module");
			var relatedModule = thisInstance.getURLParameterByName("relatedModule");
			
			if(module == 'PortfolioInformation' && relatedModule == 'Transactions'){
				thisInstance.registerRelatedListEvents();
			}
		});
	},
	
	getURLParameterByName: function(name, url) {
		if (!url) url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	},
	
	loadRelatedList : function(pageNumber){
		console.log("PT LoadRelatedList");
		var relatedListInstance = new PortfolioInformation_RelatedList_Js(this.getRecordId(), app.getModuleName(), this.getSelectedTab(), this.getRelatedModuleName());
		var params = {'page':pageNumber};
		relatedListInstance.loadRelatedList(params);
	},
	
	registerRelatedListEvents : function(){
		this.registerRelatedListSearch();
		app.showSelect2ElementView(jQuery(".relatedContainer").find('select.select2'));
		app.changeSelectElementView(jQuery(".relatedContainer"));
		app.registerEventForTimeFields(jQuery(".relatedContainer"), false);
		this.registerDateListSearch(jQuery(".relatedContainer"));
	},
	
	registerDateListSearch : function(container) {
        container.find('.dateField').each(function(index,element){
            var dateElement = jQuery(element);
            var customParams = {
                calendars: 3,
                mode: 'range',
                className : 'rangeCalendar',
                onChange: function(formated) {
                    dateElement.val(formated.join(','));
                }
            }
            app.registerEventForDatePickerFields(dateElement,false,customParams);
        });
	},
	
	registerRelatedListSearch : function() {
		var listViewPageDiv = jQuery(".relatedContainer");
		var thisInstance = this;
		listViewPageDiv.on('click','[data-trigger="listSearch"]',function(e){
			e.preventDefault();
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.loadRelatedList({'page': '1'}).then(
				function(data){
					jQuery('#pageToJump').val('1');
					jQuery('#totalPageCount').text("");
					relatedController.getRelatedPageCount();
				},
				function(textStatus, errorThrown){
				}
			);
		});

		listViewPageDiv.on('keypress','input.listSearchContributor',function(e){
			if(e.keyCode == 13){
				e.preventDefault();
				var element = jQuery(e.currentTarget);
				var parentElement = element.closest('tr');
				var searchTriggerElement = parentElement.find('[data-trigger="listSearch"]');
				searchTriggerElement.trigger('click');
			}
		});
    },
	
	registerEventForRelatedListPagination : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','#relatedListNextPageButton',function(e){
			var element = jQuery(e.currentTarget);
			if(element.attr('disabled') == "disabled"){
				return;
			}
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.nextPageHandler();
		});
		detailContentsHolder.on('click','#relatedListPreviousPageButton',function(){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.previousPageHandler();
		});
		detailContentsHolder.on('click','#relatedListPageJump',function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.getRelatedPageCount();
		});
		detailContentsHolder.on('click','#relatedListPageJumpDropDown > li',function(e){
			e.stopImmediatePropagation();
		}).on('keypress','#pageToJump',function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.pageJumpHandler(e);
		});
	},
	
	
	/**
	 * Function to show total records count in listview on hover
	 * of pageNumber text
	 */
	registerEventForTotalRecordsCount : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.totalNumberOfRecords',function(e){
			var element = jQuery(e.currentTarget);
			var totalNumberOfRecords = jQuery('#totalCount').val();
			element.addClass('hide');
			element.parent().progressIndicator({});
			if(totalNumberOfRecords == '') {
				var selectedTabElement = thisInstance.getSelectedTab();
				var relatedModuleName = thisInstance.getRelatedModuleName();
				var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
				relatedController.getRelatedPageCount().then(function(){
					thisInstance.showPagingInfo();
				});
			}else{
				thisInstance.showPagingInfo();
			}
			element.parent().progressIndicator({'mode':'hide'});
		})
	},
	
	/**
	 * Function to register Event for Sorting
	 */
	registerEventForRelatedList : function(){
		var thisInstance = this;
		var detailContentsHolder = this.getContentHolder();
		detailContentsHolder.on('click','.relatedListHeaderValues',function(e){
			var element = jQuery(e.currentTarget);
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new PortfolioInformation_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.sortHandler(element);
		});
		
		detailContentsHolder.on('click', 'button.selectRelation', function(e){
			var selectedTabElement = thisInstance.getSelectedTab();
			var relatedModuleName = thisInstance.getRelatedModuleName();
			var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
			relatedController.showSelectRelationPopup().then(function(data){
				var emailEnabledModule = jQuery(data).find('[name="emailEnabledModules"]').val();
				if(emailEnabledModule){
					thisInstance.registerEventToEditRelatedStatus();
				}
			});
		});

		detailContentsHolder.on('click', 'a.relationDelete', function(e){
			e.stopImmediatePropagation();
			var element = jQuery(e.currentTarget);
			var instance = Vtiger_Detail_Js.getInstance();
			var key = instance.getDeleteMessageKey();
			var message = app.vtranslate(key);
			Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
				function(e) {
					var row = element.closest('tr');
					var relatedRecordid = row.data('id');
					var selectedTabElement = thisInstance.getSelectedTab();
					var relatedModuleName = thisInstance.getRelatedModuleName();
					var relatedController = new Vtiger_RelatedList_Js(thisInstance.getRecordId(), app.getModuleName(), selectedTabElement, relatedModuleName);
					relatedController.deleteRelation([relatedRecordid]).then(function(response){
						relatedController.loadRelatedList();
					});
				},
				function(error, err){
				}
			);
		});
	},
});