<style>
	.portalTable>tbody>tr>td{
		padding: 8px;
		border: 1px solid !important;
	}
</style>
<table class="portalTable" border="1" style="width:100%;">
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
			<b>{vtranslate('LBL_VIEW_ALL_RECORDS', $MODULE)}</b>
		</td>
		<td class="fieldLabel textOverflowEllipsis text-center">
			<b>{vtranslate('LBL_EDIT_RECORDS', $MODULE)}</b>
		</td>
		
	</tr>
			
	{assign var=PortalModules value=['Documents', 'Contacts', 'Portfolios', 'Income', 'Performance', 'Reports']}
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
			<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}</td>
			<td class="fieldLabel textOverflowEllipsis text-center"></td>
			<td class="fieldValue text-center">
				<input type="hidden" name="portalModulesInfo[{$TAB_ID}][visible]" value="0" />
				<input type="checkbox" {if $MODULE_NAME eq 'Reports'} id="portal_reports" {/if} name="portalModulesInfo[{$TAB_ID}][visible]" value="1" {if $VISIBLE == '1'} checked {/if}/>
			</td>
			<td class="fieldValue text-center">
				{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME eq 'Reports'}
					&nbsp;
				{else}
					<input type="hidden" name="portalModulesInfo[{$TAB_ID}][record_across_org]" value="0" />
					<input type="checkbox" name="portalModulesInfo[{$TAB_ID}][record_across_org]" value="1" {if $RECORD_VISIBLE == '1'} checked {/if}/>
				{/if}
			</td>
			<td class="fieldValue text-center">
				{if $MODULE_NAME neq 'Accounts'}
					<input type="hidden" name="portalModulesInfo[{$TAB_ID}][edit_records]" value="0" />
					<input type="checkbox" name="portalModulesInfo[{$TAB_ID}][edit_records]" value="1" {if $EDIT_RECORDS == '1'} checked {/if}/>
				{/if}
			</td>
		</tr>
		{/if}
		{if $MODULE_NAME eq 'Reports'}
			
			{if count($PortalReports) > 0}
				
				{assign var=ReportTab value=$TAB_ID}
				
				{foreach key=ReprtName item=PortalReport from=$PortalReports}
					
					{if $SELECTED_PORTAL_MODULES|@count gt 0}
						{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName]['visible']}
						{assign var=REPORT_RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName]['record_across_org']}
					{else}
						{assign var=REPORT_VISIBLE value="0"}
						{assign var=REPORT_RECORD_VISIBLE value="0"}
					{/if}
	
					<tr class="portal_reports_row">
						<td class="fieldLabel textOverflowEllipsis text-left" ><a href="" class="mainmodule" data-value="{$ReprtName}" style="cursor:pointer;">{vtranslate($ReprtName, $MODULE)}<span>(+) click to enable all</span></a></td>
						
						<td class="fieldValue text-center">
							{*<input type="hidden" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$PortalReport}][visible]" value="0" />
							<input type="checkbox" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$PortalReport}][visible]" value="1" {if $REPORT_VISIBLE == '1'} checked {/if}/>*}
						</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						{*<td style="text-align: center;">
							<input type="hidden" name="portalModulesInfo[{$ReportTab}][{$PortalReport}][record_across_org]" value="0" />
							<input type="checkbox" name="portalModulesInfo[{$ReportTab}][{$PortalReport}][record_across_org]" value="1" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if}/>
						</td>*}
					</tr>
					
					{foreach item=ReportModules from=$PortalReport}
						{if $SELECTED_PORTAL_MODULES|@count gt 0}
							{assign var=REPORT_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName][$ReportModules]['visible']}
							{assign var=REPORT_RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$ReportTab]['allowed_reports'][$ReprtName][$ReportModules]['record_across_org']}
						{else}
							{assign var=REPORT_VISIBLE value="0"}
							{assign var=REPORT_RECORD_VISIBLE value="0"}
						{/if}
						<tr>
							<td>&nbsp;</td>
							<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($ReportModules, $MODULE)}</td>
							<td class="fieldValue text-center">
								<input type="hidden" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][visible]" value="0" />
								<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][visible]" value="1" {if $REPORT_VISIBLE == '1'} checked {/if}/>
							</td>
							<td class="fieldValue text-center">
								<input type="hidden" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][record_across_org]" value="0" />
								<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$ReportTab}][allowed_reports][{$ReprtName}][{$ReportModules}][record_across_org]" value="1" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if}/>
							</td>
							<td>&nbsp;</td>
						</tr>
					{/foreach}
				{/foreach}
			{/if}
		{/if}
	{/foreach}
</table>