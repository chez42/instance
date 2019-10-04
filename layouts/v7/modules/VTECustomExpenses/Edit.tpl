{*<!--
/* ********************************************************************************
* The content of this file is subject to the Custom Expenses/Bills ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{strip}
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('VTECustomExpenses', 'VTECustomExpenses')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="editViewPageDiv">
        <form id="EditView" action="index.php" method="post" name="EditVTECustomExpenses">
            <input type="hidden" name="module" id="module" value="{$MODULE}">
            <input type="hidden" name="action" value="SaveVTECustomExpenses" />
            <input type="hidden" name="record" id="record" value="{$RECORD}">
            <div class="col-sm-12 col-xs-12">
                <div class="col-sm-6 col-xs-6 form-horizontal">
                    <div class="form-group">
                        <label for="custom_expenses_module" class="control-label col-sm-3">
                            <span>Module</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_expenses_module" name="custom_expenses_module" data-rule-required="true">
                                <option value="">Select an Option</option>
                                {foreach item=MODULE_VALUES from=$ALL_MODULES}
                                    <option value="{$MODULE_VALUES->name}" {if $MODULE_VALUES->name eq $RECORDENTRIES['module']}selected{/if}>{vtranslate($MODULE_VALUES->label,$MODULE_VALUES->name)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_name" class="control-label col-sm-3">
                            <span>Name</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <input class="inputElement" id="custom_expenses_name" name="custom_expenses_name" value="{$RECORDENTRIES['custom_expenses_name']}" data-rule-required="true">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_quantity" class="control-label col-sm-3">
                            <span>Quantity</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_expenses_quantity" name="custom_expenses_quantity">
                                <option value="">Select an Option</option>
                                {if $FIELDS_OF_SELECTED_MODULE}
                                    {foreach key=FIELD_NAME item=FIELD_LABEL from=$FIELDS_OF_SELECTED_MODULE}
                                        <option value="{$FIELD_NAME}" {if $RECORDENTRIES['quantity'] eq $FIELD_NAME}selected{/if}>{$FIELD_LABEL}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_price" class="control-label col-sm-3">
                            <span>Price</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_expenses_price" name="custom_expenses_price">
                                <option value="">Select an Option</option>
                                {if $FIELDS_OF_SELECTED_MODULE}
                                    {foreach key=FIELD_NAME item=FIELD_LABEL from=$FIELDS_OF_SELECTED_MODULE}
                                        <option value="{$FIELD_NAME}" {if $RECORDENTRIES['price'] eq $FIELD_NAME}selected{/if}>{$FIELD_LABEL}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_description" class="control-label col-sm-3">
                            <span>Description</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_expenses_description" name="custom_expenses_description">
                                <option value="">Select an Option</option>
                                {if $FIELDS_OF_SELECTED_MODULE}
                                    {foreach key=FIELD_NAME item=FIELD_LABEL from=$FIELDS_OF_SELECTED_MODULE}
                                        <option value="{$FIELD_NAME}" {if $RECORDENTRIES['description'] eq $FIELD_NAME}selected{/if}>{$FIELD_LABEL}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_default_item" class="control-label col-sm-3">
                            <span>Default Item</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-4">
                            <select class="inputElement select2" id="custom_expenses_default_item_module" name="custom_expenses_default_item_module" data-rule-required="true">
                                <option value="{$PRODUCT_MODULE->get('name')}" selected>{vtranslate($PRODUCT_MODULE->get('label'),$PRODUCT_MODULE->get('name'))}</option>
                                <option value="{$SERVICE_MODULE->get('name')}" {if $RECORDENTRIES['default_item_module'] eq $SERVICE_MODULE->get('name')}selected{/if}>{vtranslate($SERVICE_MODULE->get('label'),$SERVICE_MODULE->get('name'))}</option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <div class="fieldValue">
                                <div class="referencefield-wrapper ">
                                    <input name="popupReferenceModule" value="{$PRODUCT_MODULE->get('name')}" type="hidden">
                                    <div class="input-group">
                                        <input name="custom_expenses_default_item_value" value="{$RECORDENTRIES['default_item_value']}" class="sourceField" data-displayvalue="" type="hidden">
                                        <input id="custom_expenses_default_item_value_display" name="custom_expenses_default_item_value_display" data-fieldname="custom_expenses_default_item_value" data-fieldtype="reference" class="marginLeftZero autoComplete inputElement ui-autocomplete-input" value="{$RECORDENTRIES['default_item_label']}" placeholder="Type to search" autocomplete="off" type="text" data-rule-reference_required="true" aria-required="true">
                                        <a href="#" class="clearReferenceSelection hide"> x </a>
                                        <span class="input-group-addon relatedPopup cursorPointer" title="Select">
                                            <i id="{$MODULE}_editView_fieldName_custom_expenses_default_item_value_select" class="fa fa-search"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_status" class="control-label col-sm-3">
                            <span>Status</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_expenses_status" name="custom_expenses_status">
                                <option value="Active" selected="selected">Active</option>
                                <option value="Inactive" {if $RECORDENTRIES['active'] eq 'Inactive'}selected="" {/if}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-6 custom-expenses-info">
                    <div class="label-info">
                        <h5>
                            <span class="glyphicon glyphicon-info-sign"></span> Info
                        </h5>
                    </div>
                    <span>
                        Any module can be configured to be a "Custom Expense". Once the module is configured, it will show up on the in the "+ Add Custom Invoice" picklist on the Invoice.</br></br>
                        <b>Name:</b> Name displayed in "+ Add Custom Expense" selection.<br><br>
                        <b>Quantity:</b> Map item quantity field from selected module. (if left blank, the quantity will default to 1).<br><br>
                        <b>Price: </b>Map item price field from selected module. (if left blank, the quantity will default to 0).<br><br>
                        <b>Description:</b> Map item description from selected module.<br><br>
                        <b>Default Item:</b> Can be a Product or a Service. This field is required.<br><br>
                        <b>Status:</b> Turn expense on or off.
                    </span>
                </div>
            </div>
            <div class="modal-overlay-footer clearfix">
                <div class="row clearfix">
                    <div class="textAlignCenter col-lg-12 col-md-12 col-sm-12 ">
                        <button type="submit" class="btn btn-success buttonSave">Save</button>&nbsp;&nbsp;
                        <a class="cancelLink" href="javascript:history.back()" type="reset">Cancel</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{/strip}