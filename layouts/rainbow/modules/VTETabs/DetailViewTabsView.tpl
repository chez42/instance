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
<style>
    .tab {
        display:none;
    }
    .tab.active {
        display:block;
    }
</style>
{strip}
    {assign var=TAB_CHILD_ARRAYS_BLOCK_FIELDS value=[]}
    {assign var=TAB_ARRAYS_BLOCK_FIELDS value=[]}
    {assign var=COMBINE_TAB_LIST value=VTETabs_Module_Model::getCombineTabs($MODULE_NAME)}
    {assign var=LABEL_ARRAYS_TAB_WITH_ORDER value=[]}
    {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
        <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
    {/if}
    {*Display un_tab block*}
    {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$RECORD_STRUCTURE}
        {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id'))}
        {if $TAB_INFO['is_tab_store'] eq 1}
            {$TAB_ARRAYS_BLOCK_FIELDS[$BLOCK_LABEL_KEY] = $FIELD_MODEL_LIST}
            {continue}
        {/if}
        {if $BLOCK_LABEL_KEY eq "LBL_IMAGE_INFORMATION"}
            {assign var=IS_IMG_TAB value = true}
            {assign var=IMG_TAB value= $FIELD_MODEL_LIST}
            {continue}
        {/if}
        {if $BLOCK_LABEL_KEY eq "LBL_ITEM_DETAILS"}
            {continue}
        {/if}
        {include file="ChildDetailViewTabsView.tpl"|vtemplate_path:$MODULE NONE_TAB =true FIELD_MODEL_LIST = $FIELD_MODEL_LIST BLOCK_LABEL_KEY = $BLOCK_LABEL_KEY MODULE_NAME = $MODULE_NAME BLOCK_LIST = $BLOCK_LIST USER_MODEL=$USER_MODEL TAXCLASS_DETAILS=$TAXCLASS_DETAILS DAY_STARTS=$DAY_STARTS BASE_CURRENCY_SYMBOL=$BASE_CURRENCY_SYMBOL IMAGE_DETAILS=$IMAGE_DETAILS IS_AJAX_ENABLED=$IS_AJAX_ENABLED}
        <br />
    {/foreach}
    {*End un_tab block*}
    {*Start for tab block*}
    <div id="vte_tabs" class="tabs">
    <div class="related-tabs">
        <ul class="tab-links nav nav-tabs">
            {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$TAB_ARRAYS_BLOCK_FIELDS}
                {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id'))}
                {if $TAB_INFO['parent_tab'] neq null && $TAB_INFO['parent_tab'] neq ''}
                    {if $TAB_INFO['is_tab_store'] eq 1}
                        {$TAB_CHILD_ARRAYS_BLOCK_FIELDS[$BLOCK_LABEL_KEY] = $RECORD_STRUCTURE[$BLOCK_LABEL_KEY]}
                    {/if}
                    {continue}
                {/if}
                {$LABEL_ARRAYS_TAB_WITH_ORDER[$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')] = $BLOCK_LABEL_KEY}
            {/foreach}
            {if $COMBINE_TAB_LIST|@count gt 0}
                {foreach key=TAB_KEY item=TAB_NAME from=$COMBINE_TAB_LIST}
                    {$LABEL_ARRAYS_TAB_WITH_ORDER[$TAB_KEY] = $TAB_NAME}
                {/foreach}
            {/if}
            {assign var=TAB_LIST_WITH_ORDER value=VTETabs_Module_Model::getTabsLabelWithOrder($MODULE_NAME)}
            {if $LABEL_ARRAYS_TAB_WITH_ORDER|@count gt 0}
                {foreach key=TAB_KEY item=TAB_NAME_DB from=$TAB_LIST_WITH_ORDER}
                    {foreach key=LABEL_KEY item=TAB_LABEL from=$LABEL_ARRAYS_TAB_WITH_ORDER}
                        {if vtranslate({$TAB_LABEL},{$MODULE_NAME}) eq vtranslate({$TAB_NAME_DB},{$MODULE_NAME})}
                            {if is_numeric($LABEL_KEY)}
                                {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($LABEL_KEY)}
                                    {if $TAB_INFO['num_fields'] eq 0}
                                        {continue}
                                    {/if}
                            {/if}
                            <li class="tab-item">
                                <a class="tablinks textOverflowEllipsis" href="#block-{$LABEL_KEY}" >
                                    <span class="tab-label"><strong>{vtranslate({$TAB_LABEL},{$MODULE_NAME})}</strong></span>
                                </a>
                            </li>
                        {/if}
                    {/foreach}
                {/foreach}
            {/if}
        </ul>
    </div>
    <br />
    <div class="tab-content">
        {foreach key=BLOCK_LABEL_KEY item=FIELD_MODEL_LIST from=$TAB_ARRAYS_BLOCK_FIELDS}
            {assign var=TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id'))}
            {if ($TAB_INFO['parent_tab'] neq null && $TAB_INFO['parent_tab'] neq '') || $TAB_INFO['num_fields'] eq 0}
                {continue}
            {/if}
            
            
            
            
            
            {assign var=BLOCK value=$BLOCK_LIST[$BLOCK_LABEL_KEY]}
		{if $BLOCK eq null or $FIELD_MODEL_LIST|@count lte 0}{continue}{/if}
		<div class="block block_{$BLOCK_LABEL_KEY} tab" id="block-{$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}">
			{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
			<div>
				<h5 class="textOverflowEllipsis maxWidth100">
					
				<i class="ti-plus cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}></i>

				<i class="ti-minus cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}></i>

					&nbsp;
					{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</h5>
			</div>
			<div class="blockData">
				<div class="table detailview-table">
					<div {if $IS_HIDDEN} class="hide" {/if}>
						{assign var=COUNTER value=0}
						<div class="row">
							{foreach item=FIELD_MODEL key=FIELD_NAME from=$FIELD_MODEL_LIST}
								{assign var=fieldDataType value=$FIELD_MODEL->getFieldDataType()}
								{if !$FIELD_MODEL->isViewableInDetailView()}
									{continue}
								{/if}
								{if $FIELD_MODEL->get('uitype') eq "83"}
									{foreach item=tax key=count from=$TAXCLASS_DETAILS}
										{if $COUNTER eq 2}
											</div><div class="row">
											{assign var="COUNTER" value=1}
										{else}
											{assign var="COUNTER" value=$COUNTER+1}
										{/if}
										<div class="fieldLabel col-xs-6 col-md-3">
											<span class='muted'>{vtranslate($tax.taxlabel, $MODULE)}(%)</span>
										</div>
										<div class="fieldValue col-xs-6 col-md-3">
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
										{if $COUNTER eq 2}
											</div><div class="row">
											{assign var=COUNTER value=0}
										{/if}
									{/if}
									<div class="fieldLabel col-xs-6 col-md-3 {$WIDTHTYPE}"><span class="muted">{vtranslate({$FIELD_MODEL->get('label')},{$MODULE_NAME})}</span></div>
									<div class="fieldValue col-xs-6 col-md-3 {$WIDTHTYPE}">
										<ul id="imageContainer">
											{foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
												{if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
													<li><img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="400" height="300" /></li>
												{/if}
											{/foreach}
										</ul>
									</div>
									{assign var=COUNTER value=$COUNTER+1}
								{else}
									{if $FIELD_MODEL->get('uitype') eq "20" or $FIELD_MODEL->get('uitype') eq "19" or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'}
										{if $COUNTER eq '1'}
											<div class="fieldLabel col-xs-6 col-md-3 {$WIDTHTYPE}"></div><div class="{$WIDTHTYPE}"></div></div><div class="row">
											{assign var=COUNTER value=0}
										{/if}
									{/if}
									{if $COUNTER eq 2}
										</div><div class="row">
										{assign var=COUNTER value=1}
									{else}
										{assign var=COUNTER value=$COUNTER+1}
									{/if}
									<div class="fieldLabel textOverflowEllipsis  {if $FIELD_MODEL->getName() eq 'description' or $FIELD_MODEL->get('uitype') eq '69'}col-xs-3{else}col-xs-6 col-md-3{/if} {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldLabel_{$FIELD_MODEL->getName()}" >
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
									{if $FIELD_MODEL->get('uitype') eq '19' or $fieldDataType eq 'reminder' or $fieldDataType eq 'recurrence'}
									{assign var=COUNTER value=$COUNTER+1}
									<div class="fieldValue col-xs-9 {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" >
									{else}	
									<div class="fieldValue col-xs-6 col-md-3 {$WIDTHTYPE}" id="{$MODULE_NAME}_detailView_fieldValue_{$FIELD_MODEL->getName()}" >
									{/if}	
										{assign var=FIELD_VALUE value=$FIELD_MODEL->get('fieldvalue')}
										{if $fieldDataType eq 'multipicklist'}
											{assign var=FIELD_DISPLAY_VALUE value=$FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue'))}
										{else}
											{assign var=FIELD_DISPLAY_VALUE value=Vtiger_Util_Helper::toSafeHTML($FIELD_MODEL->getDisplayValue($FIELD_MODEL->get('fieldvalue')))}
										{/if}

										<span class="value textOverflowEllipsis" style="    display: inline-block;" data-field-type="{$FIELD_MODEL->getFieldDataType()}" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '21'} style="white-space:normal;" {/if}>
											{include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName(),$MODULE_NAME) FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
										</span>
										{if $IS_AJAX_ENABLED && $FIELD_MODEL->isEditable() eq 'true' && $FIELD_MODEL->isAjaxEditable() eq 'true'}
											<span class="hide edit pull-left">
												{if $fieldDataType eq 'multipicklist'}
													<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}[]' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
												{else}
													<input type="hidden" class="fieldBasicData" data-name='{$FIELD_MODEL->get('name')}' data-type="{$fieldDataType}" data-displayvalue='{$FIELD_DISPLAY_VALUE}' data-value="{$FIELD_VALUE}" />
												{/if}
											</span>
											<span class="action pull-right"><a href="#" onclick="return false;" class="editAction"><i class="material-icons">create</i></a></span>
										{/if}
									</div>
								{/if}
								
							{/foreach}
							
						</div>
			</div>
		</div>
		</div>
		</div>
            
            
        
        {/foreach}
        {if $TAB_CHILD_ARRAYS_BLOCK_FIELDS|@count gt 0}
            {*print all child block for this tab*}
            {foreach key=TAB_KEY item=TAB_NAME from=$COMBINE_TAB_LIST}
                <div class="block tab combine" id="block-{$TAB_KEY}">
                    {foreach key=BLOCK_LABEL2 item=BLOCK_FIELDS2 from=$TAB_CHILD_ARRAYS_BLOCK_FIELDS name=blockIterator2}
                        {assign var=CHILD_TAB_INFO value=VTETabs_Module_Model::checkIsTab($BLOCK_LIST[$BLOCK_LABEL2]->get('id'))}
                        {if $BLOCK_FIELDS2|@count gt 0 && $CHILD_TAB_INFO['parent_tab'] eq {$TAB_NAME}}
                            {include file="ChildDetailViewTabsView.tpl"|vtemplate_path:$MODULE FIELD_MODEL_LIST = $BLOCK_FIELDS2 BLOCK_LABEL_KEY = $BLOCK_LABEL2 MODULE_NAME = $MODULE_NAME BLOCK_LIST = $BLOCK_LIST USER_MODEL=$USER_MODEL TAXCLASS_DETAILS=$TAXCLASS_DETAILS DAY_STARTS=$DAY_STARTS BASE_CURRENCY_SYMBOL=$BASE_CURRENCY_SYMBOL IMAGE_DETAILS=$IMAGE_DETAILS IS_AJAX_ENABLED=$IS_AJAX_ENABLED}
                        {/if}
                    {/foreach}
                </div>
            {/foreach}
        {/if}
    </div>
    
    
    {if $IS_IMG_TAB}
        <br />
        {assign var=BLOCK_LABEL_KEY value = "LBL_IMAGE_INFORMATION"}
        
        
        
        <div class="block block_{$BLOCK_LABEL_KEY} last">
			{assign var=IS_HIDDEN value=$BLOCK->isHidden()}
			{assign var=WIDTHTYPE value=$USER_MODEL->get('rowheight')}
			<input type=hidden name="timeFormatOptions" data-value='{$DAY_STARTS}' />
			<div>
				<h5 class="textOverflowEllipsis maxWidth100">
					
				<i class="ti-plus cursorPointer alignMiddle blockToggle {if !($IS_HIDDEN)} hide {/if}" data-mode="hide" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}></i>

				<i class="ti-minus cursorPointer alignMiddle blockToggle {if ($IS_HIDDEN)} hide {/if}" data-mode="show" data-id={$BLOCK_LIST[$BLOCK_LABEL_KEY]->get('id')}></i>

					&nbsp;
					{vtranslate({$BLOCK_LABEL_KEY},{$MODULE_NAME})}
				</h5>
			</div>
        
            <div class="blockData">
                <div class="table detailview-table">
					<div {if $IS_HIDDEN} class="hide" {/if}>
						{assign var=COUNTER value=0}
						<div class="row">
	                        <div class="fieldLabel col-xs-12 col-md-3 {$WIDTHTYPE}"><span class="muted">{vtranslate('Contact Image',{$MODULE_NAME})}</span></div>
	                        <div class="fieldValue col-xs-12 col-md-9 {$WIDTHTYPE}">
	                            <ul id="imageContainer">
	                                {foreach key=ITER item=IMAGE_INFO from=$IMAGE_DETAILS}
	                                    {if !empty($IMAGE_INFO.path) && !empty({$IMAGE_INFO.orgname})}
	                                        <li><img src="{$IMAGE_INFO.path}_{$IMAGE_INFO.orgname}" title="{$IMAGE_INFO.orgname}" width="400" height="300" /></li>
	                                    {/if}
	                                {/foreach}
	                            </ul>
	                        </div>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    {/if}
    </div>
    {*End tab block*}
    {if $BLOCK_LIST['LBL_ITEM_DETAILS']|@count gt 0}
        {include file='LineItemsDetail.tpl'|@vtemplate_path:'Inventory'}
    {/if}
{/strip}