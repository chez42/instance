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
        background: url('layouts/v7/modules/VTECustomHeader/resources/select.png');
    }
    #header-colorpicker p {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 30px;
        height: 30px;
        background: url('layouts/v7/modules/VTECustomHeader/resources/select.png') center;
        cursor: pointer;
    }
    .header-input-text{
        padding-left: 5px;
    }
    .rcorners{
        border-radius: 5px;
        padding: 10px;
        width: 40px;
        height: 40px;
        float: left;
    }
</style>
{strip}
<div class="container-fluid WidgetsManage">
    <link type="text/css" rel="stylesheet" href="libraries/jquery/colorpicker/css/colorpicker.css" media="screen">
    <script type="text/javascript" src="libraries/jquery/colorpicker/js/colorpicker.js"></script>
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('Custom Header', 'VTECustomHeader')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="editViewPageDiv">
        <form id="EditView" action="index.php" method="post" name="EditVTECustomHeader">
            <input type="hidden" name="module" id="module" value="{$MODULE}">
            <input type="hidden" name="action" value="SaveCustomHeader" />
            <input type="hidden" name="record" id="record" value="{$RECORD}">
            <div class="col-sm-12 col-xs-12">
                <div class="col-sm-6 col-xs-6 form-horizontal">
                    <div class="form-group">
                        <label for="custom_expenses_module" class="control-label col-sm-3">
                            <span>{vtranslate('Module', 'VTECustomHeader')}</span>
                            <span class="redColor">*</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="custom_module" name="custom_module" data-rule-required="true">
                                <option value="">{vtranslate('Select an Option', 'VTECustomHeader')}</option>
                                {foreach item=MODULE_VALUES from=$ALL_MODULES}
                                    <option value="{$MODULE_VALUES->name}" {if $MODULE_VALUES->name eq $RECORDENTRIES['module']}selected{/if}>{vtranslate($MODULE_VALUES->label,$MODULE_VALUES->name)}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_name" class="control-label col-sm-3">
                            <span>{vtranslate('Header Title', 'VTECustomHeader')}</span>
                        </label>
                        <div class="col-sm-8">
                            <input class="inputElement header-input-text" id="header" name="header" value="{$RECORDENTRIES['header']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_quantity" class="control-label col-sm-3">
                            <span>{vtranslate('Icon', 'VTECustomHeader')}</span>
                        </label>
                        <div class="col-sm-8 icon-section">
                            <button type="button" class="btn btn-primary" data-toggle="modal"
                                    data-target="#ModalIcons">
                                {vtranslate('Select Icon', 'VTECustomHeader')}
                            </button>
                            <span class="icon-module {$RECORDENTRIES['icon']}" id="icon-module" style="font-size: 30px; vertical-align: middle; padding-left: 11px;"></span>
                            <input type="hidden" id="icon" name="icon" value="{$RECORDENTRIES['icon']}">
                            <span id="header-colorpicker">
                                <p style="background-color: #{$RECORDENTRIES['color']};margin-left: 9px;margin-top: -8px;;"></p>
                            </span>
                            <input type="hidden" id="color" name="color" value="{$RECORDENTRIES['color']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="custom_expenses_quantity" class="control-label col-sm-3">
                            <span>{vtranslate('Field', 'VTECustomHeader')}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="field_name" name="field_name">
                                <option value="none">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
                                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
                                    <optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
                                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
                                            {assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
                                            {assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
                                            {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
                                            {if !empty($COLUMNNAME_API)}
                                                {assign var=columnNameApi value=$COLUMNNAME_API}
                                            {else}
                                                {assign var=columnNameApi value=getCustomViewColumnName}
                                            {/if}
                                            <option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
                                                    {if decode_html($FIELD_MODEL->$columnNameApi()) eq decode_html($RECORDENTRIES['field_name'])}
                                                        {assign var=FIELD_TYPE value=$FIELD_MODEL->getFieldType()}
                                                        {assign var=SELECTED_FIELD_MODEL value=$FIELD_MODEL}
                                                        {if $FIELD_MODEL->getFieldDataType() == 'reference'  ||  $FIELD_MODEL->getFieldDataType() == 'multireference'}
                                                            {$FIELD_TYPE='V'}
                                                        {/if}
                                                        {$FIELD_INFO['value'] = decode_html($RECORDENTRIES['field_name'])}
                                                        selected="selected"
                                                    {/if}
                                                    {if ($MODULE_MODEL->get('name') eq 'Calendar' || $MODULE_MODEL->get('name') eq 'Events') && ($FIELD_NAME eq 'recurringtype')}
                                                        {assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
                                                        {$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
                                                    {/if}
                                                    {if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'activitytype')}
                                                        {$FIELD_INFO['picklistvalues']['Task'] = vtranslate('Task', 'Calendar')}
                                                    {/if}
                                                    {if $FIELD_MODEL->getFieldDataType() eq 'reference'}
                                                        {assign var=referenceList value=$FIELD_MODEL->getWebserviceFieldObject()->getReferenceList()}
                                                        {if is_array($referenceList) && in_array('Users', $referenceList)}
                                                            {assign var=USERSLIST value=array()}
                                                            {assign var=CURRENT_USER_MODEL value = Users_Record_Model::getCurrentUserModel()}
                                                            {assign var=ACCESSIBLE_USERS value = $CURRENT_USER_MODEL->getAccessibleUsers()}
                                                            {foreach item=USER_NAME from=$ACCESSIBLE_USERS}
                                                                {$USERSLIST[$USER_NAME] = $USER_NAME}
                                                            {/foreach}
                                                            {$FIELD_INFO['picklistvalues'] = $USERSLIST}
                                                            {$FIELD_INFO['type'] = 'picklist'}
                                                        {/if}
                                                    {/if}
                                                    data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}'
                                                    {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
                                                {if $SOURCE_MODULE neq $MODULE_MODEL->get('name')}
                                                    ({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))}) {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
                                                {else}
                                                    {vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
                                                {/if}
                                            </option>
                                        {/foreach}
                                    </optgroup>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sequence" class="control-label col-sm-3">
                            <span>{vtranslate('Sequence', 'VTECustomHeader')}</span>
                         </label>
                        <div class="col-sm-8">
                            <input class="inputElement header-input-text" id="sequence" name="sequence" value="{$RECORDENTRIES['sequence']}">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="active" class="control-label col-sm-3">
                            <span>{vtranslate('Active', 'VTECustomHeader')}</span>
                        </label>
                        <div class="col-sm-8">
                            <select class="inputElement select2" id="active" name="active">
                                <option value="Active" selected="selected">Active</option>
                                <option value="Inactive" {if $RECORDENTRIES['active'] eq 'Inactive'}selected="" {/if}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="preview" class="control-label col-sm-3">
                            <span>{vtranslate('Preview', 'VTECustomHeader')}</span>
                        </label>
                        <div class="header-div col-sm-8 header-preview-section">
                            <div class="rcorners" style="float:left;border: 2px solid #{$RECORDENTRIES['color']};">
                                <span id="icon-span" class="icon-module {$RECORDENTRIES['icon']}" style="font-size: 17px;color: #{$RECORDENTRIES['color']};"></span>
                            </div>
                            <div>
                                        <span class="l-header muted"
                                              style="vertical-align: left; padding-left: 11px;">{$RECORDENTRIES['header']}</span><br />
                                        <span class="l-value"
                                              style="vertical-align: left; padding-left: 11px;">{$RECORDENTRIES['field_label']}</span>
                            </div>
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
                        Once the module is configured, the headers will show up on the record detail view.</br></br>
                                                <b>Header Title:</b> Name of header (i.e Sales Rep).<br><br>
                        <b>Icon:</b> Icon will be displayed in front of header/value.<br><br>
                        <b>Field: </b>Select the field you would like to display on the header.<br><br>
                        <b>Sequence:</b> You can sequence in which headers show up.<br><br>
                        <b>Status:</b> Turn this header on or off.
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