<?php
	include_once('includes/head.php');

	if(!isset($_SESSION["ID"])) {
		header("Location: login.php");
	} else {
		$module = 'Reports';
		include_once('includes/menu.php'); 	
		include_once('includes/function.php'); 	
	}
	
	$field = $_REQUEST['show_reports'];
	
	$accountid = $_SESSION['accountid'];
	
	$data = array();
	
	//$data = get_reports($sparams);
	
	$data['accountid'] = $accountid;
	$data['show_reports'] = $field;
	
	$grandTotals = isset($data['grandTotals'])?$data['grandTotals']:array();
	$summary_info = isset($data['summary_info'])?$data['summary_info']:array();
	$hideLinks = isset($data['hide_links'])?$data['hide_links']:array();
	
	$allowedReports = $GLOBALS['user_basic_details']['allowed_reports'];
	
?>

<div class="m-portlet m-portlet--mobile">
	<div class="m-portlet__head">
		<div class="m-portlet__head-caption">
			<div class="m-portlet__head-title">
				<h3 class="m-portlet__head-text">
					<?php 
					   if($field == 'Contacts')
                           echo 'My Accounts';
                       elseif($field == 'Accounts')
					       echo 'Household Accounts';
                    ?>
				</h3>
			</div>
		</div>
	</div>
	<div class="m-portlet__body">
		<!--begin: Datatable -->
		<div id="m_table_1_wrapper" class="dataTables_wrapper dt-bootstrap4 ">
			<div class="row">
				<div class="col-sm-12">
    				
					<?php if(isset($reportBasicFields['totalRecords']) && $reportBasicFields['totalRecords'] > 0){ ?>
						<input type="hidden" name="total_records" value="<?php echo $reportBasicFields['totalRecords']; ?>" />
					<?php } ?>
					<input type="hidden" id="allowed_account_detail" value="<?php echo (!empty($allowedReports))?1:0;?>" />
					<input type="hidden" id="show_report" value="<?php echo $field; ?>" />
					
					<table class="table table-striped- table-bordered table-hover table-checkable dataTable  " id="responsiveReportsDataTables" role="grid" style="width: 974px;">
						<thead>
							<tr role="row" class="heading">
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Account Number</th>
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Contact</th>
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Account Type</th>
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Total Value</th>
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Market Value</th>
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Cash Value</th>
								<th class='sorting' tabindex='0' aria-controls='responsiveReportsDataTables'>Nickname</th>
							</tr>
						</thead>
						<tbody> 
							<?php foreach($summary_info as $fieldsInfo){?>
								<tr role='row'>
									<td>
										<?php if(!empty($allowedReports)): ?>
											<a href="portfolio_account_detail.php?account_number=<?php echo $fieldsInfo['account_number']; ?>"><?php echo $fieldsInfo['account_number']; ?></a>
										<?php else: ?>
											<?php echo $fieldsInfo['account_number']; ?>
										<?php endif; ?>
									</td>
									<td><?php echo $fieldsInfo['contact_link']; ?></td>
									<td><?php echo $fieldsInfo['account_type']; ?></td>
									<td class="text-right">$<?php echo number_format($fieldsInfo['total_value'], "2");?></td>
									<td class="text-right">$<?php echo number_format($fieldsInfo['securities'], "2");?></td>
									<td class="text-right">$<?php echo number_format($fieldsInfo['cash'], "2");?></td>
									<td><?php echo $fieldsInfo['nickname']; ?></td>
								</tr>
							<?php }?>
						</tbody>
						<tfoot>
							<tr>
								<th><strong>Totals:</strong></th>
								<th>&nbsp;</th>
								<th>&nbsp;</th>
								<th class="text-right">$<?php echo (!empty($grandTotals) && isset($grandTotals['total_value']))?$grandTotals['total_value']:""; ?></th>
								<th class="text-right">$<?php echo (!empty($grandTotals) && isset($grandTotals['market_value']))?$grandTotals['market_value']:""; ?></th>
								<th class="text-right">$<?php echo (!empty($grandTotals) && isset($grandTotals['cash_value']))?$grandTotals['cash_value']:""; ?></th>
								<th>&nbsp;</th>
							</tr>
						</tfoot>
            		</table>
        		</div>
    		</div>
    	</div>
	</div>
</div>
<?php 
	include_once("includes/footer.php");
?>
<script src="js/Reports/detail.js" type="text/javascript"></script>