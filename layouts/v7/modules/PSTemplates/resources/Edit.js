Inventory_Edit_Js("PSTemplates_Edit_Js",{},{
	// registerBasicEvents: function(container){
	// 	this._super(container);
	// },
	// setNetTotal : function(netTotalValue){
	// 	if(netTotalValue != undefined && netTotalValue){
	// 		jQuery('#total').val(parseFloat(netTotalValue));
	// 	}else{
	// 		jQuery('#total').val(0);
	// 	}
	// 	return this;
	// },
	// setGrandTotal : function(grandTotalValue) {
	// 	return this;
	// },
	registerSubmitEvent : function () {
		var self = this;
		var editViewForm = this.getForm();
		//this._super();
		editViewForm.submit(function(e){
			var deletedItemInfo = jQuery('.deletedItem',editViewForm);
			if(deletedItemInfo.length > 0){
				e.preventDefault();
				var msg = app.vtranslate('JS_PLEASE_REMOVE_LINE_ITEM_THAT_IS_DELETED');
				app.helper.showErrorNotification({"message" : msg});
				editViewForm.removeData('submit');
				return false;
			}
			else if(jQuery('.lineItemRow').length<=0){
				e.preventDefault();
				msg = app.vtranslate('JS_NO_LINE_ITEM');
				app.helper.showErrorNotification({"message" : msg});
				editViewForm.removeData('submit');
				return false;
			}
			self.updateLineItemElementByOrder();
			var lineItemTable = self.getLineItemContentsContainer();
			jQuery('.discountSave',lineItemTable).trigger('click');
			self.updateLineItemElementByOrder();
			self.saveProductCount();
			self.saveSubTotalValue();
			self.saveTotalValue();
			self.savePreTaxTotalValue();
			return true;
		})
	},
	registerEventForChangeTargetModule: function () {
		var thisInstance = this;
		var targetModuleEle = jQuery('[name="target_module"]');
		if(app.getRecordId() != 0) {
			$('.fieldBlockContainer').css('display', 'block');
			$('.btn-addProduct').css('display', 'block');
		}
		if(targetModuleEle.length > 0){
			var targetModule = targetModuleEle.val();
			thisInstance.loadItemDetail(targetModule);
			targetModuleEle.on('change',function(){
				$('.fieldBlockContainer').css('display', 'block');
				$('.btn-addProduct').css('display', 'block');
				var targetModule = jQuery(this).val();
				thisInstance.loadItemDetail(targetModule);
			});
		}
	},
	loadItemDetail: function (targetModule) {
		if(targetModule != '' && targetModule != undefined){
			var lineItemTab = jQuery('#lineItemTab');
			params = {
				module: 'Quoter',
				view: 'MassActionAjax',
				mode: 'getItemsEdit',
				record:app.getRecordId(),
				current_module: targetModule,
				is_template: true

			};
			app.helper.showProgress();
			AppConnector.request(params).then(
				function (response) {
					app.helper.hideProgress();
					if (response.result.isActive == true) {
						jQuery("[name='subtotal'], [name='total']").remove();
						lineItemTab.html(response.result.html);
						lineItemTab.css('table-layout', 'fixed');
						jQuery('.lineItemContainer').css('margin', 0);
						jQuery('#lineItemResult').html(response.result.html1);
						var setting = response.result.setting;
						var totalSetting = response.result.totalSettings;
						//add section dropdown
						// var sectionSettings = response.result.sectionSettings;
						// Quoter_Js.addSectionDropDown(sectionSettings);
						// Quoter_Js.addRunningSubTotalDropDown(totalSetting);
						var quoterInstance = new Quoter_Js();
						quoterInstance.columnSetting = setting;
						quoterInstance.totalSetting = totalSetting;
						quoterInstance.separator = response.result.separator;
						quoterInstance.fixFieldName();
						lineItemTab.find('tr.lineItemRow').each(function () {
							Quoter_Js.fixWidthInput(jQuery(this));
							vtUtils.applyFieldElementsView(jQuery(this));
						});
						quoterInstance._registerEvents();

						Quoter_Js.registerEventForProductImages();
						//thisInstance.updateParentValueForAllItems();
						quoterInstance.calculateAllRunningSubTotalValue();
						quoterInstance.calculateTotalValueByFormula();
					}
				}
			);
		}
	},
	registerEvents: function(){
		this._super();
		this.registerEventForChangeTargetModule();
	}
});