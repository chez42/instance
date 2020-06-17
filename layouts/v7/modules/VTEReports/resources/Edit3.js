VTEReports_Edit_Js("VTEReports_Edit3_Js", {}, {a:!1, b:!1, o:function() {
    this.g();
}, j:function() {
    return this.a;
}, c:function(a) {
    this.a = a;
    return this;
}, g:function(a) {
    "undefined" == typeof a && (a = jQuery("#report_step3"));
    a.is("#report_step3") ? this.c(a) : this.c(jQuery("#report_step3"));
}, f:function() {
    var a = this.b.m();
    jQuery("#advanced_filter").u(JSON.stringify(a));
}, h:function() {
    var a = this;
    this.a.submit(function() {
        a.f();
    });
}, s:function() {
    var a = this.a;
    app.i(a);
    this.b = Vtiger_AdvanceFilter_Js.l(jQuery(".filterContainer", a));
    this.h();
    a.v();
}});