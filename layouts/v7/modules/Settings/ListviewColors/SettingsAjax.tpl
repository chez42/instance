{*/* * *******************************************************************************
* The content of this file is subject to the VTE List View Colors ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{if $COUNT_ENTITY gt 0}
    {foreach item=ENTITY from=$ENTITIES}
        <tr style="background-color: {$ENTITY.bg_color}; color: {$ENTITY.text_color};">
            <td class="listViewEntryValue" width="5%">
                <i class="glyphicon glyphicon-move alignMiddle" title="{vtranslate('LBL_MOVE_BTN', $QUALIFIED_MODULE)}" data-record="{$ENTITY.id}"></i>
            </td>
            <td class="listViewEntryValue" width="15%">{vtranslate($ENTITY.modulename, $ENTITY.modulename)}</td>
            <td class="listViewEntryValue" width="55%">
                <a class="editColorButton" href="javascript:void(0)" data-url="index.php?module=ListviewColors&view=EditViewAjax&parent=Settings&record={$ENTITY.id}" style="color: {$ENTITY.related_record_color};">
                    {$ENTITY.condition_name}
                </a>
            </td>
            <td class="listViewEntryValue" width="10%">{$ENTITY.conditions_count}</td>
            <td class="listViewEntryValue" width="10%">{$ENTITY.status}</td>
            <td class="listViewEntryValue" width="5%">
                <div class="actions pull-right">
                        <span class="actionImages">
                            <a data-url="index.php?module=ListviewColors&view=EditViewAjax&parent=Settings&record={$ENTITY.id}" class="editColorButton" href="javascript: void(0);">
                                <i class="glyphicon glyphicon-pencil alignMiddle" title="{vtranslate('LBL_EDIT_BTN', $QUALIFIED_MODULE)}"></i>
                            </a>&nbsp;
                            <a data-url="index.php?module=ListviewColors&action=DeleteAjax&parent=Settings&record={$ENTITY.id}" class="deleteColorButton" href="javascript: void(0);">
                                <i class="glyphicon glyphicon-trash alignMiddle" title="{vtranslate('LBL_DELETE_BTN', $QUALIFIED_MODULE)}"></i>
                            </a>
                        </span>
                </div>
            </td>
        </tr>
    {/foreach}
{/if}

