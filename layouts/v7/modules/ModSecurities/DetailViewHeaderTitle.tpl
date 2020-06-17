{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	<div class="col-lg-8 col-md-8 col-sm-8">
		<div class="left_wrapper" style="display:block; float:left;">
			<div class="record-header clearfix">
				{if !$MODULE}
					{assign var=MODULE value=$MODULE_NAME}
				{/if}
				<div class="hidden-sm hidden-xs recordImage bg_{$MODULE} app-{$SELECTED_MENU_CATEGORY}">
					<div class="name"><span><strong>{$MODULE_MODEL->getModuleIcon()}</strong></span></div>
				</div>

				<div class="recordBasicInfo">
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
		<div class="right_wrapper" style="display:block; float:right;">
			{include file="DetailViewEODLatestPrice.tpl"|vtemplate_path:$MODULE}
		</div>
	</div>
{/strip}