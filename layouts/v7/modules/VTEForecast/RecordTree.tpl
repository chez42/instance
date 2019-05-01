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
            {*{assign var=VIEW_NAME value={getPurifiedSmartyParameters('view')}}
            {assign var=VIEW_TYPE value={getPurifiedSmartyParameters('type')}}*}
            <div {*{if $VIEW_NAME != 'Popup'}*}class="toolbar-handle"{*{/if}*}>
                {if $CHILD_RECORD->getImageName()}
                    <img src="{$CHILD_RECORD->getPath()}{$CHILD_RECORD->getImageName()}" class="avar-tree-forecast" />
                {else}
                    <img src="layouts/vlayout/modules/VTEForecast/resources/icon-avar-tree.png" class="avar-tree-forecast" />
                {/if}
			{if $VIEW_TYPE == 'Transfer'}
				{assign var="SOURCE_RECORD_SUBPATTERN" value='::'|cat:$SOURCE_RECORD->getId()}
				{if strpos($CHILD_RECORD->getParentString(), $SOURCE_RECORD_SUBPATTERN) !== false}
					{$CHILD_RECORD->getName()}
				{else}
					<a href="{$CHILD_RECORD->getCreateChildUrl()}" data-url="{$CHILD_RECORD->getCreateChildUrl()}" class="btn roleEle sub{$CHILD_RECORD->getDepth()}" rel="tooltip" >{$CHILD_RECORD->getName()}</a>
				{/if}
			{else}
					<a href="{$CHILD_RECORD->getCreateChildUrl()}" data-url="{$CHILD_RECORD->getCreateChildUrl()}" class="btn draggable droppable sub{$CHILD_RECORD->getDepth()}" rel="tooltip" title="{vtranslate('LBL_CLICK_TO_EDIT_OR_DRAG_TO_MOVE','VTEForecast')}">{$CHILD_RECORD->getName()}</a>
			{/if}
			{*{if $VIEW_NAME != 'Popup'}*}
			<div class="toolbar">
				&nbsp;<a class='btnDelete' href="{$CHILD_RECORD->getCreateChildUrl()}" data-url="{$CHILD_RECORD->getCreateChildUrl()}" title="{vtranslate('LBL_ADD_RECORD', 'VTEForecast')}"><span class="glyphicon glyphicon-plus"></span></a>
				&nbsp;<a class='btnRemove' data-id="{$CHILD_RECORD->getId()}" href="javascript:;" data-url="{$CHILD_RECORD->getDeleteActionUrl()}" data-action="modal" title="{vtranslate('LBL_DELETE', 'VTEForecast')}"><span class="glyphicon glyphicon-trash"></span></a>
			</div>
			{*{/if}*}
		</div>

		{assign var="RECORD" value=$CHILD_RECORD}
		{include file=vtemplate_path("RecordTree.tpl", $QUALIFIED_MODULE)}
	</li>
{/foreach}
</ul>
{/strip}