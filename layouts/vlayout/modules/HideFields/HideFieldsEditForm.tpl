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
    {assign var=COUNT value=$SELECTED_FIELDS_MODEL|count}
    {assign var=CELLWIDTH value=98/$COUNT}
    <td colspan="4" style="padding: 0px;">
        <div style="overflow-x: auto; overflow-y: hidden" class="scollBarData">
        <table class="table table-bordered table-condensed listViewEntriesTable">
            <thead>
            <tr class="listViewHeaders">
                <th width="2%">&nbsp;</th>

                {foreach item=FIELD_MODEL from=$SELECTED_FIELDS_MODEL name=selected_fields}
                    {if $FIELD_MODEL->isEditable() eq 'true'}
                        <th width="{$CELLWIDTH}%" style="min-width: 180px;" class="fieldLabel {$WIDTHTYPE}" {if $FIELD_MODEL@last} colspan="2" {/if}><strong>{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}</strong></th>
                        <script>
                            jQuery('#selected_fields'+{$BLOCK_ID}).val(jQuery('#selected_fields'+{$BLOCK_ID}).val()+',{$FIELD_MODEL->getFieldName()}');
                            {if $FIELD_MODEL->get('uitype') eq '33'}
                                jQuery('#multipicklist_fields'+{$BLOCK_ID}).val(jQuery('#multipicklist_fields'+{$BLOCK_ID}).val()+',{$FIELD_MODEL->getFieldName()}');
                            {/if}
                        </script>
                    {/if}
                {/foreach}
            </tr>
            </thead>
            <tbody class="ui-sortable" id="dataTable{$BLOCK_ID}" data-block-id="{$BLOCK_ID}">
                <tr class="blockSortableClone hide">
                    <td class="fieldValue" width="2%">
                        <img title="Drag" class="alignTop" src="layouts/vlayout/skins/images/drag.png">
                        <input type="hidden" value="0" class="rowNumber">
                    </td>
                    {foreach item=FIELD_MODEL from=$SELECTED_FIELDS_MODEL name=selected_fields}
                        {if $FIELD_MODEL->isEditable() eq 'true'}
                            <td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$SOURCE_MODULE) BLOCK_FIELDS=$SELECTED_FIELDS_MODEL MODULE=$SOURCE_MODULE}
                                {if $FIELD_MODEL@last}
                                    <div class="actions pull-right" style="padding-top:7px; padding-right:10px;">
                                        &nbsp;<a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                    </div>
                                {/if}
                            </td>
                        {/if}
                    {/foreach}
                </tr>
                {if $BLOCKS_DATA|count >0}
                    {foreach item=ROW_DATA key=ROW_ID from=$BLOCKS_DATA name=block_data}
                        {assign var="rowNo" value=$smarty.foreach.block_data.iteration}
                        <tr id="row{$rowNo}" class="rowDataItem blockSortable">
                            <td class="fieldValue" width="2%">
                                <img title="Drag" class="alignTop" src="layouts/vlayout/skins/images/drag.png">
                                <input type="hidden" value="{$rowNo}" class="rowNumber">
                            </td>
                            {foreach item=FIELD_MODEL from=$SELECTED_FIELDS_MODEL name=selected_fields}
                                {assign var=FIELD_MODEL value=$FIELD_MODEL->set('fieldvalue',$ROW_DATA[$FIELD_MODEL->getFieldName()])}
                                {if $FIELD_MODEL->isEditable() eq 'true'}
                                    <td width="{$CELLWIDTH}%" style="min-width: 180px;" class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$SOURCE_MODULE) BLOCK_FIELDS=$SELECTED_FIELDS_MODEL MODULE=$SOURCE_MODULE}
                                        {if $smarty.foreach.selected_fields.iteration eq $SELECTED_FIELDS_MODEL|count}
                                            <div class="actions pull-right" style="padding-top:7px; padding-right:10px;">
                                                &nbsp;<a class="deleteRow"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                            </div>
                                        {/if}
                                    </td>
                                {/if}
                            {/foreach}
                        </tr>
                    {/foreach}
                {else}
                    <tr id="row1" class="rowDataItem blockSortable">
                        <td class="fieldValue">
                            <img title="Drag" class="alignTop" src="layouts/vlayout/skins/images/drag.png">
                            <input type="hidden" value="1" class="rowNumber">
                        </td>
                        {foreach item=FIELD_MODEL from=$SELECTED_FIELDS_MODEL name=selected_fields}
                            {if $FIELD_MODEL->isEditable() eq 'true'}
                                <td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                    {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$SOURCE_MODULE) BLOCK_FIELDS=$SELECTED_FIELDS_MODEL MODULE=$SOURCE_MODULE}
                                    {if $FIELD_MODEL@last}
                                        <div class="actions pull-right" style="padding-top:7px; padding-right:10px;">
                                            &nbsp;<a class="deleteRecordButton"><i title="{vtranslate('LBL_DELETE', $MODULE)}" class="icon-trash alignMiddle"></i></a>
                                        </div>
                                    {/if}
                                </td>
                            {/if}
                        {/foreach}
                    </tr>
                {/if}
            </tbody>
        </table>
        </div>
    </td>
</tr>
<tr class="HideFieldsHeader">
    <td colspan="4">
        <button class="btn btn-success" type="button" id="btnAddRowData{$BLOCK_ID}"><i class="icon-plus"></i> &nbsp;<strong>{vtranslate('LBL_ADD')}</strong></button>
        <input type="hidden" id="selected_fields{$BLOCK_ID}" />
        <input type="hidden" id="multipicklist_fields{$BLOCK_ID}" />
        <input type="hidden" name="table_block_data" value="1" />
    </td>
</tr>
{/strip}