<?php
$ytdperformance = $data['ytdperformance'];
$ytd_individual_performance_summed = $data['ytd_individual_performance_summed'];
$ytd_begin_values = $data['ytd_begin_values'];
$ytd_end_values = $data['ytd_end_values'];
$ytd_appreciation = $data['ytd_appreciation'];
$ytd_appreciation_percent = $data['ytd_appreciation_percent'];
$ytd_twr = $data['ytd_twr'];
$ytd_performance_summed = $data['ytd_performance_summed'];

$holdingspiearray = $data['holdingspiearray'];
$holdingspievalues = $data['holdingspievalues'];
$holdingssectorpiestring = $data['holdingssectorpiestring'];

$date_options = $data['date_options'];

echo "<input type='hidden' value='".$holdingspievalues."' id='holdings_values' class='holdings_values' />";
echo "<input type='hidden' value='".$holdingssectorpiestring."' id='sector_values' class='sector_values' />";
echo "<input type='hidden' value='gh2report' id='report_type' />"
    
?>
<style>	
	#gh2report_chart_table .borderless td, 
	#gh2report_chart_table .borderless>thead>tr>th {
		border: 0;
	}
</style>

<div class="kt-portlet">
	<div class="kt-portlet__body">
		<div class="form-group  kt-form__group row">
			<div class="col-sm-3">
				<select id="report_date_selection" class="form-control">
					<?php
						
						$selectedDate = isset($data['selectedDate'])?$data['selectedDate']:"";
						
						foreach($date_options as $option){
							
							if($selectedDate && $selectedDate == $option["option_value"])
								$selected = "selected";
							else
								$selected = "";
								
							echo '<option value="'.$option["option_value"].'" data-start_date="'.$option['date']['start'].'" data-end_date="'.$option['date']['end'].'" '.$selected.'>'.$option['option_name'].'</option>';
						}
					?>
				</select>
			</div>
			
			<?php 
				if($data['show_start_date'])
					echo '<div class="col-sm-3"><input type="text" id="select_start_date" class="form-control" value="'.$data['start_date'].'"></div>';
			
				if($data['show_end_date'])
					echo '<div class="col-sm-3"><input type="text" id="select_end_date" value="'.$data['end_date'].'" class="form-control"></div>';
			?>
			<div class="col-sm-3"><input type="button" id="calculate_report" class="btn btn-info" value="Calculate" /></div>
		</div>
	</div>
</div>

<div class="kt-portlet__body">
	<div class="row">
	<h2 style="width:100%; background-color:lightblue; text-align:center;">PORTFOLIO SUMMARY</h2>
		<div class="col-md-5">
			<div class="table-responsive report_desc" id="gh2report_chart_table">
				<table class="table borderless">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th><b>VALUE</b></th>
							<th><b>ALLOC</b></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if(!empty($holdingspiearray)){
							foreach($holdingspiearray as $pie_data){
								echo "<tr>".
									"<td style='color:".$pie_data['color']."'><b>".$pie_data['title']."</b></td>".
									"<td style='color:".$pie_data['color']."'>$".number_format($pie_data['value'])."</td>".
									"<td style='color:".$pie_data['color']."'>".number_format($pie_data['percentage'], 2)."%</td>".
								"</tr>";
							}
						
						?>
						<tr>
							<td>&nbsp;</td>
							<td>$<?php echo number_format($data['globaltotal']); ?></td>
							<td>&nbsp;</td>
						</tr>
						<?php /*
							$dividendAmount = $data['dividendAmount'];
							if($dividendAmount):
						?>
							<tr>
								<td>Dividend Accrual:</td>
								<td>$<?php echo number_format($dividendAmount, 2); ?></td>
								<td>&nbsp;</td>
							</tr>
						<?php endif; */
						}?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="col-md-7">
			<div id="dynamic_pie_holder" class="dynamic_pie_holder" style="height: 400px; width:550px;"></div>
		</div>	
	</div>
	<div class="row">
		<h2 style="width:100%; background-color:lightgrey; text-align:center;padding:10px;">
    		<span style="font-size:13px;">
    			<?php
    				echo "PERFORMANCE (".date('F d, Y', strtotime($data['GetStartDate']))." to ".date('F d, Y', strtotime($data['GetEndDate'])).")";
    			?>
			</span>
		</h2>
		<div class="table-responsive">
			<table class="table table-bordered hodings_summary">
				<thead>
					<tr>
						<th><b>Account Number</b></th>
						<th><b>Name</b></th>
						<th><b>Beginning Balance</b></th>
						<th><b>Flow</b></th>
						<th><b>Income</b></th>
						<th><b>Ending Value</b></th>
						<th><b>Investment Return</b></th>
						<th><b>TWR</b></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					if(!empty($ytd_individual_performance_summed)){
    						foreach($ytd_individual_performance_summed as $account_number => $v){
    							echo "<tr>";
    							echo "<td>**".substr($account_number, 5).' ('.$ytd_individual_performance_summed[$account_number]['account_type'].')'."</td>";
    							echo "<td>".$ytd_individual_performance_summed[$account_number]['account_name']."</td>";
    							echo "<td style='text-align:right;'>".'('.date('m/d/Y',strtotime($ytd_begin_values[$account_number]['date'])).') $'.number_format($ytd_begin_values[$account_number]['value'])."</td>";
    							echo "<td style='text-align:right;'>$".number_format($ytd_individual_performance_summed[$account_number]['Flow']['amount'])."</td>";
    							echo "<td style='text-align:right;'>$".number_format($ytd_individual_performance_summed[$account_number]['income_div_interest']['amount'])."</td>";
    							echo "<td style='text-align:right;'>$".number_format($ytd_end_values[$account_number]['value'])."</td>";
    							echo "<td style='text-align:right;'>$".number_format($ytd_appreciation[$account_number])."</td>";
    							echo "<td style='text-align:right;'>".number_format($ytd_twr[$account_number], 2)."%</td>";
    							echo "</tr>";
    						}
    					?>
    					<tr>
    						<td colspan="2"><b>Blended Portfolio Return</b></td>
    						<td style='text-align:right;'><b>$<?php echo number_format($data['GetBeginningValuesSummed']['value']);?></b></td>
    						<td style='text-align:right;'><b>$<?php echo number_format($ytd_performance_summed['Flow']['amount']);?></b></td>
    						<td style='text-align:right;'><b>$<?php echo number_format($ytd_performance_summed['income_div_interest']['amount']);?></b></td>
    						<td style='text-align:right;'><b>$<?php echo number_format($data['GetEndingValuesSummed']['value']);?></b></td>
    						<td style='text-align:right;'><b>$<?php echo number_format($data['GetCapitalAppreciation']);?></b></td>
    						<td style='text-align:right;'><b><?php echo number_format($data['GetTWR'],2);?>%</b></td>
    					</tr>
    					<tr>
    						<td colspan="7">S&amp;P 500</td>
    						<td style='text-align:right;'><b><?php echo number_format($data['GetGSPC'],2);?>%</b></td>
    						<!-- <td><?php //echo number_format($data['GetIndexSP'],2);?>%</td> -->
    					</tr>
    					<tr>
    						<td colspan="7">Barclays Aggregate Bond</td>
    						<td style='text-align:right;'><b><?php echo number_format($data['GetIndexAGG'],2);?>%</b></td>
    					</tr>
    					<tr>
    						<td colspan="7">MSCI Emerging Market index</td>
    						<td style='text-align:right;'><b><?php echo number_format($data['GetIndexEEM'],2);?>%</b></td>
    					</tr>
    					<tr>
    						<td colspan="7">MSCI EAFE index</td>
    						<td style='text-align:right;'><b><?php echo number_format($data['GetMSCIEAFE'],2);?>%</b></td>
    						<!-- <td><?php //echo number_format($data['GetIndexMSCI_EAFE'],2);?>%</td> -->
    					</tr>
    					<!--<tr>
    						<td colspan="7"><b>Blended Benchmark Return</b></td>
    						<td><b><?php //echo number_format($data['GetBenchmark'],2);?>%</b></td>
    					</tr>-->
					<?php }?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="col-md-6">
			<div id="sector_pie_holder" class="sector_pie_holder" style="height: 350px;"></div>
		</div>
		<div class="col-md-6">
			<div id="gh2_AllocationTypesWrapper"></div>
		</div>
	</div>
</div>

