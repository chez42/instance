/**
 * Created by JetBrains PhpStorm.
 * User: Stefan Warnat <support@stefanwarnat.de>
 * Date: 18.02.15 23:13
 * You must not use this file without permission.
 */
jQuery(function() {

    var html = '<li><div id="openTimeTracker" class="dropdown"><div class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true"><a class="fa fa-clock-o" id="openTimeTrackerToggle" aria-hidden="true" href="#openTimeTracker"><img style="display:none; -webkit-filter:invert(100%);filter:invert(100%);" id="pendingTimerCounter" alt="running Timer"  title="running Timer" data-url="modules/Timecontrol/tmp/user_' +_USERMETA.id + '.png" src="modules/Timecontrol/tmp/user_' + _USERMETA.id + '.png?nocache=' + Math.random() + '" /></a></div><div class="dropdown-menu" style="width:400px;padding:5px;" id="timeControlPopUp"><div id="timeControlPopUpContent" style="max-height:400px;overflow: auto;">&nbsp;</div><div id="timeControlBtns" style="margin-top:10px;"></div></div></div></li>';

    jQuery('#navbar .nav').prepend(html);

    jQuery('#pendingTimerCounter').on('load', function() {
        jQuery(this).show();
    });

    jQuery('#openTimeTracker').on('click', function() {
        Timecontrol.loadTimerPopup();
    });

    if(window.location.href.indexOf('tcaccountingids') != -1) {
        var accountingids = Timecontrol.getQueryParams('tcaccountingids').split(',');

        if(accountingids.length > 0) {
            jQuery('form#EditView').append('<input type="hidden" name="timecontrol_ids" id="timecontrol_ids" value="' + accountingids.join(',') + '"/>');

            jQuery.post('index.php', {module:'Timecontrol', action:'GetAccountingData', records:accountingids}, function(response) {
                var data = response.data;

                if(response.accountid != 0 && jQuery('[name="account_id"]').length > 0) {
                    var obj = Vtiger_Edit_Js.getInstance();
                    var container = jQuery('[name="account_id"]').closest('td');
                    obj.setReferenceFieldValue(container, response.accountid);
                }

                Timecontrol.setProductData(data[0], 1);

                var rowIndex = 2;

                if(data.length > 1) {
                    data.shift();

                    jQuery.each(data, function(index, value) {
                        /*if(value.module == 'Products') {
                            jQuery('#addProduct').trigger('click');
                        } else {*/
                            jQuery('#addService').trigger('click');
                        //}

                        Timecontrol.setProductData(value, rowIndex);
                        rowIndex++;
                    });
                }
            }, 'json');
        }
    }
    
    if(window.location.href.indexOf('recallid') != -1) {
        var accountingids = Timecontrol.getQueryParams('recallid').split(',');
        var invoceId = Timecontrol.getQueryParams('record');
        if(accountingids.length > 0) {
            jQuery('form#EditView').append('<input type="hidden" name="timecontrol_ids" id="timecontrol_ids" value="' + accountingids.join(',') + '"/>');

            jQuery.post('index.php', {module:'Timecontrol', action:'GetAccountingData', records:accountingids, invoiceid:invoceId}, function(response) {
                var data = response.data;

                console.log(data);
                return false;
                Timecontrol.setProductData(data[0], 1);

                var rowIndex = 2;

                if(data.length > 1) {
                    data.shift();

                    jQuery.each(data, function(index, value) {
                        /*if(value.module == 'Products') {
                            jQuery('#addProduct').trigger('click');
                        } else {*/
                            jQuery('#addService').trigger('click');
                        //}

                        Timecontrol.setProductData(value, rowIndex);
                        rowIndex++;
                    });
                }
            }, 'json');
        }
    }
});


var Timecontrol = {
    setProductData:function(productdata, rowindex) {
        var row = jQuery('#row' + rowindex);

        var instance = Inventory_Edit_Js.getInstance();
		
        var tmp = {};
        tmp[productdata.productid] = {
			id:productdata.productid,
            name: productdata.productlabel,
            listprice: productdata.unit_price,
            taxes:productdata.taxes,
            listpricevalues:[],
            quantityInStock:99999,
            description:productdata.description,
			entityIdentifier:productdata.module,
			entityType:productdata.module,
			module:productdata.module
        };
//console.log(tmp);
		var taxes = productdata.taxes;
		
		/*$.each(taxes,function( index , value) {
			console.log(value.percentage);
			jQuery('#'+index+'_pecentage'+rowindex).val(value.percentage);
		});*/
		
        jQuery('#qty' + rowindex).val(productdata.quantity);
		jQuery('#lineItemType' + rowindex).val(productdata.module);

		//jQuery("#taxtype").val('group').prop("selected", false).change();
		jQuery("[name='taxtype']").val('individual').trigger('change');
		jQuery("[name='invoicestatus']").val('Created').trigger('change');
		
		if( rowindex == 1){
			var product_row = jQuery('.lineItemPopup',row);
			product_row.removeClass('vicon-products').addClass('vicon-services');
			product_row.attr('data-popup','ServicesPopup');
			product_row.attr('data-module-name','Services');
			product_row.attr('data-field-name','serviceid');
			product_row.attr('title','Services');
//			console.log(product_row.attr('title','Services'));
		}
		var d = new Date();
		jQuery("[name='invoicedate']").val(formatDate(d));
		jQuery("[name='duedate']").val(formatDate(addDays(d,14)));
		
        instance.mapResultsToFields( jQuery('tr#row'+rowindex), tmp);
		
    },
    reloadWidget:function() {
        var widgetContainer = jQuery('div.widgetContainer[data-url*="module=Timecontrol&view=SidebarWidget&mode=showSidebar"]');
        var key = widgetContainer.attr('id');
        app.cacheSet(key, 0);
        widgetContainer.html('');

        Vtiger_Index_Js.loadWidgets(widgetContainer);
    },
    getQueryParams: function(paramName)
    {
        var sURL = window.document.URL.toString();
        if (sURL.indexOf("?") > 0)
        {
            var arrParams = sURL.split("?");
            var arrURLParams = arrParams[1].split("&");
            var arrParamNames = new Array(arrURLParams.length);
            var arrParamValues = new Array(arrURLParams.length);

            var i = 0;
            for (i = 0; i<arrURLParams.length; i++)
            {
                var sParam =  arrURLParams[i].split("=");
                arrParamNames[i] = sParam[0];
                if (sParam[1] != "")
                    arrParamValues[i] = unescape(sParam[1]);
                else
                    arrParamValues[i] = "No Value";
            }

            for (i=0; i<arrURLParams.length; i++)
            {
                if (arrParamNames[i] == paramName)
                {
                    //alert("Parameter:" + arrParamValues[i]);
                    return arrParamValues[i];
                }
            }
            return "No Parameters Found";
        }
    },
    initTimer:function(ele) {
        jQuery(ele).stopwatch({
            startTime: new Date().valueOf() - (Number(jQuery(ele).data('staredts')) * 1000),
            formatter:function(millis, data){return jintervals(Math.floor(millis / 1000), '{DD?:}{hh}:{mm}:{ss}'); }
        }).stopwatch('start');
    },
    createBill:function() {
        var listInstance = Vtiger_List_Js.getInstance();
        var selectedIds = listInstance.readSelectedIds(false).slice();
        var excludedIds = listInstance.readExcludedIds(false).slice();
        var cvId = listInstance.getCurrentCvId();

        if(selectedIds == 'all') {
            jQuery.ajaxSetup({async:false});
            var parameter = listInstance.getDefaultParams();
            parameter.module = 'Timecontrol';
            parameter.view = undefined;
            parameter.action = 'GetSelectedIds';

            jQuery.post('index.php', parameter, function(response) {
                selectedIds =  response.ids;
            }, 'json');
            jQuery('#workflowDesignerTotal').html(selectedIds.length);
            jQuery.ajaxSetup({async:true});
        }

        window.location.href = "index.php?module=Invoice&view=Edit&tcaccountingids=" + selectedIds.join(',');
    },
    loadTimerPopup:function() {
        var html = '';

        jQuery.get('index.php', {
                    module:'Timecontrol',
                    action:'TimePopup'
                }, function(response) {
                    var html = '';
                    if(jQuery('.startCounter').length > 0) {
                        jQuery('.startCounter').stopwatch('destroy');
                    }

                    if(response.timer.length == 0) {
                        html += '<div style="text-align:center;font-style: italic;padding:5px 0;">' + response.label['no existing timer'] +  '</div><hr />';
                    }

                    html += '<h4 style="padding:5px;border-bottom:2px solid #ccc;">' + response.timer.length + ' ' + response.label['running timer'] + '</h4>';

                    jQuery.each(response.timer, function(index, value) {
                        html += '<div style="border-bottom:1px solid #ccc;padding: 5px;margin:0;">';
                            html += '<div class="clearfix">';
                                html += '<span class="pull-left"><a style="padding:0;" href="index.php?module=Timecontrol&view=Detail&record=' + value.timecontrolid + '">' + value.title + '</a></span>';
                                html += '<span class="pull-right"><a style="padding:0;"href="' + value.relatedurl + '"><strong>' + value.relatedname + '</strong></a></span>';
                            html += '</div>';

                        html += '<div style="float:right;"><a href="index.php?module=Timecontrol&action=Finish&record=' + value.timecontrolid + '" class="fa fa-check"" style="padding:0;">&nbsp;</a></div>';
                        html += '<div style="text-align:left;font-size:16px;font-weight: bold;" class="startCounter" data-staredts="' + value.timestamp + '" id="counter_' + index + '" data-index="' + index + '" >&nbsp;</div>';
                        html += '</div>';
                    });


                    htmlBtn = '<input type="button" style="margin-left:10px;" class="pull-left btn btn-primary" value="' + response.label['create timer']+ '" id="btnCreateTimer" />';
                    htmlBtn += '<input type="text" placeholder="' + response.label['title of new timer'] + '" name="quickCreateName" id="quickCreateName" style="width:150px;margin-left:30px;" value=""/><input type="button" style="margin-right:10px;float:right;" class="pull-right btn btn-primary" value="' + response.label['quick timer']+ '" id="btnQuickTimer" />';

                    jQuery('#timeControlBtns').html(htmlBtn);
                    jQuery('#timeControlPopUpContent').html(html);

                    jQuery('#timeControlPopUp').on('click', function(e) {

                        if( !jQuery( e.target ).is( "a" ) ) {
                            e.preventDefault();
                            e.stopPropagation();

                            return false;
                        }
                    });

                    jQuery('#btnQuickTimer').on('click', function(e) {
                        jQuery.post('index.php', {
                            module:'Timecontrol',
                            action:'QuickCreate',
                            title: jQuery('#quickCreateName').val(),
                            related: jQuery('#recordId').length > 0 ? jQuery('#recordId').val() : 0
                        }, function() {
                            Timecontrol.loadTimerPopup();
                            Timecontrol.reloadCounter();
                        });
                    });

                    jQuery('#btnCreateTimer').on('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        Timecontrol.openNewTimer();
                    });

                    jQuery('.startCounter').each(function(index, value) {

                        jQuery(value).stopwatch({
                            startTime: new Date().valueOf() - (Number(jQuery(value).data('staredts')) * 1000),
                            formatter:function(millis, data){return jintervals(Math.floor(millis / 1000), '{DD?:}{hh}:{mm}:{ss}'); }
                        }).stopwatch('start');
                    });

                }, 'json');
    },
    reloadCounter:function() {
        jQuery('#pendingTimerCounter').attr('src', jQuery('#pendingTimerCounter').data('url') + '?nocache=' + Math.random());
    },
    openNewTimer:function() {

        /*var objHeader = Vtiger_Header_Js.getInstance();
        objHeader.quickCreateCallBacks.push(function(result) {
            if(result.name == 'Timecontrol') { Timecontrol.reloadWidget(); Timecontrol.reloadCounter(); }
        });*/

        jQuery(jQuery('.quickCreateModule[data-name="Timecontrol"]')[0]).trigger('click');

        Timecontrol.initRecordOnCreation();
    },
    registerEvents:function() {

    },
    finishTimecontrol:function(id) {

    },
    initRecordOnCreation: function() {
        if(typeof Vtiger_Edit_Js == 'undefined') {
            return;
        }

        window.setTimeout('Timecontrol.setRecordIdOnCreation()', 500);
    },
    setRecordIdOnCreation: function() {
        if(jQuery('.quickCreateContent #relatedto_display').length == 0) {
            window.setTimeout('Timecontrol.setRecordIdOnCreation()', 500);
        }

        if(jQuery('#recordId').length > 0) {
            var EditView = Vtiger_Edit_Js.getInstance();

            var label = '';
            jQuery('div.detailViewTitle .recordLabel span').each(function(index, value) {
                if(label != '') {
                    label += ' ' + jQuery(value).text();
                } else {
                    label += jQuery(value).text();
                }
            });

            var data = {
                'name' : label,
                'id' : jQuery('#recordId').val()
            }

            EditView.setReferenceFieldValue(jQuery('.quickCreateContent #relatedto_display').closest('td'), data);
        }

        var element = jQuery('.quickCreateContent #Timecontrol_editView_fieldName_date_start');
        var dateFormat = element.data('dateFormat');
        var vtigerDateFormat = app.convertTojQueryDatePickerFormat(dateFormat);

        element.val(jQuery.datepicker.formatDate(vtigerDateFormat, new Date()));

        /*var element = jQuery('.quickCreateContent #Timecontrol_editView_fieldName_time_start');
        element.timepicker('setTime', new Date());*/

//        var element = jQuery('.quickCreateContent #Timecontrol_editView_fieldName_title');
//        element.val('Timer ' + Math.floor(Math.random() * 10000));
//
        //jQuery('.quickCreateContent #Timecontrol_editView_fieldName_date_start').DatePickerSetDate('2015-02-04 15:15:15', true);
    }
};


	function formatDate(date) {
        var d = date,
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [day,month,year].join('.');
    }
	
	function addDays(theDate, days) {
		return new Date(theDate.getTime() + days*24*60*60*1000);
	}
	
	
/**
 * jintervals 0.7 -- JavaScript library for interval formatting
 *
 * jintervals is licenced under LGPL <http://www.gnu.org/licenses/>.
 *
 * Copyright (c) 2009 Rene Saarsoo <http://code.google.com/p/jintervals/>
 *
 * Date: 2009-10-21
 */
var jintervals=(function(){function b(g,f){return a.evaluate(new c(g),d.parse(f))}var d={parse:function(j){var h=j;var f=[];while(h.length>0){var i=/^([^\\{]+)([\\{].*|)$/.exec(h);if(i){f.push(i[1]);h=i[2]}var g=/^([{].*?(?:[}]|$))(.*)$/i.exec(h);if(g){f.push(this.parseCode(g[1]));h=g[2]}if(h.charAt(0)==="\\"){f.push(h.charAt(1));h=h.slice(2)}}return f},parseCode:function(g){var f=/^[{]([smhdg])([smhdg]*)?(ays?|ours?|inutes?|econds?|reatests?|\.)?(\?(.*))?[}]$/i;var h=f.exec(g);if(!h){return false}return{type:h[1].toUpperCase(),limited:(h[1].toLowerCase()==h[1]),paddingLength:(h[2]||"").length+1,format:(h[3]||"")==""?false:(h[3]=="."?"letter":"full"),optional:!!h[4],optionalSuffix:h[5]||""}}};var a={evaluate:function(g,j){var i=this.smallestUnit(j);var n="";while(j.length>0){var f=j.shift();if(typeof f==="string"){n+=f}else{if(typeof f==="object"){var l=(f.type=="G")?g.getGreatestUnit():f.type;var h=(f.type=="G")?l:i;var k=g.get(l,f.limited,h);var m=f.format?e.translate(f.format,l,k):"";if(!f.optional||g.get(l)!=0){n+=this.zeropad(k,f.paddingLength)+m+f.optionalSuffix}}else{n+="?"}}}return n},smallestUnit:function(k){var f={S:0,M:1,H:2,D:3};var h="D";for(var g=0;g<k.length;g++){if(typeof k[g]==="object"){var j=k[g].type;if(j!=="G"&&f[j]<f[h]){h=j}}}return h},zeropad:function(g,f){var h=f-(""+g).length;return(h>0)?this.repeat("0",h)+g:g},repeat:function(g,j){var f="";for(var h=0;h<j;h++){f+=g}return f}};var c=function(f){this.seconds=f};c.prototype={get:function(h,f,g){if(!this[h]){return"?"}return this[h](f,g)},S:function(f,g){return f?this.seconds-this.M(false,g)*60:this.seconds},M:function(f,h){var g=this.seconds/60;g=(h==="M")?Math.round(g):Math.floor(g);if(f){g=g-this.H(false,h)*60}return g},H:function(g,h){var f=this.M(false,h)/60;f=(h==="H")?Math.round(f):Math.floor(f);if(g){f=f-this.D(false,h)*24}return f},D:function(f,g){var h=this.H(false,g)/24;return(g==="D")?Math.round(h):Math.floor(h)},getGreatestUnit:function(){if(this.seconds<60){return"S"}else{if(this.M(false,"M")<60){return"M"}else{if(this.H(false,"H")<24){return"H"}else{return"D"}}}}};var e={translate:function(h,f,g){var i=this.locales[this.currentLocale];var j=i[h][f];if(typeof j==="string"){return j}else{return j[i.plural(g)]}},locale:function(f){if(f){this.currentLocale=f}return this.currentLocale},currentLocale:"en_US",locales:{en_US:{letter:{D:"d",H:"h",M:"m",S:"s"},full:{D:[" day"," days"],H:[" hour"," hours"],M:[" minute"," minutes"],S:[" second"," seconds"]},plural:function(f){return(f==1)?0:1}},et_EE:{letter:{D:"p",H:"h",M:"m",S:"s"},full:{D:[" p\u00E4ev"," p\u00E4eva"],H:[" tund"," tundi"],M:[" minut"," minutit"],S:[" sekund"," sekundit"]},plural:function(f){return(f==1)?0:1}},lt_LT:{letter:{D:"d",H:"h",M:"m",S:"s"},full:{D:[" dieną"," dienas"," dienų"],H:[" valandą"," valandas"," valandų"],M:[" minutę"," minutes"," minučių"],S:[" sekundę"," sekundes"," sekundžų"]},plural:function(f){return(f%10==1&&f%100!=11?0:f%10>=2&&(f%100<10||f%100>=20)?1:2)}},ru_RU:{letter:{D:"д",H:"ч",M:"м",S:"с"},full:{D:[" день"," дня"," дней"],H:[" час"," часа"," часов"],M:[" минута"," минуты"," минут"],S:[" секунда"," секунды"," секунд"]},plural:function(f){return(f%10==1&&f%100!=11?0:f%10>=2&&f%10<=4&&(f%100<10||f%100>=20)?1:2)}},fi_FI:{letter:{D:"p",H:"h",M:"m",S:"s"},full:{D:[" päivä"," päivää"],H:[" tunti"," tuntia"],M:[" minuutti"," minuuttia"],S:[" sekunti"," sekunttia"]},plural:function(f){return(f==1)?0:1}}}};b.locale=function(f){return e.locale(f)};return b})();
// Copyright (c) 2012 Rob Cowie
// https://github.com/robcowie/jquery-stopwatch
!function(t){function a(t,a){return function(){return t+=a}}function e(t){return(10>t?"0":"")+t}function r(t){var a,r,n,o;return a=t/1e3,r=Math.floor(a%60),a/=60,n=Math.floor(a%60),a/=60,o=Math.floor(a%24),[e(o),e(n),e(r)].join(":")}function n(t,a){var e;return e="function"==typeof jintervals?function(t,a){return jintervals(t/1e3,a.format)}:r,(n=function(t,a){return e(t,a)})(t,a)}var o={init:function(e){var r={updateInterval:1e3,startTime:0,format:"{HH}:{MM}:{SS}",formatter:n};return this.each(function(){var n=t(this),o=n.data("stopwatch");if(!o){var i=t.extend({},r,e);o=i,o.active=!1,o.target=n,o.elapsed=i.startTime,o.incrementer=a(o.startTime,o.updateInterval),o.tick_function=function(){var t=o.incrementer();o.elapsed=t,o.target.trigger("tick.stopwatch",[t]),o.target.stopwatch("render")},n.data("stopwatch",o);o.target.trigger('tick.stopwatch', [o.incrementer()]);o.target.stopwatch("render");}})},start:function(){return this.each(function(){var a=t(this),e=a.data("stopwatch");e.active=!0,e.timerID=setInterval(e.tick_function,e.updateInterval),a.data("stopwatch",e)})},stop:function(){return this.each(function(){var a=t(this),e=a.data("stopwatch");clearInterval(e.timerID),e.active=!1,a.data("stopwatch",e)})},destroy:function(){return this.each(function(){{var a=t(this);a.data("stopwatch")}a.stopwatch("stop").unbind(".stopwatch").removeData("stopwatch")})},render:function(){var a=t(this),e=a.data("stopwatch");a.html(e.formatter(e.elapsed,e))},getTime:function(){var a=t(this),e=a.data("stopwatch");return e.elapsed},toggle:function(){return this.each(function(){var a=t(this),e=a.data("stopwatch");a.stopwatch(e.active?"stop":"start")})},reset:function(){return this.each(function(){var e=t(this);data=e.data("stopwatch"),data.incrementer=a(data.startTime,data.updateInterval),data.elapsed=data.startTime,e.data("stopwatch",data)})}};t.fn.stopwatch=function(a){return o[a]?o[a].apply(this,Array.prototype.slice.call(arguments,1)):"object"!=typeof a&&a?void t.error("Method "+a+" does not exist on jQuery.stopwatch"):o.init.apply(this,arguments)}}(jQuery);