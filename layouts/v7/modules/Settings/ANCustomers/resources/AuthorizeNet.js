/* ********************************************************************************
 * The content of this file is subject to the VTEAuthnet("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
var Vtiger_VTEPayments_AuthorizeNet_Js = {
    addRelatedTab: function(){
        var thisInstance = this;
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if(currentModuleView=='Detail' && currentModuleName=='Accounts'){
            /*var currentRecordId = app.getRecordId();
            var url = 'module=ANCustomers&relatedModule='+currentModuleName+'&view=CustomerProfile&record='+currentRecordId;
            var auth_net_btn =  '<span class="btn-group">';
            auth_net_btn +=         '<button class="btn btn-warning" id="Accounts_detailView_basicAction_AuthNet" onclick="Vtiger_VTEPayments_AuthorizeNet_Js.showANCustomerInfo();" >';
            auth_net_btn +=             '<strong>Auth.Net</strong>';
            auth_net_btn +=             '</button>';
            auth_net_btn +=     '</span>';
            $('.detailViewButtoncontainer .btn-toolbar').prepend(auth_net_btn);*/
            var params = {};
            params['module'] = 'ANCustomers';
            params['view'] = 'GetButton';
            app.request.post({'data':params}).then(
                function (err, data) {
                    if(err === null) {
                        if(data != ''){
                            $('.detailViewButtoncontainer .btn-group ul.dropdown-menu').append(data);
                        }
                    }
                }
            );
        }
    },

    showANCustomerInfo: function(url){
        var thisInstance = this;
        var currentRecordId = app.getRecordId();
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if(currentModuleView=='Detail' && currentModuleName=='Accounts'){
            app.helper.showProgress();
            var params = {};
            params['module'] = 'ANCustomers';
            params['relatedModule'] = currentModuleName;
            params['view'] = 'CustomerProfile';
            params['record'] = currentRecordId;

            app.request.post({'data':params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if(err === null) {
                        app.helper.hideProgress();
                        $('div.details').html(data);
                        thisInstance.registerShowEditCustomerProfileBoxEvent();
                        thisInstance.registerShowEditPaymentProfileBoxEvent();
                        thisInstance.registerDeleteCustomerProfileEvent();
                        thisInstance.registerDeletePaymentProfileEvent();
                    }
                    else {
                        app.helper.hideProgress();
                    }
                }
            );
        }
    },

    registerDeleteCustomerProfileEvent: function(){
        var thisInstance = this;
        $('#authorize-net-info .an-c-profile-deleteRecordButton').unbind('click').on('click', function (event) {
            var url = $(this).data('url');
            var message = app.vtranslate('Are you sure you want to delete?');
            app.helper.showConfirmationBox({'message' : message}).then(
                function(e) {
                    //Confirmed to delete
                    event.preventDefault();
                    app.helper.showProgress();
                    var requestParams = app.convertUrlToDataParams(url);
                    app.request.post({'data':requestParams}).then(
                        function (err, data) {
                            app.helper.hideProgress();
                            if(err === null) {
                                thisInstance.showANCustomerInfo();
                            }else{
                                app.helper.showAlertNotification({title: app.vtranslate('Error'), message: app.vtranslate('JS_NO_DELETE_PERMISSION')});
                            }
                        }
                    );
                },

                function(error, err){
                    event.preventDefault();
                    return false;
                }
            );
        });
    },

    registerDeletePaymentProfileEvent: function(){
        var thisInstance = this;
        $('#authorize-net-info .an-p-profile-deleteRecordButton').unbind('click').on('click', function (event) {
            var url = $(this).data('url');
            var message = app.vtranslate('Are you sure you want to delete?');
            app.helper.showConfirmationBox({'message' : message}).then(
                function(e) {
                    //Confirmed to delete
                    event.preventDefault();
                    app.helper.showProgress();
                    var requestParams = app.convertUrlToDataParams(url);
                    app.request.post({'data':requestParams}).then(
                        function (err, data) {
                            app.helper.hideProgress();
                            if(err === null) {
                                thisInstance.showANCustomerInfo();
                            }else{
                                app.helper.showAlertNotification({title: app.vtranslate('Error'), message: app.vtranslate('JS_NO_DELETE_PERMISSION')});
                            }
                        }
                    );
                },

                function(error, err){
                    event.preventDefault();
                    return false;
                }
            );
        });
    },

    registerShowTooltip: function(container){
        var options = {};
        options['animation'] = true;
        options['html'] = true;
        options['trigger'] = 'hover';
        var tooltipElement = container.find('.an-tooltip');
        tooltipElement.tooltip(options);
    },

    registerShowEditCustomerProfileBoxEvent: function(){
        var thisInstance = this;
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if($('#authorize-net-info').length && currentModuleView=='Detail' && currentModuleName=='Accounts'){
            $('#authorize-net-info .an-c-profile').unbind('click').on('click', function(event){
                event.preventDefault();
                app.helper.showProgress();
                var url = $(this).data('url');
                var requestParams = app.convertUrlToDataParams(url);
                app.request.get({'data':requestParams}).then(
                    function (err, data) {
                        app.helper.hideProgress();
                        if(err === null) {
                            app.helper.hideProgress();
                            app.helper.showModal(data,{"cb": function (container) {
                                var quickCreateForm = container.find('#customerProfileForm');
                                var params = {onsubmit : false};
                                quickCreateForm.vtValidate(params);
                                quickCreateForm.on('submit', function(e){
                                    e.preventDefault();
                                    var form = $(this);
                                    if(!form.valid()) {
                                        return false;
                                    }else {
                                        app.helper.showProgress();
                                        var saveBtn = form.find('[name=saveButton]');
                                        saveBtn.attr('disabled', 'disabled');
                                        if (thisInstance.validateCustomerProfileExtra(form)) {
                                            app.helper.showProgress();
                                            var actionParams = {
                                                "data": form.serializeFormData()
                                            };
                                            app.request.post(actionParams).then(
                                                function (err, data) {
                                                    if (err === null) {
                                                        app.helper.hideProgress();
                                                        app.hideModalWindow();
                                                        app.helper.showSuccessNotification({message: app.vtranslate('Record Saved!')});
                                                        //reload Customer Profile related list
                                                        thisInstance.showANCustomerInfo();
                                                    }
                                                    else {
                                                        app.helper.hideProgress();
                                                        saveBtn.removeAttr('disabled');
                                                        app.helper.showErrorNotification({message: app.vtranslate(err)});
                                                    }
                                                }
                                            );
                                        } else {
                                            //If validation fails, form should submit again
                                            saveBtn.removeAttr('disabled');
                                            app.helper.hideProgress();
                                            return false;
                                        }
                                    }
                                });

                                var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName('ANCustomers');
                                editViewInstance.registerBasicEvents(quickCreateForm);
                                vtUtils.applyFieldElementsView(quickCreateForm);
                                var vtigerInstance = Vtiger_Index_Js.getInstance();
                                vtigerInstance.registerAutoCompleteFields(quickCreateForm);
                                vtigerInstance.referenceModulePopupRegisterEvent(quickCreateForm);
                                vtigerInstance.registerClearReferenceSelectionEvent(quickCreateForm);
                                thisInstance.registerShowTooltip(container);

                                //limit description maximum 255 characters
                                $('[name=description]', quickCreateForm).on('keyup', function(event){
                                    var description = $(this).val();
                                    if(description.length>255){
                                        description = description.substring(0, 255);
                                        $(this).val(description);
                                        app.helper.showAlertNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Description is limited 255 characters.')});
                                    }
                                });

                            }});
                        }
                        else {
                            app.helper.hideProgress();
                        }
                    }
                );
            });
        }
    },

    validateCustomerProfileExtra: function(form){
        var description = form.find('[name=description]').val();
        if(description.length>255){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Description is limited 255 characters.')});
            return false;
        }
        var email = form.find('input[name=email]').val();
        if(email.length>255){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Email is limited 255 characters.')});
            return false;
        }
        var firstName = form.find('input[name=firstname]').val();
        if(firstName.length>50){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('First Name is limited 50 characters.')});
            return false;
        }
        var lastName = form.find('input[name=lastname]').val();
        if(lastName.length>50){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Last Name is limited 50 characters.')});
            return false;
        }
        var company = form.find('input[name=name]').val();
        if(company.length>50){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Customer Name is limited 50 characters.')});
            return false;
        }
        var address = form.find('input[name=address]').val();
        if(address.length>60){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Address is limited 60 characters.')});
            return false;
        }
        var city = form.find('input[name=city]').val();
        if(city.length>40){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('City is limited 40 characters.')});
            return false;
        }
        var state = form.find('input[name=state]').val();
        if(state.length>40){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('State is limited 40 characters.')});
            return false;
        }
        var zip = form.find('input[name=zip]').val();
        if(zip.length>20){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Zip is limited 20 characters.')});
            return false;
        }
        var country = form.find('input[name=country]').val();
        if(country.length>60){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Zip is limited 60 characters.')});
            return false;
        }
        var phoneNumber = form.find('input[name=phonenumber]').val();
        if(phoneNumber.length>25){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Phone Number is limited 25 digits.')});
            return false;
        }
        var faxNumber = form.find('input[name=faxnumber]').val();
        if(faxNumber.length>25){
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Fax Number is limited 25 digits.')});
            return false;
        }

        return true;
    },

    registerShowEditPaymentProfileBoxEvent: function(){
        var thisInstance = this;
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if($('#authorize-net-info').length && currentModuleView=='Detail' && currentModuleName=='Accounts'){
            if($('#authorize-net-info a.add-payment-profile').length){
                $('#authorize-net-info a.add-payment-profile').unbind('click').on('click', function(event){
                    event.preventDefault();
                    app.helper.showProgress();
                    var url = $(this).data('url');
                    var requestParams = app.convertUrlToDataParams(url);
                    app.request.get({'data':requestParams}).then(
                        function (err, data) {
                            app.helper.hideProgress();
                            if(err === null) {
                                app.helper.hideProgress();
                                app.helper.showModal(data,{"cb": function (container) {
                                    var quickCreateForm = container.find('#paymentProfileForm');
                                    var params = {onsubmit : false};
                                    quickCreateForm.vtValidate(params);
                                    quickCreateForm.on('submit', function(e){
                                        e.preventDefault();
                                        var form = $(this);
                                        if(!form.valid()) {
                                            return false;
                                        }else {
                                            app.helper.showProgress();
                                            var saveBtn = form.find('[name=saveButton]');
                                            saveBtn.attr('disabled', 'disabled');
                                            if (thisInstance.validatePaymentProfileExtra(form)) {
                                                app.helper.showProgress();
                                                var actionParams = {
                                                    "data": form.serializeFormData()
                                                };
                                                app.request.post(actionParams).then(
                                                    function (err, data) {
                                                        if (err === null) {
                                                            if(data.status == 'ERROR'){
                                                                saveBtn.removeAttr('disabled');
                                                                app.helper.hideProgress();
                                                                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: data.message});
                                                                return false;
                                                            }else {
                                                                app.helper.hideProgress();
                                                                app.hideModalWindow();
                                                                app.helper.showSuccessNotification({message: app.vtranslate('Record Saved!')});
                                                                //reload Customer Profile related list
                                                                thisInstance.showANCustomerInfo();
                                                            }
                                                        }
                                                        else {
                                                            saveBtn.removeAttr('disabled');
                                                            app.helper.hideProgress();
                                                            app.helper.showErrorNotification({message: app.vtranslate(err)});
                                                            return false;
                                                        }
                                                    }
                                                );
                                            } else {
                                                //If validation fails, form should submit again
                                                saveBtn.removeAttr('disabled');
                                                app.helper.hideProgress();
                                                return false;
                                            }
                                        }
                                    });
                                    //disable all eCheck type when initiate account_type e_check_type
                                    $('[name=e_check_type] option', quickCreateForm).each(function(){
                                        if($(this).val()!==''){
                                            $(this).attr('disabled', true);
                                        }
                                    });

                                    //show echeck type depend on bank account type
                                    $('[name=account_type]', quickCreateForm).on('change', function (event) {
                                        event.preventDefault();
                                        var element = $(this);
                                        var bank_account_type = element.val();
                                        if(bank_account_type==''){
                                            $('[name=e_check_type] option', quickCreateForm).each(function(){
                                                if($(this).val()!==''){
                                                    $(this).attr('disabled', true);
                                                }
                                            });
                                            $('[name=e_check_type]', quickCreateForm).select2('val', '');
                                        }else if(bank_account_type=='businessChecking'){
                                            $('[name=e_check_type] option', quickCreateForm).each(function(){
                                                if($(this).val()!=='CCD'){
                                                    $(this).attr('disabled', true);
                                                }else{
                                                    $(this).removeAttr('disabled');
                                                }
                                            });
                                            $('[name=e_check_type]', quickCreateForm).select2('val', '');
                                        }else{
                                            $('[name=e_check_type] option', quickCreateForm).each(function(){
                                                if($(this).val()=='CCD'){
                                                    $(this).attr('disabled', true);
                                                }else{
                                                    $(this).removeAttr('disabled');
                                                }
                                            });
                                            $('[name=e_check_type]', quickCreateForm).select2('val', '');
                                        }
                                    });
                                    $('[name=account_type]', quickCreateForm).trigger('change');

                                    var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName('ANPaymentProfile');
                                    editViewInstance.registerBasicEvents(quickCreateForm);
                                    vtUtils.applyFieldElementsView(quickCreateForm);
                                    var vtigerInstance = Vtiger_Index_Js.getInstance();
                                    vtigerInstance.registerAutoCompleteFields(quickCreateForm);
                                    vtigerInstance.referenceModulePopupRegisterEvent(quickCreateForm);
                                    vtigerInstance.registerClearReferenceSelectionEvent(quickCreateForm);
                                    thisInstance.registerShowTooltip(container);

                                    //register display fields of payment method event
                                    thisInstance.showPaymentMethodDetail();
                                    $('[name=payment_method]', quickCreateForm).on('change', function(event){
                                        event.preventDefault();
                                        var element = $(this);
                                        var payment_method = element.val();
                                        thisInstance.showPaymentMethodDetail(payment_method);
                                    });
                                    //register expiration date value
                                    $('#an-expiration-month-alias, #an-expiration-year-alias', quickCreateForm).on('change', function(event){
                                        event.preventDefault();
                                        var expiration_value = '';
                                        var month = $('#an-expiration-month-alias', quickCreateForm).val();
                                        var year = $('#an-expiration-year-alias', quickCreateForm).val();
                                        var current_month = $('#an-current-month', quickCreateForm).val();
                                        var current_year = $('#an-current-year', quickCreateForm).val();
                                        if(month != '' && year != '' && ((current_year==year && parseInt(month)>=parseInt(current_month)) || year>current_year)){
                                            expiration_value = year + '-' + month + '-' + '15';
                                        }
                                        if(expiration_value=='' && month != '' && year != ''){
                                            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Expired date must greater than current time')});
                                        }
                                        $('[name=expiration_date]', quickCreateForm).val(expiration_value);
                                    });

                                    //disable bank/card information block
                                    var paymentprofileid = $('input[name=record]', quickCreateForm).val();
                                    if(paymentprofileid){
                                        //disable editable
                                        var cardBlockContainer = $('input[name=card_number]', quickCreateForm).closest('div.fieldBlockContainer');
                                        var card_block = $('input[name=card_number]', quickCreateForm).closest('table');
                                        card_block.attr('data-editable', false);
                                        card_block.css({'background-color': '#EFEFEF'});
                                        card_block.find('input').attr('disabled', true);
                                        card_block.find('select').attr('disabled', true);

                                        var bankBlockContainer = $('input[name=account_number]', quickCreateForm).closest('div.fieldBlockContainer');
                                        var bank_block = $('input[name=account_number]', quickCreateForm).closest('table');
                                        bank_block.attr('data-editable', false);
                                        bank_block.css({'background-color': '#EFEFEF'});
                                        bank_block.find('input').attr('disabled', true);
                                        bank_block.find('select').attr('disabled', true);

                                        vtUtils.applyFieldElementsView(card_block);
                                        vtUtils.applyFieldElementsView(bank_block);
                                    }
                                }});
                            }
                            else {
                                app.helper.hideProgress();
                            }
                        }
                    );
                });
            }
        }
    },

    setBankCardEditable: function (el) {
        var blockContainer = $(el).closest('div.fieldBlockContainer');
        blockContainer.find('input').removeAttr('disabled');
        blockContainer.find('select').removeAttr('disabled');
        blockContainer.find('table').css({'background-color': 'transparent'});
        blockContainer.find('table').data('editable', true);
        vtUtils.applyFieldElementsView(blockContainer);
        $(el).parent().hide();
    },

    setCardEditable: function (el) {
        var paymentProfileForm = $(el).closest('form');
        var cardBlockContainer = $('input[name=card_number]', paymentProfileForm).closest('div.fieldBlockContainer');
        cardBlockContainer.find('input').removeAttr('disabled');
        cardBlockContainer.find('select').removeAttr('disabled');
        cardBlockContainer.find('table').css({'background-color': 'transparent'});
        cardBlockContainer.find('table').data('editable', true);
        vtUtils.applyFieldElementsView(cardBlockContainer);
        $(el).parent().hide();
    },

    setBankEditable: function (el) {
        var paymentProfileForm = $(el).closest('form');
        var bankBlockContainer = $('input[name=account_number]', paymentProfileForm).closest('div.fieldBlockContainer');
        bankBlockContainer.find('input').removeAttr('disabled');
        bankBlockContainer.find('select').removeAttr('disabled');
        bankBlockContainer.find('table').css({'background-color': 'transparent'});
        bankBlockContainer.find('table').data('editable', true);
        vtUtils.applyFieldElementsView(bankBlockContainer);
        $(el).parent().hide();
    },

    validatePaymentProfileExtra: function (form) {
        //validate credit card and bank account info
        var payment_method = form.find('[name=payment_method]').val();
        var address = form.find('[name=address]').val();
        var city = form.find('[name=city]').val();
        var state = form.find('[name=state]').val();
        var zip = form.find('[name=zip]').val();
        var country = form.find('[name=country]').val();
        var record = form.find('[name=record]').val();
        if(payment_method=='CreditCardSimpleType'){
            //valid credit card
            var card_number = form.find('[name=card_number]').val();
            var card_code = form.find('[name=card_code]').val();
            var expiration_date = form.find('[name=expiration_date]').val();
            //valid card info
            if(!record || record==''){
                if($.trim(card_number)==''){
                    // to avoid hiding of error message under the fixed nav bar
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Card Number is required')});
                    return false;
                }else if($.trim(expiration_date)==''){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Expiration Date is required')});
                    return false;
                }else if($.trim(card_number).length < 13 || $.trim(card_number).length > 16){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Card Number must has 13 to 16 digits.')});
                    return false;
                }
            }else{
                var cardEditable = form.find('[name=card_number]').closest('table').data('editable');
                if(cardEditable){
                    if($.trim(card_number)==''){
                        // to avoid hiding of error message under the fixed nav bar
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Card Number is required')});
                        return false;
                    }else if($.trim(expiration_date)==''){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Expiration Date is required')});
                        return false;
                    }else if($.trim(card_number).length < 13 || $.trim(card_number).length > 16){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Card Number must has 13 to 16 digits.')});
                        return false;
                    }
                }
            }
            //valid bill address
            if($.trim(address) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Address is required.')});
                return false;
            }else if($.trim(city) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('City is required.')});
                return false;
            }else if($.trim(state) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('State is required.')});
                return false;
            }else if($.trim(zip) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Postal Code is required.')});
                return false;
            }else if($.trim(country) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Country is required.')});
                return false;
            }
        }else if(payment_method=='BankAccountType'){
            var account_type = form.find('[name=account_type]').val();
            var routing_number = form.find('[name=routing_number]').val();
            var account_number = form.find('[name=account_number]').val();
            var name_on_account = form.find('[name=name_on_account]').val();
            var e_check_type = form.find('[name=e_check_type]').val();
            var bank_name = form.find('[name=bank_name]').val();
            //valid bank info
            if(!record || record==''){
                if($.trim(account_type)==''){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Account Type is required')});
                    return false;
                }else if($.trim(routing_number)==''){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Routing Number is required')});
                    return false;
                }else if($.trim(account_number)==''){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Account Number is required')});
                    return false;
                }else if($.trim(name_on_account)==''){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Name On Account is required')});
                    return false;
                }else if($.trim(e_check_type)==''){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Electronic Check Type is required')});
                    return false;
                }else if($.trim(routing_number).length != 9){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Routing Number must has 9 digits')});
                    return false;
                }else if($.trim(account_number).length < 5 || $.trim(account_number).length > 17){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Account Number must has 5 to 17 digits')});
                    return false;
                }else if($.trim(name_on_account).length > 22){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('The Customer\'s full name is limited 22 characters.')});
                    return false;
                }else if($.trim(bank_name).length > 50){
                    //Validation fails, form should submit again
                    app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('The Name of the Bank is limited 50 characters')});
                    return false;
                }
            }else{
                var bankEditable = form.find('[name=account_number]').closest('table').data('editable');
                if(bankEditable){
                    if($.trim(account_type)==''){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Account Type is required')});
                        return false;
                    }else if($.trim(routing_number)==''){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Routing Number is required')});
                        return false;
                    }else if($.trim(account_number)==''){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Account Number is required')});
                        return false;
                    }else if($.trim(name_on_account)==''){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Name On Account is required')});
                        return false;
                    }else if($.trim(e_check_type)==''){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Electronic Check Type is required')});
                        return false;
                    }else if($.trim(routing_number).length != 9){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Routing Number must has 9 digits')});
                        return false;
                    }else if($.trim(account_number).length < 5 || $.trim(account_number).length > 17){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Account Number must has 5 to 17 digits')});
                        return false;
                    }else if($.trim(name_on_account).length > 22){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('The Customer\'s full name is limited 22 characters.')});
                        return false;
                    }else if($.trim(bank_name).length > 50){
                        //Validation fails, form should submit again
                        app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('The Name of the Bank is limited 50 characters')});
                        return false;
                    }
                }
            }
            //valid billing address
            if($.trim(address) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Address is required.')});
                return false;
            }else if($.trim(city) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('City is required.')});
                return false;
            }else if($.trim(state) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('State is required.')});
                return false;
            }else if($.trim(zip) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Postal Code is required.')});
                return false;
            }else if($.trim(country) == ''){
                //Validation fails, form should submit again
                app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Country is required.')});
                return false;
            }
        }else{
            // to avoid hiding of error message under the fixed nav bar
            //app.formAlignmentAfterValidation(form);
            app.helper.showErrorNotification({title: app.vtranslate('Invalid'), message: app.vtranslate('Payment method is required.')});
            return false;
        }

        return true;
    },

    showPaymentMethodDetail: function(payment_method){
        if($('#paymentProfileForm').length==0){
            return;
        }
        if(typeof payment_method == 'undefined'){
            var payment_method = $('#paymentProfileForm [name=payment_method]').val();
        }
        var container = $('#paymentProfileForm');
        if(payment_method=='CreditCardSimpleType'){
            //show credit card block
            container.find('[name=card_number]').closest('div.fieldBlockContainer').show();
            //hide bank account block
            container.find('[name=bank_name]').closest('div.fieldBlockContainer').hide();
        }else if(payment_method=='BankAccountType'){
            //hide credit card block
            container.find('[name=card_number]').closest('div.fieldBlockContainer').hide();
            //show bank account block
            container.find('[name=bank_name]').closest('div.fieldBlockContainer').show();
        }else{
            //hide credit card block
            container.find('[name=card_number]').closest('div.fieldBlockContainer').hide();
            //hide bank account block
            container.find('[name=bank_name]').closest('div.fieldBlockContainer').hide();
        }
    },

    /**
     * Function returns the record id
     */
    getRecordId : function(){
        var view = app.getViewName();
        var recordId;
        if(view == "Edit"){
            recordId = jQuery('[name="record"]').val();
        }else if(view == "Detail"){
            recordId = jQuery('#recordId').val();
        }
        return recordId;
    },

    registerEvent: function(){
        this.addRelatedTab();
    }

}

jQuery(document).ready(function () {
    Vtiger_VTEPayments_AuthorizeNet_Js.registerEvent();
});