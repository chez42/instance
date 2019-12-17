<?php		
    include_once("includes/config.php");
    
	if(!isset($_SESSION['ID'])){
		header("Location: login.php");
		exit;
	}
	
	include_once("includes/head.php");
	
	include_once "includes/aside.php";
	
	include_once 'includes/top-header.php';

	global $api_username, $api_accesskey, $api_url;

	$ws_url =  $api_url . '/webservice.php';

	$loginObj = login($ws_url, $api_username, $api_accesskey);

	$session_id = $loginObj->sessionName;

	$element = array('ID' => $_SESSION['ID'], 'owner_id' => $_SESSION['ownerId']);

	$postParams = array(
		'operation'=>'get_documents',
		'sessionName'=>$session_id,
		'element'=>json_encode($element)
	);
	
	$response = postHttpRequest($ws_url, $postParams);
    
	$response = json_decode($response,true);

	$html = $response['result'];
	
?>
		
		
		<div class="kt-content  kt-grid__item kt-grid__item--fluid kt-grid kt-grid--hor" id="kt_content">
			
			<div class="kt-subheader   kt-grid__item" id="kt_subheader">
   				<div class="kt-container  kt-container--fluid ">
        			<div class="kt-subheader__main">
            			
            			<h3 class="kt-subheader__title">
							Documents
                        </h3>
                        
                        <span class="kt-subheader__separator kt-hidden"></span>
                        
                        <div class="kt-subheader__breadcrumbs">
                       		<a href="#" class="folderBreadcrumb kt-subheader__breadcrumbs-home" data-folder-id = "">
                        		<b><i style= "font-size:1.5rem !important;" class="la la-home"></i></b>
                        	</a>
                    	</div>
                        
                    </div>
			        <div class="kt-subheader__toolbar">
            			<div class="kt-subheader__wrapper">
                          <a href="#" class="btn ">
                          	Show Empty Folders : &nbsp;
                            <input class="pull-right" title="Empty Folders" type="checkbox" name="emptyFolder" value='1'>
                		  </a>
                        </div>
            		</div>
       	 		</div>
    		</div>
    		
    		
    		
    		
    		<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
				
				<div class="kt-portlet kt-portlet--mobile" style = "min-height:400px;">
					
					<div class="kt-portlet__body" id="folderViewContent">
					
						<div class="row">
                			
                			<div class="col-sm-12 col-xs-12" >
                				<input type="hidden" name="startIndex" value="50" />
                				<div class="folderContent">
                            		<?php 
                        			     echo $html;
                        			?>
            					</div>
                        	</div>
                    	</div>
					</div>
					<div id="filePreviewModal" class="modal fade" aria-hidden="true">
			
    				</div>
    				
				</div>
				
			</div>
			
		</div>			
		
		
	<?php 
	   include_once "includes/footer.php";
	?>
	</body>
	<script type="text/javascript">
    	$(document).ready(function(){

    		$(document).on('click',".folderFiles",function(){

    			$('body').waitMe({effect : 'orbit',text : '' });
    			
    			var folderId = $(this).data('folderid');
    			
    			var hasClass = $(this).hasClass('filterName');

    			var empty = false;

    			if($('[name="emptyFolder"]').prop('checked')){
    				var empty = true;
    			}
    			
    			$.ajax({

    				url:'folderData.php',

    				data: 'folder_id='+folderId+'&emptyFolder='+empty,

    				error: function(errorThrown) {
    					$('body').waitMe('hide');
    				},
    				success: function(data) {
        				
    					$('.folderContent').html(data);

    					var folderId = jQuery('.folderContent').find("[name='folderId']").val();

    					var folderName = jQuery('.folderContent').find("[name='folderName']").val();
    					
    					if(hasClass)
    						$('.kt-subheader__breadcrumbs').find('a:first').nextAll().remove();
    					
    					if(jQuery('.kt-subheader__breadcrumbs').find('.folderBreadcrumb').length > 0 ){

    						var html = '<span class="kt-subheader__breadcrumbs-separator"></span>';
                        	html += '<a href="" class="kt-subheader__breadcrumbs-link folderBreadcrumb"  data-folder-id="'+folderId+'">';
                        	html += folderName + '</a>';
                        	
                        	
    						/*var html = '<p class="current-filter-name filter-name pull-left cursorPointer" '+
    						' title="'+folderName+'">&nbsp;'+
    						'<span class="la la-angle-right pull-left leftIcon" aria-hidden="true"></span>'+
    						'<a class="folderBreadcrumb" data-folder-id="'+folderId+'">&nbsp;<b style="font-size:1.1rem;">'+folderName+'</b>&nbsp;</a> </p>';
    						*/
    						
    						jQuery('.kt-subheader__breadcrumbs').find('a:last').after(html);
    						
    					}
    					
    					$('body').waitMe('hide');

        			},

    				beforeSend: function() {}
    				
    			});
    		});
    
    		$(document).on('click','.folderBreadcrumb', function(e){

				e.preventDefault();
				
    			$('body').waitMe({effect : 'orbit',text : 'Please wait...' });

    			var curEle = $(this);
    			
    			var folderId = $(this).data('folderId');

    			var empty = false;

    			if($('[name="emptyFolder"]').prop('checked')){
    				var empty = true;
    			}
    			
    			$.ajax({
    				url:'folderData.php',
    				
    				data: 'emptyFolder='+empty+'&folder_id='+folderId,

    				error: function(errorThrown) {},
    				
    				success: function(data) {
    					$('.folderContent').html(data);

    					curEle.nextAll().remove();
    					curEle.prev().remove();

    					$('body').waitMe('hide');
    				}
    			});
    		});
    
    		$(document).on('change','[name="emptyFolder"]', function(){
        		
    			$('body').waitMe({effect : 'orbit',text : 'Please wait...' });

    			var empty = false;

    			if($(this).prop('checked')){
    				var empty = true;
    			}

    			var folderId = $(document).find('.foldersData').data('parentFolder');

        		var index = parseInt(jQuery('[name="startIndex"]').val());
        		
    			$.ajax({
    				url:'folderData.php',
    				data:'folder_id='+folderId+'&emptyFolder='+empty,
    				error: function(errorThrown) {},
    				success: function(data) {
    					$('.folderContent').html(data);
    					$('body').waitMe('hide');
    				}
    			});
    			
    		});
    
    		 jQuery(document).scroll(function() {
    			if($(document).find('.fileDrag').length > 0){
    				 if ($(window).scrollTop() + $(window).height() >= $(document).height() - 30 && 
    	    			jQuery('[name="scrollevent"]').val() == 1){
    					$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
    		    		var folderId = $(document).find('.foldersData').data('parentFolder');
    		    		var index = parseInt(jQuery('[name="startIndex"]').val());
    		    		var empty = false;
    					if($('[name="emptyFolder"]').prop('checked')){
    						var empty = true;
    					}
    		    		$.ajax({
    						url:'folderData.php',
    						data: 'folder_id='+folderId+'&index='+index+'&emptyFolder='+empty,
    						error: function(errorThrown) {
    							console.log(errorThrown);
    						},
    						success: function(data) {
    							jQuery('[name="scrollevent"]').remove();
    							console.log('daf');
    							$(document).find('.foldersData').append(data);
    							jQuery('[name="startIndex"]').val(index + 50);
    							$('body').waitMe('hide');
    						}
    					});
    		    	}
    			}
    		});

      		jQuery(document).on('click','.document_preview, .document_download', function(){
          		var self = $(this);
          		$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
          		var currentTargetObject = self.closest('a');
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
    
    	 /*$(function(){
    		    $.contextMenu({
    		        selector: '.fileDrag', 
    		        build: function($trigger, e) {
    		            return {
    		                callback: function(key, options) {
    		                	var fileId = $(this).data('fileid');
    		                	if(key == 'preview'){
    		                		var currentTargetObject = $(this).find('a');
    		                		var fileLocationType = currentTargetObject.data('filelocationtype');
    	                	        var fileName = currentTargetObject.data('filename'); 
    	                	        if(fileLocationType == 'I'){
    	                	        	
    	                	            $.ajax({
    	            						url:'filePreview.php',
    	            						data: 'file_id='+fileId,
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
    	            						}
    	            					});
    	                	            
    	                	        } else {
    	                	            var win = window.open(fileName, '_blank');
    	                	            win.focus();
    	                	        }
    		                	}
    		                },
    		                items: {
    		                    "preview": {name: "Preview/Download", icon: "fa-eye", data :('toggle','modal')},
    		                }
    		            };
    		        }
    		    });
    		});*/
    	});

    	
    	
    	MAX_UPLOAD_LIMIT_MB = '';
    	MAX_UPLOAD_LIMIT_BYTES = '';
    	
    	$(document).ready(function(){
    
    		var container = $( 'body' );
    		MAX_UPLOAD_LIMIT_MB = 90;
            MAX_UPLOAD_LIMIT_BYTES = MAX_UPLOAD_LIMIT_MB * 1024 * 1024;
            registerDragDropToUploadEvent(container);
    	});
    
    	function registerEventShowAreaDropToUpload (container) {
            var elementDragDrop = container.find('#dragdropToUpload');
            if (elementDragDrop.length == 0){
                var dragdropContainerHtml =
                    '<div id="dragdropToUpload" class="full-width text-center"style="width:100%;height: 100vh; position: fixed; z-index: 9999999; border: 2px dashed rgb(0, 135, 247); border-radius: 5px; background: rgb(255, 255, 255); opacity: 0.5">' +
                    '   <h3 style="margin-top: 25%"><span class="fa fa-upload"></span> DRAG & DROP FILE TO UPLOAD </h3>' +
                    '</div>';
                container.prepend(dragdropContainerHtml);
            }
        }
    
        function registerEventHideAreaDropToUpload(container) {
            container.find('#dragdropToUpload').remove();
        }
    
        function registerDragDropToUploadEvent(container) {
            var thisInstance = this;
            
            container.on({
                'dragover dragenter': function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if(container.find('.foldersData').data('parentFolder')){
    	                var formUploadOfDocumentModule = container.find('form[name="upload"]');
    	                if (formUploadOfDocumentModule.length == 0){
    	                   registerEventShowAreaDropToUpload(container);
    	                }
                    }
                },
                'drop': function(e) {
                    var formUploadOfDocumentModule = container.find('form[name="upload"]');
                    var folder_id = container.find('.foldersData').data('parentFolder');
                    if(folder_id){
    	                if (formUploadOfDocumentModule.length == 0){
    	                    var dataTransfer =  e.originalEvent.dataTransfer;
    	                    if( dataTransfer && dataTransfer.files.length) {
    	                        e.preventDefault();
    	                        e.stopPropagation();
    	                        $.each( dataTransfer.files, function(i, file) {
    	                            if (file.size < MAX_UPLOAD_LIMIT_BYTES){
    	                                var formData = new FormData();
    	                                formData.append("filename", file);
    	                                formData.append("title", file.name);
    	                                formData.append("filelocationtype", "I");
    	                                formData.append("doc_folder_id", folder_id);
    	                                formData.append("drag", true);
    	                                container.waitMe({effect : 'orbit',text : 'Please wait...' });
    	                                jQuery.ajax({
    	                                    url: 'upload-document.php',
    	                                    data: formData,
    	                                    cache: false,
    	                                    contentType: false,
    	                                    processData: false,
    	                                    type: 'POST',
    	                                    complete: function(){
    	                                        var params = [];
    	                                        params['message'] = 'Upload Success';
    	                                        registerEventHideAreaDropToUpload(container);
    	                                        toastr.success(params['message']);
    	                                        var folderId = $(document).find('.foldersData').data('parentFolder');
    	                    		    		var index = parseInt(jQuery('[name="startIndex"]').val());
    	                    		    		var empty = false;
    	                    					if($('[name="emptyFolder"]').prop('checked')){
    	                    						var empty = true;
    	                    					}
    	                    		    		$.ajax({
    	                    						url:'folderData.php',
    	                    						data: 'folder_id='+folderId+'&emptyFolder='+empty,
    	                    						error: function(errorThrown) {
    	                    							console.log(errorThrown);
    	                    						},
    	                    						success: function(data) {
    	                    							$('.folderContent').html(data);
    	                    							container.waitMe('hide');
    	                    						}
    	                    					});
    	                                        
    	                                    }
    	                                });
    	                            }else{
    	                                var params = [];
    	                                params['message'] = 'File upload limit '+MAX_UPLOAD_LIMIT_MB+'MB';
    	                                toastr.error(params['message'], "Error");
    	                                registerEventHideAreaDropToUpload(container);
    	                            }
    	                        });
    	                    }
    	                }
    	                registerEventHideAreaDropToUpload(container);
    	            }
    
                },
    
                'dragleave' : function (e) {
                	if(container.find('.foldersData').data('parentFolder')){
    	                var formUploadOfDocumentModule = container.find('form[name="upload"]');
    	                if (formUploadOfDocumentModule.length == 0){
    	                    if (e.target.id == 'dragdropToUpload'){
    	                       registerEventHideAreaDropToUpload(container);
    	                    }
    	                }
                	}
                }
            });
        }
    </script>
</html>    