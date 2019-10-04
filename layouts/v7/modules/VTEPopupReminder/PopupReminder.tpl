<!-- Large modal -->
<!-- Modal -->
<style>
    .ellipsis{
        display: -webkit-box;
        max-width: 200px;
        max-height: 50px;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0px;
    }
    .subject{
        width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .popup-reminder tr th{
        text-align: center;
    }

</style>

<div class="popupReminderContainer">
    <div class="modal fade" id="PopupReminder" role="dialog" data-info="{$ACTIVES}" style="z-index: 1090">
        <div class="modal-dialog" style="width: 1100px">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Activity Reminder</h4>
                </div>
                <div class="modal-body" style="overflow: scroll; max-height: 400px">
                    <table class="table table-bordered popup-reminder">
                        <thead>
                        <tr>
                            <th><input name="checkAll" type="checkbox"></th>
                            <th>Subject</th>
                            <th>Activity Type</th>
                            <th>Description</th>
                            <th>Related To</th>
                            <th style="width: 140px">Starts At</th>
                            <th style="width: 110px">Due In</th>
                            <th style="width: 65px;">Action</th>
                            <th>Snooze</th>
                        </tr>
                        </thead>
                        <tbody>
                        {foreach from = $LISTACTIVITY item =RECORD key=INDEX}
                            <tr data-info="{$RECORD['activityid']}">
                                <td><input type="checkbox" name="cbx_{$INDEX}"></td>
                                <td style="color: #15c"><a href="{$RECORD['url']}" target="_blank"><p class="subject">{$RECORD['subject']}</p></a></td>
                                <td>{$RECORD['activitytype']}</td>
                                <td>
                                    <p class="ellipsis">
                                        {$RECORD['description']}
                                    </p>
                                </td>
                                <td style="color: #15c">
                                        {foreach item=CONTACT_INFO from=$RECORD['contacts']}
                                            <a href='{$CONTACT_INFO['_model']->getDetailViewUrl()}'
                                               title='{vtranslate("Contacts", "Contacts")}'> {Vtiger_Util_Helper::getRecordName($CONTACT_INFO['id'])}</a>
                                            <br>
                                        {/foreach}
                                    {$RECORD['relatedto']}
                                </td>
                                <td style="">{$RECORD['startsat']}</td>
                                <td {if $RECORD['isred'] eq 'red'}style="color: red" {elseif $RECORD['isred'] eq 'green'}style="color: green"{else} style="color: black"{/if}>{$RECORD['duein']}</td>
                                <td>
                                    <span class="pull-right cursorPointer deleteCalendarEvent" data-module="{$RECORD['module']}" data-id = "{$RECORD['activityid']}"
                                          {*onclick="Calendar_Calendar_Js.deleteCalendarEvent({$RECORD['activityid']},'Events',false);"*}
                                          title="Delete">&nbsp;&nbsp;<i class="fa fa-trash"></i></span>
                                    <span class="pull-right cursorPointer editCalendarEvent" data-module="{$RECORD['module']}" data-id = "{$RECORD['activityid']}"
                                                      {*onclick="VTEPopupReminderJS.editCalendarEvent({$RECORD['activityid']},false);"*}
                                                      title="Edit">&nbsp;&nbsp;<i class="fa fa-pencil"></i></span>
                                    <span class="pull-right cursorPointer markAsHeld" data-module="{$RECORD['module']}" data-id = "{$RECORD['activityid']}"
                                            {*onclick="Calendar_Calendar_Js.markAsHeld({$RECORD['activityid']});" *}
                                          title="Mark as held"><i class="fa fa-check"></i></span>
                                </td>
                                <td>
                                    <select name="snooze" class="select2" style="width: 105px; height: 25px">
                                        {foreach from = $SNOOZE item = VALUE }
                                            <option {if $VALUE eq 'Select options'}value="default"
                                                    selected{else}value="{$VALUE}"{/if}>{$VALUE}</option>
                                        {/foreach}
                                    </select>
                                    <span class="glyphicon glyphicon-ok" style="color: green; display: none"></span>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <div style="float: left">
                        <select name="setallSnooze" style="width: 150px; height: 30px">
                            {foreach from = $SNOOZE item = VALUE }
                                <option {if $VALUE eq 'Select options'} value="default" selected{else}value="{$VALUE}"{/if}>{$VALUE}</option>
                            {/foreach}
                        </select>
                        <button name="btn-setallSnooze" class="btn btn-primary">Snooze</button>
                    </div>
                    <div style="float: left">
                        <div style="margin-left: 60px;margin-top: 10px;">
                            <p style="float: left;font-size: 12px; font-weight: 600;">Snooze all for:</p>
                            <button name="btn-SnoozeValue" style="float: left; margin-left: 20px;text-decoration: underline!important;color: #15c;border: none; background-color: #efefef"  value="30 Minutes">30 Min</button>
                            <button name="btn-SnoozeValue" style="float: left; margin-left: 20px;text-decoration: underline!important;color: #15c;border: none; background-color: #efefef"  value="1 Hour">1 Hours</button>
                            <button name="btn-SnoozeValue" style="float: left; margin-left: 20px;text-decoration: underline!important;color: #15c;border: none; background-color: #efefef"  value="3 Hour">3 Hours</button>
                            <button name="btn-SnoozeValue" style="float: left; margin-left: 20px;text-decoration: underline!important;color: #15c;border: none; background-color: #efefef"  value="1 Day">1 Day</button>
                        </div>

                    </div>
                    <button type="button" class="btn btn-warning" data-dismiss="modal" style="float: right">Dismiss
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>