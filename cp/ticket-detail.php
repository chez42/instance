<?php
include_once "includes/config.php";

if(!isset($_SESSION['ID'])){
    header("Location: login.php");
    exit;
}

if($_SESSION['portal_profile_image'] != ''){
    $portal_profile_image = $_SESSION['portal_profile_image'];
}

if(!empty($_SESSION['data'])){
    $basic_details = $_SESSION['data']['basic_details'];
    $firstname = $basic_details['firstname'];
    $lastname = $basic_details['lastname'];
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
	
	//$module_detail = module_info($api_url.'/webservice.php',$session_id,"HelpDesk");
	//$field_info = $module_detail['result']['fields'];
	
	$ticket_detail = retrieve_info($api_url.'/webservice.php',$session_id,"9x$recordId");
	$ticket_detail = $ticket_detail['result'];

	$params = array();
	$params['owner_id'] = $_SESSION['ownerId'];
	
	$params['ID'] = $recordId;
	
	$params['contact_id'] = $_SESSION['ID'];
	
	if(isset($_REQUEST['index']) && $_REQUEST['index'] != '' ){
	    $params['index'] = $_REQUEST['index'];
	}
	
	$postParams = array(
	    'operation'=>'get_ticket_comments',
	    'sessionName'=>$session_id,
	    'element'=>json_encode($params)
	);
	
	$response = postHttpRequest($ws_url, $postParams);
// 	echo"<pre>";print_r($response);echo"</pre>";
// 	exit;
	$response = json_decode($response,true);
	
	$comment_detail = $response['result'];
	echo"<pre>";print_r($comment_detail);echo"</pre>";exit;
	$ticketstatus = array(
	    '----------',
	    'Acknw',
	    'Open',
	    'In Progress',
	    'Hold',
	    'Wait For Response',
	    'Closed',
	    'NIGO'
	);
	
    $ticketpriorities = array('Low',
	    'Normal',
	    'High',
	    'Urgent',
	);
	
	if($ticket_detail){
?>
					
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

			<!-- begin:: Subheader -->
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
				<div class="kt-container  kt-container--fluid ">
					<div class="kt-subheader__main">
						<h3 class="kt-subheader__title">
							<button class="kt-subheader__mobile-toggle kt-subheader__mobile-toggle--left" id="kt_subheader_mobile_toggle"><span></span></button>
							Ticket </h3>
						<span class="kt-subheader__separator kt-hidden"></span>
						<div class="kt-subheader__breadcrumbs">
							<a href="#" class="kt-subheader__breadcrumbs-home"><i class="flaticon2-shelter"></i></a>
							<span class="kt-subheader__breadcrumbs-separator"></span>
							<a href="" class="kt-subheader__breadcrumbs-link">
								<?php echo $ticket_detail['ticket_no']; ?> </a>
							<span class="kt-subheader__breadcrumbs-separator"></span>
							<!-- <span class="kt-subheader__breadcrumbs-link kt-subheader__breadcrumbs-link--active">Active link</span> -->
						</div>
					</div>
				</div>
			</div>

			<!-- end:: Subheader -->

			<!-- begin:: Content -->
			<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">

				<!--Begin::App-->
				<div class="kt-grid kt-grid--desktop kt-grid--ver kt-grid--ver-desktop kt-app">

					<!--Begin:: App Aside Mobile Toggle-->
					<button class="kt-app__aside-close" id="kt_user_profile_aside_close">
						<i class="la la-close"></i>
					</button>

					<!--End:: App Aside Mobile Toggle-->

					<!--Begin:: App Aside-->
					<div class="kt-grid__item kt-app__toggle kt-app__aside" id="kt_user_profile_aside">

						<!--begin:: Widgets/Applications/User/Profile1-->
						<div class="kt-portlet" style="height: 97%;">
							<div class="kt-portlet__head  kt-portlet__head--noborder">
								<div class="kt-portlet__head-label" style="width: 100%;">
									<h3 class="kt-portlet__head-title" style="width: 100%;text-align:center;">
										<?php echo $ticket_detail['ticket_title']; ?>
									</h3>
								</div>
							</div>
							
							<div class="kt-portlet__body kt-portlet__body--fit-y">

								<!--begin::Widget -->
								<div class="kt-widget kt-widget--user-profile-1">
								
									<div class="kt-widget__body">
										<div class="kt-widget__content">
											
											<div class="kt-widget__info">
												<span class="kt-widget__label">Due Date</span>
												<a href="#" class="kt-widget__data"><?php echo $ticket_detail['cf_656']; ?></a>
											</div>
											
											<div class="kt-widget__info">
												<span class="kt-widget__label">Open days</span>
												<span class="kt-widget__data"><?php echo $ticket_detail['cf_3272']; ?></span>
											</div>
											
											<br/>
											
											<div class="kt-widget__info">
												<div class="kt-widget__data" style="width: 45%;">
													<select class="form-control form-control-sm" name="ticketpriorities">
														<?php foreach ($ticketpriorities as $priority){ ?>
															<option value="<?php echo $priority;?>" <?php if($priority == $ticket_detail['ticketpriorities']) { echo "selected"; }?> ><?php echo $priority;?></option>
														<?php } ?>
													</select>
												</div>
												<div class="kt-widget__data" style="width: 45%;">
													<select class="form-control form-control-sm" name="ticketstatus">
														<?php foreach ($ticketstatus as $status){ ?>
															<option value="<?php echo $status;?>" <?php if($status == $ticket_detail['ticketstatus']) { echo "selected"; }?> > <?php echo $status;?></option>
														<?php } ?>
													</select>
												</div>
											</div>
											<br/>
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

								<!--End:: App Aside-->

								<!--Begin:: App Content-->
					<div class="kt-grid__item kt-grid__item--fluid kt-app__content">
						<div class="row">
							<div class="col-xl-12">
								<div class="kt-portlet">
									<div class="kt-portlet__head">
										<div class="kt-portlet__head-label">
											<h3 class="kt-portlet__head-title">Comments</h3>
										</div>
									</div>
									<form class="kt-form kt-form--fit kt-form--label-right">
									<input type="hidden" name="recordId" value="<?php echo $recordId;?>">
									<input type="hidden" name="scrollevent" value="1" />
									<input type="hidden" name="startIndex" value="10" />
										<div class="kt-portlet__body">
											<div id="containers" style="">
                                                <div class="kt-scroll" id="comments-container" data-height="410" data-mobile-height="225" style="overflow:auto; max-height:430px;"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>

								<!--End:: App Content-->
							</div>

							<!--End::App-->
						</div>

						<!-- end:: Content -->
					</div>
					
					<!-- end:: Header -->
					

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
		<link href="assets/css/jquery-comments.css" rel="stylesheet" type="text/css" />
		<script src="assets/js/jquery-comments.js"></script>
    	<script>

    	$(document).ready(function(){

    		var commentsArray = <?php echo json_encode($comment_detail);?>;
    		console.log(commentsArray);
    		$(function() {
    			var saveComment = function(data) {

    				if(data.content){
    				   	$.ajax({
    						url: "save-comments.php?commentcontent="+data.content+"&customer="+<?php  echo $_REQUEST['record'];?>+"&parent="+data.parent,
    						success: function(commentsdata){
    				    	}
    				    });
    					return data;
    				}
    			}
    			$('#comments-container').comments({
    				profilePictureURL: "<?php echo $portal_profile_image;?>",
    				currentUserId: 1,
    				roundProfilePictures: false,
    				textareaRows: 2,
    				textareaPlaceholderText: 'Leave a comment',
    				enableUpvoting: false,
    				enableEditing: false,
    				//enableReplying: false,
    				enableDeleting: false,
    				enableAttachments: true,
    				enableNavigation: false,
    				enablePinging: false,
    				youText: "<?php if($lastname != '' && $firstname != ''){
    				    echo $lastname.' '.$firstname;
    						}?>",
    				
    				getComments: function(success, error) {
    					setTimeout(function() {
    						success(commentsArray);
    					}, 500);
    				},
    				postComment: function(data, success, error) {
    					setTimeout(function() {
    						success(saveComment(data));
    					}, 500);
    				},
    				putComment: function(data, success, error) {
    					setTimeout(function() {
    						success(saveComment(data));
    					}, 500);
    				},
    				deleteComment: function(data, success, error) {
    					setTimeout(function() {
    						success();
    					}, 500);
    				},
    				upvoteComment: function(data, success, error) {
    					setTimeout(function() {
    						success(data);
    					}, 500);
    				},
				    uploadAttachments: function(commentArray, success, error) {
				        var responses = 0;
				        var successfulUploads = [];
				
				        var serverResponded = function() {
				            responses++;
				            console.log(commentArray);
				            // Check if all requests have finished
				            if(responses == commentArray.length) {
				                
				                // Case: all failed
				                if(successfulUploads.length == 0) {
				                    error();

				                // Case: some succeeded
				                } else {
				                    success(successfulUploads)
				                }
				            }
				        }

				        $(commentArray).each(function(index, commentJSON) {
				        	
				            // Create form data
				            var formData = new FormData();
				            $(Object.keys(commentJSON)).each(function(index, key) {
				                var value = commentJSON[key];
				                if(value) formData.append(key, value);
				            });
				            formData.append('customer',<?php  echo $_REQUEST['record'];?>);

				            $.ajax({
				                url: 'save-comments.php',
				                type: 'POST',
				                data: formData,
				                cache: false,
				                contentType: false,
				                processData: false,
				                success: function(commentJSON) {
				                    successfulUploads.push(commentJSON);
				                    serverResponded();
				                },
				                error: function(data) {
				                    serverResponded();
				                },
				            });
				        });
				    }
    				
    			});
    		});

    		$('select[name="ticketpriorities"]').on('change', function(e){
    			var SelectElement = jQuery(e.currentTarget);
    			var priority = SelectElement.val();
				var recordId = $('[name="recordId"]').val();

				var ticket_title = "<?php echo $ticket_detail['ticket_title']; ?>";
				
				$.ajax({
					url: 'save-ticket.php',
        			type: 'POST',
        			data: 'ticketpriorities='+priority+'&recordId='+recordId+'&ticket_title='+ticket_title,
	    			error: function(errorThrown) {},
	    			success: function(data) {}
	    		});
        		
    		});

    		$('select[name="ticketstatus"]').on('change', function(e){
    			var SelectElement = jQuery(e.currentTarget);
    			var status = SelectElement.val();
				var recordId = $('[name="recordId"]').val();

				var priority = $('[name="ticketpriorities"]').val();
				var ticket_title = "<?php echo $ticket_detail['ticket_title']; ?>";
					
				$.ajax({
					url: 'save-ticket.php',
        			type: 'POST',
        			data: 'ticketpriorities='+priority+'ticketstatus='+status+'&recordId='+recordId+'&ticket_title='+ticket_title,
	    			error: function(errorThrown) {},
	    			success: function(data) {}
	    		});
    		});
    		
    	});
    	
    	</script>
		<script src="assets/js/pages/custom/user/profile.js" type="text/javascript"></script>
		
	</body>

	<!-- end::Body -->
</html>
            
        <?php }
    }
