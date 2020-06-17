{assign var=CONDITION_INFO value=$CUSTOMVIEW_MODEL->getSelectedSortingFields()}
    
<div class="filterContainer">
	<div class="conditionGroup contentsBackground">
		<div class="content">
			<div class="form-group">
				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-5">
					<select class="select2" name="sort_order_field" style="width: 50%;">
						<option value="none">{vtranslate('LBL_SELECT_FIELD',$MODULE)}</option>
							{foreach key=BLOCK_LABEL item=BLOCK_FIELDS from=$RECORD_STRUCTURE}
							<optgroup label='{vtranslate($BLOCK_LABEL, $SOURCE_MODULE)}'>
								{foreach key=FIELD_NAME item=FIELD_MODEL from=$BLOCK_FIELDS}
									{assign var=FIELD_INFO value=$FIELD_MODEL->getFieldInfo()}
									{assign var=MODULE_MODEL value=$FIELD_MODEL->getModule()}
				                    {assign var="SPECIAL_VALIDATOR" value=$FIELD_MODEL->getValidator()}
									{if !empty($COLUMNNAME_API)}
										{assign var=columnNameApi value=$COLUMNNAME_API}
									{else}
										{assign var=columnNameApi value=getCustomViewColumnName}
									{/if}
									<option value="{$FIELD_MODEL->$columnNameApi()}" data-fieldtype="{$FIELD_MODEL->getFieldType()}" data-field-name="{$FIELD_NAME}"
									{if decode_html($FIELD_MODEL->$columnNameApi()) eq decode_html($CONDITION_INFO['columnname'])}
										{assign var=FIELD_TYPE value=$FIELD_MODEL->getFieldType()}
										{assign var=SELECTED_FIELD_MODEL value=$FIELD_MODEL}
										{if $FIELD_MODEL->getFieldDataType() == 'reference'}
											{$FIELD_TYPE='V'}
										{/if}
										selected="selected"
									{/if}
									{if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'recurringtype')}
										{assign var=PICKLIST_VALUES value = Calendar_Field_Model::getReccurencePicklistValues()}
										{$FIELD_INFO['picklistvalues'] = $PICKLIST_VALUES}
									{/if}
				                    {if ($MODULE_MODEL->get('name') eq 'Calendar') && ($FIELD_NAME eq 'activitytype')}
										{$FIELD_INFO['picklistvalues']['Task'] = vtranslate('Task', 'Calendar')}
									{/if}
									{if $FIELD_MODEL->getFieldDataType() eq 'reference'}
										{assign var=referenceList value=$FIELD_MODEL->getWebserviceFieldObject()->getReferenceList()}
										{if is_array($referenceList) && in_array('Users', $referenceList)}
												{assign var=USERSLIST value=array()}
												{assign var=CURRENT_USER_MODEL value = Users_Record_Model::getCurrentUserModel()}
												{assign var=ACCESSIBLE_USERS value = $CURRENT_USER_MODEL->getAccessibleUsers()}
												
												{foreach item=USER_NAME from=$ACCESSIBLE_USERS}
														{$USERSLIST[$USER_NAME] = $USER_NAME}
												{/foreach}
												{$FIELD_INFO['picklistvalues'] = $USERSLIST}
												{$FIELD_INFO['type'] = 'picklist'}
										{/if}
									{/if}
									data-fieldinfo='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($FIELD_INFO))}' 
				                    {if !empty($SPECIAL_VALIDATOR)}data-validator='{Zend_Json::encode($SPECIAL_VALIDATOR)}'{/if}>
									{if $MODULE_MODEL->get('name') neq 'Events' and $SOURCE_MODULE neq $MODULE_MODEL->get('name')}
										({vtranslate($MODULE_MODEL->get('name'), $MODULE_MODEL->get('name'))})  {vtranslate($FIELD_MODEL->get('label'), $MODULE_MODEL->get('name'))}
									{else}
										{vtranslate($FIELD_MODEL->get('label'), $SOURCE_MODULE)}
									{/if}
									</option>
									{/foreach}
								</optgroup>
							{/foreach}
						</select>
				</div>
	   			<div class="col-lg-5 col-md-5 col-sm-5">
					<div class="row-fluid">
						<input style='margin:5px;' type="radio" name="sort_order" class="sortOrder" value="ASC" {if !empty($CONDITION_INFO) && $CONDITION_INFO['sortorder'] eq 'ASC'} checked="" {/if} />&nbsp;<span>{vtranslate('LBL_ASCENDING',$MODULE)}</span>&nbsp;&nbsp;
						<input style='margin:5px;'type="radio" name="sort_order" class="sortOrder" value="DESC" {if !empty($CONDITION_INFO) && $CONDITION_INFO['sortorder'] eq 'DESC'} checked="" {/if}/>&nbsp;<span>{vtranslate('LBL_DESCENDING',$MODULE)}</span>
					</div>
				</div>
				</div>
			</div>
		</div>
	</div>
</div>        