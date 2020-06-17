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

$recordId = '';

$loginObj = login($ws_url, $api_username, $api_accesskey);

$session_id = $loginObj->sessionName;

$module_detail = module_info($ws_url,$session_id,"HelpDesk");

$field_info = $module_detail['result']['fields'];

if($_GET['record']){
    
    $recordId = $_GET['record'];
    
    $ticket_detail = retrieve_info($ws_url,$session_id,"9x$recordId");
    
    $ticket_detail = $ticket_detail['result'];
}

?>

	<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

		<!-- begin:: Subheader -->
		<div class="kt-subheader   kt-grid__item" id="kt_subheader">
			<div class="kt-container  kt-container--fluid ">
				<div class="kt-subheader__main">
					<h3 class="kt-subheader__title">New Ticket</h3>
				</div>
			</div>
		</div>

				
		<div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile" id="kt_page_portlet">
			<div class="kt-portlet__head kt-portlet__head--lg">
				<div class="kt-portlet__head-label">
					<!-- <h3 class="kt-portlet__head-title">Edit</h3> -->
				</div>
				<div class="kt-portlet__head-toolbar">
					<a href="javascript:history.back()" class="btn btn-clean kt-margin-r-10">
						<i class="la la-arrow-left"></i>
						<span class="kt-hidden-mobile">Back</span>
					</a>
					<div class="btn-group">
						<button type="button" class="btn btn-brand" id="save_ticket">
							<i class="la la-check"></i>
							<span class="kt-hidden-mobile">Save</span>
						</button>
					</div>
				</div>
			</div>
			<div class="kt-portlet__body">
				<form class="kt-form" id="ticket_edit" method="post" action="save-ticket.php">
					
					<input type="hidden" name="recordId" value="<?php echo $recordId; ?>" />
				
					<div class="row">
						<div class="col-xl-2"></div>
						<div class="col-xl-8">
							<div class="kt-section kt-section--first">
								<div class="kt-section__body">
									
									<?php 
										$count = 0;
										foreach ($field_info as $index => $fields){ 
										
										    if($fields['name'] == 'assigned_user_id' || $fields['name'] == 'parent_id' || $fields['name'] == 'financial_advisor' || 
										        $fields['name'] == 'modifiedby' || $fields['name'] == 'id' || $fields['name'] == 'ticket_no' || $fields['name'] == 'createdtime'
										        || $fields['name'] == 'modifiedtime' || $fields['name'] == 'creator' || $fields['name'] == 'project_id'){
										        continue;
										    }
										    if($count == 0) {?>
											    <div class="form-group row">
										    <?php }?>
										    <label class="col-lg-2 col-form-label"><?php echo $fields['label'];?></label>
										    <?php if($fields['type']['name'] == 'string') {?>
                                            <div class="col-lg-3">
												<input type="text" class="form-control" name="<?php echo $fields['name'];?>" value="<?php echo $ticket_detail[$fields['name']]?>">
                                            </div>
										  	<?php } else if($fields['type']['name'] == 'picklist') {?>
										  		<div class="col-lg-3">
        									  		<select class="form-control" name="<?php echo $fields['name'];?>">
        									  			<?php 
        									  			foreach ($fields['type']['picklistValues'] as $picklist_index => $field_val) {?>
        													<option value="<?php echo $field_val['value']; ?>" <?php if($ticket_detail[$fields['name']] == $field_val['value']){ echo 'Selected'; }?>><?php echo $field_val['value'];?></option>
        												<?php }?>
        									  		</select>
    									  		</div>
										  	<?php } else if($fields['type']['name'] == 'date') { ?>
										  		<div class="col-lg-3">
										  			<div class="input-group date">
        												<input type="text" class="form-control" name="<?php echo $fields['name']; ?>" value="<?php echo $ticket_detail[$fields['name']]?>" id="kt_datepicker_3">
        												<div class="input-group-append">
        													<span class="input-group-text">
        														<i class="la la-calendar"></i>
        													</span>
        												</div>
        											</div>
										  		</div>
										  	<?php } else if($fields['type']['name'] == 'currency') {?>
										  		<div class="col-lg-3">
										  			<div class="input-group">
										  				<div class="input-group-append">
        													<span class="input-group-text">
        														<i class="la la-dollar"></i>
        													</span>
        												</div>
										  				<input type="text" class="form-control" name="<?php echo $fields['name']; ?>" value="<?php echo $ticket_detail[$fields['name']]?>">
										  			</div>
										  		</div>
										  	<?php } else if($fields['type']['name'] == 'boolean') {?>
										  		<div class="col-lg-3">
										  			<div class="input-group">
										  				<label class="kt-checkbox kt-checkbox--solid kt-checkbox--single">
	    										  			<input type="checkbox" class="form-control" name="<?php echo $fields['name']; ?>" <?php if($ticket_detail[$fields['name']] == 1) echo "checked"; ?>>
    										  				<span></span>
									  					</label>
    										  		</div>
										  		</div>
										  	<?php } else if($fields['type']['name'] == 'text') {?>
										  		<div class="col-lg-3">
										  			<input type="text" class="form-control" name="<?php echo $fields['name']; ?>" value="<?php echo $ticket_detail[$fields['name']]?>">
										  		</div>
										  	<?php } ?>
                                        	<?php if($count == 1) {
                                        	   $count = 0;
                                        	    ?>
											    </div>
										    <?php }else{
										        $count++;
										    }?>
									<?php }?>
									
									
								</div>
								<div class="col-xl-2"></div>
							</div>
						</form>
					</div>
				</div>
				<!-- end:: Content -->
			</div>
		</div>
	</div>
</div>

<!-- end:: Page -->

<!-- begin::Scrolltop -->
<div id="kt_scrolltop" class="kt-scrolltop">
	<i class="fa fa-arrow-up"></i>
</div>

<!-- end::Scrolltop -->

<!-- begin::Global Config(global config for global JS sciprts) -->
<script>
	var KTAppOptions = {
		"colors": {
			"state": {
				"brand": "#5d78ff",
				"dark": "#282a3c",
				"light": "#ffffff",
				"primary": "#5867dd",
				"success": "#34bfa3",
				"info": "#36a3f7",
				"warning": "#ffb822",
				"danger": "#fd3995"
			},
			"base": {
				"label": [
					"#c5cbe3",
					"#a1a8c3",
					"#3d4465",
					"#3e4466"
				],
				"shape": [
					"#f0f3ff",
					"#d9dffa",
					"#afb4d4",
					"#646c9a"
				]
			}
		}
	};
</script>
<?php 
    include_once "includes/footer.php";
?>
<script>

jQuery(document).ready(function() {
	
	$("#ticket_edit").validate({ 
	    ignore: ':hidden', 
	    submitHandler: function( form ) {
	        //To do
	    }
	});
});

jQuery('#save_ticket').on('click',function(){
	
	if($("#ticket_edit").valid()){
		$('#ticket_edit')[0].submit();
	}
});

</script>
<script src="assets/js/pages/crud/forms/widgets/bootstrap-datepicker.js" type="text/javascript"></script>
</body>
<!-- end::Body -->
</html>