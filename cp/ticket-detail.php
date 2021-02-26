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
    
    $record = explode('x',$_GET['record']);
    
    $recordId = $record[1];
    
    $customer_id = $_SESSION['customer_id'];
    
    $ws_url =  $api_url . '/webservice.php';
    
    $loginObj = login($ws_url, $api_username, $api_accesskey);
    
    $session_id = $loginObj->sessionName;
    
    //$module_detail = module_info($api_url.'/webservice.php',$session_id,"HelpDesk");
    //$field_info = $module_detail['result']['fields'];
    
    $ticket_detail = retrieve_info($api_url.'/webservice.php',$session_id, $_GET['record']);
    $ticket_detail = $ticket_detail['result'];
    
    $element = array('ID' => $_SESSION['ID'], 'ticket_id' => $recordId);
    
    $postParams = array(
        'operation'=>'get_ticket_documents',
        'sessionName'=>$session_id,
        'element'=>json_encode($element)
    );
    
    $response = postHttpRequest($ws_url, $postParams);
    
    $response = json_decode($response,true);
    
    $ticket_docs = $response['result'];
    
    $ticketstatus = $_SESSION['ticketstatus'];
    
    $ticketpriorities = $_SESSION['ticketpriorities'];
    $prevRecordId = null;
    $nextRecordId = null;
    $found = false;
    
    if ($_SESSION['ticket_detail_navigation']) {
        foreach($_SESSION['ticket_detail_navigation'] as $page=>$pageInfo) {
            foreach($pageInfo as $index=>$record) {
                //If record found then next record in the interation
                //will be next record
                if($found) {
                    $nextRecordId = $record;
                    break;
                }
                if($record == $recordId) {
                    $found = true;
                }
                //If record not found then we are assiging previousRecordId
                //assuming next record will get matched
                if(!$found) {
                    $prevRecordId = $record;
                }
            }
            //if record is found and next record is not calculated we need to perform iteration
            if($found && !empty($nextRecordId)) {
                break;
            }
        }
    }
    
    if($ticket_detail){
        
        $allowedModule = $_SESSION['data']['basic_details']['allowed_modules'];
        
        foreach ($allowedModule as $allModule){
            if($allModule['module'] == 'HelpDesk')
                $ticket_edit = $allModule['edit_record'];
        }
        ?>
					
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">

			<!-- begin:: Subheader -->
			<input type="hidden" name="ticket_id" value="<?php echo $recordId;?>">
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
				<div class="kt-container  kt-container--fluid ">
					<div class="kt-subheader__main">
						<h3 class="kt-subheader__title">
							<button class="kt-subheader__mobile-toggle kt-subheader__mobile-toggle--left" id="kt_subheader_mobile_toggle"><span></span></button>
							<a href="helpdesk.php"><?php echo $_SESSION['HelpDesk']; ?></a> </h3>
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
					<div class="kt-subheader__toolbar">
            			<div class="kt-subheader__wrapper">
            			
            				<button class="btn btn-dark btn-icon" title="Previous Record" onclick="window.location.href='ticket-detail.php?record=<?php echo $prevRecordId;?>'" <?php if(!$prevRecordId) echo'disabled="disabled"';?>>  
            					<i class="la la-angle-left"></i>
        					</button>
        					<button class="btn btn-dark btn-icon" title="Next Record" onclick="window.location.href='ticket-detail.php?record=<?php echo $nextRecordId;?>'" <?php if(!$nextRecordId) echo'disabled="disabled"';?>>  
            					<i class="la la-angle-right"></i>
        					</button>
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
    												<?php if($ticket_edit){?>
    													<select class="form-control form-control-sm" name="ticketpriorities">
    														<?php foreach ($ticketpriorities as $priority){ ?>
    															<option value="<?php echo $priority;?>" <?php if($priority == $ticket_detail['ticketpriorities']) { echo "selected"; }?> ><?php echo $priority;?></option>
    														<?php } ?>
    													</select>
													<?php }else{
													    echo '<b>Priority</b> : '. $ticket_detail['ticketpriorities'];
                                                    }?>
												</div>
												<div class="kt-widget__data" style="width: 45%;">
    												<?php if($ticket_edit){?>
    													<select class="form-control form-control-sm" name="ticketstatus">
    														<?php foreach ($ticketstatus as $status){ ?>
    															<option value="<?php echo $status;?>" <?php if($status == $ticket_detail['ticketstatus']) { echo "selected"; }?> > <?php echo $status;?></option>
    														<?php } ?>
    													</select>
													<?php }else{
													    echo '<b>Status</b> : '. $ticket_detail['ticketstatus'];
													}?>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="kt-portlet kt-portlet--tabs kt-portlet--height-fluid">
                            	<div class="kt-portlet__head pull-right">
                            		<div class="kt-portlet__head-label pull-right">
                            			<h3 class="kt-portlet__head-title">
                            				Documents
                            			</h3>
                            			<div style="margin-left: 90% !important;">
                                			<button class="add-doc-btn btn btn-brand btn-icon" title="Add Documents">
                                				<i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                        
                            		</div>
                            	</div>
                            	<div class="kt-portlet__body ticketDocList" >
                    				<div class="kt-widget4">
                    					<?php if(!empty($ticket_docs)){
											    foreach($ticket_docs as $document){
											    ?>
                        					<div class="kt-widget4__item">
                        						<div class="kt-widget4__pic kt-widget4__pic--pic">
                        							<img style="border-radius:10px;" src="images/<?php echo $document['icon']?>" />   
                        						</div>
                        						<div class="kt-widget4__info ticketinfo">
                        							<a href="javascript:void(0)" data-filelocationtype="<?php echo $document['filelocationtype'];?>" 
                        								data-filename="<?php echo $document['docname'];?>" data-fileid="<?php echo $document['notesid'];?>"
                        								class="kt-widget4__username" style="font-size: 0.9rem !important;"title="Preview">
                        								<?php echo $document['title']?>
                        							</a>
                        							<p class="kt-widget4__text">
                        								<span class="document_preview" title="Preview" style="font-size:1.5em!important;">
                            								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            										<rect x="0" y="0" width="24" height="24"/>
                            										<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            										<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"/>
                            									</g>
                            								</svg>
                            							</span>&nbsp;&nbsp;
                            							<span class="document_download" title="Download" style="font-size:1.5em!important;">
                            								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">
                            									<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            										<rect x="0" y="0" width="24" height="24"/>
                            										<path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>
                            										<rect fill="#000000" opacity="0.3" transform="translate(12.000000, 8.000000) rotate(-180.000000) translate(-12.000000, -8.000000) " x="11" y="1" width="2" height="14" rx="1"/>
                            										<path d="M7.70710678,15.7071068 C7.31658249,16.0976311 6.68341751,16.0976311 6.29289322,15.7071068 C5.90236893,15.3165825 5.90236893,14.6834175 6.29289322,14.2928932 L11.2928932,9.29289322 C11.6689749,8.91681153 12.2736364,8.90091039 12.6689647,9.25670585 L17.6689647,13.7567059 C18.0794748,14.1261649 18.1127532,14.7584547 17.7432941,15.1689647 C17.3738351,15.5794748 16.7415453,15.6127532 16.3310353,15.2432941 L12.0362375,11.3779761 L7.70710678,15.7071068 Z" fill="#000000" fill-rule="nonzero" transform="translate(12.000004, 12.499999) rotate(-180.000000) translate(-12.000004, -12.499999) "/>
                            									</g>
                            								</svg>
                            							</span>
                        							</p>							 		 
                        						</div>						 
                        					</div> 
                						<?php }}?> 
                            		</div>
                            	</div>
                            </div>
                            
                            
						
						</div>
					</div>
					
					<div class="kt-grid__item kt-grid__item--fluid kt-app__content ">
						<div class="row">
							<div class="col-xl-12">
								<div class="kt-portlet">
									<div class="kt-portlet__head">
										<div class="kt-portlet__head-label">
											<h3 class="kt-portlet__head-title">Description</h3>
										</div>
									</div>
									<div class="kt-portlet__body">
										<div id="containers" style="">
                                            <div class=""><?php echo $ticket_detail['description'];?></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row commentArea">
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
		<link href="assets/plugins/custom/uppy/dist/uppy.min.css" rel="stylesheet" type="text/css" />
		<script src="assets/plugins/custom/uppy/dist/uppy.min.js" type="text/javascript"></script>
		
		
    	<script>

    	$(document).ready(function(){

    		var uppy = Uppy.Core({
					autoProceed: false,
					allowMultipleUploads: true,
    				restrictions: {
        			    maxFileSize: 20971520,
        			    allowedFileTypes: ['.jpg', '.jpeg', '.png', '.gif', '.pdf', '.doc', '.docx']
        			}
				}).use(Uppy.Dashboard, {
              	inline: false,
              	trigger: '.add-doc-btn',
              	target: '.add_doc_modal',
              	replaceTargetContent: true,
                showProgressDetails: true,
                height: 470,
            }).use(Uppy.XHRUpload, { endpoint: 'upload-ticket-documents.php?ticket_id='+$("[name='ticket_id']").val() })

          	uppy.on('complete', (result) => {

          		$('#add_doc_modal').waitMe({effect : 'orbit',text : 'Please wait...' });

        		$.ajax({
					url:'FetchData.php',
					data: 'ticket_id='+$("[name='ticket_id']").val()+'&module=TicketDocuments',
					success: function(data) {
						$('.ticketDocList').replaceWith(data);
					    $('#add_doc_modal').waitMe('hide');
					}
				});
				
          	});
    		
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

    				enableAttachments: false,
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

    		jQuery(document).on('click','.document_preview, .document_download', function(){
          		var self = $(this);
          		$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
          		var currentTargetObject = self.closest('.ticketinfo').find('a');
          		var fileId = currentTargetObject.data('fileid');
        		var fileLocationType = currentTargetObject.data('filelocationtype');
    	        var fileName = currentTargetObject.data('filename'); 
    	        
    	       	if(self.hasClass('document_download')){
					var mode = 'download';
    	       	}else if(self.hasClass('document_preview')){
    	       		var mode = 'preview';
    	       	}
      			if(fileLocationType == 'I'){
    	        	
    	            $.ajax({
						url:'filePreview.php',
						data: 'file_id='+fileId+'&mode='+mode,
						error: function(errorThrown) {
							console.log(errorThrown);
						},
						success: function(data) {
    						var success;
						 	try {
						        var data = JSON.parse(data);
						        if(data.success)
							        success = true;
						    } catch (e) {
					      		success = false;
						    }
						    if(success){
						    	window.location.href = data.downloadUrl;
						    }else{
    							$(document).find('#filePreviewModal').html(data);
    							$('#filePreviewModal').modal('show');
						    }
						    $('body').waitMe('hide');
						}
					});
    	            
    	        } else {
    	            var win = window.open(fileName, '_blank');
    	            win.focus();
    	        }
      		});

      		jQuery(document).on('click','.addDocs',function(){

      			$('#add_doc_modal').modal('show');
				
      		});
    		
    	});
    	
    	</script>
        <script src="assets/js/pages/custom/user/profile.js" type="text/javascript"></script>
		
	</body>
	<div class="add_doc_modal"></div>
	<div id="filePreviewModal" class="modal fade" aria-hidden="true">
    						
	<!-- end::Body -->
</html>
            
        <?php }
    }
