<?php

$projected_graph = $data['projected_graph'];

$projected_income = $data['projected_income'];
$individual_projected = $projected_income->GetGroupedAccounts();
$monthly_total = $projected_income->GetMonthlyTotals();
$calendar = $data['calendar'];
$grand_total = $data['grand_total'];
echo "<input type='hidden' value='".$projected_graph."' id='estimate_graph_values' />";
?>

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o" style="color: #44b6ae!important;"></i>
		Projected Income
	</h1>
</div>
<div class="row">
	<div class="col-md-12">
	
		<div class="idealsteps-container">
			<nav class="idealsteps-nav"></nav>
			<div class="reports_idealforms">
				<div class="idealsteps-wrap"> 
					
					<section class="idealsteps-step">
						<div class="form-group">
							<div class="row">
								<div class="col-md-1"></div>
								<div class="col-md-10">
									<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="height: 350px;"></div>
								</div>	
								<div class="col-md-1"></div>
							</div>
						</div>
						<div class="form-group">
							<div class="table-responsive">
								<table class="table table-bordered">
									<thead>
										<tr>
											<th>Symbol</th>
											<th>Payment Rate ($)</th>
											<th>Quantity</th>
											<?php
												foreach($calendar as $month_detail){
													echo '<th class="text-center">'.$month_detail->month_name.'<br />'.$month_detail->year.'</th>';
												}
											?>
											<th>Year Total</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											foreach($individual_projected as $account_number => $holder){
												foreach($holder as $info){
										?>
													<tr>
														<td><?php echo $info->security_symbol; ?></td>
														<td><?php echo number_format($info->interest_rate,2); ?></td>
														<td><?php echo number_format($info->quantity,2); ?></td>
														<?php
															foreach($calendar as $month_detail){
																echo '<td>';
																foreach($info->pay_dates as $payDate){
																	if($payDate->month == $month_detail->month)
																		echo number_format($info->estimate_payment_amount,0);
																}
																echo '</td>';
															}
														?>
														<td><?php echo number_format($info->year_payment,0); ?></td>
													</tr>
										<?php
												}
											}
										?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="3">&nbsp;</td>
											<?php 
												foreach($calendar as $month_detail){
													echo "<td class='text-right'>";
													foreach($monthly_total as $month => $pd){
														if($month == $month_detail->month)
															echo number_format($pd,0);
													}
													echo '</td>';
												}
											?>
											<td class="text-right">$<?php echo number_format($grand_total,0) ;?></td>
										</tr>
									</tfoot>
								</table>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>
	</div>
</div>