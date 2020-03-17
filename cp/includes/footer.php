</div>
</div>
	
    <div id="kt_scrolltop" class="kt-scrolltop">
    	<i class="fa fa-arrow-up"></i>
    </div>
    <ul class="kt-sticky-toolbar" style="margin-top: 30px;">
    	<li class="kt-sticky-toolbar__item kt-sticky-toolbar__item--danger" id="kt_sticky_toolbar_chat_toggler" data-toggle="kt-tooltip" title="" data-placement="left" data-original-title="Chat">
    		<a href="#" data-toggle="modal" data-target="#kt_chat_modal" class='chat_modal'><i class="flaticon2-chat-1"></i></a>
    	</li>
    </ul>
    
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
                                       <div class="kt-avatar " id="kt_user_avatar_1">
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
	   include_once "includes/common-js.php";
	?>
	
	<link href="assets/js/waitMe.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/waitMe.min.js" type="text/javascript"></script>
    
	<script>

    	jQuery(document).ready(function() {

    		$('<audio id="chatAudio"><source src="audio/notify.ogg" type="audio/ogg"><source src="audio/notify.mp3" type="audio/mpeg"><source src="audio/notify.wav" type="audio/wav"></audio>').appendTo('body');

			window.WebSocket = window.WebSocket || window.MozWebSocket;
    		
    		var connection = new WebSocket('ws://dev.omnisrv.com:3000');
    	
    		connection.onopen = function () {};
    		connection.onerror = function (error) {};
    		
    		connection.onmessage = function (message) {

    			var data = JSON.parse(message.data);
    			
    			var contactid = data.contactid;
    			
    			var fromportal = data.fromportal;
    			
    			if(!fromportal && contactid == '<?php echo $_SESSION['ID']; ?>'){
    				$.ajax({
    					url:'getComments.php',
    					success: function(data) {
    						if($("#kt_chat_modal").is(":visible")){
        						var scrollEl = KTUtil.find(document.getElementById('kt_chat_modal'), '.kt-scroll');
        						
        						var messagesEl = KTUtil.find(document.getElementById('kt_chat_modal'), '.kt-chat__messages');
        						
        						jQuery(messagesEl).append(data);
        						
        						new PerfectScrollbar(scrollEl, {
        	                        wheelSpeed: 0.5,
        	                        swipeEasing: true,
        	                        suppressScrollX: KTUtil.attr(scrollEl, 'data-scroll-x') != 'true' ? true : false
        	                    });
        						
        						setTimeout(() => {
        							const container = document.querySelector('.kt-scroll');
        							container.scrollTop = 0; //container.scrollHeight;
        						}, 0);
    						} else {
								var params = [];
    							params['message'] = 'New Message Received';
    			               	toastr.error(params['message']);
    						}
    					}
    				});
    				$('#chatAudio')[0].play();
    			}
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
			<?php if($_SESSION['topbar']){?>
    	    	$('.kt-header__topbar-wrapper').trigger('click');
    	    	setTimeout(function() {
    	    		$('.kt-header__topbar-wrapper').trigger('click');
    	    		<?php unset($_SESSION['topbar']);?>
    	    	}, 5000);
			<?php }?>
    	    
    	});
    </script>