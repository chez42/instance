{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
 
 <span class="tag {if $ACTIVE eq true} active {/if}" title="{$TAG_MODEL->getName()}" data-type="{$TAG_MODEL->getType()}" data-id="{$TAG_MODEL->getId()}" style="border-radius: 5px !important;">
    {*<i class="activeToggleIcon material-icons">{if $ACTIVE eq true}fiber_manual_record{else}bookmark{/if}</i>*}
    <span class="tagLabel display-inline-block textOverflowEllipsis" title="{$TAG_MODEL->getName()}">{$TAG_MODEL->getName()}</span>
    {if !$NO_EDIT}
        <i class="editTag material-icons">bookmark</i>
    {/if}
    {if !$NO_DELETE}
        <i class="deleteTag material-icons">close</i>
    {/if}
</span>