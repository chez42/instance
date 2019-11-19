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
    
if(isset($_GET['record'])){
        
	global $api_url,$api_username,$api_accesskey;

	$recordId = $_GET['record'];
	
	$customer_id = $_SESSION['customer_id'];
	
	$ws_url =  $api_url . '/webservice.php';
	 
	$loginObj = login($ws_url, $api_username, $api_accesskey);

	$session_id = $loginObj->sessionName;
	
	$module_detail = module_info($api_url.'/webservice.php',$session_id,"HelpDesk");
	
	$field_info = $module_detail['result']['fields'];
	
	$ticket_detail = retrieve_info($api_url.'/webservice.php',$session_id,"9x$recordId");
	
	$ticket_detail = $ticket_detail['result'];
	

	if($ticket_detail){
?>

					<!-- end:: Header -->
					<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

						<!-- begin:: Subheader -->
						<div class="kt-subheader   kt-grid__item" id="kt_subheader">
							<div class="kt-container  kt-container--fluid ">
								<div class="kt-subheader__main">
									<h3 class="kt-subheader__title">
										Ticket <?php echo $ticket_detail['ticket_no']; ?> </h3>
								</div>
								<div class="kt-subheader__toolbar">
                        			<div class="kt-portlet__head-actions">
            							
            						</div>
                        		</div>
							</div>
						</div>

						<!-- end:: Subheader -->

						<!-- begin:: Content -->
						<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
							<div class="row">
								<div class="col-lg-12">
									<!--begin::Portlet-->
									<div class="kt-portlet">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													Detail
												</h3>
											</div>
										</div>

										<!--begin::Form-->
										<form class="kt-form kt-form--fit kt-form--label-right">
											<div class="kt-portlet__body">
												
    									<?php 
												$count = 0;
												foreach ($field_info as $index => $fields){ 
												
												    if($fields['name'] == 'assigned_user_id' || $fields['name'] == 'parent_id' || $fields['name'] == 'financial_advisor' || $fields['name'] == 'modifiedby' || $fields['name'] == 'id'){
												        continue;
												    }
												    if($count == 0) {?>
													    <div class="form-group row">
												    <?php }?>
  												    <label class="col-lg-2 col-form-label"><?php echo $fields['label'];?></label>
                                                    <div class="col-lg-4 col-form-label">
                                                    	<?php echo $ticket_detail[$fields['name']];?>
                                                    </div>
  												  
                                                	<?php if($count == 1) {
                                                	   $count = 0;
                                                	    ?>
													    </div>
												    <?php }else{
												        $count++;
												    }?>
											<?php }?>
											</div>
										</form>

									</div>
									
									<!--end::Portlet-->
								</div>
							</div>
							
							
							
							<!--<div class="row">
								<div class="col-lg-12">
									<!--begin::Portlet-->
									<!--<div class="kt-portlet">
										<div class="kt-portlet__head">
											<div class="kt-portlet__head-label">
												<h3 class="kt-portlet__head-title">
													Comments
												</h3>
											</div>
										</div>

										<!--begin::Form-->
										<!--<form class="kt-form kt-form--fit kt-form--label-right">
											<div class="kt-portlet__body">
												<div class="form-group row">
													<textarea rows="3" id="comment" name="comment" placeholder="Comments" class="form-control textAreaElement lineItemCommentBox"></textarea>
												</div>
											</div>
											<div class="kt-portlet__foot">
												<div class="kt-form__actions">
													<div class="row">
														<div class="col-lg-9 col-xl-9">
															<button type="submit" class="btn btn-success">Comment</button>&nbsp;
														</div>
													</div>
												</div>
											</div>
										</form>

										<!--end::Form-->
									<!--</div>
									
									<!--end::Portlet-->
								<!--</div>
							</div>-->
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
		
	</body>

	<!-- end::Body -->
</html>
            
        <?php }
    }
