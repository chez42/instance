{*<!--
/* ********************************************************************************
* The content of this file is subject to the Progressbar/Bills ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
{strip}
<div class="container-fluid WidgetsManage">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('Progress Bar', 'VTEProgressbar')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="editViewPageDiv">
        <form id="EditView" action="index.php" method="post" name="EditVTEProgressbar">
            <input type="hidden" name="module" id="module" value="{$MODULE}">
            <input type="hidden" name="action" value="SaveProgressbar" />
            <input type="hidden" name="record" id="record" value="{$RECORD}">
            <div class="col-sm-12 col-xs-12">
                <div class="col-sm-6 col-xs-6 form-horizontal">
                    <div class="form-group">
                        <label for="custom_expenses_module" class="control-label col-sm-3">
                            <span>{vtranslate('Module', 'VTEProgressbar')}</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_module" name="custom_module" data-rule-required="true">
                                <option value="">{vtranslate('Select an Option', 'VTEProgressbar')}</option>
                                {foreach item=MODULE_VALUES from=$ALL_MODULES}
                                    <option value="{$MODULE_VALUES->name}" {if $MODULE_VALUES->name eq $RECORDENTRIES['module']}selected{/if}>{vtranslate($MODULE_VALUES->label,$MODULE_VALUES->name)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_quantity" class="control-label col-sm-3">
                            <span>{vtranslate('Field', 'VTEProgressbar')}</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="field_name" name="field_name"  data-rule-required="true">
                                <option value="">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
                                {assign var=RELATED_FIELDS value = array('15','16','33')}
                                {assign var=EXCLUDED_FIELDS value = array('hdnTaxType','region_id')}
                                {foreach from=$SELECTED_MODULE_FIELDS key=FIELD_LBL item=BLOCK}
                                    {assign var=FIELD_ACTIVE value=$BLOCK -> get('presence')}
                                    {if $FIELD_ACTIVE != 1 && $BLOCK ->get('uitype')|in_array:$RELATED_FIELDS && !$FIELD_LBL|in_array:$EXCLUDED_FIELDS}
                                        <option value="{Vtiger_Util_Helper::toSafeHTML($FIELD_LBL)}"
                                                {if $FIELD_LBL eq decode_html($RECORDENTRIES['field_name'])}
                                            selected="selected"
                                                {/if}>
                                            {vtranslate($BLOCK ->get("label"),$SELECTED_MODULE_NAME)}
                                        </option>
                                    {/if}
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="active" class="control-label col-sm-3">
                            <span>{vtranslate('Status', 'VTEProgressbar')}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="active" name="active">
                                <option value="Active" {if $RECORDENTRIES['active'] === 1 || empty($RECORDENTRIES['active'])}selected{/if}>Active</option>
                                <option value="Inactive" {if $RECORDENTRIES['active'] === 0}selected{/if}>Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xs-6 predictive-field-info">
                    <div class="label-info">
                        <h5>
                            <span class="glyphicon glyphicon-info-sign"></span> Info
                        </h5>
                    </div>
                    <span>
                        Once the module is configured, the Progress Bar will show up on the record detail view.</br></br>
                        <b>Field: </b>Select the field you would like to display on the Progress Bar.<br><br>
                        <b>Status:</b> Turn this Progress Bar on or off.
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
