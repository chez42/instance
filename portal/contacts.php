<?php
	include_once('includes/head.php');

	if(!isset($_SESSION["ID"])) {
		header("Location: login.php");
	} else {
		$module = 'Contacts';
		include_once('includes/menu.php'); 	
		include_once('includes/function.php'); 	
	}
	
?>
<?php if(!isset($_REQUEST['action']) && $_REQUEST['action'] == ''){
	
	$sparams = array(
		'id' => $_SESSION['ID'], 
		'block'=>$module,
		'onlymine'=>false,
		'page_limit' => 10
	);
	
	$lmod = get_module_list_values($sparams);
	$data['totalRecords'] = $lmod[2][$module]['totalRecords'];
	$data['recordlist']=$lmod[1][$module]['data'];
	$data['tableheader']=$lmod[0][$module]['head'][0];
?>
<div class="m-portlet m-portlet--mobile">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">
					<i><img src="assets/img/Contacts.png"  alt="<?php echo vtranslate($module,'Vtiger'); ?>" title="<?php echo vtranslate($module,'Vtiger'); ?>"></i>
					<?php echo vtranslate($module,'Vtiger'); ?>
				</h3>
			</div>
		</div>
	</div>
	<div class="m-portlet__body">
		<!--begin: Datatable -->
		<div id="m_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4 no-footer">
			<div class="row">
				<div class="col-sm-12">
					<?php if(isset($data['totalRecords']) && $data['totalRecords'] > 0){ ?>
						<input type="hidden" name="total_records" value="<?php echo $data['totalRecords']; ?>" />
					<?php } ?>
					<?php if(isset($data['recordlist']) && count($data['recordlist'])>0){ ?>
    					<table class="table table-striped- table-bordered table-hover table-checkable dataTable no-footer " id="responsiveDataTables" role="grid" style="width: 974px;">
    						<thead>
    	  						<tr role="row">
                                	<?php foreach($data['tableheader'] as $hf) echo "<th class='sorting' tabindex='0' aria-controls='responsiveDataTables'>".vtranslate($hf['fielddata'],$module)."</th>"; ?>
                                </tr>
    						</thead>
    						<tbody>
    							<?php 
									foreach($data['recordlist'] as $key=>$record){
									    echo "<tr role='row' >";
										foreach($record as $record_fields) echo "<td>".vtranslate($record_fields['fielddata'],$module)."</td>";
										echo "</tr>";
																											
									}
								?>
    							
                			</tbody>
                		</table>
                	<?php }else { ?> 
						<h1 class="page-title">&nbsp;</h1>
						<div class="row">
							<div class="col-md-12">
								<div class="text-center">
									<h3>
										<?php echo vtranslate("No ".$module." records Found!",$module); ?>
									</h3>
								</div>
							</div>
						</div>
						<h1 class="page-title">&nbsp;</h1>
					<?php } ?>
        		</div>
    		</div>
    	</div>
	</div>
</div>

<?php }elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'detail'){
		
		$sparams = array(
			'id' => $_REQUEST['id'], 
			'block'=>$module,
			'contactid'=>$_SESSION['ID'],
		);
		
		$lmod = get_module_details($sparams);
	
		foreach($lmod[0][$module] as $ticketfield) {	
			$fieldlabel = $ticketfield['fieldlabel'];
			$fieldvalue = $ticketfield['fieldvalue'];
			$blockname = $ticketfield['blockname'];
					
			if(!isset($mod_infos[$blockname])) $mod_infos[$blockname]=array();
			$mod_infos[$blockname][]=array("label"=>$fieldlabel,"value"=>$fieldvalue);				
		}
		
		//$docs=$this->get_documents();
		//if(isset($docs) && count($docs)>0) $mod_infos=array_merge($mod_infos, $docs);
		$params = Array(
			'contactid' => $_SESSION['ID'],
			'id' => $_REQUEST['id']
		);
		
		$nameFields = get_record_entity_name_fields($params);
		
		$recordNameField = "";
		
		if(!empty($nameFields)){
			foreach($nameFields as $nameField){
				$recordNameField .= "<span>$nameField</span>";
			}
		}
		
		$data['entityname'] = $recordNameField;	
		$data['recordinfo']=$mod_infos;

?>
<div class="m-portlet m-portlet--mobile">
	
	
	<?php 
		if(isset($data['recordinfo']) && count($data['recordinfo'])>0 && $data['recordinfo']!=""){
			if(isset($data['entityname']) && $data['entityname'] != ""): 
	?>
            	<div class="m-portlet__head">
            		<div class="m-portlet__head-caption">
            			<div class="m-portlet__head-title">
            				<h3 class="m-portlet__head-text">
            					<i><img src="assets/img/Contacts.png"  alt="<?php echo vtranslate($module,'Vtiger'); ?>" title="<?php echo vtranslate($module,'Vtiger'); ?>"></i>
            					<?php echo $data['entityname']; ?>
            				</h3>
            			</div>
            		</div>
            	</div>
					
				<?php endif; ?>
				<div class="m-portlet__body">
					<?php 
						foreach($data['recordinfo'] as $blockname => $tblocks): 
							$z = 0;
							$totalcols = count($tblocks);
							$colCount = 0;
					?>
						<h5 class="form-section"><?php echo vtranslate($blockname,$module); ?></h5>
						<?php 
							foreach($tblocks as $field){
								if($z==0 || $z%2==0)
									echo '<div class="row">';
								$colCount++;
						?>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label col-md-6 pull-left"><b><?php echo vtranslate($field['label'],$module);?>:</b></label>
										<div class="col-md-6 pull-right">
											<p class="form-control-static"> <?php echo $field['value']; ?> </p>
										</div>
									</div>
								</div>
								
								<?php 
									if($totalcols == $colCount && ($colCount % 2) == 1)
										echo '<div class="col-md-6"><div class="form-group">&nbsp</div></div></div>';
								?>
						<?php
								if($z > 0 && ($z % 2) == 1) {
									echo "</div>";
									$z++;
								} else{ 
									$z++;
								}									
							}
							echo "<hr>";
						?>
				<?php endforeach; ?>
			</div>
		<?php 
			} else { 
		?> 
				<h1 class="page-title">&nbsp;</h1>
				<div class="row">
					<div class="col-md-12 page-500">
						<div class="text-center">
							<h3><?php echo vtranslate("The record you are trying to access not found!",$module); ?></h3>
						</div>
					</div>
				</div>
		<?php 
			} 
		?> 
	
</div>

<?php }?>
<?php 
	include_once("includes/footer.php");
?>
<script>
	var TotalRecords = jQuery("[name='total_records']").val();

	var DatatablesBasicBasic={
		init:function(){
			var e;(e=$("#responsiveDataTables")).DataTable({
				responsive:!0,
				pageLength:10,
				bFilter: false,
				//bInfo: false,
				bLengthChange: false,
			})
		}
	};
	jQuery(document).ready(function(){DatatablesBasicBasic.init()});
	
</script>
