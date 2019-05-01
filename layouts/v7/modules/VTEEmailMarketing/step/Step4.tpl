<div class="editViewPageDiv viewContent">
    <div class="editViewContents">
        <div name='editContent'>
            <div class='fieldBlockContainer'>
                <br/>
                <h3 class='fieldBlockHeader'>{vtranslate('Review & Send', $MODULE)}</h3>
                <hr>
                <table class="table" id="table-reviewsend">
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            <h4>{vtranslate('Campaign Name', $MODULE)}</h4>
                        </td>
                        <td class="fieldValue" id="campaignName">

                        </td>
                        <td class="fieldValue">
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            <h4>{vtranslate('Sender', $MODULE)}</h4>
                        </td>
                        <td class="fieldValue" id="sender">

                        </td>
                        <td class="fieldValue">
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            <h4>{vtranslate('List', $MODULE)}</h4>
                        </td>
                        <td class="fieldValue" >
                            <table class="table-List" id="list">
                                {if $RECORD_VTE_CAMPAIGNS['countLeads'] neq 0}
                                    <tr>
                                        <td>{vtranslate('LBL_LEADS', $MODULE)}</td>
                                        <td>{$RECORD_VTE_CAMPAIGNS['countLeads']}</td>
                                    </tr>
                                {/if}
                                {if $RECORD_VTE_CAMPAIGNS['countOrganization'] neq 0}
                                    <tr>
                                        <td>{vtranslate('LBL_ACCOUNTS', $MODULE)}</td>
                                        <td>{$RECORD_VTE_CAMPAIGNS['countOrganization']}</td>
                                    </tr>
                                {/if}
                                {if $RECORD_VTE_CAMPAIGNS['countContacts'] neq 0}
                                    <tr>
                                        <td>{vtranslate('LBL_CONTACTS', $MODULE)}</td>
                                        <td>{$RECORD_VTE_CAMPAIGNS['countContacts']}</td>
                                    </tr>
                                {/if}
                            </table>
                        </td>
                        <td class="fieldValue"  id="previewDetailVTECampaign">
                            <button class="btn btn-default" id="PreviewRecord">Preview</button>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldLabel alignMiddle">
                            <h4>{vtranslate('Subject', $MODULE)}</h4>
                        </td>
                        <td class="fieldValue" id="subject">

                        </td>
                        <td class="fieldValue">
                            <button class="btn btn-default" id="PreviewEmailTemplate">Preview</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


    <div class=""
         style=" position: fixed; width: 100%; left:0; bottom:0; background: #f0eef0; padding-top: 15px; padding-bottom: 15px; text-align: center;">
        <button style="margin-right:3px;" class="btn btn-info" name="TestEmail" type="button"><strong>Test
                Email</strong></button>
        <button style="margin-right:3px;" class="btn btn-success" name="ScheduleLater" type="button"><strong>Schedule
                Later</strong></button>
        <button style="margin-right:3px;" class="btn btn-success" name="SendNow" type="button"><strong>Send
                Now </strong></button>
        <button style="color:#ff4c42;" class="btn btn-link btnCancel" name="btnCancel" type="button">
            <strong>Cancel</strong></button>
    </div>

{include file="step/Step4Modal.tpl"|vtemplate_path:$MODULE}
