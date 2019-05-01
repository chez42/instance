{*/* ********************************************************************************
* The content of this file is subject to the Dynamic Blocks ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="widget_header clearfix">
        <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row">
        <span class="col-lg-8">
            <button class="btn addButton addDynamicBlockButton btn-default" data-url="index.php?module=DynamicBlocks&parent=Settings&view=EditView">
                <i class="fa fa-plus fa-lg"></i>&nbsp;<strong>{vtranslate('LBL_ADD', $QUALIFIED_MODULE)} {vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</strong>
            </button>
        </span>
        <span class="col-lg-4">
            <div class="pull-right col-md-6">
                <select class="select2 col-md-12" id="dynamicBlockModules">
                    <option value="All">All</option>
                    {foreach key = MODULE_NAME item=MODULE_LABEL from=$SUPPORTED_MODULES}
                        <option value="{$MODULE_NAME}" {if $MODULE_NAME eq $SELECTED_MODULE_NAME} selected {/if}>{vtranslate($MODULE_NAME, $MODULE_NAME)}</option>
                    {/foreach}
                </select>
            </div>
        </span>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="listViewContentDiv" id="listViewContents">
        <br>{include file='ListViewContents.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    </div>
</div>