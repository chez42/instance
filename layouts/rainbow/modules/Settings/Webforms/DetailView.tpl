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
<div class="detailViewContainer">
    <div class="col-sm-12">
    
    <div class=" detailview-header-block sh-effect1">
	    <div class="detailview-header">
	        <div class="row">
	            
	            <div class="col-lg-6 col-md-6 col-sm-6">
					<div class="record-header clearfix">
						{if !$MODULE}
							{assign var=MODULE value=$MODULE_NAME}
						{/if}
						<div class="recordImage bg_{$MODULE} hidden-sm hidden-xs ">
							 
						</div>
			
						<div class="recordBasicInfo" style = "padding-left:0px;">
							<div class="info-row">
								<h4>
									<span class="recordLabel pushDown" title="{$RECORD->getName()}">
										{foreach item=NAME_FIELD from=$MODULE_MODEL->getNameFields()}
											{assign var=FIELD_MODEL value=$MODULE_MODEL->getField($NAME_FIELD)}
											{if $FIELD_MODEL->getPermissions()}
												<span class="{$NAME_FIELD}">{$RECORD->get($NAME_FIELD)}</span>&nbsp;
											{/if}
										{/foreach}
									</span>
								</h4>
							</div>
							{include file="DetailViewHeaderFieldsView.tpl"|vtemplate_path:$MODULE}
						</div>
					</div>
				</div>
				{include file="modules/Settings/Webforms/DetailViewActions.tpl"|myclayout_path}
	            
	        </div>
		</div>
    
    {include file='DetailViewBlockView.tpl'|@vtemplate_path:Vtiger RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    {include file='FieldsDetailView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    </div>
</div>
</div></div>
{/strip}