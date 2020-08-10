{*<!--
/*********************************************************************************
 ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/
-->*}
{strip}
<div class="modal-dialog contactFMEdit" id="contactFMEdit">
    <div class="modal-content" >
        {assign var=HEADER_TITLE value={vtranslate('LBL_FIELD_MAPPING', $MODULE)}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
        <form class="form-horizontal m-form" name="contactsyncsettings">
            <input type="hidden" name="module" value="{$MODULENAME}" />
            <input type="hidden" name="action" value="SaveFieldMapping" />
            <input type="hidden" name="sourcemodule" value="{$SOURCE_MODULE}" />
            <input id="user_field_mapping" type="hidden" name="fieldmapping" value="fieldmappings" />
            <input id="google_fields" type="hidden" value='{Zend_Json::encode($GOOGLE_FIELDS)}' />
            <div class="modal-body">
           	 	<div id="contactsFieldMappingContainer" class="contactsFieldMappingContainer">
	           	 	<div class="row">
			    		<div class="col-sm-1"></div>
            			<div class="col-sm-10">
			           	 	<div id="exchangeDetailFieldMapping">
				           	 	<div class="form-group pull-right">
				            		<button id="editContactMapping" type="button" class="btn btn-default" data-sync-module="Contacts">{vtranslate('LBL_EDIT', $MODULENAME)}</button> 
				            	</div>
				            	
				            	<div class="form-group">
				                    <table class="table table-bordered moduleLineItemTable">
				                    	<colgroup width="30%">
				                        <thead>
				                            <tr>
				                                <th>{vtranslate('APPTITLE',$MODULENAME)}</th>
				                                <th>{vtranslate('EXTENTIONNAME',$MODULENAME)}</th>
				                            </tr>
				                        </thead>
				                        <tbody>
				                        	{foreach item=FieldMapping from=$FIELD_MAPPING}
					                            <tr>
					                                <td data-label="{vtranslate('APPTITLE',$MODULENAME)}">
					                                    {vtranslate($MODULE_FIELDS_LABEL[$FieldMapping['CRM']], $SOURCE_MODULE)}
					                                </td>
					                                <td data-label="{vtranslate('EXTENTIONNAME',$MODULENAME)}">
					                                    {vtranslate($FieldMapping['MSExchange'], $MODULENAME)}
					                                </td>
					                            </tr>
				                            {/foreach}
				                        </tbody>
				                    </table>
				                </div>
			            	</div>
			            	<div id="exchangeEditFieldMapping" class="form-group hide">
			                    <table  class="table table-bordered moduleLineItemTable" id="contactExchangeMapping">
			                    	<colgroup width="40%">
			                        <thead>
			                            <tr>
			                                <th>{vtranslate('APPTITLE',$MODULENAME)}</th>
			                                <th>{vtranslate('EXTENTIONNAME',$MODULENAME)}</th>
			                            </tr>
			                        </thead>
			                        <tbody>
			                        	{assign var=CRMSkipFields value=['firstname', 'lastname','email']}
			                            {assign var=ExchangeSkipFields value=['First Name', 'Last Name', 'Email Address']}
			                            {assign var=COUNTER value=1}
			                            {foreach item=FieldMapping from=$FIELD_MAPPING}
			                        		{if in_array($FieldMapping['CRM'], $CRMSkipFields)}
				                                <tr>
					                                <td data-label="{vtranslate('APPTITLE',$MODULENAME)}">
					                                     {vtranslate($MODULE_FIELDS_LABEL[$FieldMapping['CRM']], $SOURCE_MODULE)}
						                            </td>
					                                <td data-label="{vtranslate('EXTENTIONNAME',$MODULENAME)}">
					                                    {$FieldMapping['MSExchange']}
					                                </td>
					                            </tr>
					                        {else}
					                        	<tr class="customMapping">
					                            	<td data-label="{vtranslate('APPTITLE',$MODULENAME)}">
						                            	<div class="d-flex">
						                                    <i class="deleteMapping la la-trash col-form-label pr-1" title="Delete"></i>
						                                    <select class="form-control crm_fields select2" name="mapping[{$COUNTER}][CRM]">
						                                    	<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
						                                    	{foreach key=FieldId item=FieldModel from=$MODULE_FIELDS}
						                                    		{if in_array($FieldModel->get("name"), $CRMSkipFields) or !$FieldModel->isEditable()}
						                                    			{continue}
						                                    		{/if}
						                                    		<option value="{$FieldId}" {if $FieldMapping['CRM'] eq $FieldModel->get("name")}selected{/if}>{vtranslate($FieldModel->get("label"), $SOURCE_MODULE)}</option>
						                                    	{/foreach}
						                                    </select>
					                                    </div>
					                                </td>
					                                <td data-label="{vtranslate('EXTENTIONNAME',$MODULENAME)}">
					                                	<select class="form-control exchange_fields col-8 select2" name="mapping[{$COUNTER}][MSExchange]">
					                                		<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
					                                    	{foreach key=gLabel item=FieldLabel from=$EXCHANGE_FIELDS}
					                                    		{if in_array($FieldLabel, $ExchangeSkipFields)}
					                                    			{continue}
					                                    		{/if}
					                                    		{if $gLabel === 'Address'}
					                                    			{foreach key=OPTLABEL item=AddressFields from=$FieldLabel}
						                                    			<optgroup label="{vtranslate($OPTLABEL)|cat:' Address'}">
						                                    				{foreach item=AddressField from=$AddressFields}
						                                    					<option value="{$OPTLABEL|cat:'_'|cat:$AddressField}" {if $FieldMapping['MSExchange'] eq $OPTLABEL|cat:'_'|cat:$AddressField}selected{/if}>{vtranslate($AddressField, $MODULENAME)}</option>
						                                    				{/foreach}
						                                    			</optgroup>
					                                    			{/foreach}
					                                    		{else}
					                                    			<option value="{$FieldLabel}" {if $FieldMapping['MSExchange'] eq $FieldLabel}selected{/if}>{vtranslate($FieldLabel, $MODULENAME)}</option>
					                                    		{/if}	
					                                    	{/foreach}
					                                    </select>
					                                </td>
					                            </tr>
					                            {assign var=COUNTER value=$COUNTER+1}
			                            	{/if}
			                            {/foreach}
			                            <tr class="hide newMapping">
			                            	<td data-label="{vtranslate('APPTITLE',$MODULENAME)}">
				                            	<div class="d-flex">
				                                    <i class="deleteMapping la la-trash col-form-label pr-1" title="Delete"></i>
				                                    <select class="form-control crm_fields">
				                                    	<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
				                                    	{foreach key=FieldId item=FieldModel from=$MODULE_FIELDS}
				                                    		{if in_array($FieldModel->get("name"), $CRMSkipFields) or !$FieldModel->isEditable()}
				                                    			{continue}
				                                    		{/if}
				                                    		<option value="{$FieldId}">{vtranslate($FieldModel->get("label"), $SOURCE_MODULE)}</option>
				                                    	{/foreach}
				                                    </select>
			                                    </div>
			                                </td>
			                                <td data-label="{vtranslate('EXTENTIONNAME',$MODULENAME)}">
			                                	<select class="form-control exchange_fields col-8">
			                                		<option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
			                                    	{foreach key=gLabel item=FieldLabel from=$EXCHANGE_FIELDS}
			                                    		{if in_array($FieldLabel, $ExchangeSkipFields)}
			                                    			{continue}
			                                    		{/if}
			                                    		{if $gLabel === 'Address'}
			                                    			{foreach key=OPTLABEL item=AddressFields from=$FieldLabel}
				                                    			<optgroup label="{vtranslate($OPTLABEL)|cat:' Address'}">
				                                    				{foreach item=AddressField from=$AddressFields}
				                                    					<option value="{$OPTLABEL|cat:'_'|cat:$AddressField}">{vtranslate($AddressField, $MODULENAME)}</option>
				                                    				{/foreach}
				                                    			</optgroup>
			                                    			{/foreach}
			                                    		{else}
			                                    			<option value="{$FieldLabel}">{vtranslate($FieldLabel, $MODULENAME)}</option>
			                                    		{/if}	
			                                    	{/foreach}
			                                    </select>
			                                </td>
			                            </tr>
			                        </tbody>
			                    </table>
				               	<div class="form-group">
				        			<div class="col-sm-10">
					                	<button id="msexchangesync_addcustommapping" class="btn btn-default" type="button">
											{vtranslate('LBL_ADD_CUSTOM_FIELD_MAPPING',$MODULENAME)}
										</button>
									</div>
				           		</div>
			                </div>
			    		</div>
			   		</div>
	            </div>
            </div>
        </form>
        <div class="modal-footer hide">
            <button id="save_field_mapping" class="btn btn-success" type="submit" name="saveButton"><strong>{vtranslate('LBL_SAVE', $MODULE)}</strong></button>
            <button class = "btn btn-secondary" id="cancelEditing">{vtranslate('LBL_CANCEL', $MODULE)}</button>
    	</div>
	</div>
</div>
{/strip}