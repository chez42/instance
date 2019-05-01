<style>
	.portalTable>tbody>tr>td{
		padding: 8px;
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
			<td class="fieldLabel textOverflowEllipsis text-center">
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
		
		{assign var=PortalModules value=['Documents', 'Contacts', 'Reports', 'Portfolios', 'Income', 'Performance']}
		{*assign var=PortalReports value=['Omnivue', 'Holdings', 'Overview', 'Income']*}		
		{assign var=PortalReports value=['Portfolios'=>['Asset Class Report'],'Income'=>['Last 12 months','Last Year','Projected','Month Over Month'],'Performance'=>['Gain Loss','GH1 Report','GH2 Report','Overview']]}
		{*'Holdings',*} 
		{foreach key=TAB_ID item=MODEL from=$MODULES_MODELS}
		
			{assign var=MODULE_NAME value=$MODEL->get('name')}
				
			{if $MODEL->get('visible') != '1' || !in_array($MODULE_NAME, $PortalModules)}{continue}{/if}
				
			{if $SELECTED_PORTAL_MODULES|@count gt 0}
				{assign var=VISIBLE value=$SELECTED_PORTAL_MODULES[$TAB_ID]['visible']}
				{assign var=RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$TAB_ID]['record_across_org']}
				{assign var=EDIT_RECORDS value=$SELECTED_PORTAL_MODULES[$TAB_ID]['edit_records']}
			{/if}
			{if $MODULE_NAME neq 'Reports'}
				<tr>
					<td class="fieldValue textOverflowEllipsis text-left">
						{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}
					</td>
					<td> &nbsp;</td>
					<td class="fieldValue text-center">
						<span class="portalFieldValue">
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
							<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$TAB_ID}][visible]" data-displayvalue="{if $VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean" />
							<input type="checkbox" class="inputElement" name="portalModulesInfo[{$TAB_ID}][visible]" value="1" {if $VISIBLE == '1'} checked {/if}/>
						</span>
					</td>
					<td class="fieldValue text-center">
						<span class="portalFieldValue">
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
								<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$TAB_ID}][edit_records]" data-displayvalue="{if $EDIT_RECORDS == '1'}Yes{else}No{/if}" data-type="boolean" />
								<input type="checkbox" class="inputElement" name="portalModulesInfo[{$TAB_ID}][edit_records]" value="1" {if $EDIT_RECORDS == '1'} checked {/if}/>
							{/if}
						</span>
					</td>
					<td class="fieldValue text-center">
						<span class="portalFieldValue">
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
								<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$TAB_ID}][record_across_org]" data-displayvalue="{if $RECORD_VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean"/>
								<input type="checkbox" class="inputElement" name="portalModulesInfo[{$TAB_ID}][record_across_org]" value="1" {if $RECORD_VISIBLE == '1'} checked {/if}/>
							{/if}
						</span>
					</td>
				</tr>
			{/if}
			{if $MODULE_NAME eq 'Reports'}
				
				{if count($PortalReports) > 0}
					
					{assign var=ReportTab value=$TAB_ID}
					
					{foreach key=ReprtName item=PortalReport from=$PortalReports}
						
						{if $SELECTED_PORTAL_MODULES|@count gt 0}
							{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName]['visible']}
						{/if}
		
						<tr>
							<td class="fieldValue textOverflowEllipsis text-left">
								{vtranslate($ReprtName, $MODULE)}
							</td>
							<td class="fieldValue text-center">
								{*if $REPORT_VISIBLE == '1'} 
									{vtranslate('LBL_YES', $MODULE_NAME)}
								{else} 
									{vtranslate('LBL_NO', $MODULE_NAME)}
								{/if*}
							</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							{*<td style="text-align: center;">
								{if $REPORT_RECORD_VISIBLE == '1'} 
									{vtranslate('LBL_YES', $MODULE_NAME)}
								{else} 
									{vtranslate('LBL_NO', $MODULE_NAME)}
								{/if}
							</td>*}
						</tr>
						{foreach item=ReportModules from=$PortalReport}
							{if $SELECTED_PORTAL_MODULES|@count gt 0}
								{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName][$ReportModules]['visible']}
								{assign var=REPORT_RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName][$ReportModules]['record_across_org']}
							{/if}
							<tr>
								<td>&nbsp;</td>
								<td class="fieldValue textOverflowEllipsis text-left">
									{vtranslate($ReportModules, $MODULE)}
								</td>
								<td class="fieldValue text-center">
									<span class="portalFieldValue">
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
										<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][visible]" data-displayvalue="{if $REPORT_VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean" />
										<input type="checkbox" class="inputElement" class="{$ReprtName}" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][visible]" value="1" {if $REPORT_VISIBLE == '1'} checked {/if}/>
									</span>
								</td>
								<td>&nbsp;</td>
								<td class="fieldValue text-center">
									<span class="portalFieldValue">
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
										<input type="hidden" class="fieldBasicData" data-name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][record_across_org]" data-displayvalue="{if $REPORT_RECORD_VISIBLE == '1'}Yes{else}No{/if}" data-type="boolean" />
										<input type="checkbox" class="inputElement" class="{$ReprtName}" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][record_across_org]" value="1" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if}/>
									<span>
								</td>
								
							</tr>
						{/foreach}
					{/foreach}
				{/if}
			{/if}
		{/foreach}
	</form>
</table>	