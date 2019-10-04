{*<!--
/* ********************************************************************************
* The content of this file is subject to the Multiple SMTP ("License");
* You may not use this file except in compliance with the License
* The Initial Developer of the Original Code is VTExperts.com
* Portions created by VTExperts.com. are Copyright(C) VTExperts.com.
* All Rights Reserved.
* ****************************************************************************** */
-->*}

{strip}
    <div class='modelContainer'>
        <div id="massEdit">
            <div class="modal-header contentsBackground">
                <button type="button" class="close " data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3>{vtranslate('LBL_OUTGOING_SERVER', 'Settings:Vtiger')}</h3>&nbsp;{vtranslate('LBL_OUTGOING_SERVER_DESC', 'Settings:Vtiger')}
            </div>
            <form class="form-horizontal" action="index.php" id="outgoingMassEditContainer">
                <input type="hidden" name="id" value="{$SERVER_INFO['id']}" />
                <input type="hidden" name="userid" value="{$USERID}" />
                <input type="hidden" name="sequence" value="{$SEQUENCE}" />
                <div class="row-fluid hide errorMessage">
                    <div class="alert alert-error">
                        {vtranslate('LBL_TESTMAILSTATUS', 'Settings:Vtiger')}<strong>{vtranslate('LBL_MAILSENDERROR', 'Settings:Vtiger')}</strong>
                    </div>
                </div>
                <div name='massEditContent' class="row-fluid">
                    <div class="modal-body">
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_SERVER_NAME', 'Settings:Vtiger')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="text" name="server" value="{$SERVER_INFO['server']}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_USER_NAME', 'Settings:Vtiger')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="text" name="server_username" value="{$SERVER_INFO['server_username']}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_PASSWORD', 'Settings:Vtiger')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="password" name="server_password" value="{$SERVER_INFO['server_password']}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_FROM_EMAIL', 'Settings:Vtiger')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="text" name="from_email_field" value="{$SERVER_INFO['from_email_field']}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_REPLYTO_EMAIL', 'MultipleSMTP')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="text" name="replyto_email_field" value="{$SERVER_INFO['replyto_email_field']}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_NAME', 'MultipleSMTP')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="text" name="name" value="{$SERVER_INFO['name']}"/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_REQUIRES_AUTHENTICATION', 'Settings:Vtiger')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                <input type="checkbox" name="smtp_auth" {if $SERVER_INFO['smtp_auth']}checked{/if}/>
                            </div>
                        </div>
                        <div class="control-group">
                            <label class="muted control-label">
                                &nbsp;<strong>{vtranslate('LBL_SAVE_TO_SEND_FOLDER', 'MultipleSMTP')}</strong>
                            </label>
                            <div class="controls row-fluid">
                                {if $SERVER_INFO['id'] ==''}
                                    <input type="checkbox" name="send_folder" value="1" checked />
                                {else}
                                    &nbsp;<input type="checkbox" name="send_folder" {if $SERVER_INFO['send_folder']}checked{/if}/>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right cancelLinkContainer" style="margin-top:0px;">
                        <a class="cancelLink" type="reset" data-dismiss="modal">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                    <button class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
                </div>
            </form>
        </div>
    </div>
{/strip}