{*<!--
/* ********************************************************************************
* The content of this file is subject to the Item Lookup ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<style type="text/css">
    .popover{
        max-width:600px;
    }
</style>
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('Global Configuration', 'VTEItemLookup')}</label></div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row">
        <div class="row row-padding">
            <div class="col-lg-3 col-sm-3"><label class="field-configure-label">{vtranslate('Default Module', 'VTEItemLookup')} <a class="field-configure-label" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_DEFAULT_MODULE_TOOLTIP', $MODULE)}"><i class="fa fa-info-circle"></i></a></label></div>
            <div class="col-lg-9 col-sm-9">
                <select name="default_module" data-field-name="default_module" class="picklist-field medium-picklist">
                    <option value="Products" {if $CONFIGURE['default_module'] == 'Products' && $CONFIGURE['product_bundles'] != 1} selected{/if}>{vtranslate('Products', 'Products')}</option>
                    <option value="ProductsBundles" {if $CONFIGURE['default_module'] == 'Products' && $CONFIGURE['product_bundles'] == 1} selected{/if}>{vtranslate('Bundles', 'Products')}</option>
                    <option value="Services" {if $CONFIGURE['default_module'] == 'Services'} selected{/if}>{vtranslate('Services', 'Services')}</option>
                </select>
            </div>
        </div>
        <div class="row row-padding">
            <div class="col-lg-3 col-sm-3"><label class="field-configure-label">{vtranslate('Hide "Add Product" Button', 'VTEItemLookup')} <a class="field-configure-label" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_HIDE_ADD_PRODUCT_TOOLTIP', $MODULE)}"><i class="fa fa-info-circle"></i></a></label></div>
            <div class="col-lg-9 col-sm-9"><input style="opacity: 0;" {if $CONFIGURE['hide_add_product_button'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="hide_add_product_button" name="hide_add_product_button"></div>
        </div>
        <div class="row row-padding">
            <div class="col-lg-3 col-sm-3"><label class="field-configure-label">{vtranslate('Hide "Add Service" Button', 'VTEItemLookup')} <a class="field-configure-label" data-trigger="hover" data-html="True" data-placement="right" data-content="{vtranslate('LBL_HIDE_ADD_SERVICE_TOOLTIP', $MODULE)}"><i class="fa fa-info-circle"></i></a></label></div>
            <div class="col-lg-9 col-sm-9"><input style="opacity: 0;" {if $CONFIGURE['hide_add_service_button'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="hide_add_service_button" name="hide_add_service_button"></div>
        </div>
        <div class="row"></div>
        <div class="row"></div>
    </div>
</div>
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('Products', 'Products')}</label></div>
        <div class="col-sm-6"><h4><label>{vtranslate('Services', 'Services')}</label></div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row">
        <div class="col-lg-6 col-sm-6 border-right ">
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Filter Field 1', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <select name="product_filter_field_1" data-field-name="default_module" class="picklist-field medium-picklist">
                        <option value="">Select an option</option>
                        {foreach item=PRODUCT_FIELD from=$PRODUCT_FIELDS_MODEL}
                            {if $PRODUCT_FIELD->uitype eq 15 || $PRODUCT_FIELD->uitype eq 16 || $PRODUCT_FIELD->name eq 'vendor_id'}
                                <option value="{$PRODUCT_FIELD->name}" {if $PRODUCT_FIELD->name == $CONFIGURE['product_filter_field_1']} selected{/if}>{$PRODUCT_FIELD->label}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Filter Field 2', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <select name="product_filter_field_2" data-field-name="default_module" class="picklist-field medium-picklist">
                        <option value="">Select an option</option>
                        {foreach item=PRODUCT_FIELD from=$PRODUCT_FIELDS_MODEL}
                            {if $PRODUCT_FIELD->uitype eq 15 || $PRODUCT_FIELD->uitype eq 16 || $PRODUCT_FIELD->name eq 'vendor_id'}
                                <option value="{$PRODUCT_FIELD->name}" {if $PRODUCT_FIELD->name == $CONFIGURE['product_filter_field_2']} selected{/if}>{$PRODUCT_FIELD->label}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Filter Field 3', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <select name="product_filter_field_3" data-field-name="default_module" class="picklist-field medium-picklist">
                        <option value="">Select an option</option>
                        {foreach item=PRODUCT_FIELD from=$PRODUCT_FIELDS_MODEL}
                            {if $PRODUCT_FIELD->uitype eq 15 || $PRODUCT_FIELD->uitype eq 16 || $PRODUCT_FIELD->name eq 'vendor_id'}
                                <option value="{$PRODUCT_FIELD->name}" {if $PRODUCT_FIELD->name == $CONFIGURE['product_filter_field_3']} selected{/if}>{$PRODUCT_FIELD->label}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Show "In Stock Only" Filter', 'VTEItemLookup')}</div>
                <div class="col-lg-6 col-sm-6"><input style="opacity: 0;" {if $CONFIGURE['product_show_instock_filter'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="product_show_instock_filter" name="product_show_instock_filter"></div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Show "Inactive" Filter', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6"><input style="opacity: 0;" {if $CONFIGURE['product_show_inactive_filter'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="product_show_inactive_filter" name="product_show_inactive_filter"></div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Show "Bundles" Filter', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6"><input style="opacity: 0;" {if $CONFIGURE['product_show_bundles_filter'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="product_show_bundles_filter" name="product_show_bundles_filter"></div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Show "Picture" Column', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <div class="row">
                        <div class="col-lg-3 col-sm-6">
                            <input style="opacity: 0;" {if $CONFIGURE['product_show_picture_column'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="product_show_picture_column" name="product_show_picture_column">
                        </div>
                        <div class="col-lg-9 col-sm-6">
                            <div class="form-group">
                                <label class="field-configure-label">{vtranslate('Size (px)', 'VTEItemLookup')}</label>
                                <input type="number" value="{$CONFIGURE['product_show_picture_size_width']}" class=" inputElement picture-size text-field" name="product_show_picture_size_width"/>
                                <input type="number" value="{$CONFIGURE['product_show_picture_size_height']}" class=" inputElement picture-size text-field" name="product_show_picture_size_height"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-sm-6">
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Filter Field 1', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <select name="service_filter_field_1" data-field-name="default_module" class="picklist-field medium-picklist">
                        <option value="">Select an option</option>
                        {foreach item=SERVICE_FIELD from=$SERVICE_FIELDS_MODEL}
                            {if $SERVICE_FIELD->uitype eq 15 || $SERVICE_FIELD->uitype eq 16 || $SERVICE_FIELD->uitype eq 33}
                                <option value="{$SERVICE_FIELD->name}" {if $SERVICE_FIELD->name == $CONFIGURE['service_filter_field_1']} selected{/if}>{$SERVICE_FIELD->label}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Filter Field 2', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <select name="service_filter_field_2" data-field-name="default_module" class="picklist-field medium-picklist">
                        <option value="">Select an option</option>
                        {foreach item=SERVICE_FIELD from=$SERVICE_FIELDS_MODEL}
                            {if $SERVICE_FIELD->uitype eq 15 || $SERVICE_FIELD->uitype eq 16 || $SERVICE_FIELD->uitype eq 33}
                                <option value="{$SERVICE_FIELD->name}" {if $SERVICE_FIELD->name == $CONFIGURE['service_filter_field_2']} selected{/if}>{$SERVICE_FIELD->label}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Filter Field 3', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6">
                    <select name="service_filter_field_3" data-field-name="default_module" class="picklist-field medium-picklist">
                        <option value="">Select an option</option>
                        {foreach item=SERVICE_FIELD from=$SERVICE_FIELDS_MODEL}
                            {if $SERVICE_FIELD->uitype eq 15 || $SERVICE_FIELD->uitype eq 16 || $SERVICE_FIELD->uitype eq 33}
                                <option value="{$SERVICE_FIELD->name}" {if $SERVICE_FIELD->name == $CONFIGURE['service_filter_field_3']} selected{/if}>{$SERVICE_FIELD->label}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="row row-padding">
                <div class="col-lg-6 col-sm-6"><label class="field-configure-label">{vtranslate('Show "Inactive" Filter', 'VTEItemLookup')}</label></div>
                <div class="col-lg-6 col-sm-6"><input style="opacity: 0;" {if $CONFIGURE['service_show_inactive_filter'] == 1} checked value="on" {else} value="off"{/if} data-on-color="success"  data-id="{$LISTVIEW_ENTRY['id']}" class="switch-input" type="checkbox" data-field-name="service_show_inactive_filter" name="service_show_inactive_filter"></div>
            </div>
        </div>
    </div>
</div>