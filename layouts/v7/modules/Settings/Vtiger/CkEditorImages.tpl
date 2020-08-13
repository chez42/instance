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

	<div class="col-lg-12 col-md-12 col-sm-12">
		<div class="clearfix">
			<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
				<h3 style="margin-top: 0px;">{vtranslate('Ck Editor Images', $QUALIFIED_MODULE)}</h3>
			</div>
		</div>	
		<div>
			{assign var=WIDTHTYPE value=$CURRENT_USER_MODEL->get('rowheight')}
			<div class="block">
			  	<iframe id='viewer' src="{$URL}" height="100%" width="100%" style="min-height: 500px;"></iframe>
			</div>
		</div>
	</div>
	
	
{/strip}