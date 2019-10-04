	
	Vtiger_List_Js("Task_List_Js", {
		
		
	},
	{

		registerMarkAsHeldEvent : function(){
	        
			var thisInstance = this;
			
			var listViewContentDiv = this.getListViewContentContainer();
			
			listViewContentDiv.on('click','.markAsHeld',function(e){
	            
				var elem = jQuery(e.currentTarget);
				
				var recordId = elem.closest('tr').data('id');
	            
				app.helper.showPromptBox({
	            	
	    			message: app.vtranslate('JS_CONFIRM_MARK_AS_HELD')
	    			
	    		}).then(function (e) {
	    			app.helper.showProgress();
	                var params = {
	                    module : "Task",
	                    action : "MarkAsCompleted",
	                    mode : "markAsCompleted",
	                    record : recordId
	                }
	              
	            	app.request.post({'data': params}).then(function (e, res) {
	    				jQuery('.vt-notification').remove();
	    				if (e) {
	    					app.event.trigger('post.save.failed', e);
	    				} else if (res && res['valid'] === true && res['markedascompleted'] === true) {
	    					var urlParams = {};
	    					thisInstance.loadListViewRecords(urlParams);
	    					thisInstance.clearList();
	    				} else {
	    					app.helper.showAlertNotification({
	    						'message': app.vtranslate('JS_FUTURE_EVENT_CANNOT_BE_MARKED_AS_HELD')
	    					});
	    				}
	    			});
	                
	            },
	            function(error, err){
	                return false;
				});
				
	            e.stopPropagation();
	        });
	    },
	    
	    
	    
	    registerEvents: function() {
	        this._super();
	        this.registerMarkAsHeldEvent();
	    }
	
	});
