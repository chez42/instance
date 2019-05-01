<?php
include_once('includes/head.php');

if(!isset($_SESSION["ID"])) {
    header("Location: login.php");
} else {
    $module = 'Reports';
    include_once('includes/menu.php');
    include_once('includes/function.php');
}

$reportTypesDetail = getReportTypesData();

$defaultReport = 'monthovermonth';

$reportFunctionName = $reportTypesDetail[$defaultReport]['function_name'];

$reportFilePath = $reportTypesDetail[$defaultReport]['filepath'];

$account = getContactAccessibleAccounts($_SESSION['ID']);
$accountIdNo = array();
if($_REQUEST['show_reports'] == 'Accounts')
    $accountIdNo = getContactAccessibleAccounts($_SESSION['accountid']);

$account_number = array_merge($account,$accountIdNo);
$accounts = array_unique($account_number);
$data = $reportFunctionName($accounts);

?>

	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__head">
    		<div class="m-portlet__head-caption">
    			<div class="m-portlet__head-title">
    				<h3 class="m-portlet__head-text">
    					Month Over Month Report
    				</h3>
    			</div>
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
