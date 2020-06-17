{*/* * *******************************************************************************
* The content of this file is subject to the VTE List View Colors ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="contentHeader row">
        <h3 class="col-lg-8 textOverflowEllipsis" style="height: 30px;">
            <a href="index.php?module=ModuleManager&parent=Settings&view=List">&nbsp;{vtranslate('MODULE_MANAGEMENT',$QUALIFIED_MODULE)}</a>&nbsp;>&nbsp;{vtranslate('LBL_SETTING_HEADER', $QUALIFIED_MODULE)}
        </h3>
    </div>
    <hr>
    <div class="clearfix"></div>

    <div class="listViewContentDiv row" id="listViewContents">
        <div class="marginBottom10px">
            <span class="row btn-toolbar">
                <button type="button" data-url="index.php?module=ListviewColors&view=EditViewAjax&parent=Settings" class="btn addButton editColorButton">
                    <i class="fa fa-plus fa-lg"></i>&nbsp;
                    <strong>Add Color</strong>
                </button>
            </span>
        </div>
        <div class="col-lg-12">
            <table class="table table-bordered listViewEntriesTable vte-listview-color">
                <thead>
                    <tr class="listViewHeaders">
                        <th class="medium"></th>
                        <th class="medium">{vtranslate('LBL_MODULE_NAME_HEADER', $QUALIFIED_MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_CONDITION_NAME_HEADER', $QUALIFIED_MODULE)}</th>
                        <th class="medium">{vtranslate('LBL_CONDITIONS_COUNT_HEADER', $QUALIFIED_MODULE)}</th>
                        <th class="medium" colspan="2">{vtranslate('LBL_STATUS_HEADER', $QUALIFIED_MODULE)}</th>
                    </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

