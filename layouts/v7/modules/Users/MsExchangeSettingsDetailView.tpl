{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
{strip}
<form id="detailView" data-name-fields='{ZEND_JSON::encode($MODULE_MODEL->getNameFields())}' method="POST">
    <div class="contents">
	     <div class="block">
	        <div class="row">
	            <div class="col-xs-12 marginTop5px">
	                <div class=" pull-right detailViewButtoncontainer">
	                    <div class="btn-group  pull-right">
	                        <a class="btn btn-default" href="{$RECORD->getMsSettingsEditViewUrl()}">Edit</a>
	                    </div>  
	                </div>
	            </div>
	        </div>
        </div>
         <div class="block block_MS_Exchange" data-block="MS Exchange">

            <div class="row">
                <h4 class="col-xs-8">Calendar MS Exchange</h4>
                {if !empty($CALENDARSYNCDATA)}
                    <div class="col-xs-4 marginTop5px">
                        <div class=" pull-right detailViewButtoncontainer">
                            <div class="btn-group  pull-right">
                                <button class="btn btn-default revokeMSAccount" data-module="Calendar">Revoke Access</button>
                            </div>  
                        </div>
                    </div>
                {/if}
            </div>
            <hr>
            <div class="blockData row">
                <table class="table detailview-table no-border">
                    <tbody>
                        <tr>
		                	<td class="fieldLabel alignMiddle">
		                		User Principle Name 
		                	</td>
		                	<td class="fieldValue">
		                		{if !empty($CALENDARSYNCDATA)}{$CALENDARSYNCDATA['impersonation_identifier']}{/if}
		                	</td>
		                </tr>
		                <tr>
		                	<td class="fieldLabel alignMiddle">
		                		Sync Direction 
		                	</td>
		                	<td class="fieldValue">
	                			{if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['direction'] == "11"}Sync Both Ways{/if}
	                			{if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['direction'] == "10"}Sync from MS Exchange to CRM{/if}
	                			{if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['direction'] == "01"}Sync from CRM to MS Exchange{/if}
		                	</td>
		                </tr>
		                <tr>
		                	<td class="fieldLabel alignMiddle">
		                		Automatic Calendar Sync 
		                	</td>
		                	<td class="fieldValue">
		                		<input name="automatic_calendar_sync" disabled {if !empty($CALENDARSYNCDATA) && $CALENDARSYNCDATA['enable_cron']}checked{/if} type="checkbox" />
		                	</td>
		                </tr>
		                {if !empty($CALENDARSYNCDATA)}
		                	<tr>
		                		<td colspan="2" class="text-center">
		                			 <button type="button"  class="btn btn-success syncNow" data-module="Calendar">
		                			 	<i class="fa fa-refresh"></i> <span>Sync Now</span>
	                			 	</button>
		                		</tr>
		                	</tr>
		                {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <br>
      	<div class="block block_MS_Exchange" data-block="MS Exchange">

            <div class="row">
                <h4 class="col-xs-8">Task MS Exchange</h4>
                {if !empty($SYNCDATA)}
                    <div class="col-xs-4 marginTop5px">
                        <div class=" pull-right detailViewButtoncontainer">
                            <div class="btn-group  pull-right">
                                <button class="btn btn-default revokeMSAccount" data-module="Task" >Revoke Access</button>
                            </div>  
                        </div>
                    </div>
                {/if}
            </div>
            <hr>
            <div class="blockData row">
                <table class="table detailview-table no-border">
                    <tbody>
                        <tr>
		                	<td class="fieldLabel alignMiddle">
		                		User Principle Name 
		                	</td>
		                	<td class="fieldValue">
		                		{if !empty($SYNCDATA)}{$SYNCDATA['impersonation_identifier']}{/if}
		                	</td>
		                </tr>
		                <tr>
		                	<td class="fieldLabel alignMiddle">
		                		Sync Direction 
		                	</td>
		                	<td class="fieldValue">
	                			{if !empty($SYNCDATA) && $SYNCDATA['direction'] == "11"}Sync Both Ways{/if}
	                			{if !empty($SYNCDATA) && $SYNCDATA['direction'] == "10"}Sync from MS Exchange to CRM{/if}
	                			{if !empty($SYNCDATA) && $SYNCDATA['direction'] == "01"}Sync from CRM to MS Exchange{/if}
		                	</td>
		                </tr>
		                <tr>
		                	<td class="fieldLabel alignMiddle">
		                		Automatic Task Sync 
		                	</td>
		                	<td class="fieldValue">
		                		<input name="automatic_calendar_sync" disabled {if !empty($SYNCDATA) && $SYNCDATA['enable_cron']}checked{/if} type="checkbox" />
		                	</td>
		                </tr>
		                {if !empty($SYNCDATA)}
		                	<tr>
		                		<td colspan="2" class="text-center">
		                			 <button type="button"  class="btn btn-success syncNow" data-module="Task">
		                			 	<i class="fa fa-refresh"></i> <span>Sync Now</span>
	                			 	</button>
		                		</tr>
		                	</tr>
		                {/if}
                    </tbody>
                </table>
            </div>
        </div>
        <br>
        
        <div class="block block_MS_Exchange" data-block="MS Exchange">

            <div class="row">
                <h4 class="col-xs-8">Contact MS Exchange</h4>
                {if !empty($CONTACTSYNCDATA)}
                    <div class="col-xs-4 marginTop5px">
                        <div class=" pull-right detailViewButtoncontainer">
                            <div class="btn-group  pull-right">
                                <button class="btn btn-default revokeMSAccount" data-module="Contacts">Revoke Access</button>
                            </div>  
                        </div>
                    </div>
                {/if}
            </div>
            <hr>
            <div class="blockData row">
                <table class="table detailview-table no-border">
                    <tbody>
                        <tr>
		                	<td class="fieldLabel alignMiddle">
		                		User Principle Name 
		                	</td>
		                	<td class="fieldValue">
		                		{if !empty($CONTACTSYNCDATA)}{$CONTACTSYNCDATA['impersonation_identifier']}{/if}
		                	</td>
		                </tr>
		                <tr>
		                	<td class="fieldLabel alignMiddle">
		                		Sync Direction 
		                	</td>
		                	<td class="fieldValue">
	                			{if !empty($CONTACTSYNCDATA) && $CONTACTSYNCDATA['direction'] == "11"}Sync Both Ways{/if}
	                			{if !empty($CONTACTSYNCDATA) && $CONTACTSYNCDATA['direction'] == "10"}Sync from MS Exchange to CRM{/if}
	                			{if !empty($CONTACTSYNCDATA) && $CONTACTSYNCDATA['direction'] == "01"}Sync from CRM to MS Exchange{/if}
		                	</td>
		                </tr>
		               
		                {if !empty($CONTACTSYNCDATA)}
		                	<tr>
		                		<td colspan="2" class="text-center">
		                			 <button type="button"  class="btn btn-success syncNow" data-module="Contacts">
		                			 	<i class="fa fa-refresh"></i> <span>Sync Now</span>
	                			 	</button>
		                		</tr>
		                	</tr>
		                {/if}
                    </tbody>
                </table>
            </div>
        </div>
    <br>
{/strip}