Vtiger.Class("OwnCloud_Js",{
	
	triggerGetOwnCloudFolders : function(massActionUrl){
	
		var thisInstance = this;
			
		var listInstance = window.app.controller();
		
		var listSelectParams = listInstance.getListSelectAllParams(true);
		
		var selectedRecordCount = listInstance.getSelectedRecordCount();
		
		if (selectedRecordCount > 15) {
			app.helper.showErrorNotification({message: app.vtranslate('Only 15 Records Selected at a time.')});
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
			    			
			        		jQuery('#tree_folder')
			    				.jstree({
			    					'core' : {
			    						
			    						'data' : {
			    							
											'url' : 'index.php?module=OwnCloud&view=OwnCloudFolder&mode=getOwnCloudFolders',
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
			    							//'stripes' : true
			    							
			    						}
			    					},
			    					'sort' : function(a, b) {
			    						
			    						return this.get_type(a) === this.get_type(b) ? (this.get_text(a) > this.get_text(b) ? 1 : -1) : (this.get_type(a) >= this.get_type(b) ? 1 : -1);
			    						
			    					},
			    					'contextmenu' : {
			    						
			    						'items' : function(node) {
			    							
			    							var tmp = $.jstree.defaults.contextmenu.items();
//				    							delete tmp.create.action;
//				    							tmp.create.label = "New";
//				    							tmp.create.submenu = {
//				    								"create_folder" : {
//				    									"separator_after"	: true,
//				    									"label"				: "Folder",
//				    									"action"			: function (data) {
//				    										var inst = $.jstree.reference(data.reference),
//				    											obj = inst.get_node(data.reference);
//				    										inst.create_node(obj, { type : "default" }, "last", function (new_node) {
//				    											setTimeout(function () { inst.edit(new_node); },0);
//				    										});
//				    									}
//				    								},
//				    								 
//				    							};
//				    							if(this.get_type(node) === "file") {
//				    								delete tmp.create;
//				    							}
			    							delete tmp.remove;
			    							delete tmp.rename;
			    							delete tmp.ccp;
			    							console.log(tmp)
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
			    					
			    					'plugins' : ['contextmenu','state','dnd','sort','types','unique']//,'contextmenu'
			    					
			    				})
			    				.on('rename_node.jstree', function (e, data) {
				
			    					var docIds = $('[name="selected_ids"]').val();
			    					
			    					$.get('index.php?module=OwnCloud&view=OwnCloudFolder&mode=create_node', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text, 'docid' : docIds }).done(function (d) {
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
								
			        		});
						}
						
						var params = {};
						params.cb = callback
						app.helper.showModal(data, params);
//						
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
		
	},
	

});

jQuery(document).ready(function(){
	
	obj = new OwnCloud_Js();
	obj.registerEvents();
	
});