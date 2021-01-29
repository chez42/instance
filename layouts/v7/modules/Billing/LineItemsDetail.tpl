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
		{assign var=HEADER_LABEL value=vtranslate('Captial Flows', $MODULE_NAME)}
		<h5 class="textOverflowEllipsis">
			{$HEADER_LABEL}
		</h5>
	</div>
    <div class="lineItemTableDiv blockData">
        <table class="table table-bordered lineItemsTable" style = "margin-top:15px">
            <tbody>
                <tr class="text-center">
    				 <td class="lineItemFieldName">
    				 	<b>Trade Date</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Day Diff</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Total Days</b>
    				 </td> 
    				 <td class="lineItemFieldName">
    				 	<b>Transaction Fee</b>
    				 </td>   
    				 <td class="lineItemFieldName">
    				 	<b>Transaction Type</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Total Amount</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Transaction Amount</b>
    				 </td>
    				 <td class="lineItemFieldName">
    				 	<b>Total Adjustment</b>
    				 </td>              
	            </tr>
	            
                {foreach key=INDEX item=ITEM_DETAIL from=$RELATED_FLOWS}
                
                    <tr class="text-center">

                        <td>
                            {$ITEM_DETAIL["trade_date$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["diff_days$INDEX"]}
                        </td>

                        <td>
                            {$ITEM_DETAIL["totaldays$INDEX"]}
                        </td>
                        
                        <td>
							${$ITEM_DETAIL["trans_fee$INDEX"]}
                        </td>
                        
                        <td>
                            {$ITEM_DETAIL["transactiontype$INDEX"]}
                        </td>
                        
 						<td>
							${$ITEM_DETAIL["totalamount$INDEX"]}
                        </td>
                        
                         <td>
                            ${$ITEM_DETAIL["transactionamount$INDEX"]}
                        </td>

                        <td>
                        	<span class="pull-right">
                            	${$ITEM_DETAIL["totaladjustment$INDEX"]}
                        	</span>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    <table class="table table-bordered lineItemsTable">
    	<tr>
            <td width="83%">
                <div class="pull-right">
                    <strong>{vtranslate('add',$MODULE_NAME)}</strong>
                </div>
            </td>
            <td>
                <span class="pull-right">
                    <strong>${$FINAL_ADJUSTMENT}</strong>
                </span>
            </td>
        </tr>
        <tr>
            <td width="83%">
                <div class="pull-right">
                    <strong>{vtranslate('equals',$MODULE_NAME)}</strong>
                </div>
            </td>
            <td>
                <span class="pull-right">
                    <strong>${$FINAL_EQUAL}</strong>
                </span>
            </td>
        </tr>
        <tr>
            <td width="83%">
                <div class="pull-right">
                    <strong>{vtranslate('Total Fees Debited',$MODULE_NAME)}</strong>
                </div>
            </td>
            <td>
                <span class="pull-right">
                    <strong>${$FINAL_EQUAL}</strong>
                </span>
            </td>
        </tr>
    </table
</div>