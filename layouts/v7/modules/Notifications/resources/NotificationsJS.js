/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class("NotificationsJS", {}, {
	
    addHeaderIcon: function () {
        var thisInstance = this;
        var headerLinksBig = jQuery('#menubar_quickCreate').closest('li');
        var headerIcon = '<li>' +
            '<div style="margin:15px;" class="notifications">' +
            '<span class="notification_bell module-buttons btn dropdown-toggle" data-toggle="dropdown" aria-hidden="true" title="Notifications" style="border:0px!important;background:none!important;">' +
            '<i></i></span>' +
            '<input type="hidden" name="notification_count" value="">' +
            '<div id="notificationContainer" class="dropdown-menu" role="menu" style="margin-top: 35%!important;">' +
            '<div id="notificationTitle">Notifications <button class="btn discardall btn-default pull-right" style="margin-bottom:0px !important;">Discard All</button></div><div id="notificationsBody" class="notifications table-responsive"></div>'+
            '<div id="notificationFooter"><a target="_blank" href="index.php?module=Notifications&view=List">See All</a></div></div></li>';
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
			        
				}
			}
        }
        
	    thisInstance.registerForGetNotifications();
	   
	    thisInstance.registerForClearNotifications();
	    
	    thisInstance.registerFunctionForComment();
	    
        notificationList.on('click', '.notification_link .notification_full_name', function (event) {
            var currentTarget = jQuery(event.currentTarget);
            $.when( clickToOk(this) ).done(function() {
	            var notificationLink = currentTarget.closest('.notification_link');
	            window.location.href = notificationLink.data('href');
            })
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

                    if (count == 0) {
                        notificationList.remove();
                        var html = '<div id="notificationsBody" class="notifications table-responsive" style="height:100px;">'+
                        '<div class="emptyRecordsContent" style="display: inline-block;font-size: 16px;'+
                        'left: 50%;margin-left: -20%;position: absolute;width: 50%;top: 45%;">No Notifications found.</div>'+
                        '</div>';
                        jQuery('#notificationTitle').after(html);
                        jQuery('.discardall').hide();
                        return;
                    }

                    var items = response.items;
                    var item = null;
                    var listItem = '';
                    var itemLength = items.length;
                    var limitDivide = itemLength - 2;
                    console.log(itemLength);
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
                        if(item['relatedModule'] == "ModComments"){
                        	moduleIcon = '<i class="vicon-chat"></i>';
                        	reply = '<i title="reply" data-commentid="'+item['relatedRecord']+'" class="vicon-replytoall pull-right replyComment" style="margin:0px 20px 0px 0px !important;font-size: 1rem !important;"></i>';
                        }else if(item['relatedModule'] == "Documents"){
                        	moduleIcon = '<i class="vicon-documents" title="Documents"></i>';
                        }
                       
                        if(!$('[data-notify-id="'+ item['id']+'"]').length){
	                        listItem =
	                            '<li data-notify-id="'+ item['id'] +'">' +
	                            '   <a class="notification_link" href="javascript:;" data-href="' + item['link'] + '" data-id="' + item['id'] + '" data-rel_id="' + item['rel_id'] + '">' +
	                            '       <div class="notification-container">' +
	                            			reply +
	                            '           <i class="fa fa-check" onclick="return clickToOk(this);" title="Acknowledge"> </i>' +
	                            '           <div class="notification_detail">' +
	                            				item['description'] + 
	                            '              <span class="notification_createdtime pull-right" title="' + item['createdtime'] + '">' + item['createdtime'] + '&nbsp;</span>' +
	                            '           </div> </div>' +
	                            '       </div>' +
	                            '   </a>' +
	                            divider +
	                            '</li>';
	                        jQuery('#notificationsBody').prepend(listItem);
                        }
                    }
                    
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
		console.log(form);
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
       
    }
});

jQuery(document).ready(function() {
	
    setTimeout(function () {
    	//setInterval(function(){
	    	var instance = new NotificationsJS();
	        instance.registerEvents();
    	//}, 5000);
    }, 1000);
    
});

function clickToOk(btnOK){
	
	var currentTarget = jQuery(btnOK);
	var notificationLink = currentTarget.closest('.notification_link');
	var id = notificationLink.data('id');
	
	var params = {
		'module': 'Notifications',
		'action': 'ActionAjax',
		'mode': 'markNotificationRead',
		'record': id
	};
	var instance = new NotificationsJS();
	
	app.request.post({data: params}).then(
		function(err, response) {
			if (!err) {
				instance.updateTotalCounter(notificationLink);
                app.helper.showSuccessNotification({message:'Notification has been acknowledged'},{offset:{y: 450}});
			} else {
				app.helper.showErrorNotification({title: 'Error', message: err.message});
			}
			
		}
	);

	return false;
}
