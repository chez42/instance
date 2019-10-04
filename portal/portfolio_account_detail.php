<?php
	include_once('includes/head.php');

	if(!isset($_SESSION["ID"])) {
		header("Location: login.php");
	} else {
		$module = 'Reports';
		include_once('includes/menu.php'); 	
		include_once('includes/function.php'); 	
	}
	
	if(isset($GLOBALS['customer_accessible_accounts']))
		$accessibleAccounts = $GLOBALS['customer_accessible_accounts'];
	else
		$accessibleAccounts = $GLOBALS['customer_accessible_accounts'] = getContactAccessibleAccounts($_SESSION['ID']);
	
	$account_number = $_REQUEST['account_number'];
	
	if(!in_array($account_number,$accessibleAccounts)){
		require_once("not-authorized.php");
		include_once("includes/footer.php");
		exit;
	}
	
	// Hard-Coded Reports
	$GLOBALS['user_basic_details']['allowed_reports']['Overview']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['IncomeLastYear']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['OmniProjected']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['GHReport']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['GH2Report']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['MonthOverMonth']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['OmniIncome']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['AssetClassReport']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['GainLoss']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['OmniIntervals']['visible'] = 1;
	$GLOBALS['user_basic_details']['allowed_reports']['OmniIntervalsDaily']['visible'] = 1;
    	
	$allowedReports = $GLOBALS['user_basic_details']['allowed_reports'];
		
	$reportTypesDetail = getReportTypesData();
	
	if(!empty($allowedReports)){
			
		foreach($allowedReports as $report_name => $permissions){
			
			if($report_name == 'Omnivue') continue;
			
			if(isset($permissions['visible']) && $permissions['visible'] == 1){
				$defaultReport = str_replace(" ", "_", strtolower($report_name));
				break;
			}
		}
		
		$defaultReport = 'ghreport';
		
		$reportFunctionName = $reportTypesDetail[$defaultReport]['function_name'];
		
		$reportFilePath = $reportTypesDetail[$defaultReport]['filepath'];
		
		$params = $account_number;
		
		if($defaultReport == 'ghreport' || $defaultReport == 'gh2report')
			$params = array("account_number" => $account_number);
				
		$data = $reportFunctionName($params);
			
		$data['account_number'] = $account_number;
		
		$data['default_report'] = $defaultReport;
	}
?>

	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__head">
    		<div class="m-portlet__head-caption">
    			<div class="m-portlet__head-title">
    				<h3 class="m-portlet__head-text">
    					<label class="font-dark sbold font-lg"><?php echo $data['portfolio_data']['last_name']; ?></label>
            			<small><?php echo $data['portfolio_data']['account_number']; ?></small>
            			<input type="hidden" name="portfolio_account_number" id="portfolio_account_number" value="<?php echo $data['account_number']; ?>" />
    				</h3>
    			</div>
    		</div>
    		
			<div class="m-portlet__head-caption pull-right">
        		<?php 
        			
        			$allowedReports = $GLOBALS['user_basic_details']['allowed_reports'];
        			
        			if(!empty($allowedReports)):
        		?>
        		
    				<div class="actions">
    					<div class="dropdown">
							<a href="#" class="btn btn-outline-danger m-btn m-btn--custom m-btn--icon m-btn--pill m-btn--air" data-toggle="dropdown" id="dropdownMenuButton" type="button" aria-haspopup="true" aria-expanded="false">
									<span>
									<i class="fa fa-share"></i>
									<span class="hidden-xs"><?php echo Language::translate("Reports "); ?></span>
									<i class="fa fa-angle-down"></i>
								</span>
							</a>
    						
    						<div class="dropdown-menu load_reports" aria-labelledby="dropdownMenuButton" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 40px, 0px);">
						    	<?php 
    								foreach($allowedReports as $reportName => $reportPermission ){
    									if(isset($reportPermission['visible']) && $reportPermission['visible'] == 1){
    										
    										if($reportName == 'Omnivue' || $reportName == 'Income') continue;
    		
    										if($reportName == 'Holdings')
    											echo '<a class="dropdown-item" href="#" id="holdings">' . Language::translate("Account Summary") . '</a>';
    										else if($reportName == 'Income')
    											echo '<a class="dropdown-item" href="#" id="monthly_income">' . Language::translate("Monthly Income") . '</a>';
    										else
    											echo '<a class="dropdown-item" href="#" id="'.str_replace(" ", "_", strtolower($reportName)).'">'.Language::translate($reportName).'</a>';
    									}
    								}
    							?>
						  	</div>
    					</div>
    				</div>
    			<?php endif; ?>
    		</div>
    	</div>
		<div class="m-portlet__body portlet-body">
			
			<?php 
				require_once($reportFilePath);  
			?>

		</div>
	</div>
	
<?php 
	include_once("includes/footer.php");
?>

<script src="js/Reports/account_detail.js" type="text/javascript"></script>
