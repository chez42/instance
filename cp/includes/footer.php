</div>
</div>
	
    <div id="kt_scrolltop" class="kt-scrolltop">
    	<i class="fa fa-arrow-up"></i>
    </div>
    <!--  <ul class="kt-sticky-toolbar" style="margin-top: 30px;">
    	<li class="kt-sticky-toolbar__item kt-sticky-toolbar__item--danger" id="kt_sticky_toolbar_chat_toggler" data-toggle="kt-tooltip" title="" data-placement="left" data-original-title="Chat">
    		<a href="#" data-toggle="modal" data-target="#kt_chat_modal" class='chat_modal'><i class="flaticon2-chat-1"></i></a>
    	</li>
    </ul>-->
    
    <div class="modal fade- modal-sticky-bottom-right" id="kt_chat_modal" role="dialog" data-backdrop="false" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="kt-chat">
                    <div class="kt-portlet kt-portlet--last">
                        <div class="kt-portlet__head">
                            <div class="kt-chat__head ">
                                <div class="kt-chat__left">
                                    <div class="kt-chat__label">
                                        <a href="#" class="kt-chat__title"><?php echo $_SESSION['name'];?></a>
                                        <span class="kt-chat__status">
                                            <span class="kt-badge kt-badge--dot kt-badge--success"></span> Active
                                        </span>
                                    </div>
                                </div>
                                <div class="kt-chat__right">
                                    <button type="button" class="btn btn-clean btn-sm btn-icon" data-dismiss="modal">
                                        <i class="flaticon2-cross"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="kt-portlet__body" style="padding: 0px!important;">
                            <div class="kt-scroll kt-scroll--pull ps ps--active-y" data-height="410" data-mobile-height="300" style="height: 410px; overflow: hidden;">
                                <div class="kt-chat__messages kt-chat__messages--solid">
                                    
                                </div>
                            	<div class="ps__rail-x" style="left: 0px; bottom: 0px;">
                            		<div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                        		</div>
                        		<div class="ps__rail-y" style="top: 0px; right: -2px; height: 410px;">
                        			<div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 143px;"></div>
                    			</div>
                			</div>
                        </div>
                        <div class="kt-portlet__foot">
                    		<div class="kt-chat__input">
                    			
                    			<input type="hidden" class="profile_img"  value="<?php echo file_exists($_SESSION['portal_profile_image']) ? $_SESSION['portal_profile_image']:'';?>">
                            
                                <div class="kt-chat__editor">
                                    <textarea placeholder="Type here..." style="height: 50px"></textarea>
                                </div>
                                
                                <div class="kt-chat__toolbar">
                                    <div class="kt_chat__tools">
                                       <div class="kt-avatar " id="kt_user_avatar_1" style="display:none;">
                                            <label class="kt-avatar__upload" style="position:unset!important;" data-toggle="kt-tooltip" title="" data-original-title="Upload File">
                                                <i class="flaticon2-photograph"></i>
                                                <input type="file" >
                                            </label>
                                        </div>
                                    </div>
                                    <div class="kt_chat__actions">
                                        <button type="button" type="submit" class="btn btn-brand btn-md  btn-font-sm btn-upper btn-bold kt-chat__reply">reply</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="chatfilePreviewModal" class="modal fade" aria-hidden="true">
			
	</div>
    
    
    <div class="modal fade" id="change_password_form" tabindex="-1" role="dialog"  aria-hidden="true">
		<div class="modal-dialog" role="document">
        	<div class="modal-content">
           		<form class="form-horizontal recordEditView" id="change-password" method="post" action="">
					
					<div class="modal-header">
                		<h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
               			<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            		</div>
            
        			<div class="modal-body">
        				<div class="row">
            				<div class="col-md-8">
        						<div class="form-group">
        							<label class="control-label">New Password</label>
        							<input type="password" class="form-control" name="password" id = "password" placeholder="Enter New Password">
        							<div class="help-block with-errors"></div>
        						</div>
        					</div>
        				</div>
        				<div class="row">
        					<div class="col-md-8">
        						<div class="form-group">
        							<label class="control-label">Confirm Password</label>
        							<input type="password" class="form-control" name="confirmpassword" id = "confirmpassword"  placeholder="Confirm Password">
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
	   global $websocketUrl;
	   include_once "includes/common-js.php";
	?>
	
	<link href="assets/js/waitMe.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/waitMe.min.js" type="text/javascript"></script>
    
	<script>

    	jQuery(document).ready(function() {

    		$('<audio id="chatAudio"><source src="audio/notify.ogg" type="audio/ogg"><source src="audio/notify.mp3" type="audio/mpeg"><source src="audio/notify.wav" type="audio/wav"></audio>').appendTo('body');

			window.WebSocket = window.WebSocket || window.MozWebSocket;
			var webSocketUrl = '<?php echo $websocketUrl;?>';
    		if(webSocketUrl){
        		var connection = new WebSocket('<?php echo $websocketUrl;?>');
        	
        		connection.onopen = function () {};
        		connection.onerror = function (error) {};
        		
        		connection.onmessage = function (message) {
    
        			var data = JSON.parse(message.data);
        			
        			var contactid = data.contactid;
        			
        			var fromportal = data.fromportal;
        			
        			if(!fromportal && contactid == '<?php echo $_SESSION['ID']; ?>'){
        				
    					$.ajax({
        					
    						url : 'getComments.php',
        					
    						success: function(data) {
        						
    							if($("#kt_chat_modal").is(":visible")){
            						
    								var scrollEl = KTUtil.find(document.getElementById('kt_chat_modal'), '.kt-scroll');
            						
            						var messagesEl = KTUtil.find(document.getElementById('kt_chat_modal'), '.kt-chat__messages');
            						
            						var commentData = JSON.parse(data);
            						
            						$.each(commentData, function(ind, ele){
            							
    									var html = '';
    							
            							if(ele.users){
            								
    										html = '<div data-commentId="' + ele.commentId + '" class="kt-chat__message kt-chat__message--success" style="margin: 1.5rem; margin-top:5px !important; margin-bottom:0px !important; padding: 10px;min-width: 50%!important;">'+
            		                            '<div class="kt-chat__user">'+
            		                                '<span class="kt-media kt-media--circle kt-media--sm">';
            		                        
    										if(ele.profileImage){
            		                            html += ' <img src="'+ele.profileImage+'" alt="image">';
            		                        } else {
            		                            html += '<i class="flaticon-user"  style="font-size:30px!important;"></i>';
            		                        }
    										
            		                        html += '</span>'+
            		                                '<a href="#" class="kt-chat__username">'+ele.userName+'</a>'+
            		                                '<span class="kt-chat__datetime">'+ele.createdTime+'</span>'+
            		                            '</div>'+
            		                            '<div class="kt-chat__text">' + ele.commentContent;
            		                                if(ele.attachmentId && ele.attachmentId != "0"){
            		                                    html += '<br/><a style="font-size:11px!important;" target="_blank" href="'+ele.siteUrl+'/index.php?module=Vtiger&action=ExternalDownloadLink&record='+ele.commentId+'" >'+ele.attName+'</a>';
            		                                    html += '<a href="javascript:void(0)" data-filelocationtype="I" data-filename="" data-fileid="'+ele.commentId+'">'+
            		            							'<span class="chat_document_preview" title="Preview" style="font-size:1.5em!important;">'+
            		            								'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">'+
            		            									'<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">'+
            		            										'<rect x="0" y="0" width="24" height="24"></rect>'+
            		            										'<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>'+
            		            										'<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"></path>'+
            		            									'</g>'+
            		            								'</svg>'+
            		            							'</span>'+
            		            						'</a>';
            		                                }
            		                        html +=  '</div>'+
            		                        	'</div>';
            		                        
            							} else if(ele.client){
            								 
            								html += '<div data-commentId="'+ele.commentId+'" class="kt-chat__message kt-chat__message--right kt-chat__message--brand" style="margin:1.5rem !important; margin-top:5px !important; margin-bottom:0px !important; min-width:50%!important;">'+
            		                            '<div class="kt-chat__user">'+
            		                                '<span class="kt-chat__datetime">'+ele.createdTime+'</span>'+
            		                                '<a href="#" class="kt-chat__username">You</a>'+
            		                                '<span class="kt-media kt-media--circle kt-media--sm">';
            		                        
    										if(ele.profileImage){
            		                            html += ' <img src="'+ele.profileImage+'" alt="image">';
            		                        }else{
            		                            html += '<i class="flaticon-user" style="font-size:30px!important;"></i>';
            		                        }
    										
            		                        html += '</span>'+
            		                            '</div>'+
            		                            '<div class="kt-chat__text">'+
            		                            	ele.commentContent;
            		                                if(ele.attachmentId && ele.attachmentId != 0){
            		                                    html += '<br/><a style="font-size:11px!important;" target="_blank" href="'+ele.siteUrl+'/index.php?module=Vtiger&action=ExternalDownloadLink&record='+ele.commentId+'" >'+ele.attName+'</a>';
            		                                    html += '<a href="javascript:void(0)" data-filelocationtype="I" data-filename="" data-fileid="'+ele.commentId+'">'+
            		            							'<span class="chat_document_preview" title="Preview" style="font-size:1.5em!important;">'+
            		            								'<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1" class="kt-svg-icon">'+
            		            									'<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">'+
            		            										'<rect x="0" y="0" width="24" height="24"></rect>'+
            		            										'<path d="M3,12 C3,12 5.45454545,6 12,6 C16.9090909,6 21,12 21,12 C21,12 16.9090909,18 12,18 C5.45454545,18 3,12 3,12 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>'+
            		            										'<path d="M12,15 C10.3431458,15 9,13.6568542 9,12 C9,10.3431458 10.3431458,9 12,9 C13.6568542,9 15,10.3431458 15,12 C15,13.6568542 13.6568542,15 12,15 Z" fill="#000000" opacity="0.3"></path>'+
            		            									'</g>'+
            		            								'</svg>'+
            		            							'</span>'+
            		            						'</a>';
            		                                }
            		                        html +=  '</div>'+
            		                        	'</div>';
            							}
    									
            							if(!$(document).find('div[data-commentId="'+ele.commentId+'"]').length)
            								jQuery(messagesEl).append(html);
            							
            						});
            						 
            						
            						new PerfectScrollbar(scrollEl, {
            	                        wheelSpeed: 0.5,
            	                        swipeEasing: true,
            	                        suppressScrollX: KTUtil.attr(scrollEl, 'data-scroll-x') != 'true' ? true : false
            	                    });
            						
            						setTimeout(() => {
            							const container = document.querySelector('.kt-scroll');
            							container.scrollTop = 0;
            							scrollEl.scrollTop = parseInt(KTUtil.css(messagesEl, 'height'))+parseInt(KTUtil.css(messagesEl, 'height'));
            						}, 0);
    								
        						} else {
    								
    								var params = [];
        							params['message'] = 'New Message Received';
        			               	toastr.error(params['message']);
        						
    							}
    							
        					}
        				});
    					
        				$('#chatAudio')[0].play();
        				
    					getNotificaions();
        			
    				}
        		}
        	}
    		getNotificaions();

    		$(document).on('click', '.closeNotify', function(){
				var notifyId = $(this).data('notifyId');
				var parent = $(this).parent();
				$.ajax({
					url:'getNotifications.php?mode=read&notify_id='+notifyId,
					success: function(data) {
						var readData = JSON.parse(data);
						if(readData.success){
							parent.remove();
							var count = $('.notificationCount').data('countvalue')-1;
							$('.notificationCount').text(count+' new');
							var params = [];
							params['message'] = 'Notification Read Successfully';
			               	toastr.error(params['message']);
						}else{
							var params = [];
							params['message'] = 'Something went wrong try again later';
			               	toastr.error(params['message']);
						}
					}
				});
    		});
			
        	function getNotificaions(){
        		
				$.ajax({
					url:'getNotifications.php',
					success: function(data) {
						var notifyData = JSON.parse(data);
						$('.notificationCount').attr('data-countValue', notifyData.count);
						$('.notificationCount').text(notifyData.count+' new');
						$('.notificationContentArea').append(notifyData.html);
					}
        		});

        	}

    		var validator = $('#change-password').validate({
        	    rules : {
        	         password : "required",
        	    	 confirmpassword : {
        	    		 equalTo: "#password"
                    }
        	    },
        	});
		
        	jQuery('#change-password').on('submit', function (e) {

    	        e.preventDefault();
    	        
    	        if (validator.form()) {
    	        	
					KTApp.block('#change-password', {
		                overlayColor: '#000000',
		                type: 'v2',
		                state: 'primary',
		                message: 'Processing...'
		            });
        	        
					$.ajax({
        	            type: "POST",
        	            url: 'update-customer.php',
        	            data: jQuery('#change-password').serialize(), // serializes the form's elements.
        	            success: function(result)
        	            {
        	            	var data = JSON.parse(result);
        	            	if(data.success){
        	            		toastr.info('Password Changed Successfully');
        	            		KTApp.unblock('#change-password');
        	            		$("#change_password_form").modal('hide');
        	            	}
        	            	
        	            }
        	        });
    	        }
    	        
    	    });
			
			<?php 
				if($_SESSION['topbar']){
			?>
    	    	
				$('.kt-header__topbar-wrapper').trigger('click');
    	    	
				setTimeout(function() {
    	    		$('.kt-header__topbar-wrapper').trigger('click');
    	    		<?php unset($_SESSION['topbar']);?>
    	    	}, 5000);
			
			<?php 
				}
			?>
    	    
    	});
    </script>
    <?php if($_SESSION['chat_widget_code']){?>
        <!--Start of Tawk.to Script-->
        <script type="text/javascript">

	    	var widgetSrc = "https://embed.tawk.to/<?php echo $_SESSION['chat_widget_code'];?>/default";

            var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();

            Tawk_API.visitor = {
            	name : '<?php  echo $_SESSION['name']; ?>',
            	email : '<?php echo $_SESSION['user_email']; ?>'
           	};
            
            (function(){
                var s1 = document.createElement("script"),s0=document.getElementsByTagName("script")[0];
                s1.async = true;
                s1.src = widgetSrc;
                s1.charset = 'UTF-8';
                s1.setAttribute('crossorigin','*');
                s0.parentNode.insertBefore(s1,s0);
            })();
        </script>
        <!--End of Tawk.to Script-->
    <?php }?>