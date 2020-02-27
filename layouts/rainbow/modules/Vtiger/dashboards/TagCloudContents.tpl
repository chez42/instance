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
    	
		{foreach from=$TAGS[1] item=TAG_ID key=TAG_NAME}
                    <div class="bg-success textOverflowEllipsis label" title="{$TAG_NAME}">
			<a class="tagName cursorPointer" data-tagid="{$TAG_ID}" rel="{$TAGS[0][$TAG_NAME]}">{$TAG_NAME}</a>&nbsp;		
                    </div>
		{/foreach}
	</div></div>
    
    {/strip}	