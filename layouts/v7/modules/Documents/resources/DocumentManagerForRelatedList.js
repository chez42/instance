
jQuery.Class("Document_ManagerForRelatedList_Js",{},{
        
		createTreeFolderPopup : function() {
			
			var thisInstance = this;
        	
        	var record = app.getRecordId();
            
        	$(function () {
    			
        		jQuery('#tree_folder')
    				.jstree({
    					'core' : {
    						
    						'data' : {
    							
    							'url' : 'index.php?module=Documents&action=GetTreeData&record='+record+'&mode=get_data',
    							"dataType" : "json"
    								
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
    							delete tmp.create.action;
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
    								"create_file" : {
    									
    									"label"				: "File",
    									"action"			: function (data) {
    										var inst = $.jstree.reference(data.reference),
    											obj = inst.get_node(data.reference);
    										inst.create_node(obj, { type : "file" }, "last", function (new_node) {
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
    					
    					'plugins' : ['state','dnd','sort','types','unique']//,'contextmenu'
    					
    				})
    				.on('delete_node.jstree', function (e, data) {
    					
    					$.get('?operation=delete_node', { 'id' : data.node.id })
    						.fail(function () {
    							data.instance.refresh();
    						});
    					
    				})
    				.on('create_node.jstree', function (e, data) {
    					
    					$.get('?operation=create_node', { 'type' : data.node.type, 'id' : data.node.parent, 'text' : data.node.text })
    						.done(function (d) {
    							data.instance.set_id(data.node, d.id);
    						})
    						.fail(function () {
    							data.instance.refresh();
    						});
    					
    				})
    				.on('rename_node.jstree', function (e, data) {
    					
    					$.get('?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
    						.done(function (d) {
    							data.instance.set_id(data.node, d.id);
    						})
    						.fail(function () {
    							data.instance.refresh();
    						});
    					
    				})
    				.on('move_node.jstree', function (e, data) {
    					
    					$.get('?operation=move_node', { 'id' : data.node.id, 'parent' : data.parent })
    						.done(function (d) {
    							
    							data.instance.refresh();
    						})
    						.fail(function () {
    							data.instance.refresh();
    						});
    					
    				})
    				.on('copy_node.jstree', function (e, data) {
    					
    					$.get('?operation=copy_node', { 'id' : data.original.id, 'parent' : data.parent })
    						.done(function (d) {
    							//data.instance.load_node(data.parent);
    							data.instance.refresh();
    						})
    						.fail(function () {
    							data.instance.refresh();
    						});
    					
    				})
    				
    				.on('changed.jstree', function (e, data) {
    					
    					if(data && data.selected && data.selected.length) {
    						
    						$.get('?operation=get_content&id=' + data.selected.join(':'), function (d) {
    							if(d && typeof d.type !== 'undefined') {
    								$('#data .content').hide();
    								switch(d.type) {
    									case 'text':
    									case 'txt':
    									case 'md':
    									case 'htaccess':
    									case 'log':
    									case 'sql':
    									case 'php':
    									case 'js':
    									case 'json':
    									case 'css':
    									case 'html':
    										$('#data .code').show();
    										$('#code').val(d.content);
    										break;
    									case 'png':
    									case 'jpg':
    									case 'jpeg':
    									case 'bmp':
    									case 'gif':
    										$('#data .image img').one('load', function () { $(this).css({'marginTop':'-' + $(this).height()/2 + 'px','marginLeft':'-' + $(this).width()/2 + 'px'}); }).attr('src',d.content);
    										$('#data .image').show();
    										break;
    									default:
    										$('#data .default').html(d.content).show();
    										break;
    								}
    							}
    						});
    						
    					}
    					else {
    						$('#data .content').hide();
    						$('#data .default').html('Select a file from the tree.').show();
    					}
    					
    				})
    				.on("open_node.jstree", function (e, data) {
    					
    				    $('#tree_folder li').each(function (index,value) {
    				        var node = $("#tree_folder").jstree().get_node(this.id);
    				        if(node.type == 'default'){
    				        	
    				        	if(node.children.length <1){
    				        		
    				        		$(this).addClass('jstree-hidden');
    				        	
    				        	}else{
    				        		
    				        		var children = node.children_d;
    				        		var child = children.toString();
    				        		var str = $('input[name="module_entity_id"]').val();
    				        		
    				        		if(child.indexOf(str+'x') == '-1'){
    				        			$(this).addClass('jstree-hidden');
    				        		}
    				        		
    				        	}
    				        }else if(node.type == 'file'){
    				        	
    				        	$(this).on('click',function(){

    				        		var href = $(this).find('a').attr('href');
	    				        	 
		   	    				     window.open(href);
		   	    				     
    				        	});
    				        	
    				        }
    				          
    				    });
    				   
    				})
    				.on("ready.jstree",function(){
    					
    					$(this).jstree("open_node", "ul > li:first");
    				    $('#tree_folder li').each(function (index,value) {
    				        var node = $("#tree_folder").jstree().get_node(this.id);
    				        if(node.type == 'default'){
    				        	
    				        	if(node.children.length <1){
    				        		
    				        		$(this).addClass('jstree-hidden');
    				        	
    				        	}else{
    				        		
    				        		var children = node.children_d;
    				        		var child = children.toString();
    				        		var str = $('input[name="module_entity_id"]').val();
    				        		
    				        		if(child.indexOf(str+'x') == '-1'){
    				        			$(this).addClass('jstree-hidden');
    				        		}
    				        		
    				        	}
    				        }
    				          
    				    });
    				    $(this).jstree().close_all();
    				})
    				.bind("hover_node.jstree", function (e, data){
    					
    					var node = data.node.type;

    					if(node == 'file'){
    				        	
				        	/*$(this).find('a').tooltip({
			    			    content: "check",
			    			   // show: false,
			    			    //hide: false
			    			});*/
    						
    					}
            		    
            		});
    			
        		});
        },
        
       
         registerEvents: function() {
            this.createTreeFolderPopup();
         }
	}
);

jQuery(document).ready(function(){
   
	var dmjInstance = new Document_ManagerForRelatedList_Js();
    dmjInstance.registerEvents(); 
    
  
    app.listenPostAjaxReady(function() {
    		dmjInstance.registerEvents(); 
    });
      
});

