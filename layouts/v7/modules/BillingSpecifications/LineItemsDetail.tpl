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
        <table class="table table-bordered lineItemsTable" style = "margin-top:15px">
            <thead>
            <th colspan="4" class="lineItemBlockHeader">
                {assign var=HEADER_LABEL value=vtranslate('Schedule Items', $MODULE_NAME)}
                {$HEADER_LABEL}
            </th>
            
            </thead>
            <tbody>
                <tr class="text-center">
    				 <td class="lineItemFieldName">
    				 	<b>From</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>To</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Type</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Value</b>
    				 </td>                
	            </tr>
                {foreach key=INDEX item=ITEM_DETAIL from=$RELATED_SCHEDULE}
                    <tr class="text-center">

                        <td>
                            {$ITEM_DETAIL["from$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["to$INDEX"]}
                        </td>

                        <td>
							{$ITEM_DETAIL["type$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["value$INDEX"]}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>