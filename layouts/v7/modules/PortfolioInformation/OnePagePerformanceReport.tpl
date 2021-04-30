


	

	{if $IS_PDF eq ""}
		<div style = "float:left;margin:15px;padding:10px;border-radius:5px;background-color:white;">
	{/if}
	
			{if $IS_PDF eq ""}
			
			<div id="dateselection" class = "col-md-12" style = "margin-bottom:10px;">
				<div class = "col-md-8">
					<form method="GET" id="calculate">
						<input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
						<input type="hidden" value="PortfolioInformation" name="module" />
						<input type="hidden" value="OnePagePerformanceReport" name="view" />
						<table class="dateselectiontable">
							<tr>
								<td>
									<input type="text" id="select_start_date" name = "report_start_date" readonly value="{$START_DATE}" style="display:block; margin-right:5px;" />
								</td>
								<td>
									<input type="text" id="select_end_date" name  = "report_end_date" readonly value="{$END_DATE}" class = "inputElement"/>
								</td>
								<td>
									<input type="button" class = "btn btn-info" id="calculate_report" value="Calculate" style = "margin-left:10px;" />
								</td>
							</tr>
						</table>
					</form>
				</div>
				
				<div class = "col-md-4" style = "text-align:right;">
					<button class="btn btn-info ExportReport"><strong>Generate PDF</strong></button>
					<form method="post" id="export">
						<input type="hidden" value='{$ACCOUNT_NUMBER}' name="account_number" id="account_number" />
						<input type="hidden" value="PortfolioInformation" name="module" />
						<input type="hidden" value="OnePagePerformanceReport" name="view" />
						<input type="hidden" value="1" name="pdf" />
					</form>
				</div>
			</div>
			{/if}
			
			{assign var = "selected_period_performance_summed" value = $SELECTED_PERIOD_PERFORMANCE->GetPerformanceSummed()}
			
			<div class = "row">
				<div class = "col-xs-12">
					<div style = "font-size:20px;font-weight:600;margin-bottom:20px;text-align:center;">
						Portfolio Performance Review
					</div>
					<div style = "font-size:14px;font-weight:600;margin-bottom:20px;text-align:left;">
						{$PREPARED_FOR}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$PORTFOLIO_TYPE}&nbsp;&nbsp;&nbsp;&nbsp;Acct #:&nbsp;&nbsp;{$ACCOUNT_NUMBER}
					</div>
				</div>
			</div>
			<div class = "col-xs-8">
			<div class = "row">
				<div class = "col-xs-6">
					<div style = "font-size:14px;font-weight:600;margin-bottom:20px;text-align:left;border-bottom:1px solid black;">
						{$REPORT_PERIOD}
					</div>
				</div>
			</div>
			
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Beginning Value
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$SELECTED_PERIOD_PERFORMANCE->GetBeginningValuesSummed()->value|number_format:2:".":","}
				</div>
			</div>
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Net Contributions
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$selected_period_performance_summed.Flow->amount|number_format:2:".":","}
				</div>
			</div>
			
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Capital Appreciation
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$SELECTED_PERIOD_PERFORMANCE->getCapAppreciation()|number_format:2:".":","}
				</div>
			</div>
			
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Income
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$selected_period_performance_summed.income_div_interest->amount|number_format:2:".":","}
				</div>
			</div>
			
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Management Fees
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$selected_period_performance_summed.Reversal->amount|number_format:2:".":","}
				</div>
			</div>
			
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Other Expenses
				</div>
				<div class = "col-xs-3" style = "border-bottom:1px solid black;text-align:right;">
					${$selected_period_performance_summed.Expense->amount|number_format:2:".":","}
				</div>
			</div>
			
			
			
			<div class = "col-xs-12">
				<div class = "col-xs-9">
					Ending Value
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$SELECTED_PERIOD_PERFORMANCE->GetEndingValuesSummed()->value|number_format:2:".":","}
				</div>
			</div>
			
			<div class = "col-xs-12" style = "background-color:RGB(245, 245, 245);">
				<div class = "col-xs-9">
					Investment Gain
				</div>
				<div class = "col-xs-3" style = "text-align:right;">
					${$SELECTED_PERIOD_PERFORMANCE->GetInvestmentGain()|number_format:2:".":","}
				</div>
			</div>
			
			
			</div>
			
			{literal}
				<style>
					.performance_table > thead>tr>th {border: 0px !important;}
					.performance_table > tbody>tr>td {border: 0px  !important;}
					.table>thead>tr>th, .table>tbody>tr>th, .table>tfoot>tr>th, .table>thead>tr>td, .table>tbody>tr>td, .table>tfoot>tr>td {padding:0px;}
				</style>
			{/literal}
			<div class = "col-xs-9" style = "margin-top:20px;">
			<table class="performance_table" style = "width:100%;font-size:14px;" >
				<tr>
					<td style = "border-bottom: 1px solid black !important;padding:0px;font-weight:600;">
						Total Portfolio
					</td>
					<td style = "border-bottom: 1px solid black !important;padding:0px;font-weight:600;">
						Inception
					</td>
					<td style = "border-bottom: 1px solid black !important;padding:0px;font-weight:600;">
						Last 12 Months
					</td>
					<td style = "border-bottom: 1px solid black !important;padding:0px;font-weight:600;">
						YTD
					</td>
					<td style = "border-bottom: 1px solid black !important;font-weight:600;">
						Last 3 Months
					</td>
				</tr>
				<tr>
					<td style = "padding-top:10px;">
						TWR
					</td>
					<td  style = "padding-top:10px;">
						{$SINCE_INCEPTION_PERFORMANCE->GetTWR()|number_format:2:".":","}%
					</td>
					<td  style = "padding-top:10px;">
						{$LAST_12_MONTHS_PERFORMANCE->GetTWR()|number_format:2:".":","}%
					</td>
					<td  style = "padding-top:10px;">
						{$YTD_PERFORMANCE->GetTWR()|number_format:2:".":","}%
					</td>
					<td  style = "padding-top:10px;">
						{$LAST_3_MONTHS_PERFORMANCE->GetTWR()|number_format:2:".":","}%
					</td>
				</tr>
				<tr>
					<td>
						S&P 500
					</td>
					<td>
						{$INDEX_RETURN_DATA[2]['GSPC']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[0]['GSPC']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[1]['GSPC']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[3]['GSPC']}%
					</td>
				</tr>
				
				<tr>
					<td>
						Barcap Aggregate Bond
					</td>
					<td>
						{$INDEX_RETURN_DATA[2]['AGG']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[0]['AGG']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[1]['AGG']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[3]['AGG']}%
					</td>
				</tr>
				<tr>
					<td>
						50/50 Stocks/Bonds
					</td>
					<td>
						{$INDEX_RETURN_DATA[2]['50/50']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[0]['50/50']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[1]['50/50']}%
					</td>
					<td>
						{$INDEX_RETURN_DATA[3]['50/50']}%
					</td>
				</tr>
			</table>
			</div>
			<div class = "col-xs-12" style = "margin-top:20px;">
				Returns for periods exceeding 12 months are annualized
			</div>
			<div class = "col-xs-12">
				All returns net of fees
			</div>
			
			{literal}
				<script>
					jQuery(document).ready(function($) {
						 $(".ExportReport").click(function(e){
							e.stopImmediatePropagation();
							$("#export").submit();
						});
						$("#calculate_report").click(function(e){
							e.stopImmediatePropagation();
							$("#calculate").submit();
						});
						$("#select_start_date").datepicker({
							format: 'yyyy-mm-dd',
						});

						$("#select_end_date").datepicker({
							format: 'yyyy-mm-dd',
						});
					});	
				</script>
			{/literal}
			
				