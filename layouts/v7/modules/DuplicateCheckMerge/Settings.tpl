
<div class="widget_header container-fluid" style="padding-bottom: 20px;">
    <div class="col-lg-12 col-md-12 col-sm-12">
        <h3>
            <a href="index.php?module=ModuleManager&parent=Settings&view=List">&nbsp;{vtranslate('MODULE_MANAGEMENT',$MODULE_LBL)}</a>&nbsp;>&nbsp;{$MODULE_LBL}
        </h3>
    </div>
</div>
<form action="" enctype="multipart/form-data" method="POST" onsubmit="saveFieldsSetting(this);"/>
    <div class="dupecheck-setting container-fluid">
        <div class="col-lg-12 col-md-12 col-sm-12 select-modules" style="text-align: center; padding: 5px 0;">
            <button type="button" class="btn btn-primary" onclick="dcmAddModule(this)">
                <strong style="color: #fff;">{vtranslate('SELECT_THE_MODULES', $MODULE_LBL)}</strong>
            </button>
        </div>

        <div class="list-modules">
        {foreach item=MODULE_SETTING key=MODULE_SETTING_NAME from=$MODULE_SETTINGS}
            <div class="list-module-block">
                <div class="col-lg-12 col-md-12 col-sm-12"  style="width: 95%; padding: 10px 0 10px; border-top: 1px dotted #CCC; margin: 20px 0 0; text-align: center;">
                    <h3>{$MODULE_SETTING.tablabel}</h3>
                </div>
                <div class="col-lg-12 col-md-12 col-sm-12" style="width: 95%;">
                    <table class="table table-borderless">
                        <thead>
                            <tr>
                                <td>{vtranslate('SELECT_THE_FIELD_LBL', $MODULE_LBL)}</td>
                                <td align="center">
                                    {vtranslate('CROSS_CHECK_LBL', $MODULE_LBL)}
                                    <a href="javascript:infoPopup('info/cross_check.html')">
                                        <img src="layouts/vlayout/modules/DuplicateCheckMerge/resources/info.png" title="More info" />
                                    </a>
                                </td>
                                <td align="center">
                                    {vtranslate('ALLOW_DUPLICATES_LBL', $MODULE_LBL)}
                                    <a href="javascript:infoPopup('layouts/vlayout/modules/DuplicateCheckMerge/info/allow_duplicates.html')">
                                        <img src="layouts/vlayout/modules/DuplicateCheckMerge/resources/info.png" title="More info" />
                                    </a>
                                </td>
                                <td align="center">
                                    {vtranslate('REAL_TIME_AS_YOU_TYPE_LBL', $MODULE_LBL)}
                                    <a href="javascript:infoPopup('info/realtime_asyoutype.html')">
                                        <img src="layouts/vlayout/modules/DuplicateCheckMerge/resources/info.png" title="More info" />
                                    </a>
                                </td>
                                <td align="center">
                                    {vtranslate('REAL_TIME_OPENING_THE_RECORD_LBL', $MODULE_LBL)}
                                    <a href="javascript:infoPopup('info/realtime_openingtherecord.html')">
                                        <img src="layouts/vlayout/modules/DuplicateCheckMerge/resources/info.png" title="More info" />
                                    </a>
                                </td>
                                <td align="center">
                                    {vtranslate('PASSIVE_CLICKING_THE_ICON_LBL', $MODULE_LBL)}
                                    <a href="javascript:infoPopup('info/passive.html')">
                                        <img src="layouts/vlayout/modules/DuplicateCheckMerge/resources/info.png" title="More info" />
                                    </a>
                                </td>
                                <td align="center">
                                    {vtranslate('DUPLICATE_MERGE_LBL', $MODULE_LBL)}
                                    <a href="javascript:infoPopup('info/duplicatemerge.html')">
                                        <img src="layouts/vlayout/modules/DuplicateCheckMerge/resources/info.png" title="More info" />
                                    </a>
                                </td>
                                <td></td>
                            </tr>
                        </thead>
                        <tbody>
                        {if $MODULE_SETTING.fields|@count gt 0}
                            {foreach item=field key=i from=$MODULE_SETTING.fields.fields}
                            <tr>
                                <td valign="top">
                                    <select class="select2 list-fields" data-modulename="{$MODULE_SETTING_NAME}" name="dcm_setting[{$MODULE_SETTING_NAME}][fields][]">
                                    {foreach item=FIELD from=$MODULE_SETTING.list_field}
                                        <option value="{$FIELD->getFieldId()}" {if $field eq $FIELD->getFieldId()}selected{/if}>
                                            {vtranslate($FIELD->getFieldLabelKey(),$MODULE_SETTING_NAME)}
                                        </option>
                                    {/foreach}
                                    </select>
                                </td>
                                <td align="center" valign="top">
                                    <select name="dcm_setting[{$MODULE_SETTING_NAME}][crosscheck][{$field}][]" data-placeholder="{vtranslate('SELECT_SOME_OPTIONS_CROSSCHECK', $MODULE_LBL)}" multiple class="select2 crosscheck" style="width: 250px;">
                                        {foreach item=CROSSCHECK_FIELD from=$MODULE_SETTING.fields.crosscheck.$field}
                                            <option value="{$CROSSCHECK_FIELD.fieldid}" {if $CROSSCHECK_FIELD.selected eq 1}selected{/if} >
                                                {$CROSSCHECK_FIELD.fieldlabel}
                                            </option>
                                        {/foreach}
                                    </select>
                                </td>
                                <td align="center" valign="top">
                                    <select name="dcm_setting[{$MODULE_SETTING_NAME}][ad][]" class="select2" style="width: 75px;">
                                        <option value="1" {if $MODULE_SETTING.fields.ad[$i] eq 1}selected{/if}>Yes</option>
                                        <option value="0" {if $MODULE_SETTING.fields.ad[$i] eq 0}selected{/if}>No</option>
                                    </select>
                                </td>
                                <td align="center" valign="top">
                                    <select name="dcm_setting[{$MODULE_SETTING_NAME}][rtayt][]" class="select2" style="width: 75px;">
                                        <option value="1" {if $MODULE_SETTING.fields.rtayt[$i] eq 1}selected{/if}>Yes</option>
                                        <option value="0" {if $MODULE_SETTING.fields.rtayt[$i] eq 0}selected{/if}>No</option>
                                    </select>
                                </td>
                                <td align="center" valign="top">
                                    <select name="dcm_setting[{$MODULE_SETTING_NAME}][rtotr][]" class="select2 real-time-opening-the-record" style="width: 75px;">
                                        <option value="1" {if $MODULE_SETTING.fields.rtotr[$i] eq 1}selected{/if}>Yes</option>
                                        <option value="0" {if $MODULE_SETTING.fields.rtotr[$i] eq 0}selected{/if}>No</option>
                                    </select>
                                </td>
                                <td align="center" valign="top">
                                    <select name="dcm_setting[{$MODULE_SETTING_NAME}][passive][]" class="select2 passive" style="width: 75px;" {if $MODULE_SETTING.fields.rtotr[$i] eq 1}disabled{/if}>
                                        <option value="1" {if $MODULE_SETTING.fields.passive[$i] eq 1}selected{/if}>Yes</option>
                                        <option value="0" {if $MODULE_SETTING.fields.passive[$i] eq 0}selected{/if}>No</option>
                                    </select>
                                </td>
                                <td align="center" valign="top">
                                    <select name="dcm_setting[{$MODULE_SETTING_NAME}][duplicate_merge][]" class="select2" style="width: 75px;">
                                        <option value="1" {if $MODULE_SETTING.fields.duplicate_merge[$i] eq 1}selected{/if}>Yes</option>
                                        <option value="0" {if $MODULE_SETTING.fields.duplicate_merge[$i] eq 0}selected{/if}>No</option>
                                    </select>
                                </td>
                                <td valign="top">
                                    <a class="deleteRecordButton" href="javascript:void(0);" onclick="removeFieldRow(this, '{$MODULE_SETTING_NAME}'); return false;">
                                        <i class="icon-trash alignMiddle" title="Delete"></i>
                                    </a>
                                </td>
                            </tr>
                            {/foreach}
                        {/if}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button class="btn btn-default addButton" type="button" onclick="addNewField(this,list_field_{$MODULE_SETTING_NAME}, '{$MODULE_SETTING_NAME}'); return false;">
                                        <strong>{vtranslate('ADD_NEW_FIELD_BTN', $MODULE_LBL)}</strong>
                                    </button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        {/foreach}
        </div>

        <div class="col-lg-12 col-md-12 col-sm-12 dcm-save-btn" style="text-align: center; padding: 10px 0;">
            <button class="btn btn-success" type="submit"><strong>{vtranslate('SAVE_BTN', $MODULE_LBL)}</strong></button>
        </div>
    </div>
    <input type="hidden" name="module" value="DuplicateCheckMerge" />
    <input type="hidden" name="action" value="Settings" />
    <input type="hidden" name="task" value="saveFieldSetting" />
</form>
<div class="clearfix"></div>

{literal}
<script type="text/javascript">
    function dcmAddModule(obj){
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            data: {
                module: "DuplicateCheckMerge",
                view: "Settings",
                layout: "getModuleSetting"
            },
            success:function (response) {
                if(response.result.success){
                    app.helper.showModal(response.result.data);
                }
            }
        });
    }

    function saveFieldsSetting(obj){
        jQuery('select').each(function(){
            jQuery(this).removeAttr('disabled');
            jQuery(this).trigger("liszt:updated");
        });

    }

    function saveModuleSetting(obj){
        jQuery.ajax({
            type: "POST",
            url: "index.php",
            dataType: 'json',
            data: jQuery(obj).serialize(),
            success:function (response) {
                location.reload();
            }
        });
    }

    function addNewField(obj, list_field, module_name){
								
        var list_field_first = jQuery(list_field).find('option:first');
        var cross_check = '<select name="dcm_setting['+module_name+'][crosscheck]['+list_field_first.val()+'][]" multiple class="select2 crosscheck" style="width: 250px;">';
        cross_check += all_fields_html;
        cross_check += '</select>';
        var field_row = '';
        field_row += '<tr>';
        field_row +=     '<td>';
        field_row +=         list_field;
        field_row +=     '</td>';
        field_row +=     '<td align="center" >';
        field_row +=         cross_check;
        field_row +=     '</td>';
        field_row +=     '<td align="center" >';
        field_row +=         '<select class="select2" name="dcm_setting['+module_name+'][ad][]" style="width: 75px;">';
        field_row +=            '<option value="1">Yes</option>';
        field_row +=            '<option value="0">No</option>';
        field_row +=         '</select>';
        field_row +=     '</td>';
        field_row +=     '<td align="center">';
        field_row +=         '<select class="select2" name="dcm_setting['+module_name+'][rtayt][]" style="width: 75px;">';
        field_row +=            '<option value="1">Yes</option>';
        field_row +=            '<option value="0">No</option>';
        field_row +=         '</select>';
        field_row +=     '</td>';
        field_row +=     '<td align="center">';
        field_row +=         '<select class="select2 real-time-opening-the-record" name="dcm_setting['+module_name+'][rtotr][]" style="width: 75px;">';
        field_row +=            '<option value="1">Yes</option>';
        field_row +=            '<option value="0">No</option>';
        field_row +=         '</select>';
        field_row +=     '</td>';
        field_row +=     '<td align="center">';
        field_row +=         '<select class="select2 passive" name="dcm_setting['+module_name+'][passive][]" disabled style="width: 75px;">';
        field_row +=            '<option value="1">Yes</option>';
        field_row +=            '<option value="0" selected>No</option>';
        field_row +=         '</select>';
        field_row +=     '</td>';
        field_row +=     '<td align="center">';
        field_row +=         '<select class="select2" name="dcm_setting['+module_name+'][duplicate_merge][]" style="width: 75px;">';
        field_row +=            '<option value="1">Yes</option>';
        field_row +=            '<option value="0">No</option>';
        field_row +=         '</select>';
        field_row +=     '</td>';
        field_row +=     '<td>';
        field_row +=         '<a class="deleteRecordButton" href="javascript:void(0);" onclick="removeFieldRow(this, \''+module_name+'\'); return false;"><i class="icon-trash alignMiddle" title="Delete"></i></a>';
        field_row +=     '</td>';
        field_row += '</tr>';
        //add to end of list
        jQuery(obj).parents('table').find('tbody').append(field_row);
        vtUtils.applyFieldElementsView(jQuery(obj).parents('table').find('tbody tr:last-child'));
    }

    function removeFieldRow(obj, module_name){
        if(jQuery(obj).parents('tbody').find('tr').length == 1){
            jQuery.ajax({
                type: "POST",
                url: "index.php",
                dataType: 'json',
                data: {
                    module: "DuplicateCheckMerge",
                    action: "Settings",
                    task: "removeModule",
                    pmodule: module_name
                },
                success:function (response) {
                    jQuery(obj).parents('div.list-module-block').remove();
                }
            });
        }else{
            jQuery(obj).parents('tr').remove();
        }
    }

    function infoPopup(url) {
        var params = {};
        params.data={
            module: app.getModuleName(),
            view: 'GetInfo',
            temp:url
        }
        app.request.get(params).then(
                function (err,data) {
                    app.helper.showModal(data);
                }
        );
    }

    jQuery(document).ready(function(){
        var container = jQuery('.dupecheck-setting');
        vtUtils.applyFieldElementsView(container);
        jQuery(document).on('change', '.real-time-opening-the-record', function() {
            var passiveElement = jQuery(this).parents('tr').find('select.passive');
            if(this.value==1){
                passiveElement.prop("selectedIndex", 1);
                passiveElement.attr('disabled', true);
            }else{
                passiveElement.removeAttr('disabled');
            }
            passiveElement.trigger("change");

        });
        jQuery(document).on('change', '.list-fields', function() {
            var crosscheck = jQuery(this).parents('tr').find('select.crosscheck');
            crosscheck.attr('name', 'dcm_setting['+jQuery(this).data('modulename')+'][crosscheck]['+this.value+'][]');
            crosscheck.trigger("change");
        });
    });
</script>
{/literal}
<script type="text/javascript">
{foreach item=MODULE_SETTING key=MODULE_SETTING_NAME from=$MODULE_SETTINGS}
    var list_field_{$MODULE_SETTING_NAME} = '{$MODULE_SETTING.list_field_html}';
{/foreach}
var all_fields_html = '{$ALL_FIELDS_HTML}';
</script>