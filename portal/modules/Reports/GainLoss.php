<?php

$COMPARISON_TABLE = $data['COMPARISON_TABLE'];
?>

<div class="m-section">
	<h1 class="m-section__heading">
		<i class="fa fa-bar-chart-o "style="color: #44b6ae!important;"></i>
		<?php echo Language::translate("Positions"); ?>
	</h1>
</div>
<div class="m-wizard m-wizard--1 m-wizard--success m-wizard--step-between" id="m_wizard">

	<div class="m-wizard__form">
		<form class="m-form m-form--label-align-left- m-form--state-" id="m_form" >
			<div class="m-portlet__body">
				<div class="row">
				  	<?php
						$DYNATABLE = $COMPARISON_TABLE;
						$DYNAHEADINGS = $DYNATABLE['table_headings'];
						$DYNAROWS = $DYNATABLE['table_values']['rows'];
						$DYNARULES = $DYNATABLE['table_values']['rules'];
						$DYNACATEGORIES = $DYNATABLE['table_categories'];
						$DYNATOTALS = $DYNATABLE['TableTotals'];
						$COUNTER = 1;
						$PARENT_ID = 1;
					?>
                    <table class="table table-bordered DynaTable table-collapse GainLossTable">
                        <thead>
                        <tr>
                            <?php 
							foreach($DYNAHEADINGS as $k=>$heading){
								if($heading['hidden'] != 1){?>
									<th style="<?php echo $heading['heading_td_style'];?>"><span style="<?php echo $heading['heading_span_style'];?>"><?php echo $heading['heading'];?></span></th>
							   <?php  }
							}
							?>
                        </tr>
                        </thead>
                        <tbody>
                         <?php $CatCount = '0';
						foreach($DYNACATEGORIES as $CatArray){
							foreach($CatArray as $k=>$cat){
								if($k != 'category_id' && $k != 'totals'){
									$CatCount = $CatCount+1;
									if($cat != ''){?>
										<tr data-id="<?php echo $COUNTER;?>" data-parent id="asset_cat_<?php echo $CatCount?>" data-target=".asset_cat_<?php echo $CatCount;?>">
											<td><i class="icon-plus"></i>&nbsp;<strong><?php echo $cat?></strong></td>
											<?php foreach($DYNAHEADINGS as $a=>$heading){
												if($a != 'heading'){?>
													<td style="<?php echo $DYNARULES[$a]['cat_td_style'];?>">
														<?php if($CatArray['totals'][$a] != ''){?>
															<span style="<?php echo $DYNARULES[$a]['cat_span_style'];?>"><?php echo $DYNARULES[$a]['cat_prefix'].number_format($CatArray['totals'][$a].$DYNARULES[$a]['cat_suffix'],2);?></span>
														<?php }?>
													</td>
												<?php 
												}
											}
											$PARENT_ID = $COUNTER;
											$COUNTER = $COUNTER+1;
											?>
										</tr>
									<?php }
									foreach($DYNAROWS as $r){
										foreach($r as $index=>$row){
											if($index == 'fields' && $r['category_id'] == $CatArray['category_id']){?>
												<tr data-id='<?php echo $COUNTER?>' data-parent='<?php echo $PARENT_ID;?>' class="positions asset_cat_<?php echo $CatCount;?>">
													<?php 
													foreach($row as $k => $v){?>
														<td style="<?php echo $DYNARULES[$k]['value_td_style'];?>" 
															<?php echo $DYNARULES[$k]['html_td_modifiers'];
															if(strlen($DYNARULES[$k]['value_as_data']) > 1){
																$DYNARULES[$k]['value_as_data']=$v;}?>>
															<?php 
																$value = $v.$DYNARULES[$k]['suffix'];
																if($k != 'description' && $k != 'trade_date' && $k != 'days_held' && $k != 'system_generated'){
																	$value = number_format($v.$DYNARULES[$k]['suffix'],2);
																}
															?>																
															<span style="<?php echo $DYNARULES[$k]['value_span_style'];?>"><?php echo $DYNARULES[$k]['prefix'].$value;?></span>
														</td>
												   <?php }
													$COUNTER = $COUNTER+1;?>
												</tr>
										<?php }
										}
									}
								}
							}
						}?>
						<tr data-id="<?php echo $COUNTER;?>" data-parent>
							<?php foreach($DYNAHEADINGS as $a=>$heading){
								$VALSET = 0;
								foreach($DYNATOTALS as $k=>$v){
									if($a == $k){?>
										<td style="<?php echo $DYNARULES[$k]['total_td_style'];?>;">
											<?php if($DYNARULES[$k]['hide_from_total'] != 1){?>
												<span style="<?php echo $DYNARULES[$k]['total_span_style'];?>;"><?php echo $DYNARULES[$k]['total_prefix'].number_format($v.$DYNARULES[$k]['total_suffix'],2);?></span>
											<?php }?>
										</td>
									  <?php $VALSET = 1;
									}
								}
								if($VALSET == 0){?>
									<td style="border-top:1px dotted black;">&nbsp;</td>
								<?php }
							}
							$COUNTER = $COUNTER+1;?>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
			
		</form>
	</div>
</div>
