{*<!--
/* ********************************************************************************
* The content of this file is subject to the Google Address ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ">
    <div class="widget_header clearfix">
        <h3>{vtranslate('GoogleAddress', 'GoogleAddress')}</h3>
    </div>
    <hr>
    <div class="clearfix"></div>
    <div class="row">
            <span class="col-lg-10">
                <button class="btn addButton addAddressButton" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getEditForm">
                    <i class="fa fa-plus fa-lg"></i>&nbsp;
                    <strong>{vtranslate('LBL_ADD_ADDRESS', 'GoogleAddress')}</strong>
                </button>
            </span>

            <span class="col-lg-1">
                <button class="btn addButton pull-right editGoogleApiKey" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getGoogleApiKey">
                    <i class="fa fa-plus fa-lg"></i>&nbsp;
                    <strong>Google ApiKey</strong>
                </button>
            </span>

            <span class="col-lg-1">
                <button class="btn addButton editCountries pull-right" data-url="index.php?module=GoogleAddress&view=EditAjax&mode=getCountryForm">
                    <i class="fa fa-plus fa-lg"></i>&nbsp;
                    <strong>{vtranslate('LBL_COUNTRIES', 'GoogleAddress')}</strong>
                </button>

            </span>
    </div>
    <div class="clearfix">&nbsp;</div>
    <div class="listViewContentDiv" id="listViewContents">
        {include file='ListView.tpl'|@vtemplate_path:'GoogleAddress'}
    </div>
</div>