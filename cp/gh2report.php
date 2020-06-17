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
    
    $defaultReport = 'gh2report';
    
    $reportFunctionName = $reportTypesDetail[$defaultReport]['function_name'];
    
    $reportFilePath = $reportTypesDetail[$defaultReport]['filepath'];
    
//     $account = getContactAccessibleAccounts($_SESSION['ID']);
//     $accountIdNo = array();
//     if($_REQUEST['show_reports'] == 'Accounts')
//         $accountIdNo = getContactAccessibleAccounts($_SESSION['accountid']);
        
//     $account_number = array_merge($account,$accountIdNo);
//     $accounts = array_unique($account_number);
//     $param['account_number'] = $accounts;

    $_REQUEST['calling_record'] = $_SESSION['ID'];
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
    						GH2 Report
    						<!--<input type="hidden" name="portfolio_account_number" id="portfolio_account_number" value="<?php //echo implode(',',$accounts);?>"/>-->
						</h3>
                        
                        <span class="kt-subheader__separator kt-hidden"></span>
                        
                        <div class="kt-subheader__breadcrumbs">
                       		<a href="#" class="folderBreadcrumb kt-subheader__breadcrumbs-home" data-folder-id = "">
                        		<i class="la la-home"></i>
                        	</a>
                    	
                        	<span class="kt-subheader__breadcrumbs-separator"></span>
                           	
                           	<a href="" class="kt-subheader__breadcrumbs-link">
                          		PORTFOLIO SUMMARY
                          	</a>
                          	
                          	
                        </div>
                    </div>
                 </div>
            </div>   
            
			
			
			
			<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
				
				<div class="kt-portlet kt-portlet--mobile">
					
					
					<div class="kt-portlet__body portlet-body">
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
	<!--<script type="text/javascript">
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
	</script>-->
	<script src="js/Reports/account_detail.js" type="text/javascript"></script>
	

</html>    