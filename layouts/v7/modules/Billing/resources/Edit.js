/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Billing_Edit_Js",{
   
},{
   
	registerEvents: function() {
		
		this._super();
		this.registerAddingNewCapitalFlowEvent();
		this.registerDeleteLineItemEvent();
		
	},
	
	lineItemContentsContainer : false,
	
	rowSequenceHolder : false,

	basicRow : false,

	rowClass : 'lineItemRow',

	getCaptialFlowLineItemContentsContainer : function() {
		if(this.lineItemContentsContainer == false) {
			this.setLineItemContainer(jQuery('#LineItemTab'));
		}
		return this.lineItemContentsContainer;
	},

	setLineItemContainer : function(element) {
		this.lineItemContentsContainer = element;
		return this;
	},
	
    loadRowSequenceNumber: function() {
		if(this.rowSequenceHolder == false) {
			this.rowSequenceHolder = jQuery('.' + this.rowClass, this.getCaptialFlowLineItemContentsContainer()).length;
		}
		return this;
    },

	getNextLineItemRowNumber : function() {
		if(this.rowSequenceHolder == false){
			this.loadRowSequenceNumber();
		}
		return ++this.rowSequenceHolder;
	},
	
	
	getBasicRow : function() {
		if(this.basicRow == false){
			var lineItemTable = this.getCaptialFlowLineItemContentsContainer();
			this.basicRow = jQuery('.lineItemCloneCopy',lineItemTable)
		}
		var newRow = this.basicRow.clone(true,true);
		return newRow.removeClass('hide lineItemCloneCopy');
	},
	
	checkLineItemRow : function(){
		var lineItemTable = this.getCaptialFlowLineItemContentsContainer();
		var noRow = lineItemTable.find('.lineItemRow').length;
		if(noRow >1){
			this.showLineItemsDeleteIcon();
		}else{
			this.hideLineItemsDeleteIcon();
		}
	},

	showLineItemsDeleteIcon : function(){
		var lineItemTable = this.getCaptialFlowLineItemContentsContainer();
		lineItemTable.find('.deleteRow').show();
	},

	hideLineItemsDeleteIcon : function(){
		var lineItemTable = this.getCaptialFlowLineItemContentsContainer();
		lineItemTable.find('.deleteRow').hide();
	},

	registerAddingNewCapitalFlowEvent : function(){
		var thisInstance = this;
		var lineItemTable = this.getCaptialFlowLineItemContentsContainer();
		jQuery('#btnCapitalFlows').on('click',function(){
			var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
			var sequenceNumber = thisInstance.getNextLineItemRowNumber();
			newRow = newRow.appendTo(lineItemTable);
			thisInstance.checkLineItemRow();
			newRow.find('input.rowNumber').val(sequenceNumber);
			thisInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
			
			//vtUtils.applyFieldElementsView(newRow);
		});
	},
	
	
	updateLineItemsElementWithSequenceNumber : function(lineItemRow,expectedSequenceNumber , currentSequenceNumber){
		
		if(typeof currentSequenceNumber == 'undefined') {
			currentSequenceNumber = 0;
		}

		var idFields = new Array('trade_date','diff_days','totalamount','totaldays','transactionamount','transactiontype','trans_fee','totaladjustment');

		var expectedRowId = 'row'+expectedSequenceNumber;
		
		for(var idIndex in idFields ) {
			
			var elementId = idFields[idIndex];
			
			var actualElementId = elementId + currentSequenceNumber;
			var expectedElementId = elementId + expectedSequenceNumber;
			
			lineItemRow.find('#'+actualElementId).attr('id',expectedElementId)
					   .filter('[name="'+actualElementId+'"]').attr('name',expectedElementId);
		}

		return lineItemRow.attr('id',expectedRowId);
	},
	
	registerDeleteLineItemEvent : function(){
		var thisInstance = this;
		var lineItemTable = this.getCaptialFlowLineItemContentsContainer();

		lineItemTable.on('click','.deleteRow',function(e){
			var element = jQuery(e.currentTarget);
			element.closest('tr.'+ thisInstance.rowClass).remove();
			thisInstance.checkLineItemRow();
			thisInstance.updateLineItemElementByOrder();
			thisInstance.rowSequenceHolder = false;
			thisInstance.changeAmount();
		});
	 },

	 updateLineItemElementByOrder : function () {
		var lineItemContentsContainer  = this.getCaptialFlowLineItemContentsContainer();
		var thisInstance = this;
		jQuery('tr.'+this.rowClass ,lineItemContentsContainer).each(function(index,domElement){
			var lineItemRow = jQuery(domElement);
			var expectedRowIndex = (index+1);
			var expectedRowId = 'row'+expectedRowIndex;
			var actualRowId = lineItemRow.attr('id');
			if(expectedRowId != actualRowId) {
				var actualIdComponents = actualRowId.split('row');
				lineItemRow.find(".rowNumber").val(expectedRowIndex);
				thisInstance.updateLineItemsElementWithSequenceNumber(lineItemRow, expectedRowIndex, actualIdComponents[1]);
			}
		});
	},

	saveCapitalFlowsCount : function () {
		jQuery('#totalCaptialFlowCount').val(jQuery('tr.'+this.rowClass, this.getCaptialFlowLineItemContentsContainer()).length);
	},

	 registerSubmitEvent : function () {
		var self = this;
		var editViewForm = this.getForm();
		//this._super();
		editViewForm.submit(function(e){
			self.saveCapitalFlowsCount();
		});
	},
	
	registerBasicEvents: function(container){
		 this._super(container);
		 this.registerSubmitEvent();
	},
});