<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class Settings_MailConverter_Module_Model extends Settings_Vtiger_Module_Model {

	var $name = 'MailConverter';

	/**
	 * Function to get Create record url
	 * @return <String> Url
	 */
	public function getCreateRecordUrl() {
		return 'index.php?module=MailConverter&parent=Settings&view=Edit&mode=step1&create=new';
	}

	/**
	 * Function to get List of fields for mail converter record
	 * @return <Array> List of fields
	 */
	public function getFields() {
		$fields =  array(
			'scannername'	=> array('name' => 'scannername',	'typeofdata' => 'V~M',	'label' => 'Scanner Name',	'datatype' => 'string'),
			'server'		=> array('name' => 'server',		'typeofdata' => 'V~M',	'label' => 'Server',		'datatype' => 'string'),
			'username'		=> array('name' => 'username',		'typeofdata' => 'V~M',	'label' => 'User Name',		'datatype' => 'string'),
			'password'		=> array('name' => 'password',		'typeofdata' => 'V~M',	'label' => 'Password',		'datatype' => 'password'),
			'protocol'		=> array('name' => 'protocol',		'typeofdata' => 'C~O',	'label' => 'Protocol',		'datatype' => 'radio'),
			'ssltype'		=> array('name' => 'ssltype',		'typeofdata' => 'C~O',	'label' => 'SSL Type',		'datatype' => 'radio'),
			'sslmethod'		=> array('name' => 'sslmethod',		'typeofdata' => 'C~O',	'label' => 'SSL Method',	'datatype' => 'radio'),
			'connecturl'	=> array('name' => 'connecturl',	'typeofdata' => 'V~O',	'label' => 'Connect URL',	'datatype' => 'string', 'isEditable' => false),
			'searchfor'		=> array('name' => 'searchfor',		'typeofdata' => 'V~O',	'label' => 'Look For',		'datatype' => 'picklist'),
			'markas'		=> array('name' => 'markas',		'typeofdata' => 'V~O',	'label' => 'After Scan',	'datatype' => 'picklist'),
			'isvalid'		=> array('name' => 'isvalid',		'typeofdata' => 'C~O',	'label' => 'Status',		'datatype' => 'boolean'),
			'time_zone'		=> array('name' => 'time_zone',		'typeofdata' => 'V~O',	'label' => 'Time Zone',		'datatype' => 'picklist'),
		    'userid'        => array('name' => 'userid',		'typeofdata' => 'V~O',	'label' => 'User Id',		'datatype' => 'string', 'isEditable' => false));
		$fieldsList = array();
		foreach($fields as $fieldName => $fieldInfo) {
			$fieldModel = new Settings_MailConverter_Field_Model();
			foreach($fieldInfo as $key=>$value) {
				$fieldModel->set($key, $value);
			}
			$fieldsList[$fieldName] = $fieldModel;
		}
		return $fieldsList;
	}

	/**
	 * Function to get the field of setup Rules
	 *  @return <Array> List of setup rule fields
	 */

	public function getSetupRuleFields() {
		$ruleFields = array(
			'fromaddress'	=> array('name' => 'fromaddress',	'label' => 'LBL_FROM',		'datatype' => 'email'),
			'toaddress'		=> array('name' => 'toaddress',		'label' => 'LBL_TO',		'datatype' => 'email'),
			'cc'			=> array('name' => 'cc',			'label' => 'LBL_CC',		'datatype' => 'email'),
			'bcc'			=> array('name' => 'bcc',			'label' => 'LBL_BCC',		'datatype' => 'email'),
			'subject'		=> array('name' => 'subject',		'label' => 'LBL_SUBJECT',	'datatype' => 'picklist'),
			'body'			=> array('name' => 'body',			'label' => 'LBL_BODY',		'datatype' => 'picklist'),
			'matchusing'	=> array('name' => 'matchusing',	'label' => 'LBL_MATCH',		'datatype' => 'radio'),
			'action'		=> array('name' => 'action',		'label' => 'LBL_ACTION',	'datatype' => 'picklist')
		);
		$ruleFieldsList = array();
		foreach($ruleFields as $fieldName => $fieldInfo) {
			$fieldModel = new Settings_MailConverter_RuleField_Model();
			foreach($fieldInfo as $key=>$value) {
				$fieldModel->set($key, $value);
			}
			$ruleFieldsList[$fieldName] = $fieldModel;
		}
		return $ruleFieldsList;
	}

	/**
	 * Function to get Default url for this module
	 * @return <String> Url
	 */
	public function getDefaultUrl() {
		return 'index.php?module='.$this->getName().'&parent='.$this->getParentName().'&view=List';
	}

	public function isPagingSupported() {
		return false;
	}

	public function MailBoxExists() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT COUNT(*) AS count FROM vtiger_mailscanner", array());
		$response = $db->query_result($result, 0, 'count');
		if ($response == 0)
			return false;
		return true;
	}

	public function getDefaultId() {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT MIN(scannerid) AS id FROM vtiger_mailscanner", array());
		$id = $db->query_result($result, 0, 'id');
		return $id;
	}

	public function getMailboxes() {
		$mailBox = array();
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT scannerid, scannername FROM vtiger_mailscanner WHERE (userid = 0 OR userid IS NULL)", array());
		$numOfRows = $db->num_rows($result);
		for ($i = 0; $i < $numOfRows; $i++) {
			$mailBox[$i]['scannerid'] = $db->query_result($result, $i, 'scannerid');
			$mailBox[$i]['scannername'] = $db->query_result($result, $i, 'scannername');
		}
		return $mailBox;
	}

	public function getScannedFolders($id) {
		$folders = array();
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT foldername FROM vtiger_mailscanner_folders WHERE scannerid=? AND enabled=1", array($id));
		$numOfRows = $db->num_rows($result);
		for ($i = 0; $i < $numOfRows; $i++) {
			$folders[$i] = $db->query_result($result, $i, 'foldername');
		}
		return $folders;
	}

	public function getFolders($id) {
		include_once 'modules/Settings/MailConverter/handlers/MailScannerInfo.php';
		include_once 'modules/Settings/MailConverter/handlers/MailBox.php';
		$scannerName = Settings_MailConverter_Module_Model::getScannerName($id);
		$scannerInfo = new Vtiger_MailScannerInfo($scannerName);
		$mailBox = new Vtiger_MailBox($scannerInfo);
		$isConnected = $mailBox->connect();
		if($isConnected) {
			$allFolders = $mailBox->getFolders();
			$folders = array();
			$selectedFolders = Settings_MailConverter_Module_Model::getScannedFolders($id);
			if(is_array($allFolders)) {
				foreach ($allFolders as $a) {
						if (in_array($a, $selectedFolders)) {
							$folders[$a] = 'checked';
						} else {
							$folders[$a] = '';
						}
				}
				return $folders;
			} else {
				return $allFolders;
			}

		}
		return false;
	}

	public function getScannerName($id) {
		$db = PearDatabase::getInstance();
		$result = $db->pquery("SELECT scannername FROM vtiger_mailscanner WHERE scannerid=?", array($id));
		$scannerName = $db->query_result($result, 0, 'scannername');
		return $scannerName;
	}

	public function updateFolders($scannerId, $folders) {
		include_once 'modules/Settings/MailConverter/handlers/MailScannerInfo.php';
		$db = PearDatabase::getInstance();
		$scannerName = Settings_MailConverter_Module_Model::getScannerName($scannerId);
		$scannerInfo = new Vtiger_MailScannerInfo($scannerName);
		$lastScan = $scannerInfo->dateBasedOnMailServerTimezone('d-M-Y');
		$db->pquery("DELETE FROM vtiger_mailscanner_folders WHERE scannerid=?", array($scannerId));
		foreach ($folders as $folder) {
			$db->pquery("INSERT INTO vtiger_mailscanner_folders VALUES(?,?,?,?,?,?)", array('', $scannerId, $folder, $lastScan, '0', '1'));
		}
	}

	public function hasCreatePermissions() {
		$permissions = false;
		$recordsCount = Settings_MailConverter_Record_Model::getCount();

		global $max_mailboxes;
		if ($recordsCount < $max_mailboxes) {
			$permissions = true;
		}
		return $permissions;
	}
	
	public function getMailManagerMailboxes() {
	    $mailBox = array();
	    global $current_user;
	    $db = PearDatabase::getInstance();
	    $result = $db->pquery("SELECT scannerid, scannername FROM vtiger_mailscanner WHERE userid = ?", array($current_user->id));
	    $numOfRows = $db->num_rows($result);
	    for ($i = 0; $i < $numOfRows; $i++) {
	        $mailBox[$i]['scannerid'] = $db->query_result($result, $i, 'scannerid');
	        $mailBox[$i]['scannername'] = $db->query_result($result, $i, 'scannername');
	    }
	    return $mailBox;
	}
	
	public function setServerName($mServer) {
	    if($mServer == 'imap.gmail.com') {
	        $mServerName = 'gmail';
	    } else if($mServer == 'imap.mail.yahoo.com') {
	        $mServerName = 'yahoo';
	    } else if($mServer == 'mail.messagingengine.com') {
	        $mServerName = 'fastmail';
	    } else if($mServer == 'imap-mail.outlook.com'){
	        $mServerName = 'office365';
	    } else {
	        $mServerName = 'other';
	    }
	    return $mServerName;
	}

	public function getTimeZoneMapping(){
	    
	    return array(
	        "Pacific/Samoa" => "-11:00",
	        "Pacific/Midway" => "-11:00",
	        "Pacific/Honolulu" => "-10:00",
	        "America/Anchorage" => "-9:00",
	        "America/Tijuana" => "-8:00",
	        "America/Los_Angeles" => "-8:00",
	        "America/Denver" => "-7:00",
	        "America/Mazatlan" => "-7:00",
	        "America/Chihuahua" => "-7:00",
	        "America/Phoenix" => "-7:00",
	        "America/Regina" => "-6:00",
	        "America/Monterrey" => "-6:00",
	        "America/Mexico_City" => "-6:00",
	        "America/Chicago" => "-6:00",
	        "America/Tegucigalpa" => "-6:00",
	        "America/Rio_Branco" => "-5:00",
	        "America/Lima" => "-5:00",
	        "America/Indiana/Indianapolis" => "-5:00",
	        "America/New_York" => "-5:00",
	        "America/Bogota" => "-5:00",
	        "America/Caracas" => "-4:30",
	        "America/Santiago" => "-4:00",
	        "America/Manaus" => "-4:00",
	        "America/La_Paz" => "-4:00",
	        "America/Cuiaba" => "-4:00",
	        "America/Halifax" => "-4:00",
	        "America/Asuncion" => "-4:00",
	        "America/St_Johns" => "-3:30",
	        "America/Montevideo" => "-3:00",
	        "America/Godthab" => "-3:00",
	        "America/Argentina/Buenos_Aires" => "-3:00",
	        "America/Sao_Paulo" => "-3:00",
	        "Atlantic/South_Georgia" => "-2:00",
	        "Atlantic/Cape_Verde" => "-1:00",
	        "Atlantic/Azores" => "-1:00",
	        "Africa/Casablanca" => "0:00",
	        "UTC" => "0:00",
	        "Europe/London" => "0:00",
	        "Africa/Monrovia" => "0:00",
	        "Europe/Amsterdam" => "+1:00",
	        "Europe/Belgrade" => "+1:00",
	        "Europe/Brussels" => "+1:00",
	        "Europe/Sarajevo" => "+1:00",
	        "Africa/Algiers" => "+1:00",
	        "Asia/Amman" => "+2:00",
	        "Europe/Athens" => "+2:00",
	        "Asia/Beirut" => "+2:00",
	        "Africa/Cairo" => "+2:00",
	        "Africa/Harare" => "+2:00",
	        "Europe/Helsinki" => "+2:00",
	        "Europe/Istanbul" => "+2:00",
	        "Asia/Jerusalem" => "+2:00",
	        "Europe/Minsk" => "+2:00",
	        "Africa/Windhoek" => "+2:00",
	        "Asia/Baghdad" => "+3:00",
	        "Asia/Kuwait" => "+3:00",
	        "Africa/Nairobi" => "+3:00",
	        "Asia/Tehran" => "+3:30",
	        "Asia/Muscat" => "+4:00",
	        "Asia/Baku" => "+4:00",
	        "Europe/Moscow" => "+3:00",
	        "Asia/Tbilisi" => "+4:00",
	        "Asia/Yerevan" => "+4:00",
	        "Asia/Karachi" => "+5:00",
	        "Asia/Tashkent" => "+5:00",
	        "Asia/Kolkata" => "+5:30",
	        "Asia/Colombo" => "+5:30",
	        "Asia/Katmandu" => "+5:45",
	        "Asia/Almaty" => "+6:00",
	        "Asia/Dhaka" => "+6:00",
	        "Asia/Yekaterinburg" => "+6:00",
	        "Asia/Rangoon" => "+6:30",
	        "Asia/Bangkok" => "+7:00",
	        "Asia/Novosibirsk" => "+7:00",
	        "Asia/Brunei" => "+8:00",
	        "Asia/Krasnoyarsk" => "+8:00",
	        "Asia/Kuala_Lumpur" => "+8:00",
	        "Australia/Perth" => "+8:00",
	        "Asia/Taipei" => "+8:00",
	        "Asia/Ulaanbaatar" => "+8:00",
	        "Asia/Irkutsk" => "+9:00",
	        "Asia/Seoul" => "+9:00",
	        "Asia/Tokyo" => "+9:00",
	        "Australia/Adelaide" => "+9:30",
	        "Australia/Darwin" => "+9:30",
	        "Australia/Brisbane" => "+10:00",
	        "Australia/Canberra" => "+10:00",
	        "Pacific/Guam" => "+10:00",
	        "Australia/Hobart" => "+10:00",
	        "Asia/Vladivostok" => "+10:00",
	        "Asia/Yakutsk" => "+10:00",
	        "Etc/GMT-11" => "+11:00",
	        "Pacific/Auckland" => "+12:00",
	        "Pacific/Fiji" => "+12:00",
	        "Asia/Kamchatka" => "+12:00",
	        "Asia/Magadan" => "+12:00",
	        "Pacific/Tongatapu" => '+13:00');
	    
	}
	
}
