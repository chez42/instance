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
							
						    <button class="btn btn-brand btn-elevate btn-icon-sm createTicket">
								<i class="la la-plus"></i>
								New Ticket
							</button>
							
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
									<!-- <th>Action</th> -->
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
			
			<div class="modal fade" id="createTicketModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" style="display: none;" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        
                        <form class="form-horizontal recordEditView" id="createticket" method="post" action="" novalidate="novalidate">
					
        					<div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Create Ticket</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                </button>
                            </div>
                    
                			<div class="modal-body">
                				<div class="row">
                    				<div class="col-md-8">
                						<div class="form-group validate is-invalid">
                							<label class="control-label">Title</label>
                							<input type="text" class="form-control" name="ticket_title" id="ticket_title" placeholder="Enter Title" >
                							<div class="help-block with-errors"></div>
                						</div>
                					</div>
                				</div>
                				<div class="row">
                					<div class="col-md-8">
                						<div class="form-group">
                							<label class="control-label">Priority</label>
                							<select class="form-control" id="ticketpriorities" name="ticketpriorities">
                    							<option value="">Select an Option</option>
                    							<option value="Low">Low</option>
                    							<option value="Normal">Normal</option>
                    							<option value="High">High</option>
                    							<option value="Urgent">Urgent</option>
                    						</select>
                							<div class="help-block with-errors"></div>
                						</div>
                					</div>
                				</div>
                				<div class="row">
                					<div class="col-md-8">
                						<div class="form-group">
                							<label class="control-label">Description</label>
                							<textarea class="form-control" name="description" id="description" placeholder="Description" ></textarea>
                							<div class="help-block with-errors"></div>
                						</div>
                					</div>
                				</div>
                				<div class="row">
                					<div class="col-md-8">
                						<div class="form-group">
                							<label class="control-label">Due date</label>
                							<input class="form-control"  type="date" id="cf_656" name="cf_656">
                							<div class="help-block with-errors"></div>
                						</div>
                					</div>
                				</div>
                			</div>
                    	
                			<div class="modal-footer quickCreateActions">
                    			<button class="btn" type="reset" data-dismiss="modal">Cancel</button>
                    			<button class="btn btn-success" type="submit"><strong>Save</strong></button>
                        	</div>
                		</form>
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

    	jQuery(document).on('click','.createTicket',function(){
  			$('#createTicketModal').modal('show');
  		});
    	jQuery(document).ready(function() {

    		var validator = $('#createticket').validate({
        	    rules : {
        	    	 ticket_title : "required",
        	    	 description : "required",
        	    	 ticketpriorities : "required"
        	    },
        	});
		
        	jQuery('#createticket').on('submit', function (e) {

    	        e.preventDefault();
    	        
    	        if (validator.form()) {
    	        	$('#createticket').waitMe({effect : 'orbit',text : 'Please wait...' });
        	        $.ajax({
        	            type: "POST",
        	            url: 'save-ticket.php',
        	            data: jQuery('#createticket').serialize(), 
        	            success: function(result)
        	            {
            	            
        	            	var data = JSON.parse(result);
        	            	if(data.success){
        	            		toastr.info('Password Changed Successfully');
        	            		$('#createticket').waitMe('hide');
        	            		$("#createTicketModal").modal('hide');
        	            		location.reload();
        	            	}
        	            	
        	            }
        	        });
    	        }
    	        
    	    });
    	    
    	});
    </script>
</html>    