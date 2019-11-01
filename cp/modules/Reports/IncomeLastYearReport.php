<?php 
	$dynamic_graph = $data['dynamic_graph'];
	unset($data['dynamic_graph']);
	$data = json_encode($data);
	$data = json_decode($data, true);
	$monthly_totals = $data['monthly_totals'];
	$combined_symbols = $data['combined_symbols'];
	$year_end_totals = $data['year_end_totals'];
	$start_month = $data['start_month'];
	$end_month = $data['end_month'];
	
?>
<input type="hidden" value='<?php echo $dynamic_graph; ?>' id="estimate_graph_values" />


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
							<th>Name</th>
							<?php
								foreach($monthly_totals as $month_detail){
									echo '<th class="text-center">'.$month_detail['month'].'<br />'.$month_detail['year'].'</th>';
								}
							?>
							<th class="text-center">Total</th>
						</tr>
					</thead>
					<tbody>
						<?php 
							foreach($combined_symbols as $symbol => $symbol_values){
								echo "<tr><td>$symbol</td>
                                <td style='width:10%;'>".$symbol_values[0]['security_name']."</td>";
								foreach($monthly_totals as $mnth_info){
									echo "<td class='text-right'>";
									$found = 0;
									foreach($symbol_values as $symbol_value){
										if($mnth_info['year'] == $symbol_value['year'] && $mnth_info['month'] == $symbol_value['month']){
											echo '$'.number_format($symbol_value['amount']);
											$found = 1;
										}
									}
									if(!$found)
										echo "-";
									echo "</td>";
								}
								echo "<td class='text-right'>$".number_format($year_end_totals[$symbol])."</td></tr>";
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<?php 
								foreach($monthly_totals as $mnth_info){
									echo "<td class='text-right'>$".number_format($mnth_info['monthly_total'])."</td>";
								}
							?>
							<td class="text-right">$<?php echo number_format($data['grand_total']) ;?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</div>
					