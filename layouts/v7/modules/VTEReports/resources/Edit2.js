VTEReports_Edit_Js("VTEReports_Edit2_Js", {}, {c:!1, i:!1, j:!1, T:function() {
    this.C();
}, P:function() {
    return this.c;
}, s:function(a) {
    this.c = a;
    return this;
}, g:function() {
    0 == this.i && (this.i = jQuery("#reportsColumnsList"));
    return this.i;
}, h:function() {
    0 == this.j && (this.j = jQuery("#seleted_fields"));
    return this.j;
}, C:function(a) {
    "undefined" == typeof a && (a = jQuery("#report_step2"));
    a.is("#report_step2") ? this.s(a) : this.s(jQuery("#report_step2"));
}, V:function() {
    var a = this.g(), b = app.m(a), a = Vtiger_MultiSelect_Validator_Js.U(a);
    if (1 != a) {
        return b.u("showPrompt", a, "error", "bottomLeft", !0), app.O(this.c), !1;
    }
    b.u("hide");
    return !0;
}, w:function() {
    var a = this.c, b = this.B();
    this.h().b(JSON.stringify(b));
    var d = [], b = jQuery(".sortFieldRow", a);
    jQuery.f(b, function(a, b) {
        var c = jQuery(b), f = c.find(".selectedSortFields").b(), h = c.find(".sortOrder").filter(":checked").b(), c = c.find(".sortType").b();
        d.push([f, h, c]);
    });
    jQuery("#selected_sort_fields").b(JSON.stringify(d));
    var c = {}, a = jQuery(".CalculationFields", a).find(".calculationFieldRow"), f = 0;
    jQuery.f(a, function(a, b) {
        var d = jQuery(b).find(".calculationType:checked");
        jQuery.f(d, function(a, b) {
            c[f] = jQuery(b).b();
            f++;
        });
    });
    jQuery("#calculation_fields").b(JSON.stringify(c));
}, submit:function() {
    var a = jQuery.L();
    this.w();
    var b = this.c, d = b.aa(), c = jQuery.G({position:"html", blockInfo:{enabled:!0}});
    app.request.post(d).then(function(err,d) {
        b.R();
        c.G({mode:"hide"});
        a.resolve(d);
    }, function() {
    });
    return a.Z();
}, J:function() {
    var a = this.g();
    app.A(a, "select2", {W:25, N:{"z-index":0}});
}, B:function() {
    var a = this.g(), b = app.m(a), d = [], c = a.find("option:selected");
    b.find("li.select2-search-choice").find("div").f(function(a, b) {
        var g = jQuery(b).closest(".select2-search-choice").data("select2Data").id;
        c.f(function(a, b) {
            var c = jQuery(b);
            if (c.b() == g) {
                return d.push(c.b()), !1;
            }
        });
    });
    return d;
}, v:function() {
    var a = this.g(), b = app.m(a).find("ul.select2-choices"), d = b.find("li.select2-search-choice"), a = a.find("option:selected"), c = JSON.parse(this.h().b()), f = [], e;
    for (e in c) {
        c.hasOwnProperty(e) && f.push(e);
    }
    for (e = f.length;0 < e;e--) {
        var g = a.filter('[value="' + c[f[e - 1]] + '"]');
        d.f(function(a, c) {
            var d = jQuery(c);
            if (d.find("div").o() == g.o()) {
                return b.Y(d), !1;
            }
        });
    }
}, D:function() {
    var a = this, b = a.g();
    app.m(b).find("ul.select2-choices").ba({M:"parent", start:function() {
        a.h().K("onSortStart");
    }, update:function() {
        a.h().K("onSortEnd");
    }});
}, H:function() {
    var a = this, b = jQuery('input[name="primary_module"]').b();
    -1 !== jQuery.S(b, ["Invoice", "Quotes", "PurchaseOrder", "SalesOrder"]) && jQuery(".CalculationFields").X("change", 'input[type="checkbox"]', function(b) {
        var c = jQuery(b.currentTarget);
        b = c.b();
        var f = new RegExp(/cb:vtiger_inventoryproductrel*/), c = c.is(":checked"), e = jQuery('.CalculationFields input[type="checkbox"]').F('[value^="cb:vtiger_inventoryproductrel"]'), g = jQuery(".CalculationFields").find('[value^="cb:vtiger_inventoryproductrel"]');
        f.test(b) ? c ? e.a("checked", !1).a("disabled", !0) : 0 == g.filter(":checked").length ? e.a("disabled", !1) : e.a("checked", !1).a("disabled", !0) : c ? g.a("checked", !1).a("disabled", !0) : 0 == e.filter(":checked").length ? g.a("disabled", !1) : g.a("disabled", !0).a("checked", !1);
        a.l();
    });
}, l:function() {
    var a = app.ca("JS_CALCULATION_LINE_ITEM_FIELDS_SELECTION_LIMITATION");
    0 == jQuery("#calculationLimitationMessage").length ? jQuery(".CalculationFields").parent().append('<div id="calculationLimitationMessage" class="pull-right alert alert-info">' + a + "</div>") : jQuery("#calculationLimitationMessage").o(a);
}, I:function() {
    var a = jQuery('.CalculationFields input[type="checkbox"]').F('[value^="cb:vtiger_inventoryproductrel"]'), b = jQuery(".CalculationFields").find('[value^="cb:vtiger_inventoryproductrel"]');
    0 != a.filter(":checked").length ? (b.a("checked", !1).a("disabled", !0), this.l()) : 0 != b.filter(":checked").length && (a.a("checked", !1).a("disabled", !0), this.l());
}, $:function() {
    var a = this.c;
    this.j = this.i = !1;
    this.J();
    this.v();
    this.D();
    this.H();
    this.I();
    app.A(a);
    a.u({onValidationComplete:function(a, d) {
        return d;
    }});
}});