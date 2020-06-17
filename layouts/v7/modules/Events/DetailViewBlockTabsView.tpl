{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<style>
		.input-group {
		    min-width: 100% !important;
		}
	</style>
	
	{if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
		<input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
	{/if}
	<div id= "customtabs" class="tabs">
		<div class="related-tabs">
			<ul class="nav nav-tabs tab-links" role="tablist">
				{assign var=COUNTER value=1}
				{assign var=TABBLOCKNAME value=''}
				{assign var=COMBINE_TAB value=[]}
				{assign var=$TABNAMES value=[]}
				
				{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
					{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
					{if in_array($BLOCK->get('id'),$COMBINETABIDS)}{continue}{/if}					
					{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
					{if $COUNTER eq 1}
						{$TABBLOCKNAME = $BLOCK_LABEL_KEY}
					{/if}
 				    <li class="tab-item block block_{$BLOCK_LABEL_KEY} {if $TABBLOCKNAME eq $BLOCK_LABEL_KEY}active{/if}" data-block="{$BLOCK_LABEL_KEY}" data-blockid="{$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}">
		    			{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
						{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
						<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
				    	<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#{$BLOCK_LABEL_KEY|replace:' ':'_'}" role="tab">
				    		<span class="tab-label">
					    		<strong>
									{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
								</strong>
							</span>
				    	</a>
				    </li>
				    {$COUNTER = $COUNTER+1} 
				{/foreach}
				{foreach item=COMBINE key=TABNAME from=$COMBINETABS}
					{assign var=TABMODULE value=Settings_TabColumnView_Module_Model::getInstanceByName($MODULE_NAME)}
					{assign var=TAB value=$TABMODULE->checkTabBlockAndFields($TABNAME)}
					{if !$TAB}{continue}{/if}
					{if $TABBLOCKNAME eq ''}
						{$TABBLOCKNAME = $TABNAME}
					{/if}
					 <li class="tab-item {if $TABBLOCKNAME eq $TABNAME}active{/if} block block_{$TABNAME|replace:' ':'_'} ">
						<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
				    	<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#{$TABNAME|replace:' ':'_'}" role="tab">
				    		<span class="tab-label">
					    		<strong>
									{vtranslate({$TABNAME},{$MODULE_NAME})}
								</strong>
							</span>
				    	</a>
				    </li>
				{/foreach}
				<li class="tab-item block block_LBL_INVITE_USER_BLOCK " >
	    			{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
					{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			    	<a class="tablinks textOverflowEllipsis " data-toggle='tab' href="#LBL_INVITE_USER_BLOCK" role="tab">
			    		<span class="tab-label">
				    		<strong>
								{vtranslate("LBL_INVITE_USER_BLOCK",{$MODULE_NAME})}
							</strong>
						</span>
			    	</a>
			    </li>
			</ul>
		</div>
		<div class = "tab-content" style="margin-top:10px;">
			
			{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
				{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
				{if in_array($BLOCK->get('id'),$COMBINETABIDS)}{continue}{/if}	
				{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
				{assign var=NUM_OF_COL value=$NUMOFCOLUMNS[$BLOCK->get('id')]}
				<div class="block block_{$BLOCK_LABEL_KEY} {if $TABBLOCKNAME eq $BLOCK_LABEL_KEY}active {/if}tab-pane" id="{$BLOCK_LABEL_KEY|replace:' ':'_'}" 
					data-block="{$BLOCK_LABEL_KEY}" data-blockid="{$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}">
					<div class="blockData">
						<table class="table detailview-table no-border">
							<tbody>
								{assign var=COUNTER value=0}
								<tr>
									{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
										{assign var=fieldDataType value=$FIELD_MODEL->getFieldDataType()}
										{if !$FIELD_MODEL->isViewableInDetailView()}
											{continue}
										{/if}
										{if $FIELD_MODEL->get('uitype') eq "83"}
											{foreach item=tax key=count from=$TAXCLASS_DETAILS}
												{if $COUNTER eq $NUM_OF_COL2}
													</tr><tr>
													{assign var="COUNTER" value=1}
												{else}
													{assign var="COUNTER" value=$COUNTER+1}
												{/if}
												<td class="fieldLabel {$WIDTHTYPE}">
													<span class='muted'>{vtranslate($tax.taxlabel, $MODULE)}(%)</span>
												</td>
												<td class="fieldValue {$WIDTHTYPE}">
													<span class="value textOverflowEllipsis" data-field-type="{$FIELD_MODEL->getFieldDataType()}" >
														{if $tax.check_value eq 1}
															{$tax.percentage}
														{else}
															0
														{/if} 
													</span>
												</td>
											{/foreach}
										{else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
											{if $COUNTER neq 0}
												{if $COUNTER eq $NUM_OF_COL}
													</tr><tr>
													{assign var=COUNTER value=0}
												{/if}
											{/if}
											<td class="fieldLabel {$WIDTHTYPE}"><span class="muted">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</span></td>
											<td class="fieldValue {$WIDTHTYPE}">
												<ul id="imageContainer">
													{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
														{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
															<li><img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="400" height="300" /></li>
														{/if}
													{/foreach}
												</ul>
											</td>
											{assign var=COUNTER value=$COUNTER+1}
										{else if $fieldDataType eq "reference"}
											{if $COUNTER neq 0}
												{if $COUNTER eq $NUM_OF_COL}
													</tr><tr>
													{assign var=COUNTER value=0}
												{/if}
											{/if}
											{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
											{assign var="refrenceListCount" value=count($refrenceList)}
											<td class="fieldLabel textOverflowEllipsis {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" >
												{if $refrenceListCount > 1}
													<span class="hide referenceSelect">
														{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
														{assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
														{if !empty($REFERENCED_MODULE_STRUCTURE)}
															{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCTURE->get('name')}
														{/if}
														<select style="width: 140px;" class="select2 referenceModulesList">
															{foreach key=index item=value from=$refrenceList}
																<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $value)}</option>
															{/foreach}
														</select>
													</span>
												{/if}
												<span class="muted">{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</span>
											</td>
											<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" >
												{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
												
												{assign var=FIELD_DISPLAY_VALUE value=Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}
		
												<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" >
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
												</span>
												{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && $FIELD_MODEL->isAjaxEditable() eq 'true'}
													<span class="hide edit pull-left">
														{if $fieldDataType eq 'multireference'}
															{assign var=CONTACTINFO value=[]}
															{foreach item=CONTACT_INFO from=$RELATED_CONTACTS}
																{if $CONTACT_INFO['id']}
																	{$CONTACTINFO[] = ['id'=>$CONTACT_INFO['id'], 'name'=>Vtiger_Util_Helper::getRecordName($CONTACT_INFO['id'])]}
																{/if}
															{/foreach}
															<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}'data-type="{$fieldDataType}" data-displayvalue='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}'
															data-value='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}'  />
															 <input type="hidden" name="relatedContactInfo" data-value='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}' />
														{else}
															<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
														{/if}
													</span>
													<span class="action pull-right"><a href="#" onclick="return false;" class="editAction fa fa-pencil"></a></span>
												{/if}
											</td>
											{assign var=COUNTER value=$COUNTER+1}
										{else}
											{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19" or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'}
												{if $COUNTER eq '1'}
													<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
													{assign var=COUNTER value=0}
												{/if}
											{/if}
											{if $COUNTER eq $NUM_OF_COL}
												</tr><tr>
												{assign var=COUNTER value=1}
											{else}
												{assign var=COUNTER value=$COUNTER+1}
											{/if}
											<td class="fieldLabel textOverflowEllipsis {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
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
											</td>
											<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
												{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
												{if $fieldDataType eq 'multipicklist'}
													{assign var=FIELD_DISPLAY_VALUE value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
												{else}
													{assign var=FIELD_DISPLAY_VALUE value=Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}
												{/if}
		
												<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
													{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
												</span>
												{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && $FIELD_MODEL->isAjaxEditable() eq 'true'}
													<span class="hide edit pull-left">
														{if $fieldDataType eq 'multipicklist'}
															<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}[]' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
														{else if $fieldDataType eq 'datetime'}
															<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}'data-type="{$fieldDataType}" data-displayvalue='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'),$RECORD->getId(), $RECORD)}'
															data-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'),$RECORD->getId(), $RECORD)}'  />
														{else}
															<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
														{/if}
													</span>
													<span class="action pull-right"><a href="#" onclick="return false;" class="editAction fa fa-pencil"></a></span>
												{/if}
											</td>
										{/if}
		
										{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
											<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
										{/if}
									{/foreach}
									{* adding additional column for odd number of fields in a block *}
									{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
										<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
									{/if}
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				
			{/foreach}
			{foreach item=COMBINE key=TABNAME from=$COMBINETABS}
				<div class="block_{$TABNAME|replace:' ':'_'} tab-pane {if $TABBLOCKNAME eq $TABNAME}active{/if}" id="{$TABNAME|replace:' ':'_'}" >
					{foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
						{assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
						{if !in_array($BLOCK->get('id'),$COMBINE)}{continue}{/if}
						{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
						{assign var=NUM_OF_COL value=$NUMOFCOLUMNS[$BLOCK->get('id')]}
						<div class="block block_{$BLOCK_LABEL_KEY}" data-block="{$BLOCK_LABEL_KEY}" data-blockid="{$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}">
							{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
							{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
							<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
							<div>
								<h4 class="textOverflowEllipsis maxWidth50">
									<img class="cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if}" src="{vimage_path('arrowRight.png')}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>
									<img class="cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}" src="{vimage_path('arrowdown.png')}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}>&nbsp;
									{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
								</h4>
							</div>
							<hr>
							<div class="blockData">
								<table class="table detailview-table no-border">
									<tbody {if $IS_HIDDEN} class="hide" {/if}>
										{assign var=COUNTER value=0}
										<tr>
											{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
												{assign var=fieldDataType value=$FIELD_MODEL->getFieldDataType()}
												{if !$FIELD_MODEL->isViewableInDetailView()}
													{continue}
												{/if}
												{if $FIELD_MODEL->get('uitype') eq "83"}
													{foreach item=tax key=count from=$TAXCLASS_DETAILS}
														{if $COUNTER eq $NUM_OF_COL}
															</tr><tr>
															{assign var="COUNTER" value=1}
														{else}
															{assign var="COUNTER" value=$COUNTER+1}
														{/if}
														<td class="fieldLabel {$WIDTHTYPE}">
															<span class='muted'>{vtranslate($tax.taxlabel, $MODULE)}(%)</span>
														</td>
														<td class="fieldValue {$WIDTHTYPE}">
															<span class="value textOverflowEllipsis" data-field-type="{$FIELD_MODEL->getFieldDataType()}" >
																{if $tax.check_value eq 1}
																	{$tax.percentage}
																{else}
																	0
																{/if} 
															</span>
														</td>
													{/foreach}
												{else if $FIELD_MODEL->get('uitype') eq "69" || $FIELD_MODEL->get('uitype') eq "105"}
													{if $COUNTER neq 0}
														{if $COUNTER eq $NUM_OF_COL}
															</tr><tr>
															{assign var=COUNTER value=0}
														{/if}
													{/if}
													<td class="fieldLabel {$WIDTHTYPE}"><span class="muted">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</span></td>
													<td class="fieldValue {$WIDTHTYPE}">
														<ul id="imageContainer">
															{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
																{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
																	<li><img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="400" height="300" /></li>
																{/if}
															{/foreach}
														</ul>
													</td>
													{assign var=COUNTER value=$COUNTER+1}
												{else if $fieldDataType eq "reference"}
													{if $COUNTER neq 0}
														{if $COUNTER eq $NUM_OF_COL}
															</tr><tr>
															{assign var=COUNTER value=0}
														{/if}
													{/if}
													{assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
													{assign var="refrenceListCount" value=count($refrenceList)}
													<td class="fieldLabel textOverflowEllipsis {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" >
														{if $refrenceListCount > 1}
															<span class="hide referenceSelect">
																{assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
																{assign var="REFERENCED_MODULE_STRUCTURE" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
																{if !empty($REFERENCED_MODULE_STRUCTURE)}
																	{assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCTURE->get('name')}
																{/if}
																<select style="width: 140px;" class="select2 referenceModulesList">
																	{foreach key=index item=value from=$refrenceList}
																		<option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if}>{vtranslate($value, $value)}</option>
																	{/foreach}
																</select>
															</span>
														{/if}
														<span class="muted">{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</span>
													</td>
													<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" >
														{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
														
														{assign var=FIELD_DISPLAY_VALUE value=Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}
				
														<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" >
															{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
														</span>
														{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && $FIELD_MODEL->isAjaxEditable() eq 'true'}
															<span class="hide edit pull-left">
																{if $fieldDataType eq 'multireference'}
																	{assign var=CONTACTINFO value=[]}
																	{foreach item=CONTACT_INFO from=$RELATED_CONTACTS}
																		{if $CONTACT_INFO['id']}
																			{$CONTACTINFO[] = ['id'=>$CONTACT_INFO['id'], 'name'=>Vtiger_Util_Helper::getRecordName($CONTACT_INFO['id'])]}
																		{/if}
																	{/foreach}
																	<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}'data-type="{$fieldDataType}" data-displayvalue='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}'
																	data-value='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}'  />
																	 <input type="hidden" name="relatedContactInfo" data-value='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}' />
																{else}
																	<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
																{/if}
															</span>
															<span class="action pull-right"><a href="#" onclick="return false;" class="editAction fa fa-pencil"></a></span>
														{/if}
													</td>
													{assign var=COUNTER value=$COUNTER+1}
												{else}
													{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19" or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'}
														{if $COUNTER gte '3'}
															<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td></tr><tr>
															{assign var=COUNTER value=0}
														{/if}
													{/if}
													{if $COUNTER eq $NUM_OF_COL}
														</tr><tr>
														{assign var=COUNTER value=1}
													{else}
														{assign var=COUNTER value=$COUNTER+1}
													{/if}
													<td class="fieldLabel textOverflowEllipsis {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'} style='width:8%'{/if}>
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
													</td>
													<td class="fieldValue {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" {if $FIELD_MODEL->get('uitype') eq '19' or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
														{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
														{if $fieldDataType eq 'multipicklist'}
															{assign var=FIELD_DISPLAY_VALUE value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
														{else}
															{assign var=FIELD_DISPLAY_VALUE value=Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}
														{/if}
				
														<span class="value" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
															{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
														</span>
														{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && $FIELD_MODEL->isAjaxEditable() eq 'true'}
															<span class="hide edit pull-left">
																{if $fieldDataType eq 'multipicklist'}
																	<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}[]' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
																{else if $fieldDataType eq 'datetime'}
																	<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}'data-type="{$fieldDataType}" data-displayvalue='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'),$RECORD->getId(), $RECORD)}'
																	data-value='{$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'),$RECORD->getId(), $RECORD)}'  />
																{else if $fieldDataType eq 'multireference'}
																	{assign var=CONTACTINFO value=[]}
																	{foreach item=CONTACT_INFO from=$RELATED_CONTACTS}
																		{if $CONTACT_INFO['id']}
																			{$CONTACTINFO[] = ['id'=>$CONTACT_INFO['id'], 'name'=>Vtiger_Util_Helper::getRecordName($CONTACT_INFO['id'])]}
																		{/if}
																	{/foreach}
																	<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}'data-type="{$fieldDataType}" data-displayvalue='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}'
																	data-value='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}'  />
																	 <input type="hidden" name="relatedContactInfo" data-value='{json_encode($CONTACTINFO, $smarty.const.JSON_HEX_APOS)}' />
																{else}
																	<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
																{/if}
															</span>
															<span class="action pull-right"><a href="#" onclick="return false;" class="editAction fa fa-pencil"></a></span>
														{/if}
													</td>
												{/if}
				
												{if $FIELD_MODEL_LIST|@count eq 1 and $FIELD_MODEL->get('uitype') neq "19" and $FIELD_MODEL->get('uitype') neq "20" and $FIELD_MODEL->get('uitype') neq "30" and $FIELD_MODEL->get('name') neq "recurringtype" and $FIELD_MODEL->get('uitype') neq "69" and $FIELD_MODEL->get('uitype') neq "105"}
													<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
												{/if}
											{/foreach}
											{* adding additional column for odd number of fields in a block *}
											{if $FIELD_MODEL_LIST|@end eq true and $FIELD_MODEL_LIST|@count neq 1 and $COUNTER eq 1}
												<td class="fieldLabel {$WIDTHTYPE}"></td><td class="{$WIDTHTYPE}"></td>
											{/if}
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<br>
					{/foreach}
				</div>	
			{/foreach}
			<div class="block block_LBL_INVITE_USER_BLOCK tab-pane" id="LBL_INVITE_USER_BLOCK" >
			    {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			    {assign var="IS_HIDDEN" value=false}
			    {assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			
			    <div>
			        <h4>{vtranslate('LBL_INVITE_USER_BLOCK',{$MODULE_NAME})}</h4>
			    </div>
			    <hr>
			
			    <div class="blockData">
			        <table class="table detailview-table no-border">
			            <tbody>
			                <tr>
			                    <td class="fieldLabel {$WIDTHTYPE}">
			                        <span class="muted">{vtranslate('LBL_INVITE_USERS', $MODULE_NAME)}</span>
			                    </td>
			                    <td class="fieldValue {$WIDTHTYPE}">
			                        {foreach key=USER_ID item=USER_NAME from=$ACCESSIBLE_USERS}
			                            {if in_array($USER_ID,$INVITIES_SELECTED)}
			                                {$USER_NAME} - {vtranslate($INVITEES_DETAILS[$USER_ID],$MODULE)}
			                                <br>
			                            {/if}
			                        {/foreach}
			                    </td>
			                </tr>
			            </tbody>
			        </table>
			    </div>
			</div>
		</div>	
	</div>	
{/strip}