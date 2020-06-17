<?php

echo "<input type='hidden' value='".$data['DYNAMIC_PIE']."' id='dynamic_pie_values' />";
echo "<input type='hidden' value='assetclassreport' id='report_type' />"
    
?>

<div class="kt-portlet" style = "padding-top:0px;">
	<div class="kt-portlet__body">
		<div class="form-group m-form__group row">
			<div class="col-sm-3">
				<select id="report_date_selection" class="form-control">
                    <?php foreach($data['DATE_OPTIONS'] as $index=>$option){?>
                        <option value="<?php echo $option['option_value'];?>" data-start_date="<?php echo $option['date']['start'];?>" data-end_date="<?php echo $option['date']['end'];?>" <?php if($option['default'] == 1){ echo 'selected'; }?>><?php echo $option['option_name']; ?></option>
                    <?php }?>
                </select>
			</div>
			
			<?php 
			if($data['SHOW_START_DATE'] == 1){?>
				<div class="col-sm-3">
			 		<input type="text" id="select_start_date" class="form-control" value="<?php echo $data['START_DATE'];?>">
				</div>
			<?php }
			if($data['SHOW_END_DATE'] == 1){?>
				<div class="col-sm-3">
					<input type="text" id="select_end_date" class="form-control" value="<?php echo $data['END_DATE'];?>">
				</div>
			<?php }?>
			
			<div class="col-sm-3"><input type="button" class="btn btn-info" id="calculate_report" value="Calculate" /></div>
		</div>
	</div>
</div>

<div class="m-portlet__body">
	<div class="row">
		<div class="col-md-2"></div>
		<div class="col-md-8">
			<div id="estimate_dynamic_pie" class="estimate_dynamic_pie" style="height: 350px;"></div>
		</div>	
		<div class="col-md-2"></div>
	</div>
	<div class="row">
		<?php
		$DYNATABLE = $data['ESTIMATE_TABLE'];
		
		$DYNAHEADINGS = $DYNATABLE['table_headings'];
		$DYNAROWS = $DYNATABLE['table_values']['rows'];
		$DYNARULES = $DYNATABLE['table_values']['rules'];
		$DYNACATEGORIES = $DYNATABLE['table_categories'];
		$DYNATOTALS = $DYNATABLE['TableTotals'];
		
		?>
		<table class="table table-bordered DynaTable table-collapse">
			<thead>
			<tr>
				<?php if(!empty($DYNAHEADINGS)){
				    foreach($DYNAHEADINGS as $k=>$heading){?>
					<th style="<?php echo isset($heading['heading_td_style']) ?  $heading['heading_td_style'] :  '';?>"><span style="<?php echo isset($heading['heading_span_style']) ? str_replace("bold", 600, $heading['heading_span_style']) : '';?>"><?php echo isset($heading['heading']) ? $heading['heading'] : '';?></span></th>
				<?php }
				}?>
			</tr>
			</thead>
			<tbody>
			<?php $CatCount ='0';
			if(!empty($DYNACATEGORIES)){
    			foreach($DYNACATEGORIES as $CatArray){
    			    $count = 1;
    				foreach($CatArray as $k=>$cat){
    					if($k != 'category_id' && $k != 'totals'){
    						$CatCount = $CatCount+1;
    						if($cat != ''){?>
    							<tr data-toggle="collapse" id="asset_cat_<?php echo $CatCount;?>" data-target=".asset_cat_<?php echo $CatCount;?>">
    								<td><i class="fa fa-plus-circle" style="color:#3598dc!important;"></i>&nbsp;<strong><?php echo $cat;?></strong></td>
    								<?php foreach($DYNAHEADINGS as $a=>$heading){
    									if($a != 'heading'){?>
    										<td style="<?php echo $DYNARULES[$a]['cat_td_style'];?>">
    											<?php if($CatArray['totals'][$a] != ''){?>
    												<span style="<?php echo $DYNARULES[$a]['cat_span_style'];?>"><?php echo $DYNARULES[$a]['cat_prefix'].number_format($CatArray['totals'][$a].$DYNARULES[$a]['cat_suffix'],2);?></span>
    											<?php }?>
    										</td>
    								<?php }
    								}?>
    							</tr>
    						<?php }
    						foreach($DYNAROWS as $r){
    							foreach($r as $index=>$row){
    							    
    								if($index == 'fields' && $r['category_id'] == $CatArray['category_id'] && $count == count($CatArray)-2){?>
    									<tr class="holdings collapse asset_cat_<?php echo $CatCount;?>">
    										<?php foreach($row as $k => $v){
    										      if($v && ($k == 'quantity' || $k == 'price' || $k == 'market_value'))
    										          $v = is_numeric($v) ? number_format($v, 2) : 0;
    										    ?>
    											
    											<td style="<?php echo $DYNARULES[$k]['value_td_style'];?>">
    												<span style="<?php echo $DYNARULES[$k]['value_span_style'];echo $k;?>"><?php echo $DYNARULES[$k]['prefix'].$v.$DYNARULES[$k]['suffix'];?></span>
    											</td>
    										<?php }?>
    									</tr>
    							<?php }
    							}
    						}
    					}
    				}
    				$count++;
    			}
			}?>
			<tr>
				<?php if(!empty($DYNAHEADINGS)){
    				foreach($DYNAHEADINGS as $a=>$heading){
    					$VALSET=0;
    					foreach($DYNATOTALS as $k=>$v){
    						if($a == $k){?>
    							<td style="<?php echo $DYNARULES[$k]['total_td_style'];?>;">
    								<?php if($DYNARULES[$k]['hide_from_total'] != 1){?>
    									<span style="<?php echo $DYNARULES[$k]['total_span_style'];?>;"><?php echo $DYNARULES[$k]['total_prefix'].number_format($v.$DYNARULES[$k]['total_suffix'],2);?></span>
    								<?php }?>
    							</td>
    					   <?php $VALSET=1;
    						}
    					}
    					if($VALSET == 0){?>
    						<td style="border-top:1px dotted black;">&nbsp;</td>
    				<?php }
    				}
				}?>
			</tr>
			</tbody>
		</table>
	</div>
</div>
	
