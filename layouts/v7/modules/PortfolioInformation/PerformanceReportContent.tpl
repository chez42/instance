			{literal}
				<style>
					.overview_performance > thead>tr>th {border: 0px !important;padding:4px !important;}
					.overview_performance > tbody>tr>td {border: 0px  !important;padding:4px !important;}
				</style>
			{/literal}

			
			<div id="LHCoverPage" class = "col-xs-12" style = "display:flex;vertical-align:middle;margin-top:200px;">
				
				<div class="CoverPageLogo col-xs-8">
					<img src = "{$COVER_LOGO}" style = "width:100%;"/>
				</div>
				
				<div class="LHPreparedSection col-xs-4" style = "margin:auto;text-align:center;font-weight:800;color:black;{if $IS_PDF eq "1"}padding-top:40px;{/if}">
					<div>
						
						<div style = "font-size:25px;line-height:45px;">{'Portfolio Review'}</div>
						<div style = "font-size:18px;">{$PREPARED_FOR}</div>
						
						<div style = "font-size:18px;font-weight:normal;">{$CLIENT_ADDRESS}</div>
						<div style = "font-size:18px;font-weight:normal;">{$CLIENT_ADDRESS2}</div>
						
						
						<div style = "margin-top:50px;font-size:12px;">
							<div style = "line-height:20px;">Prepared By: {$PREPARED_BY}</div>
							<div>{$COVERPAGE->GetPreparedDate()}</div>
						</div>
					</div>
				</div>
			</div>
			
			<div style="page-break-after: always;">&nbsp;</div>
			
			<div class = "row">
				<div class = "col-xs-12">
					<div class = "col-xs-12" style = "font-size:25px;font-weight:600;margin-bottom:20px;text-align:center;">
						Portfolio Overview
					</div>
					{if $IS_PDF neq ""}
						<div style = "position:absolute;right:15;font-weight:600;font-size:14px;text-align:right;padding-top:10px;">
							<div>Period Ending: {$END_DATE}</div>
							<div style = "margin-top:5px;">Portfolio Inception Date: {$INCEPTION_DATE}</div>
						</div>
					{/if}
				</div>
			</div>
			
			<div class = "col-xs-12">
				
				<div class = "col-xs-6">
					
					<div style = "font-size:18px;font-weight:800;margin-bottom:10px;">Asset Allocation</div>
					
					<input type="hidden" value='{$HOLDINGSSECTORPIESTRING}' id="sector_values" class="sector_values" />
					
					<div id="sector_pie_holder" class="sector_pie_holder" style="height:200px; width:450px;"></div>
					
					<table class="table table-bordered DynaTable table-collapse">
						<thead>
						<tr>
							<th>Category</th>
							<th style="text-align:right;">Current Percentage</th>
							<th style="text-align:right;">Current Value</th>
						</tr>
						</thead>
						{foreach from=$HOLDINGSSECTORPIEARRAY key=k item=heading}
							<tr style="background-color:{$heading['color']}; color:white;">
								<td>{$heading['title']}</td>
								<td style="text-align:right;">{$heading['percentage']}%</td>
								<td style="text-align:right;">${$heading['value']|number_format:0:".":","}</td>
							</tr>
						{/foreach}
					</table>
				</div>
		
				<div class = "col-xs-6">
				
					<div style = "font-size:18px;font-weight:800;margin-bottom:10px;">Components of Change</div>
					
					{assign var = "last_month_performance_summed" value = $LAST_MONTH_PERFORMANCE->GetPerformanceSummed()}
					{assign var = "year_to_date_performance_summed" value = $YEAR_TO_DATE_PERFORMANCE->GetPerformanceSummed()}
					{assign var = "since_inception_performance_summed" value = $SINCE_INCEPTION_PERFORMANCE->GetPerformanceSummed()}

					{assign var = "last_month_performance" value = $LAST_MONTH_PERFORMANCE->GetPerformance()}
					{assign var = "year_to_date_performance" value = $YEAR_TO_DATE_PERFORMANCE->GetPerformance()}
					{assign var = "since_inception_performance" value = $SINCE_INCEPTION_PERFORMANCE->GetPerformance()}
					
					<table id="overview_performance" class="overview_performance table">
						<thead>
							<tr>
								<th style="font-weight:bold; vertical-align:top; text-align:left;">&nbsp;</th>
								<th style="font-weight:bold;text-align:left;">{'Last Month'}</th>
								<th style="font-weight:bold;text-align:left;">{'Year to Date'}</th>
								<th style="font-weight:bold;text-align:right;">{'Since Inception'}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="font-weight:bold;">Beginning Value</td>
								<td style="text-align:left; font-weight:bold;">${$LAST_MONTH_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:0:".":","}</td>
								<td style="text-align:left; font-weight:bold;">${$YEAR_TO_DATE_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:0:".":","}</td>
								<td style="text-align:right; font-weight:bold;">${$SINCE_INCEPTION_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:0:".":","}</td>
							</tr>
							<tr>
								<td>Net Contributions</td>
								<td style="text-align:left;">${$last_month_performance_summed.Flow->amount|number_format:0:".":","}</td>
								<td style="text-align:left;">${$year_to_date_performance_summed.Flow->amount|number_format:0:".":","}</td>
								<td style="text-align:right;">${$since_inception_performance_summed.Flow->amount|number_format:0:".":","}</td>
							</tr>
							<tr>
								<td style="font-weight:bold;">Ending Value</td>
								<td style="text-align:left;font-weight:bold;">${$LAST_MONTH_PERFORMANCE->GetEndingValuesSummed()->value|number_format:0:".":","}</td>
								<td style="text-align:left;font-weight:bold;">${$YEAR_TO_DATE_PERFORMANCE->GetEndingValuesSummed()->value|number_format:0:".":","}</td>
								<td style="text-align:right;font-weight:bold;">${$SINCE_INCEPTION_PERFORMANCE->GetEndingValuesSummed()->value|number_format:0:".":","}</td>
							</tr>
							<tr>
								<td style="font-weight:bold;">Investment Gain</td>
								<td style="text-align:left;font-weight:bold;">${$LAST_MONTH_PERFORMANCE->GetInvestmentGain()|number_format:0:".":","}</td>
								<td style="text-align:left;font-weight:bold;">${$YEAR_TO_DATE_PERFORMANCE->GetInvestmentGain()|number_format:0:".":","}</td>
								<td style="text-align:right;font-weight:bold;">${$SINCE_INCEPTION_PERFORMANCE->GetInvestmentGain()|number_format:0:".":","}</td>
							</tr>
						</tbody>
					</table>
					
					<input type="hidden" value='{$BAR_CHART_STRING}' id="PORTFOLIO_PERFORMANCE_VALUE"/>
					
					<div style = "font-size:18px;font-weight:800;margin-bottom:10px;">Portfolio Returns</div>
					
					<div id="portfolio_return" class="portfolio_return" style="height:200px;"></div>
					
					<table class="table overview_performance">
						<thead>
							<tr>
								<th style="font-weight:bold; vertical-align:top; text-align:left;">&nbsp;</th>
								<th style="font-weight:bold;text-align:left;">{'Last Month'}</th>
								<th style="font-weight:bold;text-align:left;">{'Year to Date'}</th>
								<th style="font-weight:bold;text-align:right;">{'Since Inception'}</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td style="font-weight:bold;"><div style = "float:left;width:60%;">Your Portfolio</div><div style = "float:left;margin-top:2px;width:15px;height:15px;background:#023c05;">&nbsp;</div></td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['PR']}%</td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['PR']}%</td>
								<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['PR']}%</td>
							</tr>
							<tr>
								<td style="font-weight:bold;"><div style = "float:left;width:60%;">S&P 500</div><div style = "float:left;margin-top:2px;width:15px;height:15px;background:#0d023c;">&nbsp;</div></td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['GSPC']}%</td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['GSPC']}%</td>
								<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['GSPC']}%</td>
							</tr>
							<tr>
								<td style="font-weight:bold;"><div style = "float:left;width:60%;">AGG</div><div style = "float:left;margin-top:2px;width:15px;height:15px;background:#b95504;">&nbsp;</div></td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['AGG']}%</td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['AGG']}%</td>
								<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['AGG']}%</td>
							</tr>
							<tr>
								<td colspan = "4" style = "font-size:13px;">All returns are Time-Weighted Return, net of fees. </td>
							</tr>
						</tbody>
					</table>
					
				</div>
			</div>
		
			<div style="page-break-after: always;">&nbsp;</div>
			
			<div class = "row">
				<div class = "col-xs-12">
					<div class = "col-xs-12" style = "font-size:25px;font-weight:600;margin-bottom:20px;text-align:center;">
						Asset Allocation
					</div>
					{if $IS_PDF neq ""}
						<div style = "position:absolute;right:15;font-weight:600;font-size:14px;text-align:right;padding-top:10px;">
							<div>Period Ending: {$END_DATE}</div>
							<div style = "margin-top:5px;">Portfolio Inception Date: {$INCEPTION_DATE}</div>
						</div>
					{/if}
				</div>
			</div>
			
			<div class = "col-xs-12">
				
				<div class = "col-xs-6">
					<div id="sector_pie_holder2" class="sector_pie_holder" style="height:450px; width:450px;"></div>
				</div>
				
				<div class = "col-xs-6">
					<div class = "col-xs-12" style = "margin-bottom:5px;margin-top:5px;">
						<div class = "col-xs-2" style="text-align:right;font-weight:800;">Weight</div>
						<div class = "col-xs-7" style="text-align:left;font-weight:800;">Description</div>
						<div class = "col-xs-3" style="text-align:right;font-weight:800;">Current Value</div>
					</div>
					
					<div class = "col-xs-12" style = "margin-bottom:10px;margin-top:10px;">
						<div class = "col-xs-2" style="text-align:right;font-weight:800;">{$ASSET_TOTAL_PERCENTAGE}%</div>
						<div class = "col-xs-7" style="text-align:left;font-weight:800;">Portfolio Total </div>
						<div class = "col-xs-3" style="text-align:right;font-weight:800;">${$GRAND_TOTAL}</div>
					</div>
					
					{foreach from=$HOLDINGSSECTORPIEARRAY key=k item=heading}
						<div class = "col-xs-12" style="margin-bottom:5px;margin-top:5px;border-radius:5px;padding:7px;background-color:{$heading['color']}; color:white;">
							<div class = "col-xs-2" style="text-align:right;">{$heading['percentage']}%</div>
							<div class = "col-xs-7">{$heading['title']}</div>
							<div class = "col-xs-3" style="text-align:right;">${$heading['value']|number_format:0:".":","}</div>
						</div>
					{/foreach}
				</div>
			</div>
			
			<div style="page-break-after: always;">&nbsp;</div>
			
			<div class = "row">
				<div class = "col-xs-12">
					<div class = "col-xs-12" style = "font-size:25px;font-weight:600;margin-bottom:20px;text-align:center;">
						Account Performance Summary
					</div>
					
					<div style = "position:absolute;right:15;font-weight:600;font-size:14px;text-align:right;padding-top:10px;">
						<div>Period Ending: {$END_DATE}</div>
						<div style = "margin-top:5px;">Portfolio Inception Date: {$INCEPTION_DATE}</div>
					</div>
				</div>
			</div>
			
			<div class = "col-xs-12">
				<table class="table table-bordered" style="width:100%;">
					<thead>
						<tr>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:25%; text-align:left">Account Description</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:left">Account Key</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:left">Account Type</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:left">Inception Date</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:left">Current Value</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:left">Last Month</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:left">Year to Date</th>
							<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:right">Since Inception</th>
						</tr>
					</thead>
				
					<tbody>
						
						{foreach from=$ACCOUNT_SUMMARY item=v}
							<tr>
								
								<td style="text-align:left;">{$v.account_title1}</td>
								<td style="text-align:left;">{$v.account_number}</td>
								<td style="text-align:left;">{$v.account_type}</td>
								<td style="text-align:left;">{$v.inception_date}</td>
								
								<td style="text-align:left;">${$v.current_value|number_format:2:".":","}</td>
								
								<td style="text-align:left;">{$v.last_month}%</td>
								<td style="text-align:left;">{$v.year_to_date}%</td>
								<td style="text-align:right;">{$v.since_inception}%</td>
							
							</tr>
						{/foreach}
							<tr>
								
								<td style="text-align:left;">S&P 500</td>
								<td style="text-align:left;">&nbsp;</td>
								<td style="text-align:left;">&nbsp;</td>
								<td style="text-align:left;">&nbsp;</td>
								
								<td style="text-align:left;">&nbsp;</td>
								
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['GSPC']}%</td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['GSPC']}%</td>
								<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['GSPC']}%</td>
							
							</tr>
							<tr>
								
								<td style="text-align:left;">AGG</td>
								<td style="text-align:left;">&nbsp;</td>
								<td style="text-align:left;">&nbsp;</td>
								<td style="text-align:left;">&nbsp;</td>
								
								<td style="text-align:left;">&nbsp;</td>
								
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['GSPC']}%</td>
								<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['GSPC']}%</td>
								<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['GSPC']}%</td>
							
							</tr>
					</tbody>
				</table>
			</div>	
		
		
			<div style="page-break-after: always;">&nbsp;</div>
		
			<div class = "row">
				<div class = "col-xs-12">
					<div class = "col-xs-12" style = "font-size:25px;font-weight:600;margin-bottom:20px;text-align:center;">
						Performance Summary
					</div>
					<div style = "position:absolute;right:15;font-weight:600;font-size:14px;text-align:right;padding-top:10px;">
						<div>Period Ending: {$END_DATE}</div>
						<div style = "margin-top:5px;">Portfolio Inception Date: {$INCEPTION_DATE}</div>
					</div>
				</div>
			</div>
			
			<div class = "col-xs-12">
			
				<div style = "font-size:18px;font-weight:800;margin-bottom:10px;text-align:left;">Components Of Change</div>
			
				<div class = "row">
					<div class = "col-xs-2">
						&nbsp;
					</div>
				
					<div class = "col-xs-8">
						<table id="overview_performance" class="table">
							<thead>
								<tr>
									<th style="font-weight:bold; vertical-align:top; text-align:left; font-size:16px;">&nbsp;</th>
									<th style="font-weight:bold;text-align:right;">{'Last Month'}</th>
									<th style="font-weight:bold;text-align:right;">{'Year to Date'}</th>
									<th style="font-weight:bold;text-align:right;">{'Since Inception'}</th>
								</tr>
							</thead>
							<tbody>
								<tr data-id="1" data-parent="">
									<td style="font-weight:bold;">Beginning Value</td>
									<td style="text-align:right;font-weight:bold;">${$LAST_MONTH_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</td>
									<td style="text-align:right;font-weight:bold;">${$YEAR_TO_DATE_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</td>
									<td style="text-align:right;font-weight:bold;">${$SINCE_INCEPTION_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}</td>
								</tr>
								<tr data-id="2" data-parent="">
									<td style="">Net Contributions</td>
									<td style="text-align:right;">${$last_month_performance_summed.Flow->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$year_to_date_performance_summed.Flow->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$since_inception_performance_summed.Flow->amount|number_format:2:".":","}</td>
								</tr>
								<tr data-id="4" data-parent="">
									<td style="">Capital Appreciation</td>
									<td style="text-align:right;">${$LAST_MONTH_PERFORMANCE->getCapAppreciation()|number_format:2:".":","}</td>
									<td style="text-align:right;">${$YEAR_TO_DATE_PERFORMANCE->getCapAppreciation()|number_format:2:".":","}</td>
									<td style="text-align:right;">${$SINCE_INCEPTION_PERFORMANCE->getCapAppreciation()|number_format:2:".":","}</td>
								</tr>
								<tr data-id="3" data-parent="">
									<td style="">Income</td>
									<td style="text-align:right;">${$last_month_performance_summed.income_div_interest->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$year_to_date_performance_summed.income_div_interest->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$since_inception_performance_summed.income_div_interest->amount|number_format:2:".":","}</td>
								</tr>
								
								
								<tr data-id="4" data-parent="">
									<td style="">Management Fees</td>
									<td style="text-align:right;">${$last_month_performance_summed.Reversal->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$year_to_date_performance_summed.Reversal->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$since_inception_performance_summed.Reversal->amount|number_format:2:".":","}</td>
								</tr>
								
								<tr data-id="4" data-parent="">
									<td style="">Other Expenses</td>
									<td style="text-align:right;">${$last_month_performance_summed.Expense->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$year_to_date_performance_summed.Expense->amount|number_format:2:".":","}</td>
									<td style="text-align:right;">${$since_inception_performance_summed.Expense->amount|number_format:2:".":","}</td>
								</tr>
								
								<tr data-id="6" data-parent="">
									<td style="font-weight:bold;">Ending Value</td>
									<td style="text-align:right;font-weight:bold;">${$LAST_MONTH_PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</td>
									<td style="text-align:right;font-weight:bold;">${$YEAR_TO_DATE_PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</td>
									<td style="text-align:right;font-weight:bold;">${$SINCE_INCEPTION_PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}</td>
								</tr>
								<tr data-id="5" data-parent="">
									<td style="font-weight:bold;">Investment Gain</td>
									<td style="text-align:right;font-weight:bold;">${$LAST_MONTH_PERFORMANCE->GetInvestmentGain()|number_format:2:".":","}</td>
									<td style="text-align:right;font-weight:bold;">${$YEAR_TO_DATE_PERFORMANCE->GetInvestmentGain()|number_format:2:".":","}</td>
									<td style="text-align:right;font-weight:bold;">${$SINCE_INCEPTION_PERFORMANCE->GetInvestmentGain()|number_format:2:".":","}</td>
								</tr>
							</tbody>
						</table>
					</div>
			
					<div class = "col-xs-2">
						&nbsp;
					</div>
				</div>
			
				<div style = "font-size:18px;font-weight:800;margin-bottom:10px;text-align:left;">Portfolio Returns</div>
			
				<div class = "row">
					<div class = "col-xs-2">
						&nbsp;
					</div>
					<div class = "col-xs-8">
						<table id="overview_performance" class="table">
							<thead>
								<tr>
									<th style="font-weight:bold; vertical-align:top; text-align:left; font-size:16px;">&nbsp;</th>
									<th style="font-weight:bold;text-align:left;">{'Last Month'}</th>
									<th style="font-weight:bold;text-align:left;">{'Year to Date'}</th>
									<th style="font-weight:bold;text-align:right;">{'Since Inception'}</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td style="font-weight:bold;"><div style = "float:left;width:60%;">Your Portfolio</div></td>
									<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['PR']}%</td>
									<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['PR']}%</td>
									<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['PR']}%</td>
								</tr>
								<tr>
									<td style="font-weight:bold;"><div style = "float:left;width:60%;">S&P 500</div></td>
									<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['GSPC']}%</td>
									<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['GSPC']}%</td>
									<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['GSPC']}%</td>
								</tr>
								<tr>
									<td style="font-weight:bold;"><div style = "float:left;width:60%;">AGG</div></td>
									<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[0]['AGG']}%</td>
									<td style="text-align:left;">{$PORTFOLIO_RETURNS_DATA[1]['AGG']}%</td>
									<td style="text-align:right;">{$PORTFOLIO_RETURNS_DATA[2]['AGG']}%</td>
								</tr>
							</tbody>
						</table>
					</div>
				
					<div class = "col-xs-2">
						&nbsp;
					</div>
				</div>
			</div>
		
		
			<div style="page-break-after: always;">&nbsp;</div>
		
		
		
			<div class = "row">
				<div class = "col-xs-12">
					<div class = "col-xs-12" style = "font-size:25px;font-weight:600;margin-bottom:20px;text-align:center;">
						Portfolio Holdings
					</div>
					<div style = "position:absolute;right:15;font-weight:600;font-size:14px;text-align:right;padding-top:10px;">
						<div>Period Ending: {$END_DATE}</div>
						<div style = "margin-top:5px;">Portfolio Inception Date: {$INCEPTION_DATE}</div>
					</div>
				</div>
			</div>
			
			<div class = "col-xs-12">
				<table style = "width:100%;">
					<thead>
						<tr>
							<th>
								<div class = "row">
									<div class = "col-xs-3" style = "font-weight:800;">
										Description
									</div>
									<div class = "col-xs-2" style = "font-weight:800;">
										Symbol
									</div>
									<div class = "col-xs-1" style = "font-weight:800;">
										Weight
									</div>
									<div class = "col-xs-1" style = "font-weight:800;">
										Quantity
									</div>
									<div class = "col-xs-2" style = "font-weight:800;">
										Price
									</div>
									<div class = "col-xs-3" style = "font-weight:800;">
										Current Value
									</div>
									<!-- <div class = "col-xs-2" style = "font-weight:800;display:none;">
										Current Yield
									</div> -->
								</div>
							</th>
						</tr>
					</thead>
					
					<tbody>
					
						<tr>
							<th>
								<div class = "row">
									<div class = "col-xs-3" style = "font-weight:800;">
										Portfolio Total
									</div>
									<div class = "col-xs-2" style = "font-weight:800;">
										&nbsp;
									</div>
									<div class = "col-xs-1" style = "font-weight:800;">
										{$ASSET_TOTAL_PERCENTAGE}%
									</div>
									<div class = "col-xs-1" style = "font-weight:800;">
										&nbsp;
									</div>
									<div class = "col-xs-2" style = "font-weight:800;">
										&nbsp;
									</div>
									<div class = "col-xs-3" style = "font-weight:800;">
										${$GRAND_TOTAL|number_format:2:".":","}
									</div>
									<!-- <div class = "col-xs-2" style = "font-weight:800;display:none;">
										&nbsp;
									</div> -->
								</div>
							</th>
						</tr>
					
					
						{foreach from=$HOLDINGS key = a_class item = v}
						<tr>
							<td>
								<div class = "row" style = padding:10px;border-radius:5px;background-color:{$v.color};color:white;margin-top:5px;margin-bottom:5px;">
								
									<div class = "col-xs-3">
										{$a_class}
									</div>
									<div class = "col-xs-2">
										&nbsp;
									</div>
									<div class = "col-xs-1">
										{$v.weight}%
									</div>
									<div class = "col-xs-1">
										&nbsp;
									</div>
									<div class = "col-xs-2">
										&nbsp;
									</div>
									<div class = "col-xs-3">
										${$v.current_value|number_format:2:".":","}
									</div>
									<!-- <div class = "col-xs-2" style = "display:none;">
										&nbsp;
									</div> -->
								</div>
							</td>
						</tr>
						{foreach from = $v.data item = h_data}
							<tr>
								<td>
							<div class = "row" style = padding:10px;">
								<div class = "col-xs-3">
									{$h_data.security_name}
								</div>
								<div class = "col-xs-2">
									{$h_data.symbol}
								</div>
								<div class = "col-xs-1">
									{$h_data.weight}%
								</div>
								<div class = "col-xs-1">
									{$h_data.quantity|number_format:3:".":""}
								</div>
								<div class = "col-xs-2">
									{if $h_data.price neq ''}
										${$h_data.price|number_format:2:".":","}
									{/if}
								</div>
								<div class = "col-xs-3">
									{$h_data.value|number_format:2:".":","}
								</div>
								<!-- <div class = "col-xs-2" style = "display:none;">
									{$h_data.current_yield}%
								</div>-->
							</div>
								</td>
							</tr>
						{/foreach}
					{/foreach}
				</table>
			</div>
	
		
		
		
		
	{literal}
		<script>
			
			am4core.options.commercialLicense = true;
			
			var chart = am4core.create("sector_pie_holder", am4charts.PieChart);
			
			var chartData = $.parseJSON($("#sector_values").val());
			
			if(chartData === null) { } else {
				chart.data = chartData;
				chart.depth = 10;
				chart.angle = 10;
			
				var pieSeries = chart.series.push(new am4charts.PieSeries());
			
				pieSeries.slices.template.stroke = am4core.color("#555354");
				pieSeries.dataFields.value = "value";
				pieSeries.dataFields.category = "title";
				pieSeries.fontSize = 14;
				pieSeries.slices.template.strokeWidth = 2;
				pieSeries.slices.template.strokeOpacity = 1;
				pieSeries.labels.horizontalCenter = 'middle';
				pieSeries.labels.verticalCenter = 'middle';
				pieSeries.labels.template.disabled = true;
				pieSeries.ticks.template.disabled = true;
				var colorSet = new am4core.ColorSet();
				var colors = [];
				
				$.each(chartData,function(){
					var element = jQuery(this);
					colors.push(element["0"].color);
				});

				colorSet.list = colors.map(function(color) {
					return new am4core.color(color);
				});
				pieSeries.colors = colorSet;

				self.assetPieChart = chart;
			}
			
			
			var chart = am4core.create("sector_pie_holder2", am4charts.PieChart);
			
			var chartData = $.parseJSON($("#sector_values").val());
			
			if(chartData === null) { } else {
				chart.data = chartData;
				chart.depth = 10;
				chart.angle = 10;
				var pieSeries = chart.series.push(new am4charts.PieSeries());
				pieSeries.slices.template.stroke = am4core.color("#555354");
				pieSeries.dataFields.value = "value";
				pieSeries.dataFields.category = "title";
				pieSeries.fontSize = 14;
				pieSeries.slices.template.strokeWidth = 2;
				pieSeries.slices.template.strokeOpacity = 1;
				pieSeries.labels.horizontalCenter = 'middle';
				pieSeries.labels.verticalCenter = 'middle';
				pieSeries.labels.template.disabled = true;
				pieSeries.ticks.template.disabled = true;
				var colorSet = new am4core.ColorSet();
				var colors = [];
				
				$.each(chartData,function(){
					var element = jQuery(this);
					colors.push(element["0"].color);
				});

				colorSet.list = colors.map(function(color) {
					return new am4core.color(color);
				});
				pieSeries.colors = colorSet;

				self.assetPieChart = chart;
			}
			
			
			
			var chartData = $.parseJSON($("#PORTFOLIO_PERFORMANCE_VALUE").val());
			
			if(chartData === null) { } else {
				
				var chart = am4core.create('portfolio_return', am4charts.XYChart)
				
				chart.data = chartData;
				
				chart.colors.list = [
				  am4core.color("#023C05"),
				  am4core.color("#023C05"),
				  am4core.color("#0D023C"),
				  am4core.color("#b95504"),
				];
				
				chart.colors.step = 2;

				var xAxis = chart.xAxes.push(new am4charts.CategoryAxis())
				xAxis.dataFields.category = 'category'
				xAxis.renderer.cellStartLocation = 0.1
				xAxis.renderer.cellEndLocation = 0.9
				xAxis.renderer.grid.template.location = 0;

				var yAxis = chart.yAxes.push(new am4charts.ValueAxis());
				
				function createSeries(value, name) {
					var series = chart.series.push(new am4charts.ColumnSeries())
					series.dataFields.valueY = value
					series.dataFields.categoryX = 'category'
					series.name = name
					return series;
				}
				
				createSeries('PR', 'The First');
				createSeries('GSPC', 'The Second');
				createSeries('AGG', 'The Third');
			
			}
			
			
			
		</script>
	{/literal}