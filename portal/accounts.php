<?php
	include_once('includes/head.php');

	if(!isset($_SESSION["ID"])) {
		header("Location: login.php");
	} else {
		$module = 'Accounts';
		include_once('includes/menu.php'); 	
		include_once('includes/function.php'); 	
	}
	
	$account_id = $_SESSION['accountid'];
	$sparams = array(
		'id' => $account_id, 
		'block'=> $module,
		'contactid'=>$_SESSION['ID'],
	);
		
		
	$lmod = get_details($sparams);

		
	foreach($lmod[0][$module] as $ticketfield) {	
		$fieldlabel = $ticketfield['fieldlabel'];
		$fieldvalue = $ticketfield['fieldvalue'];
		$blockname = $ticketfield['blockname'];
				
		if(!isset($mod_infos[$blockname])) $mod_infos[$blockname]=array();
		$mod_infos[$blockname][]=array("label"=>$fieldlabel,"value"=>$fieldvalue);				
	}
	
	$params = Array(
		'id' => $account_id ,
		'module' => "Documents",
		'contactid' => $_SESSION['ID'], 
	);
	
	$resultb = get_documents($params);
		
	if(isset($resultb) && count($resultb)>0 && $resultb!=""){
		$ca=0;
		foreach($resultb[1]['Documents']['data'] as $doc){ 
			$mod_infos["Attachments"][$ca]['label']="File"; 
			$mod_infos["Attachments"][$ca]['value']=$doc[1]['fielddata']; 
			$ca++;
		}
	}
	
	
	$docs = $mod_infos;
	
	if(isset($docs) && count($docs)>0) $mod_infos=array_merge($mod_infos, $docs);
	
	$params = Array(
		'contactid' => $_SESSION['ID'],
		'id' => $account_id
	);
	
	$nameFields = get_record_entity_name_fields($params);
	
	$recordNameField = "";
	
	if(!empty($nameFields)){
		foreach($nameFields as $nameField){
			$recordNameField .= "<span>$nameField</span>";
		}
	}
	
	$data['entityname'] = $recordNameField;
	$data['recordinfo'] = $mod_infos;
	
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
            					<i><img src="assets/img/Accounts.png" alt="<?php echo vtranslate($module,'Vtiger'); ?>" title="<?php echo vtranslate($module,'Vtiger'); ?>"></i>
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
						<h5 class="form-section"><?php if($blockname =='LBL_CONTACT_INFORMATION'){echo vtranslate($blockname,'Contacts');}else echo vtranslate($blockname,$module); ?></h5>
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
<?php 
	include_once("includes/footer.php");
?>