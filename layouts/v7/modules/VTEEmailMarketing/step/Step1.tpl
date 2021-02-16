<form id="formStep1" style="margin-bottom: 100px;">
<div class="editViewPageDiv viewContent">
    <div class="editViewContents">
        <div name='editContent'>
            <div class='fieldBlockContainer'>
                    <br/>
                    <h3 class='fieldBlockHeader'>{vtranslate('Campaign Details', $MODULE)}</h3>
                    <hr>
                    <table class="table table-borderless" id="table-vtecampaign" style="width: 80% ; margin : 0 auto">
                        <tr>
                            <td class="fieldLabel alignMiddle">
                                <h5 class="pull-right">{vtranslate('Campaign Name', $MODULE)}
                                    <span class="redColor">*</span></h5>
                            </td>
                            <td class="fieldValue">
                                <input type="text" name="vtecampaign_name" class="inputElement" data-rule-required="true" value="{$RECORDMODEL->get('vtecampaigns')}">
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle" >
                                <h5 class="pull-right">{vtranslate('Email/SMTP Server', $MODULE)}
                                    <span class="redColor">*</span></h5>
                            </td>
                            <td class="fieldValue">
                                <select class="select2 inputElement" name="from_serveremailid" tabindex="-1" data-rule-required="true" >
                                    <option value=""></option>
                                   {if $LIST_SERVERS}
                                        {foreach from=$LIST_SERVERS item=record_smtp}
                                            <option value="{$record_smtp['account_id']}" {if $record_smtp['default'] == 0} selected {/if}>{$record_smtp['account_name']} </option>
                                        {/foreach}
                                    {/if}
                                    
                            </td>
                        </tr>
                        {*<tr>
                            <td class="fieldLabel alignMiddle">
                            </td>
                            <td class="fieldValue">
                                <div class="alert alert-info" id="tip_config_email" style="width: 80%">
                                    <span><b>System Outgoing Email Server</b>: Configured outgoing email server (SMTP) will be used. <u><a style="cursor: pointer;" target="_blank" href="index.php?parent=Settings&module=Vtiger&view=OutgoingServerDetail&block=8&fieldid=15">Click here to view/update</a></u>.</span>
                                    <br/>
                                    <span><b>User Outgoing Email Server</b>: Emails can be sent from different email/SMTP servers. If you can't see any options in the selection, please install <u><a style="cursor: pointer" target="_blank" href="https://www.vtexperts.com/product/vtiger-outgoing-email-server">Individual Outgoing Email Server Extension</a></u> and add email/SMTP server to a user.</span>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="fieldLabel alignMiddle">
                                <h5 class="pull-right">{vtranslate('From Name', $MODULE)} <span
                                            class="redColor">*</span></h5>
                            </td>
                            <td class="fieldValue">
                                <input type="text" name="vtefrom_name" class="inputElement" data-rule-required="true" value="{$FROMEMAILNAME[0]}">
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">
                                <h5 class="pull-right">{vtranslate('From Email', $MODULE)} <span
                                            class="redColor">*</span></h5>
                            </td>
                            <td class="fieldValue">
                                <input type="email" name="vtefrom_email" style="padding: 3px 8px" class="inputElement" data-rule-required="true" value="{str_replace(')','',$FROMEMAILNAME[1])}" >
                            </td>
                        </tr>*}
                        <tr>
                            <td class="fieldLabel alignMiddle">
                                <h5 class="pull-right">{vtranslate('Assigned To', $MODULE)} <span
                                            class="redColor">*</span></h5>
                            </td>
                            <td class="fieldValue">
                                {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
                                    {if $BLOCK_FIELDS|@count gt 0}
                                        {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                                            {if $FIELD_MODEL->get('uitype') eq 53}
                                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                                            {/if}
                                        {/foreach}
                                    {/if}
                                {/foreach}
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldLabel alignMiddle">
                                <h5 class="pull-right">{vtranslate('Select Template', $MODULE)} <span class="redColor">*</span></h5>
                            </td>
                            <td class="fieldValue">
                            	<div class="input-group">
	                            	<select class="select2 inputElement select_email_template" id="select_email_template" name="templateEmail" data-rule-required="true" style="width:100% !important;"> 
		                                <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
		                                {foreach item=EMAIL_TEMPLATE key=ETEMPLATEID from=$EMAIL_TEMPLATES}
											<option value="{$ETEMPLATEID}">{$EMAIL_TEMPLATE}</option>
										{/foreach}
	                                </select>
	                                <span class="input-group-addon" title="Select">
	                                	<a target="_blank" href="index.php?module=EmailTemplates&view=Edit">
	                                		<i class="fa fa-plus"></i>
                                		</a>
	                                </span>
	                                <span class="input-group-addon" title="Select"><i class="fa fa-refresh refreshEmailTemplateDD" title="Refresh"></i></span>
                                </div>
                                &nbsp;&nbsp;<button class="btn btn-default hide" id="PreviewEmailTemplate" onclick="event.preventDefault();">Preview</button>
                            </td>
                        </tr>
                    </table>
            </div>
        </div>
    </div>
</div>
<div class=""
     style=" position: fixed; width: 100%; left:0; bottom:0; background: #f0eef0; padding-top: 15px; padding-bottom: 15px; text-align: center;">
    <button style="margin-right:3px;" class="btn btn-success btnNext" name="btnNext" id="saveVTECampaign" type="submit">
        <strong>Next</strong></button>
    <button style="color:#ff4c42;" class="btn btn-link btnCancel" name="btnCancel" type="button"><strong>Cancel</strong>
    </button>
</div>
</form>


