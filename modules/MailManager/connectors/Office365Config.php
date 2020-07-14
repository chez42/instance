<?php
/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ***********************************************************************************/

Class MailManager_Office365Config_Connector {
	static $clientId = '32679be5-4aeb-4cda-9193-fcfe74dbfdce';
	static $clientSecret = '1y5HHz~5-pW.gSmLs2C7GoVuaKS-o4se4c';

	static function getRedirectUrl() {
		global $site_URL;
		return rtrim($site_URL, '/').'/modules/MailManager/OutlookConnect.php';
	}
}
