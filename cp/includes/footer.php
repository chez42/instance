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
