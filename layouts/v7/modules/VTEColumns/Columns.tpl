{*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}
{strip}
    <div class="blockActions" style="float:left !important;width: 20%;margin-left: 69px;">
			<span>
                <i class="fa fa-info-circle vtecolumn-tooltip"></i>&nbsp; {vtranslate('Columns', $QUALIFIED_MODULE)}&nbsp;
                {assign var = NUMBER_LIST  value=array(2,3,4,5,6,7,8,9,10)}
                <select id="num_of_columns_{$BLOCK_ID}" class="select2 cb_num_of_columns" name="num_of_columns_{$BLOCK_ID}" style="min-width: 30px;">
                    {foreach item=NUMBER from=$NUMBER_LIST}
                        <option value="{$NUMBER}" {if $NUMBER eq $NUM_SELECTED_COLUMN}selected{/if}>{$NUMBER}</option>
                    {/foreach}
                </select>
                <a href="javascript:void(0);" class="custom_layout" data-id="{$BLOCK_ID}" data-url = "index.php?module=VTEColumns&view=CustomLayout&blockid={$BLOCK_ID}" style="margin-left: 5px;margin-top: 2px;"><i class="fa fa-cog fa-fw" aria-hidden="true"></i></a>
            </span>
    </div>
{/strip}