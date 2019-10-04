<?php
/* * *******************************************************************************
 * The Initial Developer of the Original Code is Manish Goyal - Devitechnosolutions.com
 * Portions created by Manish Goyal
 * All Rights Reserved.
 * ****************************************************************************** */
?>
<?php
	include_once('includes/head.php');

	if(!isset($_SESSION["ID"])) {
		header("Location: login.php");
	} else {
		$module = 'Documents';
		include_once('includes/menu.php'); 	
		include_once('includes/function.php'); 	
	}
	
	global $adb,$current_user;
	
	$folders = $adb->pquery("SELECT DISTINCT vtiger_documentfolder.documentfolderid, vtiger_documentfolder.folder_name, 
    vtiger_documentfolder.parent_id 
    FROM vtiger_notes
    INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_notes.notesid
    INNER JOIN vtiger_documentfolder ON vtiger_documentfolder.documentfolderid = vtiger_notes.doc_folder_id
    INNER JOIN vtiger_senotesrel ON vtiger_senotesrel.notesid = vtiger_notes.notesid
    WHERE vtiger_crmentity.deleted = 0 
    AND (vtiger_notes.is_private != 1 OR vtiger_notes.is_private IS NULL) 
    AND (vtiger_documentfolder.hide_from_portal != 1 OR vtiger_documentfolder.hide_from_portal IS NULL )
    AND vtiger_senotesrel.crmid = ?",
    array($_SESSION['ID']));
	
// 	AND (vtiger_crmentity.smcreatorid = ? OR vtiger_crmentity.smownerid = ?)$_SESSION['ownerId'],$_SESSION['ownerId'],
	
	$folderIds = array();
	$foldersData = array();
	if($adb->num_rows($folders)){
	    for($i=0;$i<$adb->num_rows($folders);$i++){
	        $folderIds[] = $adb->query_result($folders,$i,'documentfolderid');
	        $foldersData[] = $adb->query_result_rowdata($folders,$i);
	    }
	}
	
	/*$moduleName = "DocumentFolder";
	
	$currentUserModel = Users_Record_Model::getInstanceFromPreferenceFile($current_user->id);
	
	$queryGenerator = new QueryGenerator($moduleName, $currentUserModel);
	
	$queryGenerator->setFields( array('folder_name','id', 'parent_id') );
	
	$listviewController = new ListViewController($adb, $currentUserModel, $queryGenerator);
	
	$query = $queryGenerator->getQuery();  
	
	$query .= " AND vtiger_documentfolder.hide_from_portal != 1 AND 
        vtiger_documentfolder.documentfolderid  NOT IN (".implode(',',$folderIds).")";
	
	$pos = strpos($query, "SELECT");
	if ($pos !== false) {
	    $query = substr_replace($query, "SELECT DISTINCT vtiger_documentfolder.documentfolderid, ", $pos, strlen("SELECT"));
	}
	
	$documentFolders = $adb->pquery($query,array());
	
	
	if($adb->num_rows($documentFolders)){
	    for($i=0;$i<$adb->num_rows($documentFolders);$i++){
	        $foldersData[] = $adb->query_result_rowdata($documentFolders,$i);
	    }
	}*/
	
?>
	<div class="m-portlet m-portlet--mobile">
		<div class="m-portlet__body " id="folderViewContent">
			<div class="row">
    			<div class="col-sm-12 col-xs-12" >
    				<div style="margin-bottom:5px;font-weight:600;font-size:1.2em;">
    					<label> Notes :</label>
    					<ul>
    						<li>Right click on Document name to preview/download</li>
    					</ul>
					</div>
    				<div class="preFolder module-breadcrumb" title="Back to Previous Folder">
    					<style>
    						.preFolder .current-filter-name, .preFolder .leftIcon{
    					    	margin: 0px !important;
    					    	line-height: 20px !important;
    					    }
    					</style>
    					<span class="pull-right">
    						<label>Show Empty Folders : &nbsp;</label>
    						<input class="pull-right" title="Empty Folders" type="checkbox" name="emptyFolder" value='1'>
    					</span>
    					<?php if(!empty($foldersData)){ ?>
    						<p class="current-filter-name filter-name pull-left cursorPointer">
    							<a class="folderBreadcrumb" data-folder-id="" > <i class="la la-home" style="font-size:1.6rem;"></i> &nbsp </a>
    						</p>
    					<?php }?>
    					
    				</div>
    				<div class="clearfix"></div>
                	<div class="folderContent">
                		
                        <div class='foldersData dragfile row' data-parent-folder="" >
                    
                		<?php if(!empty($foldersData)){?>
                    		<input type="hidden" name='scrollevent' value="<?php if(count($foldersData) >= $index)echo '1';else'0';?>" />
            		   <?php  
            		          foreach($foldersData as $folderData){
            		    ?>
                		    
                            	<div class="col-md-3 folderFiles folderActions" title="<?php echo $folderData['folder_name'];?>" data-folderid="<?php echo $folderData['documentfolderid'];?>" style="padding:5px;cursor:pointer;" >
                					<div class="pull-left" ><img style="border-radius:10px;" src="assets/img/Folder.jpg" /> </div>
                					<span class="fieldLabel"><?php echo substr($folderData['folder_name'],0,20);?></span></br><span style='font-size:11px;'>File Folder</span>
                				</div>
                		
                    		<?php } }else{?>
                    			<div class="col-md-12 emptyRecordsDiv text-center " style="padding:20% 0;">
                        			<div class="emptyRecordsContent">
                        				No Folders found.
                        			</div>
                        		</div>
                			<?php }?>
                		 </div>
                	</div>
            	</div>
        	</div>
    	</div>
    	<div id="filePreviewModal" class="modal fade" aria-hidden="true">
			
    	</div>
	</div>


<?php 
	include_once("includes/footer.php");
?>
<link href="assets/global/plugins/contextMenu/jquery.contextMenu.min.css" rel="stylesheet" type="text/css" />
<link href="assets/global/plugins/waitMe/waitMe.min.css" rel="stylesheet" type="text/css" />
<script src="assets/global/plugins/contextMenu/jquery.contextMenu.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/waitMe/waitMe.min.js" type="text/javascript"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click',".folderFiles",function(){
			$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
			var folderId = $(this).data('folderid');
			var hasClass = $(this).hasClass('filterName');
			var empty = false;
			if($('[name="emptyFolder"]').prop('checked')){
				var empty = true;
			}
			$.ajax({
				url:'includes/folderData.php',
				data: 'folder_id='+folderId+'&emptyFolder='+empty,
				error: function(errorThrown) {
					console.log(errorThrown);
					$('body').waitMe('hide');
				},
				success: function(data) {
					$('.folderContent').html(data);
					var folderId = jQuery('.folderContent').find("[name='folderId']").val();
					var folderName = jQuery('.folderContent').find("[name='folderName']").val();
					
					if(hasClass)
						$('.preFolder').find('p:first').nextAll().remove();
					
					if(jQuery('.preFolder').find('.folderBreadcrumb').length > 0 ){
						
						var html = '<p class="current-filter-name filter-name pull-left cursorPointer" '+
						' title="'+folderName+'">&nbsp;'+
						'<span class="la la-angle-right pull-left leftIcon" aria-hidden="true"></span>'+
						'<a class="folderBreadcrumb" data-folder-id="'+folderId+'">&nbsp;<b style="font-size:1.1rem;">'+folderName+'</b>&nbsp;</a> </p>';
						
						jQuery('.preFolder').find('p:last').after(html);
						
					}
					$('body').waitMe('hide');
				},
				beforeSend: function() {
				}
			});
		});

		$(document).on('click','.folderBreadcrumb', function(){
			$('body').waitMe({effect : 'orbit',text : 'Please wait...' });
			var curEle = $(this);
			var folderId = $(this).data('folderId');
			var empty = false;
			if($('[name="emptyFolder"]').prop('checked')){
				var empty = true;
			}
			$.ajax({
				url:'includes/folderData.php',
				data: 'emptyFolder='+empty+'&folder_id='+folderId,
				error: function(errorThrown) {
					console.log(errorThrown);
				},
				success: function(data) {
					$('.folderContent').html(data);
					curEle.parent('p').nextAll().remove();
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
				url:'includes/folderData.php',
				data:'folder_id='+folderId+'&emptyFolder='+empty,
				error: function(errorThrown) {
					console.log(errorThrown);
				},
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
						url:'includes/folderData.php',
						data: 'folder_id='+folderId+'&index='+index+'&emptyFolder='+empty,
						error: function(errorThrown) {
							console.log(errorThrown);
						},
						success: function(data) {
							jQuery('[name="scrollevent"]').remove();
							$(document).find('.foldersData').append(data);
							jQuery('[name="startIndex"]').val(index + parseInt(jQuery('[name="listLimit"]').val()));
							$('body').waitMe('hide');
						}
					});
		    	}
			}
		});

	 $(function(){
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
		});
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
	                    						url:'includes/folderData.php',
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
