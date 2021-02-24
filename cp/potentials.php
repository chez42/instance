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
							Opportunities
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
    						<table class="table table-striped- table-bordered table-hover table table-checkable" id="tickets_list">
    							<thead>
    								<tr>
    									<th>Opportunity no</th>
    									<th>Opportunity</th>
    									<th>Sales Stage</th>
    									<th>Primary Email</th>
    									<th>Mobile Phone</th>
    									<!-- <th>Due Date</th> -->
    									<th>Last Modified</th>
    								</tr>
    								<tr>
    									<td>Opportunity no</td>
    									<td>Opportunity</td>
    									<td>Sales Stage</td>
    									<td>Primary Email</td>
    									<td>Mobile Phone</td>
    									<!-- <td>Due Date</td> -->
    									<td>Last Modified</td>
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
	
	<script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
	
	<script type="text/javascript">
	  var srchVal;
	  var searchRequest = '';
	  var selectedStatus = '';
      
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
    			url: 'FetchData.php?'+searchRequest,
    			data: function ( d ) {
        			
        			if(typeof srchVal == 'undefined'){
        				return $.extend( {}, d, {
        					"module" : 'Potentials',	
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
	        console.log(title)
	        var name = title.toLowerCase().replace(/ /g, '_');
	        if(title != 'Priority' && title != 'Sales Stage' && title != 'Due Date' && title != 'Last Modified')
	        	$(this).html( '<input type="text" class="search_filter form-control"  name="'+name+'" placeholder="Search '+title+'" />' );
	        else if(title == 'Priority'){
				var html = '<select class="search_filter form-control" name="'+name+'"><option value="">Select Priority</option>';
				html += '<option value="Low">Low</option><option value="Normal">Normal</option>'+
					'<option value="High">High</option><option value="Urgent">Urgent</option>'+
					'</select>';
	        	$(this).html( html );
	        }else if(title == 'Sales Stage'){
		        console.log('check')
				var html = '<select class="search_filter form-control" name="'+name+'"><option value="">Select Status</option>';
				html += '<option value="Recruiting Pipeline: Background Check & Recruiting Packet Sent"';
				html +='>Recruiting Pipeline: Background Check & Recruiting Packet Sent</option><option value="Recruiting Pipeline: Business Assessment"';
				html +='>Recruiting Pipeline: Business Assessment</option><option value="Recruiting Pipeline: Won"';
				html += '>Recruiting Pipeline: Won</option><option value="1 Approach"';
				html+='>1 Approach</option><option value="Recruiting Pipeline: Lost"';
				html+='>Recruiting Pipeline: Lost</option><option value="2 Qualify"';
				html +='>2 Qualify</option><option value="3 Profile"';
				html+='>3 Profile</option><option value="4. Proposal"';
				html+='>4. Proposal</option><option value="5 Refine"';
				html+='>5 Refine</option><option value="6 Negotiate"';
				html+='>6 Negotiate</option><option value="7 Agree"';
				html+='>7 Agree</option><option value="Closed Won"';
				html+='>Closed Won</option><option value="Closed Lost"';
				html+='>Closed Lost</option>'+
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
		 	colVal["module"] = 'Potentials';
			$('.search_filter').each(function(sind, sval){
				 colVal[$(this).attr('name')] = $(this).val();
			});
			srchVal = colVal;
			table.search(srchVal).draw();
		});
      	
    
    </script>
</html>    