<?php
    include_once("includes/config.php");

    if(!isset($_SESSION['ID'])){
        header("Location: login.php");
        exit;
    }
    include_once("includes/head.php");
    
    include_once "includes/aside.php";
    
    include_once 'includes/top-header.php';
    
    $module = 'Reports';
    
    $reportTypesDetail = getReportTypesData();
    
    $defaultReport = 'gainloss';
    
    $reportFunctionName = $reportTypesDetail[$defaultReport]['function_name'];
    
    $reportFilePath = $reportTypesDetail[$defaultReport]['filepath'];
    
//     $account = getContactAccessibleAccounts($_SESSION['ID']);
    
//     $accountIdNo = array();
    
//     if($_REQUEST['show_reports'] == 'Accounts')
//         $accountIdNo = getContactAccessibleAccounts($_SESSION['accountid']);
    
    
//     $account_number = array_merge($account,$accountIdNo);
    
//     $accounts = array_unique($account_number);
    
    $data = $reportFunctionName($_REQUEST);

?>
		
		
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
   				<div class="kt-container  kt-container--fluid ">
        			<div class="kt-subheader__main">
            			<span class="kt-portlet__head-icon">
        					<i class="kt-font-brand flaticon2-line-chart"></i>
        				</span>
            			<h3 class="kt-subheader__title" style = "padding-left:5px;">
        					Gain / Loss
                        </h3>
                        
                        <span class="kt-subheader__separator kt-hidden"></span>
                        
                        <div class="kt-subheader__breadcrumbs">
                       		<a href="#" class="folderBreadcrumb kt-subheader__breadcrumbs-home" data-folder-id = "">
                        		<i class="la la-home"></i>
                        	</a>
                    	
                        	<span class="kt-subheader__breadcrumbs-separator"></span>
                           	
                           	<a href="" class="kt-subheader__breadcrumbs-link">
                          		Positions
                          	</a>
                          	
                          	
                        </div>
                    </div>
                 </div>
            </div>   
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
   				<div class="kt-container  kt-container--fluid ">
        			<div class="kt-subheader__main">
            			
            			<h3 class="kt-subheader__title">
							Gain / Loss
                        </h3>
                        
                        
                        
                    </div>
			        
       	 		</div>
    		</div>
			
			
			
			<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
				
				<div class="kt-portlet kt-portlet--mobile">
					
					 <div class="kt-portlet__body">
            			<?php 
            			   require_once($reportFilePath);  
            			?>
					 </div>
					 
				</div>
			</div>
		</div>			
	
	<?php 
	   include_once "includes/footer.php";
	?>
	</body>
	
	<script src="js/Reports/account_detail.js" type="text/javascript"></script>
    
    <script src="assets/js/jquery.aCollapTable.min.js" type="text/javascript"></script>
    
    <script src="js/Reports/GainLoss.js" type="text/javascript"></script>
</html>    