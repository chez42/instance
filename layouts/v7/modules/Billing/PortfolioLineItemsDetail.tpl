{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

<div class="details block">
	<div>
		{assign var=HEADER_LABEL value=vtranslate('Porfolios Detail', $MODULE_NAME)}
		<h5 class="textOverflowEllipsis">
			{$HEADER_LABEL}
		</h5>
	</div>
    <div class="lineItemTableDiv blockData">
        <table class="table table-bordered lineItemsTable" style = "margin-top:15px">
            <tbody>
                <tr class="text-center">
    				 <td class="lineItemFieldName">
    				 	<b>Account Number</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Portfolio Value</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Bill Amount</b>
    				 </td> 
	            </tr>
	            
                {foreach key=INDEX item=ITEM_DETAIL from=$RELATED_PORTFOLIOS}
                
                    <tr class="text-center">

                        <td>
                            {$ITEM_DETAIL["portfolioid$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["portfolio_amount$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["bill_amount$INDEX"]}
                        </td>
                        
                        
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    
</div>