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
		{include file="GraphFilterHeader.tpl"|vtemplate_path:$MODULE}
	</div>
</div>
</nav>
<div id='overlayPageContent' class='fade modal overlayPageContent content-area overlay-container-60' tabindex='-1' role='dialog' aria-hidden='true'>
	<div class="data">
	</div>
	<div class="modal-dialog">
	</div>
</div>  
<div class="main-container main-container-{$MODULE}" >
		{assign var=LEFTPANELHIDE value=$CURRENT_USER_MODEL->get('leftpanelhide')}
		<div id="modnavigator" class="module-nav">
			<div class="hidden-xs hidden-sm mod-switcher-container">
				{include file="partials/Menubar.tpl"|vtemplate_path:$MODULE}
			</div>
		</div>
		<input type="hidden" value="{$VIEWID}" id="customFilter" />
		{if isset($FILTER_STATEMENT) and !empty($FILTER_STATEMENT)}
			<div class="row graph_filter_headers" style="margin-left:40px;">
				<div class="col-md-12" style="text-align: justify;">
					<h5 class="text-center">All {vtranslate($MODULE, $MODULE)} with {$FILTER_STATEMENT}.</h5>
				</div>
			</div>
		{/if}
		
		<div class="listViewPageDiv content-area full-width" id="listViewContent">




   