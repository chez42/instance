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

<style>
	.genHeaderSmall {
		font-size: 15px;
    	line-height: 22px;
	}
</style>

<div style="margin:0 auto;width: 50em;">
	<table border='0' cellpadding='5' cellspacing='0' height='600px' width="700px">
		<tr>
			<td align='center'>
				<table border='0' cellpadding='5' cellspacing='0' width='98%' style = "border-collapse: separate;
				    border-radius: 9px;
				    -moz-border-radius: 6px;
				    background-color: white;box-shadow: 0 2px 2px 0 rgba(0,0,0,0.16), 0 0 0 1px rgba(0,0,0,0.08);">
						<tr>
							<td style = "vertical-align: middle;padding: 20px;">
								<span class='genHeaderSmall'>
									{if vtranslate($MESSAGE) eq 'Handler not found.'}
										<span style = 'color: #777;'>Well... this doesn't look quite right! Please try again</span>
									{else}
										<span style = 'color: #777;'>"Hmmmm...it doesn't look like you are allowed to go there. Please try again.</span>
									{/if}
									<br/><br/>
									<a href='javascript:window.history.back();' style = "font: bold 11px Arial;
									  text-decoration: none;
									  background-color: #EEEEEE;
									  color: #333333;
									  padding: 6px;border-radius:5px;
									 ">{vtranslate('LBL_GO_BACK')}</a><br>
								</span>
							</td>
							<td rowspan='2' width ="40%"><img src="{vimage_path('robot.png')}" align="right"></td>
						</tr>
				
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>