<?php
	$categories = $data['categories'];
	$grouped = $data['grouped'];
	$positions = $data['positions'];
	$asset_class = $data['asset_class'];
	$total_weight = $data['total_weight'];
	$global_total = $data['global_total'];
	$primary = $data['primary'];
	$secondary = $data['secondary'];
	$individual = $data['individual'];
	$unsettled_cash = $data['unsettled_cash'];
	$ou = $asset_class['other']+$asset_class['unclassified'];
	$asset_class_weight = $data['asset_class_weight'];
	$individual_ac = $data['individual_ac'];
	
	$assetAllocationReportData = $data['asset_allocation_report'];
	
	$pie_values = $assetAllocationReportData['estimate_pie'];
	
	
?>

<input type="hidden" value='<?php echo $pie_values; ?>' id="dynamic_pie_values" />
<input type="hidden" id="history_chart" value='<?php echo $data["monthly_totals"]; ?>' />
<!--
<input type="hidden" id="trailing_aum_values" value='<?php echo $data["trailing_aum"]; ?>' />
<input type="hidden" id="trailing_revenue_values" value='<?php echo $data["trailing_revenue"]; ?>' />
-->

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o"style="color: #44b6ae!important;"></i>
		Asset Allocation Report
	</h1>
</div>
<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">
    <div class="m-portlet__padding-x">
    </div>
	<div class="m-wizard__head m-portlet__padding-x">	
		<div class="m-wizard__nav ">
			<div class="m-wizard__steps">
				<div class="m-wizard__step m-wizard__step--current" m-wizard-target="m_wizard_form_step_1">
				</div> 
				<div class="m-wizard__step " m-wizard-target="m_wizard_form_step_2">
				</div>
				<div class="m-wizard__step " m-wizard-target="m_wizard_form_step_3">
				</div>
			</div>
		</div>
	</div>

	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
				<div class="m-wizard__form-step m-wizard__form-step--current" id="m_wizard_form_step_1">
					<div class="row">
						<div class="row">
							<div class="col-md-4">
								<blockquote class="control-label text-justify sbold col-xs-offset-1 blockquote report_desc">
									Asset allocation is an investment strategy that aims to balance risk and reward by apportioning a portfolio's assets according to an individual's goals, risk tolerance and investment horizon. The three main asset classes - equities, fixed-income, and cash and equivalents - have different levels of risk and return, so each will behave differently over time.
								</blockquote>
							</div>
							<div class="col-md-8">
								<div id="estimate_dynamic_pie" class="report_top_pie" style="height: 320px;"></div>
							</div>								
						</div>
						<div class="table-responsive">
							<?php 
								$assetAllocationReportData = $assetAllocationReportData['estimate_table'];
								if(!empty($assetAllocationReportData)){
									$table_heading = $assetAllocationReportData['table_headings'];
									$table_categories = $assetAllocationReportData['table_categories'];
									$table_rows = $assetAllocationReportData['table_values']['rows'];
									$rules = $assetAllocationReportData['table_values']['rules'];
									$totals = $assetAllocationReportData['TableTotals'];
							?>
								<table class="table table-bordered hodings_summary">
									<thead>
										<tr>
										<?php 
											foreach($table_heading as $name => $heading){ 
												if(!empty($heading))
													echo "<th>".$heading['heading']."</th>";
												else
													echo "<th>&nbsp;</th>";
											}
										?>
										</tr>
									</thead>
									<tbody>
										<?php 
											if(!empty($table_categories)){
												foreach($table_categories as $category_info){
													$count = 1;
													echo "<tr>";
													echo "<td><strong></strong>".$category_info['estimatedtype']."</td>";
													foreach($table_heading as $head_key => $head_value){
														if($head_key =='heading')continue;
														echo "<td>";
														if(
															!empty($rules) && isset($rules[$head_key]) && !empty($rules[$head_key]) &&
															isset($rules[$head_key]['cat_smarty_modifier']) && $rules[$head_key]['cat_smarty_modifier'] != ''
														){
															if(isset($category_info['totals'][$head_key]) && $category_info['totals'][$head_key] != ''){
																$td_html = "<span>".$rules[$head_key]['cat_prefix'];
																$td_html .= $category_info['totals'][$head_key];/*"|".$rules[$head_key]['cat_smarty_modifier'];*/
																$td_html .= $rules[$head_key]['cat_suffix']."</span>";
																eval("\$td_html = \"$td_html\";");
																echo $td_html;
															} else
																echo "&nbsp";
														} else {
															if(isset($category_info['totals'][$head_key]) && $category_info['totals'][$head_key] != ''){
																echo '<span>'.$rules[$head_key]['cat_suffix'].$category_info['totals'][$head_key].$rules[$head_key]['cat_suffix'].'</span>';
															} else
																echo "&nbsp";
														}
														echo "</td>";
													}
													echo '</tr>';
													if(!empty($table_rows)){
														foreach($table_rows as $row){
															if($row['category_id'] != $category_info['category_id']) continue;
															foreach($row as $fieldname => $row_values){
																if($fieldname == 'fields' && $count == count($category_info)-2){
																	echo '<tr>';
																	foreach($row_values as $field => $value){
																		echo "<td>";
																		if(isset($rules[$field]['prefix']) || isset($rules[$field]['suffix']))
																			echo '<span>'.trim($rules[$field]['prefix'].$value.$rules[$field]['suffix']).'</span>';
																		else
																			echo $value;
																		echo '</td>';
																	}
																	echo '</tr>';
																}
															}
														}
													}
												}
												echo "<tr>";
												foreach($table_heading as $head_key => $head_value){
													$printTd = false;
													foreach($totals as $field => $total){
														if($field == $head_key){
															$printTd = true;
															if($rules[$head_key]['hide_from_total'])
																echo "<td>&nbsp;</td>";
															else {
																if(isset($rules[$head_key]['total_prefix']) || isset($rules[$head_key]['total_suffix']))
																	echo "<td>".trim($rules[$head_key]['total_prefix'].$total.$rules[$head_key]['total_suffix'])."</td>";
																else
																	echo "<td>".$total."</td>";
															}
														}
													}
													if(!$printTd)
														echo "<td>&nbsp;</td>";
												}
												echo '</tr>';
											}
										?>
									</tbody>
								</table>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="m-wizard__form-step" id="m_wizard_form_step_2">
					<div class="row">
    					<div class="m-section">
        					<h3 class="control-label">Balances</h3>
        					<span class="m-section__sub">
        						The balances view is an individualized view of each account and its holdings based on security type
        					</span>
        				</div>
						<div class="table-responsive">
							<table class="table table-bordered hodings_summary">
								<thead>
									<tr>
										<th>Symbol</th>
										<th>Description</th>
										<th>Account Number</th>
										<th>Qty</th>
										<th>Price</th>
										<th>Weight</th>
										<th>Total Value</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach($secondary as $sk => $sv){ ?>
										<tr class="secondary" data-toggle="collapse" id="detail-sec<?php echo $sk; ?>" data-target=".detail-sec<?php echo $sk; ?>">
											<td colspan="5"><?php if(!empty($individual)){?><i class="fa fa-plus-circle font-blue font-lg"></i>&nbsp;<?php }?><?php echo $sv['securitytype']; ?></td>
											<td><?php echo $sv['group_weight']; ?>%</td>
											<td>$<?php echo number_format($sv['group_total'],2,".",","); ?></td>
										</tr>
										<?php 
											foreach($individual as $ik => $iv){
												if($sv['securitytype'] == $iv['securitytype']){
										?>
												<tr class="position holdings collapse detail-sec<?php echo $sk; ?>">
													<td><?php echo $iv['security_symbol']; ?></td>
													<td><?php echo $iv['description']; ?></td>
													<td><?php echo $iv['account_number']; ?></td>
													<td><?php echo number_format($iv['quantity'],2,".",","); ?></td>
													<td>$<?php echo number_format($iv['last_price'],2,".",","); ?></td>
													<td><?php echo number_format($iv['weight'],2,".",","); ?>%</td>
													<td>$<?php echo number_format($iv['current_value'],2,".",","); ?></td>
												</tr>
									<?php
												}
											}
										}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="m-wizard__form-step " id="m_wizard_form_step_3">
					<div class="row">
						<div class="m-section col-md-12">
        					<h3 class="control-label">Account Value Over the Past 12 Months</h3>
        					<div class="m-section__content">
        					 	<div id="historical_graph" style="height: 400px;"></div>
        					</div>
        				</div>
						
					</div>
				</div>
			</div>
			<div class="m-portlet__foot m-portlet__foot--fit m--margin-top-40">
				<div class="m-form__actions m-form__actions">
					<div class="row">
    					<div class="col-sm-11 hide" id="holdings_footer_desc">
                			<p class="control-label text-justify sbold">
                				OMNIVue&trade; is an analytical tool which relies on the research, estimates and approximations of 3rd parties to perform 
                				its calculations. As such, the totals and values represented in OMNIVue&trade; may not correspond exactly to 
                				custodial statement balances and other reports of actual account value.
                			</p>
                		</div>
						<div class="col-lg-12 m--align-right">
							<a href="javascript:;" class="btn btn-lg default previous" style="background-color:#e1e5ec;" data-wizard-action="prev">
                				<i class="la la-arrow-circle-left " style="color: white;font-size: 2.5rem;"></i>
                			</a>
                			<a href="javascript:;" class="btn btn-lg green next" style="background-color:#32c5d2;" data-wizard-action="next">
                				<i class="la la-arrow-circle-right " style="color: white;font-size: 2.5rem;"></i>
                			</a>
						</div>
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
