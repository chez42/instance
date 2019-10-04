/* ********************************************************************************
 * The content of this file is subject to the Time Tracker ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */


jQuery.Class("TimeTracker_Js", {
    formatTime : function (val) {
        if(!val) {
            val=0;
        }
        var sec_num = parseInt(val, 10); // don't forget the second param
        var hours   = Math.floor(sec_num / 3600);
        var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
        var seconds = sec_num - (hours * 3600) - (minutes * 60);

        if (hours   < 10) {hours   = "0"+hours;}
        if (minutes < 10) {minutes = "0"+minutes;}
        if (seconds < 10) {seconds = "0"+seconds;}
        var time    = hours+':'+minutes+':'+seconds;
        return time;
    },

},{
    timeInSecond:null,
    intervalTimer:null,
    intervalTimerSave:null,

    intervalTimerHeader:null,
    intervalTimerSaveHeader:null,
    timerRunning: null,

    checkRunningTimer: function () {
        var instance = this;
        var form = jQuery('form[name="TrackerForm"]');
        var record=form.find('[name="parentId"]').val();
        var params = {};
        params.action = 'ActionAjax';
        params.module = 'TimeTracker';
        params.mode = 'checkRunningTimer';
        params.record = record;
        app.request.post({data:params}).then(
            function(error,data) {
                if (error===null){
                    var response = data['result'];
                    var result = response['success'];
                    if(result == true){
                        instance.timerRunning = response['data'];
                        return true;
                    }
                    return false;
                }
            }
        );
    },

    reloadPopupContent: function () {
        var form = jQuery('form[name="TrackerForm"]');
        var record=form.find('[name="parentId"]').val();
        var params = {};
        params.view = 'MassActionAjax';
        params.module = 'TimeTracker';
        params.mode = 'getTimeTrackerPopup';
        params.record = record;
        app.request.post({data:params}).then(
            function(error,data) {
                if (error===null){
                    jQuery('#timeTrackerPopup').html(data);
                    vtUtils.applyFieldElementsView(jQuery('#timeTrackerPopup'));
                    var instance = new TimeTracker_Js();
                    instance.registerEvents();
                }
            }
        );
    },

    registerEventForTimeTrackerButton: function () {
        jQuery('#timeTrackerButton').on('click', function () {
            var listActiveTimer = jQuery('#listActiveTimer').val();
            if(listActiveTimer == 0){
                var params = {};
                params.animation = "show";
                params.type = 'info';
                params.title = app.vtranslate('No Active Timers !');
                params.message = app.vtranslate('No Active Timers !');
                app.helper.showErrorNotification(params);
            }else{
                var visible= jQuery(this).data('popup-visible');
                if(visible == 'true') {
                    jQuery(this).data('popup-visible','false');
                    jQuery('#timeTrackerPopup').hide();
                }else{
                    jQuery(this).data('popup-visible','true');
                    jQuery('#timeTrackerPopup').show();
                }
            }

        });
    },

    registerEventForCommentButton: function() {
        jQuery('#commentIcon').on('click', function () {
            var status = jQuery('#controlButton').data('status');
            if(status == ''){
                return;
            }
            var autoComment=jQuery(this).data('auto-comment');
            if(autoComment == '1') {
                jQuery(this).data('auto-comment','0');
                jQuery('#auto_comment').val(0);
                jQuery('#commnentOn').hide();
                jQuery('#commentOff').show();
            }else{
                jQuery(this).data('auto-comment','1');
                jQuery('#auto_comment').val(1);
                jQuery('#commentOff').hide();
                jQuery('#commnentOn').show();
            }
        });
    },

    registerDateTimePicker: function (container) {
        var dateFormat = jQuery('#dateFormat').val();
        var timeFormat = jQuery('#timeFormat').val();
        if(timeFormat == 12) {
            container.find('.dateTimeField:not([readonly])').datetimepicker({
                format: dateFormat + ' HH:ii P',
                autoclose: true,
                todayBtn: true,
                showMeridian: true
            });
        }else{
            container.find('.dateTimeField:not([readonly])').datetimepicker({
                format: dateFormat + ' hh:ii',
                autoclose: true,
                todayBtn: true
            });
        }

        container.find('.dateTimeField:not([readonly])').datetimepicker('update');

    },

    createEvents: function () {
        var thisInstance=this;
        thisInstance.stopTimer(thisInstance.intervalTimer);
        thisInstance.stopTimer(thisInstance.intervalTimerSave);

        var progressIndicatorElement = app.helper.showProgress();
        var form = jQuery('form[name="TrackerForm"]');
        var params={};
        params = form.serializeFormData();
        params.module = 'TimeTracker';
        params.action = 'ActionAjax';
        params.mode = 'createEvents';
        app.request.post({data:params}).then(
            function(error, data) {
                if (error === null){
                    app.helper.hideProgress();
                    var params = {};
                    params['message'] = 'Record has been created';
                    app.helper.showSuccessNotification(params);
                    var saveUrl = window.location.href;
                    if(saveUrl.search('view=List')<0){
                        thisInstance.reloadPopupContent();
                        saveUrl = saveUrl.replace('&go_back=1', '');
                        window.history.pushState('', '', saveUrl);
                    }else{
                        location.reload();
                    }
                }
            }
        );
    },



    registerEventForControlButton: function () {
        var thisInstance=this;
        jQuery('#controlButton').on('click', function () {
            var status=jQuery(this).data('status');
            if(status == 'running') {
                jQuery(this).data('status','');
                jQuery('#trackerStatus').val('');
                var buttonLbl=jQuery(this).data('start-label');
                jQuery(this).html(buttonLbl);
                thisInstance.stopTimer(thisInstance.intervalTimer);
                thisInstance.stopTimer(thisInstance.intervalTimerSave);

                var endDateTime = jQuery('#endDateTime');
                if(endDateTime.val()=='') {
                    var dateFormat = jQuery('#dateFormat').val();
                    var userTime = jQuery('#user_time').val();
                    var res = userTime.split(" ");
                    var curDate = res[0];
                    if(dateFormat=='dd-mm-yyyy'){
                        var dmy = curDate.split("-");
                        curDate = dmy[2]+'-'+dmy[1]+'-'+dmy[0];
                    }else if(dateFormat=='mm-dd-yyyy'){
                        var dmy = curDate.split("-");
                        curDate = dmy[2]+'-'+dmy[0]+'-'+dmy[1];
                    }else if(dateFormat=='yyyy-mm-dd'){
                        var dmy = curDate.split("-");
                        curDate = dmy[0]+'-'+dmy[1]+'-'+dmy[2];
                    }else if(dateFormat=='yyyy-dd-mm'){
                        var dmy = curDate.split("-");
                        curDate = dmy[0]+'-'+dmy[2]+'-'+dmy[1];
                    }
                    userTime = curDate + ' '+res[1];
                    var currentTime = new Date(userTime);
                    var selectedDate = app.getDateInVtigerFormat(dateFormat, currentTime);

                    var timeFormat = jQuery('#timeFormat').val();
                    if(timeFormat == 12) {
                        endDateTime.val(selectedDate + ' ' + currentTime.toString('hh:mm tt'));
                    }else{
                        endDateTime.val(selectedDate + ' ' + currentTime.toString('HH:mm'));
                    }

                    //endDateTime.val(selectedDate + ' ' + hours + ':' + minutes + ':' + seconds);
                    if (endDateTime.attr('readonly') == undefined) {
                        endDateTime.datetimepicker('update');
                    }
                }else{
                    jQuery('#timeTrackerTotal').val(0);
                }

                // create Events
                thisInstance.createEvents();
            }else{
                var controlBtn=jQuery('#controlButton');
                var listActiveTimerTable = jQuery('#listActiveTimers');
                var recordId = jQuery('form.timeTrackerForm').find('input[name="parentId"]').val();
                if(jQuery('#timeTrackerTotalRunning').val() != undefined){
                    thisInstance.updateTimeTrackerTotal("pause");
                    thisInstance.stopTimer(thisInstance.intervalTimerHeader);
                    thisInstance.stopTimer(thisInstance.intervalTimerSaveHeader);
                    jQuery(".quickActions .timeTrackerTotalRunning").removeClass("timeTrackerTotalRunning").addClass("timeTrackerTotal");
                    jQuery('#timeTrackerTotalRunning').remove();
                    jQuery('#record_running').remove();
                    jQuery('#header_popup').html(jQuery("input[name='record_name']").val());
                }


                if(status == '') {
                    var startDateTime = jQuery('#startDateTime');
                    var dateFormat = jQuery('#dateFormat').val();
                    var userTime = jQuery('#user_time').val();
                    var res = userTime.split(" ");
                    var curDate = res[0];
                    if(dateFormat=='dd-mm-yyyy'){
                        var dmy = curDate.split("-");
                        curDate = dmy[2]+'-'+dmy[1]+'-'+dmy[0];
                    }else if(dateFormat=='mm-dd-yyyy'){
                        var dmy = curDate.split("-");
                        curDate = dmy[2]+'-'+dmy[0]+'-'+dmy[1];
                    }else if(dateFormat=='yyyy-mm-dd'){
                        var dmy = curDate.split("-");
                        curDate = dmy[0]+'-'+dmy[1]+'-'+dmy[2];
                    }else if(dateFormat=='yyyy-dd-mm'){
                        var dmy = curDate.split("-");
                        curDate = dmy[0]+'-'+dmy[2]+'-'+dmy[1];
                    }
                    userTime = curDate + ' '+res[1];
                    var currentTime = new Date(userTime);
                    var selectedDate = app.getDateInVtigerFormat(dateFormat, currentTime);
                    var timeFormat = jQuery('#timeFormat').val();
                    if(timeFormat == 12) {
                        startDateTime.val(selectedDate + ' ' + currentTime.toString('hh:mm tt'));
                    }else{
                        startDateTime.val(selectedDate + ' ' + currentTime.toString('HH:mm'));
                    }

                    if (startDateTime.attr('readonly') == undefined) {
                        startDateTime.datetimepicker('update');
                    }
                    var newEle = listActiveTimerTable.find('.row_base').clone();
                    var link = jQuery('#header_popup').attr('href');
                    newEle.removeClass('hide');
                    newEle.attr('id','record_'+recordId);
                    newEle.find('.record_name').html(jQuery('#header_popup').text());
                    newEle.find('.record_name, .play_icon').attr('href',link +'&go_back=1');
                    listActiveTimerTable.find('.timeValue').removeClass('timeTrackerTotal');
                    newEle.find('.timeTrackerTotal').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotal').val())));
                    listActiveTimerTable.prepend(newEle);
                    jQuery('#endDateTime').css('cursor','auto');
                    jQuery('#endDateTime').prop('disabled', false);
                    jQuery('#startDateTime').css('cursor','auto');
                    jQuery('#startDateTime').prop('disabled', false);
                    jQuery('.propFieldInput').prop('disabled', false);
                    jQuery('#activitytype').prop('disabled', false).trigger("liszt:updated");
                    jQuery('.propSelectFieldInput').next().eventPause('active');
                    jQuery('.propSelectFieldInput').next().find('a').removeClass('grayBgProp');
                    jQuery('#subject').val('');
                    jQuery('#description').val('');
                    jQuery('#activitytype').val(jQuery('#activitytype').data('default-value'));
                    jQuery('#activitytype').trigger("liszt:updated");
                    jQuery('#endDateTime').val('');

                }else if(status == 'pause'){
                    jQuery('.propSelectFieldInput').next().eventPause('active');
                    jQuery('.propSelectFieldInput').next().find('a').removeClass('grayBgProp');
                    listActiveTimerTable.find('.timeValue').removeClass('timeTrackerTotal');
                    listActiveTimerTable.find('#record_'+recordId+' .timeValue').addClass('timeTrackerTotal');
                    jQuery('#endDateTime').css('cursor','auto');
                    jQuery('#endDateTime').prop('disabled', false);
                    jQuery('#startDateTime').css('cursor','auto');
                    jQuery('#startDateTime').prop('disabled', false);
                    jQuery('#activitytype').prop('disabled', false).trigger("liszt:updated");
                    jQuery('.propFieldInput').prop('disabled', false);

                }
                thisInstance.intervalTimer =  setInterval(function () {
                    thisInstance.timeTrackerTimer();
                }, 1000);
                thisInstance.saveTimeTrackerData();
                thisInstance.intervalTimerSave =  setInterval(function () {
                    thisInstance.saveTimeTrackerData();
                }, 5000);
                controlBtn.data('status','running');
                jQuery('#trackerStatus').val('running');
                var buttonLbl=controlBtn.data('complete-label');
                controlBtn.html(buttonLbl)
            }
        });
    },

    saveTimeTrackerData: function () {
        var thisInstance=this;
        var form = jQuery('form[name="TrackerForm"]');
        var params={};
        params = form.serializeFormData();
        params.module = 'TimeTracker';
        params.action = 'ActionAjax';
        params.mode = 'saveTempData';
        params.currentModule = jQuery('#module').val();

        app.request.post({data:params}).then(
            function(error,data) {
                if(error !== null) {
                    if(typeof thisInstance.intervalTimer != 'undefined') {
                        thisInstance.stopTimer(thisInstance.intervalTimer);
                        thisInstance.stopTimer(thisInstance.intervalTimerSave);
                    }
                    if(typeof thisInstance.intervalTimerHeader != 'undefined') {
                        thisInstance.stopTimer(thisInstance.intervalTimerHeader);
                        thisInstance.stopTimer(thisInstance.intervalTimerSaveHeader);
                    }
                    document.location.reload();
                }
            }
        );
    },

    registerEventForPauseButton: function () {
        var thisInstance=this;

        jQuery('#btnPause').on('click', function () {
            var status = jQuery('#controlButton').data('status');
            var timeRunning = jQuery('#timeTrackerTotalRunning').val();

            if(status == '' && !timeRunning){
                return;
            }
            var controlBtn=jQuery('#controlButton');
            var status=jQuery('#trackerStatus').val();

            if(status == 'running') {
                controlBtn.data('status','pause');
                jQuery('#trackerStatus').val('pause');
                controlBtn.data('status','pause');
                var buttonLbl=controlBtn.data('resume-label');
                controlBtn.html(buttonLbl);
                thisInstance.stopTimer(thisInstance.intervalTimer);
                thisInstance.stopTimer(thisInstance.intervalTimerSave);
                thisInstance.saveTimeTrackerData();
                jQuery('#controlButton').css('cursor','auto');
                jQuery('#startDateTime').css('cursor', 'not-allowed');
                jQuery('#startDateTime').prop('disabled', true);
                jQuery('#endDateTime').css('cursor', 'not-allowed');
                jQuery('#endDateTime').prop('disabled', true);
                jQuery('.propFieldInput').prop('disabled', true);
                jQuery('#activitytype').prop('disabled', true).trigger("liszt:updated");
                jQuery('.propSelectFieldInput').next().eventPause();
                jQuery('.propSelectFieldInput').next().find('a').addClass('grayBgProp');

                var saveUrl = window.location.href;
                saveUrl = saveUrl.replace('&go_back=1', '');
                window.history.pushState('', '', saveUrl);

            }else{
                return false;
            }
        });
    },

    registerEventForCancelButton: function () {
        var thisInstance=this;
        jQuery('#btnCancel').on('click', function () {

            var status = jQuery('#controlButton').data('status');
            if(status == ''){
                return;
            }
            thisInstance.stopTimer(thisInstance.intervalTimer);
            thisInstance.stopTimer(thisInstance.intervalTimerSave);
            var form = jQuery('form[name="TrackerForm"]');
            var record=form.find('[name="parentId"]').val();
            var params = {};
            params = form.serializeFormData();
            params.action = 'ActionAjax';
            params.module = 'TimeTracker';
            params.mode = 'cancelTimeTracker';
            params.record = record;
            app.request.post({data:params}).then(
                function (error,data){
                    if(error === null){
                        thisInstance.reloadPopupContent();
                    }
                }
            );

        });
    },

    registerEventForResumeButton: function() {
        var thisInstance=this;
        jQuery('#controlButton').on('click', function (){
            var status = jQuery('#trackerStatus').val();
            if(status == 'pause'){
                jQuery('#trackerStatus').val('running');
                jQuery('#trackerStatus').data('status','running');
                jQuery('#controlButton').css('cursor','not-allowed');
                jQuery('#controlButton').html('Complete');
                jQuery('#endDateTime').css('cursor','auto');
                jQuery('#endDateTime').prop('disabled', false);
                jQuery('#startDateTime').css('cursor','auto');
                jQuery('#startDateTime').prop('disabled', false);
                jQuery('.propFieldInput').prop('disabled', false);
                jQuery('#activitytype').prop('disabled', false).trigger("liszt:updated");
                jQuery('.propSelectFieldInput').next().eventPause('active');
                jQuery('.propSelectFieldInput').next().find('a').removeClass('grayBgProp');

                thisInstance.intervalTimer =  setInterval(function () {
                    thisInstance.timeTrackerTimer();
                }, 1000);
                thisInstance.intervalTimerSave =  setInterval(function () {
                    thisInstance.saveTimeTrackerData();
                }, 5000);
                jQuery('.timeTrackerTotal').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotal').val())));
            }
        });
    },

    timeTrackerTimer: function () {
        var val = parseInt(jQuery('#timeTrackerTotal').val());
        if(!val){
            val=0;
        }
        val = val + 1;
        jQuery('#timeTrackerTotal').val(val);
        jQuery('.timeTrackerTotal').html(TimeTracker_Js.formatTime(val));
    },

    stopTimer: function (func) {
        clearInterval(func);
    },

    getTranslator: function() {
        function Translator() {
            this.lang = null;

            this.translate = function(key) {
                if(this.lang[key] != undefined) {
                    return this.lang[key];
                }
            };

            this.loadLanguage = function() {
                var thisTrans = this;
                var params = {
                    module: 'TimeTracker',
                    action: 'ActionAjax',
                    mode: 'getJSLanguage',
                };

                app.request.post({data:params}).then(
                    function(error,data) {
                        if (error === null){
                            thisTrans.lang = data;
                        }
                    }
                );
            };
        }

        return new Translator();
    },

    registerEvents: function(){
        //Load language
        var thisInstance = this;
        this.translator = this.getTranslator();
        this.translator.loadLanguage();

        var container = jQuery('form.timeTrackerForm');
        this.registerEventForCommentButton();
        this.registerDateTimePicker(container);
        this.registerEventForControlButton();
        this.registerEventForPauseButton();
        this.registerEventForCancelButton();

        // Pre run
        if(jQuery('#timeTrackerTotalRunning').val() != undefined){
            thisInstance.intervalTimerHeader =  setInterval(function () {
                thisInstance.setTimerHeader();
            }, 1000);
            thisInstance.intervalTimerSaveHeader =  setInterval(function () {
                thisInstance.updateTimeTrackerTotal();
            }, 5000);
            jQuery('.timeTrackerTotalRunning').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotalRunning').val())));
        }else{
            jQuery(".timeTrackerTotalRunning").removeClass("timeTrackerTotalRunning").addClass("timeTrackerTotal");
        }
        if(jQuery('#trackerStatus').val() == 'running') {
            var listActiveTimerTable = jQuery('#listActiveTimers');
            var recordId = jQuery('form.timeTrackerForm').find('input[name="parentId"]').val();
            listActiveTimerTable.find('.timeValue').removeClass('timeTrackerTotal');
            listActiveTimerTable.find('#record_'+recordId+' .timeValue').addClass('timeTrackerTotal');
            jQuery('#startDateTime').css('cursor', 'auto');
            jQuery('#endDateTime').css('cursor', 'auto');
            thisInstance.intervalTimer =  setInterval(function () {
                thisInstance.timeTrackerTimer();
            }, 1000);
            thisInstance.intervalTimerSave =  setInterval(function () {
                thisInstance.saveTimeTrackerData();
            }, 5000);
            jQuery('.timeTrackerTotal').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotal').val())));
        }else{
            jQuery('#startDateTime').css('cursor', 'not-allowed');
            jQuery('#startDateTime').prop('disabled', true);
            jQuery('#endDateTime').css('cursor', 'not-allowed');
            jQuery('#endDateTime').prop('disabled', true);
            jQuery('.propFieldInput').prop('disabled', true);
            jQuery('#activitytype').prop('disabled', true).trigger("liszt:updated");
            jQuery('.propSelectFieldInput').next().eventPause();
            jQuery('.propSelectFieldInput').next().find('a').addClass('grayBgProp');
            //jQuery('.propSelectFieldInput').next().bind();
            jQuery('.timeTrackerTotal').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotal').val())));
        }
    },
    registerEventsForAll:function(){
        var thisInstance = this;
        var container = jQuery('form.timeTrackerForm');
        this.registerEventForControlButton();
        this.registerEventForPauseButton();
        //this.registerEventForResumeButton();
        this.translator = this.getTranslator();
        this.translator.loadLanguage();

        jQuery('#commentIcon').css('cursor','not-allowed');
        jQuery('#btnCancel').css('cursor','not-allowed');
        // Pre run
        if(jQuery('#timeTrackerTotalRunning').val() != undefined){
            thisInstance.intervalTimerHeader =  setInterval(function () {
                thisInstance.setTimerHeader();
            }, 1000);
            thisInstance.intervalTimerSaveHeader =  setInterval(function(){
                thisInstance.updateTimeTrackerTotal();
            }, 5000);

            jQuery('.timeTrackerTotalRunning').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotalRunning').val())));
        }else{
            jQuery(".timeTrackerTotalRunning").removeClass("timeTrackerTotalRunning").addClass("timeTrackerTotal");
        }
        if(jQuery('#trackerStatus').val() == 'running') {
            jQuery(".timeTrackerTotalRunning").removeClass("timeTrackerTotalRunning").addClass("timeTrackerTotal");
            //jQuery('#controlButton').css('cursor','not-allowed');
            thisInstance.intervalTimer =  setInterval(function () {
                thisInstance.timeTrackerTimer();
            }, 1000);
            thisInstance.intervalTimerSave =  setInterval(function () {
                thisInstance.saveTimeTrackerData();
            }, 5000);
            jQuery('.timeTrackerTotal').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotal').val())));
        }else{
            jQuery('.timeTrackerTotal').html(TimeTracker_Js.formatTime(parseInt(jQuery('#timeTrackerTotal').val())));
        }
    },
    updateTimeTrackerTotal : function(status){
        var thisInstance = this;
        var timeTrackerTotal = jQuery('#timeTrackerTotalRunning').val();
        var form = jQuery('form[name="TrackerForm"]');
        var params={};
        params = form.serializeFormData();
        if(status != undefined){
            params.status = status;
        }
        params.timeTrackerTotal = timeTrackerTotal;
        params.module = 'TimeTracker';
        params.action = 'ActionAjax';
        params.mode = 'updateTimeTrackerTotal';
        params.record_running = jQuery('#record_running').val();
        app.request.post({data:params}).then(
            function(error,data) {
                if(error !== null) {
                    if(typeof thisInstance.intervalTimer != 'undefined') {
                        thisInstance.stopTimer(thisInstance.intervalTimer);
                        thisInstance.stopTimer(thisInstance.intervalTimerSave);
                    }
                    if(typeof thisInstance.intervalTimerHeader != 'undefined') {
                        thisInstance.stopTimer(thisInstance.intervalTimerHeader);
                        thisInstance.stopTimer(thisInstance.intervalTimerSaveHeader);
                    }
                    document.location.reload();
                }
            }
        );
    },
    setTimerHeader: function() {
        var status = jQuery('#trackerStatus').val();
        if(jQuery('#timeTrackerTotalRunning').val() != undefined){
            var val = parseInt(jQuery('#timeTrackerTotalRunning').val());
        }else{
            var val = parseInt(jQuery('#timeTrackerTotal').val());
        }

        if(!val){
            val=0;
        }
        val = val + 1;
        jQuery('#timeTrackerTotalRunning').val(val);
        jQuery('.timeTrackerTotalRunning').html(TimeTracker_Js.formatTime(val));

    }
});

jQuery(document).ready(function() {
    setTimeout(function () {
        initData_TimeTracker();
    }, 5000);
});
function initData_TimeTracker() {
    var sPageURL = window.location.search.substring(1);
    var targetModule = '';
    var targetView = '';
    var targetRecord = '';
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++) {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == 'module') {
            targetModule = sParameterName[1];
        }
        else if (sParameterName[0] == 'view') {
            targetView = sParameterName[1];
        }
        else if (sParameterName[0] == 'record') {
            targetRecord = sParameterName[1];
        }else if(sParameterName[0] == 'go_back'){
            isGoBack = 1;
        }
    }
    // Check config
    var params = {};
    params.action = 'ActionAjax';
    params.module = 'TimeTracker';
    params.mode = 'getModuleConfig';
    //app.helper.showProgress();
    app.request.post({data:params}).then(
        function(error,dataSelectedModules) {
            if (error === null){
                if(jQuery.inArray(targetModule,dataSelectedModules) != -1 && targetView == 'Detail') {
                    var timeTrackerButtonHtml = '<li><button id="timeTrackerButton" type="button" data-pupup-visible="false" class="btn addButton btn-default module-buttons"><div class="fa fa-clock-o" aria-hidden="true"></div>&nbsp;&nbsp;<span class="recordLabel font-x-x-large pushDown timeTrackerTotalRunning" style="color: #2787e0;">00:00:00</span></button></li>';
                    jQuery('#appnav').find('ul.nav').prepend(timeTrackerButtonHtml);

                    // Get Time Tracker form
                    var params = {};
                    params.view = 'MassActionAjax';
                    params.module = 'TimeTracker';
                    params.mode = 'getTimeTrackerPopup';
                    params.record = targetRecord;
                    if (typeof isGoBack != 'undefined') {
                        params.isGoBack = isGoBack;
                    }
                    app.request.post({data:params}).then(
                        function (error, data) {
                            if (error === null){
                                var navHeight = jQuery('.navbar-inverse').height();
                                jQuery('body').append('<div id="timeTrackerPopup" style="border:2px solid #2787e0; display: none; position: fixed; right: 0px;  background: rgb(255, 255, 255) none repeat scroll 0% 0%; z-index: 1000; bottom: 0; top: ' + navHeight + 'px; overflow-y : scroll; ">' + data + '</div>');
                                vtUtils.applyFieldElementsView(jQuery('#timeTrackerPopup'));
                                if (typeof isGoBack != 'undefined' && isGoBack == 1) {
                                    var timeTrackerButon = jQuery('#timeTrackerButton');
                                    timeTrackerButon.data('popup-visible', 'true');
                                    jQuery('#timeTrackerPopup').show();
                                    var listActiveTimerTable = jQuery('#listActiveTimers');
                                    listActiveTimerTable.find('.timeValue').first().addClass('timeTrackerTotal');
                                }
                                var instance = new TimeTracker_Js();
                                instance.registerEventForTimeTrackerButton();
                                instance.registerEvents();
                            }
                        }
                    );
                }else {
                    var currentDiv = $("#appnav").parent();
                    var currentClass = currentDiv.attr("class");
                    if(currentClass!=undefined && currentClass!=''){
                        var newClass = currentClass.replace('col-lg-5 col-md-5', 'col-lg-7 col-md-7');
                        currentDiv.attr('class', newClass);
                        var prevDiv = currentDiv.prev();
                        currentClass = prevDiv.attr("class");
                        newClass = currentClass.replace('col-lg-7 col-md-7', 'col-lg-5 col-md-5');
                        prevDiv.attr('class', newClass);
                        var timeTrackerButtonHtml = '<li><button id="timeTrackerButton" type="button" data-pupup-visible="false" class="btn addButton btn-default module-buttons"><div class="fa fa-clock-o" aria-hidden="true"></div>&nbsp;&nbsp;<span class="recordLabel font-x-x-large pushDown timeTrackerTotalRunning" style="color: #2787e0;">00:00:00</span></button></li>';
                        jQuery('#appnav').find('ul.nav').prepend(timeTrackerButtonHtml);

                        // Get Time Tracker form
                        var params = {};
                        params.view = 'MassActionAjax';
                        params.module = 'TimeTracker';
                        params.mode = 'getTimeTrackerPopupForAll';

                        app.request.post({data:params}).then(
                            function (error, data) {
                                if (error === null) {
                                    var navHeight = jQuery('.navbar-inverse').height();
                                    jQuery('body').append('<div id="timeTrackerPopup" style="border:2px solid #2787e0; display: none; position: fixed; right: 0px;  background: rgb(255, 255, 255) none repeat scroll 0% 0%; z-index: 9999; bottom: 0; top: ' + navHeight + 'px; overflow-y : scroll; ">' + data + '</div>');
                                    vtUtils.applyFieldElementsView(jQuery('#timeTrackerPopup'));
                                    var instance = new TimeTracker_Js();
                                    instance.registerEventForTimeTrackerButton();
                                    instance.registerEventsForAll();
                                }
                            }
                        );
                    }
                }
            }else{

            }
            app.helper.hideProgress();
        }
    );

    // Change url of Activities Tab of Project and Project Task
    if(targetModule == 'Project' || targetModule == 'ProjectTask') {
        var detailViewContainer = jQuery('div.detailViewContainer');
        var ActivitiesTab = detailViewContainer.find('div.related li[data-label-key="Activities"]');
        var url="module=TimeTracker&view=Activities&record="+targetRecord+"&targetModule="+targetModule+"&tab_label=Activities";
        ActivitiesTab.data('url',url);
    }

    var interval = setInterval(function() {
        var userTime  = jQuery('#user_time').val();
        if(typeof  userTime != 'undefined' && userTime!='') {
            var res = userTime.split(" ");
            var hms = res[1];
            var a = hms.split(':');
            var seconds = 0;
            if (a.length == 2)
                seconds = parseInt(a[0]) * 60 * 60 + parseInt(a[1]) * 60 + 1;
            else
                seconds = parseInt(a[0]) * 60 * 60 + parseInt(a[1]) * 60 + parseInt(a[2]) + 1;

            jQuery('#user_time').val(res[0] + ' ' + TimeTracker_Js.formatTime(seconds));
        }

    }, 1000);
}

$(window).on('beforeunload', function() {
    var status=jQuery('#trackerStatus').val();
    if(status == 'running'){
        var instance = new TimeTracker_Js();
        instance.saveTimeTrackerData();
    }
    jQuery('.chzn-select').select2();
});