/* ********************************************************************************
 * The content of this file is subject to the Google Address ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("GoogleAddress_Js", {
    editInstance: false,
    getInstance: function () {
        if (GoogleAddress_Js.editInstance == false) {
            var instance = new GoogleAddress_Js();
            GoogleAddress_Js.editInstance = instance;
            return instance;
        }
        return GoogleAddress_Js.editInstance;
    },
    autoPlaces: function (felement, ref, fullstreet) {
        var dataMapping = mappingAddress[felement];
        var field = dataMapping.street;
        var results = document.getElementById(field + '_results');
        results.innerHTML = "";
        results.style.display = 'none';
        if (jQuery("#detailView").length > 0) {
            var form = jQuery("#detailView");
            var module = app.module();
            var record = jQuery('#recordId').val();
            var service = new google.maps.places.PlacesService(results);
            service.getDetails({
                reference: ref
            }, function (place, status) {
                if (status == google.maps.places.PlacesServiceStatus.OK) {
                    results.innerHTML = place.adr_address;

                    for (felementName in dataMapping) {
                        if (felementName != 'address_name' && dataMapping[felementName] != '') {

                            var fieldName = dataMapping[felementName];
                            var tdElement = jQuery('#' + module + '_detailView_fieldValue_' + fieldName);
                            var detailViewValue = jQuery('.value', tdElement);
                            var fieldValue = '';
                            switch (felementName) {
                                case 'street':
                                    var streetAddress = jQuery('#' + field + '_results').find('.street-address').html();
                                    if (streetAddress != null && streetAddress != '') {
                                        fieldValue = streetAddress;
                                    } else if (place.address_components[0].types[0] == 'street_number' && place.address_components[1].types[0] == 'route') {
                                        fieldValue = place.address_components[0].short_name + ' ' + place.address_components[1].long_name;
                                    } else {
                                        fieldValue = fullstreet;
                                    }
                                    break;
                                case 'city':
                                    fieldValue = jQuery('#' + field + '_results').find('.locality').html();
                                    break;
                                case 'state':
                                    fieldValue = jQuery('#' + field + '_results').find('.region').html();
                                    break;
                                case 'postal_code':
                                    var postal_code = jQuery('#' + field + '_results').find('.postal-code').html();
                                    var country_name = jQuery('#' + field + '_results').find('.country-name').html();
                                    if (typeof dataMapping.last_4_zipcode_digits != 'undefined' && postal_code.length == 10 && country_name == 'United States') {
                                        postal_code = postal_code.substring(0, 5);
                                        fieldValue = postal_code;
                                    } else {
                                        fieldValue = postal_code;
                                    }
                                    break;
                                case 'country':
                                    fieldValue = jQuery('#' + field + '_results').find('.country-name').html();
                                    break;
                                case 'lat': // Todo
                                    fieldValue = jQuery('#' + field + '_results').find('.lat').html();
                                    break;
                                case 'long': // Todo
                                    fieldValue = jQuery('#' + field + '_results').find('.lng').html();
                                    break;
                                case 'phone':
                                    fieldValue = place.international_phone_number;
                                    break;
                                case 'website':
                                    fieldValue = place.website;
                                    break;
                                case 'company_name' :
                                    fieldValue = place.name;
                                    break;
                                case 'time_zone':
                                    var utc_offset = place.utc_offset;
                                    var time_zone = 0;
                                    if (utc_offset < 0) {
                                        time_zone = (utc_offset / 60 + " hours after UTC");
                                    }
                                    else {
                                        time_zone = (utc_offset / 60 + " hours before UTC");
                                    }
                                    fieldValue = time_zone;
                                    break;
                                default:
                                    fieldValue = '';
                            }

                            if (felementName == 'state' && (jQuery('#' + field + '_results').find('.region').html() == null || jQuery('#' + field + '_results').find('.locality').html() == null)) {
                                for (var i = 0; i < place.address_components.length; i++) {
                                    var addressType = place.address_components[i].types[0];
                                    if (addressType == 'administrative_area_level_1') {
                                        var val = place.address_components[i]['short_name'];
                                        if (jQuery('#' + field + '_results').find('.region').html() == null) {
                                            fieldValue = val;
                                        } else if (jQuery('#' + field + '_results').find('.locality').html() == null) {
                                            fieldValue = val;
                                        }

                                    }
                                }
                            } else if (felementName == 'city' && jQuery('#' + field + '_results').find('.locality').html() == null) {
                                for (var i = 0; i < place.address_components.length; i++) {
                                    var addressType = place.address_components[i].types[0];
                                    if (addressType == 'administrative_area_level_1') {
                                        var val = place.address_components[i]['short_name'];
                                        fieldValue = val;

                                    }
                                }
                            } else if (felementName == 'sublocality') {
                                for (var i = 0; i < place.address_components.length; i++) {
                                    var addressType = place.address_components[i].types[0];
                                    if (addressType == 'administrative_area_level_1') {
                                        var val = place.address_components[i]['short_name'];
                                        fieldValue = val;

                                    }
                                }
                            } else if (felementName == 'last_4_zipcode_digits') {
                                var data = {};
                                data['record'] = record;
                                data['module'] = module;
                                data['action'] = 'SaveAjax';
                                var postal_code = jQuery('#' + field + '_results').find('.postal-code').html();
                                var country_name = jQuery('#' + field + '_results').find('.country-name').html();
                                if (postal_code.length == 10 && (country_name == 'United States' || country_name == 'USA')) {
                                    app.request.post({data: data}).then(function(err,data){
                                        postal_code = postal_code.substring(6);
                                        var nameField = dataMapping.last_4_zipcode_digits;
                                        fieldValue = data[nameField]['value'] + ' ' + postal_code;
                                        var data = {};
                                        data['record'] = record;
                                        data['module'] = module;
                                        data['action'] = 'SaveAjax';
                                        data['field'] = fieldName;
                                        data['value'] = fieldValue;
                                        app.request.post({data: data}).then(function(err,data){
                                            var displayValue = htmlDecode(data[nameField]['display_value']);
                                            var fieldValue = htmlDecode(rdata[nameField]['value']);
                                            jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.value').html(displayValue);
                                            jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.value').removeClass('hide');
//                                            jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.edit').addClass('hide');
                                            jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.edit .fieldname').attr('data-prev-value', fieldValue);
                                            jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.edit [name="' + nameField + '"]').val(fieldValue);
                                            //if select element
                                            if (jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.edit [name="' + nameField + '"]').is('select')) {
                                                jQuery('#' + module + '_detailView_fieldValue_' + nameField + ' span.edit [name="' + nameField + '"]').trigger("liszt:updated");
                                            }
                                        });
                                    });
                                }
                            }
                            //detailViewValue.html(fieldValue);
                            var aDeferred = jQuery.Deferred();

                            fieldValue = fieldValue != null ? fieldValue : '';
                            if (felementName != 'last_4_zipcode_digits') {
                                var data = {};
                                data['record'] = record;
                                data['module'] = module;
                                data['action'] = 'SaveAjax';
                                data['field'] = fieldName;
                                data['value'] = fieldValue;
                                app.request.post({data: data}).then(
                                    function(err,data){
                                        jQuery.each(dataMapping, function (index, val) {
                                            if (index != 'address_name' && index != 'sublocality' && index != 'last_4_zipcode_digits') {
                                                var displayValue = htmlDecode(data[val]['display_value']);
                                                var fieldValue = htmlDecode(data[val]['value']);
                                                jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.value').html(displayValue);
                                                jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.value').removeClass('hide');
//                                                jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.edit').addClass('hide');
                                                jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.edit .fieldname').attr('data-prev-value', fieldValue);
                                                jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.edit [name="' + val + '"]').val(fieldValue);
                                                // Close edit
                                                jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.edit').find(".inlineAjaxSave").trigger("click");
                                                //if select element
                                                if (jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.edit [name="' + val + '"]').is('select')) {
                                                    jQuery('#' + module + '_detailView_fieldValue_' + val + ' span.edit [name="' + val + '"]').trigger("liszt:updated");
                                                }
                                            }

                                        })

                                    }
                                );
                            }
                        }
                    }
                }
            });

        } else {
            var form = jQuery('#EditView');
            var service = new google.maps.places.PlacesService(results);
            service.getDetails({
                reference: ref
            }, function (place, status) {
                if (status == google.maps.places.PlacesServiceStatus.OK) {
                    var fulAddress = place.adr_address;
                    fulAddress += "<span class='lat'>" + place.geometry.location.lat().toString() + "</span><span class='lng'>" + place.geometry.location.lng().toString() + "</span>";
                    results.innerHTML = fulAddress;
                    var streetAddress = jQuery('#' + field + '_results').find('.street-address').html();

                    if (streetAddress != null && streetAddress != '') {
                        form.find('[name="' + dataMapping.street + '"]').val(streetAddress);
                    } else if (place.address_components[0].types[0] == 'street_number' && place.address_components[1].types[0] == 'route') {
                        var address = place.address_components[0].short_name + ' ' + place.address_components[1].long_name;
                        form.find('[name="' + dataMapping.street + '"]').val(address);
                    } else {
                        form.find('[name="' + dataMapping.street + '"]').val(fullstreet);
                    }
                    form.find('[name="' + dataMapping.city + '"]').val(jQuery('#' + field + '_results').find('.locality').html());
                    form.find('[name="' + dataMapping.state + '"]').val(jQuery('#' + field + '_results').find('.region').html());
                    //form.find('[name="'+dataMapping.postal_code+'"]').val(jQuery('#'+field+'_results').find('.postal-code').html());
                    form.find('[name="' + dataMapping.country + '"]').val(jQuery('#' + field + '_results').find('.country-name').html());
                    form.find('[name="' + dataMapping.lat + '"]').val(jQuery('#' + field + '_results').find('.lat').html());
                    form.find('[name="' + dataMapping.long + '"]').val(jQuery('#' + field + '_results').find('.lng').html());

                    //If picklist
                    if (form.find('select[name="' + dataMapping.city + '"]').is('select')) {
                        form.find('select[name="' + dataMapping.city + '"]').trigger("liszt:updated");
                    }
                    if (form.find('select[name="' + dataMapping.state + '"]').is('select')) {
                        form.find('select[name="' + dataMapping.state + '"]').trigger("liszt:updated");
                    }
                    if (form.find('select[name="' + dataMapping.postal_code + '"]').is('select')) {
                        form.find('select[name="' + dataMapping.postal_code + '"]').trigger("liszt:updated");
                    }
                    if (form.find('select[name="' + dataMapping.country + '"]').is('select')) {
                        form.find('select[name="' + dataMapping.country + '"]').trigger("liszt:updated");
                    }

                    for (var i = 0; i < place.address_components.length; i++) {
                        var addressType = place.address_components[i].types[0];
                        if (addressType == 'administrative_area_level_1') {
                            var val = place.address_components[i]['short_name'];
                            if (jQuery('#' + field + '_results').find('.region').html() == null) {
                                form.find('[name="' + dataMapping.state + '"]').val(val);
                            } else if (jQuery('#' + field + '_results').find('.locality').html() == null) {
                                form.find('[name="' + dataMapping.state + '"]').val(val);
                            }
                        } else if (addressType == 'sublocality_level_1') {
                            var val = place.address_components[i]['short_name'];
                            if (jQuery('#' + field + '_results').find('.locality').html() == null) {
                                form.find('[name="' + dataMapping.city + '"]').val(val);
                            }
                            form.find('[name="' + dataMapping.sublocality + '"]').val(val);
                        }
                    }
                    var postal_code = jQuery('#' + field + '_results').find('.postal-code').html();
                    var country_name = jQuery('#' + field + '_results').find('.country-name').html();
                    if (typeof dataMapping.last_4_zipcode_digits != 'undefined' && postal_code.length == 10 && (country_name == 'United States' || country_name == 'USA')) {
                        var last_4_zipcode_digits = postal_code.substring(6);
                        postal_code = postal_code.substring(0, 5);
                        form.find('[name="' + dataMapping.last_4_zipcode_digits + '"]').val(jQuery('#' + app.getModuleName() + '_editView_fieldName_' + dataMapping.last_4_zipcode_digits).val() + ' ' + last_4_zipcode_digits);
                    }
                    form.find('[name="' + dataMapping.postal_code + '"]').val(postal_code);
                    
                    if(dataMapping.hasOwnProperty('phone')){
                        form.find('[name="'+dataMapping['phone']+'"]').val(place.international_phone_number);
                    }
                    if(dataMapping.hasOwnProperty('company_name')){
                        form.find('[name="'+dataMapping.company_name+'"]').val(place.name);
                    }
                    
                    if(dataMapping.hasOwnProperty('website')){
                        form.find('[name="'+dataMapping['website']+'"]').val(place.website);
                    }
                    if(dataMapping.hasOwnProperty('time_zone')){
                        var utc_offset = place.utc_offset;
                        var time_zone = 0;
                        if (utc_offset < 0) {
                            time_zone = (utc_offset / 60 + " hours after UTC");
                        }
                        else {
                            time_zone = (utc_offset / 60 + " hours before UTC");
                        }
                        form.find('[name="' + dataMapping['time_zone'] + '"]').val(time_zone);
                    }
                }
            });
        }
    }
}, {});

function loadScriptForGoogleAddress() {
    if (document.getElementById('map_canvas') == null){
        $("body").append("<div id='map_canvas' style='display: none!important;' class='hide'></div>");
    }
    if ((typeof google != 'undefined' && typeof google.maps != 'undefined') || jQuery('#view').val() == 'List') return;
    var params = {};
    var googleApiKey;

    params['mode'] = 'getGoogleApiKey';
    params['action'] = 'ActionAjax';
    params['module'] = 'GoogleAddress';

    googleApiKey = 'AIzaSyA_-ZgmtfP58eYNtV7I5t68Lai4UHgMiec';

    app.request.post({async: false, data: params}).then(function (err, data) {
        if (err === null) {
            if (data.apikey != '') {
                googleApiKey = data.apikey;
            }
        }
        if (typeof google == 'undefined' || typeof google.maps == 'undefined'){
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'https://maps.google.com/maps/api/js?&v=3.exp&key=' + googleApiKey + '&libraries=places';
            document.body.appendChild(script);
        }
    });
    return true;
}

function htmlDecode(value) {
    return $('<div/>').html(value).text();
}
var mappingAddress = null;
jQuery(document).ready(function () {
    // Only load when view is Edit or Detail
    if(app.view()!='Edit' && app.view()!='Detail') return;
    var isExistedGoogleAPI = loadScriptForGoogleAddress();
    if (jQuery("#detailView").length > 0) {
        var form = jQuery("#detailView");
        var module = _META.module;
    } else {
        var form = jQuery('#EditView');
        var module = form.find('input[name="module"]').val();
    }

    if (typeof(module) != 'undefined') {
        // register event to close result list
        jQuery(document).on('click', '*', function () {
            jQuery(document).find('div[id$="_results"]').hide();
        });

        // Get config fields
        var actionParams = {
            "module": "GoogleAddress",
            "mode": 'getConfigFields',
            "action": "ActionAjax",
            "source_module": module,
        };
        app.request.post({data: actionParams}).then(
            function (err, data) {
                if (data) {
                    var mapping = data.mapping;
                    var countries = data.countries;
                    mappingAddress = mapping;
                    for (felement in mapping) {
                        var dataMapping = mapping[felement];
                        var field = dataMapping.street;
                        if (app.view() == 'Edit'){
                            form.find('[name="' + field + '"]').parent().closest('.fieldValue').append('<div id="' + field + '_results" data-id="' + felement + '" style="border: 1px solid #ccc; position: absolute; z-index:1; background: #fff; padding: 0 7px; display: none"></div>');
                        } else {
                            form.find('[data-name="' + field + '"]').closest('.fieldValue').append('<div id="' + field + '_results" data-id="' + felement + '" style="border: 1px solid #ccc; position: absolute; z-index:1; background: #fff; padding: 0 7px; display: none;margin-top: 50px;"></div>');
                        }
                        form.on('keypress', '[name="' + field + '"]', function (e) {
                            jQuery(document).off('click', '*');
                            var fieldName = jQuery(e.currentTarget).attr('name');
                            var textVal = jQuery(e.currentTarget).val();
                            var results = jQuery('#' + fieldName + '_results');
                            var elementId = results.data('id');
                            results.html('');
                            results.hide();
                            var resultsHtml = '';
                            if (textVal) {
                                if (countries != '' && countries != null) {

                                    jQuery.each(countries, function (index, value) {
                                        var service = new google.maps.places.AutocompleteService();
                                        service.getPlacePredictions({
                                            input: textVal,
                                            componentRestrictions: {country: value}
                                        }, function (predictions, status) {
                                            if (status != google.maps.places.PlacesServiceStatus.OK) {
                                                //alert(status);
                                                return;
                                            }

                                            for (var i = 0, prediction; prediction = predictions[i]; i++) {
                                                resultsHtml += '<div style="cursor:pointer;margin:5px 0;" id="default_address" onclick="GoogleAddress_Js.autoPlaces(' + elementId + ', \'' + prediction.reference + '\',\'' + prediction.terms[0].value + '\')" onmouseover="jQuery(this).css(\'background-color\',\'#ffff00\')" onmouseout="jQuery(this).css(\'background-color\',\'\')">' + prediction.description + '</div>';
                                            }
                                            results.html(resultsHtml);
                                            results.show();
                                        });
                                    });
                                } else {
                                    var service = new google.maps.places.AutocompleteService(jQuery(e.currentTarget), {types: ['geocode']});
                                    service.getPlacePredictions({
                                        input: textVal
                                    }, function (predictions, status) {
                                        if (status != google.maps.places.PlacesServiceStatus.OK) {
                                            //alert(status);
                                            return;
                                        }
                                        for (var i = 0, prediction; prediction = predictions[i]; i++) {
                                            resultsHtml += '<div style="cursor:pointer;margin:5px 0;" id="default_address" onclick="GoogleAddress_Js.autoPlaces(' + elementId + ', \'' + prediction.reference + '\',\'' + prediction.terms[0].value + '\')" onmouseover="jQuery(this).css(\'background-color\',\'#ffff00\')" onmouseout="jQuery(this).css(\'background-color\',\'\')">' + prediction.description + '</div>';
                                        }
                                        results.html(resultsHtml);
                                        results.show();
                                    });
                                }
                            }
                        });
                    }
                }
            }
        );
    }
});