{*/* ********************************************************************************
* The content of this file is subject to the Summary Report ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}
{strip}
<div class="detailViewContainer">
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
	<div class="detailViewInfo row-fluid" style="background: none repeat scroll 0 0 #fafafb;">
		<div class="details span12">
            <div class="contents" style="padding:10px;">
                <div id="tool_actions" class="row-fluid" style="margin-bottom: 10px;">
                    <span class="btn-toolbar span4">
                        <span class="btn-group">
                            <button type="button" class="btn addButton" onclick='window.location.href="index.php?module=SummaryReport&view=Edit"'><i class="icon-plus"></i><strong>{vtranslate('LBL_ADD_REPORT', $MODULE)}</strong></button>
                        </span>
                    </span>
                </div>
                <div id="listViewContents" class="listViewContentDiv">
{/strip}