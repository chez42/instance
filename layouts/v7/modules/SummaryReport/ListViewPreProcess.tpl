{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{include file="modules/Vtiger/partials/Topbar.tpl"}

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
<div class="main-container main-container-{$MODULE}" style="padding-left: 43px;">

	<br>
	<div id="prefPageHeader" class="detailViewTitle">
		<div class="row-fluid">
			<div class="span8">
				<span class="row-fluid marginLeftZero">
					<span class="logo span1"></span>
					<span class="span9">
						<span class="row-fluid" id="myPrefHeading">
							<h3>{vtranslate($MODULE, $MODULE)}</h3>
						</span>
						{*<span class="row-fluid">{vtranslate($MODULE, $MODULE)}</span>*}
					</span>
				</span>
			</div>
		</div>
	</div>
	<div id="tool_actions" class="row-fluid" style="margin-bottom: 10px; box-shadow: 0 -4px 4px -4px #959595">
                    <span class="btn-toolbar span4">
                        <span class="btn-group">
                            <button type="button" class="btn addButton" onclick='window.location.href="index.php?module=SummaryReport&view=Edit"'><i class="glyphicon glyphicon-plus" style="margin-right: 5px"></i><strong>{vtranslate('LBL_ADD_REPORT', $MODULE)}</strong></button>
                        </span>
                    </span>
	</div>
	<div class="listViewPageDiv full-width" id="listViewContents">
