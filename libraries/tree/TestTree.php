<?php
?>
<script src="jquery.min.js"></script>
<!-- <script src="tree.jquery.js"></script>
<link rel="stylesheet" href="jqtree.css"> 
<script src="../jsTree/dist/jstree.min.js"></script>
<link rel="stylesheet" href="../jsTree/dist/themes/default/style.min.css"> -->
<link type='text/css' rel='stylesheet' href='../layouts/v7/lib/jquery/jquery-ui-1.11.3.custom/jquery-ui.css'>
<script type="text/javascript" src="../layouts/v7/lib/jquery/jquery-ui-1.11.3.custom/jquery-ui.js"></script>
<script src="jstree.min.js"></script>
<link rel="stylesheet" href="style.min.css">
<div id="tree1"></div>

<script type="text/javascript">

/*$('#tree1').jstree({
	  "core" : {
	    "animation" : 0,
	    "check_callback" : true,
	    "themes" : { "stripes" : true },
	    'data' : {
	      'url' : function (node) {
	        return node.id === '#' ?
	          'ajax_demo_roots.json' : 'ajax_demo_children.json';
	      },
	      'data' : function (node) {
	        return { 'id' : node.id };
	      }
	    }
	  },
	  "types" : {
	    "#" : {
	      "max_children" : 1,
	      "max_depth" : 4,
	      "valid_children" : ["root"]
	    },
	    "root" : {
	      "icon" : "/static/3.3.5/assets/images/tree_icon.png",
	      "valid_children" : ["default"]
	    },
	    "default" : {
	      "valid_children" : ["default","file"]
	    },
	    "file" : {
	      "icon" : "glyphicon glyphicon-file",
	      "valid_children" : []
	    }
	  },
	  "plugins" : [
	    "contextmenu", "dnd", "search",
	    "state", "types", "wholerow"
	  ]
	});
/*var data = [
    {
        name: 'node1', id: 1,
        children: [
            { name: 'child1', id: 2 },
            { name: 'child2', id: 3 }
        ]
    },
    {
        name: 'node2', id: 4,
        children: [
            { name: 'child3', id: 5 ,
            	children: [
                    { name: 'child3', id: 5 ,
                    	is_empty_folder: true

                     }
                ]
             }
        ]
    },
    {
        name: 'node3', id: 2,
        is_empty_folder: true
        
    }
];
$('#tree1').tree({
    data: data,
    onCreateLi: function(node, $li) {
        if (node.is_empty_folder) {
            
            $('#tree1').tree('removeNode', node);
            //$li.find('.jqtree-title').before('<span class="folder-icon"></span>');
        }
    },
    autoOpen: false,
    dragAndDrop: false,
    closedIcon: '+',
    openedIcon: '-'
});

var node = $('#tree1').tree('getNodeById', 2);
//$('#tree1').tree('removeNode', node);
*/
</script>
<script>/*{
'url' : '?operation=get_node',
'data' : function (node) {
	return { 'id' : node.id };
}*/
    	var data = [
        	{
        		"text" : "Root node",
        		"icon" : "jstree-folder",
        		//"state" : { "opened" : true },
        		"id" : '1',
        		"children" : [
        			{
        				"text" : "Child node 1",
        				//"state" : { "selected" : true },
        				"icon" : "jstree-file",
        				"type" : "file",
        				"id" : '2',
        			},
        			{ 	"text" : "Child node 2", 
        				"state" : { "disabled" : true },
        				"icon" : "jstree-folder",
        				"id" : '3',
        			 }
        		]
        	},{
                "text": 'node1', 
                "icon" : "jstree-folder",
                "id" : '4',
                "children": [
                    { "text": 'child1',  "state" : 2 ,"icon" : "jstree-folder","id" : '5',},
                    { 
                        "text": 'child2',  
                        "state" : {"hidden" : false},
                        "icon" : "jstree-file",
                        "type" : "file",
                        "id" : '6', 
                    }
                ]
            },{
        		"text" : 'Test',
        		"icon" : "jstree-folder",
        		"id" : '7',
        		"children" : [
            		{
            			"text": 'child1',  "state" : 2 ,"icon" : "jstree-folder","id" : '8',
            		}
        		]
        	},
        	{
        		"text" : 'File',
        		"icon" : "jstree-file",
        		"id" : '9',
        		"type" : 'file'
        	}
        ];
        
		$(function () {
			/*$(window).resize(function () {
				var h = Math.max($(window).height() - 0, 420);
				$('#container, #data, #tree, #data .content').height(h).filter('.default').css('lineHeight', h + 'px');
			}).resize();*/

			$('#tree1')
				.jstree({
					'core' : {
						'data' : data,
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
							'stripes' : true
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
								}
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
					'plugins' : ['state','dnd','sort','types','contextmenu','unique']
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
							//data.instance.load_node(data.parent);
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
				.bind("before.jstree", function (e, data) {
					console.log('dara');
				   if(data.func === "delete_node") { 
				       var node = data.args[0][0]; 
				           if ($(node).find('li[rel="imgIR"]').length != 0){    // rel='imgIR' identifies images
				               alert("Folder must be empty to delete it.");
				               e.stopImmediatePropagation();
				               return false;
				       } 
				   }
				});

			$(".jstree-node").tooltip({
			    content: "<img src='http://placehold.it/50x50' />",
			    show: false,
			    hide: false
			});
		});/**/
		
		</script>