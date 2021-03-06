/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("NotificationsJS", {
	
	deleteNotification : function(recordId, ele){
		var params = {
			module : app.getModuleName(),
			record : recordId,
			action : 'delete'
		};
		app.request.post({data: params}).then(
			function(err, response) {
                if (!err) {
                	if(response.success){
                		$(ele).closest('.maindiv').remove();
                		app.helper.showSuccessNotification({message:app.vtranslate('Notification deleted successfully.')});
                	}else{
                		app.helper.showErrorNotification({title: 'Error', message: 'Unable to delete the notification plz try again later.'});
                	}
                }
			}
		);
	}
	
}, {
	
    addHeaderIcon: function () {
        var thisInstance = this;
        var headerLinksBig = jQuery('#menubar_quickCreate').closest('li');
        
		
		var headerIcon = '<li>' +
            '<div style="margin:15px;" class="notifications">' +
            '<span class="notification_bell module-buttons btn dropdown-toggle" aria-hidden="true" title="Notifications" style="border:0px!important;background:none!important;">' +
            '<i></i></span>' +
            
			'<input type="hidden" name="notification_count" value="">' +
            
			'<div id="notificationContainer" class="dropdown-menu" role="menu" style="margin-top: 35%!important;">' +
            
			'<div id="notificationTitle"><div class = "pull-left">Notifications</div><div class = "pull-right"><div class = "pull-left"><i class = "fa fa-envelope-open discardall" title = "Mark all as Read"></i></div><div class = "pull-left" style = "padding-top:1px;padding-left:15px;"><i class = "fa fa-list" onClick = "window.location.href=' + "'" + 'index.php?module=Notifications&view=List' + "';" + '"></i></div></div></div><div id="notificationsBody" class="notifications table-responsive"></div>';
            
	//		'<div id="notificationFooter"><a target="_blank" href="index.php?module=Notifications&view=List">See All</a></div></div></li>';
        
		
		
		headerLinksBig.before(headerIcon);

        var notificationContainer = jQuery('#headerNotification');
        var notificationList = jQuery('#notificationsBody');
        var notificationCounter = notificationContainer.find('.notification_count');
        
        if(app.getWebSocketUrl()){
        	
	    	window.WebSocket = window.WebSocket || window.MozWebSocket;
			
			var connection = new WebSocket('wss'+app.getWebSocketUrl());
		
			connection.onopen = function () {};
			connection.onerror = function (error) {};
			
			connection.onmessage = function (message) {
	
				var data = JSON.parse(message.data);
				
				var contactid = data.contactid;
				
				var fromportal = data.fromportal;
				
				var assigned_user_id = data.assigned_user_id;
				
				if(!fromportal && assigned_user_id == app.getUserId()){
					
					thisInstance.registerForGetNotifications();
					
					var style="<style>.notification_bell:after{ animation-name:ring !important;animation-duration:2s !important;animation-iteration-count: infinite !important;}</style>";
			        
					$('head').append(style);
			        
					var params = app.convertUrlToDataParams($(location).attr('href'));
			        
					if(app.getModuleName() == 'Contacts' && params.relatedModule == 'ModComments'){
			        	$('.related-tabs').find('li[data-module="ModComments"]').trigger('click');
			        }
					
				}
			}
        }
        
	    thisInstance.registerForGetNotifications();
	   
	    thisInstance.registerForClearNotifications();
	    
	    thisInstance.registerFunctionForComment();
	    
	    thisInstance.registerFunctionForAcceptInvitation();
	    
        notificationList.on('click', '.notification_link .notification_full_name', function (event) {
            var currentTarget = jQuery(event.currentTarget);
            var notificationLink = currentTarget.closest('.notification_link');
            if(notificationLink.data('module') != 'Events'){
	            $.when( clickToOk(this) ).done(function() {
		            window.location.href = notificationLink.data('href');
	            })
            }else if(notificationLink.data('module') == 'Events'){
            	window.location.href = notificationLink.data('href');
            }
        });
		
		$(document).on("click", ".notification_bell", function(e){
			$(".notifications").toggleClass("open");
		});
		
    },
    
    registerForGetNotifications : function(){
    	var thisInstance = this;
    	var notificationContainer = jQuery('#headerNotification');
        var notificationList = jQuery('#notificationsBody');
        var notificationCounter = notificationContainer.find('.notification_count');
       
        var params = {
    		'module': 'Notifications',
            'action': 'ActionAjax',
            'mode': 'getNotifications'
        };
        app.request.post({data: params}).then(
    		function(err, response) {
                if (!err) {
                	
                    //notificationList.empty();

                    var count = response.count;
                    
                    jQuery('[name="notification_count"]').val(count);
                    jQuery('.notification_bell').attr('data-before',count);

                    if (response.items.length == 0) {
                        notificationList.remove();
                        
						var html = '<div id="notificationsBody" class="notifications noNotifications table-responsive" style="height:100px;">'+
                        '<div class="emptyRecordsContent" style="display: inline-block;font-size: 16px;'+
                        'left: 50%;margin-left: -20%;position: absolute;width: 50%;top: 45%;">No Notifications found.</div>'+
                        '</div>';
                        
						jQuery('#notificationTitle').after(html);
                        
						jQuery('.discardall').hide();
                        
						jQuery('#notificationFooter').hide();
						
						return;
						
                    } else {
                    	
						jQuery('.emptyRecordsContent').remove();
                    
					}
					

                    var items = response.items;
                    var item = null;
                    var listItem = '';
                    var itemLength = items.length;
                    var limitDivide = itemLength - 2;
                    
                    for (var i = 0; i < itemLength; i++) {
                        item = items[i];
                       
                        var divider = '';
                        if (i > 0 ) {
                            divider = '<div class="divider">&nbsp;</div>';
                        }
                        var moduleIcon = '';
                        var reply = '';
                        var title = '';
                        var description = '';
                        var toolTipClass = '';
                        
                        if(item['relatedModule'] == "ModComments"){
                        	moduleIcon = '<i class="vicon-chat" title="comment" style="font-size: 1.5rem !important;"></i>';
                        	reply = '<i title="reply" data-commentid="'+item['relatedRecord']+'" class="vicon-replytoall pull-right replyComment" style="margin:0px 20px 0px 0px !important;font-size: 1rem !important;"></i>';
                        	toolTipClass ='';
                        }else if(item['relatedModule'] == "Documents"){
                        	moduleIcon = '<i class="vicon-documents" title="document" style="font-size: 1.5rem !important;"></i>';
                        	toolTipClass ='';
                        }else if(item['relatedModule'] == "Events"){
                        	moduleIcon = '<i class="vicon-calendar" title="Events" style="font-size: 1.5rem !important;"></i>';
                        	if(!item['accepted']){
                        		reply = '<i title="Accept" data-event="accept" data-eventid="'+item['rel_id']+'" class="fa fa-check-circle pull-right eventAction" style="margin:0px 20px 0px 0px !important;color:green;"></i>';
                        		reply += '<i title="Reject" data-event="reject" data-eventid="'+item['rel_id']+'" class="fa fa-times-circle pull-right eventAction" style="margin:0px 0px 0px 0px !important;color:red;"></i>';
                        	}
                        	toolTipClass ='';
                        }else if(item['type'] == "Follow Record"){
                        	moduleIcon = '<i title="Follow" class="fa fa-star-o" style="font-size: 1.5rem !important;"></i>'
                    		toolTipClass = 'followUpClass';
                        }
                        if(!$('[data-notify-id="'+ item['id']+'"]').length){
	                        listItem =
	                            '<li data-notify-id="'+ item['id'] +'">' +
	                            '   <a class="notification_link '+toolTipClass+'" href="javascript:;" data-module="'+item['relatedModule']+'" data-href="' + item['link'] + '" data-id="' + item['id'] + '" data-rel_id="' + item['rel_id'] + '">' +
	                            '       <div class="notification-container">' +
	                            			reply ;
	                        	if(item['relatedModule'] == "Events")
	                        		listItem += '           <i class="fa fa-times-circle hide markAsRead" onclick="return clickToOk(this);" title="Acknowledge"> </i>' ;
	                        	else
	                        		listItem += '           <i class="fa fa-times-circle markAsRead" onclick="return clickToOk(this);" title="Acknowledge"> </i>' ;
	                        	listItem += '           <div class="notification_detail">' +
	                        	'<div class="pull-left" style="margin: 7px 4px 0px 2px !important;">'+
	                        		moduleIcon+'</div><div><span class="notification_full_name" title="'+item['title']+'"> ' +item['title']+ '&nbsp;</span>'+
	                        	' <span class="notification_description" title="'+item['description']+'">' +item['description'].substring(0, 25)+ '...&nbsp;</span>'+			
	                            '              <span class="notification_createdtime pull-right" title="' + item['createdDateTime'] + '">' + item['createdtime'] + '&nbsp;</span>' +
	                            '           </div> </div>' +
	                            '       </div>' +
	                            '   </a>' +
	                            divider +
	                            '</li>';
	                        jQuery('#notificationsBody').prepend(listItem);
                        }
                    }
                    thisInstance.registerEventForMouse();
                }else{
                	app.helper.showErrorNotification({title: 'Error', message: err.message});
                }
            }
        );
    },
    
    
    updateTotalCounter: function (notificationLink) {
        notificationLink.closest('li').addClass('hide');

        var currentTotal = jQuery('[name="notification_count"]').val();
        currentTotal = (currentTotal) ? parseInt(currentTotal) : 0;
        var total = currentTotal - 1;
        total = (total > 0) ? total : 0;
        jQuery('[name="notification_count"]').val(total);
        jQuery('.notification_bell').attr('data-before',total);

        if (total == 0) {
            notificationList.remove();
        }
    },

    registerFunctionForComment : function(){
    	var thisInstance = this;
    	$(document).on('click', '.replyComment', function(){
    		var commentId = $(this).data('commentid');
    		var rel_id = $(this).closest('.notification_link').data('rel_id');
    		var params = {
	    		'module': 'Notifications',
	            'view': 'AddComment',
	            'comment_id' : commentId,
	            'related_id' : rel_id
    		};
    		app.helper.showProgress();
    		
    		app.request.post({data:params}).then(
    			function(error, data) {
    				app.helper.hideProgress();
    				app.helper.showModal(data);
    				var form = jQuery('form#add_comment');
    				
    				var isFormExists = form.length;
    				if(isFormExists){
    					
    					var vtigerInstance = Vtiger_Index_Js.getInstance();
    					vtigerInstance.registerMultiUpload();
    					
    					thisInstance.saveComment(form);
    				}
    			}
    		);
    		
    		
    	});
    },
    
    saveComment : function (form){
		
		var thisInstance = this;
		form.on("click","button[name='saveButton']",function(e){
			e.preventDefault();
			var rules = {};
			rules["commentcontent"] = {'required' : true};
			var params = {
				rules : rules,
				ignore: "input[type='file'].multi",
				submitHandler: function(form) {
					// to Prevent submit if already submitted
					jQuery(form).find("button[name='saveButton']").attr("disabled","disabled");
					if(this.numberOfInvalids() > 0) {
						return false;
					}
					
					if(jQuery(form).find('[name="is_private"]').prop('checked'))
						var is_private = 'on';
					else	
						var is_private = 'off';
					
					var postData = {
						'module': 'ModComments',
						'action' : 'SaveAjax',
						'related_to': jQuery('#related_to').val(),
						'commentcontent': jQuery('#commentcontent').val(),
						'filename' : Vtiger_Index_Js.files,
						'is_private' : is_private,
					};
					var formData = new FormData(jQuery(form)[0]); 
					jQuery.each(postData, function (key, value) {
						formData.append(key, value);
					});
					var postData = { 
						'url': 'index.php', 
						'type': 'POST', 
						'data': formData, 
						processData: false, 
						contentType: false 
					};
					
					app.request.post(postData).then(
						function(error,data) {
							if(error === null){
								
								app.helper.hideModal();
                                app.helper.showSuccessNotification({message:app.vtranslate('Comment saved successfully')});
                                //location.reload();
							} else {
								app.event.trigger('post.save.failed', error);
								jQuery(form).find("button[name='saveButton']").removeAttr('disabled');
							}
						}
					);
				}
			};
			validateAndSubmitForm(form,params);
		 });
		
	},

    registerForClearNotifications : function(){
    	var thisInstance = this;
    	$(document).on('click', '.discardall', function(){
    		var params = {
	    		'module': 'Notifications',
	            'action': 'ActionAjax',
	            'mode': 'discardAllNotifications'
    		};
	        app.request.post({data: params}).then(
	    		function(err, response) {
	                if (!err) {
	                	if(response.success){
	                		thisInstance.registerForGetNotifications();
	                		app.helper.showSuccessNotification({message:'Successfully clear all notifications.'},{offset:{y: 450}});
	                	}
	                }
	    		}
	        );
    	});
    },
    
    registerEvents: function () {
    	
        var thisInstance = this;
        thisInstance.addHeaderIcon();
       
    },
    
    registerFunctionForAcceptInvitation : function(){
    	var thisInstance = this;
    	$(document).on('click', '.eventAction', function(){
    		
    		var ele = $(this).closest('.notification_link');
    		var eventId = $(this).data('eventid');
    		var notify_id = ele.data('id');
    		var eventStatus = $(this).data('event');

    		if($(this).hasClass('noificationlist')){
    			var eventId = $(this).data('eventid');
        		var notify_id = $(this).data('notifyid');
        		var eventStatus = $(this).data('event');
        		var ele = $('.notification_link[data-id="'+notify_id+'"]');
    		}
    		
    		var params = {
	    		'module': 'Notifications',
	            'action': 'ActionAjax',
	            'mode' : 'eventInvitations',
	            'event_id' : eventId,
	            'record' : notify_id,
	            'status' : eventStatus
    		};
    		app.helper.showProgress();
    		
    		app.request.post({data:params}).then(
    			function(error, data) {
    				app.helper.hideProgress();
    				if(data.success)
    					ele.find('.markAsRead').trigger('click');
    				if(app.getModuleName() == 'Notifications' && app.getViewName() == 'List')
    					location.reload();
    			}
    		);
    		
    		
    	});
    },
    
    registerEventForMouse :function(){
		
		var thisInstance = this;
		
		$(document).find('#notificationsBody [data-module="Events"]').each(function(){
			
			var element = $(this);
			element.popover('destroy');
			
			var value = element.data('module');
			var recordId = element.data('rel_id');
			
			if (value !== '' && typeof value !== "undefined" && value == "Events") {
				var params= {
					'source_module': value,
					'record':recordId
				};
				thisInstance.getListRecordDetails(params).then(
					function(response){
						var data = response['data'];
						
    					var template = jQuery('<div class="popover" role="tooltip" style = "position:fixed;"><div class="arrow"></div><div class="popover-content" style="padding: 0px 2px;"></div></div>');
    					var container = '<div class="row"><h5 class="col-md-12"> <strong>Event Details :</strong> </h5>';
    					
    					container += '</div><div class="row"><div class="col-md-12"><table class="table table-striped" style="margin-bottom:0px;font-size: 0.7rem;"><tr><td><strong>Subject</strong></td><td>'+data.subject+'</td></tr>'+
    					'<tr><td><strong>Contact Name</strong></td><td>'+data.contactsLink+'</td></tr><tr><td><strong>Related To</strong></td><td>'+data.parentLink+'</td></tr>'+
    					'<tr><td><strong>Start Date & Time</strong></td><td>'+data.startDate+'</td></tr><tr><td><strong>End Date & Time</strong></td><td>'+data.endDate+'</td></tr></table></div></div>';
						element.popover({
							'content' : container,
							'width'	:'80',
							'html' : true,
							'placement' : 'left',
							'trigger' : 'hover',
							'template' : template,
							'container' : element,
						});
						
					},
					function(error, err){

					}
				);
			}   
		});
		
		$('.followUpClass').each(function(){
			
			var element = $(this);
			element.popover('destroy');
			
			var value = element.data('module');
			var recordId = element.data('id');
			
			
			if (value !== '' && typeof value !== "undefined") {
				var params= {
					'source_module': value,
					'record':recordId
				};
				
				thisInstance.getListRecordDetails(params).then(
					function(response){
						var data = response['data'];
						
    					var template = jQuery('<div class="popover" role="tooltip" style = "position:fixed;"><div class="arrow"></div><div class="popover-content" style="padding: 0px 2px;"></div></div>');
    					var container = '<div class="row">';
    					
    					container += '</div><div class="row"><div class="col-md-12"><div class="table-responsive" style="max-height:250px;">'+data.description+'</div></div></div>';
						element.popover({
							'content' : container,
							'width'	:'80',
							'html' : true,
							'placement' : 'left',
							'trigger' : 'hover',
							'template' : template,
							'container' : element,
						});
						
					},
					function(error, err){

					}
				);
			}   
		});
		
	},
        	
	getListRecordDetails : function(params) {
		var aDeferred = jQuery.Deferred();
		var mode = '';
		if(params['source_module'] == 'Events')
			mode = 'getEventData';
		else if(params['source_module'])
			mode = 'getNotificationData';
		
		var url = "index.php?module=Notifications&action=ActionAjax&record="+params['record']+"&source_module="+params['source_module']+"&mode="+mode;
		app.request.get({'url':url}).then(
			function(error, data){
				if(error == null) {
					aDeferred.resolve(data);
				} else {
					//aDeferred.reject(data['message']);
				}
			},
			function(error){
				aDeferred.reject();
			}
			)
		return aDeferred.promise();
	},
	
	registerEventForLoadMore : function(){
		var thisInstance = this;
		jQuery(document).on('click', '.loadMoreNotifications', function(){
			var listViewContainer = jQuery('#notificationListContainer');
			
			var nextPageExist = listViewContainer.find('#nextPageExist').val();
			var pageNumber = listViewContainer.find('#pageNumber').val();
			var viewid = listViewContainer.find('#cvid').val();
			var nextPageNumber = parseInt(parseFloat(pageNumber)) + 1;
			
			var type = listViewContainer.find('.tab-item.active').data('id');
			
			if ( nextPageExist) {
				var params = {
					'module' : app.getModuleName(),
					'action' : 'ActionAjax',
					'mode'	 : 'loadMoreNotifications',
					'page'	 : nextPageNumber,
					'viewid' : viewid,
					'type'	 : type
				};
				params.list_headers = listViewContainer.find('[name="list_headers"]').val();
				app.helper.showProgress();
				app.request.post({data: params}).then(
					function(err, response) {
		                if (!err) {
		                	app.helper.hideProgress();
		                	if(response.success){
		                		listViewContainer.find('.mainList').find('.'+type).append(response.data);
		                		if(response.nextpage){
		                			listViewContainer.find('#pageNumber').val(nextPageNumber);
		                		}else{
		                			listViewContainer.find('.loadMoreNotifications').prop('disabled', true);
		                		} 
		                		thisInstance.registerEventForMouse();
		                	}
		                		
		                }
					}
				);
			}
		});
		
		jQuery(document).on('click', '#notificationListContainer .tab-item', function(){
			var listViewContainer = jQuery('#notificationListContainer');
			
			var pageNumber = listViewContainer.find('#pageNumber').val();
			var viewid = listViewContainer.find('#cvid').val();
			var nextPageNumber = parseInt(parseFloat(1));
			
			var type = listViewContainer.find('.tab-item.active').data('id');
			
			if(!listViewContainer.find('.mainList').find('.'+type+' .notification_link').length && !listViewContainer.find('.mainList').find('.'+type+' .emptyRecordsDiv').length ){
				var params = {
					'module' : app.getModuleName(),
					'action' : 'ActionAjax',
					'mode'	 : 'loadMoreNotifications',
					'page'	 : nextPageNumber,
					'viewid' : viewid,
					'type'	 : type
				};
				params.list_headers = listViewContainer.find('[name="list_headers"]').val();
				app.helper.showProgress();
				app.request.post({data: params}).then(
					function(err, response) {
		                if (!err) {
		                	app.helper.hideProgress();
		                	if(response.success){
		                		listViewContainer.find('.mainList').find('.'+type).append(response.data);
		                		if(response.nextpage){
		                			listViewContainer.find('#pageNumber').val(nextPageNumber);
		                			listViewContainer.find('.loadMoreNotifications').prop('disabled', false);
		                		}else{
		                			listViewContainer.find('.loadMoreNotifications').prop('disabled', true);
		                		} 
		                		thisInstance.registerEventForMouse();
		                	}
		                		
		                }
					}
				);
			}
		})
	},
	
	registerEventForShowMore : function(){
		
		jQuery(document).on('click', '.toggleNotification', function(){
			
			var toggleElement = jQuery('<div><a class="pull-right toggleNotification" style="color: blue;"><small></small></a><div>');
			var ele = $(this).closest('.notification_description');
			
			var fullComment = vtUtils.linkifyStr(ele.data('fullcomment'));
			
			if ($(this).hasClass('showMore')) {
				toggleElement.find('small').text(ele.data('less'));
				ele.html(fullComment+toggleElement.clone().html());
			} else {
				var maxLength = 150;
				toggleElement.find('small').text(ele.data('more'));
				toggleElement.find('.toggleComment').addClass('showMore');
				ele.html(vtUtils.htmlSubstring(fullComment, maxLength)+"..."+toggleElement.clone().html());
			}
			
		});
		
	},
	
	registerEventForOpenNotifications : function(){
		var instance = this;
		jQuery(document).on('click', '.notification_bell', function(){
			var params = {
					'module': 'Notifications',
					'action': 'ActionAjax',
					'mode': 'markNotificationRead',
				};
				
				app.request.post({data: params}).then(
					function(err, response) {
						if (!err) {
							 jQuery('.notification_bell').attr('data-before',0);
						} else {
							app.helper.showErrorNotification({title: 'Error', message: err.message});
						}
						
					}
				);
		});
	}
    
});

jQuery(document).ready(function() {
	var instance = new NotificationsJS();
    setTimeout(function () {
    	//setInterval(function(){
	        instance.registerEvents();
    	//}, 5000);
    }, 1000);
    instance.registerEventForLoadMore();
    instance.registerEventForShowMore();
    instance.registerEventForOpenNotifications();
});

function clickToOk(btnOK){
	
	var currentTarget = jQuery(btnOK);
	var notificationLink = currentTarget.closest('.notification_link');
	var id = notificationLink.data('id');
	
	var params = {
		'module': 'Notifications',
		'action': 'Delete',
		'record': id
	};
	var instance = new NotificationsJS();
	
	app.request.post({data: params}).then(
		
		function(err, response) {
			
			currentTarget.closest('.notification_link').parent().fadeOut(300, function(){ 
				$(this).remove();
				
				if( !($('#notificationsBody li').length >= 1) ){
					$("#notificationsBody").html('<div class="emptyRecordsContent" style="display: inline-block;font-size: 16px;left: 50%;margin-left: -20%;position: absolute;width: 50%;top: 45%;">No Notifications found.</div>');
					$("#notificationsBody").css("height",'100px');
					$("#notificationContainer").find(".discardall").hide();
					$("#notificationContainer").find(".fa-list").hide();
					
				}
			
			});
			
		}
		
	);

	return false;
}
