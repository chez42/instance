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
	<div class="main-container main-container-{$MODULE}">
		
		<div id="modnavigator" class="module-nav">
			<div class="hidden-xs hidden-sm mod-switcher-container">
				{include file="partials/Menubar.tpl"|vtemplate_path:$MODULE}
			</div>
		</div>
		<div cid="sidebar-essentials" class="sidebar-essentials">
			{include file="partials/FolderSidebarEssentials.tpl"|vtemplate_path:$MODULE}
		</div>
		
		<div class="listViewPageDiv content-area " id="listViewContent">
			<div class="col-sm-12 col-xs-12" >
				<input type="hidden" name="view" id="view" value="{$VIEW}" />
				<input type="hidden" name="app" id="appName" value="{$SELECTED_MENU_CATEGORY}">
				<div class="preFolder module-breadcrumb" title="Back to Previous Folder">
					<style>
						.preFolder .current-filter-name, .preFolder .leftIcon{
					    	margin: 0px !important;
					    	line-height: 20px !important;
					    }
					</style>
					{if !empty($FOLDERS)}
						<p class="current-filter-name filter-name pull-left cursorPointer">
							<a class="folderBreadcrumb" data-folder-id="" > <i class="fa fa-home" style="font-size:20px"></i> &nbsp </a>
						</p>
					{/if}
				</div>
				<div class="clearfix"></div>
				<div id="table-content" class="folder-table-container" style="border:0px;margin-top:5px;">
					<table id="folder-table">
						<thead>
						</thead>
						<div class="folderContent">
							{include file="FolderContent.tpl"|vtemplate_path:$MODULE}
						</div>
					</table>	
				</div>
				<div id="scroller_wrapper" class="bottom-fixed-scroll">
					<div id="scroller" class="scroller-div"></div>
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript">
        var related_uimeta = (function() {
            var fieldInfo  = {$FIELDS_INFO};
            return {
                field: {
                    get: function(name, property) {
                        if(name && property === undefined) {
                            return fieldInfo[name];
                        }
                        if(name && property) {
                            return fieldInfo[name][property]
                        }
                    },
                    isMandatory : function(name){
                        if(fieldInfo[name]) {
                            return fieldInfo[name].mandatory;
                        }
                        return false;
                    },
                    getType : function(name){
                        if(fieldInfo[name]) {
                            return fieldInfo[name].type
                        }
                        return false;
                    }
                },
            };
        })();
    </script>