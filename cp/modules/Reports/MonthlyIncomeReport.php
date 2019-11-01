<?php 
	
	$display_months = $data['display_months'];
	
	$display_years_current = $data['display_years_current'];
	
	$main_categories_previous = $data['main_categories_previous'];
	
	$sub_sub_categories_previous = $data['sub_sub_categories_previous'];
	
	$previous_symbols = $data['previous_symbols'];
	
	/*if($previous_symbols)
		$previous_symbols = json_decode($previous_symbols, true);
	*/
	$previous_monthly_totals = $data['previous_monthly_totals'];
	
	/*if($previous_monthly_totals)
		$previous_monthly_totals = json_decode($previous_monthly_totals, true);
	*/
	
	$previous_symbols_values = $data['previous_symbols_values'];
	
	/*if($previous_symbols_values)
		$previous_symbols_values = json_decode($previous_symbols_values, true);
	 */
	 
?>
<input type="hidden" name="history_chart" value='<?php echo $data['history_data']; ?>'/>

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o "style="color: #44b6ae!important;"></i>
		<?php echo Language::translate("Income Summary"); ?>
	</h1>
	<span class="m-section__sub">
		As of: <?php echo $data['date']; ?>
	</span>
</div>
<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
				<div class="row">
					<div id="history_chart" style="height: 300px;"></div>
				</div>
				<div class="row">
					<div class="table-responsive">
						<table class="table table-bordered income_summary">
							<thead>
								<tr>
									<th rowspan="2">Symbol</th>
									<th rowspan="2"><span>Description</span></th>
									<?php 
										foreach($display_months as $month){
											echo '<th>'.$month."</th>";
										}
									?>
									<th rowspan="2">Total</th>
								</tr>
								<tr>
									<?php 
										foreach($display_months as $month){
											echo '<td><span>'.$display_years_current[$month].'</span></td>';
										}
									?>
								</tr>	
							</thead>
							<tbody>
								<?php 
									if(!empty($main_categories_previous)){
										foreach($main_categories_previous as $main_category_key => $main_category){ 
								?>
											<tr data-toggle="collapse" id="income<?php echo $main_category_key;?>" data-target=".income<?php echo $main_category_key;?>">
												<td>
													<i class="fa fa-plus-circle font-blue font-lg"></i>&nbsp;
													<span><?php echo $main_category['category']; ?></span> 
													- <em> $<?php echo number_format($main_category['category_total']); ?></em>
												</td>
												<td colspan="14">&nbsp;</td>
											</tr>
											<?php 
												foreach($sub_sub_categories_previous as $sub_sub_category_key => $sub_sub_category){ 
													if($sub_sub_category['category'] == $main_category['category']){
											?>
														<tr class="monthly_income collapse income<?php echo $main_category_key;?>" data-toggle="collapse" id="income_sub<?php echo $sub_sub_category_key;?>" data-target=".income_sub<?php echo $sub_sub_category_key;?>">
															<td><i class="fa fa-plus-circle font-blue font-lg"></i>&nbsp;<?php echo $sub_sub_category['sub_sub_category']; ?><em> - $<?php echo number_format($sub_sub_category['sub_category_total']); ?></em></td>
															<td colspan="14">&nbsp;</td>
														</tr>
														
														<?php 
															if(!empty($previous_symbols)){
																foreach($previous_symbols as $symbol_key => $symbol){ 
																	if($symbol['sub_sub_category'] == $sub_sub_category['sub_sub_category']){
														?>
																	<tr class="monthly_income_sub collapse income_sub<?php echo $sub_sub_category_key;?>">
																		<td><?php echo $symbol['symbol']; ?></td>
																		<td><span><?php echo $symbol['description']; ?></span></td>
																		<?php 
																			foreach($display_months as $month){
																				if($previous_symbols_values[$symbol['symbol']][$month]['month'] == $month){
																					echo '<td class="text-right">' . number_format($previous_symbols_values[$symbol['symbol']][$month]['amount']) . '</td>';
																				} else {
																					echo '<td>&nbsp;</td>';
																				}
																			}
																		?>
																		<td>$<?php echo number_format($symbol['symbol_total']); ?></td>
																	</tr>
								<?php
															}
														}            	
													}
												}    
											}
										}
									}
								?>
								
								<tr>
									<td colspan="2">&nbsp;</td>
									<?php foreach($display_months as $month){ ?>
										<td class="text-right"><strong>$<?php echo number_format($previous_monthly_totals[$month]['monthly_total']); ?></strong></td>
									<?php }?>
									<td><strong>$<?php echo number_format($previous_monthly_totals['grand_total']); ?></strong></td>
								</tr>
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
						<!--  <div class="form-group form">
							<h3 class="form-section">
								<span><?php echo Language::translate('Monthly Income'); ?></span>
							</h3>
						</div>	-->
						<!-- <div class="portlet">
							<div class="form-group">
								<div id="history_chart" style="height: 300px;"></div>
							</div>
						</div>
					</section>

					<section class="idealsteps-step">
						<!--  <div class="form-group form">
							<h3 class="form-section">Trailing 12 Monthly Income</h3>
						</div>	-->
						<!-- <div class="form-group">
							<div class="table-responsive">
								<table class="table table-bordered income_summary">
									<thead>
										<tr>
											<th rowspan="2">Symbol</th>
											<th rowspan="2"><span>Description</span></th>
											<?php 
												foreach($display_months as $month){
													echo '<th>'.$month."</th>";
												}
											?>
											<th rowspan="2">Total</th>
										</tr>
										<tr>
											<?php 
												foreach($display_months as $month){
													echo '<td><span>'.$display_years_current[$month].'</span></td>';
												}
											?>
										</tr>	
									</thead>
			
									<tbody>
									
										<?php 
											if(!empty($main_categories_previous)){
												foreach($main_categories_previous as $main_category_key => $main_category){ 
										?>
													<tr data-toggle="collapse" id="income<?php echo $main_category_key;?>" data-target=".income<?php echo $main_category_key;?>">
														<td>
															<i class="fa fa-plus-circle font-blue font-lg"></i>&nbsp;
															<span><?php echo $main_category['category']; ?></span> 
															- <em> $<?php echo number_format($main_category['category_total']); ?></em>
														</td>
														<td colspan="14">&nbsp;</td>
													</tr>
													<?php 
														foreach($sub_sub_categories_previous as $sub_sub_category_key => $sub_sub_category){ 
															if($sub_sub_category['category'] == $main_category['category']){
													?>
																<tr class="monthly_income collapse income<?php echo $main_category_key;?>" data-toggle="collapse" id="income_sub<?php echo $sub_sub_category_key;?>" data-target=".income_sub<?php echo $sub_sub_category_key;?>">
																	<td><i class="fa fa-plus-circle font-blue font-lg"></i>&nbsp;<?php echo $sub_sub_category['sub_sub_category']; ?><em> - $<?php echo number_format($sub_sub_category['sub_category_total']); ?></em></td>
																	<td colspan="14">&nbsp;</td>
																</tr>
																
																<?php 
																	if(!empty($previous_symbols)){
																		foreach($previous_symbols as $symbol_key => $symbol){ 
																			if($symbol['sub_sub_category'] == $sub_sub_category['sub_sub_category']){
																?>
																			<tr class="monthly_income_sub collapse income_sub<?php echo $sub_sub_category_key;?>">
																				<td><?php echo $symbol['symbol']; ?></td>
																				<td><span><?php echo $symbol['description']; ?></span></td>
																				<?php 
																					foreach($display_months as $month){
																						if($previous_symbols_values[$symbol['symbol']][$month]['month'] == $month){
																							echo '<td class="text-right">' . number_format($previous_symbols_values[$symbol['symbol']][$month]['amount']) . '</td>';
																						} else {
																							echo '<td>&nbsp;</td>';
																						}
																					}
																				?>
																				<td>$<?php echo number_format($symbol['symbol_total']); ?></td>
																			</tr>
										<?php
																	}
																}            	
															}
														}    
													}
												}
											}
										?>
										
										<tr>
											<td colspan="2">&nbsp;</td>
											<?php foreach($display_months as $month){ ?>
												<td class="text-right"><strong>$<?php echo number_format($previous_monthly_totals[$month]['monthly_total']); ?></strong></td>
											<?php }?>
											<td><strong>$<?php echo number_format($previous_monthly_totals['grand_total']); ?></strong></td>
										</tr>
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
</div> -->