<table class="table table-bordered listViewEntriesTable" style="border-top: 0px; border-top-right-radius: 0px; border-top-left-radius: 0px;">
	<thead>
		<tr>
			<td colspan="2">
				<input type="hidden" name="holdingschart" value='{$HOLDINGSCHART}' />
				<div class="pull-right"><strong>Price Date: {$DATE}</strong></div>
			</td>
		</tr>	
		<tr>
			<td>
				<span style="font-size:16px; font-weight:bold;">Holdings</span>
				<span style="font-size:14px"><strong> -  </strong> {$ACCOUNTNAME}</span>
			</td>
			<td>
				<form method="post" action="index.php?module=PortfolioInformation&action=PrintReport&report_type=holdings&{$ACCOUNT}&calling_record={$CALLING_RECORD}">
				    <input type="hidden" name="TWR_QTR" id="pTWR_QTR" />
				    <input type="hidden" name="TWR_YTD" id="pTWR_YTD" />
				    <input type="hidden" name="TWR_INCEPTION" id="pTWR_INCEPTION" />
				    <input type="hidden" name="report" value="holdings" />    
				    <input type="submit" name="print_pdf" class="btn pull-right" value="Print PDF" />
				</form>
			</td>
		</tr>
		{if $HIDE_PIE != '1'}
			<tr>
				<td colspan="2">
					<div id="holdings_chart_positions" style="height: 300px; width: 600px; margin-right:100px; float:right;"></div>
				</td>
			</tr>
		{/if}
		<tr>
			<td colspan="2" style="padding: 0px;">	
			
				<div id="portfolio_holdings">
				
					<table class="table listViewEntriesTable">
						<thead>
							<tr>
						        <th>Symbol</th>
						        <th>Description</th>
						        <th>Portfolio Account</th>
						        <th>Quantity</th>
						        <th>Current Price</th>
						        <th>Current Value</th>
						        <th>Cost Basis</th>
						        <th>Unrealized G/L</th>
						        <th>% G/L</th>
						        <th>Weight</th>
							</tr>
							{foreach from=$TOTALS key=title item=value}
								<tr style="background: rgba(0, 0, 0, 0.12)">
									<td><strong>{$title}</strong></td>
									<td colspan="4">&nbsp;</td>
									<td><strong>${$MAIN_CATEGORIES.$title.totals.$title.sub_total|number_format:2}</strong></td>
									<td><strong>${$MAIN_CATEGORIES.$title.totals.$title.cba|number_format:2}</strong></td>
									<td><strong>${$MAIN_CATEGORIES.$title.totals.$title.ugl|number_format:2}</strong></td>
									<td><strong>{$MAIN_CATEGORIES.$title.totals.$title.gl|string_format:"%.01f"}%</strong></td>
									<td><strong>{$MAIN_CATEGORIES.$title.totals.$title.weight|string_format:"%.01f"}%</strong></td>
								</tr>
								
								{foreach from=$value key=sub_sub_category item=val}
    								{if !$val.sub_total}
    									<tr style="background: rgba(0, 0, 0, 0.08)">
											<td><strong>&nbsp;&nbsp;{$sub_sub_category}</strong></td>
											<td colspan="4">&nbsp;</td>
											<td><strong>${$SUB_SUB_CATEGORIES.$sub_sub_category.sub_total|number_format:2}</strong></td>
											<td><strong>${$SUB_SUB_CATEGORIES.$sub_sub_category.cba|number_format:2}</strong></td>
											<td><strong>${$SUB_SUB_CATEGORIES.$sub_sub_category.ugl|number_format:2}</strong></td>
											<td><strong>{$SUB_SUB_CATEGORIES.$sub_sub_category.gl|string_format:"%.01f"}%</strong></td>
											<td><strong>{$SUB_SUB_CATEGORIES.$sub_sub_category.weight|string_format:"%.01f"}%</strong></td>    
    									</tr>
    									
    									{foreach from=$val key=k item=v}
        										{if !$v.sub_total}
        										<tr style="#ffffff">
										            <td><a href="" onclick="return false;" name='security_symbol'>&nbsp;&nbsp;&nbsp;{$v.security_symbol}</a></td>
										            <td>{$v.description}</td>
										            <td>{$v.account_number}</td>
										            {if $v.quantity EQ 0}
										                <td>&nbsp;</td>
										            {else}
										                <td>{$v.quantity|number_format:2}</td>
										            {/if}
										            {if $v.current_price EQ 0}
										                <td>&nbsp;</td>
										            {else}
										                <td>${$v.current_price|number_format:2}</td>
										            {/if}
										            <td>${$v.total_value|number_format:2}</td>
										            <td>${$v.cost_basis_adjustment|number_format:2}</td>
										            <td>{if $v.ugl < 0}
										                    (${$v.ugl|number_format:2|replace:'-':''})
										                {else}
										                    ${$v.ugl|number_format:2}
										                {/if}
										            </td>
										            <td>{$v.gl|string_format:"%.01f"}%</td>
										            <td>{$v.weight|string_format:"%.01f"}%</td>
        										</tr>
        									{/if}
    									{/foreach}
    								{/if}
								{/foreach}
							{/foreach}
						    <tr><!--Grand Totals-->
						        <th><strong>Total</strong></th>
						        <th colspan="4">&nbsp;</th>
						        <th><strong>${$GRANDTOTALS.value|number_format:2}</strong></th>
						        <th><strong>${$GRANDTOTALS.cba|number_format:2}</strong></th>
						        <th><strong>{if $GRANDTOTALS.ugl < 0}
						                (${$GRANDTOTALS.ugl|number_format:2|replace:'-':''})
						            {else}
						                ${$GRANDTOTALS.ugl|number_format:2}
						            {/if}</strong>
						        </th>
						        <th><strong>{$GRANDTOTALS.gl|string_format:"%.01f"}%</strong></th>
						        <th><strong>{$GRANDTOTALS.weight|string_format:"%.01f"}%</strong></th>
						    </tr>
						</thead>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h3>Accounts Used in Report</h3>
				
				{foreach from=$ACCOUNTSUSED key=title item=v}
				    <p><a href="index.php?module=Positions&action=positions&acct_number={$title}&record={$ACCOUNTNUMBER}">{$title} ({$v.acct_name}) {$v.nickname}</a></p>
				{/foreach}
			</td>
		</tr>
		{foreach from=$MESSAGES key=k item=v}
		    <tr>
		        <td colspan="2"><strong>{$v}</strong></td>
		    </tr>
		{/foreach}
	</thead>	
</table>
</div><!--portfolio_holdings-->
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}