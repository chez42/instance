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
{literal}
<style>
.error_page div {
  line-height: 1.5;
  display: inline-block;
  vertical-align: middle;
}
</style>
{/literal}
{if $MESSAGE eq 'Permission denied'}
	<div class = "error_page" style = "text-align:center;">
		<p style = "font-size:150px;padding-top:50px;">403</p>
		<p style = "vertical-align: top;">Access to this feature is restricted for the assigned profile.
		<br/>Please contact your administrator for more details.</p>
	</div>
{else}
	<div class = "error_page" style = "text-align:center;">
		<p style = "font-size:150px;padding-top:50px;">500</p>
		<p style = "vertical-align: top;">Some unexpected error.
		<br/>Please contact your administrator for more details.</p>
	</div>
{/if}