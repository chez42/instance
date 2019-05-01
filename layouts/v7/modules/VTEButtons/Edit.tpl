{*<!--
/* ********************************************************************************
* The content of this file is subject to the Custom Header/Bills ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<style>
    #header-colorpicker{
        position: relative;
        width: 36px;
        height: 36px;
        background: url('layouts/v7/modules/VTEButtons/resources/select.png');
    }
    #header-colorpicker p {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 30px;
        height: 30px;
        background: url('layouts/v7/modules/VTEButtons/resources/select.png') center;
        cursor: pointer;
    }
    .header-input-text{
        padding-left: 5px;
    }
    .rcorners{
        border-radius: 2px;
        padding: 5px 10px;
        width: auto;
        font-size: 13px;
        float: right;
    }

</style>
{strip}
<div class="container-fluid WidgetsManage">
    <link type="text/css" rel="stylesheet" href="libraries/jquery/colorpicker/css/colorpicker.css" media="screen">
    <script type="text/javascript" src="libraries/jquery/colorpicker/js/colorpicker.js"></script>
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('Button Details', 'VTEButtons')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <form id="EditVTEButtons" action="index.php" method="post" name="EditVTEButtons">
    <div class="editViewPageDiv">
        <input type="hidden" name="module" id="module" value="{$MODULE}">
        <input type="hidden" name="action" value="SaveAjax" />
        <input type="hidden" name="record" id="record" value="{$RECORD}">
        <input type="hidden" id="stdfilterlist" name="stdfilterlist" value=""/>
        <input type="hidden" id="advfilterlist" name="advfilterlist" value=""/>
        <input type="hidden" id="strfieldslist" name="strfieldslist" value=""/>
        <div class="col-sm-12 col-xs-12">
            <div class="col-sm-6 col-xs-6 form-horizontal">
                <div class="form-group">
                    <label for="custom_expenses_module" class="control-label col-sm-3">
                        <span>{vtranslate('Module', 'VTEButtons')}</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="col-sm-8">
                        <select class="inputElement select2" id="custom_module" name="custom_module" data-rule-required="true">
                            <option value="">{vtranslate('Select an Option', 'VTEButtons')}</option>
                            {assign var='EXCLUDE_MODULE_ARRAY' value=','|explode:"Quotes,PurchaseOrder,SalesOrder,Services,Products"}
                            {foreach item=MODULE_VALUES from=$ALL_MODULES}
                               {* {if in_array($MODULE_VALUES->name, $EXCLUDE_MODULE_ARRAY)}
                                    {continue}
                                {/if}*}
                                <option value="{$MODULE_VALUES->name}" {if $MODULE_VALUES->name eq $RECORDENTRIES['module']}selected{/if}>{vtranslate($MODULE_VALUES->label,$MODULE_VALUES->name)}</option>
                            {/foreach}
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="custom_expenses_name" class="control-label col-sm-3">
                        <span>{vtranslate('Button Title', 'VTEButtons')}</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="col-sm-8">
                        <input class="inputElement header-input-text" id="header" name="header" value="{$RECORDENTRIES['header']}" data-rule-required="true">
                    </div>
                </div>
                <div class="form-group">
                    <label for="custom_expenses_quantity" class="control-label col-sm-3">
                        <span>{vtranslate('Icon/Label', 'VTEButtons')}</span>
                        <span class="redColor">*</span>
                    </label>
                    <div class="col-sm-8 icon-section">
                        <button type="button" class="btn btn-primary btnicon" data-toggle="modal"
                                data-target="#ModalIcons">
                            {vtranslate('Select Icon', 'VTEButtons')}
                        </button>
                        <span class="icon-module {$RECORDENTRIES['icon']}" id="icon-module" style="font-size: 30px; vertical-align: middle; padding-left: 11px;"></span>
                        <input type="hidden" id="icon" name="icon" value="{$RECORDENTRIES['icon']}">
                        <span id="header-colorpicker">
                            <p style="background-color: #{if $RECORDENTRIES['color'] !=''}{$RECORDENTRIES['color']}{else}1969e8{/if};margin-left: 9px;margin-top: -8px;;"></p>
                        </span>
                        <div class="header-div header-preview-section" style="float: right;">
                            <div class="rcorners" style="float:left;border: 2px solid #{if $RECORDENTRIES['color'] !=''}{$RECORDENTRIES['color']}{else}1969e8{/if};color:#{if $RECORDENTRIES['color'] !=''}{$RECORDENTRIES['color']}{else}1969e8{/if}; ">
                                <span id="icon-span" class="icon-module {$RECORDENTRIES['icon']}" style="font-size: 17px;color: #{$RECORDENTRIES['color']};"></span>
                                <span class="l-header"
                                      style="vertical-align: left; padding-left: 11px;">{$RECORDENTRIES['header']}</span>
                            </div>
                        </div>
                        <input type="hidden" id="color" name="color" value="{if $RECORDENTRIES['color'] !=''}{$RECORDENTRIES['color']}{else}1969e8{/if}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="sequence" class="control-label col-sm-3">
                        <span>{vtranslate('Sequence', 'VTEButtons')}</span>
                     </label>
                    <div class="col-sm-8">
                        <input class="inputElement header-input-text" id="sequence" name="sequence" value="{$RECORDENTRIES['sequence']}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="active" class="control-label col-sm-3">
                        <span>{vtranslate('Active', 'VTEButtons')}</span>
                    </label>
                    <div class="col-sm-8">
                        <select class="inputElement select2" id="active" name="active">
                            <option value="Active" selected="selected">Active</option>
                            <option value="Inactive" {if $RECORDENTRIES['active'] eq 'Inactive'}selected="" {/if}>Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xs-6 custom-header-info">
                <div class="label-info">
                    <h5>
                        <span class="glyphicon glyphicon-info-sign"></span> Info
                    </h5>
                </div>
                <span>
                    Once the module is configured, the buttons will show up on the record detail view.<br><br>
                    <b>Button Title:</b> Name of button (i.e Update Address).<br><br>
                    <b>Icon/Label:</b> Icon will be displayed in front of button/value.<br><br>
                    <b>Sequence:</b> You can sequence in which buttons show up.<br><br>
                    <b>Status:</b> Turn this button on or off.<br><br>
                    <b>Fields:</b> Select fields to be displayed when then button is clicked.<br><br>
                    <b>Condition (optional):</b> Specify condition when the button should be shown. For example, show button "Update Address" if Billing Street, City, State is empty.
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
    </div>
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('Modal Popup', 'VTEButtons')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="editViewPageDiv">
        <div class="col-sm-12 col-xs-12 form-horizontal">
            <div class="form-group">
                <label for="custom_expenses_quantity" class="control-label col-sm-2">
                    <span>{vtranslate('Fields', 'VTEButtons')}</span>
                    <span class="redColor">*</span>
                </label>
                <div class="col-sm-10 columnsSelectDiv">
                    <select data-placeholder="Select columns" multiple class="select2 columnsSelect select2-offscreen" id="field_name" name="field_name" style="width:100%;" data-rule-required="true">
                        {foreach item=FIELD_NAME from=$RECORDENTRIES['field_name']}
                            {assign var=CUR_FIELD_LABEL value={$MODULE_MODEL->getFieldLabel({$SELECTED_MODULE_NAME},{$FIELD_NAME})}}
                            <option value="{$FIELD_NAME}" data-field-name="{$FIELD_NAME}" selected>{vtranslate($CUR_FIELD_LABEL, $SOURCE_MODULE)}</option>
                        {/foreach}
                        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                            {if $BLOCK_LABEL eq 'LBL_ITEM_DETAILS'}{continue}{/if}
                            <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                                {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                    {if in_array($FIELD_NAME, $RECORDENTRIES['field_name'])}
                                        {continue}
                                    {/if}
                                    {assign var=CUR_FIELD_NAME value=$FIELD_NAME|substr:0:6}
                                    {if $FIELD_MODEL->get('uitype') eq "72" || $FIELD_MODEL->get('uitype') eq "83" || $CUR_FIELD_NAME eq "cf_acf" || $FIELD_NAME eq 'cf_team'|| $FIELD_NAME eq 'cf_teammembers'}{continue}{/if}
                                    {if $FIELD_MODEL->isEditable() eq true}
                                    <option value="{$FIELD_NAME}" data-field-name="{$FIELD_NAME}">{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}</option>
                                    {/if}
                                {/foreach}
                            </optgroup>
                        {/foreach}
                    </select>
                   {if $RECORDENTRIES['field_name'] !=''}
                        <input type="hidden" name="selected_fields" value="{$RECORDENTRIES['field_name']}">
                    {/if}
                </div>
            </div>
            <div class="form-group marginBottom10px">
                <label for="custom_expenses_quantity" class="control-label col-sm-2">
                    <span>{vtranslate('Condition (optional)', 'VTEButtons')}</span>
                </label>
                <div class="col-sm-10 row ">
                    <div class="col-sm-12 vte-advancefilter">
                        <div class="" id="table-conditions" >
                            {include file='AdvanceFilter.tpl'|@vtemplate_path MODULE='Vtiger'}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
{/strip}
<!-- Modal -->
<div class="modal fade" id="ModalIcons" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document" style="width: 650px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Modal Header</h4>
            </div>
            <div class="modal-body">
                <div class="form">
                    {assign var=LISTICONS_LENGTH value=(count($LISTICONS) -1)}
                    {assign var=INDEX value = 0 }
                    <table data-length="{$LISTICONS_LENGTH}" border="1px solid #cccccc">
                        {foreach from = $LISTICONS item =val key=k }
                            {assign var=MODE4OK value=(($INDEX mod 14) == 0)}
                            {if $MODE4OK}
                                <tr>
                            {/if}
                            <td style="padding: 5px;" class="cell-icon">
                                <span class="{$k} icon-module" style="font-size: 30px; vertical-align: middle;" data-info="{$val}"></span>
                            </td>
                            {if ($INDEX mod 14) == 13 or $LISTICONS_LENGTH == $INDEX}
                                </tr>
                            {/if}
                            <input type="hidden" value="{$INDEX++}">

                        {/foreach}

                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary btn-submit">Save</button>
            </div>
        </div>
    </div>
</div>