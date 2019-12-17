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
    		<style>
                tbody>tr>td:not(:first-child){
                    text-align: left;
                }
    		</style>
    		<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
				
				<div class="kt-portlet kt-portlet--mobile">
					<div class="kt-portlet__body">
    					<div class="table-responsive">
    						<table class="table table-striped- table-bordered table-hover table table-checkable" id="tickets_list">
    							<thead>
    								<tr>
    									<th>Title</th>
    									<th>Ticket Number</th>
    									<th>Priority</th>
    									<th>Status</th>
    									<th>Open days</th>
    									<th>Due Date</th>
    									<th>Last Modified</th>
    								</tr>
    								<tr>
    									<td>Title</td>
    									<td>Ticket Number</td>
    									<td>Priority</td>
    									<td>Status</td>
    									<td>Open days</td>
    									<td>Due Date</td>
    									<td>Last Modified</td>
    								</tr>
    							</thead>
    						</table>
						</div>
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
                							<div class="input-group date">
                								<input type="text" class="form-control" name="cf_656"  id="kt_datepicker_3">
            									<div class="input-group-append">
            										<span class="input-group-text">
            											<i class="la la-calendar"></i>
            										</span>
            									</div>
            								</div>
<!--                 							<input class="form-control"  type="text" id="kt_datepicker_3"  name="cf_656"> -->
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
	
	<script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
	
	<script type="text/javascript">
	  var srchVal;
      var table = jQuery('#tickets_list').DataTable({
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
    			url: 'FetchData.php',
    			data: function ( d ) {
        			console.log(srchVal)
        			if(typeof srchVal == 'undefined'){
        				return $.extend( {}, d, {
        					"module" : 'Tickets',	
        				} );
        			}else
    					return $.extend( {}, d, srchVal );
    			}	
    		},
    		
//     		drawCallback: function (settings, json) {
        		
//     		},
    		
    		dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
    			"<'row'<'col-sm-5'i><'col-sm-12'tr>>" +
    			"<'row'<'col-sm-5'i><'col-sm-7'p>>",
    	});

		$('#tickets_list thead td').each( function (i) {
	        var title = $(this).text();
	        var name = title.toLowerCase().replace(/ /g, '_');
	        if(title != 'Priority' && title != 'Status' && title != 'Due Date' && title != 'Last Modified')
	        	$(this).html( '<input type="text" class="search_filter form-control"  name="'+name+'" placeholder="Search '+title+'" />' );
	        else if(title == 'Priority'){
				var html = '<select class="search_filter form-control" name="'+name+'"><option value="">Select Priority</option>';
				html += '<option value="Low">Low</option><option value="Normal">Normal</option>'+
					'<option value="High">High</option><option value="Urgent">Urgent</option>'+
					'</select>';
	        	$(this).html( html );
	        }else if(title == 'Status'){
				var html = '<select class="search_filter form-control" name="'+name+'"><option value="">Select Status</option>';
				html += '<option value="----------">----------</option><option value="Acknw">Acknw</option>'+
					'<option value="Open">Open</option><option value="In Progress">In Progress</option>'+
					'<option value="Hold">Hold</option><option value="Wait For Response">Wait For Response</option>'+
					'<option value="Closed">Closed</option><option value="NIGO">NIGO</option>'+
					'</select>';
	        	$(this).html( html );
	        }else if (title == 'Due Date' || title == 'Last Modified'){
		        var html = '<div class="input-group date">'+
        				'<input type="text" class="search_filter form-control" name="'+name+'"  id="kt_datepicker_3" palceholder="mm/dd/yyyy">'+
        				'<div class="input-group-append">'+
        					'<span class="input-group-text">'+
        						'<i class="la la-calendar"></i>'+
        					'</span>'+
        				'</div>'+
        			'</div>';
        			$(this).html( html );
// 	        	$(this).html( '<input type="date" class="search_filter form-control"  name="'+name+'" placeholder="Search '+title+'" />' );
	        }
		        
 	    });

		$(document).on('change', '.search_filter', function(){
		 	var length = $('.search_filter').length;
		 	var colVal = {};
		 	colVal["module"] = 'Tickets';
			$('.search_filter').each(function(sind, sval){
				 colVal[$(this).attr('name')] = $(this).val();
			});
			srchVal = colVal;
			table.search(srchVal).draw();
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