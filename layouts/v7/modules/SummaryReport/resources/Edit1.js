/* ********************************************************************************
 * The content of this file is subject to the Summary Report ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

SummaryReport_Edit_Js("SummaryReport_Edit1_Js",{},{

	step1Container : false,
	
	init : function() {
		this.initialize();
	},
	/**
	 * Function to get the container which holds all the reports step1 elements
	 * @return jQuery object
	 */
	getContainer : function() {
		return this.step1Container;
	},

	/**
	 * Function to set the reports step1 container
	 * @params : element - which represents the reports step1 container
	 * @return : current instance
	 */
	setContainer : function(element) {
		this.step1Container = element;
		return this;
	},
	
	/**
	 * Function  to intialize the reports step1
	 */
	initialize : function(container) {
		if(typeof container == 'undefined') {
			container = jQuery('#report_step1');
		}
		if(container.is('#report_step1')) {
			this.setContainer(container);
		}else{
			this.setContainer(jQuery('#report_step1'));
		}
	},

    getSelectedModuleFields: function (container) {
        var thisInstance = this;
        var select_module = jQuery('#primary_module').val();
        var reporttype = jQuery('#reporttype').val();
		app.helper.showProgress();
		var actionParams = {
            "type":"POST",
            "url": "index.php?module=SummaryReport&view=EditAjax&mode=getFields",
            "dataType":"html",
            "data" : {
                "select_module" : select_module,
                "reporttype" : reporttype
            }
        };
		app.request.post(actionParams).then(
			function(err,data){
				if(err === null) {
					app.helper.hideProgress();;
					if(data) {
						container.find('#fields').html(data);
						// TODO Make it better with jQuery.on
						vtUtils.applyFieldElementsView(container);;
						//register all select2 Elements
						vtUtils.applyFieldElementsView(container.find('select.select2'));
						thisInstance.makeFieldsListSortable(container);
					}
				}
			});
    },

	registerPrimaryModuleChangeEvent : function(container){
		var thisInstance = this;
		jQuery('#primary_module').on('change',function(e){
            thisInstance.getSelectedModuleFields(container);
		});
        jQuery('#reporttype').on('change',function(e){
            thisInstance.getSelectedModuleFields(container);
        });
	},

    makeFieldsListSortable : function (container) {
        var thisInstance = this;
        var selectElement = container.find('#selected_fields');
        var select2Element = app.getSelect2ElementFromSelect(selectElement);
        var select2ChoiceElement = select2Element.find('ul.select2-choices');
        select2ChoiceElement.sortable({
            'containment': select2ChoiceElement,
            start: function() { },
            update: function() {
                var selectedValuesByOrder = {};
                var selectedOptions = selectElement.find('option:selected');
                var orderedSelect2Options = select2Element.find('li.select2-search-choice').find('div');
                var i = 1;
                orderedSelect2Options.each(function(index,element){
                    var chosenOption = jQuery(element);
                    selectedOptions.each(function(optionIndex, domOption){
                        var option = jQuery(domOption);
                        if(option.html() == chosenOption.html()) {
                            selectedValuesByOrder[i++] = option.val();
                            return false;
                        }
                    });
                });
                container.find('input[name="selectedFieldsList"]').val(JSON.stringify(selectedValuesByOrder));
            }
        });
    },
	/*
	 * Function to check Duplication of report Name
	 * returns boolean true or false
	 */
	checkDuplicateName : function(details) {
		var aDeferred = jQuery.Deferred();
		/*var moduleName = app.getModuleName();
		var params = {
			'module' : moduleName,
			'action' : "CheckDuplicate",
			'reportname' : details.reportName,
			'record' : details.reportId,
			'isDuplicate' : details.isDuplicate
		}
		
		AppConnector.request(params).then(
			function(data) {
				var response = data['result'];
				var result = response['success'];
				if(result == true) {
					aDeferred.reject(response);
				} else {
					aDeferred.resolve(response);
				}
			},
			function(error,err){
				aDeferred.reject();
			}
        );*/
        aDeferred.resolve(response);
		return aDeferred.promise();
	},

    updateFieldOrder : function(container) {
        var selectedValuesByOrder = {};
        var selectElement = container.find('#selected_fields');
        var select2Element = app.getSelect2ElementFromSelect(selectElement);
        var selectedOptions = selectElement.find('option:selected');
        var orderedSelect2Options = select2Element.find('li.select2-search-choice').find('div');
        var i = 1;
        orderedSelect2Options.each(function(index,element){
            var chosenOption = jQuery(element);
            selectedOptions.each(function(optionIndex, domOption){
                var option = jQuery(domOption);
                if(option.html() == chosenOption.html()) {
                    selectedValuesByOrder[i++] = option.val();
                    return false;
                }
            });
        });
        container.find('input[name="selectedFieldsList"]').val(JSON.stringify(selectedValuesByOrder));
    },
	submit : function(){
		var thisInstance = this;
		var aDeferred = jQuery.Deferred();
		var form = this.getContainer();
        thisInstance.updateFieldOrder(form);

		var formData = form.serializeFormData();
		
		var params = {};
		var reportName = jQuery.trim(formData.reportname);
		var reportId = formData.record;

		app.helper.showProgress();

		app.request.post({data:formData}).then(
			function(err,data) {
				form.hide();
				app.helper.hideProgress();
				aDeferred.resolve(data);
			},
			function(error,err){

			}
		);
		return aDeferred.promise();
	},

	
	registerEvents : function(){
        var container = this.getContainer();
		this.registerPrimaryModuleChangeEvent(container);
        this.makeFieldsListSortable(container);
        //
		// var opts = app.validationEngineOptions;
		// // to prevent the page reload after the validation has completed
		// opts['onValidationComplete'] = function(form,valid) {
         //    //returns the valid status
         //    return valid;
        // };
		// opts['promptPosition'] = "bottomRight";
		// container.validationEngine(opts);
	}
});
jQuery(document).ready(function() {
	var instance = new SummaryReport_Edit1_Js();
	instance.registerEvents();
});