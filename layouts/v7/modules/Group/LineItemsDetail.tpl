{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

<div class="details block">
    <div class="lineItemTableDiv">
        <table class="table lineItemsTable" style = "margin-top:15px">
            <thead>
            <th colspan="4" class="lineItemBlockHeader">
                {assign var=HEADER_LABEL value=vtranslate('Group Billing Items', $MODULE_NAME)}
                {$HEADER_LABEL}
            </th>
            
            </thead>
            <tbody>
                <tr class="text-center">
    				 <td class="lineItemFieldName">
    				 	<b>Portfolio</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Billing Specification</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Active</b>
    				 </td>
	            </tr>
                {foreach key=INDEX item=ITEM_DETAIL from=$RELATED_ITEMS}
                    <tr class="text-center">

                        <td>
                            {$ITEM_DETAIL["portfolioname$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["billingspecificationname$INDEX"]}
                        </td>

                        <td>
							{$ITEM_DETAIL["active$INDEX"]}
                        </td>

                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>