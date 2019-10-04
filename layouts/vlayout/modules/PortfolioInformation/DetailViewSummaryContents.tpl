{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}
{strip}
	{if $INTEGRITY.bad > 0}
		{if $INTEGRITY.info[0].difference != 0}
{*			<p><img src="layouts/vlayout/skins/alphagrey/images/red_x.png" />&nbsp;&nbsp;Omniscient has found an issue with the Custodial feed and as a result this Portfolio seems to be off by approximately ${$INTEGRITY.info[0].difference|number_format:2:".":","}. <br />${$INTEGRITY.info[0].custodian_value|number_format:2:".":","} is the true value of this account and the data integrity team is performing a manual reconciliation to provide you the most accurate data possible!</p>*}
		{/if}
	{/if}
	{if $INTEGRITY.info[0].special_notes eq 1}
		{if $INTEGRITY.info[0].notes.margin_balance neq 0}
{*			<p style="color:green;">Margin Balance: ${$INTEGRITY.info[0].notes.margin_balance|number_format:2}</p>*}
		{/if}
		{if $INTEGRITY.info[0].notes.money_market_funds neq 0}
{*			<p style="color:green;">Money Market Funds: ${$INTEGRITY.info[0].notes.money_market_funds|number_format:2}</p>*}
		{/if}
	{/if}
	{if $DATE_DIFFERENCE < -1}
{*		<p><span style="background-color:#CD0000; padding:2px; color:white;">The last update from the custodian was given {$LAST_DATE}, {$DATE_DIFFERENCE} days ago.  {if $LAST_DATE eq "Unknown"}This may be a closed account and Omniscient is in the process of verifying the integrity{/if}</span></p>*}
{*		<p style="text-align:right;">Last custodial update {$LAST_DATE} {if $LAST_DATE eq "Unknown"}This may be a closed account and no longer part of the custodial feed{/if}</p>*}
	{/if}
	{include file='SummaryViewWidgets.tpl'|vtemplate_path:$MODULE_NAME}
{/strip}