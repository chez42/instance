{*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
<ul>
{foreach from=$RECORD->getChildren() item=CHILD_RECORD}
	<li data-role="{$CHILD_RECORD->getParentString()}" data-roleid="{$CHILD_RECORD->getId()}">
        <div class="toolbar-handle">
            {if $CHILD_RECORD->getImageName()}
                <img src="{$CHILD_RECORD->getPath()}{$CHILD_RECORD->getImageName()}" class="avar-tree-forecast" />
            {else}
                <img src="layouts/vlayout/modules/VTEForecast/resources/icon-avar-tree.png" class="avar-tree-forecast" />
            {/if}
            <a href="#"  class="btn roleEle userNode sub{$CHILD_RECORD->getDepth()}" rel="tooltip" data-nodeid="{$CHILD_RECORD->getId()}" data-nodename="{$CHILD_RECORD->getName()}">{$CHILD_RECORD->getName()}</a>
		</div>

		{assign var="RECORD" value=$CHILD_RECORD}
		{include file=vtemplate_path("RecordTreeViewForecast.tpl", $MODULE_NAME)}
	</li>
{/foreach}
</ul>
{/strip}