{*/* ********************************************************************************
* The content of this file is subject to the Hide Fields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
    <tr class="HideFieldsHeader">
        {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
        <td colspan="4" style="padding: 0px;">
            <div style="overflow-x: auto; overflow-y: hidden" class="scollBarData">
            <table class="table table-bordered table-condensed listViewEntriesTable" >
                <thead>
                <tr class="listViewHeaders">
                    <th width="2%">&nbsp;
                        <input type="hidden" id="selected_fields{$BLOCK_ID}" />
                        <input type="hidden" id="multipicklist_fields{$BLOCK_ID}" />
                    </th>
                    {foreach item=FIELD_MODEL from=$SELECTED_FIELDS_MODEL name=selected_fields}
                        <th style="min-width: 180px;" class="fieldLabel {$WIDTHTYPE}" {if $FIELD_MODEL@last} colspan="2" {/if}><strong>{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}</strong></th>
                        <script>
                            jQuery('#selected_fields'+{$BLOCK_ID}).val(jQuery('#selected_fields'+{$BLOCK_ID}).val()+',{$FIELD_MODEL->getFieldName()}');
                            {if $FIELD_MODEL->get('uitype') eq '33'}
                            jQuery('#multipicklist_fields'+{$BLOCK_ID}).val(jQuery('#multipicklist_fields'+{$BLOCK_ID}).val()+',{$FIELD_MODEL->getFieldName()}');
                            {/if}
                        </script>
                    {/foreach}
                </tr>
                </thead>
                <tbody class="ui-sortable" id="dataTable{$BLOCK_ID}" data-block-id="{$BLOCK_ID}">
                    {if $BLOCKS_DATA|count >0}
                        {foreach item=ROW_DATA key=ROW_ID from=$BLOCKS_DATA name=block_data}
                            {*{assign var="rowNo" value=$smarty.foreach.block_data.iteration}*}
                            <tr id="row{$ROW_ID}" class="rowDataItem blockSortable" >
                                <td class="fieldValue" width="2%">
                                    {*<img title="Drag" class="alignTop" src="layouts/vlayout/skins/images/drag.png">*}
                                    <input type="hidden" value="{$ROW_ID}" class="rowNumber">
                                </td>
                                {foreach item=FIELD_MODEL from=$SELECTED_FIELDS_MODEL name=selected_fields}
                                    {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$ROW_DATA[$FIELD_MODEL->getFieldName()])}
                                    {if $FIELD_MODEL->get('uitype') neq "83"}
                                        <td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {/if}>
                                            <div class="row-fluid">
                                             <span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                                {$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
                                             </span>
                                            {if $FIELD_MODEL->isEditable() eq 'true' && ($FIELD_MODEL->getFieldDataType()!=Vtiger_Field_Model::REFERENCE_TYPE)}
                                                <span class="hide edit">
                                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$SOURCE_MODULE) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$SOURCE_MODULE}

                                                    <br />
                                                    <a href="javascript:void(0);" data-field-name="{$FIELD_MODEL->getFieldName()}{if $FIELD_MODEL->get('uitype') eq '33'}[]{/if}" data-row-id="{$ROW_ID}" data-block-id="{$BLOCK_ID}" data-record-id="{$RECORD_ID}" class="hoverEditSave">{vtranslate('LBL_SAVE')}</a> |
                                                    <a href="javascript:void(0);" class="hoverEditCancel">{vtranslate('LBL_CANCEL')}</a>
                                                </span>
                                            {/if}
                                            </div>
                                        </td>
                                    {/if}
                                {/foreach}
                            </tr>
                        {/foreach}
                    {else}
                    {/if}
                </tbody>
            </table>
            </div>
        </td>
    </tr>
{/strip}