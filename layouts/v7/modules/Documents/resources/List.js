/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

Vtiger_List_Js("Documents_List_Js", {
    
    massMove : function(url) {
        var self = new Documents_List_Js();
        self.massMove(url);
    },
    
    exportZip : function(url) {
    	
    	var self = app.controller();
        self.exportZip(url);
        
    },
    
    addFolder : function() {
        var self = new Documents_List_Js();
        self.registerAddFolder();
    },
    
    
}, {
    
    registerSearchEvent : function(container) {
        container.find('#searchFolders').on('keydown', function(e) {
            if(e.keyCode === 13) {
                e.preventDefault();
            }
        });
        
        container.find('#searchFolders').on('keyup', function() {
            var searchKey = jQuery(this).val();
            searchKey = searchKey.toLowerCase();
            jQuery('.folder', container).removeClass('selectedFolder');
            container.find('#foldersList').find('.folder').removeClass('hide');
            container.find('#foldersList').find('.folder').filter(function() {
                var currentElement = jQuery(this);
                var folderName = currentElement.find('.foldername').text();
                folderName = folderName.toLowerCase();
                var status = folderName.indexOf(searchKey);
                if(status === -1) return true;
                return false;
            }).addClass('hide');
        });
    },
    
    registerFolderSelectionEvent : function(container) {
        jQuery('.folder', container).on('click', function() {
            jQuery('.folder', container).removeClass('selectedFolder');
            var currentSelection = jQuery(this);
            currentSelection.addClass('selectedFolder');
            var folderId = currentSelection.data('folderId');
            jQuery('input[name="folderid"]').val(folderId);
        });
    },
    
    registerMoveDocumentsEvent : function(container) {
        var self = this;
        container.find('#moveDocuments').on('submit', function(e) {
            e.preventDefault();
            if(container.find('.folder').filter('.selectedFolder').length) {
                var formData = jQuery(e.currentTarget).serializeFormData();
                app.helper.showProgress();
                app.request.post({'data':formData}).then(function(e,res) {
                    app.helper.hideProgress();
                    if(!e) {
                        app.helper.showSuccessNotification({
                            'message' : res.message
                        });
                    } else {
                        app.helper.showErrorNotification({
                            'message' : app.vtranslate('JS_OPERATION_DENIED')
                        });
                    }
                    app.helper.hideModal();
                    self.loadListViewRecords();
                });
            } else {
                app.helper.showAlertNotification({
                    'message' : app.vtranslate('JS_SELECT_A_FOLDER')
                });
            }
        });
    },
    
    registerMoveDocumentsEvents : function(container) {
        this.registerSearchEvent(container);
        this.registerFolderSelectionEvent(container);
        this.registerMoveDocumentsEvent(container);
    },
    
    massMove : function(url) {
        var self = this;
        var listInstance = Vtiger_List_Js.getInstance();
		var validationResult = listInstance.checkListRecordSelected();
		if(!validationResult){
			var selectedIds = listInstance.readSelectedIds(true);
			var excludedIds = listInstance.readExcludedIds(true);
			var cvId = listInstance.getCurrentCvId();
			var postData = {
				"selected_ids":selectedIds,
				"excluded_ids" : excludedIds,
				"viewname" : cvId
			};

            if(app.getModuleName() === 'Documents'){
                var defaultparams = listInstance.getDefaultParams();
                postData['folder_id'] = defaultparams['folder_id'];
                postData['folder_value'] = defaultparams['folder_value'];
            }
			var params = {
				"url":url,
				"data" : postData
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                            self.registerMoveDocumentsEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
    
    unMarkAllFilters : function() {
        jQuery('.listViewFilter').removeClass('active');
    },
    
    unMarkAllTags : function() {
        var container = jQuery('#listViewTagContainer');
        container.find('.tag').removeClass('active').find('i.activeToggleIcon').removeClass('fa-circle-o').addClass('fa-circle');
    },
    
    unMarkAllFolders : function() {
        jQuery('.documentFolder').removeClass('active');
        
    },
    
    registerFoldersClickEvent : function() {
        var self = this;
        var filters = jQuery('#module-filters');
        filters.on('click', '.documentFolder',function(e) {
        	
            var targetElement = jQuery(e.target);
            
            if(!targetElement.hasClass('jstree-ocl')){
            	
	            if(targetElement.is('.dropdown-toggle') || targetElement.closest('ul').hasClass('dropdown-menu') ) return;
	            var element = jQuery(e.currentTarget);
	            
	            var el = jQuery(element);
	            
	            self.resetData();
	            self.unMarkAllFilters();
	            self.unMarkAllTags();
	            self.unMarkAllFolders();
	            el.closest('li').addClass('active');
	
	            self.loadFilter(jQuery('input[name="allCvId"]').val(), {
	                folder_id : 'doc_folder_id',
	                folder_value : el.data('filter-id')
	            });
	            
				var filtername = jQuery('a[class="filterName"]',element).text();
			
            }
        });
    },
    
    registerFiltersClickEvent : function() {
        var self = this;
        var filters = jQuery('#module-filters');
        filters.on('click', '.listViewFilter', function() {
            self.unMarkAllFolders();
        });
    },
    
    addFolderToList : function(folderDetails) {
        var html = ''+
        '<li style="font-size:12px;" class="documentFolder">'+
            '<a class="filterName" href="javascript:void(0);" data-filter-id="'+folderDetails.folderid+'" data-folder-name="'+folderDetails.folderName+'" title="'+folderDetails.folderDesc+'">'+
                '<i class="fa fa-folder"></i> '+
                '<span class="foldername">'+folderDetails.folderName+'</span>'+
            '</a>'+
            '<div class="dropdown pull-right">'+
                '<span class="fa fa-caret-down dropdown-toggle" data-toggle="dropdown" aria-expanded="true"></span>'+
                '<ul class="dropdown-menu dropdown-menu-right vtDropDown" role="menu">'+
					'<li class="editFolder " data-folder-id="'+folderDetails.folderid+'">'+
						'<a role="menuitem" ><i class="fa fa-pencil-square-o"></i>&nbsp;Edit</a>'+
					'</li>'+
                    '<li class="deleteFolder" data-deletable="1" data-folder-id="'+folderDetails.folderid+'">'+
                        '<a role="menuitem"><i class="fa fa-trash"></i>&nbsp;Delete</a>'+
                    '</li>'+
                '</ul>'+
            '</div>'+
        '</li>';
        jQuery('#folders-list').append(html).find('.documentFolder:last').find('.foldername').text(folderDetails.folderName);
    },
    
    registerAddFolderModalEvents : function(container) {
        var self = this;
        var addFolderForm = jQuery('#addDocumentsFolder');
        addFolderForm.vtValidate({
            submitHandler: function(form) {
                var formData = addFolderForm.serializeFormData();
                app.helper.showProgress();
                app.request.post({'data': formData}).then(function(e, res) {
                    app.helper.hideProgress();
                    if (!e) {
                        app.helper.hideModal();
                        app.helper.showSuccessNotification({
                            'message': res.message
                        });
                        var folderDetails = res.info;
                        self.addFolderToList(folderDetails);
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
    
    registerAddFolderEvent : function() {
        var self = this;
        var filters = jQuery('#module-filters');
        filters.find('#createFolder').on('click', function() {
            var params = {
                'module' : app.getModuleName(),
                'view' : 'AddFolder'
            };
            app.helper.showProgress();
            app.request.get({'data':params}).then(function(e,res) {
                app.helper.hideProgress();
                if(!e) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                            self.registerAddFolderModalEvents(modalContainer);
                        }
                    });
                }
            });
        });
    },
    
    registerFoldersSearchEvent : function() {
    	
        var filters = jQuery('#module-filters');
        filters.find('.search-folders').on('keyup', function(e) {
            var value = $(this).val().toLowerCase();
            $("#folders-list li").filter(function() {
              $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    },
    
    registerDeleteFolderEvent : function() {
        var filters = jQuery('#module-filters');
        filters.on('click','li.deleteFolder',function(e) {
            var element = jQuery(e.currentTarget);
            
            var deletable = element.data('deletable');
            if(deletable == '1') {
                app.helper.showPromptBox({
                    'message' : app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE')
                }).then(function() {
                    var folderId = element.data('folderId');
                    var params = {
                        module : app.getModuleName(),
                        mode  : 'delete_folder',
                        action : 'GetTreeData',
                        id : folderId
                    };
                    app.helper.showProgress();
                    app.request.post({'data' : params}).then(function(e,res) {
                        app.helper.hideProgress();
                        if(!e) {
                            filters.find('.documentFolder').filter(function() {
                                var currentTarget = jQuery(this);
                                if(currentTarget.find('a.filterName').data('filterId') == folderId) {
                                    return true;
                                }
                                return false;
                            }).remove();
                            app.helper.showSuccessNotification({
                                'message' : res.message
                            });
                        }
                    });
                });
            } else {
                app.helper.showAlertNotification({
                    'message' : app.vtranslate('JS_FOLDER_IS_NOT_EMPTY')
                });
            }
        });
    },
    
    updateFolderInList : function(folderDetails) {
        jQuery('#folders-list').find('a.filterName[data-filter-id="'+folderDetails.folderid+'"]')
                .find('.foldername').text(folderDetails.folderName);
    },
    
    registerEditFolderModalEvents : function(container) {
        var self = this;
        container.find('#addDocumentsFolder').on('submit', function(e) {
            e.preventDefault();
            var formData = jQuery(this).serializeFormData();
            app.helper.showProgress();
            app.request.post({'data':formData}).then(function(e,res) {
                app.helper.hideProgress();
                if(!e) {
                    app.helper.hideModal();
                    if(res.success){
	                    app.helper.showSuccessNotification({
	                        'message' : res.message
	                    });
	                    var folderDetails = res.info;
	                    self.updateFolderInList(folderDetails);
                    }else{
                    	app.helper.showAlertNotification({
                            'message' : res.message
                        });
                    }
                    
                } else {
                    app.helper.showAlertNotification({
                        'message' : e
                    });
                }
            });
        });
    },
    
    registerFolderEditEvent : function() {
        var self = this;
        var filters = jQuery('#module-filters');
        filters.on('click','li.editFolder',function(e) {
            var element = jQuery(e.currentTarget);
            var folderId = element.data('folderId');
            var params = {
                'module' : app.getModuleName(),
                'view' : 'AddFolder',
                'folderid' : folderId,
                'mode' : 'edit_folder'
            };
            app.helper.showProgress();
            app.request.get({'data':params}).then(function(e,res) {
                app.helper.hideProgress();
                if(!e) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                            self.registerEditFolderModalEvents(modalContainer);
                        }
                    });
                }
            });
        });
    },
    
    registerRowDoubleClickEvent: function () {
        return true;
    },

	getDefaultParams: function() {
		
		var search_value = jQuery('.sidebar-menu').find('.documentFolder.active').data('filterId');
		
		var customParams = {
			'folder_id' : 'doc_folder_id',
			'folder_value' : search_value
		};
		
		if(
			jQuery("#selectedDocFolID").length > 0 && 
			typeof jQuery("#selectedDocFolID").val() != 'undefined' &&
			jQuery("#selectedDocFolID").val() > 0
		){
			customParams['folder_value'] = jQuery("#selectedDocFolID").val();
		}
			
		var params = this._super();
		if(search_value){
			jQuery.extend(params,customParams);
		}
		return params;
	},
    
    registerEvents: function() {
        this._super();
        
        this.registerFoldersClickEvent();
        this.registerAddFolderEvent();
        this.registerFoldersSearchEvent();
        this.registerFolderEditEvent();
        this.registerDeleteFolderEvent();
        this.registerFiltersClickEvent();
        
		this.createTreeFolder();
		
		//To make folder non-deletable if a document is uploaded
		app.event.on('post.documents.save', function(event, data){
			var folderid = data.folderid;
			var folder = jQuery('#folders-list').find('[data-folder-id="'+folderid+'"]').filter('.deleteFolder');
			if(folder.length) {
				folder.attr('data-deletable', '0');
			}
		})
		
		/*var filters = jQuery('.module-filters').not('.module-extensions');
        var scrollContainers = filters.find(".scrollContainer").not('.tags');
        jQuery.each(scrollContainers,function(key,scroll){
            var scroll = jQuery(scroll);
            //var listcontentHeight = 100;
            //scroll.css("height",listcontentHeight);
            scroll.perfectScrollbar('update');
        });
        
		var filters = jQuery('.module-filters').not('.module-extensions');
        var scrollContainers = filters.find(".scrollContainer.scrollContainerFolder");
        jQuery.each(scrollContainers,function(key,scroll){
            var scroll = jQuery(scroll);
            //var listcontentHeight = 150;
            //scroll.css("height",listcontentHeight);
            scroll.perfectScrollbar('update');
        });*/
        
        
		
    },
    
    createTreeFolder : function() {
		
		var thisInstance = this;
    	var moduleName = app.getModuleName();
    	
    	$(function () {
			
    		jQuery('#tree_folder')
				.jstree({
					'core' : {
						
						'check_callback' : function(o, n, p, i, m) {
							
							if (o == "delete_node" ) {
								
	    						var child = $("#tree_folder").jstree().get_node(n).children;
	    						
	    						if(child.length > 0){
	    							
    								app.helper.showErrorNotification({title: 'Error', message: 'Enable to Delete this Folder.'});
    								return false;
	    						}else{
	    							return true;
	    						}
	    						
				            }
				            
							if(m && m.dnd && m.pos !== 'i') { return false; }
							if(o === "move_node" || o === "copy_node") {
								if(this.get_node(n).parent === this.get_node(p).id) { return false; }
							}
							return true;
							
						},
						'themes' : {
							
							'responsive' : false,
							'variant' : 'small',
							
						}
					},
					'sort' : function(a, b) {
						
						return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
						
					},
					'contextmenu' : {
						
						'items' : function(node) {
							
							var tmp = $.jstree.defaults.contextmenu.items();
							delete tmp.create.action;
							
							tmp.ccp = false;
							tmp.remove = false;
							tmp.create.label = "New";
							tmp.create.submenu = {
									
								"create_folder" : {
									
									"separator_after"	: true,
									"label"				: "Folder",
									"action"			: function (data) {
										var inst = $.jstree.reference(data.reference),
											obj = inst.get_node(data.reference);
										inst.create_node(obj, { type : "default" }, "last", function (new_node) {
											setTimeout(function () { inst.edit(new_node); },0);
										});
									}
							
								},
								
								
							};
							if(this.get_type(node) === "file") {
								delete tmp.create;
							}
							return tmp;
						}
					},
					'types' : {
						
						'default' : { 'icon' : 'jstree-folder' },
						'file' : { 'valid_children' : [], 'icon' : 'jstree-file' }
						
					},
					'unique' : {
						
						'duplicate' : function (name, counter) {
							return name + ' ' + counter;
						}
					
					},
					
					"search": {
	                    "case_sensitive": false,
	                    "show_only_matches": true
	                },
	                
					'plugins' : ['state','sort','types','unique','search']//'contextmenu',
					
				})
				.on('delete_node.jstree', function (e, data) {
					
					var message = app.vtranslate('JS_LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE');
					var msg = app.helper.showPromptBox({'message' : message}).then(function(response) {
						$.get('?module='+moduleName+'&action=GetTreeData&mode=delete_node', { 'id' : data.node.id })
						.done(function (d) {
							
							if(d.result.success)
								app.helper.showSuccessNotification({message:app.vtranslate('Folder Deleted Successfully')});
							else
								$("#tree_folder").jstree().refresh();
						})
						.fail(function () {
							data.instance.refresh();
						});
					},
					function(error,err) {
					}); 
					console.log(msg);
				})
				.on('create_node.jstree', function (e, data) {
					
					$.get('?module='+moduleName+'&action=GetTreeData&mode=create_node', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text })
						.done(function (d) {
							data.instance.set_id(data.node, d.result.id);
							if(d.result.id)
								app.helper.showSuccessNotification({message:app.vtranslate('Folder Created Successfully')});
							else if(d.result.message)
								app.helper.showErrorNotification({message: d.result.message});
						})
						.fail(function () {
							data.instance.refresh();
						});
					
				})
				.on('rename_node.jstree', function (e, data) {
					
					$.get('?module='+moduleName+'&action=GetTreeData&mode=rename_node', { 'id' : data.node.id, 'text' : data.text })
					.done(function (d) {
							
						var id = d.result.id;
						
						if(id){
							app.helper.showSuccessNotification({message:app.vtranslate('Folder Name Changed Successfully')});
							data.instance.set_id(data.node, id);
						}else{
							var msg = d.result.message;
							if(!msg)
								var msg = 'Unable to rename Your Folder.';
							
							app.helper.showErrorNotification({title: 'Error', message: msg});
						}
						
					})
					.fail(function () {
						
						data.instance.refresh();
						
					});
				
				}).on('search.jstree', function (nodes, str, res) {
					
				    if (str.nodes.length===0) {
				    	
				    	var target = nodes.currentTarget;
				    	
				        $('#tree_folder').jstree(true).hide_all();

				    	$("<div class='text-center'><strong>No Data Found</strong></div>").appendTo(target).css('color','#8b0000');
				    	
				    }
				    
				});
			
    		});
    },
    
    
    exportZip : function(url) {
        var listInstance = this;
        var validationResult = listInstance.checkListRecordSelected();
		if(!validationResult){
			
			var postData = listInstance.getListSelectAllParams(true);

            if(app.getModuleName() === 'Documents'){
                var defaultparams = listInstance.getDefaultParams();
                postData['folder_id'] = defaultparams['folder_id'];
                postData['folder_value'] = defaultparams['folder_value'];
            }
			var params = {
				"url":url,
				"data" : postData
			};
            
            app.helper.showProgress();
            app.request.get(params).then(function(e,res) {
                app.helper.hideProgress();
                if(!e && res) {
                    app.helper.showModal(res, {
                        'cb' : function(modalContainer) {
                        	listInstance.registerExportFileModalEvents(modalContainer);
                        }
                    });
                }
            });
		} else{
			listInstance.noRecordSelectedAlert();
		}
    },
    
    registerExportFileModalEvents : function(container) {
        var self = this;
        var addFolderForm = jQuery('#exportFiles');
        addFolderForm.vtValidate({
            submitHandler: function(form) {
            	
                var formData = addFolderForm.serializeFormData();
                app.helper.showProgress();
                form.submit();
				app.helper.hideModal();
				app.helper.hideProgress();
					
            }
        });
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
	                        location.reload();
	                        app.helper.hideProgress();
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
    
    registerAddFolder: function(){
    	var thisInstance = this;
    	var params = {
			'mode' : 'create_folder',
			'src' : 'List',
    	};
    	params['module'] = app.getModuleName();
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
    
});