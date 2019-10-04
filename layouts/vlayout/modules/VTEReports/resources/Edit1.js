VTEReports_Edit_Js("VTEReports_Edit1_Js", {}, {g:!1, a:!1, b:!1, H:function() {
    this.m();
}, C:function() {
    return this.a;
}, h:function(a) {
    this.a = a;
    return this;
}, c:function() {
    0 == this.b && (this.b = jQuery("#secondary_module"));
    return this.b;
}, m:function(a) {
    "undefined" == typeof a && (a = jQuery("#report_step1"));
    a.is("#report_step1") ? this.h(a) : this.h(jQuery("#report_step1"));
    this.o();
}, o:function() {
    this.g = jQuery("#relatedModules").data("value");
}, l:function(a) {
    return this.g[a];
}, u:function(a) {
    a = this.l(a);
    var b = "", c;
    for (c in a) {
        a.hasOwnProperty(c) && (b += '<option value="' + c + '">' + a[c] + "</option>");
    }
    this.c().G(b).S("change");
}, w:function() {
    var a = this;
    jQuery("#primary_module").J("change", function(b) {
        b = jQuery(b.currentTarget).T();
        a.u(b);
    });
}, j:function(a) {
    var b = jQuery.i();
    a = {module:app.D(), action:"CheckDuplicate", reportname:a.N, record:a.M, isDuplicate:a.s};
    AppConnector.request(a).then(function(a) {
        a = a.result;
        1 == a.success ? b.reject(a) : b.resolve(a);
    }, function() {
        b.reject();
    });
    return b.v();
}, submit:function() {
    var a = jQuery.i(), b = this.a, c = b.P(), d = {}, f = jQuery.trim(c.O), g = c.K, e = jQuery.f({position:"html", blockInfo:{enabled:!0}});
    this.j({reportName:f, reportId:g, isDuplicate:c.s}).then(function() {
        AppConnector.request(c).then(function(c) {
            b.F();
            e.f({mode:"hide"});
            a.resolve(c);
        }, function() {
        });
    }, function(b) {
        e.f({mode:"hide"});
        d = {title:app.W("JS_DUPLICATE_RECORD"), text:b.message};
        Vtiger_Helper_Js.R(d);
        a.reject();
    });
    return a.v();
}, A:function() {
    var a = this.c();
    app.B(a, "select2", {I:2});
}, L:function() {
    this.w();
    this.A();
    var a = this.a, b = app.V;
    b.onValidationComplete = function(a, b) {
        return b;
    };
    b.promptPosition = "bottomRight";
    a.U(b);
}});