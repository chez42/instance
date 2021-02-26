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
		<td class="fieldLabel textOverflowEllipsis text-center"{if !$REPORT_PERMISSION} style = "display:none;" {/if}>
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
			
	{assign var=PortalModules value=[{getTabid('HelpDesk')} => 'Tickets', {getTabid('Documents')} => 'Documents', {getTabid('Potentials')} => 'Potentials', {getTabid('Products')} => 'Products', {getTabid('Reports')} => 'Reports']}
	{assign var=PortalReports value=['Portfolios'=>['Asset Class Report'],'Income'=>['Last 12 months','Last Year','Projected','Month Over Month'],'Performance'=>['Gain Loss','GH1 Report','GH2 Report','Overview']]}
	
	{foreach key=TAB_ID item=MODULE_NAME from=$PortalModules}
		
		{assign var=NAME_MODULE value=strtolower(str_replace(' ', '_', $MODULE_NAME))}
		
		{if $SELECTED_PORTAL_MODULES|@count gt 0}
			{assign var=VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_visible]}
			{assign var=RECORD_VISIBLE value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_record_across_org]}
			{assign var=EDIT_RECORDS value=$SELECTED_PORTAL_MODULES[$NAME_MODULE|cat:_edit_records]}
		{/if}
		{if $MODULE_NAME neq 'Reports'}		
		<tr>
			<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($MODULE_NAME, 'Settings:CustomerPortal')}</td>
			<td class="fieldLabel textOverflowEllipsis text-center" {if !$REPORT_PERMISSION} style = "display:none;" {/if}></td>
			<td class="fieldValue text-center">
				<label class="checkbox-switch">
					<input type="hidden" name="portalModulesInfo[{$NAME_MODULE}_visible]" id="portalModulesInfo[{$NAME_MODULE}_visible]" value="0" />
					{*<input style="opacity: 0;" {if $VISIBLE == '1'} checked value="1" {else} value="0"{/if} data-on-color="success" class="checkboxSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_visible]" id="portalModulesInfo[{$NAME_MODULE}_visible]">*}
					<input class="inputElement" type="checkbox" {if $MODULE_NAME eq 'Reports'} id="portal_reports" {/if} name="portalModulesInfo[{$NAME_MODULE}_visible]" value="1" {if $VISIBLE == '1'} checked {/if}/>
					<span class="checkbox-slider checkbox-round"></span>
				</label>
			</td>
			<td class="fieldValue text-center">
				{if $MODULE_NAME eq 'Accounts' or $MODULE_NAME eq 'Reports'}
					&nbsp;
				{else}
					<label class="checkbox-switch">
						<input type="hidden" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" id="portalModulesInfo[{$NAME_MODULE}_record_across_org]" value="0" />
						{*<input style="opacity: 0;" {if $RECORD_VISIBLE == '1'} checked value="1" {else} value="0"{/if} data-on-color="success" class="checkboxSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" id="portalModulesInfo[{$NAME_MODULE}_record_across_org]">*}
						<input class="inputElement" type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_record_across_org]" value="1" {if $RECORD_VISIBLE == '1'} checked {/if}/>
						<span class="checkbox-slider checkbox-round"></span>
					</label>
				{/if}
			</td>
			<td class="fieldValue text-center">
				{if $MODULE_NAME neq 'Accounts' && $MODULE_NAME neq 'Potentials' && $MODULE_NAME neq 'Products'}
					<label class="checkbox-switch">
						<input type="hidden" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" id="portalModulesInfo[{$NAME_MODULE}_edit_records]" value="0" />
						{*<input style="opacity: 0;" {if $EDIT_RECORDS == '1'} checked value="1" {else} value="0"{/if} data-on-color="success" class="checkboxSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" id="portalModulesInfo[{$NAME_MODULE}_edit_records]">*}
						<input class="inputElement" type="checkbox" name="portalModulesInfo[{$NAME_MODULE}_edit_records]" value="1" {if $EDIT_RECORDS == '1'} checked {/if}/>
						<span class="checkbox-slider checkbox-round"></span>
					</label>
				{/if}
			</td>
		</tr>
		{/if}
		{if $MODULE_NAME eq 'Reports'}
			
			{if count($PortalReports) > 0}
				
				{assign var=ReportTab value=$TAB_ID}
				
				{foreach key=ReprtName item=PortalReport from=$PortalReports}
					
					<tr class="portal_reports_row"  {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
						<td class="fieldLabel textOverflowEllipsis text-left" ><a href="#" class="mainmodule" data-value="{$ReprtName}" style="cursor:pointer;">{vtranslate($ReprtName, $MODULE)}<span>(+) click to enable all</span></a></td>
						
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
						{else}
							{assign var=REPORT_VISIBLE value="0"}
							{assign var=REPORT_RECORD_VISIBLE value="0"}
						{/if}
						<tr  {if !$REPORT_PERMISSION} style = "display:none;" {/if}>
							<td>&nbsp;</td>
							<td class="fieldLabel textOverflowEllipsis text-left">{vtranslate($ReportModules, $MODULE)}</td>
							<td class="fieldValue text-center">
								<label class="checkbox-switch">
									<input type="hidden" name="portalModulesInfo[{$NAME_REPORT}_visible]" id="portalModulesInfo[{$NAME_REPORT}_visible]" value="0" />
									{*<input style="opacity: 0;"  {if $REPORT_VISIBLE == '1'} checked value="1" {else} value="0"{/if} data-on-color="success" class="checkboxSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_REPORT}_visible]" id="portalModulesInfo[{$NAME_REPORT}_visible]">*}
									<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_visible]" value="1" {if $REPORT_VISIBLE == '1'} checked {/if}/>
									<span class="checkbox-slider checkbox-round"></span>
								</label>
							</td>
							<td class="fieldValue text-center">
								<label class="checkbox-switch">
									<input type="hidden" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" id="portalModulesInfo[{$NAME_REPORT}_record_across_org]" value="0" />
									{*<input style="opacity: 0;"  {if $REPORT_RECORD_VISIBLE == '1'} checked value="1" {else} value="0"{/if} data-on-color="success" class="checkboxSwitch"  type="checkbox" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" id="portalModulesInfo[{$NAME_REPORT}_record_across_org]">*}
									<input type="checkbox" class="{$ReprtName}" name="portalModulesInfo[{$NAME_REPORT}_record_across_org]" value="1" {if $REPORT_RECORD_VISIBLE == '1'} checked {/if}/>
									<span class="checkbox-slider checkbox-round"></span>
								</label>
							</td>
							<td>&nbsp;</td>
						</tr>
					{/foreach}
				{/foreach}
			{/if}
		{/if}
	{/foreach}
</table>