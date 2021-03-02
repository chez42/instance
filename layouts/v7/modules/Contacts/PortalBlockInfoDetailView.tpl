<style>
	.portalTable>tbody>tr>td{
		padding: 8px;
		border: 1px solid !important;
	}
</style>
<table class="portalTable" border="1" style="width:100%;">
	<form name="portalPermissions">
		<col width="20%">
		<col width="20%">
		<col width="20%">
		<col width="20%">
		<col width="20%">
		<tr>
			<td class="fieldLabel textOverflowEllipsis text-left">
				<b>{vtranslate('LBL_MODULE', $MODULE)}</b>
			</td>
			<td class="fieldLabel textOverflowEllipsis text-center" {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
				&nbsp;
			</td>
			<td class="fieldLabel textOverflowEllipsis text-center">
				<b>{vtranslate('LBL_ENABLE_MODULE', $MODULE)}</b>
			</td>
			<td class="fieldLabel textOverflowEllipsis text-center">
				<b>{vtranslate('LBL_EDIT_RECORDS', $MODULE)}</b>
			</td>
			<td class="fieldLabel textOverflowEllipsis text-center">
				<b>{vtranslate('LBL_VIEW_ALL_RECORDS', $MODULE)}</b>
			</td>
		</tr>
		
		{assign var=PortalModules value=[{getTabid('HelpDesk')} => 'Tickets', {getTabid('Documents')} => 'Documents', {getTabid('Potentials')} => 'Potentials', {getTabid('Products')} => 'Products', {getTabid('Reports')} => 'Reports']}	
		{assign var=PortalReports value=['Portfolios'=>['Asset Class Report'],'Income'=>['Last 12 months','Last Year','Projected','Month Over Month'],'Performance'=>['Gain Loss','GH1 Report','GH2 Report','Overview']]}
		{*'Holdings',*} 
		{foreach key=TAB_ID item=MODULE_NAME from=$PortalModules}
			{assign var=NAME_MODULE value=strtolower(str_replace(' ', '_', $MODULE_NAME))}
			{if $SELECTED_PORTAL_MODULES|@count gt 0}
				{assign var=VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_visible]}
				{assign var=RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_record_across_org]}
				{assign var=EDIT_RECORDS value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_edit_records]}
			{/if}
			{if $MODULE_NAME neq 'Reports'}
				<tr>
					<td class="fieldValue textOverflowEllipsis text-left">
						{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}
					</td>
					<td {if !$REPORT_PERMISSION} style = "display:none;" {/if}> &nbsp;</td>
					<td class="fieldValue text-center">
						<label class="checkbox-switch">
							<input style="width:15px;height:15px;" {if $VISIBLE == '1'} checked {/if} class="inputElement input-checkbox portalSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_visible]" id="portalModulesInfo[{$NAME_MODULE}_visible]">
							<span class="checkbox-slider checkbox-round"></span>
						</label>
						<!-- <span class="portalFieldValue">
							{if $VISIBLE eq '1'} Yes {else} No {/if}
						</span>
						<span class="action pull-right">
							<a href="#" onclick="return false;" class="editPortalAction fa fa-pencil"></a>
						</span>
						<div class="input-save-wrap portalSaveButton pull-right hide">
							<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlinePortalAjaxSave"><i class="fa fa-check"></i></span>
							<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlinePortalAjaxCancel"><i class="fa fa-close"></i></span>
						</div>
						<span class="hide editPortal">
							<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$NAME_MODULE}_visible]" data-displayvalue="{if $VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean" />
							<input type="checkbox" class="inputElement" name="portalModulesInfo[{$NAME_MODULE}_visible]" value="1" {if $VISIBLE == '1'} checked {/if}/>
						</span> -->
					</td>
					<td class="fieldValue text-center">
						{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME == 'Reports' or $MODULE_NAME eq 'Potentials' or $MODULE_NAME eq 'Products'}
							&nbsp;
						{else}
							<label class="checkbox-switch">
								<input style="width:15px;height:15px;" {if $EDIT_RECORDS == '1'} checked {/if} class="inputElement input-checkbox portalSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" id="portalModulesInfo[{$NAME_MODULE}_edit_records]">
								<span class="checkbox-slider checkbox-round"></span>
							</label>
						{/if}
						<!-- <span class="portalFieldValue">
							{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME == 'Reports'}
								&nbsp;
							{else if $EDIT_RECORDS == '1'} 
								{vtranslate('LBL_YES', $MODULE_NAME)}
							{else} 
								{vtranslate('LBL_NO', $MODULE_NAME)}
							{/if}
						</span>
						<span class="action pull-right">
							<a href="#" onclick="return false;" class="editPortalAction fa fa-pencil"></a>
						</span>
						<div class="input-save-wrap portalSaveButton pull-right hide">
							<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlinePortalAjaxSave"><i class="fa fa-check"></i></span>
							<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlinePortalAjaxCancel"><i class="fa fa-close"></i></span>
						</div>
						<span class="hide editPortal">
							{if $MODULE_NAME neq 'Accounts'}
								<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$NAME_MODULE}_edit_records]" data-displayvalue="{if $EDIT_RECORDS == '1'}Yes{else}No{/if}" data-type="boolean" />
								<input type="checkbox" class="inputElement" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" value="1" {if $EDIT_RECORDS == '1'} checked {/if}/>
							{/if}
						</span> -->
					</td>
					<td class="fieldValue text-center">
						
						{if $MODULE_NAME eq 'Accounts'}
							&nbsp;
						{else}
							<label class="checkbox-switch">
								<input style="width:15px;height:15px;" {if $RECORD_VISIBLE == '1'} checked {/if} class="inputElement input-checkbox portalSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" id="portalModulesInfo[{$NAME_MODULE}_record_across_org]">
								<span class="checkbox-slider checkbox-round"></span>
							</label>
						{/if}
						<!-- <span class="portalFieldValue">
							{if $MODULE_NAME eq 'Accounts'}
								&nbsp;
							{else if $RECORD_VISIBLE == '1'} 
								{vtranslate('LBL_YES', $MODULE_NAME)}
							{else} 
								{vtranslate('LBL_NO', $MODULE_NAME)}
							{/if}
						</span>
						<span class="action pull-right">
							<a href="#" onclick="return false;" class="editPortalAction fa fa-pencil"></a>
						</span>
						<div class="input-save-wrap portalSaveButton pull-right hide">
							<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlinePortalAjaxSave"><i class="fa fa-check"></i></span>
							<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlinePortalAjaxCancel"><i class="fa fa-close"></i></span>
						</div>
						<span class="hide editPortal">
							{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME eq 'Reports'}
								&nbsp;
							{else}
								<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" data-displayvalue="{if $RECORD_VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean"/>
								<input type="checkbox" class="inputElement" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" value="1" {if $RECORD_VISIBLE == '1'} checked {/if}/>
							{/if}
						</span> -->
					</td>
				</tr>
			{/if}
			{if $MODULE_NAME eq 'Reports'}
				
				{if count($PortalReports) > 0}
					
					{assign var=ReportTab value=$TAB_ID}
					
					{foreach key=ReprtName item=PortalReport from=$PortalReports}
						
						<tr {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
						
							<td class="fieldValue textOverflowEllipsis text-left">
								{vtranslate($ReprtName, $MODULE)}
							</td>
							<td class="fieldValue text-center">
								
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							
						</tr>
						{foreach item=ReportModules from=$PortalReport}
							{assign var=NAME_REPORT value=strtolower(str_replace(' ', '_', $ReportModules))}
							{if $SELECTED_PORTAL_MODULES|@count gt 0}
								{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_REPORT|cat:_visible]}
								{assign var=REPORT_RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_REPORT|cat:_record_across_org]}
							{/if}
							<tr {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
								<td>&nbsp;</td>
								<td class="fieldValue textOverflowEllipsis text-left">
									{vtranslate($ReportModules, $MODULE)}
								</td>
								<td class="fieldValue text-center">
									<label class="checkbox-switch">
										<input style="width:15px;height:15px;" {if $REPORT_VISIBLE == '1'} checked {/if}  class="inputElement input-checkbox portalSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_REPORT}_visible]" id="portalModulesInfo[{$NAME_REPORT}_visible]">
										<span class="checkbox-slider checkbox-round"></span>
									</label>
									<!-- <span class="portalFieldValue">
										{if $REPORT_VISIBLE == '1'} 
											{vtranslate('LBL_YES', $MODULE_NAME)}
										{else} 
											{vtranslate('LBL_NO', $MODULE_NAME)}
										{/if}
									</span>
									<span class="action pull-right">
										<a href="#" onclick="return false;" class="editPortalAction fa fa-pencil"></a>
									</span>
									<div class="input-save-wrap portalSaveButton pull-right hide">
										<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlinePortalAjaxSave"><i class="fa fa-check"></i></span>
										<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlinePortalAjaxCancel"><i class="fa fa-close"></i></span>
									</div>
									<span class="hide editPortal">
										<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$NAME_REPORT}_visible]" data-displayvalue="{if $REPORT_VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean" />
										<input type="checkbox" class="inputElement" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_visible]" value="1" {if $REPORT_VISIBLE == '1'} checked {/if}/>
									</span> -->
								</td>
								<td>&nbsp;</td>
								<td class="fieldValue text-center">
									<label class="checkbox-switch">
										<input style="width:15px;height:15px;" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if} class="inputElement input-checkbox portalSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" id="portalModulesInfo[{$NAME_REPORT}_record_across_org]">
										<span class="checkbox-slider checkbox-round"></span>
									</label>
									<!--<span class="portalFieldValue">
										{if $REPORT_RECORD_VISIBLE == '1'} 
											{vtranslate('LBL_YES', $MODULE_NAME)}
										{else} 
											{vtranslate('LBL_NO', $MODULE_NAME)}
										{/if}
									</span>
									<span class="action pull-right">
										<a href="#" onclick="return false;" class="editPortalAction fa fa-pencil"></a>
									</span>
									<div class="input-save-wrap portalSaveButton pull-right hide">
										<span class="pointerCursorOnHover input-group-addon input-group-addon-save inlinePortalAjaxSave"><i class="fa fa-check"></i></span>
										<span class="pointerCursorOnHover input-group-addon input-group-addon-cancel inlinePortalAjaxCancel"><i class="fa fa-close"></i></span>
									</div>
									<span class="hide editPortal">
										<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" data-displayvalue="{if $REPORT_RECORD_VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean" />
										<input type="checkbox" class="inputElement" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" value="1" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if}/>
									<span>-->
								</td>
								
							</tr>
						{/foreach}
					{/foreach}
				{/if}
			{/if}
		{/foreach}
	</form>
</table>	