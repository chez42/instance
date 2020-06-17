/*********************************************************************************
 * The content of this file is subject to the VTE Reports license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is Vtiger Experts s.r.o.
 * Portions created by Vtiger Experts s.r.o. are Copyright(C) Vtiger Experts s.r.o.
 * All Rights Reserved.
 ********************************************************************************/

var empty_values = ["todaymore", "todayless", "older1days", "older7days", "older15days", "older30days", "older60days", "older90days", "older120days"];

if (document.all)
    var browser_ie = true;
else if (document.layers)
    var browser_nn4 = true;
else if (document.layers || (!document.all && document.getElementById))
    var browser_nn6 = true;

function getObj(n, d) {

    var p, i, x;

    if (!d) {
        d = document;
    }

    if (n != undefined) {
        if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
            d = parent.frames[n.substring(p + 1)].document;
            n = n.substring(0, p);
        }
    }

    if (d.getElementById) {
        x = d.getElementById(n);
        // IE7 was returning form element with name = n (if there was multiple instance)
        // But not firefox, so we are making a double check
        if (x && x.id != n)
            x = false;
    }

    for (i = 0; !x && i < d.forms.length; i++) {
        x = d.forms[i][n];
    }

    for (i = 0; !x && d.layers && i < d.layers.length; i++) {
        x = getObj(n, d.layers[i].document);
    }

    if (!x && !(x = d[n]) && d.all) {
        x = d.all[n];
    }

    if (typeof x == 'string') {
        x = null;
    }

    return x;
}

/* VTE-CR SlOl | 6.6.2014 8:39  */
function replaceAll(find, replace, str) {
    return str.replace(new RegExp(escapeRegExp(find), 'g'), replace);
}
/* VTE-END 6.6.2014 8:39  */

function replaceUploadSize() {
    var upload = document.getElementById('key_upload_maxsize').value;
    upload = "'" + upload + "'";
    upload = upload.replace(/000000/g, "");
    upload = upload.replace(/'/g, "");
    document.getElementById('key_upload_maxsize').value = upload;
}


function vtlib_field_help_show_this(basenode, fldname) {
    var domnode = $('vtlib_fieldhelp_div');



    var helpcontent = document.getElementById('helpInfo').value;


    if (!domnode) {
        domnode = document.createElement('div');
        domnode.id = 'vtlib_fieldhelp_div';
        domnode.className = 'dvtSelectedCell';
        domnode.style.position = 'absolute';
        domnode.style.width = '150px';
        domnode.style.padding = '4px';
        domnode.style.fontWeight = 'normal';
        document.body.appendChild(domnode);

        domnode = $('vtlib_fieldhelp_div');
        Event.observe(domnode, 'mouseover', function() {
            $('vtlib_fieldhelp_div').show();
        });
        Event.observe(domnode, 'mouseout', vtlib_field_help_hide);
    }
    else {
        domnode.show();
    }
    domnode.innerHTML = helpcontent;
    fnvshobj(basenode, 'vtlib_fieldhelp_div');
}

var gcurrepfolderid = 0;
function trimfValues(value)
{
    var string_array;
    string_array = value.split(":");
    return string_array[4];
}

function vtereports_updatefOptions(sel, opSelName, selectedVal) {
    jQuery(document).ready(function() {
        var typeofdata = [];
        typeofdata['V'] = ['e', 'n', 's', 'ew', 'c', 'k', 'isn', 'isnn'];
        typeofdata['N'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['SUM'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['AVG'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['MIN'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['MAX'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['COUNT'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['T'] = ['e', 'n', 'l', 'g', 'm', 'h', 'bw', 'b', 'a', 'isn', 'isnn'];
        typeofdata['I'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['C'] = ['e', 'n', 'isn', 'isnn'];
        typeofdata['D'] = ['e', 'n', 'l', 'g', 'm', 'h', 'bw', 'b', 'a', 'isn', 'isnn'];
        typeofdata['NN'] = ['e', 'n', 'l', 'g', 'm', 'h', 'isn', 'isnn'];
        typeofdata['E'] = ['e', 'n', 's', 'ew', 'c', 'k', 'isn', 'isnn'];

        // VTE-CR SlOl
        var s_typeofdata = [];
        s_typeofdata['S'] = ['e', 'n', 's', 'ew', 'c', 'k', 'isn', 'isnn'];

        var fLabels = [];
        fLabels['e'] = app.vtranslate('EQUALS');
        fLabels['n'] = app.vtranslate('NOT_EQUALS_TO');
        fLabels['s'] = app.vtranslate('STARTS_WITH');
        fLabels['ew'] = app.vtranslate('ENDS_WITH');
        fLabels['c'] = app.vtranslate('CONTAINS');
        fLabels['k'] = app.vtranslate('DOES_NOT_CONTAINS');
        fLabels['l'] = app.vtranslate('LESS_THAN');
        fLabels['g'] = app.vtranslate('GREATER_THAN');
        fLabels['m'] = app.vtranslate('LESS_OR_EQUALS');
        fLabels['h'] = app.vtranslate('GREATER_OR_EQUALS');
        fLabels['bw'] = app.vtranslate('BETWEEN');
        fLabels['b'] = app.vtranslate('BEFORE');
        fLabels['a'] = app.vtranslate('AFTER');
        fLabels['isn'] = app.vtranslate('IS_NULL');
        fLabels['isnn'] = app.vtranslate('IS_NOT_NULL');

        var selObj = document.getElementById(opSelName);
        if (selObj) {
            var fieldtype = null;

            var currOption = selObj.options[selObj.selectedIndex];
            var currField = sel.options[sel.selectedIndex];
            if (currField.value != null && currField.value.length != 0) {
                fieldtype = trimfValues(currField.value);
                var sel_fields = JSON.parse(document.getElementById("sel_fields").value);
                var std_filter_columns = document.getElementById("std_filter_columns").value;
                if (typeof std_filter_columns != 'undefined' && std_filter_columns != "") {
                    var std_filter_columns_arr = std_filter_columns.split('<%jsstdjs%>');
                } else {
                    var std_filter_columns_arr = [];
                }
                var std_filter_criteria = document.getElementById("std_filter_criteria").value;
                if (std_filter_columns_arr.indexOf(currField.value) > -1) {
                    var std_filter_criteria_obj = selObj;
                    var std_filter_criteria_arr = JSON.parse(std_filter_criteria);
                    var nSFCVal = std_filter_criteria_obj.length;
                    for (nLoop = 0; nLoop < nSFCVal; nLoop++) {
                        std_filter_criteria_obj.remove(0);
                    }
                    std_filter_criteria_obj.options[0] = new Option('None', '');
                    var sfc_i = 1;
                    for (var filter_opt in std_filter_criteria_arr) {
                        std_filter_criteria_obj.options[sfc_i] = new Option(std_filter_criteria_arr[filter_opt], filter_opt);
                        sfc_i++;
                    }
                    for (var si = 0; si < std_filter_criteria_obj.length; si++) {
                        if (std_filter_criteria_obj.options[si].value == selectedVal) {
                            std_filter_criteria_obj.options[si].selected = true;
                        }
                    }
                } else if (sel_fields[currField.value]) {
                    var ops = s_typeofdata["S"];
                } else {

                    var ops = typeofdata[fieldtype];
                }
                // VTE-END
                var off = 0;
                if (ops != null) {

                    var nMaxVal = selObj.length;
                    for (nLoop = 0; nLoop < nMaxVal; nLoop++) {
                        selObj.remove(0);
                    }
                    selObj.options[0] = new Option('None', '');
                    if (currField.value == '') {
                        selObj.options[0].selected = true;
                    }
                    off = 1;
                    for (var i = 0; i < ops.length; i++) {
                        var label = fLabels[ops[i]];
                        if (label == null)
                            continue;
                        var option = new Option(fLabels[ops[i]], ops[i]);
                        selObj.options[i + off] = option;
                        if (currOption != null && currOption.value == option.value || option.value == selectedVal) {
                            option.selected = true;
                        }
                    }
                }
            } else {
                var nMaxVal = selObj.length;
                for (nLoop = 0; nLoop < nMaxVal; nLoop++) {
                    selObj.remove(0);
                }
                selObj.options[0] = new Option('None', '');
                if (currField.value == '') {
                    selObj.options[0].selected = true;
                }
            }
        }
    })
}

// Setting cookies
function set_cookie(name, value, exp_y, exp_m, exp_d, path, domain, secure) {
    var cookie_string = name + "=" + escape(value);

    if (exp_y) {
        var expires = new Date(exp_y, exp_m, exp_d);
        cookie_string += "; expires=" + expires.toGMTString();
    }

    if (path)
        cookie_string += "; path=" + escape(path);

    if (domain)
        cookie_string += "; domain=" + escape(domain);

    if (secure)
        cookie_string += "; secure";

    document.cookie = cookie_string;
}

// Retrieving cookies
function get_cookie(cookie_name)
{
    var results = document.cookie.match(cookie_name + '=(.*?)(;|$)');

    if (results)
        return (unescape(results[1]));
    else
        return null;
}


// Delete cookies
function delete_cookie(cookie_name)
{
    var cookie_date = new Date( );  // current date & time
    cookie_date.setTime(cookie_date.getTime() - 1);
    document.cookie = cookie_name += "=; expires=" + cookie_date.toGMTString();
}
function goToURL(url)
{
    document.location.href = url;
}

function invokeAction(actionName)
{
    if (actionName == "newReport")
    {
        goToURL("?module=VTEReports&action=NewReport0&return_module=VTEReports&return_action=index");
        return;
    }
    goToURL("/crm/ScheduleReport.do?step=showAllSchedules");
}
function verify_data(form) {
    var isError = false;
    var errorMessage = "";
    if (trim(form.folderName.value) == "") {
        isError = true;
        errorMessage += "\nFolder Name";
    }
    // Here we decide whether to submit the form.
    if (isError == true) {
        alert(app.vtranslate('MISSING_FIELDS') + errorMessage);
        return false;
    }
    return true;
}

function setObjects()
{
    var availListObj = getObj("availList");
    var selectedColumnsObj = getObj("selectedColumns");

    var moveupLinkObj = getObj("moveup_link");
    var moveupDisabledObj = getObj("moveup_disabled");
    var movedownLinkObj = getObj("movedown_link");
    var movedownDisabledObj = getObj("movedown_disabled")
}

function addColumn(columns)
{
    if (columns == "selectedColumnsRel") {
        var selectedColumnsObj = getObj("selectedColumns");
    } else if (columns == "selectedQFColumnsRel") {
        var selectedColumnsObj = getObj("selectedQFColumns");
    } else if (columns == "selectedSummaries") {
        var selectedColumnsObj = getObj("selectedSummaries");
    } else {
        var selectedColumnsObj = getObj(columns);
    }

    for (i = 0; i < selectedColumnsObj.length; i++)
    {
        selectedColumnsObj.options[i].selected = false
    }

    if (columns == "selectedColumns") {
        addColumnStep1();
    } else if (columns == "selectedColumnsRel") {
        addColumnStep3();
    } else if (columns == "selectedQFColumnsRel") {
        addColumnStep4();
    } else if (columns == "selectedSummaries") {
        addColumnStep5();
    } else {
        addColumnStep2();
    }
}

function addColumnStep1()
{
    var availListObj = getObj("availList");
    var selectedColumnsObj = getObj("selectedColumns");

    var availModules = document.getElementById("availModules");
    var selectedModule = availModules.options[availModules.selectedIndex].text;
    var selectedModule_value = availModules.options[availModules.selectedIndex].value;
    var selectedPrimaryModule = document.getElementById('primarymodule').options[document.getElementById('primarymodule').selectedIndex].value;

    // if module is != primary module it is neccessary to remove -+space from start of selectedmodule text e.g. - Invoice to Invoice
    if (selectedModule_value != selectedPrimaryModule) {
        selectedModule = selectedModule.replace("- ", "");
    }

    if (availListObj.options.selectedIndex > -1)
    {
        for (i = 0; i < availListObj.length; i++)
        {
            if (availListObj.options[i].selected == true)
            {
                var rowFound = false;
                for (j = 0; j < selectedColumnsObj.length; j++)
                {
                    if (selectedColumnsObj.options[j].value == availListObj.options[i].value)
                    {
                        var rowFound = true;
                        var existingObj = selectedColumnsObj.options[j];
                        break;
                    }
                }

                if (rowFound != true)
                {
                    var newColObj = document.createElement("OPTION");
                    newColObj.value = availListObj.options[i].value;
                    newColObj.text = availListObj.options[i].text + " [" + selectedModule + "]";
                    selectedColumnsObj.appendChild(newColObj);
                    newColObj.selected = true
                }
                else
                {
                    existingObj.selected = true
                }
                availListObj.options[i].selected = false;
                addColumnStep1();
            }
        }
    }

    var oldselectvalue = getObj("SortByColumn").value;
    getObj("SortByColumn").innerHTML = "<option value='none'>" + none_lang + "</option>";
    getObj("SortByColumn").innerHTML += selectedColumnsObj.innerHTML;
    getObj("SortByColumn").value = oldselectvalue;
}

function addColumnStep2()
{
    var availListObj = getObj("availQFList");
    var selectedColumnsObj = getObj("selectedQFColumns");

    if (availListObj.options.selectedIndex > -1)
    {
        for (i = 0; i < availListObj.length; i++)
        {
            if (availListObj.options[i].selected == true)
            {
                var rowFound = false;
                for (j = 0; j < selectedColumnsObj.length; j++)
                {
                    if (selectedColumnsObj.options[j].value == availListObj.options[i].value)
                    {
                        var rowFound = true;
                        var existingObj = selectedColumnsObj.options[j];
                        break;
                    }
                }

                if (rowFound != true)
                {
                    var newColObj = document.createElement("OPTION");
                    newColObj.value = availListObj.options[i].value;
                    if (browser_ie)
                        newColObj.innerText = availListObj.options[i].innerText;
                    else if (browser_nn4 || browser_nn6)
                        newColObj.text = availListObj.options[i].text;
                    selectedColumnsObj.appendChild(newColObj);
                    newColObj.selected = true
                }
                else
                {
                    existingObj.selected = true
                }
                availListObj.options[i].selected = false;
                addColumnStep2();
            }
        }
    }
}


function addColumnStep3() {
    var selectedColumnsObj = getObj("selectedColumns");

    var oldselectvalue = getObj("SortByColumn").value;

    getObj("SortByColumn").innerHTML = "<option value='none'>" + none_lang + "</option>";
    getObj("SortByColumn").innerHTML += selectedColumnsObj.innerHTML;

    getObj("SortByColumn").value = oldselectvalue;
}

function addColumnStep4()
{
    var availListObj = getObj("availQFList2");
    var selectedColumnsObj = getObj("selectedQFColumns");
    if (availListObj.options.selectedIndex > -1)
    {
        for (i = 0; i < availListObj.length; i++)
        {
            if (availListObj.options[i].selected == true)
            {
                var rowFound = false;
                for (j = 0; j < selectedColumnsObj.length; j++)
                {
                    if (selectedColumnsObj.options[j].value == availListObj.options[i].value)
                    {
                        var rowFound = true;
                        var existingObj = selectedColumnsObj.options[j];
                        break;
                    }
                }

                if (rowFound != true)
                {
                    var newColObj = document.createElement("OPTION");
                    newColObj.value = availListObj.options[i].value;
                    if (browser_ie)
                        newColObj.innerText = availListObj.options[i].innerText;
                    else if (browser_nn4 || browser_nn6)
                        newColObj.text = availListObj.options[i].text;
                    selectedColumnsObj.appendChild(newColObj);
                    newColObj.selected = true
                }
                else
                {
                    existingObj.selected = true
                }
                availListObj.options[i].selected = false;
                addColumnStep4();
            }
        }
    }
}

function addColumnStep5()
{

    var availListObj = getObj("availListSum");
    var selectedColumnsObj = getObj("selectedSummaries");
    if (availListObj.options.selectedIndex > -1)
    {
        for (i = 0; i < availListObj.length; i++)
        {
            if (availListObj.options[i].selected == true)
            {
                var rowFound = false;
                for (j = 0; j < selectedColumnsObj.length; j++)
                {
                    if (selectedColumnsObj.options[j].value == availListObj.options[i].value)
                    {
                        var rowFound = true;
                        var existingObj = selectedColumnsObj.options[j];
                        break;
                    }
                }

                if (rowFound != true)
                {

                    if (availListObj.options[i].value != "none") {
                        var newColObj = document.createElement("OPTION");
                        newColObj.value = availListObj.options[i].value;

                        var selectedSumModuleObj = document.getElementById("SummariesModules");
                        var selectedSumModule = selectedSumModuleObj.options[selectedSumModuleObj.selectedIndex].text;
                        if (selectedSumModule.substring(0, 2) == "- ") {
                            selectedSumModule = selectedSumModule.substring(2);
                        }

                        var avl_text = availListObj.options[i].text;
                        newColObj.text = avl_text + " (" + selectedSumModule + ")";
                        addToSummSortOrder(availListObj.options[i]);
                        selectedColumnsObj.appendChild(newColObj);
                    }
                    // newColObj.selected=true
                }
                else
                {
                    existingObj.selected = true
                }
                availListObj.options[i].selected = false;
                addColumnStep5();
            }
        }
    }
}
// VTE-CR SlOl 21. 3. 2014 14:14:58
function setSelSummSortOrder(selectedval) {
    var summaries_orderby_columnObj = getObj("summaries_orderby_column");

    if (summaries_orderby_columnObj) {
        for (j = 0; j < summaries_orderby_columnObj.length; j++)
        {
            if (summaries_orderby_columnObj.options[j].value == selectedval)
            {
                var go_j = j;
                var soc_str = summaries_orderby_columnObj.options[j].value;
                summaries_orderby_columnObj.options[j].selected = true;
                break;
            }
        }
        // document.getElementById("summaries_orderby_columnString").value = soc_str;
        if(typeof  summaries_orderby_columnObj.options[go_j] != "undefined") summaries_orderby_columnObj.options[go_j].selected = true;
    }
}
function delSummSortOrder(opt_obj) {
    var sortbyColumnsObj = getObj("summaries_orderby_column");
    for (j = 0; j < sortbyColumnsObj.options.length; j++)
    {
        if (opt_obj.value == sortbyColumnsObj.options[j].value) {
            sortbyColumnsObj.remove(j);
        }
    }
}
function delSortOrder(opt_obj) {
    var sortbyColumnsObj = getObj("SortByColumn");
    for (j = 0; j < sortbyColumnsObj.options.length; j++)
    {
        if (opt_obj.value == sortbyColumnsObj.options[j].value) {
            sortbyColumnsObj.remove(j);
        }
    }
}
// function addToSummSortOrder(opt_obj) {
//     var summaries_orderby_columnObj = getObj("summaries_orderby_column");
//     if(typeof summaries_orderby_columnObj != "undefined"){
//         var rowFound = false;
//         for (j = 0; j < summaries_orderby_columnObj.length; j++) {
//             if (summaries_orderby_columnObj.options[j].value == opt_obj.value) {
//
//                 var rowFound = true;
//                 break;`````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````````
//             }
//         }
//         if (rowFound != true) {
//             var newColObj = document.createElement("OPTION")
//             var e = document.getElementById("summaries_orderby_columnString").value;
//             "none" != opt_obj.value && (d.value = opt_obj.value, d.text = opt_obj.text, opt_obj.value == e && (d.selected = !0), b.appendChild(d))
//             if (opt_obj.value != "none") {
//                 newColObj.value = opt_obj.value
//                 newColObj.text = opt_obj.text
//                 summaries_orderby_columnObj.appendChild(newColObj);
//             }
//         }
//     }
// }
function addToSummSortOrder(a) {
    var b = getObj("summaries_orderby_column");
    if ("undefined" != typeof b) {
        var d = !1;
        for (j = 0; j < b.length; j++)if (b.options[j].value == a.value) {
            d = !0;
            break
        }
        if (1 != d) {
            d = document.createElement("OPTION");
            var e = document.getElementById("summaries_orderby_columnString").value;
            "none" != a.value && (d.value = a.value, d.text = a.text, a.value == e && (d.selected = !0), b.appendChild(d))
        }
    }
}
function addToSortOrder(opt_obj) {
    var orderby_columnObj = getObj("SortByColumn");
    if(typeof orderby_columnObj != "undefined"){
        var rowFound = false;
        for (j = 0; j < orderby_columnObj.length; j++) {
            if (orderby_columnObj.options[j].value == opt_obj.value) {
                var rowFound = true;
                break;
            }
        }
        if (rowFound != true) {
            var newColObj = document.createElement("OPTION");
            if (opt_obj.value != "none") {
                newColObj.value = opt_obj.value;
                newColObj.text = opt_obj.text;
                orderby_columnObj.appendChild(newColObj);
            }
        }
    }
}


function selectedColumnClick(oSel)
{
    var error_msg = '';
    var error_str = false;
    if (oSel.selectedIndex > -1) {
        for (var i = 0; i < oSel.options.length; ++i) {
            if (oSel.options[i].selected == true && oSel.options[i].disabled == true) {
                error_msg = error_msg + oSel.options[i].text + ',';
                error_str = true;
                oSel.options[i].selected = false;
            }
        }
    }
    if (error_str)
    {
        error_msg = error_msg.substr(0, error_msg.length - 1);
        alert(app.vtranslate('NOT_ALLOWED_TO_EDIT_FIELDS') + "\n" + error_msg);
        return false;
    }
    else
        return true;
}
function delColumn(columns)
{
    var selectedColumnsObj = getObj(columns);
    if (selectedColumnsObj.options.selectedIndex > -1)
    {
        for (i = 0; i < selectedColumnsObj.options.length; i++)
        {
            if (selectedColumnsObj.options[i].selected == true)
            {
                delSummSortOrder(selectedColumnsObj.options[i]);
                // VTE-CR SlOl 4. 9. 2013 14:55:44
                if (columns == "selectedColumns") {
                    var sortbyColumnsObj = getObj("SortByColumn");
                    for (j = 0; j < sortbyColumnsObj.options.length; j++)
                    {
                        if (selectedColumnsObj.options[i].value == sortbyColumnsObj.options[j].value) {
                            sortbyColumnsObj.remove(j);
                        }
                    }
                }
                // VTE-END
                selectedColumnsObj.remove(i);
                delColumn(columns);
            }
        }
    }
}

function hasOptions(obj) {
    if (obj != null && obj.options != null) {
        return true;
    }
    return false;
}

function swapOptions(obj, i, j) {
    var o = obj.options;
    var i_selected = o[i].selected;
    var j_selected = o[j].selected;
    var temp = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
    var temp2 = new Option(o[j].text, o[j].value, o[j].defaultSelected, o[j].selected);
    o[i] = temp2;
    o[j] = temp;
    o[i].selected = j_selected;
    o[j].selected = i_selected;
}

function moveUp(columns)
{
    // VTE-CR SlOl 2/20/2014 8:13:46 PM
    var obj = getObj(columns);
    if (!hasOptions(obj)) {
        return;
    }
    for (var i = 0; i < obj.options.length; i++) {
        if (obj.options[i].selected) {
            if (i != 0 && !obj.options[i - 1].selected) {
                swapOptions(obj, i, i - 1);
                obj.options[i - 1].selected = true;
            }
        }
    }
}

function moveDown(columns)
{
    var obj = getObj(columns);
    if (!hasOptions(obj)) {
        return;
    }
    for (var i = obj.options.length - 1; i >= 0; i--) {
        if (obj.options[i].selected) {
            if (i != (obj.options.length - 1) && !obj.options[i + 1].selected) {
                swapOptions(obj, i, i + 1);
                obj.options[i + 1].selected = true;
            }
        }
    }
}

function disableMove()
{
    var selectedColumnsObj = getObj("selectedColumns");
    var cnt = 0;
    for (i = 0; i < selectedColumnsObj.options.length; i++)
    {
        if (selectedColumnsObj.options[i].selected == true)
            cnt++
    }

    if (cnt > 1)
    {
        moveupLinkObj.style.display = movedownLinkObj.style.display = "none";
        moveupDisabledObj.style.display = movedownDisabledObj.style.display = "block"
    }
    else
    {
        moveupLinkObj.style.display = movedownLinkObj.style.display = "block";
        moveupDisabledObj.style.display = movedownDisabledObj.style.display = "none"
    }
}


function hideTabs()
{

}

function showSaveDialog()
{
    // VTE-CR SlOl 20.12.2010 VTE singlerow
    var url = "index.php?module=VTEReports&action=SaveReport";
    window.open(url, "Save_Report", "width=550,height=350,top=20,left=20;toolbar=no,status=no,menubar=no,directories=no,resizable=yes,scrollbar=no")
}

function saveAndRunReport()
{
    var primarymoduleObj = getObj("primarymodule"); // reportfolder
    var selectedColumn = trim(primarymoduleObj.value);
    document.NewReport.report_primarymodule.value = selectedColumn;
    var selectedColumnsObj = getObj("selectedColumns");
    var selectedSummariesObj = getObj("selectedSummaries");
    if (selectedColumnsObj.options.length == 0 && selectedSummariesObj.options.length == 0)
    {
        var params = {
            title: app.vtranslate('COLUMNS_CANNOT_BE_EMPTY'),
            type: 'error'
        };
        Vtiger_Helper_Js.showPnotify(params);
        return false;
    }

    var relatedmodules = '';
    var all_related_modules_str = document.getElementById('all_related_modules').value;
    if (all_related_modules_str != '') {
        var all_related_modules = all_related_modules_str.split(":");
        for (i = 0; i <= (all_related_modules.length - 1); i++)
        {
            var rel_mod_actual = 'relmodule_' + all_related_modules[i];
            var actual_rel_module = document.getElementById(rel_mod_actual);
            if (actual_rel_module.checked)
                relatedmodules += actual_rel_module.value + ':';
        }
    }
    document.NewReport.secondarymodule.value = relatedmodules;

    var escapedOptions = ['account_id', 'contactid', 'contact_id', 'product_id', 'parent_id', 'campaignid', 'potential_id', 'assigned_user_id1', 'quote_id', 'accountname', 'salesorder_id', 'vendor_id', 'time_start', 'time_end', 'lastname'];

    var conditionColumns = vt_getElementsByName('tr', "conditionColumn");
    var criteriaConditions = [];
    var sel_fields = JSON.parse(document.getElementById("sel_fields").value);
    for (var i = 0; i < conditionColumns.length; i++) {
        var columnRowId = conditionColumns[i].getAttribute("id");
        var columnRowInfo = columnRowId.split("_");
        var columnGroupId = columnRowInfo[1];
        var columnIndex = columnRowInfo[2];

        if (columnGroupId != "0")
            ctype = "f";
        else
            ctype = "g";

        var columnId = ctype + "col" + columnIndex;
        var columnObject = getObj(columnId);
        var selectedColumn = columnObject.value;
        var selectedColumnIndex = columnObject.selectedIndex;
        var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;
        if (columnObject.options[selectedColumnIndex].value != "none") {
            var comparatorId = ctype + "op" + columnIndex;
            var comparatorObject = getObj(comparatorId);
            var comparatorValue = comparatorObject.value;

            var valueId = ctype + "val" + columnIndex;
            var valueObject = getObj(valueId);
            var specifiedValue = valueObject.value;

            var extValueId = ctype + "val_ext" + columnIndex;
            var extValueObject = getObj(extValueId);
            if (extValueObject) {
                extendedValue = extValueObject.value;
            }

            var glueConditionId = ctype + "con" + columnIndex;
            var glueConditionObject = getObj(glueConditionId);
            var glueCondition = '';
            if (glueConditionObject) {
                glueCondition = glueConditionObject.value;
            }

            if(conditionColumns.length>1){
                if (!emptyCheckVte(columnId, " Column ", "text")){
                    return false;
                }
                if (!emptyCheckVte(comparatorId, selectedColumnLabel + " Option", "text")){
                    return false;
                }
            }

            var col = selectedColumn.split(":");

            var std_filter_columns = document.getElementById("std_filter_columns").value;
            if (typeof std_filter_columns != 'undefined' && std_filter_columns!=""){
                var std_filter_columns_arr = std_filter_columns.split('<%jsstdjs%>');
            }else{
                var std_filter_columns_arr = [];
            }

            if (std_filter_columns_arr.indexOf(selectedColumn) > -1) {
                if(comparatorValue=="custom"){
                    if (!emptyCheckVte("jscal_field_sdate"+columnIndex, " Column ", "text")){
                        return false;
                    }
                }
                if(comparatorValue=="custom"){
                    if (!emptyCheckVte("jscal_field_edate"+columnIndex, " Column ", "text")){
                        return false;
                    }
                }
                var start_date = document.getElementById("jscal_field_sdate"+columnIndex).value;
                var end_date = document.getElementById("jscal_field_edate"+columnIndex).value;
                var specifiedValue = start_date+"<;@STDV@;>"+end_date;
            }else{
                if (escapedOptions.indexOf(col[3]) == -1) {
                    if (col[4] == 'T') {
                        var datime = specifiedValue.split(" ");
                        if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH"))
                            return false;
                        if (datime.length > 1)
                            if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                                return false
                    }
                    else if (col[4] == 'D')
                    {
                        if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                            return false;
                        if (extValueObject) {
                            if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                                return false
                        }
                    } else if (col[4] == 'I')
                    {
                        if (!intValidate(valueId, selectedColumnLabel + " (Integer Criteria)" + i))
                            return false
                    } else if (col[4] == 'N')
                    {
                        if (!numValidate(valueId, selectedColumnLabel + " (Number) ", "any", true))
                            return false
                    } else if (col[4] == 'E')
                    {
                        if (!patternValidate(valueId, selectedColumnLabel + " (Email Id)", "EMAIL"))
                            return false
                    }
                    // VTE-CR SlOl 28. 3. 2014 8:39:20
                    if (sel_fields[selectedColumn]) {
                        var selectElement = jQuery('#s_fval'+columnIndex);
                        specifiedValue = selectElement.val();

                    }
                }
            }
            //Added to handle yes or no for checkbox fields in reports advance filters.
            if (col[4] == "C") {
                if (specifiedValue == "1")
                    specifiedValue = getObj(valueId).value = 'yes';
                else if (specifiedValue == "0")
                    specifiedValue = getObj(valueId).value = 'no';
            }
            if (extValueObject && extendedValue != null && extendedValue != '')
                specifiedValue = specifiedValue + ',' + extendedValue;

            criteriaConditions[columnIndex] = {"groupid": columnGroupId,
                "columnname": selectedColumn,
                "comparator": comparatorValue,
                "value": specifiedValue,
                "column_condition": glueCondition
            };
        }
    }
    jQuery('#advft_criteria').value = JSON.stringify(criteriaConditions);

    var conditionGroups = vt_getElementsByName('div', "conditionGroup");
    var criteriaGroups = [];
    for (var i = 0; i < conditionGroups.length; i++)
    {
        var groupTableId = conditionGroups[i].getAttribute("id");
        var groupTableInfo = groupTableId.split("_");
        var groupIndex = groupTableInfo[1];

        var groupConditionId = "gpcon" + groupIndex;
        var groupConditionObject = getObj(groupConditionId);
        var groupCondition = '';
        if (groupConditionObject) {
            groupCondition = groupConditionObject.value;
        }
        criteriaGroups[groupIndex] = {"groupcondition": groupCondition};
    }
    jQuery('#advft_criteria_groups').value = JSON.stringify(criteriaGroups);

    // groupconditioncolumn start
    var GroupconditionColumns = vt_getElementsByName('tr', "groupconditionColumn");
    var GroupcriteriaConditions = [];
    // VTE-CR SlOl 26. 3. 2014 13:26:01 SELECTBOX VALUES INTO FILTERS
    for (var i = 0; i < GroupconditionColumns.length; i++) {

        var columnRowId = GroupconditionColumns[i].getAttribute("id");
        var columnRowInfo = columnRowId.split("_");
        var columnGroupId = columnRowInfo[1];
        var columnIndex = columnRowInfo[2];

        if (columnGroupId != "0")
            ctype = "f";
        else
            ctype = "g";

        var columnId = ctype + "groupcol" + columnIndex;
        var columnObject = getObj(columnId);
        var selectedColumn = columnObject.value;
        var selectedColumnIndex = columnObject.selectedIndex;
        var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;
        if (columnObject.options[selectedColumnIndex].value != "none") {
            var comparatorId = ctype + "groupop" + columnIndex;
            var comparatorObject = getObj(comparatorId);
            var comparatorValue = comparatorObject.value;
            var valueId = ctype + "groupval" + columnIndex;
            var valueObject = getObj(valueId);
            var specifiedValue = valueObject.value;

            var glueConditionId = ctype + "groupcon" + columnIndex;
            var glueConditionObject = getObj(glueConditionId);
            var glueCondition = '';
            if (glueConditionObject) {
                glueCondition = glueConditionObject.value;
            }

            if(GroupconditionColumns.length>1){
                if (!emptyCheckVte(columnId, " Column ", "text")){
                    // i < GroupconditionColumns.length
                    return false;
                }
                if (!emptyCheckVte(comparatorId, selectedColumnLabel + " Option", "text")){
                    return false;
                }
            }

            var col = selectedColumn.split(":");
            if (escapedOptions.indexOf(col[3]) == -1) {
                if (col[4] == 'T') {
                    var datime = specifiedValue.split(" ");
                    if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH"))
                        return false;
                    if (datime.length > 1)
                        if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                            return false
                }
                else if (col[4] == 'D')
                {
                    if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                        return false;
                    if (extValueObject) {
                        if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                            return false
                    }
                } else if (col[4] == 'I')
                {
                    if (!intValidate(valueId, selectedColumnLabel + " (Integer Criteria)" + i))
                        return false
                } else if (col[4] == 'N')
                {
                    if (!numValidate(valueId, selectedColumnLabel + " (Number) ", "any", true))
                        return false
                } else if (col[4] == 'E')
                {
                    if (!patternValidate(valueId, selectedColumnLabel + " (Email Id)", "EMAIL"))
                        return false
                }
            }
            //Added to handle yes or no for checkbox fields in reports advance filters.
            if (col[4] == "C") {
                if (specifiedValue == "1")
                    specifiedValue = getObj(valueId).value = 'yes';
                else if (specifiedValue == "0")
                    specifiedValue = getObj(valueId).value = 'no';
            }
            if (extValueObject && extendedValue != null && extendedValue != '')
                specifiedValue = specifiedValue + ',' + extendedValue;

            GroupcriteriaConditions[columnIndex] = {"groupid": columnGroupId,
                "columnname": selectedColumn,
                "comparator": comparatorValue,
                "value": specifiedValue,
                "column_condition": glueCondition
            };
        }
    }
    jQuery('#groupft_criteria').value = JSON.stringify(GroupcriteriaConditions);
    formSelectedColumnString();
    formSelectedSummariesString();
    // formSelectColumnString();
    getCurl();
    //getQFurl();
    getLabelsurl();
    // formSelectQFColumnString();
    return true;
    // document.NewReport.submit();
}
//VTE-CR SlOl 26. 8. 2013 11:51:24
function formSelectedSummariesString()
{
    var selectedSumStr = "";
    jQuery('tr.drag_to_sort_order').each(function(){
        selectedSumStr += jQuery(this).data('id') + ";";
    });
    if(document.getElementById("selectedSummariesString") !== null) document.getElementById("selectedSummariesString").value = selectedSumStr;
}
function formSelectedColumnString()
{
    var selectedColStr = "";
    jQuery('tr.drag_to_sort_order').each(function(){
        selectedColStr += jQuery(this).data('id') + ";";
    });
    if(document.getElementById("selectedColumnsString") !== null) document.getElementById("selectedColumnsString").value = selectedColStr;
}
function nextStepVte() {
    var go_to_step = actual_step + 1;
    if (go_to_step == 2) {
        go_to_step = go_to_step + 2;
    }
    var actual_step = go_to_step;
    if (go_to_step != 12) {
        changeSteps4U(go_to_step);
    } else {
        changeSteps();
    }
}
function setSTDFilter(selectedStdFilter) {
    var newurl2 = 'action=VTEReportsAjax&mode=ajax&file=ChangeSteps&module=VTEReports';
    newurl2 += '&step=getStdFilter';
    if ((selectedStdFilter) && selectedStdFilter != "") {
        newurl2 += '&selectedStdFilter=' + selectedStdFilter;
    }
    if (document.getElementById('record'))
        var record = document.getElementById('record').value;
    else
        var record = '';
    newurl2 += '&record=' + record;

    if (document.getElementById('primarymodule').type == "text") {
        var selectedPrimaryModule = document.getElementById('primarymodule').value;
    } else if (document.getElementById('primarymodule') != "" && document.getElementById('primarymodule').type != "hidden") {
        var selectedPrimaryIndex = document.getElementById('primarymodule').selectedIndex;
        var selectedPrimaryModule = document.getElementById('primarymodule').options[selectedPrimaryIndex].value;
    } else {
        selectedPrimaryModule = "";
    }
    newurl2 += '&reportmodule=' + selectedPrimaryModule;
    newurl2 += '&primarymodule=' + selectedPrimaryModule;
    // remove old values
    if (typeof document.NewReport.stdDateFilterField != 'undefined' && document.NewReport.stdDateFilterField != "") {
        var selObj = document.NewReport.stdDateFilterField;
        var nMaxVal = selObj.length;
        for (nLoop = 0; nLoop < nMaxVal; nLoop++)
        {
            selObj.remove(0);
        }
        selObj.options[0] = new Option('None', '');
        selObj.options[0].selected = true;
    }
    // remove end
    if (document.getElementById("current_action")) {
        var c_action = document.getElementById("current_action").value;
    } else {
        var c_action = "";
    }
    if (c_action != "" && c_action == "resultGenerate") {
        var jqxhr = jQuery.post('index.php?' + newurl2, function(response) {
            // alert( "success" );
        })
            .done(function(response) {
                var option_values = response.split("#@!@#");
                var responseVal = JSON.parse(option_values[0]);
                var selected_option = option_values[1];
                for (var widgetId in responseVal)
                {
                    if (responseVal.hasOwnProperty(widgetId))
                    {
                        option = responseVal[widgetId];
                        var oOption = document.createElement("OPTION");
                        oOption.value = option["value"];
                        if (option["value"] == selected_option) {
                            oOption.selected = true;
                        }
                        oOption.appendChild(document.createTextNode(option["text"]));
                        document.NewReport.stdDateFilterField.appendChild(oOption);
                    }
                }
            })
    } else {
        new Ajax.Request('index.php',
            {queue: {position: 'end', scope: 'command'},
                method: 'post',
                postBody: newurl2,
                onComplete: function(response) {
                    var option_values = response.responseText.split("#@!@#");
                    var responseVal = JSON.parse(option_values[0]);
                    var selected_option = option_values[1];
                    for (var widgetId in responseVal)
                    {
                        if (responseVal.hasOwnProperty(widgetId))
                        {
                            option = responseVal[widgetId];
                            var oOption = document.createElement("OPTION");
                            oOption.value = option["value"];
                            if (option["value"] == selected_option) {
                                oOption.selected = true;
                            }
                            oOption.appendChild(document.createTextNode(option["text"]));
                            document.NewReport.stdDateFilterField.appendChild(oOption);
                        }
                    }

                }
            }
        );
    }
}

// VTE-CR SlOl 27. 2. 2014 9:52:47
function getQFurl() {
    var c = [];
    var qfurl = "";
    c = document.getElementsByTagName('input');
    for (var i = 0; i < c.length; i++)
    {
        if (c[i].type == 'checkbox')
        {
            Control_Data = c[i].name.split(':');
            if (Control_Data[0] == "qf" && c[i].checked == true)
            {
                if (qfurl != "") {
                    qfurl += "$_@_$";
                }
                qfurl += c[i].name;
            }
        }
    }
    document.getElementById('qf_to_go').value = qfurl;
    return qfurl;
}
// VTE-CR SlOl 27. 2. 2014 9:56:38
function getCurl() {
    var c = [];
    var curl = "";
    c = document.getElementsByTagName('input');
    for (var i = 0; i < c.length; i++)
    {
        if (c[i].type == 'checkbox')
        {
            Control_Data = c[i].name.split(':');
            if (Control_Data[0] == "cb" && c[i].checked == true)
            {
                if (curl != "") {
                    curl += "$_@_$";
                }
                curl += c[i].name;
            }
        }
    }
    document.getElementById('curl_to_go').value = curl;
    return curl;
}
function getLabelsurl() {
    var c = [];
    var lblurl = "";
    c = document.getElementsByTagName('input');
    for (var i = 0; i < c.length; i++)
    {
        if (c[i]) {
            var id_text = c[i].id;
            var search_for_lc = "_lLbLl_";
            if (id_text.indexOf(search_for_lc) > -1 && id_text.indexOf("hidden_") == -1) {
                if (lblurl != "") {
                    lblurl += "$_@_$";
                }
                var str_lblurl = c[i].id + "_lLGbGLl_" + c[i].value;
                str_lblurl = replaceAll("&", "@AMPKO@", str_lblurl);
                lblurl += str_lblurl;
            }
        }
    }
    document.getElementById('labels_to_go').value = lblurl;

    return lblurl;
}
// VTE-END 12. 3. 2014 14:12:17

function setRelModules(relMod) {
    var relModArr = document.NewReport.relatedmodules.value.split(':');
    var retstring = Array();
    if (!inMyArray(relModArr, relMod)) {
        if (relModArr == '') {
            relModArr[0] = relMod;
        } else {
            relModArr.push(relMod);
        }
        retstring = relModArr;
    } else {
        for (i = 0; i < relModArr.length; i++) {
            if (relModArr[i] != relMod) {
                retstring.push(relModArr[i]);
            }
        }

    }
    document.NewReport.relatedmodules.value = retstring.join(':');
    return false;
}

function inMyArray(myarray, myvalue)
{
    var i;
    for (i = 0; i < myarray.length; i++) {
        if (myarray[i] == myvalue) {
            return true;
        }
    }
    return false;
}

function changeSteps1()
{
    if (getObj('step4').style.display != 'none')
    {
        var escapedOptions = ['account_id', 'contactid', 'contact_id', 'product_id', 'parent_id', 'campaignid', 'potential_id', 'assigned_user_id1', 'quote_id', 'accountname', 'salesorder_id', 'vendor_id', 'time_start', 'time_end', 'lastname'];

        var conditionColumns = vt_getElementsByName('tr', "conditionColumn");
        var criteriaConditions = [];
        for (var i = 0; i < conditionColumns.length; i++) {

            var columnRowId = conditionColumns[i].getAttribute("id");
            var columnRowInfo = columnRowId.split("_");
            var columnGroupId = columnRowInfo[1];
            var columnIndex = columnRowInfo[2];

            var columnId = "fcol" + columnIndex;
            var columnObject = getObj(columnId);
            var selectedColumn = trim(columnObject.value);
            var selectedColumnIndex = columnObject.selectedIndex;
            var selectedColumnLabel = columnObject.options[selectedColumnIndex].text;

            var comparatorId = "fop" + columnIndex;
            var comparatorObject = getObj(comparatorId);
            var comparatorValue = trim(comparatorObject.value);

            var valueId = "fval" + columnIndex;
            var valueObject = getObj(valueId);
            var specifiedValue = trim(valueObject.value);

            var extValueId = "fval_ext" + columnIndex;
            var extValueObject = getObj(extValueId);
            if (extValueObject) {
                extendedValue = trim(extValueObject.value);
            }

            var glueConditionId = "fcon" + columnIndex;
            var glueConditionObject = getObj(glueConditionId);
            var glueCondition = '';
            if (glueConditionObject) {
                glueCondition = trim(glueConditionObject.value);
            }

            if (!emptyCheckVte(columnId, " Column ", "text"))
                return false;
            if (!emptyCheckVte(comparatorId, selectedColumnLabel + " Option", "text"))
                return false;

            var col = selectedColumn.split(":");
            if (escapedOptions.indexOf(col[3]) == -1) {
                if (col[4] == 'T') {
                    var datime = specifiedValue.split(" ");
                    if (!re_dateValidate(datime[0], selectedColumnLabel + " (Current User Date Time Format)", "OTH"))
                        return false;
                    if (datime.length > 1)
                        if (!re_patternValidate(datime[1], selectedColumnLabel + " (Time)", "TIMESECONDS"))
                            return false
                }
                else if (col[4] == 'D')
                {
                    if (!dateValidate(valueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                        return false;
                    if (extValueObject) {
                        if (!dateValidate(extValueId, selectedColumnLabel + " (Current User Date Format)", "OTH"))
                            return false
                    }
                } else if (col[4] == 'I')
                {
                    if (!intValidate(valueId, selectedColumnLabel + " (Integer Criteria)" + i))
                        return false
                } else if (col[4] == 'N')
                {
                    if (!numValidate(valueId, selectedColumnLabel + " (Number) ", "any", true))
                        return false
                } else if (col[4] == 'E')
                {
                    if (!patternValidate(valueId, selectedColumnLabel + " (Email Id)", "EMAIL"))
                        return false
                }
            }

            //Added to handle yes or no for checkbox fields in reports advance filters.
            if (col[4] == "C") {
                if (specifiedValue == "1")
                    specifiedValue = getObj(valueId).value = 'yes';
                else if (specifiedValue == "0")
                    specifiedValue = getObj(valueId).value = 'no';
            }
            if (extValueObject && extendedValue != null && extendedValue != '')
                specifiedValue = specifiedValue + ',' + extendedValue;

            criteriaConditions[columnIndex] = {"groupid": columnGroupId,
                "columnname": selectedColumn,
                "comparator": comparatorValue,
                "value": specifiedValue,
                "column_condition": glueCondition
            };
        }

        $('advft_criteria').value = JSON.stringify(criteriaConditions);

        var conditionGroups = vt_getElementsByName('div', "conditionGroup");
        var criteriaGroups = [];
        for (var i = 0; i < conditionGroups.length; i++) {
            var groupTableId = conditionGroups[i].getAttribute("id");
            var groupTableInfo = groupTableId.split("_");
            var groupIndex = groupTableInfo[1];

            var groupConditionId = "gpcon" + groupIndex;
            var groupConditionObject = getObj(groupConditionId);
            var groupCondition = '';
            if (groupConditionObject) {
                groupCondition = trim(groupConditionObject.value);
            }
            criteriaGroups[groupIndex] = {"groupcondition": groupCondition};

        }
        $('advft_criteria_groups').value = JSON.stringify(criteriaGroups);

        var date1 = getObj("startdate");
        var date2 = getObj("enddate");

        //# validation added for date field validation in final step of report creation
        if ((date1.value != '') || (date2.value != ''))
        {

            if (!dateValidate("startdate", "Start Date", "D"))
                return false;

            if (!dateValidate("enddate", "End Date", "D"))
                return false;

            if (!dateComparison("startdate", 'Start Date', "enddate", 'End Date', 'LE'))
                return false;
        }

    }
    if (getObj('step12').style.display != 'none') {
        saveAndRunReport();
    } else {
        for (i = 0; i < divarray.length; i++) {
            if (getObj(divarray[i]).style.display != 'none') {
                if (i == 1 && selectedColumnsObj.options.length == 0) {
                    deleteColumnRow(1, 0);
                }
                if (divarray[i] == 'step4') {
                    document.getElementById("next").value = finish_text;
                }
                hide(divarray[i]);
                show(divarray[i + 1]);
                tableid = divarray[i] + 'label';
                newtableid = divarray[i + 1] + 'label';
                getObj(tableid).className = 'dvtUnSelectedCell';
                getObj(newtableid).className = 'dvtSelectedCell';
                break;
            }
        }
    }
}
function changeStepsback1()
{
    if (getObj('step1').style.display != 'none')
    {
        document.NewReport.action.value = 'NewReport0';
        // VTE-END SlOl
        document.NewReport.submit();
    } else
    {
        for (i = 0; i < divarray.length; i++)
        {
            if (getObj(divarray[i]).style.display != 'none')
            {
                document.getElementById("next").value = next_text + '>';
                hide(divarray[i]);
                show(divarray[i - 1]);
                tableid = divarray[i] + 'label';
                newtableid = divarray[i - 1] + 'label';
                getObj(tableid).className = 'settingsTabList';
                getObj(newtableid).className = 'settingsTabSelected';
                break;
            }

        }
    }
}
function changeSteps(savetype)
{
    if (!savetype || savetype == "") {
        savetype = "Save&Run";
    }
    var report_name_val = document.getElementById('reportname').value;

    if (report_name_val == "")
    {
        params = {
            title: app.vtranslate('MISSING_REPORT_NAME'),
            text: data['message']
        };
        Vtiger_Helper_Js.showPnotify(params);
        jQuery("#reportname").focus();
        return false;
    }
    else
    {
        // VTE-CR SlOl 9. 9. 2013 13:36:29 scheduler
        var isScheduledObj = getObj("isReportScheduled");
        if (isScheduledObj.checked == true) {
            var selectedRecipientsObj = getObj("selectedRecipients");

            if (selectedRecipientsObj.options.length == 0) {
                var params = {
                    title: app.vtranslate('RECIPIENTS_CANNOT_BE_EMPTY'),
                    type: 'error'
                };
                Vtiger_Helper_Js.showPnotify(params);
                return false;
            }

            var selectedUsers = [];
            var selectedGroups = [];
            var selectedRoles = [];
            var selectedRolesAndSub = [];
            for (i = 0; i < selectedRecipientsObj.options.length; i++) {
                var selectedCol = selectedRecipientsObj.options[i].value;
                var selectedColArr = selectedCol.split("::");
                if (selectedColArr[0] == "users")
                    selectedUsers.push(selectedColArr[1]);
                else if (selectedColArr[0] == "groups")
                    selectedGroups.push(selectedColArr[1]);
                else if (selectedColArr[0] == "roles")
                    selectedRoles.push(selectedColArr[1]);
                else if (selectedColArr[0] == "rs")
                    selectedRolesAndSub.push(selectedColArr[1]);
            }

            var selectedRecipients = {users: selectedUsers, groups: selectedGroups,
                roles: selectedRoles, rs: selectedRolesAndSub};
            var selectedRecipientsJson = JSON.stringify(selectedRecipients);
            document.NewReport.selectedRecipientsString.value = selectedRecipientsJson;

            var scheduledInterval = {scheduletype: document.NewReport.scheduledType.value,
                month: document.NewReport.scheduledMonth.value,
                date: document.NewReport.scheduledDOM.value,
                day: document.NewReport.scheduledDOW.value,
                time: document.NewReport.scheduledTime.value
            };

            var scheduledIntervalJson = JSON.stringify(scheduledInterval);
            document.NewReport.scheduledIntervalString.value = scheduledIntervalJson;

            var curl = "";
            c = document.getElementsByTagName('input');
            for (var i = 0; i < c.length; i++)
            {
                if (c[i].type == 'checkbox')
                {
                    Control_Data = c[i].name.split(':');
                    if (Control_Data[0] == "cb" && c[i].checked == true)
                    {
                        if (curl != "") {
                            curl += "$_@_$";
                        }
                        curl += c[i].name;
                    }
                }
            }
            document.NewReport.curl_to_go.value = curl;
        }
        getUpSelectedSharing();
        if (savetype != "") {
            document.getElementById("SaveType").value = savetype;
        }
        if (saveAndRunReport()) {
            document.NewReport.submit();
        }
    }
}
function changeStepsback()
{
    if (actual_step != 1)
    {
        var last_step = actual_step - 1;
        // VTE SlOl step 2 and step 3 disabled -2 steps
        if (last_step == 3) {
            last_step = last_step - 2;
        }
        /* REPORT TYPE REMOVED
         if (last_step == 4)
         {
         // var objreportType = document.forms.NewReport['reportType'];
         var objreportType = document.getElementsByName('reportType');
         if(objreportType[0].checked == true) objreportType = objreportType[0];
         else if(objreportType[1].checked == true) objreportType = objreportType[1];
         else if(objreportType[2].checked == true) objreportType = objreportType[2];
         else if(objreportType[3].checked == true) objreportType = objreportType[3];
         if (objreportType.value != 'summary' && objreportType.value != 'grouping') last_step--;
         }
         */
        changeSteps4U(last_step);

        if (last_step == 1)
        {
            document.getElementById('back_rep_top').disabled = true;
        }
    }
}
function editReport(id)
{
    var arg = 'index.php?module=VTEReports&action=NewReport&record=' + id + '&mode=edit';
    document.location.href = arg;
}
function CreateReport(module)
{
    var arg = 'index.php?module=VTEReports&action=NewReport&folder=' + gcurrepfolderid + '&reportmodule=' + module + '&primarymodule=' + module + '&mode=create';
    document.location.href = arg;
}
function fnPopupWin(winName) {
    window.open(winName, "ReportWindow", "width=790px,height=630px,scrollbars=yes");
}
function re_dateValidate(fldval, fldLabel, type) {
    if (re_patternValidate(fldval, fldLabel, "DATE") == false)
        return false;
    var dateval = fldval.replace(/^\s+/g, '').replace(/\s+$/g, '');

    var dateelements = splitDateVal(dateval);

    var dd = dateelements[0];
    var mm = dateelements[1];
    var yyyy = dateelements[2];

    if (dd < 1 || dd > 31 || mm < 1 || mm > 12 || yyyy < 1 || yyyy < 1000) {
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }

    if ((mm == 2) && (dd > 29)) {//checking of no. of days in february month
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }

    if ((mm == 2) && (dd > 28) && ((yyyy % 4) != 0)) {//leap year checking
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }

    switch (parseInt(mm)) {
        case 2 :
        case 4 :
        case 6 :
        case 9 :
        case 11 :
            if (dd > 30) {
                alert(app.vtranslate('ENTER_VALID') + fldLabel);
                return false
            }
    }

    var currdate = new Date();
    var chkdate = new Date();

    chkdate.setYear(yyyy);
    chkdate.setMonth(mm - 1);
    chkdate.setDate(dd);

    if (type != "OTH") {
        if (!compareDates(chkdate, fldLabel, currdate, "current date", type)) {
            return false
        } else
            return true;
    } else
        return true;
}

//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function re_patternValidate(fldval, fldLabel, type) {
    if (type.toUpperCase() == "DATE") {//DATE validation

        switch (userDateFormat) {
            case "yyyy-mm-dd" :
                var re = /^\d{4}(-)\d{1,2}\1\d{1,2}$/;
                break;
            case "mm-dd-yyyy" :
            case "dd-mm-yyyy" :
                var re = /^\d{1,2}(-)\d{1,2}\1\d{4}$/
        }
    }


    if (type.toUpperCase() == "TIMESECONDS") {//TIME validation
        var re = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$");
    }
    if (!re.test(fldval)) {
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    } else
        return true
}

//added to fix the ticket #5117
function standardFilterDisplay()
{
    var stdDateFilterField = document.getElementById('stdDateFilterField');
    if (document.getElementById('stdDateFilterField')) {
        var stdDateFilterFieldIndex = stdDateFilterField.selectedIndex;
        var stdDateFilterFieldValue = stdDateFilterField.options[stdDateFilterFieldIndex].value;

        if (stdDateFilterField.options.length <= 0 || (stdDateFilterField.selectedIndex > -1 && stdDateFilterField.options[stdDateFilterField.selectedIndex].value == "Not Accessible")) {
            getObj('stdDateFilter').disabled = true;
            getObj('startdate').disabled = true;
            getObj('enddate').disabled = true;
            getObj('jscal_trigger_date_start').style.visibility = "hidden";
            getObj('jscal_trigger_date_end').style.visibility = "hidden";
        } else {
            getObj('stdDateFilter').disabled = false;
            getObj('startdate').disabled = false;
            getObj('enddate').disabled = false;
            getObj('jscal_trigger_date_start').style.visibility = "visible";
            getObj('jscal_trigger_date_end').style.visibility = "visible";
        }
    }
}

function updateRelFieldOptions(sel, opSelName) {
    jQuery(document).ready(function() {
        var selObj = document.getElementById(opSelName);
        if (selObj) {
            var fieldtype = null;
            var currOption = selObj.options[selObj.selectedIndex];
            var currField = sel.options[sel.selectedIndex];
            // VTE-CR SlOl 26. 3. 2014 13:26:01 SELECTBOX VALUES INTO FILTERS
            var sel_fields = JSON.parse(document.getElementById("sel_fields").value);
            var opSelName_array = opSelName.split("val_");
            var row_i = opSelName_array[1];

            var filter_criteria_obj = document.getElementById("fop" + row_i);
            if (sel_fields[currField.value] && (filter_criteria_obj.options[filter_criteria_obj.selectedIndex].value=="e" || filter_criteria_obj.options[filter_criteria_obj.selectedIndex].value=="n")) {

                var r_sel_fields = document.getElementById("fval" + row_i).value;

                if (document.getElementById("current_action")) {
                    var c_action = document.getElementById("current_action").value;
                } else {
                    var c_action = "";
                }

                var postData = {
                    "module": "VTEReports",
                    "action": 'IndexAjax',
                    "mode": "getFilterColHtml",
                    "sel_fields": JSON.stringify(sel_fields),
                    "sfield_name": "fval" + row_i,
                    "record": getReportRecordID(),
                    "currField": currField.value
                };

                if (typeof r_sel_fields != 'undefined' && r_sel_fields != "") {
                    postData["r_sel_fields"] = r_sel_fields;
                }

                var actionParams = {
                    "type": "POST",
                    "url": 'index.php',
                    "dataType": "html",
                    "data": postData
                };
                app.request.post(actionParams).then(
                    function(err,data){
                        if(err === null) {
                            var container = jQuery("#node3span" + row_i + "_ajx");
                            container.html(data);
                            container.css("display", "block");
                            jQuery("#node3span" + row_i + "_st").css("display", "none");
                            jQuery("#node3span" + row_i + "_djx").css("display", "none");
                            vtUtils.applyFieldElementsView(container.find('select.select2'));
                            vtUtils.applyFieldElementsView(container);

                            container.find('input.select2-input').attr('style', 'width: 100% !important;margin-left: 10px;margin-top: -1px;');
                        }else{
                            // to do
                        }
                    }
                );

            } else {
                document.getElementById("node3span" + row_i + "_ajx").innerHTML = "";
                jQuery("#node3span" + row_i + "_st").css("display", "block");
                jQuery("#node3span" + row_i + "_ajx").css("display", "none");
                jQuery("#node3span" + row_i + "_djx").css("display", "none");

                if(filter_criteria_obj.options[filter_criteria_obj.selectedIndex].value=="isn" || filter_criteria_obj.options[filter_criteria_obj.selectedIndex].value=="isnn"){
                    jQuery("#fval"+row_i).val("");
                }

                //document.getElementById("node3span" + row_i + "_st").style.display = "block";
                //document.getElementById("node3span" + row_i + "_ajx").style.display = "none";
                //document.getElementById("node3span" + row_i + "_djx").style.display = "none";
                // VTE-END 26. 3. 2014 13:25:55
                if (currField.value != null && currField.value.length != 0) {
                    fieldtype = trimfValues(currField.value);
                    ops = rel_fields[fieldtype];
                    var off = 0;
                    if (ops != null) {
                        var nMaxVal = selObj.length;
                        for (nLoop = 0; nLoop < nMaxVal; nLoop++) {
                            selObj.remove(0);
                        }
                        selObj.options[0] = new Option('None', '');
                        if (currField.value == '') {
                            selObj.options[0].selected = true;
                        }
                        off = 1;
                        for (var i = 0; i < ops.length; i++) {
                            var field_array = ops[i].split("::");
                            var label = field_array[1];
                            var field = field_array[0];
                            if (label == null)
                                continue;
                            var option = new Option(label, field);
                            selObj.options[i + off] = option;
                            if (currOption != null && currOption.value == option.value) {
                                option.selected = true;
                            }
                        }
                    }
                } else {
                    var nMaxVal = selObj.length;
                    for (nLoop = 0; nLoop < nMaxVal; nLoop++) {
                        selObj.remove(0);
                    }
                    selObj.options[0] = new Option('None', '');
                    if (currField.value == '') {
                        selObj.options[0].selected = true;
                    }
                }
            }
        }

        // std_filter cheching

        // var std_filter_columns = document.getElementById("std_filter_columns").value;
        var std_filter_columns = jQuery("#std_filter_columns").val();

        if (typeof std_filter_columns != 'undefined' && std_filter_columns != "") {
            var std_filter_columns_arr = std_filter_columns.split('<%jsstdjs%>');

            if (std_filter_columns_arr.indexOf(sel.value) > -1) {
                /*var std_filter_criteria_obj = document.getElementById("fop" + row_i);
                 var std_filter_criteria_arr = JSON.parse(std_filter_criteria);
                 var nSFCVal = std_filter_criteria_obj.length;
                 for (nLoop = 0; nLoop < nSFCVal; nLoop++)
                 {
                 std_filter_criteria_obj.remove(0);
                 }
                 std_filter_criteria_obj.options[0] = new Option('None', '');
                 var sfc_i = 1;
                 for (var filter_opt in std_filter_criteria_arr)
                 {
                 std_filter_criteria_obj.options[sfc_i] = new Option(std_filter_criteria_arr[filter_opt], filter_opt);
                 sfc_i++;
                 }*/
                var r_sel_fields = document.getElementById("fval" + row_i).value;
//                document.getElementById("fval" + row_i).value = "";
                getFilterDateHtml(row_i, r_sel_fields);
            } else {
                document.getElementById("node3span" + row_i + "_djx").innerHTML = "";
                if (sel_fields[currField.value] && (filter_criteria_obj.options[filter_criteria_obj.selectedIndex].value=="e" || filter_criteria_obj.options[filter_criteria_obj.selectedIndex].value=="n")) {
                    document.getElementById("node3span" + row_i + "_ajx").style.display = "block";
                    document.getElementById("node3span" + row_i + "_st").style.display = "none";
                } else {
                    document.getElementById("node3span" + row_i + "_ajx").style.display = "none";
                    document.getElementById("node3span" + row_i + "_st").style.display = "block";
                }
                // document.getElementById("fval" + row_i).value = "";
                document.getElementById("node3span" + row_i + "_djx").style.display = "none";
            }
        }

    });
}

function getFilterDateHtml(row_i, r_sel_fields) {
    var node3span_obj = document.getElementById("node3span" + row_i + "_st");

    var reportID = getReportRecordID();

    var postData = {
        "mode": "getFilterDateHtml",
        "columnIndex": row_i,
        "record": getReportRecordID()
    };

    if (typeof r_sel_fields != 'undefined' && r_sel_fields != "") {
        postData["r_sel_fields"] = r_sel_fields;
    }
    if (typeof document.getElementById("fop" + row_i) != 'undefined') {
        var fop_type_val = document.getElementById("fop" + row_i).value;
        postData["fop_type"] = fop_type_val;
    }

    var actionParams = {
        "type": "POST",
        "url": 'index.php?module=VTEReports&action=IndexAjax',
        "dataType": "html",
        "data": postData
    };
    app.request.post(actionParams).then(
        function(err,data){
            if(err === null) {
                document.getElementById("node3span" + row_i + "_djx").innerHTML = data;
                document.getElementById("node3span" + row_i + "_st").style.display = "none";
                document.getElementById("node3span" + row_i + "_ajx").style.display = "none";
                document.getElementById("fval" + row_i).value = "";
                document.getElementById("node3span" + row_i + "_djx").style.display = "block";
                if(fop_type_val=="custom"){
                    var opObj = document.getElementById('fop' + row_i);
                    var currOp = opObj.options[opObj.selectedIndex];
                    var seOption_type = currOp.value;
                    var s_obj = document.getElementById("jscal_field_sdate" + row_i);
                    var e_obj = document.getElementById("jscal_field_edate" + row_i);
                    var st_obj = document.getElementById("jscal_trigger_sdate" + row_i);
                    var et_obj = document.getElementById("jscal_trigger_edate" + row_i);
                    showDateRange(s_obj, e_obj, st_obj, et_obj, seOption_type);
                    //var s_obj_date = jQuery("#jscal_field_sdate0");
                    //var e_obj_date = jQuery("#jscal_field_edate0");
                    //console.log(s_obj_date);
                    //vtUtils.applyFieldElementsView(s_obj_date);
                    //vtUtils.applyFieldElementsView(e_obj_date);
                    var s_obj_date = jQuery("#jscal_field_sdate_val_" + row_i );
                    var e_obj_date = jQuery("#jscal_field_edate_val_" + row_i);
                    var userDateFormat = app.getDateFormat();
                    var elementDateFormat = s_obj_date.data('dateFormat');
                    if(typeof elementDateFormat !== 'undefined') {
                        userDateFormat = elementDateFormat;
                    }
                    var defaultPickerParams = {
                        autoclose: true,
                        todayBtn: "linked",
                        format: userDateFormat,
                        todayHighlight: true,
                        clearBtn : true
                    };
                    s_obj_date.datepicker(defaultPickerParams);
                    e_obj_date.datepicker(defaultPickerParams);
                    s_obj_date.show();
                    e_obj_date.show();
                    jQuery("#jscal_trigger_sdate" + row_i).removeClass("hide");
                    jQuery("#jscal_trigger_edate" + row_i).removeClass("hide");
                }
            }else{
                // to do
            }
        }
    );
}

function AddFieldToFilter(id, sel) {
    if (trim(document.getElementById("fval" + id).value) == '') {
        document.getElementById("fval" + id).value = document.getElementById("fval_" + id).value;
    } else {
        document.getElementById("fval" + id).value = document.getElementById("fval" + id).value + "," + document.getElementById("fval_" + id).value;
    }
}
function fnLoadRepValues(tab1, tab2, block1, block2) {
    document.getElementById(block1).style.display = 'block';
    document.getElementById(block2).style.display = 'none';
    document.getElementById(tab1).className = 'dvtSelectedCell';
    document.getElementById(tab2).className = 'dvtUnSelectedCell';
}

/**
 * IE has a bug where document.getElementsByName doesnt include result of dynamically created
 * elements
 */
function vt_getElementsByName(tagName, elementName) {
    var inputs = document.getElementsByTagName(tagName);
    var selectedElements = [];
    for (var i = 0; i < inputs.length; i++) {
        if (inputs.item(i).getAttribute('name') == elementName) {
            selectedElements.push(inputs.item(i));
        }
    }
    return selectedElements;
}

function formSelectQFColumnString()
{
    var selectedColStr = "";
    var selectedQFColumnsObj = getObj("selectedQFColumns");

    for (i = 0; i < selectedQFColumnsObj.options.length; i++)
    {
        selectedColStr += selectedQFColumnsObj.options[i].value + ";";
    }
    document.NewReport.selectedQFColumnsString.value = selectedColStr;
}

function uncheckAll(el)
{
    var selObj = document.getElementById(el);

    for (var i = 0; i < selObj.length; i++)
        selObj.options[i].selected = false;

}
function checkAll(el)
{
    selObj = document.getElementById(el);

    for (var i = 0; i < selObj.length; i++)
        selObj.options[i].selected = true;

}

function CreateCustomReport(reporttype)
{
    var arg = 'index.php?module=VTEReports&action=NewCustomReport&folder=' + gcurrepfolderid + '&reporttype=' + reporttype + '&mode=create';
    document.location.href = arg;
}

function editCustomReport(id, reporttype)
{
    var arg = 'index.php?module=VTEReports&action=NewCustomReport&record=' + id + '&reporttype=' + reporttype + '&mode=edit';
    document.location.href = arg;
}

function setRelModules(relMod) {
    var relModArr = document.NewReport.relatedmodules.value.split(':');
    var retstring = Array();
    if (!inMyArray(relModArr, relMod)) {
        if (relModArr == '') {
            relModArr[0] = relMod;
        } else {
            relModArr.push(relMod);
        }
        retstring = relModArr;
    } else {
        for (i = 0; i < relModArr.length; i++) {
            if (relModArr[i] != relMod) {
                retstring.push(relModArr[i]);
            }
        }
    }
    document.NewReport.relatedmodules.value = retstring.join(':');
    return false;
}

function setScheduleOptions() {

    var stid = document.getElementById('scheduledType').value;
    switch (stid) {
        case "0": // nothing choosen
        case "1": // hourly
            document.getElementById('scheduledMonthSpan').style.display = 'none';
            document.getElementById('scheduledDOMSpan').style.display = 'none';
            document.getElementById('scheduledDOWSpan').style.display = 'none';
            document.getElementById('scheduledTimeSpan').style.display = 'inline';
            break;
        case "2": // daily
            document.getElementById('scheduledMonthSpan').style.display = 'none';
            document.getElementById('scheduledDOMSpan').style.display = 'none';
            document.getElementById('scheduledDOWSpan').style.display = 'none';
            document.getElementById('scheduledTimeSpan').style.display = 'inline';
            break;
        case "3": // weekly
        case "4": // bi-weekly
            document.getElementById('scheduledMonthSpan').style.display = 'none';
            document.getElementById('scheduledDOMSpan').style.display = 'none';
            document.getElementById('scheduledDOWSpan').style.display = 'inline';
            document.getElementById('scheduledTimeSpan').style.display = 'inline';
            break;
        case "5": // monthly
            document.getElementById('scheduledMonthSpan').style.display = 'none';
            document.getElementById('scheduledDOMSpan').style.display = 'inline';
            document.getElementById('scheduledDOWSpan').style.display = 'none';
            document.getElementById('scheduledTimeSpan').style.display = 'inline';
            break;
        case "6": // annually
            document.getElementById('scheduledMonthSpan').style.display = 'inline';
            document.getElementById('scheduledDOMSpan').style.display = 'inline';
            document.getElementById('scheduledDOWSpan').style.display = 'none';
            document.getElementById('scheduledTimeSpan').style.display = 'inline';
            break;
    }
}

function emptyCheckVte(fldName, fldLabel, fldType) {
    var currObj = getObj(fldName);
    if(typeof currObj == 'undefined') return false;
    if (fldType == "text") {
        if (currObj.value=="" || currObj.value.replace(/^\s+/g, '').replace(/\s+$/g, '').length == 0) {
            if(typeof jQuery("#s_"+fldName).val() != 'undefined' && jQuery("#s_"+fldName).val()!=""){
                return true;
            }
            var params = {
                title: app.vtranslate(fldLabel +app.vtranslate('VAL_CANNOT_BE_EMPTY')),
                type: 'error'
            };
            Vtiger_Helper_Js.showPnotify(params);
            try {
                currObj.focus()
            } catch (error) {
            }
            return false
        }
        else {
            return true
        }
    } else if ((fldType == "textarea")
        && (typeof (CKEDITOR) !== 'undefined' && CKEDITOR.intances[fldName] !== 'undefined')) {
        var textObj = CKEDITOR.intances[fldName];
        var textValue = textObj.getData();
        if (trim(textValue) == '' || trim(textValue) == '<br>') {
            alert(fldLabel + app.vtranslate('CANNOT_BE_NONE'));
            return false;
        } else {
            return true;
        }
    } else {
        if (trim(currObj.value) == '') {
            alert(fldLabel + app.vtranslate('CANNOT_BE_NONE'));
            return false
        } else
            return true
    }
}
function getFieldsOptionsSearch(search_input) {
    var search_for = search_input.value;
    var search_for_lc = search_for.toLowerCase();
    jQuery("div.div_column_child_off").each(function(){
        var this_value = jQuery(this).find('p').html().toLowerCase();
        if(this_value.indexOf(search_for_lc) !== -1){
            jQuery(this).show();
        }
        else{
            jQuery(this).hide();
        }
    });
}
function getSUMFieldsOptionsSearch(search_input) {
    var search_for = search_input.value;
    var search_for_lc = search_for.toLowerCase();
    var selectedPrimaryIndex = document.getElementById('primarymodule').selectedIndex;
    var selectedPrimaryModule = document.getElementById('primarymodule').options[selectedPrimaryIndex].value;

    aviable_fields = document.getElementById("availSumModValues").innerHTML;
    var mod_groups_a2 = aviable_fields.split("(!#_ID@ID_#!)");
    var module_groupid = mod_groups_a2[0];
    var module_name = mod_groups_a2[1];
    var aviable_fields = mod_groups_a2[2];

    var AvaiSelectedModules = document.getElementById("SummariesModules");
    var selectedModule = AvaiSelectedModules.options[AvaiSelectedModules.selectedIndex].value;
    var selectedModuleText = SummariesModules.options[SummariesModules.selectedIndex].text;
    if (module_groupid == selectedModule) {
        document.getElementById('availListSum').innerHTML = "";

        var optgroups = aviable_fields.split("(|@!@|)");
        for (i = 0; i < optgroups.length; i++)
        {

            var optgroup = optgroups[i].split("(|@|)");

            if (optgroup[0] != '')
            {
                var oOptgroup = document.createElement("OPTGROUP");
                oOptgroup.label = selectedModuleText + " - " + optgroup[0];

                var responseVal = JSON.parse(optgroup[1]);
                for (var widgetId in responseVal)
                {
                    if (responseVal.hasOwnProperty(widgetId))
                    {
                        var option = responseVal[widgetId];
                        var option_text = option["text"];
                        var option_text_lc = option_text.toLowerCase();
                        if (option_text_lc.indexOf(search_for_lc) > -1) {
                            var oOption = document.createElement("OPTION");
                            oOption.value = option["value"];
                            oOption.appendChild(document.createTextNode(option_text));
                            oOptgroup.appendChild(oOption);
                            if (i == 0) {
                                document.getElementById('availListSum').appendChild(oOption);
                            } else {
                                document.getElementById('availListSum').appendChild(oOptgroup);
                            }
                        }
                    }
                }
            }
        }
        return true;
    }
}
function getTLFieldsOptionsSearch(search_input) {
    var search_for = search_input.value;
    var search_for_lc = search_for.toLowerCase();

    var aviable_fields_json = document.getElementById("avail_timeline_columns").value;
    var aviable_fields = JSON.parse(aviable_fields_json);
    document.getElementById('timeline_columns').innerHTML = "";
    for (var widgetId in aviable_fields)
    {
        if (aviable_fields.hasOwnProperty(widgetId))
        {
            var oOptgroup = document.createElement("OPTGROUP");
            oOptgroup.label = widgetId;
            for (var avfId in aviable_fields[widgetId])
            {
                if (aviable_fields[widgetId].hasOwnProperty(avfId))
                {
                    var option_text = aviable_fields[widgetId][avfId]['text'];
                    var option_val = aviable_fields[widgetId][avfId]['value'];
                    var option_text_lc = option_text.toLowerCase();
                    if (option_text_lc.indexOf(search_for_lc) > -1) {
                        var oOption = document.createElement("OPTION");
                        oOption.value = option_val;
                        oOption.appendChild(document.createTextNode(option_text));
                        oOptgroup.appendChild(oOption);
                        if (widgetId == 0) {
                            document.getElementById('timeline_columns').appendChild(oOption);
                        } else {
                            document.getElementById('timeline_columns').appendChild(oOptgroup);
                        }
                    }
                }
            }
        }
    }
    return true;
}
function addOndblclick(vOption) {
    if ((vOption.selectedIndex) || vOption.selectedIndex == 0) {
        var selectedOption = vOption.options[vOption.selectedIndex].value;
        addColumn('selectedColumns');
    }
}
function addOndblclickSUM(vOption) {
    if ((vOption.selectedIndex) || vOption.selectedIndex == 0) {
        var selectedOption = vOption.options[vOption.selectedIndex].value;
        addColumn('selectedSummaries');
    }
}
function defineModuleFields(availModules) {

    setModuleFields(availModules, "getStep5Columns");
}

function defineSUMModuleFields(availModules) {

    setModuleFields(availModules, "getStep5SUMColumns");
}

function setModuleFields(availModules, mode) {

    if (mode == "getStep5SUMColumns") {
        var search_input_name = "search_input_sum";
        var field_name = "SummariesModules";
        var is_sum = "Sum";
    } else if (mode == "getStep5Columns") {
        var search_input_name = "search_input";
        var field_name = "availModValues";
        var is_sum = "";
    }

    if(availModules.selectedIndex < 0) {
        availModules.selectedIndex=0;
    }

    var selectedModule = availModules.options[availModules.selectedIndex].value;
    var field_options = document.getElementById(field_name).innerHTML;

    document.getElementById(search_input_name).value = "";

    var postData = {
        "selectedmodule": selectedModule,
        "mode": mode
    };

    var primarymodule = jQuery('#primarymodule').val();

    var actionParams = {
        "type": "POST",
        "url": 'index.php?module=VTEReports&action=IndexAjax&primarymoduleid='+primarymodule,
        "dataType": "html",
        "data": postData
    };
    app.request.post(actionParams).then(
        function(err,data){
            if(err === null) {
                setAvailableFields(data, is_sum);
            }else{
                // to do
            }
        }
    );
}

function setStep5Columns(step5_result) {

    var availablemodules = JSON.parse(step5_result[0]);
    var aviable_fields = step5_result[1];

    var avaimodules_sbox = document.getElementById('availModules');
    avaimodules_sbox.innerHTML = "";
    avaimodules_sbox.options.length = 0;
    for (var widgetId in availablemodules) {
        if (availablemodules.hasOwnProperty(widgetId)) {
            var option = availablemodules[widgetId];
            var oOption = document.createElement("OPTION");
            oOption.value = option["id"];
            if (option["checked"] == "checked") {
                oOption.checked = true;
                var option_name = option["name"];
            } else {
                var option_name = "- " + option["name"];
            }
            oOption.appendChild(document.createTextNode(option_name));
            avaimodules_sbox.appendChild(oOption);
        }
    }
    setAvailableFields(aviable_fields, "")
}
function setAvailableFields(aviable_fields, is_sum) {
    var availModValuesBox = document.getElementById("availModValues");
    if(availModValuesBox !== null) availModValuesBox.innerHTML = aviable_fields;
    var mod_groups_a2 = aviable_fields.split("(!#_ID@ID_#!)");
    var module_groupid = mod_groups_a2[0];
    var module_name = mod_groups_a2[1];
    var aviable_fields = mod_groups_a2[2];

    if (is_sum != "") {
        var availModules = document.getElementById("SummariesModules");
        var selectedModuleText = availModules.options[availModules.selectedIndex].text + " - ";
    } else {
        var availModules = document.getElementById("availModules");
        var selectedModuleText = "";
    }
    var selectedModule = availModules.options[availModules.selectedIndex].value;
    var selectedColumnsList = jQuery("#selectedColumnsList").val();
    var reporttype = jQuery("#reporttype").val();
    if(reporttype != "tabular") selectedColumnsList = jQuery("#selectedSummariesTmp").val();
    var arrSelectedColumnsList = selectedColumnsList.split(";");
    if(is_sum != "") {
        //selectedColumnsList = jQuery("#selectedColumnsList").val();
        var selectedColumnsList = jQuery("#selectedSummariesTmp").val();
        arrSelectedColumnsList = selectedColumnsList.split(";");
        jQuery.each(arrSelectedColumnsList,function(key,value){
            var clName =  value.split("##$$##");
            arrSelectedColumnsList[key] = clName[0];
        });

    }

    if (module_groupid.trim() == selectedModule.trim()) {
        var optgroups = aviable_fields.split("(|@!@|)");
        for (i = 0; i < optgroups.length; i++) {
            var optgroup = optgroups[i].split("(|@|)");
            if (optgroup[0] != '') {
                var block_title_div = document.createElement("div");
                block_title_div.className = "block_title_div";
                var title_block = optgroup[0];
                if (is_sum == "") {
                    title_block = title_block.substring(3);//Remove - char on first
                }
                block_title_div.innerHTML = '<p class="block_title_content">'+title_block+'</p>';
                var responseVal = JSON.parse(optgroup[1]);
                for (var widgetId in responseVal) {
                    if (responseVal.hasOwnProperty(widgetId)) {
                        var parent_div = document.getElementById('div_selected_column');
                        if(is_sum == "") parent_div = document.getElementById('div_list_fields_column');
                        var option = responseVal[widgetId];
                        if(document.getElementById(option["value"]) === null){
                            var oOption = document.createElement("div");
                            oOption.id = option["value"];
                            var num_pos = jQuery.inArray( option["value"], arrSelectedColumnsList );
                            if(num_pos >= 0){
                                arrSelectedColumnsList.splice(num_pos, 1);
                                oOption.className  = "div_column_child div_column_child_on";
                            }
                            else{
                                oOption.className  = "div_column_child div_column_child_off";
                            }
                            var img = document.createElement("img");
                            img.src = "modules/VTEReports/img/check_ico.png";
                            oOption.appendChild(img);
                            var preOption = document.createElement("p");
                            var txt_div = document.createTextNode(option["text"]);
                            preOption.appendChild(txt_div);
                            oOption.appendChild(preOption);
                            block_title_div.appendChild(oOption);
                            if( document.getElementById(option["value"]) === null) parent_div.appendChild(block_title_div);
                        }
                    }
                }
            }
        }
    }
}
function setAvailableSUMFields(aviable_fields) {
    setAvailableFields(aviable_fields, "Sum");
}
function checkEmptyLabel(input_id) {
    if (document.getElementById(input_id)) {
        var str = document.getElementById(input_id).value;
        if (str.trim() == "") {
            document.getElementById(input_id).value = document.getElementById("hidden_" + input_id).value;
        }
    }
}
/* VTE-CR SlOl | 13.5.2014 12:09 */
function escapeRegExp(string) {
    return string.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
}
/* VTE-CR SlOl | 20.4.2015 12:28 */
function matrix_js(group_i){
    if(group_i==2){
        var sec_group_i = group_i+1;
    }else if(group_i==3){
        var sec_group_i = group_i-1;
    }

    //alert(group_i+" - "+jQuery("#timeline_type"+group_i+"  :selected").val()+" - "+sec_group_i+" - "+jQuery("#timeline_type"+sec_group_i+' :selected').val());

    var timeline_type = document.getElementById("timeline_type"+group_i);
    var timeline_typeSec = document.getElementById("timeline_type"+sec_group_i);

    var Group = document.getElementById("Group"+group_i);
    var GroupSec = document.getElementById("Group"+sec_group_i);

    if(group_i==2 && jQuery('#Group'+sec_group_i).val()=="none"){
        timeline_type.options[1].selected = true;
        timeline_typeSec.options[0].selected = true;
        return false;
    }else if(group_i==3 && jQuery('#Group'+group_i).val()=="none"){
        timeline_type.options[0].selected = true;
        timeline_typeSec.options[1].selected = true;
        return false;
    }

    if(group_i==2){
        if(jQuery("#timeline_type"+group_i).val()=="cols"){
            jQuery("#timeline_type"+sec_group_i).val("rows");
        }else{
            jQuery("#timeline_type"+sec_group_i).val("cols");
        }
    }else if(group_i==3){
        if(jQuery("#timeline_type"+group_i).val()=="cols"){
            jQuery("#timeline_type"+sec_group_i).val("rows");
        }else{
            jQuery("#timeline_type"+sec_group_i).val("cols");
        }
    }

}
/* VTE-END */
function checkTimeLineColumn(groupObj, group_i) {
    if (group_i > 1) {
        if (document.getElementById("Group1").options[document.getElementById("Group1").selectedIndex].value == "none") {
            alert(document.getElementById("Group1").name + app.vtranslate('CANNOT_BE_EMPTY'));
            groupObj.options[0].selected = true;
            return false;
        }
        if (document.getElementById("Group2").options[document.getElementById("Group2").selectedIndex].value == "none") {
            alert(document.getElementById("Group2").name + app.vtranslate('CANNOT_BE_EMPTY'));
            groupObj.options[0].selected = true;
            return false;
        }
    }
    if (groupObj) {
        var selected_option = groupObj.options[groupObj.selectedIndex].value;
        if (document.getElementById("date_options_json")) {
            var date_options_json = document.getElementById("date_options_json").innerHTML;
            console.log(date_options_json);
            if (date_options_json) {
                var date_options = JSON.stringify(date_options_json);
                if (date_options.indexOf(selected_option) > 0) {
                    // timelinecolumn_html to replace @NMColStr , need to replace with col_str and then insert into div id = radio_group1
                    var timelinecolumn_html = document.getElementById('timelinecolumn_html').value;
                    var timelinecolumn_html = replaceAll("@NMColStr", "_Group" + group_i, timelinecolumn_html);
                    var timelinecolumn_html = replaceAll("value='", "value='" + selected_option, timelinecolumn_html);
                    document.getElementById("radio_group" + group_i).innerHTML = timelinecolumn_html;
                } else {
                            if(selected_option.indexOf(":D") > 0){
                                var prebox = jQuery("#timeline_type" +group_i).val();
                                if(prebox == "cols"){
                                    var timelinecolumn_html = document.getElementById('timelinecolumn_html').value;
                                    timelinecolumn_html = replaceAll("@NMColStr", "_Group" + group_i, timelinecolumn_html);
                                    document.getElementById("radio_group" + group_i).innerHTML = timelinecolumn_html;
                                }
                            }
                            else{
                                document.getElementById("radio_group" + group_i).innerHTML = "";
                            }

                }
            }
        }

    }
}
function getGroupTimeLineValue(group_i) {
    var timeline_frequency = "";
    if (document.getElementsByName('TimeLineColumn_Group' + group_i)) {
        var TimeLineColumnRadios = document.getElementsByName('TimeLineColumn_Group' + group_i);
        for (var i = 0, length = TimeLineColumnRadios.length; i < length; i++) {
            if (TimeLineColumnRadios[i].checked) {
                timeline_frequency = TimeLineColumnRadios[i].value;
                break;
            }
        }
    }
    return timeline_frequency;
}
function getGroupTimeLineType(group_i) {
    var timeline_type_val = "";
    if (document.getElementById('timeline_type' + group_i)) {
        var timeline_type = document.getElementById('timeline_type' + group_i);
        timeline_type_val = timeline_type.options[timeline_type.selectedIndex].value;
    }
    return timeline_type_val;
}
/* VTE-END */
/* VTE-CR SlOl | 23.6.2014 15:01  */
function getUpSelectedSharing() {
    var sharingSelectedStr = "";
    if (document.getElementById('sharingSelectedColumns')) {
        var sharingSelectedColumns = document.getElementById('sharingSelectedColumns');
        var sharingSelectedStr = "";
        for (i = 0; i <= (sharingSelectedColumns.length - 1); i++)
        {
            sharingSelectedStr += sharingSelectedColumns[i].value + '|';
        }
        document.getElementById('sharingSelectedColumnsString').value = sharingSelectedStr;
    }
    return sharingSelectedStr;
}
/* VTE-END 23.6.2014 15:02  */
/* VTE-CR SlOl | 2.7.2014 11:45 */
function defineChartType(chart_type_element) {
    var chart_type_option = chart_type_element.options[chart_type_element.selectedIndex];

    for (var i = 0; i < chart_type_element.options.length; i++)
    {
        var image_id = chart_type_element.options[i].value + "_type";
        if (document.getElementById(image_id)) {
            document.getElementById(image_id).style.display = "none";
        }
    }

    if (chart_type_option.value != null && chart_type_option.value.length != 0)
    {
        var chart_type_value = chart_type_option.value;
        var image_id = chart_type_value + "_type";
        setChartColumns(chart_type_option);
        if (document.getElementById(image_id)) {
            document.getElementById(image_id).style.display = "block";
        }
    }
}
/* VTE-CR SlOl | 2.7.2014 13:27 */
function setChartTitle(ch_title_obj) {
    var chart_type_element = "";
    if(document.getElementById("chartType") !== null){
        chart_type_element = document.getElementById("chartType");
    }
    else{
        return false;
    }
    var chart_type_option = chart_type_element.options[chart_type_element.selectedIndex];
    if (chart_type_option.value == "none") {
        return false;
    }
    if (ch_title_obj && ch_title_obj.value != "") {
        if (document.getElementById("chart_title_div")) {
            document.getElementById("chart_title_div").innerHTML = ch_title_obj.value;
        }
    }
}
function setChartColumns(chart_type_option) {
    if (chart_type_option == "pie" || chart_type_option == "funnel") {
        document.getElementById("ycols").innerHTML = "&nbsp";
        document.getElementById("xcols").innerHTML = "&nbsp";
    }
}
/* VTE-CR SlOl | 22.7.2014 13:45 */
function VTEReportsDateValidate(fldName, fldLabel, type) {
    if (patternValidate(fldName, fldLabel, "DATE") == false)
        return false;
    var dateval = getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');

    var dateelements = splitDateVal(dateval);

    var dd = dateelements[0];
    var mm = dateelements[1];
    var yyyy = dateelements[2];

    if (dd < 1 || dd > 31 || mm < 1 || mm > 12 || yyyy < 1 || yyyy < 1000) {
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        try {
            getObj(fldName).focus()
        } catch (error) {
        }
        return false
    }

    if ((mm == 2) && (dd > 29)) {//checking of no. of days in february month
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        try {
            getObj(fldName).focus()
        } catch (error) {
        }
        return false
    }

    if ((mm == 2) && (dd > 28) && ((yyyy % 4) != 0)) {//leap year checking
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        try {
            getObj(fldName).focus()
        } catch (error) {
        }
        return false
    }

    switch (parseInt(mm)) {
        case 2 :
        case 4 :
        case 6 :
        case 9 :
        case 11 :
            if (dd > 30) {
                alert(app.vtranslate('ENTER_VALID') + fldLabel);
                try {
                    getObj(fldName).focus()
                } catch (error) {
                }
                return false
            }
    }

    var currdate = new Date();
    var chkdate = new Date();

    chkdate.setYear(yyyy);
    chkdate.setMonth(mm - 1);
    chkdate.setDate(dd);

    if (type != "OTH") {
        if (!compareDates(chkdate, fldLabel, currdate, "current date", type)) {
            try {
                getObj(fldName).focus()
            } catch (error) {
            }
            return false
        } else
            return true;
    } else
        return true;
}
function VTEReportsre_dateValidate(fldval, fldLabel, type) {
    if (re_patternValidate(fldval, fldLabel, "DATE") == false)
        return false;
    var dateval = fldval.replace(/^\s+/g, '').replace(/\s+$/g, '');

    var dateelements = splitDateVal(dateval);

    var dd = dateelements[0];
    var mm = dateelements[1];
    var yyyy = dateelements[2];

    if (dd < 1 || dd > 31 || mm < 1 || mm > 12 || yyyy < 1 || yyyy < 1000) {
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }

    if ((mm == 2) && (dd > 29)) {//checking of no. of days in february month
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }

    if ((mm == 2) && (dd > 28) && ((yyyy % 4) != 0)) {//leap year checking
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }

    switch (parseInt(mm)) {
        case 2 :
        case 4 :
        case 6 :
        case 9 :
        case 11 :
            if (dd > 30) {
                alert(app.vtranslate('ENTER_VALID') + fldLabel);
                return false
            }
    }

    var currdate = new Date();
    var chkdate = new Date();

    chkdate.setYear(yyyy);
    chkdate.setMonth(mm - 1);
    chkdate.setDate(dd);

    if (type != "OTH") {
        if (!compareDates(chkdate, fldLabel, currdate, "current date", type)) {
            return false
        } else
            return true;
    } else
        return true;
}
function VTEReportsStDateValidate(columnIndex, comparatorValue, fldLabel) {
    if (typeof document.getElementById("jscal_field_sdate" + columnIndex) != 'undefined' && typeof document.getElementById("jscal_field_edate" + columnIndex) != 'undefined') {
        switch (comparatorValue) {
            case "custom" :
                if (!emptyCheckVte("jscal_field_sdate" + columnIndex, fldLabel, "date")) {
                    return false;
                }
                if (!emptyCheckVte("jscal_field_edate" + columnIndex, fldLabel, "date")) {
                    return false;
                }
                break;
        }
    }
    return true;
}
function setSelectedCriteriaValue(criteria_obj, selected_value) {
    for (var fci = 0; fci < criteria_obj.options.length; fci++) {
        if (criteria_obj.options[fci].value == selected_value) {
            criteria_obj.options[fci].selected = true;
        }
    }
}

function SaveChartImg(image_path, name) {
    if (document.getElementById("current_action")) {
        var c_action = document.getElementById("current_action").value;
    } else {
        var c_action = "";
    }
    //var download_url = "index.php?module=VTEReports&action=VTEReportsAjax&file=DownloadFile&filepath=" + image_path + "&filename=" + name + ".png";
    var download_url = "index.php?module=VTEReports&action=IndexAjax&mode=DownloadFile&filepath=" + image_path + "&filename=" + name + ".png";
    var features = "width=20,height=20";
    var opened_win = window.open(download_url, name, features);
    return true;
}


function generatePDF(id, pdfmaker_active, is_test_write_able, div_id) {
    if (pdfmaker_active == true || pdfmaker_active == 1) {
        var report_filename_obj = document.getElementById('report_filename');
        if (report_filename_obj && report_filename_obj.value != "") {
            exportToPDF(report_filename_obj.value);
            return true;
        } else if (is_test_write_able == true || is_test_write_able == 1) {
            document.getElementById(div_id).style.display = "block";
            return false;
        } else {
            document.getElementById(div_id).style.display = "block";
            return false;
        }
    } else {
        document.getElementById(div_id).style.display = "block";
        return false;
    }
}

function printDiv(){
    var printContents = document.getElementById("vtereports_html").innerHTML;
    w = window.open();

    w.document.write(printContents);
    w.document.write('<scr' + 'ipt type="text/javascript">' + 'window.onload = function() { window.print(); window.close(); };' + '</sc' + 'ript>');

    w.document.close(); // necessary for IE >= 10
    w.focus(); // necessary for IE >= 10

    return true;
}

/**
 * return Report Record id based on edit/detail view
 */
function getReportRecordID() {
    var view = jQuery("#view").val();
    var reportID = "";
    if(view=="Edit"){
        reportID = document.NewReport.record.value;
    }else{
        reportID = jQuery("#recordId").val();
    }
    return reportID;
}

// FROM vt540
function numValidate(fldName,fldLabel,format,neg) {
    var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
    if(typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
        while(val.indexOf(userCurrencySeparator) != -1) {
            val = val.replace(userCurrencySeparator,'');
        }
    }
    if(typeof userDecimalSeparator != 'undefined' && userDecimalSeparator != '') {
        if(val.indexOf(userDecimalSeparator) != -1) {
            val = val.replace(userDecimalSeparator,'.');
        }
    }
    if (format!="any") {
        if (isNaN(val)) {
            var invalid=true
        } else {
            var format=format.split(",");
            var splitval=val.split(".");
            if (neg==true) {
                if (splitval[0].indexOf("-")>=0) {
                    if (splitval[0].length-1>format[0])
                        invalid=true
                } else {
                    if (splitval[0].length>format[0])
                        invalid=true
                }
            } else {
                if (val<0)
                    invalid=true;
                else if (format[0]==2 && splitval[0]==100 && (!splitval[1] || splitval[1]==0))
                    invalid=false;
                else if (splitval[0].length>format[0])
                    invalid=true
            }
            if (splitval[1])
                if (splitval[1].length>format[1])
                    invalid=true
        }
        if (invalid==true) {
            alert(app.vtranslate('INVALID')+fldLabel);
            try {
                getObj(fldName).focus()
            } catch(error) { }
            return false
        }else return true
    } else {
        // changes made -- to fix the ticket#3272
        if(fldName == "probability" || fldName == "commissionrate")
        {
            var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
            var splitval=val.split(".");
            var arr_len = splitval.length;
            var len = 0;

            if(arr_len > 1)
                len = splitval[1].length;
            if(isNaN(val))
            {
                alert(app.vtranslate('INVALID')+fldLabel);
                try {
                    getObj(fldName).focus()
                }catch(error) { }
                return false
            }
            else if(splitval[0] > 100 || len > 3 || (splitval[0] >= 100 && splitval[1] > 0))
            {
                alert( fldLabel + app.vtranslate('EXCEEDS_MAX'));
                return false;
            }
        }
        else {
            var splitval=val.split(".");
            if(splitval[0]>18446744073709551615)
            {
                alert( fldLabel + app.vtranslate('EXCEEDS_MAX'));
                return false;
            }
        }

        if (neg==true)
            var re=/^(-|)(\d)*(\.)?\d+(\.\d\d*)*$/;
        else
            var re=/^(\d)*(\.)?\d+(\.\d\d*)*$/
    }

    //for precision check. ie.number must contains only one "."
    var dotcount=0;
    for (var i = 0; i < val.length; i++)
    {
        if (val.charAt(i) == ".")
            dotcount++;
    }

    if(dotcount>1)
    {
        alert(app.vtranslate('INVALID')+fldLabel);
        try {
            getObj(fldName).focus()
        }catch(error) { }
        return false;
    }

    if (!re.test(val)) {
        alert(app.vtranslate('INVALID')+fldLabel);
        try {
            getObj(fldName).focus()
        } catch(error) { }
        return false
    } else return true
}

function intValidate(fldName,fldLabel) {
    var val=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');
    if(typeof userCurrencySeparator != 'undefined' && userCurrencySeparator != '') {
        while(val.indexOf(userCurrencySeparator) != -1) {
            val = val.replace(userCurrencySeparator,'');
        }
    }
    if (isNaN(val) || (val.indexOf(".")!=-1 && fldName != 'potential_amount' && fldName != 'list_price'))
    {
        alert(app.vtranslate('INVALID')+fldLabel);
        try {
            getObj(fldName).focus()
        } catch(error) { }
        return false
    }
    else if((fldName != 'employees' || fldName != 'noofemployees') && (val < -2147483648 || val > 2147483647))
    {
        alert(fldLabel +app.vtranslate('OUT_OF_RANGE'));
        return false;
    }
    else if((fldName == 'employees' || fldName != 'noofemployees') && (val < 0 || val > 2147483647))
    {
        alert(fldLabel +app.vtranslate('OUT_OF_RANGE'));
        return false;
    }
    else
    {
        return true
    }
}

function dateValidate(fldName,fldLabel,type) {
    if(patternValidate(fldName,fldLabel,"DATE")==false)
        return false;
    dateval=getObj(fldName).value.replace(/^\s+/g, '').replace(/\s+$/g, '');

    var dateelements=splitDateVal(dateval);

    dd=dateelements[0];
    mm=dateelements[1];
    yyyy=dateelements[2];

    if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
        alert(app.vtranslate('ENTER_VALID')+fldLabel);
        try {
            getObj(fldName).focus()
        } catch(error) { }
        return false
    }

    if ((mm==2) && (dd>29)) {//checking of no. of days in february month
        alert(app.vtranslate('ENTER_VALID')+fldLabel);
        try {
            getObj(fldName).focus()
        } catch(error) { }
        return false
    }

    if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
        alert(app.vtranslate('ENTER_VALID')+fldLabel);
        try {
            getObj(fldName).focus()
        } catch(error) { }
        return false
    }

    switch (parseInt(mm)) {
        case 2 :
        case 4 :
        case 6 :
        case 9 :
        case 11 :
            if (dd>30) {
                alert(app.vtranslate('ENTER_VALID')+fldLabel);
                try {
                    getObj(fldName).focus()
                } catch(error) { }
                return false
            }
    }

    var currdate=new Date();
    var chkdate=new Date();

    chkdate.setYear(yyyy);
    chkdate.setMonth(mm-1);
    chkdate.setDate(dd);

    if (type!="OTH") {
        if (!compareDates(chkdate,fldLabel,currdate,"current date",type)) {
            try {
                getObj(fldName).focus()
            } catch(error) { }
            return false
        } else return true;
    } else return true;
}

function re_dateValidate(fldval,fldLabel,type) {
    if(re_patternValidate(fldval,fldLabel,"DATE")==false)
        return false;
    dateval=fldval.replace(/^\s+/g, '').replace(/\s+$/g, '');

    var dateelements=splitDateVal(dateval);

    dd=dateelements[0];
    mm=dateelements[1];
    yyyy=dateelements[2];

    if (dd<1 || dd>31 || mm<1 || mm>12 || yyyy<1 || yyyy<1000) {
        alert(app.vtranslate('ENTER_VALID')+fldLabel);
        return false
    }

    if ((mm==2) && (dd>29)) {//checking of no. of days in february month
        alert(app.vtranslate('ENTER_VALID')+fldLabel);
        return false
    }

    if ((mm==2) && (dd>28) && ((yyyy%4)!=0)) {//leap year checking
        alert(app.vtranslate('ENTER_VALID')+fldLabel);
        return false
    }

    switch (parseInt(mm)) {
        case 2 :
        case 4 :
        case 6 :
        case 9 :
        case 11 :
            if (dd>30) {
                alert(app.vtranslate('ENTER_VALID')+fldLabel);
                return false
            }
    }

    var currdate=new Date();
    var chkdate=new Date();

    chkdate.setYear(yyyy);
    chkdate.setMonth(mm-1);
    chkdate.setDate(dd);

    if (type!="OTH") {
        if (!compareDates(chkdate,fldLabel,currdate,"current date",type)) {
            return false
        } else return true;
    } else return true;
}

//Copied from general.js and altered some lines. becos we cant send vales to function present in general.js. it accept only field names.
function re_patternValidate(fldval,fldLabel,type) {

    if (type.toUpperCase()=="EMAIL") {
        /*changes made to fix -- ticket#3278 & ticket#3461
         var re=new RegExp(/^.+@.+\..+$/)*/
        //Changes made to fix tickets #4633, #5111  to accomodate all possible email formats
        var re=new RegExp(/^[a-zA-Z0-9]+([\_\-\.]*[a-zA-Z0-9]+[\_\-]?)*@[a-zA-Z0-9]+([\_\-]?[a-zA-Z0-9]+)*\.+([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)*$/)
    }

    if (type.toUpperCase()=="DATE") {//DATE validation

        switch (userDateFormat) {
            case "yyyy-mm-dd" :
                var re = /^\d{4}(-)\d{1,2}\1\d{1,2}$/;
                break;
            case "mm-dd-yyyy" :
            case "dd-mm-yyyy" :
                var re = /^\d{1,2}(-)\d{1,2}\1\d{4}$/
        }
    }


    if (type.toUpperCase()=="TIMESECONDS") {//TIME validation
        var re = new RegExp("^([0-1][0-9]|[2][0-3]):([0-5][0-9]):([0-5][0-9])$|^([0-1][0-9]|[2][0-3]):([0-5][0-9])$");
    }
    if (!re.test(fldval)) {
        alert(app.vtranslate('ENTER_VALID') + fldLabel);
        return false
    }
    else return true
}

function patternValidate(fldName,fldLabel,type) {
    var currObj=getObj(fldName);

    if (type.toUpperCase()=="EMAIL") //Email ID validation
    {
        /*changes made to fix -- ticket#3278 & ticket#3461
         var re=new RegExp(/^.+@.+\..+$/)*/
        //Changes made to fix tickets #4633, #5111  to accomodate all possible email formats
        var re=new RegExp(/^[a-zA-Z0-9]+([!"#$%&'()*+,./:;<=>?@\^_`{|}~-]?[a-zA-Z0-9])*@[a-zA-Z0-9]+([\_\-\.]?[a-zA-Z0-9]+)*\.([\-\_]?[a-zA-Z0-9])+(\.?[a-zA-Z0-9]+)?$/);
    }

    if (type.toUpperCase()=="DATE") {//DATE validation
        //YMD
        //var reg1 = /^\d{2}(\-|\/|\.)\d{1,2}\1\d{1,2}$/ //2 digit year
        //var re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/ //4 digit year

        //MYD
        //var reg1 = /^\d{1,2}(\-|\/|\.)\d{2}\1\d{1,2}$/
        //var reg2 = /^\d{1,2}(\-|\/|\.)\d{4}\1\d{1,2}$/

        //DMY
        //var reg1 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{2}$/
        //var reg2 = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/

        switch (userDateFormat) {
            case "yyyy-mm-dd" :
                var re = /^\d{4}(\-|\/|\.)\d{1,2}\1\d{1,2}$/;
                break;
            case "mm-dd-yyyy" :
            case "dd-mm-yyyy" :
                var re = /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/
        }
    }

    if (type.toUpperCase()=="TIME") {//TIME validation
        var re = /^\d{1,2}\:\d{2}:\d{2}$|^\d{1,2}\:\d{2}$/
    }
    //Asha: Remove spaces on either side of a Email id before validating
    if (type.toUpperCase()=="EMAIL" || type.toUpperCase() == "DATE") currObj.value = trim(currObj.value);
    if (!re.test(currObj.value)) {
        alert(app.vtranslate('ENTER_VALID') + fldLabel  + " ("+type+")");
        try {
            currObj.focus()
        } catch(error) {
            // Fix for IE: If element or its wrapper around it is hidden, setting focus will fail
            // So using the try { } catch(error) { }
        }
        return false
    }
    else return true
}

function ChartDataSeries(ch_style_obj){
    var currOption = ch_style_obj.options[ch_style_obj.selectedIndex];
    if(typeof currOption != "undefined"){
        if(currOption.value=="group2"){
            //jQuery('#data_series1').val("");
            jQuery('#data_series2').val("");
            jQuery('#chartType2').val("");
            jQuery("#chartType2").attr("disabled","true");
            jQuery("#data_series2").attr("disabled","true");
            jQuery('#data_series3').val("");
            jQuery('#chartType3').val("");
            jQuery("#chartType3").attr("disabled","true");
            jQuery("#data_series3").attr("disabled","true");
        }else{
            jQuery("#chartType2").attr("disabled","false");
            jQuery("#chartType2").removeAttr('disabled');
            jQuery("#data_series2").attr("disabled","false");
            jQuery("#data_series2").removeAttr('disabled');
            jQuery("#chartType3").attr("disabled","false");
            jQuery("#chartType3").removeAttr('disabled');
            jQuery("#data_series3").attr("disabled","false");
            jQuery("#data_series3").removeAttr('disabled');
        }
    }
}
// VTE-CR SlOl 22. 5. 2015 12:18:19
function saveChartImageToFile(export_pdf_format,pdf_file_name,ch_image_name,chart_image){
    var file_path = 'test/VTEReports/'+ch_image_name+'.png';

    var postData = {
        "filename": ch_image_name,
        "report_filename": pdf_file_name,
        "filepath": file_path,
        "export_pdf_format": export_pdf_format,
        "canvasData": chart_image
    };

    var actionParams = {
        "type": "POST",
        "url": 'index.php?module=VTEReports&action=SaveImage',
        "dataType": "html",
        "data": postData
    };
    app.request.post(actionParams).then(
        function(err,data){
            if(err === null) {
                window.open('index.php?module=VTEReports&action=SaveImage&mode=download&export_pdf_format='+export_pdf_format+'&report_filename='+pdf_file_name+'&filepath='+file_path, '_blank');
            }else{
                // to do
            }
        }
    );

}
function saveChartAsBinary(){
    var id = jQuery('#recordId').val();
    var export_pdf_format = jQuery('#export_pdf_format').val();
    var pdf_file_name = jQuery('#pdf_file_name').val();
    var ch_image_name = jQuery('#ch_image_name').val();

    var svg = document.getElementById("vtereports_widget_"+id).children[0].innerHTML;
    canvg(document.getElementById("canvas"),svg);
    var img = canvas.toDataURL("image/png"); //img is data:image/png;base64

    img = img.replace("data:image/png;base64,", "");

    //jQuery("#binaryImage").attr("src", "data:image/png;base64,"+img);
    saveChartImageToFile(export_pdf_format,pdf_file_name,ch_image_name,img);
}
function exportToPDF(report_filename){

    var export_pdf_format = jQuery('#export_pdf_format').val();

    var vtereports_name = jQuery('.vteTableText').html();
    var vtereports_html = jQuery('#vtereports_html').html();

    var id = jQuery('#recordId').val();
    var export_pdf_format = jQuery('#export_pdf_format').val();

    var chart_image = "";

    if (document.getElementById("vtereports_widget_"+id) != null){
        var ch_image_name = jQuery('#ch_image_name').val();

        var svg = document.getElementById("vtereports_widget_"+id).children[0].innerHTML;
        canvg(document.getElementById("canvas"),svg);
        var chart_image = canvas.toDataURL("image/png");

        chart_image = chart_image.replace("data:image/png;base64,", "");
    }

    jQuery('#form_export_pdf_format').val(export_pdf_format);
    jQuery('#form_filename').val(report_filename);
    jQuery('#form_report_name').val(vtereports_name);
    jQuery('#form_report_html').val(vtereports_html);
    jQuery('#form_chart_canvas').val(chart_image);
    jQuery('#CreatePDF').submit();
    return false;
}
// VTE-END