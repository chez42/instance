{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

{assign var=APP_IMAGE_MAP value=Vtiger_MenuStructure_Model::getAppIcons()}
{assign var=APP_COLOR value=Vtiger_MenuStructure_Model::getAppColor()}
<style>
	.app-{$SELECTED_MENU_CATEGORY}{
	    background: {$APP_COLOR[$SELECTED_MENU_CATEGORY]} !important;
	}
</style>
<div class="col-sm-12 col-xs-12 app-indicator-icon-container app-{$SELECTED_MENU_CATEGORY}">
    <div class="row" title="{strtoupper(vtranslate($MODULE, $MODULE))}">
        <span class="app-indicator-icon fa fa-trash"></span>
    </div>
</div>
    
{include file="modules/Vtiger/partials/SidebarAppMenu.tpl"}