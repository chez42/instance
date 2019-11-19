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

global $api_url,$api_username,$api_accesskey;

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
						
						<div class="form-group row">
							<label class="col-lg-2 col-form-label">Street</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="mailingstreet" value="<?php echo $customer_detail['mailingstreet']; ?>">
                    		</div>
                    		<label class="col-lg-2 col-form-label">City</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="mailingcity" value="<?php echo $customer_detail['mailingcity']; ?>">
                    		</div>
						</div>
						<div class="form-group row">
							<label class="col-lg-2 col-form-label">State</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="mailingstate" value="<?php echo $customer_detail['mailingstate']; ?>">
                		    </div>
                    		<label class="col-lg-2 col-form-label">Zip</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="mailingzip" value="<?php echo $customer_detail['mailingzip']; ?>">
                    		</div>
						</div>
						<div class="form-group row">
							<label class="col-lg-2 col-form-label">Mobile Phone</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="mobile" value="<?php echo $customer_detail['mobile']; ?>">
                			</div>
                			<label class="col-lg-2 col-form-label">Office Phone</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="phone" value="<?php echo $customer_detail['phone']; ?>">
                            </div>
						</div>
						<div class="form-group row">
							<label class="col-lg-2 col-form-label">Home Phone</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="homephone" value="<?php echo $customer_detail['homephone']; ?>">
        					</div>
        					<label class="col-lg-2 col-form-label">Email</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="email" value="<?php echo $customer_detail['email']; ?>">
                            </div>
						</div>
						<div class="form-group row">
							<label class="col-lg-2 col-form-label">LinkedIn</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="cf_805" value="<?php echo $customer_detail['cf_805']; ?>">
                            </div>
                            <label class="col-lg-2 col-form-label">Birthdate</label>
							<div class="col-lg-4">
								<div class="input-group date">
									<input type="text" class="form-control" name="birthday" value="<?php echo $customer_detail['birthday']; ?>" id="kt_datepicker_3">
									<div class="input-group-append">
										<span class="input-group-text">
											<i class="la la-calendar"></i>
										</span>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-lg-2 col-form-label">Marital status</label>
							<div class="col-lg-4">
								<select class="form-control" name="cf_2182" aria-invalid="false">
									<option value="">Select an Option</option>
									<option value="Married" <?php echo ($customer_detail['cf_2182'] == 'Married')?'selected':''; ?>>Married</option>
									<option value="Divorced" <?php echo ($customer_detail['cf_2182'] == 'Divorced')?'selected':''; ?>>Divorced</option>
									<option value="Widowed" <?php echo ($customer_detail['cf_2182'] == 'Widowed')?'selected':''; ?>>Widowed</option>
									<option value="DomesticPartner" <?php echo ($customer_detail['cf_2182'] == 'DomesticPartner')?'selected':''; ?>>DomesticPartner</option>
									<option value="Unknown" <?php echo ($customer_detail['cf_2182'] == 'Unknown')?'selected':''; ?>>Unknown</option>
									<option value="Separated" <?php echo ($customer_detail['cf_2182'] == 'Separated')?'selected':''; ?>>Separated</option>
									<option value="Single" <?php echo ($customer_detail['cf_2182'] == 'Single')?'selected':''; ?>>Single</option>
									<option value="Life Partner" <?php echo ($customer_detail['cf_2182'] == 'Life Partner')?'selected':''; ?>>Life Partner</option>
								</select>
                            </div>
                            <label class="col-lg-2 col-form-label">Wedding Anniversary</label>
							<div class="col-lg-4">
								<input type="text" class="form-control" name="cf_667" value="<?php echo $customer_detail['cf_667']; ?>">
                            </div>
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
		jQuery(document).ready(function() {
    		$("#customer-info").validate({ 
    		    submitHandler: function( form ) {
    		    	KTApp.block('#customer-info', {
		                overlayColor: '#000000',
		                type: 'v2',
		                state: 'primary',
		                message: 'Processing...'
		            });
    		    	$.ajax({
        	            type: "POST",
        	            url: 'update-customer.php',
        	            data: jQuery('#customer-info').serialize(), // serializes the form's elements.
        	            success: function(result){
        	            	var data = JSON.parse(result);
        	            	if(data.success){
        	            		toastr.info('Changes Saved Successfully');
        	            	}
        	            	KTApp.unblock('#customer-info');
        	            }
        	        });
    		    }
    		});
    		$("#update-info").click(function(){
    			$("#customer-info").submit();
    		});
    	});
    </script>
	
</html>   