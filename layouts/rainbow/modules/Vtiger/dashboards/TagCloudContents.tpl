{************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************}
{strip}
    <div class="col-sm-12">

        <div class="text-info">
        <h5><i class="material-icons">bookmark</i> Total {vtranslate('LBL_TAGS', $MODULE_NAME)} : {count($TAGS)}</h5></div>




    <div class="tagsContainer" id="tagCloud">
		{foreach from=$TAGS item=TAG_MODEL key=TAG_ID}
			{assign var=TAG_LABEL value=$TAG_MODEL->getName()}
            <div class="bg-success textOverflowEllipsis label tag" title="{$TAG_LABEL}" data-type="{$TAG_MODEL->getType()}" data-id="{$TAG_ID}">
				<a class="tagName cursorPointer" data-tagid="{$TAG_ID}" rel="{$TAGS[0][$TAG_NAME]}">{$TAG_LABEL}</a>&nbsp;		
            </div>
		{/foreach}
	</div></div>
    
    {/strip}	