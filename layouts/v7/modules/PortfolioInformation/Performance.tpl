<table class="table table-bordered listViewEntriesTable" style="border-top: 0px; border-top-right-radius: 0px; border-top-left-radius: 0px;">
	<thead>
		<tr>
			<td colspan="4">
				<h3>Performance Review</h3>
			</td>
		</tr>
		<tr>
			<form method="post" action="index.php?module=PortfolioInformation&action=PrintReport&report_type=performance&account_number[]={$ACCOUNT}&calling_record={$CALLING_RECORD}">
				<td><input type="checkbox" name="enable_goal" value="1" /><span>Print Goal</span></td>
			    <td><input type="checkbox" name="enable_inception" value="1" /><span>Print Inception</span></td>
			    <td><input type="checkbox" name="enable_expenses" value="1" /><span>Separate Expenses</span></td>
				<td><input type="submit" class="btn" name="print_pdf" value="Print PDF" /></td>
			</form>
		</tr>
	</thead>
</tabel>
<div id="portfolio_holdings">
	<input type="hidden" name="pids" value="{$PIDS}" />
	<form method="POST">
	    <input type="hidden" name="report" value="performance" />
	</form>
	<table class="table table-bordered listViewEntriesTable" id="investmentreturns" style="border-top: 0px; border-top-right-radius: 0px; border-top-left-radius: 0px;">
		<thead>		
			<tr>
		    	<th colspan="2">
		      		<h4>
			            {$INCEPTION.start_date|replace:'00:00:00':''} - 
			            {$INCEPTION.end_date|replace:'00:00:00':''}
		    		</h4>
		    	</th>
		    </tr>
		</thead>
		<tbody>
		    <tr>
		        <td>Beginning Value</td>
		        <td>{$INCEPTION.start_value|number_format:2}</td>
		    </tr>
		    <tr>
		        <td>Net Contributions</td>
		        <td>{$INCEPTION.net_contributions|number_format:2}</td>
		    </tr>
		    <tr>
		        <td>Capital Appreciation</td>
		        <td>{$INCEPTION.capital_appreciation|number_format:2}</td>
		    </tr>
		    <tr>
		        <td>Income</td>
		        <td>{$INCEPTION.income|number_format:2}</td>
		    </tr>
		    <tr>
		        <td>
		        	<label>Expenses</label>
		        	<label>&emsp;Managemenet Fees ({abs($INCEPTION.management_total)|number_format:2})</label>
		        	<label>&emsp;Other Expenses ({abs($INCEPTION.other_expenses)|number_format:2})</label>
	        	</td>
		        <td>{$INCEPTION.expenses|number_format:2}</td>
		    </tr>
	        <tr>
		        <td>Ending Value</td>
		        <td>{$INCEPTION.end_value|number_format:2}</td>
		    </tr>
		    <tr>
		        <td>Investment Return</td>
		        <td>{$INCEPTION.investment_return|number_format:2}</td>
		    </tr>
		</tbody>
	</table>
</div>
		
<table class="table table-bordered listViewEntriesTable" style="margin-top:20px;">
	<thead>
		<tr>
            <th>Total Portfolio</th>
            <th>Trailing 3 Mos</th>
            <th>Trailing 12 Mos</th>
            <th>Year to Date</th>
            <th>Inception</th>
        </tr>
	</thead>
	<tbody>
        <tr>
	    	<td>Time Weighted Return (net)</td>
	      	<td><div name="TWR_QTR_TYPE"></div><div name="TWR_QTR"><span name="QTR_CALCULATING">Calculating...</span></div></td>
	      	<td><div name="TWR_TRAILING_TYPE"></div><div name="TWR_TRAILING"><span name="TRAILING_CALCULATING">Calculating...</span></div></td>
	      	<td><div name="TWR_YTD_TYPE"></div><div name="TWR_YTD"><span name="YTD_CALCULATING">Calculating...</span></div></td>
	      	<td><div name="TWR_INCEPTION_TYPE"></div><div name="TWR_INCEPTION"><span name="INCEPTION_CALCULATING">Calculating...</span></div></td>
        </tr>
        <tr>
            <td align="left" valign="top">S&amp;P 500</td>
            <td>{$QTR_REF}%</td>
            <td><div name="TWR_WARNING" style="color:red; display:inline;"></div>{$TRAILING_REF}%</td>
            <td>{$YTD_REF}%</td>
            <td>{$INCEPTION_REF}%</td>
        </tr>
        <tr class="hide">
        	<td name="WARNING" colspan="5" style='color:red;'>&nbsp;</td>
        </tr>
        <tr>
            <td>Barcap Aggregate Bond</td>
            <td>{$QTR_BAB}%</td>
            <td>{$TRAILING_BAB}%</td>
            <td>{$YTD_BAB}%</td>
            <td>{$INCEPTION_BAB}%</td>
        </tr>
        <tr>
            <td>Goal %</td>
            <td>{$GOAL}%</td>
            <td>{$GOAL}%</td>
            <td>{$GOAL}%</td>
            <td>{$GOAL}%</td>
        </tr>
		{if $ACCOUNTINFO|@count gt '0'}
			<tr>
				<td colspan="5">			
				    {foreach from=$ACCOUNTINFO item=v}
				        <p><a href="index.php?module=Portfolios&action=AccountView&acct={$v.account_number}&parenttab=Sales">{$v.account_name} ({$v.account_number})</a></p>
				    {/foreach}
		    	</td>
			</tr>
		{/if}
	</tbody>
</table>

{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}