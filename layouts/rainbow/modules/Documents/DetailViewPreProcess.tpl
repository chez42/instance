{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}

{include file="modules/Vtiger/partials/Topbar.tpl"|myclayout_path}

<div class="container-fluid app-nav">
    <div class="row">
        {include file="partials/SidebarHeader.tpl"|vtemplate_path:$MODULE}
        {include file="ModuleHeader.tpl"|vtemplate_path:$MODULE}
    </div>
</div>
</nav>    
     <div id='overlayPageContent' class='fade modal overlayPageContent content-area overlay-container-60' tabindex='-1' role='dialog' aria-hidden='true'>
        <div class="data">
        </div>
        <div class="modal-dialog">
        </div>
    </div>
<div class="container-fluid main-container">
    <div class="row">
        <div class="detailViewContainer viewContent clearfix">
            <div class="col-sm-12 col-xs-12 content-area" style="padding-left: 15px;">
                {include file="DetailViewHeader.tpl"|vtemplate_path:$MODULE}
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        {include file="DetailViewTagList.tpl"|vtemplate_path:$MODULE}
                    </div>
                </div>   
            </div>
                <div class="detailview-content container-fluid">
                    <input id="recordId" type="hidden" value="{$RECORD->getId()}" />
                    {include file="ModuleRelatedTabs.tpl"|vtemplate_path:$MODULE}
                    <div class="details row" style="margin-top:10px;">
