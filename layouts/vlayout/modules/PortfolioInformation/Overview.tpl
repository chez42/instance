<div class="PortfolioInformationOverviewReport">

	<input type="hidden" name="pids" value="{$PIDS}" />
	<input type="hidden" name="holdingschart" value='{$HOLDINGSCHART}' />
	<input type="hidden" name="incomechart" value='{$VALUEHISTORY}' />

	<div class="row-fluid ReportTitle detailViewTitle">
		<div class="span12">
			<div class="row-fluid">
				<div class="span6">
					<span class="row-fluid">
						<h4 class="recordspan pushDown"><span>Household Overview Client - {$CLIENT_NAME}</span></h4>
					</span>
					<span class="row-fluid"><span class="title_span">As of: {$AS_OF}</span></span>
				</div>
				<div class="span6">
					<div class="pull-right">
						<form id="OverviewForm" method="post">
							<input type="hidden" name="module" value="PortfolioInformation" />
							<input type="hidden" name="action" value="PrintReport" />
							<input type="hidden" name="report_type" value="overview" />
							<input type="hidden" name="client_name" value="{$CLIENT_NAME}" />
							<input type="hidden" name="account_number[]" value="{$ACCOUNT}" />

    						<input type="hidden" name="report" value="overview" />
						    
						    {foreach from=$ACCOUNTINFO key=title item=v}
						        <input type="hidden" class="overview_account_numbers" name="overview_account_numbers[]" value="{$title}" />
						    {/foreach}
    
						    {foreach from=$ALL_ACCOUNTS key=k item=v}
						        <input type="hidden" name="all_accounts[]" value="{$v}" />
						    {/foreach}

						    <div class="btn-toolbar">
								<span class="btn-group">
									<input type="button" name="print_pdf" value="Print PDF" class="btn" />
								</span>
							</div>
						</form>
					</div>
				</div>
    		</div>
    	</div>
    </div>
    
    <div class="detailViewInfo row-fluid">
		<div class="contents">
			<div class="row-fluid">

				<div class="idealsteps-container">
					<nav class="idealsteps-nav"></nav>
					<div class="reports_idealforms">
						<div class="idealsteps-wrap"> 
		
							<section class="idealsteps-step">
								<div class="row-fluid">
									<h3>Asset Allocation</h3>
									<div class="span2">&nbsp;</div>
									<div class="span8">
									    <div id="holdings_chart_overview" style="height: 500px;"></div>
									</div>
									<div class="span2">&nbsp;</div>
								</div>
							</section>
							
							<section class="idealsteps-step">
								<div class="row-fluid">
									<h3>Account Value Over Last 12 Months</h3>
									<div class="span2">&nbsp;</div>
									<div class="span8">
									    <div id="income_chart" style="height: 300px;"></div>
									</div>
									<div class="span2">&nbsp;</div>
								</div>
							</section>
							
							
							<section class="idealsteps-step">
								<div class="row-fluid marginBottom10px">
									<h3>Accounts Used in Report</h3>
								</div>
								<div class="span12" style="margin-left: 0px;">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>Account Name</th>
												<th>Account Number</th>
												<th>Nick Name</th>
											</tr>
										</thead>
										<tbody>
											{foreach from=$ACCOUNTINFO key=title item=v}
												<tr>
													<td>{$v.acct_name}</td>
													<td>{$title}</td>
													<td>{$v.nickname}</td>
												</tr>
											{/foreach}
										</tbody>
									</table>
								</div>
							</section>
						
							<section class="idealsteps-step clearfix">
								<div class="row-fluid marginBottom10px">
									<h3>Household Investment Returns</h3>
								</div>
								<div class="span12" style="margin-left: 0px;">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>&nbsp;</th>
												<th>
													<span>Last Year</span>
													<div>
														<span>({$LYR.start_date|replace:'00:00:00':''} to {$LYR.end_date|replace:'00:00:00':''})</span>
													</div>
												</th>
												<th>
													<span>Trailing 3</span>
													<div>
														<span>({$QTR.start_date|replace:'00:00:00':''} to {$QTR.end_date|replace:'00:00:00':''})</span>
													</div>
												</th>
												<th>
													<span>Trailing 12</span>
													<div>
														<span>({$TRAILING.start_date|replace:'00:00:00':''} to {$TRAILING.end_date|replace:'00:00:00':''})</span>
													</div>
												</th>
												<th>
													<span>YTD</span>
													<div>
														<span>({$YTD.start_date|replace:'00:00:00':''} to {$YTD.end_date|replace:'00:00:00':''})</span>
													</div>
												</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Beginning Value</td>
												<td>{$LYR.start_value|number_format:2}</td>
												<td>{$QTR.start_value|number_format:2}</td>
												<td>{$TRAILING.start_value|number_format:2}</td>
												<td>{$YTD.start_value|number_format:2}</td>
											</tr>
											<tr>
												<td>Net Contributions</td>
												<td>{$LYR.net_contributions|number_format:2}</td>
												<td>{$QTR.net_contributions|number_format:2}</td>
												<td>{$TRAILING.net_contributions|number_format:2}</td>
												<td>{$YTD.net_contributions|number_format:2}</td>
											</tr>
											<tr>
												<td>Short Values</td>
												<td>{$LYR.shorts|number_format:2}</td>
												<td>{$QTR.shorts|number_format:2}</td>
												<td>{$TRAILING.shorts|number_format:2}</td>
												<td>{$YTD.shorts|number_format:2}</td>
											</tr>
											<tr>
												<td>Net Income</td>
												<td>{$LYR.net_income|number_format:2}</td>
												<td>{$QTR.net_income|number_format:2}</td>
												<td>{$TRAILING.net_income|number_format:2}</td>
												<td>{$YTD.net_income|number_format:2}</td>
											</tr>
											<tr>
												<td>Capital Appreciation</td>
												<td>{$LYR.capital_appreciation|number_format:2}</td>
												<td>{$QTR.capital_appreciation|number_format:2}</td>
												<td>{$TRAILING.capital_appreciation|number_format:2}</td>
												<td>{$YTD.capital_appreciation|number_format:2}</td>
											</tr>
											<tr>
												<td>Ending Value</td>
												<td>{$LYR.end_value|number_format:2}</td>
												<td>{$QTR.end_value|number_format:2}</td>
												<td>{$TRAILING.end_value|number_format:2}</td>
												<td>{$YTD.end_value|number_format:2}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</section>	
							
							<section class="idealsteps-step clearfix">
								<div class="row-fluid marginBottom10px">
									<h3>Additional Details</h3>
								</div>
								<div class="span12" style="margin-left: 0px;">
									<table class="table table-bordered">
										<thead>
											<tr>
												<th>&nbsp;</th>
												<th>Last Year</th>
												<th>Trailing 3</th>
												<th>Trailing 12</th>
												<th>YTD</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td>Contributions</td>
												<td>{$LYR.contributions|number_format:2}</td>
												<td>{$QTR.contributions|number_format:2}</td>
												<td>{$TRAILING.contributions|number_format:2}</td>
												<td>{$YTD.contributions|number_format:2}</td>
											</tr>
											<tr>
												<td>Withdrawals</td>
												<td>{$LYR.withdrawals|number_format:2}</td>
												<td>{$QTR.withdrawals|number_format:2}</td>
												<td>{$TRAILING.withdrawals|number_format:2}</td>
												<td>{$YTD.withdrawals|number_format:2}</td>
											</tr>
											<tr>
												<td>Expenses</td>
												<td>{$LYR.expenses|number_format:2}</td>
												<td>{$QTR.expenses|number_format:2}</td>
												<td>{$TRAILING.expenses|number_format:2}</td>
												<td>{$YTD.expenses|number_format:2}</td>
											</tr>
											<tr>
												<td>Management Fee</td>
												<td>{$LYR.management_fee|number_format:2}</td>
												<td>{$QTR.management_fee|number_format:2}</td>
												<td>{$TRAILING.management_fee|number_format:2}</td>
												<td>{$YTD.management_fee|number_format:2}</td>
											</tr>
											<tr>
												<td>Other Expenses</td>
												<td>{$LYR.other_expenses|number_format:2}</td>
												<td>{$QTR.other_expenses|number_format:2}</td>
												<td>{$TRAILING.other_expenses|number_format:2}</td>
												<td>{$YTD.other_expenses|number_format:2}</td>
											</tr>
											<tr>
												<td>Income</td>
												<td>{$LYR.income|number_format:2}</td>
												<td>{$QTR.income|number_format:2}</td>
												<td>{$TRAILING.income|number_format:2}</td>
												<td>{$YTD.income|number_format:2}</td>
											</tr>
											<tr>
												<td>Investment Return</td>
												<td>{$LYR.investment_return|number_format:2}</td>
												<td>{$QTR.investment_return|number_format:2}</td>
												<td>{$TRAILING.investment_return|number_format:2}</td>
												<td>{$YTD.investment_return|number_format:2}</td>
											</tr>
										</tbody>
									</table>
								</div>
							</section>
							
							<section class="idealsteps-step clearfix">
								
								<div class="row-fluid marginBottom10px">
									<h3>Performance Summary</h3>
								</div>
								<div class="span12" style="margin-left: 0px;">
									<table class="table table-bordered">
										<thead>
											<tr>
										    	<th>Total portfolio</th>
										      	<th>Trailing 3</th>
										      	<th>Trailing 12</th>
										      	<th>YTD</th>
										      	<th>Inception</th>
										    </tr>
										</thead>
										<tbody>
											<tr>
										      <td>Time Weighted Return</td>
										      <td><div name="TWR_QTR_TYPE"></div><div name="TWR_QTR"><span name="QTR_CALCULATING">Calculating...</span></div></td>
										      <td><div name="TWR_TRAILING_TYPE"></div><div name="TWR_TRAILING"><span id="TRAILING_CALCULATING">Calculating...</span></div></td>
										      <td><div name="TWR_YTD_TYPE"></div><div name="TWR_YTD"><span name="YTD_CALCULATING">Calculating...</span></div></td>
										      <td><div name="TWR_INCEPTION_TYPE"></div><div name="TWR_INCEPTION"><span name="INCEPTION_CALCULATING">Calculating...</span></div></td>
										    </tr>
										    <tr>
										      <td>S&amp;P 500</td>
										      <td>{$QTR_REF}%</td>
										      <td><span name="TWR_WARNING" style="color:red;"></span>{$TRAILING_REF}%</td>
										      <td>{$YTD_REF}%</td>
										      <td>{$INCEPTION_REF}%</td>
										    </tr>
										    <tr class="hide">
										        <td colspan="5" name="WARNING" style="color:red;"></td>
										    </tr>
										    <tr>
										        <td>Barcap Aggregate Bond</td>
										        <td>{$QTR_BAB}%</td>
										        <td>{$TRAILING_BAB}%</td>
										        <td>{$YTD_BAB}%</td>
										        <td>{$INCEPTION_BAB}%</td>
										    </tr>
										</tbody>
									</table>
								</div>
							</section>
						</div>
					</div>
				</div>
			</div>
			
			<div class="btn-toolbar pull-right">
				<span class="btn-group pull-left">
					<button class="btn previous" id="ReportPreviousButton"><i class="icon-chevron-left"></i></button>
				</span>
				<span class="btn-group pull-right">	
					<button class="btn next" id="ReportNextButton"><i class="icon-chevron-right"></i></button>
				</span>
			</div>
			
		</div>
	</div>
</div>