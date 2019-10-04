jQuery.Class('VTEPopupReminderJS', {
    popupReminderInstance : false,
    getInstance: function () {
        VTEPopupReminderJS.popupReminderInstance = new VTEPopupReminderJS();
        return VTEPopupReminderJS.popupReminderInstance;
    },
},{
    editCalendarEvent: function (eventId, isRecurring) {
        this.showEditEventModal(eventId, isRecurring);
    },
    showEditEventModal: function (eventId, isRecurring) {
        this.showEditModal('Events', eventId, isRecurring);
    },
    showEditTaskModal: function (taskId) {
        this.showEditModal('Calendar', taskId);
    },
    editCalendarTask: function (taskId) {
        this.showEditTaskModal(taskId);
    },
    showEditModal: function (moduleName, record, isRecurring) {
                var actionParams = {
                    "type":"POST",
                    "url":'index.php?module=VTEPopupReminder&view=QuickCreateAjax&sourceModule='+moduleName+'&mode=showCalendarPopup&record=' + record,
                    "dataType":"html",
                    "data" : {}
                };
                var progressIndicatorElement = jQuery.progressIndicator();

                AppConnector.request(actionParams).then(
                    function(data) {
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                        if(data) {
                            app.showModalWindow(data,function(data){
                                app.showScrollBar(jQuery('div[name="massEditContent"]'), {'height':'250px'});
                            },{'width':'90%'})
                        }
                        var massEditForm = jQuery(document).find("#module_Events_Fields");
                        massEditForm.validationEngine(app.validationEngineOptions);
                        var editInstance = Vtiger_Edit_Js.getInstanceByModuleName('Calendar');
                        editInstance.registerBasicEvents(massEditForm);
                        var thisInstance = VTEPopupReminderJS.getInstance();
                        thisInstance.registerSaveEvent();
                    },
                    function(error,err){
                        progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    }
                );
    },
    // registerEditEventModalEvents: function (modalContainer, isRecurring) {
    //     this.validateAndUpdateEvent(modalContainer, isRecurring);
    // },
    validateAndUpdateEvent: function (modalContainer, isRecurring) {
        var thisInstance = this;
        var params = {
            submitHandler: function (form) {
                jQuery("button[name='saveButton']").attr("disabled", "disabled");
                if (this.numberOfInvalids() > 0) {
                    jQuery("button[name='saveButton']").removeAttr("disabled");
                    return false;
                }
                var e = jQuery.Event(Vtiger_Edit_Js.recordPresaveEvent);
                app.event.trigger(e);
                if (e.isDefaultPrevented()) {
                    return false;
                }
                // if (isRecurring) {
                //     app.helper.showConfirmationForRepeatEvents().then(function (postData) {
                //         thisInstance._updateEvent(form, postData);
                //     });
                // } else {
                //     thisInstance._updateEvent(form);
                // }
            }
        };
        modalContainer.find('form').vtValidate(params);
    },
    _updateEvent: function (form, extraParams) {
        var formData = jQuery(form).serializeFormData();
        extraParams = extraParams || {};
        jQuery.extend(formData, extraParams);
        app.helper.showProgress();
        app.request.post({data: formData}).then(function (err, data) {
            if (!err) {
                app.helper.showSuccessNotification({"message": ''});
            } else {
                app.helper.showErrorNotification({"message": err});
            }
            app.event.trigger("post.QuickCreateForm.save", data, jQuery(form).serializeFormData());
            app.helper.hideModal();
            app.helper.hideProgress();
            $('#PopupReminder').modal('hide');
            $("#popup-reminder").trigger('click');
        });
    },
    deleteCalendarEvent: function (eventId, sourceModule, isRecurring) {
        var thisInstance = this;
        if (isRecurring) {
            app.helper.showConfirmationForRepeatEvents().then(function (postData) {
                thisInstance._deleteCalendarEvent(eventId, sourceModule, postData);
            });
        } else {
            app.helper.showConfirmationBox({
                message: app.vtranslate('LBL_DELETE_CONFIRMATION')
            }).then(function () {
                thisInstance._deleteCalendarEvent(eventId, sourceModule);
            });
        }
    },
    _deleteCalendarEvent: function (eventId, sourceModule, extraParams) {
        var thisInstance = this;
        if (typeof extraParams === 'undefined') {
            extraParams = {};
        }
        var params = {
            "module": "Calendar",
            "action": "DeleteAjax",
            "record": eventId,
            "sourceModule": sourceModule
        };
        jQuery.extend(params, extraParams);

        app.helper.showProgress();
        app.request.post({'data': params}).then(function (e, res) {
            app.helper.hideProgress();
            if (!e) {
                var deletedRecords = res['deletedRecords'];
                for (var key in deletedRecords) {
                    var eventId = deletedRecords[key];
                    if (app.view() === 'Calendar' || app.view() === 'SharedCalendar') {
                        thisInstance.getCalendarViewContainer().fullCalendar('removeEvents', eventId);
                    }
                }
                app.helper.showSuccessNotification({
                    'message': app.vtranslate('JS_RECORD_DELETED')
                });
                $('#PopupReminder').modal('hide');
                $("#popup-reminder").trigger('click');
            } else {
                app.helper.showErrorNotification({
                    'message': app.vtranslate('JS_NO_DELETE_PERMISSION')
                });
            }
        });
    },
    markAsHeld: function (recordId) {
        var thisInstance = this;
        var message = app.vtranslate('JS_CONFIRM_MARK_AS_HELD');
        Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function () {
            var params = {
                module: "Calendar",
                action: "SaveFollowupAjax",
                mode: "markAsHeldCompleted",
                record: recordId
            };

            AppConnector.request(params).then(function(data){
                if(data['error']){
                    var param = {text:app.vtranslate('Permissions Denied')};
                    Vtiger_Helper_Js.showPnotify(param);
                }
                else if(data['result'].valid && data['result'].markedascompleted){
                    //Update listview and pagination
                    var orderBy = jQuery('#orderBy').val();
                    var sortOrder = jQuery("#sortOrder").val();
                    var urlParams = {
                        "orderby": orderBy,
                        "sortorder": sortOrder
                    }
                    if (app.getViewName() == "List" && app.getModuleName() == "Calendar"){
                        jQuery('#recordsCount').val('');
                        jQuery('#totalPageCount').text('');
                        listInstance  =  Vtiger_List_Js.getInstance();
                        listInstance.getListViewRecords(urlParams).then(function(){
                            listInstance.updatePagination();
                        });
                    }
                    if(data['result'].activitytype == 'Task')
                        var param = {text:app.vtranslate('Task marked as Completed')};
                    else
                        var param = {text:app.vtranslate('Event marked as Held')};
                    Vtiger_Helper_Js.showMessage(param);
                }
                else{
                    var param = {text:app.vtranslate('Future event cannot be marked as Held')};
                    Vtiger_Helper_Js.showPnotify(param);
                }
            });
        });
    },
    updateAllEventsOnCalendar: function () {
        this._updateAllOnCalendar("Events");
        this.updateAgendaListView();
    },
    updateAgendaListView: function () {
        var calendarView = this.getCalendarViewContainer().fullCalendar('getView');
        if (calendarView.name === 'vtAgendaList') {
            this.getCalendarViewContainer().fullCalendar('rerenderEvents');
        }
    },
    getCalendarViewContainer: function () {
        if (!Calendar_Calendar_Js.calendarViewContainer.length) {
            Calendar_Calendar_Js.calendarViewContainer = jQuery('#mycalendar');
        }
        return Calendar_Calendar_Js.calendarViewContainer;
    },
    getFeedRequestParams: function (start, end, feedCheckbox) {
        var dateFormat = 'YYYY-MM-DD';
        var startDate = start.format(dateFormat);
        var endDate = end.format(dateFormat);
        return {
            'start': startDate,
            'end': endDate,
            'type': feedCheckbox.data('calendarFeed'),
            'fieldname': feedCheckbox.data('calendarFieldname'),
            'color': feedCheckbox.data('calendarFeedColor'),
            'textColor': feedCheckbox.data('calendarFeedTextcolor'),
            'conditions': feedCheckbox.data('calendarFeedConditions')
        };
    },
    _updateAllOnCalendar: function (calendarModule) {
        var thisInstance = this;
        this.getCalendarViewContainer().fullCalendar('addEventSource',
            function (start, end, timezone, render) {
                var activeFeeds = jQuery('[data-calendar-feed="' + calendarModule + '"]:checked');

                var activeFeedsRequestParams = {};
                activeFeeds.each(function () {
                    var feedCheckbox = jQuery(this);
                    var feedRequestParams = thisInstance.getFeedRequestParams(start, end, feedCheckbox);
                    activeFeedsRequestParams[feedCheckbox.data('calendarSourcekey')] = feedRequestParams;
                });

                if (activeFeeds.length) {
                    var requestParams = {
                        'module': app.getModuleName(),
                        'action': 'Feed',
                        'mode': 'batch',
                        'feedsRequest': activeFeedsRequestParams
                    };
                    var events = [];
                    app.helper.showProgress();
                    activeFeeds.attr('disabled', 'disabled');
                    app.request.post({'data': requestParams}).then(function (e, data) {
                        if (!e) {
                            data = JSON.parse(data);
                            for (var feedType in data) {
                                var feed = JSON.parse(data[feedType]);
                                feed.forEach(function (entry) {
                                    events.push(entry);
                                });
                            }
                        } else {
                            console.log("error in response : ", e);
                        }
                        activeFeeds.each(function () {
                            var feedCheckbox = jQuery(this);
                            thisInstance.removeEvents(feedCheckbox);
                        });
                        render(events);
                        activeFeeds.removeAttr('disabled');
                        app.helper.hideProgress();
                    });
                }
            });
    },
    removeEvents: function (feedCheckbox) {
        var module = feedCheckbox.data('calendarFeed');
        var conditions = feedCheckbox.data('calendarFeedConditions');
        var fieldName = feedCheckbox.data('calendarFieldname');
        this.getCalendarViewContainer().fullCalendar('removeEvents',
            function (eventObj) {
                return module === eventObj.module && eventObj.conditions === conditions && fieldName === eventObj.fieldName;
            });
    },
    // updateCalendarView: function (activitytype) {
    //     if (app.view() === 'Calendar' || app.view() === 'SharedCalendar') {
    //         if (activitytype === 'Event') {
    //             this.updateAllEventsOnCalendar();
    //         } else {
    //             this.updateAllTasksOnCalendar();
    //         }
    //     }
    // },
    updateListView: function () {
        if (app.view() === 'List') {
            var listInstance = Vtiger_List_Js.getInstance();
            listInstance.loadListViewRecords();
        }
    },
    registerEventEditCalendarEvent : function () {
        jQuery(document).on('click', ".editCalendarEvent",function () {
            var thisInstance = VTEPopupReminderJS.getInstance();
            var focus = $(this);
            var idEvent = focus.data('id');
            var module = focus.data('module');
            if(module == 'Events') {
                thisInstance.editCalendarEvent(idEvent, false);
            }else{
                thisInstance.editCalendarTask(idEvent, false);
            }
        })
    },
    registerEventDeleteCalendarEvent : function () {
        jQuery(document).on('click', ".deleteCalendarEvent",function (e) {
            var thisInstance = VTEPopupReminderJS.getInstance();
            var focus = $(this);
            var recordId = focus.data('id');
            var message = app.vtranslate('Are you sure you want to delete?');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(
                function(e) {
                    //Confirmed to delete
                    var params = {
                        "module": "Calendar",
                        "action": "DeleteAjax",
                        "record": recordId
                    }
                    AppConnector.request(params).then(function(data){
                        if(data.success) {
                            var view = app.getViewName();
                            if(view == 'Calendar' && app.getModuleName() == 'Calendar'){
                                thisInstance = new Calendar_CalendarView_Js();
                                thisInstance.getCalendarView().fullCalendar('removeEvents', recordId);
                            }
                            var param = {text:app.vtranslate('Calendar entry deleted successfully')};
                            Vtiger_Helper_Js.showMessage(param);
                            $('#PopupReminder').modal('hide');
                            $("#popup-reminder").trigger('click');
                        } else {
                            var  params = {
                                text : app.vtranslate('You do not have permission to delete calendar entries.')
                            }
                            Vtiger_Helper_Js.showPnotify(params);
                        }

                    });
                },
                function(error, err){
                    e.preventDefault();
                    return false;
                });
        });
    },
    registerEventmarkAsHeld : function () {
        jQuery(document).on('click', ".markAsHeld",function () {
            var thisInstance = VTEPopupReminderJS.getInstance();
            var focus = $(this);
            var idEvent = focus.data('id');
            var module = focus.data('module');
            if(module == 'Events') {
                thisInstance.markAsHeld(idEvent, 'Events',false);

            }else{
                thisInstance.markAsHeld(idEvent, 'Calendar',false);
            }
        })
    },
    registerSaveEvent: function() {
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        jQuery(document).find("#massEditContainer").on('click','button[name="saveButton"]', function(e){
            var formCalendar=jQuery(document).find("#module_Events_Fields");
            var quickCreateCalUrl = formCalendar.serializeFormData();
            var progressIndicatorElement = jQuery.progressIndicator();
            AppConnector.request(quickCreateCalUrl).then(
                function(data) {
                    progressIndicatorElement.progressIndicator({'mode' : 'hide'});
                    app.hideModalWindow();
                    $('#PopupReminder').modal('hide');
                    $("#popup-reminder").trigger('click');
                }
            );
        })
    },
    registerEvents : function () {
        this.registerEventEditCalendarEvent();
        this.registerEventDeleteCalendarEvent();
        this.registerEventmarkAsHeld();
    }
});
jQuery(document).ready(function () {
    var instance = new VTEPopupReminderJS();
    instance.registerEvents();
    $(document).find('button[type="submit"]').on('click',function (e) {
        var element=jQuery(e.currentTarget);
        var form = element.closest('#EditView');
        var paramModule = form.find('input[name="module"]').val();
        var paramAction = form.find('input[name="action"]').val();
        var paramRecord = form.find('input[name="record"]');
        var reminder_interval = form.find('[name="reminder_interval"]');
        var paramTimeFormatOptions = form.find('input[name="timeFormatOptions"]');
        if(paramModule == 'Users' && paramAction == 'Save' && paramRecord.length > 0 && paramTimeFormatOptions.length >0 && reminder_interval.length > 0) {
            var Interval = reminder_interval.val();
            var params = {
                module : 'VTEPopupReminder',
                action : 'ActionAjax',
                mode : 'updateRecordActivity',
                interval : Interval,
            }
            AppConnector.request(params).then(
                function (data) {
                    var param = {
                        title : app.vtranslate('JS_MESSAGE'),
                        text: 'Update Activity Record Success',
                        animation: 'show',
                        type: 'success'
                    };
                    Vtiger_Helper_Js.showPnotify(param);
                }
            )
        }
    });

    var setPopupReminder = setInterval(function () {
        var params =  {
            module: 'VTEPopupReminder',
            view: 'MassActionAjax'
        };
        registerEventShowPopupReminder(params);

    }, 30000);
    var paramsCheck = {};
    paramsCheck.action = 'ActionAjax';
    paramsCheck.module = 'VTEPopupReminder';
    paramsCheck.mode = 'checkEnable';
    AppConnector.request(paramsCheck).then(
        function (data) {
            if (data.result.enable == '1') {
                //Add same type of icon(vicon), once clicked - it will open popup manually.
                var popupReminder = $('#headerLinksBig').find('#popup-reminder');
                if (popupReminder.length == 0) {
                    $('#headerLinksBig').find('.settingIcons:eq(0)').before('<span id ="popup-reminder" style="float: left"><a href="javascript:void(0)"><img style="width: 20px; margin-left: 10px" src="layouts/vlayout/modules/VTEPopupReminder/resources/imgs/icon-booking2.png" alt="Feedback" title="Popup Reminder"></a></span>');
                    // $('#headerLinksBig').append('<span id ="popup-reminder">aaaa</span>');
                }
            }});
    $(document).on('click','#popup-reminder',function () {
        var params =  {
            module: 'VTEPopupReminder',
            isShow : 'isShow',
            view: 'MassActionAjax'
        };
        registerEventShowPopupReminder(params);
    });
    var registerEventShowPopupReminder = function (params) {
        var paramsCheck = {};
        paramsCheck.action = 'ActionAjax';
        paramsCheck.module = 'VTEPopupReminder';
        paramsCheck.mode = 'checkEnable';
        AppConnector.request(paramsCheck).then(
            function (data) {
                if (data.result.enable == '1') {
                    AppConnector.request(params).then(
                        function(data){
                            if(data.indexOf("notShow")>=0) {
                                data = JSON.parse(data);
                            }
                            if(data.result != 'notShow') {
                                var containerModal = $(data);
                                var modal = $(containerModal).find('#PopupReminder');
                                if($('.popupReminderContainer').length > 0 && $('#PopupReminder').hasClass('in') != true) {
                                    $('.popupReminderContainer').html(modal);
                                }else if($('.popupReminderContainer').length == 0) {
                                    $('body').append(containerModal);
                                }
                                var actives = modal.data('info');
                                if( $('#PopupReminder').hasClass('in') != true) {
                                    $('#PopupReminder').modal('show');
                                }
                                //updatePopupReminderDateTime
                                modal.find('[name="snooze"]').on('change', function () {
                                    var focus = $(this);
                                    var snooze = focus.val();
                                    if(snooze != 'default') {
                                        var recordEvent= [];
                                        recordEvent.push(focus.closest('tr').data('info'));
                                        if(recordEvent.length >0) {
                                            var actionParams = {
                                                module : 'VTEPopupReminder',
                                                action : 'ActionAjax',
                                                mode : 'updatePopupReminderDateTime',
                                                recordEvent : recordEvent,
                                                snooze : snooze
                                            }
                                            AppConnector.request(actionParams).then(
                                                function(data){
                                                    var iconCheck = focus.closest('td').find('.glyphicon');
                                                    iconCheck.css('display', 'inline-block');
                                                }
                                            );
                                        }
                                    }
                                });

                                //registerEvent for checkbox
                                modal.find('input[name="checkAll"]').on('click', function () {
                                    if ($(this).is(':checked')) {
                                        modal.find('input[name^="cbx_"]').attr('checked','checked');
                                    }else {
                                        modal.find('input[name^="cbx_"]').removeAttr('checked');
                                    }

                                });

                                //registerEvent for btn-setallSnooze
                                modal.find('[name="btn-setallSnooze"]').on('click', function () {
                                    var listcbx = modal.find('input[name^="cbx_"]:checked');
                                    if(modal.find('input[name^="cbx_"]:checked').length > 0) {
                                        var listRecord = [];
                                        var snoozeAll = modal.find('[name="setallSnooze"]').val();
                                        if(snoozeAll != 'default') {
                                            $.each(listcbx, function (idx, val) {
                                                var focus =$(this);
                                                var record = focus.closest('tr').data('info');
                                                listRecord.push(record);
                                            });
                                            if(listRecord.length > 0) {
                                                var actionParams = {
                                                    module : 'VTEPopupReminder',
                                                    action : 'ActionAjax',
                                                    mode : 'updatePopupReminderDateTime',
                                                    recordEvent : listRecord,
                                                    snooze : snoozeAll
                                                };
                                                AppConnector.request(actionParams).then(
                                                    function(data){
                                                        $.each(listcbx, function (idx, val) {
                                                            var focus =$(this);
                                                            var iconCheck = focus.closest('tr').find('.glyphicon');
                                                            iconCheck.css('display', 'inline-block');
                                                        });
                                                    }
                                                );
                                            }
                                        }
                                    }else {
                                        Vtiger_Helper_Js.showPnotify('Please select record  !!!');
                                    }
                                });
                            }

                        }
                    );
                }
            });
    }


});