{*/* * *******************************************************************************
* The content of this file is subject to the VTE List View Colors ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

<div class="container-fluid">
    <form id="rleFormUninstall" name="rleFormUninstall" action="" method="post">
        <div class="contentHeader row-fluid">
            <h3 class="span8 textOverflowEllipsis">
                <a href="index.php?module=ModuleManager&parent=Settings&view=List">&nbsp;{vtranslate('MODULE_MANAGEMENT',$QUALIFIED_MODULE)}</a>&nbsp;>&nbsp;{vtranslate('LBL_SETTING_HEADER', $QUALIFIED_MODULE)}
            </h3>
        </div>
        <hr>
        <div class="clearfix"></div>

        <div class="listViewContentDiv row-fluid" id="listViewContents">

            <div class="marginBottom10px highlightBackgroundColor padding20px">
                <label>{vtranslate('LBL_UNINSTALL_MODULE', $QUALIFIED_MODULE)}</label>
            </div>
            <div class="marginBottom10px">
                <button type="button" id="rel_uninstall_btn" class="btn btn-danger"><strong>{vtranslate('LBL_UNINSTALL', $QUALIFIED_MODULE)}</strong></button>
                <button class="btn btn-info" type="button" onclick="javascript:window.history.back();"><strong>{vtranslate('CANCEL_BTN',$QUALIFIED_MODULE)}</strong></button>
            </div>

        </div>
    </form>
</div>

