{*/* ********************************************************************************
* The content of this file is subject to the Custom Forms & Views ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</h3>
    </div>
    <hr>
    <div class="row-fluid">
        <span class="span8 btn-toolbar">
            <button class="btn addCustomForms" data-url="index.php?module=CustomFormsViews&parent=Settings&view=EditView">
                <i class="icon-plus"></i>&nbsp;<strong>{vtranslate('LBL_ADD', $QUALIFIED_MODULE)} {vtranslate($QUALIFIED_MODULE, $QUALIFIED_MODULE)}</strong>
            </button>
        </span>
    </div>
    <div class="listViewContentDiv" id="listViewContents">
        <br>{include file='ListViewContents.tpl'|@vtemplate_path:$QUALIFIED_MODULE}
    </div>
</div>