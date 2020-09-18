Vtiger.Class("OwnCloud_Js",{
	
	triggerGetOwnCloudFolders : function(massActionUrl){
	
		var thisInstance = this;
			
		var listInstance = window.app.controller();
		
		var listSelectParams = listInstance.getListSelectAllParams(true);
		
		var selectedRecordCount = listInstance.getSelectedRecordCount();
		
		if (selectedRecordCount > 20) {
			app.helper.showErrorNotification({message: app.vtranslate('Max 20 Documents allowed at a time')});
			return;
		}
		
		if (listSelectParams) {
			
			var postData = listInstance.getDefaultParams();
			delete postData.module;
			delete postData.view;
			delete postData.parent;
			var data = app.convertUrlToDataParams(massActionUrl);
			postData = jQuery.extend(postData, data);
			postData = jQuery.extend(postData, listSelectParams);
			postData['srcmodule'] = app.getModuleName();
			
			app.helper.showProgress();
			
			app.request.get({'data': postData}).then(
				
				function (err, data) {
					
					app.helper.hideProgress();
					
					if (data) {
						
						var callback = function (data) {
							$(function () {
			    			
								jQuery('#tree_folder').jstree({
			    					'core' : {
			    						'data' : {
			    							'url' : 'index.php?module=OwnCloud&view=GetFolders&mode=getOwnCloudFolders',
			    							"dataType" : "json",
			    							"data" : function (node) {
			    								return { 'id' : node.id };
			    						     }
			    						},
			    						'check_callback' : function(o, n, p, i, m) {
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
			    							delete tmp.remove;
			    							delete tmp.rename;
			    							delete tmp.ccp;
			    							
			    							return tmp;
			    						}
			    					},
			    					
			    					'types' : {
			    						'default' : { 'icon' : 'jstree-folder' },
			    						'file' : { 'valid_children' : [], 'icon' : 'jstree-file' }
			    					},
			    					
			    					/*'unique' : {
			    						'duplicate' : function (name, counter) {
			    							return name + ' ' + counter;
			    						}
			    					},*/
			    					
			    					//'plugins' : ['contextmenu','state','dnd','sort','types','unique']//,'contextmenu'
			    					
			    					'plugins' : ['contextmenu', 'state', 'sort', 'types']
			    					
			    				}).on('rename_node.jstree', function (e, data) {
				
			    					var params = {};
			    					
			    					params.module = 'OwnCloud';
			    					
			    					params.action = 'ManageFolder';
			    					
			    					params.mode = 'CreateFolder';
			    					
			    					params.type = data.node.type;
			    					
			    					params.id = data.node.parent;
			    					
			    					params.text = data.node.text;
			    					
			    					app.request.post({data:params}).then(function(err,d){
			    						
			    						data.instance.set_id(data.node, d.id);
			    						
			    						/*data.instance.refresh();
			    						if(d.result.id)
											app.helper.showSuccessNotification({message:app.vtranslate('Folder Created Successfully')});
										else if(d.result.message)
											app.helper.showErrorNotification({message: d.result.message});*/
			    						
			    					});
			    				}).on('select_node.jstree', function(e, data){
									$("#own_cloud_folder").val(data.node.id);
								});
							});
			        	}
						
						var params = {};
						params.cb = callback
						app.helper.showModal(data, params);
						app.helper.hideProgress();
					}
				}
			);
			
		} else {
			listInstance.noRecordSelectedAlert();
		}
		
	},
	
},{
    
	registerEvents : function(){
		var self = this;
		if(app.getModuleName() == 'Users' && app.getViewName() == 'PreferenceDetail'){
			var buttonContainer = jQuery('.detailViewContainer');
            
			buttonContainer.find('ul.dropdown-menu').append('<li class=  "ownCloudCreds"><a href = "#">OwnCloud Credentials</a></li>');
			
			self.registerEventForCredentialsButton();
		}
		
		self.registerEventForSyncDocuments();
		
	},
	
	registerEventForSyncDocuments  : function(){
		
		$(document).on('click', '#syncDocuments', function(){
			
			var listInstance = window.app.controller();
			
			var listSelectParams = listInstance.getListSelectAllParams(true);
			
			var params = {};
			
			params.module = 'OwnCloud';
			
			params.action = 'SyncDocuments';
			
			params.own_cloud_folder = $("#own_cloud_folder").val();
			
			listSelectParams = jQuery.extend(listSelectParams, params);
			
			app.helper.showProgress();
			
			app.request.post({data:listSelectParams}).then(function(err,data){
				if(err === null) {
					app.helper.showSuccessNotification({message:app.vtranslate('Documents Sync Successfully')});
					app.helper.hideProgress();
				}
			});
			
			
		});
	},
	
	
	registerEventForCredentialsButton  : function(){
		var thisInstance = this;
		$(document).on('click', '.ownCloudCreds', function(){
			var btn = this;
			var params = {};
			params.module = 'OwnCloud';
			params.view = 'Settings';
			params.submode = 'userPrefrenceSettings';
			params.record = app.getRecordId();
			app.helper.showProgress();
			app.request.post({data:params}).then(function(err,data){
				if(err === null) {
					app.helper.hideProgress();
					app.helper.showModal(data, {
                        'cb' : function(modalContainer) {
                        	thisInstance.registerSaveOwnCloudCredentials(modalContainer);
                        }
                    });
				}
			});
			
		});
	},
	
	registerSaveOwnCloudCredentials: function(modalContainer){
		var self = this;
		modalContainer.find('[name="saveButton"]').on('click',function(){
		
			jQuery('#updatedetails').vtValidate({
				
				submitHandler: function (form) {
					var domForm = jQuery(form);
					var formData = jQuery(form).serializeFormData();
	
					var formData = new FormData(domForm[0]);
					var params = {
						url: "index.php",
						type: "POST",
						data: formData,
						processData: false,
						contentType: false
					};
					app.helper.showProgress();
					app.request.post(params).then(function (err, data) {
						app.helper.hideProgress();
						if (!err) {
							app.helper.hideModal();
							app.helper.showSuccessNotification({message:'Owncloud Credentials Saved Successfully!'});
						}
					});
					return false;
				}
			});
		});
    	
    },
	

});

jQuery(document).ready(function(){
	
	obj = new OwnCloud_Js();
	obj.registerEvents();
	
});