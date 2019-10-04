/* ********************************************************************************
 * The content of this file is subject to the VTEPayments("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */
var Vtiger_VTEPayments_AuthorizeNet_Js = {
    addRelatedTab: function () {
        var thisInstance = this;
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if (currentModuleView == 'Detail' && currentModuleName == 'Accounts') {
            /*var currentRecordId = thisInstance.getRecordId();
            var url = 'module=ANCustomers&relatedModule=' + currentModuleName + '&view=CustomerProfile&record=' + currentRecordId;
            var auth_net_btn = '<span class="btn-group">';
            auth_net_btn += '<button class="btn btn-warning" id="Accounts_detailView_basicAction_AuthNet" onclick="Vtiger_VTEPayments_AuthorizeNet_Js.showANCustomerInfo();" >';
            auth_net_btn += '<strong>Auth.Net</strong>';
            auth_net_btn += '</button>';
            auth_net_btn += '</span>';
            $('.detailViewButtoncontainer .btn-toolbar').prepend(auth_net_btn);*/
            var aDeferred = jQuery.Deferred();
            var params = {};
            params['module'] = 'ANCustomers';
            params['view'] = 'GetButton';
            AppConnector.request(params).then(
                function (data) {
                    if(data != ''){
                        $('.detailViewButtoncontainer .btn-group ul.dropdown-menu.pull-right').append(data);
                    }
                },
                function (error, err) {
                }
            );
            return aDeferred.promise();
        }
    },

    showANCustomerInfo: function (url) {
        var thisInstance = this;
        var currentRecordId = this.getRecordId();
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if (currentModuleView == 'Detail' && currentModuleName == 'Accounts') {
            var progressInstance = jQuery.progressIndicator({
                'position': 'html',
                'blockInfo': {
                    'enabled': true
                }
            });
            var aDeferred = jQuery.Deferred();
            var params = {};
            params['module'] = 'ANCustomers';
            params['relatedModule'] = currentModuleName;
            params['view'] = 'CustomerProfile';
            params['record'] = currentRecordId;
            AppConnector.request(params).then(
                function (data) {
                    progressInstance.progressIndicator({'mode': 'hide'});
                    $('#detailView .contents').html(data);
                    thisInstance.registerShowEditCustomerProfileBoxEvent();
                    thisInstance.registerDeleteCustomerProfileEvent();
                    thisInstance.registerShowEditPaymentProfileBoxEvent();
                    thisInstance.registerDeletePaymentProfileEvent();
                },
                function (error, err) {
                    progressInstance.progressIndicator({'mode': 'hide'});
                }
            );
            return aDeferred.promise();
        }
    },

    registerDeleteCustomerProfileEvent: function(){
        var thisInstance = this;
        $('#authorize-net-info .an-c-profile-deleteRecordButton').unbind('click').on('click', function (event) {
            var url = $(this).data('url');
            var message = app.vtranslate('Are you sure you want to delete?');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    //Confirmed to delete
                    AppConnector.request(url).then(function(data){
                        if(data.success) {
                            thisInstance.showANCustomerInfo();
                        } else {
                            var  params = {
                                text : app.vtranslate('JS_NO_DELETE_PERMISSION')
                            }
                            Vtiger_Helper_Js.showPnotify(params);
                        }

                    });
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
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    //Confirmed to delete
                    AppConnector.request(url).then(function(data){
                        if(data.success) {
                            thisInstance.showANCustomerInfo();
                        } else {
                            var  params = {
                                text : app.vtranslate('JS_NO_DELETE_PERMISSION')
                            }
                            Vtiger_Helper_Js.showPnotify(params);
                        }

                    });
                },
                function(error, err){
                    event.preventDefault();
                    return false;
                }
            );
        });
    },


    registerShowTooltip: function (container) {
        var options = {};
        options['animation'] = true;
        options['html'] = true;
        options['trigger'] = 'hover';
        var tooltipElement = container.find('.an-tooltip');
        tooltipElement.tooltip(options);
    },

    registerShowEditCustomerProfileBoxEvent: function () {
        var thisInstance = this;
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if ($('#authorize-net-info').length && currentModuleView == 'Detail' && currentModuleName == 'Accounts') {
            $('#authorize-net-info .an-c-profile').unbind('click').on('click', function (event) {
                event.preventDefault();
                var progressInstance = jQuery.progressIndicator({
                    'position': 'html',
                    'blockInfo': {
                        'enabled': true
                    }
                });
                var url = $(this).data('url');
                app.showModalWindow(null, url, function (container) {
                    var quickCreateForm = container.find('#customerProfileForm');
                    var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName('ANCustomers');
                    editViewInstance.registerBasicEvents(quickCreateForm);
                    quickCreateForm.validationEngine(app.validationEngineOptions);
                    app.registerEventForDatePickerFields(quickCreateForm);
                    thisInstance.registerShowTooltip(container);
                    //register cancel button
                    $('.cancelLink', quickCreateForm).on('click', function () {
                        app.hideModalWindow();
                    });
                    //limit description maximum 255 characters
                    $('[name=description]', quickCreateForm).on('keyup', function (event) {
                        var description = $(this).val();
                        if (description.length > 255) {
                            description = description.substring(0, 255);
                            $(this).val(description);
                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Description is limited 255 characters.'));
                        }
                    });
                    //register event save item
                    $('#save-btn', quickCreateForm).on('click', function (event) {
                        event.preventDefault();
                        var element = $(this);
                        element.attr('disabled', true);
                        var form = element.closest('form');
                        var parentElement = element.closest('div');

                        if (form.validationEngine('validate')) {
                            if (thisInstance.validateCustomerProfileExtra(form)) {
                                parentElement.progressIndicator();
                                var params = form.serialize();
                                var aDeferred = jQuery.Deferred();
                                AppConnector.request(params).then(
                                    function (data) {
                                        if(data.success === false){
                                            parentElement.progressIndicator({'mode': 'hide'});
                                            var msg_params = {
                                                title : app.vtranslate('JS_MESSAGE'),
                                                text: app.vtranslate(data.error.message),
                                                animation: 'show',
                                                type: 'error'
                                            };
                                            Vtiger_Helper_Js.showPnotify(msg_params);
                                            element.removeAttr('disabled');
                                            aDeferred.resolve(data);
                                        }else{
                                            parentElement.progressIndicator({'mode': 'hide'});
                                            app.hideModalWindow();
                                            var msg_params = {
                                                title : app.vtranslate('JS_MESSAGE'),
                                                text: app.vtranslate('Record Saved!'),
                                                animation: 'show',
                                                type: 'success'
                                            };
                                            Vtiger_Helper_Js.showPnotify(msg_params);
                                            aDeferred.resolve(data);
                                            //reload Customer Profile related list
                                            thisInstance.showANCustomerInfo();
                                        }
                                    },
                                    function (error, err) {
                                        parentElement.progressIndicator({'mode': 'hide'});
                                        element.removeAttr('disabled');
                                    }
                                );
                                return aDeferred.promise();
                            } else {
                                //If validation fails, form should submit again
                                element.removeAttr('disabled');
                            }
                        } else {
                            //If validation fails, form should submit again
                            element.removeAttr('disabled');
                            // to avoid hiding of error message under the fixed nav bar
                            //app.formAlignmentAfterValidation(form);
                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Please fill to all required fields.'));
                        }
                    });
                });
            });
        }
    },

    validateCustomerProfileExtra: function (form) {
        var description = form.find('[name=description]').val();
        if (description.length > 255) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Description is limited 255 characters.'));
            return false;
        }
        var email = form.find('input[name=email]').val();
        if (email.length > 255) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Email is limited 255 characters.'));
            return false;
        }
        var firstName = form.find('input[name=firstname]').val();
        if (firstName.length > 50) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('First Name is limited 50 characters.'));
            return false;
        }
        var lastName = form.find('input[name=lastname]').val();
        if (lastName.length > 50) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Last Name is limited 50 characters.'));
            return false;
        }
        var company = form.find('input[name=name]').val();
        if (company.length > 50) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Customer Name is limited 50 characters.'));
            return false;
        }
        var address = form.find('input[name=address]').val();
        if (address.length > 60) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Address is limited 60 characters.'));
            return false;
        }
        var city = form.find('input[name=city]').val();
        if (city.length > 40) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('City is limited 40 characters.'));
            return false;
        }
        var state = form.find('input[name=state]').val();
        if (state.length > 40) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('State is limited 40 characters.'));
            return false;
        }
        var zip = form.find('input[name=zip]').val();
        if (zip.length > 20) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Zip is limited 20 characters.'));
            return false;
        }
        var country = form.find('input[name=country]').val();
        if (country.length > 60) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Zip is limited 60 characters.'));
            return false;
        }
        var phoneNumber = form.find('input[name=phonenumber]').val();
        if (phoneNumber.length > 25) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Phone Number is limited 25 digits.'));
            return false;
        }
        var faxNumber = form.find('input[name=faxnumber]').val();
        if (faxNumber.length > 25) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('Fax Number is limited 25 digits.'));
            return false;
        }

        return true;
    },

    registerShowEditPaymentProfileBoxEvent: function () {
        var thisInstance = this;
        var currentModuleName = app.getModuleName();
        var currentModuleView = app.getViewName();
        if ($('#authorize-net-info').length && currentModuleView == 'Detail' && currentModuleName == 'Accounts') {
            if ($('#authorize-net-info a.add-payment-profile').length) {
                $('#authorize-net-info a.add-payment-profile').unbind('click').on('click', function (event) {
                    event.preventDefault();
                    var progressInstance = jQuery.progressIndicator({
                        'position': 'html',
                        'blockInfo': {
                            'enabled': true
                        }
                    });
                    var url = $(this).data('url');
                    app.showModalWindow(null, url, function (container) {
                        var quickCreateForm = container.find('#paymentProfileForm');
                        var editViewInstance = Vtiger_Edit_Js.getInstanceByModuleName('Vtiger');
                        editViewInstance.registerBasicEvents(quickCreateForm);
                        quickCreateForm.validationEngine(app.validationEngineOptions);
                        app.registerEventForDatePickerFields(quickCreateForm);
                        thisInstance.registerShowTooltip(container);
                        //register cancel button
                        $('.cancelLink', quickCreateForm).on('click', function () {
                            app.hideModalWindow();
                        });
                        //register display fields of payment method event
                        thisInstance.showPaymentMethodDetail();
                        $('[name=payment_method]', quickCreateForm).on('change', function (event) {
                            event.preventDefault();
                            var element = $(this);
                            var payment_method = element.val();
                            thisInstance.showPaymentMethodDetail(payment_method);
                        });
                        //register expiration date value
                        $('#an-expiration-month-alias, #an-expiration-year-alias', quickCreateForm).on('change', function (event) {
                            event.preventDefault();
                            var expiration_value = '';
                            var month = $('#an-expiration-month-alias', quickCreateForm).val();
                            var year = $('#an-expiration-year-alias', quickCreateForm).val();
                            var current_month = $('#an-current-month', quickCreateForm).val();
                            var current_year = $('#an-current-year', quickCreateForm).val();
                            if (month != '' && year != '') {
                                if ((current_year == year && parseInt(month) >= parseInt(current_month)) || year > current_year) {
                                    expiration_value = year + '-' + month + '-' + '15';
                                }else{
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate('Expired date must greater than current time'));
                                }
                            }
                            $('[name=expiration_date]', quickCreateForm).val(expiration_value);
                        });

                        //disable all eCheck type when initiate account_type e_check_type
                        $('[name=e_check_type] option', quickCreateForm).each(function(){
                            if($(this).val()!==''){
                                $(this).attr('disabled', true);
                            }
                        });
                        $('[name=e_check_type]', quickCreateForm).val('').trigger('liszt:updated');

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
                                $('[name=e_check_type]', quickCreateForm).val('').trigger('liszt:updated');
                            }else if(bank_account_type=='businessChecking'){
                                $('[name=e_check_type] option', quickCreateForm).each(function(){
                                    if($(this).val()!=='CCD'){
                                        $(this).attr('disabled', true);
                                    }else{
                                        $(this).removeAttr('disabled');
                                    }
                                });
                                $('[name=e_check_type]', quickCreateForm).val('').trigger('liszt:updated');
                            }else{
                                $('[name=e_check_type] option', quickCreateForm).each(function(){
                                    if($(this).val()=='CCD'){
                                        $(this).attr('disabled', true);
                                    }else{
                                        $(this).removeAttr('disabled');
                                    }
                                });
                                $('[name=e_check_type]', quickCreateForm).val('').trigger('liszt:updated');
                            }
                        });

                        //disable bank/card information block
                        var paymentprofileid = $('input[name=record]', quickCreateForm).val();
                        if(paymentprofileid){
                            //disable editable
                            var card_block = $('input[name=card_number]', quickCreateForm).closest('table');
                            var cardBlockContainer = card_block.find('th.blockHeader');
                            card_block.attr('data-editable', false);
                            card_block.css({'background-color': '#EFEFEF'});
                            card_block.find('input').attr('disabled', true);
                            card_block.find('select').attr('disabled', true);

                            var bank_block = $('input[name=account_number]', quickCreateForm).closest('table');
                            var bankBlockContainer = bank_block.find('th.blockHeader');
                            bank_block.attr('data-editable', false);
                            bank_block.css({'background-color': '#EFEFEF'});
                            bank_block.find('input').attr('disabled', true);
                            bank_block.find('select').attr('disabled', true);

                            $('select', card_block).trigger('liszt:updated');
                            $('select', bank_block).trigger('liszt:updated');
                        }

                        //register event save item
                        $('#save-btn', quickCreateForm).on('click', function (event) {
                            event.preventDefault();
                            var element = $(this);
                            var parentElement = element.parent();
                            element.attr('disabled', true);
                            var form = element.closest('form');

                            if (form.validationEngine('validate')) {
                                //valide credit card and bank account info
                                var payment_method = $('#paymentProfileForm [name=payment_method]').val();
                                var address = form.find('[name=address]').val();
                                var city = form.find('[name=city]').val();
                                var state = form.find('[name=state]').val();
                                var zip = form.find('[name=zip]').val();
                                var country = form.find('[name=country]').val();
                                var record = form.find('[name=record]').val();
                                if (payment_method == 'CreditCardSimpleType') {
                                    //valid credit card
                                    var card_number = $('#paymentProfileForm [name=card_number]').val();
                                    var card_code = $('#paymentProfileForm [name=card_code]').val();
                                    var expiration_date = $('#paymentProfileForm [name=expiration_date]').val();
                                    if(!record || record==''){
                                        if ($.trim(card_number) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            // to avoid hiding of error message under the fixed nav bar
                                            //app.formAlignmentAfterValidation(form);
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Card Number is required'));
                                            return false;
                                        } else if ($.trim(expiration_date) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Expiration Date is required'));
                                            return false;
                                        } else if ($.trim(card_number).length < 13 || $.trim(card_number).length > 16) {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Card Number must has 13 to 16 digits.'));
                                            return false;
                                        }
                                    }else{
                                        var cardEditable = form.find('[name=card_number]').closest('table').data('editable');
                                        if(cardEditable){
                                            if ($.trim(card_number) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                // to avoid hiding of error message under the fixed nav bar
                                                //app.formAlignmentAfterValidation(form);
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Card Number is required'));
                                                return false;
                                            } else if ($.trim(expiration_date) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Expiration Date is required'));
                                                return false;
                                            } else if ($.trim(card_number).length < 13 || $.trim(card_number).length > 16) {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Card Number must has 13 to 16 digits.'));
                                                return false;
                                            }
                                        }
                                    }

                                    //valid bill address
                                    if ($.trim(address) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('Address is required.'));
                                        return false;
                                    } else if ($.trim(city) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('City is required.'));
                                        return false;
                                    } else if ($.trim(state) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('State is required.'));
                                        return false;
                                    } else if ($.trim(zip) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('Postal Code is required.'));
                                        return false;
                                    } else if ($.trim(country) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('Country is required.'));
                                        return false;
                                    }
                                } else if (payment_method == 'BankAccountType') {
                                    var account_type = $('#paymentProfileForm [name=account_type]').val();
                                    var routing_number = $('#paymentProfileForm [name=routing_number]').val();
                                    var account_number = $('#paymentProfileForm [name=account_number]').val();
                                    var name_on_account = $('#paymentProfileForm [name=name_on_account]').val();
                                    var e_check_type = $('#paymentProfileForm [name=e_check_type]').val();
                                    var bank_name = $('#paymentProfileForm [name=bank_name]').val();
                                    //valid bank info
                                    if(!record || record==''){
                                        if ($.trim(account_type) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Account Type is required'));
                                            return false;
                                        } else if ($.trim(routing_number) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Routing Number is required'));
                                            return false;
                                        } else if ($.trim(account_number) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Account Number is required'));
                                            return false;
                                        } else if ($.trim(name_on_account) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Name On Account is required'));
                                            return false;
                                        } else if ($.trim(e_check_type) == '') {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Electronic Check Type is required'));
                                            return false;
                                        } else if ($.trim(routing_number).length != 9) {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Routing Number must has 9 digits.'));
                                            return false;
                                        } else if ($.trim(account_number).length < 5 || $.trim(account_number).length > 17) {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('Account Number must has 5 to 17 digits.'));
                                            return false;
                                        } else if ($.trim(name_on_account).length > 22) {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('The Customer\'s full name is limited 22 characters.'));
                                            return false;
                                        } else if ($.trim(bank_name).length > 50) {
                                            //Validation fails, form should submit again
                                            element.removeAttr('disabled');
                                            Vtiger_Helper_Js.showPnotify(app.vtranslate('The name of the bank is limited 50 characters.'));
                                            return false;
                                        }
                                    }else{
                                        var bankEditable = form.find('[name=account_number]').closest('table').data('editable');
                                        if(bankEditable){
                                            if ($.trim(account_type) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Account Type is required'));
                                                return false;
                                            } else if ($.trim(routing_number) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Routing Number is required'));
                                                return false;
                                            } else if ($.trim(account_number) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Account Number is required'));
                                                return false;
                                            } else if ($.trim(name_on_account) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Name On Account is required'));
                                                return false;
                                            } else if ($.trim(e_check_type) == '') {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Electronic Check Type is required'));
                                                return false;
                                            } else if ($.trim(routing_number).length != 9) {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Routing Number must has 9 digits.'));
                                                return false;
                                            } else if ($.trim(account_number).length < 5 || $.trim(account_number).length > 17) {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Account Number must has 5 to 17 digits.'));
                                                return false;
                                            } else if ($.trim(name_on_account).length > 22) {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('The Customer\'s full name is limited 22 characters.'));
                                                return false;
                                            } else if ($.trim(bank_name).length > 50) {
                                                //Validation fails, form should submit again
                                                element.removeAttr('disabled');
                                                Vtiger_Helper_Js.showPnotify(app.vtranslate('The name of the bank is limited 50 characters.'));
                                                return false;
                                            }
                                        }
                                    }

                                    //valid billing address
                                    if ($.trim(address) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('Address is required.'));
                                        return false;
                                    } else if ($.trim(city) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('City is required.'));
                                        return false;
                                    } else if ($.trim(state) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('State is required.'));
                                        return false;
                                    } else if ($.trim(zip) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('Postal Code is required.'));
                                        return false;
                                    } else if ($.trim(country) == '') {
                                        //Validation fails, form should submit again
                                        element.removeAttr('disabled');
                                        Vtiger_Helper_Js.showPnotify(app.vtranslate('Country is required.'));
                                        return false;
                                    }
                                } else {
                                    //Validation fails, form should submit again
                                    element.removeAttr('disabled');
                                    // to avoid hiding of error message under the fixed nav bar
                                    //app.formAlignmentAfterValidation(form);
                                    Vtiger_Helper_Js.showPnotify(app.vtranslate('Payment method is required.'));
                                    return false;
                                }

                                parentElement.progressIndicator();
                                var params = form.serialize();
                                var aDeferred = jQuery.Deferred();
                                AppConnector.request(params).then(
                                    function (data) {
                                        parentElement.progressIndicator({'mode': 'hide'});
                                        if(data.success === false){
                                            aDeferred.resolve(data);
                                            var msg_params = {
                                                title : app.vtranslate('JS_MESSAGE'),
                                                text: app.vtranslate(data.error.message),
                                                animation: 'show',
                                                type: 'error'
                                            };
                                            Vtiger_Helper_Js.showPnotify(msg_params);
                                            element.removeAttr('disabled');
                                        }else{
                                            if (data.result.status == 'SUCCESS') {
                                                app.hideModalWindow();
                                                aDeferred.resolve(data);
                                                var msg_params = {
                                                    title : app.vtranslate('JS_MESSAGE'),
                                                    text: app.vtranslate('Record Saved!'),
                                                    animation: 'show',
                                                    type: 'success'
                                                };
                                                Vtiger_Helper_Js.showPnotify(msg_params);
                                                //reload Customer Profile related list
                                                thisInstance.showANCustomerInfo();
                                            } else {
                                                Vtiger_Helper_Js.showPnotify(data.result.message);
                                                element.removeAttr('disabled');
                                                aDeferred.resolve(data);
                                            }
                                        }
                                    },
                                    function (error, err) {
                                        parentElement.progressIndicator({'mode': 'hide'});
                                    }
                                );
                                return aDeferred.promise();
                            } else {
                                //If validation fails, form should submit again
                                element.removeAttr('disabled');
                                // to avoid hiding of error message under the fixed nav bar
                                //app.formAlignmentAfterValidation(form);
                                Vtiger_Helper_Js.showPnotify(app.vtranslate('Please fill to all required fields.'));
                            }
                        });
                    });
                });
            }
        }
    },

    setBankCardEditable: function (el) {
        var blockContainer = $(el).closest('.blockContainer');
        blockContainer.find('input').removeAttr('disabled');
        blockContainer.find('select').removeAttr('disabled');
        blockContainer.css({'background-color': 'transparent'});
        blockContainer.data('editable', true);
        $('select', blockContainer).trigger('liszt:updated');
        $(el).parent().hide();
    },

    setCardEditable: function (el) {
        var paymentProfileForm = $(el).find('form#paymentProfileForm');
        var cardBlockContainer = $('input[name=card_number]', paymentProfileForm).closest('table');
        cardBlockContainer.find('input').removeAttr('disabled');
        cardBlockContainer.find('select').removeAttr('disabled');
        cardBlockContainer.css({'background-color': 'transparent'});
        cardBlockContainer.find('table').data('editable', true);
        $('select', cardBlockContainer).trigger('liszt:updated');
        el.remove();
    },

    setBankEditable: function (el) {
        var paymentProfileForm = $(document).find('form#paymentProfileForm');
        var bankBlockContainer = $('input[name=account_number]', paymentProfileForm).closest('table');
        bankBlockContainer.find('input').removeAttr('disabled');
        bankBlockContainer.find('select').removeAttr('disabled');
        bankBlockContainer.css({'background-color': 'transparent'});
        bankBlockContainer.data('editable', true);
        $('select', bankBlockContainer).trigger('liszt:updated');
        el.remove();
    },

    showPaymentMethodDetail: function (payment_method) {
        if ($('#paymentProfileForm').length == 0) {
            return;
        }
        if (typeof payment_method == 'undefined') {
            var payment_method = $('#paymentProfileForm [name=payment_method]').val();
        }
        var container = $('#paymentProfileForm');
        if (payment_method == 'CreditCardSimpleType') {
            //show credit card block
            container.find('[name=card_number]').closest('table').show();
            //hide bank account block
            container.find('[name=bank_name]').closest('table').hide();
        } else if (payment_method == 'BankAccountType') {
            //hide credit card block
            container.find('[name=card_number]').closest('table').hide();
            //show bank account block
            container.find('[name=bank_name]').closest('table').show();
        } else {
            //hide credit card block
            container.find('[name=card_number]').closest('table').hide();
            //hide bank account block
            container.find('[name=bank_name]').closest('table').hide();
        }
    },

    /**
     * Function returns the record id
     */
    getRecordId: function () {
        var view = app.getViewName();
        var recordId;
        if (view == "Edit") {
            recordId = jQuery('[name="record"]').val();
        } else if (view == "Detail") {
            recordId = jQuery('#recordId').val();
        }
        return recordId;
    },

    registerEvent: function () {
        this.addRelatedTab();
    }

}

jQuery(document).ready(function () {
    Vtiger_VTEPayments_AuthorizeNet_Js.registerEvent();
});