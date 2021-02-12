{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
	
	<div class="relatedHeader">
		<div class="btn-toolbar row">
			<div class="col-lg-12 col-md-12 col-sm-12 btn-toolbar">
				<div class="btn-group">
					<button type="button" class="btn addButton btn-primary" onclick='PandaDoc_Js.sendPandaDocDocument("index.php?module=PandaDoc&view=MassActionAjax&mode=sendPandaDocDocument")' >
						Send Document
					</button>
					<button type="button" class="btn addButton btn-primary" onclick='PandaDoc_Js.showTokenPandaDocDocument("index.php?module=PandaDoc&view=MassActionAjax&mode=showPandaDocTokens")' >
						Show Tokens
					</button>
				</div>
				&nbsp;
			</div>
			
		</div>
		<div class="clearfix" style="margin: 5px;"></div>
		      
	</div>
{/strip}