/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("BillingSpecifications_Edit_Js",{
   
},{
   
	registerEventForTypeField : function(){
		
		var ele = jQuery(document).find('[name="billing_type"]');
		
		if(ele.val() == 'Fixed Rate' || ele.val() == 'Fixed Amount'){
			jQuery(document).find('#scheduleLineItemTab').hide();			
		}else{
			jQuery(document).find('[name="amount_value"]').prop('disabled', true);
			jQuery(document).find('#scheduleLineItemTab').show();

		}
		
		ele.on('click',function(){
			
			if($(this).val() == 'Fixed Amount' || $(this).val() == 'Fixed Rate'){
				jQuery(document).find('[name="amount_value"]').prop('disabled', false);
				jQuery(document).find('#scheduleLineItemTab').hide();
			}else{
				jQuery(document).find('[name="amount_value"]').prop('disabled', true);
				jQuery(document).find('#scheduleLineItemTab').show();
			}
			
		});
		
	},
	
	registerEvents: function() {
		
		this._super();
		this.registerEventForTypeField();
		this.registerAddingNewScheduleEvent();
		this.registerDeleteLineItemEvent();
		
	},
	
	lineItemContentsContainer : false,
	
	rowSequenceHolder : false,

	basicRow : false,

	rowClass : 'lineItemRow',

	getScheduleLineItemContentsContainer : function() {
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
			this.rowSequenceHolder = jQuery('.' + this.rowClass, this.getScheduleLineItemContentsContainer()).length;
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
			var lineItemTable = this.getScheduleLineItemContentsContainer();
			this.basicRow = jQuery('.lineItemCloneCopy',lineItemTable)
		}
		var newRow = this.basicRow.clone(true,true);
		return newRow.removeClass('hide lineItemCloneCopy');
	},
	
	checkLineItemRow : function(){
		var lineItemTable = this.getScheduleLineItemContentsContainer();
		var noRow = lineItemTable.find('.lineItemRow').length;
		if(noRow >1){
			this.showLineItemsDeleteIcon();
		}else{
			this.hideLineItemsDeleteIcon();
		}
	},

	showLineItemsDeleteIcon : function(){
		var lineItemTable = this.getScheduleLineItemContentsContainer();
		lineItemTable.find('.deleteRow').show();
	},

	hideLineItemsDeleteIcon : function(){
		var lineItemTable = this.getScheduleLineItemContentsContainer();
		lineItemTable.find('.deleteRow').hide();
	},

	registerAddingNewScheduleEvent : function(){
		var thisInstance = this;
		var lineItemTable = this.getScheduleLineItemContentsContainer();
		jQuery('#btnAddSchedule').on('click',function(){
			var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
			var sequenceNumber = thisInstance.getNextLineItemRowNumber();
			newRow = newRow.appendTo(lineItemTable);
			thisInstance.checkLineItemRow();
			newRow.find('input.rowNumber').val(sequenceNumber);
			thisInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
			newRow.find('.scheduletype').addClass('select2');
			if(newRow.find('.select2-container.scheduletype').length > 1){
				newRow.find('.select2-container.scheduletype').first().remove();
			}
			vtUtils.applyFieldElementsView(newRow);
		});
	},
	
	
	updateLineItemsElementWithSequenceNumber : function(lineItemRow,expectedSequenceNumber , currentSequenceNumber){
		
		if(typeof currentSequenceNumber == 'undefined') {
			currentSequenceNumber = 0;
		}

		var idFields = new Array('from','to','type','value');

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
		var lineItemTable = this.getScheduleLineItemContentsContainer();

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
		var lineItemContentsContainer  = this.getScheduleLineItemContentsContainer();
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

	saveScheduleCount : function () {
		jQuery('#totalscheduleCount').val(jQuery('tr.'+this.rowClass, this.getScheduleLineItemContentsContainer()).length);
	},

	 registerSubmitEvent : function () {
		var self = this;
		var editViewForm = this.getForm();
		//this._super();
		editViewForm.submit(function(e){
			self.saveScheduleCount();
		});
	},
	
	registerBasicEvents: function(container){
		 this._super(container);
		 this.registerSubmitEvent();
	},
});