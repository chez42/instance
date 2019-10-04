<?php
$ytdperformance = $data['ytdperformance'];
$ytd_individual_performance_summed = $ytdperformance->GetIndividualSummedBalance();
$ytd_begin_values = $ytdperformance->GetIndividualBeginValues();
$ytd_end_values = $ytdperformance->GetIndividualEndValues();
$ytd_appreciation = $ytdperformance->GetIndividualCapitalAppreciation();
$ytd_appreciation_percent = $ytdperformance->GetIndividualCapitalAppreciationPercent();
$ytd_twr = $ytdperformance->GetIndividualTWR();
$ytd_performance_summed = $ytdperformance->GetPerformanceSummed();

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
<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o"style="color: #44b6ae!important;"></i>
		PORTFOLIO SUMMARY
	</h1>
</div>
<div class="m-portlet">
	<div class="m-portlet__body">
		<div class="form-group  m-form__group row">
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
<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
					<div class="row">
						<div class="col-md-4">
							<div class="table-responsive report_desc" id="gh2report_chart_table">
								<table class="table borderless">
									<thead>
										<tr>
											<th>&nbsp;</th>
											<th>VALUE</th>
											<th>ALLOC</th>
										</tr>
									</thead>
									<tbody>
										<?php 
											foreach($holdingspiearray as $pie_data){
												echo "<tr>".
													"<td>".$pie_data['title']."</td>".
													"<td>$".number_format($pie_data['value'],2)."</td>".
													"<td>".number_format($pie_data['percentage'], 2)."%</td>".
												"</tr>";
											}
										?>
										<tr>
											<td>&nbsp;</td>
											<td>$<?php echo number_format($data['globaltotal'], 2); ?></td>
											<td>&nbsp;</td>
										</tr>
										<?php 
											$dividendAmount = $ytdperformance->GetDividendAccrualAmount();
											if($dividendAmount):
										?>
											<tr>
												<td>Dividend Accrual:</td>
												<td>$<?php echo number_format($dividendAmount, 2); ?></td>
												<td>&nbsp;</td>
											</tr>
										<?php endif; ?>
									</tbody>
								</table>
							</div>
						</div>
						<div class="col-md-8">
							<div id="dynamic_pie_holder" class="dynamic_pie_holder" style="height: 350px;"></div>
						</div>	
					</div>
					<div class="row">
						<h3 class="control-label">
							<?php
								echo "PERFORMANCE(".date('F, Y', strtotime($ytdperformance->GetStartDate()))." to ".date('F, Y', strtotime($ytdperformance->GetEndDate())).")";
							?>
						</h3>
						<div class="table-responsive">
							<table class="table table-bordered hodings_summary">
								<thead>
									<tr>
										<th>Account Number</th>
										<th>Name</th>
										<th>Beginning Balance</th>
										<th>Flow</th>
										<th>Income</th>
										<th>Ending Value</th>
										<th>Investment Return</th>
										<th>TWR</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										foreach($ytd_individual_performance_summed as $account_number => $v){
											echo "<tr>";
											echo "<td>**".substr($account_number, 5)."</td>";
											echo "<td>".$ytd_individual_performance_summed[$account_number]['account_name']."</td>";
											echo "<td>$".number_format($ytd_begin_values[$account_number]->value)."</td>";
											echo "<td>$".number_format($ytd_individual_performance_summed[$account_number]['Flow']->amount, 2)."</td>";
											echo "<td>$".number_format($ytd_individual_performance_summed[$account_number]['income_div_interest']->amount, 2)."</td>";
											echo "<td>$".number_format($ytd_end_values[$account_number]->value, 2)."</td>";
											echo "<td>$".number_format($ytd_appreciation[$account_number], 2)."</td>";
											echo "<td>".number_format($ytd_twr[$account_number], 2)."%</td>";
											echo "</tr>";
										}
									?>
									<tr>
										<td colspan="2"><b>Blended Portfolio Return</b></td>
										<td><b>$<?php echo number_format($ytdperformance->GetBeginningValuesSummed()->value,2);?></b></td>
										<td><b>$<?php echo number_format($ytd_performance_summed['Flow']->amount,2);?></b></td>
										<td><b>$<?php echo number_format($ytd_performance_summed['income_div_interest']->amount,2);?></b></td>
										<td><b>$<?php echo number_format($ytdperformance->GetEndingValuesSummed()->value,2);?></b></td>
										<td><b>$<?php echo number_format($ytdperformance->GetCapitalAppreciation(),2);?></b></td>
										<td><b><?php echo number_format($ytdperformance->GetTWR(),2);?>%</b></td>
									</tr>
									<tr>
										<td colspan="7">S&amp;P 500</td>
										<td><?php echo number_format($ytdperformance->GetIndex("S&P 500"),2);?>%</td>
									</tr>
									<tr>
										<td colspan="7">Barclays Aggregate Bond</td>
										<td><?php echo number_format($ytdperformance->GetIndex("AGG"),2);?>%</td>
									</tr>
									<tr>
										<td colspan="7">MSCI Emerging Market index</td>
										<td><?php echo number_format($ytdperformance->GetIndex("EEM"),2);?>%</td>
									</tr>
									<tr>
										<td colspan="7">MSCI EAFE index</td>
										<td><?php echo number_format($ytdperformance->GetIndex("MSCI_EAFE"),2);?>%</td>
									</tr>
									<tr>
										<td colspan="7"><b>Blended Benchmark Return</b></td>
										<td><b><?php echo number_format($ytdperformance->GetBenchmark(),2);?>%</b></td>
									</tr>
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
