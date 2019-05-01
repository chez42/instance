{*<!--
/* ********************************************************************************
* The content of this file is subject to the Masked Input ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('LBL_MASKED_FIELD_INPUT', 'MaskedInput')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="contents row-fluid">
        <div class="col-lg-6">
                <button class="btn addButton addRecordButton" data-url="index.php?module=MaskedInput&view=EditAjax&mode=getConfiguredFieldForm">
                    <i class="fa fa-plus fa-lg"></i>&nbsp;
                    <strong>{vtranslate('LBL_ADD_FIELD', 'MaskedInput')} </strong>
                </button>
            <div class="clearfix"></div>
            <div class="listViewContentDiv" id="MaskedInputFieldList" style="padding-top:10px;">
                {include file='MaskedInputFields.tpl'|@vtemplate_path:'MaskedInput'}
            </div>
        </div>
        <div class="col-lg-6">
			<button class="btn addButton addRecordButton" data-url="index.php?module=MaskedInput&view=EditAjax&mode=getCustomInputForm">
                <i class="fa fa-plus fa-lg"></i>&nbsp;
                <strong>{vtranslate('Create', 'MaskedInput')} {vtranslate('LBL_CUSTOM', 'MaskedInput')} {vtranslate('MaskedInput', 'MaskedInput')}</strong>
            </button>
            <div class="clearfix"></div>
            <div class="listViewContentDiv" id="MaskedInputList" style="padding-top:10px;">
                {include file='MaskedInputs.tpl'|@vtemplate_path:'MaskedInput'}
            </div>
        </div>
    </div>
</div>

