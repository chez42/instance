/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class NotificationsJS */
Vtiger.Class("NotificationsJS", {}, {
    addHeaderIcon: function () {
        var thisInstance = this;
        var headerLinksBig = jQuery('#menubar_quickCreate').closest('li');
        var headerIcon = '<li>' +
            '<div id="notificationContainer" class="dropdown" style="margin-top: 15px;" class="">' +
            '<a href="#" id="headerNotification" class="dropdown-toggle" >' +
            '<span class="vicon vicon-bell" aria-hidden="true" title="Notifications"></span>' +
            '<strong class="notification_count">?</strong></a>' +
            '<div id="headerNotificationList" class="dropdown-menu" role="menu">' +
            '</div></li>';
        headerLinksBig.before(headerIcon);

        jQuery('#headerNotification .vicon-bell').on('click',function (e) {
            jQuery('#notificationContainer').toggleClass('open');
        });
        jQuery('body').on('click', function (e) {
            if (!jQuery('#notificationContainer').is(e.target) && jQuery('#notificationContainer').has(e.target).length === 0 && jQuery('.open').has(e.target).length === 0) {
                jQuery('#notificationContainer').removeClass('open');
            }
        });

        var notificationContainer = jQuery('#headerNotification');
        var notificationList = jQuery('#headerNotificationList');
        var notificationCounter = notificationContainer.find('.notification_count');

        //// Init total notice
        var params = {
            'module': 'Notifications',
            'action': 'ActionAjax',
            'mode': 'getNotifications'
        };
        AppConnector.request(params).then(
            function (response) {
                if (response.success == true) {
                    notificationList.empty();

                    var count = response.result.count;
                    jQuery('.notification_count').html(count);

                    if (count == 0) {
                        // Empty list
                        notificationList.remove();
                        return;
                    }

                    var items = response.result.items;
                    var item = null;
                    var listItem = '';
                    var itemLength = items.length;
                    var limitDivide = itemLength - 2;

                    // for (var k in items) {
                    for (var i = 0; i < itemLength; i++) {
                        item = items[i];

                        var divider = '';
                        if (i >= 0 && i <= limitDivide) {
                            divider = '<div class="divider">&nbsp;</div>';
                        }

                        listItem =
                            '<li>' +
                            '   <a class="notification_link" href="javascript:;" data-href="' + item['link'] + '" data-id="' + item['id'] + '" data-rel_id="' + item['rel_id'] + '">' +
                            '       <div class="notification-container">' +
                            '           <i class="icon icon-ok" onclick="return clickToOk(this);" title="Acknowledge"> <img src="layouts/v7/modules/Notifications/resources/img/icon-ok.png" alt="Acknowledge"></i>' +
                            // '        <div class="notification_thumbnail"><img src="' + item['thumbnail'] + '" /></div>' +
                            '           <div class="notification_detail">' +
                            '               <span class="notification_full_name" title="' + item['full_name'] + '">' + item['full_name'] + '&nbsp;</span>' +
                            '               <span class="notification_description" title="' + item['description'] + '">' + item['description'] + '&nbsp;</span>' +
                            '               <span class="notification_createdtime" title="' + item['createdtime'] + '">' + item['createdtime'] + '&nbsp;</span>' +
                            '           </div>' +
                            '           <i class="icon icon-pp" onclick="return clickToPP(this);" title="Postpone"><img src="layouts/v7/modules/Notifications/resources/img/icon-pp.png" alt="Postpone"></i>' +
                            '           <div class="clearfix"></div>' +
                            '       </div>' +
                            '   </a>' +
                            divider +
                            '</li>';

                        jQuery('#headerNotificationList').append(listItem);
                    }
                }
            },
            function (error) {
                //TODO : Handle error
                console.log('error =', error);
            }
        );

      
        // Redirect link
        notificationList.on('click', '.notification_link .notification_full_name', function (event) {
            var currentTarget = jQuery(event.currentTarget);
            var notificationLink = currentTarget.closest('.notification_link');
            window.location.href = notificationLink.data('href');
        });

    },
    /**
     * Fn - updateTotalCounter
     *
     * @param notificationLink
     * @param notificationList
     * @param notificationCounter
     */
    updateTotalCounter: function (notificationLink, notificationList, notificationCounter) {
        notificationLink.closest('li').addClass('hide');
        // Remove last divide
        notificationList.find('li:not(.hide)').filter(':last').find('.divider').remove();

        // Update counter
        var currentTotal = notificationCounter.text();
        currentTotal = (currentTotal) ? parseInt(currentTotal) : 0;
        var total = currentTotal - 1;
        total = (total > 0) ? total : 0;
        notificationCounter.text(total);

        if (total == 0) {
            // Remove empty list
            notificationList.remove();
        }
    },

    /**
     * Fn - registerEvents
     */
    registerEvents: function () {
        var thisInstance = this;
        thisInstance.addHeaderIcon();
    }
});
function init_func(){
	 // Check enable
    var params = {};
    params.action = 'ActionAjax';
    params.module = 'Notifications';
    params.mode = 'checkEnable';
    AppConnector.request(params).then(
        function(data) {
            if (data.result.enable == '1') {
                var instance = new NotificationsJS();
                instance.registerEvents();
            }
        }
    );

}
//On Page Load
jQuery(document).ready(function() {
    setTimeout(function () {
        initData_Notifications();
    }, 10000);
});
function initData_Notifications() {
    // Only load when loadHeaderScript=1 BEGIN #241208
    if (typeof VTECheckLoadHeaderScript == 'function') {
        if (!VTECheckLoadHeaderScript('Notifications')) {
            return;
        }
    }
    // Only load when loadHeaderScript=1 END #241208

   init_func();
}
function clickToOk(btnOK){
	var notificationContainer = jQuery('#headerNotification');
    var notificationList = jQuery('#headerNotificationList');
	var notificationCounter = notificationContainer.find('.notification_count');
	var currentTarget = jQuery(btnOK);
	var notificationLink = currentTarget.closest('.notification_link');
	var id = notificationLink.data('id');

	// Mark notification read
	var params = {
		'module': 'Notifications',
		'action': 'ActionAjax',
		'mode': 'markNotificationRead',
		'record': id
	};
	var instance = new NotificationsJS();
	AppConnector.request(params).then(
		function (response) {
			if (response.success == true) {
				instance.updateTotalCounter(notificationLink, notificationList, notificationCounter);
                app.helper.showSuccessNotification({message:'Notification has been acknowledged'},{offset:{y: 450}});
			} else {
				Vtiger_Helper_Js.showMessage({
					text: response.error.message
				})
			}
		},
		function (error) {
			Vtiger_Helper_Js.showMessage({
				text: error
			})
		}
	);

	return false;
}
function clickToPP(btnPP){
	var notificationContainer = jQuery('#headerNotification');
    var notificationList = jQuery('#headerNotificationList');
	var notificationCounter = notificationContainer.find('.notification_count');
	var currentTarget = jQuery(btnPP);
	var notificationLink = currentTarget.closest('.notification_link');
	var instance = new NotificationsJS();
	instance.updateTotalCounter(notificationLink, notificationList, notificationCounter);
    app.helper.showSuccessNotification({message:'Notification has been postponed'},{offset:{y: 450}});
	return false;
}