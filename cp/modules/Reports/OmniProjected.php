<?php

$projected_graph = $data['projected_graph'];

$individual_projected = $data['individual_projected'];
$monthly_total = $data['monthly_total'];
$calendar = $data['calendar'];
$grand_total = $data['grand_total'];
echo "<input type='hidden' value='".$projected_graph."' id='estimate_graph_values' />";
?>


<div class="row">
	<div class="col-md-12">
	
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
							if(!empty($calendar)){
								foreach($calendar as $month_detail){
									echo '<th class="text-center">'.$month_detail['month_name'].'<br />'.$month_detail['year'].'</th>';
								}
							}
							?>
							<th>Year Total</th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if(!empty($individual_projected)){
							foreach($individual_projected as $account_number => $holder){
								foreach($holder as $info){
						?>
									<tr>
										<td><?php echo $info['security_symbol']; ?></td>
										<td><?php echo number_format($info['interest_rate'],2); ?></td>
										<td><?php echo number_format($info['quantity']); ?></td>
										<?php
											foreach($calendar as $month_detail){
												echo '<td>';
												foreach($info['pay_dates'] as $payDate){
													if($payDate['month'] == $month_detail['month'])
														echo number_format($info['estimate_payment_amount']);
												}
												echo '</td>';
											}
										?>
										<td><?php echo number_format($info['year_payment']); ?></td>
									</tr>
						<?php
								}
							}
						}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="3">&nbsp;</td>
							<?php 
							if(!empty($calendar)){
								foreach($calendar as $month_detail){
									echo "<td class='text-right'>";
									foreach($monthly_total as $month => $pd){
										if($month == $month_detail['month'])
											echo number_format($pd);
									}
									echo '</td>';
								}
							}
							?>
							<td class="text-right">$<?php echo number_format($grand_total) ;?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
					
	</div>
</div>