/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger.Class('Documents_FolderViewRelated_Js', {

	fileObj : false,
	referenceCreateMode : false,
	referenceFieldName : '',

	getInstance : function() {
		return new Documents_FolderViewRelated_Js();
	},

}, {
	
	registerFolderClickEvent : function(){
		var thisInstance = this;
		jQuery(document).on('click','.folderFiles', function(){
			app.helper.showProgress();
			var folderId = $(this).data('folderid');
			var hasClass = $(this).hasClass('filterName');
			var parentId = $('[name="parent_id"]').val();
			
			var params = {
				module : app.getModuleName(),
				relatedModule: 'Documents',
				view: 'Folder',
				record: folderId,
				mode : 'folderViewForDocs',
				submode : 'openFolderFiles',
				parentid : parentId
			};
			
			app.request.post({data: params}).then(function (err, res) {
				if(err == null){
					jQuery('.folderContent').html(res);
					var folderId = jQuery('.folderContent').find("[name='folderId']").val();
					var folderName = jQuery('.folderContent').find("[name='folderName']").val();
					
					if(hasClass)
						$('.preFolder').find('p:first').nextAll().remove();
					
					if(jQuery('.preFolder').find('.folderBreadcrumb').length > 0 ){
						
						var html = '<p class="current-filter-name filter-name pull-left cursorPointer" '+
						' title="'+folderName+'">&nbsp;'+
						'<span class="fa fa-angle-right pull-left leftIcon" aria-hidden="true"></span>'+
						'<a class="folderBreadcrumb" data-folder-id="'+folderId+'">&nbsp;'+folderName+'&nbsp;</a> </p>';
						
						jQuery('.preFolder').find('p:last').after(html);
						
					}
					thisInstance.makeFilesSortable();
					thisInstance.loadMoreScrollEvent();
				}
				app.helper.hideProgress();
			});
		});
	},
	
	registerBackButtonClickEvent : function(){
		var thisInstance = this;
		jQuery(document).on('click','.folderBreadcrumb', function(){
			app.helper.showProgress();
			var curEle = $(this);
			var folderId = $(this).data('folderId');
			var parentId = $('[name="parent_id"]').val();
			var params = {
				module : app.getModuleName(),
				relatedModule: 'Documents',
				view: 'Folder',
				record: folderId,
				mode : 'folderViewForDocs',	
				submode : 'openFolderFiles',
				parentid : parentId
			};
			app.request.post({data: params}).then(function (err, res) {
				if(err == null){
					jQuery('.folderContent').html(res);
					curEle.parent('p').nextAll().remove();
					thisInstance.makeFilesSortable();
					thisInstance.loadMoreScrollEvent();
				}
				app.helper.hideProgress();
			});
		});
	},
	
	loadMoreScrollEvent : function(){
		var thisInstance = this;
		var $table = jQuery('#folder-table');
		var scrollContainers = $table.closest('.folder-table-container');
		var parentId = $('[name="parent_id"]').val();	
		 jQuery(document).scroll(function() {
	           
			if(scrollContainers.find('.fileDrag').length > 0){
				
				 if ($(window).scrollTop() + $(window).height() >= $(document).height() - 30 && 
	    			jQuery('[name="scrollevent"]').val() == 1){
		    		app.helper.showProgress();
		    		var folderId = scrollContainers.find('.foldersData').data('parentFolder');
		    		var index = parseInt(jQuery('[name="startIndex"]').val());
		    		
		    		var params = {
	    				module : app.getModuleName(),
	    				relatedModule: 'Documents',
	    				view: 'Folder',
	    				record: folderId,
	    				mode : 'folderViewForDocs',	
	    				submode : 'loadMoreFiles',
	    				index : index,
	    				parentid : parentId
	    			};
		    		app.request.post({data: params}).then(function (err, res) {
						if(err == null){
							if(res){
								jQuery('[name="scrollevent"]').remove();
								scrollContainers.find('.foldersData').append(res);
								jQuery('[name="startIndex"]').val(index + parseInt(jQuery('[name="listLimit"]').val()));
								thisInstance.makeFilesSortable();
							}
						}
						app.helper.hideProgress();
					});
		    	}
			}
		});
		
	},
	
	/**
	 * Function to regiser the event to make the files sortable
	 */
	makeFilesSortable: function () {
		
		var thisInstance = this;
		var contents = jQuery('#listViewContent').find('.folderContent');
		var table = contents.find('.dragfile');
		
		table.find('.fileDrag').draggable({
			'revert': "invalid",
			'helper':'clone',
			'appendTo':'body',
			'drag':function(e, ui){
				ui.helper.css({
					'width': '30%',
					'background-color':'transparent',
					'height':'auto',
					'z-index':'100001',
				});
			},
		});
		
		$('.connectedSortable').droppable({
			'accept' : '.fileDrag',
			drop: function( event, ui ) {
				var currentBlock = jQuery(this);
				var newFolderId = currentBlock.find('.filterName').data('folderid');
				var draggedElement = jQuery(ui.draggable);
		    	var itemId = draggedElement.data('fileid');
		    	draggedElement.hide();
		    	var params = {
		    		'folderId' : newFolderId,
		    		'itemId' : itemId
		    	};
		    	thisInstance.updateDocumentFolder(params);
			},
		});
	
	},
	
	updateDocumentFolder : function(params){
		
		app.helper.showProgress();
		params['module'] = 'Documents',
		params['action'] = 'UpdateFolder',
		params['mode'] = 'save',
		
		app.request.post({data: params}).then(function (err, res) {
			if(err == null){
				if(res.success)
					app.helper.showSuccessNotification({message : res.message});
				else
					app.helper.showErrorNotification({'message': 'Fail to move document'});
			}else{
				app.helper.showErrorNotification({'message': 'Fail to move document'});
			}
			app.helper.hideProgress();
		});
	},
	
	registerEvents: function(){
		var thisInstance = this;
		var filters = jQuery('.module-filters');
        var scrollContainers = filters.find(".scrollContainer");
        jQuery.each(scrollContainers,function(key,scroll){
            var scroll = jQuery(scroll);
            var listcontentHeight = 350;
            scroll.css("height",listcontentHeight);
            scroll.perfectScrollbar();
        }); 
        thisInstance.registerFolderClickEvent();
        thisInstance.registerBackButtonClickEvent();
        thisInstance.registerNewFileDragEvent();
        thisInstance.registerEventForContextMenu();
        thisInstance.registerEventForCreateFolder();
        
//        var vtigerInstance = Vtiger_Index_Js.getInstance();
//    	vtigerInstance.registerEvents();
    	
	},
	
	registerAddFolderModalEvents : function(container) {
        var self = this;
        var addFolderForm = jQuery('#addDocumentsFolder');
        addFolderForm.vtValidate({
            submitHandler: function(form) {
                var formData = addFolderForm.serializeFormData();
                app.helper.showProgress();
                app.request.post({'data': formData}).then(function(e, res) {
                    
                    if (!e) {
                        if(res.message)
                        	var message = res.message;
                        if(res.success){
                        	app.helper.hideModal();
	                        app.helper.showSuccessNotification({
	                            'message': message
	                        });
	                        self.loadSideBar();
	                        self.loadFolderView();
                        }else{
                        	 app.helper.hideProgress();
                        	app.helper.showErrorNotification({
                                'message': message
                            });
                        }
                    }
                    if (e) {
                        app.helper.showErrorNotification({
                            'message': e
                        });
                    }
                });
            }
        });
    },
    
    registerAddRenameDeleteFolder: function(params){
    	var thisInstance = this;
    	params['module'] = 'Documents';
        params['view'] = 'AddFolder';
        app.helper.showProgress();
        app.request.get({'data':params}).then(function(e,res) {
            app.helper.hideProgress();
            if(!e) {
                app.helper.showModal(res, {
                    'cb' : function(modalContainer) {
                    	thisInstance.registerAddFolderModalEvents(modalContainer);
                    }
                });
            }
        });
    },
    
    registerDeleteFolderEvent: function(params){
    	var thisInstance = this;
    	params['module'] = 'Documents';
		params['action'] = 'GetTreeData';
		app.helper.showProgress();
		app.request.post({'data': params}).then(function(e, res) {
			if (!e) {
                if(res.message)
                	var message = res.message;
                if(res.success){
                    app.helper.showSuccessNotification({
                        'message': message
                    });
                    thisInstance.loadSideBar();
                    thisInstance.loadFolderView();
                }else{
                	 app.helper.hideProgress();
                	app.helper.showErrorNotification({
                        'message': message
                    });
                }
            }
            if (e) {
                app.helper.showErrorNotification({
                    'message': e
                });
            }
		});
    },
    
	registerEventForContextMenu : function(){
		var thisInstance = this;
		$(function(){
		    $.contextMenu({
		        selector: '.folderBreadcrumb:last', 
		        build: function($trigger, e) {
		            return {
		                callback: function(key, options) {
		                	if(key == 'new'){
			                	var folderId = $(this).data('folderId');
			                	var params = { 
		                			'parent' :folderId,
		                			'mode' : 'create_folder'
			                    };
			                	thisInstance.registerAddRenameDeleteFolder(params);
		                	}
		                },
		                items: {
		                    "new": {name: "New Folder", icon: "fa-folder"},
		                }
		            };
		        }
		    });
		});
		
		$(function(){
		    $.contextMenu({
		        selector: '.folderActions', 
		        build: function($trigger, e) {
		            return {
		                callback: function(key, options) {
		                	var folderId = $(this).data('folderid');
		                	if(key == 'edit'){
			                	var params = { 
		                			'folderid' :folderId,
		                			'mode' : 'edit_folder'
			                    };
			                	thisInstance.registerAddRenameDeleteFolder(params);
		                	}else if (key == 'delete'){
		                		
		                		var params = { 
		                			'id' : folderId,
	                	        	'mode' : 'delete_folder',
		                		}
		                		thisInstance.registerDeleteFolderEvent(params);
		                	}
		                },
		                items: {
		                    "edit": {name: "Edit", icon: "fa-pencil-square-o"},
		                    "delete": {name: "Delete", icon: "fa-trash"},
		                }
		            };
		        }
		    });
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
	                	            var params = {
	                	                module : 'Documents',
	                	                view : 'FilePreview',
	                	                record : fileId
	                	            };
	                	            app.request.post({"data":params}).then(function(err,data){
	                	                app.helper.showModal(data);
	                	            });
	                	        } else {
	                	            var win = window.open(fileName, '_blank');
	                	            win.focus();
	                	        }
		                	}else if(key == 'edit'){
		                		var recordUrl = "index.php?module=Documents&view=Edit&record="+fileId;
		                		thisInstance.showOverlayEditView(recordUrl);
		                	}else if(key == 'delete'){
		                		var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
		                		app.helper.showPromptBox({'message': message}).then(function () {
		                			thisInstance.deleteRecord(fileId);
		                		});
		                	}
		                	
		                },
		                items: {
		                    "edit": {name: "Edit", icon: "fa-pencil"},
		                    "preview": {name: "Preview", icon: "fa-eye"},
		                    "delete": {name: "Delete", icon: "fa-trash"},
		                }
		            };
		        }
		    });
		});
	},
	
	registerOverlayEditEvents: function(module, container) {
		var editInstance = Vtiger_Edit_Js.getInstanceByModuleName(module);
		editInstance.setModuleName(module);
		var editContainer = container.find('.overlayEdit');
		editInstance.setEditViewContainer(editContainer);
		editInstance.registerEvents(false);
	},

	showOverlayEditView: function(recordUrl) {
		var self = this;
		var params = app.convertUrlToDataParams(recordUrl);
		params['displayMode'] = 'overlay';
		app.helper.showProgress();
		app.request.get({data: params}).then(function(err, response) {
				app.helper.hideProgress();
				var overlayParams = {'backdrop': 'static', 'keyboard': false};
				app.helper.loadPageContentOverlay(response, overlayParams).then(function(container) {
				var height = jQuery(window).height() - jQuery('.app-fixed-navbar').height() - jQuery('.overlayFooter').height() - 80;

					var scrollParams = {
						setHeight: height,
						alwaysShowScrollbar: 2,
						autoExpandScrollbar: true,
						setTop: 0,
							scrollInertia: 70
					}
					app.helper.showVerticalScroll(jQuery('.editViewContents'), scrollParams);
					self.registerOverlayEditEvents(params.module, container);
					self.registerRecordSave();
					app.event.trigger('post.overLayEditView.loaded', jQuery('.overlayEdit'));
				});
			});
	},
	
	registerRecordSave: function(){
		var thisInstance = this;
		app.event.on('post.overLayEditView.loaded',function(e, container){
			jQuery('#EditView').vtValidate({
				submitHandler : function(form){
					window.onbeforeunload = null;
					var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
					app.event.trigger(e);
					if(e.isDefaultPrevented()) {
						return false;
					}
					var formData = new FormData(form);
					var postParams = {
						data: formData,
						contentType: false,
						processData: false
					};
					app.helper.showProgress();
					app.request.post(postParams).then(function(err,data){
						app.helper.hideProgress();
						if (err === null) {
							jQuery('.vt-notification').remove();
							app.helper.hidePageContentOverlay();
							var params = [];
				            params['message'] = 'Document Edit Successfully';
				            app.helper.showSuccessNotification(params);
							thisInstance.loadFolderView();
						} else {
							app.event.trigger('post.save.failed', err);
						}
				});
				return false;
				}
			});

			jQuery('#EditView').find('.saveButton').on('click', function(e){
				window.onbeforeunload = null;
			});
		});
	},
	
	loadFolderView: function(){
		var folderId = jQuery('[name="folderId"]').val();
		var parentId = $('[name="parent_id"]').val();
		var params = {
			module : app.getModuleName(),
			relatedModule: 'Documents',
			view: 'Folder',
			record: folderId,
			mode : 'folderViewForDocs',	
			submode : 'openFolderFiles',
			parentid : parentId
		};
		app.request.post({data: params}).then(function (err, res) {
			if(err == null){
				jQuery('.folderContent').html(res);
			}
			app.helper.hideProgress();
		});
	},
	
	deleteRecord: function (recordId) {
		var thisInstance = this;
		var module = 'Documents';
		
		var postData = {
			"data": {
				"module": module,
				"action": "DeleteAjax",
				"record": recordId,

			}
		};

		app.helper.showProgress();
		app.request.post(postData).then(function (err, data) {
			if (err == null) {
				var params = [];
	            params['message'] = 'Document Delete Successfully';
	            app.helper.showSuccessNotification(params);
				thisInstance.loadFolderView();
			} else {
				app.helper.hideProgress();
				app.helper.showErrorNotification({message: app.vtranslate(err.message)})
			}
		});
	},
	
	getListViewContentHeight: function () {
		var windowHeight = jQuery(window).height();
		var listViewContentHeight = windowHeight * 0.76103500761035;
		var listViewTable = jQuery('#folder-table');
		if (listViewTable.length) {
			if (!listViewTable.find('tr.emptyRecordsDiv').length) {
				var listTableHeight = jQuery('#folder-table').height();
				if (listTableHeight < listViewContentHeight) {
					listViewContentHeight = listTableHeight + 3;
				}
			}
		}
		if(listViewContentHeight < 50 || listViewContentHeight > 500)
			listViewContentHeight = 500;
		return listViewContentHeight + 'px';
	},
	
	getListViewContentWidth: function () {
		return '100%';
	},
	
	registerFloatingThead: function () {
		if (typeof $.fn.perfectScrollbar !== 'function' || typeof $.fn.floatThead !== 'function') {
			return;
		}
		var $table = jQuery('#folder-table');
		if (!$table.length)
			return;
		var height = this.getListViewContentHeight();
		var width = this.getListViewContentWidth();
		var tableContainer = $table.closest('.folder-table-container');
		tableContainer.css({
			'position': 'relative',
			'height': height,
			'width': width
		});
		tableContainer.perfectScrollbar({
			'wheelPropagation': true
		});
		$table.floatThead({
			scrollContainer: function ($table) {
				return $table.closest('.folder-table-container');
			}
		});
	},
	
	MAX_UPLOAD_LIMIT_MB : '',
	
	MAX_UPLOAD_LIMIT_BYTES : '',
		
	registerNewFileDragEvent : function(){
        var thisInstance = this;
        var container = $( 'body' );

        var params = {};
        params.module = 'Documents';
        params.action = 'GetMaxLimit';
        app.request.post({data:params}).then(
            function(error, response) {
                if (error === null){
                	
                    MAX_UPLOAD_LIMIT_MB = response.MAX_UPLOAD_LIMIT_MB;
                    MAX_UPLOAD_LIMIT_BYTES = MAX_UPLOAD_LIMIT_MB * 1024 * 1024;
                    thisInstance.registerDragDropToUploadEvent(container);
                    
                }
        });
    },

    registerEventShowAreaDropToUpload : function (container) {
        var elementDragDrop = container.find('#dragdropToUpload');
        if (elementDragDrop.length == 0){
            var dragdropContainerHtml =
                '<div id="dragdropToUpload" class="full-width text-center"style="height: 100vh; position: fixed; z-index: 9999999; border: 2px dashed rgb(0, 135, 247); border-radius: 5px; background: rgb(255, 255, 255); opacity: 0.5">' +
                '   <h3 style="margin-top: 25%"><span class="fa fa-upload"></span> DRAG & DROP FILE TO UPLOAD </h3>' +
                '</div>';
            container.prepend(dragdropContainerHtml);
        }
    },

    registerEventHideAreaDropToUpload : function (container) {
        container.find('#dragdropToUpload').remove();
    },

    registerDragDropToUploadEvent : function (container) {
        var thisInstance = this;
        var moduleName = app.getModuleName();
        
        container.on({
            'dragover dragenter': function(e) {
                e.preventDefault();
                e.stopPropagation();
                if(container.find('.foldersData').data('parentFolder')){
	                var formUploadOfDocumentModule = container.find('form[name="upload"]');
	                if (formUploadOfDocumentModule.length == 0){
	                    thisInstance.registerEventShowAreaDropToUpload(container);
	                }
                }
            },
            'drop': function(e) {
                var formUploadOfDocumentModule = container.find('form[name="upload"]');
                var folder_id = container.find('.foldersData').data('parentFolder');
                var parentId = $('[name="parent_id"]').val();	
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
	                                formData.append("module", "Documents");
	                                formData.append("action", "SaveAjax");
	                                formData.append("notes_title", file.name);
	                                formData.append("filelocationtype", "I");
	                                formData.append("doc_folder_id", folder_id);
	                                formData.append("sourceRecord",parentId);
	                                formData.append("relationOperation", true);
	                                formData.append("sourceModule", app.getModuleName());
	                                
	                                app.helper.showProgress();
	                                jQuery.ajax({
	                                    url: 'index.php',
	                                    data: formData,
	                                    cache: false,
	                                    contentType: false,
	                                    processData: false,
	                                    type: 'POST',
	                                    complete: function(){
	                                        var params = [];
	                                        params['message'] = 'Upload Success';
	                                        app.helper.showSuccessNotification(params);
	                                        thisInstance.registerEventHideAreaDropToUpload(container);
	                                        thisInstance.loadFolderView();
	                                    }
	                                });
	                            }else{
	                                var params = [];
	                                params['message'] = 'File upload limit '+MAX_UPLOAD_LIMIT_MB+'MB';
	                                app.helper.showErrorNotification(params);
	                                thisInstance.registerEventHideAreaDropToUpload(container);
	                            }
	                        });
	                    }
	                }
	                thisInstance.registerEventHideAreaDropToUpload(container);
	            }

            },

            'dragleave' : function (e) {
            	if(container.find('.foldersData').data('parentFolder')){
	                var formUploadOfDocumentModule = container.find('form[name="upload"]');
	                if (formUploadOfDocumentModule.length == 0){
	                    if (e.target.id == 'dragdropToUpload'){
	                        thisInstance.registerEventHideAreaDropToUpload(container);
	                    }
	                }
            	}
            }
        });
    },
    
    registerEventForCreateFolder : function(){
    	var thisInstance = this;
    	$(document).on('click','.createFolder',function(){
	    	var folderId = $("[name='folderId']").val();
	    	var params = { 
	    			'parent' :folderId,
	    			'mode' : 'create_folder'
	            };
	    	thisInstance.registerAddRenameDeleteFolder(params);
    	});
    },
    
    loadSideBar: function(){
		var params = {
			module : app.getModuleName(),
			relatedModule: 'Documents',
			view: 'Folder',
			mode : 'folderViewForDocs',	
			submode : 'sidbarEssentials',
		};
		app.request.post({data: params}).then(function (err, res) {
			if(err == null){
				jQuery('.sidebarFolder').html(res);
				var filters = jQuery('.module-filters');
		        var scrollContainers = filters.find(".scrollContainer");
		        jQuery.each(scrollContainers,function(key,scroll){
		            var scroll = jQuery(scroll);
		            var listcontentHeight = 350;
		            scroll.css("height",listcontentHeight);
		            scroll.perfectScrollbar();
		        }); 
			}
		});
	},
	
});

jQuery(document).ready(function () {
	var instance = Documents_FolderViewRelated_Js.getInstance();
	instance.registerEvents();
});

