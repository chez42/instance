<?php
include_once("includes/config.php");

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

include_once("includes/head.php");

include_once "includes/aside.php";

include_once 'includes/top-header.php';

include_once("includes/functions.php");

global $api_username, $api_accesskey, $api_url;

$ws_url =  $api_url . '/webservice.php';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

$element = array(
    'id' => $id,
    'mode' => 'headers',
);

$postParams = array(
    'operation'=>'get_related_potentials',
    'sessionName' => $session_id,
    'element'=>json_encode($element)
);

$response = postHttpRequest($ws_url, $postParams);

$headerResponse = json_decode($response, true);

$headers = $headerResponse['result'];

?>
		
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
			
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
   				<div class="kt-container  kt-container--fluid ">
        			<div class="kt-subheader__main">
            			<h3 class="kt-subheader__title">
							<?php echo $_SESSION['Potentials']; ?>
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
    						<table class="table table-striped- table-bordered table-hover table table-checkable" id="potentials_list">
    							<thead>
    								<tr>
    									<th></th>
    									<?php foreach($headers as $name => $fieldData){
    									    $fields[] = $fieldData['fieldname'];
    									    ?>
    										<th><?php echo $fieldData['label']?></th>
    									<?php }?>
    								</tr>
    								<tr>
    									<td></td>
    									<?php foreach($headers as $name => $fieldData){?>
    										<td data-name="<?php echo $fieldData['fieldname'];?>" data-column="<?php echo $fieldData['columnname']?>" data-type="<?php echo $fieldData['datatype'];?>" data-picklist='<?php echo json_encode($fieldData['picklistVlaue'])?>'><?php echo $fieldData['label']?></td>
    									<?php }?>
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
	  var selectedStatus = '';
      
      var table = jQuery('#potentials_list').DataTable({
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
    			url: 'FetchData.php?fields=<?php echo implode(',', $fields);?>',
    			data: function ( d ) {
        			
        			if(typeof srchVal == 'undefined'){
        				return $.extend( {}, d, {
        					"module" : 'Potentials',	
        				} );
        			}else
    					return $.extend( {}, d, srchVal );
    			}	
    		},
    		
    		dom: "<'row'<'col-sm-3'l><'col-sm-3'f><'col-sm-6'p>>" +
    			"<'row'<'col-sm-5'i><'col-sm-12'tr>>" +
    			"<'row'<'col-sm-5'i><'col-sm-7'p>>",
    	});

      	$('#potentials_list tbody').on('click', 'tr', function () {
            var data = table.row( this ).data();
           	window.location.href = 'potential-detail.php?record='+$(data[0]).attr('id');
        } );

		$('#potentials_list thead td').each( function (i) {
			
	        var title = $(this).data('type');
	       
	        var name = $(this).data('name');

	        var column = $(this).data('column');
	        
	        if(title != 'datetime' && title != 'date' && title != 'picklist' && title != 'multipicklist' && title != '' && typeof title  !== "undefined")
	        	$(this).html( '<input type="text" class="search_filter form-control"  name="'+name+'" placeholder="Search '+title+'" />' );
	        else if(title == 'picklist' || title == 'multipicklist'){
	        	var picklist = $(this).data('picklist');
	        	
				var html = '<select class="search_filter form-control" name="'+name+'"><option value="">Select value..</option>';
				$.each(picklist,function(i,val){
					html += '<option value="'+val+'">'+val+'</option>';
				});
				html +='</select>';
				
	        	$(this).html( html );
	        }else if (title == 'datetime' || title == 'date'){
		        var html = '<div class="input-group date">'+
        				'<input type="text" class="search_filter form-control" name="'+name+'"  id="kt_datepicker_3" palceholder="mm/dd/yyyy">'+
        				'<div class="input-group-append">'+
        					'<span class="input-group-text">'+
        						'<i class="la la-calendar"></i>'+
        					'</span>'+
        				'</div>'+
        			'</div>';
        			$(this).html( html );
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