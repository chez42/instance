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
					<div class="kt-grid__item kt-app__toggle kt-app__aside ticketDetails" id="kt_user_profile_aside" style = "width:290px;">

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
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="kt-grid__item kt-grid__item--fluid kt-app__content commentArea">
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
                                                <div class="kt-scroll" id="comments-container" data-height="410" data-mobile-height="225"></div>
											</div>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
					
					
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
		<link href="assets/css/jquery-comments.css" rel="stylesheet" type="text/css" />
		<script src="assets/js/jquery-comments.js"></script>
    	<script>

    	$(document).ready(function(){

    		$(function() {
				$('#comments-container').comments({

    				profilePictureURL: "<?php echo $portal_profile_image;?>",
    				
    				currentUserId: 1,

    				roundProfilePictures: false,

    				enableReplying : true,
    				
    				textareaRows: 3,

    				textareaPlaceholderText: 'Leave a comment',

    				enableUpvoting: false,
    				enableEditing: false,
    				enableDeleting: false,

    				enableAttachments: true,
    				enableNavigation: false,
    				enablePinging: false,
    				
    				youText: "<?php if($lastname != '' && $firstname != ''){
    				            echo $lastname.' '.$firstname;
    						  }
    						  ?>",
    				
    				getComments: function(success, error) {
    					 $.ajax({
				            type: 'POST',
				            data: 'record='+<?php echo $recordId; ?>,
				            dataType: 'json',
				            url: 'GetTicketComments.php',
				            success: function(commentsArray) {
					            success(commentsArray)
				            },
				            error: error
				     	});
    				},
    				
    				postComment: function(commentJSON, success, error) {

    					$('.commentArea').waitMe({effect : 'orbit',text : '' });
    					
						commentJSON.ticketid = '<?php  echo $_REQUEST['record'];?>';

						var formData = new FormData();

						$.each(commentJSON,function(ind, value){
							 formData.append(ind, value);
						});

						formData.append('file', file);
						
						$.ajax({
    			            type: 'post',
    			            url: 'save-comments.php',
    			            data: formData,
    			            cache: false,
			                contentType: false,
			                processData: false,
    			            success: function(comment) {
    			            	var data = JSON.parse(comment);
				                if(data.result.success){
				                	commentJSON['id'] = data.result.modcommentid;
				                	if(data.result.fileUrl){
    				                	commentJSON['file_url'] = data.result.fileUrl;
    			                		commentJSON['file_mime_type'] = data.result.filetype;
    			                		commentJSON['file'] = data.result.filename;
				                	}
				                	success(commentJSON);
				                	file = {};
				                	$('.filename').remove();
				                }
				                $('.commentArea').waitMe('hide');
    			            },
    			            error: error
    			        });
    			        
    				},

    			
    				
    			});
    		});

    		$('[name="ticketpriorities"]').on('change', function(e){
    			$('.ticketDetails').waitMe({effect : 'orbit',text : '' });
    			var SelectElement = jQuery(e.currentTarget);
    			var priority = SelectElement.val();
				var recordId = $('[name="recordId"]').val();

				var ticket_title = "<?php echo $ticket_detail['ticket_title']; ?>";
				
				$.ajax({
					url: 'save-ticket.php',
        			type: 'POST',
        			data: 'ticketpriorities='+priority+'&recordId='+recordId+'&ticket_title='+ticket_title,
	    			error: function(errorThrown) {
	    				 $('.ticketDetails').waitMe('hide');
	    			},
	    			success: function(data) {
	    				 $('.ticketDetails').waitMe('hide');
	    			}
	    		});
        		
    		});

    		$('[name="ticketstatus"]').on('change', function(e){
    			$('.ticketDetails').waitMe({effect : 'orbit',text : '' });
    			var SelectElement = jQuery(e.currentTarget);
    			var status = SelectElement.val();
				var recordId = $('[name="recordId"]').val();

				var priority = $('[name="ticketpriorities"]').val();
				var ticket_title = "<?php echo $ticket_detail['ticket_title']; ?>";
					
				$.ajax({
					url: 'save-ticket.php',
        			type: 'POST',
        			data: 'ticketpriorities='+priority+'&ticketstatus='+status+'&recordId='+recordId+'&ticket_title='+ticket_title,
	    			error: function(errorThrown) {
	    				 $('.ticketDetails').waitMe('hide');
	    			},
	    			success: function(data) {
	    				 $('.ticketDetails').waitMe('hide');
	    			}
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
