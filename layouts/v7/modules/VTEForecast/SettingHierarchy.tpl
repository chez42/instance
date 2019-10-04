{*/* ********************************************************************************
* The content of this file is subject to the VTEForecast ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */*}

{strip}
<div class="container-fluid">	
	<div class="clearfix treeView">
		<ul>
			<li data-node="{$ROOT_RECORD->getParentString()}" data-nodeid="{$ROOT_RECORD->getId()}">
				<div class="toolbar-handle">
                    {if $ROOT_RECORD->getRootImageName()}
                        <img src="{$ROOT_RECORD->getPath()}{$ROOT_RECORD->getRootImageName()}" class="avar-tree-forecast" />
                    {else}
                        <img src="layouts/vlayout/modules/VTEForecast/resources/icon-avar-logo.png" class="avar-tree-forecast" />
                    {/if}
					<a href="javascript:;" class="btn btn-inverse draggable droppable">{$ROOT_RECORD->getName()}</a>
					<div class="toolbar" title="{vtranslate('LBL_ADD_RECORD','VTEForecast')}">
						&nbsp;<a href="{$ROOT_RECORD->getCreateChildUrl()}" data-url="{$ROOT_RECORD->getCreateChildUrl()}" data-action="modal"><span class="glyphicon glyphicon-plus"></span></a>
					</div>
				</div>
				{assign var="RECORD" value=$ROOT_RECORD}
				{include file=vtemplate_path("RecordTree.tpl", $QUALIFIED_MODULE)}
			</li>
		</ul>
	</div>
</div>

{/strip}

