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

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o"style="color: #44b6ae!important;"></i>
		Account Overview
	</h1>
</div>
<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
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
									<?php echo "<th>Trailing 3 (" . $t3Performance['start_date']." to ". $t3Performance['end_date'] .")</th>"; ?>
									<?php echo "<th>Year to Date (" . $t6Performance['start_date']." to ". $t6Performance['end_date'] .")</th>"; ?>
									<?php echo "<th>Trailing 12 (" . $t12Performance['start_date']." to ". $t12Performance['end_date'] .")</th>"; ?>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td>Beginning Value</td>
									<?php 
										echo "<td>$".number_format($t3Performance['beginning_values'],2)."</td>"; 
										echo "<td>$".number_format($t6Performance['beginning_values'],2)."</td>"; 
										echo "<td>$".number_format($t12Performance['beginning_values'],2)."</td>"; 
									?>
								</tr>
								
								<tr data-toggle="collapse" id="detail-row-flow" data-target=".detail-row-flow">
									<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Flow</td>
									<td>$<?php echo number_format($t3Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></td>
									<td>$<?php echo number_format($t6Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></td>
									<td>$<?php echo number_format($t12Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></td>
								</tr>
								<?php	
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
								?>
								
								<tr data-toggle="collapse" id="detail-row-income" data-target=".detail-row-income">
									<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Income</td>
									<td>$<?php echo number_format($t3Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></td>
									<td>$<?php echo number_format($t6Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></td>
									<td>$<?php echo number_format($t12Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></td>
								</tr>
								<?php	
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
								?>
								
								<tr data-toggle="collapse" id="detail-row-expense" data-target=".detail-row-expense">
									<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Expenses</td>
									<td>$<?php echo number_format($t3Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></td>
									<td>$<?php echo number_format($t6Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></td>
									<td>$<?php echo number_format($t12Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></td>
								</tr>
								<?php	
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
								?>
								
								<tr>
									<td>Ending Value</td>
									<td>$<?php echo number_format($t3Performance['ending_values'],2); ?></td>
									<td>$<?php echo number_format($t6Performance['ending_values'],2); ?></td>
									<td>$<?php echo number_format($t12Performance['ending_values'],2); ?></td>
								</tr>
								<tr>
									<td>Investment Return</td>
									<td>$<?php echo number_format($t3Performance['capital_appreciation'],2); ?></td>
									<td>$<?php echo number_format($t6Performance['capital_appreciation'],2); ?></td>
									<td>$<?php echo number_format($t12Performance['capital_appreciation'],2); ?></td>
								</tr>
								
								<tr data-toggle="collapse" id="detail-row-twr" data-target=".detail-row-twr">
									<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Time Weighted Return (as of <?php echo $t3Performance['interval_end_date']; ?>)</td>
									<td><?php echo number_format($t3Performance['twr'], 2, ".",","); ?>%</td>
									<td><?php echo number_format($t6Performance['twr'], 2, ".",","); ?>%</td>
									<td><?php echo number_format($t12Performance['twr'], 2, ".",","); ?>%</td>
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
									<?php echo "<td colspan='9'>Trailing 3 (" . $t3Performance['start_date']." to ". $t3Performance['end_date'] .")</th>"; ?>
								</tr>
								<?php
									$t3_individual_performance_summed = $individual_summary['t3']['individual_performance_summed'];
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
								?>
								<tr>
									<?php echo "<td colspan='9'>Year to Date (" . $t6Performance['start_date']." to ". $t6Performance['end_date'] .")</th>"; ?>
								</tr>
								<?php
									$t6_individual_performance_summed = $individual_summary['t6']['individual_performance_summed'];
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
								?>
								<tr>
									<?php echo "<td colspan='9'>Trailing 12 (" . $t12Performance['start_date']." to ". $t12Performance['end_date'] .")</th>"; ?>
								</tr>
								<?php
									$t12_individual_performance_summed = $individual_summary['t12']['individual_performance_summed'];
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
								?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
    var WizardDemo=function(){
    	jQuery(document).find("#m_wizard");
    	var e,r,i=jQuery(document).find("#m_form");
    	return{
    		init:function(){
    			var n;jQuery(document).find("#m_wizard"),
    			i=jQuery(document).find("#m_form"),
    			(r=new mWizard("m_wizard",{startStep:1})).on("beforeNext",function(r){}),
    			r.on("change",function(e){mUtil.scrollTop()}),
    			r.on("change",function(e){1===e.getStep()})
    		}
    	}
    }();
    jQuery(document).ready(function(){WizardDemo.init()});
</script>
<!-- <div class="row">
	<div class="col-md-12">
	
		<div class="idealsteps-container">
			<nav class="idealsteps-nav"></nav>
			<div class="reports_idealforms">
				<div class="idealsteps-wrap"> 
					
					<section class="idealsteps-step">
						
						<div class="form-group">
							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<div id="estimate_dynamic_pie" class="estimate_dynamic_pie" style="height: 350px;"></div>
								</div>	
								<div class="col-md-2"></div>
							</div>
						</div>
					</section>
					
					<section class="idealsteps-step">
						
						<div class="form-group">
							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-8">
									<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="height: 350px;"></div>
								</div>	
								<div class="col-md-2"></div>
							</div>
						</div>
					</section>
						   
					
					<section class="idealsteps-step">
						<div class="form-group">
							<div class="table-responsive">
								<table class="table table-bordered hodings_summary">
									<thead>
										<tr>
											<th>Performance</th>
											<?php echo "<th>Trailing 3 (" . $t3Performance['start_date']." to ". $t3Performance['end_date'] .")</th>"; ?>
											<?php echo "<th>Year to Date (" . $t6Performance['start_date']." to ". $t6Performance['end_date'] .")</th>"; ?>
											<?php echo "<th>Trailing 12 (" . $t12Performance['start_date']." to ". $t12Performance['end_date'] .")</th>"; ?>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td>Beginning Value</td>
											<?php 
												echo "<td>$".number_format($t3Performance['beginning_values'],2)."</td>"; 
												echo "<td>$".number_format($t6Performance['beginning_values'],2)."</td>"; 
												echo "<td>$".number_format($t12Performance['beginning_values'],2)."</td>"; 
											?>
										</tr>
										
										<tr data-toggle="collapse" id="detail-row-flow" data-target=".detail-row-flow">
											<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Flow</td>
											<td>$<?php echo number_format($t3Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></td>
											<td>$<?php echo number_format($t6Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></td>
											<td>$<?php echo number_format($t12Performance['performance_summed']['Flow']['amount'], 2, ".",","); ?></td>
										</tr>
										<?php	
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
										?>
										
										<tr data-toggle="collapse" id="detail-row-income" data-target=".detail-row-income">
											<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Income</td>
											<td>$<?php echo number_format($t3Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></td>
											<td>$<?php echo number_format($t6Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></td>
											<td>$<?php echo number_format($t12Performance['performance_summed']['Income']['amount'], 2, ".",","); ?></td>
										</tr>
										<?php	
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
										?>
										
										<tr data-toggle="collapse" id="detail-row-expense" data-target=".detail-row-expense">
											<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Expenses</td>
											<td>$<?php echo number_format($t3Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></td>
											<td>$<?php echo number_format($t6Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></td>
											<td>$<?php echo number_format($t12Performance['performance_summed']['Expense']['amount'], 2, ".",","); ?></td>
										</tr>
										<?php	
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
										?>
										
										<tr>
											<td>Ending Value</td>
											<td>$<?php echo number_format($t3Performance['ending_values'],2); ?></td>
											<td>$<?php echo number_format($t6Performance['ending_values'],2); ?></td>
											<td>$<?php echo number_format($t12Performance['ending_values'],2); ?></td>
										</tr>
										<tr>
											<td>Investment Return</td>
											<td>$<?php echo number_format($t3Performance['capital_appreciation'],2); ?></td>
											<td>$<?php echo number_format($t6Performance['capital_appreciation'],2); ?></td>
											<td>$<?php echo number_format($t12Performance['capital_appreciation'],2); ?></td>
										</tr>
										
										<tr data-toggle="collapse" id="detail-row-twr" data-target=".detail-row-twr">
											<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;Time Weighted Return (as of <?php echo $t3Performance['interval_end_date']; ?>)</td>
											<td><?php echo number_format($t3Performance['twr'], 2, ".",","); ?>%</td>
											<td><?php echo number_format($t6Performance['twr'], 2, ".",","); ?>%</td>
											<td><?php echo number_format($t12Performance['twr'], 2, ".",","); ?>%</td>
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
					</section>
					
					<section class="idealsteps-step">
						<div class="form-group">
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
											<?php echo "<td colspan='9'>Trailing 3 (" . $t3Performance['start_date']." to ". $t3Performance['end_date'] .")</th>"; ?>
										</tr>
										<?php
											$t3_individual_performance_summed = $individual_summary['t3']['individual_performance_summed'];
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
										?>
										<tr>
											<?php echo "<td colspan='9'>Year to Date (" . $t6Performance['start_date']." to ". $t6Performance['end_date'] .")</th>"; ?>
										</tr>
										<?php
											$t6_individual_performance_summed = $individual_summary['t6']['individual_performance_summed'];
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
										?>
										<tr>
											<?php echo "<td colspan='9'>Trailing 12 (" . $t12Performance['start_date']." to ". $t12Performance['end_date'] .")</th>"; ?>
										</tr>
										<?php
											$t12_individual_performance_summed = $individual_summary['t12']['individual_performance_summed'];
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
										?>
									</tbody>
								</table>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="tools pull-right">
			<a href="javascript:;" class="btn btn-lg default previous" style="background-color:#e1e5ec;">
				<i class="la la-arrow-circle-left " style="color: white;font-size: 2.5rem;"></i>
			</a>
			<a href="javascript:;" class="btn btn-lg green next" style="background-color:#32c5d2;">
				<i class="la la-arrow-circle-right " style="color: white;font-size: 2.5rem;"></i>
			</a>
		</div>
	</div>
</div>		 -->