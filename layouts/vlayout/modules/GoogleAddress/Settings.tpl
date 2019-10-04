{*<!--
/* ********************************************************************************
* The content of this file is subject to the Google Address ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="container-fluid">
    <div class="widget_header row-fluid">
        <h3>{vtranslate('GoogleAddress', 'GoogleAddress')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row-fluid">
        <span class="span9 btn-toolbar">
            <button class="btn addButton addAddressButton" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getEditForm">
                <i class="icon-plus"></i>&nbsp;
                <strong>{vtranslate('LBL_ADD_ADDRESS', 'GoogleAddress')}</strong>
            </button>
        </span>

        <span class="span3 btn-toolbar">
            <button class="btn addButton editCountries" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getCountryForm">
                <i class="icon-plus"></i>&nbsp;
                <strong>{vtranslate('LBL_COUNTRIES', 'GoogleAddress')}</strong>
            </button>

            <button class="btn addButton pull-right editGoogleApiKey" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getGoogleApiKey">
                <i class="icon-plus"></i>&nbsp;
                <strong>Google ApiKey</strong>
            </button>

        </span>
    </div>
    <div class="clearfix"></div>
    <div class="listViewContentDiv" id="listViewContents">
        {include file='ListView.tpl'|@vtemplate_path:'GoogleAddress'}
    </div>
</div>

