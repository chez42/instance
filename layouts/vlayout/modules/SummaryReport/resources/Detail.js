/* ********************************************************************************
 * The content of this file is subject to the Summary Report ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

jQuery.Class("SummaryReport_Detail_Js",{},{


    calculateValues : function(){
        //handled advanced filters saved values.
        var advfilterlist = this.advanceFilterInstance.getValues();
        return JSON.stringify(advfilterlist);
    },
    /**
     * Function which will register condition change
     */
    registerConditionChange : function() {
        var filterContainer = jQuery('.filterContainer');
        var thisInstance = this;
        filterContainer.on('change','select[name="comparator"]', function(e){
            var comparatorSelectElement = jQuery(e.currentTarget);
            var row = comparatorSelectElement.closest('div.conditionRow');
            //To handle the validation depending on condtion
            thisInstance.loadFieldSpecificUi(filterContainer);

        });
    },

    getDateFieldUI: function(comparatorSelectedOptionVal,dateSpecificConditions, fieldName, dateFormat) {
        if(comparatorSelectedOptionVal == 'bw' || comparatorSelectedOptionVal == 'custom'){
            var html = '<div class="date"><input class="dateField" data-calendar-type="range" name="'+ fieldName +'" data-date-format="'+ dateFormat +'" type="text" ReadOnly="true" value=""></div>';
            var element = jQuery(html);
            return element;
        }
        else if (comparatorSelectedOptionVal in dateSpecificConditions) {
            var startValue = dateSpecificConditions[comparatorSelectedOptionVal]['startdate'];
            var endValue = dateSpecificConditions[comparatorSelectedOptionVal]['enddate'];
            if(comparatorSelectedOptionVal == 'today' || comparatorSelectedOptionVal == 'tomorrow' || comparatorSelectedOptionVal == 'yesterday') {
                var html = '<input name="'+ fieldName +'" type="text" ReadOnly="true" value="'+ startValue +'">';
            } else {
                var html = '<input name="'+ fieldName +'" type="text" ReadOnly="true" value="'+ startValue +','+ endValue +'">';
            }
            return jQuery(html);
        }
        else {
            var html = '<div class="input-append">'+
                '<div class="date">'+
                '<input class="dateField" type="text" name="'+ fieldName +'"  data-date-format="'+ dateFormat +'"  value="" />'+
                '<span class="add-on"><i class="icon-calendar"></i></span>'+
                '</div>'+
                '</div>';
            var fieldUi = jQuery(html);
            var dateTimeFieldValue = fieldUi.find('.dateField').val();
            var dateValue = dateTimeFieldValue.split(' ');
            if(dateValue[1] == '00:00:00') {
                dateTimeFieldValue = dateValue[0];
            }
            else if(comparatorSelectedOptionVal == 'e' || comparatorSelectedOptionVal == 'n' ||
                comparatorSelectedOptionVal == 'b' || comparatorSelectedOptionVal == 'a') {
                var dateTimeArray = dateTimeFieldValue.split(' ');
                dateTimeFieldValue = dateTimeArray[0];
            }
            fieldUi.find('.dateField').val(dateTimeFieldValue);
            return fieldUi;
        }
    },

    loadFieldSpecificUi : function(container) {
        var row = container.find('div.conditionRow');
        var fieldUiHolder = row.find('.fieldUiHolder');
        var conditionSelectElement = row.find('select[name="comparator"]');
        var dateConditionInfo = jQuery('[name="date_filters"]').data('value');;
        var fieldName = 'date_filter';
        var dateFormat = jQuery('[name="date_format"]').data('value');
        var fieldSpecificUi = this.getDateFieldUI(conditionSelectElement.val(),dateConditionInfo, fieldName, dateFormat);

        //remove validation since we dont need validations for all eleements
        // Both filter and find is used since we dont know whether the element is enclosed in some conainer like currency



        if(fieldSpecificUi.find('.add-on').length > 0){
            fieldSpecificUi.filter('.input-append').addClass('row-fluid');
            fieldSpecificUi.find('.input-append').addClass('row-fluid');
            fieldSpecificUi.filter('.input-prepend').addClass('row-fluid');
            fieldSpecificUi.find('.input-prepend').addClass('row-fluid');
            fieldSpecificUi.find('input[type="text"]').css('width','79%');
        } else {
            fieldSpecificUi.filter('[name="'+ fieldName +'"]').addClass('row-fluid');
            fieldSpecificUi.find('[name="'+ fieldName +'"]').addClass('row-fluid');
        }

        fieldSpecificUi.filter('[name="'+ fieldName +'"]').attr('data-value', 'value').removeAttr('data-validation-engine');
        fieldSpecificUi.find('[name="'+ fieldName +'"]').attr('data-value','value').removeAttr('data-validation-engine');


        fieldUiHolder.html(fieldSpecificUi);

        if (fieldSpecificUi.has('input.dateField').length > 0){
            var calendarType = fieldSpecificUi.find('.dateField').data('calendarType');
            if(calendarType == 'range'){
                var customParams = {
                    calendars: 3,
                    mode: 'range',
                    className : 'rangeCalendar',
                    onChange: function(formated) {
                        fieldSpecificUi.find('.dateField').val(formated.join(','));
                    }
                }
                app.registerEventForDatePickerFields(fieldSpecificUi,false,customParams);
            }else{
                app.registerEventForDatePickerFields(fieldSpecificUi);
            }
        }

        // Is Empty, today, tomorrow, yesterday conditions does not need any field input value - hide the UI
        // re-enable if condition element is chosen.
        var specialConditions = ["y","today","tomorrow","yesterday","ny"];
        if (specialConditions.indexOf(conditionSelectElement.val()) != -1) {
            fieldUiHolder.hide();
        } else {
            fieldUiHolder.show();
        }

        return this;
    },

    registerSaveOrGenerateReportEvent : function(container){
        var thisInstance = this;
        jQuery('.generateReport').on('click',function(e){
            e.preventDefault();
            var progressIndicatorElement = jQuery.progressIndicator({
            });
            var postData = {
                'record' : jQuery('#recordId').val(),
                'view' : "SaveAjax",
                'module' : app.getModuleName(),
                'comparator' : container.find('select[name="comparator"]').val(),
                'value' : container.find('input[name="date_filter"]').val()
            };
            if(container.find('input[name="include_comments"]').length >0) {
                if(container.find('input[name="include_comments"]').is(':checked')) {
                    postData['include_comments']=1;
                }
            }
            AppConnector.request(postData).then(
                function(data){
                    progressIndicatorElement.progressIndicator({mode:'hide'})
                    jQuery('div.contentsDiv').find('#reportContentsDiv').html(data);
                    Vtiger_Helper_Js.showHorizontalTopScrollBar();

                    // To get total records count
                    var count  = parseInt(jQuery('#updatedCount').val());
                    jQuery('#countValue').text(count);
                }
            );
        });
    },

    registerExportEvents : function(container) {
        jQuery('.reportActions').on('click',function(e){
            var element = jQuery(e.currentTarget);
            var href = element.data('href');
            var comparator = container.find('select[name="comparator"]').val();
            var value = container.find('input[name="date_filter"]').val();
            var include_comments=0;
            if(container.find('input[name="include_comments"]').length >0) {
                if(container.find('input[name="include_comments"]').is(':checked')) {
                    include_comments = 1;
                }
            }
            var newEle = '<form action='+href+' method="POST">'+
            '<input type = "hidden" name ="'+csrfMagicName+'"  value=\''+csrfMagicToken+'\'>'+
            '<input type="hidden" value="" name="comparator" id="comparator" />' +
            '<input type="hidden" value="" name="value" id="value" />' +
            '<input type="hidden" value="" name="include_comments" id="include_comments" />' +
            '</form>';
            var headerContainer =  jQuery('div.reportsDetailHeader');
            var ele = jQuery(newEle);
            var form = ele.appendTo(headerContainer);
            form.find('#comparator').val(comparator);
            form.find('#value').val(value);
            form.find('#include_comments').val(include_comments);
            form.submit();
        });
    },

    registerEvents: function () {
        var container = jQuery('#detailView');
        app.changeSelectElementView(container);
        this.registerSaveOrGenerateReportEvent(container);
        this.registerExportEvents(container);
        this.registerConditionChange();
        container.find('select[name="comparator"]').trigger('change');
        // To get total records count
        var count  = parseInt(jQuery('#updatedCount').val());
        jQuery('#countValue').text(count);
    }
});
