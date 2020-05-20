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
        <div class="contents detailview-table">
            {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
                <div class="block block_{$BLOCK_LABEL_KEY}">
                    {assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
                {if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
                {assign var=IS_HIDDEN value=$BLOCK->isHidden()}
                {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
                <input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
                <div class="row">
                    <h4 class="col-xs-8">{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}</h4>
                    <div class="col-xs-4 marginTop5px">
                        <div class=" pull-right detailViewButtoncontainer">
                            <div class="btn-group  pull-right">
                                <a class="btn btn-default" href="{$RECORD->getCalendarSettingsEditViewUrl()}">Edit</a>
                            </div>  
                        </div>
                    </div>
                </div>
                <hr>
                <div class="blockData row">
                    <div class="">
                            {assign var=COUNTER value=0}
                            <div class="row">
                                {foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
                                    {assign var=fieldDataType value=$FIELD_MODEL->getFieldDataType()}
                                    {if !$FIELD_MODEL->isViewableInDetailView()}
                                        {continue}
                                    {/if}
                                    {if $FIELD_MODEL->get('uitype') eq "83"}
                                        {foreach item=tax key=count from=$TAXCLASS_DETAILS}
                                            {if $COUNTER eq 1}
                                            </div><div class="row">
                                                {assign var="COUNTER" value=1}
                                            {else}
                                                {assign var="COUNTER" value=$COUNTER+1}
                                            {/if}
                                            <div class="col-lg-6 fieldLabel {$WIDTHTYPE}">
                                                <span class='muted'>{vtranslate($tax.taxlabel, $MODULE)}(%)</span>
                                            </div>
                                            <div class="col-lg-6 fieldValue {$WIDTHTYPE}">
                                                <span class="value textOverflowEllipsis" data-field-type="{$FIELD_MODEL->getFieldDataType()}" >
                                                    {if $tax.check_value eq 1}
                                                        {$tax.percentage}
                                                    {else}
                                                        0
                                                    {/if} 
                                                </span>
                                            </div>
                                        {/foreach}
                                    {else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
                                        {if $COUNTER neq 0}
                                            {if $COUNTER eq 1}
                                            </div><div class="row">
                                                {assign var=COUNTER value=0}
                                            {/if}
                                        {/if}
                                        <div class="col-lg-6 fieldLabel {$WIDTHTYPE}"><span class="muted">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</span></div>
                                        <div class="col-lg-6 fieldValue {$WIDTHTYPE}">
                                            <div id="imageContainer" width="300" height="200">
                                                {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
                                                    {if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
                                                        <img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" width="300" height="200">
                                                    {/if}
                                                {/foreach}
                                            </div>
                                        </div>
                                        {assign var=COUNTER value=$COUNTER+1}
                                     {else if $FIELD_MODEL->getName() eq "appointment_url"}
                                    	<div class="col-lg-6 fieldLabel  {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" >
                                            <span class="muted">
                                                {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                            </span>
                                        </div>
                                        <div class="col-lg-6 fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}">
                                    		{substr($APPOINTMENTURL, 0, 30)} &nbsp;&nbsp; <button class="btn-default copytoclipboard" data-url="{$APPOINTMENTURL}"><i class="fa fa-copy"></i></button>
                                		</div>
                                    {else}
                                        {if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19" or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'}
                                            {if $COUNTER eq '1'}
                                                <div class="col-lg-6 fieldLabel {$WIDTHTYPE}"></div><div class="col-lg-6 {$WIDTHTYPE}"></div></div><div class="row">
                                                    {assign var=COUNTER value=0}
                                                {/if}
                                            {/if}
                                            {if $COUNTER eq 1}
                                        </div><div class="row">
                                            {assign var=COUNTER value=1}
                                        {else}
                                            {assign var=COUNTER value=$COUNTER+1}
                                        {/if}
                                        <div class="col-lg-6 fieldLabel textOverflowEllipsis {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
                                            <span class="muted">
                                                {if $MODULE_NAME eq 'Documents' && $FIELD_MODEL->get('label') eq "File Name" && $RECORD->get('filelocationtype') eq 'E'}
                                                    {vtranslate("LBL_FILE_URL",{$MODULE_NAME})}
                                                {else}
                                                    {vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}
                                                {/if}
                                                {if ($FIELD_MODEL->get('uitype') eq '72') && ($FIELD_MODEL->getName() eq 'unit_price')}
                                                    ({$BASE_CURRENCY_SYMBOL})
                                                {/if}
                                            </span>
                                        </div>
                                        <div class="col-lg-6 fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>

                                            {assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
                                            {if $fieldDataType eq 'multipicklist'}
                                                {assign var=FIELD_DISPLAY_VALUE value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
                                            {else}
                                                {assign var=FIELD_DISPLAY_VALUE value=Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}
                                            {/if}

                                            <span class="value textOverflowEllipsis" data-field-type="{$FIELD_MODEL->getFieldDataType()}"  {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
                                                {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                                            </span>
                                            {if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && $FIELD_MODEL->isAjaxEditable() eq 'true'}
                                                <div class="hide edit pull-left calendar-timezone clearfix">
                                                    {if $fieldDataType eq 'multipicklist'}
                                                        <input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}[]' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
                                                    {else}
                                                        <input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
                                                    {/if}
                                                </div>
                                                <span class="action pull-right"><a href="#" onclick="return false;" class="editAction ti-pencil"></a></span>
                                                {/if}
                                        </div>
                                    {/if}

                                    {if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
                                        </div><div class="row"><div class="col-lg-6 fieldLabel {$WIDTHTYPE}"></div><div class="col-lg-6 {$WIDTHTYPE}"></div></div>
                                        {/if}
                                    {/foreach}
                                    {* adding additional column for odd number of fields in a block *}
                                    {if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
                                     </div><div class="row"><div class="col-lg-6 fieldLabel {$WIDTHTYPE}"></div><div class="col-lg-6 {$WIDTHTYPE}"></div></div>
                                    {/if}
                            </div>
                    </div>
                </div>
            </div>
            <br>
        {/foreach}
        
        <div class='fieldBlockContainer'>
            <h4 class='fieldBlockHeader' >Business Hours</h4>
            <hr>
            <table class="table table-borderless">
                <tr>
                	<td class="fieldLabel text-center">
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['monday_start']}btn-success{else}btn-danger{/if}">Mon</button>
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['tuesday_start']}btn-success{else}btn-danger{/if}">Tue</button>
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['wednesday_start']}btn-success{else}btn-danger{/if}">Wed</button>
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['thursday_start']}btn-success{else}btn-danger{/if}">Thu</button>
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['friday_start']}btn-success{else}btn-danger{/if}">Fri</button>
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['saturday_start']}btn-success{else}btn-danger{/if}">Sat</button>
                	</td>
                	<td class="fieldLabel text-center">
                		<button type="button" style="border-radius:5px !important;" class="btn {if $BUSINESSHOURS['sunday_start']}btn-success{else}btn-danger{/if}">Sun</button>
                	</td>
                </tr>
                <tr>
                	<td class="fieldLabel text-center">
                		<div>
                			<span>Start Time : </span>
                			<br><br>
                			<span>End Time : </span>
                		</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="monday_pick">
		                	{$BUSINESSHOURS['monday_start']}
							<br><br>
							{$BUSINESSHOURS['monday_end']}
						</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="tuesday_pick">
		                	{$BUSINESSHOURS['tuesday_start']}
							<br><br>
							{$BUSINESSHOURS['tuesday_end']}
						</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="wednesday_pick">
		                	{$BUSINESSHOURS['wednesday_start']}
							<br><br>
							{$BUSINESSHOURS['wednesday_end']}
						</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="thursday_pick">
		                	{$BUSINESSHOURS['thursday_start']}
							<br><br>
							{$BUSINESSHOURS['thursday_end']}
						</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="friday_pick">
		                	{$BUSINESSHOURS['friday_start']}
							<br><br>
							{$BUSINESSHOURS['friday_end']}
						</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="saturday_pick">
		                	{$BUSINESSHOURS['saturday_start']}
							<br><br>
							{$BUSINESSHOURS['saturday_end']}
						</div>
                	</td>
                	<td class="fieldLabel text-center">
                		<div class="sunday_pick">
		                	{$BUSINESSHOURS['sunday_start']}
							<br><br>
							{$BUSINESSHOURS['sunday_end']}
						</div>
                	</td>
                </tr>
                <tr>
                	
                </tr>
            </table>
        </div>
        <br>
    {/strip}