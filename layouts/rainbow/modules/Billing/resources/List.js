Vtiger_List_Js("Billing_List_Js",{
	
	exportTDAFormat : function(url) {
    	var self = app.controller();
        self.exportTDAFormat(url);
    },
    
	exportFidelityFormat : function(url) {
    	var self = app.controller();
        self.exportFidelityFormat(url);
    },
	
},{
	
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
		
		params.billing_date = $("#billing_date").val();
		params.billing_price_date = $("#billing_price_date").val();
		
		return params;
	},
	
	getListSearchParams: function (includeStarFilters) {
		if (typeof includeStarFilters == "undefined") {
			includeStarFilters = true;
		}
		var listViewPageDiv = this.getListViewContainer();
		var listViewTable = listViewPageDiv.find('.searchRow');
		var searchParams = new Array();
		var currentSearchParams = new Array();
		if (listViewPageDiv.find('#currentSearchParams').val()) {
			currentSearchParams = JSON.parse(listViewPageDiv.find('#currentSearchParams').val());
		}

		if (this.filterClick) {
			return;
		}
		listViewTable.find('.listSearchContributor').each(function (index, domElement) {
			var searchInfo = new Array();
			var searchContributorElement = jQuery(domElement);
			var fieldName = searchContributorElement.attr('name');
			
			if (typeof uimeta !== "undefined") {
				var fieldInfo = uimeta.field.get(fieldName);
			}

			if (typeof fieldInfo == 'undefined') {
				fieldInfo = searchContributorElement.data("fieldinfo");
			}

			if (currentSearchParams != null) {
				if (typeof fieldName != 'undefined') {
					if (fieldName in currentSearchParams) {
						delete currentSearchParams[fieldName];
					}
				}

				if ('starred' in currentSearchParams) {
					delete currentSearchParams['starred'];
				}
			}

			var searchValue = searchContributorElement.val();

			if (typeof searchValue == "object") {
				if (searchValue == null) {
					searchValue = "";
				} else {
					searchValue = searchValue.join(',');
				}
			}
			searchValue = searchValue.trim();
			if (searchValue.length <= 0) {
				//continue
				return true;
			}
			var searchOperator = 'c';
			if (fieldInfo.type == "date" || fieldInfo.type == "datetime") {
				searchOperator = 'bw';
			} else if (fieldInfo.type == 'percentage' || fieldInfo.type == "double" || fieldInfo.type == "integer"
					|| fieldInfo.type == 'currency' || fieldInfo.type == "number" || fieldInfo.type == "boolean" ||
					fieldInfo.type == "picklist") {
				searchOperator = 'e';
			}
			var storedOperator = searchContributorElement.parent().parent().find('.operatorValue').val();
			if (storedOperator) {
				searchOperator = storedOperator;
				storedOperator = false;
			}
			searchInfo.push(fieldName);
			searchInfo.push(searchOperator);
			searchInfo.push(searchValue);

			searchParams.push(searchInfo);
		});
		for (var i in currentSearchParams) {
//			Number.isInteger(parseInt(i)) (Previously Used which is not supported by IE.)
//			http://codereview.stackexchange.com/questions/101484/simple-function-to-verify-if-a-number-is-integer
//			http://stackoverflow.com/questions/26482645/number-isintegerx-which-is-created-can-not-work-in-ie
			if (!this.isInteger(parseInt(i))) {
				continue;
			}
			var fieldName = currentSearchParams[i]['fieldName'];
			var searchValue = currentSearchParams[i]['searchValue'];
			var searchOperator = currentSearchParams[i]['comparator'];
			if (fieldName == null || fieldName.length <= 0) {
				continue;
			}
			var searchInfo = new Array();
			searchInfo.push(fieldName);
			searchInfo.push(searchOperator);
			searchInfo.push(searchValue);
			searchParams.push(searchInfo);
		}
		
		if($("#billing_price_date").val() != ''){
			var searchInfo = new Array();
			searchInfo.push('beginning_price_date');
			searchInfo.push('e');
			searchInfo.push($("#billing_price_date").val());
			searchParams.push(searchInfo);
		}
		
		if($("#billing_date").val() != ''){
			var searchInfo = new Array();
			searchInfo.push('start_date');
			searchInfo.push('e');
			searchInfo.push($("#billing_date").val());
			searchParams.push(searchInfo);
		}
		
		if (searchParams.length > 0) {
			var listSearchParams = new Array(searchParams);
		} else {
			var listSearchParams = new Array();
		}
		if (includeStarFilters) {
			listSearchParams = this.addStarSearchParams(listSearchParams);
		}
		
		return listSearchParams;
	},
	
	
	registerEvents: function(callParent) {
		this._super();
		this.registerBillingPeriodRangeChangeEvent();
    },
    
    registerBillingPeriodRangeChangeEvent: function(){
    	
		var self = this;
    	
		var params = {};
    	
		$(document).on("click", ".filter_billing", function(){
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
		vtUtils.applyFieldElementsView($("#billing-filters"));
	},
	
	
	exportTDAFormat : function(url) {
        
		var listInstance = this;
        
		var validationResult = listInstance.checkListRecordSelected();
		
		if(!validationResult){
			
			var postData = listInstance.getListSelectAllParams(true);

			var params = {
				"url":url,
				"data" : postData
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerExportFileModalEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
	
	exportFidelityFormat : function(url) {
        
		var listInstance = this;
        
		var validationResult = listInstance.checkListRecordSelected();
		
		if(!validationResult){
			
			var postData = listInstance.getListSelectAllParams(true);

			var params = {
				"url":url,
				"data" : postData
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerExportFileModalEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
	
	registerExportFileModalEvents : function(container) {
        
		var self = this;
        
		var addFolderForm = jQuery('#exportFile');
		
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
	
});