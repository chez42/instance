{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{strip}
    <div class="col-md-12">
        {if $ACTIVE_PRODUCTS_MODLUE == 0}
        <button title="Products" style="padding: 5px 5px;" id="" class="button-change-item-module btn btn-{if $CURRENT_SELECTED_ITEM_MODLUE eq 'Products' && $CONFIGURE['product_bundles'] != 1}primary{else}default{/if}" data-module="Products"><strong>{vtranslate('Products', 'Products')}</strong></button>
        <button title="Products Bundles" style="padding: 5px 5px;" id="" class="button-change-item-module btn btn-{if $CURRENT_SELECTED_ITEM_MODLUE eq 'Products' && $CONFIGURE['product_bundles'] == 1}primary{else}default{/if}" data-module="Products" data-bundles="1"><span style="font-size: 15px;line-height: 1;" class="vicon-inventory icon-module" data-info="\e639"></span></button>
        {/if}
        {if $ACTIVE_SERVICES_MODLUE == 0}
        <button title="Services" style="padding: 5px 5px;" id="" class="button-change-item-module btn btn-{if $CURRENT_SELECTED_ITEM_MODLUE eq 'Services'}primary{else}default{/if}" data-module="Services"><strong>{vtranslate('Services', 'Services')}</strong></button>
        {/if}
    </div>
{/strip}
