{*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}
{strip}
    <div class="blockActions" style="float:left !important;width: 20%" id="blockActions{$BLOCK_ID}">
			<span>

                <i class="fa fa-info-circle vtetab-tooltip"></i>&nbsp; {vtranslate('Convert to Tab', $QUALIFIED_MODULE)}&nbsp;
                <input style="opacity: 0;" type="checkbox"
                        {if $IS_TAB} checked value='0' {else} value='1' {/if} class ='cursorPointer bootstrap-switch' name="is_tab" id="is_tab_{$BLOCK_ID}"
                       data-on-text="{vtranslate('LBL_YES', $QUALIFIED_MODULE)}" data-off-text="{vtranslate('LBL_NO', $QUALIFIED_MODULE)}" data-on-color="primary" data-block-id="{$BLOCK_ID}" />
                <a href="javascript:void(0);" title="{$PARENT_TAB}" class="combine_tab" id="combine_tab_{$BLOCK_ID}" data-url = "index.php?module=VTETabs&view=CombineTab&blockid={$BLOCK_ID}" style="margin-left: 5px;margin-top: 2px;"><i class="fa fa-cog fa-fw" aria-hidden="true"></i></a>
            </span>
    </div>
{/strip}