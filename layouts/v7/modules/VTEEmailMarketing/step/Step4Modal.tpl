{*-- Modal Record VTECampaigns --*}
<div id="ModalPreviewRecord" class="fc-overlay-modal modal-content hide" style="max-height: 550px;">
    <div class="overlayHeader">
        <div class="modal-header">
            <div class="clearfix">
                <div class="pull-right ">
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                        <span aria-hidden="true" class='fa fa-close'></span>
                    </button>
                </div>
            </div>
            <div class="clearfix marginTop10px">
                <div class="col-md-8"><h4>{vtranslate('LBL_RECORD_ON_VTECAMPAINGS',$MODULE)}</h4></div>
                <div class="col-md-4">
                    <div class="listViewActions">
                        <div class="btn-group pull-right">
                            <button type="button" id="PreviousPageButtonCampains" class="btn btn-default" disabled=""><i class="fa fa-caret-left"></i></button>
                            <button type="button" id="PageJumpCampains"class="btn btn-default">
                                <i class="fa fa-ellipsis-h icon" title="Page Jump"></i>
                            </button>
                            <ul class="listViewBasicAction dropdown-menu" id="PageJumpDropDownCampains">
                                <li>
                                    <div class="listview-pagenum">
                                        <span>Page</span>
                                        <strong><span class="pageCurrent">1</span></strong>
                                        <span>of</span>
                                        <strong><span id="totalPageCount"></span></strong>
                                    </div>
                                    <div class="listview-pagejump">
                                        <input type="text" id="pageToJumpCampains" placeholder="Jump To" class="listViewPagingInput text-center">
                                        <button type="button" id="pageToJumpSubmitCampains" class="btn btn-success listViewPagingInputSubmit text-center">GO</button>
                                    </div>
                                </li>
                            </ul>
                            <button type="button" id="NextPageButton" class="btn btn-default" disabled=""><i class="fa fa-caret-right"></i></button>
                        </div>
                        <span class="pageNumbers  pull-right" style="position:relative;top:7px;">
                            <span class="pageNumbersText">1 to 1</span>
                            <span class="totalNumberOfRecords cursorPointer" title="Click for this list size">
                                of 1
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <hr/>
        </div>
    </div>
    <div class='modal-body'>
        <div class='datacontent container-fluid' >
            <table class="table table-fixed" id="table-preview-record">
                <thead>
                <tr>
                    <th style="width: 30%">Module</th>
                    <th style="width: 34%">Name</th>
                    <th style="width: 34%">Email</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>


{*-- End Modal Record VTECampaigns --*}

{* -- Modal Test Email --*}
<div class="modal hide TestEmail" id="modalTestEmail" role="dialog" style="display: block; padding-left: 15px">
    <form class="form-horizontal" id="massEmailForm" method="post" action="index.php" enctype="multipart/form-data"
          name="massEmailForm">
        <div class="modal-dialog">
            <div class="modal-content" style="margin-top: 27%;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{vtranslate('LBL_TESTEMAIL', $MODULE)}</h4>
                </div>
                <div class="modal-body">
                    <table class="table table-borderless" style="width: 80%; margin-left: 10%">
                        <tr>
                            <td class="fieldLabel alignMiddle" width="25%">
                                <h4 class="pull-right">{vtranslate('LBL_EMAIL', $MODULE)}
                                    <span class="redColor">*</span></h4>
                            </td>
                            <td class="fieldValue">
                                <input type="email" class="inputElement" style="width: 80%;margin-top: 5px"
                                       data-rule-required="true" name="to">
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <div style="margin-right: 37%">
                        <button style="margin-right:3px;" class="btn btn-success" name="btnSendTestEmail" id="sendEmail"
                                type="submit"><strong>Send</strong></button>
                        <button style="color:#ff4c42;" class="btn btn-link btnCancel" data-dismiss="modal"
                                type="button"><strong>Cancel</strong></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{* -- End Modal Test Email --*}

{* -- Modal Schedule Later --*}
<div class="modal hide ScheduleLater" id="modalScheduleLater" role="dialog" style="display: block; padding-left: 15px">
    <form id="formScheduleLater">
        <input type="hidden" name="schedule_batch_delivery" value="">
        <div class="modal-dialog">
            <div class="modal-content" style="margin-top: 16%;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h4 class="modal-title">{vtranslate('LBL_SETUP_SCHEDULE', $MODULE)}</h4>
                </div>
                <div class="modal-body">
                    <div id="schedule-error">

                    </div>
                    <div class="schedule-body" style="width: 60%; margin-left: 25%">
                        <h5 class="text-label">{vtranslate('LBL_DELIVERY_DATE', $MODULE)}</h5>
                        <div class="input-group inputElement schedule-date" style="margin-bottom: 3px">
                            <input type="text" class="dateField form-control" name="schedule_date"
                                   data-date-format="dd-mm-yyyy" value="{$CURRENT_DATE_USER}" data-rule-required="true"
                                   data-rule-date="true" aria-required="true" aria-invalid="false">
                            <span class="input-group-addon"><i class="fa fa-calendar "></i></span>
                        </div>
                        <div class="input-group inputElement time schedule-time">
                            <input type="text" data-format="12" name="schedule_time";
                                   class="timepicker-default form-control ui-timepicker-input"
                                   value="{$CURRENT_TIME_USER}" data-rule-required="true" data-rule-time="true" autocomplete="off"
                                   aria-required="true" aria-invalid="false">
                            <span class="input-group-addon" style="width: 30px;"><i class="fa fa-clock-o"></i></span>
                        </div>

                        <div class="schedule-batch-delivery">
                            <h5 class="pull-left text-label">{vtranslate('LBL_BATCH_DELIVERY', $MODULE)}
                                (<strong style="color:red" id="total"></strong>)
                            </h5>
                                <button type="button" id="btn-On-Off" class="btn btn-sm btn-toggle"
                                        data-toggle="button" aria-pressed="false" autocomplete="off">
                                    <div class="handle"></div>
                                </button>
                        </div>
                        <div class="schedule-config-send">
                            Send
                            <input type="text" name="schedule_number_email" class="inputElement" style="width: 25%"
                                   onkeypress='return event.charCode >= 48 && event.charCode <= 57' value="">
                            Email Every
                            <select class="inputElement" name="schedule_frequency" style="width: 35%">
                                {$min = 15}
                                {for $i=1 ; $i<9 ; $i++}
                                    <option value="{($min*60)*$i}">{$min*$i} Minutes</option>
                                {/for}
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div style="margin-right: 40%">
                        <button style="margin-right:3px;" class="btn btn-success" id="saveScheduleLater"
                                type="button"><strong>Save</strong></button>
                        <button style="color:#ff4c42;" class="btn btn-link btnCancel" data-dismiss="modal"
                                type="button"><strong>Cancel</strong></button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
{* -- End Modal Schedule Later --*}

