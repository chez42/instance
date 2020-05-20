{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}

<div class="editViewPageDiv row">
    <div class="col-sm-12 col-xs-12">
        <form class="form-horizontal recordEditView MsExchangeSettingEditView" id="EditView" name="EditView" method="post" action="index.php" enctype="multipart/form-data">
            <div class="editViewBody">
                <div class="editViewContents">
                    {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                    {assign var=QUALIFIED_MODULE_NAME value={$MODULE}}
                    {assign var=IS_PARENT_EXISTS value=strpos($MODULE,":")}
                    {if $IS_PARENT_EXISTS}
                        {assign var=SPLITTED_MODULE value=":"|explode:$MODULE}
                        <input type="hidden" name="module" value="{$SPLITTED_MODULE[1]}" />
                        <input type="hidden" name="parent" value="{$SPLITTED_MODULE[0]}" />
                    {else}
                        <input type="hidden" name="module" value="{$MODULE}" />
                    {/if}
                    <input type="hidden" name="action" value="MsExchangeSave" />
                    <input type="hidden" name="record" value="{$RECORD->getId()}" />
                    <input type="hidden" name="mode" value="MsExchangeSetting" />
                    
                   <div name='editContent'>
						<div class='fieldBlockContainer'>
				            <h4 class='fieldBlockHeader' >MS Exchange Settings</h4>
				            <hr>
				            <table class="table table-borderless">
				                <tr>
				                	<td class="fieldLabel alignMiddle">
				                		User Principle Name 
				                	</td>
				                	<td class="fieldValue">
				                		<input type="text" name="user_principal_name" class="form-control" value="{if $PRINCIPAL}{$PRINCIPAL}{/if}">
				                	</td>
				                </tr>
				                <tr>
				           			<td colspan="2">
								         <div class='fieldBlockContainer'>
								            <h5 class='fieldBlockHeader' >Calendar MS Exchange</h5>
								            <hr>
								            <table class="table table-borderless">
								                <tr>
								                	<td class="fieldLabel alignMiddle">
								                		Sync Direction 
								                	</td>
								                	<td class="fieldValue">
								                		<select name="sync_direction" class="form-control select2">
								                			<option value="" >Select an option</option>
								                			<option value="11" {if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['direction'] == "11"}selected{/if}> Sync Both Ways </option>
								                			<option value="10" {if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['direction'] == "10"}selected{/if}> Sync from MS Exchange to CRM </option>
								                			<option value="01" {if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['direction'] == "01"}selected{/if}> Sync from CRM to MS Exchange </option>
								                		</select>
								                	</td>
								                </tr>
								                <tr>
								                	<td class="fieldLabel alignMiddle">
								                		Automatic Calendar Sync 
								                	</td>
								                	<td class="fieldValue">
								                		<input name="automatic_calendar_sync" {if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['enable_cron']}checked{/if} type="checkbox" />
								                	</td>
								                </tr>
								                {if !$CALENDARSYNCDATA['sync_start_from']}
									                <tr>
									                	<td class="fieldLabel alignMiddle">Sync Start From</td>
								                        <td class="fieldValue">
								                        	<div class="input-group">
								                        		<input type="text" name="calendar_sync_start_from" class="dateField form-control m-input " data-rule-required="true"  data-rule-date="true" data-date-format="{$CURRENTUSER_MODEL->get('date_format')}" data-fieldtype="date" value="{if $CALENDARSYNCDATA['sync_start_from']}{date('m-d-Y', strtotime($CALENDARSYNCDATA['sync_start_from']))}{else}{date('m-d-Y', strtotime("-1 month"))}{/if}"/>
								                        		<div class="input-group-append input-group-addon">
																	<span class="input-group-text ">
																		<i class="fa fa-calendar "></i>
																	</span>
																</div>
															</div>
														</td>
									                </tr>
								                {/if}
								            </table>
								        </div>
							        </td>
						        </tr>
						        <tr>
					       			<td colspan="2">
								         <div class='fieldBlockContainer'>
								            <h5 class='fieldBlockHeader' >Task MS Exchange</h5>
								            <hr>
								            <table class="table table-borderless">
								                <tr>
								                	<td class="fieldLabel alignMiddle">
								                		Sync Direction 
								                	</td>
								                	<td class="fieldValue">
								                		<select name="task_sync_direction" class="form-control select2">
								                			<option value="" >Select an option</option>
								                			<option value="11" {if !empty($SYNCDATA) && $SYNCDATA['direction'] == "11"}selected{/if}> Sync Both Ways </option>
								                			<option value="10" {if !empty($SYNCDATA) && $SYNCDATA['direction'] == "10"}selected{/if}> Sync from MS Exchange to CRM </option>
								                			<option value="01" {if !empty($SYNCDATA) && $SYNCDATA['direction'] == "01"}selected{/if}> Sync from CRM to MS Exchange </option>
								                		</select>
								                	</td>
								                </tr>
								                <tr>
								                	<td class="fieldLabel alignMiddle">
								                		Automatic Task Sync 
								                	</td>
								                	<td class="fieldValue">
								                		<input name="automatic_task_sync" {if !empty($SYNCDATA) && $SYNCDATA['enable_cron']}checked{/if} type="checkbox" />
								                	</td>
								                </tr>
								                {if !$SYNCDATA['sync_start_from']}
									                <tr>
									                	<td class="fieldLabel alignMiddle">Sync Start From</td>
								                        <td class="fieldValue">
								                        	<div class="input-group">
								                        		<input type="text" name="task_sync_start_from" class="dateField form-control m-input " data-rule-required="true"  data-rule-date="true" data-date-format="{$CURRENTUSER_MODEL->get('date_format')}" data-fieldtype="date" value="{if $SYNCDATA['sync_start_from']}{date('m-d-Y', strtotime($SYNCDATA['sync_start_from']))}{else}{date('m-d-Y', strtotime("-1 month"))}{/if}"/>
								                        		<div class="input-group-append input-group-addon">
																	<span class="input-group-text ">
																		<i class="fa fa-calendar "></i>
																	</span>
																</div>
															</div>
														</td>
									                </tr>
								                {/if}
								            </table>
								        </div>
							        </td>
						        </tr>
						        <tr>
					        		<td colspan="2">
								         <div class='fieldBlockContainer'>
								            <h5 class='fieldBlockHeader' >Contact MS Exchange</h5>
								            <hr>
								            <table class="table table-borderless">
								                <tr>
								                	<td class="fieldLabel alignMiddle">
								                		Sync Direction 
								                	</td>
								                	<td class="fieldValue">
								                		<select name="contact_sync_direction" class="form-control select2">
								                			<option value="" >Select an option</option>
								                			<option value="11" {if !empty($CONTACTSYNCDATA) && $CONTACTSYNCDATA['direction'] == "11"}selected{/if}> Sync Both Ways </option>
								                			<option value="10" {if !empty($CONTACTSYNCDATA) && $CONTACTSYNCDATA['direction'] == "10"}selected{/if}> Sync from MS Exchange to CRM </option>
								                			<option value="01" {if !empty($CONTACTSYNCDATA) && $CONTACTSYNCDATA['direction'] == "01"}selected{/if}> Sync from CRM to MS Exchange </option>
								                		</select>
								                	</td>
								                </tr>
								                {* <tr>
								                	<td class="fieldLabel alignMiddle">Sync Start From</td>
							                        <td class="fieldValue">
							                        	<div class="input-group">
							                        		<input type="text" name="contact_sync_start_from" class="dateField form-control m-input " data-rule-required="true"  data-rule-date="true" data-date-format="{$CURRENTUSER_MODEL->get('date_format')}" data-fieldtype="date" value="{if $CONTACTSYNCDATA['sync_start_from']}{date('m-d-Y', strtotime($CONTACTSYNCDATA['sync_start_from']))}{else}{date('m-d-Y', strtotime("-1 month"))}{/if}"/>
							                        		<div class="input-group-append input-group-addon">
																<span class="input-group-text ">
																	<i class="fa fa-calendar "></i>
																</span>
															</div>
														</div>
													</td>
								                </tr> *}
								            </table>
								        </div>
							        </td>
						        </tr>
			                </table>
			            </div>
                   </div>
                </div>
            </div>
            <div class='modal-overlay-footer clearfix'>
                <div class="row clearfix">
                    <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                        <button type='submit' class='btn btn-success saveButton MsExchangeSettingEditViewSave' type="submit" >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                        <a class='cancelLink'  href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>