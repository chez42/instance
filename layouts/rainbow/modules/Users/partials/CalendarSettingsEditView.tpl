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
    {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
        <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
    {/if}
    <div name='editContent'>
        {foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE name=blockIterator}
            {if $BLOCK_FIELDS|@count gt 0}
                <div class='fieldBlockContainer'>
                    <h4 class='fieldBlockHeader' >{vtranslate($BLOCK_LABEL, $MODULE)}</h4>
                    <hr>
                    <div class="table table-borderless">
                        <div class="row">
                            {assign var=COUNTER value=0}
                            {foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS name=blockfields}
                                {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                                {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                                {assign var="refrenceListCount" value=count($refrenceList)}
                                 {if $FIELD_MODEL->getName() eq 'appointment_url'}
                                	{continue}
                            	{/if}
                                {if $FIELD_MODEL->isEditable() eq true}
                                    {if $FIELD_MODEL->get('uitype') eq "19"}
                                        {if $COUNTER eq '1'}
                                            <div class="col-xs-12 col-md-3 "></div><div class="col-xs-12 col-md-3"></div></div><div class="row">
                                            {assign var=COUNTER value=0}
                                        {/if}
                                    {/if}
                                    {if $COUNTER eq 2}
                                    </div><div class="row">
                                        {assign var=COUNTER value=1}
                                    {else}
                                        {assign var=COUNTER value=$COUNTER+1}
                                    {/if}
                                    <div class="fieldLabel col-xs-12 col-md-3 alignMiddle">
                                        {if $isReferenceField eq "reference"}
                                            {if $refrenceListCount > 1}
                                                <select style="width: 140px;" class="select2 referenceModulesList">
                                                    {foreach key=index item=value from=$refrenceList}
                                                        <option value="{$value}">{vtranslate($value, $value)}</option>
                                                    {/foreach}
                                                </select>
                                            {else}
                                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                            {/if}
                                        {else}
                                            {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                                        {/if}
                                        &nbsp;{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                    </div>
                                    <div class="fieldValue col-xs-12 col-md-3" {if $FIELD_MODEL->getFieldDataType() eq 'boolean'} style="width:25%" {/if} {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                                        {if $FIELD_MODEL->getFieldDataType() eq 'image' || $FIELD_MODEL->getFieldDataType() eq 'file'}
                                            <div class='col-lg-4 col-md-4 redColor'>
                                                {vtranslate('LBL_NOTE_EXISTING_ATTACHMENTS_WILL_BE_REPLACED', $MODULE)}
                                            </div>
                                        {/if}
                                        {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                                    </div>
                                {/if}
                            {/foreach}
                            {*If their are odd number of fields in edit then border top is missing so adding the check*}
                            {if $COUNTER is odd}
                                <div class="col-xs-12 col-md-3"></div>
                                <div class="col-xs-12 col-md-3"></div>
                            {/if}
                        </div>
                    </div>
                </div>
                <br>
            {/if}
        {/foreach}
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
				                <tr>
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
				                </tr>
				            </table>
				        </div>
			        </td>
		        </tr>
	        </table>
        </div>
        <br>
        <div class='fieldBlockContainer'>
            <h4 class='fieldBlockHeader' >Business Hours</h4>
            <hr>
            <table class="table table-borderless">
                <tr>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="monday" class="btn {if $BUSINESSHOURS['monday_start']}btn-success{else}btn-danger{/if} bus_hours">Mon</button>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="tuesday" class="btn {if $BUSINESSHOURS['tuesday_start']}btn-success{else}btn-danger{/if} bus_hours">Tue</button>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="wednesday" class="btn {if $BUSINESSHOURS['wednesday_start']}btn-success{else}btn-danger{/if} bus_hours">Wed</button>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="thursday" class="btn {if $BUSINESSHOURS['thursday_start']}btn-success{else}btn-danger{/if} bus_hours">Thu</button>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="friday" class="btn {if $BUSINESSHOURS['friday_start']}btn-success{else}btn-danger{/if} bus_hours">Fri</button>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="saturday" class="btn {if $BUSINESSHOURS['saturday_start']}btn-success{else}btn-danger{/if} bus_hours">Sat</button>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<button type="button" style="border-radius:5px !important;" data-day="sunday" class="btn {if $BUSINESSHOURS['sunday_start']}btn-success{else}btn-danger{/if} bus_hours">Sun</button>
                	</td>
                </tr>
                <tr>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div>
                			<span class="fieldLabel">Start Time</span>
                			<br><br><br>
                			<span class="fieldLabel">End Time</span>
                		</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="monday_pick"  {if !$BUSINESSHOURS['monday_start']} style="display:none;" {/if} >
		                	<select id="monday_start" class="inputElement select2"  name="time[monday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['monday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="monday_end" class="inputElement select2"  name="time[monday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['monday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="tuesday_pick" {if !$BUSINESSHOURS['tuesday_start']} style="display:none;" {/if}>
		                	<select id="tuesday_start" class="inputElement select2"  name="time[tuesday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['tuesday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="tuesday_end" class="inputElement select2"  name="time[tuesday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['tuesday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="wednesday_pick" {if !$BUSINESSHOURS['wednesday_start']} style="display:none;" {/if}>
		                	<select id="wednesday_start" class="inputElement select2"  name="time[wednesday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['wednesday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="wednesday_end" class="inputElement select2"  name="time[wednesday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['wednesday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="thursday_pick" {if !$BUSINESSHOURS['thursday_start']} style="display:none;" {/if}>
		                	<select id="thursday_start" class="inputElement select2"  name="time[thursday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['thursday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="thursday_end" class="inputElement select2"  name="time[thursday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['thursday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="friday_pick" {if !$BUSINESSHOURS['friday_start']} style="display:none;" {/if}>
		                	<select id="friday_start" class="inputElement select2"  name="time[friday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['friday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="friday_end" class="inputElement select2"  name="time[friday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['friday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="saturday_pick" {if !$BUSINESSHOURS['saturday_start']} style="display:none;" {/if}>
		                	<select id="saturday_start" class="inputElement select2"  name="time[saturday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['saturday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="saturday_end" class="inputElement select2"  name="time[saturday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['saturday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                	<td class="fieldLabel" style="width:0px !important;text-align:center !important;">
                		<div class="sunday_pick" {if !$BUSINESSHOURS['sunday_start']} style="display:none;" {/if}>
		                	<select id="sunday_start" class="inputElement select2"  name="time[sunday_start]" style="width: 100px!imporant;">
								<option value="">{vtranslate('Start time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['sunday_start']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
							<br><br>
							<select id="sunday_end" class="inputElement select2"  name="time[sunday_end]" style="width: 100px!imporant;">
								<option value="">{vtranslate('End time','Vtiger')}</option>
								{foreach item=PICKLIST_VALUE key=PICKLIST_NAME from=$TIMEPICKLIST}
									<option value="{Vtiger_Util_Helper::toSafeHTML($PICKLIST_NAME)}" {if $PICKLIST_VALUE eq $BUSINESSHOURS['sunday_end']} selected {/if}>{$PICKLIST_VALUE}</option>
								{/foreach}
							</select>
						</div>
                	</td>
                </tr>
                <tr>
                	
                </tr>
            </table>
        </div>
        <br>
    </div> 