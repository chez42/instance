<?php
include_once("includes/config.php");

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

include_once("includes/head.php");

include_once "includes/aside.php";

include_once 'includes/top-header.php';

$account= $_REQUEST['account'];


?>
		
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
			
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
   				<div class="kt-container  kt-container--fluid ">
        			<div class="kt-subheader__main">
            			<h3 class="kt-subheader__title">
							Positions
                        </h3>
                    </div>
       	 		</div>
    		</div>
    		<style>
                tbody>tr>td:not(:first-child){
                    text-align: left;
                }
    		</style>
    		<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
				
				<div class="kt-portlet kt-portlet--mobile">
					<div class="kt-portlet__body">
    					<div class="table-responsive">
    						<table class="table table-striped- table-bordered table-hover table table-checkable" id="position_list">
    							<thead>
    								<tr>
    									<th>Name</th>
    									<th>Account</th>
    									<th>Quantity</th>
    									<th>Price</th>
    									<th>Value</th>
    									<th>Portfolio%</th>
    									<th>Sec Type</th>
    									<th>Asset Class</th>
    									<th>Last Update</th>
    									<th>Symbol</th>
    								</tr>
    								
    							</thead>
    						</table>
						</div>
					</div>
				</div>
			</div>
		</div>			
		
		
	<?php 
	   include_once "includes/footer.php";
	?>
	</body>
	
	<script type="text/javascript">
	  var srchVal;
	  var searchRequest = '';
	  var selectedStatus = '';
      <?php if($account){?>
	  	var searchRequest = "account=<?php echo $account; ?>";
	  <?php }?>
      
      var table = jQuery('#position_list').DataTable({
		 	bSort: false,
    		responsive: false,
    		searchDelay: 500,
    		processing: true,
    		serverSide: true,
    		searching: false,
    		language: {
    		    "emptyTable" : "No Records Found",
    		},
    		
    		ajax: {
    			url: 'FetchData.php?'+searchRequest,
    			data: function ( d ) {
        			
        			if(typeof srchVal == 'undefined'){
        				return $.extend( {}, d, {
        					"module" : 'Position',	
        				} );
        			}else
    					return $.extend( {}, d, srchVal );
    			}	
    		},
    		
    		dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
    			"<'row'<'col-sm-5'i><'col-sm-12'tr>>" +
    			"<'row'<'col-sm-5'i><'col-sm-7'p>>",
    	});

    </script>
</html>    