<?php

$holdings_values = $data['holdingspievalues'];
$t12balances = $data['t12balances'];

unset($data['holdingspievalues']);
unset($data['t12balances']);

$data = json_encode($data);
$data = json_decode($data, true);


$performance_summary = $data['performance_summary'];
$t3Performance = $performance_summary['t3'];
$t6Performance = $performance_summary['t6'];
$t12Performance = $performance_summary['t12'];

$tablecategories = $data['tablecategories'];
$individual_summary = $data['individual_summary'];
?>

<input type="hidden" value='<?php echo $holdings_values; ?>' id="dynamic_pie_values" class="dynamic_pie_values" />
<input type="hidden" id="t12_balances" class="t12_balances" value='<?php echo $t12balances; ?>' />
<input type='hidden' value='overview' id='report_type' />
<div class="kt-portlet" style = "padding-top:0px;">
	<div class="kt-portlet__body">
		<div class="form-group m-form__group row">
			
			<div class="col-sm-3">
				<input type="text" id="select_end_date" class="form-control" value="<?php echo $data['END_DATE'];?>">
			</div>
		
			<div class="col-sm-3"><input type="button" class="btn btn-info" id="calculate_report" value="Calculate" /></div>
		</div>
	</div>
</div>
<div class="kt-section">
	<h2>
		<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <rect x="0" y="0" width="24" height="24"></rect>
                <rect fill="#000000" opacity="0.3" x="13" y="4" width="3" height="16" rx="1.5"></rect>
                <rect fill="#000000" x="8" y="9" width="3" height="11" rx="1.5"></rect>
                <rect fill="#000000" x="18" y="11" width="3" height="9" rx="1.5"></rect>
                <rect fill="#000000" x="3" y="13" width="3" height="7" rx="1.5"></rect>
            </g>
        </svg>
		Account Overview
	</h2>
</div>

<div class="kt-portlet__body">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<div id="estimate_dynamic_pie" class="estimate_dynamic_pie" style="height: 350px;"></div>
		</div>	
		<div class="col-md-2"></div>
	</div>
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="height: 350px;"></div>
		</div>	
		<div class="col-md-2"></div>
	</div>
	<div class="row">
		<div class="table-responsive">
			<table class="table table-bordered hodings_summary">
				<thead>
					<tr>
						<th>Performance</th>
						<?php echo "<th>Trailing 3 (" . $t3Performance['user_start_date']." to ". $t3Performance['user_end_date'] .")</th>"; ?>
						<?php echo "<th>Year to Date (" . $t6Performance['user_start_date']." to ". $t6Performance['user_end_date'] .")</th>"; ?>
						<?php echo "<th>Trailing 12 (" . $t12Performance['user_start_date']." to ". $t12Performance['user_end_date'] .")</th>"; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><strong>Beginning Value</strong></td>
						<?php 
							echo "<td><strong>$".number_format($t3Performance['beginning_values'],2)."</strong></td>"; 
							echo "<td><strong>$".number_format($t6Performance['beginning_values'],2)."</strong></td>"; 
							echo "<td><strong>$".number_format($t12Performance['beginning_values'],2)."</strong></td>"; 
						?>
					</tr>
					
					<tr data-toggle="collapse" id="detail-row-flow" data-target=".detail-row-flow">
						<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;<strong>Flow</strong></td>
						<td><strong>$<?php echo number_format($t3Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></strong></td>
						<td><strong>$<?php echo number_format($t6Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></strong></td>
						<td><strong>$<?php echo number_format($t12Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></strong></td>
					</tr>
					<?php	
					if(!empty($tablecategories)){
						foreach($tablecategories as $cat_index => $table_category){
							if($cat_index == 'Flow'){
								foreach($table_category as $value){
					?>
									<tr class="holdings collapse detail-row-flow">
										<td><?php echo $value; ?></td>
										<td>$<?php echo number_format($t3Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
										<td>$<?php echo number_format($t6Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
										<td>$<?php echo number_format($t12Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
									</tr>
									
					<?php
								}
							}
						}
					}
					?>
					
					<tr data-toggle="collapse" id="detail-row-income" data-target=".detail-row-income">
						<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;<strong>Income</strong></td>
						<td><strong>$<?php echo number_format($t3Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></strong></td>
						<td><strong>$<?php echo number_format($t6Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></strong></td>
						<td><strong>$<?php echo number_format($t12Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></strong></td>
					</tr>
					<?php	
					if(!empty($tablecategories)){
						foreach($tablecategories as $cat_index => $table_category){
							if($cat_index == 'Income'){
								foreach($table_category as $value){
					?>
									<tr class="holdings collapse detail-row-income">
										<td><?php echo $value; ?></td>
										<td>$<?php echo number_format($t3Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
										<td>$<?php echo number_format($t6Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
										<td>$<?php echo number_format($t12Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
									</tr>
									
					<?php
								}
							}
						}
					}
					?>
					
					<tr data-toggle="collapse" id="detail-row-expense" data-target=".detail-row-expense">
						<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;<strong>Expenses</strong></td>
						<td><strong>$<?php echo number_format($t3Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></strong></td>
						<td><strong>$<?php echo number_format($t6Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></strong></td>
						<td><strong>$<?php echo number_format($t12Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></strong></td>
					</tr>
					<?php	
					if(!empty($tablecategories)){
						foreach($tablecategories as $cat_index => $table_category){
							if($cat_index == 'Expense'){
								foreach($table_category as $value){
					?>
									<tr class="holdings collapse detail-row-expense">
										<td><?php echo $value; ?></td>
										<td>$<?php echo number_format($t3Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
										<td>$<?php echo number_format($t6Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
										<td>$<?php echo number_format($t12Performance['performance'][$cat_index][$value]['amount'],2); ?></td>
									</tr>
									
					<?php
								}
							}
						}
					}
					?>
					
					<tr>
						<td><strong>Ending Value</strong></td>
						<td><strong>$<?php echo number_format($t3Performance['ending_values'],2); ?></strong></td>
						<td><strong>$<?php echo number_format($t6Performance['ending_values'],2); ?></strong></td>
						<td><strong>$<?php echo number_format($t12Performance['ending_values'],2); ?></strong></td>
					</tr>
					<tr>
						<td><strong>Investment Return</strong></td>
						<td><strong>$<?php echo number_format($t3Performance['capital_appreciation'],2); ?></strong></td>
						<td><strong>$<?php echo number_format($t6Performance['capital_appreciation'],2); ?></strong></td>
						<td><strong>$<?php echo number_format($t12Performance['capital_appreciation'],2); ?></strong></td>
					</tr>
					
					<tr data-toggle="collapse" id="detail-row-twr" data-target=".detail-row-twr">
						<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;<strong>Time Weighted Return (as of <?php echo $t3Performance['interval_end_date']; ?>)</strong></td>
						<td><strong><?php echo number_format($t3Performance['twr'], 2, ".",","); ?>%</strong></td>
						<td><strong><?php echo number_format($t6Performance['twr'], 2, ".",","); ?>%</strong></td>
						<td><strong><?php echo number_format($t12Performance['twr'], 2, ".",","); ?>%</strong></td>
					</tr>

					<tr class="holdings collapse detail-row-twr">
						<td>S&P 500</td>
						<td><?php echo number_format($t3Performance['sp500'],2); ?>%</td>
						<td><?php echo number_format($t6Performance['sp500'],2); ?>%</td>
						<td><?php echo number_format($t12Performance['sp500'],2); ?>%</td>
					</tr>
					<tr class="holdings collapse detail-row-twr">
						<td>AGG</td>
						<td><?php echo number_format($t3Performance['agg'],2); ?>%</td>
						<td><?php echo number_format($t6Performance['agg'],2); ?>%</td>
						<td><?php echo number_format($t12Performance['agg'],2); ?>%</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="table-responsive">
			<table class="table table-bordered hodings_summary">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Account Number</th>
						<th>Beginning Balance</th>
						<th>Flow</th>
						<th>Income</th>
						<th>Expenses</th>
						<th>Ending Value</th>
						<th>Investment Return</th>
						<th>TWR</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<?php echo "<td colspan='9'>Trailing 3 (" . date('M y',strtotime($t3Performance['start_date']))." to ". date('M y',strtotime($t3Performance['end_date'])) .")</th>"; ?>
					</tr>
					<?php
						$t3_individual_performance_summed = $individual_summary['t3']['individual_performance_summed'];
						if(!empty($t3_individual_performance_summed)){
    						foreach($t3_individual_performance_summed as $account_number => $val){
    					?>
    							<tr>
    								<td>&nbsp;</td>
    								<td><?php echo $account_number; ?></td>
    								<td>$<?php echo number_format($individual_summary['t3']['begin_values'][$account_number]['value'], 2); ?></td>
    								<td>$<?php echo number_format($t3_individual_performance_summed[$account_number]['Flow']['amount'],2); ?></td>
    								<td>$<?php echo number_format($t3_individual_performance_summed[$account_number]['Income']['amount'],2); ?></td>
    								<td>$<?php echo number_format($t3_individual_performance_summed[$account_number]['Expense']['amount'],2); ?></td>
    								<td>$<?php echo number_format($individual_summary['t3']['end_values'][$account_number]['value'], 2); ?></td>
    								<td>$<?php echo number_format($individual_summary['t3']['appreciation'][$account_number], 2); ?></td>
    								<td><?php echo number_format($individual_summary['t3']['twr'][$account_number], 2); ?>%</td>
    							</tr>
    					<?php											
    						}
						}
					?>
					<tr>
						<?php echo "<td colspan='9'>Year to Date (" . date('M y',strtotime($t6Performance['start_date']))." to ". date('M y',strtotime($t6Performance['end_date'])) .")</th>"; ?>
					</tr>
					<?php
						$t6_individual_performance_summed = $individual_summary['t6']['individual_performance_summed'];
						if(!empty($t6_individual_performance_summed)){
    						foreach($t6_individual_performance_summed as $account_number => $val){
    					?>
    							<tr>
    								<td>&nbsp;</td>
    								<td><?php echo $account_number; ?></td>
    								<td>$<?php echo number_format($individual_summary['t6']['begin_values'][$account_number]['value'], 2); ?></td>
    								<td>$<?php echo number_format($t6_individual_performance_summed[$account_number]['Flow']['amount'],2); ?></td>
    								<td>$<?php echo number_format($t6_individual_performance_summed[$account_number]['Income']['amount'],2); ?></td>
    								<td>$<?php echo number_format($t6_individual_performance_summed[$account_number]['Expense']['amount'],2); ?></td>
    								<td>$<?php echo number_format($individual_summary['t6']['end_values'][$account_number]['value'], 2); ?></td>
    								<td>$<?php echo number_format($individual_summary['t6']['appreciation'][$account_number], 2); ?></td>
    								<td><?php echo number_format($individual_summary['t6']['twr'][$account_number], 2); ?>%</td>
    							</tr>
    					<?php											
    						}
						}
					?>
					<tr>
						<?php echo "<td colspan='9'>Trailing 12 (" . date('M y',strtotime($t12Performance['start_date']))." to ". date('M y',strtotime($t12Performance['end_date'])) .")</th>"; ?>
					</tr>
					<?php
						$t12_individual_performance_summed = $individual_summary['t12']['individual_performance_summed'];
						if(!empty($t12_individual_performance_summed)){
    						foreach($t12_individual_performance_summed as $account_number => $val){
    					?>
    							<tr>
    								<td>&nbsp;</td>
    								<td><?php echo $account_number; ?></td>
    								<td>$<?php echo number_format($individual_summary['t12']['begin_values'][$account_number]['value'], 2); ?></td>
    								<td>$<?php echo number_format($t12_individual_performance_summed[$account_number]['Flow']['amount'],2); ?></td>
    								<td>$<?php echo number_format($t12_individual_performance_summed[$account_number]['Income']['amount'],2); ?></td>
    								<td>$<?php echo number_format($t12_individual_performance_summed[$account_number]['Expense']['amount'],2); ?></td>
    								<td>$<?php echo number_format($individual_summary['t12']['end_values'][$account_number]['value'], 2); ?></td>
    								<td>$<?php echo number_format($individual_summary['t12']['appreciation'][$account_number], 2); ?></td>
    								<td><?php echo number_format($individual_summary['t12']['twr'][$account_number], 2); ?>%</td>
    							</tr>
    					<?php											
    						}
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
