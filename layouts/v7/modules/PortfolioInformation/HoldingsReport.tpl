<div class="PortfolioInformationHoldingsReport holdings_wrapper">

	<input type="hidden" value='{$PIE}' id="pie_values" />
	<input type="hidden" value='{$ESTIMATE_PIE}' id="estimate_pie_values" />
	<input type="hidden" id="history_chart" value='{$MONTHLY_TOTALS}' />
	<input type="hidden" id="trailing_aum_values" value='{$TRAILING_AUM}' />
	<input type="hidden" id="trailing_revenue_values" value='{$TRAILING_REVENUE}' />

	<div class="row-fluid ReportTitle detailViewTitle">
		<div class=" span12 ">
			<div class="row-fluid">
				<div class="span6">
					<div class="row-fluid">
						<span class="recordLabel font-x-x-large textOverflowEllipsis span pushDown"><span>OMNIVue Asset Allocation</span>&nbsp;</span>
					</div>
				</div>
				<div class="span6">
					<div class="pull-right">
						{if $PIE_FILE eq 1}
					        <img src="{$PIE_FILE}" />
					    {else}
						    <div class="btn-toolbar">
								<span class="btn-group">
									<button class="btn ExportReport"><strong>Generate PDF</strong></button>
								</span>
							</div>
					        <form method="post" id="export">
					            <input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
					            <input type="hidden" value="PortfolioInformation" name="module" />
					            <input type="hidden" value="HoldingsReport" name="view" />
					            <input type="hidden" value="" name="pie_image" id="pie_image" />
					            <input type="hidden" value="" name="aum_image" id="aum_image" />
					            <input type="hidden" value="" name="revenue_image" id="revenue_image" />
					            <input type="hidden" value="1" name="pdf" />
					            <input type="hidden" value="{$CALLING_RECORD}" name="calling_record" />
					        </form>
					    {/if}
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<div class="detailViewInfo row-fluid">
		
		<div class="contents">
		
			<div class="row-fluid">
		        <h4>
		        	OMNIVue&trade; determines the <span style="text-decoration: underline;">actual</span> exposure to various
		            asset classes utilizing data from respected 3rd parties to provide a more accurate view of a portfolio&rsquo;s true asset allocation.
		        </h4>
		        <h4 style="margin-top: 10px; margin-bottom: 10px;">
		        	<small>
		        		OMNIVue&trade; relies on the estimates and approximations of these 3rd parties to perform its calculations.
		                As such, the totals and values represented in OMNIVue&trade; may not correspond exactly to custodial 
		            	statement balances and other reports of actual account value.
		        	</small>
		        </h4>
	        </div>
	        
	        <div id="holdings_summary_wrapper" class="row-fluid">
	        	<div class="span12">
		        	<div class="span5" style="margin-left: 0px;">
			        	<table class="table table-bordered table-collapse" id="report_top_table">
			                <thead>
			                    <tr>
			                        <th>Holdings Summary</th>
			                        <th>Weight</th>
			                        <th>Total</th>
			                    </tr>
			                </thead>
	                		<tbody>
				                {if $ASSET_CLASS.equities neq 0}
				                	<tr data-toggle="collapse" id="asset_equities" data-target=".asset_equities">
										<td><i class="icon-plus"></i>&nbsp;Equity</td>
									    <td>{$ASSET_CLASS_WEIGHT.equities|number_format:2}%</td>
				                        <td>${$ASSET_CLASS.equities|number_format:2:".":","}</td>
				                    </tr>
									<tr class="holdings collapse asset_equities">
				                        <td style="">&nbsp;&nbsp;US Stock</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.us_stock_value|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_equities">
				                        <td>&nbsp;&nbsp;Intl Stock</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.intl_stock_value|number_format:2:".":","}</td>
				                    </tr>
				                {/if}
				                {if $ASSET_CLASS.fixed neq 0}
				                    <tr data-toggle="collapse" id="asset_fixed_income" data-target=".asset_fixed_income">
				                        <td><i class="icon-plus"></i>&nbsp;Fixed Income</td>
				                        <td>{$ASSET_CLASS_WEIGHT.fixed|number_format:2}%</td>
				                        <td>${$ASSET_CLASS.fixed|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_fixed_income">
				                        <td>&nbsp;&nbsp;US Bond</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.us_bond_value|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_fixed_income">
				                        <td>&nbsp;&nbsp;Intl Bond</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.intl_bond_value|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_fixed_income">
				                        <td>&nbsp;&nbsp;Preferred</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.preferred_net_value|number_format:2:".":","}</td>
				                    </tr>
				                {/if}
				                {if $ASSET_CLASS.cash neq 0}
				                    <tr>
				                        <td>Cash</td>
				                        <td>{$ASSET_CLASS_WEIGHT.cash|number_format:2}%</td>
				                        <td>${$INDIVIDUAL_AC.cash_net_value|number_format:2:".":","}</td>
				                    </tr>
				                {/if}
				                {if $ou neq 0}
				                    <tr data-toggle="collapse" id="asset_other" data-target=".asset_other">
				                        <td><i class="icon-plus"></i>&nbsp;Other</td>
				                        <td>{$ASSET_CLASS_WEIGHT.other|number_format:2+$ASSET_CLASS_WEIGHT.unclassified|number_format:2}%</td>
				                        <td>${$ou|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_other">
				                        <td>&nbsp;&nbsp;Convertible</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.convertible_net_value|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_other">
				                        <td>&nbsp;&nbsp;Other</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.other_net_value|number_format:2:".":","}</td>
				                    </tr>
				                    <tr class="holdings collapse asset_other">
				                        <td>&nbsp;&nbsp;Unclassified</td>
				                        <td> - </td>
				                        <td>${$INDIVIDUAL_AC.unclassified_net_value|number_format:2:".":","}</td>
				                    </tr>
				                {/if}
								<tr>
			                        <td class="align_left">Total Value</td>
			                        <td class="align_center">-</td>
			                        <td class="align_right">${$GLOBAL_TOTAL.global_total|number_format:2:".":","}</td>
			                    </tr>
			                </tbody>
            			</table>
		        	</div>
		            <div class="span7" id="">
			        	{if !empty($PIE_FILE) AND $PIE_IMAGE eq 1}
			                <img src="{$PIE_FILE}" style="width:70%; float:right;"/>
			            {else}
			                <div id="report_top_pie" class="report_top_pie" style="height: 320px;"></div>
			            {/if}
		           	</div>
		    	</div>
        	</div>
        	
        	<div class="holdings_report row-fluid">
        		<table class="table table-bordered table-collapse">
            		<thead>
			            <tr>
			            	<th colspan="11">OMNIVue</th>
			            </tr>
			     	</thead>
			     	<tbody>
			     		
			     		<tr>
			     			<td>Symbol</td>
			                <td>Description</td>
			                <td>Qty</td>
			                <td>Price</td>
			                <td colspan="5"><label class="textAlignCenter">Asset Allocation</label></td>
			                <td style="text-align: right;">Weight</td>
			                <td>Total Value</td>
			           	</tr>
			           	
			           	<tr>
			                <td>&nbsp;</td>
			                <td>&nbsp;</td>
			                <td>&nbsp;</td>
			                <td>&nbsp;</td>
			                <td>EQ</td>
			                <td>FI</td>
			                <td>CS</td>
			                <td>OT</td>
			                <td>UC</td>
			                <td>&nbsp;</td>
			                <td>&nbsp;</td>
			            </tr>
			            {foreach from=$CATEGORIES key=ck item=cv}
			                <tr data-toggle="collapse" id="primary_{$ck}" data-target=".primary_{$ck}">
				                <td><i class="icon-plus"></i>&nbsp;{$ck}</td>
			                    <td colspan="9"style="text-align: right;">{$cv.weight|number_format:2:".":","}%</td>
			                    <td>${$cv.total|number_format:2:".":","}</td>
			                </tr>
			                {foreach from=$GROUPED key=ik item=iv}
			                    {if $ck eq $iv.category}
			                        {assign var='equity' value=$POSITIONS[$iv.security_symbol].equity}
			                        {assign var="fi" value=$POSITIONS[$iv.security_symbol].fixed}
			                        {assign var='cash' value=$POSITIONS[$iv.security_symbol].cash}
			                        {assign var="other" value=$POSITIONS[$iv.security_symbol].other}
			                        {assign var="symbol" value=$iv.security_symbol}
		                      		<tr class="holdings collapse primary_{$ck}">
		                                <td><label class="hover_symbol_holdings" id="{$iv.security_symbol}" data-account='{$iv.account_number}'>&nbsp;&nbsp;&nbsp;&nbsp;{$iv.security_symbol}</label></td>
		                                <td>{$iv.description}</td>
		                                <td>{$iv.quantity|number_format:2:".":","}</td>
		                                <td>${$iv.last_price|number_format:2:".":","}</td>
		                                <td>{$equity|number_format:2:".":","}%</td>
		                                <td>{$fi|number_format:2:".":","}%</td>
		                                <td>{$cash|number_format:2:".":","}%</td>
		                                <td>{$other|number_format:2:".":","}%</td>
		                                <td>{$iv.unclassified_net_value|number_format:2:".":","}%</td>
		                                <td style="text-align: right;">{$iv.weight|number_format:2:".":","}%</td>
		                                <td>${$iv.current_value|number_format:2:".":","}</td>
		                            </tr>
			    				{/if}
			          		{/foreach}
            			{/foreach}
			            <tr>
			                <td colspan="4"><strong>Asset Allocation Values</strong></td>
			                <td><strong>${$ASSET_CLASS.equities|number_format:2:".":","}</strong></td>
			                <td><strong>${$ASSET_CLASS.fixed|number_format:2:".":","}</strong></td>
			                <td><strong>${$ASSET_CLASS.cash|number_format:2:".":","}</strong></td>
			                <td><strong>${$ASSET_CLASS.other|number_format:2:".":","}</strong></td>
			                <td><strong>${$ASSET_CLASS.unclassified|number_format:2:".":","}</strong></td>
			                <td style="text-align: right;"><strong>{$TOTAL_WEIGHT}%</strong></td>
			                <td><strong>${$GLOBAL_TOTAL.global_total|number_format:2:".":","}</strong></td>
			            </tr>
			        </tbody>
			     </table>
        	</div> 
		</div>
	</div>
