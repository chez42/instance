Vtiger_Detail_Js("ModSecurities_Detail_Js",{
	
	syncFromYahooFinance : function(symbol){
		
		var element = jQuery('<div></div>');
		
		app.helper.showProgress();
		var recordId =  jQuery('#recordId').val();
		var moduleName = app.getModuleName();
			
		var params = {
			action : 'syncFromYahooFinance',
			record : recordId,
			module : moduleName,
		};

		AppConnector.request(params).then(
			function(data) {
				
				app.helper.hideProgress();
				if(data){
					var response = data.result;
					var message = response.message;
					if(response.success){
						 app.helper.showSuccessNotification({message:app.vtranslate(message)});
						 jQuery('li[data-label-key="Security Details"]').trigger('click');
					} else {
						app.helper.showErrorNotification({'message': app.vtranslate(message)})
					}
				}
			}
		);
						
	}
},{
	
	loadSelectedTabContents: function(tabElement, urlAttributes){
		var self = this;
		var detailViewContainer = this.getDetailViewContainer();
		var url = tabElement.data('url');
		if(url){
			self.loadContents(url,urlAttributes).then(function(data){
				self.deSelectAllrelatedTabs();
				self.markRelatedTabAsSelected(tabElement);
				var container = jQuery('.relatedContainer');
				app.event.trigger("post.relatedListLoad.click",container.find(".searchRow"));
				// Added this to register pagination events in related list
				var relatedModuleInstance = self.getRelatedController();
				//Summary tab is clicked
				if(tabElement.data('linkKey') == self.detailViewSummaryTabLabel) {
					self.registerSummaryViewContainerEvents(detailViewContainer);
					self.registerEventForPicklistDependencySetup(self.getForm());
				}

				//Detail tab is clicked
				if(tabElement.data('linkKey') == self.detailViewDetailTabLabel) {
					self.registerEventForPicklistDependencySetup(self.getForm());
				}

				// Registering engagement events if clicked tab is History
				if(tabElement.data('labelKey') == self.detailViewHistoryTabLabel){
					var engagementsContainer = jQuery(".engagementsContainer");
					if(engagementsContainer.length > 0){
						app.event.trigger("post.engagements.load");
					}
				}

				relatedModuleInstance.initializePaginationEvents();
				
				if(!container.length){
					var joucontainer = jQuery('.journalsrelatedContainer');
					if(joucontainer.length){
						var instance =   Vtiger_Journal_Js.getInstance();
						instance.registerEvents();
					}
					var historicalDataContainer = jQuery('.historicalDataRelatedContainer');
					if(historicalDataContainer.length){
						var instance =   Vtiger_HistoricalDataList_Js.getInstance();
						instance.registerEvents();
					}
				}
				
				//prevent detail view ajax form submissions
				jQuery('form#detailView').on('submit', function(e) {
					e.preventDefault();
				});
			});
		}
	},
	
});
	
