Vtiger.Class('VTEPopupReminderJS', {
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
        var thisInstance = this;
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="' + moduleName + '"]');
        if (quickCreateNode.length <= 0) {
            app.helper.showAlertNotification({
                'message': app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED')
            });
        } else {
            var quickCreateUrl = quickCreateNode.data('url');
            var quickCreateEditUrl = quickCreateUrl + '&mode=edit&record=' + record;
            quickCreateNode.data('url', quickCreateEditUrl);
            quickCreateNode.trigger('click');
            quickCreateNode.data('url', quickCreateUrl);

            if (moduleName === 'Events') {
                app.event.one('post.QuickCreateForm.show', function (e, form) {
                    thisInstance.registerEditEventModalEvents(form.closest('.modal'), isRecurring);
                });
            }
        }
    },
    registerEditEventModalEvents: function (modalContainer, isRecurring) {
        this.validateAndUpdateEvent(modalContainer, isRecurring);
    },
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
                if (isRecurring) {
                    app.helper.showConfirmationForRepeatEvents().then(function (postData) {
                        thisInstance._updateEvent(form, postData);
                    });
                } else {
                    thisInstance._updateEvent(form);
                }
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
        app.helper.showConfirmationBox({
            message: app.vtranslate('Are you sure you want to mark Event/Todo as Held?')
        }).then(function () {
            var requestParams = {
                module: "Calendar",
                action: "SaveFollowupAjax",
                mode: "markAsHeldCompleted",
                record: recordId
            };

            app.request.post({'data': requestParams}).then(function (e, res) {
                if (e) {
                    app.helper.showErrorNotification({
                        'message': app.vtranslate('JS_PERMISSION_DENIED')
                    });
                } else if (res && res['valid'] === true && res['markedascompleted'] === true) {
                    thisInstance.updateListView();
                    thisInstance.updateCalendarView(res.activitytype);
                    $('#PopupReminder').modal('hide');
                    $("#popup-reminder").trigger('click');
                } else {
                    app.helper.showAlertNotification({
                        'message': app.vtranslate('JS_FUTURE_EVENT_CANNOT_BE_MARKED_AS_HELD')
                    });
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
    updateCalendarView: function (activitytype) {
        if (app.view() === 'Calendar' || app.view() === 'SharedCalendar') {
            if (activitytype === 'Event') {
                this.updateAllEventsOnCalendar();
            } else {
                this.updateAllTasksOnCalendar();
            }
        }
    },
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
        jQuery(document).on('click', ".deleteCalendarEvent",function () {
            var thisInstance = VTEPopupReminderJS.getInstance();
            var focus = $(this);
            var idEvent = focus.data('id');
            var module = focus.data('module');
            thisInstance.deleteCalendarEvent(idEvent, module,false);
        })
    },
    registerEventmarkAsHeld : function () {
        jQuery(document).on('click', ".markAsHeld",function () {
            var thisInstance = VTEPopupReminderJS.getInstance();
            var focus = $(this);
            var idEvent = focus.data('id');
            var module = focus.data('module');
            thisInstance.markAsHeld(idEvent, module,false);
        })
    },
    registerEvents : function () {
        this._super();
        this.registerEventEditCalendarEvent();
        this.registerEventDeleteCalendarEvent();
        this.registerEventmarkAsHeld();
    }
});

jQuery(document).ready(function() {
    setTimeout(function () {
        initData_VTEPopupReminder();
    }, 7000);
});
function initData_VTEPopupReminder() {
    var instance = new VTEPopupReminderJS();
    instance.registerEvents();

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
                //Add same type of icon(vicon), once clicked - it will open popup manually.
                var popupReminder = $('#navbar').find('#popup-reminder');
                if (popupReminder.length == 0) {
                    $('#navbar').find('ul li:eq(0)').before('<li id ="popup-reminder" style="color: green;"><div><a href="javascript:void(0)" class="fa fa-calendar-check-o" title="Popup Reminder" aria-hidden="true"></a></div></li>');
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
        app.request.post({data:paramsCheck}).then(
            function (err,data) {
                if (data.enable == '1') {
                    app.request.post({data: params}).then(
                        function (err, data) {
                            if (err === null) {
                                if(data != 'notShow') {

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
                                                };
                                                app.request.post({data: actionParams}).then(
                                                    function (err, data) {
                                                        if (err === null) {
                                                            var iconCheck = focus.closest('td').find('.glyphicon');
                                                            iconCheck.css('display', 'inline');
                                                        }
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
                                        var snoozeAll = modal.find('[name="setallSnooze"]').val();
                                        if(snoozeAll != 'default') {
                                            if(modal.find('input[name^="cbx_"]:checked').length > 0) {
                                                var listRecord = [];
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
                                                    app.request.post({data: actionParams}).then(
                                                        function (err, data) {
                                                            if (err === null) {
                                                                $.each(listcbx, function (idx, val) {
                                                                    var focus =$(this);
                                                                    var iconCheck = focus.closest('tr').find('.glyphicon');
                                                                    iconCheck.css('display', 'inline');
                                                                });
                                                            }
                                                            $(".popupReminderContainer").fadeOut();
                                                        }
                                                    );

                                                }
                                            }else {
                                                var params = {
                                                    message: 'please select record.',
                                                };
                                                app.helper.showErrorNotification(params);
                                            }
                                        }

                                    });
                                    //registerEvent for btn-SnoozeValue
                                    modal.find('[name="btn-SnoozeValue"]').on('click', function () {
                                        modal.find('input[name^="cbx_"]').attr('checked','checked');
                                        var listcbx = modal.find('input[name^="cbx_"]:checked');
                                        if(modal.find('input[name^="cbx_"]:checked').length > 0) {
                                            var listRecord = [];
                                            var snoozeAll = modal.find('[name="btn-SnoozeValue"]').val();
                                            if (snoozeAll) {
                                                $.each(listcbx, function (idx, val) {
                                                    var focus = $(this);
                                                    var record = focus.closest('tr').data('info');
                                                    listRecord.push(record);
                                                });
                                                if (listRecord.length > 0) {
                                                    var actionParams = {
                                                        module: 'VTEPopupReminder',
                                                        action: 'ActionAjax',
                                                        mode: 'updatePopupReminderDateTime',
                                                        recordEvent: listRecord,
                                                        snooze: snoozeAll
                                                    };

                                                    app.request.post({data: actionParams}).then(
                                                        function (err, data) {
                                                            if (err === null) {
                                                                $.each(listcbx, function (idx, val) {
                                                                    var focus =$(this);
                                                                    var iconCheck = focus.closest('tr').find('.glyphicon');
                                                                    iconCheck.css('display', 'inline');
                                                                });
                                                            }
                                                            $(".popupReminderContainer").fadeOut();
                                                        }
                                                    );
                                                }


                                            } else {
                                                var params = {
                                                    message: 'please select record.',
                                                };
                                                app.helper.showErrorNotification(params);
                                            }
                                        }
                                    });
                                }

                            }
                        }
                    );
                }
            });
    }
}