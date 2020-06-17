<link rel="stylesheet" href="layouts/vlayout/modules/Omniscient/css/Report_Buttons.css" type="text/css" />
{foreach key=index item=cssModel from=$STYLES}
    <link rel="{$cssModel->getRel()}" href="{$cssModel->getHref()}" type="{$cssModel->getType()}" media="{$cssModel->getMedia()}" />
{/foreach}
<style>
	#portfolio_details table tr td{
		padding : 0px;
	}
	.acc_report td {
		border-top: none !important;
	}
	.acc_report {
		background: #f7f7f9;
	}
	.acc_report tbody tr:hover td,
	.acc_report tbody tr:hover th{
		background-color: initial !important;
	}
	
	.btm_rep {
		/*background-color: transparent !important;
		border-top: 1px solid #ddd;*/
	}
</style>
<input type="hidden" class="chartdata" value='{$CHARTDATA}' />

<div class="ReportTop" style="margin-bottom: 20px;">
	
	<div nowrap="" style="padding-left:10px;padding-right:50px;margin-top:10px;margin-bottom:10px;"><strong>As of: {$DATE}</strong></div>

	<table class="table table-bordered listViewEntriesTable acc_report" style="">
		<thead>
			<tr>
		        <td>
		            <input type="hidden" name="report" value="positions" />
		            <h4>Account Details</h4>
		        </td>
		        <td class="pull-right">
		        	<span class="btn-group pull-left">
		        		<input type="button" value="Report Settings" name="REPORT_SETTINGS" id="settings" class="btn"/>
		        	</span>
					<span class="btn-group pull-left">
						<form method="post" action="index.php?module=PortfolioInformation&action=PrintReport&account_number[]={$ACCT_DETAILS.number}" style="float:right;">
	                		<input type="submit" value="PRINT" name="PRINT" id="print_report" class="btn" />
		        		</form>
		        	</span>
				</td>
		    </tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">
					<div id="portfolio_details">
						<table>
 							<tr>
 								<td>      
									<table>
								        <tr>
									    	<td><label>Account Name</label></td>
									    	<td>{$ACCT_DETAILS.name}</td>
									    </tr>
								        <tr>
								        	<td><label>Account Number</label></td>
								        	<td>{$ACCT_DETAILS.number}</td>
								        </tr>
								        <tr>
								        	<td><label>Master Account</label></td>
								        	<td>{$ACCT_DETAILS.master_account}</td>
								        </tr>
								        <tr>
								        	<td><label>Custodian</label></td>
								        	<td>{$ACCT_DETAILS.custodian}</td>
								        </tr>
								        <tr>
								        	<td><label>Account Type</label></td>
								        	<td>{$ACCT_DETAILS.type}</td>
								        </tr>
								        <tr>
								        	<td><label>Management Fee</label></td>
								        	<td>{$ACCT_DETAILS.management_fee}</td>
								        </tr>
								        <tr>
								        	<td><label>Market Value</label></td>
								        	<td>${$ACCT_DETAILS.market_value|number_format:2}</td>
								        </tr>
								        <tr>
								        	<td><label>Cash Value</label></td>
								        	<td>${$ACCT_DETAILS.cash_value|number_format:2}</td>
								        </tr>
								        <tr>
								        	<td><label>Annual Management Fees (Trailing 12)</label></td>
								        	<td style="font-weight:bold; color:green;">${$ACCT_DETAILS.annual_fee|number_format:2}</td>
								        </tr>
								        <tr>
								        	<td><label>Total Value</label></td>
								        	<td>${$ACCT_DETAILS.total|number_format:2}</td>
								        </tr>
									</table>
								</td>
								<td>
									<table border="0" cellpadding="0" cellspacing="0" width="100%" style="float:left; margin-left:20px;">
								 		<tr>
								 			<td>
								 				<div id="report_top_pie" style="height: 320px; width: 450px; float:left; margin-top:-20px;"></div>
								 			</td>
								 		</tr> 
								 	</table>
								</td>
							</tr>
						</table>
					</div>
				</td>
			</tr>
			<!--  <tr>
				<td colspan="2">
					Account issue?  Try a 
					<span class="account_reconcile" data-number="{$ACCT_DETAILS.number}" style="color:blue;">
						<strong>reconcile</strong>
					</span>
				</td>
			</tr>-->
		</tbody>
	</table>
</div>
<div style="clear:both;"></div>

<table class="table table-bordered listViewEntriesTable" style="border-bottom: 0px; border-bottom-right-radius: 0px; border-bottom-left-radius: 0px;">
	<thead>
		<tr>
			<td colspan="5" style="padding: 10px;"><h3>Account Reports</h3></td>
		</tr>
		<tr>
			<td><a href="#" class="btn nav_report report_detail" onclick="return false;"><input type="hidden" class="report_type" value="holdings" /><input type="hidden" class="nav_page" value="2" />Holdings</a></td>
			<td><a href="#" class="btn nav_report report_detail" onclick="return false;"><input type="hidden" class="report_type" value="monthly_income" /><input type="hidden" class="nav_page" value="3" />Monthly Income</a></td>
			<td><a href="#" class="btn nav_report report_detail" onclick="return false;"><input type="hidden" class="report_type" value="performance" /><input type="hidden" class="nav_page" value="4" />Performance</a></td>
			<td><a href="#" class="btn nav_report report_detail" onclick="return false;"><input type="hidden" class="report_type" value="transactions" /><input type="hidden" class="nav_page" value="1" />Transactions</a></td>
			<td><a href="#" class="btn nav_report report_detail" onclick="return false;"><input type="hidden" class="report_type" value="overview" /><input type="hidden" class="nav_page" value="1" />Overview</a></td>
		</tr>
	</thead>
</table>
{foreach key=index item=jsModel from=$SCRIPTS}
    <script type="{$jsModel->getType()}" src="{$jsModel->getSrc()}"></script>
{/foreach}
