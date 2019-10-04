/* ********************************************************************************
 * The content of this file is subject to the Notifications ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

/** @class NotificationsJS */
jQuery.Class("NotificationsJS", {}, {
    addHeaderIcon: function () {
        var thisInstance = this;
        var headerLinksBig = jQuery('#headerLinksBig');
        var headerIcon =
            '<span class="dropdown span">' +
            '<span class="dropdown-toggle settingIcons" data-toggle="dropdown" href="#">' +
            '<a id="headerNotification">' +
            '<img title="Notifications" alt="Notifications" src="layouts/vlayout/modules/Notifications/resources/img/icon-bell.png" class="alignMiddle">' +
            '<strong class="notification_count">?</strong>' +
            '</a>' +
            '</span>' +
            '<ul id="headerNotificationList" class="dropdown-menu pull-right">' +
            //'<li><a target="" href="#">&nbsp;</a></li>' +
            '</ul>' +
            '</span>';
        headerLinksBig.prepend(headerIcon);
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
                            '           <button class="btn btn-small btn-success" onclick="return clickToOk(this);"> '+ app.vtranslate('OK') +'</button>' +
                            // '        <div class="notification_thumbnail"><img src="' + item['thumbnail'] + '" /></div>' +
                            '           <div class="notification_detail">' +
                            '               <span class="notification_full_name" title="' + item['full_name'] + '">' + item['full_name'] + '&nbsp;</span>' +
                            '               <span class="notification_description" title="' + item['description'] + '">' + item['description'] + '&nbsp;</span>' +
                            '               <span class="notification_createdtime" title="' + item['createdtime'] + '">' + item['createdtime'] + '&nbsp;</span>' +
                            '           </div>' +
                            '           <button class="btn btn-small btn-warning" onclick="return clickToPP(this);">' + app.vtranslate('PP') + '&nbsp;</button>' +
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
        notificationLink.closest('li').remove();
        // Remove last divide
        notificationList.find('li:last').find('.divider').remove();

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
jQuery(document).ready(function () {
   init_func();
});
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
			} else {
				Vtiger_Helper_Js.showMessage({
					text: response.error.message
				})
			}
		},
		function (error) {
			console.log(error);
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
	return false;
}