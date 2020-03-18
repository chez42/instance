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
            '<div id="notificationTitle">Notifications</div><div id="notificationsBody" class="notifications table-responsive"></div>'+
            '<div id="notificationFooter"><a target="_blank" href="index.php?module=Notifications&view=List">See All</a></div></div></li>';
        headerLinksBig.before(headerIcon);

        var notificationContainer = jQuery('#headerNotification');
        var notificationList = jQuery('#notificationsBody');
        var notificationCounter = notificationContainer.find('.notification_count');
        
    	window.WebSocket = window.WebSocket || window.MozWebSocket;
		
		var connection = new WebSocket('ws://dev.omnisrv.com:3000');
	
		connection.onopen = function () {};
		connection.onerror = function (error) {};
		
		connection.onmessage = function (message) {

			var data = JSON.parse(message.data);
			
			var contactid = data.contactid;
			
			var fromportal = data.fromportal;
			
			var assigned_user_id = data.assigned_user_id;
			
			if(!fromportal && assigned_user_id == app.getUserId()){
				thisInstance.registerForGetNotifications();
			}
		}
       
	    thisInstance.registerForGetNotifications();
	   
        notificationList.on('click', '.notification_link .notification_full_name', function (event) {
            var currentTarget = jQuery(event.currentTarget);
            $.when( clickToOk(this) ).done(function() {
	            var notificationLink = currentTarget.closest('.notification_link');
	            window.location.href = notificationLink.data('href');
            })
        });

    },
    
    registerForGetNotifications : function(){
    	
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
                        return;
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
                        if(!$('[data-notify-id="'+ item['id']+'"]').length){
	                        listItem =
	                            '<li data-notify-id="'+ item['id'] +'">' +
	                            '   <a class="notification_link" href="javascript:;" data-href="' + item['link'] + '" data-id="' + item['id'] + '" data-rel_id="' + item['rel_id'] + '">' +
	                            '       <div class="notification-container">' +
	                            '           <i class="fa fa-check" onclick="return clickToOk(this);" title="Acknowledge"> </i>' +
	                            '           <div class="notification_detail">' +
	                            '               <span class="notification_full_name" title="' + item['full_name'] + '">' + item['full_name'] + '&nbsp;</span>' +
	                            '               <span class="notification_description" title="' + item['description'] + '">' + item['description'] + '&nbsp;</span>' +
	                            '               <span class="notification_createdtime pull-right" title="' + item['createdtime'] + '">' + item['createdtime'] + '&nbsp;</span>' +
	                            '           </div>' +
	//    	                            '           <div class="clearfix"></div>' +
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
