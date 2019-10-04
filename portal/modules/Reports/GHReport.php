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
$dynamic_pie = $data['dynamic_pie'];

$date_options = $data['date_options'];

echo "<input type='hidden' value='".$holdingspievalues."' id='holdings_values' class='holdings_values' />";
echo "<input type='hidden' value='".$dynamic_pie."' id='estimate_pie_values' />";
echo "<input type='hidden' value='ghreport' id='report_type' />"
?>
<style>	
	#ghreport_chart_table .borderless td, 
	#ghreport_chart_table .borderless>thead>tr>th {
		border: 0;
	}
	.GHReport_UI_Wrapper{display:block; border:2px solid black; max-width:1024px; border-radius:25px; margin-left:auto;
                     margin-right:auto; padding:5px; background-color: #FFFFCC}
	.boxsizingborder{-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box; width:100%;}
    
    #GHReport_header{width:100%;}
    
    #GHReport_header .pdf_crm_logo{float:left;width:25%;}
    
    #GHReport_header tr td{color: #2F3851;font-weight: bold;}
    
    .grey_header{width:100%;background-color:rgba(0,0,0,0.3);text-align:center;border : 2px solid rgba(0,0,0,0.5);color: white;}
    
    .blue_header{width:100%;background-color:#33256C;text-align:center;color: white;}
    
    .borderTop{border-top:1px solid black; padding-top:10px;}
    
    .borderBottom{border-bottom:1px solid black;}
</style>

<div class="m-section m-section--last">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o"style="color: #44b6ae!important;"></i>
		PORTFOLIO SUMMARY
	</h1> 
</div>
<div class="m-portlet">
	<div class="m-portlet__body">
		<div class="form-group m-form__group row">
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
				<div id="GHReport_wrapper" class="GHReport_UI_Wrapper">
				
					<div class="GHReport_section">
						<h2 class="blue_header">PORTFOLIO SUMMARY</h2>
						<table style="width:100%" border="0">
							<tr>
								<td style="width:50%;">
									<table style="display:block; width:90%; font-size:16px;"  border="0">
										<thead>
										<tr>
											<th>&nbsp;</th>
											<th style="font-weight:bold; text-align:right;" class="borderBottom">VALUE</th>
											<th style="font-weight:bold; text-align:right;" class="borderBottom">ALLOC</th>
										</tr>
										</thead>
										<tbody>
										<?php foreach($holdingspiearray as $v){?>
											<tr>
												<td style="font-weight:bold; width:50%; padding-bottom:10px;"><?php echo $v['title'];?></td>
												<td style="text-align:right; width:25%;">$<?php echo number_format($v['value'],2,".",",");?></td>
												<td style="text-align:right; width:25%;"><?php echo number_format($v['percentage'],2,".",",");?>%</td>
											</tr>
										<?php }?>
										<tr>
											<td>&nbsp;</td>
											<td style="text-align:right;" class="borderTop borderBottom">$<?php echo number_format($data['globaltotal'],2,".",",");?></td>
											<td>&nbsp;</td>
										</tr>
										<?php if($MARGIN_BALANCE != 0){?>
											<tr>
												<td colspan="3">
													<p>Margin Balance: <span style="<?php if($MARGIN_BALANCE < 0){?>color:red;<?php }else{?>color:green;<?php }?>">$<?php echo number_format($MARGIN_BALANCE,2);?></span></p>
												</td>
											</tr>
										<?php }
										if($NET_CREDIT_DEBIT != 0){?>
											<tr>
												<td colspan="3">
													<p>Net Credit Debit: <span style="<?php if($NET_CREDIT_DEBIT < 0){?>color:red;<?php }else{?>color:green;<?php }?>">$<?php echo number_format($NET_CREDIT_DEBIT,2);?></span></p>
												</td>
											</tr>
									   <?php }
									   if($UNSETTLED_CASH != 0){?>
											<tr>
												<td colspan="3">
													<p>Unsettled Cash: <span style="<?php if($UNSETTLED_CASH < 0){?>color:red;<?php }else{?>color:green;<?php }?>">$<?php echo number_format($UNSETTLED_CASH,2);?></span></p>
												</td>
											</tr>
										<?php }
										if($ytdperformance->GetDividendAccrualAmount() != 0){?>
											<tr>
												<td style="padding-top:10px; font-weight:bold;">Dividend Accrual:</td>
												<td style="text-align:right; padding-top:10px;">$<?php echo number_format($ytdperformance->GetDividendAccrualAmount(),2,".",",");?></td>
												<td>&nbsp;</td>
											</tr>
										<?php }?>
										</tbody>
									</table>
								</td>
								<td>
									<div id="dynamic_pie_holder" class="dynamic_pie_holder" style="height: 300px; width:450px;"></div>
								</td>
							</tr>
						</table>
					</div>
					<div class="GHReport_section">
						<h2 class="grey_header" >
							<span style="font-size:14px;">
								<?php
									echo "PERFORMANCE(".date('F, Y',strtotime($ytdperformance->GetStartDate()))." to ".date('F, Y', strtotime($ytdperformance->GetEndDate())).")";
								?>
							</span>
						</h2>
						<table class="table" style="display:block; width:100%;"  border="0">
							<thead>
							<tr>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center; text-decoration:underline;">ACCOUNT NAME</th>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center; text-decoration:underline;">ACCT NUMBER</th>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:15%; text-align:center; text-decoration:underline;">BEG. BALANCE</th>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">ADDTNS/ WTHDRWLS</th>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">CHANGE IN VALUE</th>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">END BALANCE</th>
								<th style="font-weight:bold; background-color:RGB(245, 245, 245); width:5%; text-align:center; text-decoration:underline;">ESTIMATED INCOME</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach($ytd_individual_performance_summed as $account_number=>$v){?>
								<tr <?php if($ytd_individual_performance_summed[$account_number]['Flow']->disable_performance == 1){?> style="<!-- background-color:#FFFFE0; -->" <?php }?>>
									<td><?php echo $ytd_individual_performance_summed[$account_number]['account_name'];?></td>
									<td>**<?php echo substr($account_number,5);?></td>
									<td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_begin_values[$account_number]->value,2,".",",");?></span></td>
									<td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_individual_performance_summed[$account_number]['Flow']->amount,2,".",",");?></span></td>
									<td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_performance_summed['Income']->amount,2,".",",");?></span></td>
									<td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_end_values[$account_number]->value,2,".",",");?></span></td>
									<td><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_individual_performance_summed[$account_number]['income_div_interest']->amount,2,".",",");?></span></td>
								</tr>
							<?php }?>
							<tr>
								<td style="background-color:RGB(245, 245, 245); font-weight:bold;" colspan="2">&nbsp;</td>
								<td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytdperformance->GetBeginningValuesSummed()->value,2,".",",");?></span></td>
								<td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_performance_summed['Flow']->amount,2,".",",");?></span></td>
								<td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_performance_summed['Income']->amount,2,".",",");?></span></td>
								<td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytdperformance->GetEndingValuesSummed()->value,2,".",",");?></span></td>
								<td style="text-align:right; background-color:RGB(245, 245, 245); font-weight:bold;"><span style="text-align:left; float:left;width:10%;">$</span><span style="text-align:right; float:right;width:90%;"><?php echo number_format($ytd_performance_summed['income_div_interest']->amount,2,".",",");?></span></td>
							</tr>
							</tbody>
						</table>
					</div>
					<div class="GHReport_section">
						<h2 class="blue_header"><span style="font-size:14px;"><span style="font-size:14px;">Benchmark and Index Performance</span></h2>
						<table class="table" border="0">
							<tbody>
							<tr>
								<td colspan="2" style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:left; text-decoration:underline;">PORTFOLIO PERFORMANCE</td>
								<td colspan="2" style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:left; text-decoration:underline;">BENCHMARK PERFORMANCE</td>
							</tr>
							<tr>
								<td>Combined Return</td>
								<td style="text-align:right; font-weight:bold;"><?php echo number_format($ytd_twr[$account_number],2,".",",");?>%</td>
								<td>Combined Benchmark</td>
								<td style="text-align:right; font-weight:bold;"><?php echo number_format($ytdperformance->GetBenchmark(),2,".",",");?>%</td>
							</tr>
							<tr>
								<td colspan="4" style="font-weight:bold; background-color:RGB(245, 245, 245); text-align:left; text-decoration:underline;">BENCHMARK PERFORMANCE</td>
							</tr>
							<tr>
								<td>S&amp;P 500</td>
								<td style="text-align:right; font-weight:bold;"><?php echo number_format($ytdperformance->GetIndex("S&P 500"),2,".",",");?>%</td>
								<td>AGG</td>
								<td style="text-align:right; font-weight:bold;"><?php echo number_format($ytdperformance->GetIndex("AGG"),2,".",",");?>%</td>
							</tr>
							<tr>
								<td>MSCI Emerging Market index</td>
								<td style="text-align:right; font-weight:bold;"><?php echo number_format($ytdperformance->GetIndex("EEM"),2,".",",");?>%</td>
								<td>MSCI EAFE index</td>
								<td style="text-align:right; font-weight:bold;"><?php echo number_format($ytdperformance->GetIndex("MSCI_EAFE"),2,".",",");?>%</td>
							</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			
		</form>
	</div>
</div>

