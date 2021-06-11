Vtiger_List_Js("Billing_List_Js",{},{
	
	getDefaultParams: function () {
		
		var container = this.getListViewContainer();
		var pageNumber = container.find('#pageNumber').val();
		var module = this.getModuleName();
		var parent = app.getParentModuleName();
		var cvId = this.getCurrentCvId();
		var orderBy = container.find('[name="orderBy"]').val();
		var sortOrder = container.find('[name="sortOrder"]').val();
		var appName = container.find('#appName').val();
		var params = {
			'module': module,
			'parent': parent,
			'page': pageNumber,
			'view': "List",
			'viewname': cvId,
			'orderby': orderBy,
			'sortorder': sortOrder,
			'app': appName
		}
		params.search_params = JSON.stringify(this.getListSearchParams());
		params.tag_params = JSON.stringify(this.getListTagParams());
		params.nolistcache = (container.find('#noFilterCache').val() == 1) ? 1 : 0;
		params.starFilterMode = container.find('.starFilter li.active a').data('type');
		params.list_headers = container.find('[name="list_headers"]').val();
		params.tag = container.find('[name="tag"]').val();
		
		params.billing_period_range = $("#billing_period_range").val();
		
		return params;
	},
	
	registerEvents: function(callParent) {
		this._super();
		this.registerBillingPeriodRangeChangeEvent();
    },
    
    registerBillingPeriodRangeChangeEvent: function(){
    	
		var self = this;
    	
		var params = {};
    	
		$(document).on("change", "#billing_period_range", function(){
    		self.clearList();
    		self.loadListViewRecords();
    	});
		
    },
    
    postLoadListViewRecords: function (res) {
		var self = this;
		self.placeListContents(res);
		app.event.trigger('post.listViewFilter.click', jQuery('.searchRow'));
		app.helper.hideProgress();
		self.markSelectedIdsCheckboxes();
		self.registerDynamicListHeaders();
		self.registerPostLoadListViewActions();
		vtUtils.applyFieldElementsView($("#billing-period-range-div"));
	},
	
});