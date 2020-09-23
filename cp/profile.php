<?php
include_once "includes/config.php";

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

include_once("includes/functions.php");

include_once("includes/head.php");

include_once "includes/aside.php";

include_once 'includes/top-header.php';

global $api_url, $api_username, $api_accesskey, $profilefield;

$ws_url =  $api_url . '/webservice.php';

$customerId = $_SESSION['ID'];

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

$customer_detail = retrieve_info($ws_url,$session_id,"4x$customerId");

$customer_detail = $customer_detail['result'];

//$module_detail = module_info($ws_url,$session_id,"Contacts");
//$field_info = $module_detail['result']['fields'];

?>

	<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
			
		<div class="kt-subheader   kt-grid__item" id="kt_subheader">
			<div class="kt-container  kt-container--fluid ">
    			<div class="kt-subheader__main">
        			
        			<h3 class="kt-subheader__title">
						Customer Information
                    </h3>
                    
                    <span class="kt-subheader__separator kt-hidden"></span>
                    
                    <div class="kt-subheader__breadcrumbs">
                   		<a href="#" class="folderBreadcrumb kt-subheader__breadcrumbs-home" data-folder-id = "">
                    		
                    	</a>
                	</div>
                    
                </div>
		        <div class="kt-subheader__toolbar">
        			<div class="kt-subheader__wrapper">
                    <div class="btn-group">
						<button type="button" class="btn btn-brand" id="update-info">
							<i class="la la-check"></i>
							<span class="kt-hidden-mobile">Save</span>
						</button>
					</div>
                    </div>
        		</div>
   	 		</div>
		</div>
    		
		<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
			
			<div class="kt-portlet kt-portlet--mobile">
				
				<div class="kt-portlet__body">
					<form class="kt-form" id="customer-info" method="post" action="update-customer-info.php">
						<input type="hidden" name="recordId" value="<?php echo $recordId; ?>" />
						<input type="hidden" name="all_fields" value='<?php echo json_encode($profilefield);?>'>
						<div class="form-group row">
							<?php $count =0; 
							     foreach($profilefield as $profile_field){
							    ?>
    							<label class="col-lg-2 col-form-label"><?php echo $profile_field['label']?></label>
    							<div class="col-lg-4">
    								<?php if($profile_field['type'] == 'salutation' || $profile_field['type'] == 'string' || $profile_field['type'] == 'text' 
    								    || $profile_field['type'] == 'email' || $profile_field['type'] == 'phone'){?>
        								<input type="text" class="form-control" name="<?php echo $profile_field['name'];?>" value="<?php echo $customer_detail[$profile_field['name']]; ?>"
        								<?php if(!$customer_detail[$profile_field['name']]) echo'style="border-color: #f7de63;"';?>>
                        			<?php }else if($profile_field['type'] == 'picklist'){?>
                        				<select class="form-control" name="<?php echo $profile_field['name'];?>" aria-invalid="false" <?php if(!$customer_detail[$profile_field['name']]) echo'style="border-color: #f7de63;"';?>>
        									<option value="">Select an Option</option>
        									<?php foreach($profile_field['picklist'] as $pickValue => $picklist){?>
        										<option value="<?php echo $pickValue;?>" <?php echo ($customer_detail[$profile_field['name']] == $pickValue)?'selected':''; ?>><?php echo $picklist;?></option>
        									<?php }?>
        								</select>
    								<?php }else if($profile_field['type'] == 'multipicklist'){
    								    $multiVal = explode(' |##| ', $customer_detail[$profile_field['name']]);
    								    ?>
    									<select class="form-control" multiple name="<?php echo $profile_field['name'];?>[]" aria-invalid="false" <?php if(!$customer_detail[$profile_field['name']]) echo'style="border-color: #f7de63;"';?>>
        									<?php foreach($profile_field['picklist'] as $pickValue => $picklist){?>
        										<option value="<?php echo $pickValue;?>" <?php echo (in_array($pickValue,$multiVal))?'selected':''; ?>><?php echo $picklist;?></option>
        									<?php }?>
        								</select>
                        			<?php }else if($profile_field['type'] == 'date'){?>
                        				<div class="input-group date">
        									<input type="text" class="form-control kt_datepicker_3" name="<?php echo $profile_field['name'];?>" value="<?php echo $customer_detail[$profile_field['name']] ? date('m-d-Y',strtotime($customer_detail[$profile_field['name']])) : '' ; ?>" id="kt_datepicker_3"
        									<?php if(!$customer_detail[$profile_field['name']]) echo'style="border-color: #f7de63;"';?>>
        									<div class="input-group-append">
        										<span class="input-group-text">
        											<i class="la la-calendar"></i>
        										</span>
        									</div>
        								</div>
                        			<?php }else if($profile_field['type'] == 'boolean'){?>
                        				<input type="checkbox" class="" name="<?php echo $profile_field['name'];?>" value="<?php echo $customer_detail[$profile_field['name']] ? 'on' : 'off'; ?>"
        								<?php if(!$customer_detail[$profile_field['name']]) echo'style="border-color: #f7de63;"'; else echo 'checked';?>>
                        			<?php }?>
                        		</div>
                        		
                    		<?php $count++;
							     if (!($count % 2)){ echo '</div><div class="form-group row">'; }
						     }?>
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
		jQuery(document).ready(function() {
    		$("#customer-info").validate({ 
    		    submitHandler: function( form ) {
    		    	KTApp.block('#customer-info', {
		                overlayColor: '#000000',
		                type: 'v2',
		                state: 'primary',
		                message: 'Processing...'
		            });

					var date ='';
		            jQuery('.kt_datepicker_3').each(function(i, e){
						if(jQuery(e).val()){
    						var d = new Date(jQuery(e).val()),
    	    		        month = (d.getMonth() + 1),
    	    		        day = d.getDate(),
    	    		        year = d.getFullYear();
    
    						month = (month<10)?'0'+month:month;
    						day = (day<10)?'0'+day:day;
    						
    						date += '&'+jQuery(e).attr('name')+'='+year+'-'+month+'-'+day;
						}
		            });


    		    	$.ajax({
        	            type: "POST",
        	            url: 'update-customer.php',
        	            data: jQuery('#customer-info').serialize()+date, // serializes the form's elements.
        	            success: function(result){
        	            	var data = JSON.parse(result);
        	            	if(data.success){
        	            		toastr.info('Changes Saved Successfully');
        	            		location.reload();
        	            	}
        	            	KTApp.unblock('#customer-info');
        	            }
        	        });
    		    }
    		});

    		$('[type="checkbox"]').click(function(){
				console.log($(this).val())
				if($(this).prop('checked'))
					$(this).val('on');
				else
					$(this).val('off');

    		});
    		
    		$("#update-info").click(function(){
    			$("#customer-info").submit();
    		});

    		 $('.kt_datepicker_3').datepicker({
			 	format: 'mm-dd-yyyy',
	            todayBtn: "linked",
	            clearBtn: true,
	            todayHighlight: true,
	         });
    	    		
    	});
    </script>
	
</html>   