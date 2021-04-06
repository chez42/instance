/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Group_Edit_Js",{
   
},{
   
	registerReferenceSelectionEvent : function(container) {
        var thisInstance = this;

       jQuery('input[name="household"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent,function(e,data){
           var params = {
    		   'module' : 'Group',
    		   'action' : 'ActionAjax',
    		   'mode'   : 'getAccountPortfolios',
    		   'record' : data.record
           };
    	   
    	   app.request.post({data:params}).then(function(err,res){
    		   
    		   $.each(res,function(i,val){
    			   thisInstance.AddingNewItemRow(val,i);
    		   });
    		   
    	   });
    	   
        });
	},
	
	 AddingNewItemRow: function(recordData,ind){
		 
        var thisInstance = this;
        
        var lineItemTable = this.getLineItemContentsContainer();
        
        var totalLineItems = lineItemTable.find(".lineItemRow").length;
      
        if(totalLineItems>0){
        	
        	jQuery.each(lineItemTable.find(".lineItemRow"), function(i,lineItemRow){
        		
        		var rowIndex = jQuery(lineItemRow).find(".rowNumber").val();
        		
        		var portfolioId = jQuery(lineItemRow).find("#portfolioid"+rowIndex);
        
        	    if(typeof portfolioId.val() == "undefined" || portfolioId.val() == null || portfolioId.val() == ''){
        	    
        	    	jQuery(lineItemRow).find(".deleteRow").trigger("click");
        	    }     
        	});
        }
        
        var lastrw = jQuery('#lineItemTab tbody tr:last');
        prod = lastrw.find('input.portfolio');
        seqno = thisInstance.getNextLineItemRowNumber();
    
        var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
        var sequenceNumber = seqno;
        newRow = newRow.appendTo(lineItemTable);
        thisInstance.checkLineItemRow();
        newRow.find('input.rowNumber').val(sequenceNumber);
        thisInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
        newRow.find('[name="portfolioid'+sequenceNumber+'"]').val(recordData.portfolioid);
        newRow.find('[name="portfolioid'+sequenceNumber+'_display"]').val(recordData.portfolioname).prop('disabled',true);
        newRow.find('.portfolioClearReferenceSelection').removeClass('hide');

    },
	
	registerEvents: function() {
		this._super();
		this.registerAddingNewItemEvent();
		this.registerDeleteLineItemEvent();
	},
	
	lineItemContentsContainer : false,
	
	rowSequenceHolder : false,

	basicRow : false,

	rowClass : 'lineItemRow',

	getLineItemContentsContainer : function() {
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
			this.rowSequenceHolder = jQuery('.' + this.rowClass, this.getLineItemContentsContainer()).length;
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
			var lineItemTable = this.getLineItemContentsContainer();
			this.basicRow = jQuery('.lineItemCloneCopy',lineItemTable)
		}
		var newRow = this.basicRow.clone(true,true);
		return newRow.removeClass('hide lineItemCloneCopy');
	},
	
	checkLineItemRow : function(){
		var lineItemTable = this.getLineItemContentsContainer();
		var noRow = lineItemTable.find('.lineItemRow').length;
		if(noRow >1){
			this.showLineItemsDeleteIcon();
		}else{
			this.hideLineItemsDeleteIcon();
		}
	},

	showLineItemsDeleteIcon : function(){
		var lineItemTable = this.getLineItemContentsContainer();
		lineItemTable.find('.deleteRow').show();
	},

	hideLineItemsDeleteIcon : function(){
		var lineItemTable = this.getLineItemContentsContainer();
		lineItemTable.find('.deleteRow').hide();
	},

	registerAddingNewItemEvent : function(){
		var thisInstance = this;
		var lineItemTable = this.getLineItemContentsContainer();
		jQuery('#btnAddItem').on('click',function(){
			var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
			var sequenceNumber = thisInstance.getNextLineItemRowNumber();
			newRow = newRow.appendTo(lineItemTable);
			//thisInstance.checkLineItemRow();
			newRow.find('input.rowNumber').val(sequenceNumber);
			thisInstance.updateLineItemsElementWithSequenceNumber(newRow,sequenceNumber);
			
			vtUtils.applyFieldElementsView(newRow);
		});
	},
	
	
	updateLineItemsElementWithSequenceNumber : function(lineItemRow,expectedSequenceNumber , currentSequenceNumber){
		
		if(typeof currentSequenceNumber == 'undefined') {
			currentSequenceNumber = 0;
		}

		var idFields = new Array('portfolioid','portfolioid_display','billingspecificationid','billingspecificationid_display','active');

		var expectedRowId = 'row'+expectedSequenceNumber;
		
		for(var idIndex in idFields ) {
			
			var elementId = idFields[idIndex];
			if(elementId == 'portfolioid_display'){
				elementId = elementId.split("_");
				var actualElementId = elementId[0] + currentSequenceNumber + "_" + elementId[1];
				var expectedElementId = elementId[0] + expectedSequenceNumber + "_" + elementId[1];
			}else if(elementId == 'billingspecificationid_display'){
				
				elementId = elementId.split("_");
				var actualElementId = elementId[0] + currentSequenceNumber + "_" + elementId[1];
				var expectedElementId = elementId[0] + expectedSequenceNumber + "_" + elementId[1];
				
			} else {
				var actualElementId = elementId + currentSequenceNumber;
				var expectedElementId = elementId + expectedSequenceNumber;
			}
			lineItemRow.find('#'+actualElementId).attr('id',expectedElementId)
					   .filter('[name="'+actualElementId+'"]').attr('name',expectedElementId);
		}

		return lineItemRow.attr('id',expectedRowId);
	},
	
	registerDeleteLineItemEvent : function(){
		var thisInstance = this;
		var lineItemTable = this.getLineItemContentsContainer();

		lineItemTable.on('click','.deleteRow',function(e){
			var element = jQuery(e.currentTarget);
			element.closest('tr.'+ thisInstance.rowClass).remove();
			//thisInstance.checkLineItemRow();
			thisInstance.updateLineItemElementByOrder();
			thisInstance.rowSequenceHolder = false;
		});
	 },

	 updateLineItemElementByOrder : function () {
		var lineItemContentsContainer  = this.getLineItemContentsContainer();
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

	saveItemCount : function () {
		jQuery('#totalItemCount').val(jQuery('tr.'+this.rowClass, this.getLineItemContentsContainer()).length);
	},

	 registerSubmitEvent : function () {
		var self = this;
		var editViewForm = this.getForm();
		//this._super();
		editViewForm.submit(function(e){
			self.saveItemCount();
		});
	},
	
	registerBasicEvents: function(container){
		 this._super(container);
		 this.registerSubmitEvent();
		 this.registerReferenceSelectionEvent(container);
	},
});