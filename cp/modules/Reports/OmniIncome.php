<?php


$START_MONTH = $data['START_MONTH'];
$END_MONTH = $data['END_MONTH'];
$DYNAMIC_GRAPH = $data['DYNAMIC_GRAPH'];
$MONTHLY_TOTALS = $data['MONTHLY_TOTALS'];
$COMBINED_SYMBOLS = $data['COMBINED_SYMBOLS'];
$YEAR_END_TOTALS = $data['YEAR_END_TOTALS'];
$GRAND_TOTAL = $data['GRAND_TOTAL'];

echo "<input type='hidden' value='".$DYNAMIC_GRAPH."' id='estimate_graph_values' />";

?>

    
	<style>
	   table {font-size:13px;}
	</style>

	<div class="kt-portlet__body" style = "padding-top:0px;">
		<div class="row">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<div id="dynamic_chart_holder" class="dynamic_chart_holder" style="height: 350px;"></div>
			</div>	
			<div class="col-md-1"></div>	
		</div>
		<div class="row">
			<div class="table-responsive"> 
				<table id="income_combined" class="collap_income table table-bordered">
					<thead>
					<tr>
						<th style="font-weight:bold;">Symbol</th>
						<th style="font-weight:bold;">Name</th>
						<?php if(!empty($MONTHLY_TOTALS)){
						    foreach($MONTHLY_TOTALS as $v){   ?>
							<th style="font-weight:600; text-align:center;"><?php echo $v['month'];?><br /><?php echo $v['year'];?></th>
						<?php }}?>
						<th style="font-weight:bold; text-align:center;">Total</th>
					</tr>
					<thead>
					<tbody>
					<?php 
					if(!empty($COMBINED_SYMBOLS)){
					   foreach($COMBINED_SYMBOLS as $symbol=>$symbol_values){ ?>
						<tr>
							<td style="width:10%;"><?php echo $symbol;?></td>
							<td style="width:10%;"><?php echo $symbol_values[0]['security_name'];?></td>
								<?php foreach ($MONTHLY_TOTALS as $ym){?>
									<td style="text-align:right;">
									<?php     $found = 0;
									foreach($symbol_values as $v){
										if ($ym['year'] == $v['year'] && $ym['month'] == $v['month']){
											echo '$'.number_format($v['amount'],0,".",",");
											$found = 1;
										}
									}
									if ($found != 1){
										echo '-';
									}?>
									</td>
								<?php }?>
							<td style="text-align:right;">$<?php echo number_format($YEAR_END_TOTALS[$symbol],0,".",",");?></td>
						</tr>
					<?php }
					}?>
					<tr>
						<td colspan="2">&nbsp;</td>
					<?php
					if(!empty($MONTHLY_TOTALS)){
					foreach($MONTHLY_TOTALS as $v){?>
						<td style="font-weight:bold; text-align:right;">$<?php echo number_format($v['monthly_total'],0,".",",");?></td>
					<?php }
					}?>
						<td style="font-weight:bold; text-align:right;">$<?php echo number_format($GRAND_TOTAL,0,".",",");?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
		

