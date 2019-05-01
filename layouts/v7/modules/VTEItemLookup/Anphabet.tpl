{*<!--
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
-->*}

{strip}
    {assign var=ANPHABET value=range('A', 'Z')}
    <button class="btn btn-xs btn-default anphabet-filter-button" data-value="all" type="button">All</button>
    {foreach item=CHARACTER from=$ANPHABET}<button data-value="{$CHARACTER}" class="btn btn-xs btn-{if $ANPHABET_FILTER eq $CHARACTER}primary{else}default{/if} anphabet-filter-button" type="button">{$CHARACTER}</button>{/foreach}
{/strip}