<?php
include_once("includes/config.php");

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

include_once("includes/head.php");

include_once "includes/aside.php";

include_once 'includes/top-header.php';

?>
		
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
			
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
   				<div class="kt-container  kt-container--fluid ">
        			<div class="kt-subheader__main">
            			<h3 class="kt-subheader__title">
							Tickets
                        </h3>
                    </div>
                    <div class="kt-subheader__toolbar">
            			<div class="kt-portlet__head-actions">
							
							<!--  <a href="edit-ticket.php" class="btn btn-brand btn-elevate btn-icon-sm">
								<i class="la la-plus"></i>
								New Ticket
							</a> -->
							
						</div>
            		</div>
       	 		</div>
    		</div>
    		
    		<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
				
				<div class="kt-portlet kt-portlet--mobile">
					<div class="kt-portlet__body">
						<table class="table table-striped- table-bordered table-hover table-checkable" id="tickets_list">
							<thead>
								<tr>
									<th>Title</th>
									<th>Ticket Number</th>
									<th>Priority</th>
									<th>Status</th>
									<th>Action</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			
		</div>			
		
		
	<?php 
	   include_once "includes/footer.php";
	?>
	</body>
	<script type="text/javascript">
    	var table = jQuery('#tickets_list');
    	table.DataTable({
    		responsive: true,
    		searchDelay: 500,
    		processing: true,
    		serverSide: true,
    		searching: false,
    		language: {
    		    "emptyTable" : "No Records Found",
    		},
    		
    		ajax: {
    			url: 'FetchData.php',
    			data: function ( d ) {
    				return $.extend( {}, d, {
    					"module": 'Tickets',
    				} );
    			}
    		},
    		dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
    			"<'row'<'col-sm-5'i><'col-sm-12'tr>>" +
    			"<'row'<'col-sm-5'i><'col-sm-7'p>>",
    	});
    </script>
</html>    