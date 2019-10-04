{*/* ********************************************************************************
* The content of this file is subject to the Hide Fields ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid WidgetsManage hide-field">
    <div class="widget_header row">
        <div class="col-sm-6"><h4><label>{vtranslate('HideFields', 'HideFields')}</label>
        </div>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row hide-field-action">
        <div class="col-lg-8">
            <button class="btn btn-default addButton addWidget" data-url="index.php?module=HideFields&view=EditAjax&mode=getEditForm">
                <i class="fa fa-plus"></i>&nbsp;<strong>{vtranslate('LBL_ADD', $QUALIFIED_MODULE)} {vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</strong>
            </button>
        </div>
        <div class="col-lg-4">
            <div class="pull-right hide">
                <select class="select2" id="HideFieldsModules" style="width: 220px;">
                    <option value="All">All</option>
                    {foreach key=MODULE_NAME item=MODULE_LABEL from=$SUPPORTED_MODULES}
                        <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE_NAME} selected {/if}>{vtranslate($MODULE_NAME, $MODULE_NAME)}</option>
                    {/foreach}
                </select>
            </div>
        </div>
    </div>
    <br>
    <div class="clearfix"></div>
    <div class="summaryWidgetContainer">
        <div class="row-fluid">
            <div class="listViewContentDiv" id="listViewContents">
                <br>{include file='ListViewContents.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
            </div>
        </div>
    </div>
</div>